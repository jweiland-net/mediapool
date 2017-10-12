<?php
namespace JWeiland\Mediapool\Import\Video;

/*
* This file is part of the TYPO3 CMS project.
*
* It is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License, either version 2
* of the License, or any later version.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*
* The TYPO3 project - inspiring people to share!
*/

use GuzzleHttp\Client;
use JWeiland\Mediapool\Domain\Model\Video;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;

/**
 * Class YouTubeVideoImport
 *
 * @package JWeiland\Mediapool\Import\Video;
 */
class YouTubeVideoImport extends AbstractVideoImport
{
    /**
     * URL to fetch video information via GET request
     * player = embedHtml
     * snippet = channelId, title, description, tags and categoryId
     */
    const VIDEO_API_URL = 'https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=player,snippet';

    /**
     * Name of the video platform
     *
     * @var string
     */
    protected $platformName = 'YouTube';

    /**
     * Platform hosts
     *
     * @var array
     */
    protected $platformHosts = ['https://youtube.com', 'https://www.youtube.com', 'https://youtu.be'];

    /**
     * Video
     *
     * @var Video
     */
    protected $video;

    /**
     * YouTube Video ID
     *
     * @var string
     */
    protected $videoId = '';

    /**
     * Youtube Data API v3 key
     *
     * @var string
     */
    protected $apiKey = '';

    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Configuration Utility
     *
     * @var ConfigurationUtility
     */
    protected $configurationUtility;

    /**
     * Guzzle Client for HTTP requests
     *
     * @var Client
     */
    protected $client;

    /**
     * inject objectManager
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * inject configurationUtility
     *
     * @param ConfigurationUtility $configurationUtility
     * @return void
     */
    public function injectConfigurationUtility(ConfigurationUtility $configurationUtility)
    {
        $this->configurationUtility = $configurationUtility;
    }

    /**
     * inject client
     *
     * @param Client $client
     * @return void
     */
    public function injectClient(Client $client)
    {
        $this->client = $client;
    }

    /**
     * This method must return a video object filled with
     * all given properties like title, description, ...
     * that are related to the $video->link from a video
     * platform. Otherwise the method must return bool false
     * to signal a wrong link.
     *
     * @param Video $video an existing or new video object with a link
     * @return Video|bool filled video object or bool false
     */
    public function getFilledVideoObject(Video $video)
    {
        $this->apiKey = $this->getApiKey();
        $this->video = $video;
        if (($videoId = $this->getVideoId()) === false) {
            return false;
        }
        $this->videoId = $videoId;
        $this->fetchVideoInformation();
        return $this->video;
    }

    /**
     * Returns the YouTube Data API v3 key if set.
     * Otherwise throws exception.
     *
     * @return string
     * @throws \Exception
     */
    protected function getApiKey() : string
    {
        $extConf = $this->configurationUtility->getCurrentConfiguration('mediapool');
        if ($extConf['youtubeDataApiKey']['value'] != '') {
            return $extConf['youtubeDataApiKey']['value'];
        } else {
            throw new \Exception(
                'YouTube Data API v3 key is mandatory but not set. Please set an API-Key to get' .
                ' YouTubeVideoImport working (Extension Manager > Mediapool > Configuration).',
                1507791149
            );
        }
    }

    /**
     * Returns the if of passed video link if valid.
     * Otherwise returns false
     *
     * @return string|bool false if link is not valid
     */
    protected function getVideoId()
    {
        $query = parse_url($this->video->getLink(), PHP_URL_QUERY);
        parse_str($query, $parsedQuery);
        if (isset($parsedQuery['v'])) {
            return $parsedQuery['v'];
        }
        return false;
    }

    /**
     * Fetch video information from YouTube Data API v3 and inserts
     * it into $this->video. Fills title, description, uploadDate and playerHTML.
     *
     * @return void
     * @throws \Exception if fetching information is not successful
     */
    protected function fetchVideoInformation()
    {
        $response = $this->client->request(
            'GET',
            sprintf(self::VIDEO_API_URL, $this->videoId, $this->apiKey)
        );
        // ok
        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody()->getContents(), true);
            if (count($result['items']) && isset($result['items'][0]['snippet'], $result['items'][0]['player'])) {
                $this->video->setTitle($result['items'][0]['snippet']['title']);
                $this->video->setDescription(nl2br($result['items'][0]['snippet']['description']));
                $this->video->setUploadDate(new \DateTime($result['items'][0]['snippet']['publishedAt']));
                $this->video->setPlayerHTML($result['items'][0]['player']['embedHtml']);
                $this->video->setVideoId('yt_' . $this->videoId);
            }
        // invalid api key
        } elseif ($response->getStatusCode() == 400) {
            throw new \Exception(
                sprintf(
                    'Fetching video information for %s failed! Got the following response from YouTube: %s.' .
                    ' Please check your API-key.',
                    $this->video->getLink(),
                    $response->getBody()->getContents()
                ),
                1507792488
            );
        // other problems
        } else {
            throw new \Exception(
                sprintf(
                    'Fetching video information for %s failed! Got status code %d and the' .
                    ' following response: %s',
                    $this->video->getLink(),
                    $response->getStatusCode(),
                    $response->getBody()->getContents()
                ),
                1507794777
            );
        }
    }
}

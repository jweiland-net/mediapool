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
use JWeiland\Mediapool\Domain\Repository\VideoRepository;
use JWeiland\Mediapool\Service\YouTubeService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
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
     * status = uploadStatus, privacyStatus, ...
     */
    const VIDEO_API_URL = 'https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=player,snippet,status';

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
     * @deprecated remove
     */
    protected $videoId = '';

    /**
     * YouTube Video IDs
     * single video: 'exi0iht_kLw'
     * multiple videos: 'exi0iht_kLw,Vfw1pAmLlY,jzTVVocFaVE'
     *
     * @var string
     */
    protected $videoIds = '';

    /**
     * Youtube Data API v3 key
     *
     * @var string
     */
    protected $apiKey = '';

    /**
     * YouTube Service
     *
     * @var YouTubeService
     */
    protected $youTubeService;

    /**
     * inject youTubeService
     *
     * @param YouTubeService $youTubeService
     * @return void
     */
    public function injectYouTubeService(YouTubeService $youTubeService)
    {
        $this->youTubeService = $youTubeService;
    }

    /**
     * Initialize Object
     * get and set api key
     *
     * @return void
     */
    public function initializeObject()
    {
        $this->apiKey = $this->youTubeService->getApiKey();
    }

    /**
     * This method must return a video object filled with
     * all given properties like title, description, ...
     * that are related to the $video->link from a video
     * platform. Otherwise the method must return null or
     * throw a exception on error.
     *
     * @param Video $video an existing or new video object with a link
     * @return Video
     * @throws VideoPermissionException
     * @throws InvalidVideoIdException
     */
    public function getFilledVideoObject(Video $video) : Video
    {
        $this->video = $video;
        if (!($videoId = $this->getVideoId())) {
            throw new InvalidVideoIdException(
                'Could not extract a video id from your video link ' . $video->getLink() . '.',
                1508165920
            );
        }
        $this->videoIds = $videoId;
        $items = $this->fetchVideoInformation();
        if ($items && $this->checkVideoPermission($items[0])) {
            $this->video->setTitle($items[0]['snippet']['title']);
            $this->video->setDescription($items[0]['snippet']['description']);
            $this->video->setUploadDate(new \DateTime($items[0]['snippet']['publishedAt']));
            $this->video->setPlayerHTML($items[0]['player']['embedHtml']);
            $this->video->setVideoId('yt_' . $this->videoIds);
            $this->video->setThumbnail($items[0]['snippet']['thumbnails']['medium']['url']);
            return $this->video;
        } else {
            throw new VideoPermissionException(
                'Either the selected video is a private video or it is marked as not embeddable.',
                1508165820
            );
        }
    }

    /**
     * Fetches information for all given $videoIds and returns the information as an DataHandler
     * compatible array.
     *
     * @param string $videoIds
     * @param int $pid
     * @param string $recordUids includes all UIDs as a comma separated list
     * @return array
     */
    public function getDataArrayForPlaylist(string $videoIds, int $pid, string &$recordUids = '') : array
    {
        /** @var VideoRepository $videoRepository */
        $videoRepository = $this->objectManager->get(VideoRepository::class);
        $this->videoIds = $videoIds;
        $data = [];
        $recordUidArray = [];
        foreach ($this->fetchVideoInformation() as $i => $item) {
            // don´t create a new record, if a video with the same id on current pid already exists
            $queryResult = $videoRepository->findByVideoId('yt_' . (string)$item['id'], $pid);
            if ($video = $queryResult->getFirst()) {
                $recordUidArray[] = $video->getUid();
            } else {
                $uploadDate = new \DateTime($item['snippet']['publishedAt']);
                $recordUidArray[] = 'NEW1234' . $i;
                $data['tx_mediapool_domain_model_video']['NEW1234' . $i] = [
                    'pid' => $pid,
                    'link' => 'https://youtu.be/' . (string)$item['id'],
                    'title' => (string)$item['snippet']['title'],
                    'description' => (string)$item['snippet']['description'],
                    'upload_date' => $uploadDate->getTimestamp(),
                    'player_html' => (string)$item['player']['embedHtml'],
                    'video_id' => 'yt_' . (string)$item['id'],
                    'thumbnail' => (string)$item['snippet']['thumbnails']['medium']['url']
                ];
            }
        }
        $recordUids = implode(',', $recordUidArray);
        return $data;
    }

    /**
     * Checks a video for privacy status and embeddable
     *
     * @param array $item
     * @return bool true if permissions are ok otherwise false
     */
    protected function checkVideoPermission(array $item) : bool
    {
        $hasPermission = true;
        // if we can´t watch or embed the video throw exception
        if (
            $item['status']['privacyStatus'] === 'private' ||
            $item['status']['embeddable'] == false
        ) {
            $hasPermission = false;
        }
        return $hasPermission;
    }

    /**
     * Returns the if of passed video link if valid.
     * Otherwise returns false
     *
     * @return string empty string if link is not valid
     */
    protected function getVideoId() : string
    {
        $query = parse_url($this->video->getLink(), PHP_URL_QUERY);
        parse_str($query, $parsedQuery);
        if (isset($parsedQuery['v'])) {
            return $parsedQuery['v'];
        }
        return '';
    }

    /**
     * Fetches video information for $this->videoIds and returns the
     * items array from YouTube Data v3 API with parts snippet and player
     * Example on https://developers.google.com/youtube/v3/docs/videos/list
     *
     * @todo throw error if result items is empty?
     * @param array $items leave it empty
     * @param string $additionalRequestParams additional request parameters
     * @return array
     * @throws \HttpRequestException
     */
    protected function fetchVideoInformation(array $items = [], string $additionalRequestParams = '') : array
    {
        $response = $this->client->request(
            'GET',
            sprintf(self::VIDEO_API_URL, $this->videoIds, $this->apiKey) . $additionalRequestParams
        );
        // ok
        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody()->getContents(), true);
            if (count($result['items'])) {
                foreach ($result['items'] as $item) {
                    // only add video if permissions are ok
                    if ($this->checkVideoPermission($item)) {
                        $items[] = $item;
                    }
                }
                // call recursive if nextPageToken is set
                if (isset($result['nextPageToken'])) {
                    $items = self::fetchVideoInformation($items, '&pageToken=' . $result['nextPageToken']);
                }
            }
            return $items;
            // invalid api key
        } elseif ($response->getStatusCode() == 400) {
            throw new \HttpRequestException(
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
            throw new \HttpRequestException(
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

<?php
namespace JWeiland\Mediapool\Import\Playlist;

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

use JWeiland\Mediapool\Configuration\ExtConf;
use JWeiland\Mediapool\Import\Video\YouTubeVideoImport;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class YoutubePlaylistImport
 */
class YoutubePlaylistImport extends AbstractPlaylistImport
{
    /**
     * URL to fetch playlist items via GET request
     */
    const PLAYLIST_ITEMS_API_URL = 'https://www.googleapis.com/youtube/v3/playlistItems?playlistId=%s&key=%s&part=contentDetails,status&maxResults=50';

    /**
     * URL to fetch playlist title via GET request
     */
    const PLAYLIST_API_URL = 'https://www.googleapis.com/youtube/v3/playlists?id=%s&key=%s&part=snippet';

    /**
     * URL to fetch channel data (uploads playlist, ...) via GET request
     */
    const CHANNELS_LIST_API_URL = 'https://www.googleapis.com/youtube/v3/channels?key=%s&part=contentDetails&%s';

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
     * YouTube Video Import
     *
     * @var YouTubeVideoImport
     */
    protected $youTubeVideoImport;

    /**
     * Youtube Data API v3 key
     *
     * @var string
     */
    protected $apiKey = '';

    /**
     * Playlist ID
     *
     * @var string
     */
    protected $playlistId = '';

    /**
     * inject youTubeVideoImport
     *
     * @param YouTubeVideoImport $youTubeVideoImport
     */
    public function injectYouTubeVideoImport(YouTubeVideoImport $youTubeVideoImport)
    {
        $this->youTubeVideoImport = $youTubeVideoImport;
    }

    /**
     * This method must return an array with the following structure
     * [
     *     'fieldArray' => [
     *         'title' => 'Video title',
     *         'videos' => '<comma separated list of record uids>'
     *     'dataHandler' => [] DataHandler compatible array, you can put your videos into here !
     * ];
     *
     * @param string $playlistLink like https://www.youtube.com/playlist?list=PL-ABvQXa8oyE4zbwSy4V6K5YTD6S_lhu-
     * @param int $pid to store video records from this playlist
     * @return array as showed above
     */
    public function getPlaylistInformation(string $playlistLink, int $pid): array
    {
        $this->apiKey = GeneralUtility::makeInstance(ExtConf::class)->getYoutubeDataApiKey();
        if (!($playlistId = $this->getPlaylistId($playlistLink))) {
            $this->addFlashMessageAndLog(
                'youTubePlaylistImport.invalid_id.title',
                'youTubePlaylistImport.invalid_id.message',
                [$playlistLink]
            );
            return [];
        }
        $this->playlistId = $playlistId;
        $information = $this->fetchPlaylistInformation();
        $videos = $this->fetchPlaylistItems();
        $resultArray = [];
        $videoIds = [];
        if ($videoIds !== false && $information !== false) {
            $i = 0;
            foreach ($videos as $item) {
                if (isset($item['status']['privacyStatus']) && $item['status']['privacyStatus'] === 'private') {
                    // skip private videos
                    continue;
                }
                $videoIds['NEW' . $i] = ['pid' => $pid, 'video' => trim($item['contentDetails']['videoId'])];
                $i++;
            }
            $recordUids = '';
            $data = $this->youTubeVideoImport->processDataArray($videoIds, $pid, $recordUids, true);
            // return empty array on error
            if ($this->youTubeVideoImport->hasError()) {
                return [];
            }
            $resultArray = [
                'fieldArray' => [
                    'pid' => $pid,
                    'link' => $playlistLink,
                    'playlist_id' => 'yt_' . $playlistId,
                    'title' => (string)$information[0]['snippet']['title'],
                    'thumbnail' => (string)$information[0]['snippet']['thumbnails']['medium']['url'],
                    'thumbnail_large' => (string)$information[0]['snippet']['thumbnails']['standard']['url'],
                    'videos' => $recordUids
                ],
                'dataHandler' => $data
            ];
        }
        return $resultArray;
    }

    /**
     * Returns the id of playlist link.
     * Otherwise returns false
     *
     * @param string $playlistLink
     * @return string empty string if link is not valid
     */
    protected function getPlaylistId(string $playlistLink): string
    {
        $playlistId = '';
        if (
            preg_match('@https://www\.youtube\.com/channel/(?<id>[^/]*)@', $playlistLink, $matches)
            && array_key_exists('id', $matches)
        ) {
            $playlistId = $this->getUploadsPlaylistIdFromYouTubeChannel($matches['id'], '');
        } elseif (
            preg_match('@https://www\.youtube\.com/user/(?<user>[^/]*)@', $playlistLink, $matches)
            && array_key_exists('user', $matches)
        ) {
            $playlistId = $this->getUploadsPlaylistIdFromYouTubeChannel('', $matches['user']);
        } else {
            $query = parse_url($playlistLink, PHP_URL_QUERY);
            parse_str($query, $parsedQuery);
            if (isset($parsedQuery['list'])) {
                $playlistId = $parsedQuery['list'];
            }
        }
        return $playlistId;
    }

    /**
     * Returns the playlist id of the "Uploads" playlist from a YouTube channel.
     *
     * @param string $channelId
     * @param string $user
     * @return string
     */
    protected function getUploadsPlaylistIdFromYouTubeChannel(string $channelId, string $user): string
    {
        $playlistId = '';
        if ($channelId) {
            // Newer YouTube channels without username like https://www.youtube.com/channel/<id>
            $channelParam = 'id=' . $channelId;
        } else {
            // Old school YouTube channels like https://www.youtube.com/user/<username>
            $channelParam = 'forUsername=' . $user;
        }
        $response = $this->client->request(
            'GET',
            sprintf(self::CHANNELS_LIST_API_URL, $this->apiKey, $channelParam)
        );
        if ($response->getStatusCode() === 200) {
            $result = json_decode($response->getBody()->getContents(), true);
            if (isset($result['items'][0]['contentDetails']['relatedPlaylists']['uploads'])) {
                $playlistId = $result['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
            }
        } else {
            $this->checkResponseStatusCode($response);
        }
        return $playlistId;
    }

    /**
     * Checks the response status code
     *
     * @param ResponseInterface $response
     * @throws \HttpRequestException
     */
    protected function checkResponseStatusCode(ResponseInterface $response)
    {
        // invalid api key
        if ($response->getStatusCode() == 400) {
            throw new \HttpRequestException(
                sprintf(
                    'Fetching playlist information for %s failed! Got the following response from YouTube: %s.' .
                    ' Please check your API-key.',
                    $this->playlistId,
                    $response->getBody()->getContents()
                ),
                1508146718
            );
            // other problems
        }
        throw new \HttpRequestException(
                sprintf(
                    'Fetching playlist information for %s failed! Got status code %d and the' .
                    ' following response: %s',
                    $this->playlistId,
                    $response->getStatusCode(),
                    $response->getBody()->getContents()
                ),
                1508146719
            );
    }

    /**
     * Fetches playlist items from YouTube Data v3 API
     * returns merged item array from YouTube Data v3 API
     *
     * @param array $items leave it empty
     * @param string $additionalRequestParams additional request parameters
     * @return array empty array on error
     */
    protected function fetchPlaylistItems(array $items = [], string $additionalRequestParams = '')
    {
        $response = $this->client->request(
            'GET',
            sprintf(self::PLAYLIST_ITEMS_API_URL, $this->playlistId, $this->apiKey) . $additionalRequestParams
        );
        // ok
        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody()->getContents(), true);
            if (count($result['items'])) {
                foreach ($result['items'] as $item) {
                    $items[] = $item;
                }
                // call recursive if nextPageToken is set
                if (isset($result['nextPageToken'])) {
                    $items = self::fetchPlaylistItems($items, '&pageToken=' . $result['nextPageToken']);
                }
                return $items;
            }
        } else {
            $this->checkResponseStatusCode($response);
        }
        return [];
    }

    /**
     * Fetches playlist information like title and thumbnail
     * returns item array from YouTube Data v3 API
     *
     * @return array empty array on error
     */
    protected function fetchPlaylistInformation(): array
    {
        $response = $this->client->request(
            'GET',
            sprintf(self::PLAYLIST_API_URL, $this->playlistId, $this->apiKey)
        );
        // ok
        if ($response->getStatusCode() == 200) {
            $result = json_decode($response->getBody()->getContents(), true);
            if (count($result['items'])) {
                return $result['items'];
            }
        } else {
            $this->checkResponseStatusCode($response);
        }
        return [];
    }
}

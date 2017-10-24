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

use JWeiland\Mediapool\Import\Video\YouTubeVideoImport;
use JWeiland\Mediapool\Service\YouTubeService;
use Psr\Http\Message\ResponseInterface;

/**
 * Class YoutubePlaylistImport
 *
 * @package JWeiland\Mediapool\Import\Playlist;
 */
class YoutubePlaylistImport extends AbstractPlaylistImport
{
    /**
     * URL to fetch playlist items via GET request
     */
    const PLAYLIST_ITEMS_API_URL = 'https://www.googleapis.com/youtube/v3/playlistItems?playlistId=%s&key=%s&part=contentDetails&maxResults=50';

    /**
     * URL to fetch playlist title via GET request
     */
    const PLAYLIST_API_URL = 'https://www.googleapis.com/youtube/v3/playlists?id=%s&key=%s&part=snippet';

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
     * YouTube Service
     *
     * @var YouTubeService
     */
    protected $youTubeService;

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
     * @return void
     */
    public function injectYouTubeVideoImport(YouTubeVideoImport $youTubeVideoImport)
    {
        $this->youTubeVideoImport = $youTubeVideoImport;
    }

    /**
     * inject youtubeService
     *
     * @param YouTubeService $youTubeService
     * @return void
     */
    public function injectYoutubeService(YouTubeService $youTubeService)
    {
        $this->youTubeService = $youTubeService;
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
     * @throws InvalidPlaylistIdException
     */
    public function getPlaylistInformation(string $playlistLink, int $pid) : array
    {
        $this->apiKey = $this->youTubeService->getApiKey();
        if (!($playlistId = $this->getPlaylistId($playlistLink))) {
            throw new InvalidPlaylistIdException(
                'Could not extract playlist id from playlist link ' . $playlistLink . '.',
                1508221105
            );
        }
        $this->playlistId = $playlistId;
        $information = $this->fetchPlaylistInformation();
        $videos = $this->fetchPlaylistItems();
        $resultArray = [];
        $videoIds = [];
        if ($videoIds !== false && $information !== false) {
            $recordUids = '';
            foreach ($videos as $item) {
                $videoIds[] = $item['contentDetails']['videoId'];
            }
            $data = $this->youTubeVideoImport->processDataArray(implode(',', $videoIds), $pid, $recordUids);
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
    protected function getPlaylistId(string $playlistLink) : string
    {
        $query = parse_url($playlistLink, PHP_URL_QUERY);
        parse_str($query, $parsedQuery);
        if (isset($parsedQuery['list'])) {
            return $parsedQuery['list'];
        }
        return '';
    }

    /**
     * Checks the response status code
     *
     * @param ResponseInterface $response
     * @return void
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
        } else {
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
    protected function fetchPlaylistInformation() : array
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

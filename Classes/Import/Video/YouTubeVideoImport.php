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


use JWeiland\Mediapool\Domain\Model\Video;
use JWeiland\Mediapool\Domain\Repository\VideoRepository;
use JWeiland\Mediapool\Service\YouTubeService;


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
        parent::initializeObject();
        $this->apiKey = $this->youTubeService->getApiKey();
    }

    /**
     * Implode video ids from videos array and unify the pased array
     * Can handle pure video ids, video ids with prefix and video urls
     * e.g.
     * [4 => 'exi0iht_kLw', 5 => 'yt_Vfw1pAmLlY', 'NEW1234' => 'https://youtu.be/jzTVVocFaVE']
     * will result:
     * 'exi0iht_kLw,Vfw1pAmLlY,jzTVVocFaVE'
     * and a unified array:
     * [4 => 'exi0iht_kLw', 5 => 'Vfw1pAmLlY', 'NEW1234' => 'jzTVVocFaVE']
     *
     * @param array $videos
     * @return string
     */
    protected function implodeVideoIdsAndUnifyArray(array &$videos): string
    {
        $videoIds = [];
        foreach ($videos as &$videoId) {
            if (strpos($videoId, 'http') === 0) {
                $videoId = $this->getVideoId($videoId);
            } elseif (strpos(YouTubeService::PLATFORM_PREFIX, $videoId) === 0) {
                $videoId = substr($videoId, strlen(YouTubeService::PLATFORM_PREFIX));
            }
            $videoIds[] = $videoId;
        }
        return implode(',', $videoIds);
    }

    /**
     * Fetches information for all passed $videos and returns the information as an DataHandler
     * compatible array.
     *
     * Creates or updates video records from $videos.
     * Make sure to use
     *  NEW... as array item key for new records OR the record uid for existing records
     *  video id OR video id with prefix OR video url as array item value.
     *
     * e.g.
     * [4 => 'exi0iht_kLw', 5 => 'yt_Vfw1pAmLlY', 'NEW1234' => 'https://youtu.be/jzTVVocFaVE']
     * in this example the records 4 and 5 got updated and a new record
     * for jzTVVocFaVE would be created
     *
     * @param array $videos
     * @param int $pid this will be the pid of NEW records
     * @param string $recordUids reference that includes all UIDs as a comma separated list
     * @param bool $checkExistingVideos if true the video id in combination with the pid will be checked and no
     *                                  new record will be created if a record with the same video id already exists.
     *                                  Existing videos will be added to $recordUids too!
     * @return array the data array for DataHandler. This is a reference so it will be modified and can be used
     *               after method call.
     */
    public function processDataArray(
        array $videos,
        int $pid,
        string &$recordUids = '',
        bool $checkExistingVideos = false
    ): array
    {
        /** @var VideoRepository $videoRepository */
        $videoRepository = $this->objectManager->get(VideoRepository::class);
        $this->videoIds = $this->implodeVideoIdsAndUnifyArray($videos);
        $fetchedVideoInformation = $this->fetchVideoInformation();
        $data = [];
        $recordUidArray = [];
        foreach ($videos as $uid => $videoId) {
            // check if video information for video is in array
            if (is_array($fetchedVideoInformation[$videoId])) {
                $videoInformation = $fetchedVideoInformation[$videoId];
                $existingVideo = null;
                // if true check for a record with the same video id and use it instead of
                // creating a new one
                if ($checkExistingVideos) {
                    $queryResult = $videoRepository->findByVideoId(
                        YouTubeService::PLATFORM_PREFIX . $videoId,
                        $pid
                    );
                    $existingVideo = $queryResult->getFirst();
                }
                $recordUid = $existingVideo ? $existingVideo->getUid() : $uid;
                $recordUidArray[] = $recordUid;
                // $videoInformation is already unified and casted. Add it to the $data array
                $data['tx_mediapool_domain_model_video'][$recordUid] = $videoInformation;
                $data['tx_mediapool_domain_model_video'][$recordUid]['pid'] = $pid;
            } elseif ($fetchedVideoInformation[$videoId] === 'noPermission') {
                // if the video is private or set as not embeddable
                $this->addFlashMessageAndLog(
                    'youTubeVideoImport.missing_youtube_permission.title',
                    'youTubeVideoImport.missing_youtube_permission.message',
                    [$videoId]
                );
            } else {
                // never fetched it ?
                $this->addFlashMessageAndLog(
                    'youTubeVideoImport.missing_video_information.title',
                    'youTubeVideoImport.missing_video_information.message',
                    [$videoId]
                );
                $this->hasError = true;
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
     * Returns the id of passed video link if valid.
     * Otherwise returns false
     *
     * @param string $videoLink
     * @return string empty string if link is not valid
     */
    protected function getVideoId(string $videoLink) : string
    {
        $query = parse_url($videoLink, PHP_URL_QUERY);
        parse_str($query, $parsedQuery);
        preg_match('/https\:\/\/youtu\.be\/(.+)/', $videoLink, $matches);
        if (count($matches) === 2) {
            return $matches[1];
        } elseif (isset($parsedQuery['v'])) {
            return $parsedQuery['v'];
        }
        return '';
    }

    /**
     * Fetches video information for $this->videoIds and returns fetched items
     * as array with casted values
     * from YouTube Data v3 API with parts snippet and player
     * Example on https://developers.google.com/youtube/v3/docs/videos/list
     *
     * @return array with fetched video information
     */
    protected function fetchVideoInformation() : array
    {
        $videoIds = explode(',', $this->videoIds);
        $loops = [];
        $offset = 0;
        // limit the amount of items per request to 50 (api maximum)
        while(count($videoIds)) {
            if (count($videoIds) > 50) {
                $loops[] = implode(',', array_splice($videoIds, $offset, 50));
            } else {
                $loops[] = implode(',', array_splice($videoIds, $offset));
            }
        }

        $items = [];
        foreach ($loops as $videoIds) {
            $items = array_merge($items, $this->doRequest($videoIds));
        }
        return $items;
    }

    /**
     * Request video information for $videoIds
     * recursive call if API provides a nextPageToken
     *
     * @param string $videoIds
     * @param array $items previous items for recursive call - leave it empty
     * @param string $additionalRequestParams
     * @return array
     * @throws \HttpRequestException
     */
    protected function doRequest(string $videoIds, array $items = [], string $additionalRequestParams = ''): array
    {
        $response = $this->client->request(
            'GET',
            sprintf(self::VIDEO_API_URL, $videoIds, $this->apiKey) . $additionalRequestParams
        );
        // ok
        if ($response->getStatusCode() === 200) {
            $result = json_decode($response->getBody()->getContents(), true);
            if (count($result['items'])) {
                foreach ($result['items'] as $item) {
                    // only add video if permissions are ok
                    if ($this->checkVideoPermission($item)) {
                        $items[(string)$item['id']] = $this->getArrayForItem($item);
                    } else {
                        $items[(string)$item['id']] = 'noPermission';
                    }
                }
                // call recursive if nextPageToken is set
                if (isset($result['nextPageToken'])) {
                    $items = self::doRequest($videoIds, $items, '&pageToken=' . $result['nextPageToken']);
                }
            }
            return $items;
            // invalid api key
        } elseif ($response->getStatusCode() === 400) {
            throw new \HttpRequestException(
                sprintf(
                    'Fetching video information for %s failed! Got the following response from YouTube: %s.' .
                    ' Please check your API-key.',
                    $this->video->getLink(),
                    $response->getBody()->getContents()
                ),
                1507792488
            );
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

    /**
     * Get array with casted values for $item
     *
     * @param array $item from API
     * @return array with casted values for passed $item
     */
    protected function getArrayForItem(array $item) : array
    {
        $uploadDate = new \DateTime($item['snippet']['publishedAt']);
        return [
            'link' => 'https://youtu.be/' . (string)$item['id'],
            'title' => (string)$item['snippet']['title'],
            'description' => nl2br((string)$item['snippet']['description']),
            'upload_date' => $uploadDate->getTimestamp(),
            'player_html' => (string)$item['player']['embedHtml'],
            'video_id' => YouTubeService::PLATFORM_PREFIX . (string)$item['id'],
            'thumbnail' => (string)$item['snippet']['thumbnails']['medium']['url'],
            'thumbnail_large' => $this->getLargestThumbnailForVideo($item)
        ];
    }

    /**
     * Get the largest available thumbnail for $item
     *
     * @param array $item from API
     * @return string thumbnail url
     */
    protected function getLargestThumbnailForVideo(array $item) : string
    {
        // in best case we get the maxres thumbnail otherwise use fallback
        // as defined in array
        $keys = ['maxres', 'standard', 'high', 'medium', 'default'];
        foreach ($keys as $key) {
            if (isset($item['snippet']['thumbnails'][$key])) {
                return $item['snippet']['thumbnails'][$key]['url'];
            }
        }
        return '';
    }
}

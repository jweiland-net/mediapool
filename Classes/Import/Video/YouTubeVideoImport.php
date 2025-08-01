<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Import\Video;

use JWeiland\Mediapool\Configuration\ExtConf;
use JWeiland\Mediapool\Domain\Repository\VideoRepository;
use JWeiland\Mediapool\Helper\MessageHelper;
use JWeiland\Mediapool\Import\AbstractImport;
use TYPO3\CMS\Core\Error\Http\StatusException;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

readonly class YouTubeVideoImport extends AbstractImport implements VideoImportInterface
{
    /**
     * Platform prefix. Used for YouTube video id
     */
    private const YOUTUBE_PLATFORM_PREFIX = 'yt_';

    /**
     * URL to fetch video information via GET request
     * player = embedHtml
     * snippet = channelId, title, description, tags and categoryId
     * status = uploadStatus, privacyStatus, ...
     */
    private const VIDEO_API_URL = 'https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=player,snippet,status';

    protected const PLATFORM_NAME = 'YouTube';

    protected const PLATFORM_HOSTS = [
        'https://youtube.com',
        'https://www.youtube.com',
        'https://youtu.be',
    ];

    public function __construct(
        private RequestFactory $requestFactory,
        private VideoRepository $videoRepository,
        private MessageHelper $messageHelper,
        private ExtConf $extConf
    ) {}

    /**
     * Implode video ids from the video array and unify the passed array
     * Can handle pure video ids, video ids with prefix and video urls
     * e.g.
     * [4 => 'exi0iht_kLw', 5 => 'yt_Vfw1pAmLlY', 'NEW1234' => 'https://youtu.be/jzTVVocFaVE']
     * will result:
     * 'exi0iht_kLw,Vfw1pAmLlY,jzTVVocFaVE'
     * and a unified array:
     * [4 => 'exi0iht_kLw', 5 => 'Vfw1pAmLlY', 'NEW1234' => 'jzTVVocFaVE']
     */
    protected function implodeVideoIdsAndUnifyArray(array &$videos): array
    {
        $videoIds = [];
        foreach ($videos as &$video) {
            if (str_starts_with($video['video'], 'http')) {
                // ToDo: add error if getVideoId returns empty string
                $video['video'] = $this->getVideoId($video['video']);
            } elseif (str_starts_with(self::YOUTUBE_PLATFORM_PREFIX, $video['video'])) {
                $video['video'] = substr($video['video'], strlen(self::YOUTUBE_PLATFORM_PREFIX));
            }
            $videoIds[] = $video['video'];
        }

        return $videoIds;
    }

    /**
     * Fetches information for all passed $videos and returns the information as a DataHandler
     * compatible array.
     *
     * Creates or updates video records from $videos.
     * Make sure to use
     *  NEW... as an array item key for new records OR the record uid for existing records
     *  An array with an entry 'video' that contains video link OR video id OR video link
     *  and additionally in the same array an entry 'pid' which contains the pid. The pid
     *  is not mandatory!
     *
     * e.g.
     * [
     *     4 => ['pid' => 3, 'video' => 'exi0iht_kLw'],
     *     5 => ['pid' => 3, 'video' => 'yt_Vfw1pAmLlY'],
     *     'NEW1234' => ['video' => 'https://youtu.be/jzTVVocFaVE']
     * ]
     * in this example the records 4 and 5 got updated and a new record
     * for jzTVVocFaVE would be created
     *
     * @param int $pid this will be the pid of NEW records
     * @param string $recordUids reference that includes all UIDs as a comma separated list
     * @param bool $checkExistingVideos if true the video id in combination with the pid will be checked and no
     *                                  new record will be created if a record with the same video id already exists.
     *                                  Existing videos will be added to $recordUids too!
     * @return array|null the data array for DataHandler. This is a reference, so it will be modified and can be used
     *                    after method call.
     */
    public function processDataArray(
        array $videos,
        int $pid,
        string &$recordUids = '',
        bool $checkExistingVideos = false
    ): ?array {
        $fetchedVideoInformation = $this->fetchVideoInformation(
            $this->implodeVideoIdsAndUnifyArray($videos)
        );
        $data = [];
        $recordUidArray = [];

        foreach ($videos as $uid => $video) {
            $videoId = $video['video'];
            $pid = (int)($video['pid'] ?? $pid);

            // check if video information for video is in the array
            if (is_array($fetchedVideoInformation[$videoId])) {
                $videoInformation = $fetchedVideoInformation[$videoId];
                $existingVideo = null;
                // if true, check for a record with the same video id and use it instead of
                // creating a new one
                if ($checkExistingVideos) {
                    $queryResult = $this->videoRepository->findByVideoId(
                        self::YOUTUBE_PLATFORM_PREFIX . $videoId,
                        $pid,
                    );
                    $existingVideo = $queryResult->getFirst();
                }

                $recordUid = $existingVideo ? $existingVideo->getUid() : $uid;
                $recordUidArray[] = $recordUid;
                // $videoInformation is already unified and casted. Add it to the $data array
                $data['tx_mediapool_domain_model_video'][$recordUid] = $videoInformation;

                // add pid on new records
                if (is_string($recordUid) && str_starts_with($recordUid, 'NEW')) {
                    $data['tx_mediapool_domain_model_video'][$recordUid]['pid'] = $pid;
                }
            } elseif ($fetchedVideoInformation[$videoId] === 'noPermission') {
                // if the video is private or set as not embeddable
                $this->messageHelper->addFlashMessage(
                    LocalizationUtility::translate(
                        'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:youTubeVideoImport.missing_youtube_permission.message',
                        null,
                        [$videoId],
                    ),
                    LocalizationUtility::translate(
                        'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:youTubeVideoImport.missing_youtube_permission.title',
                    ),
                    ContextualFeedbackSeverity::ERROR,
                );
                return null;
            } else {
                // never fetched it
                $this->messageHelper->addFlashMessage(
                    LocalizationUtility::translate(
                        'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:youTubeVideoImport.missing_video_information.message',
                        null,
                        [$videoId],
                    ),
                    LocalizationUtility::translate(
                        'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:youTubeVideoImport.missing_video_information.title',
                    ),
                    ContextualFeedbackSeverity::ERROR,
                );
                return null;
            }
        }

        $recordUids = implode(',', $recordUidArray);

        return $data;
    }

    /**
     * Checks a video for privacy status and embeddable
     *
     * @return bool true if permissions are ok otherwise false
     */
    protected function checkVideoPermission(array $item): bool
    {
        $hasPermission = true;

        // if we can't watch or embed the video, throw exception
        if (
            $item['status']['privacyStatus'] === 'private'
            || $item['status']['embeddable'] == false
        ) {
            $hasPermission = false;
        }

        return $hasPermission;
    }

    /**
     * Returns the id of a passed video link if valid.
     * Otherwise, returns false
     *
     * @return string empty string if the link is not valid
     */
    protected function getVideoId(string $videoLink): string
    {
        $parsedUrl = parse_url($videoLink);
        $videoId = '';
        if (array_key_exists('query', $parsedUrl)) {
            parse_str($parsedUrl['query'], $parsedQuery);
            $videoId = $parsedQuery['v'] ?? '';
        }

        return $videoId ?: substr($parsedUrl['path'] ?? '/', 1);
    }

    /**
     * Fetches video information for $this->videoIds and returns fetched items
     * as an array with casted values
     * from YouTube Data v3 API with parts snippet and player
     * Example on https://developers.google.com/youtube/v3/docs/videos/list
     *
     * @return array with fetched video information
     */
    protected function fetchVideoInformation(array $videoIds): array
    {
        $items = [];
        foreach (array_chunk($videoIds, 50) as $chunkWithVideoIds) {
            $items[] = $this->doRequest($chunkWithVideoIds);
        }

        return array_merge(...$items);
    }

    /**
     * Request video information for $videoIds
     * recursive call if API provides a nextPageToken
     *
     * @param array $items previous items for recursive call - leave it empty
     * @throws StatusException
     */
    protected function doRequest(array $videoIds, array $items = [], string $additionalRequestParams = ''): array
    {
        $response = $this->requestFactory->request(
            sprintf(
                self::VIDEO_API_URL . $additionalRequestParams,
                implode(',', $videoIds),
                $this->extConf->getYoutubeDataApiKey(),
            ),
        );

        if ($response->getStatusCode() === 200) {
            $result = json_decode((string)$response->getBody(), true);
            if (is_array($result['items'])) {
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
                    $items = $this->doRequest($videoIds, $items, '&pageToken=' . $result['nextPageToken']);
                }
            }

            // invalid api key
            return $items;
        }

        if ($response->getStatusCode() === 400) {
            throw new StatusException(
                sprintf(
                    'Fetching video information for %s failed! Got the following response from YouTube: %s.' .
                    ' Please check your API-key.',
                    implode(',', $videoIds),
                    $response->getBody(),
                ),
                1507792488,
            );
        }

        throw new StatusException(
            sprintf(
                'Fetching video information for %s failed! Got status code %d and the following response: %s',
                implode(',', $videoIds),
                $response->getStatusCode(),
                $response->getBody(),
            ),
            1507794777,
        );
    }

    /**
     * Get an array with casted values for $item
     *
     * @param array $item from API
     * @return array with casted values for passed $item
     */
    protected function getArrayForItem(array $item): array
    {
        $uploadDate = new \DateTime($item['snippet']['publishedAt']);

        return [
            'link' => 'https://youtu.be/' . $item['id'],
            'title' => (string)$item['snippet']['title'],
            'description' => nl2br((string)$item['snippet']['description']),
            'upload_date' => $uploadDate->getTimestamp(),
            'player_html' => (string)$item['player']['embedHtml'],
            'video_id' => self::YOUTUBE_PLATFORM_PREFIX . $item['id'],
            'thumbnail' => (string)$item['snippet']['thumbnails']['medium']['url'],
            'thumbnail_large' => $this->getLargestThumbnailForVideo($item),
        ];
    }

    /**
     * Get the largest available thumbnail for $item
     *
     * @param array $item from API
     * @return string thumbnail url
     */
    protected function getLargestThumbnailForVideo(array $item): string
    {
        // in the best case we get the maxres thumbnail otherwise use fallback
        // as defined in the array
        $keys = ['maxres', 'standard', 'high', 'medium', 'default'];
        foreach ($keys as $key) {
            if (isset($item['snippet']['thumbnails'][$key])) {
                return $item['snippet']['thumbnails'][$key]['url'];
            }
        }

        return '';
    }
}

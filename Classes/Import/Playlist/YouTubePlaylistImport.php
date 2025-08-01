<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Import\Playlist;

use JWeiland\Mediapool\Configuration\ExtConf;
use JWeiland\Mediapool\Helper\MessageHelper;
use JWeiland\Mediapool\Import\AbstractImport;
use JWeiland\Mediapool\Import\Video\YouTubeVideoImport;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Error\Http\StatusException;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

readonly class YouTubePlaylistImport extends AbstractImport implements PlaylistImportInterface
{
    /**
     * URL to fetch playlist items via GET request
     */
    private const PLAYLIST_ITEMS_API_URL = 'https://www.googleapis.com/youtube/v3/playlistItems?playlistId=%s&key=%s&part=contentDetails,status&maxResults=50';

    /**
     * URL to fetch playlist title via GET request
     */
    private const PLAYLIST_API_URL = 'https://www.googleapis.com/youtube/v3/playlists?id=%s&key=%s&part=snippet';

    /**
     * URL to fetch channel data (uploads playlist, ...) via GET request
     */
    private const CHANNELS_LIST_API_URL = 'https://www.googleapis.com/youtube/v3/channels?key=%s&part=contentDetails&%s';

    /**
     * Name of the video platform
     */
    protected const PLATFORM_NAME = 'YouTube';

    protected const PLATFORM_HOSTS = [
        'https://youtube.com',
        'https://www.youtube.com',
        'https://youtu.be',
    ];

    public function __construct(
        private YouTubeVideoImport $youTubeVideoImport,
        private RequestFactory $requestFactory,
        private MessageHelper $messageHelper,
        private ExtConf $extConf
    ) {}

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
        if (!($playlistId = $this->getPlaylistId($playlistLink))) {
            $this->messageHelper->addFlashMessage(
                LocalizationUtility::translate(
                    'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:youTubePlaylistImport.invalid_id.message',
                    null,
                    [$playlistLink],
                ),
                LocalizationUtility::translate(
                    'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:youTubePlaylistImport.invalid_id.title',
                ),
                ContextualFeedbackSeverity::ERROR,
            );
            return [];
        }

        $information = $this->fetchPlaylistInformation($playlistId);
        $videos = $this->fetchPlaylistItems($playlistId);
        $videoIds = [];

        $i = 0;
        foreach ($videos as $item) {
            if (isset($item['status']['privacyStatus']) && $item['status']['privacyStatus'] === 'private') {
                // skip private videos
                continue;
            }
            $videoIds['NEW' . $i] = [
                'pid' => $pid,
                'video' => trim($item['contentDetails']['videoId']),
            ];
            $i++;
        }

        $recordUids = '';
        $data = $this->youTubeVideoImport->processDataArray($videoIds, $pid, $recordUids, true);

        // On error return empty array
        if ($data === null) {
            return [];
        }

        return [
            'fieldArray' => [
                'pid' => $pid,
                'link' => $playlistLink,
                'playlist_id' => 'yt_' . $playlistId,
                'title' => (string)$information[0]['snippet']['title'],
                'thumbnail' => (string)$information[0]['snippet']['thumbnails']['medium']['url'],
                'thumbnail_large' => (string)$information[0]['snippet']['thumbnails']['standard']['url'],
                'videos' => $recordUids,
            ],
            'dataHandler' => $data,
        ];
    }

    /**
     * Returns the id of a playlist link.
     * Otherwise, returns false
     */
    protected function getPlaylistId(string $playlistLink): string
    {
        if ($playlistLink === '') {
            return '';
        }

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

        $response = $this->requestFactory->request(
            sprintf(
                self::CHANNELS_LIST_API_URL,
                $this->extConf->getYoutubeDataApiKey(),
                $channelParam,
            ),
        );

        if ($response->getStatusCode() === 200) {
            $result = json_decode((string)$response->getBody(), true);
            if (isset($result['items'][0]['contentDetails']['relatedPlaylists']['uploads'])) {
                $playlistId = $result['items'][0]['contentDetails']['relatedPlaylists']['uploads'];
            }
        } else {
            $this->checkResponseStatusCode($response, $playlistId);
        }

        return $playlistId;
    }

    /**
     * Checks the response status code
     *
     * @throws StatusException
     */
    protected function checkResponseStatusCode(ResponseInterface $response, string $playlistId): void
    {
        // invalid api key
        if ($response->getStatusCode() === 400) {
            throw new StatusException(
                sprintf(
                    'Fetching playlist information for %s failed! Got the following response from YouTube: %s.' .
                    ' Please check your API-key.',
                    $playlistId,
                    $response->getBody(),
                ),
                1508146718,
            );
        }

        throw new StatusException(
            sprintf(
                'Fetching playlist information for %s failed! Got status code %d and the' .
                ' following response: %s',
                $playlistId,
                $response->getStatusCode(),
                $response->getBody(),
            ),
            1508146719,
        );
    }

    /**
     * Fetches playlist items from YouTube Data v3 API
     * returns a merged item array from YouTube Data v3 API
     *
     * @param array $items leave it empty
     * @param string $additionalRequestParams additional request parameters
     * @return array empty array on error
     */
    protected function fetchPlaylistItems(
        string $playlistId,
        array $items = [],
        string $additionalRequestParams = '',
    ): array {
        $response = $this->requestFactory->request(
            sprintf(
                self::PLAYLIST_ITEMS_API_URL . $additionalRequestParams,
                $playlistId,
                $this->extConf->getYoutubeDataApiKey(),
            ),
        );

        if ($response->getStatusCode() === 200) {
            $result = json_decode((string)$response->getBody(), true);
            if (count($result['items'])) {
                foreach ($result['items'] as $item) {
                    $items[] = $item;
                }
                // call recursive if nextPageToken is set
                if (isset($result['nextPageToken'])) {
                    $items = self::fetchPlaylistItems(
                        $playlistId,
                        $items,
                        '&pageToken=' . $result['nextPageToken']
                    );
                }
                return $items;
            }
        } else {
            $this->checkResponseStatusCode($response, $playlistId);
        }

        return [];
    }

    /**
     * Fetches playlist information like title and thumbnail
     * returns item array from YouTube Data v3 API
     *
     * @return array empty array on error
     */
    protected function fetchPlaylistInformation(string $playlistId): array
    {
        $response = $this->requestFactory->request(
            sprintf(
                self::PLAYLIST_API_URL,
                $playlistId,
                $this->extConf->getYoutubeDataApiKey(),
            ),
        );

        if ($response->getStatusCode() === 200) {
            $result = json_decode((string)$response->getBody(), true);
            if (!empty($result['items'])) {
                return $result['items'];
            }
        } else {
            $this->checkResponseStatusCode($response, $playlistId);
        }

        return [];
    }
}

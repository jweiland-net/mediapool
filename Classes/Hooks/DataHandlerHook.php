<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Hooks;

use JWeiland\Mediapool\Configuration\Exception\MissingYouTubeApiKeyException;
use JWeiland\Mediapool\Service\PlaylistService;
use JWeiland\Mediapool\Service\VideoService;
use JWeiland\Mediapool\Traits\GetFlashMessageQueueTrait;
use JWeiland\Mediapool\Traits\GetPlaylistRepositoryTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\SysLog\Action\Database as SystemLogDatabaseAction;
use TYPO3\CMS\Core\SysLog\Error as SystemLogErrorClassification;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class to get VideoData from a external video service
 * e.g. YouTube
 */
class DataHandlerHook implements LoggerAwareInterface
{
    use LoggerAwareTrait;
    use GetFlashMessageQueueTrait;
    use GetPlaylistRepositoryTrait;

    public const TABLE_VIDEO = 'tx_mediapool_domain_model_video';
    public const TABLE_PLAYLIST = 'tx_mediapool_domain_model_playlist';

    /**
     * @var DataHandler
     */
    protected $dataHandler;

    /**
     * @var PlaylistService
     */
    private $playlistService;

    /**
     * @var VideoService
     */
    private $videoService;

    public function __construct(PlaylistService $playlistService, VideoService $videoService)
    {
        $this->playlistService = $playlistService;
        $this->videoService = $videoService;
    }

    /**
     * Using DataHandler hook to fetch and insert video information
     * for tx_mediapool_domain_model_video
     */
    public function processDatamap_beforeStart(DataHandler $dataHandler): void
    {
        if (
            array_key_exists(self::TABLE_VIDEO, $dataHandler->datamap)
            || array_key_exists(self::TABLE_PLAYLIST, $dataHandler->datamap)
        ) {
            $this->dataHandler = $dataHandler;
        }

        try {
            $uid = 0;
            $table = '';

            if (
                array_key_exists(self::TABLE_VIDEO, $dataHandler->datamap) &&
                !array_key_exists(self::TABLE_PLAYLIST, $dataHandler->datamap)
            ) {
                // save single video
                $table = self::TABLE_VIDEO;
                $this->processVideos($dataHandler->datamap[self::TABLE_VIDEO]);
            } elseif (array_key_exists(self::TABLE_PLAYLIST, $dataHandler->datamap)) {
                // save playlist
                $table = self::TABLE_PLAYLIST;
                foreach ($dataHandler->datamap[self::TABLE_PLAYLIST] as $uid => &$fields) {
                    $this->processPlaylist($uid, $fields);
                }
            }
        } catch (MissingYouTubeApiKeyException $missingYouTubeApiKeyException) {
            $this->dataHandler->log(
                $table,
                $uid,
                SystemLogDatabaseAction::UPDATE,
                0,
                SystemLogErrorClassification::USER_ERROR,
                $missingYouTubeApiKeyException->getMessage(),
                -1
            );

            $flashMessage = GeneralUtility::makeInstance(
                FlashMessage::class,
                $missingYouTubeApiKeyException->getMessage(),
                'Missing YouTube API key',
                AbstractMessage::ERROR,
                true
            );

            $this->getFlashMessageQueue()->addMessage($flashMessage);
        } catch (\Exception $e) {
            $flashMessage = GeneralUtility::makeInstance(
                FlashMessage::class,
                LocalizationUtility::translate(
                    'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:data_handler.exception.message',
                    null,
                    [$e->getCode()]
                ),
                LocalizationUtility::translate(
                    'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:data_handler.exception.title'
                ),
                AbstractMessage::ERROR,
                true
            );

            $this->getFlashMessageQueue()->addMessage($flashMessage);

            $this->logger->error(
                'Exception while running DataHandler hook from ext:mediapool: ' . $e->getMessage() .
                '(' . $e->getCode() . ') ' . $e->getFile() . ' Line: ' . $e->getLine()
            );

            // Prevent DataHandler from saving
            $this->dataHandler->datamap = [];
        }
    }

    protected function processVideos(array $dataHandlerVideoTable)
    {
        $videos = [];
        foreach ($dataHandlerVideoTable as $uid => $fields) {
            $videos[$uid] = ['video' => $fields['link']];
            if ($fields['pid']) {
                $videos[$uid]['pid'] = (int)$fields['pid'];
            }
        }

        // use current pid as video pid
        $data = $this->videoService->getVideoData($videos, (int)GeneralUtility::_POST('popViewId'));
        if ($data) {
            foreach ($data[self::TABLE_VIDEO] as $uid => &$fields) {
                // override pid if declared in original field array
                if (isset($dataHandlerVideoTable[$uid]['pid'])) {
                    $fields['pid'] = (int)$dataHandlerVideoTable[$uid]['pid'];
                }
            }
            unset($fields);
            ArrayUtility::mergeRecursiveWithOverrule($this->dataHandler->datamap, $data);
        } else {
            // Prevent DataHandler from saving because we don´t have data to save :(
            $this->dataHandler->datamap = [];
        }
    }

    /**
     * @param int|string $uid of the playlist
     */
    protected function processPlaylist($uid, array &$fieldArray): void
    {
        $pid = (int)($fieldArray['pid'] ?? 0);
        if ($pid === 0) {
            $pid = $this->getPlaylistRepository()->findPidByUid($uid);
        }

        $data = $this->playlistService->getPlaylistData($fieldArray['link'] ?? '', $pid);

        if ($data !== []) {
            ArrayUtility::mergeRecursiveWithOverrule($fieldArray, $data['fieldArray']);
            ArrayUtility::mergeRecursiveWithOverrule($this->dataHandler->datamap, $data['dataHandler']);
        } else {
            // Prevent from saving because the video object has no video information
            $this->dataHandler->datamap = [];
        }
    }
}

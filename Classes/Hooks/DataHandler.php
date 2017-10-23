<?php
namespace JWeiland\Mediapool\Hooks;

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

use Codeception\Coverage\Subscriber\Local;
use JWeiland\Mediapool\Domain\Model\Video;
use JWeiland\Mediapool\Domain\Repository\PlaylistRepository;
use JWeiland\Mediapool\Import\NoApiKeyException;
use JWeiland\Mediapool\Import\Playlist\InvalidPlaylistIdException;
use JWeiland\Mediapool\Import\Video\InvalidVideoIdException;
use JWeiland\Mediapool\Import\Video\VideoPermissionException;
use JWeiland\Mediapool\Service\PlaylistService;
use JWeiland\Mediapool\Service\VideoService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class to get VideoData from a external video service
 * e.g. YouTube
 *
 * @package JWeiland\Mediapool\Hooks;
 */
class DataHandler
{
    /**
     * Table names
     */
    const TABLE_VIDEO = 'tx_mediapool_domain_model_video';
    const TABLE_PLAYLIST = 'tx_mediapool_domain_model_playlist';

    /**
     * Data Handler
     *
     * @var \TYPO3\CMS\Core\DataHandling\DataHandler
     */
    protected $dataHandler;

    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectMananger;

    /**
     * Flash Message Queue
     *
     * @var FlashMessageQueue
     */
    protected $flashMessageQueue;

    /**
     * Using DataHandler hook to fetch and insert video information
     * for tx_mediapool_domain_model_video
     *
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
     * @return void
     */
    public function processDatamap_beforeStart(
        \TYPO3\CMS\Core\DataHandling\DataHandler $dataHandler
    ) {
        // initialize
        if (
            array_key_exists(self::TABLE_VIDEO, $dataHandler->datamap) ||
            array_key_exists(self::TABLE_PLAYLIST, $dataHandler->datamap)
        ) {
            $this->dataHandler = $dataHandler;
            $this->objectMananger = GeneralUtility::makeInstance(ObjectManager::class);
            /** @var FlashMessageService $flashMessageService */
            $flashMessageService = $this->objectMananger->get(FlashMessageService::class);
            $this->flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
        }

        // save single video
        if (
            array_key_exists(self::TABLE_VIDEO, $dataHandler->datamap) &&
            !array_key_exists(self::TABLE_PLAYLIST, $dataHandler->datamap)
        ) {
            foreach ($dataHandler->datamap[self::TABLE_VIDEO] as &$fields) {
                // process single video and break on error
                if ($this->_processSingleVideo($fields) === false) {
                    break;
                }
            }
            // save playlist
        } elseif (array_key_exists(self::TABLE_PLAYLIST, $dataHandler->datamap)) {
            foreach ($dataHandler->datamap[self::TABLE_PLAYLIST] as $uid => &$fields) {
                // process playlist and break on error
                if ($this->processPlaylist($uid, $fields) === false) {
                    break;
                }
            }
        }
    }

    protected function processSingleVideo(array &$fieldArray): bool
    {
        // todo: use new method processDataArray instead of getFilledVideoObject
    }

    /**
     * Process tx_mediapool_domain_model_video object
     *
     * @param array $fieldArray
     * @return bool true on success otherwise false
     * @throws \Exception if unknown exception was thrown
     */
    protected function _processSingleVideo(array &$fieldArray): bool
    {
        $success = true;
        /** @var VideoService $videoService */
        $videoService = $this->objectMananger->get(VideoService::class);
        $video = new Video();
        $video->setLink($fieldArray['link']);
        try {
            $video = $videoService->getFilledVideoObject($video);
        } catch (\Exception $exception) {
            // catch exceptions and show create flash messages
            switch (get_class($exception)) {
                case NoApiKeyException::class:
                case \HttpRequestException::class:
                    $translationKey = 'contact_administrator';
                    break;
                case InvalidVideoIdException::class:
                    $translationKey = 'invalid_video_exception';
                    break;
                default:
                    throw $exception;
                    break;
            }
            /** @var FlashMessage $flashMessage */
            $flashMessage = GeneralUtility::makeInstance(
                FlashMessage::class,
                $this->translateError('data_handler.' . $translationKey . '.message', [$exception->getCode()]),
                $this->translateError('data_handler.' . $translationKey . '.title'),
                FlashMessage::ERROR
            );
            $this->flashMessageQueue->addMessage($flashMessage);
            GeneralUtility::sysLog(
                'There was an exception while processing single video: ' . $exception->getMessage() .
                '(' . $exception->getCode() . ')',
                'mediapool',
                GeneralUtility::SYSLOG_SEVERITY_ERROR
            );
            $this->dataHandler->datamap = [];
            return false;
        }
        if (is_object($video)) {
            $fieldArray['link'] = $video->getLink();
            $fieldArray['title'] = $video->getTitle();
            $fieldArray['description'] = $video->getDescription();
            $fieldArray['upload_date'] = $video->getUploadDate()->getTimestamp();
            $fieldArray['video_id'] = $video->getVideoId();
            $fieldArray['player_html'] = $video->getPlayerHtml();
            $fieldArray['thumbnail'] = $video->getThumbnail();
        } else {
            // Add error message
            $flashMessage = GeneralUtility::makeInstance(
                FlashMessage::class,
                $this->translateError('video_service.video.' . $video . '.body'),
                $this->translateError('video_service.video.' . $video . '.head'),
                FlashMessage::ERROR
            );
            $this->flashMessageQueue->addMessage($flashMessage);
            // Prevent from saving because the video object has no video
            // information
            $this->dataHandler->datamap = [];
            $success = false;
        }
        return $success;
    }

    /**
     * Process tx_mediapool_domain_model_playlist object
     *
     * @param int|string $uid of the playlist
     * @param array $fieldArray
     * @return bool true on success otherwise false
     * @throws \Exception if unknown exception was thrown
     */
    protected function processPlaylist($uid, array &$fieldArray): bool
    {
        $success = true;
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var PlaylistService $playlistService */
        $playlistService = $objectManager->get(PlaylistService::class);
        if (!($pid = $fieldArray['pid'])) {
            $playlistRepository = $objectManager->get(PlaylistRepository::class);
            $pid = $playlistRepository->findPidByUid($uid);
        }
        try {
            $data = $playlistService->getPlaylistData($fieldArray['link'], $pid);
        } catch (\Exception $exception) {
            // catch exceptions and show create flash messages
            switch (get_class($exception)) {
                case NoApiKeyException::class:
                case \HttpRequestException::class:
                    $translationKey = 'contact_administrator';
                    break;
                case InvalidPlaylistIdException::class:
                    $translationKey = 'invalid_playlist_exception';
                    break;
                default:
                    throw $exception;
                    break;
            }
            /** @var FlashMessage $flashMessage */
            $flashMessage = GeneralUtility::makeInstance(
                FlashMessage::class,
                $this->translateError('data_handler.' . $translationKey . '.message', [$exception->getCode()]),
                $this->translateError('data_handler.' . $translationKey . '.title'),
                FlashMessage::ERROR
            );
            $this->flashMessageQueue->addMessage($flashMessage);
            GeneralUtility::sysLog(
                'There was an exception while processing playlist: ' . $exception->getMessage() .
                '(' . $exception->getCode() . ')',
                'mediapool',
                GeneralUtility::SYSLOG_SEVERITY_ERROR
            );
            $this->dataHandler->datamap = [];
            return false;
        }
        if (is_array($data)) {
            ArrayUtility::mergeRecursiveWithOverrule($fieldArray, $data['fieldArray']);
            ArrayUtility::mergeRecursiveWithOverrule($this->dataHandler->datamap, $data['dataHandler']);
        } else {
            // Add error message
            $flashMessage = GeneralUtility::makeInstance(
                FlashMessage::class,
                $this->translateError('video_service.playlist.' . $data . '.body'),
                $this->translateError('video_service.playlist.' . $data . '.head'),
                FlashMessage::ERROR
            );
            $this->flashMessageQueue->addMessage($flashMessage);
            // Prevent from saving because the video object has no video
            // information
            $this->dataHandler->datamap = [];
            $success = false;
        }
        return $success;
    }

    /**
     * Translate error message
     *
     * @param string $key
     * @param array $arguments
     * @return NULL|string
     */
    protected function translateError(string $key, array $arguments = [])
    {
        return LocalizationUtility::translate(
            'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:' . $key,
            'mediapool',
            $arguments
        );
    }
}

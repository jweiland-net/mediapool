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

use JWeiland\Mediapool\Domain\Repository\PlaylistRepository;
use JWeiland\Mediapool\Service\PlaylistService;
use JWeiland\Mediapool\Service\VideoService;
use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogManager;
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
     * Logger
     *
     * @var Logger
     */
    protected $logger;

    /**
     * DataHandler constructor.
     */
    public function __construct()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
    }

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

        try {
            // save single video
            if (
                array_key_exists(self::TABLE_VIDEO, $dataHandler->datamap) &&
                !array_key_exists(self::TABLE_PLAYLIST, $dataHandler->datamap)
            ) {
                $this->processVideos($dataHandler->datamap[self::TABLE_VIDEO]);
                // save playlist
            } elseif (array_key_exists(self::TABLE_PLAYLIST, $dataHandler->datamap)) {
                foreach ($dataHandler->datamap[self::TABLE_PLAYLIST] as $uid => &$fields) {
                    // process playlist and break on error
                    if ($this->processPlaylist($uid, $fields) === false) {
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            /** @var FlashMessage $flashMessage */
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
                FlashMessage::ERROR,
                [$e->getCode()]
            );
            $this->flashMessageQueue->addMessage($flashMessage);
            $this->logger->error(
                'Exception while running DataHandler hook from ext:mediapool: ' . $e->getMessage() .
                '(' . $e->getCode() . ') ' . $e->getFile() . ' Line: ' . $e->getLine()
            );
            // Prevent DataHandler from saving
            $this->dataHandler->datamap = [];
        }
    }

    /**
     * Process videos
     *
     * @param array $dataHandlerVideoTable
     * @return void
     */
    protected function processVideos(array $dataHandlerVideoTable)
    {
        $videos = [];
        foreach ($dataHandlerVideoTable as $uid => $fields) {
            $videos[$uid] = ['video' => $fields['link']];
            if ($fields['pid']) {
                $videos[$uid]['pid'] = (int)$fields['pid'];
            }
        }
        /** @var VideoService $videoService */
        $videoService = $this->objectMananger->get(VideoService::class);
        // use current pid as video pid
        $data = $videoService->getVideoData($videos, (int)GeneralUtility::_POST('popViewId'));
        if ($data) {
            foreach ($data[self::TABLE_VIDEO] as $uid => &$fields) {
                // override pid if declared in original field array
                if (isset($dataHandlerVideoTable[$uid]['pid'])) {
                    $fields['pid'] = (int)$dataHandlerVideoTable[$uid]['pid'];
                }
            }
            ArrayUtility::mergeRecursiveWithOverrule($this->dataHandler->datamap, $data);
        } else {
            // Prevent DataHandler from saving because we don´t have data to save :(
            $this->dataHandler->datamap = [];
        }
    }

    /**
     * Process playlist
     *
     * @param int|string $uid of the playlist
     * @param array $fieldArray
     * @return void
     */
    protected function processPlaylist($uid, array &$fieldArray)
    {
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var PlaylistService $playlistService */
        $playlistService = $objectManager->get(PlaylistService::class);
        if (!($pid = $fieldArray['pid'])) {
            $playlistRepository = $objectManager->get(PlaylistRepository::class);
            $pid = $playlistRepository->findPidByUid($uid);
        }
        $data = $playlistService->getPlaylistData($fieldArray['link'], $pid);
        if ($data) {
            ArrayUtility::mergeRecursiveWithOverrule($fieldArray, $data['fieldArray']);
            ArrayUtility::mergeRecursiveWithOverrule($this->dataHandler->datamap, $data['dataHandler']);
        } else {
            // Prevent from saving because the video object has no video information
            $this->dataHandler->datamap = [];
        }
    }
}

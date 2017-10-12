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

use JWeiland\Mediapool\Domain\Model\Video;
use JWeiland\Mediapool\Service\VideoService;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
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
     * Using DataHandler hook to fetch and insert video information
     * for tx_mediapool_domain_model_video
     *
     * @param array $fieldArray
     * @param string $table
     * @param int|string $id
     * @param \TYPO3\CMS\Core\DataHandling\DataHandler $parentObject
     * @return void
     */
    public function processDatamap_preProcessFieldArray(
        array &$fieldArray,
        string $table,
        $id,
        \TYPO3\CMS\Core\DataHandling\DataHandler $parentObject
    ) {
        if ($table === 'tx_mediapool_domain_model_video') {
            /** @var ObjectManager $objectManager */
            $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
            /** @var VideoService $videoService */
            $videoService = $objectManager->get(VideoService::class);
            /** @var FlashMessageService $flashMessageService */
            $flashMessageService = $objectManager->get(FlashMessageService::class);
            $flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
            $video = new Video();
            $video->setLink($fieldArray['link']);
            $video = $videoService->getFilledVideoObject($video);
            if (is_object($video)) {
                $fieldArray['link'] = $video->getLink();
                $fieldArray['title'] = $video->getTitle();
                $fieldArray['description'] = $video->getDescription();
                $fieldArray['upload_date'] = $video->getUploadDate()->getTimestamp();
                $fieldArray['player_html'] = $video->getPlayerHTML();
            } else {
                // Add error message
                $flashMessage = GeneralUtility::makeInstance(
                    FlashMessage::class,
                    LocalizationUtility::translate('video_service.error_message.' . $video . '.body', 'mediapool'),
                    LocalizationUtility::translate('video_service.error_message.' . $video . '.head', 'mediapool'),
                    FlashMessage::ERROR
                );
                $flashMessageQueue->addMessage($flashMessage);
                // Prevent from saving because the video object has no video
                // information
                $fieldArray = [];
            }
        }
    }
}

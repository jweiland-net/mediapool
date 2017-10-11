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

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * Class to get VideoData from a external video service
 * e.g. YouTube
 *
 * @package JWeiland\Mediapool\Hooks;
 */
class DataHandler
{
    /**
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
        DebuggerUtility::var_dump($fieldArray, 'fieldArray');
        DebuggerUtility::var_dump($table, 'table');
        $fieldArray = [];
        /** @var ObjectManager $objectManager */
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        /** @var FlashMessageService $flashMessageService */
        $flashMessageService = $objectManager->get(FlashMessageService::class);
        $flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();

        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            'Test',
            'Header',
            FlashMessage::ERROR
        );

        $flashMessageQueue->addMessage($flashMessage);
    }
}

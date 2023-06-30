<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool;

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class AbstractBase
 * Base class for AbstractImport, VideoService and PlaylistService
 */
abstract class AbstractBase
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var string
     */
    protected $errorMessagesFile = 'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf';

    /**
     * @var FlashMessageQueue
     */
    protected $flashMessageQueue;

    public function injectObjectManager(ObjectManager $objectManager): void
    {
        $this->objectManager = $objectManager;
    }

    public function initializeObject(): void
    {
        $flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
        $this->flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
    }

    /**
     * Add flash message
     *
     * Please notice: $title and $message are trans-unit ids and can not be used without language file.
     * You can override the language file with $this->errorMessagesFile = '<EXT:your_ext/...>'.
     */
    public function addFlashMessageAndLog(
        string $title,
        string $message,
        array $messageArguments = []
    ): void {
        $title = LocalizationUtility::translate($this->errorMessagesFile . ':' . $title) ?? '[no-title]';
        $message = LocalizationUtility::translate($this->errorMessagesFile . ':' . $message, '', $messageArguments) ?? '[no-message]';
        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $message,
            $title,
            AbstractMessage::ERROR
        );
        $this->flashMessageQueue->addMessage($flashMessage);
    }
}

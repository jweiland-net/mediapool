<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool;

use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Log\LogManager;
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
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Language file for error messages
     *
     * @var string
     */
    protected $errorMessagesFile = 'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf';

    /**
     * FlashMessage Queue
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
     * inject objectManager
     *
     * @param ObjectManager $objectManager
     */
    public function injectObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Initialize object
     */
    public function initializeObject()
    {
        $this->logger = GeneralUtility::makeInstance(LogManager::class)->getLogger(__CLASS__);
        $flashMessageService = $this->objectManager->get(FlashMessageService::class);
        $this->flashMessageQueue = $flashMessageService->getMessageQueueByIdentifier();
    }

    /**
     * Add flash message and log entry
     * Please notice: $title and $message are trans-unit ids and can not be used without language file.
     * You can override the language file with $this->errorMessagesFile = '<EXT:your_ext/...>'.
     * A log entry only will be created if $logMessage is filled with text!
     *
     * @param string $title trans-unit id for title
     * @param string $message trans-unit id for title
     * @param array $messageArguments arguments for message trans-unit
     * @param int $flashMessageSeverity FlashMessage::<ERROR|OK|...>
     * @param int $logLevel use LogLevel::<...> constants
     * @param string $logMessage If a custom log message is set, a log entry will be created
     * @param array $logMessageArguments arguments for log message
     */
    public function addFlashMessageAndLog(
        string $title,
        string $message,
        array $messageArguments = [],
        int $flashMessageSeverity = FlashMessage::ERROR,
        int $logLevel = LogLevel::ERROR,
        string $logMessage = '',
        array $logMessageArguments = []
    ) {
        $title = LocalizationUtility::translate($this->errorMessagesFile . ':' . $title);
        $message = LocalizationUtility::translate($this->errorMessagesFile . ':' . $message, '', $messageArguments);
        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $message,
            $title,
            $flashMessageSeverity
        );
        $this->flashMessageQueue->addMessage($flashMessage);
        if ($logMessage) {
            $this->logger->log($logLevel, $logMessage, $logMessageArguments);
        }
    }
}

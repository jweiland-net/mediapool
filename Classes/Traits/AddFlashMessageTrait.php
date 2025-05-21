<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Traits;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Trait to provide a method to add FlashMessages to queue
 */
trait AddFlashMessageTrait
{
    use GetFlashMessageQueueTrait;

    private const LANGUAGE_FILE = 'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf';

    /**
     * Add a flash message
     *
     * Please notice: $title and $message are trans-unit ids and can not be used without language file.
     * You can override the language file with $this->errorMessagesFile = '<EXT:your_ext/...>'.
     */
    public function addFlashMessage(
        string $title,
        string $message,
        array $messageArguments = []
    ): void {
        $title = LocalizationUtility::translate(self::LANGUAGE_FILE . ':' . $title);
        $message = LocalizationUtility::translate(
            self::LANGUAGE_FILE . ':' . $message,
            '',
            $messageArguments,
        );

        $this->getFlashMessageQueue()->addMessage(GeneralUtility::makeInstance(
            FlashMessage::class,
            $message ?? '[no-message]',
            $title ?? '[no-title]',
            ContextualFeedbackSeverity::ERROR,
        ));
    }
}

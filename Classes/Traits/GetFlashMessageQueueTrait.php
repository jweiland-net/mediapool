<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/glossary2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Traits;

use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Trait to get the current flash message queue
 */
trait GetFlashMessageQueueTrait
{
    private function getFlashMessageQueue(): FlashMessageQueue
    {
        return $this->getFlashMessageService()->getMessageQueueByIdentifier();
    }

    private function getFlashMessageService(): FlashMessageService
    {
        return GeneralUtility::makeInstance(FlashMessageService::class);
    }
}

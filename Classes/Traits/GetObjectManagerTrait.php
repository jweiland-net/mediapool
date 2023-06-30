<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Traits;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Object\ObjectManagerInterface;

/**
 * Trait to get the ObjectManager of Extbase
 * Only use that class for extbase classes. Try to prefer GeneralUtility::makeInstance wherever possible.
 * ToDo: Remove that class while removing TYPO3 10 compatibility
 */
trait GetObjectManagerTrait
{
    private function getObjectManager(): ObjectManagerInterface
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }
}

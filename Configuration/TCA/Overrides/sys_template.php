<?php

/*
 * This file is part of the package jweiland/glossary2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

if (!defined('TYPO3')) {
    die('Access denied.');
}

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'mediapool',
    'Configuration/TypoScript/Mediapool',
    'Mediapool Video/Playlist',
);
ExtensionManagementUtility::addStaticFile(
    'mediapool',
    'Configuration/TypoScript/Gallery',
    'Mediapool Gallery',
);

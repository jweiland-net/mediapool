<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

ExtensionManagementUtility::addStaticFile(
    'mediapool',
    'Configuration/TypoScript/Mediapool',
    'Mediapool Video/Playlist'
);
ExtensionManagementUtility::addStaticFile(
    'mediapool',
    'Configuration/TypoScript/Gallery',
    'Mediapool Gallery'
);

<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('mediapool', 'Configuration/TypoScript/Mediapool', 'Mediapool Video/Playlist');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('mediapool', 'Configuration/TypoScript/Gallery', 'Mediapool Gallery');

<?php
defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mediapool_domain_model_video');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_mediapool_domain_model_playlist');

// Register main plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'mediapool',
    'Mediapool',
    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.mediapool.title'
);

<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'mediapool',
    'tx_mediapool_domain_model_playlist'
);

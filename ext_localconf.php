<?php
defined('TYPO3_MODE') or die();

// Hook into DataHandler to get video information into fieldArray and abort if a wrong
// video url was submitted
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['mediapool'] =
    \JWeiland\Mediapool\Hooks\DataHandler::class;

// Add YouTubeVideoPlatform to videoPlatforms
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoPlatforms']['YouTube'] =
    \JWeiland\Mediapool\Service\YouTubeVideoPlatform::class;

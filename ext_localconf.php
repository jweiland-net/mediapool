<?php
defined('TYPO3_MODE') or die();

// Hook into DataHandler to get video information into fieldArray and abort if a wrong
// video url was submitted
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['mediapool'] =
    \JWeiland\Mediapool\Hooks\DataHandler::class;

// Add YouTubeVideoImport
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport']['YouTube'] =
    \JWeiland\Mediapool\Import\Video\YouTubeVideoImport::class;

// Add renderType to display video importers
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1507799836] = [
    'nodeName' => 'videoLink',
    'priority' => '70',
    'class' => \JWeiland\Mediapool\Form\Element\VideoLinkElement::class,
];

// Add renderType to display video title as header
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1507811472] = [
    'nodeName' => 'videoHeader',
    'priority' => '70',
    'class' => \JWeiland\Mediapool\Form\Element\VideoHeaderElement::class,
];

// Add renderType to display text
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1507811779] = [
    'nodeName' => 'videoText',
    'priority' => '70',
    'class' => \JWeiland\Mediapool\Form\Element\VideoTextElement::class,
];

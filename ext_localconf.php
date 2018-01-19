<?php
// Hook into DataHandler to get video information into fieldArray and abort if a wrong
// video url was submitted
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['mediapool'] =
    \JWeiland\Mediapool\Hooks\DataHandler::class;

// Register YouTubeVideoImport
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport']['YouTube'] =
    \JWeiland\Mediapool\Import\Video\YouTubeVideoImport::class;

// Register YouTubePlaylistImport
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['playlistImport']['YouTube'] =
    \JWeiland\Mediapool\Import\Playlist\YoutubePlaylistImport::class;

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

// Add renderType to display videos inside a playlist
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1508229522] = [
    'nodeName' => 'inlineVideo',
    'priority' => '70',
    'class' => \JWeiland\Mediapool\Form\Element\InlineVideoElement::class,
];

// Register task for updating video information
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\JWeiland\Mediapool\Task\UpdateVideoInformation::class] = [
    'extension' => 'mediapool',
    'title' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang.xlf:scheduler.update_video_information.title',
    'description' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang.xlf:scheduler.update_video_information.description',
    'additionalFields' => \JWeiland\Mediapool\Task\UpdateVideoInformationAdditionalFieldProvider::class
];

// Register task for updating video information
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\JWeiland\Mediapool\Task\UpdatePlaylistInformation::class] = [
    'extension' => 'mediapool',
    'title' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang.xlf:scheduler.update_playlist_information.title',
    'description' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang.xlf:scheduler.update_playlist_information.description',
    'additionalFields' => \JWeiland\Mediapool\Task\UpdatePlaylistInformationAdditionalFieldProvider::class
];

// Configure main plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'JWeiland.mediapool',
    'Mediapool',
    [
        'Video' => 'show,listRecommended',
        'Playlist' => 'listByCategory'
    ]
);

// Configure gallery plugin
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'JWeiland.mediapool',
    'Gallery',
    [
        'Gallery' => 'preview',
    ]
);

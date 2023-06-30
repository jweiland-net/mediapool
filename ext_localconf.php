<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(static function () {
    // Configure main plugin
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Mediapool',
        'Mediapool',
        [
            \JWeiland\Mediapool\Controller\VideoController::class => 'show,listRecommended',
            \JWeiland\Mediapool\Controller\PlaylistController::class => 'listByCategory,listLatestVideos,listVideos',
        ]
    );

    // Configure gallery plugin
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Mediapool',
        'Gallery',
        [
            \JWeiland\Mediapool\Controller\GalleryController::class => 'preview',
        ]
    );

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

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1643272486] = [
        'nodeName' => 'videoPlayer',
        'priority' => 70,
        'class' => \JWeiland\Mediapool\Form\Element\VideoPlayerElement::class,
    ];

    // Register icons. ToDo: Migrate to Icons.php when removing TYPO3 10 compatibility
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'mediapool-mediapool',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:mediapool/Resources/Public/Icons/tx_mediapool_domain_model_video.svg']
    );
    $iconRegistry->registerIcon(
        'mediapool-gallery',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:mediapool/Resources/Public/Icons/gallery.svg']
    );

    // Add ts config for content element wizard
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mediapool/Configuration/TSconfig/ContentElementWizard.tsconfig">'
    );

    // Hooks

    // Register UpdateWizards
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['mediapoolPlaylistSlug']
        = \JWeiland\Mediapool\Updates\PlaylistSlugUpdate::class;
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['mediapoolVideoSlug']
        = \JWeiland\Mediapool\Updates\VideoSlugUpdate::class;

    // Hook into DataHandler to get video information into fieldArray and abort if a wrong video url was submitted
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['mediapool'] =
        \JWeiland\Mediapool\Hooks\DataHandlerHook::class;

    // Register YouTubePlaylistImport
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['playlistImport']['YouTube'] =
        \JWeiland\Mediapool\Import\Playlist\YoutubePlaylistImport::class;

    // Register YouTubeVideoImport
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport']['YouTube'] =
        \JWeiland\Mediapool\Import\Video\YouTubeVideoImport::class;

    // Register task for updating video information
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\JWeiland\Mediapool\Task\UpdateVideoInformation::class] = [
        'extension' => 'mediapool',
        'title' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang.xlf:scheduler.update_video_information.title',
        'description' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang.xlf:scheduler.update_video_information.description',
        'additionalFields' => \JWeiland\Mediapool\Task\UpdateVideoInformationAdditionalFieldProvider::class,
    ];

    // Register task for updating video information
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\JWeiland\Mediapool\Task\UpdatePlaylistInformation::class] = [
        'extension' => 'mediapool',
        'title' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang.xlf:scheduler.update_playlist_information.title',
        'description' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang.xlf:scheduler.update_playlist_information.description',
        'additionalFields' => \JWeiland\Mediapool\Task\UpdatePlaylistInformationAdditionalFieldProvider::class,
    ];
});

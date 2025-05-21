<?php

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

if (!defined('TYPO3')) {
    die('Access denied.');
}

use JWeiland\Mediapool\Controller\GalleryController;
use JWeiland\Mediapool\Controller\PlaylistController;
use JWeiland\Mediapool\Controller\VideoController;
use JWeiland\Mediapool\Form\Element\InlineVideoElement;
use JWeiland\Mediapool\Form\Element\VideoHeaderElement;
use JWeiland\Mediapool\Form\Element\ShowSupportedVideoPlatforms;
use JWeiland\Mediapool\Form\Element\VideoPlayerElement;
use JWeiland\Mediapool\Form\Element\VideoTextElement;
use JWeiland\Mediapool\Hook\DataHandlerHook;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::configurePlugin(
    'Mediapool',
    'Recommended',
    [
        VideoController::class => 'listRecommended',
    ],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'Mediapool',
    'Detail',
    [
        VideoController::class => 'show',
        PlaylistController::class => 'listByCategory',
    ],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'Mediapool',
    'RecentByCategory',
    [
        VideoController::class => 'listRecentByCategory',
    ],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'Mediapool',
    'Latest',
    [
        PlaylistController::class => 'listLatestVideos, listVideos',
    ],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'Mediapool',
    'List',
    [
        PlaylistController::class => 'listVideos',
    ],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'Mediapool',
    'GalleryPreview',
    [
        GalleryController::class => 'preview',
    ],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

ExtensionUtility::configurePlugin(
    'Mediapool',
    'GalleryTeaser',
    [
        GalleryController::class => 'teaser',
    ],
    [],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
);

// Add a field wizard to show supported video platforms
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1507799836] = [
    'nodeName' => 'showSupportedVideoPlatforms',
    'priority' => '70',
    'class' => ShowSupportedVideoPlatforms::class,
];

// Add renderType to display video title as header
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1507811472] = [
    'nodeName' => 'videoHeader',
    'priority' => '70',
    'class' => VideoHeaderElement::class,
];

// Add renderType to display text
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1507811779] = [
    'nodeName' => 'videoText',
    'priority' => '70',
    'class' => VideoTextElement::class,
];

// Add renderType to display videos inside a playlist
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1508229522] = [
    'nodeName' => 'inlineVideo',
    'priority' => '70',
    'class' => InlineVideoElement::class,
];

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1643272486] = [
    'nodeName' => 'videoPlayer',
    'priority' => 70,
    'class' => VideoPlayerElement::class,
];

// Hook into DataHandler to get video information into fieldArray and abort if a wrong video url was submitted
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['mediapool']
    = DataHandlerHook::class;

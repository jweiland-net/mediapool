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

use JWeiland\Mediapool\Controller\GalleryController;
use JWeiland\Mediapool\Controller\PlaylistController;
use JWeiland\Mediapool\Controller\VideoController;
use JWeiland\Mediapool\Form\Element\InlineVideoElement;
use JWeiland\Mediapool\Form\Element\VideoHeaderElement;
use JWeiland\Mediapool\Form\Element\VideoLinkElement;
use JWeiland\Mediapool\Form\Element\VideoPlayerElement;
use JWeiland\Mediapool\Form\Element\VideoTextElement;
use JWeiland\Mediapool\Hook\DataHandlerHook;
use JWeiland\Mediapool\Updates\PlaylistSlugUpdate;
use JWeiland\Mediapool\Updates\VideoSlugUpdate;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Configure main plugin
ExtensionUtility::configurePlugin(
    'Mediapool',
    'Mediapool',
    [
        VideoController::class => 'show,listRecommended',
        PlaylistController::class => 'listByCategory,listLatestVideos,listVideos',
    ],
);

// Configure gallery plugin
ExtensionUtility::configurePlugin(
    'Mediapool',
    'Gallery',
    [
        GalleryController::class => 'preview',
    ],
);

// Add renderType to display video importers
$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1507799836] = [
    'nodeName' => 'videoLink',
    'priority' => '70',
    'class' => VideoLinkElement::class,
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

// Hooks

// Register UpdateWizards
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['mediapoolPlaylistSlug']
    = PlaylistSlugUpdate::class;

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/install']['update']['mediapoolVideoSlug']
    = VideoSlugUpdate::class;

// Hook into DataHandler to get video information into fieldArray and abort if a wrong video url was submitted
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['mediapool']
    = DataHandlerHook::class;

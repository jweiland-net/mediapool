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

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// Register main plugin
ExtensionUtility::registerPlugin(
    'Mediapool',
    'Mediapool',
    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.mediapool.title',
    'EXT:mediapool/Resources/Public/Icons/video_play.svg',
);

// Register gallery preview CE
ExtensionUtility::registerPlugin(
    'Mediapool',
    'GalleryPreview',
    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.gallery.preview.title',
    'EXT:mediapool/Resources/Public/Icons/gallery.svg',
);
ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Configuration,pi_flexform,',
    'mediapool_gallerypreview',
    'after:subheader',
);
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:mediapool/Configuration/FlexForms/GalleryPreview.xml',
    'mediapool_gallerypreview',
);

// Register gallery teaser CE
ExtensionUtility::registerPlugin(
    'Mediapool',
    'GalleryTeaser',
    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.gallery.teaser.title',
    'EXT:mediapool/Resources/Public/Icons/gallery.svg',
);
ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Configuration,pi_flexform,',
    'mediapool_galleryteaser',
    'after:subheader',
);
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:mediapool/Configuration/FlexForms/GalleryTeaser.xml',
    'mediapool_galleryteaser',
);


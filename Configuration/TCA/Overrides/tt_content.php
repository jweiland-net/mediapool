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

// Register Playlist: recommended plugin
ExtensionUtility::registerPlugin(
    'Mediapool',
    'Recommended',
    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.mediapool.recommended.title',
    'EXT:mediapool/Resources/Public/Icons/video_play.svg',
);
ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Configuration,pi_flexform,',
    'mediapool_recommended',
    'after:subheader',
);
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:mediapool/Configuration/FlexForms/MediapoolRecommended.xml',
    'mediapool_recommended',
);

// Register Playlist: detail plugin
ExtensionUtility::registerPlugin(
    'Mediapool',
    'Detail',
    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.mediapool.detail.title',
    'EXT:mediapool/Resources/Public/Icons/video_play.svg',
);

// Register Playlist: recent-by-category plugin
ExtensionUtility::registerPlugin(
    'Mediapool',
    'RecentByCategory',
    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.mediapool.recentbycategory.title',
    'EXT:mediapool/Resources/Public/Icons/video_play.svg',
);
ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Configuration,pi_flexform,',
    'mediapool_recentbycategory',
    'after:subheader',
);
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:mediapool/Configuration/FlexForms/MediapoolRecentByCategory.xml',
    'mediapool_recentbycategory',
);

// Register Playlist: latest plugin
ExtensionUtility::registerPlugin(
    'Mediapool',
    'Latest',
    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.mediapool.latest.title',
    'EXT:mediapool/Resources/Public/Icons/video_play.svg',
);
ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Configuration,pi_flexform,',
    'mediapool_latest',
    'after:subheader',
);
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:mediapool/Configuration/FlexForms/MediapoolLatest.xml',
    'mediapool_latest',
);

// Register Playlist: list plugin
ExtensionUtility::registerPlugin(
    'Mediapool',
    'List',
    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.mediapool.list.title',
    'EXT:mediapool/Resources/Public/Icons/video_play.svg',
);
ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Configuration,pi_flexform,',
    'mediapool_list',
    'after:subheader',
);
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:mediapool/Configuration/FlexForms/MediapoolList.xml',
    'mediapool_list',
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

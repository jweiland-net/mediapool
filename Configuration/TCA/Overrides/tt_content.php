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

call_user_func(static function () {
    // Register main plugin
    ExtensionUtility::registerPlugin(
        'Mediapool',
        'Mediapool',
        'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.mediapool.title',
        'EXT:mediapool/Resources/Public/Icons/video_play.svg',
    );

    // Register gallery plugin
    ExtensionUtility::registerPlugin(
        'Mediapool',
        'Gallery',
        'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.gallery.title',
        'EXT:mediapool/Resources/Public/Icons/gallery.svg',
    );

    // Add flexform for Mediapool plugin
    $pluginSignature = 'mediapool_mediapool';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key,pages,recursive';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:mediapool/Configuration/FlexForms/Mediapool.xml',
    );

    // Add flexform for gallery plugin
    $pluginSignature = 'mediapool_gallery';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key,pages,recursive';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:mediapool/Configuration/FlexForms/Gallery.xml',
    );
});

<?php
if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(static function () {
    // Register main plugin
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Mediapool',
        'Mediapool',
        'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.mediapool.title',
        'EXT:mediapool/Resources/Public/Icons/video_play.svg'
    );

    // Register gallery plugin
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Mediapool',
        'Gallery',
        'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:plugin.gallery.title',
        'EXT:mediapool/Resources/Public/Icons/gallery.svg'
    );

    // Add flexform for Mediapool plugin
    $pluginSignature = 'mediapool_mediapool';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key,pages,recursive';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:mediapool/Configuration/FlexForms/Mediapool.xml'
    );

    // Add flexform for gallery plugin
    $pluginSignature = 'mediapool_gallery';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key,pages,recursive';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        $pluginSignature,
        'FILE:EXT:mediapool/Configuration/FlexForms/Gallery.xml'
    );
});

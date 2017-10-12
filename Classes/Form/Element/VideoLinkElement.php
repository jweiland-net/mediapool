<?php
namespace JWeiland\Mediapool\Form\Element;

/*
* This file is part of the TYPO3 CMS project.
*
* It is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License, either version 2
* of the License, or any later version.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*
* The TYPO3 project - inspiring people to share!
*/

use JWeiland\Mediapool\Import\Video\AbstractVideoImport;
use JWeiland\Mediapool\Utility\VideoPlatformUtility;
use TYPO3\CMS\Backend\Form\Element\InputTextElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class VideoLinkElement
 *
 * @package JWeiland\Mediapool\Form\Element;
 */
class VideoLinkElement extends InputTextElement
{
    /**
     * Render input field
     *
     * @return array
     */
    public function render(): array
    {
        // get HTML code from input field
        $resultArray = parent::render();
        $config = $this->data['parameterArray']['fieldConf']['config'];
        $size = MathUtility::forceIntegerInRange($config['size'] ?? $this->defaultInputWidth, $this->minimumInputWidth,
            $this->maxInputWidth);
        $width = $this->formMaxWidth($size);
        $videoPlatformsHTML = [];
        $videoPlatformsHTML[] = '<div class="form-control-wrap" style="max-width: ' . $width . 'px">';
        $videoPlatformsHTML[] = '<div class="form-wizards-wrap">';
        $videoPlatformsHTML[] = '<div class="form-wizards-element">';
        $videoPlatformsHTML[] = $this->getSupportedVideoPlatformsHTML();
        $videoPlatformsHTML[] = '</div>';
        $videoPlatformsHTML[] = '</div>';
        $videoPlatformsHTML[] = '</div>';
        $videoPlatformsHTML = implode(LF, $videoPlatformsHTML);
        $resultArray['html'] .= $videoPlatformsHTML;
        return $resultArray;
    }

    /**
     * Get HTML for supported video platforms
     *
     * @return string
     */
    protected function getSupportedVideoPlatformsHTML(): string
    {
        $html = LocalizationUtility::translate(
            'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:render_type.' .
            'video_link_element.supported_video_platforms'
        ) . '<br />';
        /** @var AbstractVideoImport $videoPlatform */
        foreach (VideoPlatformUtility::getRegisteredVideoPlatforms() as $videoPlatformNameSpace) {
            // because we just need the platform name we don´t need to call this with object manager
            $videoPlatform = GeneralUtility::makeInstance($videoPlatformNameSpace);
            VideoPlatformUtility::checkVideoImportClass($videoPlatform);
            $html .= sprintf('<span class="label label-primary">%s</span>', $videoPlatform->getPlatformName());
        }
        return $html;
    }
}

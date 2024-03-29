<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Form\Element;

use JWeiland\Mediapool\Utility\VideoPlatformUtility;
use TYPO3\CMS\Backend\Form\Element\InputTextElement;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class VideoLinkElement
 */
class VideoLinkElement extends InputTextElement
{
    /**
     * TCA field config
     *
     * @var array
     */
    protected $config;

    /**
     * Render input field
     *
     * @return array
     */
    public function render(): array
    {
        // get HTML code from input field
        $resultArray = parent::render();
        $this->config = $this->data['parameterArray']['fieldConf']['config'];
        $size = MathUtility::forceIntegerInRange(
            $this->config['size'] ?? $this->defaultInputWidth,
            $this->minimumInputWidth,
            $this->maxInputWidth
        );
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
     */
    protected function getSupportedVideoPlatformsHTML(): string
    {
        $html = LocalizationUtility::translate(
            'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:render_type.' .
            'video_link_element.supported_video_platforms'
        ) . '<br />';

        try {
            if ($this->config['importType'] === 'playlist') {
                $registeredImporters = VideoPlatformUtility::getRegisteredPlaylistImporters();
            } else {
                $registeredImporters = VideoPlatformUtility::getRegisteredVideoImporters();
            }

            foreach ($registeredImporters as $registeredImporter) {
                $html .= sprintf(
                    '<span class="label label-primary">%s</span>',
                    $registeredImporter->getPlatformName()
                );
            }
        } catch (\Exception $e) {
        }

        return $html;
    }
}

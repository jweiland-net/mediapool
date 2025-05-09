<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Form element to render a video using the content of video.player_html field
 */
class VideoPlayerElement extends AbstractFormElement
{
    /**
     * Render player or a message if player html is not set
     */
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $parameterArray = $this->data['parameterArray'];
        $config = $parameterArray['fieldConf']['config'];
        $size = MathUtility::forceIntegerInRange($config['size'] ?? $this->defaultInputWidth, $this->minimumInputWidth, $this->maxInputWidth);
        $width = $this->formMaxWidth($size);
        if ($playerHTML = $parameterArray['itemFormElValue']) {
            $resultArray['html'] .= '<div>' . $playerHTML . '</div>';
        } else {
            $resultArray['html'] .= '<div class="alert alert-info" role="alert" style="max-width: ' . $width . 'px">' .
                LocalizationUtility::translate(
                    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:' .
                    'tx_mediapool_domain_model_video.empty_field',
                ) .
                '</div>';
        }

        return $resultArray;
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/glossary2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class VideoHeaderElement extends AbstractFormElement
{
    /**
     * This will render a <h4> header with item value as text.
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render(): array
    {
        $parameterArray = $this->data['parameterArray'];
        $resultArray = $this->initializeResultArray();
        $itemValue = $parameterArray['itemFormElValue'];
        $config = $parameterArray['fieldConf']['config'];
        $size = MathUtility::forceIntegerInRange($config['size'] ?? $this->defaultInputWidth, $this->minimumInputWidth, $this->maxInputWidth);
        $width = $this->formMaxWidth($size);

        // display alert if item value is empty
        if (!$itemValue) {
            $html = [];
            $html[] = '<div class="alert alert-info" role="alert" style="max-width: ' . $width . 'px">';
            $html[] = LocalizationUtility::translate(
                'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:' .
                'tx_mediapool_domain_model_video.empty_field',
            );
            $html[] = '</div>';
            $resultArray['html'] = implode(LF, $html);
            return $resultArray;
        }

        $html = [];
        $html[] =
            '<input type="hidden" name="' . $parameterArray['itemFormElName'] . '" value="' .
            htmlspecialchars($parameterArray['itemFormElValue']) . '" />';
        $html[] = '<div class="formengine-field-item t3js-formengine-field-item">';
        $html[] =   '<div class="form-wizards-wrap">';
        $html[] =       '<div class="form-wizards-element">';
        $html[] =           '<div class="form-control-wrap" style="max-width: ' . $width . 'px">';
        $html[] =               '<h4>' . htmlspecialchars($itemValue) . '</h4>';
        $html[] =           '</div>';
        $html[] =       '</div>';
        $html[] =   '</div>';
        $html[] = '</div>';
        $resultArray['html'] = implode(LF, $html);

        return $resultArray;
    }
}

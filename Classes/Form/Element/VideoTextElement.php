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
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class VideoTextElement
 */
class VideoTextElement extends AbstractFormElement
{
    /**
     * This will render a paragraph with item value as text.
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $parameterArray = $this->data['parameterArray'];
        $resultArray = $this->initializeResultArray();
        $itemValue = $parameterArray['itemFormElValue'];
        $config = $parameterArray['fieldConf']['config'];
        $size = MathUtility::forceIntegerInRange($config['size'] ?? $this->defaultInputWidth, $this->minimumInputWidth, $this->maxInputWidth);
        $width = (int)$this->formMaxWidth($size);
        $evalList = GeneralUtility::trimExplode(',', $config['eval'], true);

        // display alert if item value is empty
        if (!$itemValue) {
            $html = [];
            $html[] = '<div class="alert alert-info" role="alert" style="max-width: ' . $width . 'px">';
            $html[] = LocalizationUtility::translate(
                'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:' .
                'tx_mediapool_domain_model_video.empty_field'
            );
            $html[] = '</div>';
            $resultArray['html'] = implode(LF, $html);
            return $resultArray;
        }

        foreach ($evalList as $evalFunc) {
            if ($itemValue && $evalFunc === 'datetime') {
                $itemValue = date($GLOBALS['TYPO3_CONF_VARS']['SYS']['ddmmyy'], $itemValue);
            }
        }

        $html = [];
        $html[] =
            '<input type="hidden" name="' . $parameterArray['itemFormElName'] . '" value="' .
            htmlspecialchars($parameterArray['itemFormElValue']) . '" />';
        $html[] = '<div class="formengine-field-item t3js-formengine-field-item">';
        $html[] =   '<div class="form-wizards-wrap">';
        $html[] =       '<div class="form-wizards-element">';
        $html[] =           '<div class="form-control-wrap" style="max-width: ' . $width . 'px">';
        $html[] =               '<p>' . $itemValue . '</p>';
        $html[] =           '</div>';
        $html[] =       '</div>';
        $html[] =   '</div>';
        $html[] = '</div>';
        $resultArray['html'] = implode(LF, $html);

        return $resultArray;
    }
}

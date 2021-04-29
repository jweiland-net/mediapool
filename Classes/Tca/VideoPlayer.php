<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Tca;

use TYPO3\CMS\Backend\Form\Element\UserElement;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class VideoPlayer
 */
class VideoPlayer
{
    /**
     * Default width value for a couple of elements like text
     *
     * @var int
     */
    protected $defaultInputWidth = 30;

    /**
     * Minimum width value for a couple of elements like text
     *
     * @var int
     */
    protected $minimumInputWidth = 10;

    /**
     * Maximum width value for a couple of elements like text
     *
     * @var int
     */
    protected $maxInputWidth = 50;

    /**
     * Render player or a message if player html is not set
     *
     * @param array $parameterArray
     * @param UserElement $userElement
     * @return string
     */
    public function render(array $parameterArray, UserElement $userElement): string
    {
        $config = $parameterArray['fieldConf']['config'];
        $size = MathUtility::forceIntegerInRange($config['size'] ?? $this->defaultInputWidth, $this->minimumInputWidth, $this->maxInputWidth);
        $width = (int)$this->formMaxWidth($size);

        if ($playerHTML = $parameterArray['row']['player_html']) {
            return $playerHTML;
        }
        return
                '<div class="alert alert-info" role="alert" style="max-width: ' . $width . 'px">' .
                LocalizationUtility::translate(
                    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:' .
                    'tx_mediapool_domain_model_video.empty_field'
                ) .
                '</div>';
    }

    /**
     * Returns the max width in pixels for an elements like input and text
     *
     * @param int $size The abstract size value (1-48)
     * @return int Maximum width in pixels
     */
    protected function formMaxWidth($size = 48)
    {
        $compensationForLargeDocuments = 1.33;
        $compensationForFormFields = 12;

        $size = round($size * $compensationForLargeDocuments);
        return ceil($size * $compensationForFormFields);
    }
}

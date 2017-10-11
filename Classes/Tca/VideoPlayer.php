<?php
namespace JWeiland\Mediapool\Tca;

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

use TYPO3\CMS\Backend\Form\Element\UserElement;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class VideoPlayer
 *
 * @package JWeiland\Mediapool\Tca;
 */
class VideoPlayer
{
    /**
     * Render player or a message if player html is not set
     *
     * @param array $parameterArray
     * @param UserElement $userElement
     * @return string
     */
    public function render(array $parameterArray, UserElement $userElement) : string
    {
        if ($playerHTML = $parameterArray['row']['player_html']) {
            return $playerHTML;
        } else {
            return
                '<div class="alert alert-info" role="alert">' .
                LocalizationUtility::translate(
                    'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:' .
                    'tx_mediapool_domain_model_video.player_html.no_video'
                ) .
                '</div>';
        }
    }
}

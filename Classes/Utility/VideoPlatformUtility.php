<?php
namespace JWeiland\Mediapool\Utility;

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

use JWeiland\Mediapool\Service\AbstractVideoPlatform;

/**
 * Class VideoPlatformUtility
 *
 * @package JWeiland\Mediapool\Utility;
 */
class VideoPlatformUtility
{
    /**
     * Returns an array with registered video platforms.
     * Does not validate if the registered classes are children from AbstractVideoPlatform!
     *
     * @return array
     * @throws \Exception if no video platforms are registered
     */
    public static function getRegisteredVideoPlatforms(): array
    {
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoPlatforms'])) {
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoPlatforms'];
        } else {
            throw new \Exception(
                'At least one video platform must be registered to get information about a video!' .
                ' Please check $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'mediapool\'][\'videoPlatforms\'] for' .
                ' registered video platforms.',
                1507729404
            );
        }
    }

    /**
     * Checks if $videoPlatform is an instance of AbstractVideoPlatform
     *
     * @param AbstractVideoPlatform $videoPlatform
     * @return void if everything is ok
     */
    public static function checkVideoPlatform(AbstractVideoPlatform $videoPlatform)
    {
    }
}

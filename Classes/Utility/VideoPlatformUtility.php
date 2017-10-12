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

use JWeiland\Mediapool\Import\Video\AbstractVideoImport;

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
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport'])) {
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport'];
        } else {
            throw new \Exception(
                'At least one video importer must be registered to get information about a video!' .
                ' Please check $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'mediapool\'][\'videoImport\'] for' .
                ' registered video importers.',
                1507729404
            );
        }
    }

    /**
     * Checks if $videoImport is an instance of AbstractVideoImport
     *
     * @param AbstractVideoImport $videoImport
     * @return void if everything is ok
     */
    public static function checkVideoImportClass(AbstractVideoImport $videoImport)
    {
    }
}

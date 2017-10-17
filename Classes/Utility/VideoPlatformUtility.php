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

use JWeiland\Mediapool\Import\MissingImporterException;
use JWeiland\Mediapool\Import\Playlist\AbstractPlaylistImport;
use JWeiland\Mediapool\Import\Video\AbstractVideoImport;

/**
 * Class VideoPlatformUtility
 *
 * @package JWeiland\Mediapool\Utility;
 */
class VideoPlatformUtility
{
    /**
     * Returns an array with registered video importers.
     * Does not validate if the registered classes are children from AbstractVideoImport!
     *
     * @return array
     * @throws \Exception if no video importers are registered
     */
    public static function getRegisteredVideoImporters(): array
    {
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport'])) {
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport'];
        } else {
            throw new MissingImporterException(
                'At least one video importer must be registered to get information about a video!' .
                ' Please check $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'mediapool\'][\'videoImport\'] for' .
                ' registered video importers.',
                1507729404
            );
        }
    }

    /**
     * Returns an array with registered playlist importers.
     * Does not validate if the registered classes are children from AbstractPlaylistImport!
     *
     * @return array
     * @throws \Exception if no playlist importers are registered
     */
    public static function getRegisteredPlaylistImporters(): array
    {
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['playlistImport'])) {
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['playlistImport'];
        } else {
            throw new MissingImporterException(
                'At least one playlist importer must be registered to get information about a playlist!' .
                ' Please check $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'mediapool\'][\'playlistImport\'] for' .
                ' registered playlist importers.',
                1507881065
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

    /**
     * Checks if $playlistImport is an instance of AbstractPlaylistImport
     *
     * @param AbstractPlaylistImport $playlistImport
     * @return void if everything is ok
     */
    public static function checkPlaylistImportClass(AbstractPlaylistImport $playlistImport)
    {
    }
}

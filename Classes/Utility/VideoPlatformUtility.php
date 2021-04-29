<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Utility;

use JWeiland\Mediapool\Import\MissingImporterException;
use JWeiland\Mediapool\Import\Playlist\AbstractPlaylistImport;
use JWeiland\Mediapool\Import\Video\AbstractVideoImport;

/**
 * Class VideoPlatformUtility
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
        }
        throw new MissingImporterException(
                'At least one video importer must be registered to get information about a video!' .
                ' Please check $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'mediapool\'][\'videoImport\'] for' .
                ' registered video importers.',
                1507729404
            );
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
        }
        throw new MissingImporterException(
                'At least one playlist importer must be registered to get information about a playlist!' .
                ' Please check $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'mediapool\'][\'playlistImport\'] for' .
                ' registered playlist importers.',
                1507881065
            );
    }

    /**
     * Checks if $videoImport is an instance of AbstractVideoImport
     *
     * @param AbstractVideoImport $videoImport
     */
    public static function checkVideoImportClass(AbstractVideoImport $videoImport)
    {
    }

    /**
     * Checks if $playlistImport is an instance of AbstractPlaylistImport
     *
     * @param AbstractPlaylistImport $playlistImport
     */
    public static function checkPlaylistImportClass(AbstractPlaylistImport $playlistImport)
    {
    }
}

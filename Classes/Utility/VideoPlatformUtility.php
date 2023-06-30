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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class VideoPlatformUtility
 */
class VideoPlatformUtility
{
    /**
     * Returns an array with registered video importers.
     * Does not validate if the registered classes are children from AbstractVideoImport!
     *
     * @return AbstractVideoImport[]
     * @throws \Exception if no video importers are registered
     */
    public static function getRegisteredVideoImporters(): array
    {
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport'] ?? false)) {
            $registeredVideoImporters = [];
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport'] as $className) {
                if (!class_exists($className)) {
                    continue;
                }

                $registeredVideoImporter = GeneralUtility::makeInstance($className);
                if ($registeredVideoImporter instanceof AbstractVideoImport) {
                    $registeredVideoImporters[] = $registeredVideoImporter;
                }
            }

            return $registeredVideoImporters;
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
     *
     * @return AbstractPlaylistImport[]
     * @throws \Exception if no playlist importers are registered
     */
    public static function getRegisteredPlaylistImporters(): array
    {
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['playlistImport'] ?? false)) {
            $registeredPlaylistImporters = [];
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['playlistImport'] as $className) {
                if (!class_exists($className)) {
                    continue;
                }

                $registeredPlaylistImporter = GeneralUtility::makeInstance($className);
                if ($registeredPlaylistImporter instanceof AbstractPlaylistImport) {
                    $registeredPlaylistImporters[] = $registeredPlaylistImporter;
                }
            }

            return $registeredPlaylistImporters;
        }

        throw new MissingImporterException(
            'At least one playlist importer must be registered to get information about a playlist!' .
            ' Please check $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'mediapool\'][\'playlistImport\'] for' .
            ' registered playlist importers.',
            1507881065
        );
    }
}

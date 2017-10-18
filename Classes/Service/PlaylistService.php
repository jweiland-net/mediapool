<?php
namespace JWeiland\Mediapool\Service;

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

use JWeiland\Mediapool\Domain\Model\Playlist;
use JWeiland\Mediapool\Domain\Model\Video;
use JWeiland\Mediapool\Import\Playlist\AbstractPlaylistImport;
use JWeiland\Mediapool\Utility\VideoPlatformUtility;
use JWeiland\Mediapool\Import\Video\AbstractVideoImport;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class PlaylistService
 *
 * ! use ObjectManager to get an instance of this class !
 *
 * @package JWeiland\Mediapool\Service;
 */
class PlaylistService
{
    const COLLECTION_PLAYLIST_INFORMATION_FAILED = 1;
    const NO_VIDEO_PLATFORM_MATCH = 2;

    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * inject objectManager
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Returns an array that includes a prepared fieldArray for DataHandler
     * as an array with new records for DataHandler
     *
     * @param string $playlistLink like https://www.youtube.com/playlist?list=PL-ABvQXa8oyE4zbwSy4V6K5YTD6S_lhu
     * @param int $pid to save video records created by PlaylistImport
     * @return array|int returns int that equals constants of this class to signal an specified error
     */
    public function getPlaylistData(string $playlistLink, int $pid)
    {
        $playlistImporters = VideoPlatformUtility::getRegisteredPlaylistImporters();
        foreach ($playlistImporters as $playlistImporterNamespace) {
            /** @var AbstractPlaylistImport $playlistImporter */
            $playlistImporter = $this->objectManager->get($playlistImporterNamespace);
            VideoPlatformUtility::checkPlaylistImportClass($playlistImporter);
            if ($this->isPlaylistOfVideoImport($playlistLink, $playlistImporter)) {
                if ($playlist = $playlistImporter->getPlaylistInformation($playlistLink, $pid)) {
                    return $playlist;
                } else {
                    return self::COLLECTION_PLAYLIST_INFORMATION_FAILED;
                }
            }
        }
        return self::NO_VIDEO_PLATFORM_MATCH;
    }

    /**
     * Checks if one of the hosts from $playlistImport matches with
     * $playlistLink.
     *
     * @param string $playlistLink
     * @param AbstractPlaylistImport $playlistImport
     * @return bool true if true, false if false you know ;)
     */
    protected function isPlaylistOfVideoImport(string $playlistLink, AbstractPlaylistImport $playlistImport): bool
    {
        foreach ($playlistImport->getPlatformHosts() as $host) {
            if (strpos($playlistLink, $host) === 0) {
                return true;
            }
        }
        return false;
    }
}

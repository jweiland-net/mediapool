<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Service;

use JWeiland\Mediapool\AbstractBase;
use JWeiland\Mediapool\Import\Playlist\AbstractPlaylistImport;
use JWeiland\Mediapool\Utility\VideoPlatformUtility;

use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class PlaylistService
 *
 * ! use ObjectManager to get an instance of this class !
 */
class PlaylistService extends AbstractBase
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
     * @return array returns int that equals constants of this class to signal an specified error
     */
    public function getPlaylistData(string $playlistLink, int $pid): array
    {
        $playlistImporters = VideoPlatformUtility::getRegisteredPlaylistImporters();
        foreach ($playlistImporters as $playlistImporterNamespace) {
            /** @var AbstractPlaylistImport $playlistImporter */
            $playlistImporter = $this->objectManager->get($playlistImporterNamespace);
            VideoPlatformUtility::checkPlaylistImportClass($playlistImporter);
            if ($this->isPlaylistOfVideoImport($playlistLink, $playlistImporter)) {
                return $playlistImporter->getPlaylistInformation($playlistLink, $pid);
            }
        }
        $this->addFlashMessageAndLog(
            'playlist_service.no_match.title',
            'playlist_service.no_match.message'
        );
        return [];
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

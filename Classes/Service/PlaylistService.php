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

/**
 * Class PlaylistService
 *
 * ! use ObjectManager to get an instance of this class !
 */
class PlaylistService extends AbstractBase
{
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

<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Service;

use JWeiland\Mediapool\Helper\MessageHelper;
use JWeiland\Mediapool\Import\Playlist\PlaylistImportInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PlaylistService
{
    protected array $importers;

    public function __construct(
        iterable $importers,
        protected MessageHelper $messageHelper,
    ) {
        foreach ($importers as $importer) {
            if ($importer instanceof PlaylistImportInterface) {
                $this->importers[] = $importer;
            }
        }
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
        try {
            foreach ($this->importers as $registeredPlaylistImporter) {
                if ($this->isPlaylistOfVideoImport($playlistLink, $registeredPlaylistImporter)) {
                    return $registeredPlaylistImporter->getPlaylistInformation($playlistLink, $pid);
                }
            }
        } catch (\Exception $e) {
        }

        $this->messageHelper->addFlashMessage(
            LocalizationUtility::translate(
                'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:playlist_service.no_match.message',
            ),
            LocalizationUtility::translate(
                'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:playlist_service.no_match.title',
            ),
            ContextualFeedbackSeverity::ERROR,
        );

        return [];
    }

    /**
     * Checks if one of the hosts from $playlistImport matches with $playlistLink.
     */
    protected function isPlaylistOfVideoImport(string $playlistLink, PlaylistImportInterface $playlistImport): bool
    {
        foreach ($playlistImport->getPlatformHosts() as $host) {
            if (str_starts_with($playlistLink, $host)) {
                return true;
            }
        }

        return false;
    }
}

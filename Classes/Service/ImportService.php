<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Service;

use JWeiland\Mediapool\Import\Playlist\PlaylistImportInterface;
use JWeiland\Mediapool\Import\Video\VideoImportInterface;

/**
 * ImportService
 *
 * This service exists purely as a workaround for legacy instantiation behavior in TYPO3 11–12.
 *
 * The problem: Classes like `VideoLinkElement` are instantiated by TYPO3 directly using
 * `GeneralUtility::makeInstance()` instead of going through the Symfony Dependency Injection
 * container. This makes it impossible to inject dependencies like all services implementing
 * `PlaylistImportInterface` via constructor injection.
 *
 * This service collects all tagged services implementing `PlaylistImportInterface` and
 * `PlaylistImportInterface` and provides them through a single, DI-aware entry point. It
 * allows us to work around the limitations of TYPO3's manual instantiation process.
 *
 * The good news: As of TYPO3 13, constructor injection finally works for form elements and
 * similar framework-handled classes – meaning this workaround can and should be removed once
 * the extension targets TYPO3 13+.
 *
 * Until then: Yes, this is a hack. Yes, it’s a waste of time. No, you're not alone.
 */
class ImportService
{
    protected array $playlistImporters = [];

    protected array $videoImporters = [];

    public function __construct(iterable $playlistImporters, iterable $videoImporters)
    {
        foreach ($playlistImporters as $playlistImporter) {
            if ($playlistImporter instanceof PlaylistImportInterface) {
                $this->playlistImporters[] = $playlistImporter;
            }
        }

        foreach ($videoImporters as $videoImporter) {
            if ($videoImporter instanceof VideoImportInterface) {
                $this->videoImporters[] = $videoImporter;
            }
        }
    }

    public function getPlaylistImporters(): array
    {
        return $this->playlistImporters;
    }

    public function getVideoImporters(): array
    {
        return $this->videoImporters;
    }
}

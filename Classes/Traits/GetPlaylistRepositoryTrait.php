<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Traits;

use JWeiland\Mediapool\Domain\Repository\PlaylistRepository;

/**
 * Trait to get the PlaylistRepository
 * ToDo: Remove that class while removing TYPO3 10 compatibility
 */
trait GetPlaylistRepositoryTrait
{
    use GetObjectManagerTrait;

    private function getPlaylistRepository(): PlaylistRepository
    {
        return $this->getObjectManager()->get(PlaylistRepository::class);
    }
}

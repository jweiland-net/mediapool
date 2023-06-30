<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Traits;

use JWeiland\Mediapool\Domain\Repository\VideoRepository;

/**
 * Trait to get the VideoRepository
 * ToDo: Remove that class while removing TYPO3 10 compatibility
 */
trait GetVideoRepositoryTrait
{
    use GetObjectManagerTrait;

    private function getVideoRepository(): VideoRepository
    {
        return $this->getObjectManager()->get(VideoRepository::class);
    }
}

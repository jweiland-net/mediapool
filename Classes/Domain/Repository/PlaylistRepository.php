<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/glossary2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Domain\Repository;

use JWeiland\Mediapool\Domain\Model\Playlist;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository to get records from the table: tx_mediapool_domain_model_playlist
 */
class PlaylistRepository extends Repository
{
    /**
     * Find playlists by category
     *
     * @return QueryResultInterface|Playlist[]
     */
    public function findByCategory(int $categoryUid): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->contains('categories', $categoryUid),
        );

        return $query->execute();
    }
}

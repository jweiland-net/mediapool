<?php
namespace JWeiland\Mediapool\Domain\Repository;

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

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class PlaylistRepository
 *
 * @package JWeiland\Mediapool\Domain\Repository;
 */
class PlaylistRepository extends Repository
{
    /**
     * Find playlists by category
     *
     * @param int $categoryUid
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByCategory(int $categoryUid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->contains('categories', $categoryUid)
        );
        return $query->execute();
    }

    /**
     * Find pid of a playlist by uid
     *
     * @param int $playlistUid
     * @return mixed
     */
    public function findPidByUid(int $playlistUid)
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_mediapool_domain_model_playlist');
        return $connection
            ->select(['pid'], 'tx_mediapool_domain_model_playlist', ['uid' => $playlistUid])
            ->fetch()['pid'];
    }
}

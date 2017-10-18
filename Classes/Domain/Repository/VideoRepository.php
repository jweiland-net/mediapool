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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class VideoRepository
 *
 * @package JWeiland\Mediapool\Domain\Repository;
 */
class VideoRepository extends Repository
{
    /**
     * Find a video by video id
     *
     * @param string $videoId
     * @param int $pid
     * @return array|\TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findByVideoId(string $videoId, int $pid)
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);
        $query->matching(
            $query->logicalAnd(
                $query->equals('videoId', $videoId),
                $query->equals('pid', $pid)
            )
        );
        return $query->execute();
    }

    /**
     * Find all links and uids without respecting pid
     *
     * @return array
     */
    public function findAllLinksAndUids()
    {
        /** @var Connection  $connection */
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(
            'tx_mediapool_domain_model_video'
        );
        return $connection->select(['uid', 'link'], 'tx_mediapool_domain_model_video')->fetchAll();
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Service\Record;

use Doctrine\DBAL\Exception;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class PlaylistRecordService
{
    protected QueryBuilder $queryBuilder;

    public function __construct(QueryBuilder $queryBuilder)
    {
        $this->queryBuilder = $queryBuilder;
    }

    /**
     * Find all playlist links and uids without respecting pid
     *
     * @return array records with fields: uid, link
     */
    public function findAllLinksAndUids(): array
    {
        $queryBuilder = $this->queryBuilder;
        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        try {
            return $queryBuilder
                ->select('uid', 'link')
                ->from('tx_mediapool_domain_model_playlist')
                ->executeQuery()
                ->fetchAllAssociative();
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Find the link and uid of records by pid
     *
     * @param array $pages UIDs of page records
     * @return array records with fields: uid, link
     */
    public function findLinksAndUidsByPages(array $pages): array
    {
        $queryBuilder = $this->queryBuilder;
        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        try {
            return $queryBuilder
                ->select('uid', 'link')
                ->from('tx_mediapool_domain_model_playlist')
                ->where(
                    $queryBuilder->expr()->in(
                        'pid',
                        $queryBuilder->createNamedParameter($pages, Connection::PARAM_INT_ARRAY),
                    ),
                )
                ->executeQuery()
                ->fetchAllAssociative();
        } catch (Exception $e) {
            return [];
        }
    }

    public function getPidByPlaylistUid(int $playlistUid): int
    {
        $queryBuilder = $this->queryBuilder;
        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        try {
            $playlistRecord = $queryBuilder
                ->select('pid')
                ->from('tx_mediapool_domain_model_playlist')
                ->where(
                    $queryBuilder->expr()->eq(
                        'uid',
                        $queryBuilder->createNamedParameter($playlistUid),
                    ),
                )
                ->executeQuery()
                ->fetchAssociative();
        } catch (Exception $e) {
            return 0;
        }

        if (!is_array($playlistRecord)) {
            return 0;
        }

        return $playlistRecord['pid'] ?? 0;
    }
}

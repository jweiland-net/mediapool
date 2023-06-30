<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Domain\Repository;

use JWeiland\Mediapool\Traits\GetObjectManagerTrait;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository to get records from table: tx_mediapool_domain_model_video
 */
class VideoRepository extends Repository
{
    use GetObjectManagerTrait;

    public function findByVideoId(string $videoId, int $pid): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching(
            $query->logicalAnd([
                $query->equals('videoId', $videoId),
                $query->equals('pid', $pid)
            ])
        );
        return $query->execute();
    }

    /**
     * Find all links and uids without respecting pid
     *
     * @return array records with fields: uid, link
     */
    public function findAllLinksAndUids(): array
    {
        $connection = GeneralUtility::makeInstance(ConnectionPool::class)->getConnectionForTable(
            'tx_mediapool_domain_model_video'
        );

        return $connection->select(['uid', 'link'], 'tx_mediapool_domain_model_video')->fetchAll();
    }

    /**
     * Find link and uid of records by pid
     *
     * @param string $pids comma separated list of pids
     * @return array records with fields: uid, link
     */
    public function findLinksAndUidsByPid(string $pids): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable(
            'tx_mediapool_domain_model_video'
        );
        $query = $queryBuilder
            ->select('uid', 'link')
            ->from('tx_mediapool_domain_model_video')
            ->where(
                $queryBuilder->expr()->in('pid', $pids)
            );

        return $query->execute()->fetchAll();
    }

    /**
     * Find recent videos of playlists by category uids
     * will return an multidimensional array like
     * $arr = [
     *     <categoryUid> => [
     *         'category' => <instance of category>,
     *         'playlist' => <instance of playlist>,
     *         'video' => <instance of video>
     *     ],
     *     ...
     * ];
     *
     * @param string $categoryUids comma separated list of uids (e.g. 1,4,6)
     */
    public function findRecentByCategories(string $categoryUids): array
    {
        $categoryRepository = $this->getObjectManager()->get(CategoryRepository::class);
        $recentVideos = [];
        foreach (explode(',', $categoryUids) as $categoryUid) {
            $category = $categoryRepository->findByUid($categoryUid);
            $recent = $this->findRecentByCategory($categoryUid);
            if ($recent) {
                $recentVideos[$categoryUid] = [
                    'category' => $category,
                    'playlist' => $recent['playlist'],
                    'video' => $recent['video']
                ];
            }
        }
        return $recentVideos;
    }

    /**
     * Find recent videos of playlists by category
     * will return an array like
     * $arr = [
     *     'playlist' => <instance of playlist>,
     *     'video' => <instance of the newest video in this playlist>
     * ];
     */
    public function findRecentByCategory(int $categoryUid): array
    {
        $playlistRepository = $this->getObjectManager()->get(PlaylistRepository::class);
        $playlists = $playlistRepository->findByCategory($categoryUid);
        $uploadDate = 0;
        $recentVideo = [];
        foreach ($playlists as $playlist) {
            foreach ($playlist->getVideos() as $video) {
                if ($video->getUploadDate() > $uploadDate) {
                    $uploadDate = $video->getUploadDate();
                    $recentVideo = [
                        'playlist' => $playlist,
                        'video' => $video
                    ];
                }
            }
        }
        return $recentVideo;
    }
}

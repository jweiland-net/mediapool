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


use JWeiland\Mediapool\Domain\Model\Playlist;
use JWeiland\Mediapool\Domain\Model\Video;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Domain\Repository\CategoryRepository;
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
     * @return array records with fields: uid, link
     */
    public function findAllLinksAndUids()
    {
        /** @var Connection $connection */
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
    public function findLinksAndUidsByPid(string $pids)
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
     * @return array
     */
    public function findRecentByCategories(string $categoryUids)
    {
        $categoryRepository = $this->objectManager->get(CategoryRepository::class);
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
     *
     * @param int $categoryUid
     * @return array
     */
    public function findRecentByCategory(int $categoryUid): array
    {
        $playlistRepository = $this->objectManager->get(PlaylistRepository::class);
        $playlists = $playlistRepository->findByCategory($categoryUid);
        $uploadDate = 0;
        $recentVideo = [];
        /** @var Playlist $playlist */
        foreach ($playlists as $playlist) {
            /** @var Video $video */
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

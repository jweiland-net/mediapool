<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Repository to get records from the table: tx_mediapool_domain_model_video
 */
class VideoRepository extends Repository
{
    protected ConnectionPool $connectionPool;

    protected CategoryRepository $categoryRepository;

    protected PlaylistRepository $playlistRepository;

    public function injectConnectionPool(ConnectionPool $connectionPool): void
    {
        $this->connectionPool = $connectionPool;
    }

    public function injectCategoryRepository(CategoryRepository $categoryRepository): void
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function injectPlaylistRepository(PlaylistRepository $playlistRepository): void
    {
        $this->playlistRepository = $playlistRepository;
    }

    public function findByVideoId(string $videoId, int $pid): QueryResultInterface
    {
        $query = $this->createQuery();
        $query->getQuerySettings()->setRespectStoragePage(false);

        $query->matching(
            $query->logicalAnd(
                $query->equals('videoId', $videoId),
                $query->equals('pid', $pid),
            ),
        );

        return $query->execute();
    }

    /**
     * Find recent videos of playlists by category uids
     * will return a multidimensional array like
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
        $recentVideos = [];

        foreach (GeneralUtility::intExplode(',', $categoryUids, true) as $categoryUid) {
            $category = $this->categoryRepository->findByUid($categoryUid);
            $recent = $this->findRecentByCategory($categoryUid);
            if ($recent) {
                $recentVideos[$categoryUid] = [
                    'category' => $category,
                    'playlist' => $recent['playlist'],
                    'video' => $recent['video'],
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
        $playlists = $this->playlistRepository->findByCategory($categoryUid);
        $uploadDate = 0;
        $recentVideo = [];

        foreach ($playlists as $playlist) {
            foreach ($playlist->getVideos() as $video) {
                if ($video->getUploadDate() > $uploadDate) {
                    $uploadDate = $video->getUploadDate();
                    $recentVideo = [
                        'playlist' => $playlist,
                        'video' => $video,
                    ];
                }
            }
        }

        return $recentVideo;
    }
}

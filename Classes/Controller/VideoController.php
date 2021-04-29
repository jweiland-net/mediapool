<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Controller;

use JWeiland\Mediapool\Domain\Model\Playlist;
use JWeiland\Mediapool\Domain\Model\Video;
use JWeiland\Mediapool\Domain\Repository\VideoRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class VideoController
 */
class VideoController extends ActionController
{
    /**
     * @var VideoRepository
     */
    protected $videoRepository;

    public function injectVideoRepository(VideoRepository $videoRepository): void
    {
        $this->videoRepository = $videoRepository;
    }

    /**
     * Shows a single video and additionally a playlist if $playlist is set
     *
     * @param Video $video
     * @param Playlist|null $playlist
     */
    public function showAction(Video $video, Playlist $playlist = null): void
    {
        if ($playlist !== null && !$playlist->getVideos()->contains($video)) {
            throw new \InvalidArgumentException(
                'Passed video is not inside passed playlist! Please check your arguments.',
                1508316980
            );
        }
        $this->view->assign('video', $video);
        $this->view->assign('playlist', $playlist);
    }

    /**
     * List recommended videos
     *
     * @throws \InvalidArgumentException if a selected recommended video could not be found
     */
    public function listRecommendedAction(): void
    {
        $recommendedVideos = [];
        foreach (explode(',', $this->settings['recommendedVideos']) as $recommendedVideoUid) {
            $recommendedVideo = $this->videoRepository->findByUid($recommendedVideoUid);
            if ($recommendedVideo === null) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'The selected recommended video %d could not be found.',
                        $recommendedVideoUid
                    ),
                    1508316983
                );
            }
            $recommendedVideos[] = $recommendedVideo;
        }
        $this->view->assign('detailPage', $this->settings['detailPage']);
        $this->view->assign('recommendedVideos', $recommendedVideos);
    }

    /**
     * List recent videos sorted by selected categories
     */
    public function listRecentByCategoryAction(): void
    {
        $this->view->assign(
            'recentVideos',
            $this->videoRepository->findRecentByCategories($this->settings['categories'])
        );
    }
}

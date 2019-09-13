<?php
namespace JWeiland\Mediapool\Controller;

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
use JWeiland\Mediapool\Domain\Repository\VideoRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class VideoController
 */
class VideoController extends ActionController
{
    /**
     * Video Repository
     *
     * @var VideoRepository
     */
    protected $videoRepository;

    /**
     * inject videoRepository
     *
     * @param VideoRepository $videoRepository
     */
    public function injectVideoRepository(VideoRepository $videoRepository)
    {
        $this->videoRepository = $videoRepository;
    }

    /**
     * Shows a single video and additionally a playlist if $playlist is set
     *
     * @param Video $video
     * @param Playlist|null $playlist
     */
    public function showAction(Video $video, Playlist $playlist = null)
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
    public function listRecommendedAction()
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
    public function listRecentByCategoryAction()
    {
        $this->view->assign(
            'recentVideos',
            $this->videoRepository->findRecentByCategories($this->settings['categories'])
        );
    }
}

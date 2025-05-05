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
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class VideoController extends ActionController
{
    protected VideoRepository $videoRepository;

    public function injectVideoRepository(VideoRepository $videoRepository): void
    {
        $this->videoRepository = $videoRepository;
    }

    /**
     * Shows a single video and additionally a playlist if $playlist is set
     */
    public function showAction(Video $video, ?Playlist $playlist = null): ResponseInterface
    {
        if ($playlist !== null && !$playlist->getVideos()->contains($video)) {
            throw new \InvalidArgumentException(
                'Passed video is not inside passed playlist! Please check your arguments.',
                1508316980,
            );
        }

        $this->view->assign('video', $video);
        $this->view->assign('playlist', $playlist);

        return $this->htmlResponse();
    }

    /**
     * List recommended videos
     *
     * @throws \InvalidArgumentException if a selected recommended video could not be found
     */
    public function listRecommendedAction(): ResponseInterface
    {
        $recommendedVideos = [];

        foreach (GeneralUtility::trimExplode(',', $this->settings['recommendedVideos'], true) as $recommendedVideoUid) {
            $recommendedVideo = $this->videoRepository->findByUid($recommendedVideoUid);
            if ($recommendedVideo === null) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'The selected recommended video %d could not be found.',
                        $recommendedVideoUid,
                    ),
                    1508316983,
                );
            }

            $recommendedVideos[] = $recommendedVideo;
        }

        $this->view->assign('detailPage', $this->settings['detailPage']);
        $this->view->assign('recommendedVideos', $recommendedVideos);

        return $this->htmlResponse();
    }

    /**
     * List recent videos sorted by selected categories
     *
     * ToDo: This method is not registered in ext_localconf.php. It will be called by SCA of FlexForm. This method must be migrated into its own plugin while upgrading to TYPO3 12.
     */
    public function listRecentByCategoryAction(): ResponseInterface
    {
        $this->view->assign(
            'recentVideos',
            $this->videoRepository->findRecentByCategories($this->settings['categories']),
        );

        return $this->htmlResponse();
    }
}

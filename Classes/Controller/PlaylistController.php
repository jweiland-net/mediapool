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
use JWeiland\Mediapool\Domain\Repository\PlaylistRepository;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class PlaylistController
 *
 * @package JWeiland\Mediapool\Controller
 */
class PlaylistController extends ActionController
{
    /**
     * Playlist Repository
     *
     * @var PlaylistRepository
     */
    protected $playlistRepository;

    /**
     * inject playlistRepository
     *
     * @param PlaylistRepository $playlistRepository
     * @return void
     */
    public function injectPlaylistRepository(PlaylistRepository $playlistRepository)
    {
        $this->playlistRepository = $playlistRepository;
    }

    /**
     * List playlists by category
     *
     * @param Category $category
     * @return void
     */
    public function listByCategoryAction(Category $category)
    {
        $playlists = $this->playlistRepository->findByCategory($category->getUid());
        $this->view->assign('detailPage', $this->settings['detailPage']);
        $this->view->assign('category', $category);
        $this->view->assign('playlists', $playlists);
    }

    /**
     * List latest videos of a playlist
     */
    public function listLatestVideosAction()
    {
        $this->view->assign('playlist', $this->playlistRepository->findByUid($this->settings['playlist']));
    }

    /**
     * List all videos of a playlist
     *
     * @param Playlist|null $playlist either pass a playlist or use the given from $this->settings
     */
    public function listVideosAction(Playlist $playlist = null)
    {
        if ($playlist === null) {
            $playlist = $this->playlistRepository->findByUid($this->settings['playlist']);
        }
        $this->view->assign('playlist', $playlist);
    }
}

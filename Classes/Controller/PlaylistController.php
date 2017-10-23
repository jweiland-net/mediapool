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
        $this->view->assign('category', $category);
        $this->view->assign('playlists', $playlists);
    }
}

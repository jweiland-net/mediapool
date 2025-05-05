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
use JWeiland\Mediapool\Domain\Repository\PlaylistRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Domain\Model\Category;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class PlaylistController extends ActionController
{
    protected PlaylistRepository $playlistRepository;

    public function injectPlaylistRepository(PlaylistRepository $playlistRepository): void
    {
        $this->playlistRepository = $playlistRepository;
    }

    public function listByCategoryAction(Category $category): ResponseInterface
    {
        $playlists = $this->playlistRepository->findByCategory($category->getUid());

        $this->view->assign('detailPage', $this->settings['detailPage']);
        $this->view->assign('category', $category);
        $this->view->assign('playlists', $playlists);

        return $this->htmlResponse();
    }

    public function listLatestVideosAction(): ResponseInterface
    {
        $this->view->assign('playlist', $this->playlistRepository->findByUid($this->settings['playlist']));

        return $this->htmlResponse();
    }

    /**
     * @param Playlist|null $playlist either pass a playlist or use the given from $this->settings
     */
    public function listVideosAction(?Playlist $playlist = null): ResponseInterface
    {
        if ($playlist === null) {
            $playlist = $this->playlistRepository->findByUid($this->settings['playlist']);
        }

        $this->view->assign('playlist', $playlist);

        return $this->htmlResponse();
    }
}

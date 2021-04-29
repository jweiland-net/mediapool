<?php

declare(strict_types = 1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Controller;

use TYPO3\CMS\Core\Resource\Collection\AbstractFileCollection;
use TYPO3\CMS\Core\Resource\FileCollectionRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class GalleryController
 */
class GalleryController extends ActionController
{
    /**
     * @var FileCollectionRepository
     */
    protected $fileCollectionRepository;

    /**
     * inject fileCollectionRepository
     *
     * @param FileCollectionRepository $fileCollectionRepository
     */
    public function injectFileCollectionRepository(FileCollectionRepository $fileCollectionRepository)
    {
        $this->fileCollectionRepository = $fileCollectionRepository;
    }

    /**
     * Gallery preview action
     * displays a preview image that contains a fancybox3 gallery
     */
    public function previewAction()
    {
        $this->view->assign('fileCollections', $this->getFileCollections());
    }

    /**
     * Gallery teaser action
     * displays three galleries and a more button with configurable
     * target page
     */
    public function teaserAction()
    {
        $this->view->assign('fileCollections', $this->getFileCollections());
    }

    /**
     * Get file collections from settings
     *
     * @return array
     */
    protected function getFileCollections(): array
    {
        $fileCollections = [];
        if ($this->settings['file_collections']) {
            foreach (explode(',', $this->settings['file_collections']) as $uid) {
                $fileCollection = $this->fileCollectionRepository->findByUid((int)$uid);
                if ($fileCollection instanceof AbstractFileCollection) {
                    $fileCollection->loadContents();
                    $fileCollections[] = $fileCollection;
                }
            }
        }
        return $fileCollections;
    }
}

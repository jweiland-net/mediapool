<?php declare(strict_types=1);
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

use TYPO3\CMS\Core\Resource\Collection\AbstractFileCollection;
use TYPO3\CMS\Core\Resource\FileCollectionRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class GalleryController
 *
 * @package JWeiland\Mediapool\Controller;
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
     * @return void
     */
    public function injectFileCollectionRepository(FileCollectionRepository $fileCollectionRepository)
    {
        $this->fileCollectionRepository = $fileCollectionRepository;
    }

    /**
     * Gallery preview action
     * displays a preview image that contains a fancybox3 gallery
     *
     * @return void
     */
    public function previewAction()
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

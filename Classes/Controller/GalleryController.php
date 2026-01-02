<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Controller;

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Resource\Collection\AbstractFileCollection;
use TYPO3\CMS\Core\Resource\Exception\ResourceDoesNotExistException;
use TYPO3\CMS\Core\Resource\FileCollectionRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

class GalleryController extends ActionController
{
    public function __construct(protected FileCollectionRepository $fileCollectionRepository) {}

    /**
     * Gallery preview action
     * displays a preview image that contains a fancybox3 gallery
     */
    public function previewAction(): ResponseInterface
    {
        $this->view->assign('fileCollections', $this->getFileCollections());

        return $this->htmlResponse();
    }

    /**
     * Gallery teaser action
     * displays three galleries and a more button with a configurable
     * target page
     */
    public function teaserAction(): ResponseInterface
    {
        $this->view->assign('fileCollections', $this->getFileCollections());

        return $this->htmlResponse();
    }

    /**
     * @return AbstractFileCollection[]
     */
    protected function getFileCollections(): array
    {
        $fileCollections = [];

        if (isset($this->settings['fileCollections']) && $this->settings['fileCollections']) {
            foreach (GeneralUtility::intExplode(',', $this->settings['fileCollections'], true) as $uid) {
                try {
                    $fileCollection = $this->fileCollectionRepository->findByUid($uid);
                } catch (ResourceDoesNotExistException $e) {
                    continue;
                }

                if ($fileCollection instanceof AbstractFileCollection) {
                    $fileCollection->loadContents();
                    $fileCollections[] = $fileCollection;
                }
            }
        }

        return $fileCollections;
    }
}

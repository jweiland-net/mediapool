<?php
namespace JWeiland\Mediapool\Form\Element;

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

use JWeiland\Mediapool\Domain\Repository\VideoRepository;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class InlineVideoElement
 *
 * @package JWeiland\Mediapool\Form\Element;
 */
class InlineVideoElement extends AbstractFormElement
{
    /**
     * Fluid Standalone View
     *
     * @var StandaloneView
     */
    protected $view;

    /**
     * Video Repository
     *
     * @var VideoRepository
     */
    protected $videoRepository;

    /**
     * InlineVideoElement constructor.
     *
     * @param NodeFactory $nodeFactory
     * @param array $data
     */
    public function __construct(NodeFactory $nodeFactory, array $data)
    {
        parent::__construct($nodeFactory, $data);
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->view = $objectManager->get(StandaloneView::class);
        $this->videoRepository = $objectManager->get(VideoRepository::class);
    }

    /**
     * Handler for single nodes
     *
     * @return array As defined in initializeResultArray() of AbstractNode
     */
    public function render()
    {
        $resultArray = $this->initializeResultArray();
        $parameterArray = $this->data['parameterArray'];
        $videos = [];
        foreach (explode(',', $parameterArray['itemFormElValue']) as $uid) {
                $videos[] = $this->videoRepository->findByUid($uid);
        }
        $this->view->setTemplatePathAndFilename(
            ExtensionManagementUtility::extPath('mediapool') .
            'Resources/Private/Templates/InlineVideoElement/InlineVideoElement.html'
        );
        $this->view->assignMultiple(['elementId' => $parameterArray['itemFormElID'], 'videos' => $videos]);
        $resultArray['html'] = $this->view->render();
        return $resultArray;
    }
}

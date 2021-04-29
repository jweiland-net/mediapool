<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Form\Element;

use JWeiland\Mediapool\Domain\Repository\VideoRepository;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Backend\Form\NodeFactory;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class InlineVideoElement
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
    public function render(): array
    {
        $resultArray = $this->initializeResultArray();
        $parameterArray = $this->data['parameterArray'];
        $config = $parameterArray['fieldConf']['config'];
        $size = MathUtility::forceIntegerInRange($config['size'] ?? $this->defaultInputWidth, $this->minimumInputWidth, $this->maxInputWidth);
        $width = $this->formMaxWidth($size);

        if (!$parameterArray['itemFormElValue']) {
            $html = [];
            $html[] = '<div class="alert alert-info" role="alert" style="max-width: ' . $width . 'px">';
            $html[] = LocalizationUtility::translate(
                'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:' .
                'tx_mediapool_domain_model_video.empty_field'
            );
            $html[] = '</div>';
            $resultArray['html'] = implode(LF, $html);
            return $resultArray;
        }

        $this->view->setTemplatePathAndFilename(
            ExtensionManagementUtility::extPath('mediapool') .
            'Resources/Private/Templates/InlineVideoElement/InlineVideoElement.html'
        );
        $this->view->assignMultiple([
            'elementId' => $parameterArray['itemFormElID'],
            'videos' => $parameterArray['itemFormElValue'],
            'amountOfVideos' => count($parameterArray['itemFormElValue'])
        ]);
        $resultArray['html'] = $this->view->render();
        return $resultArray;
    }
}

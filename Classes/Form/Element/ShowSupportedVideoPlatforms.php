<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Form\Element;

use JWeiland\Mediapool\Import\Playlist\PlaylistImportInterface;
use JWeiland\Mediapool\Import\Video\VideoImportInterface;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\MathUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ShowSupportedVideoPlatforms extends AbstractFormElement
{
    protected array $playlistImporters = [];

    protected array $videoImporters = [];

    public function __construct(iterable $playlistImporters, iterable $videoImporters)
    {
        foreach ($playlistImporters as $playlistImporter) {
            if ($playlistImporter instanceof PlaylistImportInterface) {
                $this->playlistImporters[] = $playlistImporter;
            }
        }

        foreach ($videoImporters as $videoImporter) {
            if ($videoImporter instanceof VideoImportInterface) {
                $this->videoImporters[] = $videoImporter;
            }
        }
    }

    /**
     * Render input field
     */
    public function render(): array
    {
        $config = $this->data['parameterArray']['fieldConf']['config'];

        $size = MathUtility::forceIntegerInRange(
            $this->config['size'] ?? $this->defaultInputWidth,
            $this->minimumInputWidth,
            $this->maxInputWidth,
        );

        $width = $this->formMaxWidth($size);

        $videoPlatformsHTML = [];
        $videoPlatformsHTML[] = '<div class="form-control-wrap" style="max-width: ' . $width . 'px">';
        $videoPlatformsHTML[] = '<div class="form-wizards-wrap">';
        $videoPlatformsHTML[] = '<div class="form-wizards-element">';
        $videoPlatformsHTML[] = $this->getSupportedVideoPlatformsHTML($config);
        $videoPlatformsHTML[] = '</div>';
        $videoPlatformsHTML[] = '</div>';
        $videoPlatformsHTML[] = '</div>';
        $videoPlatformsHTML = implode(LF, $videoPlatformsHTML);

        return [
            'html' => $videoPlatformsHTML,
        ];
    }

    /**
     * Get HTML for supported video platforms
     */
    protected function getSupportedVideoPlatformsHTML(array $config): string
    {
        $html = LocalizationUtility::translate(
            'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:render_type.' .
            'video_link_element.supported_video_platforms',
        ) . '<br />';

        try {
            if ($config['importType'] === 'playlist') {
                $registeredImporters = $this->playlistImporters;
            } else {
                $registeredImporters = $this->videoImporters;
            }

            foreach ($registeredImporters as $registeredImporter) {
                $html .= sprintf(
                    '<span class="label label-primary">%s</span>',
                    $registeredImporter->getPlatformName(),
                );
            }
        } catch (\Exception $e) {
        }

        return $html;
    }
}

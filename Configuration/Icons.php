<?php

/*
 * This file is part of the package jweiland/glossary2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'mediapool-mediapool' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mediapool/Resources/Public/Icons/tx_mediapool_domain_model_video.svg',
    ],
    'mediapool-gallery' => [
        'provider' => SvgIconProvider::class,
        'source' => 'EXT:mediapool/Resources/Public/Icons/gallery.svg',
    ],
];

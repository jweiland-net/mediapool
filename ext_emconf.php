<?php

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mediapool',
    'description' => 'Embed your favorite YouTube Videos and Playlists. Import description, title and more by just pasting the YouTube link.',
    'category' => 'plugin',
    'state' => 'stable',
    'author' => 'Stefan Froemken',
    'author_email' => 'projects@jweiland.net',
    'author_company' => 'jweiland.net',
    'version' => '5.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.3-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

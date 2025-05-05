<?php

/**
 * The source of icons used in this extension is
 * https://www.google.com/design/icons/ (License: https://github.com/google/material-design-icons/blob/master/LICENSE)
 * https://icomoon.io Pack IcoMoon - Free (License: https://creativecommons.org/licenses/by/4.0/)
 */

$EM_CONF[$_EXTKEY] = [
    'title' => 'Mediapool',
    'description' => 'Embed your favorite YouTube Videos and Playlists. Import description, title and more by just pasting the YouTube link.',
    'category' => 'plugin',
    'state' => 'stable',
    'author' => 'Stefan Froemken',
    'author_email' => 'projects@jweiland.net',
    'author_company' => 'jweiland.net',
    'version' => '3.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.23-12.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

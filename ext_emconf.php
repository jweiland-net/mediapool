<?php
/**
 * The source of icons used in this extension is
 * https://www.google.com/design/icons/ (License: https://github.com/google/material-design-icons/blob/master/LICENSE)
 * https://icomoon.io Pack IcoMoon - Free (License: https://creativecommons.org/licenses/by/4.0/)
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Mediapool',
    'description' =>
        'Embed your favorite YouTube Videos and Playlists. Import description, title and more by just pasting the YouTube link.',
    'category' => 'plugin',
    'state' => 'stable',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Pascal Rinker',
    'author_email' => 'projects@jweiland.net',
    'author_company' => 'jweiland.net',
    'version' => '1.4.0',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-9.5.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

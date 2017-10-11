<?php
/**
 * The source of icons used in this extension is https://icomoon.io - IcoMoon - Free
 * License of those icons: https://creativecommons.org/licenses/by/4.0/
 */
$EM_CONF[$_EXTKEY] = [
    'title' => 'Mediapool',
    'description' =>
        'Embed your favorite YouTube Videos and Playlists.' .
        'Import description, title and more by just pasting the YouTube link.',
    'category' => 'plugin',
    'state' => 'beta',
    'uploadfolder' => 0,
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'author' => 'Pascal Rinker',
    'author_email' => 'projects@jweiland.net',
    'author_company' => 'jweiland.net',
    'version' => '0.0.1',
    'constraints' => [
        'depends' => [
            'typo3' => '8.7.0-8.7.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];

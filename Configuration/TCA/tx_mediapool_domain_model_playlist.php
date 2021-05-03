<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_playlist',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'default_sortby' => 'crdate',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime'
        ],
        'searchFields' => 'title,link',
        'iconfile' => 'EXT:mediapool/Resources/Public/Icons/tx_mediapool_domain_model_playlist.svg'
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'date',
                'default' => 0,
            ]
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'date',
                'default' => 0,
                'range' => [
                    'upper' => mktime(0, 0, 0, 1, 1, 2038),
                ]
            ]
        ],
        'pid' => [
            'config' => [
                'type' => 'passthrough',
            ]
        ],
        'link' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_playlist.link',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required',
                'default' => '',
                'placeholder' => 'https://www.youtube.com/watch?v=Fm5SoReSv5M',
                'renderType' => 'videoLink',
                'importType' => 'playlist',
            ]
        ],
        'title' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_playlist.title',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'renderType' => 'videoHeader',
            ]
        ],
        'videos' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_playlist.videos',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_mediapool_domain_model_video',
                'multiple' => true,
                'renderType' => 'inlineVideo',
                'foreign_table' => 'tx_mediapool_domain_model_video',
                'MM' => 'tx_mediapool_playlist_video_mm',
                'minitems' => 0,
                'maxitems' => 1000,
            ]
        ],
        'playlist_id' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'thumbnail' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'link,title,videos,playlist_id,pid'
        ],
    ],
];

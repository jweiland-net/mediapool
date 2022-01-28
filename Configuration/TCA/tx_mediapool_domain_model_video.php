<?php
return [
    'ctrl' => [
        'title' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_video',
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
        'iconfile' => 'EXT:mediapool/Resources/Public/Icons/tx_mediapool_domain_model_video.svg'
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
        'link' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_video.link',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required',
                'default' => '',
                'placeholder' => 'https://www.youtube.com/watch?v=Fm5SoReSv5M',
                'renderType' => 'videoLink',
            ]
        ],
        'upload_date' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_video.upload_date',
            'config' => [
                'type' => 'input',
                'eval' => 'datetime',
                'renderType' => 'videoText',
            ]
        ],
        'title' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_video.title',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'renderType' => 'videoHeader',
            ]
        ],
        'player_html' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_video.player_html',
            'config' => [
                'type' => 'user',
                'renderType' => 'videoPlayer'
            ]
        ],
        'description' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_video.description',
            'config' => [
                'type' => 'text',
                'renderType' => 'videoText',
            ]
        ],
        'slug' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:slug',
            'config' => [
                'type' => 'slug',
                'generatorOptions' => [
                    'fields' => ['title'],
                    'fieldSeparator' => '/',
                    'prefixParentPageSlug' => false,
                    'replacements' => [
                        '/' => ''
                    ],
                    'fallbackCharacter' => '-',
                    'eval' => 'uniqueInSite',
                    'default' => ''
                ]
            ]
        ],
        'video_id' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'thumbnail' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'thumbnail_large' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'link,upload_date,title,player_html,description,slug,thumbnail,thumbnail_large'
        ],
    ],
];

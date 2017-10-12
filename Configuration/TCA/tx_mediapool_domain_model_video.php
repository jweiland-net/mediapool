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
    'interface' => [
        'showRecordFieldList' => 'hidden,starttime,endtime,title,description,upload_date,link'
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30
            ]
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0
            ]
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'date',
                'default' => 0,
            ]
        ],
        'endtime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.endtime',
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
        'title' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_video.title',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'renderType' => 'videoHeader',
            ]
        ],
        'description' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_video.description',
            'config' => [
                'type' => 'text',
                'renderType' => 'videoText',
            ]
        ],
        'upload_date' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_video.upload_date',
            'config' => [
                'type' => 'input',
                'eval' => 'datetime',
                'renderType' => 'videoText',
            ]
        ],
        'link' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_video.link',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required',
                'default' => '',
                'placeholder' => 'https://www.youtube.com/watch?v=Fm5SoReSv5M',
                'renderType' => 'videoLink',
            ]
        ],
        'player_html' => [
            'exclude' => true,
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_video.player_html',
            'config' => [
                'type' => 'user',
                'userFunc' => \JWeiland\Mediapool\Tca\VideoPlayer::class . '->render'
            ]
        ]
    ],
    'types' => [
        '0' => [
            'showitem' => 'link,upload_date,title,player_html,description,'
        ],
    ],
];

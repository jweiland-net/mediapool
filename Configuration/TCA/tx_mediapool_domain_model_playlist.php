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
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title',
        'iconfile' => 'EXT:mediapool/Resources/Public/Icons/tx_mediapool_domain_model_playlist.svg',
    ],
    'types' => [
        '0' => [
            'showitem' => 'title,slug,link,videos,categories',
        ],
    ],
    'columns' => [
        't3ver_label' => [
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 30,
            ],
        ],
        'hidden' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => 0,
            ],
        ],
        'starttime' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'date',
                'default' => 0,
            ],
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
                ],
            ],
        ],
        'pid' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'title' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_playlist.title',
            'config' => [
                'type' => 'input',
                'eval' => 'trim',
                'renderType' => 'videoHeader',
            ],
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
                        '/' => '',
                    ],
                    'fallbackCharacter' => '-',
                    'eval' => 'uniqueInSite',
                    'default' => '',
                ],
            ],
        ],
        'link' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_playlist.link',
            'config' => [
                'type' => 'input',
                'eval' => 'trim,required',
                'default' => '',
                'placeholder' => 'https://www.youtube.com/playlist?list=PLbDO1duet8JWl9BJeCxM5z4Zi3nMcwEIE',
                'renderType' => 'videoLink',
                'importType' => 'playlist',
            ],
        ],
        'videos' => [
            'label' => 'LLL:EXT:mediapool/Resources/Private/Language/locallang_db.xlf:tx_mediapool_domain_model_playlist.videos',
            'config' => [
                'type' => 'group',
                'allowed' => 'tx_mediapool_domain_model_video',
                'multiple' => true,
                'renderType' => 'inlineVideo',
                'foreign_table' => 'tx_mediapool_domain_model_video',
                'MM' => 'tx_mediapool_playlist_video_mm',
                'minitems' => 0,
                'maxitems' => 1000,
            ],
        ],
        'categories' => [
            'exclude' => true,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_category.categories',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectTree',
                'treeConfig' => [
                    'parentField' => 'parent',
                    'appearance' => [
                        'showHeader' => true,
                        'expandAll' => true,
                        'maxLevels' => 99,
                    ],
                ],
                'MM' => 'sys_category_record_mm',
                'MM_match_fields' => [
                    'fieldname' => 'categories',
                    'tablenames' => 'tx_mediapool_domain_model_playlist',
                ],
                'MM_opposite_field' => 'items',
                'foreign_table' => 'sys_category',
                'foreign_table_where' => ' AND (sys_category.sys_language_uid = 0 OR sys_category.l10n_parent = 0) ORDER BY sys_category.sorting',
                'size' => 10,
                'minitems' => 0,
                'maxitems' => 99,
                'behaviour' => [
                    'allowLanguageSynchronization' => true,
                ],
            ],
        ],
        'playlist_id' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'thumbnail' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];

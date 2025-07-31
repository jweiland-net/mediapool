<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Upgrade;

use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Core\Utility\Exception\MissingArrayPathException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Migrate switchableControllerActions to list_type
 */
#[UpgradeWizard('mediapool_moveOldFlexFormSettingsUpgrade')]
class MoveOldFlexFormSettingsUpgrade implements UpgradeWizardInterface
{
    public function getTitle(): string
    {
        return '[mediapool] Move old FlexForm fields to new FlexForm sheet';
    }

    public function getDescription(): string
    {
        return 'It seems that some fields of FlexForm have not been updated yet. ' .
            'Please start this wizard to re-arrange the fields to their new location.';
    }

    public function updateNecessary(): bool
    {
        $records = $this->getTtContentRecordsWithMediapoolPlugin();
        foreach ($records as $record) {
            $valueFromDatabase = (string)$record['pi_flexform'] !== '' ? GeneralUtility::xml2array($record['pi_flexform']) : [];
            if (!is_array($valueFromDatabase)) {
                continue;
            }

            if (empty($valueFromDatabase)) {
                continue;
            }

            if (!isset($valueFromDatabase['data'])) {
                continue;
            }

            if (!is_array($valueFromDatabase['data'])) {
                continue;
            }

            if (array_key_exists('sDEFAULT', $valueFromDatabase['data'])) {
                return true;
            }

            $checkSettings = [
                'data/sDEF/lDEF/switchableControllerActions',
            ];

            foreach ($checkSettings as $checkSetting) {
                try {
                    if (ArrayUtility::getValueByPath($valueFromDatabase, $checkSetting)) {
                        return true;
                    }
                } catch (MissingArrayPathException) {
                    // If value does not exist, check further requirements
                }
            }
        }

        return false;
    }

    public function executeUpdate(): bool
    {
        $records = $this->getTtContentRecordsWithMediapoolPlugin();
        foreach ($records as $record) {
            $valueFromDatabase = (string)$record['pi_flexform'] !== '' ? GeneralUtility::xml2array($record['pi_flexform']) : [];
            if (!is_array($valueFromDatabase)) {
                continue;
            }

            if (empty($valueFromDatabase)) {
                continue;
            }

            $ttContentType = $this->migrateSwitchableControllerActions($valueFromDatabase, $record['list_type']);

            $connection = $this->getConnectionPool()->getConnectionForTable('tt_content');
            $connection->update(
                'tt_content',
                [
                    'CType' => $ttContentType,
                    'list_type' => '',
                    'pi_flexform' => $this->checkValue_flexArray2Xml($valueFromDatabase),
                ],
                [
                    'uid' => (int)$record['uid'],
                ],
                [
                    'pi_flexform' => Connection::PARAM_STR,
                ],
            );
        }

        return true;
    }

    /**
     * Get all (incl. deleted/hidden) tt_content records with mediapool plugin
     */
    protected function getTtContentRecordsWithMediapoolPlugin(): array
    {
        $queryBuilder = $this->getConnectionPool()->getQueryBuilderForTable('tt_content');
        $queryBuilder->getRestrictions()->removeAll();

        $queryResult = $queryBuilder
            ->select('uid', 'list_type', 'pi_flexform')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq(
                    'CType',
                    $queryBuilder->createNamedParameter('list'),
                ),
                $queryBuilder->expr()->like(
                    'list_type',
                    $queryBuilder->createNamedParameter('mediapool_%'),
                ),
            )
            ->executeQuery();

        $records = [];
        while ($record = $queryResult->fetchAssociative()) {
            $records[] = $record;
        }

        return $records;
    }

    protected function migrateSwitchableControllerActions(array &$valueFromDatabase, string $ttContentType): string
    {
        $actions = '';

        try {
            $actions = ArrayUtility::getValueByPath(
                $valueFromDatabase,
                'data/sDEF/lDEF/switchableControllerActions/vDEF',
            );
        } catch (MissingArrayPathException) {
        }

        if ($ttContentType === 'mediapool_mediapool') {
            $ttContentType = match ($actions) {
                'Video->listRecommended' => 'mediapool_recommended',
                'Video->show;Playlist->listByCategory' => 'mediapool_detail',
                'Video->listRecentByCategory' => 'mediapool_recentbycategory',
                'Playlist->listLatestVideos;Playlist->listVideos' => 'mediapool_latest',
                'Playlist->listVideos' => 'mediapool_list',
                default => 'mediapool_list',
            };
        } elseif ($ttContentType === 'mediapool_gallery') {
            $ttContentType = match ($actions) {
                'Gallery->preview' => 'mediapool_gallerypreview',
                'Gallery->teaser' => 'mediapool_galleryteaser',
                default => 'mediapool_gallerypreview',
            };
        }

        // Remove old reference
        unset($valueFromDatabase['data']['sDEF']['lDEF']['switchableControllerActions']);

        return $ttContentType;
    }

    /**
     * Converts an array to FlexForm XML
     */
    protected function checkValue_flexArray2Xml(array $array): string
    {
        return GeneralUtility::makeInstance(FlexFormTools::class)
            ->flexArray2Xml($array);
    }

    protected function getConnectionPool(): ConnectionPool
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }

    /**
     * @return array<class-string<DatabaseUpdatedPrerequisite>>
     */
    public function getPrerequisites(): array
    {
        return [
            DatabaseUpdatedPrerequisite::class,
        ];
    }
}

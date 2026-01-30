<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Update;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/**
 * Migrate switchableControllerActions to list_type
 */
#[UpgradeWizard('mediapool_flexformKeyUpgrade')]
class FlexFormSettingsKeyUpdate implements UpgradeWizardInterface
{
    public function getTitle(): string
    {
        return '[mediapool] Update FlexForm settings key for mediapool plugin';
    }

    public function getDescription(): string
    {
        return 'Renames settings in flexforms which were using underscore like file_collections' .
            'to fileCollections in tt_content records.';
    }

    public function updateNecessary(): bool
    {
        $queryBuilder = $this->getConnectionPool()
            ?->getConnectionForTable('tt_content')
            ->createQueryBuilder();

        $count = $queryBuilder
            ->count('uid')
            ->from('tt_content')
            ->where(
                $queryBuilder->expr()->like(
                    'CType',
                    $queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards('mediapool') . '%')
                )
            )
            ->andWhere(
                $queryBuilder->expr()
                    ->like('pi_flexform', $queryBuilder->createNamedParameter('%file_collections%'))
            )
            ->executeQuery()
            ->fetchOne();

        return (bool)$count;
    }

    public function executeUpdate(): bool
    {
        $connection = $this->getConnectionPool()->getConnectionForTable('tt_content');

        $queryBuilder = $connection->createQueryBuilder();
        $statements = $queryBuilder
            ->select('uid', 'pi_flexform')
            ->from('tt_content')
            // Filter by your plugin CType to avoid processing everything
            ->where(
                $queryBuilder->expr()->like(
                    'CType',
                    $queryBuilder->createNamedParameter('%' . $queryBuilder->escapeLikeWildcards('mediapool') . '%')
                )
            )
            ->andWhere(
                $queryBuilder->expr()->like(
                    'pi_flexform',
                    $queryBuilder->createNamedParameter('%settings.file_collections%')
                )
            )
            ->executeQuery();

        foreach ($statements->fetchAllAssociative() as $row) {
            $flexFormContent = (string)$row['pi_flexform'];

            // Targeted replacement of the field index attribute
            $updatedFlexForm = str_replace(
                'field index="settings.file_collections"',
                'field index="settings.fileCollections"',
                $flexFormContent
            );

            if ($updatedFlexForm !== $flexFormContent) {
                $connection->update(
                    'tt_content',
                    ['pi_flexform' => $updatedFlexForm],
                    ['uid' => (int)$row['uid']]
                );
            }
        }

        return true;
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

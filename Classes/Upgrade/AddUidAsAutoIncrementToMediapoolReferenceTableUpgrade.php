<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Upgrade;

use Doctrine\DBAL\Exception;
use Doctrine\DBAL\Platforms\MariaDBPlatform;
use Doctrine\DBAL\Platforms\MySQLPlatform;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

/*
 * Upgrade Wizard for adding `uid` column to `tx_mediapool_playlist_video_mm` table. This UpgradeWizard addresses a
 * database migration challenge specific to the table `tx_mediapool_playlist_video_mm` within the TYPO3 extension. In
 * typical scenarios, TYPO3’s own DB Compare in the Install Tool cannot add a `uid` column with `AUTO_INCREMENT` to an
 * existing table because it attempts to create the column first and set the PRIMARY KEY in a separate step. MySQL and
 * MariaDB, however, require both operations to be performed in a single SQL statement, otherwise the migration fails
 * at the SQL level.
 *
 * This UpgradeWizard manually adds the missing `uid` column, combining `AUTO_INCREMENT` and PRIMARY KEY creation
 * atomically. On MySQL/MariaDB, the column is positioned at the beginning of the table using the `FIRST` clause.
 * Since PostgresSQL and SQLite do not support the `FIRST` modifier, the new column appears at the end of the table
 * in those systems.
 *
 * Note: Due to the requirement of the `DatabaseUpdatedPrerequisite` for all UpgradeWizards called from the
 * Install Tool — and because this requirement can never be fulfilled while the table structure remains invalid — this
 * wizard is only executable from the CLI.
 */
#[UpgradeWizard('mediapool_addUidAsAutoIncrementToMediapoolReferenceTableUpgrade')]
class AddUidAsAutoIncrementToMediapoolReferenceTableUpgrade implements UpgradeWizardInterface
{
    public const TABLE = 'tx_mediapool_playlist_video_mm';

    public function getTitle(): string
    {
        return 'Add column "uid" to mediapool reference table';
    }

    public function getDescription(): string
    {
        return 'TYPO3 will first create the `AUTO_INCREMENT` column and then attempt to create the `PRIMARY` index. '
            . 'This approach is invalid because both definitions need to be combined into a single SQL statement to '
            . 'ensure proper functionality. '
            . 'Note: This UpgradeWizard can only be executed from the command line interface (CLI).';
    }
    public function updateNecessary(): bool
    {
        try {
            $schemaManager = $this->getConnectionPool()
                ->getConnectionForTable(self::TABLE)
                ->createSchemaManager();

            return !$schemaManager->introspectTable(self::TABLE)->hasColumn('uid');
        } catch (Exception) {
            return false;
        }
    }

    public function executeUpdate(): bool
    {
        $connection = $this->getConnectionPool()
            ->getConnectionForTable(self::TABLE);

        try {
            $platform = $connection->getDatabasePlatform();
            $isMySqlLike = $platform instanceof MySQLPlatform || $platform instanceof MariaDBPlatform;

            if ($isMySqlLike) {
                $connection->executeStatement('
                    ALTER TABLE `tx_mediapool_playlist_video_mm`
                    ADD `uid` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST
                ');
            } else {
                $connection->executeStatement('
                    ALTER TABLE `tx_mediapool_playlist_video_mm`
                    ADD `uid` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST
                ');
            }
        } catch (Exception) {
            return false;
        }

        return true;
    }

    public function getPrerequisites(): array
    {
        // Do not add DatabaseUpdatedPrerequisite here!
        // As long as the table has an invalid structure, DatabaseUpdatedPrerequisite
        // will never be finished.
        return [];
    }

    public function getConnectionPool(): ConnectionPool
    {
        return GeneralUtility::makeInstance(ConnectionPool::class);
    }
}

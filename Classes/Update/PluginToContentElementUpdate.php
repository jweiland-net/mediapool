<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/masterplan.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Update;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\AbstractListTypeToCTypeUpdate;

/**
 * With TYPO3 13 all plugins have to be declared as content elements (CType) insteadof "list_type"
 */
#[UpgradeWizard('mediapool_migratePluginsToContentElementsUpdate')]
class PluginToContentElementUpdate extends AbstractListTypeToCTypeUpdate
{
    protected function getListTypeToCTypeMapping(): array
    {
        return [
            'mediapool_recommended' => 'mediapool_recommended',
            'mediapool_detail' => 'mediapool_detail',
            'mediapool_recentbycategory' => 'mediapool_recentbycategory',
            'mediapool_latest' => 'mediapool_latest',
            'mediapool_list' => 'mediapool_list',
            'mediapool_gallerypreview' => 'mediapool_gallerypreview',
            'mediapool_galleryteaser' => 'mediapool_galleryteaser',
        ];
    }

    public function getTitle(): string
    {
        return 'EXT:mediapool - Migrate plugins to Content Elements';
    }

    public function getDescription(): string
    {
        return 'The modern way to register plugins for TYPO3 is to register them as content element types. ' .
            'Running this wizard will migrate all jw_forms plugins to content element (CType)';
    }
}

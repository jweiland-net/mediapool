<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Updates;

/**
 * Update empty slugs of mediapool videos
 */
class VideoSlugUpdate extends AbstractSlugUpdate
{
    protected $table = 'tx_mediapool_domain_model_video';

    /**
     * @return string Unique identifier of this updater
     */
    public function getIdentifier(): string
    {
        return 'mediapoolVideoSlug';
    }

    /**
     * @return string Title of this updater
     */
    public function getTitle(): string
    {
        return '[mediapool] Update empty slugs of mediapool videos';
    }

    /**
     * @return string Longer description of this updater
     */
    public function getDescription(): string
    {
        return 'Update mediapool video records with empty slug field.';
    }
}

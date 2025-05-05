<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/glossary2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Updates;

/**
 * Update empty slugs of mediapool playlists
 */
class PlaylistSlugUpdate extends AbstractSlugUpdate
{
    protected $table = 'tx_mediapool_domain_model_playlist';

    public function getIdentifier(): string
    {
        return 'mediapoolPlaylistSlug';
    }

    public function getTitle(): string
    {
        return '[mediapool] Update empty slugs of mediapool playlists';
    }

    public function getDescription(): string
    {
        return 'Update mediapool playlist records with empty slug field.';
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Import\Playlist;

/**
 * For use to add video platforms to this extension like YouTubeVideoPlatform
 * To add a new playlist import, your class must implement interface PlaylistImportInterface
 */
interface PlaylistImportInterface
{
    /**
     * This method must return an array with the following structure
     * [
     *     'fieldArray' => [
     *         'title' => 'Video title',
     *         'videos' => '<comma separated list of record uids>'
     *      ],
     *     'dataHandler' => [] DataHandler compatible array, you can put your videos into here !
     * ];
     *
     * @param string $playlistLink like https://www.youtube.com/playlist?list=PL-ABvQXa8oyE4zbwSy4V6K5YTD6S_lhu-
     * @param int $pid to store video records created with PlaylistImport
     * @return array as showed above
     */
    public function getPlaylistInformation(string $playlistLink, int $pid): array;

    /**
     * Returns PlatformName
     */
    public function getPlatformName(): string;

    /**
     * Returns PlatformHosts
     */
    public function getPlatformHosts(): array;
}

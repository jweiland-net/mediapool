<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Playlist
 */
class Playlist extends AbstractEntity
{
    /**
     * Title
     * imported from video platform
     *
     * @var string
     */
    protected $title = '';

    /**
     * Playlist link
     * imported from video platform
     *
     * @var string
     */
    protected $link = '';

    /**
     * Video Identifier
     *
     * Please use a prefix for a video platform
     * like <prefix>_<videoIdFromPlatform>
     * e.g. yt_tNtENjljxVo
     *
     * @var string
     */
    protected $playlist_id = '';

    /**
     * Videos of this playlist
     * imported from video platform
     *
     * @var ObjectStorage<Video>
     */
    protected $videos;

    /**
     * Path to Thumbnail
     * this can be local AND external
     * like: /fileadmin/playlists/playlist.jpg
     * or: https://domain.tld/thumbs/playlist.jpg
     *
     * @var string
     */
    protected $thumbnail = '';

    /**
     * @var string
     */
    protected $slug = '';

    public function __construct()
    {
        $this->videos = new ObjectStorage();
    }

    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject()
    {
        $this->videos = $this->videos ?? new ObjectStorage();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getPlaylistId(): string
    {
        return $this->playlist_id;
    }

    public function setPlaylistId(string $playlist_id): void
    {
        $this->playlist_id = $playlist_id;
    }

    public function addVideo(Video $video): void
    {
        $this->videos->attach($video);
    }

    public function removeVideo(Video $videoToRemove): void
    {
        $this->videos->detach($videoToRemove);
    }

    public function getVideos(): ObjectStorage
    {
        return $this->videos;
    }

    public function setVideos(ObjectStorage $videos): void
    {
        $this->videos = $videos;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }
}

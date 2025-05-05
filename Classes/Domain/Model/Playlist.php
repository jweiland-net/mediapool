<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Domain\Model;

use TYPO3\CMS\Extbase\Annotation as Extbase;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Playlist extends AbstractEntity
{
    /**
     * Title imported from video platform
     */
    protected string $title = '';

    /**
     * Playlist link imported from video platform
     */
    protected string $link = '';

    /**
     * Video Identifier
     *
     * Please use a prefix for a video platform
     * like <prefix>_<videoIdFromPlatform>
     * e.g. yt_tNtENjljxVo
     */
    protected string $playlist_id = '';

    /**
     * Videos of this playlist imported from video platform
     *
     * @var ObjectStorage<Video>
     */
    protected ObjectStorage $videos;

    /**
     * @var ObjectStorage<Category>
     * @Extbase\ORM\Lazy
     */
    protected ObjectStorage $categories;

    /**
     * Path to Thumbnail
     * this can be local AND external
     * like: /fileadmin/playlists/playlist.jpg
     * or: https://domain.tld/thumbs/playlist.jpg
     */
    protected string $thumbnail = '';

    protected string $slug = '';

    public function __construct()
    {
        $this->videos = new ObjectStorage();
        $this->categories = new ObjectStorage();
    }

    /**
     * Called again with initialize object, as fetching an entity from the DB does not use the constructor
     */
    public function initializeObject()
    {
        $this->videos = $this->videos ?? new ObjectStorage();
        $this->categories = $this->categories ?? new ObjectStorage();
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

    public function getCategories(): ObjectStorage
    {
        return $this->categories;
    }

    public function setCategories(ObjectStorage $categories): void
    {
        $this->categories = $categories;
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

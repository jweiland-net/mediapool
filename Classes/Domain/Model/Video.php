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

/**
 * Class Video
 */
class Video extends AbstractEntity
{
    /**
     * Video title imported from video platform
     *
     * @var string
     */
    protected $title = '';

    /**
     * Video description imported from video platform
     *
     * @var string
     */
    protected $description = '';

    /**
     * Upload date imported from video platform
     *
     * @var \DateTime
     */
    protected $uploadDate;

    /**
     * Link to the video (e.g. YouTube)
     *
     * @var string
     */
    protected $link = '';

    /**
     * Code to embed the e.g. YouTube-Player
     *
     * @var string
     */
    protected $playerHtml = '';

    /**
     * Video Identifier
     *
     * Please use a prefix for a video platform
     * like <prefix>_<videoIdFromPlatform>
     * e.g. yt_tNtENjljxVo
     *
     * @var string
     */
    protected $videoId = '';

    /**
     * Path to Thumbnail
     * this can be local AND external
     * like: /fileadmin/videos/video.jpg
     * or: https://domain.tld/thumbs/video.jpg
     *
     * @var string
     */
    protected $thumbnail = '';

    /**
     * Path to the large Thumbnail
     * same than $thumbnail but larger
     *
     * @var string
     */
    protected $thumbnailLarge = '';

    /**
     * @var string
     */
    protected $slug = '';

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getUploadDate(): ?\DateTime
    {
        return $this->uploadDate;
    }

    public function setUploadDate(\DateTime $uploadDate): void
    {
        $this->uploadDate = $uploadDate;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getPlayerHtml(): string
    {
        return $this->playerHtml;
    }

    public function setPlayerHtml(string $playerHtml): void
    {
        $this->playerHtml = $playerHtml;
    }

    public function getVideoId(): string
    {
        return $this->videoId;
    }

    /**
     * Sets VideoId
     *
     * Please use a prefix for a video platform
     * like <prefix>_<videoIdFromPlatform>
     * e.g. yt_tNtENjljxVo
     *
     * @param string $videoId
     */
    public function setVideoId(string $videoId): void
    {
        $this->videoId = $videoId;
    }

    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    public function setThumbnail(string $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function getThumbnailLarge(): string
    {
        return $this->thumbnailLarge;
    }

    public function setThumbnailLarge(string $thumbnailLarge): void
    {
        $this->thumbnailLarge = $thumbnailLarge;
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

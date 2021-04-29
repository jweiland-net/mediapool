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
     * Video title
     * imported from video platform
     *
     * @var string
     */
    protected $title = '';

    /**
     * Video description
     * imported from video platform
     *
     * @var string
     */
    protected $description = '';

    /**
     * Upload date
     * imported from video platform
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
     * Returns Title
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Sets Title
     *
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * Returns Description
     *
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets Description
     *
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Returns UploadDate
     *
     * @return \DateTime
     */
    public function getUploadDate(): \DateTime
    {
        return $this->uploadDate;
    }

    /**
     * Sets UploadDate
     *
     * @param \DateTime $uploadDate
     */
    public function setUploadDate(\DateTime $uploadDate)
    {
        $this->uploadDate = $uploadDate;
    }

    /**
     * Returns Link
     *
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * Sets Link
     *
     * @param string $link
     */
    public function setLink(string $link)
    {
        $this->link = $link;
    }

    /**
     * Returns PlayerHTML
     *
     * @return string
     */
    public function getPlayerHtml(): string
    {
        return $this->playerHtml;
    }

    /**
     * Sets PlayerHTML
     *
     * @param string $playerHtml
     */
    public function setPlayerHtml(string $playerHtml)
    {
        $this->playerHtml = $playerHtml;
    }

    /**
     * Returns VideoId
     *
     * @return string
     */
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
    public function setVideoId(string $videoId)
    {
        $this->videoId = $videoId;
    }

    /**
     * Returns Thumbnail
     *
     * @return string
     */
    public function getThumbnail(): string
    {
        return $this->thumbnail;
    }

    /**
     * Sets Thumbnail
     *
     * @param string $thumbnail
     */
    public function setThumbnail(string $thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * Returns ThumbnailLarge
     *
     * @return string
     */
    public function getThumbnailLarge(): string
    {
        return $this->thumbnailLarge;
    }

    /**
     * Sets ThumbnailLarge
     *
     * @param string $thumbnailLarge
     */
    public function setThumbnailLarge(string $thumbnailLarge)
    {
        $this->thumbnailLarge = $thumbnailLarge;
    }
}

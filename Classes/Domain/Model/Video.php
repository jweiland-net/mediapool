<?php
namespace JWeiland\Mediapool\Domain\Model;

/*
* This file is part of the TYPO3 CMS project.
*
* It is free software; you can redistribute it and/or modify it under
* the terms of the GNU General Public License, either version 2
* of the License, or any later version.
*
* For the full copyright and license information, please read the
* LICENSE.txt file that was distributed with this source code.
*
* The TYPO3 project - inspiring people to share!
*/

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Class Video
 *
 * @package JWeiland\Mediapool\Domain\Model;
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
}

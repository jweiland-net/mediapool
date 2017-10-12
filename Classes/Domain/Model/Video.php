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
     * UID
     *
     * @var int
     */
    protected $uid = 0;

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
    protected $playerHTML = '';

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
     * Returns Uid
     *
     * @return int
     */
    public function getUid(): int
    {
        return $this->uid;
    }

    /**
     * Sets Uid
     *
     * @param int $uid
     */
    public function setUid(int $uid)
    {
        $this->uid = $uid;
    }

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
    public function getPlayerHTML(): string
    {
        return $this->playerHTML;
    }

    /**
     * Sets PlayerHTML
     *
     * @param string $playerHTML
     */
    public function setPlayerHTML(string $playerHTML)
    {
        $this->playerHTML = $playerHTML;
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
}

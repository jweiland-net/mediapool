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

use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Class Playlist
 *
 * @package JWeiland\Mediapool\Domain\Model;
 */
class Playlist
{
    /**
     * UID
     *
     * @var int
     */
    protected $uid = 0;

    /**
     * PageID
     *
     * @var int
     */
    protected $pid = 0;

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
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\JWeiland\Mediapool\Domain\Model\Video>
     */
    protected $videos;

    /**
     * Playlist constructor.
     */
    public function __construct()
    {
        $this->videos = new ObjectStorage();
    }

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
     * Returns Pid
     *
     * @return int
     */
    public function getPid(): int
    {
        return $this->pid;
    }

    /**
     * Sets Pid
     *
     * @param int $pid
     */
    public function setPid(int $pid)
    {
        $this->pid = $pid;
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
     * Returns PlaylistId
     *
     * @return string
     */
    public function getPlaylistId(): string
    {
        return $this->playlist_id;
    }

    /**
     * Sets PlaylistId
     *
     * @param string $playlist_id
     */
    public function setPlaylistId(string $playlist_id)
    {
        $this->playlist_id = $playlist_id;
    }

    /**
     * Adds a Video
     *
     * @param Video $video
     * @return void
     */
    public function addVideo(Video $video)
    {
        $this->videos->attach($video);
    }

    /**
     * Removes a Video
     *
     * @param Video $videoToRemove The Video to be removed
     * @return void
     */
    public function removeVideo(Video $videoToRemove)
    {
        $this->videos->detach($videoToRemove);
    }

    /**
     * Returns Videos
     *
     * @return ObjectStorage
     */
    public function getVideos(): ObjectStorage
    {
        return $this->videos;
    }

    /**
     * Sets Videos
     *
     * @param ObjectStorage $videos
     */
    public function setVideos(ObjectStorage $videos)
    {
        $this->videos = $videos;
    }
}

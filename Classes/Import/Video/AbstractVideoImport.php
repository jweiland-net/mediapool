<?php
namespace JWeiland\Mediapool\Import\Video;

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

use JWeiland\Mediapool\Domain\Model\Video;

/**
 * Class AbstractVideoPlatform
 * for use to a add video importer to this extension
 * like YouTubeVideoImport
 *
 * To add a new video platform you must declare your video import class
 * inside your extensions ext_localconf.php for the video import hook
 * $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport'][<PlatformName>] = ...
 *
 * @package JWeiland\Mediapool\Import\Video;
 */
class AbstractVideoImport
{
    /**
     * Name of the video platform
     * e.g. YouTube
     *
     * @var string
     */
    protected $platformName = '';

    /**
     * Array filled with hosts of this video importer
     * e.g. ['https://youtube.com', 'https://youtu.be']
     * this hosts are needed to identify the link of
     * the video object ($video->link)
     *
     * @var array
     */
    protected $platformHosts = [];

    /**
     * This method must return a video object filled with
     * all given properties like title, description, ...
     * that are related to the $video->link from a video
     * platform. Otherwise the method must return bool false
     * to signal a wrong link.
     *
     * @param Video $video an existing or new video object with a link
     * @return Video|bool filled video object or bool false
     */
    public function getFilledVideoObject(Video $video)
    {
        // modify the video object
        $video->setTitle('Set a title');
        $video->setDescription('Set a description');
        $video->setPlayerHTML('<iframe>Video embed code</iframe>');
        $video->setUploadDate(new \DateTime());
        // add a prefix before the platform video id like <prefix>_<videoId>
        $video->setVideoId('pr_dk35023jfn1');
        // return the modified video object
        return $video;
    }

    /**
     * Returns PlatformName
     *
     * @return string
     */
    public function getPlatformName(): string
    {
        return $this->platformName;
    }

    /**
     * Returns PlatformHosts
     *
     * @return array
     */
    public function getPlatformHosts(): array
    {
        return $this->platformHosts;
    }
}

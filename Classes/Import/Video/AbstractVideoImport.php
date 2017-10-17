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
use JWeiland\Mediapool\Import\AbstractImport;

/**
 * Class AbstractVideoPlatform
 * for use to a add video importer to this extension
 * like YouTubeVideoImport
 * To add a new video platform you must declare your video import class
 * inside your extensions ext_localconf.php for the video import hook
 * $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport'][<PlatformName>] = ...
 *
 * @package JWeiland\Mediapool\Import\Video;
 */
abstract class AbstractVideoImport extends AbstractImport
{
    /**
     * This method must return a video object filled with
     * all given properties like title, description, ...
     * that are related to the $video->link from a video
     * platform. Otherwise the method must return null or
     * throw a exception on error.
     *
     * // modify the video object
     * $video->setTitle('Set a title');
     * $video->setDescription('Set a description');
     * $video->setPlayerHTML('<iframe>Video embed code</iframe>');
     * $video->setUploadDate(new \DateTime());
     * // add a prefix before the platform video id like <prefix>_<videoId>
     * $video->setVideoId('pr_dk35023jfn1');
     * $video->setThumbnail('/path/to/thumbnail.jpg');
     * otherwise $video->setThumbnail('https://domain.tld/img.jpg');
     *
     * @param Video $video an existing or new video object with a link
     * @return Video filled video object or null
     */
    abstract public function getFilledVideoObject(Video $video) : Video;
}

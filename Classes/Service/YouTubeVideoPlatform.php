<?php
namespace JWeiland\Mediapool\Service;

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
 * Class YouTubeVideoPlatform
 *
 * @package JWeiland\Mediapool\Service;
 */
class YouTubeVideoPlatform extends AbstractVideoPlatform
{
    /**
     * Name of the video platform
     *
     * @var string
     */
    protected $platformName = 'YouTube';

    /**
     * Platform hosts
     *
     * @var array
     */
    protected $platformHosts = ['https://youtube.com' , 'https://youtu.be'];

    /**
     * Video
     *
     * @var Video
     */
    protected $video;

    /**
     * YouTube Video ID
     *
     * @var string
     */
    protected $videoId = '';

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
        // todo: add API-Key to ext_conf_template
        // todo: check if API-Key is set
        $this->video = $video;
        if (($videoId = $this->getVideoId()) === false) {
            return false;
        }
        $this->videoId = $videoId;
        // todo: collect video information from YouTube Data API
    }

    /**
     * Returns the if of passed video link if valid.
     * Otherwise returns false
     *
     * @return string|bool false if link is not valid
     */
    protected function getVideoId()
    {
        $parsedLink = parse_url($this->video->getLink(), PHP_URL_QUERY);
        parse_str($parsedLink['query'], $parsedQuery);
        if (isset($parsedQuery['v'])) {
            return $parsedQuery['v'];
        }
        return false;
    }
}

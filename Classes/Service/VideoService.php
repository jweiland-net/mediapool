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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class VideoService
 *
 * @package JWeiland\Mediapool\Service;
 */
class VideoService
{
    const COLLECTION_VIDEO_INFORMATION_FAILED = 1;
    const NO_VIDEO_PLATFORM_MATCH = 2;

    /**
     * Returns a video object filled with
     * all given properties like title, description, ...
     *
     * @param Video $video $video->link must be filled with a url!
     * @return Video|int returns int that equals constants of this class to signal an specified error
     * @throws \Exception if a registered video platform is not type of AbstractVideoPlatform
     */
    public function getFilledVideoObject(Video $video)
    {
        $videoPlatforms = $this->getRegisteredVideoPlatforms();
        foreach ($videoPlatforms as $videoPlatformNamespace) {
            /** @var AbstractVideoPlatform $videoPlatform */
            $videoPlatform = GeneralUtility::makeInstance($videoPlatformNamespace);
            if (!$videoPlatform instanceof AbstractVideoPlatform) {
                throw new \Exception(
                    sprintf(
                        'The registered video platform %s is not type of %s!',
                        $videoPlatformNamespace,
                        AbstractVideoPlatform::class
                    ),
                    1507730887
                );
            }
            if ($this->isVideoFromVideoPlatform($video, $videoPlatform)) {
                if (($video =  $videoPlatform->getFilledVideoObject($video)) !== false) {
                    return $video;
                } else {
                    return self::COLLECTION_VIDEO_INFORMATION_FAILED;
                }
            }
        }
        return self::NO_VIDEO_PLATFORM_MATCH;
    }

    /**
     * Returns an array with registered video platforms.
     * Does not validate if the registered classes are children from AbstractVideoPlatform!
     *
     * @return array
     * @throws \Exception if no video platforms are registered
     */
    protected function getRegisteredVideoPlatforms(): array
    {
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoPlatforms'])) {
            return $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoPlatforms'];
        } else {
            throw new \Exception(
                'At least one video platform must be registered to get information about a video!' .
                ' Please check $GLOBALS[\'TYPO3_CONF_VARS\'][\'EXTCONF\'][\'mediapool\'][\'videoPlatforms\'] for' .
                ' registered video platforms.',
                1507729404
            );
        }
    }

    /**
     * Checks if one of the hosts from $videoPlatform matches with
     * $video->link.
     *
     * @param Video $video
     * @param AbstractVideoPlatform $videoPlatform
     * @return bool true if true, false if false you know ;)
     */
    protected function isVideoFromVideoPlatform(Video $video, AbstractVideoPlatform $videoPlatform): bool
    {
        foreach ($videoPlatform->getPlatformHosts() as $host) {
            if (strpos($host, $video->getLink()) === 0) {
                return true;
            }
        }
        return false;
    }
}

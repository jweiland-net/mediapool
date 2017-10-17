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
use JWeiland\Mediapool\Utility\VideoPlatformUtility;
use JWeiland\Mediapool\Import\Video\AbstractVideoImport;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class VideoService
 *
 * ! use ObjectManager to get an instance of this class !
 *
 * @package JWeiland\Mediapool\Service;
 */
class VideoService implements SingletonInterface
{
    const COLLECTION_VIDEO_INFORMATION_FAILED = 1;
    const NO_VIDEO_PLATFORM_MATCH = 2;

    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * inject objectManager
     *
     * @param ObjectManager $objectManager
     * @return void
     */
    public function injectObjectManager(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Returns a video object filled with
     * all given properties like title, description, ...
     *
     * @param Video $video $video->link must be filled with a url!
     * @return Video|int returns int that equals constants of this class to signal an specified error
     */
    public function getFilledVideoObject(Video $video)
    {
        $videoPlatforms = VideoPlatformUtility::getRegisteredVideoImporters();
        foreach ($videoPlatforms as $videoPlatformNamespace) {
            /** @var AbstractVideoImport $videoPlatform */
            $videoPlatform = $this->objectManager->get($videoPlatformNamespace);
            VideoPlatformUtility::checkVideoImportClass($videoPlatform);
            if ($this->isVideoFromVideoPlatform($video, $videoPlatform)) {
                if ($video =  $videoPlatform->getFilledVideoObject($video)) {
                    return $video;
                } else {
                    return self::COLLECTION_VIDEO_INFORMATION_FAILED;
                }
            }
        }
        return self::NO_VIDEO_PLATFORM_MATCH;
    }

    /**
     * Checks if one of the hosts from $videoPlatform matches with
     * $video->link.
     *
     * @param Video $video
     * @param AbstractVideoImport $videoPlatform
     * @return bool true if true, false if false you know ;)
     */
    protected function isVideoFromVideoPlatform(Video $video, AbstractVideoImport $videoPlatform): bool
    {
        foreach ($videoPlatform->getPlatformHosts() as $host) {
            if (strpos($video->getLink(), $host) === 0) {
                return true;
            }
        }
        return false;
    }
}

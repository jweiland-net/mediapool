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

use JWeiland\Mediapool\Import\NoApiKeyException;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;

/**
 * Class YouTubeService
 *
 * @package JWeiland\Mediapool\Service;
 */
class YouTubeService
{
    /**
     * Platform prefix. Used for video id
     */
    const PLATFORM_PREFIX = 'yt_';

    /**
     * Object Manager
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * Configuration Utility
     *
     * @var ConfigurationUtility
     */
    protected $configurationUtility;

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
     * inject configurationUtility
     *
     * @param ConfigurationUtility $configurationUtility
     * @return void
     */
    public function injectConfigurationUtility(ConfigurationUtility $configurationUtility)
    {
        $this->configurationUtility = $configurationUtility;
    }

    /**
     * Returns the YouTube Data API v3 key if set.
     * Otherwise throws exception.
     *
     * @return string
     * @throws \Exception
     */
    public function getApiKey() : string
    {
        $extConf = $this->configurationUtility->getCurrentConfiguration('mediapool');
        if ($extConf['youtubeDataApiKey']['value'] != '') {
            return $extConf['youtubeDataApiKey']['value'];
        } else {
            throw new NoApiKeyException(
                'YouTube Data API v3 key is mandatory but not set. Please set an API-Key to get' .
                ' YouTubeVideoImport working (Extension Manager > Mediapool > Configuration).',
                1507791149
            );
        }
    }
}

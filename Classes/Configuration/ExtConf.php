<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Configuration;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * This class streamlines all settings from extension manager
 */
class ExtConf implements SingletonInterface
{
    /**
     * @var string
     */
    protected $youtubeDataApiKey = '';

    public function __construct()
    {
        // get global configuration
        $extConf = GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('mediapool') ?? [];
        // call setter method foreach configuration entry
        foreach ($extConf as $key => $value) {
            $methodName = 'set' . ucfirst($key);
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }
    }

    public function getYoutubeDataApiKey(): string
    {
        return $this->youtubeDataApiKey;
    }

    public function setYoutubeDataApiKey(string $youtubeDataApiKey): void
    {
        $this->youtubeDataApiKey = $youtubeDataApiKey;
    }
}

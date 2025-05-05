<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/glossary2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Configuration;

use JWeiland\Mediapool\Configuration\Exception\MissingYouTubeApiKeyException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * This class streamlines all settings from the extension manager
 */
class ExtConf
{
    protected string $youtubeDataApiKey = '';

    public function __construct(ExtensionConfiguration $extensionConfiguration)
    {
        // get global configuration
        try {
            $extConf = $extensionConfiguration->get('mediapool') ?? [];
            // call setter method foreach configuration entry
            foreach ($extConf as $key => $value) {
                $methodName = 'set' . ucfirst($key);
                if (method_exists($this, $methodName)) {
                    $this->$methodName($value);
                }
            }
        } catch (ExtensionConfigurationExtensionNotConfiguredException | ExtensionConfigurationPathDoesNotExistException $e) {
        }
    }

    public function getYoutubeDataApiKey(): string
    {
        if ($this->youtubeDataApiKey === '') {
            throw new MissingYouTubeApiKeyException(
                'Missing YouTube API key in extension settings of extension: mediapool',
            );
        }

        return $this->youtubeDataApiKey;
    }

    public function setYoutubeDataApiKey(string $youtubeDataApiKey): void
    {
        $this->youtubeDataApiKey = $youtubeDataApiKey;
    }
}

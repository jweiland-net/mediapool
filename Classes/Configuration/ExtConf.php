<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Configuration;

use JWeiland\Mediapool\Configuration\Exception\MissingYouTubeApiKeyException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

/**
 * This class streamlines all settings from the extension manager
 */
#[Autoconfigure(constructor: 'create')]
final readonly class ExtConf
{
    private const EXT_KEY = 'mediapool';

    private const DEFAULT_SETTINGS = [
        'youtubeDataApiKey' => '',
    ];

    public function __construct(
        private string $youtubeDataApiKey = self::DEFAULT_SETTINGS['youtubeDataApiKey'],
    ) {}

    public static function create(ExtensionConfiguration $extensionConfiguration): self
    {
        $extensionSettings = self::DEFAULT_SETTINGS;

        // Overwrite default extension settings with values from EXT_CONF
        try {
            $extensionSettings = array_merge(
                $extensionSettings,
                $extensionConfiguration->get(self::EXT_KEY),
            );
        } catch (ExtensionConfigurationExtensionNotConfiguredException|ExtensionConfigurationPathDoesNotExistException) {
        }

        return new self(
            youtubeDataApiKey: (string)$extensionSettings['youtubeDataApiKey'],
        );
    }

    public function getYoutubeDataApiKey(): string
    {
        if ($this->youtubeDataApiKey === '') {
            throw new MissingYouTubeApiKeyException(
                'Missing YouTube API key in extension settings of extension: mediapool',
                1343309942,
            );
        }

        return $this->youtubeDataApiKey;
    }
}

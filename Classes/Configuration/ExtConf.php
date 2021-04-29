<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Configuration;

use TYPO3\CMS\Core\SingletonInterface;

/**
 * This class streamlines all settings from extension manager
 */
class ExtConf implements SingletonInterface
{
    /**
     * @var string
     */
    protected $youtubeDataApiKey = '';

    /**
     * constructor of this class
     * This method reads the global configuration and calls the setter methods.
     */
    public function __construct()
    {
        // get global configuration
        $extConf = unserialize(
            $GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mediapool'] ?? [],
            [
                'allowed_classes' => false
            ]
        );
        if (is_array($extConf)) {
            // call setter method foreach configuration entry
            foreach ($extConf as $key => $value) {
                $methodName = 'set' . ucfirst($key);
                if (method_exists($this, $methodName)) {
                    $this->$methodName($value);
                }
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

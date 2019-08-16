<?php
namespace JWeiland\Mediapool\Import;

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

use GuzzleHttp\Client;
use JWeiland\Mediapool\AbstractBase;

/**
 * Class AbstractImport
 */
class AbstractImport extends AbstractBase
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
     * this hosts are needed to identify the passed link
     *
     * @var array     */
    protected $platformHosts = [];

    /**
     * Guzzle Client for HTTP requests
     *
     * @var Client
     */
    protected $client;

    /**
     * This property is used by VideoService and PlaylistService.
     * They will only save data if this is false. Otherwise you have
     * to add your own error messages with $this->addFlashMessageAndLog()
     * or your own error method.
     *
     * @var bool
     */
    protected $hasError = false;

    /**
     * inject client
     *
     * @param Client $client
     */
    public function injectClient(Client $client)
    {
        $this->client = $client;
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

    /**
     * Will return true if at least one error occurred.
     *
     * @return bool
     */
    public function hasError(): bool
    {
        return $this->hasError;
    }
}

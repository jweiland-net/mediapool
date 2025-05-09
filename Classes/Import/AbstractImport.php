<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Import;

class AbstractImport
{
    /**
     * Name of the video platform
     * e.g., YouTube
     */
    protected string $platformName = '';

    /**
     * Array filled with hosts of this video importer
     * e.g. ['https://youtube.com', 'https://youtu.be']
     * these hosts are needed to identify the passed link
     */
    protected array $platformHosts = [];

    /**
     * This property is used by VideoService and PlaylistService.
     * They will only save data if this is false. Otherwise, you have
     * to add your own error messages with $this->addFlashMessage()
     * or your own error method.
     */
    protected bool $hasError = false;

    /**
     * Returns PlatformName
     */
    public function getPlatformName(): string
    {
        return $this->platformName;
    }

    /**
     * Returns PlatformHosts
     */
    public function getPlatformHosts(): array
    {
        return $this->platformHosts;
    }

    /**
     * Will return true if at least one error occurred.
     */
    public function hasError(): bool
    {
        return $this->hasError;
    }
}

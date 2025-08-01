<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Import;

abstract readonly class AbstractImport
{
    /**
     * Name of the video platform
     * e.g., YouTube
     */
    protected const PLATFORM_NAME = '';

    /**
     * Array filled with hosts of this video importer
     * e.g. ['https://youtube.com', 'https://youtu.be']
     * these hosts are needed to identify the passed link
     */
    protected const PLATFORM_HOSTS = [];

    public function getPlatformName(): string
    {
        return static::PLATFORM_NAME;
    }

    public function getPlatformHosts(): array
    {
        return static::PLATFORM_HOSTS;
    }
}

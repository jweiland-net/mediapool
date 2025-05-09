<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Import\Video;

/**
 * For use, to add video importer to this extension like YouTubeVideoImport
 * To add a new video platform, your class must implement interface VideoImportInterface
 */
interface VideoImportInterface
{
    /**
     * Fetches information for all passed $videos and returns the information as a DataHandler
     * compatible array.
     *
     * Creates or updates video records from $videos.
     * Make sure to use
     *  NEW... as an array item key for new records OR the record uid for existing records
     *  An array with an entry 'video' that contains video link OR video id OR video link
     *  and additionally in the same array an entry 'pid', which contains the pid. The pid
     *  is not mandatory!
     *
     * e.g.
     * [
     *     4 => ['pid' => 3, 'video' => 'exi0iht_kLw'],
     *     5 => ['pid' => 3, 'video' => 'yt_Vfw1pAmLlY'],
     *     'NEW1234' => ['video' => 'https://youtu.be/jzTVVocFaVE']
     * ]
     * in this example the records 4 and 5 got updated and a new record
     * for jzTVVocFaVE would be created
     *
     * @param int $pid this will be the pid of NEW records
     * @param string $recordUids reference that includes all UIDs as a comma-separated list
     * @param bool $checkExistingVideos if true, the video id in combination with the pid will be checked and no
     *                                  new record will be created if a record with the same video id already exists.
     *                                  Existing videos will be added to $recordUids too!
     * @return array the data array for DataHandler. This is a reference, so it will be modified and can be used
     *               after method call.
     */
    public function processDataArray(
        array $videos,
        int $pid,
        string &$recordUids = '',
        bool $checkExistingVideos = false
    ): array;

    /**
     * Returns PlatformName
     */
    public function getPlatformName(): string;

    /**
     * Returns PlatformHosts
     */
    public function getPlatformHosts(): array;

    /**
     * Will return true if at least one error occurred.
     */
    public function hasError(): bool;
}

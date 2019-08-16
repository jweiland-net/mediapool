<?php
namespace JWeiland\Mediapool\Import\Video;

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

use JWeiland\Mediapool\Import\AbstractImport;

/**
 * Class AbstractVideoPlatform
 * for use to a add video importer to this extension
 * like YouTubeVideoImport
 * To add a new video platform you must declare your video import class
 * inside your extensions ext_localconf.php for the video import hook
 * $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['videoImport'][<PlatformName>] = ...
 */
abstract class AbstractVideoImport extends AbstractImport
{
    /**
     * Fetches information for all passed $videos and returns the information as an DataHandler
     * compatible array.
     *
     * Creates or updates video records from $videos.
     * Make sure to use
     *  NEW... as array item key for new records OR the record uid for existing records
     *  An array with an entry 'video' that contains video link OR video id OR video link
     *  and additionally in the same array an entry 'pid' which contains the pid. The pid
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
     * @param array $videos
     * @param int $pid this will be the pid of NEW records
     * @param string $recordUids reference that includes all UIDs as a comma separated list
     * @param bool $checkExistingVideos if true the video id in combination with the pid will be checked and no
     *                                  new record will be created if a record with the same video id already exists.
     *                                  Existing videos will be added to $recordUids too!
     * @return array the data array for DataHandler. This is a reference so it will be modified and can be used
     *               after method call.
     */
    abstract public function processDataArray(
        array $videos,
        int $pid,
        string &$recordUids = '',
        bool $checkExistingVideos = false
    ): array;
}

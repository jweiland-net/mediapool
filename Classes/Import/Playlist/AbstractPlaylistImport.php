<?php
namespace JWeiland\Mediapool\Import\Playlist;

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
 * Class AbstractPlaylistImport
 * for use to add video platforms to this extension
 * like YouTubeVideoPlatform
 * To add a new playlist import you must declare your class
 * inside your extensions ext_localconf.php for the playlist import hook
 * $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['playlistImport'][<PlatformName>] = ...
 */
abstract class AbstractPlaylistImport extends AbstractImport
{
    /**
     * This method must return an array with the following structure
     * [
     *     'fieldArray' => [
     *         'title' => 'Video title',
     *         'videos' => '<comma separated list of record uids>'
     *      ],
     *     'dataHandler' => [] DataHandler compatible array, you can put your videos into here !
     * ];
     *
     * @param string $playlistLink like https://www.youtube.com/playlist?list=PL-ABvQXa8oyE4zbwSy4V6K5YTD6S_lhu-
     * @param int $pid to store video records created with PlaylistImport
     * @return array as showed above
     */
    abstract public function getPlaylistInformation(string $playlistLink, int $pid): array;
}

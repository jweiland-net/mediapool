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

/**
 * Class AbstractPlaylistImport
 * for use to add video platforms to this extension
 * like YouTubeVideoPlatform
 *
 * To add a new playlist import you must declare your class
 * inside your extensions ext_localconf.php for the playlist import hook
 * $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['mediapool']['playlistImport'][<PlatformName>] = ...
 *
 * @package JWeiland\Mediapool\Import\Playlist;
 */
class AbstractPlaylistImport
{
}

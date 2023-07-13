<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Task;

use JWeiland\Mediapool\Traits\AddFlashMessageTrait;
use JWeiland\Mediapool\Traits\GetPlaylistRepositoryTrait;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Task to update the playlist information with fresh data from YouTube
 */
class UpdatePlaylistInformation extends AbstractTask
{
    use AddFlashMessageTrait;
    use GetPlaylistRepositoryTrait;

    /**
     * Task mode
     * 0 = update all records
     * 1 = update selected records
     *
     * @var int
     */
    public $mode = 0;

    /**
     * Only relevant if $mode == 1
     * comma separated list of pages/folders selection
     *
     * @var string
     */
    public $pageSelection = '';

    public function execute(): bool
    {
        if ($this->mode === 0) {
            // fetch all
            $playlists = $this->getPlaylistRepository()->findAllLinksAndUids();
        } else {
            // fetch selected
            $playlists = $this->getPlaylistRepository()->findLinksAndUidsByPid($this->pageSelection);
        }

        // Early return, if there are no playlists to process
        if ($playlists === []) {
            return true;
        }

        // Create data array for data handler to use the DataHandler Hook
        $data = [];

        foreach ($playlists as $playlist) {
            $data['tx_mediapool_domain_model_playlist'][$playlist['uid']] = [
                'link' => $playlist['link'],
            ];
        }

        $dataHandler = $this->getDataHandler();
        $dataHandler->start($data, []);
        $dataHandler->process_datamap();

        if ($dataHandler->errorLog !== []) {
            foreach ($dataHandler->errorLog as $errorLog) {
                $this->addFlashMessage(
                    'Error',
                    $errorLog
                );
            }

            return false;
        }

        return true;
    }

    private function getDataHandler(): DataHandler
    {
        return GeneralUtility::makeInstance(DataHandler::class);
    }
}

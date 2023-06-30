<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Task;

use JWeiland\Mediapool\Domain\Repository\PlaylistRepository;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Task to update the playlist information with fresh data from YouTube
 */
class UpdatePlaylistInformation extends AbstractTask
{
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

    /**
     * @var PlaylistRepository
     */
    protected $playlistRepository;

    /**
     * @var DataHandler
     */
    protected $dataHandler;

    public function execute(): bool
    {
        $this->init();

        if ($this->mode === 0) {
            // fetch all
            $playlists = $this->playlistRepository->findAllLinksAndUids();
        } else {
            // fetch selected
            $playlists = $this->playlistRepository->findLinksAndUidsByPid($this->pageSelection);
        }

        // create data array for data handler
        // to use the DataHandler Hook
        $data = [];
        foreach ($playlists as $playlist) {
            $data['tx_mediapool_domain_model_playlist'][$playlist['uid']] = [
                'link' => $playlist['link']
            ];
        }

        $this->dataHandler->start($data, []);
        $this->dataHandler->process_datamap();

        return true;
    }

    protected function init(): void
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->playlistRepository = $objectManager->get(PlaylistRepository::class);
        $this->dataHandler = $objectManager->get(DataHandler::class);
    }
}

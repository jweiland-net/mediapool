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
 * Class UpdatePlaylistInformation
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
     * Playlist repository
     *
     * @var PlaylistRepository
     */
    protected $playlistRepository;

    /**
     * Data Handler
     *
     * @var DataHandler
     */
    protected $dataHandler;

    /**
     * This is the main method that is called when a task is executed
     * It MUST be implemented by all classes inheriting from this one
     * Note that there is no error handling, errors and failures are expected
     * to be handled and logged by the client implementations.
     * Should return TRUE on successful execution, FALSE on error.
     *
     * @return bool Returns TRUE on successful execution, FALSE on error
     */
    public function execute()
    {
        $this->init();
        if ($this->mode === 0) {
            // fetch all
            $playlists = $this->playlistRepository->findAllLinksAndUids();
        } else {
            // fetch selected
            $playlists = $this->playlistRepository->findLinksAndUidsByPid($this->pageSelection);
        }
        $data = [];
        // create data array for data handler
        // to use the DataHandler Hook
        foreach ($playlists as $playlist) {
            $data['tx_mediapool_domain_model_playlist'][$playlist['uid']] = [
                'link' => $playlist['link']
            ];
        }
        $this->dataHandler->start($data, []);
        $this->dataHandler->process_datamap();
        return true;
    }

    /**
     * Init task
     */
    protected function init()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->playlistRepository = $objectManager->get(PlaylistRepository::class);
        $this->dataHandler = $objectManager->get(DataHandler::class);
    }
}

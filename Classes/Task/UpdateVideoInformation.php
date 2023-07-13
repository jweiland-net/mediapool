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
use JWeiland\Mediapool\Traits\GetVideoRepositoryTrait;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Class UpdateVideoInformation
 */
class UpdateVideoInformation extends AbstractTask
{
    use AddFlashMessageTrait;
    use GetVideoRepositoryTrait;

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
     * This is the main method that is called when a task is executed
     * It MUST be implemented by all classes inheriting from this one
     * Note that there is no error handling, errors and failures are expected
     * to be handled and logged by the client implementations.
     * Should return TRUE on successful execution, FALSE on error.
     *
     * @return bool Returns TRUE on successful execution, FALSE on error
     */
    public function execute(): bool
    {
        if ($this->mode === 0) {
            // fetch all
            $videos = $this->getVideoRepository()->findAllLinksAndUids();
        } else {
            // fetch selected
            $videos = $this->getVideoRepository()->findLinksAndUidsByPid($this->pageSelection);
        }

        // Early return, if there are no videos to process
        if ($videos === []) {
            return true;
        }

        $data = [];

        // create data array for data handler
        // to use the DataHandler Hook
        foreach ($videos as $video) {
            $data['tx_mediapool_domain_model_video'][$video['uid']] = [
                'link' => $video['link'],
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

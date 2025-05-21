<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Command;

use JWeiland\Mediapool\Service\Record\VideoRecordService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class UpdateVideoInformationCommand extends Command
{
    public function __construct(
        private readonly VideoRecordService $videoRecordService,
        private readonly LoggerInterface $logger
    ) {
        parent::__construct();
    }

    public function configure(): void
    {
        $this->addArgument(
            'pageSelection',
            InputArgument::OPTIONAL,
            LocalizationUtility::translate('LLL:EXT:mediapool/Resources/Private/Language/locallang.xlf:scheduler.update_playlist_information.page_selection', 'mediapool'),
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $pageSelection = GeneralUtility::intExplode(',', $input->getArgument('pageSelection') ?? '', true);
        if ($pageSelection === []) {
            // fetch all
            $videos = $this->videoRecordService->findAllLinksAndUids();
        } else {
            // fetch selected
            $videos = $this->videoRecordService->findLinksAndUidsByPages($pageSelection);
        }

        // Early return, if there are no videos to process
        if ($videos === []) {
            return Command::SUCCESS;
        }

        // Create a data array for DataHandler to use the DataHandler Hook
        $data = [];

        // create the data array for data handler
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
                $output->writeln($errorLog);
                $this->logger->error($errorLog);
            }

            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function getDataHandler(): DataHandler
    {
        return GeneralUtility::makeInstance(DataHandler::class);
    }
}

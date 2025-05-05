<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/glossary2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Command;

use JWeiland\Mediapool\Service\Record\PlaylistRecordService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class UpdatePlaylistInformationCommand extends Command
{
    protected PlaylistRecordService $playlistRecordService;

    protected LoggerInterface $logger;

    public function __construct(
        PlaylistRecordService $playlistRecordService,
        LoggerInterface $logger,
    ) {
        parent::__construct();

        $this->playlistRecordService = $playlistRecordService;
        $this->logger = $logger;
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
            $playlists = $this->playlistRecordService->findAllLinksAndUids();
        } else {
            // fetch selected
            $playlists = $this->playlistRecordService->findLinksAndUidsByPages($pageSelection);
        }

        // Early return, if there are no playlists to process
        if ($playlists === []) {
            return Command::SUCCESS;
        }

        // Create a data array for DataHandler to use the DataHandler Hook
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

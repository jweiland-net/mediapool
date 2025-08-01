<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Service;

use JWeiland\Mediapool\Helper\MessageHelper;
use JWeiland\Mediapool\Import\Video\VideoImportInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class VideoService
{
    /**
     * @var VideoImportInterface[]
     */
    protected array $importers = [];

    public function __construct(
        iterable $importers,
        protected MessageHelper $messageHelper,
    ) {
        foreach ($importers as $importer) {
            if ($importer instanceof VideoImportInterface) {
                $this->importers[] = $importer;
            }
        }
    }

    /**
     * Get video data
     * $videos array must have to the following structure:
     *
     * Pid is not mandatory
     * e.g.
     * [
     *     5 => ['pid' => 3, 'video' => 'https://youtu.be/yt_Vfw1pAmLlY'],
     *     'NEW1234' => ['video' => 'https://youtu.be/jzTVVocFaVE']
     * ]
     *
     * @param array $videos key = uid, value = video url see example
     * @param int $pid where videos will be saved
     * @return array for DataHandler
     */
    public function getVideoData(array $videos, int $pid): array
    {
        $data = [];
        $videoPlatformMatch = 0;

        try {
            foreach ($this->importers as $registeredVideoImporter) {
                $videosOfVideoPlatform = [];
                foreach ($videos as $uid => $video) {
                    if ($this->isVideoFromVideoPlatform($video['video'], $registeredVideoImporter)) {
                        $videosOfVideoPlatform[$uid] = $video;
                        $videoPlatformMatch++;
                    }
                }
                if ($videosOfVideoPlatform
                    && $processedDataArray = $registeredVideoImporter->processDataArray($videosOfVideoPlatform, $pid)
                ) {
                    $data[] = $processedDataArray;
                }
            }

            $data = array_merge(...$data);
            $imported = count($data['tx_mediapool_domain_model_video'] ?? []);
            $total = count($videos);

            if (!$videoPlatformMatch) {
                $this->messageHelper->addFlashMessage(
                    LocalizationUtility::translate(
                        'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:video_service.no_match.message',
                    ),
                    LocalizationUtility::translate(
                        'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:video_service.no_match.title',
                    ),
                    ContextualFeedbackSeverity::ERROR,
                );
            } elseif ($imported !== $total) {
                $this->messageHelper->addFlashMessage(
                    LocalizationUtility::translate(
                        'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:video_service.import_mismatch.message',
                        null,
                        [$imported, $total],
                    ),
                    LocalizationUtility::translate(
                        'LLL:EXT:mediapool/Resources/Private/Language/error_messages.xlf:video_service.import_mismatch.title',
                    ),
                    ContextualFeedbackSeverity::ERROR,
                );
            }
        } catch (\Exception) {
        }

        return $data;
    }

    /**
     * Checks if one of the hosts from $videoPlatform matches with the video link.
     */
    protected function isVideoFromVideoPlatform(string $videoLink, VideoImportInterface $videoPlatform): bool
    {
        foreach ($videoPlatform->getPlatformHosts() as $host) {
            if (str_starts_with($videoLink, $host)) {
                return true;
            }
        }

        return false;
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Service;

use JWeiland\Mediapool\AbstractBase;
use JWeiland\Mediapool\Import\Video\AbstractVideoImport;
use JWeiland\Mediapool\Utility\VideoPlatformUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class VideoService
 */
class VideoService extends AbstractBase implements SingletonInterface
{
    /**
     * Get video data
     * $videos array must have to following structure:
     *
     * pid is not mandatory
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
            foreach (VideoPlatformUtility::getRegisteredVideoImporters() as $registeredVideoImporter) {
                $videosOfVideoPlatform = [];
                foreach ($videos as $uid => $video) {
                    if ($this->isVideoFromVideoPlatform($video['video'], $registeredVideoImporter)) {
                        $videosOfVideoPlatform[$uid] = $video;
                        $videoPlatformMatch++;
                    }
                }
                if ($videosOfVideoPlatform) {
                    $data[] = $registeredVideoImporter->processDataArray($videosOfVideoPlatform, $pid);
                }
            }

            $data = array_merge(...$data);
            $imported = count($data['tx_mediapool_domain_model_video']);
            $total = count($videos);

            if (!$videoPlatformMatch) {
                $this->addFlashMessage(
                    'video_service.no_match.title',
                    'video_service.no_match.message'
                );
            } elseif ($imported !== $total) {
                $this->addFlashMessage(
                    'video_service.import_mismatch.title',
                    'video_service.import_mismatch.message',
                    [$imported, $total]
                );
            }
        } catch (\Exception $e) {
        }

        return $data;
    }

    /**
     * Checks if one of the hosts from $videoPlatform matches with the video link.
     */
    protected function isVideoFromVideoPlatform(string $videoLink, AbstractVideoImport $videoPlatform): bool
    {
        foreach ($videoPlatform->getPlatformHosts() as $host) {
            if (strpos($videoLink, $host) === 0) {
                return true;
            }
        }

        return false;
    }
}

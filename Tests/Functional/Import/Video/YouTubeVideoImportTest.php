<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Tests\Functional\Import\Video;

use JWeiland\Mediapool\Configuration\ExtConf;
use JWeiland\Mediapool\Domain\Repository\VideoRepository;
use JWeiland\Mediapool\Helper\MessageHelper;
use JWeiland\Mediapool\Import\Video\YouTubeVideoImport;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 */
class YouTubeVideoImportTest extends FunctionalTestCase
{
    public YouTubeVideoImport $subject;

    public MessageHelper|MockObject $messageHelperMock;

    public RequestFactory|MockObject $requestFactoryMock;

    public VideoRepository|MockObject $videoRepositoryMock;

    protected array $testExtensionsToLoad = [
        'jweiland/mediapool',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageHelperMock = $this->createMock(MessageHelper::class);

        $this->requestFactoryMock = $this->createMock(RequestFactory::class);

        $this->videoRepositoryMock = $this->createMock(VideoRepository::class);

        $extConf = new ExtConf(
            youtubeDataApiKey: 'YouTubeApiKey',
        );

        $this->subject = new YouTubeVideoImport(
            $this->requestFactoryMock,
            $this->videoRepositoryMock,
            $this->messageHelperMock,
            $extConf,
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->messageHelperMock,
            $this->requestFactoryMock,
            $this->videoRepositoryMock,
            $this->subject,
        );

        parent::tearDown();
    }

    #[Test]
    public function getPlattformNameWillReturnPlattformName(): void
    {
        self::assertSame(
            'YouTube',
            $this->subject->getPlatformName(),
        );
    }

    #[Test]
    public function getPlatformHostsWillReturnPlatformHosts(): void
    {
        self::assertSame(
            [
                'https://youtube.com',
                'https://www.youtube.com',
                'https://youtu.be',
            ],
            $this->subject->getPlatformHosts(),
        );
    }

    #[Test]
    public function processDataArrayWithEmptyVideosWillReturnEmptyArray(): void
    {
        self::assertSame(
            [],
            $this->subject->processDataArray([], 0),
        );
    }

    #[Test]
    public function processDataArrayWithVideosWillReturnData(): void
    {
        $date = new \DateTimeImmutable();

        $url = sprintf(
            'https://www.googleapis.com/youtube/v3/videos?id=%s&key=%s&part=player,snippet,status',
            'skipNoID,bIuds49uJEg,qzqEUgQu67Q',
            'YouTubeApiKey',
        );

        $jsonResponse = new JsonResponse([
            'items' => [
                0 => [
                    'status' => [
                        'privacyStatus' => 'skipNoID',
                    ],
                ],
                1 => [
                    'id' => 'bIuds49uJEg',
                    'status' => [
                        'privacyStatus' => 'private',
                    ],
                ],
                2 => [
                    'id' => 'qzqEUgQu67Q',
                    'player' => [
                        'embedHtml' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/qzqEUgQu67Q"></iframe>',
                    ],
                    'snippet' => [
                        'description' => 'Diesem Video wurde keine Beschreibung hinzugefügt',
                        'publishedAt' => $date->format('Y-m-d\TH:i:s\Z'),
                        'thumbnails' => [
                            'medium' => [
                                'url' => 'https://example.com/medium.jpg',
                            ],
                            'standard' => [
                                'url' => 'https://example.com/standard.jpg',
                            ],
                        ],
                        'title' => 'TYPO3 Video Tutorials von jweiland.net',
                    ],
                    'status' => [
                        'embeddable' => true,
                    ],
                ],
            ],
        ]);

        $this->requestFactoryMock
            ->expects(self::atLeastOnce())
            ->method('request')
            ->willReturnMap([
                [$url, $jsonResponse],
            ]);

        self::assertSame(
            [
                'tx_mediapool_domain_model_video' => [
                    24 => [
                        'link' => 'https://youtu.be/qzqEUgQu67Q',
                        'title' => 'TYPO3 Video Tutorials von jweiland.net',
                        'description' => 'Diesem Video wurde keine Beschreibung hinzugefügt',
                        'upload_date' => $date->getTimestamp(),
                        'player_html' => '<iframe width="560" height="315" src="https://www.youtube.com/embed/qzqEUgQu67Q"></iframe>',
                        'video_id' => 'yt_qzqEUgQu67Q',
                        'thumbnail' => 'https://example.com/medium.jpg',
                        'thumbnail_large' => 'https://example.com/standard.jpg',
                    ],
                ],
            ],
            $this->subject->processDataArray(
                [
                    6 => [
                        'video' => 'https://www.youtube.com/watch?v=skipNoID&pp=ygUMandlaWxhbmQubmV0',
                        'pid' => 1,
                    ],
                    12 => [
                        'video' => 'https://www.youtube.com/watch?v=bIuds49uJEg&pp=ygUMandlaWxhbmQubmV0',
                        'pid' => 1,
                    ],
                    24 => [
                        'video' => 'https://www.youtube.com/watch?v=qzqEUgQu67Q&pp=ygUMandlaWxhbmQubmV0',
                        'pid' => 1,
                    ],
                ],
                1
            ),
        );
    }
}

<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Tests\Functional\Service;

use JWeiland\Mediapool\Helper\MessageHelper;
use JWeiland\Mediapool\Import\Video\YouTubeVideoImport;
use JWeiland\Mediapool\Service\VideoService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 */
class VideoServiceTest extends FunctionalTestCase
{
    public VideoService $subject;

    public YouTubeVideoImport|MockObject $youTubeVideoImportMock;

    public MessageHelper|MockObject $messageHelperMock;

    protected array $testExtensionsToLoad = [
        'jweiland/mediapool',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->youTubeVideoImportMock = $this->createMock(YouTubeVideoImport::class);

        $this->messageHelperMock = $this->createMock(MessageHelper::class);

        $this->subject = new VideoService(
            [$this->youTubeVideoImportMock],
            $this->messageHelperMock,
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->youTubeVideoImportMock,
            $this->messageHelperMock,
            $this->subject,
        );

        parent::tearDown();
    }

    #[Test]
    public function getVideoDataWithEmptyVideosWillAddNoMatchMessage(): void
    {
        $this->messageHelperMock
            ->expects(self::atLeastOnce())
            ->method('addFlashMessage')
            ->with(
                self::stringContains('could not find a matching video platform'),
                self::identicalTo('No video platform match'),
            );

        self::assertSame(
            [],
            $this->subject->getVideoData([], 0),
        );
    }

    #[Test]
    public function getVideoDataWithWrongPlattformVideosWillAddNoMatchMessage(): void
    {
        $this->messageHelperMock
            ->expects(self::atLeastOnce())
            ->method('addFlashMessage')
            ->with(
                self::stringContains('could not find a matching video platform'),
                self::identicalTo('No video platform match'),
            );

        self::assertSame(
            [],
            $this->subject->getVideoData(
                [
                    12 => [
                        'video' => 'https://vimeo.com/123456789',
                    ],
                    24 => [
                        'video' => 'https://example.com/123456789',
                    ],
                ],
                0
            ),
        );
    }

    #[Test]
    public function getVideoDataWithVideosWillAddImportMismatchMessage(): void
    {
        $dataArray = [
            'tx_mediapool_domain_model_video' => [
                'foo' => 'bar',
            ],
        ];

        $this->youTubeVideoImportMock
            ->expects(self::atLeastOnce())
            ->method('getPlatformHosts')
            ->willReturn([
                'https://youtube.com',
                'https://www.youtube.com',
                'https://youtu.be',
            ]);

        $this->youTubeVideoImportMock
            ->expects(self::atLeastOnce())
            ->method('processDataArray')
            ->willReturn($dataArray);

        $this->messageHelperMock
            ->expects(self::atLeastOnce())
            ->method('addFlashMessage')
            ->with(
                self::stringStartsWith('There was an mismatch while importing videos'),
                self::identicalTo('Import mismatch'),
            );

        self::assertSame(
            $dataArray,
            $this->subject->getVideoData(
                [
                    12 => [
                        'video' => 'https://vimeo.com/123456789',
                    ],
                    24 => [
                        'video' => 'https://youtube.com/123456789',
                    ],
                ],
                0
            ),
        );
    }

    #[Test]
    public function getVideoDataWithVideosWillReturnDataArray(): void
    {
        $dataArray = [
            'tx_mediapool_domain_model_video' => [
                0 => [
                    'bla' => 'blub',
                ],
                1 => [
                    'foo' => 'bar',
                ],
            ],
        ];

        $this->youTubeVideoImportMock
            ->expects(self::atLeastOnce())
            ->method('getPlatformHosts')
            ->willReturn([
                'https://youtube.com',
                'https://www.youtube.com',
                'https://youtu.be',
            ]);

        $this->youTubeVideoImportMock
            ->expects(self::atLeastOnce())
            ->method('processDataArray')
            ->willReturn($dataArray);

        $this->messageHelperMock
            ->expects(self::never())
            ->method('addFlashMessage');

        self::assertSame(
            $dataArray,
            $this->subject->getVideoData(
                [
                    12 => [
                        'video' => 'https://youtu.be/987654321',
                    ],
                    24 => [
                        'video' => 'https://youtube.com/123456789',
                    ],
                ],
                0
            ),
        );
    }
}

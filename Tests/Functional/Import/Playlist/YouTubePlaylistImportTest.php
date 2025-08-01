<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Tests\Functional\Import\Playlist;

use JWeiland\Mediapool\Configuration\ExtConf;
use JWeiland\Mediapool\Helper\MessageHelper;
use JWeiland\Mediapool\Import\Playlist\YouTubePlaylistImport;
use JWeiland\Mediapool\Import\Video\YouTubeVideoImport;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * Test case.
 */
class YouTubePlaylistImportTest extends FunctionalTestCase
{
    public YouTubePlaylistImport $subject;

    public MessageHelper|MockObject $messageHelperMock;

    public RequestFactory|MockObject $requestFactoryMock;

    public YouTubeVideoImport|MockObject $youTubeVideoImportMock;

    protected array $testExtensionsToLoad = [
        'jweiland/mediapool',
    ];

    protected function setUp(): void
    {
        parent::setUp();

        $this->messageHelperMock = $this->createMock(MessageHelper::class);

        $this->requestFactoryMock = $this->createMock(RequestFactory::class);

        $this->youTubeVideoImportMock = $this->createMock(YouTubeVideoImport::class);

        $extConf = new ExtConf(
            youtubeDataApiKey: 'YouTubeApiKey',
        );

        $this->subject = new YouTubePlaylistImport(
            $this->youTubeVideoImportMock,
            $this->requestFactoryMock,
            $this->messageHelperMock,
            $extConf,
        );
    }

    protected function tearDown(): void
    {
        unset(
            $this->messageHelperMock,
            $this->requestFactoryMock,
            $this->youTubeVideoImportMock,
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
    public function getPlaylistInformationWithEmptyPlaylistLinkWillReturnEmptyArray(): void
    {
        $this->messageHelperMock
            ->expects(self::once())
            ->method('addFlashMessage')
            ->with(
                self::stringStartsWith('Could not extract playlist id from playlist link'),
                self::identicalTo('Invalid playlist id'),
            );

        self::assertSame(
            [],
            $this->subject->getPlaylistInformation('', 0),
        );
    }

    #[Test]
    public function getPlaylistInformationWithPlaylistLinkWillReturnEmptyPlaylistInformation(): void
    {
        $this->messageHelperMock
            ->expects(self::never())
            ->method('addFlashMessage');

        $playListIdUrl = sprintf(
            'https://www.googleapis.com/youtube/v3/channels?key=%s&part=contentDetails&%s',
            'YouTubeApiKey',
            'id=testChannelId',
        );

        $playListIdJsonResponse = new JsonResponse([
            'items' => [
                0 => [
                    'contentDetails' => [
                        'relatedPlaylists' => [
                            'uploads' => 'testPlayListId',
                        ],
                    ],
                ],
            ],
        ]);

        $playListInformationUrl = sprintf(
            'https://www.googleapis.com/youtube/v3/playlists?id=%s&key=%s&part=snippet',
            'testPlayListId',
            'YouTubeApiKey',
        );

        $playListInformationJsonResponse = new JsonResponse([
            'items' => [
                0 => [
                    'snippet' => [
                        'title' => 'Test Playlist',
                        'thumbnails' => [
                            'medium' => [
                                'url' => 'https://example.com/testPlaylistId/medium.jpg',
                            ],
                            'standard' => [
                                'url' => 'https://example.com/testPlaylistId/standard.jpg',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $playListItemsUrl = sprintf(
            'https://www.googleapis.com/youtube/v3/playlistItems?playlistId=%s&key=%s&part=contentDetails,status&maxResults=50',
            'testPlayListId',
            'YouTubeApiKey',
        );

        $playListItemsJsonResponse = new JsonResponse([
            'items' => [
                0 => [
                    'status' => [
                        'privacyStatus' => 'private',
                    ],
                    'contentDetails' => [
                        'videoId' => 'testPrivateVideoId',
                    ],
                ],
                1 => [
                    'status' => [
                        'privacyStatus' => 'public',
                    ],
                    'contentDetails' => [
                        'videoId' => 'testPublicVideoId',
                    ],
                ],
            ],
        ]);

        $this->requestFactoryMock
            ->expects(self::atLeastOnce())
            ->method('request')
            ->willReturnMap([
                [$playListIdUrl, $playListIdJsonResponse],
                [$playListInformationUrl, $playListInformationJsonResponse],
                [$playListItemsUrl, $playListItemsJsonResponse],
            ]);

        $this->youTubeVideoImportMock
            ->expects(self::once())
            ->method('processDataArray')
            ->with(
                self::identicalTo([
                    'NEW0' => [
                        'pid' => 0,
                        'video' => 'testPublicVideoId',
                    ],
                ]),
                self::identicalTo(0),
                self::identicalTo(''),
                self::identicalTo(true),
            )
            ->willReturn(null);

        self::assertSame(
            [],
            $this->subject->getPlaylistInformation(
                'https://www.youtube.com/channel/testChannelId',
                0
            ),
        );
    }

    #[Test]
    public function getPlaylistInformationWithPlaylistLinkWillReturnPlaylistInformation(): void
    {
        $this->messageHelperMock
            ->expects(self::never())
            ->method('addFlashMessage');

        $playListIdUrl = sprintf(
            'https://www.googleapis.com/youtube/v3/channels?key=%s&part=contentDetails&%s',
            'YouTubeApiKey',
            'id=testChannelId',
        );

        $playListIdJsonResponse = new JsonResponse([
            'items' => [
                0 => [
                    'contentDetails' => [
                        'relatedPlaylists' => [
                            'uploads' => 'testPlayListId',
                        ],
                    ],
                ],
            ],
        ]);

        $playListInformationUrl = sprintf(
            'https://www.googleapis.com/youtube/v3/playlists?id=%s&key=%s&part=snippet',
            'testPlayListId',
            'YouTubeApiKey',
        );

        $playListInformationJsonResponse = new JsonResponse([
            'items' => [
                0 => [
                    'snippet' => [
                        'title' => 'Test Playlist',
                        'thumbnails' => [
                            'medium' => [
                                'url' => 'https://example.com/testPlaylistId/medium.jpg',
                            ],
                            'standard' => [
                                'url' => 'https://example.com/testPlaylistId/standard.jpg',
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $playListItemsUrl = sprintf(
            'https://www.googleapis.com/youtube/v3/playlistItems?playlistId=%s&key=%s&part=contentDetails,status&maxResults=50',
            'testPlayListId',
            'YouTubeApiKey',
        );

        $playListItemsJsonResponse = new JsonResponse([
            'items' => [
                0 => [
                    'status' => [
                        'privacyStatus' => 'private',
                    ],
                    'contentDetails' => [
                        'videoId' => 'testPrivateVideoId',
                    ],
                ],
                1 => [
                    'status' => [
                        'privacyStatus' => 'public',
                    ],
                    'contentDetails' => [
                        'videoId' => 'testPublicVideoId',
                    ],
                ],
            ],
        ]);

        $this->requestFactoryMock
            ->expects(self::atLeastOnce())
            ->method('request')
            ->willReturnMap([
                [$playListIdUrl, $playListIdJsonResponse],
                [$playListInformationUrl, $playListInformationJsonResponse],
                [$playListItemsUrl, $playListItemsJsonResponse],
            ]);

        $this->youTubeVideoImportMock
            ->expects(self::once())
            ->method('processDataArray')
            ->with(
                self::identicalTo([
                    'NEW0' => [
                        'pid' => 0,
                        'video' => 'testPublicVideoId',
                    ],
                ]),
                self::identicalTo(0),
                self::identicalTo(''),
                self::identicalTo(true),
            )
            ->willReturn([
                'foo' => 'bar',
            ]);

        self::assertSame(
            [
                'fieldArray' => [
                    'pid' => 0,
                    'link' => 'https://www.youtube.com/channel/testChannelId',
                    'playlist_id' => 'yt_testPlayListId',
                    'title' => 'Test Playlist',
                    'thumbnail' => 'https://example.com/testPlaylistId/medium.jpg',
                    'thumbnail_large' => 'https://example.com/testPlaylistId/standard.jpg',
                    'videos' => '',
                ],
                'dataHandler' => [
                    'foo' => 'bar',
                ],
            ],
            $this->subject->getPlaylistInformation(
                'https://www.youtube.com/channel/testChannelId',
                0
            ),
        );
    }
}

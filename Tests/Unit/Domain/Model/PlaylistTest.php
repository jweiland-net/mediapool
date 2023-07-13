<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Tests\Unit\Domain\Model;

use JWeiland\Mediapool\Domain\Model\Playlist;
use JWeiland\Mediapool\Domain\Model\Video;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Test case.
 */
class PlaylistTest extends UnitTestCase
{
    protected Playlist $subject;

    protected function setUp(): void
    {
        $this->subject = new Playlist();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject
        );

        parent::tearDown();
    }

    /**
     * @test
     */
    public function getTitleWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleWillSetTitle(): void
    {
        $this->subject->setTitle('Jochen Weiland');

        self::assertSame(
            'Jochen Weiland',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function getLinkWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getLink()
        );
    }

    /**
     * @test
     */
    public function setLinkWillSetLink(): void
    {
        $this->subject->setLink('https://jweiland.net');

        self::assertSame(
            'https://jweiland.net',
            $this->subject->getLink()
        );
    }

    /**
     * @test
     */
    public function getPlaylistIdWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getPlaylistId()
        );
    }

    /**
     * @test
     */
    public function setPlaylistIdWillSetPlaylistId(): void
    {
        $this->subject->setPlaylistId('ctmh83279cgmh5428');

        self::assertSame(
            'ctmh83279cgmh5428',
            $this->subject->getPlaylistId()
        );
    }

    /**
     * @test
     */
    public function getVideosWillInitiallyReturnEmptyObjectStorage(): void
    {
        self::assertEquals(
            new ObjectStorage(),
            $this->subject->getVideos()
        );
    }

    /**
     * @test
     */
    public function setVideosWillSetVideos(): void
    {
        $videos = new ObjectStorage();
        $this->subject->setVideos($videos);

        self::assertSame(
            $videos,
            $this->subject->getVideos()
        );
    }

    /**
     * @test
     */
    public function addVideoWillAddVideoToObjectStorage(): void
    {
        $video = new Video();
        $this->subject->addVideo($video);

        self::assertSame(
            $video,
            $this->subject->getVideos()->current()
        );
    }

    /**
     * @test
     */
    public function removeVideoWillRemoveVideoFromObjectStorage(): void
    {
        $video = new Video();
        $videos = new ObjectStorage();
        $videos->attach($video);
        $this->subject->setVideos($videos);

        $this->subject->removeVideo($video);

        self::assertCount(
            0,
            $this->subject->getVideos()
        );
    }

    /**
     * @test
     */
    public function getCategoriesWillInitiallyReturnEmptyObjectStorage(): void
    {
        self::assertEquals(
            new ObjectStorage(),
            $this->subject->getCategories()
        );
    }

    /**
     * @test
     */
    public function setCategoriesWillSetCategories(): void
    {
        $categories = new ObjectStorage();
        $this->subject->setCategories($categories);

        self::assertSame(
            $categories,
            $this->subject->getCategories()
        );
    }

    /**
     * @test
     */
    public function getThumbnailWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getThumbnail()
        );
    }

    /**
     * @test
     */
    public function setThumbnailWillSetThumbnail(): void
    {
        $this->subject->setThumbnail('thumbnail.png');

        self::assertSame(
            'thumbnail.png',
            $this->subject->getThumbnail()
        );
    }

    /**
     * @test
     */
    public function getSlugWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getSlug()
        );
    }

    /**
     * @test
     */
    public function setSlugWillSetSlug(): void
    {
        $this->subject->setSlug('/video/super-mario');

        self::assertSame(
            '/video/super-mario',
            $this->subject->getSlug()
        );
    }
}

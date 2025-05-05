<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/glossary2.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Tests\Unit\Domain\Model;

use JWeiland\Mediapool\Domain\Model\Video;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class VideoTest extends UnitTestCase
{
    protected Video $subject;

    protected function setUp(): void
    {
        $this->subject = new Video();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
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
            $this->subject->getTitle(),
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
            $this->subject->getTitle(),
        );
    }

    /**
     * @test
     */
    public function getDescriptionWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getDescription(),
        );
    }

    /**
     * @test
     */
    public function setDescriptionWillSetDescription(): void
    {
        $this->subject->setDescription('Please, listen to me...');

        self::assertSame(
            'Please, listen to me...',
            $this->subject->getDescription(),
        );
    }

    /**
     * @test
     */
    public function getUploadDateWillInitiallyReturnNull(): void
    {
        self::assertNull(
            $this->subject->getUploadDate(),
        );
    }

    /**
     * @test
     */
    public function setUploadDateWillSetUploadDate(): void
    {
        $date = new \DateTime('now');

        $this->subject->setUploadDate($date);

        self::assertSame(
            $date,
            $this->subject->getUploadDate(),
        );
    }

    /**
     * @test
     */
    public function getLinkWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getLink(),
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
            $this->subject->getLink(),
        );
    }

    /**
     * @test
     */
    public function getPlayerHtmlWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getPlayerHtml(),
        );
    }

    /**
     * @test
     */
    public function setPlayerHtmlWillSetPlayerHtml(): void
    {
        $this->subject->setPlayerHtml('<strong>Hello world</strong>');

        self::assertSame(
            '<strong>Hello world</strong>',
            $this->subject->getPlayerHtml(),
        );
    }

    /**
     * @test
     */
    public function getVideoIdWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getVideoId(),
        );
    }

    /**
     * @test
     */
    public function setVideoIdWillSetVideoId(): void
    {
        $this->subject->setVideoId('cgmh34829xghwo8g8h5og');

        self::assertSame(
            'cgmh34829xghwo8g8h5og',
            $this->subject->getVideoId(),
        );
    }

    /**
     * @test
     */
    public function getThumbnailWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getThumbnail(),
        );
    }

    /**
     * @test
     */
    public function setThumbnailWillSetThumbnail(): void
    {
        $this->subject->setThumbnail('thumbnail.jpg');

        self::assertSame(
            'thumbnail.jpg',
            $this->subject->getThumbnail(),
        );
    }

    /**
     * @test
     */
    public function getThumbnailLargeWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getThumbnailLarge(),
        );
    }

    /**
     * @test
     */
    public function setThumbnailLargeWillSetThumbnailLarge(): void
    {
        $this->subject->setThumbnailLarge('thumbnail-large.jpg');

        self::assertSame(
            'thumbnail-large.jpg',
            $this->subject->getThumbnailLarge(),
        );
    }

    /**
     * @test
     */
    public function getSlugWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getSlug(),
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
            $this->subject->getSlug(),
        );
    }
}

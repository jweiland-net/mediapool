<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */


use JWeiland\Mediapool\Domain\Model\Category;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class YoutubePlaylistImportTest extends FunctionalTestCase
{
    protected \JWeiland\Mediapool\Import\Playlist\YoutubePlaylistImport $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new Category();
    }

    protected function tearDown(): void
    {
        unset(
            $this->subject,
        );

        parent::tearDown();
    }

    #[Test]
    public function getTitleWillInitiallyReturnEmptyString(): void
    {
        self::assertSame(
            '',
            $this->subject->getTitle(),
        );
    }

    #[Test]
    public function setTitleWillSetTitle(): void
    {
        $this->subject->setTitle('Jochen Weiland');

        self::assertSame(
            'Jochen Weiland',
            $this->subject->getTitle(),
        );
    }
}

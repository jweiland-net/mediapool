<?php

declare(strict_types=1);

/*
 * This file is part of the package jweiland/mediapool.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\Mediapool\Tests\Unit\Domain\Model;

use JWeiland\Mediapool\Domain\Model\Category;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Test case.
 */
class CategoryTest extends UnitTestCase
{
    protected Category $subject;

    protected function setUp(): void
    {
        $this->subject = new Category();
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
}

<?php

declare(strict_types=1);

/*
 * This file is part of the community-maintained Playwright PHP project.
 * It is not affiliated with or endorsed by Microsoft.
 *
 * (c) 2025-Present - Playwright PHP - https://github.com/playwright-php
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Playwright\Behat\Tests\Unit\Context\Initializer;

use Behat\Behat\Context\Context;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Playwright\Behat\Context\Initializer\PlaywrightAwareInitializer;
use Playwright\Behat\Context\PlaywrightAwareContext;
use Playwright\Behat\PlaywrightManager;

#[CoversClass(PlaywrightAwareInitializer::class)]
final class PlaywrightAwareInitializerTest extends TestCase
{
    public function testInjectsManagerIntoAwareContexts(): void
    {
        $manager = new PlaywrightManager([]);
        $context = new class implements Context, PlaywrightAwareContext {
            public ?PlaywrightManager $received = null;

            public function setPlaywrightManager(PlaywrightManager $manager): void
            {
                $this->received = $manager;
            }
        };

        (new PlaywrightAwareInitializer($manager))->initializeContext($context);

        $this->assertSame($manager, $context->received);
    }

    public function testIgnoresOtherContexts(): void
    {
        $context = new class implements Context {};

        (new PlaywrightAwareInitializer(new PlaywrightManager([])))->initializeContext($context);

        $this->assertInstanceOf(Context::class, $context);
    }
}

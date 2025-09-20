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

namespace Playwright\Behat\Tests\Unit\Context;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Playwright\Behat\Context\PlaywrightContext;
use Playwright\Behat\Exception\BrowserNotStartedException;

#[CoversClass(PlaywrightContext::class)]
final class PlaywrightContextTest extends TestCase
{
    private PlaywrightContext $context;

    protected function setUp(): void
    {
        $this->context = new PlaywrightContext();
        $this->context->setPlaywrightConfig([
            'headless' => true,
            'browser' => 'chromium',
            'base_url' => 'http://localhost:8080',
        ]);
    }

    public function testBrowserNotStartedException(): void
    {
        $this->expectException(BrowserNotStartedException::class);
        $this->context->iAmOn('/test-page');
    }

    public function testGettersReturnNullInitially(): void
    {
        $this->assertNull($this->context->getPage());
        $this->assertNull($this->context->getBrowser());
        $this->assertNull($this->context->getPlaywright());
    }

    public function testScreenshotsArrayIsEmpty(): void
    {
        $this->assertEmpty($this->context->getScreenshots());
    }
}

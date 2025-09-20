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

namespace Playwright\Behat\Tests\Integration;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Playwright\Behat\Context\PlaywrightContext;

#[CoversClass(PlaywrightContext::class)]
final class ContextIntegrationTest extends TestCase
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

    public function testContextCanBeConfigured(): void
    {
        $this->assertNull($this->context->getPage());
        $this->assertNull($this->context->getBrowser());
        $this->assertEmpty($this->context->getScreenshots());
    }

    public function testContextHasPublicMethods(): void
    {
        $this->assertTrue(method_exists($this->context, 'iAmOn'));
        $this->assertTrue(method_exists($this->context, 'iClickOn'));
        $this->assertTrue(method_exists($this->context, 'iShouldSee'));
        $this->assertTrue(method_exists($this->context, 'iTakeAScreenshotNamed'));
    }
}

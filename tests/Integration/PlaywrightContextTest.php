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
use Playwright\Behat\Context\RawPlaywrightContext;
use Playwright\Behat\Exception\ExpectationFailedException;
use Playwright\Behat\PlaywrightManager;

#[CoversClass(PlaywrightContext::class)]
#[CoversClass(RawPlaywrightContext::class)]
final class PlaywrightContextTest extends TestCase
{
    use FixtureServerTrait;

    private static PlaywrightManager $manager;
    private static string $screenshotDir;
    private PlaywrightContext $context;

    public static function setUpBeforeClass(): void
    {
        self::startFixtureServer();
        self::$screenshotDir = sys_get_temp_dir().'/playwright-behat-'.uniqid();
        self::$manager = new PlaywrightManager([
            'base_url' => self::fixtureBaseUrl(),
            'screenshot_dir' => self::$screenshotDir,
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        self::$manager->shutdown();
        array_map('unlink', glob(self::$screenshotDir.'/*') ?: []);
        @rmdir(self::$screenshotDir);
    }

    protected function setUp(): void
    {
        $this->context = new PlaywrightContext();
        $this->context->setPlaywrightManager(self::$manager);
    }

    protected function tearDown(): void
    {
        self::$manager->closePage();
    }

    public function testNavigationStepsUseTheBaseUrl(): void
    {
        $this->context->iAmOn('/index.html');

        $this->assertSame('Fixture home', $this->context->getPage()->title());
    }

    public function testFillAndClickSubmitTheForm(): void
    {
        $this->context->iAmOn('/index.html');
        $this->context->iFillWith('#email', 'user@test.com');
        $this->context->iClickOn('#login-button');

        $this->assertSame(self::fixtureBaseUrl().'/login.html?email=user%40test.com&password=', $this->context->getPage()->url());
    }

    public function testIShouldSeePassesWhenTheTextIsOnThePage(): void
    {
        $this->context->iAmOn('/index.html');

        $this->context->iShouldSee('Welcome');

        $this->assertTrue(true);
    }

    public function testIShouldSeeFailsWithAReadableMessage(): void
    {
        $this->context->iAmOn('/index.html');

        $this->expectException(ExpectationFailedException::class);
        $this->expectExceptionMessage('Expected to see "Not there" on http://127.0.0.1:8971/index.html');

        $this->context->iShouldSee('Not there');
    }

    public function testNamedScreenshotIsSavedInTheConfiguredDirectory(): void
    {
        $this->context->iAmOn('/index.html');

        $this->context->iTakeAScreenshotNamed('Home page');

        $this->assertFileExists(self::$screenshotDir.'/home-page.png');
    }
}

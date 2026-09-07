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
use Playwright\Behat\PlaywrightManager;
use Playwright\Page\PageInterface;

#[CoversClass(PlaywrightManager::class)]
final class PlaywrightManagerTest extends TestCase
{
    use FixtureServerTrait;

    private static PlaywrightManager $manager;
    private static string $screenshotDir;

    public static function setUpBeforeClass(): void
    {
        self::startFixtureServer();
        self::$screenshotDir = sys_get_temp_dir().'/playwright-behat-'.uniqid();
        self::$manager = new PlaywrightManager([
            'headless' => true,
            'browser' => 'chromium',
            'base_url' => self::fixtureBaseUrl(),
            'screenshot_dir' => self::$screenshotDir,
            'auto_screenshot_on_failure' => true,
            'timeout' => 10000,
            'slow_mo' => 0,
            'viewport' => ['width' => 800, 'height' => 600],
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        self::$manager->shutdown();
        array_map('unlink', glob(self::$screenshotDir.'/*') ?: []);
        @rmdir(self::$screenshotDir);
    }

    protected function tearDown(): void
    {
        self::$manager->closePage();
    }

    public function testGetPageOpensAPageLazily(): void
    {
        $this->assertFalse(self::$manager->hasPage());

        $page = self::$manager->getPage();

        $this->assertInstanceOf(PageInterface::class, $page);
        $this->assertTrue(self::$manager->hasPage());
        $this->assertSame($page, self::$manager->getPage());
    }

    public function testClosePageKeepsTheBrowserAndIsolatesTheNextPage(): void
    {
        $first = self::$manager->getPage();
        $browser = self::$manager->getBrowser();
        $first->goto('/index.html');
        $first->evaluate('() => localStorage.setItem("token", "abc")');

        self::$manager->closePage();

        $this->assertFalse(self::$manager->hasPage());
        $second = self::$manager->getPage();
        $this->assertNotSame($first, $second);
        $this->assertSame($browser, self::$manager->getBrowser());
        $second->goto('/index.html');
        $this->assertNull($second->evaluate('() => localStorage.getItem("token")'));
    }

    public function testRelativeUrlsResolveAgainstBaseUrl(): void
    {
        $page = self::$manager->getPage();

        $page->goto('/login.html');

        $this->assertSame(self::fixtureBaseUrl().'/login.html', $page->url());
        $this->assertSame('Logged in', $page->title());
    }

    public function testViewportIsApplied(): void
    {
        $this->assertSame(['width' => 800, 'height' => 600], self::$manager->getPage()->viewportSize());
    }

    public function testSaveScreenshotWritesIntoScreenshotDir(): void
    {
        self::$manager->getPage()->goto('/index.html');

        $path = self::$manager->saveScreenshot('Home page: before login');

        $this->assertSame(self::$screenshotDir.'/home-page-before-login.png', $path);
        $this->assertFileExists($path);
    }

    public function testShutdownClosesEverythingAndAllowsARestart(): void
    {
        self::$manager->getPage();

        self::$manager->shutdown();

        $this->assertFalse(self::$manager->hasPage());
        $page = self::$manager->getPage();
        $page->goto('/index.html');
        $this->assertSame('Fixture home', $page->title());
    }
}

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

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

/**
 * Runs the real behat binary against tests/Fixtures: the only test that
 * proves the extension, the initializer and the listener are wired together.
 */
#[CoversNothing]
final class BehatRunTest extends TestCase
{
    use FixtureServerTrait;

    private const SCREENSHOT_DIR = __DIR__.'/../Fixtures/var/screenshots';

    public static function setUpBeforeClass(): void
    {
        self::startFixtureServer();
    }

    protected function setUp(): void
    {
        array_map('unlink', glob(self::SCREENSHOT_DIR.'/*.png') ?: []);
    }

    public function testPassingSuiteRunsGreen(): void
    {
        $process = $this->runBehat('passing');

        $this->assertSame(0, $process->getExitCode(), $process->getOutput().$process->getErrorOutput());
        $this->assertStringContainsString('4 scenarios (4 passed)', $process->getOutput());
        $this->assertFileExists(self::SCREENSHOT_DIR.'/after-login.png');
    }

    public function testFailingScenarioReportsTheExpectationAndLeavesAScreenshot(): void
    {
        $process = $this->runBehat('failing');

        $this->assertSame(1, $process->getExitCode(), $process->getOutput().$process->getErrorOutput());
        $this->assertStringContainsString('Expected to see "Not there"', $process->getOutput());
        $this->assertStringNotContainsString('Fatal error', $process->getOutput());
        $this->assertFileExists(self::SCREENSHOT_DIR.'/failed-missing-text-on-page-2.png');
    }

    private function runBehat(string $suite): Process
    {
        $process = new Process([
            PHP_BINARY,
            __DIR__.'/../../vendor/bin/behat',
            '--config', __DIR__.'/../Fixtures/behat.yml',
            '--suite', $suite,
            '--format', 'progress',
            '--no-colors',
            '--no-interaction',
        ]);
        $process->setTimeout(120);
        $process->run();

        return $process;
    }
}

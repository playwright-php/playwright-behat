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

use Symfony\Component\Process\Process;

/**
 * Serves tests/Fixtures/site with the PHP built-in server for the test process.
 */
trait FixtureServerTrait
{
    private static ?Process $fixtureServer = null;

    public static function fixtureBaseUrl(): string
    {
        return 'http://127.0.0.1:8971';
    }

    public static function startFixtureServer(): void
    {
        if (null !== self::$fixtureServer) {
            return;
        }

        $process = new Process([PHP_BINARY, '-S', '127.0.0.1:8971', '-t', __DIR__.'/../Fixtures/site']);
        $process->start();

        for ($i = 0; $i < 50; ++$i) {
            if (false !== @file_get_contents(self::fixtureBaseUrl().'/index.html')) {
                self::$fixtureServer = $process;
                register_shutdown_function(static fn () => $process->stop());

                return;
            }
            usleep(100_000);
        }

        $process->stop();
        throw new \RuntimeException('Fixture server did not start: '.$process->getErrorOutput());
    }
}

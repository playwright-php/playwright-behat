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

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Playwright\Behat\Context\PlaywrightContext;
use Playwright\Behat\ServiceContainer\PlaywrightExtension;

// The same file works on Behat 3.x and 4.x; YAML configuration is gone in 4.0.
return (new Config())
    ->withProfile((new Profile('default'))
        ->withExtension(new Extension(PlaywrightExtension::class, [
            'base_url' => 'http://127.0.0.1:8971',
            'screenshot_dir' => '%paths.base%/var/screenshots',
        ]))
        ->withSuite((new Suite('passing'))
            ->withPaths('%paths.base%/features/passing')
            ->withContexts(PlaywrightContext::class))
        ->withSuite((new Suite('failing'))
            ->withPaths('%paths.base%/features/failing')
            ->withContexts(PlaywrightContext::class))
    );

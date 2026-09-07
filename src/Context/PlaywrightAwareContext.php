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

namespace Playwright\Behat\Context;

use Behat\Behat\Context\Context;
use Playwright\Behat\PlaywrightManager;

/**
 * A context that receives the shared PlaywrightManager before the run starts.
 */
interface PlaywrightAwareContext extends Context
{
    public function setPlaywrightManager(PlaywrightManager $manager): void;
}

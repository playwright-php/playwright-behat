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

use Playwright\Behat\Exception\PlaywrightException;
use Playwright\Behat\PlaywrightManager;
use Playwright\Browser\BrowserInterface;
use Playwright\Page\PageInterface;

/**
 * Base class for project contexts that drive the browser without inheriting
 * the built-in steps. Extend it and use getPage() in your own step methods.
 */
abstract class RawPlaywrightContext implements PlaywrightAwareContext
{
    private ?PlaywrightManager $playwright = null;

    public function setPlaywrightManager(PlaywrightManager $manager): void
    {
        $this->playwright = $manager;
    }

    public function getPlaywrightManager(): PlaywrightManager
    {
        return $this->playwright ?? throw new PlaywrightException(sprintf('%s did not receive a PlaywrightManager. Enable %s in your Behat configuration.', static::class, 'Playwright\Behat\ServiceContainer\PlaywrightExtension'));
    }

    /**
     * The page of the current scenario, opened on first call.
     */
    public function getPage(): PageInterface
    {
        return $this->getPlaywrightManager()->getPage();
    }

    public function getBrowser(): BrowserInterface
    {
        return $this->getPlaywrightManager()->getBrowser();
    }
}

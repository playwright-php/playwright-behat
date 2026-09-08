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

use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Playwright\Behat\Exception\ExpectationFailedException;

/**
 * Ready-made steps for the most common browser interactions.
 *
 * Selectors are Playwright selectors: CSS by default, plus text=, role= and
 * the other engines Playwright supports.
 */
final class PlaywrightContext extends RawPlaywrightContext
{
    #[Given('I am on :url')]
    #[When('I go to :url')]
    public function iAmOn(string $url): void
    {
        $this->getPage()->goto($url);
    }

    #[When('I click on :selector')]
    public function iClickOn(string $selector): void
    {
        // waitFor() runs in the browser and honours the configured timeout;
        // the library's own actionability wait in click() does not.
        $locator = $this->getPage()->locator($selector);
        $locator->waitFor();
        $locator->click();
    }

    #[When('I fill :selector with :value')]
    public function iFillWith(string $selector, string $value): void
    {
        $locator = $this->getPage()->locator($selector);
        $locator->waitFor();
        $locator->fill($value);
    }

    #[Then('I should see :text')]
    public function iShouldSee(string $text): void
    {
        $page = $this->getPage();

        if (!str_contains($page->content() ?? '', $text)) {
            throw new ExpectationFailedException(sprintf('Expected to see "%s" on %s', $text, $page->url()));
        }
    }

    #[When('I take a screenshot named :name')]
    public function iTakeAScreenshotNamed(string $name): void
    {
        $this->getPlaywrightManager()->saveScreenshot($name);
    }
}

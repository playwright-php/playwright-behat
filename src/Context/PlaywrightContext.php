<?php

declare(strict_types=1);

/*
 * This file is part of the playwright-php/playwright package.
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PlaywrightPHP\Behat\Context;

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Hook\AfterScenario;
use Behat\Hook\BeforeScenario;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use PHPUnit\Framework\Assert;
use PlaywrightPHP\Behat\Exception\BrowserNotStartedException;

/**
 * @author Simon André <smn.andre@gmail.com>
 */
final class PlaywrightContext extends RawPlaywrightContext
{
    #[BeforeScenario]
    public function setupBrowser(BeforeScenarioScope $scope): void
    {
        $this->startBrowser();
    }

    #[AfterScenario]
    public function teardownBrowser(AfterScenarioScope $scope): void
    {
        if (($this->config['auto_screenshot_on_failure'] ?? true) && isset($this->page)) {
            if (false === $scope->getTestResult()->isPassed()) {
                $this->takeScreenshot('failed-scenario-'.$scope->getScenario()->getTitle());
            }
        }

        $this->stopBrowser();
    }

    #[Given('I am on :url')]
    #[When('I go to :url')]
    public function iAmOn(string $url): void
    {
        $this->ensureBrowserStarted();

        $finalUrl = $this->resolveUrl($url);
        $this->page->goto($finalUrl);
    }

    #[When('I click on :selector')]
    public function iClickOn(string $selector): void
    {
        $this->ensureBrowserStarted();
        $this->page->locator($selector)->click();
    }

    #[When('I fill :selector with :value')]
    public function iFillWith(string $selector, string $value): void
    {
        $this->ensureBrowserStarted();
        $this->page->locator($selector)->fill($value);
    }

    #[Then('I should see :text')]
    public function iShouldSee(string $text): void
    {
        $this->ensureBrowserStarted();
        $content = $this->page->content();
        Assert::assertStringContainsString($text, $content);
    }

    #[When('I take a screenshot named :name')]
    public function iTakeAScreenshotNamed(string $name): void
    {
        $this->ensureBrowserStarted();
        $this->screenshots[] = $this->page->screenshotAuto($name);
    }

    private function ensureBrowserStarted(): void
    {
        if (!isset($this->page)) {
            throw new BrowserNotStartedException();
        }
    }

    private function resolveUrl(string $url): string
    {
        if (filter_var($url, FILTER_VALIDATE_URL)) {
            return $url;
        }

        $baseUrl = $this->config['base_url'] ?? '';
        if ($baseUrl && !str_starts_with($url, '/')) {
            $url = '/'.$url;
        }

        return $baseUrl.$url;
    }
}

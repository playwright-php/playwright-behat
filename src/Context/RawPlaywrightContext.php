<?php

declare(strict_types=1);

/*
 * This file is part of the playwright-php/playwright package.
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PlaywrightPHP\Behat\Context;

use Behat\Behat\Context\Context;
use PlaywrightPHP\Browser\BrowserInterface;
use PlaywrightPHP\Browser\BrowserType;
use PlaywrightPHP\Configuration\PlaywrightConfig;
use PlaywrightPHP\Page\PageInterface;
use PlaywrightPHP\PlaywrightClient;
use PlaywrightPHP\PlaywrightFactory;

/**
 * @author Simon André <smn.andre@gmail.com>
 */
abstract class RawPlaywrightContext implements Context
{
    protected PlaywrightClient $playwright;
    protected BrowserInterface $browser;
    protected PageInterface $page;
    protected PlaywrightConfig $playwrightConfig;
    protected array $config = [];
    protected array $screenshots = [];

    public function __construct(array $config = [])
    {
        if (!empty($config)) {
            $this->setPlaywrightConfig($config);
        }
    }

    public function setPlaywrightConfig(array $config): void
    {
        $this->config = $config;

        $browserType = match ($config['browser'] ?? 'chromium') {
            'firefox' => BrowserType::FIREFOX,
            'webkit' => BrowserType::WEBKIT,
            default => BrowserType::CHROMIUM,
        };

        $this->playwrightConfig = new PlaywrightConfig(
            browser: $browserType,
            headless: $config['headless'] ?? true,
            screenshotDir: $config['screenshot_dir'] ?? sys_get_temp_dir().'/behat-screenshots',
            timeoutMs: $config['timeout'] ?? 30000,
            slowMoMs: isset($config['slow_mo']['delay']) ? (int) $config['slow_mo']['delay'] : 0
        );
    }

    protected function startBrowser(): void
    {
        if (!isset($this->playwright)) {
            $this->playwright = PlaywrightFactory::create($this->playwrightConfig);

            $browserBuilder = match ($this->playwrightConfig->browser) {
                BrowserType::FIREFOX => $this->playwright->firefox(),
                BrowserType::WEBKIT => $this->playwright->webkit(),
                default => $this->playwright->chromium(),
            };

            $browserBuilder->withHeadless($this->playwrightConfig->headless);

            if ($this->playwrightConfig->slowMoMs > 0) {
                $browserBuilder->withSlowMo($this->playwrightConfig->slowMoMs);
            }

            if (!empty($this->playwrightConfig->args)) {
                $browserBuilder->withArgs($this->playwrightConfig->args);
            }

            $this->browser = $browserBuilder->launch();
            $this->page = $this->browser->newPage();

            if (isset($this->config['viewport'])) {
                $this->page->setViewportSize(
                    $this->config['viewport']['width'],
                    $this->config['viewport']['height']
                );
            }
        }
    }

    protected function stopBrowser(): void
    {
        $this->page?->close();
        $this->browser?->close();
        $this->playwright?->close();
        unset($this->playwright, $this->browser, $this->page);
    }

    protected function takeScreenshot(string $name = ''): string
    {
        if (!$name) {
            $name = 'screenshot-'.date('Y-m-d-H-i-s');
        }

        $path = $this->page->screenshotAuto($name);
        $this->screenshots[] = $path;

        return $path;
    }

    public function getPage(): ?PageInterface
    {
        return $this->page ?? null;
    }

    public function getBrowser(): ?BrowserInterface
    {
        return $this->browser ?? null;
    }

    public function getPlaywright(): ?PlaywrightClient
    {
        return $this->playwright ?? null;
    }

    public function getScreenshots(): array
    {
        return $this->screenshots;
    }
}

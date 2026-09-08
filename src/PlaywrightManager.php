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

namespace Playwright\Behat;

use Playwright\Browser\BrowserContextInterface;
use Playwright\Browser\BrowserInterface;
use Playwright\Browser\BrowserType;
use Playwright\Configuration\PlaywrightConfig;
use Playwright\Page\PageInterface;
use Playwright\PlaywrightClient;
use Playwright\PlaywrightFactory;

/**
 * Owns the Playwright client and browser for the whole Behat run and hands
 * out one isolated page per scenario.
 *
 * The browser is launched on first use and kept until shutdown(). Each page
 * lives in its own browser context, so cookies and storage never leak from
 * one scenario to the next.
 */
final class PlaywrightManager
{
    private ?PlaywrightClient $client = null;
    private ?BrowserInterface $browser = null;
    private ?BrowserContextInterface $context = null;
    private ?PageInterface $page = null;

    /**
     * @param array<string, mixed> $config processed extension configuration
     */
    public function __construct(private readonly array $config)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * Timeout in milliseconds for browser actions and navigations.
     */
    public function getTimeout(): int
    {
        return (int) ($this->config['timeout'] ?? 30000);
    }

    public function getBrowser(): BrowserInterface
    {
        if (null !== $this->browser) {
            return $this->browser;
        }

        $config = $this->createPlaywrightConfig();
        $this->client = PlaywrightFactory::create($config);

        $builder = match ($config->browser) {
            BrowserType::FIREFOX => $this->client->firefox(),
            BrowserType::WEBKIT => $this->client->webkit(),
            BrowserType::CHROMIUM => $this->client->chromium(),
        };
        $builder->withHeadless($config->headless);
        if ($config->slowMoMs > 0) {
            $builder->withSlowMo($config->slowMoMs);
        }

        return $this->browser = $builder->launch();
    }

    public function hasPage(): bool
    {
        return null !== $this->page;
    }

    /**
     * Returns the page of the current scenario, opening it on first call.
     */
    public function getPage(): PageInterface
    {
        if (null !== $this->page) {
            return $this->page;
        }

        $options = ['viewport' => $this->config['viewport'] ?? ['width' => 1280, 'height' => 720]];
        if (is_string($this->config['base_url'] ?? null)) {
            $options['baseURL'] = $this->config['base_url'];
        }

        $this->context = $this->getBrowser()->newContext($options);
        $this->context->setDefaultTimeout($this->getTimeout());

        return $this->page = $this->context->newPage();
    }

    /**
     * Closes the current scenario page and its browser context.
     */
    public function closePage(): void
    {
        $context = $this->context;
        $this->page = null;
        $this->context = null;

        $context?->close();
    }

    /**
     * Saves a screenshot of the current page under the configured directory.
     *
     * @return string the written file path
     */
    public function saveScreenshot(string $name): string
    {
        $dir = $this->getScreenshotDir();
        if (!is_dir($dir) && !@mkdir($dir, 0777, true) && !is_dir($dir)) {
            throw new \RuntimeException(sprintf('Cannot create screenshot directory "%s".', $dir));
        }

        $path = $dir.'/'.self::slugify($name).'.png';
        $this->getPage()->screenshot($path);

        return $path;
    }

    public function getScreenshotDir(): string
    {
        $dir = $this->config['screenshot_dir'] ?? null;

        return is_string($dir) && '' !== $dir ? rtrim($dir, '/') : sys_get_temp_dir().'/playwright-behat';
    }

    /**
     * Closes the page, the browser and the Playwright client.
     */
    public function shutdown(): void
    {
        $this->closePage();

        $browser = $this->browser;
        $client = $this->client;
        $this->browser = null;
        $this->client = null;

        $browser?->close();
        $client?->close();
    }

    private function createPlaywrightConfig(): PlaywrightConfig
    {
        // timeoutMs bounds the round trip to the Node bridge, not a browser
        // action: keep it above the action timeout so the browser reports the
        // failure (with its call log) before the transport gives up.
        return new PlaywrightConfig(
            browser: BrowserType::from(is_string($this->config['browser'] ?? null) ? $this->config['browser'] : 'chromium'),
            headless: (bool) ($this->config['headless'] ?? true),
            timeoutMs: max(30000, $this->getTimeout() + 5000),
            slowMoMs: (int) ($this->config['slow_mo'] ?? 0),
            screenshotDir: $this->getScreenshotDir(),
        );
    }

    private static function slugify(string $name): string
    {
        $slug = strtolower(trim((string) preg_replace('/[^A-Za-z0-9]+/', '-', $name), '-'));

        return '' === $slug ? 'screenshot' : $slug;
    }
}

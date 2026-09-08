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

namespace Playwright\Behat\Tests\Unit\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Playwright\Behat\Context\Initializer\PlaywrightAwareInitializer;
use Playwright\Behat\EventListener\ScenarioListener;
use Playwright\Behat\PlaywrightManager;
use Playwright\Behat\ServiceContainer\PlaywrightExtension;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\ContainerBuilder;

#[CoversClass(PlaywrightExtension::class)]
final class PlaywrightExtensionTest extends TestCase
{
    private PlaywrightExtension $extension;

    protected function setUp(): void
    {
        $this->extension = new PlaywrightExtension();
    }

    public function testConfigKey(): void
    {
        $this->assertSame('playwright', $this->extension->getConfigKey());
    }

    public function testConfigureProvidesDefaults(): void
    {
        $config = $this->processConfig([]);

        $this->assertTrue($config['headless']);
        $this->assertSame('chromium', $config['browser']);
        $this->assertSame(30000, $config['timeout']);
        $this->assertNull($config['base_url']);
        $this->assertSame(['width' => 1280, 'height' => 720], $config['viewport']);
        $this->assertTrue($config['auto_screenshot_on_failure']);
        $this->assertSame(0, $config['slow_mo']);
    }

    public function testConfigureRejectsUnknownBrowser(): void
    {
        $this->expectException(\Symfony\Component\Config\Definition\Exception\InvalidConfigurationException::class);

        $this->processConfig(['browser' => 'opera']);
    }

    public function testLoadRegistersManagerInitializerAndListener(): void
    {
        $container = new ContainerBuilder();
        $config = $this->processConfig(['base_url' => 'http://localhost:8000']);

        $this->extension->load($container, $config);

        $manager = $container->getDefinition(PlaywrightManager::class);
        $this->assertSame($config, $manager->getArgument(0));

        $initializer = $container->getDefinition(PlaywrightAwareInitializer::class);
        $this->assertTrue($initializer->hasTag(ContextExtension::INITIALIZER_TAG));

        $listener = $container->getDefinition(ScenarioListener::class);
        $this->assertTrue($listener->hasTag(EventDispatcherExtension::SUBSCRIBER_TAG));
    }

    public function testLoadResolvesBasePathInScreenshotDir(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('paths.base', '/project');
        $config = $this->processConfig([]);

        $this->extension->load($container, $config);

        $argument = $container->getParameterBag()->resolveValue($container->getDefinition(PlaywrightManager::class)->getArgument(0));
        $this->assertSame('/project/var/screenshots', $argument['screenshot_dir']);
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return array<string, mixed>
     */
    private function processConfig(array $config): array
    {
        $tree = new TreeBuilder('playwright');
        $this->extension->configure($tree->getRootNode());

        return (new Processor())->process($tree->buildTree(), [$config]);
    }
}

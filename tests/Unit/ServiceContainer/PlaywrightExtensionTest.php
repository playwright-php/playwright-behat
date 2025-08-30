<?php

declare(strict_types=1);

/*
 * This file is part of the playwright-php/playwright package.
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PlaywrightPHP\Behat\Tests\Unit\ServiceContainer;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use PlaywrightPHP\Behat\ServiceContainer\PlaywrightExtension;
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

    public function testGetConfigKey(): void
    {
        $this->assertEquals('playwright', $this->extension->getConfigKey());
    }

    public function testDefaultConfiguration(): void
    {
        $processor = new Processor();
        $config = $processor->processConfiguration(
            $this->extension->getConfigurationDefinition(),
            []
        );

        $this->assertTrue($config['headless']);
        $this->assertEquals('chromium', $config['browser']);
        $this->assertEquals(30000, $config['timeout']);
        $this->assertTrue($config['auto_screenshot_on_failure']);
    }

    public function testLoad(): void
    {
        $container = new ContainerBuilder();

        $config = [
            'headless' => true,
            'browser' => 'chromium',
            'screenshot_dir' => '/tmp/screenshots',
            'timeout' => 30000,
            'base_url' => null,
            'viewport' => ['width' => 1280, 'height' => 720],
            'browser_options' => [],
            'auto_screenshot_on_failure' => true,
            'slow_mo' => ['delay' => 0],
        ];

        $this->extension->load($container, $config);

        $this->assertTrue($container->hasParameter('playwright.config'));
    }
}

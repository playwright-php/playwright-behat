<?php

declare(strict_types=1);

/*
 * This file is part of the playwright-php/playwright package.
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace PlaywrightPHP\Behat\ServiceContainer;

use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class PlaywrightExtension implements ExtensionInterface
{
    public const PLAYWRIGHT_ID = 'playwright';

    public function getConfigKey(): string
    {
        return self::PLAYWRIGHT_ID;
    }

    public function initialize(ExtensionManager $extensionManager): void
    {
        // Extension initialization if needed
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('headless')
                    ->defaultTrue()
                    ->info('Run browser in headless mode')
                ->end()
                ->enumNode('browser')
                    ->values(['chromium', 'firefox', 'webkit'])
                    ->defaultValue('chromium')
                    ->info('Browser type to use')
                ->end()
                ->scalarNode('screenshot_dir')
                    ->defaultValue('%paths.base%/var/screenshots')
                    ->info('Directory to store screenshots')
                ->end()
                ->integerNode('timeout')
                    ->defaultValue(30000)
                    ->min(1000)
                    ->info('Default timeout in milliseconds')
                ->end()
                ->scalarNode('base_url')
                    ->defaultNull()
                    ->info('Base URL for relative navigation')
                ->end()
                ->arrayNode('viewport')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('width')
                            ->defaultValue(1280)
                            ->min(100)
                        ->end()
                        ->integerNode('height')
                            ->defaultValue(720)
                            ->min(100)
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('browser_options')
                    ->useAttributeAsKey('name')
                    ->prototype('variable')->end()
                    ->info('Additional browser launch options')
                ->end()
                ->booleanNode('auto_screenshot_on_failure')
                    ->defaultTrue()
                    ->info('Automatically take screenshot on scenario failure')
                ->end()
                ->arrayNode('slow_mo')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('delay')
                            ->defaultValue(0)
                            ->min(0)
                            ->info('Delay between actions in milliseconds')
                        ->end()
                    ->end()
                ->end()
            ->end();
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $this->loadParameters($container, $config);
    }

    public function process(ContainerBuilder $container): void
    {
        // Post-processing if needed
    }

    public function getConfigurationDefinition(): ConfigurationInterface
    {
        return new class implements ConfigurationInterface {
            public function getConfigTreeBuilder(): TreeBuilder
            {
                $treeBuilder = new TreeBuilder('playwright');
                $rootNode = $treeBuilder->getRootNode();

                $rootNode
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('headless')
                            ->defaultTrue()
                            ->info('Run browser in headless mode')
                        ->end()
                        ->enumNode('browser')
                            ->values(['chromium', 'firefox', 'webkit'])
                            ->defaultValue('chromium')
                            ->info('Browser type to use')
                        ->end()
                        ->scalarNode('screenshot_dir')
                            ->defaultValue('%paths.base%/var/screenshots')
                            ->info('Directory to store screenshots')
                        ->end()
                        ->integerNode('timeout')
                            ->defaultValue(30000)
                            ->min(1000)
                            ->info('Default timeout in milliseconds')
                        ->end()
                        ->scalarNode('base_url')
                            ->defaultNull()
                            ->info('Base URL for relative navigation')
                        ->end()
                        ->arrayNode('viewport')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('width')
                                    ->defaultValue(1280)
                                    ->min(100)
                                ->end()
                                ->integerNode('height')
                                    ->defaultValue(720)
                                    ->min(100)
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('browser_options')
                            ->useAttributeAsKey('name')
                            ->prototype('variable')->end()
                            ->info('Additional browser launch options')
                        ->end()
                        ->booleanNode('auto_screenshot_on_failure')
                            ->defaultTrue()
                            ->info('Automatically take screenshot on scenario failure')
                        ->end()
                        ->arrayNode('slow_mo')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->integerNode('delay')
                                    ->defaultValue(0)
                                    ->min(0)
                                    ->info('Delay between actions in milliseconds')
                                ->end()
                            ->end()
                        ->end()
                    ->end();

                return $treeBuilder;
            }
        };
    }

    private function loadParameters(ContainerBuilder $container, array $config): void
    {
        $container->setParameter('playwright.config', $config);
        $container->setParameter('playwright.headless', $config['headless']);
        $container->setParameter('playwright.browser', $config['browser']);
        $container->setParameter('playwright.screenshot_dir', $config['screenshot_dir']);
        $container->setParameter('playwright.timeout', $config['timeout']);
        $container->setParameter('playwright.base_url', $config['base_url']);
        $container->setParameter('playwright.viewport', $config['viewport']);
    }
}

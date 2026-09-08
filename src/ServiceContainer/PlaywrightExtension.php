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

namespace Playwright\Behat\ServiceContainer;

use Behat\Behat\Context\ServiceContainer\ContextExtension;
use Behat\Testwork\EventDispatcher\ServiceContainer\EventDispatcherExtension;
use Behat\Testwork\ServiceContainer\Extension as ExtensionInterface;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use Playwright\Behat\Context\Initializer\PlaywrightAwareInitializer;
use Playwright\Behat\EventListener\ScenarioListener;
use Playwright\Behat\PlaywrightManager;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

final class PlaywrightExtension implements ExtensionInterface
{
    public function getConfigKey(): string
    {
        return 'playwright';
    }

    public function initialize(ExtensionManager $extensionManager): void
    {
    }

    public function configure(ArrayNodeDefinition $builder): void
    {
        $builder
            ->addDefaultsIfNotSet()
            ->children()
                ->booleanNode('headless')
                    ->defaultTrue()
                    ->info('Run the browser without a visible window')
                ->end()
                ->enumNode('browser')
                    ->values(['chromium', 'firefox', 'webkit'])
                    ->defaultValue('chromium')
                    ->info('Browser engine to launch')
                ->end()
                ->scalarNode('base_url')
                    ->defaultNull()
                    ->info('Prefix for relative URLs passed to goto()')
                ->end()
                ->scalarNode('screenshot_dir')
                    ->defaultValue('%paths.base%/var/screenshots')
                    ->info('Directory for named and failure screenshots')
                ->end()
                ->booleanNode('auto_screenshot_on_failure')
                    ->defaultTrue()
                    ->info('Save a screenshot when a scenario fails')
                ->end()
                ->integerNode('timeout')
                    ->defaultValue(30000)
                    ->min(0)
                    ->info('Default action and navigation timeout in milliseconds')
                ->end()
                ->integerNode('slow_mo')
                    ->defaultValue(0)
                    ->min(0)
                    ->info('Delay between browser operations in milliseconds')
                ->end()
                ->arrayNode('viewport')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->integerNode('width')->defaultValue(1280)->min(1)->end()
                        ->integerNode('height')->defaultValue(720)->min(1)->end()
                    ->end()
                ->end()
            ->end();
    }

    public function load(ContainerBuilder $container, array $config): void
    {
        $container->setDefinition(PlaywrightManager::class, new Definition(PlaywrightManager::class, [$config]));

        $initializer = new Definition(PlaywrightAwareInitializer::class, [new Reference(PlaywrightManager::class)]);
        $initializer->addTag(ContextExtension::INITIALIZER_TAG);
        $container->setDefinition(PlaywrightAwareInitializer::class, $initializer);

        $listener = new Definition(ScenarioListener::class, [new Reference(PlaywrightManager::class)]);
        $listener->addTag(EventDispatcherExtension::SUBSCRIBER_TAG);
        $container->setDefinition(ScenarioListener::class, $listener);
    }

    public function process(ContainerBuilder $container): void
    {
    }
}

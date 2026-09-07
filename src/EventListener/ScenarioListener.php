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

namespace Playwright\Behat\EventListener;

use Behat\Behat\EventDispatcher\Event\AfterScenarioTested;
use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use Playwright\Behat\PlaywrightManager;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Closes the scenario page after each scenario (saving a screenshot first
 * when it failed) and the browser after the run.
 */
final class ScenarioListener implements EventSubscriberInterface
{
    public function __construct(private readonly PlaywrightManager $manager)
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ScenarioTested::AFTER => ['afterScenario', -10],
            ExampleTested::AFTER => ['afterScenario', -10],
            ExerciseCompleted::AFTER => ['afterExercise', -10],
        ];
    }

    public function afterScenario(AfterScenarioTested $event): void
    {
        if (!$this->manager->hasPage()) {
            return;
        }

        try {
            if (!$event->getTestResult()->isPassed() && ($this->manager->getConfig()['auto_screenshot_on_failure'] ?? true)) {
                $scenario = $event->getScenario();
                $this->manager->saveScreenshot(sprintf('failed-%s-%d', $scenario->getTitle() ?? 'scenario', $scenario->getLine()));
            }
        } finally {
            $this->manager->closePage();
        }
    }

    public function afterExercise(): void
    {
        $this->manager->shutdown();
    }
}

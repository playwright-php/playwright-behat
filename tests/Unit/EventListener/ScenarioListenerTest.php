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

namespace Playwright\Behat\Tests\Unit\EventListener;

use Behat\Behat\EventDispatcher\Event\ExampleTested;
use Behat\Behat\EventDispatcher\Event\ScenarioTested;
use Behat\Testwork\EventDispatcher\Event\ExerciseCompleted;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Playwright\Behat\EventListener\ScenarioListener;

#[CoversClass(ScenarioListener::class)]
final class ScenarioListenerTest extends TestCase
{
    public function testListensToScenariosOutlineExamplesAndTheEndOfTheRun(): void
    {
        $events = ScenarioListener::getSubscribedEvents();

        $this->assertArrayHasKey(ScenarioTested::AFTER, $events);
        $this->assertArrayHasKey(ExampleTested::AFTER, $events);
        $this->assertArrayHasKey(ExerciseCompleted::AFTER, $events);
    }
}

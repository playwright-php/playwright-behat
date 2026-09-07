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

namespace Playwright\Behat\Context\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;
use Playwright\Behat\Context\PlaywrightAwareContext;
use Playwright\Behat\PlaywrightManager;

final class PlaywrightAwareInitializer implements ContextInitializer
{
    public function __construct(private readonly PlaywrightManager $manager)
    {
    }

    public function initializeContext(Context $context): void
    {
        if ($context instanceof PlaywrightAwareContext) {
            $context->setPlaywrightManager($this->manager);
        }
    }
}

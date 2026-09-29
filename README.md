<div align="center">
<a href="https://github.com/playwright-php"><img src="https://github.com/playwright-php/.github/raw/main/profile/playwright-php.png" alt="Playwright PHP" /></a>

&nbsp; ![PHP Version](https://img.shields.io/badge/PHP-8.2+-05971B?labelColor=09161E&color=1D8D23&logoColor=FFFFFF)
&nbsp; ![CI](https://img.shields.io/github/actions/workflow/status/playwright-php/playwright-behat/CI.yml?branch=main&label=Tests&color=1D8D23&labelColor=09161E&logoColor=FFFFFF)
&nbsp; ![Release](https://img.shields.io/github/v/release/playwright-php/playwright-behat?label=Stable&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)
&nbsp; ![License](https://img.shields.io/github/license/playwright-php/playwright-behat?label=License&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)

</div>

# Playwright PHP for Behat

A Behat extension that runs your scenarios in a real browser through
[Playwright PHP](https://github.com/playwright-php/playwright).

Use it when feature files are the contract with your product owners and the
steps need a browser: Chromium, Firefox, or WebKit. The extension launches the
browser once per run and gives every scenario its own browser context, so
cookies and storage never leak between scenarios and no browser is relaunched.

If your suite already relies on Mink, use
[playwright-mink](https://github.com/playwright-php/playwright-mink) instead:
it keeps the Mink API and swaps the driver.

## Installation

The extension requires PHP 8.2 or later, Behat 3.23 or later (including 4.x),
and Playwright PHP 1.4 or later.

```bash
composer require --dev playwright-php/playwright-behat
vendor/bin/playwright-install --browsers
```

## Configuration

Enable the extension and add the built-in context to a suite. With Behat 3.x,
in `behat.yml`:

```yaml
default:
  extensions:
    Playwright\Behat\ServiceContainer\PlaywrightExtension:
      base_url: 'http://localhost:8000'
  suites:
    web:
      paths: ['%paths.base%/features']
      contexts:
        - Playwright\Behat\Context\PlaywrightContext
```

Behat 4 drops YAML configuration. The PHP form below works on Behat 3.x and 4.x:

```php
<?php
// behat.php

use Behat\Config\Config;
use Behat\Config\Extension;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Playwright\Behat\Context\PlaywrightContext;
use Playwright\Behat\ServiceContainer\PlaywrightExtension;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withExtension(new Extension(PlaywrightExtension::class, [
            'base_url' => 'http://localhost:8000',
        ]))
        ->withSuite((new Suite('web'))
            ->withPaths('%paths.base%/features')
            ->withContexts(PlaywrightContext::class)));
```

All options and their defaults:

| Option                       | Default                        | Meaning                                                    |
|------------------------------|--------------------------------|------------------------------------------------------------|
| `browser`                    | `chromium`                     | `chromium`, `firefox`, or `webkit`                         |
| `headless`                   | `true`                         | Run without a visible window                               |
| `base_url`                   | `null`                         | Prefix for relative URLs passed to `goto()`                |
| `timeout`                    | `30000`                        | Default timeout for actions and navigations, in ms         |
| `slow_mo`                    | `0`                            | Delay between browser operations, in ms                    |
| `viewport`                   | `{width: 1280, height: 720}`   | Viewport of every scenario page                            |
| `screenshot_dir`             | `%paths.base%/var/screenshots` | Where named and failure screenshots go                     |
| `auto_screenshot_on_failure` | `true`                         | Save `failed-<scenario>-<line>.png` when a scenario fails  |

## Usage

The built-in `PlaywrightContext` provides these steps. Selectors are Playwright
selectors: CSS by default, plus `text=`, `role=` and the other engines.

```gherkin
Feature: Login
  Scenario: Sign in with valid credentials
    Given I am on "/login"
    When I fill "#email" with "user@example.com"
    And I fill "#password" with "secret"
    And I click on "button[type=submit]"
    Then I should see "Dashboard"
    And I take a screenshot named "after login"
```

| Step                                  | Effect                                              |
|---------------------------------------|-----------------------------------------------------|
| `Given I am on :url`, `When I go to :url` | Navigates; relative URLs resolve against `base_url` |
| `When I click on :selector`           | Waits for the element, then clicks it               |
| `When I fill :selector with :value`   | Waits for the field, then fills it                  |
| `Then I should see :text`             | Fails unless the page HTML contains the text        |
| `When I take a screenshot named :name`| Saves `<slug>.png` under `screenshot_dir`           |

A failing step throws `Playwright\Behat\Exception\ExpectationFailedException`,
or the Playwright PHP exception for a timeout.

## Custom steps

Extend `RawPlaywrightContext` to write your own steps against the Playwright
PHP `Page`. The page is opened on first use and closed after the scenario:

```php
<?php

use Behat\Step\When;
use Playwright\Behat\Context\RawPlaywrightContext;

final class AdminContext extends RawPlaywrightContext
{
    #[When('I sign in as an administrator')]
    public function signInAsAdministrator(): void
    {
        $page = $this->getPage();
        $page->goto('/admin/login');
        $page->locator('#username')->fill('admin');
        $page->locator('#password')->fill('secret');
        $page->locator('[type="submit"]')->click();
    }
}
```

A context that cannot extend `RawPlaywrightContext` can implement
`Playwright\Behat\Context\PlaywrightAwareContext` instead: the extension calls
`setPlaywrightManager()` on it before the run, and
`PlaywrightManager::getPage()` returns the page of the current scenario.

## Testing

```bash
composer install
vendor/bin/playwright-install --browsers
vendor/bin/phpunit
```

The suite includes a real `behat` run against `tests/Fixtures`, including a
failing scenario that must leave a screenshot. Repository CI runs it on PHP
8.2, 8.3 and 8.4 against the latest Behat 3.x, plus one job on the lowest
supported dependencies and one on the latest Behat 4.x.

## License

Playwright PHP for Behat is released under the [MIT License](LICENSE).

<div align="center">

&nbsp; ![PHP Version](https://img.shields.io/badge/PHP-8.3+-05971B?labelColor=09161E&color=1D8D23&logoColor=FFFFFF)
&nbsp; ![CI](https://img.shields.io/github/actions/workflow/status/playwright-php/playwright-behat/CI.yaml?branch=main&label=Tests&color=1D8D23&labelColor=09161E&logoColor=FFFFFF)
&nbsp; ![Release](https://img.shields.io/github/v/release/playwright-php/playwright-behat?label=Stable&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)
&nbsp; ![License](https://img.shields.io/github/license/playwright-php/playwright-behat?label=License&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)

</div>

# PlaywrightPHP - Behat Extension

> [!WARNING]
> This package is in active development phase.

A Behat extension that integrates [PlaywrightPHP](https://playwright-php.dev) for modern browser automation testing.

## Installation

```bash
composer require playwrightphp/behat-extension --dev
```

## Configuration

Add the extension to your `behat.yml`:

```yaml
default:
  extensions:
    Playwright\Behat\ServiceContainer\PlaywrightExtension:
      headless: false
      browser: chromium
      screenshot_dir: 'var/screenshots'
      timeout: 30000
      base_url: 'http://localhost:8000'
      viewport:
        width: 1280
        height: 720
      
  suites:
    web:
      paths: ['features']
      contexts:
        - Playwright\Behat\Context\PlaywrightContext
```

## Usage

### Basic Steps

```gherkin
Feature: User Authentication
  Scenario: Login with valid credentials
    Given I am on "/login"
    When I fill "#email" with "user@test.com"
    And I fill "#password" with "password123"
    And I click on "#login-button"
    Then I should see "Welcome"
```

### Custom Context

Extend `RawPlaywrightContext` for custom logic:

```php
use Playwright\Behat\Context\RawPlaywrightContext;
use Behat\Step\When;

class MyCustomContext extends RawPlaywrightContext
{
    #[When('I login as admin')]
    public function loginAsAdmin(): void
    {
        $this->page->goto('/admin/login');
        $this->page->locator('#username')->fill('admin');
        $this->page->locator('#password')->fill('secret');
        $this->page->locator('[type="submit"]')->click();
    }
}
```

## Configuration Options

| Option                       | Type   | Default                      | Description                              |
|------------------------------|--------|------------------------------|------------------------------------------|
| `headless`                   | bool   | true                         | Run browser in headless mode             |
| `browser`                    | string | chromium                     | Browser type (chromium, firefox, webkit) |
| `screenshot_dir`             | string | %paths.base%/var/screenshots | Screenshot directory                     |
| `timeout`                    | int    | 30000                        | Default timeout in milliseconds          |
| `base_url`                   | string | null                         | Base URL for relative paths              |
| `viewport.width`             | int    | 1280                         | Browser viewport width                   |
| `viewport.height`            | int    | 720                          | Browser viewport height                  |
| `auto_screenshot_on_failure` | bool   | true                         | Auto screenshot on failure               |
| `browser_options`            | array  | []                           | Additional browser launch options        |
| `slow_mo.delay`              | int    | 0                            | Delay between actions (ms)               |

## Available Steps

### Navigation
- `Given I am on ":url"`
- `When I go to ":url"`

### Interactions  
- `When I click on ":selector"`
- `When I fill ":selector" with ":value"`
- `When I select ":value" from ":selector"`
- `When I check ":selector"`
- `When I press ":button"`

### Waits
- `When I wait for ":selector"`
- `When I wait :seconds seconds`
- `When I wait for the page to load`

### Assertions
- `Then I should see ":text"`
- `Then ":selector" should be visible`
- `Then the current URL should contain ":fragment"`

And many more! Check the `PlaywrightContext` class for all available steps.

## License

This package is released by the [Playwright PHP](https://playwright-php.dev) 
project under the MIT License. See the [LICENSE](LICENSE) file for details.

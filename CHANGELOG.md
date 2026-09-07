# CHANGELOG

## [Unreleased]

### Added

- `PlaywrightExtension` registers a `PlaywrightManager` shared by the whole run, a context initializer for every `PlaywrightAwareContext`, and a listener that closes the scenario page after each scenario and the browser after the run.
- `RawPlaywrightContext::getPage()` opens one isolated page per scenario, in its own browser context.
- `ExpectationFailedException`, thrown by assertion steps.
- A screenshot named `failed-<scenario>-<line>.png` is saved when a scenario fails (`auto_screenshot_on_failure`).
- Support for Behat 4.0.0-alpha1 and Symfony 8 components.

### Changed

- Requires PHP 8.2 or later and `playwright-php/playwright` 1.4 or later.
- Relative URLs resolve through Playwright's `baseURL` context option.
- `slow_mo` is an integer (milliseconds) instead of `slow_mo.delay`.

### Removed

- `browser_options`, which was accepted and never read.
- `BrowserNotStartedException`: a page is always available inside a scenario.
- The `@BeforeScenario` / `@AfterScenario` hooks on `PlaywrightContext`; the extension manages the lifecycle.

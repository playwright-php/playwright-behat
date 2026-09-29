# CHANGELOG

## [Unreleased]

- Support Behat 4.0 stable: `behat/behat` now allows `^3.23 || ^4.0`, and CI runs
  against the latest Behat 4.x instead of 4.0.0-alpha1.

## [0.7.0] - 2026-09-08

First release. A Behat extension that runs scenarios in a Playwright-controlled
browser: one browser per run, one isolated page per scenario, five built-in
steps, a screenshot on failure, and `RawPlaywrightContext` for custom steps.

Runs on PHP 8.2 to 8.4, Behat 3.23 and later, and Behat 4.0.0-alpha1.

Before 1.0, a minor version may break backward compatibility.

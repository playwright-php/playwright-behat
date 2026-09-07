<div align="center">
<a href="https://github.com/playwright-php"><img src="https://github.com/playwright-php/.github/raw/main/profile/playwright-php.png" alt="Playwright PHP" /></a>

&nbsp; ![PHP Version](https://img.shields.io/badge/PHP-8.2+-05971B?labelColor=09161E&color=1D8D23&logoColor=FFFFFF)
&nbsp; ![CI](https://img.shields.io/github/actions/workflow/status/playwright-php/playwright-behat/CI.yml?branch=main&label=Tests&color=1D8D23&labelColor=09161E&logoColor=FFFFFF)
&nbsp; ![License](https://img.shields.io/github/license/playwright-php/playwright-behat?label=License&labelColor=09161E&color=1D8D23&logoColor=FFFFFF)

</div>

# Playwright PHP for Behat

A Behat extension that drives a real browser through
[Playwright PHP](https://github.com/playwright-php/playwright).

## Status

Unreleased. The package installs and its test suite runs, but the extension
configuration is not yet connected to the Behat contexts, so a `behat.yml`
setup does not work end to end. Do not use it in an application yet.

If you use Behat through Mink, use
[playwright-mink](https://github.com/playwright-php/playwright-mink) instead:
it is released and supported.

## Development

```bash
composer install
vendor/bin/playwright-install --browsers
composer cs-check
composer sa
composer test
```

## License

Released under the [MIT License](LICENSE).

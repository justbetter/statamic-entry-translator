<a href="github.com/justbetter/statamic-static-cache-warmer" title="JustBetter">
    <img src="./art/banner.svg" alt="Banner">
</a>

# Statamic Static cache warmer

This Statamic addon provides actions that will automaticly translate content for an entry to multiple locales.

## Requirements

- PHP ^8.3 or ^8.4
- Laravel ^12.0
- Statamic ^5.0

## Installation

You can install this addon via Composer:

```bash
composer require just-better/statamic-entry-translator
```

## Configuration

You can publish the config by

```bash
php artisan vendor:publish --tag=justbetter-statamic-entry-translator
```

You can now find the config file at `config/justbetter/statamic-entry-translator.php`.
After publishing the config, you can set the queue.


## Credits

- [Bob Wezelman](https://github.com/BobWez98)
- [Kevin Meijer](https://github.com/kevinmeijer97)
- [All Contributors](../../contributors)

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

<a href="https://justbetter.nl" title="JustBetter">
    <img src="./art/footer.svg" alt="JustBetter logo">
</a>
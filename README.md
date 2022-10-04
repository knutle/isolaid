# Aids your isolated package testing

[![Latest Version on Packagist](https://img.shields.io/packagist/v/knutle/isolaid.svg?style=flat-square)](https://packagist.org/packages/knutle/isolaid)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/knutle/isolaid/run-tests?label=tests)](https://github.com/knutle/isolaid/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/knutle/isolaid/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/knutle/isolaid/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/knutle/isolaid.svg?style=flat-square)](https://packagist.org/packages/knutle/isolaid)

This package allows you to define test routes to serve your package views with test data while you are working on them, without requiring a full Laravel install or build step.

## Installation

You can install the package via composer:

```bash
composer require --dev knutle/isolaid
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="isolaid-config"
```

This is the contents of the published config file:

```php
use Illuminate\Support\Facades\Route;

return [
    'routes' => function () {
        // put your debug routes here
        
        Route::get('/', function () {
            return view('isolaid::debug', [
                'today' => now()->format('Y-m-d H:i:s')
            ]);
        });
    }
];
```

## Usage

```shell
./isolaid serve
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Credits

- [Knut Leborg](https://github.com/knutle)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

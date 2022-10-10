# Preview package views in isolation

[![Latest Version on Packagist](https://img.shields.io/packagist/v/knutle/isoview.svg?style=flat-square)](https://packagist.org/packages/knutle/isoview)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/knutle/isoview/run-tests?label=tests)](https://github.com/knutle/isoview/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/workflow/status/knutle/isoview/Fix%20PHP%20code%20style%20issues?label=code%20style)](https://github.com/knutle/isoview/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/knutle/isoview.svg?style=flat-square)](https://packagist.org/packages/knutle/isoview)


This package allows you to quickly and easily preview your package views in isolation during development.  
You simply define some test routes specifically for testing, then you can view your changes immediately.   
This works exactly the same as your normal `php artisan serve`, without requiring a full Laravel install or build step.  

## Installation

You can install the package via composer:

```bash
composer require --dev knutle/isoview
```

Then run the install command through the CLI:

```bash
./vendor/bin/isoview install
```

You should now see a new isoview.php file in ./routes at the root of your package.   
This is where you will define all your test routes.


## Usage

Once you have some routes ready, you can start the server using the CLI again:

```bash
./vendor/bin/isoview serve
```

This will serve your pages from [http://127.0.0.1:8010](http://127.0.0.1:8010).  
By default, an index page at `/` is provided that provides a list of links for all your available test routes.

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

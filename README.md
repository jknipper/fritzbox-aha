# fritzbox-aha

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-coveralls]][link-coveralls]
[![Total Downloads][ico-downloads]][link-downloads]

PHP implementation of the [AVM Home Automation HTTP Interface](https://avm.de/fileadmin/user_upload/Global/Service/Schnittstellen/AHA-HTTP-Interface.pdf).
* Currently only DECT heating controls are supported

## Install

Via Composer

``` bash
$ composer require jknipper/fritzbox-aha
```

## Usage

``` php
use \JanKnipper\FritzboxAHA\FritzboxAHA;
...
$aha = new FritzboxAHA("fritz.box", "user", "password");
```

See [example](example/example1.php)

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CONDUCT](CONDUCT.md) for details.

## Security

If you discover any security related issues, please email mail@jakni.de instead of using the issue tracker.

## Credits

- [Jan Knipper][link-author]
- [All Contributors][link-contributors]

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/jknipper/fritzbox-aha.svg?style=flat-square
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/jknipper/fritzbox-aha/master.svg?style=flat-square
[ico-coveralls]: https://img.shields.io/coveralls/jknipper/fritzbox-aha/master.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/jknipper/fritzbox-aha.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/jknipper/fritzbox-aha
[link-travis]: https://travis-ci.org/jknipper/fritzbox-aha
[link-coveralls]: https://coveralls.io/r/jknipper/fritzbox-aha?branch=master
[link-downloads]: https://packagist.org/packages/jknipper/fritzbox-aha
[link-author]: https://github.com/jknipper
[link-contributors]: ../../contributors


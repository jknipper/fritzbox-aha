# fritzbox-aha

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Build Status][ico-travis]][link-travis]
[![Coverage Status][ico-coveralls]][link-coveralls]
[![Total Downloads][ico-downloads]][link-downloads]

PHP implementation of the [AVM Home Automation HTTP Interface](https://avm.de/fileadmin/user_upload/Global/Service/Schnittstellen/AHA-HTTP-Interface.pdf).

Supported devices:

* Comet DECT/FRITZ!DECT 300 heating controls
* FRITZ!DECT 200 power switch
* FRITZ!DECT 210 power switch (not tested)

## Install

Via Composer

``` bash
$ composer require jknipper/fritzbox-aha
```

## Usage

``` php
use \sgoettsch\FritzboxAHA\FritzboxAHA;
$aha = new FritzboxAHA();
$aha->login("fritz.box", "user", "password");
```

See [example1](examples/example1.php) [example2](examples/example2.php)

## Change log

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) and [CODE OF CONDUCT](CODE_OF_CONDUCT.md) for details.

## Security

If you discover any security related issues, please email github@jakni.de instead of using the issue tracker.

## Credits

- [Jan Knipper][link-author]
- [All Contributors][link-contributors]

## Sources

https://avm.de/fileadmin/user_upload/Global/Service/Schnittstellen/AHA-HTTP-Interface.pdf
https://avm.de/fileadmin/user_upload/Global/Service/Schnittstellen/AVM_Technical_Note_-_Session_ID.pdf

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


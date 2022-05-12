# Change Log
All notable changes to this project are documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased](https://github.com/ravage84/SwissPaymentSlip/compare/0.12.2...master)
### Added

### Changed
- Add PHP 7.2 by name in Travis as PHP nightly seems to be PHP 7.3-dev
- Removed unfinished support for Red Payment Slip (ES)

### Fixed

## [0.12.2](https://github.com/ravage84/SwissPaymentSlip/releases/tag/0.12.2) - 2017-08-07
### Added
- Added a Stickler CI config file
- Added test coverage under PHP 7.0, 7.1 and nightly (7.2) on Travis CI
- Achieved compatibility with PHP 7.0, 7.1 and nightly (7.2)

### Changed
- Removed HHVM support
- Updated various dependencies incl. PHPUnit to 4.8.x and PHPCS to 2.9.x
- Improve Composer installation instructions #20

### Fixed
- Fix "A non-numeric value encountered" error #34
- Fix type error in test suite on PHP 7.0 and upwards #35

## [0.12.1](https://github.com/ravage84/SwissPaymentSlip/releases/tag/0.12.1) - 2016-02-26
### Fixed
- Fixed mistakenly right padded cents (Thanks to @FFDani)

## [0.12.0](https://github.com/ravage84/SwissPaymentSlip/releases/tag/0.12.0) - 2015-06-15
### Added
- "composer test" command executing phpunit
- "composer check-codestyle" command executing phpcs
- "composer fix-checkstyle" command executing phpcbf

### Changed
- Updated PHP dependency to 5.4 or higher
- Updated squizlabs/php_codesniffer development dependency to version ^2.1.0
- Updated phpunit/phpunit development dependency to version ^4.0.0
- Changed to short array syntax

### Fixed
- Let setNotForPayment() respect disabled data

## [0.11.1](https://github.com/ravage84/SwissPaymentSlip/releases/tag/0.11.1) - 2015-02-18
### Changed
- Exclude development/testing only related stuff from the Composer package

## [0.11.0](https://github.com/ravage84/SwissPaymentSlip/releases/tag/0.11.0) - 2015-02-18
### Added
- $displayBackground as settable/gettable property

## [0.10.0](https://github.com/ravage84/SwissPaymentSlip/releases/tag/0.10.0) - 2015-02-18
### Changed
- Removed the parameters from the getAllElements (API breaking)
  Added gettable/settable properties instead.
- Moved code line data & element to orange payment slip classes (API breaking)

### Fixed
- Removed breaking code from example
- Made getDisplayXXXX methods dependent on the related data setting(s)
  This fixes a newly introduced issue where calling the getAllElements method
  would throw an exception, if there was one or more disabled element data.

## [0.9.0](https://github.com/ravage84/SwissPaymentSlip/releases/tag/0.9.0) - 2015-02-13
### Added
- A base exception class for library related exceptions
- An exception class for when disabled data is accessed

### Changed
- An exception will be thrown when disabled data is requested instead of returning false
- An exception will be thrown when disabled data is tried to be set instead of simply not set it
- Various data setter methods are now public instead of protected as there was no actual reason
- Various minor improvements to the DocBlocks

## [0.8.0](https://github.com/ravage84/SwissPaymentSlip/releases/tag/0.8.0) - 2015-02-12
### Added
- A common class for payment slip (not their data containers) test cases
- Packagist Download & Latest badges to the README
- Testing with HHVM for Travis CI
- A .gitattributes
- Implemented a fluent interface

### Changed
- Split the red and orange slips into two sub classes each (for the slip and its data container)
- Throw exceptions when calling methods with invalid parameters instead of returning false (API breaking)
- Changed parameter count and order of getAllElements (API breaking)

### Fixed
- Removed misleading time key, which fooled Packagist
- Improved DocBlocks and documentation
- Vastly reworked and cleaned up the all tests

## [0.7.0](https://github.com/ravage84/SwissPaymentSlip/releases/tag/0.7.0) - 2015-02-06
### Added
- This change log
- .editorconfig file
- PHPUnit 3.7.38 as development dependency
- PHPMD 2.1.* as development dependency
- Scrutinizer CI integration & badges
- composer.lock (not ignored anymore)

### Changed
- Fully adopted the PSR2 Code Style
- Various CS and DocBlock improvements and other code clean up
- Renamed SwissPaymentSlip to PaymentSlip and SwissPaymentSlipData to PaymentSlipData
- Improved documentation
- Adopted the PSR-4 autoloader standard

### Fixed
- Some minor issues reported by Scrutinizer CI

## [0.6.0](https://github.com/ravage84/SwissPaymentSlip/releases/tag/0.6.0) - 2013-09-16
### Added
- "Not for payment" switch

## [0.5.0](https://github.com/ravage84/SwissPaymentSlip/releases/tag/0.5.0) - 2013-03-08
### Added
- Initial commit with README, LICENSE, composer.json, Travis CI integration, PHPUnit config, actual code, tests and some examples

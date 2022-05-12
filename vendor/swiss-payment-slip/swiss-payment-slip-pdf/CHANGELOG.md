# Change Log
All notable changes to this project are documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased](https://github.com/ravage84/SwissPaymentSlipPdf/compare/0.13.1...master)
### Added

### Changed

### Fixed

## [0.13.1](https://github.com/ravage84/SwissPaymentSlipPdf/releases/tag/0.13.1) - 2015-02-18
### Changed
- Updated the swiss-payment-slip/swiss-payment-slip dependency to version 0.11.1 (API compatible)
- Exclude development/testing only related stuff from the Composer package

## [0.13.0](https://github.com/ravage84/SwissPaymentSlipPdf/releases/tag/0.13.0) - 2015-02-18
### Added
- More tests

### Changed
- Moved the $paymentSlip parameter from the constructor to the createPaymentSlip method (API breaking)
  This way one can create a reusable PaymentSlipPdf object instead of recreating one
  each time he/she wants to create a payment slip as PDF.
  The object is still not fully non stateful but the state (the payment slip reference)
  gets unset after creating the payment slip as PDF.
- Improved the example by making a data dump while processing the payment slip to showcase the functionality

## [0.12.0](https://github.com/ravage84/SwissPaymentSlipPdf/releases/tag/0.12.0) - 2015-02-18
### Changed
- Updated the swiss-payment-slip/swiss-payment-slip dependency to version 0.11.0 (API breaking)
- Remove $withBackground parameter from createPaymentSlip method
  As it is now settable in the PaymentSlip class.

## [0.11.0](https://github.com/ravage84/SwissPaymentSlipPdf/releases/tag/0.11.0) - 2015-02-18
### Changed
- Updated the swiss-payment-slip/swiss-payment-slip dependency to version 0.10.0 (API breaking)
- Remove the $fillZeroes parameter in createPaymentSlip method (API breaking)

## [0.10.0](https://github.com/ravage84/SwissPaymentSlipPdf/releases/tag/0.10.0) - 2015-02-17
### Changed
- Updated the swiss-payment-slip/swiss-payment-slip dependency to version 0.9.0 (API compatible)
  This could lead to exceptions thrown, if you have disabled one or more element data
  but haven't disabled the display of them.

## [0.9.0](https://github.com/ravage84/SwissPaymentSlipPdf/releases/tag/0.9.0) - 2015-02-17
### Changed
- Updated the swiss-payment-slip/swiss-payment-slip dependency to version 0.8.0 (API breaking)
- Removed the $formatted parameter from PaymentSlipPdf::createPaymentSlip() (API breaking)
  This removes the possibility to decide whether the reference number of the orange payment slip gets formatted or not.
  This functionality must be reimplemented in PaymentSlip somehow.

## [0.8.0](https://github.com/ravage84/SwissPaymentSlipPdf/releases/tag/0.8.0) - 2015-02-17
### Changed
- Updated the swiss-payment-slip/swiss-payment-slip dependency to version 0.7.0 (API breaking)

## [0.7.1](https://github.com/ravage84/SwissPaymentSlipPdf/releases/tag/0.7.1) - 2015-02-17
### Changed
- Updated the swiss-payment-slip/swiss-payment-slip dependency to version 0.6.0 (API compatible)

## [0.7.0](https://github.com/ravage84/SwissPaymentSlipPdf/releases/tag/0.7.0) - 2015-02-17
### Added
- This change log
- .editorconfig file
- PHPUnit 3.7.38 as development dependency
- Scrutinizer CI integration & badges
- composer.lock (not ignored anymore)
- Testing with newer PHP versions and  HHVM in Travis CI
- A .gitattributes
- Packagist Download & Latest badges to the README
- PHPCS 2.1.* as development dependency
- Setup some tests

### Changed
- Set swiss-payment-slip/swiss-payment-slip dependency to version 0.5.0
- Renamed SwissPaymentSlipPdf to PaymentSlipPdf (API breaking)
- Fully adopted the PSR2 Code Style
- Various CS and DocBlock improvements and other code clean up
- Adopted the PSR-4 autoloader standard
- Use a Type Hint for SwissPaymentSlip in the constructor
- Throw an InvalidArgumentException when constructing a SwissPaymentSlipPdf object with invalid parameters
- Reduce complexity of writePaymentSlipLines(), throw InvalidArgumentExceptions
- Implemented/Defined a fluent interface

### Fixed
- Removed misleading time key, which fooled Packagist

## [0.6.0](https://github.com/ravage84/SwissPaymentSlipPdf/releases/tag/0.6.0) - 2013-03-13
### Added
- Added parameter $elementName to SwissPaymentSlipPdf::writePaymentSlipLines()

## [0.5.0](https://github.com/ravage84/SwissPaymentSlipPdf/releases/tag/0.5.0) - 2013-03-08
### Added
- Initial commit with README, LICENSE, composer.json, Travis CI integration, PHPUnit config and actual code

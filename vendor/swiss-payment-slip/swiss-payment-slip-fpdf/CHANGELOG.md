# Change Log
All notable changes to this project are documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased](https://github.com/ravage84/SwissPaymentSlipFpdf/compare/0.6.0...master)
### Added

### Changed

### Fixed

## [0.6.0](https://github.com/ravage84/SwissPaymentSlipFpdf/releases/tag/0.6.0) - 2013-04-01
### Added
- Scrutinizer CI integration & badges
- .editorconfig file
- PHPUnit 3.7.38 as development dependency
- Testing with newer PHP versions and HHVM through Travis CI
- A .gitattributes
- PHPCS 2.1.* as development dependency
- composer.lock (not ignored anymore)
- This change log
- Packagist Download & Latest badges to the README
- A bunch of incomplete tests

### Changed
- Updated the swiss-payment-slip/swiss-payment-slip-pdf dependency to version 0.13.* (API breaking)
- Adjusted the calls to the updated SwissPaymentSlipPdf class
- Added the PDFs generated through the examples to the ignore list
- Changed convertColor2Rgb method to protected instead of private
- Remove set minimum-stability ("dev"), get stable version of dependencies
- Set itbz/fpdf dependency to 1.7.*
- Fully adopted the PSR2 Code Style
- Renamed SwissPaymentSlipFpdf to PaymentSlipFpdf
- Adopted the PSR-4 autoloader standard
- Improved CS, DobBlocks and documentation
- Exclude development/testing only related stuff from the Composer package
- Implement a fluent interface

### Fixed
- Removed misleading time key, which fooled Packagist

## [0.5.0](https://github.com/ravage84/SwissPaymentSlipFpdf/releases/tag/0.5.0) - 2013-03-07
# Added
- Initial release including code, FPDF fonts, examples, a readme and a Travis configuration

SwissPaymentSlip - Swiss Payment Slips
======================================

[![Travis Build Status](https://travis-ci.org/ravage84/SwissPaymentSlip.png?branch=master)](https://travis-ci.org/ravage84/SwissPaymentSlip)
[![Scrutinizer CI Build Status](https://scrutinizer-ci.com/g/ravage84/SwissPaymentSlip/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ravage84/SwissPaymentSlip/build-status/master)
[![Scrutinizer CI Code Coverage](https://scrutinizer-ci.com/g/ravage84/SwissPaymentSlip/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ravage84/SwissPaymentSlip/?branch=master)
[![Scrutinizer CI Code Quality](https://scrutinizer-ci.com/g/ravage84/SwissPaymentSlip/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ravage84/SwissPaymentSlip/?branch=master)
[![Total Downloads](https://poser.pugx.org/swiss-payment-slip/swiss-payment-slip/d/total.png)](https://packagist.org/packages/swiss-payment-slip/swiss-payment-slip)
[![Latest Stable Version](https://poser.pugx.org/swiss-payment-slip/swiss-payment-slip/v/stable.png)](https://packagist.org/packages/swiss-payment-slip/swiss-payment-slip)

Do you need to create Swiss payment slips (called ES or ESR) as PDF files in a project of yours?
Then you found almost the right place, go to [SwissPaymentSlipFpdf](https://github.com/ravage84/SwissPaymentSlipFpdf)
or [SwissPaymentSlipTcpdf](https://github.com/ravage84/SwissPaymentSlipTcpdf).

If you need to include some basic logic for Swiss payment slips in your PHP code base then you are probably right though...

The partial and unfinished support for the red payment slip was dropped in version 0.13.0 due to lack of demand. 

How to use
----------

Just install the the package (see [Installation](https://github.com/ravage84/SwissPaymentSlip#installation)) and
check out the contained examples in the
[examples folder](https://github.com/ravage84/SwissPaymentSlip/tree/master/examples).

If you need help for extending and/or customizing the library
to your needs, just ask for help.

Installation
------------

### Requirements

- PHP 5.4.x+

### Composer

To add this library to your project through [Composer](http://getcomposer.org),
[install Composer](http://getcomposer.org/doc/00-intro.md#system-requirements) on your system first,
then [initialize a composer.json file](https://getcomposer.org/doc/03-cli.md#init)
and then execute the following from within your project's folder:

    $ php composer.phar require "swiss-payment-slip/swiss-payment-slip"

Background Story
----------------

In february 2013 I was looking for a solution to create swiss payment slips for a project I had to do at my work place.
After a short Google search I came across Manuel Reinhard's
[blog post](http://sprain.ch/blog/downloads/class-esr-besr-einzahlungsschein-php/) about the class he made for that.
On his [Github project's page](https://github.com/sprain/class.Einzahlungsschein.php) I found
[Peter Siska's](https://github.com/peschee) [pull request](https://github.com/sprain/class.Einzahlungsschein.php/pull/5).
His pull request introduced PSR-0 compatibility and he created a composer package on [Packagist](http://packagist.org/).
So I tried Peter's version and it suited my basic needs.

BUT since the customer I was working for used custom designed payment slips I couldn't use Manuel's/Peter's script
since it wasn't flexible enough.
Now I had to decide whether I want to "just" change the script to fit my needs or to rewrite it and
make it as flexible as possible.
I decided myself for the latter.

TODOs
-----

- Improve code documentation
- Add more examples/improve existing ones, showcase disabling the various field blocks better
- Improve the tests
- Release the stable release of the API

Submitting bugs and feature requests
------------------------------------

Bugs and feature request are tracked on [GitHub](https://github.com/ravage84/SwissPaymentSlip/issues).

Author
------

This project was created by [Marc Würth](https://github.com/ravage84).
See [Background Story](#background-story) for more details.

License
-------

SwissPaymentSlip is licensed under the MIT License.
See the [LICENSE](https://github.com/ravage84/SwissPaymentSlip/blob/master/LICENSE) file for details.

Thanks to
---------

- <http://www.smoke8.net/> for public designs of Einzahlungsscheinen

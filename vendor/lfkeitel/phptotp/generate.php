#!/usr/bin/php
<?php
/*
 * Usage: generate.php [key]
 *
 * If no key is provided as an arg, the script will ask for it.
 *
 */

include __DIR__.'/vendor/autoload.php';

use lfkeitel\phptotp\Totp;
use lfkeitel\phptotp\Base32;

$key = '';

if ($argc == 2) {
    $key = $argv[1];
} else {
    echo "Enter secret key: ";
    $key = trim(fgets(STDIN));

    if ($key == '') {
        echo "No key provided\n";
        exit(1);
    }
}

$key = Base32::decode($key);

echo "Token: " . (new Totp())->GenerateToken($key) . "\n";

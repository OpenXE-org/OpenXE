<?php

include __DIR__.'/../vendor/autoload.php';

use lfkeitel\phptotp\Hotp;
use lfkeitel\phptotp\Totp;

$key = '12345678901234567890';

// Test values are from RFC 4226
$hotp_tests = [
    '755224',
    '287082',
    '359152',
    '969429',
    '338314',
    '254676',
    '287922',
    '162583',
    '399871',
    '520489',
];

$hotp = new Hotp();

$hotp_failed = false;
foreach ($hotp_tests as $count => $token) {
    $t = $hotp->GenerateToken($key, $count);
    if ($t != $token) {
        echo "Count $count: Expected $token, got $t\n";
        $hotp_failed = true;
    }
}

if (!$hotp_failed) {
    echo "HOTP tests passed\n";
}

// Test data from RFC 6238
// time, sha1, sha256, sha512
$totp_tests = [
    [59, '94287082', '46119246', '90693936'],
    [1111111109, '07081804', '68084774', '25091201'],
    [1111111111, '14050471', '67062674', '99943326'],
    [1234567890, '89005924', '91819424', '93441116'],
    [2000000000, '69279037', '90698825', '38618901'],
    [20000000000, '65353130', '77737706', '47863826'],
];

$totp_length = 8;

$totp1 = new Totp();
$totp256 = new Totp('sha256');
$totp512 = new Totp('sha512');

$totp_failed = false;
foreach ($totp_tests as $test) {
    $sha1 = $totp1->GenerateToken($key, $test[0], $totp_length);
    if ($sha1 != $test[1]) {
        $totp_failed = true;
        echo "SHA1: Time {$test[0]}. Expected {$test[1]}, got $sha1\n";
    }

    $sha256 = $totp256->GenerateToken($key, $test[0], $totp_length);
    if ($sha256 != $test[2]) {
        $totp_failed = true;
        echo "SHA256: Time {$test[0]}. Expected {$test[2]}, got $sha256\n";
    }

    $sha512 = $totp512->GenerateToken($key, $test[0], $totp_length);
    if ($sha512 != $test[3]) {
        $totp_failed = true;
        echo "SHA512: Time {$test[0]}. Expected {$test[3]}, got $sha512\n";
    }
}

if (!$totp_failed) {
    echo "TOTP tests passed\n";
}

if ($hotp_failed || $totp_failed) {
    exit(1);
}

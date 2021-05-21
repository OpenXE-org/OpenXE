<?php

namespace Xentral\Components\Token;

use DateTime;
use Exception;
use lfkeitel\phptotp\Base32;
use lfkeitel\phptotp\Totp;
use Xentral\Components\Token\Exception\TotpTokenManagerException;

final class TOTPTokenManager
{
    /**
     * @param string   $inputToken
     * @param string   $secret
     * @param int|null $timestamp
     * @param int      $timeWindow
     *
     * @return bool
     */
    public function isTokenValid($inputToken, $secret, $timestamp = null, $timeWindow = 60)
    {
        if (is_null($timestamp)) {
            $timestamp = (new DateTime())->getTimestamp();
        }

        $start = $timestamp - ($timeWindow / 2);
        $end = $timestamp + ($timeWindow / 2);

        for ($now = $start; $now <= $end; $now += 30) {
            if ($inputToken === $this->generateToken($secret, $now)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param string   $secret
     * @param int|null $timestamp
     *
     * @throws TotpTokenManagerException
     *
     * @return string
     */
    public function generateToken($secret, $timestamp = null)
    {
        $totp = new Totp();

        try {
            $token = $totp->GenerateToken(Base32::decode($secret), $timestamp);
        } catch (Exception $e) {
            throw new TotpTokenManagerException($e->getMessage(), $e->getCode(), $e);
        }

        return $token;
    }

    /**
     * @param int $length
     *
     * @throws TotpTokenManagerException
     *
     * @return string
     */
    public function generateBase32Secret($length = 16)
    {
        if (!function_exists('openssl_random_pseudo_bytes')) {
            throw new TotpTokenManagerException('Can not generate secret. OpenSSL PHP extension is missing.');
        }

        try {
            $secretBytes = Totp::GenerateSecret($length); // @todo Eventuell StringUtil::random verwenden
            return Base32::encode($secretBytes);
        } catch (Exception $e) {
            throw new TotpTokenManagerException($e->getMessage(), $e->getCode(), $e);
        }
    }
}

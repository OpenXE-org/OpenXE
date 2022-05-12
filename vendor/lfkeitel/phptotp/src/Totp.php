<?php

namespace lfkeitel\phptotp;

class Totp extends Hotp
{
    private $startTime;
    private $timeInterval;

    public function __construct($algo = 'sha1', $start = 0, $ti = 30)
    {
        parent::__construct($algo);
        $this->startTime = $start;
        $this->timeInterval = $ti;
    }

    public function GenerateToken($key, $time = null, $length = 6)
    {
        // Pad the key if necessary
        if ($this->algo === 'sha256') {
            $key = $key . substr($key, 0, 12);
        } elseif ($this->algo === 'sha512') {
            $key = $key . $key . $key . substr($key, 0, 4);
        }

        // Get the current unix timestamp if one isn't given
        if (is_null($time)) {
            $time = (new \DateTime())->getTimestamp();
        }

        // Calculate the count
        $now = $time - $this->startTime;
        $count = floor($now / $this->timeInterval);

        // Generate a normal HOTP token
        return parent::GenerateToken($key, $count, $length);
    }
}

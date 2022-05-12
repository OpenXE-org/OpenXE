<?php

namespace FiskalyClient\errors\exceptions;

require_once('FiskalyException.php');

class FiskalyHttpTimeoutException extends FiskalyException
{
    public function __construct($message)
    {
        parent::__construct($message);
    }
}

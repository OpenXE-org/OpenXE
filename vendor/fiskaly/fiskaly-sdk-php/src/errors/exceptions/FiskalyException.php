<?php

namespace FiskalyClient\errors\exceptions;

use Exception;

class FiskalyException extends Exception
{
    public function __construct($message = '')
    {
        parent::__construct($message);
    }
}

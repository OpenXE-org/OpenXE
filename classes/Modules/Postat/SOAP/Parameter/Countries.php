<?php

namespace Xentral\Modules\Postat\SOAP\Parameter;

use Xentral\Modules\Postat\SOAP\ParameterInterface;
use Xentral\Modules\Postat\SOAP\PostAtException;

class Countries implements ParameterInterface
{
    private $countries;

    public function __construct(array $countries)
    {
        if (!is_array($countries)) {
            throw new PostAtException('The given target countries are invalid.');
        }

        $this->countries = $countries;
    }

    public function getData(): array
    {
        return $this->countries;
    }
}

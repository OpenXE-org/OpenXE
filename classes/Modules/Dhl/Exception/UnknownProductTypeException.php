<?php

namespace Xentral\Modules\Dhl\Exception;

use Throwable;

class UnknownProductTypeException extends DhlBaseException
{

    static public function fromValid($validValues){
        return new UnknownProductTypeException("ProductType muss eines aus folgenden sein: {$validValues}");
    }
    static public function invalidDescription(){
        return new UnknownProductTypeException('Produktbeschreibung muss gegeben sein');
    }
}

<?php

declare(strict_types=1);

namespace Xentral\Modules\Postat\SOAP;

/**
 * Each API method call should be represented by a class that implements this interface.
 *
 * The name of the class must be identical with the name of the API method found
 * in the Post.at SOAP API documentation.
 *
 * See the existing API method implementations in the Postat/SOAP/Method/ directory.
 */
interface MethodInterface
{
    /**
     * MethodInterface constructor.
     *
     * @param PostAtClient $client
     */
    public function __construct(PostAtClient $client);

    /**
     * @param ParameterInterface $data Data needed for the body of the SOAP call.
     */
    public function call(ParameterInterface $data);
}

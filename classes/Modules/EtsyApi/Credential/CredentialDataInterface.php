<?php

namespace Xentral\Modules\EtsyApi\Credential;

interface CredentialDataInterface
{
    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return string
     */
    public function getSecret();
}

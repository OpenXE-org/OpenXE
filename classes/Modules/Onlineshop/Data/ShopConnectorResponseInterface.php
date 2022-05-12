<?php

declare(strict_types=1);

namespace Xentral\Modules\Onlineshop\Data;

interface ShopConnectorResponseInterface
{
    /**
     * @return bool
     */
    public function isSuccessful(): bool;

    /**
     * @return string
     */
    public function getMessage(): string;
}

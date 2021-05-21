<?php

declare(strict_types=1);

namespace Xentral\Modules\Onlineshop\Data;

class ShopConnectorOrderStatusUpdateResponse implements ShopConnectorResponseInterface
{
    /** @var bool $success */
    protected $success;
    /** @var string $message */
    protected $message;

    public function  __construct(bool $success, string $message)
    {
        $this->success = $success;
        $this->message = $message;
    }

    /**
     * @return bool
     */
    public function isSuccessful(): bool
    {
        return $this->success;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
   }
}

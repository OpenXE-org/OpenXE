<?php

declare(strict_types=1);

namespace Xentral\Modules\Office365Api\Wrapper;

use erpAPI;

final class CompanyConfigWrapper
{
    /** @var erpAPI */
    private $erpApi;

    public function __construct(erpAPI $erpApi)
    {
        $this->erpApi = $erpApi;
    }

    public function get(string $key): ?string
    {
        try {
            $value = $this->erpApi->GetKonfiguration($key);
            return $value ?: null;
        } catch (\Exception $e) {
            return null;
        }
    }

    public function set(string $key, string $value): void
    {
        try {
            $this->erpApi->SetKonfigurationValue($key, $value);
        } catch (\Exception $e) {
            // Silently fail - config might not be available in all contexts
        }
    }

    public function delete(string $key): void
    {
        try {
            $this->erpApi->SetKonfigurationValue($key, '');
        } catch (\Exception $e) {
            // Silently fail
        }
    }
}

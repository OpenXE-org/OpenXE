<?php

namespace Xentral\Modules\Api\LegacyBridge;

class LegacyApiLazyProxy
{
    /** @var \Api $realLegacyApi */
    private $realLegacyApi;

    /** @var bool $isInitialized */
    private $isInitialized = false;

    /**
     * Magischer Aufruf für Methoden
     *
     * @param string $action
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($action, $arguments)
    {
        if ($this->isInitialized === false) {
            $this->lazyLoad();
        }

        return call_user_func_array(array($this->realLegacyApi, $action), $arguments);
    }

    /**
     * Magischer Getter für Eigenschaften
     *
     * @param string $property
     *
     * @return mixed|null
     */
    public function __get($property)
    {
        if ($this->isInitialized === false) {
            $this->lazyLoad();
        }

        if (property_exists($this->realLegacyApi, $property)) {
            return $this->realLegacyApi->{$property};
        }

        return null;
    }

    /**
     * Legacy-API nachladen
     */
    private function lazyLoad()
    {
        $app = new LegacyApplication();

        $apiobj = $app->erp->LoadModul('api');
        $apiobj->app = $app;

        if (!$apiobj instanceof \Api) {
            throw new \RuntimeException('Legacy-API could not be loaded');
        }

        $this->realLegacyApi = $apiobj;
        $this->isInitialized = true;
    }
}

<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Provider;

interface SearchIndexProviderInterface
{
    /**
     * @return string Nur Kleinbuchstaben erlaubt
     */
    public function getIndexName();

    /**
     * @return string
     */
    public function getIndexTitle();

    /**
     * @return string|null
     */
    public function getModuleName();
}

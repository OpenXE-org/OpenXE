<?php

namespace Xentral\Modules\SuperSearch\SearchIndex\Data;

final class IndexItem
{
    /** @var IndexIdentifier $identifier */
    public $identifier;

    /** @var IndexData $data */
    public $data;

    /** @var string|null $module */
    public $module;

    /**
     * @param IndexIdentifier $identifier
     * @param IndexData       $data
     * @param string|null            $moduleName
     */
    public function __construct(IndexIdentifier $identifier, IndexData $data, $moduleName = null)
    {
        $this->identifier = $identifier;
        $this->data = $data;
        $this->module = $moduleName;
    }
}

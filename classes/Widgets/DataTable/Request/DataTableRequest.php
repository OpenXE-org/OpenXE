<?php

namespace Xentral\Widgets\DataTable\Request;

use Xentral\Components\Http\Request;
use Xentral\Widgets\DataTable\Exception\InvalidArgumentException;

final class DataTableRequest
{
    /** @var Request $request */
    private $request;

    /** @var DataTableRequestParameter $params */
    private $params;

    /**
     * @param Request                   $request
     * @param DataTableRequestParameter $parameter
     */
    public function __construct(Request $request, DataTableRequestParameter $parameter)
    {
        $method = $request->getMethod();
        if (!in_array($method, ['GET', 'POST'], true)) {
            throw new InvalidArgumentException(sprintf(
                'Can not create DataTableRequest instance. HTTP method "%s" is invalid.', $method
            ));
        }

        $this->request = $request;
        $this->params = $parameter;
    }

    /**
     * @param Request $request
     *
     * @return self
     */
    public static function fromRequest(Request $request)
    {
        $parameters = DataTableRequestParameter::fromRequest($request);

        return new self($request, $parameters);
    }

    /**
     * @return bool
     */
    public function isDataRequest()
    {
        if (!$this->isValidDataTableRequest()) {
            return false;
        }
        if (!$this->isAjax()) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    private function isValidDataTableRequest()
    {
        if (empty($this->getParams()->getTableName())) {
            return false;
        }
        if ($this->params->getDraw() < 1) {
            return false;
        }
        if (empty($this->params->getColumnsValues()) ||
            empty($this->params->getOrderValues()) ||
            empty($this->params->getSearchValues())) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function isAjax()
    {
        return $this->request->isAjax();
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->request->getMethod();
    }

    /**
     * @return DataTableRequestParameter
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @return Request
     */
    public function getOriginalRequest()
    {
        return $this->request;
    }

    /**
     * @return bool
     */
    public function isExportRequest()
    {
        if (!$this->isValidDataTableRequest()) {
            return false;
        }
        if ($this->isAjax()) {
            return false;
        }
        if (empty($this->params->getExportValues())) {
            return false;
        }

        return true;
    }
}

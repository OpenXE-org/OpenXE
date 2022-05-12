<?php

namespace Xentral\Modules\Api\Resource\Result;

abstract class AbstractResult
{
    const RESULT_TYPE_ITEM = 'item';
    const RESULT_TYPE_COLLECTION = 'collection';

    /** @var string $type */
    protected $type;

    /** @var array $data */
    protected $data;

    /** @var array $pagination */
    protected $pagination;

    /** @var bool $success Als Kennzeichen ob Anlegen oder Bearbeiten erfolgreich war */
    protected $success;

    /**
     * @param array $collection
     * @param array $pagination
     */
    abstract public function __construct(array $collection, array $pagination = null);

    /**
     * Ergebnis als Array zurückgeben
     *
     * @return array
     */
    public function getResult()
    {
        $result = [];

        // Success-Flag ganz oben anzeigen
        if ($this->success !== null) {
            $result['success'] = $this->success;
        }

        $result['data'] = $this->getData();

        // Paginierung als letztes anzeigen
        if ($this->pagination !== null) {
            $result['pagination'] = $this->pagination;
        }

        return $result;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return array
     */
    public function getPagination()
    {
        return $this->pagination;
    }

    /**
     * @return string [item|collection]
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param bool $success
     */
    public function setSuccess($success)
    {
        $this->success = (bool)$success;
    }

    /**
     * @return bool
     */
    public function isItem()
    {
        return $this->type === self::RESULT_TYPE_ITEM;
    }

    /**
     * @return bool
     */
    public function isCollection()
    {
        return $this->type === self::RESULT_TYPE_COLLECTION;
    }
}

<?php

namespace Xentral\Modules\DocuvitaSolutionsApi\Data;


class DocuvitaSolutionsTemplateData
{


    /** @var array */
    private $queryResult;

    /** @var string */
    private $template;

    /** @var string */
    private $refType;

    /**
     * @return string
     */
    public function getRefType()
    {
        return $this->refType;
    }

    /**
     * DocuvitaSolutionsTemplateData constructor.
     *
     * @param string $template
     * @param array  $ids
     * @param string $refType
     */
    public function __construct($template, $ids, $refType)
    {
        $this->queryResult = $ids;
        $this->template = $template;
        $this->refType = $refType;
    }

    public function getQueryResult()
    {
        return $this->queryResult;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }


}
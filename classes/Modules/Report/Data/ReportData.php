<?php

namespace Xentral\Modules\Report\Data;

use JsonSerializable;
use Xentral\Modules\Report\Exception\FormDataException;

class ReportData implements JsonSerializable
{
    /**@var string $name */
    private $name;

    /** @var string $description */
    private $description;

    /** @var int $projectId */
    private $projectId;

    /**@var string $sqlQuery */
    private $sqlQuery;

    /**@var ReportColumnCollection|null $columns */
    private $columns;

    /** @var ReportParameterCollection|null $parameters */
    private $parameters;

    /** @var int $id */
    private $id;

    /** @var string $remark */
    private $remark;

    /** @var string $category */
    private $category;

    /** @var bool $readonly */
    private $readonly;

    /** @var bool $isFavorite */
    private $isFavorite;

    /** @var string $csvDelimiter */
    private $csvDelimiter;

    /** @var string $csvEnclosure */
    private $csvEnclosure;

    /**
     * @param string                    $name
     * @param string                    $description
     * @param int                       $projectId
     * @param string                    $sqlQuery
     * @param ReportColumnCollection    $columns
     * @param ReportParameterCollection $parameters
     * @param int                       $id
     * @param string                    $remark
     * @param string                    $category
     * @param bool                      $readonly
     * @param null                      $csvDelimiter
     * @param null                      $csvEnclosure
     */
    public function __construct(
        $name,
        $description = '',
        $projectId = 0,
        $sqlQuery = '',
        $columns = null,
        $parameters = null,
        $id = 0,
        $remark = '',
        $category = '',
        $readonly = false,
        $csvDelimiter = ',',
        $csvEnclosure = ''
    ) {
        $this->name = $name;
        $this->description = $description;
        $this->projectId = $projectId;
        $this->sqlQuery = $sqlQuery;
        $this->columns = $columns;
        $this->parameters = $parameters;
        $this->id = $id;
        $this->remark = $remark;
        $this->category = $category;
        $this->readonly = $readonly;
        $this->csvDelimiter = $csvDelimiter;
        $this->csvEnclosure = $csvEnclosure;
    }

    /**
     * @param array $formData
     *
     * @return ReportData
     */
    public static function fromFormData($formData)
    {
        if (!isset($formData['name'])) {
            throw new FormDataException('name is required');
        }
        $name = $formData['name'];
        $category = '';
        if (isset($formData['category'])) {
            $category = $formData['category'];
        }
        $description = '';
        if (isset($formData['description'])) {
            $description = $formData['description'];
        }
        $project = 0;
        if (isset($formData['project'])) {
            $project = (int)$formData['project'];
        }
        $query = '';
        if (isset($formData['sql_query'])) {
            $query = $formData['sql_query'];
        }
        $id = 0;
        if (isset($formData['id'])) {
            $id = $formData['id'];
        }
        $remarks = '';
        if (isset($formData['remark'])) {
            $remarks = $formData['remark'];
        }
        $readonly = false;
        if (isset($formData['readonly']) && $formData['readonly'] === 1) {
            $readonly = true;
        }
        $delimiter = ',';
        if (isset($formData['csv_delimiter'])) {
            $delimiter = $formData['csv_delimiter'];
        }
        $quote = '';
        if (isset($formData['csv_enclosure'])) {
            $quote = $formData['csv_enclosure'];
        }
        $instance = new self(
            $name,
            $description,
            $project,
            $query,
            null,
            null,
            $id,
            $remarks,
            $category,
            $readonly,
            $delimiter,
            $quote
        );
        if (isset($formData['is_favorite']) && $formData['is_favorite'] === 1) {
            $instance->isFavorite = true;
        }

        return $instance;
    }

    /**
     * @return array
     */
    public function getFormData()
    {
        $data = [
            'name'          => $this->getName(),
            'description'   => $this->getDescription(),
            'project'       => $this->getProjectId(),
            'sql_query'     => $this->getSqlQuery(),
            'id'            => $this->getId(),
            'remark'        => $this->getRemark(),
            'category'      => $this->getCategory(),
            'csv_delimiter' => $this->getCsvDelimiter(),
            'csv_enclosure'     => $this->getCsvEnclosure(),
        ];
        if ($this->isFavorite === true) {
            $data['is_favorite'] = 1;
        }

        return $data;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return int
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @return string
     */
    public function getSqlQuery()
    {
        return $this->sqlQuery;
    }

    /**
     * @return ReportColumnCollection|null
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @return ReportParameterCollection|null
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * @param bool $readonly
     *
     * @return void
     */
    public function setReadonly($readonly)
    {
        $this->readonly = $readonly;
    }

    /**
     * @return bool
     */
    public function isReadonly()
    {
        return $this->readonly;
    }

    /**
     * @return bool
     */
    public function isFavorite()
    {
        return $this->isFavorite;
    }

    /**
     * @return string
     */
    public function getCsvDelimiter()
    {
        return $this->csvDelimiter;
    }

    /**
     * @return string
     */
    public function getCsvEnclosure()
    {
        return $this->csvEnclosure;
    }

    /**
     * @param bool $isFavorite
     */
    public function setIsFavorite($isFavorite)
    {
        $this->isFavorite = $isFavorite;
    }

    /**
     * @param ReportColumnCollection $columns
     *
     * @return ReportData
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;

        return $this;
    }

    /**
     * @param ReportParameterCollection $parameters
     *
     * @return ReportData
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * Specify data which should be serialized to JSON
     *
     * @return mixed data which can be serialized by <b>json_encode</b>,
     */
    public function jsonSerialize()
    {
        $data = [
            'name'          => $this->getName(),
            'description'   => $this->getDescription(),
            'project'       => $this->getProjectId(),
            'sql_query'     => $this->getSqlQuery(),
            'columns'       => $this->getColumns(),
            'parameters'    => $this->getParameters(),
            'remark'        => $this->getRemark(),
            'category'      => $this->getCategory(),
            'csv_delimiter' => $this->getCsvDelimiter(),
            'csv_enclosure' => $this->getCsvEnclosure(),
        ];
        if ($this->isReadonly()) {
            $data['readonly'] = true;
        }

        return $data;
    }
}

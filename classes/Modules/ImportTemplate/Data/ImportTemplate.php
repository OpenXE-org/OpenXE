<?php

namespace Xentral\Modules\ImportTemplate\Data;

use JsonSerializable;
use Xentral\Modules\ImportTemplate\Exception\InvalidTemplateDataException;

class ImportTemplate implements JsonSerializable
{
    /**@var string $id */
    private $id;

    /**@var string $label */
    private $label = '';

    /**@var string $delimiter */
    private $delimiter='';

    /**@var int $lineNumber */
    private $lineNumber=0;

    /**@var string $masking */
    private $masking='';

    /**@var string $importCharSet */
    private $importCharSet='';

    /**@var string $fields */
    private $fields='';

    /**@var string $target */
    private $target='';

    /**@var string $internalNote */
    private $internalNote='';

    /**@var int $utf8decode */
    private $utf8decode=1;

    /**@var string $charset */
    private $charset='';

    /**
     * @param array $data
     *
     * @throws InvalidTemplateDataException
     *
     * @return ImportTemplate
     */
    public static function fromArray($data)
    {
        $isValid = self::validate($data);
        if (!$isValid) {
            throw new InvalidTemplateDataException();
        }

        return self::fromDbState($data);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    private static function validate($data)
    {
        $isValid = true;

        if (isset($data['fields'])) {
            $regex = '/[\r\n0-9]+:/';
            preg_match_all($regex, $data['fields'], $matches);
            if (count($matches[0]) == 0) {
                $isValid = false;
            }
        } else {
            $isValid = false;
        }

        if (isset($data['label'])) {
            if (strlen($data['label']) == 0) {
                $isValid = false;
            }
        } else {
            $isValid = false;
        }

        return $isValid;
    }

    public static function fromDbState($data)
    {
        $instance = new self();

        if (isset($data['id'])) {
            $instance->id = $data['id'];
        }

        if (isset($data['label'])) {
            $instance->label = $data['label'];
        }

        if (isset($data['delimiter'])) {
            $instance->delimiter = $data['delimiter'];
        }

        if (isset($data['lineNumber'])) {
            $instance->lineNumber = $data['lineNumber'];
        }

        if (isset($data['masking'])) {
            $instance->masking = $data['masking'];
        }

        if (isset($data['importCharSet'])) {
            $instance->importCharSet = $data['importCharSet'];
        }

        if (isset($data['fields'])) {
            $instance->fields = $data['fields'];
        }

        if (isset($data['target'])) {
            $instance->target = $data['target'];
        }

        if (isset($data['internalNote'])) {
            $instance->internalNote = $data['internalNote'];
        }

        if (isset($data['utf8decode'])) {
            $instance->utf8decode = $data['utf8decode'] == 1;
        }

        if (isset($data['charset'])) {
            $instance->charset = $data['charset'];
        }

        return $instance;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function getDelimiter()
    {
        return $this->delimiter;
    }

    public function getLineNumber()
    {
        return $this->lineNumber;
    }

    public function getMasking()
    {
        return $this->masking;
    }

    public function getImportCharSet()
    {
        return $this->importCharSet;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getTarget()
    {
        return $this->target;
    }

    public function getInternalNote()
    {
        return $this->internalNote;
    }

    public function getUtf8decode()
    {
        return $this->utf8decode;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function updateLabel($label)
    {
        $this->label = $label;
    }

    public function jsonSerialize()
    {

        return [
            'id'            => $this->id,
            'label'         => $this->label,
            'delimiter'     => $this->delimiter,
            'lineNumber'    => $this->lineNumber,
            'masking'       => $this->masking,
            'importCharSet' => $this->importCharSet,
            'fields'        => $this->fields,
            'target'        => $this->target,
            'internalNote'  => $this->internalNote,
            'utf8decode'    => $this->utf8decode,
            'charset'       => $this->charset,
        ];
    }
}

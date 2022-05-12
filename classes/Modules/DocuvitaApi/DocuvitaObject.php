<?php

namespace Xentral\Modules\DocuvitaApi;

use Xentral\Modules\DocuvitaApi\Exception\UnsupportedFileTypeException;

/**
 * @property string       OBJ_MAILTO
 * @property string       OBJ_VOUCHERDATE
 * @property string       OBJ_VOUCHERNUMBER
 * @property string       OBJ_PARENTOBJECT
 * @property bool|string  OBJ_NAME
 * @property false|string OBJ_DATECREATED
 * @property int          OBJ_OBJECTTYPE
 */
class DocuvitaObject
{
    /** @var array PROPERTYTYPES */
    const PROPERTYTYPES = [
        'S' => 'FieldValueString',
        'N' => 'FieldValueNumeric',
        'D' => 'FieldValueDateTime',
    ];

    /** @var mixed $originalObject */
    private $originalObject;

    /**
     * @param array  $originalObject
     * @param string $sessionGUID
     */
    public function __construct($originalObject, $sessionGUID)
    {
        $this->originalObject = $originalObject;
        $this->originalObject->SessionGuid = $sessionGUID;
        $this->setVersionOriginalFilename('');
    }

    /**
     * @param string $propertyName
     * @param string $propertyValue
     *
     * @throws UnsupportedFileTypeException
     *
     * @return void
     */
    public function __set($propertyName, $propertyValue)
    {
        foreach ($this->originalObject->ObjectPropertyList as $property) {
            if ($property->FieldName === $propertyName) {
                $propertyValueFieldName = self::PROPERTYTYPES[$property->FieldType];
                if (!isset($propertyValueFieldName)) {
                    throw new UnsupportedFileTypeException(sprintf(
                        'Field type "%s" not supported',
                        $property->FieldType
                    ));
                }
                $property->$propertyValueFieldName = $propertyValue;

                return;
            }
        }
    }

    /**
     * @param $propertyName
     *
     * @throws UnsupportedFileTypeException
     *
     * @return string|null
     */
    public function __get($propertyName)
    {
        foreach ($this->originalObject->ObjectPropertyList as $property) {
            if ($property->FieldName === $propertyName) {
                $propertyValueFieldName = self::PROPERTYTYPES[$property->FieldType];
                if (!isset($propertyValueFieldName)) {
                    throw new UnsupportedFileTypeException(sprintf(
                        'Field type "%s" not supported',
                        $property->FieldType
                    ));
                }

                return $property->$propertyValueFieldName;
            }
        }

        return null;
    }

    /**
     * @param string $propertyName
     *
     * @return bool
     */
    public function __isset($propertyName)
    {
        return $this->$propertyName !== null;
    }

    /**
     * @return string
     */
    public function getDocUploadGuid()
    {
        return $this->originalObject->DocUploadGuid;
    }

    /**
     * @param string $sessionGuid
     *
     * @return void
     */
    public function setSessionGuid($sessionGuid)
    {
        $this->originalObject->SessionGuid = $sessionGuid;
    }

    /**
     * @param string $fileName
     *
     * @return void
     */
    public function setVersionOriginalFilename($fileName)
    {
        $this->originalObject->VersionOriginalFilename = $fileName;
    }

    /**
     * @returns string
     */
    public function getVersionOriginalFilename()
    {
        return $this->originalObject->VersionOriginalFilename;
    }

    /**
     * @return false|string
     */
    public function toJSON()
    {
        return json_encode($this->originalObject);
    }
}

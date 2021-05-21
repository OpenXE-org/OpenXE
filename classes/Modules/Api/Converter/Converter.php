<?php

namespace Xentral\Modules\Api\Converter;

class Converter
{
    const CONVERTER_TYPE_JSON = 'json';
    const CONVERTER_TYPE_XML = 'xml';

    /**
     * @var array
     */
    protected static $validTypes = array(
        self::CONVERTER_TYPE_JSON,
        self::CONVERTER_TYPE_XML
    );

    /** @var XmlConverter $xml */
    protected $xml;

    /** @var JsonConverter $json */
    protected $json;

    /**
     * @param XmlConverter  $xml
     * @param JsonConverter $json
     */
    public function __construct(XmlConverter $xml, JsonConverter $json)
    {
        $this->xml = $xml;
        $this->json = $json;
    }

    /**
     * @param array $array
     *
     * @return string
     */
    public function arrayToJson(array $array)
    {
        return $this->json->fromArray($array);
    }

    /**
     * @param $jsonString
     *
     * @return array
     */
    public function jsonToArray($jsonString)
    {
        return $this->json->toArray($jsonString);
    }

    /**
     * @param array  $array
     * @param string $rootNode
     *
     * @return string
     */
    public function arrayToXml(array $array, $rootNode = 'xml')
    {
        return $this->xml->convertArrayToXmlString($array, $rootNode);
    }

    /**
     * @param string $xmlString
     * @param bool   $wrap
     *
     * @return array
     */
    public function xmlToArray($xmlString, $wrap = false)
    {
        return $this->xml->convertXmlStringToArray($xmlString, $wrap);
    }

    /**
     * @param string $type
     * @param array  $data
     *
     * @return string
     */
    public function arrayTo($type, array $data)
    {
        $type = strtolower($type);

        if (!in_array($type, $this->getSupportedTypes(), true)) {
            throw new \RuntimeException(sprintf(
                'Converter type "%s" is not supported.', $type
            ));
        }

        return $this->{$type}->fromArray($data);
    }

    /**
     * @param string $type
     * @param string $content
     *
     * @return array
     */
    public function toArray($type, $content)
    {
        $type = strtolower($type);

        if (!in_array($type, $this->getSupportedTypes(), true)) {
            throw new \RuntimeException(sprintf(
                'Converter type "%s" is not supported.', $type
            ));
        }

        return $this->{$type}->toArray($content);
    }

    /**
     * @return array
     */
    public function getSupportedTypes()
    {
        return self::$validTypes;
    }
}

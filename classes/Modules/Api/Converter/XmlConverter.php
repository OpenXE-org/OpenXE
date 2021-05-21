<?php

namespace Xentral\Modules\Api\Converter;

use Xentral\Modules\Api\Converter\Exception\ConvertionException;

/**
 * @todo Tests
 */
class XmlConverter implements ConverterInterface
{
    /**
     * @param array  $array
     * @param string $rootNode
     *
     * @return string
     */
    public function fromArray($array, $rootNode = 'xml')
    {
        return $this->convertArrayToXmlString($array, $rootNode);
    }

    /**
     * @param string $data
     * @param bool   $wrap
     *
     * @return array
     */
    public function toArray($data, $wrap = false)
    {
        return $this->convertXmlStringToArray($data, $wrap);
    }

    /**
     * Kovertiert einen XML-String in ein Array
     *
     * @param string $xml
     * @param bool   $wrap
     *
     * @return array
     *
     * @throws \RuntimeException
     */
    public function convertXmlStringToArray($xml, $wrap = false)
    {
        if ($wrap) {
            $xml = "<data>{$xml}</data>";
        }

        $simplexml = simplexml_load_string($xml, null, LIBXML_NOCDATA);
        if ($simplexml === false) {
            throw new ConvertionException('XML could not be decoded.');
        }
        $array = $this->convertSimpleXmlToArray($simplexml);

        return $array;
    }

    /**
     * Wandelt ein SimpleXml-Objekt in ein Array
     *
     * @param \SimpleXMLElement $object
     *
     * @return array|string
     */
    public function convertSimpleXmlToArray($object)
    {
        $array = (array)$object;
        if (empty($array)) {
            return '';
        }

        foreach ($array as $key => $value) {
            if (is_object($value) || is_array($value)) {
                $array[$key] = $this->convertSimpleXmlToArray($value);
            }
        }

        return $array;
    }

    /**
     * @param array  $array
     * @param string $rootNode Name des Root-Elements
     *
     * @return \SimpleXMLElement
     */
    public function convertArrayToSimpleXml($array, $rootNode = 'xml')
    {
        $xml = new \SimpleXMLElement(
            sprintf('<?xml version="1.0" encoding="UTF-8"?><%s></%s>', $rootNode, $rootNode)
        );

        $this->arrayToXmlHelper($xml, $array);

        return $xml;
    }

    /**
     * @param array  $array
     * @param string $rootNode
     *
     * @return string
     */
    public function convertArrayToXmlString($array, $rootNode = 'xml')
    {
        $simpleXml = $this->convertArrayToSimpleXml($array, $rootNode);

        return $simpleXml->asXML();
    }

    /**
     * @see convertArrayToSimpleXml
     *
     * @param \SimpleXMLElement $xmlObj
     * @param array             $array
     */
    protected function arrayToXmlHelper(&$xmlObj, $array)
    {
        foreach ($array as $key => $value) {
            // Wenn kein Knotenname ermittelt werden konnte > den Knoten 'item' nennen
            $subNodeName = is_int($key) ? 'item' : $key;

            if (is_array($value)) {
                $subNode = $xmlObj->addChild((string)$subNodeName);
                $this->arrayToXmlHelper($subNode, $value);
            } else {
                $xmlObj->addChild((string)$subNodeName, htmlspecialchars($value, ENT_QUOTES));
            }
        }
    }

}

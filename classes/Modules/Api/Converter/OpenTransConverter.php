<?php


namespace Xentral\Modules\Api\Converter;

use SimpleXMLElement;
use Xentral\Modules\Api\Converter\Exception\ConvertionException;

class OpenTransConverter implements ConverterInterface
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
     *
     * @return array
     */
    public function toArray($data)
    {
        return $this->convertXmlStringToArray($data);
    }

    /**
     * @param array  $array
     * @param string $rootNode
     *
     * @return string
     */
    public function arrayToXml(array $array, $rootNode = 'xml')
    {
        return $this->convertArrayToXmlString($array, $rootNode);
    }

    /**
     * @param string $xml
     *
     * @return SimpleXMLElement
     */
    public function getXmlFromString($xml)
    {
        return simplexml_load_string($xml, null, LIBXML_NOCDATA);
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
    public function convertXmlStringToArray($xml)
    {
        $namespaces = [];
        $simplexml = simplexml_load_string($xml, null, LIBXML_NOCDATA);
        if(is_object($simplexml)) {
            $namespaces = $simplexml->getNamespaces();
        }
        if ($simplexml === false) {
            throw new ConvertionException('XML could not be decoded.');
        }

        return $this->convertSimpleXmlToArray($simplexml, $namespaces);
    }

    /**
     * @param array|SimpleXMLElement $attributes
     *
     * @return string
     */
    protected function attributeKey($attributes) {
        $ret = '';
        if(empty($attributes)) {
            return $ret;
        }
        foreach($attributes as $key => $attribute) {
            if((is_array($attribute) || is_object($attribute)) && count($attribute) === 1) {
                $ret .= ' '.$key.'="'.reset($attribute).'"';
                continue;
            }
            $ret .= ' '.$key.'="'.$attribute.'"';
        }
        return $ret;
    }

    /**
     * @param SimpleXMLElement $object
     * @param array|null       $namespaces
     *
     * @return array|string
     */
    public function convertSimpleXmlToArray($object, $namespaces)
    {
        $array = [];
        $isObject = is_object($object);
        $cobject = $isObject?count($object):0;
        if($isObject && $cobject === 0) {
            $name = $object->getName();
            $attributes = $object->attributes();
            $attributeKey = $this->attributeKey($attributes);
            $array[$name.$attributeKey] = (string)$object;

            return $array;
        }
        $arr = (array)$object;
        if(isset($arr['@attributes'])) {
            unset($arr['@attributes']);
        }
        $keys = array_keys($arr);
        $count =  count($keys);
        if($isObject && !empty($arr)) {
            foreach($object as $key => $value) {
                if($key === '@attributes') {
                    continue;
                }
                if($key === 0 && $count === 1) {
                    return $value;
                }
                $valueArr = (array)$value;
                if(isset($valueArr['@attributes'])) {
                    unset($valueArr['@attributes']);
                }
                if(is_object($value) && !empty($valueArr)) {
                    $cValue = count($value);
                    $cValueArr = count($valueArr);
                    $attributes = $value->attributes();
                    $attributeKey = $this->attributeKey($attributes);
                    if(isset($array[$key.$attributeKey])) {
                        if(!isset($array[$key.$attributeKey][0])) {
                            $array[$key.$attributeKey] = [$array[$key.$attributeKey]];
                        }
                        if($cValue === 0 || ($cValue  <= 1 && $cValueArr === 1)) {
                            $valueReset = reset($valueArr);
                            if(!is_object($valueReset) && !is_array($valueReset)) {
                                $array[$key.$attributeKey][] = $valueReset;
                                continue;
                            }
                        }

                        $array[$key.$attributeKey][] = $this->convertSimpleXmlToArray($value, $namespaces);
                        continue;
                    }
                    if($cValue === 0 || ($cValue <= 1 && $cValueArr === 1)) {
                        $valueReset = reset($valueArr);
                        if (!is_object($valueReset) && !is_array($valueReset)) {
                            $array[$key.$attributeKey] = $valueReset;
                            continue;
                        }
                    }
                    $array[$key.$attributeKey] = $this->convertSimpleXmlToArray($value, $namespaces);
                }
                else {
                    $array[$key] = (string)$value;
                }
            }
            return $array;
        }

        return (string)$object;
    }

    /**
     * Wandelt ein SimpleXml-Objekt in ein Array
     *
     * @param SimpleXMLElement $object
     *
     * @return array|string
     */
    public function convertSimpleXmlToArray_old($object, $namespaces)
    {
        if(is_object($object)) {
            $attributes = (array)$object->attributes();
            $namespace = $object->getNamespaces();
            if(!empty($attributes) || !empty($namespace)) {
                if($attributes) {

                }
            }
        }
        $array = (array)$object;
        if (empty($array)) {
            return '';
        }

        foreach ($array as $key => $value) {
            $isObject = is_object($value);
            if ($isObject || is_array($value)) {
                $attributes = null;
                if($key === '@attributes') {
                    if($value) {

                    }
                }
                if($key !== '@attributes' && $isObject) {
                    $attributes = (array)$value->attributes();
                    if(!empty($attributes)) {
                        foo($attributes);
                    }
                }
                $array[$key] = $this->convertSimpleXmlToArray($value);
            }
        }

        return $array;
    }

    /**
     * @param array  $array
     * @param string $rootNode Name des Root-Elements
     *
     * @return SimpleXMLElement
     */
    public function convertArrayToSimpleXml($array, $rootNode = 'xml')
    {
        $rootNodeCloser = explode(' ', $rootNode);
        $rootNodeCloser = reset($rootNodeCloser);
        $xml = new SimpleXMLElement(
            sprintf('<?xml version="1.0" encoding="UTF-8"?><%s></%s>', $rootNode, $rootNodeCloser)
        );
        $nameSpaces = $this->getNameSpacesByNode($rootNode);
        $this->arrayToXmlHelper($xml, $array, $nameSpaces);

        return $xml;
    }

    /**
     * @param string $node
     *
     * @return array
     */
    protected function getNameSpacesByNode($node)
    {
        $nameSpaces = [];
        $nodeArr = explode(' ', $node);
        unset($nodeArr[0]);
        foreach($nodeArr as $nodeVal) {
            $nodeVal = trim($nodeVal);
            if(empty($nodeVal)) {
                continue;
            }
            if(preg_match_all('/xmlns(:{0,1})([^=]*)="([^"]+)"/', $nodeVal, $matches)) {
                $nameSpaces[$matches[2][0]] = $matches[3][0];
            }
        }

        return $nameSpaces;
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
     * @param SimpleXMLElement $xmlObj
     * @param array            $array
     * @param array            $nameSpaces
     * @param string           $parentTag
     * @param array            $attributesFromParent
     */
    protected function arrayToXmlHelper(&$xmlObj, $array, $nameSpaces = [],  $parentTag = '', $attributesFromParent = [])
    {
        foreach ($array as $key => $value) {
            // Wenn kein Knotenname ermittelt werden konnte > den Knoten 'item' nennen
            $subNodeName = is_int($key) ? 'item' : $key;
            if(!empty($parentTag) && is_int($key)) {
                $subNodeName = $parentTag;
            }
            list($subNodeName, $attributes, $nameSpace) = $this->getAttributesFromKey($subNodeName, $nameSpaces);

            if (is_array($value)) {
                $useParentTag = !empty($key);
                foreach ($value as $key2 => $value2) {
                    if(!is_int($key2) || !$useParentTag) {
                        $useParentTag = false;
                        break;
                    }
                }
                if($useParentTag) {
                    $this->arrayToXmlHelper($xmlObj, $value, $nameSpaces, $subNodeName, $attributes);
                }
                else {
                    $subNode = $xmlObj->addChild((string)$subNodeName, null, $nameSpace);
                    if (!empty($attributes)) {
                        foreach ($attributes as $attribute) {
                            $subNode->addAttribute((string)$attribute[0],
                                empty($attribute[1]) ? '' : (string)$attribute[1]);
                        }
                    }
                    elseif(!empty($attributesFromParent)) {
                        foreach ($attributesFromParent as $attribute) {
                            $subNode->addAttribute((string)$attribute[0],
                                empty($attribute[1]) ? '' : (string)$attribute[1]);
                        }
                    }
                    $this->arrayToXmlHelper($subNode, $value, $nameSpaces,$subNodeName);
                }
            } else {
                $subNode = $xmlObj->addChild((string)$subNodeName, htmlspecialchars($value, ENT_QUOTES), $nameSpace);
                if(!empty($attributes)) {
                    foreach($attributes as $attribute) {
                        $subNode->addAttribute((string)$attribute[0], empty($attribute[1])?'':(string)$attribute[1]);
                    }
                }
                elseif(!empty($attributesFromParent)) {
                    foreach($attributesFromParent as $attribute) {
                        $subNode->addAttribute((string)$attribute[0], empty($attribute[1])?'':(string)$attribute[1]);
                    }
                }
            }
        }
    }

    /**
     * @param string $key
     * @param array  $nameSpaces
     *
     * @return array
     */
    protected function getAttributesFromKey($key, $nameSpaces = [])
    {
        $keyArr = explode(' ', $key);
        $nameSpace = null;
        $node = $keyArr[0];
        if(strpos($node, ':') !== false) {
            list($nameSpaceShort, $node) = explode(':', $node, 2);
            if($nameSpaceShort !== '' && isset($nameSpaces[$nameSpaceShort])) {
                $nameSpace = $nameSpaces[$nameSpaceShort];
            }
        }
        unset($keyArr[0]);
        $attributes = [];
        foreach($keyArr as $attr) {
            if(empty($attr)) {
                continue;
            }
            $attrA = explode('=', $attr,2);
            if(!empty($attrA[1])) {
                $attrA[1] = trim($attrA[1],'"');
            }
            $attributes[] = $attrA;
        }

        return [$node, $attributes, $nameSpace];
    }

}
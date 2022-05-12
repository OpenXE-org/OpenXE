<?php

namespace Xentral\Modules\Company\Service;

final class DocumentCustomizationBlockParser
{
    /**
     * @deprecated
     */
    private $erp;

    /**
     * @param \erpAPI $erp
     */
    public function __construct($erp)
    {
        $this->erp = $erp;
    }

    /**
     * @param string $content
     * @param array  $variables
     *
     * @return string
     */
    public function parse($content, $variables)
    {
        $elements = explode("\n", str_replace("\r", '', trim($content)));

        foreach ($elements as $key => $el) {
            if ($el === '') {
                $elements[$key] = '|';
            }
        }

        foreach ($elements as $key => $el) {
            $el = trim($el);
            $elements[$key] = $el;
            foreach ($variables as $prevKey => $preVal) {
                if (strpos($el, '{' . $prevKey . '}') !== false) {
                    $elements[$key] = trim(str_replace('{' . $prevKey . '}', $preVal, $el));
                    break;
                }
            }
        }
        $content = $this->erp->ParseIfVars(implode("\n", $elements));

        return $this->erp->RemoveUnusedParsevars($content);
    }
}
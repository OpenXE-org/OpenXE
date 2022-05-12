<?php

namespace Xentral\Widgets\DataTable\Result;

use Xentral\Widgets\DataTable\Exception\InvalidArgumentException;

final class DataTableHtmlResult
{
    /** @var string $tableHtml */
    private $tableHtml;

    /** @var array $scriptOptions Initialization options for DataTable */
    private $scriptOptions = [];

    /**
     * @param string $tableHtml
     * @param array  $scriptOptions
     *
     * @throws InvalidArgumentException
     */
    public function __construct($tableHtml, array $scriptOptions)
    {
        if (empty($tableHtml)) {
            throw new InvalidArgumentException('Required parameter "tableHtml" is empty.');
        }
        if (empty($scriptOptions)) {
            throw new InvalidArgumentException('Required parameter "scriptOptions" is empty.');
        }

        $this->tableHtml = $tableHtml;
        $this->scriptOptions = $scriptOptions;
    }

    /**
     * @return string
     */
    public function getResult()
    {
        return $this->getHtml();
    }

    /**
     * @return string
     */
    public function getHtml()
    {
        $html = '<div class="datatable-container">';
        $html .= $this->getTableHtml();
        $html .= $this->getScriptHtml();
        $html .= '</div>';

        return $html;
    }

    /**
     * @return string
     */
    public function getTableHtml()
    {
        return $this->tableHtml;
    }

    /**
     * @return string
     */
    public function getScriptHtml()
    {
        $optionsJsonString = json_encode(
            $this->getScriptOptions(),
            JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT
        );

        return sprintf('<script type="application/json">%s</script>', $optionsJsonString);
    }

    /**
     * @return array
     */
    public function getScriptOptions()
    {
        return $this->scriptOptions;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->getHtml();
    }
}

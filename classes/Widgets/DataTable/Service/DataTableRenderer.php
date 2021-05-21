<?php

namespace Xentral\Widgets\DataTable\Service;

use Xentral\Widgets\DataTable\Column\Column;
use Xentral\Widgets\DataTable\DataTableInterface;
use Xentral\Widgets\DataTable\Result\DataTableHtmlResult;

final class DataTableRenderer
{
    /**
     * @param DataTableInterface $table
     *
     * @return DataTableHtmlResult
     */
    public function createHtmlResult(DataTableInterface $table)
    {
        return new DataTableHtmlResult($this->getHtmlTable($table), $this->getDataTableOptions($table));
    }

    /**
     * @param DataTableInterface $table
     *
     * @return array
     */
    private function getDataTableOptions(DataTableInterface $table)
    {
        $options = $table->getOptions()->toArray();

        $options['ajax'] = [
            'url'  => $table->getConfig()->getAjaxUrl(),
            'type' => $table->getConfig()->getAjaxMethod(),
            'data' => $table->getConfig()->getAjaxParams(),
        ];
        $options['columns'] = $table->getColumns()->toArray();

        return $options;
    }

    /**
     * @param DataTableInterface $table
     *
     * @return string
     */
    private function getHtmlTable(DataTableInterface $table)
    {
        $columns = $table->getColumns();
        $headerHtml1 = '';
        $footerHtml = '';

        /** @var Column $column */
        foreach ($columns as $column) {
            $headerHtml1 .= sprintf('<th data-name="%s">%s</th>', $column->getName(), $column->getTitle());
            if ($column->has('footerHtml')) {
                $footerHtml .= sprintf('<th data-name="%s">%s</th>', $column->getName(), $column->get('footerHtml'));
            } else {
                $footerHtml .= sprintf('<th data-name="%s">%s</th>', $column->getName(), $column->getTitle());
            }
        }

        $html = "\n";
        $html .= sprintf(
                '<table id="%s" class="%s" width="100%%" data-autoinit="%s">',
                $table->getConfig()->getTableName(),
                $table->getConfig()->getCssClassesString(),
                $table->getConfig()->isAutoInit() ? 'true' : 'false'
            ) . "\n";
        $html .= '<thead>';
        $html .= '<tr>' . $headerHtml1 . '</tr>';
        $html .= '</thead>' . "\n";
        $html .= '<tfoot><tr>' . $footerHtml . '</tr></tfoot>' . "\n";
        $html .= '</table>' . "\n";

        return $html;
    }
}

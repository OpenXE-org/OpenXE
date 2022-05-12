<?php

declare(strict_types=1);

namespace Xentral\Modules\Report\Service;

use Xentral\Components\Util\StringUtil;
use Xentral\Modules\Report\Data\ReportColumn;

class ReportColumnFormatter
{
    /**
     * @param ReportColumn $column
     * @param string       $targetColumn
     *
     * @return string
     */
    public function formatColumnExpression(ReportColumn $column, string $targetColumn): string
    {
        switch ($column->getFormatType()) {
            case ReportColumn::FORMAT_SUM_MONEY_DE:
                $template = 'FORMAT(`%1$s`, 2, \'de_DE\') AS `%1$s`';
                break;

            case ReportColumn::FORMAT_SUM_MONEY_EN:
                $template = 'FORMAT(`%1$s`, 2, \'en_EN\') AS `%1$s`';
                break;

            case ReportColumn::FORMAT_DATE_DMY:
                $template = 'DATE_FORMAT(`%1$s`, \'%%d.%%m.%%Y\') AS `%1$s`';
                break;

            case ReportColumn::FORMAT_DATE_YMD:
                $template = 'DATE_FORMAT(`%1$s`, \'%%Y.%%m.%%d\') AS `%1$s`';
                break;

            case ReportColumn::FORMAT_DATE_DMYHIS:
                $template = 'DATE_FORMAT(`%1$s`, \'%%d.%%m.%%Y %%H:%%i:%%s\') AS `%1$s`';
                break;

            case ReportColumn::FORMAT_DATE_YMDHIS:
                $template = 'DATE_FORMAT(`%1$s`, \'%%Y.%%m.%%d %%H:%%i:%%s\') AS `%1$s`';
                break;

            case ReportColumn::FORMAT_CUSTOM:
                return $this->resolveCustomFormat($column->getFormatStatement(), $targetColumn);

            default:
                $template = '`%1$s`';
        }

        return sprintf($template, $targetColumn);
    }

    /**
     * @param string $formatStatement
     * @param string $targetColumn
     *
     * @return string
     */
    private function resolveCustomFormat(string $formatStatement, string $targetColumn): string
    {
        $customFormat = $formatStatement;
        if (preg_match('/(.*)\s?as\s?.+$/i', $formatStatement, $matches)) {
            $customFormat = rtrim($matches[1]);
        }
        $statement = preg_replace('/{VALUE}/i', sprintf('`%s`', $targetColumn), $customFormat);
        $statement .= sprintf(' AS `%s`', $targetColumn);

        if (StringUtil::startsWith($statement, ' AS ')) {
            return sprintf('`%s`', $targetColumn);
        }

        return $statement;
    }
}

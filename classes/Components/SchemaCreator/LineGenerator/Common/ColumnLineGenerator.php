<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\LineGenerator\Common;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\EscapingException;
use Xentral\Components\Database\Exception\QueryFailureException;
use Xentral\Components\SchemaCreator\Exception\LineGeneratorException;
use Xentral\Components\SchemaCreator\Interfaces\CharTypeInterface;
use Xentral\Components\SchemaCreator\Interfaces\DecimalTypeInterface;
use Xentral\Components\SchemaCreator\Interfaces\EnumAndSetInterface;
use Xentral\Components\SchemaCreator\Interfaces\IntegerTypeInterface;
use Xentral\Components\SchemaCreator\Interfaces\ColumnInterface;
use Xentral\Components\SchemaCreator\Interfaces\NumericTypeInterface;
use Xentral\Components\SchemaCreator\Interfaces\ColumnTextInterface;
use Xentral\Components\SchemaCreator\Type\Datetime;
use Xentral\Components\SchemaCreator\Type\Timestamp;
use Xentral\Components\SchemaCreator\Type\Year;

final class ColumnLineGenerator
{

    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->db = $database;
    }

    /**
     * @param ColumnInterface $column
     *
     * @return string
     */
    private function generateType(ColumnInterface $column): string
    {
        if ($column instanceof IntegerTypeInterface) {
            $spec = $column->isUnsigned() ? NumericTypeInterface::NON_NEGATIV : NumericTypeInterface::WITH_NEGATIV;
            if ($column instanceof DecimalTypeInterface) {
                return sprintf(
                    '%s(%d, %d) %s',
                    $column->getType(),
                    $column->getLength(),
                    $column->getDecimals(),
                    $spec
                );
            }

            return sprintf('%s(%d) %s', $column->getType(), $column->getLength(), $spec);
        }

        if ($column instanceof CharTypeInterface || $column instanceof Year) {
            return sprintf('%s(%d)', $column->getType(), $column->getLength());
        }

        if ($column instanceof EnumAndSetInterface) {
            return sprintf('%s(%s)', $column->getType(), $column->getReferences());
        }

        return $column->getType();
    }

    /**
     * @param ColumnInterface $column
     *
     * @throws EscapingException
     *
     * @return string
     */
    public function generateLine(ColumnInterface $column): string
    {
        $line = [];
        $line[] = $this->db->escapeIdentifier($column->getField());
        $line[] = $this->generateType($column);

        if ($defaultLine = $this->getLineDefault($column)) {
            $line[] = $defaultLine;
        }

        if ($charset = $this->generateCharset($column)) {
            $line[] = $charset;
        }

        if ($collate = $this->generateCollate($column)) {
            $line[] = $collate;
        }

        if ($comment = $this->generateComment($column)) {
            $line[] = $comment;
        }

        $outLine = trim(implode(' ', $line));
        if ($this->isAutoIncrementField($column)) {
            $outLine .= ' AUTO_INCREMENT';
        }

        return $outLine;
    }


    /**
     * @param $column
     *
     * @throws EscapingException
     *
     * @return string|null
     */
    private function generateComment($column): ?string
    {
        return $this->hasOption('comment', $column) ? sprintf(
            "COMMENT %s",
            $this->db->escapeString($this->getOption('comment', $column))
        ) : null;
    }

    /**
     * @param $column
     *
     * @throws EscapingException
     *
     * @return string|null
     */
    private function generateCharset($column): ?string
    {
        return $this->hasOption('charset', $column) ? sprintf(
            "CHARACTER SET %s",
            $this->db->escapeString(
                $this->getOption('charset', $column)
            )
        ) : null;
    }

    /**
     * @param $column
     *
     * @throws EscapingException
     *
     * @return string|null
     */
    private function generateCollate($column): ?string
    {
        return $this->hasOption('collate', $column) ? sprintf(
            'COLLATE %s',
            $this->db->escapeString(
                $this->getOption('collate', $column)
            )
        ) : null;
    }

    /**
     * @param ColumnInterface $column
     *
     * @return array
     */
    public function toArray(ColumnInterface $column): array
    {
        return [
            'field'       => $column->getField(),
            'type'        => $column->getType(),
            'length'      => method_exists($column, 'getLength') ? $column->getLength() : null,
            'decimals'    => method_exists($column, 'getDecimals') ? $column->getDecimals() : null,
            'unsigned'    => method_exists($column, 'isUnsigned') ? $column->isUnsigned() : false,
            'nullable'    => $this->isNullable($column),
            'default'     => $this->getOption('default', $column),
            'extra'       => $this->getOption('extra', $column),
            'references'  => method_exists($column, 'getReferences') ? $column->getReferences() : null,
            'sql_default' => $this->getLineDefault($column),
        ];
    }

    /**
     * @param string               $key
     * @param ColumnInterface|null $column
     *
     * @return mixed|null
     */
    public function getOption(string $key, ColumnInterface $column)
    {
        $options = $this->getAllOptions($column);

        return $options[$key] ?? null;
    }

    /**
     * @param ColumnInterface $column
     *
     * @return array
     */
    private function getAllOptions(ColumnInterface $column): array
    {
        $options = $column->getOptions();
        $options = array_merge(ColumnInterface::DEFAULT_PARAMS, $options);

        if (array_key_exists('extra', $options) && $options['extra'] !== null) {
            $options['extra'] = $this->extraMapping($options['extra']);
        }

        return $options;
    }

    /**
     * @param string          $key
     * @param ColumnInterface $column
     *
     * @return bool
     */
    public function hasOption(string $key, ColumnInterface $column): bool
    {
        $options = $this->getAllOptions($column);

        return array_key_exists($key, $options) && $options[$key] !== null;
    }

    /**
     * @param ColumnInterface $column
     *
     * @return bool
     */
    private function isNullable(ColumnInterface $column): bool
    {
        return $column->isNullable();
    }

    /**
     * @param ColumnInterface $column
     *
     * @return string
     */
    private function getLineDefault(ColumnInterface $column): string
    {
        if ($this->isNullable($column) === true) {
            $defaultValue = $column instanceof Timestamp ? 'NULL DEFAULT NULL' : 'DEFAULT NULL';

            if ($this->getOption('default', $column) === null) {
                return $defaultValue;
            }

            $customDefault = $this->getOption('default', $column);

            if (($column instanceof Timestamp || $column instanceof Datetime) &&
                $this->containMySQLTimeConstant($customDefault) === true) {
                return sprintf('DEFAULT %s', strtoupper($this->getOption('default', $column)));
            }

            return sprintf("DEFAULT '%s'", $this->getOption('default', $column));
        }

        if (($column instanceof ColumnTextInterface) || $this->isAutoIncrementField($column) === true) {
            return 'NOT NULL';
        }
        $default = $this->getDefault($column);

        if ($this->hasOption('extra', $column)) {
            $default .= ' ' . $this->getOption('extra', $column);
        }

        if ($this->isNullable($column) === false) {
            $default = 'NOT NULL ' . $default;
        }

        return $default;
    }

    /**
     * @param ColumnInterface $column
     *
     * @return string
     */
    private function getDefault(ColumnInterface $column): string
    {
        if ($column instanceof NumericTypeInterface) {
            return $this->hasOption('default', $column) ? sprintf(
                "DEFAULT '%d'",
                $this->getOption('default', $column)
            ) : '';
        }

        $isNullable = $this->isNullable($column);
        $defaultSet = $this->getOption('default', $column);
        if ($isNullable === false && $defaultSet !== null) {
            return sprintf("DEFAULT '%s'", $defaultSet);
        }

        return "DEFAULT ''";
    }

    /**
     * @param ColumnInterface $column
     *
     * @return bool
     */
    private function isAutoIncrementField(ColumnInterface $column): bool
    {
        return $this->hasOption('extra', $column) && in_array(
                $this->getOption('extra', $column),
                ['ai', 'auto_increment', 'AUTO_INCREMENT'],
                true
            );
    }

    /**
     * @param string $extra
     *
     * @return string
     */
    private function extraMapping(string $extra): string
    {
        $short = ['ai' => 'AUTO_INCREMENT'];

        if (array_key_exists($extra, $short)) {
            return $short[$extra];
        }

        return strtoupper($extra);
    }

    /**
     * @param string $tableName
     *
     * @throws LineGeneratorException
     *
     * @return array
     */
    public function fetchColumnsFromDb(string $tableName): array
    {
        try {
            $columns = $this->db->fetchAll('SHOW COLUMNS FROM ' . $tableName);
        } catch (QueryFailureException $exception) {
            throw new LineGeneratorException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception->getPrevious()
            );
        }

        $result = [];
        foreach ($columns as $column) {
            $isNull = strtoupper($column['Null']) === 'YES' && $column['Default'] === null;
            $isNullable = strtoupper($column['Null']) === 'YES';
            $extra = !empty($column['Extra']) ? strtoupper($column['Extra']) : null;
            $default = $isNull ? 'DEFAULT NULL' : "DEFAULT '" . $column['Default'] . "'";
            if ($isNullable === false && $isNull === false) {
                $default = "NOT NULL DEFAULT '" . $column['Default'] . "'";
            }

            $resultType = $column['Type'];
            $references = null;
            if (preg_match('/^(enum|set)\w*/i', $resultType, $found) && count($found) > 0) {
                $resultType = strtoupper($found[0]);
                $reference_values = str_replace(['(', ')', $found[0], '\''], '', $column['Type']);
                $references = $reference_values;
            } else {
                $resultType = strtoupper($resultType);
            }
            $length = null;
            $decimals = null;
            preg_match('/^(decimal|double|float)\(\d*(,\d*)?\)/i', $resultType, $decimalFound);
            if (count($decimalFound) > 0) {
                $decimals = 0;
                preg_match('/\(\d*(,\d*)?\)/', $resultType, $doubleLengthFound);

                if (count($decimalFound) === 3) {
                    $decimals = (int)str_replace(',', '', $decimalFound[2]);
                }
                $length = (int)str_replace(['(', ')'], '', $doubleLengthFound[0]);
                $resultType = str_replace($decimalFound[0], $decimalFound[1], $resultType);
            }
            if ($length === null) {
                preg_match('/\(\d*\)/', $resultType, $lengthFound);
                if (count($lengthFound) > 0) {
                    $length = (int)str_replace(['(', ')'], '', $lengthFound[0]);
                    $resultType = str_replace($lengthFound[0], '', $resultType);
                }
            }

            $unsigned = false;

            $resultType_exploded = explode(' ', $resultType);
            if (count($resultType_exploded) > 1 && in_array(
                    $resultType_exploded[1],
                    [NumericTypeInterface::WITH_NEGATIV, NumericTypeInterface::NON_NEGATIV],
                    true
                )) {
                $unsigned = $resultType_exploded[1] === NumericTypeInterface::NON_NEGATIV;
                $resultType = trim(str_replace($resultType_exploded[1], '', $resultType));
            }

            $result[] = [
                'field'       => $column['Field'],
                'type'        => $resultType,
                'length'      => $length,
                'decimals'    => $decimals,
                'unsigned'    => $unsigned,
                'nullable'    => $isNullable,
                'default'     => $column['Default'],
                'extra'       => $extra,
                'references'  => $references,
                'sql_default' => $extra === 'AUTO_INCREMENT' ? 'NOT NULL' : $default,
            ];
        }

        return $result;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    private function containMySQLTimeConstant(string $value): bool
    {
        return (boolean)preg_match('/(\bCURRENT_TIMESTAMP\b|\bNOW\b|\bLOCALTIME\b|\bLOCALTIMESTAMP\b)/i', $value);
    }
}

<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\LineGenerator\Common;

use Xentral\Components\Database\Database;
use Xentral\Components\Database\Exception\EscapingException;
use Xentral\Components\SchemaCreator\Option\TableOption;

final class TableOptionsGenerator
{
    /** @var Database */
    private $db;

    /** @var string $optionLine */
    private $optionLine;

    /** @var string[] $defaultParams */
    protected $defaultParams = [
        'engine'        => 'InnoDB',
        'table_charset' => 'utf8',
        'collation'     => 'utf8_general_ci',
        'comment'       => null,
    ];

    /**
     * @param Database $db
     */
    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param array $options
     *
     * @throws EscapingException
     *
     * @return string
     */
    public function generate(array $options = []): string
    {
        $options = array_merge($this->defaultParams, $options);
        $this->addEngine($options['engine']);

        if (array_key_exists('table_charset', $options) && !empty($options['table_charset'])) {
            $this->addCharset($options['table_charset']);
        }

        if (array_key_exists('collation', $options) && !empty($options['collation'])) {
            $this->addCollation($options['collation']);
        }

        if (array_key_exists('comment', $options) && !empty($options['comment'])) {
            $this->addComment($options['comment']);
        }

        return $this->optionLine;
    }

    /**
     * @param string $engine
     *
     * @throws EscapingException
     *
     * @return void
     */
    private function addEngine(string $engine): void
    {
        $this->optionLine = sprintf(' ENGINE=%s', $this->db->escapeString($engine));
    }

    /**
     * @param string $charset
     *
     * @throws EscapingException
     *
     * @return void
     */
    private function addCharset(string $charset): void
    {
        $this->optionLine .= sprintf(' DEFAULT CHARSET=%s', $this->db->escapeString($charset));
    }

    /**
     * @param string $collation
     *
     * @throws EscapingException
     *
     * @return void
     */
    private function addCollation(string $collation): void
    {
        $this->optionLine .= sprintf(' DEFAULT COLLATE = %s', $this->db->escapeString($collation));
    }

    /**
     * @param string $comment
     *
     * @throws EscapingException
     *
     * @return void
     */
    private function addComment(string $comment): void
    {
        $this->optionLine .= sprintf(" COMMENT = %s", $this->db->escapeString($comment));
    }

    /**
     * @param string $tableName
     *
     * @return array
     */
    public function fetchOptionsFromDb(string $tableName): array
    {
        $options = $this->db->fetchRow('SHOW TABLE STATUS WHERE Name =:table', ['table' => $tableName]);

        return [
            'engine'        => array_key_exists('Engine', $options) ? $options['Engine'] : 'InnoDB',
            'table_charset' => array_key_exists('Charset', $options) ? $options['Charset'] : 'utf8',
            'collation'     => array_key_exists('Collation', $options) ? $options['Collation'] : 'utf8_general_ci',
            'comment'       => array_key_exists('Comment', $options) ? $options['Comment'] : '',
        ];
    }

    /**
     * @param TableOption $option
     * @param string|null $charset
     *
     * @throws EscapingException
     *
     * @return string
     */
    public function alterOptions(TableOption $option, ?string $charset = null): string
    {
        switch ($option->getOption()) {
            case 'comment':
                $sqlOption = sprintf('COMMENT = %s', $this->db->escapeString($option->getValue()));
                break;
            case 'engine':
                $sqlOption = sprintf('ENGINE = %s', $this->db->escapeString($option->getValue()));
                break;
            case 'charset':
                $sqlOption = sprintf('CONVERT TO CHARACTER SET %s', $this->db->escapeString($option->getValue()));
                break;
            case 'collation':
                $charset = $charset ?? $this->defaultParams['table_charset'];
                $sqlOption = sprintf(
                    'CONVERT TO CHARACTER SET %s COLLATE %s',
                    $this->db->escapeString($charset),
                    $this->db->escapeString($option->getValue())
                );
                break;
            default:
                $sqlOption = '';
        }

        return $sqlOption;
    }

}

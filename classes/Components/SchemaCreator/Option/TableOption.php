<?php

declare(strict_types=1);

namespace Xentral\Components\SchemaCreator\Option;

final class TableOption
{

    /** @var string $option */
    private $option;

    /** @var string $value */
    private $value;

    /**
     * @param string $option
     * @param string $value
     */
    public function __construct(string $option, string $value)
    {
        $this->option = $option;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getOption(): string
    {
        return $this->option;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $engine
     *
     * @return TableOption
     */
    public static function fromEngine(string $engine): TableOption
    {
        return new self('engine', $engine);
    }

    /**
     * @param string $collation
     *
     * @return TableOption
     */
    public static function fromCollation(string $collation): TableOption
    {
        return new self('collation', $collation);
    }

    /**
     * @param string $charset
     *
     * @return TableOption
     */
    public static function fromCharset(string $charset): TableOption
    {
        return new self('charset', $charset);
    }

    /**
     * @param string $comment
     *
     * @return TableOption
     */
    public static function fromComment(string $comment): TableOption
    {
        return new self('comment', $comment);
    }

}

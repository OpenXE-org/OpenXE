<?php

declare(strict_types=1);

namespace Xentral\Components\Logger\Context;

final class Invocation
{
    /**  @var string $class */
    private $class;

    /** @var string $function */
    private $function;

    /** @var int $line */
    private $line;

    /** @var string $file */
    private $file;

    /**
     * @param string $file
     * @param string $function
     * @param int    $line
     * @param string $class
     */
    public function __construct(string $class, string $function, int $line, string $file)
    {
        $this->class = $class;
        $this->function = $function;
        $this->line = $line;
        $this->file = $file;
    }

    /**
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getFunction(): string
    {
        return $this->function;
    }

    /**
     * @return int
     */
    public function getLine(): int
    {
        return $this->line;
    }

    /**
     * @return string
     */
    public function getFile(): string
    {
        return $this->file;
    }
}

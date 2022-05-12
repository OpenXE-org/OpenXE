<?php

declare(strict_types=1);

namespace Xentral\Components\Logger\Handler;

use Xentral\Components\Database\Database;
use Xentral\Components\Logger\Context\ContextInterface;

final class DatabaseLogHandler extends AbstractLogHandler
{
    /** @var Database $db */
    private $db;

    /**
     * @param Database $database
     * @param string   $level
     */
    public function __construct(Database $database, string $level)
    {
        $this->setMinimumLevel($level);
        $this->db = $database;
    }

    /**
     * @param string           $level
     * @param string           $message
     * @param ContextInterface $context
     *
     * @return void
     */
    public function addLogEntry(string $level, string $message, ContextInterface $context): void
    {
        if (!$this->canHandle($level)) {
            return;
        }

        $values = [
            'level'         => strtoupper($level),
            'message'       => $message,
            'class'         => null,
            'method'        => null,
            'line'          => null,
            'origin_type'   => null,
            'origin_detail' => null,
            'dump'          => null,
        ];

        $values['class'] = $context->getClass();
        $values['method'] = $context->getFunction();
        $values['line'] = $context->getLine();

        if ($context->hasOrigin()) {
            $values['origin_type'] = $context->getOriginType();
            $values['origin_detail'] = $context->getOriginDetail();
        }

        if ($context->hasDump()) {
            $values['dump'] = print_r($context->getDump(), true);
        }

        if ($context->hasException()) {
            $values['dump'] = (string)$context->getException();
        }

        $sql = 'INSERT INTO `log`
                (`log_time`, `level`, `message`, `class`, `method`, `line`, `origin_type`, `origin_detail`, `dump`) 
                VALUES 
                (NOW(), :level, :message, :class, :method, :line, :origin_type, :origin_detail, :dump)';
        $this->db->perform($sql, $values);
    }
}

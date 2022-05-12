<?php

declare(strict_types=1);

namespace Xentral\Core\Installer;

use Xentral\Components\Database\DatabaseConfig;
use Xentral\Components\Database\Exception\EscapingException;
use Xentral\Components\SchemaCreator\Collection\SchemaCollection;
use Xentral\Components\SchemaCreator\Exception\LineGeneratorException;
use Xentral\Components\SchemaCreator\Exception\SchemaCreatorTableException;
use Xentral\Components\SchemaCreator\SchemaCreator;
use RuntimeException;

final class TableSchemaEnsurer
{
    /** @var SchemaCreator $creator */
    private $creator;

    /** @var InstallerCacheConfig $config */
    private $config;

    /** @var DatabaseConfig $dbConfig */
    private $dbConfig;

    /**
     * @param SchemaCreator        $creator
     * @param InstallerCacheConfig $config
     * @param DatabaseConfig       $dbConfig
     */
    public function __construct(SchemaCreator $creator, InstallerCacheConfig $config, DatabaseConfig $dbConfig)
    {
        $this->creator = $creator;
        $this->config = $config;
        $this->dbConfig = $dbConfig;
    }

    /**
     * @param SchemaCollection $collection
     *
     * @throws RuntimeException
     * @throws EscapingException
     * @throws LineGeneratorException
     * @throws SchemaCreatorTableException
     *
     * @return void
     */
    public function ensureSchemas(SchemaCollection $collection): void
    {
        $sqlSchema = [];
        foreach ($collection as $schema) {
            $schemaIndexes = $schema->getIndexes();
            if (!$schemaIndexes->hasPrimaryKey()) {
                throw new RuntimeException(
                    sprintf(
                        'Primary key is missing in schema for table "%s".',
                        $schema->getTable()
                    )
                );
            }
            $query = $this->creator->getSqlSchema($schema);
            if (!empty($query)) {
                $query = sprintf('%s;',rtrim($query, ';'));
            }
            $sqlSchema[] = $query;
        }

        $sql = implode("\n", $sqlSchema);

        if (trim($sql) === '') {
            return;
        }

        $sqlFile = $this->getSqlFilePath();
        if (!@file_put_contents($sqlFile, $sql)) {
            throw new RuntimeException(
                sprintf(
                    'SQL-Datei "%s" cannot be created. Probably there are no write permissions in %s',
                    $sqlFile,
                    $this->config->getUserDataTempDir()
                )
            );
        }
        $this->importSql($sqlFile);
    }

    /**
     * @param string $sqlFile
     *
     * @throws RuntimeException
     *
     * @return void
     */
    private function importSql(string $sqlFile): void
    {
        if (!$this->canExec()) {
            throw new RuntimeException(
                'PHP function "exec" is not available or has been disabled by php.ini settings.'
            );
        }

        if (!is_file($sqlFile) || filesize($sqlFile) < 1) {
            return;
        }
        @exec(
            sprintf(
                'mysql -D%s -h%s -u%s -p%s < %s',
                escapeshellarg($this->dbConfig->getDatabase()),
                escapeshellarg($this->dbConfig->getHostname()),
                escapeshellarg($this->dbConfig->getUsername()),
                escapeshellarg($this->dbConfig->getPassword()),
                escapeshellarg($sqlFile)
            ),
            $output,
            $returnVar
        );

        switch ($returnVar) {
            case 0:
                // No error
                break;
            case 1:
                throw new RuntimeException('General error: ' . implode(' ', $output));
                break;
            case 126:
                throw new RuntimeException('Can not execute "mysql" command.');
                break;
            case 127:
                throw new RuntimeException('Command "mysql" not found.');
                break;
        }

        unlink($sqlFile);
    }

    /**
     * @return string
     */
    private function getSqlFilePath(): string
    {
        return $this->config->getUserDataTempDir() . '/' . uniqid('schema-', true) . '.sql';
    }

    /**
     * @return bool
     */
    private function canExec(): bool
    {
        $functionName = 'exec';
        if (!function_exists($functionName)) {
            return false;
        }

        $disabledFunctions = explode(',', ini_get('disable_functions'));
        foreach ($disabledFunctions as $disabledFunction) {
            if (trim($disabledFunction) === $functionName) {
                return false;
            }
        }

        return true;
    }
}

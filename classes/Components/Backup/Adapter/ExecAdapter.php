<?php

namespace Xentral\Components\Backup\Adapter;

use Xentral\Components\Database\DatabaseConfig;

final class ExecAdapter implements AdapterInterface
{

    /** @var DatabaseConfig $config */
    private $config;

    /** @var int timeout */
    const TIME_OUT = 3600;

    /**
     * @param DatabaseConfig    $config
     * @param string            $file
     *
     * @param null|string|array $tables
     *
     * @param null|string       $where
     *
     * @param bool              $quickMode Without SET INNODB_STRICT_MODE=0; Advantage quick and space-saving
     *
     * @return void
     */
    public function createDump(DatabaseConfig $config, $file, $tables = null, $where = null, $quickMode = true)
    {
        $this->config = $config;
        $sAsBackup = $this->config->getDatabase();
        if ($tables !== null) {
            if (is_array($tables)) {
                $tables = implode(' ', $tables);
            }
            $sAsBackup .= ' --tables ' . $tables;
        }
        if ($where !== null) {
            $sAsBackup .= " --where=\"$where\"";
        }
        if ($quickMode !== true) {
            $cmd = "echo 'SET INNODB_STRICT_MODE=0;' > {$file} && mysqldump --no-tablespaces --extended-insert {$sAsBackup} --no-create-db -h{$this->config->getHostname()} -u{$this->config->getUsername()} -p'{$this->config->getPassword()}' >> {$file} && gzip -c {$file} > " . $file . '.gz && rm -f' . $file;
        } else {
            $cmd = "mysqldump --no-tablespaces  --extended-insert {$sAsBackup} --no-create-db -h{$this->config->getHostname()} -u{$this->config->getUsername()} -p'{$this->config->getPassword()}' | gzip > " . $file . '.gz';
        }
        $this->execute($cmd);
    }

    /**
     * @param DatabaseConfig $config
     * @param string         $file
     *
     * @return void
     */
    public function restoreDump(DatabaseConfig $config, $file)
    {
        $this->config = $config;
        $cmd = "gunzip < {$file} | mysql -D{$this->config->getDatabase()} -h{$this->config->getHostname()} -u{$this->config->getUsername()} -p'{$this->config->getPassword()}'";
        $this->execute($cmd);
    }

    /**
     * @param string $pidFile
     *
     * @return string
     */
    public function getStatus($pidFile)
    {
        if (file_exists($pidFile) && ($time = file_get_contents($pidFile)) && (time() - (int)$time) < static::TIME_OUT) {
            return AdapterInterface::STATUS_WORKING;
        }

        return AdapterInterface::STATUS_WAITING;
    }

    /**
     * @param string $cmd
     */
    protected function execute($cmd)
    {
        @exec($cmd);
    }
}

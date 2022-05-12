<?php

namespace Xentral\Modules\DemoExporter;

use Xentral\Components\Database\Database;

final class DemoExporterGateway
{
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    /**
     * @param string $name
     *
     * @return array
     */
    public function getDemoExporterConfigurationValue($name)
    {
        return $this->db->fetchRow(
            'SELECT k.wert FROM `konfiguration` AS `k` WHERE k.name=:name', ['name' => (string)$name]
        );
    }

}

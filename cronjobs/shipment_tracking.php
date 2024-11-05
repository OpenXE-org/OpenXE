<?php

// SPDX-FileCopyrightText: 2024 Andreas Palm
//
// SPDX-License-Identifier: LicenseRef-EGPL-3.1

use Xentral\Components\Database\Database;
use Xentral\Modules\ShippingMethod\Model\ShipmentStatus;

error_reporting(E_ERROR);

include_once dirname(__DIR__) . '/xentral_autoloader.php';

if(empty($app) || !($app instanceof ApplicationCore)){
  $app = new ApplicationCore();
}


/** @var Database $db */
$db = $app->Container->get('Database');

$shipments_sql = "SELECT CONCAT(va.id, ';', va.modul) module, vp.id, vp.tracking, vp.status
    FROM versandpakete vp
    JOIN versandarten va ON vp.versandart = va.type
    WHERE status IN ('neu', 'versendet')";
$shipments = $db->fetchGroup($shipments_sql);

foreach ($shipments as $module => $vps) {
    list($moduleId, $moduleName) = explode(';', $module,2);
    $module = $app->erp->LoadVersandModul($moduleName, intval($moduleId));

    foreach ($vps as $vp) {
        $status = match ($module->GetShipmentStatus($vp['tracking'])) {
            ShipmentStatus::Announced => 'neu',
            ShipmentStatus::EnRoute => 'versendet',
            ShipmentStatus::Delivered => 'abgeschlossen',
            default => null,
        };
        if ($status === null || $status === $vp['status']) continue;
        $db->perform('UPDATE versandpakete SET status = :status WHERE id = :id',
            ['status' => $status, 'id' => $vp['id']]);
    }
}

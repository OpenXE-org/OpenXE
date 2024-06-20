<?php
/*
 * SPDX-FileCopyrightText: 2022 Andreas Palm
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 */

namespace Xentral\Modules\SubscriptionCycle;

use ApplicationCore;
use DateTimeImmutable;
use DateTimeInterface;
use Xentral\Components\Database\Database;

class SubscriptionModule implements SubscriptionModuleInterface
{
  private ApplicationCore $app;
  private Database $db;

  public function __construct(ApplicationCore $app, Database $db)
  {
    $this->app = $app;
    $this->db = $db;
  }

  public function GetPositions(int $address, string $documentType, DateTimeInterface $calculationDate = null): array
  {
    if ($calculationDate === null)
      $calculationDate = new DateTimeImmutable('today');

    $sql = "SELECT 
        aa.id, 
        @start := GREATEST(aa.startdatum, aa.abgerechnetbis) as start,
        @end := IF(aa.enddatum = '0000-00-00' OR aa.enddatum > :calcdate, :calcdate, aa.enddatum) as end,
        @cycles := GREATEST(aa.zahlzyklus, CASE
            WHEN aa.preisart = 'monat' THEN
                TIMESTAMPDIFF(MONTH, @start, @end)
            WHEN aa.preisart = 'jahr' THEN
                TIMESTAMPDIFF(YEAR,  @start, @end)
            WHEN aa.preisart = '30tage' THEN
                FLOOR(TIMESTAMPDIFF(DAY, @start, @end) / 30)
        END+1) as cycles,
        CASE
            WHEN aa.preisart = 'monat' THEN
                DATE_ADD(@start, INTERVAL @cycles MONTH)
            WHEN aa.preisart = 'jahr' THEN
                DATE_ADD(@start, INTERVAL @cycles YEAR)
            WHEN aa.preisart = '30tage' THEN
                DATE_ADD(@start, INTERVAL @cycles*30 DAY )
            END as newend,
        aa.preisart,
        aa.adresse,
        aa.preis,
        aa.rabatt,
        aa.bezeichnung,
        aa.beschreibung,
        aa.artikel,
        aa.menge,
        aa.waehrung
        FROM abrechnungsartikel aa
        JOIN artikel a on aa.artikel = a.id
        WHERE aa.dokument = :doctype
        AND greatest(aa.startdatum, aa.abgerechnetbis) <= :calcdate
        AND (aa.enddatum = '0000-00-00' OR aa.abgerechnetbis < aa.enddatum)
        AND aa.preisart IN ('monat', 'jahr', '30tage')  
        AND aa.adresse = :address";

    return $this->db->fetchAll($sql, [
        'doctype' => $documentType,
        'calcdate' => $calculationDate->format('Y-m-d'),
        'address' => $address]);
  }

  public function CreateInvoice(int $address, DateTimeInterface $calculationDate = null) {
    $positions = $this->GetPositions($address, 'rechnung', $calculationDate);
    if(empty($positions))
      return;

    $invoice = $this->app->erp->CreateRechnung($address);
    $this->app->erp->LoadRechnungStandardwerte($invoice, $address);
    foreach ($positions as $pos) {
      $beschreibung = $pos['beschreibung'];

      $starts = DateTimeImmutable::createFromFormat('Y-m-d', $pos['start'])->format('d.m.Y');
      $newends = DateTimeImmutable::createFromFormat('Y-m-d', $pos['newend'])->format('d.m.Y');
      $beschreibung .= "<br>Zeitraum: $starts - $newends";

      $this->app->erp->AddRechnungPositionManuell($invoice, $pos['artikel'], $pos['preis'],
          $pos['menge']*$pos['cycles'], $pos['bezeichnung'], $beschreibung, $pos['waehrung'], $pos['rabatt']);
      $this->db->exec("UPDATE abrechnungsartikel SET abgerechnetbis='{$pos['newend']}' WHERE id={$pos['id']}");
    }
    $this->app->erp->RechnungNeuberechnen($invoice);
    //$this->app->erp->BelegFreigabe('rechnung', $invoice);
  }

  public function CreateOrder(int $address, DateTimeInterface $calculationDate = null) {
    $positions = $this->GetPositions($address, 'auftrag', $calculationDate);
    if(empty($positions))
      return;

    $orderid = $this->app->erp->CreateAuftrag($address);
    $this->app->erp->LoadAuftragStandardwerte($orderid, $address);
    foreach ($positions as $pos) {
      $beschreibung = $pos['beschreibung'];

      $starts = DateTimeImmutable::createFromFormat('Y-m-d', $pos['start'])->format('d.m.Y');
      $newends = DateTimeImmutable::createFromFormat('Y-m-d', $pos['newend'])->format('d.m.Y');
      $beschreibung .= "<br>Zeitraum: $starts - $newends";

      $this->app->erp->AddAuftragPositionManuell($orderid, $pos['artikel'], $pos['preis'],
          $pos['menge']*$pos['cycles'], $pos['bezeichnung'], $beschreibung, $pos['waehrung'], $pos['rabatt']);
      $this->db->exec("UPDATE abrechnungsartikel SET abgerechnetbis='{$pos['newend']}' WHERE id={$pos['id']}");
    }
    $this->app->erp->AuftragNeuberechnen($orderid);
    //$this->app->erp->BelegFreigabe('auftrag', $orderid);
  }
}
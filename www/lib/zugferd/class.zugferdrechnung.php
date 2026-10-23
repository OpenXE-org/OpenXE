<?php
/*
 * SPDX-FileCopyrightText: 2026 Creatronics GmbH
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 *
 * ZUGFeRD-Erzeugung fuer Rechnungen (Profil EN 16931).
 *
 * Baut aus neutral aufbereiteten Rechnungsdaten das CII-XML und fuehrt es mit
 * einer vorhandenen PDF zu einer ZUGFeRD-PDF (PDF/A-3) zusammen. Die Klasse
 * kennt keine Datenbank; das Laden der Daten erledigt RechnungPDFCustom.
 */

use horstoeko\zugferd\ZugferdDocumentBuilder;
use horstoeko\zugferd\ZugferdDocumentPdfMerger;
use horstoeko\zugferd\ZugferdProfiles;

class ZugferdRechnung
{
  /** Rechnungsart nach UNTDID 1001 */
  const TYP_RECHNUNG = '380';

  /** Mehrwertsteuer-Kategorien nach UNTDID 5305 */
  const KATEGORIE_STANDARD = 'S';
  const KATEGORIE_BEFREIT = 'E';
  const KATEGORIE_IG_LIEFERUNG = 'K';
  const KATEGORIE_REVERSE_CHARGE = 'AE';
  const KATEGORIE_EXPORT = 'G';

  /** Einheiten aus OpenXE (klein geschrieben) auf UN/ECE Rec. 20 */
  private static $einheiten = [
    'stk' => 'H87', 'stück' => 'H87', 'stueck' => 'H87', 'st' => 'H87', 'st.' => 'H87',
    'h' => 'HUR', 'std' => 'HUR', 'std.' => 'HUR', 'stunde' => 'HUR', 'stunden' => 'HUR',
    'min' => 'MIN', 'minute' => 'MIN', 'minuten' => 'MIN',
    'tag' => 'DAY', 'tage' => 'DAY', 'monat' => 'MON', 'monate' => 'MON', 'jahr' => 'ANN',
    'kg' => 'KGM', 'g' => 'GRM', 'm' => 'MTR', 'km' => 'KMT', 'l' => 'LTR',
    'pauschal' => 'LS', 'pausch.' => 'LS', 'psch' => 'LS', 'psch.' => 'LS',
  ];

  /**
   * Baut das XML.
   *
   * Erwartete Schluessel in $daten:
   *   nummer, datum (Y-m-d), lieferdatum (Y-m-d|''), waehrung, bestellnummer, kundennummer,
   *   zahlungsziel_tage, ust_befreit (0 Inland, 1 EU, 2 Export), summe_brutto,
   *   eu_kategorie (K oder AE),
   *   verkaeufer: name, strasse, plz, ort, land, ustid, steuernummer, email, iban, bic, kontoinhaber
   *   kaeufer: name, abteilung, unterabteilung, strasse, adresszusatz, plz, ort, land, ustid, email
   *   positionen[]: nummer, bezeichnung, beschreibung, menge, einheit, netto_einzeln, netto_gesamt,
   *                 steuersatz, steuertext
   */
  public static function xml(array $daten): string
  {
    if(empty($daten['nummer'])) {
      throw new InvalidArgumentException('Rechnung hat keine Belegnummer (Entwurf).');
    }
    if(empty($daten['positionen'])) {
      throw new InvalidArgumentException('Rechnung hat keine Positionen.');
    }

    $v = $daten['verkaeufer'];
    $k = $daten['kaeufer'];
    $waehrung = !empty($daten['waehrung']) ? strtoupper($daten['waehrung']) : 'EUR';
    $datum = new DateTime($daten['datum']);

    $doc = ZugferdDocumentBuilder::CreateNew(ZugferdProfiles::PROFILE_EN16931);
    $doc->setDocumentInformation((string)$daten['nummer'], self::TYP_RECHNUNG, $datum, $waehrung);

    // Verkaeufer
    $doc->setDocumentSeller($v['name']);
    if(!empty($v['ustid'])) {
      $doc->addDocumentSellerTaxRegistration('VA', self::kompakt($v['ustid']));
    }
    if(!empty($v['steuernummer'])) {
      $doc->addDocumentSellerTaxRegistration('FC', trim($v['steuernummer']));
    }
    $doc->setDocumentSellerAddress($v['strasse'] ?: null, null, null, $v['plz'] ?: null, $v['ort'] ?: null, self::land($v['land']));
    if(!empty($v['email'])) {
      $doc->setDocumentSellerCommunication('EM', $v['email']);
    }

    // Kaeufer
    $doc->setDocumentBuyer($k['name'], !empty($daten['kundennummer']) ? (string)$daten['kundennummer'] : null);
    $zeilen = array_values(array_filter([$k['abteilung'] ?? '', $k['unterabteilung'] ?? '', $k['strasse'] ?? '', $k['adresszusatz'] ?? ''], 'strlen'));
    $doc->setDocumentBuyerAddress($zeilen[0] ?? null, $zeilen[1] ?? null, isset($zeilen[2]) ? implode(', ', array_slice($zeilen, 2)) : null,
      $k['plz'] ?: null, $k['ort'] ?: null, self::land($k['land']));
    if(!empty($k['ustid'])) {
      $doc->addDocumentBuyerTaxRegistration('VA', self::kompakt($k['ustid']));
    }
    if(!empty($k['email'])) {
      $doc->setDocumentBuyerCommunication('EM', $k['email']);
    }
    if(!empty($daten['bestellnummer'])) {
      $doc->setDocumentBuyerOrderReferencedDocument((string)$daten['bestellnummer']);
    }

    // Leistungsdatum; bei EU-Lieferung braucht es zusaetzlich das Lieferland
    $leistung = !empty($daten['lieferdatum']) ? new DateTime($daten['lieferdatum']) : $datum;
    $doc->setDocumentSupplyChainEvent($leistung);

    // Positionen und Steueraufschluesselung
    $steuern = [];
    $summeNetto = 0.0;
    $nr = 0;
    foreach($daten['positionen'] as $p) {
      $nr++;
      $kategorie = self::kategorie((float)$p['steuersatz'], (int)$daten['ust_befreit'], $daten['eu_kategorie'] ?? self::KATEGORIE_IG_LIEFERUNG);
      $menge = (float)$p['menge'];
      $netto = round((float)$p['netto_gesamt'], 2);
      $einzel = $menge != 0.0 ? (float)$p['netto_einzeln'] : $netto;
      if($einzel < 0) {
        // EN 16931 erlaubt keinen negativen Preis, das Vorzeichen geht in die Menge
        $einzel = -$einzel;
        $menge = -$menge;
      }

      $doc->addNewPosition((string)$nr);
      $doc->setDocumentPositionProductDetails(
        self::text($p['bezeichnung']) ?: 'Position '.$nr,
        self::text($p['beschreibung']) ?: null,
        !empty($p['nummer']) ? (string)$p['nummer'] : null
      );
      $doc->setDocumentPositionNetPrice(round($einzel, 4));
      $doc->setDocumentPositionQuantity($menge, self::einheit($p['einheit'] ?? ''));
      $doc->addDocumentPositionTax($kategorie, 'VAT', (float)$p['steuersatz']);
      $doc->setDocumentPositionLineSummation($netto);

      $schluessel = $kategorie.'|'.number_format((float)$p['steuersatz'], 2, '.', '');
      if(!isset($steuern[$schluessel])) {
        $steuern[$schluessel] = ['kategorie' => $kategorie, 'satz' => (float)$p['steuersatz'], 'basis' => 0.0, 'text' => self::text($p['steuertext'] ?? '')];
      }
      $steuern[$schluessel]['basis'] += $netto;
      $summeNetto += $netto;
    }

    $summeSteuer = 0.0;
    foreach($steuern as $s) {
      $basis = round($s['basis'], 2);
      $betrag = round($basis * $s['satz'] / 100, 2);
      $summeSteuer += $betrag;
      [$grund, $grundCode] = self::befreiungsgrund($s['kategorie'], $s['text']);
      $doc->addDocumentTax($s['kategorie'], 'VAT', $basis, $betrag, $s['satz'], $grund, $grundCode);
      if(in_array($s['kategorie'], [self::KATEGORIE_IG_LIEFERUNG, self::KATEGORIE_EXPORT], true)) {
        $doc->setDocumentShipTo($k['name']);
        $doc->setDocumentShipToAddress($zeilen[0] ?? null, null, null, $k['plz'] ?: null, $k['ort'] ?: null, self::land($k['land']));
      }
    }

    // Zahlung
    $faellig = null;
    if(!empty($daten['zahlungsziel_tage'])) {
      $faellig = (clone $datum)->modify('+'.(int)$daten['zahlungsziel_tage'].' days');
    }
    if(!empty($v['iban'])) {
      $doc->addDocumentPaymentMeanToCreditTransfer(self::kompakt($v['iban']), $v['kontoinhaber'] ?: $v['name'], null,
        !empty($v['bic']) ? self::kompakt($v['bic']) : null, (string)$daten['nummer']);
    }
    if($faellig !== null) {
      $doc->addDocumentPaymentTerm('Zahlbar ohne Abzug bis '.$faellig->format('d.m.Y'), $faellig);
    }

    // Summen: Abweichung zur OpenXE-Gesamtsumme (Rundung pro Position) als Rundungsbetrag
    $summeNetto = round($summeNetto, 2);
    $summeSteuer = round($summeSteuer, 2);
    $gesamt = round($summeNetto + $summeSteuer, 2);
    $zahlbar = isset($daten['summe_brutto']) ? round((float)$daten['summe_brutto'], 2) : $gesamt;
    $rundung = round($zahlbar - $gesamt, 2);
    if(abs($rundung) > 0.05) {
      throw new RuntimeException(sprintf('Summe XML %.2f weicht von Rechnungssumme %.2f ab.', $gesamt, $zahlbar));
    }
    $doc->setDocumentSummation($gesamt, $zahlbar, $summeNetto, 0.0, 0.0, $summeNetto, $summeSteuer, $rundung != 0.0 ? $rundung : null);

    return $doc->getContent();
  }

  /** Bettet das XML in die PDF ein und liefert die ZUGFeRD-PDF als String. */
  public static function pdf(string $xml, string $pdf): string
  {
    $merger = new ZugferdDocumentPdfMerger($xml, $pdf);
    // gleiche Eingabe ergibt gleiche Datei, damit das PDF-Archiv keine Doppel anlegt
    $merger->setDeterministicModeEnabled(true);
    return $merger->generateDocument()->downloadString();
  }

  public static function kategorie(float $satz, int $ustBefreit, string $euKategorie): string
  {
    if($satz > 0) {
      return self::KATEGORIE_STANDARD;
    }
    if($ustBefreit === 1) {
      return $euKategorie === self::KATEGORIE_REVERSE_CHARGE ? self::KATEGORIE_REVERSE_CHARGE : self::KATEGORIE_IG_LIEFERUNG;
    }
    if($ustBefreit === 2) {
      return self::KATEGORIE_EXPORT;
    }
    return self::KATEGORIE_BEFREIT;
  }

  private static function befreiungsgrund(string $kategorie, string $text): array
  {
    switch($kategorie) {
      case self::KATEGORIE_IG_LIEFERUNG:
        return [$text ?: 'Steuerfreie innergemeinschaftliche Lieferung', 'VATEX-EU-IC'];
      case self::KATEGORIE_REVERSE_CHARGE:
        return [$text ?: 'Steuerschuldnerschaft des Leistungsempfaengers', 'VATEX-EU-AE'];
      case self::KATEGORIE_EXPORT:
        return [$text ?: 'Steuerfreie Ausfuhrlieferung', 'VATEX-EU-G'];
      case self::KATEGORIE_BEFREIT:
        return [$text ?: 'Steuerbefreit', null];
    }
    return [null, null];
  }

  public static function einheit(string $einheit): string
  {
    $einheit = mb_strtolower(trim(self::text($einheit)));
    if($einheit === '') {
      return 'H87';
    }
    if(isset(self::$einheiten[$einheit])) {
      return self::$einheiten[$einheit];
    }
    // bereits ein UN/ECE-Code
    if(preg_match('/^[A-Z0-9]{2,3}$/', strtoupper($einheit)) && strlen($einheit) === 3) {
      return strtoupper($einheit);
    }
    return 'H87';
  }

  /** HTML und Entities aus OpenXE-Texten entfernen. */
  public static function text($text): string
  {
    $text = (string)$text;
    if($text !== '' && !mb_check_encoding($text, 'UTF-8')) {
      $text = mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
    }
    $text = preg_replace('#<br\s*/?>|</p>|</li>|</tr>#i', "\n", $text);
    $text = html_entity_decode(strip_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $text = str_replace("\xC2\xA0", ' ', $text);
    $text = preg_replace("/[ \t]+/", ' ', $text);
    $text = preg_replace("/\n\s*\n+/", "\n", $text);
    return trim($text);
  }

  private static function kompakt($wert): string
  {
    return strtoupper(preg_replace('/\s+/', '', (string)$wert));
  }

  private static function land($land): string
  {
    $land = strtoupper(trim((string)$land));
    return $land !== '' ? $land : 'DE';
  }
}

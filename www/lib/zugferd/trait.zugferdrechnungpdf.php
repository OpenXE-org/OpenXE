<?php
/*
 * SPDX-FileCopyrightText: 2026 Creatronics GmbH
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 *
 * Rechnungs-PDF als ZUGFeRD (EN 16931).
 *
 * RechnungPDF bindet diesen Trait ein (www/lib/dokumente/class.rechnung.php),
 * damit class.rechnung_custom.php fuer eigene Anpassungen frei bleibt. Eine
 * eigene RechnungPDFCustom erbt das Verhalten; ueberschreibt sie Output(),
 * displayDocument(), inlineDocument(), displayTMP() oder getArchivByID(), muss
 * sie dort parent:: aufrufen.
 *
 * Neu erzeugte PDFs (Anzeige, Download, Druck, Archiv, Mailanhang) enden in
 * Output(); dort wird das XML erzeugt und eingebettet. PDFs aus dem Archiv
 * (schreibgeschuetzte Rechnungen) bekommen es beim Ausliefern. Klappt das
 * nicht, geht die normale PDF raus und der Fehler ins Log.
 *
 * Ein- und ausschalten unter Einstellungen > Firmendaten > Belege
 * ("ZUGFeRD Rechnung", darunter "mit Logo" und "Logo-Hoehe"); ohne Haken
 * bleibt alles wie bisher.
 *
 * Das Flag rechnung.xmlrechnung darf nicht gesetzt sein, sonst erzeugt OpenXE
 * gar keine PDF, sondern nur das Smarty-XML.
 */

require_once dirname(__DIR__, 3).'/vendor/autoload.php';
require_once __DIR__.'/class.zugferdrechnung.php';

trait ZugferdRechnungPdf
{
  /** @var array fertige ZUGFeRD-PDFs je md5 der Eingabe, damit mehrfache Output()-Aufrufe nicht neu rechnen */
  private $zugferdFertig = [];

  /** @var string|null|false XML der Rechnung; false = keins moeglich */
  private $zugferdXml;

  /** @var int|null Pufferebene des Archivwegs (siehe zugferdArchivStart) */
  private $zugferdPufferEbene;

  public function Output($name = '', $dest = '')
  {
    $dest = strtoupper((string)$dest);
    if($dest === '') {
      $dest = $name === '' ? 'I' : 'F';
    }
    if($name === '') {
      $name = 'doc.pdf';
    }

    $this->zugferdProtokoll('Output('.$dest.')');
    $pdf = $this->zugferd(parent::Output('', 'S'));

    switch($dest) {
      case 'I':
      case 'D':
        if(PHP_SAPI !== 'cli') {
          header('Content-Type: '.($dest === 'I' ? 'application/pdf' : 'application/x-download'));
          header('Content-Disposition: '.($dest === 'I' ? 'inline' : 'attachment').'; filename="'.$name.'"');
          header('Cache-Control: private, max-age=0, must-revalidate');
          header('Pragma: public');
        }
        echo $pdf;
        return '';
      case 'F':
        if(file_put_contents($name, $pdf) === false) {
          $this->Error('Unable to create output file: '.$name);
        }
        return '';
      case 'S':
        return $pdf;
    }
    $this->Error('Incorrect output destination: '.$dest);
    return '';
  }

  /*
   * Schreibgeschuetzte Rechnungen liefert OpenXE aus dem PDF-Archiv aus, ohne
   * Output() aufzurufen. Archivierte PDFs von vor dieser Klasse haben kein XML;
   * es wird beim Ausliefern eingebettet, die Archivdatei bleibt unveraendert.
   */

  public function displayDocument($from_archiv = false)
  {
    $this->zugferdProtokoll('displayDocument(archiv='.($from_archiv ? 1 : 0).')');
    $this->zugferdArchivStart($from_archiv);
    parent::displayDocument($from_archiv);
    $this->zugferdArchivEnde();
  }

  public function inlineDocument($from_archiv = false)
  {
    $this->zugferdProtokoll('inlineDocument(archiv='.($from_archiv ? 1 : 0).')');
    $this->zugferdArchivStart($from_archiv);
    parent::inlineDocument($from_archiv);
    $this->zugferdArchivEnde();
  }

  /**
   * Die Elternmethoden geben die Archivdatei per echo aus und beenden das Skript
   * (ExitXentral, Datenbank danach geschlossen). Darum: XML vorher erzeugen,
   * Ausgabe puffern und in einer Shutdown-Funktion einbetten. Ein Ausgabe-Callback
   * geht nicht, weil er erst nach den Destruktoren laeuft und FPDI dann abbricht.
   */
  private function zugferdArchivStart($from_archiv): void
  {
    $this->zugferdPufferEbene = null;
    if(!$from_archiv) {
      return;
    }
    $this->zugferdXml();
    ob_start();
    $this->zugferdPufferEbene = ob_get_level();
    register_shutdown_function([$this, 'zugferdArchivEnde']);
  }

  public function zugferdArchivEnde(): void
  {
    $ebene = $this->zugferdPufferEbene;
    if($ebene === null) {
      return;
    }
    $this->zugferdPufferEbene = null;
    while(ob_get_level() > $ebene) {
      ob_end_flush();
    }
    if(ob_get_level() !== $ebene) {
      return;
    }
    $ausgabe = ob_get_clean();
    if(strncmp($ausgabe, '%PDF', 4) === 0) {
      try {
        $ausgabe = $this->zugferd($ausgabe);
      }
      catch(Throwable $e) {
      }
    }
    echo $ausgabe;
  }

  public function displayTMP($from_archiv = false)
  {
    $datei = parent::displayTMP($from_archiv);
    $this->zugferdProtokoll('displayTMP(archiv='.($from_archiv ? 1 : 0).', aus Archiv='.($this->ausarchiv ? 1 : 0).')');
    if($this->ausarchiv && is_file($datei)) {
      $inhalt = file_get_contents($datei);
      $neu = $this->zugferd($inhalt);
      if($neu !== $inhalt) {
        file_put_contents($datei, $neu);
      }
    }
    return $datei;
  }

  public function getArchivByID($id)
  {
    $ergebnis = parent::getArchivByID($id);
    if(!empty($ergebnis['file'])) {
      $eintrag = $this->app->DB->SelectRow(sprintf('SELECT table_name, table_id FROM pdfarchiv WHERE id = %d LIMIT 1', (int)$id));
      if(!empty($eintrag) && $eintrag['table_name'] === 'rechnung') {
        $this->doctypeid = (int)$eintrag['table_id'];
        $ergebnis['file'] = $this->zugferd($ergebnis['file']);
      }
    }
    return $ergebnis;
  }

  /** Liefert die PDF mit eingebettetem XML oder bei Problemen unveraendert zurueck. */
  private function zugferd(string $pdf): string
  {
    // schon eine ZUGFeRD-PDF (z. B. aus dem Archiv nach Einfuehrung dieser Klasse)
    if(strpos($pdf, 'factur-x.xml') !== false || strpos($pdf, 'zugferd-invoice.xml') !== false) {
      $this->zugferdProtokoll('PDF hat schon XML');
      return $pdf;
    }
    $schluessel = md5($pdf);
    if(isset($this->zugferdFertig[$schluessel])) {
      return $this->zugferdFertig[$schluessel];
    }
    $xml = $this->zugferdXml();
    if($xml === null) {
      return $pdf;
    }
    try {
      $this->zugferdFertig[$schluessel] = ZugferdRechnung::pdf($xml, $this->zugferdLogo($pdf));
      $this->zugferdProtokoll('XML eingebettet');
      return $this->zugferdFertig[$schluessel];
    }
    catch(Throwable $e) {
      $this->zugferdFehler($e);
      return $pdf;
    }
  }

  /**
   * ZUGFeRD-Logo auf Seite 1, nur wenn das XML eingebettet wird. Bild:
   * www/lib/zugferd/zugferd-logo.png. Standard: waagerecht mittig, 3,5 mm hoch,
   * senkrecht mittig auf dem Barcode (Code39 ist 3 mm hoch). Abweichend ueber
   * die Firmendaten-Werte zugferd_logo_x, zugferd_logo_y, zugferd_logo_hoehe (mm);
   * Schalter zugferd_logo (Firmendaten > Belege). Fehler beim Stempeln: PDF ohne Logo.
   */
  private function zugferdLogo(string $pdf): string
  {
    $erp = $this->app->erp;
    $bild = __DIR__.'/zugferd-logo.png';
    if((string)$erp->Firmendaten('zugferd_logo') !== '1' || !is_file($bild)) {
      return $pdf;
    }
    $wert = function($name) use ($erp) {
      $v = $erp->Firmendaten($name);
      return ($v === null || $v === '') ? null : (float)str_replace(',', '.', $v);
    };
    $hoehe = $wert('zugferd_logo_hoehe');
    if($hoehe === null || $hoehe < 1 || $hoehe > 30) {
      $hoehe = 3.5;
    }
    $y = $wert('zugferd_logo_y');
    if($y === null) {
      $y = !empty($this->barcode_y) ? (float)$this->barcode_y + 1.5 - $hoehe / 2 : 10.0;
    }
    try {
      $pdf = ZugferdRechnung::stempel($pdf, $bild, $wert('zugferd_logo_x'), $y, $hoehe);
      $this->zugferdProtokoll('Logo gestempelt');
    }
    catch(Throwable $e) {
      $this->zugferdProtokoll('Logo nicht gestempelt: '.$e->getMessage());
    }
    return $pdf;
  }

  /** XML der Rechnung, einmal je Objekt; null bei Entwurf, XML-Rechnung oder Fehler. */
  private function zugferdXml(): ?string
  {
    if($this->zugferdXml !== null) {
      return $this->zugferdXml === false ? null : $this->zugferdXml;
    }
    $this->zugferdXml = false;
    // Schalter unter Einstellungen > Firmendaten > Belege
    if((string)$this->app->erp->Firmendaten('zugferd_rechnung') !== '1') {
      return null;
    }
    $id = (int)$this->doctypeid;
    if($id <= 0) {
      $this->zugferdProtokoll('uebersprungen: keine Rechnungs-ID');
      return null;
    }
    $kopf = $this->app->DB->SelectRow("SELECT * FROM rechnung WHERE id = $id LIMIT 1");
    // Entwurf ohne Nummer oder reine XML-Rechnung: nichts einbetten
    if(empty($kopf) || empty($kopf['belegnr']) || (string)$kopf['belegnr'] === '0' || !empty($kopf['xmlrechnung'])) {
      $this->zugferdProtokoll('uebersprungen: '.(empty($kopf) ? 'Rechnung nicht gefunden' : (!empty($kopf['xmlrechnung']) ? 'xmlrechnung gesetzt' : 'Entwurf ohne Belegnummer')));
      return null;
    }
    try {
      $this->zugferdXml = ZugferdRechnung::xml($this->zugferdDaten($kopf));
      return $this->zugferdXml;
    }
    catch(Throwable $e) {
      $this->zugferdFehler($e, $kopf['belegnr']);
      return null;
    }
  }

  private function zugferdFehler(Throwable $e, $belegnr = null): void
  {
    $meldung = 'ZUGFeRD fuer Rechnung '.($belegnr ?? $this->doctypeid).' nicht erzeugt: '.$e->getMessage();
    $this->zugferdProtokoll('FEHLER '.get_class($e).': '.$e->getMessage().' ('.basename($e->getFile()).':'.$e->getLine().')');
    try {
      $this->app->Container->get('Logger')->error($meldung);
    }
    catch(Throwable $ignoriert) {
      error_log($meldung);
    }
  }

  /**
   * Fehlersuche: eine Zeile je Schritt nach userdata/zugferd.log (nur Rechnungs-ID,
   * keine Kundendaten). Abschalten mit der Konstante ZUGFERD_PROTOKOLL = false.
   */
  private function zugferdProtokoll(string $text): void
  {
    if(defined('ZUGFERD_PROTOKOLL') && !ZUGFERD_PROTOKOLL) {
      return;
    }
    try {
      $datei = rtrim((string)$this->app->Conf->WFuserdata, '/').'/zugferd.log';
      @file_put_contents($datei, date('Y-m-d H:i:s').' Rechnung-ID '.(int)$this->doctypeid.': '.$text."\n", FILE_APPEND);
    }
    catch(Throwable $e) {
    }
  }

  /** Rechnungsdaten aus OpenXE fuer ZugferdRechnung::xml() aufbereiten. */
  private function zugferdDaten(array $kopf): array
  {
    $id = (int)$kopf['id'];
    $erp = $this->app->erp;

    // GetSteuerPosition liefert immer den nominalen Satz; ob Steuer anfaellt,
    // entscheidet OpenXE (wie in RechnungPDF) ueber RechnungMitUmsatzsteuer()
    $mitSteuer = (bool)$erp->RechnungMitUmsatzsteuer($id);
    $vermerk = $mitSteuer ? '' : $this->zugferdSteuervermerk($kopf);

    $positionen = [];
    $rows = $this->app->DB->SelectArr("SELECT * FROM rechnung_position WHERE rechnung = $id ORDER BY sort");
    foreach((array)$rows as $row) {
      // ausgeblendete Unterpositionen ohne Betrag erscheinen auch in der PDF nicht
      if(!empty($row['ausblenden_im_pdf']) && round((float)$row['umsatz_netto_gesamt'], 2) == 0.0) {
        continue;
      }
      $steuersatz = null;
      $steuertext = null;
      $erloes = null;
      $erp->GetSteuerPosition('rechnung', $row['id'], $steuersatz, $steuertext, $erloes);
      if(!$mitSteuer || ($row['umsatzsteuer'] ?? '') === 'befreit') {
        $steuersatz = 0;
        $steuertext = $vermerk;
      }
      $positionen[] = [
        'nummer' => $row['nummer'],
        'bezeichnung' => $row['bezeichnung'],
        'beschreibung' => empty($kopf['ohne_artikeltext']) ? $row['beschreibung'] : '',
        'menge' => $row['menge'],
        'einheit' => $row['einheit'],
        'netto_einzeln' => $row['umsatz_netto_einzeln'],
        'netto_gesamt' => $row['umsatz_netto_gesamt'],
        'steuersatz' => (float)$steuersatz,
        'steuertext' => $steuertext,
      ];
    }

    $kaeuferEmail = $kopf['email'];
    if(empty($kaeuferEmail)) {
      $kaeuferEmail = $this->app->DB->Select('SELECT email FROM adresse WHERE id = '.(int)$kopf['adresse'].' LIMIT 1');
    }

    [$iban, $bic] = $this->zugferdBank();

    return [
      'nummer' => $kopf['belegnr'],
      'datum' => $kopf['datum'],
      'lieferdatum' => ($kopf['lieferdatum'] && $kopf['lieferdatum'] !== '0000-00-00') ? $kopf['lieferdatum'] : '',
      'waehrung' => $kopf['waehrung'],
      'bestellnummer' => $kopf['ihrebestellnummer'],
      'kundennummer' => $kopf['kundennummer'],
      'zahlungsziel_tage' => (int)$kopf['zahlungszieltage'],
      'ust_befreit' => (int)$kopf['ust_befreit'],
      'summe_brutto' => $kopf['soll'],
      'eu_kategorie' => (string)$erp->Firmendaten('zugferd_eu_kategorie') ?: ZugferdRechnung::KATEGORIE_IG_LIEFERUNG,
      'verkaeufer' => [
        'name' => $erp->Firmendaten('name'),
        'strasse' => $erp->Firmendaten('strasse'),
        'plz' => $erp->Firmendaten('plz'),
        'ort' => $erp->Firmendaten('ort'),
        'land' => $erp->Firmendaten('land'),
        // im Feld "steuernummer" steht in OpenXE die USt-IdNr. (Maske: "USTID")
        'ustid' => $this->zugferdIstUstId($erp->Firmendaten('steuernummer')) ? $erp->Firmendaten('steuernummer') : '',
        'steuernummer' => $erp->Firmendaten('zugferd_steuernummer')
          ?: ($this->zugferdIstUstId($erp->Firmendaten('steuernummer')) ? '' : $erp->Firmendaten('steuernummer')),
        'email' => $erp->Firmendaten('email'),
        'iban' => $iban,
        'bic' => $bic,
        'kontoinhaber' => $erp->Firmendaten('zugferd_kontoinhaber'),
      ],
      'kaeufer' => [
        'name' => $kopf['name'],
        'abteilung' => $kopf['abteilung'],
        'unterabteilung' => $kopf['unterabteilung'],
        'strasse' => $kopf['strasse'],
        'adresszusatz' => $kopf['adresszusatz'],
        'plz' => $kopf['plz'],
        'ort' => $kopf['ort'],
        'land' => $kopf['land'],
        'ustid' => $kopf['ustid'],
        'email' => $kaeuferEmail,
      ],
      'positionen' => $positionen,
    ];
  }

  /**
   * OpenXE hat kein eigenes Bankfeld. Vorrang haben die Werte zugferd_iban und
   * zugferd_bic (firmendaten_werte), sonst wird die Fusszeile durchsucht.
   */
  private function zugferdBank(): array
  {
    $erp = $this->app->erp;
    $iban = (string)$erp->Firmendaten('zugferd_iban');
    $bic = (string)$erp->Firmendaten('zugferd_bic');
    if($iban === '') {
      $fusszeile = '';
      for($spalte = 0; $spalte <= 5; $spalte++) {
        for($zeile = 0; $zeile <= 5; $zeile++) {
          $fusszeile .= ' '.$erp->Firmendaten('footer_'.$spalte.'_'.$zeile);
        }
      }
      if(preg_match('/\b([A-Z]{2}\d{2}(?:\s?[A-Z0-9]{4}){3,7}(?:\s?[A-Z0-9]{1,3})?)\b/', $fusszeile, $treffer)) {
        $iban = $treffer[1];
      }
      if($bic === '' && preg_match('/(?:BIC|SWIFT)[^A-Z0-9]*([A-Z]{6}[A-Z0-9]{2}(?:[A-Z0-9]{3})?)\b/', $fusszeile, $treffer)) {
        $bic = $treffer[1];
      }
    }
    return [$iban, $bic];
  }

  /** Steuervermerk wie auf der PDF (Texte aus den Firmendaten, Platzhalter ersetzt). */
  private function zugferdSteuervermerk(array $kopf): string
  {
    $erp = $this->app->erp;
    $vermerk = '';
    if((int)$kopf['ust_befreit'] === 2) {
      $vermerk = (string)$erp->Beschriftung('export_lieferung_vermerk');
    }
    elseif((int)$kopf['ust_befreit'] === 1 && !empty($kopf['ustid'])) {
      $vermerk = (string)$erp->Beschriftung('eu_lieferung_vermerk');
    }
    $land = substr(strtoupper((string)$kopf['ustid']), 0, 2);
    if($land === '' || $land === strtoupper((string)$kopf['land']) || !$erp->IsEU($land)) {
      $land = $kopf['land'];
    }
    return str_replace(['{LAND}', '{USTID}'], [$land, $kopf['ustid']], $vermerk);
  }

  private function zugferdIstUstId($wert): bool
  {
    return (bool)preg_match('/^[A-Z]{2}[0-9A-Z]{8,12}$/', strtoupper(preg_replace('/\s+/', '', (string)$wert)));
  }
}

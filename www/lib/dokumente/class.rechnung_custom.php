<?php
/*
 * SPDX-FileCopyrightText: 2026 Creatronics GmbH
 *
 * SPDX-License-Identifier: LicenseRef-EGPL-3.1
 *
 * Rechnungs-PDF als ZUGFeRD (EN 16931).
 *
 * OpenXE nimmt diese Klasse automatisch statt RechnungPDF, sobald sie existiert
 * (xentral_autoloader.php). Alle Ausgabewege (Anzeige, Download, Druck, Archiv,
 * Mailanhang) enden in Output(); dort wird das XML erzeugt und eingebettet.
 * Klappt das nicht, geht die normale PDF raus und der Fehler ins Log.
 *
 * Das Flag rechnung.xmlrechnung darf nicht gesetzt sein, sonst erzeugt OpenXE
 * gar keine PDF, sondern nur das Smarty-XML.
 */

require_once dirname(__DIR__, 3).'/vendor/autoload.php';
require_once dirname(__DIR__).'/zugferd/class.zugferdrechnung.php';

class RechnungPDFCustom extends RechnungPDF
{
  /** @var string|null fertige ZUGFeRD-PDF, damit mehrfache Output()-Aufrufe nicht neu rechnen */
  private $zugferdPdf;

  public function Output($name = '', $dest = '')
  {
    $dest = strtoupper((string)$dest);
    if($dest === '') {
      $dest = $name === '' ? 'I' : 'F';
    }
    if($name === '') {
      $name = 'doc.pdf';
    }

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

  /** Liefert die ZUGFeRD-PDF oder bei Problemen unveraendert die normale PDF. */
  private function zugferd(string $pdf): string
  {
    if($this->zugferdPdf !== null) {
      return $this->zugferdPdf;
    }
    $id = (int)$this->doctypeid;
    if($id <= 0) {
      return $pdf;
    }
    $kopf = $this->app->DB->SelectRow("SELECT * FROM rechnung WHERE id = $id LIMIT 1");
    // Entwurf ohne Nummer oder reine XML-Rechnung: nichts einbetten
    if(empty($kopf) || empty($kopf['belegnr']) || (string)$kopf['belegnr'] === '0' || !empty($kopf['xmlrechnung'])) {
      return $pdf;
    }
    try {
      $xml = ZugferdRechnung::xml($this->zugferdDaten($kopf));
      $this->zugferdPdf = ZugferdRechnung::pdf($xml, $pdf);
      return $this->zugferdPdf;
    }
    catch(Throwable $e) {
      $meldung = 'ZUGFeRD fuer Rechnung '.$kopf['belegnr'].' nicht erzeugt: '.$e->getMessage();
      try {
        $this->app->Container->get('Logger')->error($meldung);
      }
      catch(Throwable $ignoriert) {
        error_log($meldung);
      }
      return $pdf;
    }
  }

  /** Rechnungsdaten aus OpenXE fuer ZugferdRechnung::xml() aufbereiten. */
  private function zugferdDaten(array $kopf): array
  {
    $id = (int)$kopf['id'];
    $erp = $this->app->erp;

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

  private function zugferdIstUstId($wert): bool
  {
    return (bool)preg_match('/^[A-Z]{2}[0-9A-Z]{8,12}$/', strtoupper(preg_replace('/\s+/', '', (string)$wert)));
  }
}

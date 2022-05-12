<?php
/*
 * SepaXmlCreator - by Thomas Schiffler.de
 * http://www.ThomasSchiffler.de/2013_09/code-schnipsel/sepa-sammeluberweisung-xml-datei-mit-php-erstellen
 *
 * Copyright (c) 2013 Thomas Schiffler (http://www.ThomasSchiffler.de
 * GPL (http://www.opensource.org/licenses/gpl-license.php) license.
 *
 */

class SepaBuchung{
  var $end2end, $iban, $bic, $kontoinhaber, $verwendungszweck, $amount;

  // Mandatsinformationen für Lastschriften
  var $mandatId, $mandatDatum, $mandatAenderung;

  function __construct() {
    $this->end2end = "NOTPROVIDED";
  }

  function setEnd2End($end2end) {
    $this->end2end = $this->normalizeString($end2end);
  }

  function setIban($iban) {
    $this->iban = str_replace(' ','',$iban);
  }

  function setBic($bic) {
    $this->bic = $bic;
  }

  function setName($name) {
    $this->kontoinhaber = $this->normalizeString($name);
  }

  function setVerwendungszweck($verwendungszweck) {
    $this->verwendungszweck = $this->normalizeString($verwendungszweck);
  }

  function setBetrag($betrag) {
    $this->amount = $betrag;
  }

  /*
   * Methode zum Setzen des Mandates - notwendig beim Generieren von Lastschriften. Wenn gewünscht kann
   * nur die Mandats-ID gesetzt werden, hierbei wird das aktuelle Tagesdatum als Datum der Mandatserteilung
   * genommen. Das Datum ist im Format (YYYY-mm-dd - bsp. 2013-11-02 zu übergeben)
   * 
   * @param String $id
   * @param String $mandatDatum
   * @param boolean $mandatAenderung - true wenn das Mandat seit letzer Erteilung geändert wurde
   */
  function setMandat($id, $mandatDatum = null, $mandatAenderung = true) {
    $this->mandatId = $id;
    $this->mandatAenderung = $mandatAenderung;

    if (!isset($mandatDatum)) {
      $this->mandatDatum = date('Y-m-d', time());	
    } else {
      $this->mandatDatum = $mandatDatum;
    }
  }

  function normalizeString($input) {
    // Only below characters can be used within the XML tags according the guideline.
    // a b c d e f g h i j k l m n o p q r s t u v w x y z
    // A B C D E F G H I J K L M N O P Q R S T U V W X Y Z
    // 0 1 2 3 4 5 6 7 8 9
    // / - ? : ( ) . , ‘ +
    // Space
    //
    // Create a normalized array and cleanup the string $XMLText for unexpected characters in names
    $normalizeChars = array(
        'Á'=>'A', 'À'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Å'=>'A', 'Ä'=>'Ae', 'Æ'=>'AE', 'Ç'=>'C',
        'É'=>'E', 'È'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Í'=>'I', 'Ì'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ð'=>'Eth',
        'Ñ'=>'N', 'Ó'=>'O', 'Ò'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O',
        'Ú'=>'U', 'Ù'=>'U', 'Û'=>'U', 'Ü'=>'Ue', 'Ý'=>'Y',

        'á'=>'a', 'à'=>'a', 'â'=>'a', 'ã'=>'a', 'å'=>'a', 'ä'=>'ae', 'æ'=>'ae', 'ç'=>'c',
        'é'=>'e', 'è'=>'e', 'ê'=>'e', 'ë'=>'e', 'í'=>'i', 'ì'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'eth',
        'ñ'=>'n', 'ó'=>'o', 'ò'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'oe', 'ø'=>'o',
        'ú'=>'u', 'ù'=>'u', 'û'=>'u', 'ü'=>'ue', 'ý'=>'y',

        'ß'=>'ss', 'þ'=>'thorn', 'ÿ'=>'y',

        '&'=>'u.', '@'=>'at', '#'=>'h', '$'=>'s', '%'=>'perc', '^'=>'-','*'=>'-'
        );

    $output = strtr($input, $normalizeChars);

    return $output;
  }
}

class SepaXmlCreator {
  var $buchungssaetze = array();

  var $accountName, $accountIban, $accountBic;
  var $offset = 0, $fixedDate;
  var $waehrung = "EUR";

  // Mode = 1 -> Überweisung / Mode = 2 -> Basislastschrift
  var $mode = 1;
  var $isFirst = true;

  // Gläubiger-ID
  var $glaeubigerId;

  // XML-Errors
  private $xmlerrors;

  function setDebitorValues($name, $iban, $bic) {
    trigger_error('Use setAccountValues($name, $iban, $bic) instead', E_USER_DEPRECATED);

    $this->setAccountValues($name, $iban, $bic);
  }

  function setAccountValues($name, $iban, $bic) {

    $this->accountName = $name;
    $this->accountIban = $iban;
    $this->accountBic = $bic;
  }

  function setGlaeubigerId($glaeubigerId) {
    $this->glaeubigerId = $glaeubigerId;
  }

  function setCurrency($currency) {
    $this->waehrung = $currency;
  }

  function addBuchung($buchungssatz) {
    array_push($this->buchungssaetze, $buchungssatz);
  }

  function setAusfuehrungOffset($offset) {
    $this->offset = $offset;
  }

  function setAusfuehrungDatum($datum) {
    $this->fixedDate = $datum;
  }

  function generateSammelueberweisungXml() {
    // Set Mode = 1 -> Sammelüberweisung
    $this->mode = 1;
    return $this->getGeneratedXml();
  }

  function generateBasislastschriftXml() {
    // Set Mode = 2 -> Basislastschrift
    $this->mode = 2;

    return $this->getGeneratedXml();
  }

  function setIsFolgelastschrift() {
    $this->isFirst = false;
  }

  function getGeneratedXml() {	
    $dom = new DOMDocument('1.0', 'utf-8');

    // Build Document-Root
    $document = $dom->createElement('Document');
    if ($this->mode == 2) {
      $document->setAttribute('xmlns', 'urn:iso:std:iso:20022:tech:xsd:pain.008.002.02');
      $document->setAttribute('xsi:schemaLocation', 'urn:iso:std:iso:20022:tech:xsd:pain.008.002.02 pain.008.002.02.xsd');
    } else {
      $document->setAttribute('xmlns', 'urn:iso:std:iso:20022:tech:xsd:pain.001.002.03');
      $document->setAttribute('xsi:schemaLocation', 'urn:iso:std:iso:20022:tech:xsd:pain.001.002.03 pain.001.002.03.xsd');
    }
    $document->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');		
      $dom->appendChild($document);

    // Build Content-Root
    if ($this->mode == 2) {
      $content = $dom->createElement('CstmrDrctDbtInitn');
    } else {
      $content = $dom->createElement('CstmrCdtTrfInitn');
    }

    $document->appendChild($content);

    // Build Header
    $header = $dom->createElement('GrpHdr');
    $content->appendChild($header);

    $creationTime = time();

    // Msg-ID
    $header->appendChild($dom->createElement('MsgId', $this->accountBic . '00' . date('YmdHis', $creationTime)));
    $header->appendChild($dom->createElement('CreDtTm', date('Y-m-d', $creationTime) . 'T' . date('H:i:s', $creationTime) . '.000Z'));
    $header->appendChild($dom->createElement('NbOfTxs', count($this->buchungssaetze)));
    $header->appendChild($initatorName = $dom->createElement('InitgPty'));
    $initatorName->appendChild($dom->createElement('Nm', $this->accountName));

    // PaymentInfo
    $paymentInfo = $dom->createElement('PmtInf');
    $content->appendChild($paymentInfo);

    $paymentInfo->appendChild($dom->createElement('PmtInfId', 'PMT-ID0-' . date('YmdHis', $creationTime)));
    switch ($this->mode) {
      case 2:
        // 2 = Basislastschrift
        $paymentInfo->appendChild($dom->createElement('PmtMtd', 'DD'));
        break;
      default:
        // Default / 1 = Überweisung
        $paymentInfo->appendChild($dom->createElement('PmtMtd', 'TRF'));
        break;
    }
    $paymentInfo->appendChild($dom->createElement('BtchBookg', 'false'));
    $paymentInfo->appendChild($dom->createElement('NbOfTxs', count($this->buchungssaetze)));
    $paymentInfo->appendChild($dom->createElement('CtrlSum', number_format($this->getUmsatzsumme(), 2, '.', '')));
    $paymentInfo->appendChild($tmp1 = $dom->createElement('PmtTpInf'));
    $tmp1->appendChild($tmp2 = $dom->createElement('SvcLvl'));
    $tmp2->appendChild($dom->createElement('Cd', 'SEPA'));

    if ($this->mode == 2) {
      // zusätzliche Attribute für Lastschriften
      $tmp1->appendChild($tmp2 = $dom->createElement('LclInstrm'));
      $tmp2->appendChild($dom->createElement('Cd', 'CORE'));
      if ($this->isFirst) {
        $tmp1->appendChild($dom->createElement('SeqTp', 'FRST'));
      } else {
        $tmp1->appendChild($dom->createElement('SeqTp', 'RCUR'));
      }
    }

    // Ausführungsdatum berechnen
    if (isset($this->fixedDate)) {
      $ausfuehrungsdatum = $this->fixedDate;
    } else {
      $ausfuehrungszeit = $creationTime;
      if ($this->offset > 0) {
        $ausfuehrungszeit = $ausfuehrungszeit + (24 * 3600 * $this->offset);
      }

      $ausfuehrungsdatum = date('Y-m-d', $ausfuehrungszeit);
    }

    if ($this->mode == 2) {
      $paymentInfo->appendChild($dom->createElement('ReqdColltnDt', $ausfuehrungsdatum));
    } else {
      $paymentInfo->appendChild($dom->createElement('ReqdExctnDt', $ausfuehrungsdatum));
    }

    // eigene Account-Daten Daten
    if ($this->mode == 2) {
      $paymentInfo->appendChild($tmp1 = $dom->createElement('Cdtr'));
    } else {
      $paymentInfo->appendChild($tmp1 = $dom->createElement('Dbtr'));
    }
    $tmp1->appendChild($dom->createElement('Nm', $this->accountName));

    if ($this->mode == 2) {
      $paymentInfo->appendChild($tmp1 = $dom->createElement('CdtrAcct'));
    } else {
      $paymentInfo->appendChild($tmp1 = $dom->createElement('DbtrAcct'));
    }

    $tmp1->appendChild($tmp2 = $dom->createElement('Id'));
    $tmp2->appendChild($dom->createElement('IBAN', $this->accountIban));

    if ($this->mode == 2) {
      $paymentInfo->appendChild($tmp1 = $dom->createElement('CdtrAgt'));
    } else {
      $paymentInfo->appendChild($tmp1 = $dom->createElement('DbtrAgt'));
    }

    $tmp1->appendChild($tmp2 = $dom->createElement('FinInstnId'));
    $tmp2->appendChild($dom->createElement('BIC', $this->accountBic));

    $paymentInfo->appendChild($dom->createElement('ChrgBr', 'SLEV'));

    if ($this->mode == 2) {
      $paymentInfo->appendChild($tmp1 = $dom->createElement('CdtrSchmeId'));
      $tmp1->appendChild($tmp2 = $dom->createElement('Id'));
      $tmp2->appendChild($tmp3 = $dom->createElement('PrvtId'));
      $tmp3->appendChild($tmp4 = $dom->createElement('Othr'));
      $tmp4->appendChild($dom->createElement('Id', $this->glaeubigerId));
      $tmp4->appendChild($tmp5 = $dom->createElement('SchmeNm'));
      $tmp5->appendChild($dom->createElement('Prtry', 'SEPA'));
    }

    // Buchungssätze hinzufügen
    foreach ($this->buchungssaetze as $buchungssatz) {
      if ($this->mode == 2) {
        $paymentInfo->appendChild($buchung = $dom->createElement('DrctDbtTxInf'));
      } else {
        $paymentInfo->appendChild($buchung = $dom->createElement('CdtTrfTxInf'));
      }

      // End2End setzen
      if (isset($buchungssatz->end2end)) {
        $buchung->appendChild($tmp1 = $dom->createElement('PmtId'));
        $tmp1->appendChild($dom->createElement('EndToEndId', $buchungssatz->end2end));
      }

      // Betrag
      if ($this->mode == 2) {
        $buchung->appendChild($tmp2 = $dom->createElement('InstdAmt', number_format($buchungssatz->amount, 2, '.', '')));
        $tmp2->setAttribute('Ccy', $this->waehrung);
      } else {
        $buchung->appendChild($tmp1 = $dom->createElement('Amt'));
        $tmp1->appendChild($tmp2 = $dom->createElement('InstdAmt', number_format($buchungssatz->amount, 2, '.', '')));
        $tmp2->setAttribute('Ccy', $this->waehrung);
      }

      if ($this->mode == 2) {
        // Lastschrift -> Mandatsinformationen
        $buchung->appendChild($tmp1 = $dom->createElement('DrctDbtTx'));
        $tmp1->appendChild($tmp2 = $dom->createElement('MndtRltdInf'));
        $tmp2->appendChild($dom->createElement('MndtId', $buchungssatz->mandatId));
        $tmp2->appendChild($dom->createElement('DtOfSgntr', $buchungssatz->mandatDatum));
        if ($buchungssatz->mandatAenderung) {
          $tmp2->appendChild($dom->createElement('AmdmntInd', 'true'));
        } else {
          $tmp2->appendChild($dom->createElement('AmdmntInd', 'false'));
        }
      }

      // Institut
      if ($this->mode == 2) {
        $buchung->appendChild($tmp1 = $dom->createElement('DbtrAgt'));
      } else {
        $buchung->appendChild($tmp1 = $dom->createElement('CdtrAgt'));
      }
      $tmp1->appendChild($tmp2 = $dom->createElement('FinInstnId'));
      $tmp2->appendChild($dom->createElement('BIC', $buchungssatz->bic));

      // Inhaber
      if ($this->mode == 2) {
        $buchung->appendChild($tmp1 = $dom->createElement('Dbtr'));
      } else {
        $buchung->appendChild($tmp1 = $dom->createElement('Cdtr'));
      }
      $tmp1->appendChild($dom->createElement('Nm', $buchungssatz->kontoinhaber));

      // IBAN
      if ($this->mode == 2) {
        $buchung->appendChild($tmp1 = $dom->createElement('DbtrAcct'));
      } else {
        $buchung->appendChild($tmp1 = $dom->createElement('CdtrAcct'));
      }
      $tmp1->appendChild($tmp2 = $dom->createElement('Id'));
      $tmp2->appendChild($dom->createElement('IBAN', $buchungssatz->iban));

      if ($this->mode == 2) {
        $buchung->appendChild($tmp1 = $dom->createElement('UltmtDbtr'));
        $tmp1->appendChild($dom->createElement('Nm', $buchungssatz->kontoinhaber));
      }

      // Verwendungszweck
      if (strlen($buchungssatz->verwendungszweck) > 0) {
        $buchung->appendChild($tmp1 = $dom->createElement('RmtInf'));
        $tmp1->appendChild($dom->createElement('Ustrd', $buchungssatz->verwendungszweck));
      }
    }

    // XML exportieren
    return $dom->saveXML();
  }

  function getUmsatzsumme() {
    $betrag = 0;

    foreach ($this->buchungssaetze as $buchungssatz) {
      $betrag = $betrag + $buchungssatz->amount;
    }

    return $betrag;
  }

  public function validateBasislastschriftXml($xmlfile) {
    return $this->validateXML($xmlfile, 'pain.008.002.02.xsd');
  }

  public function validateUeberweisungXml($xmlfile) {
    return $this->validateXML($xmlfile, 'pain.001.002.03.xsd');
  }

  protected function validateXML($xmlfile, $xsdfile) {
    libxml_use_internal_errors(true);

    $feed = new DOMDocument();

    $result = $feed->load($xmlfile);
    if ($result === false) {
      $this->xmlerrors[] = "Document is not well formed\n";
    }
    if (@($feed->schemaValidate(dirname(__FILE__) . '/' . $xsdfile))) {

      return true;
    } else {
      $this->xmlerrors[] = "! Document is not valid:\n";
      $errors = libxml_get_errors();

      foreach ($errors as $error) {
        $this->xmlerrors[] = "---\n" . sprintf("file: %s, line: %s, column: %s, level: %s, code: %s\nError: %s",
            basename($error->file),
            $error->line,
            $error->column,
            $error->level,
            $error->code,
            $error->message
            );
      }
    }
    return false;
  }

  public function printXmlErrors() {

    if (!is_array($this->xmlerrors)) return;
    foreach ($this->xmlerrors as $error) {
      echo $error;

    }
  }
}



?>

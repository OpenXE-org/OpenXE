<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php


class DokuArbeitszeit extends FPDFWAWISION
{
  public $name="Benedikt Sauter";	
  public $firma="Xentral ERP Software GmbH";
  public $strasse="Holzbachstrasse 4";
  public $plzOrt="86152 Augsburg";
  public $email="info@embedded-projects.net";

  public $kundenNr="1411228799";
  public $benutzername="info@embedded-projects.net";

  public $glID="DE81ZZZ00000404068";
  public $mandatsRef="PL118...";
  public $swift="DEUTDE...";
  public $iban="DE75....";

  public $musterlinie = "_____________________________________";

  public $ermaechText="Ich ermaechtige die ... , Zahlungen von meinem Konto mittels Lastschrift einzuziehen. Zugleich weise ich mein Kreditinstitut an, die von der ... auf mein Konto gezogenen Lastschriften einzuloesen.";

  public $hinweis="Hinweis: Ich kann (Wir können) innerhalb von acht Wochen, beginnend mit dem Belastungsdatum, die Erstattung des belasteten Betrages verlangen. Es gelten dabei die mit meinem (unserem) Kreditinstitut vereinbarten Bedingungen.";
  function __construct()
  {
    parent::__construct();		
  }
  function render()
  {

    $this->AddPage();

    $this->makeUeberschrift('SEPA-Lastschriftmandat');

    $this->makeLeerZeile();

    $this->makeListEintrag("Firma / Name",$this->firma);
    $this->makeListEintrag("Ansprechpartner",$this->name);
    $this->makeListEintrag("Strasse und Hausnummer", $this->strasse);
    $this->makeListEintrag("PLZ / Ort", $this->plzOrt);
    $this->makeListEintrag("E-Mail", $this->email);

    $this->makeLeerZeile();

    $this->makeListEintrag("Ihre Kundennummer:", $this->kundenNr);
    //$this->makeListEintrag("Ihr Benutzername", $this->benutzername);

    $this->makeLeerZeile();

    $this->makeText($this->ermaechText);
    $this->makeLeerZeile();
    $this->makeText($this->hinweis);

    $this->makeLeerZeile();

    $this->makeListEintrag("Gläubinger-Identifikationsnummer", $this->glID);
    $this->makeListEintrag("Mandatsreferenz:", $this->mandatsRef);

    $this->makeLeerZeile();

    $this->makeListEintrag("Swift BIC", $this->swift);
    $this->makeListEintrag("Bankkontnonummer - IBAN", $this->iban);
    $this->makeLeerZeile();

    $datum=date("d.m.Y");
    $this->makeListEintrag("Unterschrift / Stempel:", $this->musterlinie);
    $this->makeListEintrag("Ort, Datum:", $this->musterlinie);

  }

  // Funktionen

  function makeUeberschrift($in)
  {
    $this->SetFont('Arial','B',16);
    $this->Cell(180,10,$in ,1,1,'C');
  }

  function makeListEintrag($in1, $in2)
  {
    $this->SetFont('Arial','',12);
    $this->Cell(80,10,$in1,0,0);

    $this->SetFont('Arial','',12);
    $this->Cell(80,10,$in2,0,1);			
  }

  function makeText($in)
  {
    $this->SetFont('Arial','',10);
    $this->MultiCell(160,5,$in,0);			
  }

  function makeLeerZeile()
  {
    $this->SetFont('Arial','',10);
    $this->Cell(80,10,' ',0,2);
  }

  // getter

  function getName()
  {
    $out=$this->name;
    return $out;
  }

  // setter
  function setName($in)
  {
    $this->name=$in;
  }


}

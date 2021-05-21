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

class SepaMandat extends SuperFPDF
{
  public $name="Benedikt Sauter";	
  public $firma="Xentral ERP Software GmbH";
  public $strasse="Fuggerstrasse 11";
  public $plzOrt="86150 Augsburg";
  public $email="kontakt@xentral.com";

  public $kundenNr="1411228799";
  public $benutzername="info@embedded-projects.net";

  public $glID="DE81ZZZ00000404068";
  public $mandatsRef="PL118...";
  public $swift="";
  public $iban="";
  public $bank="";
  public $mandatsreferenzart="";

  public $firmensepa=0;

  public $musterlinie = "_____________________________________";

  public $ermaechText="Ich ermaechtige die ... , Zahlungen von meinem Konto mittels Lastschrift einzuziehen. Zugleich weise ich mein Kreditinstitut an, die von der ... auf mein Konto gezogenen Lastschriften einzuloesen.";

  public $hinweis="Hinweis: Ich kann (Wir können) innerhalb von acht Wochen, beginnend mit dem Belastungsdatum, die Erstattung des belasteten Betrages verlangen. Es gelten dabei die mit meinem (unserem) Kreditinstitut vereinbarten Bedingungen.";

  function __construct($app,$projekt="")
  {
    $this->app=$app;
    parent::__construct('P','mm','A4');
  }
  function render()
  {

    $this->AddPage();

    if($this->mandatsreferenzart=="wdh")
      $text=" (Wiederkehrende Zahlungen / Recurring Payments)";


    if($this->firmensepa)
    {
      $this->makeUeberschrift('SEPA-Firmenlastschrift-Mandat'.$text);
    }
    else
    {
      $this->makeUeberschrift('SEPA-Lastschriftmandat'.$text);
    }

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

    $this->makeListEintrag("Gläubiger-Identifikationsnummer", $this->glID);
    $this->makeListEintrag("Mandatsreferenz:", $this->mandatsRef);

    $this->makeLeerZeile();

    $this->makeListEintrag("Kreditinstitut", $this->bank);
    $this->makeListEintrag("Swift BIC", $this->swift);
    $this->makeListEintrag("Bankkontonummer - IBAN", $this->iban);
    $this->makeLeerZeile();

    $datum=date("d.m.Y");
    $this->makeListEintrag("Unterschrift / Stempel:", $this->musterlinie);
    $this->makeListEintrag("Ort, Datum:", $this->musterlinie);

  }

  // Funktionen

  function makeUeberschrift($in)
  {
    $this->SetFontClassic('Arial','B',12);
    $this->Cell(180,10,$this->app->erp->ReadyForPDF($in) ,1,1,'C');
  }

  function makeListEintrag($in1, $in2)
  {
    $this->SetFontClassic('Arial','',12);
    $this->Cell(80,10,$this->app->erp->ReadyForPDF($in1),0,0);

    $this->SetFontClassic('Arial','',12);
    $this->Cell(80,10,$this->app->erp->ReadyForPDF($in2),0,1);			
  }

  function makeText($in)
  {
    $this->SetFontClassic('Arial','',10);
    $this->MultiCell(160,5,$this->app->erp->ReadyForPDF($in),0);			
  }

  function makeLeerZeile()
  {
    $this->SetFontClassic('Arial','',10);
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

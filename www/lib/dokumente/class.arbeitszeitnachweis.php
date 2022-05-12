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
class ArbeitszeitnachweisPDF extends Dokumentenvorlage {
  public $doctype;
  public $siteindex;
  public $stunden;
  public $jahr;
  public $monat;
  public $zoll;
  public $startjahr;
  public $startmonat;
  public $aktmitarbeiter;
  public $aktzeit;
  
  function __construct($app,$projekt="")
  {
    $this->aktzeit = 0;
    $this->zoll = false;
    $this->app=$app;
    $this->siteindex = -1;
    $this->jahr = false;
    $this->monat = false;
    $this->stunden = false;
    //parent::Dokumentenvorlage();
    $this->doctype="arbeitszeitnachweis";
    $this->doctypeOrig="Arbeitszeitnachweis";
    parent::__construct($this->app,$projekt);
    $this->briefpapier="";
    $this->briefpapier2="";
    $this->logofile = "";
  } 

  function AddAbweichung($value)
  {
    $this->aktzeit += $value;
    $this->SetValue('aktzeiten',$this->aktzeit);
  }
  
  public function setValue($name , $value)
  {
    $this->{$name}[$this->siteindex] = $value;
    $this->doctypeOrig="Arbeitszeitnachweis für den Monat ".$this->monat[$this->siteindex]."/".$this->jahr[$this->siteindex]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Mitarbeiter: ".$this->name[$this->siteindex]." (ID: ".$this->mitarbeiternummer[$this->siteindex].")";
    $this->filename = "AN".$this->jahr[$this->siteindex].$this->monat[$this->siteindex]."MA".$this->mitarbeiternummer[$this->siteindex].".pdf";
  }
    
  public function setFName($value)
  {
    $this->filename = $value;
    
  }
  
  public function setDocT($value)
  {
    $this->doctypeOrig = $value;
  }

  public function renderDoctype() {

    //$this->Ln(1);

    if($this->doctype=="brief")
      $betreffszeile  = $this->app->erp->Firmendaten('brieftext');
    else
      $betreffszeile  = $this->app->erp->Firmendaten('betreffszeile');

    $this->SetY(0);//+$this->abstand_artikeltabelleoben); //Hoehe Box
    //$this->SetY(80+$this->abstand_artikeltabelleoben); //Hoehe Box
    $this->SetFont($this->GetFont(),'B',$betreffszeile);
    $this->SetY(20);//+$this->abstand_artikeltabelleoben); //Hoehe Box
    $this->SetY($this->GetY()+$this->abstand_betreffzeileoben);
    $this->Cell(25,6,$this->app->erp->ReadyForPDF($this->doctypeOrig));
    $this->SetY($this->GetY()-$this->abstand_betreffzeileoben);

    //$this->SetY($this->GetY()+$this->abstand_betreffzeileoben);
    $this->SetY($this->GetY()+$this->abstand_artikeltabelleoben); //Hoehe Box

    $this->SetY(30);//+$this->abstand_artikeltabelleoben); //Hoehe Box

    $dokumententext  = $this->app->erp->Firmendaten('dokumententext');
    $this->SetFont($this->GetFont(),'',$dokumententext);
    $this->SetY(30);//+$this->abstand_artikeltabelleoben); //Hoehe Box

    /*
       $this->MultiCell(90,5,"Mitarbeiter:              Benedikt Sauter","LR",'L');
       $this->MultiCell(90,5,"Termin:                    11.11.2013 - 17.12.2013","LR",'L');
       $this->MultiCell(95,5,"Von:                         08:00","L",'L');
       $this->SetY($this->GetY()-5); $this->SetX(95);
       $this->MultiCell(10,5,"18:00","R",'L');
       $this->MultiCell(90,5,"Reiseanlass:            Musik machen mit lustigen Leuten","LR",'L');
       $this->MultiCell(90,5,"Kunde (optional):     Mueller AG","BLR",'L');
     */
  }
  
  public function renderDocument() {
    // prepare page details
    SuperFPDF::__construct('P','mm','A4');
    
    
    $stundenwidth1 = 10;
    $stundenwidth2 = 20;
    $stundenwidth3 = 20;
    $stundenwidth4 = 20;
    $stundenwidth5 = 20;
    $stundenwidth6 = 20;
    $stundenwidth7 = 20;
    

    for($i = 0; $i <= $this->siteindex; $i++)
    {
      $stundenwidth8 = 0;
      $drawgesamt = false;
      if(!empty($this->startjahr) && !empty($this->startmonat) && ($this->startjahr != $this->jahr[$i] || $this->startmonat != $this->monat[$i]))
      {
        $drawgesamt = true;
        $stundenwidth8 = 50;
      }
      $this->AddPage();
      $this->SetDisplayMode("real","single");

      $this->SetMargins(15,50);
      $this->SetAutoPageBreak(true,40);
      $this->AliasNbPages('{nb}');

      //if($this->barcode!="")
      //{
      //  $y = $this->GetY();
      //  $this->Code39(155, $y+1, $this->barcode, 1, 5);
      //}

      $this->abstand_betreffzeileoben=0;
      $this->logofile = "";//$this->app->erp->GetTMP()."/".$this->app->Conf->WFdbname."_logo.jpg";
      $this->briefpapier="";
      $this->renderDoctype();
      if(isset($infofields))unset($infofields);
      $infofields[]=array("Firma",$this->firmenname);
      $infofields[]=array("Mitarbeiter",$this->name[$i]);
      $infofields[]=array("Personalnummer",$this->mitarbeiternummer[$i]);
      $infofields[]=array("Zeitraum",$this->monat[$i]."/".$this->jahr[$i]);
      
      $this->renderInfoBox($infofields);

      $this->renderArbeitszeitnachweisItems($i);
    
      $this->Ln(3);
    
      $cellhoehe   = 5;
      //position
      if($this->zoll)
      {      
        $this->Cell(55,$cellhoehe,'Monatssumme netto (Dauer): ',0,0,'R'); /*hier Summenberechnung anfügen*/
        $this->Cell(15,$cellhoehe,$this->stunden[$i],0,0,'R'); /*hier Summenberechnung anfügen*/
        $this->Cell(70,$cellhoehe,'Stunden',0,0,'L');
      } else {
        if(!isset($this->azsoll) || !isset($this->azsoll[$i]))$this->azsoll[$i] = '0:00';
        if(!isset($this->azist) || !isset($this->azist[$i]))$this->azist[$i] = '0:00';
        if(!isset($this->urlaub) || !isset($this->urlaub[$i]))$this->urlaub[$i] = '0:00';
        if(!isset($this->krank) || !isset($this->krank[$i]))$this->krank[$i] = '0:00';
        if(!isset($this->feiertag) || !isset($this->feiertag[$i]))$this->feiertag[$i] = '0:00';
        if(!isset($this->unburlaub) || !isset($this->unburlaub[$i]))$this->unburlaub[$i] = '0:00';
        if(!isset($this->abweichung) || !isset($this->abweichung[$i]))$this->abweichung[$i] = '0:00';
        if(!isset($this->fehltage) || !isset($this->fehltage[$i]))$this->fehltage[$i] = '0:00';
        $tabellenbeschriftung  = $this->app->erp->Firmendaten('tabellenbeschriftung');

        $this->SetFont($this->GetFont(),'B',$tabellenbeschriftung);
        
        $this->Cell($stundenwidth1, $cellhoehe, 'Az Soll',0,0,'L');
        $this->Cell($stundenwidth2, $cellhoehe, 'Az Ist',0,0,'R');
        $this->Cell($stundenwidth3, $cellhoehe, 'Urlaub',0,0,'R');
        $this->Cell($stundenwidth4, $cellhoehe, 'Krank',0,0,'R');
        $this->Cell($stundenwidth5, $cellhoehe, 'Feiertag',0,0,'R');
        $this->Cell($stundenwidth6, $cellhoehe, 'unb. Urlaub',0,0,'R');
        $this->Cell($stundenwidth6, $cellhoehe, 'Fehlzeit',0,0,'R');
        $this->SetFont($this->GetFont(),'',$tabellenbeschriftung);
        $this->Cell($stundenwidth7, $cellhoehe, '(angaben in Stunden, Minuten)',0,0,'L');
        
        $this->Ln(5);
        $this->Cell($stundenwidth1, $cellhoehe, $this->azsoll[$i],0,0,'L');
        $this->Cell($stundenwidth2, $cellhoehe, $this->azist[$i],0,0,'R');
        $this->Cell($stundenwidth3, $cellhoehe, $this->urlaub[$i],0,0,'R');
        $this->Cell($stundenwidth4, $cellhoehe, $this->krank[$i],0,0,'R');
        $this->Cell($stundenwidth5, $cellhoehe, $this->feiertag[$i],0,0,'R');
        $this->Cell($stundenwidth6, $cellhoehe, $this->unburlaub[$i],0,0,'R');
        $this->Cell($stundenwidth6, $cellhoehe, $this->fehltage[$i],0,0,'R');
        $this->SetFont($this->GetFont(),'B',$tabellenbeschriftung);
        $this->Ln(5);
        $this->Line($this->GetX(), $this->GetY(), 190, $this->GetY()); 
        $this->Cell($stundenwidth1, $cellhoehe, 'Summe (Arbeitszeit komplett)',0,0,'L');
        
        $this->Cell(195-$stundenwidth1-$stundenwidth7-$stundenwidth8, $cellhoehe,'Abweichung in Std/min.' ,0,0,'R');
        if($drawgesamt)
        {
          $this->Cell($stundenwidth8, $cellhoehe,'Abweichung in Std/min. gesamt' ,0,0,'R');
        }        
        
        $this->SetFont($this->GetFont(),'',$tabellenbeschriftung);
        $this->Ln(5);
        $this->Cell($stundenwidth1, $cellhoehe, $this->stunden[$i],0,0,'L');
        if(strpos($this->abweichung[$i],'-') !== false)$this->SetTextColor(255,0,0);
        $this->Cell(195-$stundenwidth1-$stundenwidth7-$stundenwidth8, $cellhoehe, $this->abweichung[$i],0,0,'R');
        if(strpos($this->abweichung[$i],'-') !== false)$this->SetTextColor(0,0,0);
        if($drawgesamt)
        {
          $abweichungvorz = '';
          $abweichung = $this->aktzeiten[$i];
          if($abweichung > 0)
          {
            $abweichungvorz = '+';
          }elseif($abweichung < 0)
          {
            $abweichungvorz = '-';
            $abweichung = -$abweichung;
          }
          
          if($abweichungvorz == '-')$this->SetTextColor(255,0,0);
          $this->Cell($stundenwidth8, $cellhoehe,$abweichungvorz.' '.floor($abweichung/60).':'.($abweichung % 60 < 10?'0':'').($abweichung % 60).($buchungsfehler?' ?':'') ,0,0,'R');
          if($abweichungvorz == '-')$this->SetTextColor(0,0,0);
        }  
        $this->Ln(5);
      }

      $this->Ln(5);
      //$this->Ln(5);	  
    
      $cellhoehe   = 7;
      //position
      $this->Cell(88,$cellhoehe,'Unterschrift Arbeitnehmer: ',1,0,'L');
      $this->Cell(88,$cellhoehe,'Unterschrift Arbeitgeber: ',1,0,'L');
      $this->Ln(7);
      $this->Cell(88,$cellhoehe,'Ort, Datum: ',1,0,'L');
      $this->Cell(88,$cellhoehe,'Ort, Datum: ',1,0,'L');
      $this->Ln(8);
      $this->Cell(176,$cellhoehe,'Legende: U: bezahlter Urlaub, N: Unbezahlter Urlaub, K: Krank, F: Feiertag, B: Brückentag, FT: Fehltag',1,0,'L');
    }
    $this->renderFooter();
  }

  
  function addArbeitszeit($add_date, $add_from, $add_to, $add_break, $add_total, $add_added, $add_comm1, $add_comm2, $sollminuten)
  {	
    $this->add_date[$this->siteindex][] = $add_date;
    $this->add_date_clear[$this->siteindex][] = date("j", strtotime($add_date));
    $this->add_from[$this->siteindex][] = $add_from;
    $this->add_to[$this->siteindex][] = $add_to;
    $this->add_break[$this->siteindex][] = $add_break;
    $this->add_total[$this->siteindex][] = $add_total;
    $this->add_added[$this->siteindex][] = $add_added;
    $this->add_comm1[$this->siteindex][] = $add_comm1;
    $this->add_comm2[$this->siteindex][] = $add_comm2;
    $this->sollminuten[$this->siteindex][] = $sollminuten;
  }
  
  
  public function renderArbeitszeitnachweisItems($i) {


    $dayWidth     = 10;
    $am1Width     = 15;
    $am2Width     = 15;
    $am3Width     = 15;
    $am4Width     = 15;
    $am5Width     = 22;
    $am6Width     = 18;
    $am7Width     = 15;
    $descWidth   = 51;

    $cellhoehe   = 6;
    $jahr = '';
    $mmonat = '';
    if(isset($this->jahr[$i]))$jahr = $this->jahr[$i];
    if(isset($this->monat[$i]))$monat = $this->monat[$i];
    // render table header
    if(isset($this->textDetails['body'])) $this->Ln();
    else $this->Ln(8);
    $tabellenbeschriftung  = $this->app->erp->Firmendaten('tabellenbeschriftung');

    $this->SetFont($this->GetFont(),'B',$tabellenbeschriftung);

    if($this->doctype=="arbeitszeitnachweis") 
    {
      $this->Cell($dayWidth,6,'Tag');
      $this->Cell($am1Width,6,'Beginn',0,0,'R');
      $this->Cell($am2Width,6,'Ende',0,0,'R');
      $this->Cell($am3Width,6,'Pause',0,0,'R');
      $this->Cell($am4Width,6,'Dauer',0,0,'R');
      $this->Cell($am5Width,6,'erfasst am',0,0,'R');
      $this->Cell($am7Width,6,'Soll',0,0,'R');
      $this->Cell($am6Width,6,'Kürzel',0,0,'R');
      $this->Cell($descWidth,6,'Beschreibung',0,0,'R');
    } 

    $this->Ln();
    $this->Line($this->GetX(), $this->GetY(), 190, $this->GetY()); 
    $this->Ln(2);

    // render table body
    $tabelleninhalt  = $this->app->erp->Firmendaten('tabelleninhalt');
    
    $this->SetFont($this->GetFont(),'',$tabelleninhalt);

    $heute=1; 
    for($heute = 1; $heute <= 31; $heute++){
      $cellhoehe   = 4.5;
      //position
 /*   $this->Cell($amWidth,$cellhoehe,$item['beginn'],0,0,'R');
      $this->Cell($amWidth,$cellhoehe,$item['ende'],0,0,'R');
      $this->Cell($amWidth,$cellhoehe,$item['pause'],0,0,'R');
      $this->Cell($amWidth,$cellhoehe,$item['dauer'],0,0,'R');
      $this->Cell($amWidth,$cellhoehe,$item['aufgezeichnet'],0,0,'R');*/
      $entries = count($this->add_date[$i]) - 1;
      $k = 0;
      if(in_array($heute, $this->add_date_clear[$i]))
      {
        while($k <= $entries)
        {		
          if (date("j", strtotime($this->add_date[$i][$k])) == $heute) 
          {
            $font = $this->GetFont();
            if(date("w", strtotime($this->add_date[$i][$k])) == 0 || date("w", strtotime($this->add_date[$i][$k])) == 6)$this->SetFont($font,'U',$tabelleninhalt);
            $this->Cell($dayWidth,$cellhoehe,$heute,0,0,'C');
            if(date("w", strtotime($this->add_date[$i][$k])) == 0 || date("w", strtotime($this->add_date[$i][$k])) == 6)$this->SetFont($font,'',$tabelleninhalt);
            $this->Cell($am1Width,$cellhoehe,$this->add_from[$i][$k],0,0,'R');
            $this->Cell($am2Width,$cellhoehe,$this->add_to[$i][$k],0,0,'R');
            $this->Cell($am3Width,$cellhoehe,$this->add_break[$i][$k],0,0,'R');
            $this->Cell($am4Width,$cellhoehe,($this->sollminuten[$i][$k] != '0:00' || $this->add_total[$i][$k] != '0:00')?$this->add_total[$i][$k]:'-',0,0,'R');
            $this->Cell($am5Width,$cellhoehe,($this->add_added[$i][$k] != ''?date("d.m.Y", strtotime($this->add_added[$i][$k])):''),0,0,'R');
            $this->Cell($am7Width,$cellhoehe,($this->sollminuten[$i][$k] != '0:00' || $this->add_total[$i][$k] != '0:00')? $this->sollminuten[$i][$k]:'-',0,0,'R');
            $this->Cell($am6Width,$cellhoehe,$this->add_comm1[$i][$k],0,0,'R');
            $this->Cell($descWidth,$cellhoehe,$this->add_comm2[$i][$k],0,0,'R');
          }
         $k++;
        }
      }
      else
      {
        if($monat && $jahr && !checkdate($monat ,  $heute ,  $jahr ))
        {

          $this->Cell($dayWidth,$cellhoehe,'',0,0,'C');
          $this->Cell($am1Width,$cellhoehe,'',0,0,'R');
          $this->Cell($am2Width,$cellhoehe,'',0,0,'R');
          $this->Cell($am3Width,$cellhoehe,'',0,0,'R');
          $this->Cell($am4Width,$cellhoehe,'',0,0,'R');
          $this->Cell($am5Width,$cellhoehe,'',0,0,'R');
          $this->Cell($am7Width,$cellhoehe,'',0,0,'R');
          $this->Cell($am6Width,$cellhoehe,'',0,0,'R');
          $this->Cell($descWidth,$cellhoehe,'',0,0,'R'); 
        }else{
          $font = $this->GetFont();
          if(date("w", strtotime($jahr.'-'.(strlen($monat) < 2?'0':'').$monat.'-'.($heute < 10?'0':'').$heute)) == 0 || date("w", strtotime($jahr.'-'.(strlen($monat) < 2?'0':'').$monat.'-'.($heute < 10?'0':'').$heute)) == 6)
          {
            $this->SetFont($font,'U',$tabelleninhalt);
          }
          $this->Cell($dayWidth,$cellhoehe,$heute,0,0,'C');
          if(date("w", strtotime($jahr.'-'.(strlen($monat) < 2?'0':'').$monat.'-'.($heute < 10?'0':'').$heute)) == 0 || date("w", strtotime($jahr.'-'.(strlen($monat) < 2?'0':'').$monat.'-'.($heute < 10?'0':'').$heute)) == 6)
          {
            $this->SetFont($font,'',$tabelleninhalt);
          }
          $this->Cell($am1Width,$cellhoehe,'-',0,0,'R');
          $this->Cell($am2Width,$cellhoehe,'-',0,0,'R');
          $this->Cell($am3Width,$cellhoehe,'-',0,0,'R');
          $this->Cell($am4Width,$cellhoehe,'-',0,0,'R');
          $this->Cell($am5Width,$cellhoehe,'-',0,0,'R');
          $this->Cell($am7Width,$cellhoehe,'-',0,0,'R');
          $this->Cell($am6Width,$cellhoehe,'-',0,0,'R');
          $this->Cell($descWidth,$cellhoehe,'-',0,0,'R');
        }        
      }
      $this->Ln();
/*	  //Beschreibungszelle
      $position_x   = $this->GetX();
      $position_y   = $this->GetY();
      $this->MultiCell($descWidth,$cellhoehe,$this->add7,0,Alignment.RIGHT, false);
      $position_y_end_name   = $this->GetY();

      $this->SetXY(($position_x + $descWidth), $position_y);
      Zweite Zeile
      $this->Ln();
      $zeilenuntertext  = $this->app->DB->Select("SELECT zeilenuntertext FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");
      //$this->SetY($position_y_end_name+$zeilenuntertext/2);
      $this->SetFont($this->GetFont(),'',$zeilenuntertext);
      $this->Cell($dayWidth);
      //$this->Cell($descWidth);
      //if($this->doctype=="arbeitsnachweis") $this->Cell($taxWidth);
      $this->MultiCell($descWidth,($zeilenuntertext/2),trim($this->app->erp->ReadyForPDF($item['desc'])),0); // 4 = abstand
*/
      $this->SetFont($this->GetFont(),'',$tabelleninhalt);

    }
    $this->Line($this->GetX(), $this->GetY(), 190, $this->GetY()); 
  }  


  function GetArbeitszeitnachweis($id,$info="",$extrafreitext="")
  {
    $this->siteindex++;
    $adresse = (int)$id;// $this->app->DB->Select("SELECT id FROM reisekosten WHERE id='$id' LIMIT 1");

    // das muss vom reisekosten sein!!!!
    //$this->setRecipientLieferadresse($id,"reisekosten");

    // OfferNo, customerId, OfferDate
    $this->mitarbeiternummer[$this->siteindex] = $this->app->DB->Select("SELECT mitarbeiternummer FROM adresse WHERE id='$adresse' LIMIT 1");
    $mitarbeiter = $this->mitarbeiternummer[$this->siteindex];
    $this->name[$this->siteindex] = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$adresse' LIMIT 1");
    
    $bestellbestaetigung = $this->app->DB->Select("SELECT bestellbestaetigung FROM reisekosten WHERE id='$id' LIMIT 1");
    /*$this->datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%d.%m.%Y') FROM reisekosten WHERE id='$id' LIMIT 1");

    $this->von = $this->app->DB->Select("SELECT DATE_FORMAT(von,'%d.%m.%Y') FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->bis = $this->app->DB->Select("SELECT DATE_FORMAT(bis,'%d.%m.%Y') FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->von_zeit = $this->app->DB->Select("SELECT von_zeit FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->bis_zeit = $this->app->DB->Select("SELECT bis_zeit FROM reisekosten WHERE id='$id' LIMIT 1");
    $belegnr = $this->app->DB->Select("SELECT belegnr FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->anlass = $this->app->DB->Select("SELECT anlass FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->freitext = $this->app->DB->Select("SELECT freitext FROM reisekosten WHERE id='$id' LIMIT 1");
    $projekt = $this->app->DB->Select("SELECT projekt FROM reisekosten WHERE id='$id' LIMIT 1");
    $mitarbeiter = $this->app->DB->Select("SELECT mitarbeiter FROM reisekosten WHERE id='$id' LIMIT 1");
    

	$this->monat = $this->app->DB->Select("SELECT DATE_FORMAT(von, '%m') FROM reisekosten WHERE id='$id' LIMIT 1");
	$this->jahr = $this->app->DB->Select("SELECT DATE_FORMAT(von, '%Y') FROM reisekosten WHERE id='$id' LIMIT 1");
	*/
    $this->firmenname = $this->app->erp->Firmendaten('name');
    $this->projektabkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$projekt'");

    //$this->barcode=$belegnr;

    //$ohne_briefpapier = $this->app->DB->Select("SELECT ohne_briefpapier FROM reisekosten WHERE id='$id' LIMIT 1");

    $this->doctype="arbeitszeitnachweis";
    

    $this->doctypeOrig="Arbeitszeitnachweis für den Monat ".$this->monat."/".$this->jahr."    Mitarbeiter: ".$this->name." (ID: ".$this->mitarbeiternummer.")";

    //$waehrung = $this->app->DB->Select("SELECT waehrung FROM reisekosten_position WHERE reisekosten='$id' LIMIT 1");
   
    /* Dateiname */
    //$datum = $this->app->DB->Select("SELECT DATE_FORMAT(datum,'%Y%m%d') FROM reisekosten WHERE id='$id' LIMIT 1");
    //$belegnr= $this->app->DB->Select("SELECT belegnr FROM reisekosten WHERE id='$id' LIMIT 1");
    $this->filename = $this->jahr."ma".$this->mitarbeiternummer.".pdf";

    //$this->setBarcode($belegnr);
  }


}

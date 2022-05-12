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
include_once "class.superfpdf.php";

class KatalogPDF extends SuperFPDF 
{
  public $app;
  //Current column
  var $col=0;

  var $spaltenbreite = 40;    // Allgemeine Spaltenbreite
  var $spaltenabstand = 5;    // Abstand zwischen den Spalten
  var $rand_oben = 10;	      // oberer Rand
  var $rand_unten = 10;	      // unterer Rand
  var $rand_links = 6;	      // linker Rand bei gerader Seite
  var $rand_links_u = 8;      // linker Rand bei ungerader Seite 
  var $A5breite = 148; 
  var $A5hoehe = 210;
  var $textSize = 384;	      // es sind x zeichen erlaubt
  var $caption;		      // Array welches die Ueberschriften enthaelt

  var $footertext;

  function __construct($app)
  {
    $this->app = $app;
    parent::__construct('P','mm','A5');

    $this->AddFont('HelveticaBoldCond','','HLBC____.php');

    $this->AddFont('HelveticaBoldCondItalic','','HLBCO___.php');
    $this->AddFont('HelveticaCond','','HLC_____.php');
    $this->AddFont('HelveticaCondItalic','','HLCO____.php');

    $this->SetAutoPageBreak(true,$this->rand_unten);

 		$this->footertext = "© ".date('Y')." ".$this->app->erp->Firmendaten("name");
  }

  function TitelSeite()
  {
    $this->AddPage(); //titel
    $this->AddPage();
  }

  function Header()
  {
    $zahl = $this->PageNo();
    if($zahl == 1) 
    { 
      // 1. Seite
      $pagecount = $this->setSourceFile("./lib/dokumente/Ausgabe.pdf");
      $tplidx = $this->ImportPage(1);
      $this->useTemplate($tplidx,0,0,0);
      $this->SetMargins(18,5);
      $this->SetXY(18,5);
    }     
    
    if($zahl % 2 == 0) 
    { 
      // Zahl ist gerade  
      $pagecount = $this->setSourceFile("./lib/dokumente/AusgabeLinks.pdf");
      $tplidx = $this->ImportPage(1);
      $this->useTemplate($tplidx,0,0,0);
      $this->renderCaption();
      $this->SetMargins($this->rand_links,$this->rand_oben);
      $this->SetXY($this->rand_links,$this->rand_oben);
    } 

    if($zahl % 2 != 0 && $zahl != 1) 
    { 
      // Zahl ist ungerade
      $pagecount = $this->setSourceFile("./lib/dokumente/AusgabeRechts.pdf");
      $tplidx = $this->ImportPage(1);
      $this->useTemplate($tplidx,0,0,0);
      $this->renderCaption(); 
      $this->SetMargins($this->rand_links_u,$this->rand_oben);
      $this->SetXY($this->rand_links_u,$this->rand_oben);
    }
  }

  function renderCaption()
  {
    $page = $this->PageNo();
    $breite = ($this->spaltenbreite*3) + ($this->spaltenabstand*2);
    $hoehe = 4;
    //echo  $page."<br>";
    for($i=0;$i<count($this->caption);$i++)
    {
      //echo $page." - ".$this->caption[$i][caption]."<br>";
      if($this->caption[$i][page]==$page)
      {
	$this->SetFont('HelveticaBoldCond','','9');
	$this->SetTextColor(90, 90, 90);

	if($page % 2 == 0) // Seite ist gerade
	{
	  $this->SetMargins($this->rand_links,4);
	  $this->SetXY($this->rand_links, 4);
	  $this->Cell($breite,$hoehe,$this->caption[$i][caption],0,0,'L');
	}

	if($page % 2 != 0 && $page != 1) // Seite ist ungerade
	{
	  $this->SetMargins($this->rand_links_u,4);
	  $this->SetXY($this->rand_links_u, 4);
	  $this->Cell($breite,$hoehe,$this->caption[$i][caption],0,0,'R');
	}
      }
    }
  }

  function Footer()
  {
    $zahl = $this->PageNo();
    
    if($zahl % 2 == 0) 
    { 
    //seitenzahl
    $this->SetXY(6,-15);
    $this->SetFont('Arial','','8');
    $this->SetTextColor(28);
    $this->Cell(10,10,$this->PageNo(),0,0,'R');

    // Zahl ist gerade  rechts
    $this->SetXY(70,-15);
    $this->SetFont('Arial','','6');
    $this->SetTextColor(173,173,173);
    $this->Cell(14,10,$this->footertext,0,0,'L');

    
    } 
    if($zahl % 2 != 0 && $zahl != 1)  
    { 
      // Zahl ist ungerade  
      $this->SetXY(130,-15);
      $this->SetFont('Arial','','8');
      $this->SetTextColor(28);
      $this->Cell(10,10,$this->PageNo(),0,0,'L');
    
      $this->SetXY(60,-15);
      $this->SetFont('Arial','','6');
      $this->SetTextColor(173,173,173);
      $this->Cell(14,10,$this->footertext,0,0,'R');
    }  
  }

  function SetCol($col)
  {
    $zahl = $this->PageNo();
    
    if($zahl % 2 != 0) // Zahl ist ungerade
      $linkerrand = $this->rand_links_u;
    else  // Zahl ist gerade 
      $linkerrand = $this->rand_links;
    
    //Set position at a given column
    $this->col=$col;
    $_x=$linkerrand+$col*($this->spaltenbreite+$this->spaltenabstand);
    $this->SetLeftMargin($_x);
    $this->SetX($_x);
  } 

  function AcceptPageBreak()
  {
    $zahl = $this->PageNo();
    
    //Method accepting or not automatic page break
    if($this->col<2)
    {
      //Go to next column
      $this->SetCol($this->col+1);
      //Keep on page
      return false;
    }
    else
    {
      //Go back to first column
      $this->SetCol(0);
      $this->AddPage();
      //Page break
      return true;
    }
  }

  function nextCell()
  {
    // Trennlinie kurz ueber der Mitte
    $border = $this->rand_oben + ($this->A5hoehe-$this->rand_unten) /2;

    if($this->GetY() <= $border)
      $this->SetY($border);
    else
    {
      $this->SetY($this->rand_oben);
      $this->AcceptPageBreak();
    }
  }

  public function Index()
  {
    if(!empty($this->caption))
    {
      $this->index=">> Punkt1\n>> Punkt2\n>> Punkt3";
      $captionArr=$this->caption;

      $temp = "";
      $seperator=">>";
    
      for($i=0;$i<count($captionArr);$i++)
      {
	$seite = $captionArr[$i][page];
	$caption = $captionArr[$i][caption];
	if($caption != $temp)
	{
	  $ersteSeite=$seite;
	  $index.=$seperator." ".$caption." | S. ".$ersteSeite."\n";
	  $temp = $caption;
	  $this->IndexElementCount++;
	}
      }
      $this->index = $index;
    }
  }

  function ArtikelText($artikelarray)
  {
    $borderDebug = 0;
    $this->TitelSeite();
    // Inhaltsverzeichnis rendern
    if(!empty($this->index))
    {
      $this->SetFillColor(173,173,173);
      $this->SetTextColor(255,255,255);

      $hoehe = $this->A5hoehe/2-$this->GetY(); 
      $cellHoehe =3;

      $this->Rect($this->GetX(),$this->GetY(),$this->spaltenbreite,$hoehe,F);
      $this->SetXY($this->GetX()+5,$this->GetY()+$hoehe-(2*$cellHoehe+0.5+$this->IndexElementCount*($cellHoehe-0.5))); //Y + Spaltenhoehe - (Ueberschrift+Abstand+eintraege*eintragshoehe)

      $this->SetFont('HelveticaBoldCond','','6');
      $this->Cell($this->spaltenbreite,$cellHoehe,"Inhalt");
      $this->Ln($cellHoehe + 0.5);

      $this->SetX($this->GetX()+5);

      $this->SetFont('HelveticaCond','','5');
      $this->MultiCell($this->spaltenbreite-$this->GetX()+5, $cellHoehe-0.5,$this->index,0,'L');
      $this->nextCell();
    }

    for($i=0;$i<count($artikelarray);$i++)
    {
      $name= $artikelarray[$i][name];
      $bild= $artikelarray[$i][bild];
      $text= $artikelarray[$i][beschreibung];
      $preis = $artikelarray[$i][preis];
      $hersteller = $artikelarray[$i][hersteller];
      $bestellnr = $artikelarray[$i][bestellnr];
      $staffelpreise = $artikelarray[$i]['staffelpreise'];
      $einheit = $artikelarray[$i]['einheit'];

      if($einheit == ""){
        if($this->app->erp->Firmendaten('artikeleinheit_standard') != ""){
          $einheit = $this->app->erp->Firmendaten('artikeleinheit_standard');
        }else{
          $einheit = "Stk";
        }
      }
 
      if($this->app->erp->Firmendaten('waehrung') != ""){
        $waehrung = $this->app->erp->Firmendaten('waehrung');
      }else{
        $waehrung = "EUR";
      }

      //alternatives Bild laden
      if($bild=="")
      {
      	$id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$bestellnr' LIMIT 1");
      	$shopBild =  $this->app->DB->Select("SELECT datei FROM datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$id' LIMIT 1");
      	$bild = $this->app->erp->GetDateiPfad($shopBild);
      }
      else
      	$bild = $this->app->erp->GetDateiPfad($bild);
      

      if(!is_file($bild))    
				$bild = '/home/eproo/eproo-master/app/main/webroot/lib/dokumente/std.jpg';
      
      if(is_file($bild))
      {
      	$size=@getimagesize($bild);
      	$bildproportion=$size[0]/$size[1];
      	$y = $this->GetY();
      	$y1 = $y + $this->spaltenbreite/$bildproportion;
      	if($y1 > ($this->A5hoehe-$this->rand_unten))
      	{
      	  $this->AcceptPageBreak();
      	  $this->Image($bild, $this->GetX(), $this->GetY(),$this->spaltenbreite,0,'JPG');
      	  $this->SetY($this->GetY() + $this->spaltenbreite/$bildproportion);
      	}
      	else
      	{
      	  $this->Image($bild, $this->GetX(), $y, $this->spaltenbreite,0,'JPG');
      	  $this->SetY($y1);
      	}
      }

      if($hersteller!="")
      {
      	$this->SetFont('HelveticaCond','','5');
      	$this->SetTextColor(100, 100, 100);
      	$this->MultiCell($this->spaltenbreite,2.5,$hersteller,$borderDebug,'L');
      	$this->Ln(0);	
      }

      if($name!="")
      {
      	$this->SetFont('HelveticaBoldCond','','7');
      	$this->SetTextColor(0, 0, 0);
      	$this->MultiCell($this->spaltenbreite,3,$name,$borderDebug,'L');
      	$this->Ln(1);
      }

      if($text!="")
      {
      	$this->SetFont('HelveticaCond','','6');
      	$this->SetTextColor(0, 0, 0);
      	$this->MultiCell($this->spaltenbreite,3,substr($text,0,$this->textSize),$borderDebug,'L');
      	$this->Ln(0.5);
      }
  /* 
      if($preis!="")
      {
	      $this->SetFont('HelveticaCond','','5');
        $this->SetTextColor(50, 50, 50);
        $this->MultiCell($this->spaltenbreite,2.5,"Preis: ".number_format($preis,2,'.','')." EUR",$borderDebug,'L');
	      $this->Ln(0);
      }

*/ 
      if($bestellnr!="")
      {
	      $this->SetFont('HelveticaCond','','5');
        $this->SetTextColor(50, 50, 50);
        $this->MultiCell($this->spaltenbreite,2.5,"Bestellnr.: ".$bestellnr." | "."Preis: ".number_format($preis,2,'.','')." ".$waehrung,$borderDebug,'L');
      	//$this->SetFont('HelveticaBoldCond','','5');
      	//$this->Cell($this->spaltenbreite,2.5,"http://shop.embedded-projects.net/".$bestellnr,$borderDebug,0,'L',0,"http://shop.embedded-projects.net/".$bestellnr);
      }

      if(count($staffelpreise) > 1){
        //$this->Ln(1);
        $this->SetFont('HelveticaCond','','5');
        $this->SetTextColor(50, 50, 50);
        foreach($staffelpreise as $key=>$value){
          $this->MultiCell($this->spaltenbreite,3,"ab ".number_format($value['ab_menge'],2,'.','')." ".$einheit.": ".number_format($value['preis'],2,'.','')." ".$waehrung,$borderDebug,'L');
        }        
      }
  
      $this->nextCell();
    }
  }

  public function SetCaption($page, $caption)
  {
    if($page>0 && $caption!="")
      $this->caption[] = array("page"=>$page,"caption"=>$caption);
  }
}    

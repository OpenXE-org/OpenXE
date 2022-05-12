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
class LagermindestmengenPDF extends Dokumentenvorlage {
  public $doctype;
  var $rows;
  var $name;
  public function __construct($app,$rows,$projekt='',$name)
  {
    $this->app=$app;
    $this->doctype="lagermindestmenge";
    $this->doctypeOrig="Lagermindestmengen Liste";
    
    $this->rows = $rows;
    $this->name = $name;

    parent::__construct($this->app,$projekt);

    $this->filename = $this->app->erp->Dateinamen(date('Ymd').'_'.date('His').'_Lagermindestmengen_Umlagerliste' . '_' . $name .'.pdf');
    $this->setBarcode(date('dmYHi').$this->app->User->GetAdresse());
  } 

  public function renderDocument() {
    // prepare page details
    SuperFPDF::__construct('P','mm','A4');

    $this->AddPage();
    $this->SetDisplayMode("real","single");

    $this->SetMargins(15,50);
    $this->SetAutoPageBreak(true,40);
    $this->AliasNbPages('{nb}');


    //if($this->barcode!="")
    {
      $y = $this->GetY();
      $this->Code39(145, $y+1, $this->barcode, 1, 5);
    }

    // Bei PosAbschlussPDF immer oben beginnen
    $this->abstand_betreffzeileoben=0;

    $this->renderDoctype();

    $infobox[]=array("Bearbeiter",$this->name);
    $infobox[]=array("Erstellt am",date('d.m.Y')." um ".date('H:i'));
    $this->renderInfoBox($infobox);
    $this->Ln(5);
   
    $this->SetWidths(array(25,50,12,25,63));
    $this->SetFillColor(235, 235, 235); 

    if ($this->rows) {
      $fill = 1;
      foreach ($this->rows as $row) {
        $iRow = array();
        foreach ($row as $field) {
          $iRow[] = $field;
        }
        if ($fill == 1) {
          $this->SetFillColor(200,200,200); 
          $this->Row($iRow, 1);
          $this->SetFillColor(245, 245, 245); 
        } else if ($fill % 2 == 0) {
          $this->Row($iRow, 0);
        } else {
          $this->Row($iRow, 0); //1
        }
        $fill++;
      }
    }
    $this->renderFooter();
  }

  var $widths;
  var $aligns;

  function SetWidths($w) {
      $this->widths=$w;
  }

  function SetAligns($a) {
      $this->aligns=$a;
  }

  function Row($data,$fill = 0,$width=0) {
      $nb = 0;
      
      for($i=0;$i<count($data);$i++) {
        $nb = max($nb,$this->NbLines($this->widths[$i],$data[$i]));
      }

      $h = 5*$nb;

      $this->CheckPageBreak($h);
      for($i=0;$i<count($data);$i++)
      {
          $w = $this->widths[$i];
          $a = isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
          $x = $this->GetX();
          $y = $this->GetY();
          $this->Rect($x,$y,$w,$h);
          $this->MultiCell($w,5,$data[$i],0,$a,$fill);
          $this->SetXY($x+$w,$y);
      }
      $this->Ln($h);
  }

  function CheckPageBreak($h)
  {
      //If the height h would cause an overflow, add a new page immediately
      if($this->GetY()+$h>$this->PageBreakTrigger)
          $this->AddPage($this->CurOrientation);
  }

  function NbLines($w,$txt)
  {
      //Computes the number of lines a MultiCell of width w will take
      $cw=&$this->CurrentFont['cw'];
      if($w==0)
          $w=$this->w-$this->rMargin-$this->x;
      $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
      $s=str_replace("\r",'',$txt);
      $nb=strlen($s);
      if($nb>0 and $s[$nb-1]=="\n")
          $nb--;
      $sep=-1;
      $i=0;
      $j=0;
      $l=0;
      $nl=1;
      while($i<$nb)
      {
          $c=$s[$i];
          if($c=="\n")
          {
              $i++;
              $sep=-1;
              $j=$i;
              $l=0;
              $nl++;
              continue;
          }
          if($c==' ')
              $sep=$i;
          $l+=$cw[$c];
          if($l>$wmax)
          {
              if($sep==-1)
              {
                  if($i==$j)
                      $i++;
              }
              else
                  $i=$sep+1;
              $sep=-1;
              $j=$i;
              $l=0;
              $nl++;
          }
          else
              $i++;
      }
      return $nl;
  }



}

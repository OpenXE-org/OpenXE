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
class Etikett extends SuperFPDF {

  public $barcode2;
  public $subbarcodetxt;
  public $subbarcode2txt;
  public $prebarcodetxt;
  public $prebarcode2txt;

  /***********************************
   * 		user space functions
   ***********************************/
  function __construct($doctype="default") {
    $this->doctype=$doctype;
  }


  /***********************************
   * 		data aggregation functions
   ***********************************/	
  // label settings
  public function setBarcode($barcode) {
    $this->barcode = $barcode;
  }	
  public function setBarcode2($barcode) {
    $this->barcode2 = $barcode;
  }
  public function setSubBarcodeText($text) {
    $this->subbarcodetxt = $text;
  }		
  public function setSubBarcode2Text($text) {
    $this->subbarcode2txt = $text;
  }		
  public function setPreBarcodeText($text) {
    $this->prebarcodetxt = $text;
  }	
  public function setPreBarcode2Text($text) {
    $this->prebarcode2txt = $text;
  }		
  public function setPostBarcodeText($text) {
    $this->postbarcodetxt = $text;
  }	
  public function setPostBarcode2Text($text) {
    $this->postbarcode2txt = $text;
  }	

  public function displayDocument() {
    $this->renderDocument();
    $this->Output();
  }

  public function sendDocument() { 
    $this->renderDocument();
    return $this->Output($this->filename,'S'); 
  }	



  /***********************************
   * 			private functions
   ***********************************/		
  private function renderDocument() {
    if($this->doctype=="chargeklein") {
      PDF::__construct('P','mm',array(25,10));
    } elseif($this->doctype=="chargegross") {
      PDF::__construct('P','mm',array(100,45));
    } 

    $this->AddFont('HelveticaBoldCond','','HLBC____.php');
    $this->AddFont('HelveticaBoldCondItalic','','HLBCO___.php');
    $this->AddFont('HelveticaCond','','HLC_____.php');
    $this->AddFont('HelveticaCondItalic','','HLCO____.php');
    $this->AddPage();
    $this->SetDisplayMode("real","single");		

    if($this->doctype=="chargeklein") {
      $this->SetFont('Courier','B',7);
      if(isset($this->prebarcodetxt))	$this->Text(1,3,	$this->prebarcodetxt);
      if(isset($this->prebarcode2txt))$this->Text(1,8.5,$this->prebarcode2txt);
      $this->SetFont('Courier','B',7);
      if(isset($this->subbarcodetxt))	$this->Text(1,5.5,	$this->subbarcodetxt);
      if(isset($this->barcode))				$this->Code39(5,1.5,$this->barcode, 0.5, 1.5);
      if(isset($this->barcode2))			$this->Code39(5,7,	$this->barcode2, 0.5, 1.5);
    } elseif($this->doctype=="chargegross") {
      $this->SetAutoPageBreak(false);
      $this->SetMargins(1,1);
      $this->SetFont('Courier','B',10);
      if(isset($this->postbarcodetxt))	{
        $this->SetXY(60,1);
        $this->MultiCell(60,4,$this->postbarcodetxt);
      }			
      if(isset($this->subbarcodetxt))	{
        $this->SetXY(1,12.5);
        $this->MultiCell(60,4,$this->subbarcodetxt);
      }
      if(isset($this->subbarcode2txt))$this->Text(2, 43, $this->subbarcode2txt);
      if(isset($this->barcode))				$this->Code39(2,2, $this->barcode, 0.5,10);
      if(isset($this->barcode2))			$this->Code39(2,30,$this->barcode2,0.5,10);

      // draw table
      $this->SetDrawColor(128,128,128);
      $this->SetLineWidth(.3);
      $this->SetXY(60,18);
      for($i=0;$i<4;$i++){
        for($j=0;$j<4;$j++)
          $this->Cell(9,6,"",1,0);
        $this->SetXY(60,$this->GetY()+6);
      }
    } 
  }
}


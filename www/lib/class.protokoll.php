<?php
include_once('class.superfpdf.php');

class Protokoll extends SuperFPDF {
	public $doctype;

	public $logofile;
	public $items;
	public $footer;
	
	
	/***********************************
	 * 		user space functions
	 ***********************************/
	function __construct($doctype="default") {
		$this->doctype = $doctype;
		$this->setupTax();
	}
	
	
	
	
	/***********************************
	 * 		data aggregation functions
	 ***********************************/	
	public function addItem($rdata){
		$rdata['tprice'] = $rdata['amount']*$rdata['price'];
		$this->items[]=$rdata;
	}	
	
	public function setFooter1($rdata){
		$this->footer[0]=$rdata;
	}	
	public function setFooter2($rdata){
		$this->footer[1]=$rdata;
	}
	 
	 

	public function setLogo($logofile) {
		$this->logofile = "../pdf/images/".$logofile;
	}

	
	
	public function Header() {
		$this->Line(0,87,2,87);
		$this->Line(0,148,4,148);
		$this->Line(0,192,2,192);
		if(isset($this->logofile)) 
			$this->Image($this->logofile,15,10,110);
	}  
	
	public function Footer() {
		$this->SetXY(10,-34);
		$this->SetFont('HelveticaCond','',8);
		$this->Cell(0,10,'Seite '.$this->PageNo().'/{nb}',0,0,'C');
		
		$this->SetXY(10,-26);
		$this->SetDrawColor(50);
		$this->SetTextColor(50);
		$this->SetFont('HelveticaCond','',7);
		$this->MultiCell(45,3,"  Sitz der Gesellschaft / Lieferanschrift\n  WaWision GmbH\n  Holzbachstraße 4\n  D-86152 Augsburg\n  Telefon +49 821 27 95 99 0\n  Telefax +49 821 27 95 99 20",'L','L');
		$this->SetXY(55,-26);
		$this->MultiCell(40,3,"  Geschäftsführer:\n  Dipl.-Inf.(FH) Benedikt Sauter\n  Handelsregister: HRB 23930\n  Amtsgericht: Augsburg\n  \n  ",'L','L');
		
		if(isset($this->footer[0])){
			$this->SetXY(95,-26);
			$this->MultiCell(50,3,$this->footer[0],'L','L');
		}
		if(isset($this->footer[1])){
			$this->SetXY(145,-26);
			$this->MultiCell(50,3,$this->footer[1],'LR','L');
		}

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
	// setup relevant data for a invoice
	private function setupTax() {
		// full taxrate
		define("USTV",0.19);
		// reduced taxrate
		define("USTR",0.07);
	}	
	
	
	
	private function renderDocument() {
		// prepare page details
		parent::PDF('P','mm','A4');

		$this->AddFont('HelveticaBoldCond','','HLBC____.php');
		$this->AddFont('HelveticaBoldCondItalic','','HLBCO___.php');
		$this->AddFont('HelveticaCond','','HLC_____.php');
		$this->AddFont('HelveticaCondItalic','','HLCO____.php');
		// invoke Header() and Footer() by adding a new page
		$this->AddPage();
		$this->SetDisplayMode("real","single");
			
		$this->SetMargins(15,50);
		$this->SetAutoPageBreak(true,30); 
		$this->AliasNbPages('{nb}');
		
		if(!empty($this->items)) $this->renderItems();
	}

	private function renderItems() {
    $amWidth 		= 15;
		$itemNoWidth = 35;
		$eanWidth = 38;
		$descWidth 	= 85;
		$taxWidth 	= 22;
    $priceWidth = 22;
    $sumWidth 	= 22;
		// $lineLength = $amWidth + $itemNoWidth + $descWidth + $taxWidth + $priceWidth + $sumWidth;
    $cellhoehe 	= 5;

		// render table header
		if(isset($this->textDetails['body'])) $this->Ln();
		else $this->Ln(31);
		
		$this->SetFont('HelveticaBoldCond','',10);
	  $this->Cell($amWidth,6,'Menge',0,0,'C');
	  $this->Cell($itemNoWidth,6,'Artikelnr',0,0,'C');
	  $this->Cell($eanWidth,6,'EAN',0,0,'C');
	  $this->Cell($descWidth,6,'Artikel');
		$this->Ln();
		$this->Line($this->GetX(), $this->GetY(), 190, $this->GetY()); 
		$this->Ln(2);
	
		// render table body
		$this->SetFont('HelveticaCond','',10);
    
    foreach($this->items as $item){
			$this->Cell($amWidth,$cellhoehe,$item['amount'],0,0,'C');
			$this->Cell($itemNoWidth,$cellhoehe,$item['itemno'],0,0,'L');
//			$this->Code39($this->GetX(),$this->GetY()+1,$item['barcode'],0.5,3);
			$this->Cell($eanWidth);
  	  
			$this->Cell($descWidth,$cellhoehe,$item['name'],0);
			$this->Ln();
			if(isset($item['desc'])) {
				$this->SetFont('HelveticaCond','',8);
				$this->Cell($amWidth);
				$this->Cell($itemNoWidth);
				$this->Cell($eanWidth);
				$this->MultiCell($descWidth,3,$item['desc'],0);
				$this->Ln();
				$this->SetFont('HelveticaCond','',10);
			}
		}
		$this->Line($this->GetX(), $this->GetY(), 190, $this->GetY()); 
	}	
}

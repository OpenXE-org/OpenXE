<?php

// Pfad zum Font-Ordner
define("FPDF_FONTPATH",__DIR__."/font/");
// fpdf-Klasse holen
require("fpdf.php");


/************************************/
/* class PDF                        */
/************************************/
class PDF extends FPDF
{
    var $B;
    var $I;
    var $U;
    var $HREF;
    var $fontList;
    var $issetfont;
    var $issetcolor;
    

		
		
		function __construct($orientation='P', $unit='mm', $format='v1', $_title='vertrag.pdf', $_url='', $_debug=false)
    {
        $this->FPDF($orientation, $unit, $format);
        $this->B=0;
        $this->I=0;
        $this->U=0;
        $this->HREF='';
        $this->PRE=false;
        $this->SetFont('helvetica', '', 10);
        $this->fontlist=array("Times", "Courier");
        $this->issetfont=false;
        $this->issetcolor=false;
        $this->articletitle=$_title;
        $this->articleurl=$_url;
        $this->debug=$_debug;
        $this->AliasNbPages();
    }

 





    

 
} // class PDF

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
//function hex2dec
//returns an associative array (keys: R, G, B) from
//a hex html code (e.g. #3FE5AA)
function hex2dec($couleur = "#000000"){
  $R = substr($couleur, 1, 2);
  $rouge = hexdec($R);
  $V = substr($couleur, 3, 2);
  $vert = hexdec($V);
  $B = substr($couleur, 5, 2);
  $bleu = hexdec($B);
  $tbl_couleur = array();
  $tbl_couleur['R']=$rouge;
  $tbl_couleur['G']=$vert;
  $tbl_couleur['B']=$bleu;
  return $tbl_couleur;
}

//conversion pixel -> millimeter in 72 dpi
function px2mm($px){
  return $px*25.4/72;
}

function txtentities($html){
  $trans = get_html_translation_table(HTML_ENTITIES);
  $trans = array_flip($trans);
  return strtr($html, $trans);
}

class SuperFPDF extends PDF_EPS {

  /***********************************
   *     user space functions
   ***********************************/
  public $addpdf = null;

  function __construct($orientation='P',$unit='mm',$format='A4',$app=null){

    if($app!=null)
      $this->app=$app;

    $this->B=0;
    $this->I=0;
    $this->U=0;
    $this->HREF='';
    $this->BeforeStack=array();

    $this->number_ol=0;
    parent::__construct($orientation,$unit,$format);
  }

  public function AddPDF($file)
  {
    $filename = '/tmp/'.md5($file.microtime(true)).'.pdf';
    if($fp = fopen($filename, 'wb')) 
    {
      fwrite($fp, $file);
      fclose($fp);
      if(file_exists($filename) && is_file($filename)){
        $this->addpdf[] = $filename;
        return true;
      }
    }
    return false;
  }

  function displayAnhaenge($output='D')
  {
    //SuperFPDF::__construct('P','mm','A4');
    $this->seite_von_sichtbar = 1;
    
    //		if($this->app->erp->Firmendaten("schriftart")!="")
    //			$this->SetFont($this->app->erp->Firmendaten("schriftart"));  

    if($this->addpdf && is_array($this->addpdf) && count($this->addpdf) > 0)
    {
      
      foreach($this->addpdf as $addpdf)
      {
        
        $filename = $addpdf;

        $pn = $this->setSourceFile($filename);
        //$this->AddPage();
        //$this->SetXY(0,0);
        for($i = 1; $i <= $pn ; $i++)
        {
          $fn =& $this->current_filename;
          $mediabox = $this->parsers[$fn]->getPageMediaBox($i);
          //$this->app->erp->LogFile(addslashes(json_encode($mediabox)));
          if($i >= 1)
          {
            //if(isset($mediabox['w']) && (float)$mediabox['w'] > 210.1 )
            if(isset($mediabox['w']) && (float)$mediabox['w'] > 216.0 )
            {
            $this->AddPage('L');
            } else {
            $this->AddPage();  
            }
            $this->SetXY(0,0);                        
          }
          $tplidx = $this->ImportPage($i);
          $this->useTemplate($tplidx);
        }
      }
    }    
    $result = $this->Output($this->filename,$output);

    if($output=="S")
      return $result;
    else
      exit;
  }


  /***********************************
   *     data aggregation functions
   ***********************************/  

  /*
   * general setter function
   *
   * proper usage:
   * $field may be either a string or an array of strings
   * if field is an array, $rdata must be an array(A) of associative arrays(B) with each of B representing a field
   * if field is a string, $rdata must be an associative array 
   *
   */
  public function setDocumentDetails($field, $rdata){
    if(is_array($field)) {
      for($i=0;$i<count($field);$i++) {
        $this->$field[$i] = $rdata[$i];
      }
    } else $this->$field=$rdata;
  }

  //////////////////////////////////////
  //html parser

  function WriteHtmlCell($cellWidth, $html){
    $rm = $this->rMargin;
    $this->SetRightMargin($this->w - $this->GetX() - $cellWidth);
    $this->WriteHtml($html);
    $this->SetRightMargin($rm);
    /*
    // Alter Quelltext
    $this->SetLeftMargin($cellWidth);
    $this->WriteHtml($html);
    $this->SetLeftMargin($cellWidth);
     */
  }


  function WriteHTML($html)
  {
    $x = $this->GetX();
    $this->SetLeftMargin($x);

    $allowed = array("ul","ol","li","b","u","i","a","img","p","br","strong","em","font","tr","blockquote","span"); //remove all unsupported tags

    //file_put_contents("/tmp/bene22",$html,FILE_APPEND); // wenn es wieder nicht passt prüfen warum

    /*$html = htmlspecialchars($html);
    file_put_contents("/tmp/bene222",$html,FILE_APPEND);

    foreach( $allowed as $a ){
        $html = str_replace("&lt;".$a."&gt;", "<".$a.">", $html);
        $html = str_replace("&lt;/".$a."&gt;", "</".$a.">", $html);
        $html = str_replace("&lt;br /&gt;","<br />",$html);
    }
*/

    //file_put_contents("/tmp/bene222",$html,FILE_APPEND);
    while(strpos($html ,'<<')!==false)$html = str_replace("<<", "&lt;<", $html);
    while(strpos($html ,'>>')!==false)$html = str_replace(">>", ">&gt;", $html);

    $html = str_replace("<+", "&lt;+", $html); // Fix: "<+/-2mm" wird ansonten von strip_tags() entfernt
    $html = str_replace("<=", "&lt;=", $html); // Fix: Kleinergleich wird ansonten von strip_tags() entfernt
    $html = str_replace(" >=", " &gt;=", $html); // Fix: Größergleich wird ansonten durch "> =" ersetzt
    $html = str_replace("< ", "&lt; ", $html);
    $html = str_replace(" >", "&gt; ", $html);//Leerzeichen umgedreht
    $html=strip_tags($html, "<ul><ol><li><b><u><i><a><img><p><br><strong><em><font><tr><blockquote><span>"); //remove all unsupported tags

    $html = str_replace('&amp;','&',$html);
    $html = str_replace('&uuml;','ü',$html);
    $html = str_replace('&auml;','ä',$html);
    $html = str_replace('&ouml;','ö',$html);
    $html = str_replace('&szlig;','ß',$html);
    $html = str_replace('&Uuml;','Ü',$html);
    $html = str_replace('&Auml;','Ä',$html);
    $html = str_replace('&Ouml;','Ö',$html);

    $this->BeforeStack[] = array('FontFamily'=>$this->FontFamily,'FontStyle'=>$this->FontStyle,'FontSizePt'=>$this->FontSizePt,'FontSize'=>$this->FontSize,'TextColor'=>$this->TextColor);
    //$html=str_replace("\n", ' ', $html); //replace carriage returns by spaces
    $a=preg_split('/<(.*)>/U', $html, -1, PREG_SPLIT_DELIM_CAPTURE); //explodes the string
    foreach($a as $i=>$e)
    {
      if($i%2==0)
      {
        //Text
        if($this->HREF)
          $this->PutLink($this->HREF, $e);
        else {
          $e = str_replace( chr( 194 ) . chr( 160 ), ' ', $e );
          $this->Write($this->FontSize*1.3,stripslashes(txtentities($e)));
        }
      }
      else
      {
        //Tag
        if ($e[0] == '/') {
            $this->CloseTag(strtoupper(substr($e, 1)));
        } else {
          //Extract attributes
          //TODO BRUNO nicht leerstelle da style="color:rgb(255, 12 ,... probleme macht"
          $a2=explode(' ', $e,2);
          $tag=strtoupper(array_shift($a2));
          $attr=array();
          foreach($a2 as $v)
            if(preg_match('/^([^=]*)=["\']?([^"\']*)["\']?$/', $v, $a3))
              $attr[strtoupper($a3[1])]=$a3[2];
          $this->OpenTag($tag, $attr);
        }
      }
    }

    $LastStack = array_pop($this->BeforeStack);
    foreach($LastStack as $key=>$value)
      $this->$key = $value;
    $this->SetLeftMargin($this->app->erp->Firmendaten("abstand_seitenrandlinks")); 
  }

  function OpenTag($tag, $attr)
  {
    // simulate stack
    $this->BeforeStack[] = array('FontFamily'=>$this->FontFamily,'FontStyle'=>$this->FontStyle,'FontSizePt'=>$this->FontSizePt,'FontSize'=>$this->FontSize,'TextColor'=>$this->TextColor);
    $stack = "";

    //Opening tag
    switch($tag){
      case 'STRONG':
        $this->SetStyle('B', true);
        break;
      case 'UL':
        $this->firstlist=true;
        $this->Ln();
        break;
      case 'OL':
        $this->firstlist=true;
        $this->number_ol=1;
        $this->Ln();
        break;
      case 'LI':
        //$this->SetStyle('B', true);
        if(!$this->firstlist)
          $this->Ln();

        if($this->number_ol > 0)
        {
          $blt = $this->number_ol.")";
          $this->number_ol++;
        } 
        else {
          if(isset($this->ttffontuni) && $this->ttffontuni)
            $blt = "•";
          else
            $blt = chr(127);
        }
        $blt_width = $this->GetStringWidth($blt)+$this->cMargin*2;
        $this->Cell($blt_width, $this->FontSize*1.5, $blt, 0, 0, false);

        $this->firstlist=false;
        break;
      case 'EM':
        $this->SetStyle('I', true);
        break;
      case 'B':
      case 'I':
      case 'U':
        $this->SetStyle($tag, true);
        break;
      case 'A':
        $this->HREF=$attr['HREF'];
        break;
      case 'IMG':
        if(isset($attr['SRC']) and (isset($attr['WIDTH']) or isset($attr['HEIGHT']))) {
          if(!isset($attr['WIDTH']))
            $attr['WIDTH'] = 0;
          if(!isset($attr['HEIGHT']))
            $attr['HEIGHT'] = 0;
          $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
        }
        break;
      case 'TR':
      case 'BLOCKQUOTE':
      case 'BR':
        //$this->Ln($this->FontSize);
        $this->Ln();//$this->FontSize);
        break;
      case 'P':
        //$this->Ln(4);
        $this->Ln();
        break;
      case 'FONT':
        if (isset($attr['COLOR']) and $attr['COLOR']!='') {
          if (strpos($cssval[1], 'rgb') !== false) { 
            $color_tmp = str_replace(array("rgb","(",")"),"",$attr['COLOR']);
            $coul = explode(",",$color_tmp);
            $this->SetTextColor($coul[0], $coul[1], $coul[2]);
          } else {
            $coul=hex2dec($attr['COLOR']);
            $this->SetTextColor($coul['R'], $coul['G'], $coul['B']);
          }
          $stack = "issetcolor";
        }
        if (isset($attr['FACE']) and in_array(strtolower($attr['FACE']), $this->fontlist)) {
          $this->SetFont(strtolower($attr['FACE']));
          $stack = "issetfont";
        }
        break;
      case 'SPAN':
        if (isset($attr['STYLE']) and $attr['STYLE']!='') {
          $cssval = explode(":",$attr['STYLE']); 
          switch($cssval[0])
          {
            case "color":
              if (strpos($cssval[1], 'rgb') !== false) { 
                $color_tmp = str_replace(array("rgb","(",")"," "),"",$cssval[1]);
                $coul = explode(",",$color_tmp);
                $this->SetTextColor($coul[0], $coul[1], $coul[2]);
              } else {
                $coul=hex2dec($cssval[1]);
                $this->SetTextColor($coul['R'], $coul['G'], $coul['B']);
              }
              $stack = "issetcolor";
              break;

            case "font-family":
              //if (in_array(strtolower($cssval[1], $this->fontlist))) {
              $this->SetFont(strtolower(rtrim($cssval[1],";")));
              $stack="issetfont";
              //}
              break;
            case "font-size":
              //if (in_array(strtolower($cssval[1], $this->fontlist))) {
              $fontsize = str_replace(array('px','pt'),'',$cssval[1]);
              if($fontsize!="")
              {
                $this->SetFontSize($fontsize);
                $stack = "issetsize";
              }
              //}
              break;

          }
        }
        break;
    }
    $this->IssetStack[] = $stack;
  }

  function CloseTag($tag)
  {
    $LastStack = array_pop($this->BeforeStack);
    $stack = array_pop($this->IssetStack);

    //Closing tag
    if($tag=='STRONG')
      $tag='B';
    if($tag=='EM')
      $tag='I';

    if($tag=='OL')
    {
      $this->number_ol=0;
      $this->Ln();
    }
    if($tag=='UL')
    {
      $this->number_ol=0;
      $this->Ln();
    }
    //if($tag=='LI')
    //  $this->Ln(px2mm($this->FontSizePt));

    if($tag=='B' or $tag=='I' or $tag=='U')
      $this->SetStyle($tag, false);

    if($tag=='A')
      $this->HREF='';
    if($tag=='FONT'){
      if ($stack=="issetcolor") {
        $this->SetTextColor($LastStack['TextColor']);
      }
      if ($stack=="issetfont") {
        $this->SetFont($LastStack['FontFamily']);
      }
    }
    // muss wie ein stack werden bei geschachtelten 
    if($tag=='SPAN'){
      if ($stack=="issetcolor") {
        $this->SetTextColor($LastStack['TextColor']);
      }
      if ($stack=="issetfont") {
        $this->SetFont($LastStack['FontFamily']);
      }
      if ($stack=="issetsize") {
        $this->SetFontSize($LastStack['FontSizePt']);
      }
    }
  }

  function SetStyle($tag, $enable)
  {
    //Modify style and select corresponding font
    $this->$tag+=($enable ? 1 : -1);
    $style='';
    foreach(array('B', 'I', 'U') as $s)
      if($this->$s>0)
        $style.=$s;
    $this->SetFont('', $style);
  }

  function PutLink($URL, $txt)
  {
    //Put a hyperlink
    $this->SetTextColor(0, 0, 255);
    $this->SetStyle('U', true);
    $this->Write(5, $txt, $URL);
    $this->SetStyle('U', false);
    $this->SetTextColor(0);
  } 


  //Tabellen funktionen

  function SetTableWidths($w)
  {
    //Set the array of column widths
    $this->table_widths=$w;
  }

  function SetTableFonts($f)
  {
    $this->table_fonts=$f;
  }

  function SetTableAligns($a)
  {
    //Set the array of column alignments
    $this->table_aligns=$a;
  }

  function TableRow($data)
  {
    //Calculate the height of the row
    $nb=0;
    for($i=0;$i<count($data);$i++)
      $nb=max($nb,$this->NbLines($this->table_widths[$i],$data[$i]));
    $h=5*$nb;

    //Issue a page break first if needed
    $this->CheckTablePageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
      $w=$this->table_widths[$i];
      $a=isset($this->table_aligns[$i]) ? $this->table_aligns[$i] : 'L';
      //Save the current position
      $x=$this->GetX();
      $y=$this->GetY();
      //Draw the border
      $this->Rect($x,$y,$w,$h);
      //Print the text
      $this->MultiCell($w,5,$data[$i],0,$a);
      //Put the position to the right of the cell
      $this->SetXY($x+$w,$y);
    }
    //Go to the next line
    $this->Ln($h);
  }

  function CheckTablePageBreak($h)
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


  function GetCheckDigit($barcode)
  {
    //Compute the check digit
    $sum=0;
    for($i=1;$i<=11;$i+=2)
      $sum+=3*$barcode[$i];
    for($i=0;$i<=10;$i+=2)
      $sum+=$barcode[$i];
    $r=$sum%10;
    if($r>0)
      $r=10-$r;
    return $r;
  }

  function TestCheckDigit($barcode)
  {
    //Test validity of check digit
    $sum=0;
    for($i=1;$i<=11;$i+=2)
      $sum+=3*$barcode[$i];
    for($i=0;$i<=10;$i+=2)
      $sum+=$barcode[$i];
    return ($sum+$barcode[12])%10==0;
  }


  function EAN13($x, $y, $barcode, $h=16, $w=.3)
  {
    $this->BarcodeEANUPC($x,$y,$barcode,$h,$w,13);
  }

  function UPC_A($x, $y, $barcode, $h=16, $w=.35)
  {
    $this->BarcodeEANUPC($x,$y,$barcode,$h,$w,12);
  }


  function BarcodeEANUPC($x, $y, $barcode, $h, $w, $len)
  {
    //Padding
    $barcode=str_pad($barcode,$len-1,'0',STR_PAD_LEFT);
    if($len==12)
      $barcode='0'.$barcode;
    //Add or control the check digit
    if(strlen($barcode)==12)
      $barcode.=$this->GetCheckDigit($barcode);
    //    elseif(!$this->TestCheckDigit($barcode))
    //        $this->Error('Incorrect check digit');
    //Convert digits to bars
    $codes=array(
        'A'=>array(
          '0'=>'0001101','1'=>'0011001','2'=>'0010011','3'=>'0111101','4'=>'0100011',
          '5'=>'0110001','6'=>'0101111','7'=>'0111011','8'=>'0110111','9'=>'0001011'),
        'B'=>array(
          '0'=>'0100111','1'=>'0110011','2'=>'0011011','3'=>'0100001','4'=>'0011101',
          '5'=>'0111001','6'=>'0000101','7'=>'0010001','8'=>'0001001','9'=>'0010111'),
        'C'=>array(
          '0'=>'1110010','1'=>'1100110','2'=>'1101100','3'=>'1000010','4'=>'1011100',
          '5'=>'1001110','6'=>'1010000','7'=>'1000100','8'=>'1001000','9'=>'1110100')
        );
    $parities=array(
        '0'=>array('A','A','A','A','A','A'),
        '1'=>array('A','A','B','A','B','B'),
        '2'=>array('A','A','B','B','A','B'),
        '3'=>array('A','A','B','B','B','A'),
        '4'=>array('A','B','A','A','B','B'),
        '5'=>array('A','B','B','A','A','B'),
        '6'=>array('A','B','B','B','A','A'),
        '7'=>array('A','B','A','B','A','B'),
        '8'=>array('A','B','A','B','B','A'),
        '9'=>array('A','B','B','A','B','A')
        );
    $code='101';
    $p=$parities[$barcode[0]];
    for($i=1;$i<=6;$i++)
      $code.=$codes[$p[$i-1]][$barcode[$i]];
    $code.='01010';
    for($i=7;$i<=12;$i++)
      $code.=$codes['C'][$barcode[$i]];
    $code.='101';
    //Draw bars
    for($i=0;$i<strlen($code);$i++)
    {
      if($code[$i]=='1')
        $this->Rect($x+$i*$w,$y,$w,$h,'F');
    }
    //Print text uder barcode
    //  $this->SetFont('Arial','',10);
    //  $this->Text($x,$y+$h+11/$this->k,substr($barcode,-$len));
  }


  public function Code128($x, $y, $code, $w, $h)
  {
    if($h=="")$h=5;
    if($w=="")$w=30;

    $T128 = null;                                         // Tableau des codes 128
    $ABCset = "";                                  // jeu des caractères éligibles au C128
    $Aset = "";                                    // Set A du jeu des caractères éligibles
    $Bset = "";                                    // Set B du jeu des caractères éligibles
    $Cset = "";                                    // Set C du jeu des caractères éligibles
    $SetFrom = null;                                      // Convertisseur source des jeux vers le tableau
    $SetTo = null;                                        // Convertisseur destination des jeux vers le tableau
    $JStart = array("A" => 103, "B" => 104, "C" => 105); // Caractères de sélection de jeu au début du C128
    $JSwap = array("A" => 101, "B" => 100, "C" => 99);   // Caractères de changement de jeu

    $T128[] = array(2, 1, 2, 2, 2, 2);           //0 : [ ]               // composition des caract�res
    $T128[] = array(2, 2, 2, 1, 2, 2);           //1 : [!]
    $T128[] = array(2, 2, 2, 2, 2, 1);           //2 : ["]
    $T128[] = array(1, 2, 1, 2, 2, 3);           //3 : [#]
    $T128[] = array(1, 2, 1, 3, 2, 2);           //4 : [$]
    $T128[] = array(1, 3, 1, 2, 2, 2);           //5 : [%]
    $T128[] = array(1, 2, 2, 2, 1, 3);           //6 : [&]
    $T128[] = array(1, 2, 2, 3, 1, 2);           //7 : [']
    $T128[] = array(1, 3, 2, 2, 1, 2);           //8 : [(]
    $T128[] = array(2, 2, 1, 2, 1, 3);           //9 : [)]
    $T128[] = array(2, 2, 1, 3, 1, 2);           //10 : [*]
    $T128[] = array(2, 3, 1, 2, 1, 2);           //11 : [+]
    $T128[] = array(1, 1, 2, 2, 3, 2);           //12 : [,]
    $T128[] = array(1, 2, 2, 1, 3, 2);           //13 : [-]
    $T128[] = array(1, 2, 2, 2, 3, 1);           //14 : [.]
    $T128[] = array(1, 1, 3, 2, 2, 2);           //15 : [/]
    $T128[] = array(1, 2, 3, 1, 2, 2);           //16 : [0]
    $T128[] = array(1, 2, 3, 2, 2, 1);           //17 : [1]
    $T128[] = array(2, 2, 3, 2, 1, 1);           //18 : [2]
    $T128[] = array(2, 2, 1, 1, 3, 2);           //19 : [3]
    $T128[] = array(2, 2, 1, 2, 3, 1);           //20 : [4]
    $T128[] = array(2, 1, 3, 2, 1, 2);           //21 : [5]
    $T128[] = array(2, 2, 3, 1, 1, 2);           //22 : [6]
    $T128[] = array(3, 1, 2, 1, 3, 1);           //23 : [7]
    $T128[] = array(3, 1, 1, 2, 2, 2);           //24 : [8]
    $T128[] = array(3, 2, 1, 1, 2, 2);           //25 : [9]
    $T128[] = array(3, 2, 1, 2, 2, 1);           //26 : [:]
    $T128[] = array(3, 1, 2, 2, 1, 2);           //27 : [;]
    $T128[] = array(3, 2, 2, 1, 1, 2);           //28 : [<]
    $T128[] = array(3, 2, 2, 2, 1, 1);           //29 : [=]
    $T128[] = array(2, 1, 2, 1, 2, 3);           //30 : [>]
    $T128[] = array(2, 1, 2, 3, 2, 1);           //31 : [?]
    $T128[] = array(2, 3, 2, 1, 2, 1);           //32 : [@]
    $T128[] = array(1, 1, 1, 3, 2, 3);           //33 : [A]
    $T128[] = array(1, 3, 1, 1, 2, 3);           //34 : [B]
    $T128[] = array(1, 3, 1, 3, 2, 1);           //35 : [C]
    $T128[] = array(1, 1, 2, 3, 1, 3);           //36 : [D]
    $T128[] = array(1, 3, 2, 1, 1, 3);           //37 : [E]
    $T128[] = array(1, 3, 2, 3, 1, 1);           //38 : [F]
    $T128[] = array(2, 1, 1, 3, 1, 3);           //39 : [G]
    $T128[] = array(2, 3, 1, 1, 1, 3);           //40 : [H]
    $T128[] = array(2, 3, 1, 3, 1, 1);           //41 : [I]
    $T128[] = array(1, 1, 2, 1, 3, 3);           //42 : [J]
    $T128[] = array(1, 1, 2, 3, 3, 1);           //43 : [K]
    $T128[] = array(1, 3, 2, 1, 3, 1);           //44 : [L]
    $T128[] = array(1, 1, 3, 1, 2, 3);           //45 : [M]
    $T128[] = array(1, 1, 3, 3, 2, 1);           //46 : [N]
    $T128[] = array(1, 3, 3, 1, 2, 1);           //47 : [O]
    $T128[] = array(3, 1, 3, 1, 2, 1);           //48 : [P]
    $T128[] = array(2, 1, 1, 3, 3, 1);           //49 : [Q]
    $T128[] = array(2, 3, 1, 1, 3, 1);           //50 : [R]
    $T128[] = array(2, 1, 3, 1, 1, 3);           //51 : [S]
    $T128[] = array(2, 1, 3, 3, 1, 1);           //52 : [T]
    $T128[] = array(2, 1, 3, 1, 3, 1);           //53 : [U]
    $T128[] = array(3, 1, 1, 1, 2, 3);           //54 : [V]
    $T128[] = array(3, 1, 1, 3, 2, 1);           //55 : [W]
    $T128[] = array(3, 3, 1, 1, 2, 1);           //56 : [X]
    $T128[] = array(3, 1, 2, 1, 1, 3);           //57 : [Y]
    $T128[] = array(3, 1, 2, 3, 1, 1);           //58 : [Z]
    $T128[] = array(3, 3, 2, 1, 1, 1);           //59 : [[]
    $T128[] = array(3, 1, 4, 1, 1, 1);           //60 : [\]
    $T128[] = array(2, 2, 1, 4, 1, 1);           //61 : []]
    $T128[] = array(4, 3, 1, 1, 1, 1);           //62 : [^]
    $T128[] = array(1, 1, 1, 2, 2, 4);           //63 : [_]
    $T128[] = array(1, 1, 1, 4, 2, 2);           //64 : [`]
    $T128[] = array(1, 2, 1, 1, 2, 4);           //65 : [a]
    $T128[] = array(1, 2, 1, 4, 2, 1);           //66 : [b]
    $T128[] = array(1, 4, 1, 1, 2, 2);           //67 : [c]
    $T128[] = array(1, 4, 1, 2, 2, 1);           //68 : [d]
    $T128[] = array(1, 1, 2, 2, 1, 4);           //69 : [e]
    $T128[] = array(1, 1, 2, 4, 1, 2);           //70 : [f]
    $T128[] = array(1, 2, 2, 1, 1, 4);           //71 : [g]
    $T128[] = array(1, 2, 2, 4, 1, 1);           //72 : [h]
    $T128[] = array(1, 4, 2, 1, 1, 2);           //73 : [i]
    $T128[] = array(1, 4, 2, 2, 1, 1);           //74 : [j]
    $T128[] = array(2, 4, 1, 2, 1, 1);           //75 : [k]
    $T128[] = array(2, 2, 1, 1, 1, 4);           //76 : [l]
    $T128[] = array(4, 1, 3, 1, 1, 1);           //77 : [m]
    $T128[] = array(2, 4, 1, 1, 1, 2);           //78 : [n]
    $T128[] = array(1, 3, 4, 1, 1, 1);           //79 : [o]
    $T128[] = array(1, 1, 1, 2, 4, 2);           //80 : [p]
    $T128[] = array(1, 2, 1, 1, 4, 2);           //81 : [q]
    $T128[] = array(1, 2, 1, 2, 4, 1);           //82 : [r]
    $T128[] = array(1, 1, 4, 2, 1, 2);           //83 : [s]
    $T128[] = array(1, 2, 4, 1, 1, 2);           //84 : [t]
    $T128[] = array(1, 2, 4, 2, 1, 1);           //85 : [u]
    $T128[] = array(4, 1, 1, 2, 1, 2);           //86 : [v]
    $T128[] = array(4, 2, 1, 1, 1, 2);           //87 : [w]
    $T128[] = array(4, 2, 1, 2, 1, 1);           //88 : [x]
    $T128[] = array(2, 1, 2, 1, 4, 1);           //89 : [y]
    $T128[] = array(2, 1, 4, 1, 2, 1);           //90 : [z]
    $T128[] = array(4, 1, 2, 1, 2, 1);           //91 : [{]
    $T128[] = array(1, 1, 1, 1, 4, 3);           //92 : [|]
    $T128[] = array(1, 1, 1, 3, 4, 1);           //93 : [}]
    $T128[] = array(1, 3, 1, 1, 4, 1);           //94 : [~]
    $T128[] = array(1, 1, 4, 1, 1, 3);           //95 : [DEL]
    $T128[] = array(1, 1, 4, 3, 1, 1);           //96 : [FNC3]
    $T128[] = array(4, 1, 1, 1, 1, 3);           //97 : [FNC2]
    $T128[] = array(4, 1, 1, 3, 1, 1);           //98 : [SHIFT]
    $T128[] = array(1, 1, 3, 1, 4, 1);           //99 : [Cswap]
    $T128[] = array(1, 1, 4, 1, 3, 1);           //100 : [Bswap]
    $T128[] = array(3, 1, 1, 1, 4, 1);           //101 : [Aswap]
    $T128[] = array(4, 1, 1, 1, 3, 1);           //102 : [FNC1]
    $T128[] = array(2, 1, 1, 4, 1, 2);           //103 : [Astart]
    $T128[] = array(2, 1, 1, 2, 1, 4);           //104 : [Bstart]
    $T128[] = array(2, 1, 1, 2, 3, 2);           //105 : [Cstart]
    $T128[] = array(2, 3, 3, 1, 1, 1);           //106 : [STOP]
    $T128[] = array(2, 1);                       //107 : [END BAR]


    for ($i = 32; $i <= 95; $i++) {                                            // jeux de caract�res
      $ABCset .= chr($i);
    }
    $Aset = $ABCset;
    $Bset = $ABCset;

    for ($i = 0; $i <= 31; $i++) {
      $ABCset .= chr($i);
      $Aset .= chr($i);
    }
    for ($i = 96; $i <= 127; $i++) {
      $ABCset .= chr($i);
      $Bset .= chr($i);
    }
    for ($i = 200; $i <= 210; $i++) {                                           // controle 128
      $ABCset .= chr($i);
      $Aset .= chr($i);
      $Bset .= chr($i);
    }
    $Cset = "0123456789" . chr(206);

    for ($i = 0; $i < 96; $i++) {                                                   // convertisseurs des jeux A & B
      @$SetFrom["A"] .= chr($i);
      @$SetFrom["B"] .= chr($i + 32);
      @$SetTo["A"] .= chr(($i < 32) ? $i + 64 : $i - 32);
      @$SetTo["B"] .= chr($i);
    }
    for ($i = 96; $i < 107; $i++) {                                                 // contr�le des jeux A & B
      @$SetFrom["A"] .= chr($i + 104);
      @$SetFrom["B"] .= chr($i + 104);
      @$SetTo["A"] .= chr($i);
      @$SetTo["B"] .= chr($i);
    }

    /////////////

    $Aguid = "";                                                                      // Cr�ation des guides de choix ABC
    $Bguid = "";
    $Cguid = "";
    for ($i = 0; $i < strlen($code); $i++) {
      $needle = substr($code, $i, 1);
      $Aguid .= ((strpos($Aset, $needle) === false) ? "N" : "O");
      $Bguid .= ((strpos($Bset, $needle) === false) ? "N" : "O");
      $Cguid .= ((strpos($Cset, $needle) === false) ? "N" : "O");
    }

    $SminiC = "OOOO";
    $IminiC = 4;

    $crypt = "";
    while ($code > "") {
      // BOUCLE PRINCIPALE DE CODAGE
      $i = strpos($Cguid, $SminiC);                                                // for�age du jeu C, si possible
      if ($i !== false) {
        $Aguid [$i] = "N";
        $Bguid [$i] = "N";
      }

      if (substr($Cguid, 0, $IminiC) == $SminiC) {                                  // jeu C
        $crypt .= chr(($crypt > "") ? $JSwap["C"] : $JStart["C"]);  // d�but Cstart, sinon Cswap
        $made = strpos($Cguid, "N");                                             // �tendu du set C
        if ($made === false) {
          $made = strlen($Cguid);
        }
        if (fmod($made, 2) == 1) {
          $made--;                                                            // seulement un nombre pair
        }
        for ($i = 0; $i < $made; $i += 2) {
          $crypt .= chr(strval(substr($code, $i, 2)));                          // conversion 2 par 2
        }
        $jeu = "C";
      } else {
        $madeA = strpos($Aguid, "N");                                            // �tendu du set A
        if ($madeA === false) {
          $madeA = strlen($Aguid);
        }
        $madeB = strpos($Bguid, "N");                                            // �tendu du set B
        if ($madeB === false) {
          $madeB = strlen($Bguid);
        }
        $made = (($madeA < $madeB) ? $madeB : $madeA);                         // �tendu trait�e
        $jeu = (($madeA < $madeB) ? "B" : "A");                                // Jeu en cours

        $crypt .= chr(($crypt > "") ? $JSwap[$jeu] : $JStart[$jeu]); // d�but start, sinon swap

        $crypt .= strtr(substr($code, 0, $made), $SetFrom[$jeu], $SetTo[$jeu]); // conversion selon jeu

      }
      $code = substr($code, $made);                                           // raccourcir l�gende et guides de la zone trait�e
      $Aguid = substr($Aguid, $made);
      $Bguid = substr($Bguid, $made);
      $Cguid = substr($Cguid, $made);
    }                                                                          // FIN BOUCLE PRINCIPALE

    $check = ord($crypt[0]);                                                   // calcul de la somme de contr�le
    for ($i = 0; $i < strlen($crypt); $i++) {
      $check += (ord($crypt[$i]) * $i);
    }
    $check %= 103;

    $crypt .= chr($check) . chr(106) . chr(107);                               // Chaine crypt�e compl�te

    $i = (strlen($crypt) * 11) - 8;                                            // calcul de la largeur du module
    $modul = $w / $i;
    $Rect = "";
    for ($i = 0; $i < strlen($crypt); $i++) {                                      // BOUCLE D'IMPRESSION
      $c = $T128[ord($crypt[$i])];
      for ($j = 0; $j < count($c); $j++) {
        $this->Rect($x, $y, $c[$j] * $modul, $h, "F");
        $x += ($c[$j++] + $c[$j]) * $modul;
      }
    }
  }


  /*
     Informations
Author: The-eh
License: Freeware
Description
This script implements Code 39 barcodes. A Code 39 barcode can encode a string with the following characters: digits (0 to 9), uppercase letters (A to Z) and 8 additional characters (- . space $ / + % *).

Code39(float xpos, float ypos, string code [, float baseline [, float height]])
xpos: abscissa of barcode
ypos: ordinate of barcode
code: value of barcode
baseline: corresponds to the width of a wide bar (defaults to 0.5)
height: bar height (defaults to 5) 
   */
  public function Code39($xpos, $ypos, $code, $baseline=0.5, $height=5, $printText=false){

    if(preg_replace("/[^0-9a-zA-Z_\s%*+-\/$]/", "", $code)!=$code) return;
    

    $wide = $baseline;
    $narrow = $baseline / 3 ;
    $gap = $narrow;

    $barChar['0'] = 'nnnwwnwnn';
    $barChar['1'] = 'wnnwnnnnw';
    $barChar['2'] = 'nnwwnnnnw';
    $barChar['3'] = 'wnwwnnnnn';
    $barChar['4'] = 'nnnwwnnnw';
    $barChar['5'] = 'wnnwwnnnn';
    $barChar['6'] = 'nnwwwnnnn';
    $barChar['7'] = 'nnnwnnwnw';
    $barChar['8'] = 'wnnwnnwnn';
    $barChar['9'] = 'nnwwnnwnn';
    $barChar['A'] = 'wnnnnwnnw';
    $barChar['B'] = 'nnwnnwnnw';
    $barChar['C'] = 'wnwnnwnnn';
    $barChar['D'] = 'nnnnwwnnw';
    $barChar['E'] = 'wnnnwwnnn';
    $barChar['F'] = 'nnwnwwnnn';
    $barChar['G'] = 'nnnnnwwnw';
    $barChar['H'] = 'wnnnnwwnn';
    $barChar['I'] = 'nnwnnwwnn';
    $barChar['J'] = 'nnnnwwwnn';
    $barChar['K'] = 'wnnnnnnww';
    $barChar['L'] = 'nnwnnnnww';
    $barChar['M'] = 'wnwnnnnwn';
    $barChar['N'] = 'nnnnwnnww';
    $barChar['O'] = 'wnnnwnnwn';
    $barChar['P'] = 'nnwnwnnwn';
    $barChar['Q'] = 'nnnnnnwww';
    $barChar['R'] = 'wnnnnnwwn';
    $barChar['S'] = 'nnwnnnwwn';
    $barChar['T'] = 'nnnnwnwwn';
    $barChar['U'] = 'wwnnnnnnw';
    $barChar['V'] = 'nwwnnnnnw';
    $barChar['W'] = 'wwwnnnnnn';
    $barChar['X'] = 'nwnnwnnnw';
    $barChar['Y'] = 'wwnnwnnnn';
    $barChar['Z'] = 'nwwnwnnnn';
    $barChar['-'] = 'nwnnnnwnw';
    $barChar['.'] = 'wwnnnnwnn';
    $barChar[' '] = 'nwwnnnwnn';
    $barChar['*'] = 'nwnnwnwnn';
    $barChar['$'] = 'nwnwnwnnn';
    $barChar['/'] = 'nwnwnnnwn';
    $barChar['+'] = 'nwnnnwnwn';
    $barChar['%'] = 'nnnwnwnwn';
    //$this->SetFont('Arial', '', 10);
    if($printText) $this->Text($xpos, $ypos + $height + 4, $code);
    $this->SetFillColor(0);

    $code = '*'.strtoupper($code).'*';
    for($i=0; $i<strlen($code); $i++){
      $char = $code[$i];
      if(!isset($barChar[$char])){
        $this->Error('Invalid character in barcode: '.$char);
      }
      $seq = $barChar[$char];
      for($bar=0; $bar<9; $bar++){
        if ($seq[$bar] == 'n') {
          $lineWidth = $narrow;
        }else{
          $lineWidth = $wide;
        }
        if($bar % 2 == 0){
          $this->Rect($xpos, $ypos, $lineWidth, $height, 'F');
        }
        $xpos += $lineWidth;
      }
      $xpos += $gap;
    }
  }

}


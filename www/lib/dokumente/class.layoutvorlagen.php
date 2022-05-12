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

class LayoutvorlagenPDF extends SuperFPDF {
  public $doctype;
  public $doctypeOrig;

  public $logofile;
  public $sender;
  public $recipient;
  public $corrDetails;
  public $InfoBox;
  public $endInfoBox;
  public $boldCorrDetails;
  public $textDetails;
  public $items;

  public $ust_befreit;

  public $barcode;
  public $firmendatenid;
  public $absender;
  public $id;
  public $hintergrund;
  public $positionen;
  public $vorlage;
  public $language;
  public $aktFillColor;
  public $aktDrawColor;
  public $aktTextColor;
  public $aktFontSize;
  public $aktFont;
  public $addpdf = null;
  public $pagecount;
  public $page2file;

  /***********************************
   *     data aggregation functions
   ***********************************/  
  function __construct($app,$projekt="") {

    $this->app=$app;
    $this->language = 'deutsch';
  }

  function SetLanguage($language)
  {
    if($language == 'deutsch')$this->language = $language;else $this->language = 'englisch';
  }
  
  function GetLayoutvorlage($id,$elementlist = array())
  {
    $this->id = (int)$id;

    if(!$this->id)return false;
    $vorlage = $this->app->DB->SelectArr("select * from layoutvorlagen where id = ".$this->id. " limit 1");

    if(!isset($vorlage[0]))return false;

    $this->vorlage = $vorlage[0];
    $this->positionen = $this->app->DB->SelectArr("select * from layoutvorlagen_positionen where layoutvorlage = '".$this->id."' order by position_y,  sort");
    if(count($elementlist) > 0)
    {
      foreach($this->positionen as $key => $position)
      {

        $pattern = '/((\{[a-zA-Z0-9_]+[^\}]*\}))/';
        preg_match_all($pattern,$this->positionen[$key]['inhalt_deutsch'],$matches);
        
        for($i=0;$i<count($matches[0]);$i++)
        {
          $found = false;
          foreach($elementlist as $k => $el){
            if('{'.$k.'}' == $matches[0][$i])$found = true;
            if('{'.strtoupper($k).'}' == $matches[0][$i])$found = true;
          }
          if($found && $position['typ'] === 'bild' && empty($position['bild_deutsch'])
            && empty($position['bild_englisch'])
          ) {
            $position['image'] = true;
          }
          
          if(!$found)$this->positionen[$key]['inhalt_deutsch'] = str_replace($matches[0][$i], '', $this->positionen[$key]['inhalt_deutsch']);
        }
        preg_match_all($pattern,$this->positionen[$key]['inhalt_englisch'],$matches);
        for($i=0;$i<count($matches[0]);$i++)
        {
          $found = false;
          foreach($elementlist as $k => $el){
            if('{'.$k.'}' == $matches[0][$i])$found = true;
            if('{'.strtoupper($k).'}' == $matches[0][$i])$found = true;
            
          }
          if(!$found)$this->positionen[$key]['inhalt_englisch'] = str_replace($matches[0][$i], '', $this->positionen[$key]['inhalt_englisch']);
        }
        foreach($elementlist as $k => $el)
        {
          $this->positionen[$key]['inhalt_deutsch'] = str_replace('{'.$k.'}', $el, $this->positionen[$key]['inhalt_deutsch']);
          $this->positionen[$key]['inhalt_englisch'] = str_replace('{'.$k.'}', $el, $this->positionen[$key]['inhalt_englisch']);
          $this->positionen[$key]['inhalt_deutsch'] = str_replace('{'.strtoupper($k).'}', $el, $this->positionen[$key]['inhalt_deutsch']);
          $this->positionen[$key]['inhalt_englisch'] = str_replace('{'.strtoupper($k).'}', $el, $this->positionen[$key]['inhalt_englisch']);
   
        }
        if(!empty($position['image']) && !empty($this->positionen[$key]['inhalt_deutsch'])) {
          $this->positionen[$key]['bild_deutsch'] = $this->positionen[$key]['inhalt_deutsch'];
          $this->positionen[$key]['inhalt_deutsch'] = '';
        }
      }
    }
    return true;
  }
  
  function SetSchriftgroesse($size)
  {
    if($size > 0 && $size != $this->aktFontSize)
    {
      $this->aktFontSize = $size;
      $this->SetFontSize($size);
    }
      
  }
  
  function SetColors($text, $draw, $fill)
  {
      
    if($text && $this->aktTextColor != $text)
    {
        
      $this->aktTextColor = $text;
      
      $rgb = hex2dec($text);
      
      $this->SetTextColor($rgb['R'], $rgb['G'], $rgb['B']);
    }
    
    if($draw && $this->aktDrawColor != $draw)
    {
      $this->aktDrawColor = $draw;
      $rgb = hex2dec($draw);
      $this->SetDrawColor($rgb['R'], $rgb['G'], $rgb['B']);
    }
    if($fill && $this->aktFillColor != $fill)
    {
      $this->aktFillColor = $fill;
      $rgb = hex2dec($fill);
      $this->SetFillColor($rgb['R'], $rgb['G'], $rgb['B']);
    }    
    
  }


  function renderDocument()
  {
    
    $schriftarten = $this->app->erp->GetFonts();

    //$hintergrund = $this->app->DB->Select("SELECT hintergrund FROM firmendaten WHERE id='".$this->firmendatenid."' LIMIT 1");    
    // Multicell

    $this->vorlage['format'] = strtoupper($this->vorlage['format']);
    if (strpos($this->vorlage['format'], 'L') !== false) {
      $this->vorlage['format'] = str_replace('L','',$this->vorlage['format']); 
      parent::__construct('L','mm',$this->vorlage['format']);
    } else {
      parent::__construct('P','mm',$this->vorlage['format']);
    }

    $this->SetAutoPageBreak(true,0);
    $this->aktDrawColor = '#000000';
    $this->aktFillColor = '#FFFFFF';
    $this->aktFont = 'arial';
    $this->aktTextColor = '#000000';
    $this->SetDrawColor(0,0,0);
    $this->SetTextColor(0,0,0);
    $this->setFillColor(1,1,1);
    $this->SetFont('arial');
    $this->SetFontSize(12);
    $this->AddPage();
    if($this->vorlage['pdf_hintergrund'])
    {
      $this->SetXY(0,0);
      $pdf = base64_decode($this->vorlage['pdf_hintergrund']);

      
      $filename = '/tmp/'.md5($position['id'].$position['pdf_'.$this->language]).'.pdf';
      //$this->app->erp->LogFile($filename);
      if($fp = fopen($filename, 'wb')) 
      {
        fwrite($fp, $pdf);
        //$this->app->erp->LogFile("write");
      }
      if(file_exists($filename) && is_file($filename)){
        //$this->app->erp->LogFile("pdf_parser");
        $this->pagecount = $this->setSourceFile($filename);
        if($this->pagecount > 1)$this->page2file = $filename;
        $tplidx = $this->ImportPage(1);
        $this->useTemplate($tplidx); 
        //$this->app->erp->LogFile("parsed");
      } 
    }

    $altpage = 1;
    foreach($this->positionen as $key => $position)
    {
      //$this->app->erp->LogFile($this->positionen[$key]['typ']);
      if($this->positionen[$key]['sichtbar']) {
        //$this->app->erp->LogFile("h: ".$this->h);
        /*if($this->positionen[$key]['position_y'] + $this->positionen[$key]['hoehe'] > $this->h)
        {
          $addy = $this->tMargin + $this->h - $this->positionen[$key]['position_y'];
          
          $found = false;
          foreach($this->positionen as $k => $v)
          {
            if(!$found)
            {
              if($k == $key)$found = true;
            } else {
              //$this->app->erp->LogFile("Addy: ".$addy);
              if(isset($this->positionen[$k]['position_y']) && $this->positionen[$k]['position_y'] > $this->positionen[$key]['position_y'])
              {
                $alty = $this->positionen[$k]['position_y'];
                $this->positionen[$k]['position_y'] += $addy;
                //$this->app->erp->LogFile("allg alty: ".$alty." neu: ".$this->positionen[$k]['position_y']." addy: ".$addy);
              }
            }
          }
          $this->positionen[$key]['position_y'] += $addy; 
        }*/
        switch($this->positionen[$key]['typ'])
        {
          
          case 'textfeld':

            switch($this->positionen[$key]['schrift_align'])
            {
              case 'center':
                  $align = 'C';
              break;
              case 'right':
                  $align = 'R';
              break;
              default:
                  $align = 'L';
              break;
            }

            if($this->language !== 'deutsch' && empty($this->positionen[$key]['inhalt_'.$this->language])){
              $this->positionen[$key]['inhalt_' . $this->language] = $this->positionen[$key]['inhalt_deutsch'];
            }elseif($this->language === 'deutsch' && empty($this->positionen[$key]['inhalt_'.$this->language]))
            {
              $this->positionen[$key]['inhalt_' . $this->language] = $this->positionen[$key]['inhalt_englisch'];
            }
            
            //$this->app->erp->LogFile("Page: ".$this->page." y: ".$this->positionen[$key]['position_y']);
            $posy_rel =  $this->positionen[$key]['position_y'] - $this->h*($this->page-1);
            //$this->app->erp->LogFile("text y: ".$this->positionen[$key]['position_y']." rel: ".$posy_rel);
        
            $this->SetXY($this->positionen[$key]['position_x'], $posy_rel);
            $this->SetColors($this->positionen[$key]['schrift_farbe'],$this->positionen[$key]['rahmen_farbe'],$this->positionen[$key]['hintergrund_farbe']);
            $this->SetLineWidth($this->positionen[$key]['rahmen']);

            $this->SetXY($this->positionen[$key]['position_x'], $posy_rel);
            $this->SetSchriftgroesse($this->positionen[$key]['schrift_groesse']);
            $style = ($this->positionen[$key]['schrift_fett']?'B':'').($this->positionen[$key]['schrift_kursiv']?'I':'');
            
            if(isset($this->positionen[$key]['schrift_art']) && $this->positionen[$key]['schrift_art'] && $this->positionen[$key]['schrift_art'] != 'arial' && $this->positionen[$key]['schrift_art'] != 'times')
            {
              
              if($style)
              {
                if(!isset($this->FontFiles[$this->positionen[$key]['schrift_art'].$style])&&!isset($this->FontFiles[strtolower($this->positionen[$key]['schrift_art']).$style]) &&isset($schriftarten[$this->positionen[$key]['schrift_art']][strtolower($style)]))
                {
                  
                  $this->AddFont($this->positionen[$key]['schrift_art'],$style,$this->positionen[$key]['schrift_art'].strtolower($style).'.php');
                }
              }else{
                if(!isset($this->FontFiles[$this->positionen[$key]['schrift_art'].'.z'])&&!isset($this->FontFiles[strtolower($this->positionen[$key]['schrift_art'])]))
                {
                  
                  $this->AddFont($this->positionen[$key]['schrift_art'],'',$this->positionen[$key]['schrift_art'].'.php');
                }
              }
            }
            /*
            $dummyt = 'fonts: ';
            foreach($this->fonts as $kkk => $vvv)
            {
              $dummyt .= ", ".$kkk;
              
            }
            $this->app->erp->LogFile($dummyt);*/
            if(($this->positionen[$key]['schrift_art']?$this->positionen[$key]['schrift_art']:'arial') == 'arial')
            {
              
              $this->SetFont($this->positionen[$key]['schrift_art']?$this->positionen[$key]['schrift_art']:'arial',($this->positionen[$key]['schrift_underline'] == 1?'U':'').($this->positionen[$key]['schrift_kursiv']?'I':'').($this->positionen[$key]['schrift_fett']?'B':''));
            } else {
              if(isset($this->fonts[$this->positionen[$key]['schrift_art'].$style])||isset($this->fonts[strtolower($this->positionen[$key]['schrift_art']).$style]))
              {
                
                if(isset($this->fonts[$this->positionen[$key]['schrift_art'].$style]))
                {
                  
                  $this->SetFont($this->positionen[$key]['schrift_art']?$this->positionen[$key]['schrift_art']:'arial',($this->positionen[$key]['schrift_underline'] == 1?'U':'').($this->positionen[$key]['schrift_kursiv']?'I':'').($this->positionen[$key]['schrift_fett']?'B':''));
                }else{
                  
                  $this->SetFont(strtolower($this->positionen[$key]['schrift_art'])?strtolower($this->positionen[$key]['schrift_art']):'arial',($this->positionen[$key]['schrift_underline'] == 1?'U':'').($this->positionen[$key]['schrift_kursiv']?'I':'').($this->positionen[$key]['schrift_fett']?'B':''));
                }
              }else{
                if(isset($this->fonts[$this->positionen[$key]['schrift_art']])||isset($this->fonts[strtolower($this->positionen[$key]['schrift_art'])]))
                {
                  if(isset($this->fonts[$this->positionen[$key]['schrift_art']]))
                  {
                    
                    $this->SetFont($this->positionen[$key]['schrift_art']?$this->positionen[$key]['schrift_art']:'arial',($this->positionen[$key]['schrift_underline'] == 1?'U':'').($this->positionen[$key]['schrift_kursiv']?'':'').($this->positionen[$key]['schrift_fett']?'':''));
                  }else{
                    
                    $this->SetFont(strtolower($this->positionen[$key]['schrift_art'])?strtolower($this->positionen[$key]['schrift_art']):'arial',($this->positionen[$key]['schrift_underline'] == 1?'U':'').($this->positionen[$key]['schrift_kursiv']?'':'').($this->positionen[$key]['schrift_fett']?'':''));
                  }
                  
                } else {
                  
                  $this->SetFont('arial',($this->positionen[$key]['schrift_underline'] == 1?'U':'').($this->positionen[$key]['schrift_kursiv']?'I':'').($this->positionen[$key]['schrift_fett']?'B':''));  
                }
              }
            }
            
            //$this->positionen[$key]['inhalt_'.$this->language] = html_entity_decode($this->positionen[$key]['inhalt_'.$this->language]);
            $xbevor = $this->GetX();
            $ybevor = $this->GetY();
            $pvor = $this->page;
            //$this->Multicell($this->positionen[$key]['breite'],isset($this->positionen[$key]['zeilen_hoehe']) && $this->positionen[$key]['zeilen_hoehe']?$this->positionen[$key]['zeilen_hoehe']:5, ($this->WriteHTMLCell($xbevor,($this->app->erp->ReadyForPDF(($this->positionen[$key]['inhalt_'.$this->language]))))),(!empty($this->positionen[$key]['rahmen_farbe'])?1:0), $align,(!empty($this->positionen[$key]['hintergrund_farbe'])?1:0)); //$this->WriteHTML
            //$this->Multicell($this->positionen[$key]['breite'],isset($this->positionen[$key]['zeilen_hoehe']) && $this->positionen[$key]['zeilen_hoehe']?$this->positionen[$key]['zeilen_hoehe']:5, $this->app->erp->ReadyForPDF(($this->positionen[$key]['inhalt_'.$this->language])),(!empty($this->positionen[$key]['rahmen_farbe'])?1:0), $align,(!empty($this->positionen[$key]['hintergrund_farbe'])?1:0)); //$this->WriteHTML
           
            if(!empty($this->positionen[$key]['rahmen_farbe'])|| !empty($this->positionen[$key]['hintergrund_farbe']))
            {
              if($this->positionen[$key]['rahmen'] == 0)$this->positionen[$key]['rahmen_farbe'] = '#ffffff';
              //$this->app->erp->LogFile("rechteck y: ".$this->positionen[$key]['position_y']." rel: ".$posy_rel);
              $this->SetLineWidth($this->positionen[$key]['rahmen']/10);
              $this->SetColors($this->positionen[$key]['schrift_farbe'],$this->positionen[$key]['rahmen_farbe'],$this->positionen[$key]['hintergrund_farbe']);
              $this->Rect($this->positionen[$key]['position_x'], $posy_rel,$this->positionen[$key]['breite'],$this->positionen[$key]['hoehe'],empty($this->positionen[$key]['hintergrund_farbe']) || strtolower($this->positionen[$key]['hintergrund_farbe']) == '#ffffff'?'B':'F');
              
            }
            $this->SetXY($xbevor,$ybevor);
            
            if($this->positionen[$key]['zeichenbegrenzung'] && $this->positionen[$key]['zeichenbegrenzung_anzahl'])
            {
              $this->positionen[$key]['inhalt_'.$this->language] = substr($this->positionen[$key]['inhalt_'.$this->language],0,$this->positionen[$key]['zeichenbegrenzung_anzahl']);
            }
            
            if($this->app->erp->isHTML($this->positionen[$key]['inhalt_'.$this->language])){
              $this->Multicell($this->positionen[$key]['breite'],isset($this->positionen[$key]['zeilen_hoehe']) && $this->positionen[$key]['zeilen_hoehe']?$this->positionen[$key]['zeilen_hoehe']:5, $this->WriteHTML(($this->positionen[$key]['inhalt_'.$this->language])),0, $align,0); //$this->WriteHTML
            }else{
              $this->Multicell($this->positionen[$key]['breite'],isset($this->positionen[$key]['zeilen_hoehe']) && $this->positionen[$key]['zeilen_hoehe']?$this->positionen[$key]['zeilen_hoehe']:5, $this->app->erp->ReadyForPDF($this->positionen[$key]['inhalt_'.$this->language]),0, $align,0); //$this->WriteHTML
            }
            
            //$this->app->erp->LogFile($this->positionen[$key]['breite'].' '.(isset($this->positionen[$key]['zeilen_hoehe']) && $this->positionen[$key]['zeilen_hoehe']?$this->positionen[$key]['zeilen_hoehe']:5).' '.($this->WriteHTMLCell($xbevor,($this->app->erp->ReadyForPDF(($this->positionen[$key]['inhalt_'.$this->language]))))).' '.(!empty($this->positionen[$key]['rahmen_farbe'])?1:0).' '.$align);
            $xnach = $this->GetX();
            $ynach = $this->GetY();
            $pnach = $this->page;
            if($pnach == $pvor && $ynach < $ybevor + $this->positionen[$key]['hoehe'])$this->SetY($ybevor + $this->positionen[$key]['hoehe']);
            
            //$addy = ($ynach - $ybevor + $this->h*($pnach - $pvor)) - $this->positionen[$key]['hoehe'];
            
            //$this->app->erp->LogFile("Bevor: ".$xbevor.", ".$ybevor." nach: ".$xnach.", ".$ynach." Text:".addslashes($this->positionen[$key]['inhalt_'.$this->language]));
            if($this->positionen[$key]['hoehe'] > 0 && ($ynach - $ybevor + $this->h*($pnach - $pvor)) > $this->positionen[$key]['hoehe'])
            {
              $addy = ($ynach - $ybevor + $this->h*($pnach - $pvor)) - $this->positionen[$key]['hoehe']; 
              //$this->app->erp->LogFile("addy: ".$addy." hoehe: ".$this->positionen[$key]['hoehe']." Bevor: ".$xbevor.", ".$ybevor." nach: ".$xnach.", ".$ynach." Text:".addslashes($this->positionen[$key]['inhalt_'.$this->language]));
              $found = false;
              foreach($this->positionen as $k => $v)
              {
                if(!$found)
                {
                  if($k == $key)$found = true;
                } else {
                  if(isset($this->positionen[$k]['position_y']) && $this->positionen[$k]['position_y'] > $this->positionen[$key]['position_y'])
                  {
                    $alty = $this->positionen[$k]['position_y'];
                    $this->positionen[$k]['position_y'] += $addy;
                  }
                }
              }
            }
          break;
          case 'barcode':
            $posy_rel =  $this->positionen[$key]['position_y'] - $this->h*($this->page-1);
            //$this->app->erp->LogFile("text y: ".$this->positionen[$key]['position_y']." rel: ".$posy_rel);
        
            //$this->SetXY($this->positionen[$key]['position_x'], $posy_rel);
            // fuer Anzeige Barcode
            $this->positionen[$key]['inhalt_'.$this->language] = preg_replace('/\{([^{}]*+|(?R))*\}\s*/', '', $this->positionen[$key]['inhalt_'.$this->language]);
          
            $xbevor = $this->GetX();
            $ybevor = $this->GetY();
            $pvor = $this->page;
            $this->Code39($this->positionen[$key]['position_x'],$posy_rel, ($this->app->erp->ReadyForPDF(($this->positionen[$key]['inhalt_'.$this->language]))), 0.9, ($this->positionen[$key]['schrift_groesse']?$this->positionen[$key]['schrift_groesse']:5));
            $xnach = $this->GetX();
            $ynach = $this->GetY();
            $pnach = $this->page;
            
            //$addy = ($ynach - $ybevor + $this->h*($pnach - $pvor)) - $this->positionen[$key]['hoehe'];
            
            //$this->app->erp->LogFile("Bevor: ".$xbevor.", ".$ybevor." nach: ".$xnach.", ".$ynach." Text:".addslashes($this->positionen[$key]['inhalt_'.$this->language]));
            if($this->positionen[$key]['hoehe'] > 0 && ($ynach - $ybevor + $this->h*($pnach - $pvor)) > $this->positionen[$key]['hoehe'])
            {
              $addy = ($ynach - $ybevor + $this->h*($pnach - $pvor)) - $this->positionen[$key]['hoehe']; 
              //$this->app->erp->LogFile("addy: ".$addy." hoehe: ".$this->positionen[$key]['hoehe']." Bevor: ".$xbevor.", ".$ybevor." nach: ".$xnach.", ".$ynach." Text:".addslashes($this->positionen[$key]['inhalt_'.$this->language]));
              $found = false;
              foreach($this->positionen as $k => $v)
              {
                if(!$found)
                {
                  if($k == $key)$found = true;
                } else {
                  //$this->app->erp->LogFile("Addy: ".$addy);
                  if(isset($this->positionen[$k]['position_y']) && $this->positionen[$k]['position_y'] > $this->positionen[$key]['position_y'])
                  {
                    $alty = $this->positionen[$k]['position_y'];
                    $this->positionen[$k]['position_y'] += $addy;
                    //$this->app->erp->LogFile("Text alty: ".$alty." neu: ".$this->positionen[$k]['position_y']." addy: ".$addy);
                  }
                }
              }
            }
          
          break;
          case 'linie':
            $posy_rel =  $this->positionen[$key]['position_y'] - $this->h*($this->page-1);
            //$this->app->erp->LogFile("linie y: ".$this->positionen[$key]['position_y']." rel: ".$posy_rel);
            $this->SetLineWidth($this->positionen[$key]['rahmen']/10);
            if($this->positionen[$key]['rahmen_farbe']){
              $this->SetColors($this->positionen[$key]['schrift_farbe'],$this->positionen[$key]['rahmen_farbe'],$this->positionen[$key]['hintergrundfarbe']);
              $this->Line($this->positionen[$key]['position_x'], $posy_rel,$this->positionen[$key]['position_x']+ $this->positionen[$key]['breite'],$posy_rel+$this->positionen[$key]['hoehe']);
            }
          break;
          case 'rechteck';
            $posy_rel =  $this->positionen[$key]['position_y'] - $this->h*($this->page-1);
            //$this->app->erp->LogFile("rechteck y: ".$this->positionen[$key]['position_y']." rel: ".$posy_rel);
            $this->SetLineWidth($this->positionen[$key]['rahmen']/10);
            if($this->positionen[$key]['rahmen_farbe']){
              $this->SetColors($this->positionen[$key]['schrift_farbe'],$this->positionen[$key]['rahmen_farbe'],$this->positionen[$key]['hintergrund_farbe']);
              $this->Rect($this->positionen[$key]['position_x'], $posy_rel,$this->positionen[$key]['breite'],$this->positionen[$key]['hoehe'],empty($this->positionen[$key]['hintergrund_farbe'])?'B':'F');
            }
          break;
          case 'bild':
            $posy_rel =  $this->positionen[$key]['position_y'] - $this->h*($this->page-1);
            //$this->app->erp->LogFile("bild y: ".$this->positionen[$key]['position_y']." rel: ".$posy_rel);
            $bildlang = $this->language;
            if(!$this->positionen[$key]['bild_'.$this->language])$bildlang = $this->language == 'englisch'?'deutsch':'englisch';
        
            if($this->positionen[$key]['bild_'.$bildlang])
            {
              $type = $this->positionen[$key]['bild_'.$bildlang.'_typ'];
              $bild = base64_decode($this->positionen[$key]['bild_'.$bildlang]);
              
              
              $im = imagecreatefromstring($bild);
              if ($im !== false) {
                $type = strtolower($type);
                $type = '';
                if($type == '')$type = $this->get_img_type($bild);
                if($type == 'jpg')$type = 'jpeg';
                $this->SetXY($this->positionen[$key]['position_x'], $posy_rel);
                
                switch(strtolower($type)){
                  case "png": 
                    imagepng($im, '/tmp/'.md5($this->positionen[$key]['id'].$this->positionen[$key]['bild_'.$bildlang]).'.png');
                    $size = getimagesize('/tmp/'.md5($this->positionen[$key]['id'].$this->positionen[$key]['bild_'.$bildlang]).'.png');
                    if($this->positionen[$key]['breite'] > 0 && $this->positionen[$key]['hoehe'] > 0)
                    {
                      $ratiob = $size[0]/$this->positionen[$key]['breite'];
                      $ratioh = $size[1]/$this->positionen[$key]['hoehe'];
                      if($ratiob < $ratioh){
                        $newbreite = $ratiob / $ratioh * $this->positionen[$key]['breite'];
                        $this->SetXY($this->positionen[$key]['position_x']+ ($this->positionen[$key]['breite']-$newbreite) / 2, $posy_rel);
                        $this->positionen[$key]['breite'] = $newbreite;
                      } else {
                        $newhoehe = $ratioh / $ratiob * $this->positionen[$key]['hoehe'];
                        $this->SetXY($this->positionen[$key]['position_x'], $posy_rel+ ($this->positionen[$key]['hoehe']-$newhoehe) / 2);
                        $this->positionen[$key]['hoehe'] = $newhoehe;
                      }
                    }
                    //$this->app->erp->LogFile($this->GetX().'  '. $this->GetY());
                    $this->Image('/tmp/'.md5($this->positionen[$key]['id'].$this->positionen[$key]['bild_'.$bildlang]).'.png',$this->GetX(), $this->GetY(),$this->positionen[$key]['breite'],$this->positionen[$key]['hoehe'],'png');
                    unlink('/tmp/'.md5($this->positionen[$key]['id'].$this->positionen[$key]['bild_'.$bildlang]).'.png');
                  break;
                  case "jpeg": case "jpg": 
                    $bla = imagejpeg($im, '/tmp/'.md5($this->positionen[$key]['id'].$this->positionen[$key]['bild_'.$bildlang]).'.jpg');
                    $size = getimagesize('/tmp/'.md5($this->positionen[$key]['id'].$this->positionen[$key]['bild_'.$bildlang]).'.jpg');
                    if($this->positionen[$key]['breite'] > 0 && $this->positionen[$key]['hoehe'] > 0)
                    {
                      $ratiob = $size[0]/$this->positionen[$key]['breite'];
                      $ratioh = $size[1]/$this->positionen[$key]['hoehe'];
                      if($ratiob < $ratioh){
                        $newbreite = $ratiob / $ratioh * $this->positionen[$key]['breite'];
                        //$this->app->erp->LogFile("breite: ".$this->GetX().'  '. $this->GetY());
                        $this->SetXY($this->positionen[$key]['position_x']+ ($this->positionen[$key]['breite']-$newbreite) / 2, $posy_rel);
                        //$this->app->erp->LogFile("breite: ".$this->GetX().'  '. $this->GetY());
                        $this->positionen[$key]['breite'] = $newbreite;
                      } else {
                        $newhoehe = $ratioh / $ratiob * $this->positionen[$key]['hoehe'];
                        //$this->app->erp->LogFile("hoehe: ".$this->GetX().'  '. $this->GetY());
                        $this->SetXY($this->positionen[$key]['position_x'], $posy_rel+ ($this->positionen[$key]['hoehe']-$newhoehe) / 2);
                        //$this->app->erp->LogFile("hoehe: ".$this->GetX().'  '. $this->GetY());
                        $this->positionen[$key]['hoehe'] = $newhoehe;
                      }
                    }
                    //$this->app->erp->LogFile($this->GetX().'  '. $this->GetY());
                    $this->Image('/tmp/'.md5($this->positionen[$key]['id'].$this->positionen[$key]['bild_'.$bildlang]).'.jpg',$this->GetX(), $this->GetY(),$this->positionen[$key]['breite'],$this->positionen[$key]['hoehe'],'jpg');
                    unlink('/tmp/'.md5($this->positionen[$key]['id'].$this->positionen[$key]['bild_'.$bildlang]).'.jpg');
                  break;
                  default:
                  
                  break;
                }
                imagedestroy($im);
              }
            }
          break;   
        }
      }
    }
    if($this->addpdf)
    {
      foreach($this->addpdf as $addpdf)
      {
        $filename = $addpdf;
        $this->AddPage();
        $this->SetXY(0,0);
        $this->setSourceFile($filename);
        
        $tplidx = $this->ImportPage(1);
        $this->useTemplate($tplidx); 
      }
    }
    if(isset($this->pagecount) && $this->pagecount > 1 && $this->page < $this->pagecount)
    {
      for($i = $this->page; $i < $this->pagecount; $i++)$this->AddPage();
    }
  }

  function get_img_type($data) {
    $magics = array(
        'ffd8ff' => 'jpg',
        '89504e470d0a1a0a' => 'png',
    );
       
    foreach ($magics as $str => $ext) {
      if (strtolower(bin2hex(substr($data, 0, strlen($str)/2))) == $str) return $ext;
    }
       
    return NULL;
  }

  public function Header() {
    if($this->PageNo() > 1 && $this->pagecount > 1 && $this->page2file != '')
    {
      $this->setSourceFile($this->page2file);
      if($this->pagecount && $this->pagecount > 2 && $this->PageNo() <= $this->pagecount)
      {
        $tplidx = $this->ImportPage($this->PageNo());
      }else{
        $tplidx = $this->ImportPage(2);
      }
      $this->useTemplate($tplidx);
    }
  }  

  public function Footer() {

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

  public function inlineDocument($name = null) {
    $this->renderDocument();
    if(!is_null($name))
    {
      $this->Output($this->app->erp->GetTMP().$name,'F');
      return true;
    }else{
      header('Content-type: application/pdf');
      $this->Output();
    }
    exit;
  }

  public function ArchiviereDocument($schreibschutz = false)
  {
    $this->filename = $this->app->erp->Dateinamen($this->filename);
    $dir = $this->app->Conf->WFuserdata."/pdfarchiv/".$this->app->Conf->WFdbname;
    if(!is_dir($dir)){
      if(!mkdir($dir, 0700,true))$this->app->erp->LogFile('Fehler beim erstellen von '.$dir);
      //				echo "fehlt";
    }
    if(!is_dir($dir))return false;
    if(!is_dir($dir."/".$this->table))
      if(!mkdir ($dir."/".$this->table,0700,true))$this->app->erp->LogFile('Fehler beim erstellen von '.$dir."/".$this->table);
    if(!is_dir($dir."/".$this->table))return;
    $md5alt = false;
    $altesdokument = $this->app->DB->SelectArr("SELECT * from pdfarchiv where table_id = '".$this->id."'  and table_name = '".$this->table."' AND doctype = '".$this->app->DB->real_escape_string($this->doctype)."' AND doctypeorig = '".$this->app->DB->real_escape_string($this->doctypeOrig)."' ORDER BY zeitstempel DESC LIMIT 1");
    if($altesdokument)
    {
      $altesdokument = reset($altesdokument);
      $md5alt = $altesdokument['checksum'];
      if(!is_file($dir."/".$this->table."/".$md5alt."_".$this->id."_".$this->filename))
      {
        if(file_exists($dir."/".$this->table."/".$this->id."_".$this->filename))
        {
          rename($dir."/".$this->table."/".$this->id."_".$this->filename,$dir."/".$this->table."/".$md5alt."_".$this->id."_".$this->filename);
          if(file_exists($dir."/".$this->table."/".$md5alt."_".$this->id."_".$this->filename))
          {
            $md5sum_tmp = md5_file($dir."/".$this->table."/".$md5alt."_".$this->id."_".$this->filename);
            if($md5sum_tmp !== $md5alt)
            {
              $this->app->DB->Update("UPDATE pdfarchiv set checksum = '$md5sum_tmp' WHERE id = '".$altesdokument['id']."'");
              $md5alt = $md5sum_tmp;
            }
          }
        }
      }
    }


    $this->Output($dir."/".$this->table."/".$this->id."_".$this->filename,'F');
    
    if($this->table)$this->app->DB->Update("UPDATE ".$this->table." SET zuarchivieren = 0 WHERE id = '".$this->id."'");
		// neue sicherung
		$md5sum_tmp = md5_file($dir."/".$this->table."/".$this->id."_".$this->filename);
    
    if($md5sum_tmp !== $md5alt || empty($altesdokument) || empty($altesdokument['dateiname']))
    {
      
      $belegnummer = str_ireplace('.pdf','', substr($this->filename,strrpos($this->filename,'_')+1));
      
      $this->app->DB->Insert("INSERT INTO pdfarchiv (zeitstempel, checksum, table_id, table_name, bearbeiter,erstesoriginal,doctype,doctypeorig,dateiname,belegnummer ) values (NOW(),'$md5sum_tmp','".$this->id."','".$this->table."','".$this->app->User->GetName()."',".($md5alt?0:0).",'".$this->app->DB->real_escape_string($this->doctype)."','".$this->app->DB->real_escape_string($this->doctypeOrig)."','".$this->app->DB->real_escape_string($md5sum_tmp."_".$this->id."_".$this->filename)."','".$this->app->DB->real_escape_string($belegnummer)."')");
      $newid = $this->app->DB->GetInsertID();
      if($schreibschutz || $this->app->DB->Select("SELECT schreibschutz FROM ".$this->table." WHERE id = '".$this->id."' LIMIT 1"))$this->app->DB->Update("UPDATE pdfarchiv SET schreibschutz = 1 WHERE id = '$newid' LIMIT 1");
      if(!file_exists($dir."/".$this->table."/".$md5sum_tmp."_".$this->id."_".$this->filename))
      {
        rename($dir."/".$this->table."/".$this->id."_".$this->filename, $dir."/".$this->table."/".$md5sum_tmp."_".$this->id."_".$this->filename);
      }
    }elseif($md5alt && isset($altesdokument['id']) && $altesdokument['id'])
    {
      if($schreibschutz || $this->app->DB->Select("SELECT schreibschutz FROM ".$this->table." WHERE id = '".$this->id."' LIMIT 1"))$this->app->DB->Update("UPDATE pdfarchiv SET schreibschutz = 1 WHERE id = '".$altesdokument['id']."' LIMIT 1");
    }
  }

  public function archiveDocument()
  {
    if($this->table && $this->id)
    {
      $this->ArchiviereDocument($this->app->DB->Select("SELECT schreibschutz FROM `".trim($this->app->DB->real_escape_string($this->table))."` WHERE id = '".(int)$this->id."' LIMIT 1")?true:false);
    }else{
      $this->ArchiviereDocument();
    }
    return;
    $dir = $this->app->Conf->WFuserdata."pdfmirror/".$this->app->Conf->WFdbname;
    if(!is_dir($dir)){
      mkdir($dir, 0700,true);
      //				echo "fehlt";
    }	
    if(!is_dir($dir."/".$this->table))
      mkdir ($dir."/".$this->table,0700,true);

    $this->Output($dir."/".$this->table."/".$this->id."_".$this->filename,'F');
  }

  public function displayDocument() {
    $this->renderDocument();
    $this->archiveDocument();
    $this->Output($this->filename,'D');
    exit;
  }

  public function displayTMP() {
    $this->renderDocument();
    $this->archiveDocument();
    $this->Output("/tmp/".$this->filename,'F');
    return "/tmp/".$this->filename;
  }


  public function sendDocument() { 
    $this->renderDocument();
    $this->archiveDocument();
    $this->Output($this->filename,'S'); 
    exit;
  }  


  /***********************************
   *       public functions
   ***********************************/  

  function GetFont()
  {
    if($this->app->erp->Firmendaten("schriftart")!="")
      return $this->app->erp->Firmendaten("schriftart");
    else
      return 'Arial';
  }


}

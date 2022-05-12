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
use Xentral\Components\Barcode\BarcodeFactory;

include_once "class.superfpdf.php";

class EtikettenPDF extends SuperFPDF {

  function __construct($app,$projekt="") {
    $this->app=$app;
    $this->page_definded=false;
  }

  function SetXML($xml)
  {
    if(empty($xml))return;
    $xml = str_replace('&','&amp;', $xml);
    try {
      $label = new SimpleXMLElement($xml);
    } catch (Exception $e) {
      return;
    }

    foreach($label as $key=>$items)
    {
      switch($key)
      {
        case "settings":
          if(!$this->page_definded)
          {
            parent::__construct('P','mm',array(trim($items->attributes()->width),trim($items->attributes()->height)));
            $this->page_definded = true;
          }

          $this->SetAutoPageBreak(false);
          $this->SetFont('Arial','B',4);
          $this->SetLeftMargin(0);
          $this->SetMargins(0,0,0);
          $this->AddPage();
          break;

        case "line":
          $this->SetXY(trim($items->attributes()->x),$items->attributes()->y,$items[0]); 
          $this->SetFont('Arial','',$items->attributes()->size*2);
          $this->MultiCell(0,$items->attributes()->size,$items[0],0,'L');
          break;
          
        case "rectangle":
          $attributes = $items->attributes();
          $borderwidth = 1;
          if(isset($attributes->size))$borderwidth = $attributes->size;
          $this->SetLineWidth($borderwidth / 10);
          $this->Rect($items->attributes()->x, $items->attributes()->y,trim($items->attributes()->width),trim($items->attributes()->height),'B');
        break;
  
        case "image":
          $filename = '';
          if(isset($items->attributes()->src))
          {
            $src = str_replace('&amp;','&',$items->attributes()->src);
            if(stripos($src,'http://') === false && stripos($src,'https://') === false)
            {
              $src = 'http://'.$src;
            }
            $content = file_get_contents($src);
            if($content)
            {
              $filename = rtrim($this->app->erp->GetTMP(),'/').'/'.md5(microtime(true).$items[0]);
              file_put_contents($filename.'1.jpg', $content);
              
              $bildbreite = trim($items->attributes()->width);
              $bildhoehe = trim($items->attributes()->height);
              if(!class_exists('image'))include_once(__DIR__.'/../class.image.php');
              $img = new image($this->app);
              if($bildbreite > 0)
              {
                $breite = $bildbreite;
              }else{
                $breite = 30;
              }
              if($bildhoehe > 0)
              {
                $hoehe = $bildhoehe;
              }else{
                $hoehe = $breite;
              }
              $_breite = $breite;
              $_hoehe = $hoehe;
              list($width, $height) = getimagesize($filename.'1.jpg');
              if($width > 0 && $height > 0)
              {
                $scalex = $breite / $width;
                $scaley = $hoehe / $height;
                if($scalex < $scaley)
                {
                  $hoehe /= $scaley / $scalex;
                  $hoehe = ceil($hoehe);
                }else{
                  $breite /= $scalex / $scaley;
                  $breite = ceil($breite);
                }
                $str = $content;
                $manipulator = new ImageManipulator($str);
                $manipulator->resample($_breite*10, $_hoehe*10, false,true, true);
                $typ = IMAGETYPE_JPEG;
                $manipulator->save($filename.'2.jpg', $typ);
                $items[0] = $filename.'2.jpg';
              }
            }
            
          }
          $type = exif_imagetype ( trim($items[0]) );

          switch($type)
          {
            case IMAGETYPE_GIF: $type="gif"; break;
            case IMAGETYPE_JPEG: $type="jpg"; break;
            case IMAGETYPE_PNG: $type="png"; break;
            default: $type="";
          }
          if($type!="")
          {
            $this->Image(trim($items[0]),trim($items->attributes()->x),trim($items->attributes()->y),trim($items->attributes()->width),trim($items->attributes()->height),$type);
          } 
          if($filename != '')
          {
            unlink($filename.'1.jpg');
            unlink($filename.'2.jpg');
          }
          break; 

        case "barcode":
          if((String)($items->attributes()->type)=="E30") {
            $this->EAN13($items->attributes()->x,$items->attributes()->y, $items[0],$items->attributes()->size);
          }
          else if((String)($items->attributes()->type)=="Code128" || (String)($items->attributes()->type)=="1") {
            $this->Code128($items->attributes()->x, $items->attributes()->y, $items[0], $items->attributes()->width, $items->attributes()->size);
          }
          else if((String)($items->attributes()->type)=="GS1-128" || (String)($items->attributes()->type)=="1") {
          //$items[0] = "!FNC1!0104012345012345!FNC1!081231!FNC1!1012345";

          $tmp =explode("!FNC1!",$items[0]);
          $codewithfnc1 = implode(chr(206),$tmp);

          //echo chr(206)."0104012345012345".chr(206)."081231".chr(206)."1012345";
          //$this->Code128($items->attributes()->x, $items->attributes()->y, chr(206)."0104012345012345".chr(206)."081231".chr(206)."1012345", $items->attributes()->width, $items->attributes()->size); // 206 = FNC1
          $this->Code128($items->attributes()->x, $items->attributes()->y, $codewithfnc1, $items->attributes()->width, $items->attributes()->size); // 206 = FNC1
          //$this->Code128($items->attributes()->x, $items->attributes()->y, chr(206).$items[0], $items->attributes()->width, $items->attributes()->size); // 206 = FNC1
          }
          else { // standard auf 2 bzw default
            $this->Code39($items->attributes()->x,$items->attributes()->y, $items[0], 0.5, $items->attributes()->size);//, $printText=false)
          }

          break;
        case "qrcode":
            /** @var BarcodeFactory $factory */
            $factory = $this->app->Container->get('BarcodeFactory');
            $ecLevel = 'M';
            $type = 'png';
            $filename = rtrim($this->app->erp->GetTMP(),'/').'/'.md5(microtime(true).$items[0]).'.' . $type;

            $qrText = (string) $items[0];
            $qrcode = $factory->createQrCode($qrText, $ecLevel);
            $width = $items->attributes()->width ?? $items->attributes()->size;
            $height = $items->attributes()->height ?? $items->attributes()->size;
            $image = $qrcode->toPng(trim($width), trim($height));

            if(file_put_contents($filename, $image) === false) {
                throw new RuntimeException('qrcode image cannot be created. Perhaps due to missing write permission');
            }
            unset($image);
            $this->Image(
              trim($filename),
              trim((int) $items->attributes()->x),
              trim((int) $items->attributes()->y),
              trim((int) $width),
              trim((int) $height),
              $type
            );
            unlink($filename);

        break;
      }
    }

  }

}

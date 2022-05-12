<?php
error_reporting(E_ALL ^ E_NOTICE ^ E_DEPRECATED ^ E_STRICT);

//error_reporting(0);
include_once(dirname(__FILE__)."/../conf/main.conf.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.mysql.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.secure.php");
include_once(dirname(__FILE__)."/../phpwf/plugins/class.user.php");
include_once(dirname(__FILE__)."/../www/lib/imap.inc.php");
include_once(dirname(__FILE__)."/../www/lib/class.erpapi.php");

if(is_file(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php"))
  include_once(dirname(__FILE__)."/../www/lib/class.erpapi_custom.php");

include_once(dirname(__FILE__)."/../www/lib/class.httpclient.php");
$aes = '';
$phpversion = (String)phpversion();
if($phpversion{0} == '7' && (int)$phpversion{2} > 0)$aes = '2';
if($aes == 2 && is_file(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php"))
{
  include_once(dirname(__FILE__)."/../www/lib/class.aes".$aes.".php");
}else
  include_once(dirname(__FILE__)."/../www/lib/class.aes.php");
include_once(dirname(__FILE__)."/../www/lib/class.remote.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.phpmailer.php");
include_once(dirname(__FILE__)."/../www/plugins/phpmailer/class.smtp.php");
//include_once(dirname(__FILE__)."/coface_log.php");
//include_once(dirname(__FILE__)."/coface_io.php");

if(is_file("objectapi/mysql/_gen/object.gen.artikel.php"))
  include_once("objectapi/mysql/_gen/object.gen.artikel.php");
	else
  include_once(dirname(__FILE__)."/../www/objectapi/mysql/_gen/object.gen.artikel.php");
if(is_file("objectapi/mysql/_gen/object.gen.adresse.php"))
  include_once("objectapi/mysql/_gen/object.gen.adresse.php");
	else
  include_once(dirname(__FILE__)."/../www/objectapi/mysql/_gen/object.gen.adresse.php");
if(is_file("objectapi/mysql/_gen/object.gen.auftrag_position.php"))
  include_once("objectapi/mysql/_gen/object.gen.auftrag_position.php");
	else
  include_once(dirname(__FILE__)."/../www/objectapi/mysql/_gen/object.gen.auftrag_position.php");
if(is_file("objectapi/mysql/_gen/object.gen.auftrag.php"))
  include_once("objectapi/mysql/_gen/object.gen.auftrag.php");
	else
  include_once(dirname(__FILE__)."/../www/objectapi/mysql/_gen/object.gen.auftrag.php");
if(is_file("objectapi/mysql/_gen/object.gen.lager_platz_inhalt.php"))
  include_once("objectapi/mysql/_gen/object.gen.lager_platz_inhalt.php");
	else
  include_once(dirname(__FILE__)."/../www/objectapi/mysql/_gen/object.gen.lager_platz_inhalt.php");
if(is_file("objectapi/mysql/_gen/object.gen.verkaufspreise.php"))
  include_once("objectapi/mysql/_gen/object.gen.verkaufspreise.php");
	else
  include_once(dirname(__FILE__)."/../www/objectapi/mysql/_gen/object.gen.verkaufspreise.php");

class coface_Daten
{
  public $Struktur;
  public $Daten;
  
  public function istGleich(&$Datensatz)
  {
    foreach($this->Daten as $k => $v)
    {
      if($v)
      {
        if(false !== ($val = $Datensatz->GetDataByName($k)))
        {
          if($val != $v)
          {
            //echo $val." <> ".$v;
            return false;
          }
        } else {
          echo " no val:  ".$v."\r\n";
          return false;
        }
      }
    }
    foreach($Datensatz->Daten as $k => $v)
    {
      if($v)
      {
        if(false !== ($val = $this->GetDataByName($k)))
        {
          if($val != $v)
          {
            //echo $val." <> ".$v;
            return false;
          }
        } else 
        {
          echo " no val:  ".$v."\r\n";
          return false;
        }
      }
    }
    return true;
  }
  
  public function getByName($name)
  {
    if(!$this->Struktur) return false;
    foreach($this->Struktur as $k => $v)
    {
      if($v[1] == strtoupper($name))return $v;
    }
    return false;
  }
  
  public function getByNr($nr)
  {
    if(isset($this->Struktur[$nr]))return $this->Struktur[$nr];
    return false;
  }
  
  protected function KeineSonderzeichen($text)
  {
    $text = str_replace ("ä", "ae", $text);
    $text = str_replace ("Ä", "Ae", $text);
    $text = str_replace ("ö", "oe", $text);
    $text = str_replace ("Ö", "Oe", $text);
    $text = str_replace ("ü", "ue", $text);
    $text = str_replace ("Ü", "Ue", $text);
    $text = str_replace ("ß", "ss", $text);
    $text = str_replace ("&", "u", $text);
    $text = preg_replace('#[^-_.A-Za-z0-9]', '', $text);
    return $text;
  }
  
  public function GetDataByName($name)
  {
    if(isset($this->Daten[$name]))return $this->Daten[$name];
    return false;
  }
  
  public function FillFromArray(&$arr)
  {
    foreach($this->Struktur as $k => $v)
    {
      if(isset($arr[$v[1]]) && !is_null($arr[$v[1]]))
      {
        if(!$this->FillData($v[1], $arr[$v[1]]))
        {
          return false;
        }
      }
    }
    if($this->getByName('FLNUM')!==false && isset($arr['FLNUM']) && isset($this->FLNUM))$this->FLNUM = (int)$arr['FLNUM'];
    return true;
  }
  
  public function FillData($name, $wert)
  {
    $wert = str_replace('NONBLOCKINGZERO','', $wert);
    if(($name == 'DBCOTRY' || $name == 'DBLANG') && !$wert)$wert = 'DE';
    $wert = html_entity_decode($wert);
    
    $name = trim(strtoupper($name));
    $found = false;
    foreach($this->Struktur as $k => $v)
    {
      if($v[1] == $name)$found = $k;
    }
    if(!$found)return false;
    if(substr($this->Struktur[$found][3],0,1) == 'A')
    {
      if(strpos($this->Struktur[$found][3],'Sonerzeichen') !== false)$wert = $this->KeineSonderzeichen($wert);
      $this->Daten[$name] = mb_substr($wert, 0, $this->Struktur[$found][2],'UTF-8');
      return true;
    } elseif(substr($this->Struktur[$found][3],0,1) == 'N'){
      $wert = str_replace(',','.', $wert);
      if(!is_numeric($wert))return false;
      if($this->Struktur[$found][3] == 'N')
      {
        $wert = (int)$wert;
        if($wert < 0)return false;
        if($wert >= pow(10,$this->Struktur[$found][2]))return false;
        $this->Daten[$name] = $wert;
        return true;
      }
      $vz = false;
      if($vzpos = strpos($this->Struktur[$found][3],'VZ+'))
      {
        $vz = true;
        $Str = substr($this->Struktur[$found][3], $vzpos + 3);
      } else {
        $Str = $this->Struktur[$found][3];
        if($KPos = strpos($Str, '('))$Str = substr($Str, $KPos + 1);
      }
      if($Kpos = strrpos($Str, ')'))$Str = substr($Str, 0, $Kpos);
      $Komma = false;
      if(strpos($Str, ','))$Komma = true;
      $Nachkomma = 0;
      if($Komma)
      {
        $Stellen = preg_split("/,/", $Str);
        if(isset($Stellen[1]))$Nachkomma = $Stellen[1];
        $vorkomma = $Stellen[0];
      } else {
        $vorkomma = (int)$Str;
      }
      if(!$vz){
        if($wert < 0)
        {
          return false;
        }
      }
      $wert = round($wert, $Nachkomma);
      if(round(abs($wert)) >= pow(10, $vorkomma))
      {
        return false;
      }
      $this->Daten[$name] = $wert;
      return true;
      
    } elseif(substr($this->Struktur[$found][3],0,10) == 'TT.MM.JJJJ'){
      if($wert == '0000-00-00')
      {
        $this->Daten[$name] = null;
      }else{
        if(strpos($wert,'.'))
        {
          $werta = preg_split("/\./", $wert);
          if(count($werta) == 3)
          {
            
            $this->Daten[$name] = $werta[2].''.$werta[1].''.$werta[0];
            return true;
          }
        }
        if(false ===($ts = strtotime($wert)))
        {
          if(strpos($wert,'-'))
          {
            $werta = preg_split("/\-/", $wert);
            if(count($werta) == 3)
            {
              
              $this->Daten[$name] = str_replace('-','',$wert);
              return true;
            } else return false;
          }
          return false;
        }
        $this->Daten[$name] = date("Ymd", $ts);
      }
      
      return true;
    }
    
    if(isset($this->Struktur[$name]))
    {
      if($this->Struktur)
      $this->Daten[$name] = $wert;
      return true;
    }
    return false;
  }
  
  function genFeldnamenzeile()
  {
    $erg = '';
    foreach($this->Struktur as $k => $v)
    {
      if($erg != '')$erg .= ';'; 
      $erg .= '"'.$v[1].'"';
    }
    return $erg;
  }
  
  function genDatenZeile()
  {
    $erg = '';
    foreach($this->Struktur as $k => $v)
    {
      if($erg != '')$erg .= ';'; 
      $erg .= '"';
      if(isset($this->Daten[$v[1]]))
      {
        if(substr($v[3],0,1) == 'N')
        {
          if($v[3] == 'N')
          {
            $wert = str_replace('.',',',$this->Daten[$v[1]]);
            //$erg .= (strlen($wert)< $v[2]?str_repeat('0',$v[2]-strlen($wert)):'').$wert;
            $erg .= $wert;
          } else {
            $erg .= str_replace('.',',',$this->Daten[$v[1]]);  
          }
        } else {
          $erg .= $this->Daten[$v[1]];
        }
      }
      $erg .= '"';
    }
    return $erg;    
  }
  
  function pruefe()
  {
    foreach($this->Struktur as $k => $v)
    {
      if($v[4] == 'M')
      {
        if(!isset($this->Daten[$v[1]]))return false;
        if($this->Daten[$v[1]] == '')return false;
      }
    }
    return true;
  }
  
}


class coface_OP_Kopf extends coface_Daten
{
  function __construct() {
    $this->Struktur[1] = array(1 => 'FLNUM', 2 => 6, 3 => 'N', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[2] = array(1 => 'FLDATE', 2 => 10, 3 => 'TT.MM.JJJJ', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[3] = array(1 => 'CLNAME1', 2 => 35, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[4] = array(1 => 'CLNAME2', 2 => 35, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[5] = array(1 => 'CLPLACE', 2 => 35, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[6] = array(1 => 'CLZIP', 2 => 10, 3 => 'A', 4 => 'M',               5 => '', 6 => '');
    $this->Struktur[7] = array(1 => 'CLSTREET', 2 => 35, 3 => 'A', 4 => 'M',            5 => '', 6 => '');
    $this->Struktur[8] = array(1 => 'CLNR', 2 => 10, 3 => 'A', 4 => 'M',                5 => '', 6 => '');
    $this->Struktur[9] = array(1 => 'FLBELANZ', 2 => 8, 3 => 'N', 4 => 'M',             5 => '', 6 => '');
    $this->Struktur[10] = array(1 => 'FLBELSUM', 2 => 20, 3 => 'N (VZ+16,2)', 4 => 'M', 5 => '', 6 => '');
  }
}

class coface_OP_Daten extends coface_Daten
{
  var $FLNUM;
  
  function __construct($FLNUM = 0) {
    $this->FLNUM = $FLNUM;
    $this->Struktur[1] = array(1 => 'OPCLNR', 2 => 10, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[2] = array(1 => 'OPDBNR', 2 => 15, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[3] = array(1 => 'OPITMTYP', 2 => 5, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[4] = array(1 => 'OPITMNR', 2 => 20, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[5] = array(1 => 'OPITMNRREF', 2 => 20, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[6] = array(1 => 'OPITMDTL', 2 => 10, 3 => 'TT.MM.JJJJ', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[7] = array(1 => 'OPITMDAT', 2 => 10, 3 => 'TT.MM.JJJJ', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[8] = array(1 => 'OPITMDTD', 2 => 10, 3 => 'TT.MM.JJJJ', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[9] = array(1 => 'OPAMT', 2 => 22, 3 => 'N (VZ+18,2)', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[10] = array(1 => 'OPCUR', 2 => 3, 3 => 'A', 4 => '', 5 => 'M', 6 => '');
    $this->Struktur[11] = array(1 => 'OPAMTMN', 2 => 22, 3 => 'N (VZ+18,2)', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[12] = array(1 => 'OPCURMN', 2 => 3, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[13] = array(1 => 'OPITMDLV', 2 => 1, 3 => 'N', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[14] = array(1 => 'OPITMTAX', 2 => 3, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[15] = array(1 => 'OPITMDY1', 2 => 3, 3 => 'N', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[16] = array(1 => 'OPITMRD1', 2 => 6, 3 => 'N (3,2)', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[17] = array(1 => 'OPITMDY2', 2 => 3, 3 => 'N', 4 => '', 5 => 'K', 6 => '');
    $this->Struktur[18] = array(1 => 'OPITMRD2', 2 => 6, 3 => 'N (3,2)', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[19] = array(1 => 'OPITMDY3', 2 => 3, 3 => 'N', 4 => '', 5 => 'K', 6 => '');
    $this->Struktur[20] = array(1 => 'OPITMTXT', 2 => 254, 3 => 'A', 4 => '', 5 => 'K', 6 => '');
    
  }
  
}

class coface_DB_Kopf extends coface_Daten 
{
  function __construct() {
    $this->Struktur[1] = array(1 => 'FLNUM', 2 => 6, 3 => 'N', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[2] = array(1 => 'FLDATE', 2 => 10, 3 => 'TT.MM.JJJJ', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[3] = array(1 => 'CLNAME1', 2 => 35, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[4] = array(1 => 'CLNAME2', 2 => 35, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[5] = array(1 => 'CPLACE', 2 => 35, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[6] = array(1 => 'CLZIP', 2 => 10, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[7] = array(1 => 'CLSTREET', 2 => 35, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[8] = array(1 => 'CLNR', 2 => 10, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[9] = array(1 => 'FLITNR', 2 => 8, 3 => 'N', 4 => 'M', 5 => '', 6 => '');

  }
}

class coface_DB_Daten extends coface_Daten 
{
  var $FLNUM;
  function __construct($FLNUM = 0) {
    $this->FLNUM = $FLNUM;
    $this->Struktur[1] = array(1 => 'DBCLNR', 2 => 10, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[2] = array(1 => 'DBNR', 2 => 15, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[3] = array(1 => 'DBNRZE', 2 => 15, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[4] = array(1 => 'DBNAME1', 2 => 40, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[5] = array(1 => 'DBNAME2', 2 => 40, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[6] = array(1 => 'DBKEY', 2 => 10, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[7] = array(1 => 'DBSTREET', 2 => 46, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[8] = array(1 => 'DBSTREETNR', 2 => 6, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[9] = array(1 => 'DBZIP', 2 => 12, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[10] = array(1 => 'DBPLACE', 2 => 30, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[11] = array(1 => 'DBCOTRY', 2 => 2, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[12] = array(1 => 'DBPOBOX', 2 => 12, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[13] = array(1 => 'DBPOZIP', 2 => 12, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[14] = array(1 => 'DBPOCO', 2 => 12, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[15] = array(1 => 'DBLANG', 2 => 2, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[16] = array(1 => 'DBCONTAC', 2 => 40, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[17] = array(1 => 'DBTEL', 2 => 24, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[18] = array(1 => 'DBTELEXT', 2 => 6, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[19] = array(1 => 'DBFAX', 2 => 24, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[20] = array(1 => 'DBFAXEXT', 2 => 6, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[21] = array(1 => 'DBEMAIL', 2 => 70, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[22] = array(1 => 'DBVATID', 2 => 14, 3 => 'A (keine Sonderzeichen)', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[23] = array(1 => 'DBPAYCON', 2 => 10, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[24] = array(1 => 'DBITTRM', 2 => 3, 3 => 'N', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[25] = array(1 => 'DBLITYP', 2 => 1, 3 => 'A', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[26] = array(1 => 'DBLIMIT', 2 => 26, 3 => 'N (23,2)', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[27] = array(1 => 'DBCURLMT', 2 => 3, 3 => 'A', 4 => '', 5 => 'M', 6 => '');
    $this->Struktur[28] = array(1 => 'DBDALIEFF', 2 => 10, 3 => 'TT.MM.JJJJ', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[29] = array(1 => 'DBDALITO', 2 => 10, 3 => 'TT.MM.JJJJ', 4 => 'M', 5 => '', 6 => '');
    $this->Struktur[30] = array(1 => 'DBLIMITS', 2 => 26, 3 => 'N (23,2)', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[31] = array(1 => 'DBCURRS', 2 => 3, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[32] = array(1 => 'DBDALISEFF', 2 => 10, 3 => 'TT.MM.JJJJ', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[33] = array(1 => 'DBDALISTO', 2 => 10, 3 => 'TT.MM.JJJJ', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[34] = array(1 => 'DBNRCO', 2 => 9, 3 => 'A (keine Sonderzeichen)', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[35] = array(1 => 'DBCINR', 2 => 12, 3 => 'A (keine Sonderzeichen)', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[36] = array(1 => 'DBFLGEX', 2 => 1, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
    $this->Struktur[37] = array(1 => 'DBREMARKS', 2 => 254, 3 => 'A', 4 => 'K', 5 => '', 6 => '');
  }
}


class coface_gen_DB
{
  var $app;
  function __construct(&$app)
  {
    $this->app = $app;
  }
  
  function Insert( $datensatz)
  {
    $classname = get_class($datensatz);
    if(!$classname)return false;
    if(!class_exists($classname)){
      //echo "Klasse existiert nicht";
      return false;
    }
    
    if(!isset($datensatz->Struktur))
    {
      //echo "Struktur existiert nicht";
      return false;
    }
    $sql = "insert into ".$classname." (";
    $first = true;
    foreach($datensatz->Struktur as $k => $v)
    {
      if(!$first)$sql .= ",";
      $first = false;
      $sql .= $v[1];
    }
    if(isset($datensatz->FLNUM))
    {
      $sql .= ', FLNUM';
    }
    $sql .= ") values (";
    $first = true;
    foreach($datensatz->Struktur as $k => $v)
    {
      if(!$first)$sql .= ", ";
      $first = false;
      if(isset($datensatz->Daten[$v[1]]))
      {
        if(substr($datensatz->Struktur[$k][3],0,1) == 'A')
        {
          $sql .= "'".$datensatz->Daten[$v[1]]."' ";
        } elseif(substr($datensatz->Struktur[$k][3],0,1) == 'N'){
          if($datensatz->Struktur[$k][3] == 'N')
          {
            $sql .= (int)$datensatz->Daten[$v[1]];
          } else {
            $vz = false;
            if($vzpos = strpos($datensatz->Struktur[$k][3],'VZ+'))
            {
              $vz = true;
              $Str = substr($datensatz->Struktur[$k][3], $vzpos + 3);
            } else {
              $Str = $datensatz->Struktur[$k][3];
              
              if($KPos = strpos($Str, '('))$Str = substr($Str, $KPos + 1);
              
            }
            if($Kpos = strrpos($Str, ')'))$Str = substr($Str, 0, $Kpos);
            $Komma = false;
            if(strpos($Str, ','))$Komma = true;
            $Nachkomma = 0;
            if($Komma)
            {
              $Stellen = preg_split("/,/", $Str);
              if(isset($Stellen[1]))$Nachkomma = $Stellen[1];
              $vorkomma = $Stellen[0];
            } else {
              $vorkomma = (int)$Str;
            }
            if($Nachkomma)
            {
              $sql .= (float)$datensatz->Daten[$v[1]];
            } else {
              $sql .= (int)$datensatz->Daten[$v[1]];
            }
          }
        } elseif(substr($datensatz->Struktur[$k][3],0,10) == 'TT.MM.JJJJ'){
          if(strpos($datensatz->Daten[$v[1]],'.'))
          {
            $arr = preg_split("/\./", $datensatz->Daten[$v[1]]);
            $sql .= "'".$arr[2].'-'.$arr[1].'-'.$arr[0]."' ";
          } else {
            $sql .= "'".$datensatz->Daten[$v[1]]."' ";      
          }
        }
      }else {
        $sql .= " NULL ";
      }
    }
    if(isset($datensatz->FLNUM))
    {
      $sql .= ', '.($datensatz->FLNUM?(int)$datensatz->FLNUM:'NULL');
    }
    $sql .= ")";
    return $sql;
  }
  
  function genCreate($classname)
  {
    if(!class_exists($classname)){
      echo "Klasse existiert nicht";
      return false;
    }
    $tmp = new $classname();
    if(!isset($tmp->Struktur))
    {
      echo "Struktur existiert nicht";
      return false;
    }
    $sql = "create table ".$classname." (
    id int(11) NOT NULL AUTO_INCREMENT,
    " ;
    
    foreach($tmp->Struktur as $k => $v)
    {
      $sql .= $v[1]. " ";
      if(substr($tmp->Struktur[$k][3],0,1) == 'A')
      {
        $sql .= " varchar(".$tmp->Struktur[$k][2].") ";
      } elseif(substr($tmp->Struktur[$k][3],0,1) == 'N'){
        if($tmp->Struktur[$k][3] == 'N')
        {
          $sql .= " int(15) ";
        } else {
          $vz = false;
          if($vzpos = strpos($tmp->Struktur[$k][3],'VZ+'))
          {
            $vz = true;
            $Str = substr($tmp->Struktur[$k][3], $vzpos + 3);
          } else {
            $Str = $tmp->Struktur[$k][3];
            
            if($KPos = strpos($Str, '('))$Str = substr($Str, $KPos + 1);
            
          }
          if($Kpos = strrpos($Str, ')'))$Str = substr($Str, 0, $Kpos);
          $Komma = false;
          if(strpos($Str, ','))$Komma = true;
          $Nachkomma = 0;
          if($Komma)
          {
            
            $Stellen = preg_split("/,/", $Str);
            if(isset($Stellen[1]))$Nachkomma = $Stellen[1];
            $vorkomma = $Stellen[0];
          } else {
            $vorkomma = (int)$Str;
          }
          if($Nachkomma)
          {
            $sql .= " decimal(".((int)$Nachkomma+(int)$vorkomma).",".((int)$Nachkomma).") ";
            
          } else {
            $sql .= " int(15) ";
          }

        }
        
      } elseif(substr($tmp->Struktur[$k][3],0,10) == 'TT.MM.JJJJ'){
        $sql .= " date ";      
      }
      $sql .= "DEFAULT NULL,\r\n";
    }
    if(strpos($classname,'Daten') !== false)$sql .= "FLNUM int(11) DEFAULT NULL, ";
    $sql .= "PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";
    return $sql;
  }
}



class cofaceConfig
{
  public static $Kundennummer = '2099/001';
  public static $ExcelRoh = 'Entwickler.xlt';
  public static $kvoid = 1;
  public static $FTPServer = '';
  public static $FTPPort = null;
  public static $FTPUser = null;
  public static $FTPPW = null;
  public static $Folder = 'coface/';
  public function init()
  {
    if(strpos(self::$ExcelRoh,dirname(__FILE__))===false)self::$ExcelRoh = dirname(__FILE__).'/'.self::$ExcelRoh;
    if(strpos(self::$Folder,dirname(__FILE__))===false)self::$Folder = dirname(__FILE__).'/'.self::$Folder;
  }
}

cofaceConfig::init();

class app_t {
  var $DB;
  var $User;
  var $erp;
}

if(!isset($app)){
	$app = new app_t();
	$conf = new Config();
  $app->Conf = &$conf;
  $app->User = new User($app);
	$app->DB = new DB($conf->WFdbhost,$conf->WFdbname,$conf->WFdbuser,$conf->WFdbpass,null,$conf->WFdbport);
	$erp = new erpAPI($app);
	$app->erp = $erp;
	$remote = new Remote($app);
}


//$tmpgenDB = new coface_gen_DB($app);
/*echo $tmpgenDB->genCreate("coface_OP_Kopf");
echo $tmpgenDB->genCreate("coface_OP_Daten");
echo $tmpgenDB->genCreate("coface_DB_Kopf");
echo $tmpgenDB->genCreate("coface_DB_Daten");
echo "\r\n";*/
//$tmp = new coface_DB_Daten();
//$tmp->FillData("DBCLNR","ABCDEFGH");
//$tmp->FillData("DBLIMIT", 5.2);
//echo $tmpgenDB->Insert($tmp);
//echo "\r\n";
//$job->maxlinesperfile = 3000;
/*if(isset($argv[1]) && strpos($argv[1],'file=') !== false)
{
  $log = new coface_log($app);
  $job = new cofaceIOJob($app, $log);
  print_r($job->getFileInfo(str_replace('file=','',$argv[1])));
}elseif(isset($_GET['file']) ){
  $log = new coface_log($app);
  $job = new cofaceIOJob($app, $log);
  print_r($job->getFileInfo($_GET['file']));
}else{
  
  if(isset($argv[1]) && strpos($argv[1],'runall') !== false || isset($_GET['runall']) || (isset($_GET['cmd']) && $_GET['cmd'] == 'runall'))
  {
    $log = new coface_log($app);
    $job = new cofaceIOJob($app, $log);
    $job->runAll();
  }
}
*/


function cmp($a, $b)
{
  return strcasecmp($a, $b);
}

if(isset($argv[1]) && $argv[1] == 'all')
{
$tmp = new WaWisioncoface($app);
echo $tmp->getAll();
echo $tmp->Hochladen();
print_r($tmp->error);
}

if(isset($argv[1]) && $argv[1] == 'export')
{
  $sammlung = new coface_Sammlung($app);
  //$tmp = new WaWisioncoface($app);
  echo $sammlung->lastDateinummer();
  $sammlung->Export();
  $sammlung->Hochladen();
}
if(isset($argv[1]) && is_file($argv[1]))
{
  $tmp = new WaWisioncoface($app);
  $tmp->ImportEdison($argv[1]);
  $tmp->Hochladen();
}


class WaWisioncoface
{
  var $app;
  var $sammlung;
  var $error;
  function __construct(&$app)
  {
    $this->app = $app;
  }
  
  public function Hochladen()
  {
    if($this->sammlung->Hochladen())return true;
    if($this->sammlung->error)$this->error[] = $this->sammlung->error;
    return false;
  }
  
  function ImportEdison($datei)
  {
    $this->sammlung = new coface_Sammlung($this->app);
    $DBs = false;
    $OPs = false;
    if(strrpos($datei,'/') === false)
    {
      $datei = dirname(__FILE__).'/'.$datei;
    }
    if(!file_exists($datei))return false;
    if($fh = fopen($datei,'r'))
    {
      if($header = fgetcsv($fh,0,";") !== FALSE)
      {
        $i = 0;
        while (($data = fgetcsv($fh, 0, ";",'"')) !== FALSE) {
          $i++;
          //if($i % 1000 = 0)echo ".";
          if(isset($data[10])){
            $betrag = round((float)str_replace(",",".",trim($data[10])),2);
            
            $belegdatum = $data[2];
            $belegnummer = $data[3];
            $kundennummer = $data[1];
            
            if(!isset($DBs[$kundennummer]))$DBs[$kundennummer] = true;
            
            $belegnummer = str_replace("NR",'',$belegnummer);
            $belegnummer = str_replace(".",'',$belegnummer);
            $tmp['belegnummer'] = trim($belegnummer);
            $tmp['betrag'] = $betrag;
            $tmp['buchungstext'] = trim($data[11]);
            $status = $data[15];
            $mahnbar = trim($data[16]) == 'nicht mahnbar'?0:1;
            $mahnstufe = $data[8];
            $tmp['status'] = $status;
            $tmp['mahnbar'] = $mahnbar;
            $tmp['mahnstufe'] = $mahnstufe;
            if(!isset($OPs[$kundennummer]))$OPs[$kundennummer] = false;
            $OPs[$kundennummer][] = $tmp;
            unset($tmp);
            
            

            /*
            if($betrag > 0)
            {
              $belegnummer1 = trim($data[3]);
              if($belegnummer1)
              {
                $rechnungid = $this->app->DB->Select("select id from rechnung where belegnr like '".addslashes($belegnummer1)."' limit 1");
                if($rechnungid)
                {
                  echo "offene Rechnung: ".$rechnungid."   ".$belegnummer1."  ".$betrag."\r\n";
                  $this->app->DB->Update("update rechnung set zahlungssstatus = 'offen' where id = ".$rechnungid);
                } else {
                  echo "offene Rechnung nicht gefunden: ".$belegnummer1."   ".$betrag."\r\n";
                  
                }
                
              }
            }*/
            
          }
        }
        fclose($fh);
        $anzdb = 0;
        $anzgr0 = 0;
        $anzadr = 0;
        $anzops = 0;
        if(isset($DBs) && is_array($DBs))
        {
          foreach($DBs as $k => $v)
          {
            $anzdb++;
            if(isset($OPs[$k]) && is_array($OPs[$k]))
            {
              
              $adresse = $this->app->DB->select("SELECT id from adresse where kundennummer like '".$k."' limit 1");
              if($adresse)
              {
                $anzadr++;
                $gesamtoffen = 0;
                $rechnungsnr = '';
                echo "\r\n".$k." count: ". count($OPs[$k])." - ";
                foreach($OPs[$k] as $k2 => $v2)
                {
                  $anzops++;
                  //echo $v2['betrag']." ";
                  echo $v2['belegnummer']." ";
                  if($v2['belegnummer'] && (int)$v2['belegnummer']."" == $v2['belegnummer'] && substr($v2['belegnummer'],0,1) == '4')$rechnungsnr = $v2['belegnummer'];
                  $gesamtoffen += $v2['betrag'];
                }
                echo " Betrag: ".$gesamtoffen." Rnr: ".$rechnungsnr;
                if($gesamtoffen > 0 && $rechnungsnr)
                {
                  echo $gesamtoffen;
                  $rechnung = $this->app->DB->SelectArr("SELECT * from rechnung where belegnr = '".$rechnungsnr."' limit 1");
                  if($rechnung)
                  {
                    $rechnung = reset($rechnung);
                    $DB = new coface_DB();
                    if(!$rechnung['kundennummer'])$rechnung['kundennummer'] = $k;
                    $DB->SetAdr($rechnung);
                    $this->sammlung->addDB($DB);
                    $Rechnungen = false;
                    $kombEinzahlungen = false;
                    
                    foreach($OPs[$k] as $k2 => $v2)
                    {
                      $isrechnung = false;
                      $isgutschrift = false;
                      if($v2['belegnummer'] && is_numeric($v2['belegnummer']) && substr($v2['belegnummer'],0,1) == '4')
                      {
                        $tmprechnung = $this->app->DB->SelectArr("SELECT * from rechnung where belegnr = '".$v2['belegnummer']."' limit 1");
                        if($tmprechnung)
                        {
                          $isrechnung = true;
                          if(!isset($Rechnungen[$v2['belegnummer']]))$Rechnungen[$v2['belegnummer']] = false;
                          $Rechnungen[$v2['belegnummer']][] = $v2;
                        }
                        //Rechnung
                        
                      }elseif($v2['belegnummer'] && is_numeric($v2['belegnummer']) && substr($v2['belegnummer'],0,1) == '9')
                      {
                        //Gutschrift
                        $tmpgutschrift = $this->app->DB->SelectArr("SELECT adresse, rechnungid from gutschrift where belegnr = '".$v2['belegnummer']."' limit 1");
                        if($tmpgutschrift)
                        {
                          $tmpgutschrift = reset($tmpgutschrift);
                          if($tmpgutschrift['rechnungid'])
                          {
                            $rechnungsnrg = $this->app->DB->Select("SELECT belegnr where id = ".$tmpgutschrift['rechnungid']." limit 1");
                            if($rechnungsnrg)
                            {
                              $Rechnungen[$rechnungsnrg][] = $v2;
                              $isgutschrift = true;
                            } else {
                              if(!isset($Rechnungen['unbekannteGS']))$Rechnungen['unbekannteGS'] = false;
                              $Rechnungen['unbekannteGS'][] = $v2;
                              $isgutschrift = true;
                              
                            }
                            
                          } else {
                            if(!isset($Rechnungen['unbekannteGS']))$Rechnungen['unbekannteGS'] = false;
                            $Rechnungen['unbekannteGS'][] = $v2;
                            $isgutschrift = true;
                          }
                        }
                      }
                      if(!isgutschrift && !isrechnung)
                      {
                        $split = preg_split('/,/',$v2['buchungstext']);
                        if(is_array($split))
                        {
                          foreach($split as $k3 => $v3)
                          {
                            $split2 = preg_split('/:/',$v3);
                            {
                              if(is_array($split2))
                              {
                                if(isset($split2[1]))
                                {
                                  if(is_numeric(trim($split2[1])) && substr(trim($split2[1]),0,1) == '4')
                                  {
                                    $rechnungsnrg = $this->app->DB->Select("SELECT * from rechnung where belegnr = ".trim($split2[1])." limit 1");
                                    if($rechnugsnrg)
                                    {
                                      if(!isset($kombEinzahlungen[$k2]))$kombEinzahlungen[$k2] = false;
                                      $kombEinzahlungen[$k2][] = $rechnungsnrg;
                                    }else{
                                      
                                    }
                                    
                                  }
                                  
                                } else {
                                  if(is_numeric(trim($split2[0])) && substr(trim($split2[0]),0,1) == '4')
                                  {
                                    $rechnungsnrg = $this->app->DB->Select("SELECT * from rechnung where belegnr = ".trim($split2[0])." limit 1");
                                    if($rechnugsnrg)
                                    {
                                      if(!isset($kombEinzahlungen[$k2]))$kombEinzahlungen[$k2] = false;
                                      $kombEinzahlungen[$k2][] = $rechnungsnrg;
                                    }else{
                                      
                                    }
                                  }
                                }
                                
                                
                              }
                              
                              
                            }
                            
                          }
                          if(!isset($kombEinzahlungen[$k2]))
                          {
                            if(!isset($Rechnungen['unbekannt']))$Rechnungen['unbekannt'] = false;
                            $Rechnungen['unbekannt'][] = $v2;
                          }
                          
                        }
                        
                      }
                      
                      
                    }
                    if(isset($Rechnungen) && $Rechnungen && !isset($Rechnungen['unbekannt']) && !isset($Rechnungen['unbekannteGS']))
                    {
                      echo ".";
                      foreach($Rechnungen as $k4 => $v4)
                      {
                        if($k4 != 'unbekannt' && $k4 != 'unbekannteGS'){
                          if(is_array($v4))
                          {
                            $gesbetrag = 0;
                            foreach($v4 as $k5 => $v5)
                            {
                              $gesbetrag += $v5['betrag'];
                            }
                            if($gesbetrag > 0)
                            {
                              /*
                              if(isset($kombEinzahlungen))
                              {
                                foreach($kombEinzahlungen as $k6 => $v6)
                                {
                                  foreach($v6 as $k7 => $v7)
                                  {
                                    if($v7['belegnr'] == $k4)
                                    {
                                      if($OPs[$k][$k6]['betrag'] >= $gesbetrag)
                                      {
                                        
                                      }else{
                                        
                                      }
                                    }
                                    
                                  }
                                  
                                }
                                
                              }*/
                              foreach($v4 as $k5 => $v5)
                              {
                                if(substr($v5['belegnummer'],0,1) == '4')
                                {
                                  if($v5['betrag'] > 0)
                                  {
                                    
                                    
                                    $OP = new coface_OP();
                                    $OP->SetBelegnummer($v5['belegnummer']);
                                    $OP->SetBelegnummerRef($v5['belegnummer']);
                                    $OP->DebitorenRechnung();
                                    
                                    $OP->SetData($rechnung);
                                    $OP->SetBelegdatum($v5['belegdatum']);
                                    if($v5['mahnstufe'])$OP->SetMahnstufe($v5['mahnstufe']);
                                    $OP->SetBetrag($v5['betrag']);
                                    $OP->SetBelegdatum($rechnung['datum']);
                                    if($rechnung['versendet_am'] && $rechnung['versendet_am'] != '0000-00-00')$OP->SetLieferdatum($rechnung['versendet_am']);
                                    $OP->SetFaelligkeitsdatum(date('Y-m-d',strtotime($rechnung['datum']) + 86400 * (int)$rechnung['zahlungszieltage']));
                                    $this->sammlung->addOP($OP);
                                  
                                  }else{
                                    $OP = new coface_OP();
                                    $OP->SetBelegnummer($v5['belegnummer']);
                                    $OP->SetBelegnummerRef($v5['belegnummer']);
                                    
                                    $OP->SetData($rechnung);
                                    if($v5['mahnstufe'])$OP->SetMahnstufe($v5['mahnstufe']);
                                    $OP->DebitorenZahlung();
                                    $OP->SetBelegdatum($rechnung['datum']);
                                    if($rechnung['versendet_am'] && $rechnung['versendet_am'] != '0000-00-00')$OP->SetLieferdatum($rechnung['versendet_am']);
                                    $OP->SetFaelligkeitsdatum(date('Y-m-d',strtotime($rechnung['datum']) + 86400 * (int)$rechnung['zahlungszieltage']));

                                    $OP->SetBetrag($v5['betrag']);
                                    
                                  }
                                  
                                }elseif(substr($v5['belegnummer'],0,1) == '9')
                                {
                                  $OP = new coface_OP();
                                  $OP->DebitorenGutschrift();
                                  $OP->SetBelegnummer($v5['belegnummer']);
                                  $OP->SetBelegnummerRef($k4);
                                    
                                  $OP->SetData($rechnung);
                                  if($v5['mahnstufe'])$OP->SetMahnstufe($v5['mahnstufe']);
                                  $OP->SetBelegdatum($rechnung['datum']);
                                  if($rechnung['versendet_am'] && $rechnung['versendet_am'] != '0000-00-00')$OP->SetLieferdatum($rechnung['versendet_am']);
                                  $OP->SetFaelligkeitsdatum(date('Y-m-d',strtotime($rechnung['datum']) + 86400 * (int)$rechnung['zahlungszieltage']));

                                  $OP->SetBetrag($v5['betrag']);
                                  $this->sammlung->addOP($OP);
                                }
                                
                                
                              }
                              
                              
                            }
                            
                          }
                        }
                        
                      }
                      
                    }
                    
                    
                    $anzgr0++;
                  } else {
                    
                    echo "SELECT * from rechnung where belegnr = '".$rechnungsnr."' limit 1\r\n ";
                  }
                }
              }
            }
            
            
            
          }
                $this->sammlung->SpeichernInDB();
      $this->sammlung->Export();
        }
        die ("Anzahl: ".$anzdb." ".$anzgr0." ".$anzadr." ".$anzops);
        
      }
      
      
      
      $OP = new coface_OP();
      $DB = new coface_DB();
      $OP->SetBelegnummer($rechnung['belegnr']);
      $OP->SetBelegnummerRef($rechnung['belegnr']);
      $DB->SetAdr($rechnung);
      $OP->SetData($rechnung);
      $OP->SetBelegdatum($rechnung['datum']);
/*
            $OP = new coface_OP();
            $DB = new coface_DB();
            $OP->SetBelegnummer($rechnung['belegnr']);
            $OP->SetBelegnummerRef($rechnung['belegnr']);
            $DB->SetAdr($rechnung);
            $OP->SetData($rechnung);
            $OP->SetBelegdatum($rechnung['datum']);
            if($rechnung['versendet_am'] && $rechnung['versendet_am'] != '0000-00-00')$OP->SetLieferdatum($rechnung['versendet_am']);
   
            $OP->SetFaelligkeitsdatum(date('Y-m-d',strtotime($rechnung['datum']) + 86400 * (int)$rechnung['zahlungszieltage']));
            switch($rechnung['mahnwesen'])
            {
              case 'mahnung1':
                $OP->SetMahnstufe(1);
              break;
              case 'mahnung2':
                $OP->SetMahnstufe(2);
              break;          
              case 'mahnung3':
                $OP->SetMahnstufe(3);
              break;
            }
            if($rechnung['zahlungszielskonto'])
            {
              $OP->SetSkonto($rechnung['zahlungszieltageskonto'],$rechnung['zahlungszielskonto']);
            }
            if(isset($corechnungen) && isset($corechnungen[$rechnung['belegnr']]))
            {
              //Alte Rechnung nehmen und verrechnen
            }
            if($rechnung['status'] == 'storniert')
            {
              //$this->Create
              $OP->StornoRechnung();
            } else {
              if($rechnung['ist'] != $rechnung['soll'])
              {
                $OP->SetBetrag($rechnung['soll']-$rechnung['ist']);
              }
              if($rechnung['ist'] > 0)$OP->Restposten();
              
            }
            $this->sammlung->addDB($DB);
            $this->sammlung->addOP($OP);

*/
      return true;
    } else return false;
    
    
    
    
  }
  
  public function getAll()
  {
    $this->sammlung = new coface_Sammlung($this->app);
    //inner join auftrag.a on r.auftrag = a.belegnr 
    $rechnungen_akt = $this->app->DB->SelectArr("select r.*, g.id as gid, g.belegnr as gbelegnr,  g.ist as gist, g.soll as gsoll from rechnung r left join gutschrift g on r.id = g.rechnungid where (r.zahlungsstatus = 'offen' or (r.zahlungsstatus = 'bezahlt' and r.ist <> r.soll and r.ist <> r.soll - r.soll * r.zahlungszielskonto / 100)) and (r.status = 'versendet' or r.status = 'storniert') and r.belegnr <> ''  order by belegnr limit 1000");
    $lastOP = $this->app->DB->Select("select max(FLNUM) from coface_OP_Daten");
    if($lastOP)
    {
      $cofaceversendete_rechnungen = $this->app->DB->SelectArr("select r.*, c.* from rechnung r inner join coface_OP_Daten c on r.belegnr = c.OPITMNR where c.FLNUM = ".$lastOP." order by belegnr");
      if($cofaceversendete_rechnungen)
      {
        foreach($cofaceversendete_rechnungen as $k => $v)
        {
          $corechnungen[$v['belegnr']] = $v;
        }
        unset($cofaceversendete_rechnungen);
      }
      $cofaceversendete_gutschriften = $this->app->DB->SelectArr("select r.*, g.id as gid, g.belegnr as gbelegnr,  g.ist as gist, g.soll as gsoll from rechnung r inner join gutschrift g on r.id = g.rechnungid inner join coface_OP_Daten on g.belegnr = c.OPITMNR where c.FLNUM = ".$lastOP." order by belegnr");
      
      if($cofaceversendete_gutschriften)
      {
        foreach($cofaceversendete_gutschriften as $k => $v)
        {
          $cogutschriften[$v['gbelegnr']] = $v;
        }
        unset($cofaceversendete_gutschriften);
      }
      
      $OPSaldo_alt = $this->app->DB->Select("select sum(OPAMTMN) from coface_OP_Daten where FLNUM = ".$lastOP);
    } else {
      echo "Keine alten Daten\r\n";
      $OPSaldo_alt = 0;
    }
    
    //$zahlungen_akt = $this->app->DB->SelectArr("select ke.*, k.iban, k.swift from kontoauszuege_zahlungseingang ke inner join kontoauszuege ka on ke.kontoauszug = ka.id inner join konten k on ka.konto = k.id order by ke.datum");
    
    
    if($rechnungen_akt || isset($corechnungen))
    {
      echo "akt Rechnungen\r\n";
      if($rechnungen_akt)
      {
        $letztebelegnr = '';
        $altrechnung = false;
        foreach($rechnungen_akt as $k => $rechnung)
        {
          echo ".";
          if($letztebelegnr != $rechnung['belegnr'])
          {
            $letztebelegnr = $rechnung['belegnr'];
            $gleichenr = false;
          } else {
            $gleichenr = true;
          }
          if(!$gleichenr){
            $OP = new coface_OP();
            $DB = new coface_DB();
            $OP->SetBelegnummer($rechnung['belegnr']);
            $OP->SetBelegnummerRef($rechnung['belegnr']);
            $DB->SetAdr($rechnung);
            $OP->SetData($rechnung);
            $OP->SetBelegdatum($rechnung['datum']);
            if($rechnung['versendet_am'] && $rechnung['versendet_am'] != '0000-00-00')$OP->SetLieferdatum($rechnung['versendet_am']);
   
            $OP->SetFaelligkeitsdatum(date('Y-m-d',strtotime($rechnung['datum']) + 86400 * (int)$rechnung['zahlungszieltage']));
            switch($rechnung['mahnwesen'])
            {
              case 'mahnung1':
                $OP->SetMahnstufe(1);
              break;
              case 'mahnung2':
                $OP->SetMahnstufe(2);
              break;          
              case 'mahnung3':
                $OP->SetMahnstufe(3);
              break;
            }
            if($rechnung['zahlungszielskonto'])
            {
              $OP->SetSkonto($rechnung['zahlungszieltageskonto'],$rechnung['zahlungszielskonto']);
            }
            if(isset($corechnungen) && isset($corechnungen[$rechnung['belegnr']]))
            {
              //Alte Rechnung nehmen und verrechnen
            }
            if($rechnung['status'] == 'storniert')
            {
              //$this->Create
              $OP->StornoRechnung();
            } else {
              if($rechnung['ist'] != $rechnung['soll'])
              {
                $OP->SetBetrag($rechnung['soll']-$rechnung['ist']);
              }
              if($rechnung['ist'] > 0)$OP->Restposten();
              
            }
            $this->sammlung->addDB($DB);
            $this->sammlung->addOP($OP);
          }
          if(!is_null($rechnung['gsoll']) && $rechnung['gbelegnr'])
          {
            $OP = new coface_OP();
            $DB = new coface_DB();
            $OP->SetBelegnummerRef($rechnung['belegnr']);
            $OP->SetBelegnummer($rechnung['gbelegnr']);
            $DB->SetAdr($rechnung);
            $OP->SetData($rechnung);
            $OP->SetBelegdatum($rechnung['datum']);
            if($rechnung['versendet_am'] && $rechnung['versendet_am'] != '0000-00-00')$OP->SetLieferdatum($rechnung['versendet_am']);
   
            $OP->SetFaelligkeitsdatum(date('Y-m-d',strtotime($rechnung['datum']) + 86400 * (int)$rechnung['zahlungszieltage']));
            switch($rechnung['mahnwesen'])
            {
              case 'mahnung1':
                $OP->SetMahnstufe(1);
              break;
              case 'mahnung2':
                $OP->SetMahnstufe(2);
              break;          
              case 'mahnung3':
                $OP->SetMahnstufe(3);
              break;
            }
            if($rechnung['zahlungszielskonto'])
            {
              $OP->SetSkonto($rechnung['zahlungszieltageskonto'],$rechnung['zahlungszielskonto']);
            }
            
            $OP->DebitorenGutschrift();
            $OP->SetBetrag($rechnung['gsoll']);
            
            $this->sammlung->addDB($DB);
            $this->sammlung->addOP($OP);
          }
          $altrechnung = $rechnung;
        }
      }
      $this->sammlung->SpeichernInDB();
      $this->sammlung->Export();
      return true;
    } else {
      echo "leer";
      return false;
    }
  }
  

}

/*
$foobar = new coface_export($app);
echo "\r\n";
echo $foobar->genEntwickler();
echo "\r\n";*/
class coface_export
{
  private $dateinummer;
  private $OPHeader;
  private $OPDaten;
  private $DBHeader;
  private $DBDaten;
  private $OPDatenalt;
  private $DBDatenalt;
  private $Dateinamen;
  private $app;
  private $Folder;
  private $Entwicklerdateiid;
  private $DateinameOP;
  private $DateinameDB;
  private $DateinameEntwickler;
  public $error;
  
  function __construct(&$app)
  {
    $this->app = $app;
    $this->OPHeader = new coface_OP_Kopf();
    $this->OPHeader->FillData('FLBELANZ',0);
    $this->OPHeader->FillData('FLBELSUM',0);
    
    $this->DBHeader = new coface_DB_Kopf();
    $this->DBHeader->FillData('FLITNR',0);
    $this->Folder = cofaceConfig::$Folder;
    if(is_null($this->Folder))$this->Folder = dirname(__FILE__).'/';
    if($firmendaten = $this->app->DB->SelectArr("select name, strasse, plz, ort from firmendaten limit 1"))$firmendaten = reset($firmendaten);
    if($firmendaten)
    {
      $this->OPHeader->FillData('CLNAME1',$firmendaten['name']);
      $this->OPHeader->FillData('CLZIP',$firmendaten['plz']);
      $this->OPHeader->FillData('CLPLACE',$firmendaten['ort']);
      $this->OPHeader->FillData('CLSTREET',$firmendaten['strasse']);
      $this->OPHeader->FillData('CLNR',cofaceConfig::$Kundennummer);
      
      $this->DBHeader->FillData('CLNAME1',$firmendaten['name']);
      $this->DBHeader->FillData('CLZIP',$firmendaten['plz']);
      $this->DBHeader->FillData('CLPLACE',$firmendaten['ort']);
      $this->DBHeader->FillData('CLSTREET',$firmendaten['strasse']);
      $this->DBHeader->FillData('CLNR',cofaceConfig::$Kundennummer);
    }
  }
  
  function getFromDB($dateinummer, $alt = false)
  {
    if(!$alt)
    {
      $this->dateinummer = (int)$dateinummer;
      $this->Entwicklerdateiid = $this->app->DB->Select("Select dateiid from coface_dateien where FLNUM = ".(int)$dateinummer);
    }
    $sql = "Select * from coface_OP_Daten where FLNUM = ".(int)$dateinummer." order by OPITMNR";
    if($query = $this->app->DB->Query($sql))
    {
      while($row = $this->app->DB->Fetch_Array($query))
      {
        $tmp = new coface_OP_Daten();
        if($tmp->FillFromArray($row))
        {
          $this->addOP($tmp, $alt);
        } else {
          $this->error[] = "OP Daten fehlerhaft";
        }
      }
    } else {
      $this->error[] = "Fehler beim Laden der OP Daten";
    }
    $sql = "Select * from coface_DB_Daten where FLNUM = ".(int)$dateinummer." order by DBNR";
    if($query = $this->app->DB->Query($sql))
    {
      while($row = $this->app->DB->Fetch_Array($query))
      {
        $tmp = new coface_DB_Daten();
        if($tmp->FillFromArray($row))
        {
          $this->addDB($tmp, $alt);
        }else{
          $this->error[] = "DB Daten fehlerhaft";
        }
      }
    } else {
      $this->error[] = "Fehler beim Laden der DB_Daten";
    }  
  }  
  
  function genDiff()
  {
    
    
  }
  
  function addOP(&$datensatz, $alt = false)
  {
    if(!$alt)
    {
      $this->OPDaten[] = $datensatz;
      $this->OPHeader->FillData('FLBELANZ',$this->OPHeader->GetDataByName('FLBELANZ')+1);
      $this->OPHeader->FillData('FLBELSUM',$this->OPHeader->GetDataByName('FLBELSUM')+$datensatz->GetDataByName('OPAMT'));
    } else {
      $this->OPDatenalt[] = $datensatz;
    }
  }
  
  function addDB(&$datensatz, $alt = false)
  {
    if(!$alt)
    {
      $this->DBDaten[] = $datensatz;
      $this->DBHeader->FillData('FLITNR',$this->DBHeader->GetDataByName('FLITNR')+1);
    } else {
      $this->DBDatenalt[] = $datensatz;
    }
  }
  
  function setEntwicklerDateiId($val)
  {
    $this->Entwicklerdateiid = (int)$val;
  }
  
  function genEntwickler()
  {
    //if(!$this->dateinummer)return false;
    $tmp = $this->app->erp->GetTMP().md5(mt_rand()).'.xls';
    $path = str_replace(basename($_SERVER['SCRIPT_FILENAME']), "", $_SERVER['SCRIPT_FILENAME']);
    $path = $path."../userdata/dms/";
    $path = $path.$this->app->Conf->WFdbname;
    while(file_exists($tmp))
    {
      $tmp = $this->app->erp->GetTMP().md5(mt_rand()).'.xls';  
    }
    
    $xls = new coface_excel();
    //TODO Füllen
    
    
    
    $xls->save($tmp);
    if(file_exists($tmp))
    {
      $fileid = $this->app->erp->CreateDatei($this->app->erp->Dateinamen('Entwickler_'.date('Ymd').'.xls'),"Entwickler ".date('d.m.Y'),"","",$tmp,$this->app->User->GetName(),false, $path);
      $this->Entwicklerdateiid = $fileid;
      $this->app->erp->AddDateiStichwort($fileid,"Entwickler","coface",$this->dateinummer);
      @unlink($tmp);
      return true;
    } else return false;
    return $tmp;
    
  }
  
  function genDateien()
  {
    if(!is_array($this->OPDaten) || count($this->OPDaten) < 1)
    {
      echo "OPDaten leer";
      $this->error[] = "OPDaten leer";
      return false;
    }
    if(!is_array($this->DBDaten)|| count($this->DBDaten)< 1)
    {
      $this->error[] = "DBDaten leer";
      return false;
    }    
    if($this->dateinummer)
    {
      $FLNUM = $this->dateinummer;
    } else {
      $FLNUM = $this->app->DB->Select("Select max(FLNUM) from coface_OP_Kopf");
    }
    if(!$FLNUM)$FLNUM = 1;
    $this->dateinummer = $FLNUM;
    /*if(!$this->Entwicklerdateiid)
    {
      $this->Entwicklerdateiid = $this->DB->Select("select datei from datei_stichwoerter where objekt = 'coface' and subjekt = 'Entwickler' and parameter = ".(int)$this->dateinummer);
    }
    if(!$this->Entwicklerdateiid)
    { 
      $this->error[] = "Keine Entwicklerdatei";
      return false;
    }*/
    $this->DBHeader->FillData('FLNUM', $FLNUM);
    $this->OPHeader->FillData('FLNUM', $FLNUM);
    $this->DBHeader->FillData('FLDATE', date('d.m.Y'));
    $this->OPHeader->FillData('FLDATE', date('d.m.Y'));
    $sumop = 0;
    foreach($this->OPDaten as $key => $val)
    {
      if($wert = $val->GetDataByName('OPAMTMN'))$sumop += $wert;
    }
    $this->OPHeader->FillData('FLBELSUM',$sumop);

    $DateinameOP = $this->Folder.'OP_'.str_replace('/','',cofaceConfig::$Kundennummer).'_EUR_'.date('Ymd').'.csv';
    $DateinameDB = $this->Folder.'DB_'.str_replace('/','',cofaceConfig::$Kundennummer).'_EUR_'.date('Ymd').'.csv';
    //$DateinameEntwickler = $this->Folder.'Entwickler.xls';
    /*if(file_exists($DateinameOP) || file_exists($DateinameDB) || file_exists($DateinameEntwickler))
    {
      $this->error[] = "Datei(en) existiert/en bereits";
      return false;
    }*/
    //OP
    if($fh = fopen($DateinameOP, 'w'))
    {
      fputs($fh, iconv('utf8','ISO-8859-1', $this->OPHeader->genFeldnamenzeile()."\r\n"));
      fputs($fh, iconv('utf8','ISO-8859-1', $this->OPHeader->genDatenZeile()."\r\n"));
      $first = true;
      foreach($this->OPDaten as $k => $v)
      {
        if($first)
        {
          fputs($fh, iconv('utf8','ISO-8859-1', $v->genFeldnamenzeile()."\r\n"));
        }
        $first = false;
        fputs($fh, iconv('utf8','ISO-8859-1', $v->genDatenZeile()."\r\n"));
      }
      fclose($fh);
      $this->DateinameOP = $DateinameOP;
      echo $DateinameOP;
    } else return false;
    //DB
    if($fh = fopen($DateinameDB, 'w'))
    {
      fputs($fh, iconv('utf8','ISO-8859-1', $this->DBHeader->genFeldnamenzeile()."\r\n"));
      fputs($fh, iconv('utf8','ISO-8859-1', $this->DBHeader->genDatenZeile()."\r\n"));
      $first = true;
      foreach($this->DBDaten as $k => $v)
      {
        if($first)
        {
          fputs($fh, iconv('utf8','ISO-8859-1', $v->genFeldnamenzeile()."\r\n"));
        }
        $first = false;
        fputs($fh, iconv('utf8','ISO-8859-1', $v->genDatenZeile()."\r\n"));
      }
      fclose($fh);
      $this->DateinameDB = $DateinameDB;
      echo $DateinameDB;
    } else return false;
    //Entwickler
    if($quelle = $this->app->erp->GetDateiPfad($this->Entwicklerdateiid))
    {
      if(@copy($quelle, $DateinameEntwickler))$this->DateinameEntwickler = $DateinameEntwickler;
    }
    return true;
  }
  function Hochladen()
  {
    if(!$this->dateinummer)return false;
    if(!$this->DateinameDB || !file_exists($this->DateinameDB))return false;
    if(!$this->DateinameOP || !file_exists($this->DateinameOP))return false;
    if(!$this->DateinameEntwickler || !file_exists($this->DateinameEntwickler))return false;
    $ftp = new coface_SFTP($this->app);
    if(!$ftp->connect())return false;
    if($ftp->uploadFile($this->DateinameDB)
      && $ftp->uploadFile($this->DateinameOP)
      && $ftp->uploadFile($this->DateinameEntwickler))
    {
      return $this->app->DB->Update("update coface_dateien set status = 'gesendet' where FLNUM = ".(int)$this->dateinummer);
    } 
    return false;
  }
}
/*
$ftp = new coface_SFTP($app);
if($ftp->connect())
{
  echo "OK\r\n";
  print_r($ftp->getFilesOnServer());
  echo $ftp->uploadFile(dirname(__FILE__).'/Entwickler.xlt');
}else{
  echo "failed\r\n";
}*/

class coface_SFTP
{
  private $app;
  private $connection;
  private $sftp;
  function __construct(&$app)
  {
    $this->app = $app;
  }
  
  function connect($server = '192.168.0.26',$username = 'root', $pw = 'G25yw3bEsp', $port = 22 )
  {
    if(false === ($this->connection = ssh2_connect($server, $port)))return false;
    if(!ssh2_auth_password($this->connection, $username, $pw))return false;
    if(false === ($sftp = ssh2_sftp($this->connection)))return false;
    return true;
  }
  
  function uploadFile($datei)
  {
    $ziel = basename($datei);
    echo "Lade hoch: ".$datei."\r\n";
    return ssh2_scp_send($this->connection, $datei, $ziel, 0644);
  }
  
  function getFilesOnServer()
  {
    $stream = ssh2_exec($this->connection,'ls -l');
    stream_set_blocking($stream, true);
    // Whichever of the two below commands is listed first will receive its appropriate output.  The second command receives nothing
    $erg =  stream_get_contents($stream);
    // Close the streams       
    fclose($stream);
    
    $erga = explode("\n",$erg);
    foreach($erga as $k => $v)
    {
      $l[$k] = preg_split("/[\s,]+/", $v);
      if(strpos($l[$k][7],':') === false)
      {
        $t = strtotime($l[$k][6].' '.$l[$k][5].' '.$l[$k][7]);
      }
      else
      {
        $t = strtotime($l[$k][6].' '.$l[$k][5].' '.date("Y").' '.$l[$k][7]);
      }
      $datei['filename'] = trim($l[$k][8]);
      $datei['ftptime'] = $t;
      $datei['filesize'] = trim($l[$k][4]);
      if($datei['filename'] != '')$ergliste[] = $datei;
    }
    if(!isset($ergliste))return false;
    return $ergliste;
  }
  
}

class coface_Sammlung
{
  private $DBs;
  private $OPs;
  private $app;
  private $genDB;
  private $newDateinummer;
  private $export;
  private $ftp;
  public $error;
  
  function __construct(&$app)
  {
    $this->app = $app;
  }
  
  function addOP($OP)
  {
    $this->OPs[] = $OP->Data;
  }
  
  function addDB($DB)
  {
    $found = false;
    if(is_array($this->DBs) && count($this->DBs) > 0)
    {
      foreach($this->DBs as $k => $v)
      {
        if($DB->Data->istGleich($v))$found = true;
        
      }
    }
    if(!$found)$this->DBs[] = $DB->Data;
  }
  
  function SpeichernInDB()
  {
    if(!is_array($this->DBs) || !is_array($this->OPs))return false;
    if(count($this->DBs) < 1 || count($this->OPs) < 1)return false;
    $this->newDateinummer = 1 + (int)$this->app->DB->Select("select max(FLNUM) from coface_dateien");
    $this->genDB = new coface_gen_DB($this->app);
    if(!$this->app->DB->Insert("insert into coface_dateien (FLNUM) values (".$this->newDateinummer.")"))return false;
    foreach($this->OPs as $k => $OP)
    {
      $OP->FLNUM = $this->newDateinummer;
      //echo $this->genDB->Insert($OP)."\r\n";
      $this->app->DB->Insert($this->genDB->Insert($OP));
    }
    foreach($this->DBs as $k => $DB)
    {
      $DB->FLNUM = $this->newDateinummer;
      //echo $this->genDB->Insert($DB)."\r\n";
      $this->app->DB->Insert($this->genDB->Insert($DB));
    }
  }
  
  function lastDateinummer()
  {
     return $this->newDateinummer = (int)$this->app->DB->Select("select max(FLNUM) from coface_dateien");
  }
  
  function Export()
  {
    echo "new export\r\n";
    $this->export = new coface_export($this->app);
    echo "getFromDB\r\n";
    $this->export->getFromDB($this->newDateinummer);
    //alte Daten
    $alt = $this->app->DB->Select("select max(FLNUM) from coface_dateien where status 'gesendet'");
    if($alt)$this->export->getFromDB($alt, true);
    $this->export->genEntwickler();
  }
  
  function Hochladen()
  {
    echo "Hochladen";
    if(!$this->export)
    {
      $this->error[] = "Export nicht geladen";
      return false;
    }
    if(!$this->export->genDateien())
    {
      if($this->export->error)$this->error[] = $this->export->error;
      $this->error[] = "Fehler beim generieren von Dateien";
      return false;
    }
    if($this->export->Hochladen())return true;
    $this->error[] = "Fehler beim Hochladen";
    return false;
  }
  
}

class coface_DB
{
  public $Data;
  function __construct()
  {
    $this->Data = new coface_DB_Daten();
    $this->Data->FillData('DBCOTRY', 'DE');
    $this->Data->FillData('DBLANG', 'DE');
    $this->Data->FillData('DBCLNR',cofaceConfig::$Kundennummer);
    $this->Data->FillData('DBLITYP','0');
    $this->Data->FillData('DBLIMIT',0);
    $this->Data->FillData('DBCURLMT','EUR');
    $this->Data->FillData('DBDALIEFF','01.01.2000');
    $this->Data->FillData('DBDALITO','31.12.2099');
  } 
  
  function SetAdr($val)
  {
    if(!is_array($val))return false;
    if(isset($val['name']))$this->Data->FillData('DBNAME1', $val['name']);
    if(isset($val['ansprechpartner']))$this->Data->FillData('DBCONTAC', $val['ansprechpartner']);
    if(isset($val['strasse']))$this->Data->FillData('DBSTREET', $val['strasse']);
    if(isset($val['plz']))$this->Data->FillData('DBZIP', $val['plz']);
    if(isset($val['ort']))$this->Data->FillData('DBPLACE', $val['ort']);
    if(isset($val['kundennummer']))$this->Data->FillData('DBNR', $val['kundennummer']);
    if(isset($val['kundennummer']))$this->Data->FillData('DBNRZE', $val['kundennummer']);
    if(isset($val['land']))$this->Data->FillData('DBCOTRY', $val['land']);
  }
  
  function SetLimitUnbekannt($val)
  {
    $this->Data->FillData('DBLITYP','2');
    $this->Data->FillData('DBLIMIT',(float)$val);
  }
  
  function SetLimitBekannt($val)
  {
    $this->Data->FillData('DBLITYP','1');
    $this->Data->FillData('DBLIMIT',(float)$val);
  }
}



class coface_OP
{
  public $Data;
  function __construct()
  {
    $this->Data = new coface_OP_Daten();
    $this->Data->FillData('OPCLNR',cofaceConfig::$Kundennummer);
    $this->Data->FillData('OPITMTYP', 'DR');
    $this->Data->FillData('OPCUR', 'EUR');
    $this->Data->FillData('OPCURMN', 'EUR');
  }
  
  public function SetBelegnummer($val)
  {
    $this->Data->FillData('OPITMNR', $val);
  }
  
  public function SetBelegnummerRef($val)
  {
    $this->Data->FillData('OPITMNRREF', $val);
  }
  
  public function SetData($val)
  {
    if(isset($val['kundennummer']))$this->Data->FillData('OPDBNR', $val['kundennummer']);
  }
  
  public function SetLieferdatum($val)
  {
    $this->Data->FillData('OPITMDTL', $val);
  }
  
  public function SetBelegdatum($val)
  {
    $this->Data->FillData('OPITMDAT', $val);
    if(!$this->Data->GetDataByName('OPITMDTL'))$this->Data->FillData('OPITMDTL', $val);
  }
  
  public function SetFaelligkeitsdatum($val)
  {
    $this->Data->FillData('OPITMDTD', $val);
  }
  
  public function SetMahnstufe($val)
  {
    $this->Data->FillData('OPITMDLV', (int)$val);
  }
  
  public function SetSkonto($Tage, $val)
  {
    $this->Data->FillData('OPITMDY1', (int)$Tage);
    $this->Data->FillData('OPITMRD1', (float)$val);
  }
  
  public function SetSkonto2($Tage, $val)
  {
    $this->Data->FillData('OPITMDY2', (int)$Tage);
    $this->Data->FillData('OPITMRD2', (float)$val);
  }
  
  public function SetSkonto3($Tage, $val = 0)
  {
    $this->Data->FillData('OPITMDY3', (int)$Tage);
  }
  
  public function SetText($val)
  {
    $this->Data->FillData('OPITMTXT', ($val));
  }
  
  public function SetBetrag($val)
  {
    $val = (float)$val;
    switch($this->Data->GetDataByName('OPITMTYP'))
    {
      case 'DR':
      case 'SG':
      case 'WS':
        if($val < 0)$val = -$val;
      break;
      case 'DG':
      case 'DZ':
      case 'SR':
        if($val > 0)$val = -$val;
      break;
    }
    $this->Data->FillData('OPAMT', $val);
    $this->Data->FillData('OPAMTMN', $val);
  }
  
  
  public function DebitorenRechnung()
  {
    $this->Data->FillData('OPITMTYP', 'DR');
  }
  
  public function DebitorenGutschrift()
  {
    $this->Data->FillData('OPITMTYP', 'DG');
    
  }
  public function DebitorenZahlung()
  {
    $this->Data->FillData('OPITMTYP', 'DZ');
  }
  
  public function StornoRechnung()
  {
    $this->Data->FillData('OPITMTYP', 'SR');  
  }
  public function StornoGutschrift()
  {
    $this->Data->FillData('OPITMTYP', 'SG');
  }
  
  public function Restposten()
  {
    $this->Data->FillData('OPITMTYP', 'RP');
  }
  
  public function Warenstreit()
  {
    $this->Data->FillData('OPITMTYP', 'WS');
  }
  
}
if(isset($argv[1]) && strpos(strtoupper($argv[1]),'EXCELTEST') !== false)
{
  $xlt = new coface_excel('Entwickler2.xlt');
  /*
  $nr = $xlt->AddPage("Test");
  if($nr)
  {
    if($xlt->AddElement($nr,'A1','moo'))
    {
      $xlt->save('EXCELTEST.xls');
    }
  }*/
  $xlt->InsertJournal("RE-Journal", "Debitor","Debitor-Nr.","1234","05.05.2015","30.07.2015","10.00");
  $xlt->InsertJournal("RE-Journal", "Debitor","Debitor-Nr.","1234","05.05.2015","30.07.2015","10.00");
  $xlt->InsertJournal("RE-Journal", "Debitor","Debitor-Nr.","1234","05.05.2015","30.07.2015","10.00");
  $xlt->InsertJournal("RE-Journal", "Debitor","Debitor-Nr.","1234","05.05.2015","30.07.2015","10.00");
  $xlt->AddZahlungseingang('Musterbank', "Debitor", "05.05.2015", "123456", '980.00', '20.00', '1000.00');
  $xlt->AddZahlungseingang('Musterbank', "Debitor 2", "05.05.2015", "123456", '80.00', '20.00', '100.00');
  $xlt->AddZahlungseingang('Musterbank', "Debitor 3", "05.05.2015", "123456", '1980.00', '20.00', '2000.00');
  $xlt->save('EXCELTEST2.xls');
}
if(isset($argv[1]) && strpos(strtoupper($argv[1]),'EDISON') !== false)
{
$tmp = new WaWisioncoface($app);
$tmp->ImportEdison("OPListe_31.05.2015.csv");
}

/*
$xlt = new coface_excel();
$xlt->AnzahlGutschriften = 1;
$xlt->GemGutschriften = 'ABC';
$xlt->SaldoGutschriften = 12.10;
$xlt->Absender = 
"Firma ABC
Muster Str. 5
86343 Königsbrunn";
$xlt->save('bla.xls');
*/
class coface_excel
{
  public $Datum;
  public $KundenNr;
  public $Ansprechpartner = "";
  public $Absender = "";
  
  public $OPSaldoalt = 0;
  public $DatumOP = "";
  public $AnzahlRechnungen = 0;
  public $AnzahlStornoRechnungen = 0;
  public $AnzahlGutschriften = 0;
  public $AnzahlStornoGutschriften = 0;
  public $AnzahlRestposten = 0;
  public $AnzahlStornoRestposten = 0;

  public $GemRechnungen = "";
  public $GemStornoRechnungen = "";
  public $GemGutschriften = "";
  public $GemStornoGutschriften = "";
  public $GemRestposten = "";
  public $GemStornoRestposten = "";
  
  public $SaldoRechnungen = 0;
  public $SaldoStornoRechnungen = 0;
  public $SaldoGutschriften = 0;
  public $SaldoStornoGutschriften = 0;
  public $SaldoRestposten = 0;
  public $SaldoStornoRestposten = 0;
  
  public $AnzahlZahlungseingaenge = 0;
  public $Netto = 0;
  public $Abzuege = 0;
  public $Brutto = 0;
  public $OPSaldoNeu = 0;
  public $eigenenHausbankkonto  = 0;
  public $CFKonto = 0;
  
  private $anz;
  
  
  function __construct($Excelfile = null)
  {
    require_once dirname(__FILE__)."/PHPExcel.php";
    $this->KundenNr = cofaceConfig::$Kundennummer;
    $this->Datum = date("d.m.Y");
    $this->DatumOP = date("d.m.Y");
    $this->_oPHPExcel = new PHPExcel();
    $this->_oExcelReader = new PHPExcel_Reader_Excel5();
    $this->_oExcelReader->setReadDataOnly(false);
    if(is_null($Excelfile))
    {
      $this->_oPHPExcel = $this->_oExcelReader->load(cofaceConfig::$ExcelRoh);
    } else {
      $this->_oPHPExcel = $this->_oExcelReader->load($Excelfile);
    }
    if($this->_oPHPExcel->getSheetCount() > 1)
    {
      for($i = 1; $i < $this->_oPHPExcel->getSheetCount(); $i++)
      {
        $this->_oPHPExcel->setActiveSheetIndex($i);
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('A5','Kunden-Nr. '.cofaceConfig::$Kundennummer);
        if($i == $this->_oPHPExcel->getSheetCount() - 1)
        {
          $this->_oPHPExcel->getActiveSheet()->SetCellValue('G7',date("d.m.Y"));
        }else{
          $this->_oPHPExcel->getActiveSheet()->SetCellValue('F7',date("d.m.Y"));  
        }
        
        
        
      }
      
      
    }
    
  }
  
  function InsertJournalNr($Sheetname, $Journalnr)
  {
    if($nr && $Sheetname)
    {
      if($this->_oPHPExcel->sheetNameExists($Sheetname))
      {
        if(!isset($this->anz[$Sheetname]))$this->anz[$Sheetname] = 0;
        $this->anz[$Sheetname]++;
        $nr = $this->_oPHPExcel->getIndex($this->_oPHPExcel->getSheetByName($Sheetname));
        if($nr)
        {
          $this->_oPHPExcel->setActiveSheetIndex($nr);
          $this->_oPHPExcel->getActiveSheet()->SetCellValue('D7',$Journalnr);
        }
      }
    }
  }
  //TO Kundenname
  function InsertJournal($Sheetname, $Debitor,$DebitorNr,$Belegnummer,$Belegdatum,$Faelligkeitsdatum,$Betrag)
  {
    if($this->_oPHPExcel->sheetNameExists($Sheetname))
    {
      if(!isset($this->anz[$Sheetname]))$this->anz[$Sheetname] = 0;
      $this->anz[$Sheetname]++;
      $nr = $this->_oPHPExcel->getIndex($this->_oPHPExcel->getSheetByName($Sheetname));
      if($nr)
      {
        $this->_oPHPExcel->setActiveSheetIndex($nr);
        if($this->anz[$Sheetname] > 2)
        {
          $this->_oPHPExcel->getActiveSheet()->insertNewRowBefore($this->anz[$Sheetname]+9,1);
        }
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('A'.($this->anz[$Sheetname]+9),$Debitor);
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('B'.($this->anz[$Sheetname]+9),$DebitorNr);
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('C'.($this->anz[$Sheetname]+9),$Belegnummer);
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('D'.($this->anz[$Sheetname]+9),$Belegdatum);
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('E'.($this->anz[$Sheetname]+9),$Faelligkeitsdatum);
        
        if($this->anz[$Sheetname] > 1)$this->_oPHPExcel->getActiveSheet()->duplicateStyle($this->_oPHPExcel->getActiveSheet()->getStyle('F10'), 'F'.($this->anz[$Sheetname]+9));
        
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('F'.($this->anz[$Sheetname]+9),$Betrag);
        //echo 'F'.($this->anz[$Sheetname]+9).' '.$Betrag. ' F'.($this->anz[$Sheetname]+($this->anz[$Sheetname] > 2?-1:0)+11)." ".'=Summe(F10:F'.($this->anz[$Sheetname]+($this->anz[$Sheetname] > 2?-1:0)+10).')'."\r\n";
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('F'.($this->anz[$Sheetname]+($this->anz[$Sheetname] > 2?-1:0)+11),'=SUM(F10:F'.($this->anz[$Sheetname]+($this->anz[$Sheetname] > 2?-1:0)+10).')');
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('F'.($this->anz[$Sheetname]+($this->anz[$Sheetname] > 2?-1:0)+12),'');
        switch($Sheetname)
        {
          case 'RE-Journal':
            $this->AnzahlRechnungen = $this->anz[$Sheetname];
            $this->GemRechnungen = $Sheetname;
            $this->SaldoRechnungen += $Betrag;
          break;
          case 'RG-Storno Journal':
            $this->AnzahlStornoRechnungen = $this->anz[$Sheetname];
            $this->GemStornoRechnungen = $Sheetname;
            $this->SaldoStornoRechnungen += $Betrag;
          break;
          case 'GS-Journal':
            $this->AnzahlGutschriften = $this->anz[$Sheetname];
            $this->GemGutschriften = $Sheetname;
            $this->SaldoGutschriften += $Betrag;
          break;
          case 'GS-Storno Journal':
            $this->AnzahlStornoGutschriften = $this->anz[$Sheetname];
            $this->GemStornoGutschriften = $Sheetname;
            $this->SaldoStornoGutschriften += $Betrag;
          break;
          case 'Restposten':
            $this->AnzahlRestpostenn = $this->anz[$Sheetname];
            $this->GemRestposten = $Sheetname;
            $this->SaldoRestposten += $Betrag;
          break;
          case 'Restposten Storno Journal':
            $this->AnzahlStornoRestposten = $this->anz[$Sheetname];
            $this->GemStornoRestposten = $Sheetname;
            $this->SaldoStornoRestposten += $Betrag;
          break;
        }
      }
    }
  }
  
  function AddZahlungseingang($Bank, $Debitor, $BelegDatumAuszug, $Belegnummer, $Netto, $Abzuege, $Brutto)
  {
    if($this->_oPHPExcel->sheetNameExists('ZE-Journal'))
    {
      if(!isset($this->anz['ZE-Journal']))$this->anz['ZE-Journal'] = 0;
      $this->anz['ZE-Journal']++;
      $nr = $this->_oPHPExcel->getIndex($this->_oPHPExcel->getSheetByName('ZE-Journal'));
      if($nr)
      {
        $this->_oPHPExcel->setActiveSheetIndex($nr);
        if($this->anz['ZE-Journal'] > 2)
        {
          $this->_oPHPExcel->getActiveSheet()->insertNewRowBefore($this->anz['ZE-Journal']+9,1);
        }
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('A'.($this->anz['ZE-Journal']+9),$Bank);
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('B'.($this->anz['ZE-Journal']+9),$Debitor);
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('C'.($this->anz['ZE-Journal']+9),$BelegDatumAuszug);
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('D'.($this->anz['ZE-Journal']+9),$Belegnummer);
        
        
        if($this->anz['ZE-Journal'] > 1)$this->_oPHPExcel->getActiveSheet()->duplicateStyle($this->_oPHPExcel->getActiveSheet()->getStyle('E10'), 'E'.($this->anz['ZE-Journal']+9));
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('E'.($this->anz['ZE-Journal']+($this->anz['ZE-Journal'] > 2?-1:0)+11),'=SUM(E10:E'.($this->anz['ZE-Journal']+($this->anz['ZE-Journal'] > 2?-1:0)+10).')');
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('E'.($this->anz['ZE-Journal']+($this->anz['ZE-Journal'] > 2?-1:0)+12),'');
        if($this->anz['ZE-Journal'] > 1)$this->_oPHPExcel->getActiveSheet()->duplicateStyle($this->_oPHPExcel->getActiveSheet()->getStyle('F10'), 'F'.($this->anz['ZE-Journal']+9));
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('F'.($this->anz['ZE-Journal']+($this->anz['ZE-Journal'] > 2?-1:0)+11),'=SUM(F10:F'.($this->anz['ZE-Journal']+($this->anz['ZE-Journal'] > 2?-1:0)+10).')');
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('F'.($this->anz['ZE-Journal']+($this->anz['ZE-Journal'] > 2?-1:0)+12),'');
        if($this->anz['ZE-Journal'] > 1)$this->_oPHPExcel->getActiveSheet()->duplicateStyle($this->_oPHPExcel->getActiveSheet()->getStyle('G10'), 'G'.($this->anz['ZE-Journal']+9));
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('G'.($this->anz['ZE-Journal']+($this->anz['ZE-Journal'] > 2?-1:0)+11),'=SUM(G10:G'.($this->anz['ZE-Journal']+($this->anz['ZE-Journal'] > 2?-1:0)+10).')');
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('G'.($this->anz['ZE-Journal']+($this->anz['ZE-Journal'] > 2?-1:0)+12),'');
        
        
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('E'.($this->anz['ZE-Journal']+9),$Netto);
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('F'.($this->anz['ZE-Journal']+9),$Abzuege);
        $this->_oPHPExcel->getActiveSheet()->SetCellValue('G'.($this->anz['ZE-Journal']+9),$Brutto);
        $this->AnzahlZahlungseingaenge = $htis->anz['ZE-Journal'];
        $this->Netto += $Netto;
        $this->Abzuege += $Abzuege;
        $this->Brutto += $Brutto;
      }
    }
  }
  
  function removeUnusedSheets()
  {
    $toremove = array();
    if(!isset($this->anz) || !isset($this->anz['RE-Journal']) || $this->anz['RE-Journal'])$toremove['RE-Journal'] = true;
    if(!isset($this->anz) || !isset($this->anz['RG-Storno Journal']) || $this->anz['RG-Storno Journal'])$toremove['RG-Storno Journal'] = true;
    if(!isset($this->anz) || !isset($this->anz['GS-Journal']) || $this->anz['GS-Journal'])$toremove['GS-Journal'] = true;
    if(!isset($this->anz) || !isset($this->anz['GS-Storno Journal']) || $this->anz['GS-Storno Journal'])$toremove['GS-Storno Journall'] = true;
    if(!isset($this->anz) || !isset($this->anz['Restposten']) || $this->anz['Restposten'])$toremove['Restposten'] = true;
    if(!isset($this->anz) || !isset($this->anz['Restposten Storno Journal']) || $this->anz['Restposten Storno Journal'])$toremove['Restposten Storno Journal'] = true;
    if(count($toremove) > 0)
    {
      foreach($toremove as $Sheetname => $val)
      {
        if($key)
        {
          if($this->_oPHPExcel->sheetNameExists($Sheetname))
          {
            $nr = $this->_oPHPExcel->getIndex($this->_oPHPExcel->getSheetByName($Sheetname));
            if($nr)
            {
              $this->_oPHPExcel->removeSheetByIndex($nr);
            }
          }
        }
      }
    }
  }
  
  function AddPage($name)
  {
    $tmp = $this->_oPHPExcel->createSheet();
    $tmp->setTitle($name);
    return $this->_oPHPExcel->getIndex($tmp);
  }
  
  function AddElement($nr, $Zelle, $val)
  {
    if(!$this->_oPHPExcel)return false;
    $n = preg_match_all("/([A-Za-z]+)(\d+)/",$Zelle, $treffer);
    if(!$n)return false;
    if(count($treffer)< 3)return false;
    if(!isset($treffer[0][0])||!isset($treffer[1][0])||!isset($treffer[2][0]))return false;
    if(strlen($treffer[1][0])>2)return false;
    if($treffer[2][0]< 1)return false;
    if($this->_oPHPExcel->getSheetCount() > $nr)
    {$this->_oPHPExcel->setActiveSheetIndex($nr);
      $this->_oPHPExcel->getActiveSheet()->SetCellValue($Zelle,$val);
      return true;
    } else {
      return false;
    }
  }
  
  function save($filename)
  {
    if(strpos($filename, '/') === false){
      if(!is_null(cofaceConfig::$Folder))
      {
        $filename = cofaceConfig::$Folder.$filename;
      }else{
        $filename = dirname(__FILE__).'/'.$filename;
      }
    }
    $this->_oPHPExcel->setActiveSheetIndex(0);
    $this->_oPHPExcel->getActiveSheet()->SetCellValue('G5',$this->KundenNr);
    $this->_oPHPExcel->getActiveSheet()->SetCellValue('A6','z. H. '.$this->Ansprechpartner);
    if(($this->Absender))
    {
      $Zeilen = explode("\n",$this->Absender);
      
      for($i = 0; $i < 6; $i++)
      {
        if(isset($Zeilen[$i]))$this->_oPHPExcel->getActiveSheet()->SetCellValue('E'.(6+$i),trim($Zeilen[$i]));
      }
    
    }
    $this->_oPHPExcel->getActiveSheet()->SetCellValue('G12',$this->Datum);
    $this->_oPHPExcel->getActiveSheet()->SetCellValue('D24',$this->DatumOP);
    if($this->OPSaldoalt)$this->_oPHPExcel->getActiveSheet()->SetCellValue('G21',$this->OPSaldoalt);
    if($this->AnzahlRechnungen)$this->_oPHPExcel->getActiveSheet()->SetCellValue('A27',$this->AnzahlRechnungen);
    if($this->AnzahlStornoRechnungen)$this->_oPHPExcel->getActiveSheet()->SetCellValue('A28',$this->AnzahlStornoRechnungen);
    if($this->AnzahlGutschriften)$this->_oPHPExcel->getActiveSheet()->SetCellValue('A29',$this->AnzahlGutschriften);
    if($this->AnzahlStornoGutschriften)$this->_oPHPExcel->getActiveSheet()->SetCellValue('A30',$this->AnzahlStornoGutschriften);
    if($this->AnzahlRestposten)$this->_oPHPExcel->getActiveSheet()->SetCellValue('A31',$this->AnzahlRestposten);
    if($this->AnzahlStornoRestposten)$this->_oPHPExcel->getActiveSheet()->SetCellValue('A32',$this->AnzahlStornoRestposten);
    
    if($this->GemRechnungen)$this->_oPHPExcel->getActiveSheet()->SetCellValue('D27',$this->GemRechnungen);
    if($this->GemStornoRechnungen)$this->_oPHPExcel->getActiveSheet()->SetCellValue('D28',$this->GemStornoRechnungen);
    if($this->GemGutschriften)$this->_oPHPExcel->getActiveSheet()->SetCellValue('D29',$this->GemGutschriften);
    if($this->GemStornoGutschriften)$this->_oPHPExcel->getActiveSheet()->SetCellValue('D30',$this->GemStornoGutschriften);
    if($this->GemRestposten)$this->_oPHPExcel->getActiveSheet()->SetCellValue('D31',$this->GemRestposten);
    if($this->GemStornoRestposten)$this->_oPHPExcel->getActiveSheet()->SetCellValue('D32',$this->GemStornoRestposten);

    if($this->SaldoRechnungen)$this->_oPHPExcel->getActiveSheet()->SetCellValue('G27',$this->SaldoRechnungen);
    if($this->SaldoStornoRechnungen)$this->_oPHPExcel->getActiveSheet()->SetCellValue('G28',$this->SaldoStornoRechnungen);
    if($this->SaldoGutschriften)$this->_oPHPExcel->getActiveSheet()->SetCellValue('G29',$this->SaldoGutschriften);
    if($this->SaldoStornoGutschriften)$this->_oPHPExcel->getActiveSheet()->SetCellValue('G30',$this->SaldoStornoGutschriften);
    if($this->SaldoRestposten)$this->_oPHPExcel->getActiveSheet()->SetCellValue('G31',$this->SaldoRestposten);
    if($this->SaldoStornoRestposten)$this->_oPHPExcel->getActiveSheet()->SetCellValue('G32',$this->SaldoStornoRestposten);    
    
    if($this->AnzahlZahlungseingaenge)$this->_oPHPExcel->getActiveSheet()->SetCellValue('A34',$this->AnzahlZahlungseingaenge);    
    if($this->Netto)$this->_oPHPExcel->getActiveSheet()->SetCellValue('D35',$this->Netto);
    if($this->Abzuege)$this->_oPHPExcel->getActiveSheet()->SetCellValue('D36',$this->Abzuege);
    if($this->Brutto)$this->_oPHPExcel->getActiveSheet()->SetCellValue('G37',$this->Brutto);
    if($this->OPSaldoNeu)$this->_oPHPExcel->getActiveSheet()->SetCellValue('G38',$this->OPSaldoNeu);
    if($this->eigenenHausbankkonto)$this->_oPHPExcel->getActiveSheet()->SetCellValue('D42',$this->eigenenHausbankkonto);
    if($this->CFKonto)$this->_oPHPExcel->getActiveSheet()->SetCellValue('D43',$this->CFKonto);
    
    $this->removeUnusedSheets();
    $objWriter = new PHPExcel_Writer_Excel5($this->_oPHPExcel);
    $objWriter->save($filename);    
  }
  
}
?>

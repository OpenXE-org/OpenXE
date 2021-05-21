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

  class StringCleaner
  {
    private $elements;
    private $htmlpuriferconfig;
    private $htmlpurifer;
    private $ruleregexps;
    /** @var Application */
    private $app;

    /**
     * StringCleaner constructor.
     *
     * @param null|Application $app
     */
    public function __construct($app = null)
    {
      $this->app = $app;
      if(class_exists('HTMLPurifier_Config')) {
        $this->htmlpuriferconfig = HTMLPurifier_Config::createDefault();
        $this->htmlpuriferconfig->set('Core.Encoding', 'UTF-8');
        $this->htmlpuriferconfig->set('Attr.AllowedFrameTargets', ['_blank']); // Allow hyperlinks with target="_blank"
        //$this->htmlpuriferconfig->set('HTML.AllowedElements', 'h1,h2,h3,h4,h5,h6,p,a,strong,em,ol,ul,li,img,param,div,br,form,label,fieldset,input,textarea,select,option');
        $this->htmlpurifer = new HTMLPurifier($this->htmlpuriferconfig);
      }
      $this->elements = array('nohtml'=> array('ust_befreit','abweichendelieferadresse','bestellungsart','bearbeiter','datum','lieferdatum','name','anrede','partner','packstation_inhaber','packstation_station','packstation_ident','packstation_plz','packstation_ort','partnerid','kennen','ihrebestellnummer'
                                        ,'abteilung','unterabteilung','ansprechpartner','adresszusatz','strasse','land','bundesstaat','plz','ort','versandart','internet','transaktionsnummer','vertrieb','zahlungsweise'
                                        ,'lieferabteilung','lieferunterabteilung','lieferansprechpartner','lieferadresszusatz','lieferstrasse','lieferland','lieferbundesstaat','lieferplz','lieferort'
                                        ,'bank_inhaber','bank_institut','bank_blz','bank_konto'
                                        ,'email','telefon','telefax','ustid','partner','projekt','herstellernummer','ean','nummer','name_de','name_ean'),
                          'nojs' => array('anabregstext','anabregstext_en','uebersicht_de','uebersicht_en','kurztext_de','kurztext_en','internebemerkung','internebezeichnung','freitext'));

      $this->rulechecks = array('digit'=>'/^[0-9]+$/'
                               ,'alpha'=>'/^[a-zA-Z]+$/'
                               ,'alphadigit'=>'/^[0-9a-zA-Z]+$/'
                               ,'username'=>'/^[0-9a-zA-Z\.\-]+$/'
                               ,'space'=>'/^[\x20]+$/'
                               ,'module'=>'/^[0-9a-zA-Z\_]$/'
                               ,'password'=>'/^[^\s\n]{1}[^\n]{5}.*$/'
                               ,'email'=>'/^[^@\s\x00-\x20]+@[^@\s\x00-\x20\.]+\.[^@\s\x00-\x20\.]+[^@\s\x00-\x20]*$/'
                               );

      $this->ruleregexps = array(
                                 'digit'=>'/[^0-9]/'
                                 ,'username'=>'/[^0-9a-zA-Z\.\-]/'
                                 ,'alpha'=>'/[^a-zA-Z]/'
                                 ,'alphadigits'=>'/[^0-9a-zA-Z]/'
                                 ,'module'=>'/[^0-9a-zA-Z\_]/'
                                 ,'moduleminus'=>'/[^0-9a-zA-Z\_\-]/'
                                 ,'alphadigitsspecial'=>'/[^0-9a-zA-Z\_\.\(\)]/'
                                 ,'base64'=>'/[^0-9a-zA-Z\=\+\-\_\/]/'
                                );
    }

    function SyntaxByElement($key, $default = '')
    {
      foreach($this->elements as $type => $arr) {
        if(in_array($key, $arr)) {
          return $type;
        }
      }
      return $default;
    }

    function CleanSQLReturn($value, $columnname, $default = '')
    {
      if($value == '' || is_numeric($value))
      {
        return $value;
      }
      if(in_array($columnname, array('nummer','name','kundennummer','bezeichnung','bezeichnunglieferant','lieferantennummer','mitarbeiternummer','name_de','name_en',
                                     'kurzbezeichnung','abkuerzung',
                                     'strasse','plz','ort','land','ansprechpartner','abteilung','unterabteilung',
                                 'liefername','lieferstrasse','lieferplz','lieferort','lieferland','lieferansprechpartner','lieferabteilung','lieferunterabteilung'))){
        return strip_tags($value);
      }
      if($default == 'xss_clean')
      {
        return $this->xss_clean($value, false);
      }
      if($this->htmlpurifer)
      {
        return $this->htmlpurifer->purify($value);
      }
      return $value;
    }

    function RuleCheck($string, $rule = null, &$found = false)
    {
      if(isset($this->rulechecks[$rule]))
      {
        $found = true;
        return preg_match_all($this->rulechecks[$rule], $string, $dummy);
      }
      switch($rule)
      {
        case 'datum':
          $found = true;

          if(preg_match_all('/([0-9]+)\.([0-9]+)\.$/', $string, $matches))
          {
            $string = $matches[1][0].'.'.$matches[2][0].'.'.date('Y');
          }

          try {
            if($x = new DateTime($string)) {
              return $x->format('Y') > 0;
            }
          }
          catch (Exception $e) {
            return false;
          }
          return false;
        break;
      }
    }

    function CheckSQLHtml($sql)
    {
      $start = 0;
      $len = strlen($sql);
      $lvl = 0;
      $col = 0;
      $ret = array(0);
      $instring = false;
      for($i = $start; $i < $len; $i++)
      {
        $char = $sql[$i];
        switch($char)
        {
          case "'":
            if($instring)
            {
              if($sql[$i-1] != '\\')
              {
                $instring = false;
              }
            }else{
              if($sql[$i-1] != '\\'){
                $instring = true;
              }
            }
          break;
          case "(":
            if($instring)
            {

            }else{
              $lvl++;
            }
          break;
          case ")":
            if($instring)
            {

            }else{
              $lvl--;
            }
          break;
          case "<":
            if($instring)
            {
              if(preg_match('/<[a-zA-Z]/',$char.$sql[$i+1]))
              {
                if($ret[$col] != 2)
                {
                  $ret[$col] = 1;
                }
              }
            }
          break;
          case ',':
            if($instring)
            {

            }else{
              if($lvl == 0)
              {
                $col++;
                $ret[$col] = 0;
              }
            }
          break;
          case 'o':
          case 'O':
            if($instring)
            {
              if($i < $len -4)
              {
                if(strtolower(substr($sql, $i, 2)) == 'on')
                {
                  if(preg_match('/^on[a-z]+(\s*)=/', substr($sql, $i)))
                  {
                    $ret[$col] = 2;
                  }
                }
              }
            }
          break;
          case 'F':
          case 'f':
            if($instring)
            {

            }else{
              if($lvl == 0)
              {
                if($i < $len - 4)
                {
                  if(strtolower(substr($sql, $i, 4)) == 'from')
                  {
                    break 2;
                  }
                }
              }
            }
          break;
        }
      }
      $where = strripos($sql, 'where');
      $restsql = substr($sql, $i, $where - $i);
      if(preg_match('/<[a-zA-Z]/', $restsql))
      {
        if(preg_match('/on[a-z]+(\s*)=/',$restsql))
        {
          if($ret)
          {
            foreach($ret as $k => $v)
            {
              $ret[$k] = 2;
            }
          }
        }else{
          if($ret)
          {
            foreach($ret as $k => $v)
            {
              if($v != 2)
              {
                $ret[$k] = 1;
              }
            }
          }
        }
      }
      return $ret;
    }

    public function CleanString($string, $rule = null, &$found = false)
    {
      if(is_null($rule))
      {
        $rule = 'nothml';
      }
      switch($rule)
      {
        case 'email':
          if($this->RuleCheck($string, $rule))
          {
            return $string;
          }
          return '';
        break;
        case 'nohtml':
          $found = true;
          if($string == '' || is_numeric($string))
          {
            return $string;
          }
          if(strpos($string,'<') === false)
          {
            return $string;
          }
          return strip_tags($string);
        break;
        case 'datum':
          $found = true;
          $string_ = $string;
          if(preg_match_all('/([0-9]+)\.([0-9]+)\.$/', $string, $matches))
          {
            $string_ = $matches[1][0].'.'.$matches[2][0].'.'.date('Y');
          }
          try
          {
            if($x = new DateTime($string_))
            {
              if($x->format('Y') <= 0)
              {
                return '';
              }
              return $string;
            }
          } catch (Exception $e) {
            return '';
          }
          return '';
        break;
        case 'xss_clean':
          $found = true;
          if($string == '' || is_numeric($string))
          {
            return $string;
          }
          if(strpos($string,'<') === false){
            return $string;
          }
          return $this->xss_clean($string, false);
        break;
        case 'nojs':
          $found = true;
          if($string == '' || is_numeric($string))return $string;
          if(strpos($string,'<') === false)return $string;
          if($this->htmlpurifer)
          {
            return $this->htmlpurifer->purify($string);
          }
          return $this->xss_clean($string);
        break;
        case 'id':
          $found = true;
          if((String)$string === '')
          {
            return $string;
          }
          return (int)$string;
        break;
        case 'doppelid':
          $found = true;
          if((String)$string === '')
          {
            return $string;
          }
          $stringa = explode('-', $string, 2);
          if(count($stringa) == 1)return (int)$stringa[0];
          return ($stringa[0]===''?'':(int)$stringa[0]).'-'.(int)$stringa[1];
        break;
        case 'module':
          $found = true;
          return preg_replace ($this->ruleregexps[$rule], '' , $string);
        break;
        default:
          if(isset($this->ruleregexps[$rule]))
          {
            $found = true;
            return preg_replace ($this->ruleregexps[$rule], '' , $string);
          }
        break;
      }
      return $string;
    }

    public function xss_clean($data, $usepurify = true)
    {
      if($usepurify && !empty($this->htmlpurifer))
      {
        return $this->htmlpurifer->purify($data);
      }
      // Fix &entity\n;
      $data = str_replace(array('&amp;','&lt;','&gt;'), array('&amp;amp;','&amp;lt;','&amp;gt;'), $data);
      $data = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $data);
      $data = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $data);
      $data = html_entity_decode($data, ENT_COMPAT, 'UTF-8');
      return $data;
      // Remove any attribute starting with "on" or xmlns
      $data = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $data);

      // Remove javascript: and vbscript: protocols
      $data = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $data);
      $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $data);
      $data = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $data);

      // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
      $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
      $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $data);
      $data = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $data);

      // Remove namespaced elements (we do not need them)
      $data = preg_replace('#</*\w+:\w[^>]*+>#i', '', $data);

      do
      {
          // Remove really unwanted tags
          $old_data = $data;
          $data = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $data);
      }
      while ($old_data !== $data);

      return $data;
    }

    function XMLArray_clean(&$xml, $lvl = 0)
    {
      if(is_string($xml))
      {

      }elseif(is_array($xml))
      {
        if(count($xml) > 0)
        {
          foreach($xml as $k => $v)
          {
            if(is_string($v))
            {
              $xml[$k] = $this->CleanString($v, $this->SyntaxByElement($k,'nojs'));
            }
            if($lvl < 10)
            {
              $this->XMLArray_clean($v, $lvl + 1);
            }
          }
        }
      }elseif(is_object($xml))
      {
        if(count($xml) > 0)
        {
          foreach($xml as $k => $v)
          {
            if(count($v) > 0)
            {
              if($lvl < 10)
              {
                $this->XMLArray_clean($v, $lvl + 1);
              }
            }elseif((String)$v != '')
            {
              if(isset($xml->$k))
              {
                //$xml->$k = $this->CleanString($v, $this->SyntaxByElement($k,'nojs'));
              }
            }
          }
        }
      }
      return $xml;
    }
  }

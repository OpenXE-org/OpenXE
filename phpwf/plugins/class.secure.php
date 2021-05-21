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

/// Secure Layer, SQL Inject. Check, Syntax Check
class Secure 
{
  public $GET;
  public $POST;

  /**
   * Secure constructor.
   *
   * @param ApplicationCore $app
   */
  public function __construct($app){
    $this->app = $app;
    // clear global variables, that everybody have to go over secure layer
    $this->GET = $_GET;
    if(isset($this->GET['msgs']) && isset($this->app->Location)) {
      $this->GET['msg'] = $this->app->Location->getMessage($this->GET['msgs']);
    }
    //    $_GET="";
    $this->POST = $_POST;
    //   $_POST="";
    if(!isset($this->app->stringcleaner) && file_exists(__DIR__. '/class.stringcleaner.php')) {
      if(!class_exists('StringCleaner')) {
        require_once __DIR__ . '/class.stringcleaner.php';
      }
      $this->app->stringcleaner = new StringCleaner($this->app);
    }

    $this->AddRule('notempty','reg','.'); // at least one sign
    $this->AddRule('alpha','reg','[a-zA-Z]');
    $this->AddRule('digit','reg','[0-9]');
    $this->AddRule('space','reg','[ ]');
    $this->AddRule('specialchars','reg','[_-]');
    $this->AddRule('email','reg','^[a-zA-Z0-9._-]+@[a-zA-Z0-9._-]+\.([a-zA-Z]{2,4})$');
    $this->AddRule('datum','reg','([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{4})');

    $this->AddRule('username','glue','alpha+digit');
    $this->AddRule('password','glue','alpha+digit+specialchars');
  }

  /**
   * @param string $name
   * @param null   $rule
   * @param string $maxlength
   * @param string $sqlcheckoff
   *
   * @return array|mixed|string
   */
  public function GetGET($name,$rule=null,$maxlength='',$sqlcheckoff='')
  {
    if($name === 'msg' && isset($this->app->erp) && method_exists($this, 'xss_clean')) {
      $ret = $this->Syntax(isset($this->GET[$name])?$this->GET[$name]:'','',$maxlength,$sqlcheckoff);
      $ret = $this->app->erp->base64_url_decode($ret);
      if(strpos($ret,'"button"') === false){
        $ret = $this->xss_clean($ret);
      }

      return $this->app->erp->base64_url_encode($ret);
    }
    if($rule === null) {
      $rule = $this->NameToRule($name);
    }
    return $this->Syntax(isset($this->GET[$name])?$this->GET[$name]:'',$rule,$maxlength,$sqlcheckoff);
  }

  function NameToRule($name)
  {
    switch($name)
    {
      case 'id':
        return 'doppelid';
        break;
      case 'sid':
        return 'alphadigits';
      break;
      case 'module':
      case 'smodule':
      case 'action':
      case 'saction':
        return 'module';
      break;
      case 'cmd':
        return 'moduleminus';
      break;
    }
    return 'nothtml';
  }
  
  public function GetPOST($name,$rule=null,$maxlength="",$sqlcheckoff="")
  {
    if($rule === null) {
      $rule = $this->NameToRule($name);
      if(isset($this->POST['ishtml_cke_'.$name]) && $this->POST['ishtml_cke_'.$name]) {
        $rule = 'nojs';
      }
    }
    
    return $this->Syntax(isset($this->POST[$name])?$this->POST[$name]:'',$rule,$maxlength,$sqlcheckoff);
  }

  public function GetPOSTForForms($name,$rule="",$maxlength="",$sqlcheckoff="")
  {
    return $this->SyntaxForForms($this->POST[$name],$rule,$maxlength,$sqlcheckoff);
  }

  public function CleanString($string, $rule='nohtml',$sqlcheckoff='')
  {
    return $this->Syntax($string, $rule, '', $sqlcheckoff);
  }

  public function xss_clean($data)
  {
    return $this->app->stringcleaner->xss_clean($data);
  }

  public function GetPOSTArray()
  {
    if(!empty($this->POST) && count($this->POST)>0)
    {
      foreach($this->POST as $key=>$value)
      {
        $key = $this->GetPOST($key,"alpha+digit+specialchars",20);
        $ret[$key]=$this->GetPOST($value);
      }	
    }
    if(!empty($ret))
    {
      return $ret;
    }

    return null;
  }

  public function GetGETArray()
  {
    if(!empty($this->GET) && count($this->GET)>0)
    {
      foreach($this->GET as $key=>$value)
      {
        $key = $this->GetGET($key,"alpha+digit+specialchars",20);
        $ret[$key]=$this->GetGET($value);
      }	
    }
    if(!empty($ret))
    {
      return $ret;
    }

    return null;
  }

  function stripallslashes($string) {

    while(strstr($string,'\\')) {
      $string = stripslashes($string);
    }
    return $string;
  } 

  public function smartstripslashes($str) {
    $cd1 = substr_count($str, "\"");
    $cd2 = substr_count($str, "\\\"");
    $cs1 = substr_count($str, "'");
    $cs2 = substr_count($str, "\\'");
    $tmp = strtr($str, array("\\\"" => "", "\\'" => ""));
    $cb1 = substr_count($tmp, "\\");
    $cb2 = substr_count($tmp, "\\\\");
    if ($cd1 == $cd2 && $cs1 == $cs2 && $cb1 == 2 * $cb2) {
      return strtr($str, array("\\\"" => "\"", "\\'" => "'", "\\\\" => "\\"));
    }
    return $str;
  }

  public function SyntaxForForms($value,$rule,$maxlength="",$sqlcheckoff="")
  {
    return $value;//mysqli_real_escape_string($this->app->DB->connection,$value);//mysqli_real_escape_string($value);
  }

  // check actual value with given rule
  public function Syntax($value,$rule,$maxlength='',$sqlcheckoff='')
  {
    $striptags = false;
    if(is_array($value))
    {
      if($sqlcheckoff != '')
      {
        return $value;
      }
      foreach($value as $k => $v)
      {
        if(is_array($v))
        {
          $value[$k] = $v;
        }else{
          $v = str_replace("\xef\xbb\xbf","NONBLOCKINGZERO",$v);
          if($striptags){
            $v = $this->stripallslashes($v);
            $v = $this->smartstripslashes($v);
            $v = $this->app->erp->superentities($v);
          }
          $value[$k] = $this->app->DB->real_escape_string($v);
        }
      }
      return $value;
    }
    
    
    $value = str_replace("\xef\xbb\xbf","NONBLOCKINGZERO",$value);

    if($striptags){
      $value = $this->stripallslashes($value);
      $value = $this->smartstripslashes($value);

      $value = $this->app->erp->superentities($value);
    }

    if(!empty($this->app->stringcleaner)) {
      if( $sqlcheckoff == '') {
        return $this->app->DB->real_escape_string($this->app->stringcleaner->CleanString($value, $rule));
      }
      return $this->app->stringcleaner->CleanString($value, $rule);
    }
    
    if($rule === 'nohtml') {
      if( $sqlcheckoff == '') {
        return $this->app->DB->real_escape_string(strip_tags($value));
      }

      return strip_tags($value);

    }
    if($rule === 'nojs') {
      if( $sqlcheckoff == '') {
        return $this->app->DB->real_escape_string($this->xss_clean($value));
      }

      return $this->xss_clean($value);
    }
    
    if($rule=='' && $sqlcheckoff == '') {
      return $this->app->DB->real_escape_string($value);//mysqli_real_escape_string($value);
    }
    if($rule=='' && $sqlcheckoff != '') {
      return $value;
    }

    // build complete regexp

    // check if rule exists

    if($this->GetRegexp($rule)!=''){
      //$v = '/^['.$this->GetRegexp($rule).']+$/';
      $v = $this->GetRegexp($rule);
      if (preg_match_all('/'.$v.'/i', $value, $teffer) ) {
        if($sqlcheckoff==''){
          return $this->app->DB->real_escape_string($value);//mysqli_real_escape_string($value);
        }

        return $value;
      }
      return '';
    }

    echo "<table border=\"1\" width=\"100%\" bgcolor=\"#FFB6C1\">
      <tr><td>Rule <b>$rule</b> doesn't exists!</td></tr></table>";
    return '';
  }


  function RuleCheck($value,$rule)
  {
    $found = false;
    if(!empty($this->app->stringcleaner)) {
      $value_ = $this->app->stringcleaner->RuleCheck($value, $rule, $found);
      if($found) {
        if($value_) {
          return true;
        }
        return false;
      }
    }
    
    $v = $this->GetRegexp($rule);
    if (preg_match_all('/'.$v.'/i', $value, $teffer) ){
      return true;
    }

    return false;
  }

  function AddRule($name,$type,$rule)
  {
    // type: reg = regular expression
    // type: glue ( already exists rules copy to new e.g. number+digit)
    $this->rules[$name]=array('type'=>$type,'rule'=>$rule);
  }

  // get complete regexp by rule name
  function GetRegexp($rule)
  {
    $rules = explode('+',$rule);
    $ret = '';
    foreach($rules as $key) {
      // check if rule is last in glue string
      if($this->rules[$key]['type']==='glue') {
        $subrules = explode('+',$this->rules[$key]['rule']);
        if(count($subrules)>0) {
          foreach($subrules as $subkey) {
            $ret .= $this->GetRegexp($subkey);
          }
        }
      }
      elseif($this->rules[$key]['type']==='reg') {
        $ret .= $this->rules[$key]['rule'];
      }
    }
    if($ret==''){
      $ret = 'none';
    }
    return $ret;
  }

}


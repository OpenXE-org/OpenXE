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

/****************************************************************************
 1. zu jedem Template muss es in einem anderen Template eine Variable geben
	in htmlheader.tpl PAGE fuer page.tpl
****************************************************************************/

/// represent a template (file.tpl)
class ThemeTemplate {
  var $NAME; //Name des Templates
  var $PATH; //PFAD des Templates
  var $parsed; //Zustand 
  var $ORIGINAL; //Parse - Text Vorlage
  var $VARS; //assoziatives Array mit Variablennamen als Index
  var $Elements;
  var $vararraycreated;
  function __construct($_path, $_file){

/*
    $fp=@fopen($_path.$_file,"r");
    if($fp){
      if(filesize($_path.$_file)>0)
				$contents = fread ($fp, filesize($_path.$_file));
      fclose($fp);
    }*/
    $this->vararraycreated = false;
    $this->PATH=$_path;
    $this->NAME=$_file;
    $this->readFile();
  }
  
  function readFile()
  {
    $_path = $this->PATH;
    $_file = $this->NAME;
    $fp=@fopen($_path.$_file,"r");
    if($fp){
      if(filesize($_path.$_file)>0)
				$contents = fread ($fp, filesize($_path.$_file));
      fclose($fp);
    }
    $this->ORIGINAL=isset($contents)?$contents:'';
    //$this->CreateVarArray();
  }


  function CreateVarArray(){
    $this->vararraycreated = true;
    $this->SetVar("",'');
    $pattern = '/((\[[A-Z0-9_]+\]))/';
    preg_match_all($pattern,$this->ORIGINAL,$matches, PREG_OFFSET_CAPTURE);
    if(!$matches)return;
    //TODO Parser umbauen, damit Variablen nicht doppelt genommen werden.
    if(count($matches[0]) > 0)
    {
      $cmatches = count($matches[0]);
      for($i=0;$i<$cmatches;$i++)
      {
        $this->Elements[$i]['before'] = substr($this->ORIGINAL, $i==0?0:($matches[0][$i-1][1] +strlen($matches[0][$i-1][0]) ), $matches[0][$i][1] - ($i==0 ?0 :  ($matches[0][$i-1][1]+strlen($matches[0][$i-1][0])) ) );
        $this->Elements[$i]['el'] = $matches[0][$i][0];
        $this->Elements[$i]['el'] = str_replace('[','',$this->Elements[$i]['el']);
        $this->Elements[$i]['el'] = str_replace(']','',$this->Elements[$i]['el']);
        if($i > 0)$this->Elements[$i-1]['nach'] = $this->Elements[$i]['before'];
      }
      $this->Elements[count($matches[0])-1]['nach'] = substr($this->ORIGINAL, $matches[0][count($matches[0])-1][1]+strlen($matches[0][count($matches[0])-1][0]));
      for($i=0;$i<$cmatches;$i++)
      {
        $matches[0][$i][0] = str_replace('[','',$matches[0][$i][0]);
        $matches[0][$i][0] = str_replace(']','',$matches[0][$i][0]);
        if(!isset($this->VARS[$matches[0][$i][0]]))
        {
          $this->SetVar($matches[0][$i][0],'');
        }
      }
    }
  }

  function Parsed()
  {
    return 1;
    if($this->parsed!=1)
    {
    $fp=@fopen($this->PATH.$this->NAME,"r");
    if($fp){
      $contents = fread ($fp, filesize($this->PATH.$this->FILE));
      fclose($fp);
    }
    $this->ORIGINAL=$contents;
    $this->CreateVarArray();

    }
    $this->parsed=1;
  }

  function AddVar($_var, $_value){ $this->VARS[$_var]=$this->VARS[$_var].$_value; }
  function SetVar($_var, $_value){ $this->VARS[$_var]=$_value; }

}

/*********************** Class PcmsTemplate ****************************/
/// Main Parser for building the html skin (gui) 
class TemplateParser { 
  var $TEMPLATELIST;
  var $VARARRAY;
  var $VARVARARRAY;


  /**
   * TemplateParser constructor.
   *
   * @param Application $app
   */
  public function __construct($app){
		$this->app = $app;
   	$this->TEMPLATELIST=null;
    $this->VARVARARRAY = null;
	}

  public function htmlspecialchars($value)
  {
    $value = str_replace(array('&ouml;','&auml;','&uuml;','&Ouml;','&Auuml;','&Uuml;','&szlig;'),array('ö','ä','ü','Ö','Ä','Ü','ß'),$value);
    $value = htmlspecialchars($value);
    return $value;
  }

  public function addTextLink($_var, $link, $text, $target = null)
  {
    $ret = '<a href="'.$link.'"'.($target?' target="'.$target.'"':'').'>'.$this->htmlspecialchars($text).'</a>';
    if($_var === 'return')
    {
      return $ret;
    }
    return $this->Add($_var, $ret);
  }

  public function addInput($_var, $value, $type = 'text', $class = '', $id = '', $name = '')
  {
    $options = array('type'=>$type);
    if($id != '')
    {
      $options['id'] = $id;
    }
    if($name != '')
    {
      $options['name'] = $name;
    }
    return $this->addButton($_var, $value, null, null, $class, $options);
  }

  public function addMessage($class, $text, $html = false, $_var = 'MESSAGE')
  {
    $ret = '';
    switch($class)
    {
      case 'error':
      case 'warning':
      case 'info':

        break;
      default:
        $class = 'info';
      break;
    }
    if(!$html)
    {
      $text = $this->htmlspecialchars($text);
    }
    $ret .= '<div class="'.$class.'">'.$text.'</div>';
    if($_var === 'return')
    {
      return $ret;
    }
    return $this->app->Tpl->Add($_var, $ret);
  }

  public function addSelect($_var, $id, $name, $options, $selected = '', $class = '')
  {
    $extra = '';
    foreach(array('id','name','class') as $k)
    {
      if($$k != '')
      {
        $extra .= ' '.$k.'="'.str_replace('"','&quot;',$$k).'"';
      }
    }
    $ret = '<select'.$extra.'>';
    if(is_array($options))
    {
      foreach($options as $k => $v)
      {
        $ret .= '<option'.
          ($k == $selected?' selected="selected"':'').' value="'.str_replace('"','&quot;',$k).
          '">'.$this->htmlspecialchars($v).
          '</option>';
      }
    }
    $ret .= '</select>';
    if($_var === 'return')
    {
      return $ret;
    }
    return $this->app->Tpl->Add($_var, $ret);
  }

  public function addButton($_var, $text, $link = null, $target = null, $class = '', $options = null)
  {
    $ret = '';
    $type = 'button';
    $extra = '';
    $extraa = array();
    if(isset($options['type'])) {
      $type = $options['type'];
    }
    if($link) {
      $ret .= '<a href="'.$link.'"'.($target?' target="'.$target.'"':'').'>';
    }
    if(is_array($options)) {
      foreach($options as $k => $v) {
        switch($k) {
          case 'name':
          case 'id':
            $extraa[] = $k.'="'.str_replace('"','&quot;',(String)$v).'"';
          break;
          default:
            if(strpos($k,'data-') === 0) {
              $extraa[] = $k.'="'.str_replace('"','&quot;',(String)$v).'"';
            }
            break;
        }
      }
    }
    if(count($extraa) > 0){
      $extra = ' ' . implode(' ', $extraa);
    }

    $ret .= '<input type="'.$type.'" value="'.str_replace('"','&quot;',$text).'" '.
      ($class!=''?' class="'.str_replace('"','',$class).'"':'').
      $extra.' />';
    if($link) {
      $ret .= '</a>';
    }
    if($_var === 'return') {
      return $ret;
    }
    $this->app->Tpl->Add($_var, $ret);

    return '';
  }

  function GetVars($tplfile)
  {
    $fp=@fopen($tplfile,"r");
    if($fp){
      $contents = fread ($fp, filesize($tplfile));
      fclose($fp);
    }
    $suchmuster = '/[\[][A-Z_]+[\]]/';
    preg_match_all($suchmuster, $contents, $treffer);
    return $treffer[0];
  }

  function ResetParser()
  {
    unset($this->TEMPLATELIST);
    unset($this->VARARRAY);
  }

  function ReadTemplatesFromPath($_path){

    $this->loadUebersetzung();

    $this->addPath($_path);
    $directory=opendir($_path);
    $i = 1;
    while ($file=readdir($directory)){
      if(strstr($file, '.tpl')){ 
        $i++;
        $this->TEMPLATELIST[$file] = new ThemeTemplate($_path,$file);	
      }
    }
    closedir($directory);
  }

  private function loadUebersetzung(){
  }

  protected function addPath($_path)
  {
    $rpos = strrpos($_path, '/www/');
    if($rpos !== false)
    {
      $this->pathes[] = substr($_path, $rpos);
    }else{
      $this->pathes[] = $_path;
    }
  }

  protected function pathLoaded($_path)
  {
    if(!$this->pathes)return false;
    $rpos = strrpos($_path, '/www/');
    if($rpos !== false)
    {
      $_path = substr($_path, $rpos);
    }
    if(in_array($_path, $this->pathes))return true;
    return false;
  }

  function LoadPathes()
  {
    $pathes = array(dirname(dirname(__DIR__))."/www/widgets/templates/_gen/",
      dirname(dirname(__DIR__))."/www/widgets/templates/",
      dirname(dirname(__DIR__))."/www/themes/".$this->app->Conf->WFconf['defaulttheme']."/templates/",
      dirname(dirname(__DIR__))."/www/pages/content/_gen/",
      dirname(dirname(__DIR__))."/www/pages/content/"
    );
    foreach($pathes as $path)
    {
      if(!$this->pathLoaded($path))$this->ReadTemplatesFromPath($path);
    }
  }

  function CreateVarArray(){
    foreach($this->TEMPLATELIST as $template=>$templatename){
      if(count($this->TEMPLATELIST[$template]->VARS) > 0){
        foreach($this->TEMPLATELIST[$template]->VARS as $key=>$value){
          $this->VARARRAY[$key]=$value;
        }
      }
    }
  }

  function ShowVariables(){
    foreach($this->VARARRAY as $key=>$value)
    echo "<b>$key =></b>".htmlspecialchars($value)."<br>";
  }

  function ParseVariables($text){
    foreach($this->VARARRAY as $key=>$value)
    {
      if($key=!"")
        $text = str_replace('['.$key.']',$value,$text);
    }
    // fill empty vars
    return $text;
  }

  function ShowTemplates(){
    foreach ($this->TEMPLATELIST as $key=> $value){
      foreach ($value as $key1=> $text){
        if(!is_array($text))echo "$key ".htmlspecialchars($text)."<br>";
        if(is_array($text))foreach($text as $key2=>$value2) echo $key2." ".$value2;
      }
      echo '<br><br>';
    }
  }
  function SetText($_var, $_value)
  {
    $this->VARARRAY[$_var]= $this->htmlspecialchars($_value);
  }

  function AddText($_var,$_value, $variable = false){
    $this->VARARRAY[$_var]=isset($this->VARARRAY[$_var])?$this->VARARRAY[$_var].$this->htmlspecialchars($_value):$this->htmlspecialchars($_value);
    if($variable)
      $this->VARVARARRAY[$_var] = $variable;
  }

  function Set($_var,$_value, $variable = false){
    $this->VARARRAY[$_var]=$_value;
    if($variable)
      $this->VARVARARRAY[$_var] = $variable;
  }

  function Add($_var,$_value, $variable = false){  
    $this->VARARRAY[$_var]=isset($this->VARARRAY[$_var])?$this->VARARRAY[$_var].$_value:$_value;
    if($variable)
      $this->VARVARARRAY[$_var] = $variable;
  }
  
  function Get($_var){  
    return $this->VARARRAY[$_var]." ";
  }
  
  function Output($_template)
  {
    echo $this->app->erp->ClearDataBeforeOutput($this->ParseTranslation($this->Parse("",$_template,1)));
  }


  function OutputAsString($_template)
  {
    return $this->app->erp->ClearDataBeforeOutput($this->Parse("",$_template,1));   
	}

  
  function pruefeuebersetzung($text, $_type = 'page', $element = null, $withspan = true)
  {
    if(is_null($this->uebersetzungmodulvorhanden))
    {
      $this->uebersetzungmodulvorhanden = true;
      if(!$this->app->erp->ModulVorhanden('wawision_uebersetzung'))$this->uebersetzungmodulvorhanden = false;
    }
    if(!$this->uebersetzungmodulvorhanden)return $text;
    if(is_array($text))
    {
      foreach($text as $k => $v)
      {
        $text[$k] = $this->pruefeuebersetzung($v, $_type, $element, $withspan);
      }
      return $text;
    }
    if($text === '')return '';
    if(is_null($element) && isset($this->app->Secure) && method_exists($this->app->Secure, 'GetGET'))$element = $this->app->Secure->GetGET('module');
    $start = '';
    $end = '';
    
    return $start.$text.$end;
  }
  
  
  function ParseTranslation($text)
  {
    $pattern = '/((\{\|)(.*?)(\|\}))/s';
    $ok = preg_match_all($pattern,$text,$matches, PREG_OFFSET_CAPTURE);
    if(!$ok)return $text;
    //TODO Parser umbauen, damit Variablen nicht doppelt genommen werden.
    if(count($matches[0]) > 0)
    {
      $cmatches = count($matches[0]);
      for($i=0;$i<$cmatches;$i++)
      {
        $Elements[$i]['before'] = substr($text, $i==0?0:($matches[0][$i-1][1] +strlen($matches[0][$i-1][0]) ), $matches[0][$i][1] - ($i==0 ?0 :  ($matches[0][$i-1][1]+strlen($matches[0][$i-1][0])) ) );
        $Elements[$i]['el'] = $matches[0][$i][0];
        $Elements[$i]['el'] = str_replace('{|','',$Elements[$i]['el']);
        $Elements[$i]['el'] = str_replace('|}','',$Elements[$i]['el']);
        if($i > 0)$Elements[$i-1]['nach'] = $Elements[$i]['before'];
      }
      $Elements[count($matches[0])-1]['nach'] = substr($text, $matches[0][count($matches[0])-1][1]+strlen($matches[0][count($matches[0])-1][0]));
    }else return $text;
    $cmatches = count($matches[0]);
    for($i=0;$i<$cmatches;$i++)
    {
      $matches[0][$i][0] = str_replace('{|','',$matches[0][$i][0]);
      $matches[0][$i][0] = str_replace('|}','',$matches[0][$i][0]);
    }
    $ret = "";
    if($Elements){
      foreach($Elements as $k => $v)
      {
        if(isset($v['before']) && $k == 0)$ret .= $v['before'];
        
        if(isset($v['before']) && strlen((String)$v['before']) > 0 && substr($v['before'],-1) == '"')
        {
          
          $pos1 = strripos($v['before'],'input');
          $pos2 = strripos($v['before'],'<');
          
          if($pos2 !== false && $pos1 !== false && $pos2 < $pos1)
          {
            $ret .=  $this->pruefeuebersetzung($v['el'],'page',null, '****');
          }else{
            $ret .=  $this->pruefeuebersetzung($v['el'],'page',null, false);
          }
        }else{
          $ret .=  $this->pruefeuebersetzung($v['el']);
        }
        if(isset($v['nach']))$ret .= $v['nach'];
      }
    }
    return $ret;
  }

  function Parse($_var, $_template,$return=0){
    if(!isset($this->TEMPLATELIST[$_template]))
    {
      $this->LoadPathes();
    }
    // check if custom template exists_template
    $checkcustom = str_replace('.tpl','_custom.tpl',$_template);
    if(isset($this->TEMPLATELIST[$checkcustom])) $_template = $checkcustom;

    //$this->AjaxParse();
    if($_var == 'PAGE')$this->app->erp->ParseMenu();
    $this->ParseVarVars();
    if($_template!=""){
      if(isset($this->TEMPLATELIST[$_template]) && !($this->TEMPLATELIST[$_template]->vararraycreated))
      {
        $this->TEMPLATELIST[$_template]->CreateVarArray();
      }
      
      //alle template variablen aufuellen mit den werten aus VARARRAY 
      if(isset($this->TEMPLATELIST[$_template]) && isset($this->TEMPLATELIST[$_template]->VARS) && count($this->TEMPLATELIST[$_template]->VARS)>0){ 
        foreach ($this->TEMPLATELIST[$_template]->VARS as $key=> $value){
          $this->TEMPLATELIST[$_template]->SetVar($key,isset($this->VARARRAY[$key])?$this->VARARRAY[$key]:'');
        }
      
        //ORIGINAL auffuellen
        $tmptpl = $this->TEMPLATELIST[$_template]->ORIGINAL;
        foreach ($this->TEMPLATELIST[$_template]->VARS as $key=>$value){
          if(!is_numeric($key) && $key!="")
          $tmptpl = str_replace("[".$key."]",$value, $tmptpl);	
        }
      } else $tmptpl = '';
      //aufgefuelltes ORIGINAL in $t_var add($_var,ORIGINAL)
      if($return==1)
        return $tmptpl;
      else
        $this->Add($_var,$tmptpl);
    }
  }

  function AddAndParse($_var, $_value, $_varparse, $_templateparse){
    $this->Set($_var, $_value);
    $this->Parse($_varparse,$_templateparse);
  }
  
  function ParseVarVars()
  {
    $pattern = '/((\[[A-Z0-9_]+\]))/';
    if(!empty($this->VARVARARRAY) && is_array($this->VARVARARRAY))
    {
      foreach($this->VARVARARRAY as $k => $el)
      {
        preg_match_all($pattern,$this->VARARRAY[$k],$matches, PREG_OFFSET_CAPTURE);

        $cmatches = $matches?count($matches[0]):0;
        for($i=0;$i<$cmatches;$i++)
        {
          $matches[0][$i][0] = str_replace('[','',$matches[0][$i][0]);
          $matches[0][$i][0] = str_replace(']','',$matches[0][$i][0]);
          if(isset($this->VARARRAY[$matches[0][$i][0]]))
          {
            $this->VARARRAY[$k] = str_replace('['.$matches[0][$i][0].']',$this->VARARRAY[$matches[0][$i][0]],$this->VARARRAY[$k]);            
          }
        }
        unset($matches);
      }
    }
  }

  function FinalParse($_template){
    $printtype = '';
    if(isset($this->TEMPLATELIST[substr($_template,0,strlen($_template)-4).'_custom.tpl']))
    {
      $_template = substr($_template,0,strlen($_template)-4).'_custom.tpl';
    } 
    $this->app->erp->ParseMenu();
    $this->ParseVarVars();
    if(isset($this->TEMPLATELIST[$_template]) && !($this->TEMPLATELIST[$_template]->vararraycreated))
    {
      $this->TEMPLATELIST[$_template]->CreateVarArray();
    }
		$print = $this->app->Secure->GetGET("print");
		$printcontent = $this->app->Secure->GetGET("printcontent");

		if($printcontent=="") $printcontent="TAB1";
		if($print=="true") {

			switch($printtype)
			{
				default:
				$out = str_replace("[PRINT]",$this->VARARRAY[$printcontent],$this->TEMPLATELIST['print.tpl']->ORIGINAL);
				echo $this->ParseTranslation($out);
			}
				exit;
		}     

    if($_template!="" && isset($this->TEMPLATELIST)){
      //alle template variablen aufuellen mit den werten aus VARARRAY
      if(count($this->TEMPLATELIST[$_template]->VARS)>0){ 
        foreach ($this->TEMPLATELIST[$_template]->VARS as $key=> $value)
        {
          $this->TEMPLATELIST[$_template]->SetVar($key,(isset($this->VARARRAY[$key])?$this->VARARRAY[$key]:''));
        }
      }
    }
    //ORIGINAL auffuellen
    
    
    $new = false;
    if($new)
    {
      //macht Noch Probleme 
      $tmptpl = '';
      if(!empty($this->TEMPLATELIST[$_template]->Elements))
      {
        foreach($this->TEMPLATELIST[$_template]->Elements as $k)
        {
          $tmptpl .= $this->ParseTranslation($k['before']);
          if(!empty($this->TEMPLATELIST[$_template]->VARS[$k['el']]))
          {
            $tmptpl .= $this->TEMPLATELIST[$_template]->VARS[$k['el']];
          }
        }
        $tmptpl .= $this->ParseTranslation($this->TEMPLATELIST[$_template]->Elements[count($this->TEMPLATELIST[$_template]->Elements)-1]['nach']);
      }else $tmptpl = $this->TEMPLATELIST[$_template]->ORIGINAL;
    }else 
    {
      $tmptpl = isset($this->TEMPLATELIST[$_template]->ORIGINAL)?$this->TEMPLATELIST[$_template]->ORIGINAL:'';
      if(isset($this->TEMPLATELIST[$_template]->VARS) && count($this->TEMPLATELIST[$_template]->VARS)>0){ 
        foreach ($this->TEMPLATELIST[$_template]->VARS as $key=>$value)
        {
          if($key!="")
          $tmptpl = str_replace("[".$key."]",$value, $tmptpl);
        }
      }
      
      if(count($this->VARARRAY)>0)
        foreach($this->VARARRAY as $key=>$value)
        {
          if($key!="")
          $tmptpl = str_replace('['.$key.']',$value,$tmptpl);
        }
    }

    // In Auftrags-Positionen-IFrame: Leere Form-Actions nicht durch # ersetzen; ansonsten springt das IFrame im Chrome
    $replaceEmptyFormAction = !$this->IsVorgangPositionenIframe();

    $tmptpl = $this->ParseTranslation($this->app->erp->ClearDataBeforeOutput($tmptpl, $replaceEmptyFormAction));
    if(isset($this->edittranslation) && $this->edittranslation)$tmptpl = $this->FormatTranslation($tmptpl);
    return $tmptpl;
  }

  function FormatTranslation($text)
  {
    $start = '<span class="edittranslation">';
    //$end = '</span>';
    $texta = explode($start, $text);
    //$anz = count($texta);
    $script = false;
    $textres = '';
    foreach($texta as $k => $v)
    {
      $scriptpos = strripos($v, '<script');
      $scriptendpos = strripos($v, '</script>');
      if($scriptpos !== false && $scriptendpos !== false)
      {
        if($scriptendpos > $scriptpos)
        {
          $script = false;
        }else{
          $script = true;
        }
      }elseif($scriptpos !== false)
      {
        $script = true;
      }elseif($scriptendpos !== false)
      {
        $script = false;
      }else{
        //Keine Aenderung
      }
      
      if($k > 0)
      {
        $pipe1 = (int)strpos($v, '|');
        $pipe2 = (int)strpos($v, '|', (int)$pipe1+1);
        $pipe3 = (int)strpos($v, '|', (int)$pipe2+1);
        $pipe4 = (int)strpos($v, '|', (int)$pipe3+1);
        $spos = 0;
        $_text = '';
        $_type = '';
        $_elem = '';
        if($pipe4 > $pipe3 && $pipe3 > $pipe2 && $pipe2 > $pipe1)
        {
          $_text = substr($v,$pipe1+1, $pipe2 - $pipe1 - 1);
          $_type = substr($v,$pipe2+1, $pipe3 - $pipe2 - 1);
          $_elem = substr($v,$pipe3+1, $pipe4 - $pipe3 - 1);
          $spos = $pipe4 + 1;
        }
        
        $endespan = strpos($v, '</span>');
        $erlaubt = true;
        $starttag = strpos($v, '<', $endespan+6);
        $startquote = strpos($v, '"', $endespan+6);
        if($starttag && strtolower(substr($v,$starttag,5)) != '</td>' && strtolower(substr($v,$starttag,5)) != '</th>' && strtolower(substr($v,$starttag,4)) != '</a>' && strtolower(substr($v,$starttag,6)) != '</div>' && strtolower(substr($v,$starttag,4)) != '</i>' && strtolower(substr($v,$starttag,9)) != '</legend>' && strtolower(substr($v,$starttag,8)) != '</label>')
        {
          $erlaubt = false;
        }
        if(substr($v,$endespan+8,1) == '"' || substr($v,$endespan+7,1) == '"')
        {
          $erlaubt = false;
        }
        if($endespan !== false && $starttag !== false && $startquote !== false && $startquote < $starttag)
        {
          $erlaubt = false;
        }
        
        if($script || !$erlaubt)
        {
          if(!$script)
          {
            $substr = $texta[$k-1];
            
            $pos1 = strripos($substr, 'input');
            $pos2 = strrpos($substr, '<');
            if($pos1 !== false && $pos2 !== false && $pos2 < $pos1)
            {
              $textres .= '****'.substr($v,$spos, $endespan - $spos).substr($v,$endespan+7);
            }else{
              $textres .= substr($v,$spos, $endespan - $spos).substr($v,$endespan+7);
            }
          }else{
            $textres .= substr($v,$spos, $endespan - $spos).substr($v,$endespan+7);
          }
        }else{
          $textres .= $start.substr($v,$spos, $endespan - $spos).'</span><span><input type="hidden" class="wawision_uebersetzung_text" value="'.base64_encode($_text).'" /><input type="hidden" class="wawision_uebersetzung_type" value="'.base64_encode($_type).'" /><input type="hidden" class="wawision_uebersetzung_elem" value="'.base64_encode($_elem).'" /></span>'.substr($v,$endespan+7);
        }
      }else{
        $textres .= $v;
      }
    }
    return $textres;
  }
  
  function AjaxParse()
  {

    foreach($this->TEMPLATELIST as $key=>$value)
    {
      foreach ($this->TEMPLATELIST[$key]->VARS as $var=>$tmp)
      {
        if(strstr($var,"AJAX"))
        {
          //$this->Set(AJAX_SELECT_PROJEKT,"Hallo");
          //$this->VARARRAY[$var]="XVZ";
          //print_r($this->VARARRAY);
        }
      }
    }
  }


  function KeywordParse()
  {

    foreach($this->TEMPLATELIST as $key=>$value)
    {
      foreach ($this->TEMPLATELIST[$key]->VARS as $var=>$tmp)
      if(strstr($var,"AJAX"))
      {
				echo $var;
      }
    }
  }

  function IsVorgangPositionenIframe()
  {
    $vorgaenge = ['anfrage','angebot','arbeitsnachweis','auftrag','bestellung','gutschrift','kalkulation','lieferschein','preisanfrage','produktion','proformarechnung','rechnung','reisekosten','verbindlichkeit'];

    $isPopup = ($this->app->BuildNavigation !== true);
    $isVorgangModule = in_array($this->app->Secure->GetGET('module'), $vorgaenge);
    $isPositionenAction = (strpos($this->app->Secure->GetGET('action'), 'position') !== false);

    return ($isPopup && $isVorgangModule && $isPositionenAction) ? true : false;
  }

} 

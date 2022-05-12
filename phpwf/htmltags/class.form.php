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

/// represent a HTML Form structure
class HTMLForm
{
  var $action;
  var $method;
  var $name;
  var $id;
 
  var $FieldList;
 
  function __construct($action="",$method="post",$name="",$id="")
  {
    $this->action=$action;
    $this->name=$name;
    $this->method=$method;
    $this->id=$id;
  }

  function Set($value)
  {
  }

  function Get()
  {

  }

  function GetClose()
  {
  }
}



class HTMLTextarea
{
  var $name;
  var $rows;
  var $value;
  var $cols;
  var $id="";
  var $readonly="";
  var $disabled="";
  var $class;

  function __construct($name,$rows,$cols,$defvalue="",$id="",$readonly="",$disabled="",$class="")
  {
    $this->name = $name;
    $this->rows = $rows;
    $this->cols = $cols;
    $this->class = $class;
    $this->value = $defvalue;
    $this->id = $id;

    if($id=="")
      $this->id = $name;

    $this->readonly = $readonly;
    $this->disabled = $disabled;
  }

  function Get()
  {
    // TEMP ACHTUNG HIER IST MIST!!!
    $value = $this->value;
/*
    if(!defined('WFHTMLTextareabr') || !WFHTMLTextareabr)$value =   preg_replace('/<br\\s*?\/??>/i', "\n", $value);
 */   
 //   $value = str_replace("\\r\\n","\n",$value);
    
    $html = "<textarea rows=\"{$this->rows}\" id=\"{$this->id}\" class=\"{$this->class}\"
       name=\"{$this->name}\" cols=\"{$this->cols}\" 
       {$this->readonly} {$this->disabled} [COMMONREADONLYINPUT]>$value</textarea>";
    return $html;
  }
  
  function GetClose()
  {
  }
}


/// fuer Datenfelder die mit in die Datenbank o.ae. kommen sollen, aber nicht durch den 
/// user in irgendeiner art und weise gesehen und manipuliert werden koennen

class BlindField
{
  var $name;
  var $value;

  function __construct($name,$value)
  {
    $this->name = $name;
    $this->value = $value;
  }
  function Get(){}
  function GetClose(){}
}


class HTMLInput
{
  var $name;
  var $type;
  var $value;
  var $dbvalue;
  var $checkvalue;
  var $onchange;
  var $onclick;
  var $defvalue;
  var $size;
  var $maxlength;
  var $tabindex;
  var $id="";
  var $readonly="";
  var $disabled="";
  var $placeholder="";
  var $class;
  var $checked;

  function __construct($name,$type,$value,$size="",$maxlength="",$id="",$defvalue="",$checked="",$readonly="",$disabled="",$class="",$onclick="",$tabindex="",$placeholder="")
  {
    $this->name = $name;
    $this->type = $type;
    $this->value = $value;
    $this->size = $size;
    $this->maxlength = $maxlength;
    $this->id = $id;
    $this->readonly = $readonly;
    $this->disabled = $disabled;
    $this->class=$class;
    $this->checked=$checked;
    $this->tabindex=$tabindex;
    $this->placeholder=$placeholder;
    $this->defvalue=$defvalue; // if value is empty use this
    $this->onclick=$onclick; 

  }

  function Get()
  {
    if($this->id=="") $this->id = $this->name;

    switch($this->type)
    {
      case "text":
	$html = "<input type=\"{$this->type}\" id=\"{$this->id}\"  class=\"{$this->class}\" tabindex=\"{$this->tabindex}\"
          name=\"{$this->name}\"  value=\"".preg_replace("/\"/","&quot;",$this->value)."\"  size=\"{$this->size}\" placeholder=\"{$this->placeholder}\"
	  maxlength=\"{$this->maxlength}\" {$this->readonly} {$this->disabled} [COMMONREADONLYINPUT]>";
      break;
      case "password":
	$html = "<input type=\"{$this->type}\" id=\"{$this->id}\"  class=\"{$this->class}\" tabindex=\"{$this->tabindex}\"
	  name=\"{$this->name}\"  value=\"{$this->value}\"  size=\"{$this->size}\"
	  maxlength=\"{$this->maxlength}\" {$this->readonly} {$this->disabled} [COMMONREADONLYINPUT]>";
      break;
      case "checkbox":
	  $html = "<input type=\"{$this->type}\" id=\"{$this->id}\"  class=\"{$this->class}\" tabindex=\"{$this->tabindex}\" 
	  name=\"{$this->name}\"  value=\"{$this->value}\" {$this->checked} onchange=\"{$this->onchange}\" onclick=\"{$this->onclick}\"
	  {$this->readonly} {$this->disabled} [COMMONREADONLYINPUT]>";
      break;
      case "radio":

        if($this->value==$this->defvalue) $this->checked="checked";

        $tmpname = str_replace('_'.$this->defvalue,'',$this->name);

	$html = "<input type=\"{$this->type}\" id=\"{$this->id}\"  class=\"{$this->class}\" tabindex=\"{$this->tabindex}\"
	name=\"{$tmpname}\"  value=\"{$this->defvalue}\" {$this->checked} onchange=\"{$this->onchange}\"
	  {$this->readonly} {$this->disabled} [COMMONREADONLYINPUT]>";
      break;
      case "submit":
	$html = "<input type=\"{$this->type}\" id=\"{$this->id}\"  class=\"{$this->class}\"
	  name=\"{$this->name}\"  value=\"{$this->value}\" 
	  {$this->readonly} {$this->disabled}>";
      break;
      case "hidden":
	$html = "<input type=\"{$this->type}\" id=\"{$this->id}\"  class=\"{$this->class}\"
	  name=\"{$this->name}\"  value=\"{$this->value}\"  size=\"{$this->size}\"
	  maxlength=\"{$this->maxlength}\" {$this->readonly} {$this->disabled}>";
      break;
    }
	
    return $html;
  }
  
  function GetClose()
  {
  }
}



class HTMLCheckbox extends HTMLInput
{
  function __construct($name,$value,$defvalue,$checkvalue="",$onclick="",$tabindex="")
  {
    
    if($checkvalue!="")
      $this->checkvalue=$checkvalue;
    else
      $this->checkvalue=$value;

    $this->name = $name;
    $this->type = "checkbox";
    $this->checkradiovalue = isset($okvalue)?$okvalue:null;
    $this->defvalue = $defvalue;
    $this->value = $value;
    $this->onclick= $onclick;
    $this->tabindex= $tabindex;
    $this->orgvalue = $value;
  }


  function Get()
  {
    if(($this->value=="" && $this->defvalue==$this->checkvalue)) {
    }
    if($this->checkvalue==$this->value) {
      $this->checked="checked";
    }
    if($this->value=="" && $this->defvalue!=$this->checkvalue)
      $this->checked="";

    $this->value = $this->checkvalue;
    //$this->value=1;
    return parent::Get();
  }

  function GetClose()
  {
  }

};

class HTMLSelect
{
  var $name;
  var $size;
  var $id;
  var $readonly;
  var $disabled;

  var $options;
  var $onchange;
  var $selected;
  var $tabindex;

  var $class;

  function __construct($name,$size,$id="",$readonly=false,$disabled=false,$tabindex="")
  {
    $this->name=$name;
    $this->size=$size;
    $this->id=$id;
    $this->readonly=$readonly;
    $this->disabled=$disabled;
    $this->tabindex=$tabindex;
    $this->class="";

    if($id=="")
      $this->id = $name;
  }

  function AddOption($option,$value)
  {
    $this->options[] = array($option,$value);
  }
  
  function AddOptionsDimensionalArray($values)
  {
    foreach($values as $key=>$value)
    {
	$this->options[] = array($value[wert],$value[schluessel]);
    }
  }


  function AddOptionsAsocSimpleArray($values)
  {
    foreach($values as $key=>$value)
      $this->options[] = array($value,$key);
  }

  function AddOptionsSimpleArray($values)
  {
    if(is_array($values))
    {
      foreach($values as $key=>$value)
      {
      	if(!is_numeric($key))
          $this->options[] = array($value,$key);
      	else
          $this->options[] = array($value,$value);
      }
    }
  }
 
  function AddOptions($values)
  {
    $number=0;
    if(count($values)>0)
    {
      foreach($values as $key=>$row)
	foreach($row as $value)
	{
	  if($number==0){
	    $option=$value;
	    $number=1;
	  }
	  else {
	    $this->options[] = array($option,$value);
	    $number=0;
	    $option="";
	  }
	}
    }

  }
  
  function Get()
  {
    $html = "<select name=\"{$this->name}\" size=\"{$this->size}\" tabindex=\"{$this->tabindex}\"
      id=\"{$this->id}\"  class=\"{$this->class}\" onchange=\"{$this->onchange}\" [COMMONREADONLYSELECT]>";

    if($this->options && count($this->options)>0)
    {
      foreach($this->options as $key=>$value)
      {
	if($this->value==$value[1])
	  $html .="<option value=\"{$value[1]}\" selected>{$value[0]}</option>";
	else
	  $html .="<option value=\"{$value[1]}\">{$value[0]}</option>";
      }

    }
    $html .="</select>";
    return $html;
  }

  function GetClose()
  {
  }

}


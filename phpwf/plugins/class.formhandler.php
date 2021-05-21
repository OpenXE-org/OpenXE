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


class FormHandlerField
{
  var $parsetarget;
  var $htmlobject;

  var $identifier;

  var $delmsg;
  var $delmsgcol;

  var $mandatoryok=true;
  var $mandatorymsg;
  var $mandatoryrule;
  var $mandatorycheck=false;
  var $mandatoryparse='MSG';

  var $htmlformat;
  var $dbformat;

  var $value;

  /**
   * FormHandlerField constructor.
   *
   * @param $identifier
   * @param $htmlobject
   */
  function __construct($identifier,$htmlobject)
  {
    $this->htmlobject = $htmlobject;
    $this->identifier = $identifier;
  }

  /**
   * @param $parsetarget
   */
  function ParseTarget($parsetarget)
  {
    $this->parsetarget = $parsetarget;
  }
}

class HTMLListEntry { var $htmlobject; var $parsetarget; var $dbvalue; var $htmlvalue;}
class MandatoryEntry { var $rule; var $message; var $messageparsetarget; }
class CallbackEntry { var $function;}

class FormActionHandler
{
  private $app;	//ok
  private $template;  //ok
  private $name;  //ok
  private $table; //ok
  private $submitname;
  public  $locafterexe;
  private $parsetarget;	//ok
  private $pkname;  //ok
  private $pkvalue;
  private $specialaction;
  private $event;

  private $replacecallbacks;

  private $formaction;
  private $values;

  public $HTMLList;
  private $MandatoryList;
  private $CallbackList;

  function __construct(&$app,$name)
  {
    $this->app = $app;
    $this->name = base64_encode($name);
    $this->HTMLList = array();
    $this->MandatoryList = array();
    $this->CallbackList = array();
  }

  function UseTable($table,$pkname="id")
  {
    $this->table = $table;
    $this->pkname = $pkname;
  }

  function UseTemplate($template,$parsetarget)
  {
    $this->template = $template;
    $this->parsetarget = $parsetarget;
  }

  function NewField($htmlobject,$parsetarget="")
  {
    $entry = new HTMLListEntry();

    if($parsetarget=="")
      $parsetarget = strtoupper($htmlobject->name);

    $entry->htmlobject=$htmlobject;
    if(get_class($htmlobject)!="BlindField")
    {
      if($this->app->Secure->GetPOST('ishtml_cke_'.$htmlobject->name))
      {
        $entry->htmlvalue = $this->app->Secure->GetPOST($htmlobject->name, "nojs");
      }else{
        $entry->htmlvalue = $this->app->Secure->GetPOST($htmlobject->name);
      }
    }
    else
      $entry->htmlvalue = $htmlobject->value;

    $entry->parsetarget=$parsetarget;

    $this->HTMLList[$htmlobject->name]=$entry; 
  }

  /* alt  
     function AddCallbackFromDB($field, $function)
     {
     $this->CallbackFromList[$field]=$function; 
     }

     function AddCallbackToDB($field, $function)
     {
     $this->CallbackToList[$field]=$function; 
     }
   */

  function ReplaceFunction($fieldname,&$ref,$callback)
  {
    $this->replacecallbacks[$fieldname] = array($fieldname,&$ref,$callback);
  }



  function AddMandatory($field, $rule,$message="Fehler",$msgparsetarget="MSG")
  {
    $entry = new MandatoryEntry();

    $entry->rule  = $rule;
    $entry->message = $message;
    $entry->msgparsetarget = $msgparsetarget;

    $this->MandatoryList[$field]=$entry; 
  }


  function SpecialActionAfterExecute($event,$href)
  {
    $this->specialaction = 1;
    $this->event = $event;
    $this->href = $href;
  }

  function SpecialActionAfterExecuteBad($event,$href)
  {
    $this->bad_specialaction = 1;
    $this->bad_event = $event;
    $this->bad_href = $href;
  }



  function ActionAfterExecute()
  {
    $module = $this->app->Secure->GetGET("module");
    $action = $this->app->Secure->GetGET("action");
    $id = $this->app->Secure->GetGET("id");
    if($this->specialaction==1)
    {
      if($this->event == "close_refresh")
      {

        echo "<script>
          parent.document.location.href=\"{$this->href}\"; 
          </script>";
        exit;
      }
      else {
        header("Location: ".$this->href);
        exit;
      }
    }

    // wenn location after execute 
    if($action=="create") { 
      $msg = base64_encode("<div class=\"error2\">Die Daten wurden gespeichert!</div>");
      header("Location: index.php?module=$module&action=edit&id=".$this->pkvalue."&msg=".$msg);
      exit;
    } 
    else if($action=="edit") 
    { 
      $msg = base64_encode("<div class=\"error2\">Die Daten wurden gespeichert!</div>");
      header("Location: index.php?module=$module&action=edit&id=".$id."&msg=".$msg);
      exit;
    } 

    $this->PrintForm();
  }

  function FormhandlerEvent()
  {
    // add formhandler action
    $this->app->Tpl->Set('FORMHANDLEREVENT',
        "<input type=\"hidden\" name=\"phpwf_formhandlerevent_{$this->name}\" value=\"go\">");

    if($this->app->Secure->GetPOST("phpwf_formhandlerevent_{$this->name}")=="go") {
      return true; 
    } else { 
      // bei false wird das originale formualr angezeigt (also das erste mal)
      return false;
    }
  } 

  function PrintForm()
  {
    foreach($this->HTMLList as $name=>$entry){
      $fieldname = $entry->htmlobject->name;
      $arr = isset($this->replacecallbacks[$fieldname])?$this->replacecallbacks[$fieldname]:null;
      if($arr && $arr[0]==$entry->htmlobject->name)
      {	
        //aufruf der replacecallback funktion
        $_fnname = $arr[2];
        $entry->htmlobject->value = $arr[1]->$_fnname(0,$entry->htmlobject->value,0);
      }

      $entry->htmlobject->value = str_replace("\\\\\\\"",'"',isset($entry->htmlobject->value)?$entry->htmlobject->value:'');
      $entry->htmlobject->value = str_replace("NONBLOCKINGZERO","&#65279;",$entry->htmlobject->value);
      $entry->htmlobject->value = str_replace('"','&quot;',$entry->htmlobject->value);

      $this->app->Tpl->Set($entry->parsetarget,$entry->htmlobject->Get().$entry->htmlobject->GetClose());
    }
    // anzeige
    $this->app->Tpl->Parse($this->parsetarget,$this->template); 
  }


  function CallbackAndMandatorycheck($returnonly = false)
  {
    // check mandatory 
    // gehe alle html felder durch und pruefe bei jedem ob es eine regel gibt
    $errors=false;
    foreach($this->HTMLList as $name=>$entry){
      $rule = $this->MandatoryList[$name]->rule;
      $msg = $this->MandatoryList[$name]->message;
      $parsetarget = $this->MandatoryList[$name]->msgparsetarget;

      $value = $this->HTMLList[$name]->htmlvalue;

      // wert wieder ins htmlfeld legen
      if(!$returnonly)$this->HTMLList[$name]->htmlobject->value = $entry->htmlvalue;

      if($rule!="") {
        if(!$this->app->Secure->RuleCheck($value,$rule)) {
          $errors = true;
          // wert entspricht nicht der regel
          if(!$returnonly)$this->app->Tpl->Add($parsetarget,"<font color=\"red\">&nbsp;$msg</font>");
        }
      }
    }

    // wenn daten im falschen format daliegen, dann return false
    if($errors)
      return false;

    // sonst callback funktionen aufrufen um daten richtig zu formatieren
    // htmlobject->value mit FromDB und value mit ToDB
    //and callback functions

    // TODOOOOOOOOO ACHTUNG DAS HIER IST 100% FALSCH!!!
    foreach($this->HTMLList as $name=>$entry){
      // werte in dbvalue felder richtig setzen
      if(!$returnonly)$this->HTMLList[$name]->dbvalue=$entry->htmlvalue;
      //$entry->
    }
    // TODOOOOOOOOO
    return true;
  }

  function Search()
  {

    $this->PrintForm();
  }


  function Edit()
  {
    if($this->FormhandlerEvent()) {
      // wenn es fehlerfrei ist dann irgendwo hin sonst wieder das formular anzeigen
      if($this->CallbackAndMandatorycheck() && $this->app->erp->commonreadonly!="1"){
        // hier aenderung an db machen
        $tmp = $this->app->ObjAPI->Get($this->table);
        $tmp->Select($this->app->Secure->GetGET("id"));
        foreach($this->HTMLList as $name=>$entry){ 	
          $methodname = "Set".ucfirst($name);
          if(method_exists($tmp,$methodname)){
            $value = $entry->dbvalue;
            // spezial konvertierung
            $arr = $this->replacecallbacks[$name];
            if($arr[0]==$name)
            { 
              // aufruf der replacecallback funktion
              $_name = $arr[2];
              $value = $arr[1]->$_name(1,$value,1); // wenn speichern dann 1 bei uebernehmen = 0
            }

            $tmp->$methodname($value);
          }
        }
        if($this->table != 'shopexport')$this->app->erp->StartChangeLog($this->table);
        $tmp->Update();
        $this->app->erp->WriteChangeLog();
        //     $this->app->Tpl->Add('MESSAGE','<div class="error2">Die Daten wurden erfolgreich gespeichert!</div>');
        // passt und speichern
        //if($this->locafterexe!="")
        if(!empty($this->table) && method_exists($tmp, 'GetId')){
          $table = $this->table;
          $tableId = $tmp->GetId();
          $this->app->erp->RunHook($table . '_update', 1, $tableId);
        }

        $this->ActionAfterExecute();
        //	else
        //  $this->PrintForm();
      } else {
        $id = $this->app->Secure->GetGET("id");
        $module = $this->app->Secure->GetGET("module");
        if($id > 0 && $module != '' && $this->app->DB->Select("SELECT id FROM $module WHERE id = '$id' AND schreibschutz = 1 LIMIT 1"))
        {
        }else{
//          if($this->app->erp->commonreadonly!="1")
            $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Bitte alle Pflichtfelder ausf&uuml;llen!</div>");
        }
        $this->PrintForm();
      }
    } else {
      //TODOOOOOOOOO 
      // hole daten 

      $tmp = $this->app->ObjAPI->Get($this->table);
      $id = $this->app->Secure->GetGET("id");

      // wenn es ein id feld mit wert gibt dann dies als id verwenden!!!!
      if(isset($this->HTMLList) && isset($this->HTMLList['id']) && $this->HTMLList['id']->htmlobject->value!="")
        $id = $this->HTMLList['id']->htmlobject->value;
      $tmp->Select($id);
      foreach($this->HTMLList as $name=>$entry){ 	
        $methodname = "Get".ucfirst($name);
        //echo $this->app->Secure->GetGET("id").' '.$methodname.' '.$tmp->$methodname().'<br>';
        if(method_exists($tmp,$methodname))
          $this->HTMLList[$name]->htmlobject->value = $tmp->$methodname();

      }
      //TODOOOOOOOOO
      $this->PrintForm();
    }
  }

  function Copy()
  {
    //if($this->FormhandlerEvent()) {
    $tmp = $this->app->ObjAPI->Get($this->table);
    $tmp->Select($this->app->Secure->GetGET("id"));
    $tmp->id="";
    $tmp->Create();
    $this->app->erp->ObjektProtokoll($this->table,$tmp->GetId(),$this->table."_create",ucfirst($this->table)." angelegt");
    // passt und speichern
    $this->ActionAfterExecute();
    //}
  }

  function Create()
  {
    if($this->FormhandlerEvent()) {
      // wenn es fehlerfrei ist dann irgendwo hin sonst wieder das formular anzeigen
      if($this->CallbackAndMandatorycheck()){
        // hier aenderung an db machen
        //echo "jetzt anlegen ";	

        // einmal mit Create ein Objekt erstellen
        $tmp = $this->app->ObjAPI->Get($this->table);
        $tmp->Create();
        $this->app->erp->ObjektProtokoll($this->table,$tmp->GetId(),$this->table."_create",ucfirst($this->table)." angelegt");

        $this->pkvalue=$tmp->GetId();

        foreach($this->HTMLList as $name=>$entry){ 	
          //echo $name;
          //echo $entry->dbvalue;

          $methodname = "Set".ucfirst($name);
          if(method_exists($tmp,$methodname)){
            // spezial konvertierung
            $arr = $this->replacecallbacks[$name];
            $value = $entry->dbvalue;
            if($arr[0]==$name)
            { 
              // aufruf der replacecallback funktion
              $_name = $arr[2];
              $value = $arr[1]->$_name(1,$value,1);
            }

            $tmp->$methodname($value);
          }

          //echo "hier wuerd ich die dbvalues speichern";  	
        }
        if($this->table != 'shopexport')$this->app->erp->StartChangeLog($this->table);
        if(!empty($this->table) && method_exists($tmp, 'GetId')){
          $table = $this->table;
          $tableId = $tmp->GetId();
        }
        $tmp->Update();
        $this->app->erp->WriteChangeLog();
        if(!empty($this->table) && method_exists($tmp, 'GetId') && !empty($tableId)){
          $this->app->erp->RunHook($table . '_create', 1, $tableId);
        }

        // passt und speichern
        $this->ActionAfterExecute();
      } else {
        // validierungsfehler
        //echo "fehler!!!";
        //if($this->app->erp->commonreadonly!="1")
          $this->app->Tpl->Add('MESSAGE',"<div class=\"error\">Bitte alle Pflichtfelder ausf&uuml;llen! [TMPSCRIPT]</div>");

        $this->PrintForm();
      }
    } else {
      // das erste mal aneigen
      //echo "das erste mal";
      $this->PrintForm();
    }
  }
}


/// some functions for easy form processing ( save form to db ...)
class FormHandler
{

  var $FormList;
  var $defaultmandatorycss;

  /**
   * FormHandler constructor.
   *
   * @param Application $app
   */
  public function __construct($app)
  {
    $this->app=$app;
  }


  function FormToDatabase($table,$extfield,$extvalue, $escape = false)
  {
    $this->app->DB->Insert("INSERT INTO $table ($extfield) VALUES ('$extvalue')");
    $id = $this->app->DB->GetInsertID();
    $zielspalten = $this->app->DB->SelectArr("show columns from ".$table);
    if($zielspalten)
    {
      foreach($zielspalten as $val)$ziel[$val['Field']] = true;
    }
    if(is_numeric($id))
    {
      $sql = "UPDATE $table SET ";
      foreach($this->app->Secure->POST as $key=>$value)
      {
        if($key != 'id' && (isset($ziel[$key]) || !$zielspalten))$sqla[] = $key." = '".($escape?$this->app->DB->real_escape_string($value):$value)."' ";
      }
      $sql .= implode(', ',$sqla)." WHERE id='$id' LIMIT 1";
      $this->app->DB->Update($sql);
      if(mysqli_error($this->app->DB->connection))
      {
        foreach($this->app->Secure->POST as $key=>$value)
        {
          if($key != 'id')$this->app->DB->Update("UPDATE $table SET $key='".($escape?$this->app->DB->real_escape_string($value):$value)."' WHERE id='$id' LIMIT 1");
        }
      }
    }

    return $id;
  }

  function ArrayUpdateDatabase($table,$id,$values, $escape = false)
  {   
    $zielspalten = $this->app->DB->SelectArr("show columns from ".$table);
    if($zielspalten)
    {
      foreach($zielspalten as $val)$ziel[$val['Field']] = true;
    }
    //$this->app->DB->Insert("INSERT INTO $table ($extfield) VALUES ('$extvalue')");
    //$id = $this->app->DB->GetInsertID();
    if(is_numeric($id))
    {
      
      $sql = "UPDATE $table SET ";
      foreach($values as $key=>$value)
      {
        if($key!="id" && (isset($ziel[$key]) || !$zielspalten))
        {
          $sqla[] = $key." = '".($escape?$this->app->DB->real_escape_string($value):$value)."' ";
        }
      }
      
      $sql .= implode(', ',$sqla)." WHERE id='$id' LIMIT 1";
      $this->app->DB->Update($sql);
      
      if(mysqli_error($this->app->DB->connection))
      {
        foreach($values as $key=>$value)
        {
          if($key != "id")
          {
            $this->app->DB->Update("UPDATE $table SET $key='".($escape?$this->app->DB->real_escape_string($value):$value)."' WHERE id='$id' LIMIT 1");
          }
        }
      }
    }
    /*
    foreach($values as $key=>$value)
    {
      if($key!="id" && is_numeric($id))
        $this->app->DB->Update("UPDATE $table SET $key='$value' WHERE id='$id' LIMIT 1");
    }*/
    return $id;
  }


  function FormUpdateDatabase($table,$id, $escape = false)
  {
    //$this->app->DB->Insert("INSERT INTO $table ($extfield) VALUES ('$extvalue')");

    //$id = $this->app->DB->GetInsertID();
    $zielspalten = $this->app->DB->SelectArr("show columns from ".$table);
    if($zielspalten)
    {
      foreach($zielspalten as $val)$ziel[$val['Field']] = true;
    }
    if(is_numeric($id))
    {
      $sql = "UPDATE $table SET ";
      foreach($this->app->Secure->POST as $key=>$value)
      {
        if($key != 'id' && (isset($ziel[$key]) || !$zielspalten))$sqla[] = $key." = '".($escape?$this->app->DB->real_escape_string($value):$value)."' ";
      }
      $sql .= implode(', ',$sqla)." WHERE id='$id' LIMIT 1";
      $this->app->DB->Update($sql);
      if(mysqli_error($this->app->DB->connection))
      {
        foreach($this->app->Secure->POST as $key=>$value)
        {
          if($key != 'id')$this->app->DB->Update("UPDATE $table SET $key='".($escape?$this->app->DB->real_escape_string($value):$value)."' WHERE id='$id' LIMIT 1");
        }
      }
    }
    /*foreach($this->app->Secure->POST as $key=>$value)
    {
      if($key!="id" && is_numeric($id))
        $this->app->DB->Update("UPDATE $table SET $key='$value' WHERE id='$id' LIMIT 1");
    }*/
    return $id;
  }


  function FormGetVars($table,$id)
  {

    $cols = $this->app->DB->GetColArray("$table");

    foreach($cols as $key=>$value)
    {
      $this->app->Tpl->Set(strtoupper($value),$this->app->DB->Select("SELECT $value FROM $table WHERE id='$id' LIMIT 1"));
    }
    return $id;
  }


  function CreateNew($name)
  {
    return new FormActionHandler($this->app,$name);
  }

  // ********************************************************************** alt 

  function DefaultMandatoryCSSClass($cssclass)
  {
    $this->defaultmandatorycss=$cssclass;
  }
  function Create($formname,$table="",$pkname="id",$pkvalue="")
  {
    $this->FormList[$formname] = new FormActionHandler();
    $this->FormList[$formname]->mandatoryparse = "MSG";
    $this->FormList[$formname]->changelist;
    $this->FormList[$formname]->table=$table;
    $this->FormList[$formname]->pkname=$pkname;
    $this->FormList[$formname]->pkvalue=$pkvalue;
    $this->FormList[$formname]->getvaluesfromdb=false;

    $formaction=$this->app->Secure->GetGET("formaction");

    // create simple list fpr repesent the form as data structure 
    $this->FormList[$formname]->HTMLList = new SimpleList();
  }


  function Template($formname,$template,$parsetarget="PAGE")
  {
    $this->FormList[$formname]->template=$template;
    $this->FormList[$formname]->parsetarget=$parsetarget;
  }



  function Execute($formname,$nextformaction)
  {

    // check if table exists
    //$this->app->DBUpgrade->Checker('tabellenname');

    $this->FormList[$formname]->formaction=$nextformaction;
    $formaction = $this->app->Secure->GetGET("formaction");

    // check for edit if id is online
    $pkname = $this->FormList[$formname]->pkname;
    if($this->FormList[$formname]->pkvalue=="")
      $this->FormList[$formname]->pkvalue=$this->app->Secure->GetGET($pkname);

    if($this->FormList[$formname]->pkvalue!="" && $formaction=="")
    { 
      $this->FormList[$formname]->getvaluesfromdb=true;
    }


    if($nextformaction=="delete")
      $formaction="delete";

    switch($formaction)
    {
      case "create":
        if($this->MandatoryCheck($formname))
        {
          $this->InsertFormToDB($formname);	
          $this->GoToLocation($formname);
        } 
        else 
        {
          // show mandatory msgs and given values
          $this->MandatoryErrors($formname);
          //$this->FillActualFields($formname);
          $this->PrintForm($formname);
        }
        break;
      case "edit":
        if($this->MandatoryCheck($formname))
        {
          //$this->FillActualFields($formname);
          $this->UpdateFormToDB($formname);	

          $this->GoToLocation($formname);
        } 
        else 
        {
          // show mandatory msgs and given values
          $this->MandatoryErrors($formname);
          //$this->FillActualFields($formname);
          $this->PrintForm($formname);
        }
        break;

      case "replace":
        if($this->MandatoryCheck($formname))
        {
          if($this->FormList[$formname]->pkvalue=="")
            $this->InsertFormToDB($formname);	
          else
            $this->UpdateFormToDB($formname);	
          $this->GoToLocation($formname);
        } 
        else 
        {
          // show mandatory msgs and given values
          $this->MandatoryErrors($formname);
          //$this->FillActualFields($formname);
          $this->PrintForm($formname);
        }
        break;

      case "delete":
        // delete actual data
        $pkname=$this->FormList[$formname]->pkname;
        $pkvalue=$this->FormList[$formname]->pkvalue;
        $table=$this->FormList[$formname]->table;

        $pkvalue = $this->app->DB->Select("SELECT $pkname FROM `$table` 
            WHERE userid='".$this->app->User->GetID()."' AND `$pkname`='$pkvalue' LIMIT 1");

        $this->app->DB->Delete("DELETE FROM `$table` WHERE `$pkname`='$pkvalue' LIMIT 1");

        $this->GoToLocation($formname);
        break;
      default:
        $this->PrintForm($formname);
    }

  }

  function GoToLocation($formname)
  {
    header("Location: ".$this->app->http."://".$_SERVER['HTTP_HOST']
        .dirname($_SERVER['REQUEST_URI'])
        ."/".$this->FormList[$formname]->locafterexe);
  }

  function GetAssocValueArray($formname,$dbformat=true)
  {
    $htmllist = &$this->FormList[$formname]->HTMLList;

    if($htmllist->items > 0)
    {
      $field = &$htmllist->getFirst();
      for($i=0; $i <= $htmllist->items; $i++)
      {
        if(get_class($field->htmlobject)=="blindfield")
        {
          $value = $field->htmlobject->value;	
        }
        else {
          if($field->value=="")
            $value = $field->htmlobject->defvalue;
          else
            $value = $field->value;
        }
        //	  echo "hier {$field->identifier} {$field->value} jetzt $value<br>";

        // convert html to database format
        if($dbformat)
        {
          $value = $this->app->String->Convert(
              $value,$field->htmlformat,$field->dbformat);
        } 

        $ret[$field->identifier]=$value;
        $field = &$htmllist->getNext();
      }
    }
    return $ret;
  }

  function InsertFormToDB($formname)
  {
    $this->app->DB->InsertArr(
        $this->FormList[$formname]->table,
        $this->FormList[$formname]->pkname,
        $this->GetAssocValueArray($formname)
        );
  }

  function UpdateFormToDB($formname)
  {
    $this->app->DB->UpdateArr(
        $this->FormList[$formname]->table,
        $this->FormList[$formname]->pkvalue,
        $this->FormList[$formname]->pkname,
        $this->GetAssocValueArray($formname)
        );
  }

  /*
Syntax: array(
array(
<select|text|password|checkbox|radio|submit|reset|file|hidden|image|button>,
name,
<array(options)|value>,
checked
),*
)
   */
  function ConvertToForm($data) {
    $outdata = array();
    foreach($data as $field){
      if($field[0]=="select") {
        $nextfield = '<select name="'.$field[1].'">';
        $endtag = '</select>';
      } else {
        $nextfield = '<input type="'.$field[0].'" name="'.$field[1].'" ';
        $endtag = '/>';
      }

      if(!empty($field[2])){
        switch($field[0]){
          case "select":
            for ($i=0;$i<count($field[2]);$i++){
              $nextfield .= '<option value="'.$field[2][$i].'">'.$field[2][$i].'</option>';
            }
            break;
          case "radio":
          case "checkbox":
            if($field[3]) $nextfield .= 'checked="checked" ';
          case "text":
          default:
            $nextfield .= 'value="'.$field[2].'" ';
        }
      }

      $nextfield .= $endtag;
      $outdata[]=$nextfield;
    }
    return $outdata;
  }
  /// define output (html) and input (database) format
  function HTMLToDBConvert($formname,$identifier,$html,$db)
  {
    $htmllist = &$this->FormList[$formname]->HTMLList;

    if($htmllist->items>0)
    {
      $field = &$htmllist->getFirst();

      for($i=0; $i <= $htmllist->items; $i++)
      {
        if($field->identifier == $identifier)
        {
          $field->htmlformat = $html;
          $field->dbformat = $db;
        }
        $field = &$htmllist->getNext();
      }
    }
  }



  function AddMandatoryField($formname,$identifier,$msg,$rule="",$parsetarget="")
  { 
    if($rule=="")
      $rule="notempty";

    $htmllist = &$this->FormList[$formname]->HTMLList;

    if($htmllist->items>0)
    {
      $form = &$htmllist->getFirst();

      for($i=0; $i <= $htmllist->items; $i++)
      {
        if($form->identifier == $identifier)
        {
          $form->mandatorycheck = true;
          $form->mandatorymsg = $msg;
          $form->mandatoryrule = $rule;
          if($parsetarget!="")
            $form->mandatoryparse = $parsetarget;
        }
        $form = &$htmllist->getNext();
      }
    }
  }


  function MandatoryCheck($formname)
  {
    $dismiss = true;
    $htmllist = &$this->FormList[$formname]->HTMLList;

    if($htmllist->items>0)
    {
      $form = &$htmllist->getFirst();
      for($i=0; $i <= $htmllist->items; $i++)
      {
        if($form->mandatorycheck)
        {
          if(!$this->app->Secure->RuleCheck($form->value,$form->mandatoryrule))
          {
            $form->mandatoryok = false;
            $dismiss=false;
          }
        }
        $form = &$htmllist->getNext();
      }
    }
    return $dismiss;
  }


  function MandatoryErrors($formname)
  {
    $htmllist = &$this->FormList[$formname]->HTMLList;

    if($htmllist->items>0)
    {
      $form = &$htmllist->getFirst();
      for($i=0; $i <= $htmllist->items; $i++)
      {
        if(!$form->mandatoryok)
        {
          $this->app->Tpl->Add($form->mandatoryparse,$form->mandatorymsg);
          // mark up error field
          $form->htmlobject->class=$this->defaultmandatorycss;
          //$form->mandatoryrule = $rule;
        }
        $form = &$htmllist->getNext();
      }
    }
  }


  function NewField($formname,$htmlobject,$identifier="",$parsetarget="")
  {
    if($identifier=="")
      $identifier = $htmlobject->name;

    if($parsetarget=="")
      $parsetarget = strtoupper($htmlobject->name);

    // create new formhandlerfield
    $field = new FormHandlerField($identifier,$htmlobject);
    $field->ParseTarget($parsetarget);


    // nur wenn werte vom formular kommen, diese wirklich als value hernehmen
    if($this->app->Secure->GetPOST($identifier)!="")
    {
      $field->value = $this->app->Secure->GetPOST($identifier);
    }

    /*else
      {
    //$field->value = $field->htmlobject->value;
    }
     */	

    $this->FormList[$formname]->HTMLList->Add($field);
  }


  function DeleteMsg($formname,$delmsg,$delmsgcol)
  {
    $this->FormList[$formname]->delmsg = $delmsg;
    $this->FormList[$formname]->delmsgcol = $delmsgcol;
  }

  function PrintForm($formname)  // work only with hmtlobjects
  {
    if($this->FormList[$formname]->formaction=="edit" && $this->FormList[$formname]->pkvalue=="")
    {
    }
    else
    {
      // show form 
      // go through htmllist an parse every field
      $htmllist = &$this->FormList[$formname]->HTMLList;

      //load values from db when action = update
      if($this->FormList[$formname]->getvaluesfromdb)
      {
        if($htmllist->items>0)
        {
          $field = &$htmllist->getFirst();

          $pkname = $this->FormList[$formname]->pkname;
          $pkvalue = $this->FormList[$formname]->pkvalue;
          $table = $this->FormList[$formname]->table;

          while($field)
          {
            $value = $this->app->DB->Select("SELECT `{$field->identifier}` FROM 
                `$table` WHERE `$pkname`='$pkvalue' LIMIT 1"); 

              //value im html 
              $field->htmlobject->value = $value;

            $field = &$htmllist->getNext();
          }
        }
        $this->FillActualFields($formname); // fuer die homepage
      }
      else 
        $this->FillActualFields($formname,false); // fuer die datenbank 



      if($htmllist->items>0)
      {
        $field = &$htmllist->getFirst();
        while($field)
        {
          $htmlobject = &$field->htmlobject;
          $this->app->Tpl->Add($field->parsetarget,$htmlobject->Get().$htmlobject->GetClose());
          $field = &$htmllist->getNext();
        }
      }
      $formaction = $this->FormList[$formname]->formaction;

      $module = $this->app->Secure->GetGET("module","alpha");

      $action = $this->app->Secure->GetGET("action","alpha");

      $pkname = $this->FormList[$formname]->pkname;
      $pkvalue = $this->FormList[$formname]->pkvalue;

      $this->app->Tpl->Set('ACTION',  
          $_SERVER['REQUEST_URI']."?module=$module&action=$action&formaction=$formaction
          &$pkname=$pkvalue");

      $this->app->Tpl->Parse(
          $this->FormList[$formname]->parsetarget,
          $this->FormList[$formname]->template
          );
    }
  }


  // add form fields to values for db input

  function FillActualFields($formname,$convert=true) // fuer die datenbank 
  {
    $htmllist = &$this->FormList[$formname]->HTMLList;
    if($htmllist->items>0)
    {
      $field = &$htmllist->getFirst();
      for($i=0; $i <= $htmllist->items; $i++)
      {

        if($this->app->Secure->GetPOST($field->identifier)!="")
        {
          $field->value = $this->app->Secure->GetPOST($field->identifier);
        }else
        {
          $field->value = $field->htmlobject->value;
        }


        if($field->value!="" && $convert){
          $value = $this->app->String->Convert(
              //$field->value,$field->htmlformat,$field->dbformat);
            $field->value,$field->dbformat,$field->htmlformat);

          $value = $this->app->String->decodeText($value);
          $field->value = $value;
        } 

        if(get_class($htmlobject)=="blindfield")
          $field->value=$field->htmlobject->value;


        $field->htmlobject->value=$field->value;


        $field = &$htmllist->getNext();
      }
    }
  }


  function LocationAfterExecute($formname,$target)
  {
    $this->FormList[$formname]->locafterexe=$target;
  }


}

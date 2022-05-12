<?php
include ("_gen/widget.gen.lager_platz.php");

class WidgetLager_platz extends WidgetGenLager_platz 
{
  private $app;
  function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {

    $action = $this->app->Secure->GetGET("action");

    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);

    $this->app->YUI->AutoComplete("adresse","adresse");
    $this->form->ReplaceFunction("adresse",$this,"ReplaceAdresse");


    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");

    $this->form->ReplaceFunction("kurzbezeichnung",$this,"ReplaceAbkuerzung");

    if($action=="platz")
    { 
      // liste zuweisen
      $pid = $this->app->Secure->GetGET("id");
      $this->app->Secure->POST["lager"]=$pid;
      $field = new HTMLInput("lager","hidden",$pid);
      $this->form->NewField($field);
    }
  }

  function ReplaceAbkuerzung($db,$abkuerzung,$fromform)
  {
    $allowed = "/[^a-z0-9A-Z]/i";
    $abkuerzung = preg_replace($allowed,"",$abkuerzung);
    return substr($abkuerzung,0,15);
  }

  function ReplaceAdresse($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAdresse($db,$value,$fromform);
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$value' LIMIT 1");
    }

    // wenn ziel datenbank
    if($db)
    { 
      return $id;
    }
    // wenn ziel formular
    else
    { 
      return $abkuerzung;
    }
  }


  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT kurzbezeichnung, id FROM lager_platz");
    $table->Display($this->parsetarget);
  }



  public function Search()
  {
    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}
?>

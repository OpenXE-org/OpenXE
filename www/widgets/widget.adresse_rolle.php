<?php
include ("_gen/widget.gen.adresse_rolle.php");

class WidgetAdresse_rolle extends WidgetGenAdresse_rolle 
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
    //$this->app->YUI->AutoComplete(PARAMETERAUTO,"parameter",array('name','abkuerzung'),"abkuerzung","","projekt");

    $this->app->YUI->AutoComplete("parameter","projektname",1);
    $this->app->YUI->AutoComplete("gruppe","gruppe",1);
    $this->form->ReplaceFunction("gruppe",$this,"ReplaceGruppe");

    // liste zuweisen
    $pid = $this->app->Secure->GetGET("id");
    $this->app->Secure->POST["adresse"]=$pid;
    $field = new HTMLInput("adresse","hidden",$pid);
    $this->form->NewField($field);

    $this->app->Secure->POST["von"]=date('y-m-d');
    $field = new HTMLInput("von","hidden",date('y-m-d'));
    $this->form->NewField($field);
/*
    if($this->app->Secure->GetPOST("parameter")!="" && $this->app->Secure->POST["objekt"]=="Projekt")
    {
      $this->app->Secure->POST["praedikat"]="von";
      $field = new HTMLInput("praedikat","hidden","von");

      $this->app->Secure->POST["objekt"]="Projekt";
      $field = new HTMLInput("objekt","hidden","Projekt");
      $this->form->NewField($field);
    }

    $subjekt= $this->app->erp->GetAdressSubject();
*/
    $field = new HTMLSelect("subjekt",0);
    $field->AddOptionsSimpleArray($subjekt);
    $this->form->NewField($field);
  }

  function ReplaceGruppe($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceGruppe($db,$value,$fromform);
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceDatum($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(strpos($value,'-') > 0) $dbformat = 1;

    // wenn ziel datenbank
    if($db)
    { 
      if($dbformat) return $value;
      else return $this->app->String->Convert($value,"%1.%2.%3","%3-%2-%1");
    }
    // wenn ziel formular
    else
    { 
      if($dbformat) return $this->app->String->Convert($value,"%1-%2-%3","%3.%2.%1");
      else return $value;
    }
  }

  function ReplaceLieferant($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(is_numeric($value)) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE name='$value' LIMIT 1");
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
    $table->Query("SELECT nummer, name_de as name,barcode, id FROM adresse_rolle order by nummer");
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

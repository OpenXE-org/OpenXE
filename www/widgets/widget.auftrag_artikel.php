<?php
include ("_gen/widget.gen.auftrag_artikel.php");

class WidgetAuftrag_artikel extends WidgetGenAuftrag_artikel 
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
    $this->form->ReplaceFunction("artikel",$this,"ReplaceArtikel");


    if($action=="artikel")
    {
      // liste zuweisen
      $pid = $this->app->Secure->GetGET("id");
      $this->app->Secure->POST["adresse"]=$pid;
      $field = new HTMLInput("adresse","hidden",$pid);
      $this->form->NewField($field);
    }

    $this->app->YUI->AutoComplete('AUTO',"artikel",array('nummer','name_de','warengruppe'),"nummer");
  }

  function ReplaceArtikel($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$value' LIMIT 1");
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
    $table->Query("SELECT nummer, name_de as name,barcode, id FROM auftrag_artikel order by nummer");
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

<?php
include ("_gen/widget.gen.arbeitspaket.php");

class WidgetArbeitspaket extends WidgetGenArbeitspaket 
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

    $this->app->YUI->DatePicker("abgabedatum");
    $this->app->YUI->AutoComplete("adresse","mitarbeiter");
    $this->app->YUI->AutoComplete("artikel_geplant","artikelnummer");

    $this->form->ReplaceFunction("adresse",$this,"ReplaceMitarbeiter");
    $this->form->ReplaceFunction("abgabedatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("artikel_geplant",$this,"ReplaceArtikel");


//    $this->app->YUI->AutoComplete("auftragid","auftrag",1);
//    $this->form->ReplaceFunction("auftragid",$this,"ReplaceAuftrag");

		$id = $this->app->Secure->GetGET("id");
		$sid = $this->app->Secure->GetGET("sid");
		if($sid=="") { $sid=$id; $id=""; }
		$vorgaenger = $this->app->erp->GetVorgaenger($sid,$id);

		
    $field = new HTMLSelect("vorgaenger",0);
    $field->AddOptionsAsocSimpleArray($vorgaenger);
    $this->form->NewField($field);


    $action = $this->app->Secure->GetGET("action");
    if($action=="arbeitspaket")
    {
      // liste zuweisen
      $pid = $this->app->Secure->GetGET("id");
      $this->app->Secure->POST["projekt"]=$pid;
      $field = new HTMLInput("projekt","hidden",$pid);
      $this->form->NewField($field);
    }
  }

  function ReplaceArtikel($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArtikel($db,$value,$fromform);
  }


  function ReplaceDatum($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDatum($db,$value,$fromform);
  }


  function ReplaceMitarbeiter($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceMitarbeiter($db,$value,$fromform);
  }


  public function Table()
  {
    $table = new EasyTable($this->app);
    $table->Query("SELECT nummer, name_de as name,barcode, id FROM einkaufspreise order by nummer");
    $table->Display($this->parsetarget);
  }

  function ReplaceAuftrag($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceAuftrag($db,$value,$fromform);
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

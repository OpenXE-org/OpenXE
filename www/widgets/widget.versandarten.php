<?php
include ("_gen/widget.gen.versandarten.php");

class WidgetVersandarten extends WidgetGenVersandarten 
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
    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    
    $aktmodul = '';
    $id = (int)$this->app->Secure->GetGET('id');
    if($id)$aktmodul = $this->app->DB->Select("SELECT modul FROM versandarten WHERE id = '$id' LIMIT 1");
    $gefunden = ($aktmodul == ''?true:false);
    $modulsel = array(''=>'');
    $modularr = $this->app->DB->SelectArr("SELECT id, modul, bezeichnung FROM versanddienstleister WHERE aktiv = 1 AND modul != '' order by bezeichnung");
    if($modularr)
    {
      foreach($modularr as $val)
      {
        if(file_exists(dirname(__FILE__).'/../lib/versandarten/'.$val['modul'].'.php'))
        {
          $modulsel[$val['modul']] = $val['bezeichnung'];
          if($aktmodul == $val['modul'])$gefunden = true;
        }
      }
    }
    if(!$gefunden)$modulsel[$aktmodul] = $aktmodul;
    $field = new HTMLSelect("selmodul",0);
    $field->AddOptionsAsocSimpleArray($modulsel);
    $this->form->NewField($field);
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  public function Table()
  {
    $this->app->YUI->TableSearch('INHALT',"versandartenlist");
    $this->app->Tpl->Parse($this->parsetarget,"rahmen70.tpl");

  }

  public function Search()
  {
    //$this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }

}
?>

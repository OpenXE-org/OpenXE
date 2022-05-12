<?php
include ("_gen/widget.gen.berichte.php");

class WidgetBerichte extends WidgetGenBerichte 
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
    $this->app->YUI->TimePicker("ftpuhrzeit");
    $this->form->ReplaceFunction("project",$this,"ReplaceProjekt");
    $this->app->YUI->AutoComplete("project","projektname",1);

    $id = $this->app->Secure->GetGET('id');
    $this->app->erp->MenuEintrag("index.php?module=berichte&action=create","Berichte Neu");
    if($id){
      $this->app->erp->MenuEintrag("index.php?module=berichte&action=edit&id=$id","Details");
      $obj = $this->app->erp->LoadModul('berichte');
      if($obj){
        $struktur = $this->app->DB->Select("SELECT struktur FROM berichte WHERE id = '$id' LIMIT 1");
        if(!$obj->sqlok($struktur)){
          $this->app->Tpl->Add('MESSAGE', '<div class="error">Nicht erlaubte Abfrage</div>');
        }else{
          $this->app->erp->MenuEintrag("index.php?module=berichte&action=pdf&id=$id","als PDF anzeigen");
          $this->app->erp->MenuEintrag("index.php?module=berichte&action=csv&id=$id","als CSV anzeigen");
          $this->app->erp->MenuEintrag("index.php?module=berichte&action=live&id=$id", "Live");

        }
      }
    }
  }
  
  public function Table()
  {
    //$table->Query("SELECT nummer,beschreibung, id FROM berichte");
 		$table = new EasyTable($this->app);
    $this->app->Tpl->Set('INHALT',"");
    $table->Query("SELECT name, id FROM berichte");
    $table->DisplayNew($this->parsetarget, "
				<a href=\"index.php?module=berichte&action=pdf&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/pdf.svg\"></a>
				<a href=\"index.php?module=berichte&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.svg\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=berichte&action=delete&id=%value%';\">
          <img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a>

        ");

  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }



  public function Search()
  {
//    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}
?>

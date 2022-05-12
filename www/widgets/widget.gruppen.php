<?php
include ("_gen/widget.gen.gruppen.php");

class Widgetgruppen extends WidgetGengruppen 
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
   	$this->app->YUI->AutoComplete("portoartikel","artikelnummer",1);
    $this->form->ReplaceFunction("portoartikel",$this,"ReplaceArtikel");
    $this->app->YUI->AutoComplete("projekt","projektname",1);
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->app->YUI->AutoComplete("kategorie","gruppen_kategorien");
    $this->form->ReplaceFunction("kategorie",$this,"ReplaceGruppenKategorien");
    $arten = array('gruppe'=>'Gruppen', 'preisgruppe'=>'Preisgruppe','verband'=>'Verband');

    // liste zuweisen

    if($this->app->erp->RechteVorhanden('vereinsverwaltung','list'))
    {
      $arten = array('gruppe'=>'Gruppen', 'preisgruppe'=>'Preisgruppe','verband'=>'Verband','regionalgruppe'=>'Regionalgruppe','kategorie'=>'Kategorie');
    }
    if($this->app->erp->ModulVorhanden('provisionenartikel'))
    {
      $arten['vertreter'] = 'Vertreter';
    }
    
    $field = new HTMLSelect("art",0,"art");
    $field->AddOptionsAsocSimpleArray($arten);
    $this->form->NewField($field);

    $id = $this->app->Secure->GetGET("id");

    if(is_numeric($id))
      $nummer_db = $this->app->DB->Select("SELECT kennziffer FROM gruppen WHERE id='$id' LIMIT 1");

    $anzahl_nummer = $this->app->DB->Select("SELECT count(id) FROM gruppen WHERE kennziffer='$nummer_db'");
    if($anzahl_nummer > 1)
    {
      $this->app->YUI->Message("error","Achtung! Die Kennziffer wurde doppelt vergeben!");
    }
    

  if($action=="create")
    { 
      // liste zuweisen
      if($this->app->Secure->POST["projekt"]=="")
      { 
        $this->app->erp->LogFile("Standard Projekt laden");
        $projekt = $this->app->DB->Select("SELECT standardprojekt FROM firma WHERE id='".$this->app->User->GetFirma()."' LIMIT 1");

        $projekt_bevorzugt=$this->app->DB->Select("SELECT projekt_bevorzugen FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
        if($projekt_bevorzugt=="1")
        { 
          $projekt = $this->app->DB->Select("SELECT projekt FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");
        }
        $field = new HTMLInput("projekt","text",$projekt);
        $field->value=$projekt;
        $this->form->NewField($field);
      }
    }
 
  }
  
  public function Table()
  {
    //$table->Query("SELECT nummer,beschreibung, id FROM gruppen");
		$this->app->YUI->TableSearch($this->parsetarget,"gruppenlist");
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

  function ReplaceArtikel($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArtikel($db,$value,$fromform);
  }


  function ReplaceGruppenKategorien($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceGruppenKategorien($db,$value,$fromform);
  }



}
?>

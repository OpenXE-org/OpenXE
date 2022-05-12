<?php
include ("_gen/widget.gen.artikelkategorien.php");

class WidgetArtikelkategorien extends WidgetGenArtikelkategorien 
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
    $this->app->YUI->AutoComplete("parent","alleartikelkategorien");
    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("parent",$this,"ReplaceArtikelkategorie");
  }
  
  public function Table()
  {
		$this->app->YUI->TableSearch($this->parsetarget,"artikelkategorienlist");
  }

  public function CheckParent($id, $parent)
  {
    if(!$parent)return true;
    if($parent == $id)return false;
    $grandparent = $this->app->DB->Select("SELECT parent FROM artikelkategorien WHERE id = '$parent' LIMIT 1");
    if($grandparent)return $this->CheckParent($id, $grandparent);
    return true;
  }

  public function Edit()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    if($id)
    {
      if($parent = $this->app->Secure->GetPOST('parent'))
      {
        $parent = $this->ReplaceArtikelkategorie(true,$parent,true);
        if(!$this->CheckParent($id, $parent))
        {
          $this->app->Tpl->Set('MESSAGE',"<div class=\"error\">Eine Kategorie kann nicth Unterkategorie von sich selbst sein!</div>");
          $this->form->PrintForm();
          return;
        }
      }
    }
    parent::Edit();

    $project = $this->app->DB->Select("SELECT projekt FROM artikelkategorien WHERE id={$id}");
    $this->updateChildren($id, $project);
  }

  private function updateChildren($parent, $project, $level = 0){
    $children = $this->app->DB->SelectArr('SELECT id FROM artikelkategorien WHERE parent=' . $this->app->DB->real_escape_string($parent));
    if($level >= 20 || empty($children)){
      return;
    }
    foreach ($children as $child){
      $this->app->DB->Update("UPDATE artikelkategorien SET projekt='$project' WHERE id={$child['id']}");
      $this->updateChildren($child['id'], $project, $level + 1);
    }
  }

  public function Search()
  {
//    $this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceProjekt($db,$value,$fromform);
  }

  function ReplaceArtikelkategorie($db,$value,$fromform)
  {
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT bezeichnung FROM artikelkategorien WHERE id='$id' AND geloescht=0 LIMIT 1");
      if($id==0 || $id=="") $abkuerzung ="";
    } else {
      $rmodule = $this->app->Secure->GetGET("module");
      $raction = $this->app->Secure->GetGET("action");
      $rid = (int)$this->app->Secure->GetGET("id");
      $pruefemodule = array('artikel','auftrag','angebot','rechnung','lieferschein','gutschrift','bestellung');
      $filter_projekt = false;
      if($raction == 'edit' && $rid && in_array($rmodule, $pruefemodule))
      {
        $projekt = $this->app->DB->Select("SELECT projekt FROM $rmodule WHERE id = '$rid' LIMIT 1");
        if($projekt)
        {
          $eigenernummernkreis = $this->app->DB->Select("SELECT eigenernummernkreis FROM projekt WHERE id = '$projekt' LIMIT 1");
          //if($eigenernummernkreis)
            $filter_projekt = $projekt;
        }
      }
      $dbformat = 0;
      $abkuerzung = $value;
      $tmp = trim($value);
      $rest = explode(" ",$tmp);
      $rest = $rest[0];
      $id =  $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE bezeichnung = '".$value."' ORDER BY ".($filter_projekt?" projekt = '$filter_projekt' DESC, ":"")." projekt LIMIT 1");
      if($id <=0) $id=0;
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

}
?>

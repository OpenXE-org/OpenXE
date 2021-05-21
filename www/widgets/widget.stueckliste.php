<?php
include ("_gen/widget.gen.stueckliste.php");

class WidgetStueckliste extends WidgetGenStueckliste 
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
    if($action!="editstueckliste")
    {
      // liste zuweisen
      $pid = $this->app->Secure->GetGET("id");
      $this->app->Secure->POST["stuecklistevonartikel"]=$pid;
      $field = new HTMLInput("stuecklistevonartikel","hidden",$pid);
      $this->form->NewField($field);

      // sortierung
      $maxsort = $this->app->DB->Select("SELECT MAX(sort) FROM stueckliste WHERE stuecklistevonartikel='$pid' LIMIT 1");
      $this->app->Secure->POST["sort"]=$maxsort+1;
      $field = new HTMLInput("sort","hidden",$maxsort+1);
      $this->form->NewField($field);
    }

    $this->app->YUI->AutoComplete("artikel","artikelnummer");
    $this->form->ReplaceFunction("artikel",$this,"ReplaceArtikel");

    $this->app->YUI->AutoComplete("alternative","artikelnummer");
    $this->form->ReplaceFunction("alternative",$this,"ReplaceArtikel");


    $this->form->ReplaceFunction("menge",$this,"ReplaceDecimal");

    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);

  }

  function ReplaceArtikel($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArtikel($db,$value,$fromform);
  }

  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }

  function DatumErsetzen($wert)
  {
    return "neuerwerert";
  }

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT nummer, name_de as name,barcode, id FROM stueckliste order by nummer");
    $table->Display($this->parsetarget);
  }

  function Edit()
  {
    if($this->app->Secure->GetPOST('artikel'))
    {
      $artikel = $this->app->Secure->GetPOST('artikel');
      $artikel = $this->app->erp->ReplaceArtikel(1,$artikel,1);
      $id = (int)$this->app->Secure->GetGET('id');
      $artikelvon = (int)$this->app->DB->Select("SELECT stuecklistevonartikel FROM stueckliste WHERE id = '$id' LIMIT 1");
      if($artikel && $artikelvon)
      {
        
        if($this->app->erp->IstStuecklistenZirkel($artikel, $artikelvon))
        {
          $msg = $this->app->erp->base64_url_encode('<div class="warning">St&uuml;ckliste enth&auml;lt Artikel die einen Zirkelbezug verursachen!</div>');
          header('Location: index.php?module=artikel&action=stueckliste&id='.$artikelvon.'&msg='.$msg);
          exit;
        }
      }
    }
    $this->form->Edit();
  }

  public function Create()
  {
    if($this->app->Secure->GetPOST('artikel'))
    {
      $artikel = $this->app->Secure->GetPOST('artikel');
      $artikel = $this->app->erp->ReplaceArtikel(1,$artikel,1);
      $artikelvon = (int)$this->app->Secure->GetGET('id');
      
      if($artikel && $artikelvon)
      {
        if($this->app->erp->IstStuecklistenZirkel($artikel, $artikelvon))
        {
          $msg = $this->app->erp->base64_url_encode('<div class="warning">St&uuml;ckliste enth&auml;lt Artikel die einen Zirkelbezug verursachen!</div>');
          header('Location: index.php?module=artikel&action=stueckliste&id='.$artikelvon.'&msg='.$msg);
          exit;
        }
      }
    }
    $this->form->Create();
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

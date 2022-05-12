<?php
include ("_gen/widget.gen.rohstoffe.php");

class WidgetRohstoffe extends WidgetGenRohstoffe 
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
    if($action!="editrohstoffe")
    {
      // liste zuweisen
      $pid = $this->app->Secure->GetGET("id");
      $this->app->Secure->POST["rohstoffvonartikel"]=$pid;
      $field = new HTMLInput("rohstoffvonartikel","hidden",$pid);
      $this->form->NewField($field);

      // sortierung
      $maxsort = $this->app->DB->Select("SELECT MAX(sort) FROM rohstoffe WHERE rohstoffevonartikel='$pid' LIMIT 1");
      $this->app->Secure->POST["sort"]=$maxsort+1;
      $field = new HTMLInput("sort","hidden",$maxsort+1);
      $this->form->NewField($field);
    }

    $this->app->YUI->AutoComplete("artikel","artikelnummer");
    $this->form->ReplaceFunction("artikel",$this,"ReplaceArtikel");
    $this->form->ReplaceFunction("menge",$this,"ReplaceDecimal");

  }

  function ReplaceArtikel($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArtikel($db,$value,$fromform);
  }

  function DatumErsetzen($wert)
  {
    return "neuerwerert";
  }

  function Edit()
  {
    if($this->app->Secure->GetPOST('artikel'))
    {
      $artikel = $this->app->Secure->GetPOST('artikel');
      $artikel = $this->app->erp->ReplaceArtikel(1,$artikel,1);
      $id = (int)$this->app->Secure->GetGET('id');
      $artikelvon = (int)$this->app->DB->Select("SELECT rohstoffevonartikel FROM rohstoffe WHERE id = '$id' LIMIT 1");
      if($artikel && $artikelvon)
      {
        
        if($this->app->erp->IstRohstoffeZirkel($artikel, $arikelvon))
        {
          $msg = $this->app->erp->base64_url_encode('<div class="error2">St&uuml;ckliste enth&auml;lt Artikel die einen Kreis entsprechen!</div>');
          header('Location: index.php?module=artikel&action=rohstoffe&id='.$artikelvon.'&msg='.$msg);
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
        if($this->app->erp->IstRohstoffeZirkel($artikel, $arikelvon))
        {
          $msg = $this->app->erp->base64_url_encode('<div class="error2">St&uuml;ckliste enth&auml;lt Artikel die einen Kreis entsprechen!</div>');
          header('Location: index.php?module=artikel&action=rohstoffe&id='.$artikelvon.'&msg='.$msg);
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


  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }

}
?>

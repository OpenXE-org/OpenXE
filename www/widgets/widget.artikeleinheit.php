<?php
include ("_gen/widget.gen.artikeleinheit.php");

class WidgetArtikeleinheit extends WidgetGenArtikeleinheit 
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
    $id = $this->app->Secure->GetGET("id");
    $action = $this->app->Secure->GetGET("action");

    $value = '';
    $dataLang = '';
    if($action === 'edit'){
      $query = sprintf("SELECT ae.einheit_de AS `einheit_de` FROM `artikeleinheit` AS `ae` WHERE ae.id = %d", $id);
      $value = $this->app->DB->Select($query);
      $dataLang = 'data-lang="artikeleinheit_[ID]"';
    }
    $text = sprintf('<input type="text" value="%s" name="einheit_de" size="40" rule="notempty" msg="Pflichfeld!" tabindex="2" %s>',
        $value, $dataLang);
    $this->app->Tpl->Set('EINHEIT_DE', $text);
  }

  public function Edit(){
    if(!empty($this->app->Secure->GetPOST('submit'))){
      $einheit_de = $this->app->Secure->GetPOST('einheit_de');
      $interneBemerkung = $this->app->Secure->GetPOST('internebemerkung');
      $id = $this->app->Secure->GetGET('id');

      $sql = sprintf("UPDATE `artikeleinheit` SET `einheit_de` = '%s',`internebemerkung` = '%s'
        WHERE `id` = %d ",$einheit_de, $interneBemerkung, $id);

      $this->app->DB->Update($sql);
      $msg = $this->app->erp->base64_url_encode('<div class="success">Die Daten wurden gespeichert!</div>');
      $this->app->Location->execute('Location: index.php?module=artikeleinheit&action=edit&id='.$id.'&msg='.$msg);
    }

    parent::Edit();
  }

  public function Create(){
    if(!empty($this->app->Secure->GetPOST('submit'))){
      $einheit_de = $this->app->Secure->GetPOST('einheit_de');
      $interneBemerkung = $this->app->Secure->GetPOST('internebemerkung');
      $sql = sprintf("INSERT INTO `artikeleinheit` (`id`,`einheit_de`,`internebemerkung`)
      VALUES(NULL,'%s','%s')",$einheit_de, $interneBemerkung);

      $this->app->DB->Insert($sql);
      $id = $this->app->DB->GetInsertID();
      $msg = $this->app->erp->base64_url_encode('<div class="success">Die Daten wurden gespeichert!</div>');
      $this->app->Location->execute('Location: index.php?module=artikeleinheit&action=edit&id='.$id.'&msg='.$msg);
    }

    parent::Create();
  }

  public function Table()
  {
    //$table->Query("SELECT nummer,beschreibung, id FROM artikeleinheit");
		$this->app->YUI->TableSearch($this->parsetarget,"artikeleinheitlist");
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

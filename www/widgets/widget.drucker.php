<?php
include '_gen/widget.gen.drucker.php';

class WidgetDrucker extends WidgetGenDrucker 
{
  /** @var Application */
  private $app;

  /**
   * WidgetDrucker constructor.
   *
   * @param Application $app
   * @param $parsetarget
   */
  public function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  public function ExtendsForm()
  {
    $this->app->Secure->POST['firma']=$this->app->User->GetFirma();
    $field = new HTMLInput('firma','hidden',$this->app->User->GetFirma());
    $this->form->NewField($field);
    $id = $this->app->Secure->GetGET('id');

    $selAnbindung = [
      'cups' => 'Kommandozeilenbefehl',
      'pdf' => 'PDF in Verzeichnis',
      'adapterbox' => 'Adapterbox',
      'email' => 'E-Mail',
      'download' => 'Download',
      'spooler' => 'Xentral Druckerspooler',
    ];
    $printerModule = $id <=0?'':$this->app->DB->Select(
      sprintf(
        'SELECT anbindung FROM drucker WHERE id = %d',
        $id
      )
    );
    $printerModuleOptions = [];
    /** @var Drucker $obj */
    $obj = $this->app->erp->LoadModul('drucker');
    if(!empty($obj) && method_exists($obj,'PrinterSelModul')){
      $printerModuleOptions = $obj->PrinterSelModul($printerModule, true);
    }
    if(empty($printerModuleOptions)) {
      $printerModuleOptions = [];
    }
    $field = new HTMLSelect('anbindung',0);
    $field->AddOptionsSimpleArray(array_unique(array_merge($selAnbindung,$printerModuleOptions)));
    $this->form->NewField($field);

    if($id > 0) {
      $printer = $this->app->DB->SelectRow(
        sprintf(
          'SELECT id,json,anbindung FROM drucker WHERE id = %d',
          $id
        )
      );
      if(!empty($printer)&& !empty($obj) && method_exists($obj,'loadPrinterModul') && !empty($printer['anbindung'])) {
        /** @var PrinterBase $obj */
        $objPrinter = $obj->loadPrinterModul($printer['anbindung'], $printer['id']);
        if(!empty($objPrinter)){
          $objPrinter->Settings('JSON');
        }
      }
    }
  }


  /**
   * @param bool       $db
   * @param string|int $value
   * @param bool       $fromform
   *
   * @return int|string|null
   */
  public function ReplaceProjekt($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    if(!$fromform) {
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$id' LIMIT 1");
    }
    else {
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$value' LIMIT 1");
    }

    // wenn ziel datenbank
    if($db) {
      return $id;
    }

    return $abkuerzung;
  }

  /**
   * @param int $db
   * @param string $value
   *
   * @return string
   */
  public function ReplaceDecimal($db,$value)
  {
    //value muss hier vom format ueberprueft werden

    return str_replace(',','.',$value);
  }

  /**
   * @param bool   $db
   * @param string $value
   *
   * @return mixed
   */
  public function ReplaceDatum($db,$value)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(strpos($value,'-') > 0) {
      $dbformat = 1;
    }

    // wenn ziel datenbank
    if($db) {
      if($dbformat) {
        return $value;
      }

      return $this->app->String->Convert($value,'%1.%2.%3','%3-%2-%1');
    }
    // wenn ziel formular

    if($dbformat) {
      return $this->app->String->Convert($value,'%1-%2-%3','%3.%2.%1');
    }

    return $value;
  }

  /**
   * @param bool       $db
   * @param int|string $value
   *
   * @return int|string|null
   */
  public function ReplaceAdresse($db,$value)
  {
    //value muss hier vom format ueberprueft werden
    if(is_numeric($value)) {
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    }
    else {
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE name='$value' AND geloescht=0 LIMIT 1");
    }

    // wenn ziel datenbank
    if($db) {
      return $id;
    }
    // wenn ziel formular

    return $abkuerzung;
  }

  public function Table()
  {
		$this->app->YUI->TableSearch('INHALT','druckerlist');
    $this->app->Tpl->Parse($this->parsetarget,'rahmen70.tpl');

  }



  public function Search()
  {

  }
}

<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php 

class WidgetGenverbindlichkeit
{

  private $app;            //application object  
  public $form;            //store form object  
  protected $parsetarget;    //target for content

  public function __construct($app,$parsetarget)
  {
    $this->app = $app;
    $this->parsetarget = $parsetarget;
    $this->Form();
  }

  public function verbindlichkeitDelete()
  {
    
    $this->form->Execute("verbindlichkeit","delete");

    $this->verbindlichkeitList();
  }

  function Edit()
  {
    $this->form->Edit();
  }

  function Copy()
  {
    $this->form->Copy();
  }

  public function Create()
  {
    $this->form->Create();
  }

  public function Search()
  {
    $this->app->Tpl->Set($this->parsetarget,"SUUUCHEEE");
  }

  public function Summary()
  {
    $this->app->Tpl->Set($this->parsetarget,"grosse Tabelle");
  }

  function Form()
  {
    $this->form = $this->app->FormHandler->CreateNew("verbindlichkeit");
    $this->form->UseTable("verbindlichkeit");
    $this->form->UseTemplate("verbindlichkeit.tpl",$this->parsetarget);

    $field = new HTMLInput("adresse","text","","30","","","","","","","","0","","");
    $this->form->NewField($field);
    $this->form->AddMandatory("adresse","notempty","Pflichtfeld!","MSGADRESSE");


    $field = new HTMLInput("rechnung","text","","20","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("eingangsdatum","text","","20","","","","","","","","0","3","");
    $this->form->NewField($field);

    $field = new HTMLInput("bestellung","text","","20","","","","","","","","0","4","");
    $this->form->NewField($field);

    $field = new HTMLSelect("zahlungsweise",0,"zahlungsweise","","","0");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnungsdatum","text","","20","","","","","","","","0","5","");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlbarbis","text","","20","","","","","","","","0","6","");
    $this->form->NewField($field);

    $field = new HTMLInput("betrag","text","","20","","","","","","","","0","7","");
    $this->form->NewField($field);

    $field = new HTMLInput("waehrung","text","","8","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("skonto","text","","20","","","","","","","","0","9","");
    $this->form->NewField($field);

    $field = new HTMLInput("ustnormal","text","","4","","","","","","","","0","10","");
    $this->form->NewField($field);

    $field = new HTMLInput("summenormal","text","","16","","","","","","","","0","11","");
    $this->form->NewField($field);


    $field = new HTMLCheckbox("skontofestsetzen","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("umsatzsteuer",0,"umsatzsteuer","","","0");
    $field->AddOption('Inland','deutschland');
    $field->AddOption('EU-Lieferung','eulieferung');
    $field->AddOption('Import','export');
    $this->form->NewField($field);

    $field = new HTMLInput("ustermaessigt","text","","4","","","","","","","","0","12","");
    $this->form->NewField($field);

    $field = new HTMLInput("summeermaessigt","text","","16","","","","","","","","0","13","");
    $this->form->NewField($field);


    $field = new HTMLInput("skontobis","text","","20","","","","","","","","0","8","");
    $this->form->NewField($field);

    $field = new HTMLInput("uststuer3","text","","4","","","","","","","","0","15","");
    $this->form->NewField($field);

    $field = new HTMLInput("summesatz3","text","","16","","","","","","","","0","16","");
    $this->form->NewField($field);


    $field = new HTMLSelect("umsatzsteuer",0,"umsatzsteuer","","","0");
    $field->AddOption('{|Inland|}','deutschland');
    $field->AddOption('{|EU-Lieferung|}','eulieferung');
    $field->AddOption('{|Import|}','import');
    $this->form->NewField($field);

    $field = new HTMLInput("uststuer4","text","","4","","","","","","","","0","17","");
    $this->form->NewField($field);

    $field = new HTMLInput("summesatz4","text","","16","","","","","","","","0","18","");
    $this->form->NewField($field);


    $field = new HTMLInput("ustid","text","","20","","","","","","","","0","8","");
    $this->form->NewField($field);

    $field = new HTMLInput("verwendungszweck","text","","40","","","","","","","","0","19","");
    $this->form->NewField($field);

    $field = new HTMLInput("frachtkosten","text","","20","","","","","","","","0","20","");
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","20","","","","","","","","0","21","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("freigabe","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("teilprojekt","text","","20","","","","","","","","0","21","");
    $this->form->NewField($field);

    $field = new HTMLInput("auftrag","text","","20","","","","","","","","0","23","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("rechnungsfreigabe","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("kostenstelle","text","","20","","","","","","","","0","25","");
    $this->form->NewField($field);

    $field = new HTMLInput("betragbezahlt","text","","20","","","","","","","","0","24","");
    $this->form->NewField($field);

    $field = new HTMLInput("sachkonto","text","","20","","","","","","","","0","26","");
    $this->form->NewField($field);

    $field = new HTMLInput("skonto_erhalten","text","","20","","","","","","","","0","27","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("klaerfall","","","1","0","28");
    $this->form->NewField($field);

    $field = new HTMLInput("bezahltam","text","","20","","","","","","","","0","29","");
    $this->form->NewField($field);

    $field = new HTMLInput("klaergrund","text","","40","","","","","","","","0","30","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("schreibschutz","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLTextarea("internebemerkung",5,50,"","","","","0");   
    $this->form->NewField($field);



    $field = new HTMLInput("buha_konto1","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_belegfeld1","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_betrag1","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_konto2","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_belegfeld2","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_betrag2","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_konto3","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_belegfeld3","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_betrag3","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_konto4","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_belegfeld4","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_betrag4","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_konto5","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_belegfeld5","text","","80","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("buha_betrag5","text","","20","","","","","","","","0","","");
    $this->form->NewField($field);




  }

}

?>
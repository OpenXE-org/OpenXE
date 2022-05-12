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

class WidgetGengruppen
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

  public function gruppenDelete()
  {
    
    $this->form->Execute("gruppen","delete");

    $this->gruppenList();
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
    $this->form = $this->app->FormHandler->CreateNew("gruppen");
    $this->form->UseTable("gruppen");
    $this->form->UseTemplate("gruppen.tpl",$this->parsetarget);

    $field = new HTMLCheckbox("aktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("name","text","","80","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("name","notempty","Pflichfeld!","MSGNAME");

    $field = new HTMLInput("kennziffer","text","","80","","","","","","","","0","2","");
    $this->form->NewField($field);
    $this->form->AddMandatory("kennziffer","notempty","Pflichfeld!","MSGKENNZIFFER");

    $field = new HTMLTextarea("internebemerkung",10,130,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLSelect("art",0,"art","","","0");
    $field->AddOption('Gruppe','gruppe');
    $field->AddOption('Preisgruppe','preisgruppe');
    $field->AddOption('Verband','verband');
    $this->form->NewField($field);

    $field = new HTMLInput("projekt","text","","80","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("kategorie","text","","80","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("grundrabatt","text","","20","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltage","text","","20","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszielskonto","text","","20","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("zahlungszieltageskonto","text","","20","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("portofrei_aktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLInput("portofreiab","text","","12","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt1","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus1","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus1_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus6","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus6_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt2","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus2","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus2_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus7","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus7_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt3","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus3","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus3_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus8","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus8_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt4","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus4","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus4_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus9","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus9_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rabatt5","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus5","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus5_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus10","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("bonus10_ab","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("provision","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("sonderrabatt_skonto","text","","5","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("zentralregulierung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("zentralerechnung","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("rechnung_periode",0,"rechnung_periode","","","0");
    $field->AddOption('t&auml;glich','1');
    $field->AddOption('w&ouml;chentlich','2');
    $field->AddOption('14t&auml;gig','4');
    $field->AddOption('monatlich','5');
    $field->AddOption('einzel','6');
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_anzahlpapier","text","","5","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("rechnung_permail","","","1","0","2");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_name","text","","50","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_abteilung","text","","50","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_strasse","text","","50","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_plz","text","","10","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_ort","text","","40","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("rechnung_email","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("kundennummer","text","","","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLCheckbox("dta_aktiv","","","1","0","0");
    $this->form->NewField($field);

    $field = new HTMLSelect("dta_variante",0,"dta_variante","","","0");
    $field->AddOption('Variante 1','1');
    $field->AddOption('Variante 2','2');
    $field->AddOption('Variante 3','3');
    $field->AddOption('Variante 4','4');
    $field->AddOption('Variante 5','5');
    $field->AddOption('Variante 6','6');
    $field->AddOption('Variante 7','7');
    $field->AddOption('Variante 8','8');
    $field->AddOption('Variante 9','9');
    $this->form->NewField($field);

    $field = new HTMLTextarea("dtavariablen",10,50,"","","","","0");   
    $this->form->NewField($field);

    $field = new HTMLSelect("dta_periode",0,"dta_periode","","","0");
    $field->AddOption('15,30','1');
    $field->AddOption('7,15,22,30','2');
    $field->AddOption('Dienstag','3');
    $field->AddOption('Montag','4');
    $field->AddOption('2,11,27','5');
    $field->AddOption('2','6');
    $field->AddOption('Freitags','7');
    $this->form->NewField($field);

    $field = new HTMLInput("partnerid","text","","50","","","","","","","","0","","");
    $this->form->NewField($field);

    $field = new HTMLInput("dta_dateiname","text","","50","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("dta_mail","text","","50","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLInput("dta_mail_betreff","text","","50","","","","","","","","0","2","");
    $this->form->NewField($field);

    $field = new HTMLTextarea("dta_mail_text",10,50,"","","","","0");   
    $this->form->NewField($field);


  }

}

?>
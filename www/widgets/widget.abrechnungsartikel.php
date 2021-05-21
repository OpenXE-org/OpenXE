<?php
include ("_gen/widget.gen.abrechnungsartikel.php");

class WidgetAbrechnungsartikel extends WidgetGenAbrechnungsartikel 
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
    $id = (int)$this->app->Secure->GetGET("id");
    $this->form->ReplaceFunction("artikel",$this,"ReplaceArtikel");
    include("./pages/rechnungslauf.php");
    $abo = new Aboabrechnung($this->app);
    $ersternaechsterMonat = $abo->GetNextFirstDay();
    $this->app->Tpl->Set('ZEITPUNKT',date('d.m.Y',strtotime($ersternaechsterMonat)));

    $this->app->YUI->DatePicker("startdatum");
    $this->app->YUI->DatePicker("abgerechnetbis");
    $this->app->YUI->DatePicker("abgrechnetam");
    $this->app->YUI->CkEditor("beschreibung","belege");


    $this->app->YUI->DatePicker("enddatum");

    $this->app->YUI->AutoComplete("artikel","artikelnummer");
    $this->form->ReplaceFunction("artikel",$this,"ReplaceArtikel");

    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("startdatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("abgerechnetbis",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("abgrechnetam",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("enddatum",$this,"ReplaceDatum");
    $this->form->ReplaceFunction("menge",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("rabatt",$this,"ReplaceDecimal");
    $this->form->ReplaceFunction("preis",$this,"ReplaceDecimal");


    $this->app->YUI->AutoComplete(PROJEKTAUTO,"projekt",array('name','abkuerzung'),"abkuerzung");

    if($action=="artikel")
    {
      // liste zuweisen
      $pid = $this->app->Secure->GetGET("id");
      $this->app->Secure->POST["adresse"]=$pid;
      $field = new HTMLInput("adresse","hidden",$pid);
      $this->form->NewField($field);
    }

    $field = new HTMLCheckbox("experte","","","1");
    $field->onclick="expertetogl(this.form.experte.value);";
    $this->form->NewField($field);



    $adresse = $this->app->DB->Select("SELECT adresse FROM abrechnungsartikel WHERE id='$id' LIMIT 1");
    $result = $this->app->DB->SelectArr("SELECT id, beschreibung FROM abrechnungsartikel_gruppe WHERE adresse='$adresse' ORDER by beschreibung");
    for($i=0;$i<=count($result);$i++)
      $gruppen[$result[$i]['id']]=$result[$i]['beschreibung'];

    //$gruppen[0]="";
    asort($gruppen);


    $field = new HTMLSelect("gruppe",0);
    $field->AddOptionsAsocSimpleArray($gruppen);
    $this->form->NewField($field);


   
  }

  function ReplaceDecimal($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceDecimal($db,$value,$fromform);
  }


  function ReplaceArtikel($db,$value,$fromform)
  {
    return $this->app->erp->ReplaceArtikel($db,$value,$fromform);
  }

  function ReplaceProjekt($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(is_numeric($value)) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$value' LIMIT 1");
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

  function ReplaceDatum($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(strpos($value,'-') > 0) $dbformat = 1;

    // wenn ziel datenbank
    if($db)
    {
      if($dbformat) return $value;
      else return $this->app->String->Convert($value,"%1.%2.%3","%3-%2-%1");
    }
    // wenn ziel formular
    else
    {
      if($dbformat) return $this->app->String->Convert($value,"%1-%2-%3","%3.%2.%1");
      else return $value;
    }
  }


  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT nummer, name_de as name,barcode, id FROM abrechnungsartikel order by nummer");
    $table->Display($this->parsetarget);
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

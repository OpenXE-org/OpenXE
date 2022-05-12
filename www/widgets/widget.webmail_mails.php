<?php
include ("_gen/widget.gen.webmail_mails.php");

class WidgetWebmail_mails extends WidgetGenWebmail_mails 
{
  private $app;
  function __construct(&$app,$parsetarget)
  {
    $this->app = &$app;
    $this->parsetarget = $parsetarget;
    parent::__construct($app,$parsetarget);
    $this->ExtendsForm();
  }

  function ExtendsForm()
  {
    $this->app->YUI->AutoComplete('PROJEKTAUTO',"projekt",array('name','abkuerzung'),"abkuerzung");
    $this->app->YUI->AutoComplete('LIEFERANTAUTO',"adresse",array('name','vorname'),"name");

    $this->form->ReplaceFunction("projekt",$this,"ReplaceProjekt");
    $this->form->ReplaceFunction("adresse",$this,"ReplaceLieferant");
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

  function ReplaceLieferant($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) { 
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT name FROM adresse WHERE id='$id' LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE name='$value' LIMIT 1");
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


  function ReplaceProjekt($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) { 
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

  public function Table()
  {
    $table = new EasyTable($this->app);  
    $table->Query("SELECT DATE_FORMAT(datum,'%d.%m.%Y') as datum, belegnr as beleg, name, zahlungsweise, id
      FROM webmail_mails order by datum");
    $table->DisplayNew(PAGE, "<a href=\"index.php?module=webmail_mails&action=edit&id=%value%\">Bearbeiten</a>
        <a href=\"index.php?module=webmail_mails&action=pdf&id=%value%\">PDF</a>");
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

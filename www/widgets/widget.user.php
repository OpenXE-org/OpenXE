<?php
include ("_gen/widget.gen.user.php");

class WidgetUser extends WidgetGenUser 
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
    //firma
    $this->app->Secure->POST["firma"]=$this->app->User->GetFirma();
    $field = new HTMLInput("firma","hidden",$this->app->User->GetFirma());
    $this->form->NewField($field);

    //$this->app->YUI->AutoComplete(ADRESSEAUTO,"adresse",array('name','ort','mitarbeiternummer'),"CONCAT(mitarbeiternummer,' ',name)","mitarbeiter");
    $this->app->YUI->AutoComplete("adresse","adresse");

    $this->app->YUI->AutoComplete("vorlage","uservorlage");

    $this->form->ReplaceFunction("adresse",$this,"ReplaceMitarbeiter");
    $this->form->ReplaceFunction("password",$this,"ReplacePassword");
    $this->form->ReplaceFunction("repassword",$this,"ReplaceRePassword");


    //$this->app->Tpl->Set(DATUM_BUCHUNG,
    //    "<img src=\"./themes/[THEME]/images/kalender.png\" onclick=\"displayCalendar(document.forms[0].datum,'dd.mm.yyyy',this)\">");

  }

  function ReplaceRePassword($db,$value,$fromform)
  { 
      return "*************************";
  }



  function ReplacePassword($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
/*
    if(is_numeric($value)) {
      $dbformat = 1;
      $id = "huhuh".$value;
      $abkuerzung = "abk";$this->app->DB->Select("SELECT CONCAT(mitarbeiternummer,' ',name) FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = "crypt $value";//$value;
      $kundennummer = substr($value,0,5);
      $name = substr($value,6);
      $id =  "crypt $value id";//$this->app->DB->Select("SELECT id FROM adresse WHERE name='$name' AND mitarbeiternummer='$kundennummer'  AND geloescht=0 LIMIT 1");
    }
*/
    $repassword = $this->app->Secure->GetPOST("repassword");
    $password = $this->app->Secure->GetPOST("password");
    // wenn ziel datenbank
    if($db)
    {
      if($password==$repassword && $password!="" && $password!="*************************")
      return crypt($value, null);
      else
			{
				return $this->app->DB->Select("SELECT `password` FROM `user` WHERE `id` = '".$this->app->User->GetID()."'");
			}
      //return crypt($value);
    }
    // wenn ziel formular
    else
    {
      return "*************************";
    }
  }




  function ReplaceMitarbeiter($db,$value,$fromform)
  { 
    //value muss hier vom format ueberprueft werden
    $dbformat = 0;
    if(!$fromform) {
      $dbformat = 1;
      $id = $value;
      $abkuerzung = $this->app->DB->Select("SELECT CONCAT(mitarbeiternummer,' ',name) FROM adresse WHERE id='$id' AND geloescht=0 LIMIT 1");
    } else {
      $dbformat = 0;
      $abkuerzung = $value;
      $kundennummer = substr($value,0,5);
      $name = substr($value,6);
      $id =  $this->app->DB->Select("SELECT id FROM adresse WHERE name='$name' AND mitarbeiternummer='$kundennummer'  AND geloescht=0 LIMIT 1");
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

  function ReplaceDecimal($db,$value,$fromform)
  {
    //value muss hier vom format ueberprueft werden

    return str_replace(",",".",$value);
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
    $this->app->Tpl->Set(INHALT,"");
    $this->app->Tpl->Set(SUBSUBHEADING,"User");
    $table->Query("SELECT description as beschreibung, username as login, id FROM user WHERE firma='".$this->app->User->GetFirma()."'");
    $table->DisplayNew(INHALT, "<a href=\"index.php?module=benutzer&action=edit&id=%value%\"><img border=\"0\" src=\"./themes/[THEME]/images/edit.svg\"></a>
        <a onclick=\"if(!confirm('Wirklich löschen?')) return false; else window.location.href='index.php?module=benutzer&action=delete&id=%value%';\">
          <img src=\"./themes/[THEME]/images/delete.svg\" border=\"0\"></a>
        ");

    /*$this->app->Tpl->Set(EXTEND,"<input type=\"button\" value=\"User exportieren\" onclick=\"window.location.href='index.php?module=user&action=exportieren'\">");
    $this->app->Tpl->Parse($this->parsetarget,"rahmen70.tpl");

    $this->app->Tpl->Set(INHALT,"");
    $this->app->Tpl->Set(SUBSUBHEADING,"User exportiert");
    $table->Query("SELECT a.name, betrag, auswahl as typ, grund FROM user, adresse a WHERE user.adresse = a.id AND user.exportiert=1");
    $table->DisplayNew(INHALT, "Grund","noAction");
    */
    $this->app->Tpl->Set(EXTEND,"");
    $this->app->Tpl->Parse($this->parsetarget,"rahmen70.tpl");

  }



  public function Search()
  {
    //$this->app->Tpl->Set($this->parsetarget,"suchmaske");
    //$this->app->Table(
    //$table = new OrderTable("veranstalter");
    //$table->Heading(array('Name','Homepage','Telefon'));
  }


}
?>

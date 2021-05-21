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

class ObjGenAdresse_Accounts
{

  private  $id;
  private  $aktiv;
  private  $adresse;
  private  $bezeichnung;
  private  $art;
  private  $url;
  private  $benutzername;
  private  $passwort;
  private  $webid;
  private  $gueltig_ab;
  private  $gueltig_bis;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM adresse_accounts WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->aktiv=$result[aktiv];
    $this->adresse=$result[adresse];
    $this->bezeichnung=$result[bezeichnung];
    $this->art=$result[art];
    $this->url=$result[url];
    $this->benutzername=$result[benutzername];
    $this->passwort=$result[passwort];
    $this->webid=$result[webid];
    $this->gueltig_ab=$result[gueltig_ab];
    $this->gueltig_bis=$result[gueltig_bis];
  }

  public function Create()
  {
    $sql = "INSERT INTO adresse_accounts (id,aktiv,adresse,bezeichnung,art,url,benutzername,passwort,webid,gueltig_ab,gueltig_bis)
      VALUES('','{$this->aktiv}','{$this->adresse}','{$this->bezeichnung}','{$this->art}','{$this->url}','{$this->benutzername}','{$this->passwort}','{$this->webid}','{$this->gueltig_ab}','{$this->gueltig_bis}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE adresse_accounts SET
      aktiv='{$this->aktiv}',
      adresse='{$this->adresse}',
      bezeichnung='{$this->bezeichnung}',
      art='{$this->art}',
      url='{$this->url}',
      benutzername='{$this->benutzername}',
      passwort='{$this->passwort}',
      webid='{$this->webid}',
      gueltig_ab='{$this->gueltig_ab}',
      gueltig_bis='{$this->gueltig_bis}'
      WHERE (id='{$this->id}')";

    $this->app->DB->Update($sql);
  }

  public function Delete($id="")
  {
    if(is_numeric($id))
    {
      $this->id=$id;
    }
    else
      return -1;

    $sql = "DELETE FROM adresse_accounts WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->aktiv="";
    $this->adresse="";
    $this->bezeichnung="";
    $this->art="";
    $this->url="";
    $this->benutzername="";
    $this->passwort="";
    $this->webid="";
    $this->gueltig_ab="";
    $this->gueltig_bis="";
  }

  public function Copy()
  {
    $this->id = "";
    $this->Create();
  }

 /** 
   Mit dieser Funktion kann man einen Datensatz suchen 
   dafuer muss man die Attribute setzen nach denen gesucht werden soll
   dann kriegt man als ergebnis den ersten Datensatz der auf die Suche uebereinstimmt
   zurueck. Mit Next() kann man sich alle weiteren Ergebnisse abholen
   **/ 

  public function Find()
  {
    //TODO Suche mit den werten machen
  }

  public function FindNext()
  {
    //TODO Suche mit den alten werten fortsetzen machen
  }

 /** Funktionen um durch die Tabelle iterieren zu koennen */ 

  public function Next()
  {
    //TODO: SQL Statement passt nach meiner Meinung nach noch nicht immer
  }

  public function First()
  {
    //TODO: SQL Statement passt nach meiner Meinung nach noch nicht immer
  }

 /** dank dieser funktionen kann man die tatsaechlichen werte einfach 
  ueberladen (in einem Objekt das mit seiner klasse ueber dieser steht)**/ 

  function SetId($value) { $this->id=$value; }
  function GetId() { return $this->id; }
  function SetAktiv($value) { $this->aktiv=$value; }
  function GetAktiv() { return $this->aktiv; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetArt($value) { $this->art=$value; }
  function GetArt() { return $this->art; }
  function SetUrl($value) { $this->url=$value; }
  function GetUrl() { return $this->url; }
  function SetBenutzername($value) { $this->benutzername=$value; }
  function GetBenutzername() { return $this->benutzername; }
  function SetPasswort($value) { $this->passwort=$value; }
  function GetPasswort() { return $this->passwort; }
  function SetWebid($value) { $this->webid=$value; }
  function GetWebid() { return $this->webid; }
  function SetGueltig_Ab($value) { $this->gueltig_ab=$value; }
  function GetGueltig_Ab() { return $this->gueltig_ab; }
  function SetGueltig_Bis($value) { $this->gueltig_bis=$value; }
  function GetGueltig_Bis() { return $this->gueltig_bis; }

}

?>
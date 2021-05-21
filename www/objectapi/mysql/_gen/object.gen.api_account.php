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

class ObjGenApi_Account
{

  private  $id;
  private  $bezeichnung;
  private  $initkey;
  private  $importwarteschlange_name;
  private  $event_url;
  private  $remotedomain;
  private  $aktiv;
  private  $importwarteschlange;
  private  $cleanutf8;
  private  $uebertragung_account;
  private  $projekt;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM api_account WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->initkey=$result['initkey'];
    $this->importwarteschlange_name=$result['importwarteschlange_name'];
    $this->event_url=$result['event_url'];
    $this->remotedomain=$result['remotedomain'];
    $this->aktiv=$result['aktiv'];
    $this->importwarteschlange=$result['importwarteschlange'];
    $this->cleanutf8=$result['cleanutf8'];
    $this->uebertragung_account=$result['uebertragung_account'];
    $this->projekt=$result['projekt'];
  }

  public function Create()
  {
    $sql = "INSERT INTO api_account (id,bezeichnung,initkey,importwarteschlange_name,event_url,remotedomain,aktiv,importwarteschlange,cleanutf8,uebertragung_account,projekt)
      VALUES('','{$this->bezeichnung}','{$this->initkey}','{$this->importwarteschlange_name}','{$this->event_url}','{$this->remotedomain}','{$this->aktiv}','{$this->importwarteschlange}','{$this->cleanutf8}','{$this->uebertragung_account}','{$this->projekt}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE api_account SET
      bezeichnung='{$this->bezeichnung}',
      initkey='{$this->initkey}',
      importwarteschlange_name='{$this->importwarteschlange_name}',
      event_url='{$this->event_url}',
      remotedomain='{$this->remotedomain}',
      aktiv='{$this->aktiv}',
      importwarteschlange='{$this->importwarteschlange}',
      cleanutf8='{$this->cleanutf8}',
      uebertragung_account='{$this->uebertragung_account}',
      projekt='{$this->projekt}'
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

    $sql = "DELETE FROM api_account WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bezeichnung="";
    $this->initkey="";
    $this->importwarteschlange_name="";
    $this->event_url="";
    $this->remotedomain="";
    $this->aktiv="";
    $this->importwarteschlange="";
    $this->cleanutf8="";
    $this->uebertragung_account="";
    $this->projekt="";
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
  function SetBezeichnung($value) { $this->bezeichnung=$value; }
  function GetBezeichnung() { return $this->bezeichnung; }
  function SetInitkey($value) { $this->initkey=$value; }
  function GetInitkey() { return $this->initkey; }
  function SetImportwarteschlange_Name($value) { $this->importwarteschlange_name=$value; }
  function GetImportwarteschlange_Name() { return $this->importwarteschlange_name; }
  function SetEvent_Url($value) { $this->event_url=$value; }
  function GetEvent_Url() { return $this->event_url; }
  function SetRemotedomain($value) { $this->remotedomain=$value; }
  function GetRemotedomain() { return $this->remotedomain; }
  function SetAktiv($value) { $this->aktiv=$value; }
  function GetAktiv() { return $this->aktiv; }
  function SetImportwarteschlange($value) { $this->importwarteschlange=$value; }
  function GetImportwarteschlange() { return $this->importwarteschlange; }
  function SetCleanutf8($value) { $this->cleanutf8=$value; }
  function GetCleanutf8() { return $this->cleanutf8; }
  function SetUebertragung_Account($value) { $this->uebertragung_account=$value; }
  function GetUebertragung_Account() { return $this->uebertragung_account; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }

}

?>
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

class ObjGenTicket
{

  private  $id;
  private  $schluessel;
  private  $zeit;
  private  $projekt;
  private  $bearbeiter;
  private  $quelle;
  private  $status;
  private  $adresse;
  private  $kunde;
  private  $warteschlange;
  private  $mailadresse;
  private  $prio;
  private  $betreff;
  private  $zugewiesen;
  private  $inbearbeitung;
  private  $inbearbeitung_user;
  private  $firma;
  private  $notiz;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM ticket WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->schluessel=$result[schluessel];
    $this->zeit=$result[zeit];
    $this->projekt=$result[projekt];
    $this->bearbeiter=$result[bearbeiter];
    $this->quelle=$result[quelle];
    $this->status=$result[status];
    $this->adresse=$result[adresse];
    $this->kunde=$result[kunde];
    $this->warteschlange=$result[warteschlange];
    $this->mailadresse=$result[mailadresse];
    $this->prio=$result[prio];
    $this->betreff=$result[betreff];
    $this->zugewiesen=$result[zugewiesen];
    $this->inbearbeitung=$result[inbearbeitung];
    $this->inbearbeitung_user=$result[inbearbeitung_user];
    $this->firma=$result[firma];
    $this->notiz=$result[notiz];
  }

  public function Create()
  {
    $sql = "INSERT INTO ticket (id,schluessel,zeit,projekt,bearbeiter,quelle,status,adresse,kunde,warteschlange,mailadresse,prio,betreff,zugewiesen,inbearbeitung,inbearbeitung_user,firma,notiz)
      VALUES('','{$this->schluessel}','{$this->zeit}','{$this->projekt}','{$this->bearbeiter}','{$this->quelle}','{$this->status}','{$this->adresse}','{$this->kunde}','{$this->warteschlange}','{$this->mailadresse}','{$this->prio}','{$this->betreff}','{$this->zugewiesen}','{$this->inbearbeitung}','{$this->inbearbeitung_user}','{$this->firma}','{$this->notiz}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE ticket SET
      schluessel='{$this->schluessel}',
      zeit='{$this->zeit}',
      projekt='{$this->projekt}',
      bearbeiter='{$this->bearbeiter}',
      quelle='{$this->quelle}',
      status='{$this->status}',
      adresse='{$this->adresse}',
      kunde='{$this->kunde}',
      warteschlange='{$this->warteschlange}',
      mailadresse='{$this->mailadresse}',
      prio='{$this->prio}',
      betreff='{$this->betreff}',
      zugewiesen='{$this->zugewiesen}',
      inbearbeitung='{$this->inbearbeitung}',
      inbearbeitung_user='{$this->inbearbeitung_user}',
      firma='{$this->firma}',
      notiz='{$this->notiz}'
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

    $sql = "DELETE FROM ticket WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->schluessel="";
    $this->zeit="";
    $this->projekt="";
    $this->bearbeiter="";
    $this->quelle="";
    $this->status="";
    $this->adresse="";
    $this->kunde="";
    $this->warteschlange="";
    $this->mailadresse="";
    $this->prio="";
    $this->betreff="";
    $this->zugewiesen="";
    $this->inbearbeitung="";
    $this->inbearbeitung_user="";
    $this->firma="";
    $this->notiz="";
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
  function SetSchluessel($value) { $this->schluessel=$value; }
  function GetSchluessel() { return $this->schluessel; }
  function SetZeit($value) { $this->zeit=$value; }
  function GetZeit() { return $this->zeit; }
  function SetProjekt($value) { $this->projekt=$value; }
  function GetProjekt() { return $this->projekt; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetQuelle($value) { $this->quelle=$value; }
  function GetQuelle() { return $this->quelle; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetKunde($value) { $this->kunde=$value; }
  function GetKunde() { return $this->kunde; }
  function SetWarteschlange($value) { $this->warteschlange=$value; }
  function GetWarteschlange() { return $this->warteschlange; }
  function SetMailadresse($value) { $this->mailadresse=$value; }
  function GetMailadresse() { return $this->mailadresse; }
  function SetPrio($value) { $this->prio=$value; }
  function GetPrio() { return $this->prio; }
  function SetBetreff($value) { $this->betreff=$value; }
  function GetBetreff() { return $this->betreff; }
  function SetZugewiesen($value) { $this->zugewiesen=$value; }
  function GetZugewiesen() { return $this->zugewiesen; }
  function SetInbearbeitung($value) { $this->inbearbeitung=$value; }
  function GetInbearbeitung() { return $this->inbearbeitung; }
  function SetInbearbeitung_User($value) { $this->inbearbeitung_user=$value; }
  function GetInbearbeitung_User() { return $this->inbearbeitung_user; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetNotiz($value) { $this->notiz=$value; }
  function GetNotiz() { return $this->notiz; }

}

?>
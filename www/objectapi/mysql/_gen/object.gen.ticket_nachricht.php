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

class ObjGenTicket_Nachricht
{

  private  $id;
  private  $ticket;
  private  $verfasser;
  private  $bearbeiter;
  private  $mail;
  private  $zeit;
  private  $zeitausgang;
  private  $text;
  private  $textausgang;
  private  $betreff;
  private  $bemerkung;
  private  $medium;
  private  $versendet;
  private  $status;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM ticket_nachricht WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->ticket=$result[ticket];
    $this->verfasser=$result[verfasser];
    $this->bearbeiter=$result[bearbeiter];
    $this->mail=$result[mail];
    $this->zeit=$result[zeit];
    $this->zeitausgang=$result[zeitausgang];
    $this->text=$result[text];
    $this->textausgang=$result[textausgang];
    $this->betreff=$result[betreff];
    $this->bemerkung=$result[bemerkung];
    $this->medium=$result[medium];
    $this->versendet=$result[versendet];
    $this->status=$result[status];
  }

  public function Create()
  {
    $sql = "INSERT INTO ticket_nachricht (id,ticket,verfasser,bearbeiter,mail,zeit,zeitausgang,text,textausgang,betreff,bemerkung,medium,versendet,status)
      VALUES('','{$this->ticket}','{$this->verfasser}','{$this->bearbeiter}','{$this->mail}','{$this->zeit}','{$this->zeitausgang}','{$this->text}','{$this->textausgang}','{$this->betreff}','{$this->bemerkung}','{$this->medium}','{$this->versendet}','{$this->status}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE ticket_nachricht SET
      ticket='{$this->ticket}',
      verfasser='{$this->verfasser}',
      bearbeiter='{$this->bearbeiter}',
      mail='{$this->mail}',
      zeit='{$this->zeit}',
      zeitausgang='{$this->zeitausgang}',
      text='{$this->text}',
      textausgang='{$this->textausgang}',
      betreff='{$this->betreff}',
      bemerkung='{$this->bemerkung}',
      medium='{$this->medium}',
      versendet='{$this->versendet}',
      status='{$this->status}'
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

    $sql = "DELETE FROM ticket_nachricht WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->ticket="";
    $this->verfasser="";
    $this->bearbeiter="";
    $this->mail="";
    $this->zeit="";
    $this->zeitausgang="";
    $this->text="";
    $this->textausgang="";
    $this->betreff="";
    $this->bemerkung="";
    $this->medium="";
    $this->versendet="";
    $this->status="";
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
  function SetTicket($value) { $this->ticket=$value; }
  function GetTicket() { return $this->ticket; }
  function SetVerfasser($value) { $this->verfasser=$value; }
  function GetVerfasser() { return $this->verfasser; }
  function SetBearbeiter($value) { $this->bearbeiter=$value; }
  function GetBearbeiter() { return $this->bearbeiter; }
  function SetMail($value) { $this->mail=$value; }
  function GetMail() { return $this->mail; }
  function SetZeit($value) { $this->zeit=$value; }
  function GetZeit() { return $this->zeit; }
  function SetZeitausgang($value) { $this->zeitausgang=$value; }
  function GetZeitausgang() { return $this->zeitausgang; }
  function SetText($value) { $this->text=$value; }
  function GetText() { return $this->text; }
  function SetTextausgang($value) { $this->textausgang=$value; }
  function GetTextausgang() { return $this->textausgang; }
  function SetBetreff($value) { $this->betreff=$value; }
  function GetBetreff() { return $this->betreff; }
  function SetBemerkung($value) { $this->bemerkung=$value; }
  function GetBemerkung() { return $this->bemerkung; }
  function SetMedium($value) { $this->medium=$value; }
  function GetMedium() { return $this->medium; }
  function SetVersendet($value) { $this->versendet=$value; }
  function GetVersendet() { return $this->versendet; }
  function SetStatus($value) { $this->status=$value; }
  function GetStatus() { return $this->status; }

}

?>
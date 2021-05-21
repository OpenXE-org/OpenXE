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

class ObjGenDokumente
{

  private  $id;
  private  $adresse_from;
  private  $adresse_to;
  private  $typ;
  private  $von;
  private  $firma;
  private  $ansprechpartner;
  private  $an;
  private  $email_an;
  private  $firma_an;
  private  $adresse;
  private  $plz;
  private  $ort;
  private  $land;
  private  $datum;
  private  $betreff;
  private  $content;
  private  $signatur;
  private  $send_as;
  private  $email;
  private  $printer;
  private  $fax;
  private  $sent;
  private  $deleted;
  private  $created;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM dokumente WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->adresse_from=$result[adresse_from];
    $this->adresse_to=$result[adresse_to];
    $this->typ=$result[typ];
    $this->von=$result[von];
    $this->firma=$result[firma];
    $this->ansprechpartner=$result[ansprechpartner];
    $this->an=$result[an];
    $this->email_an=$result[email_an];
    $this->firma_an=$result[firma_an];
    $this->adresse=$result[adresse];
    $this->plz=$result[plz];
    $this->ort=$result[ort];
    $this->land=$result[land];
    $this->datum=$result[datum];
    $this->betreff=$result[betreff];
    $this->content=$result[content];
    $this->signatur=$result[signatur];
    $this->send_as=$result[send_as];
    $this->email=$result[email];
    $this->printer=$result[printer];
    $this->fax=$result[fax];
    $this->sent=$result[sent];
    $this->deleted=$result[deleted];
    $this->created=$result[created];
  }

  public function Create()
  {
    $sql = "INSERT INTO dokumente (id,adresse_from,adresse_to,typ,von,firma,ansprechpartner,an,email_an,firma_an,adresse,plz,ort,land,datum,betreff,content,signatur,send_as,email,printer,fax,sent,deleted,created)
      VALUES('','{$this->adresse_from}','{$this->adresse_to}','{$this->typ}','{$this->von}','{$this->firma}','{$this->ansprechpartner}','{$this->an}','{$this->email_an}','{$this->firma_an}','{$this->adresse}','{$this->plz}','{$this->ort}','{$this->land}','{$this->datum}','{$this->betreff}','{$this->content}','{$this->signatur}','{$this->send_as}','{$this->email}','{$this->printer}','{$this->fax}','{$this->sent}','{$this->deleted}','{$this->created}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE dokumente SET
      adresse_from='{$this->adresse_from}',
      adresse_to='{$this->adresse_to}',
      typ='{$this->typ}',
      von='{$this->von}',
      firma='{$this->firma}',
      ansprechpartner='{$this->ansprechpartner}',
      an='{$this->an}',
      email_an='{$this->email_an}',
      firma_an='{$this->firma_an}',
      adresse='{$this->adresse}',
      plz='{$this->plz}',
      ort='{$this->ort}',
      land='{$this->land}',
      datum='{$this->datum}',
      betreff='{$this->betreff}',
      content='{$this->content}',
      signatur='{$this->signatur}',
      send_as='{$this->send_as}',
      email='{$this->email}',
      printer='{$this->printer}',
      fax='{$this->fax}',
      sent='{$this->sent}',
      deleted='{$this->deleted}',
      created='{$this->created}'
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

    $sql = "DELETE FROM dokumente WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->adresse_from="";
    $this->adresse_to="";
    $this->typ="";
    $this->von="";
    $this->firma="";
    $this->ansprechpartner="";
    $this->an="";
    $this->email_an="";
    $this->firma_an="";
    $this->adresse="";
    $this->plz="";
    $this->ort="";
    $this->land="";
    $this->datum="";
    $this->betreff="";
    $this->content="";
    $this->signatur="";
    $this->send_as="";
    $this->email="";
    $this->printer="";
    $this->fax="";
    $this->sent="";
    $this->deleted="";
    $this->created="";
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
  function SetAdresse_From($value) { $this->adresse_from=$value; }
  function GetAdresse_From() { return $this->adresse_from; }
  function SetAdresse_To($value) { $this->adresse_to=$value; }
  function GetAdresse_To() { return $this->adresse_to; }
  function SetTyp($value) { $this->typ=$value; }
  function GetTyp() { return $this->typ; }
  function SetVon($value) { $this->von=$value; }
  function GetVon() { return $this->von; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetAnsprechpartner($value) { $this->ansprechpartner=$value; }
  function GetAnsprechpartner() { return $this->ansprechpartner; }
  function SetAn($value) { $this->an=$value; }
  function GetAn() { return $this->an; }
  function SetEmail_An($value) { $this->email_an=$value; }
  function GetEmail_An() { return $this->email_an; }
  function SetFirma_An($value) { $this->firma_an=$value; }
  function GetFirma_An() { return $this->firma_an; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetPlz($value) { $this->plz=$value; }
  function GetPlz() { return $this->plz; }
  function SetOrt($value) { $this->ort=$value; }
  function GetOrt() { return $this->ort; }
  function SetLand($value) { $this->land=$value; }
  function GetLand() { return $this->land; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetBetreff($value) { $this->betreff=$value; }
  function GetBetreff() { return $this->betreff; }
  function SetContent($value) { $this->content=$value; }
  function GetContent() { return $this->content; }
  function SetSignatur($value) { $this->signatur=$value; }
  function GetSignatur() { return $this->signatur; }
  function SetSend_As($value) { $this->send_as=$value; }
  function GetSend_As() { return $this->send_as; }
  function SetEmail($value) { $this->email=$value; }
  function GetEmail() { return $this->email; }
  function SetPrinter($value) { $this->printer=$value; }
  function GetPrinter() { return $this->printer; }
  function SetFax($value) { $this->fax=$value; }
  function GetFax() { return $this->fax; }
  function SetSent($value) { $this->sent=$value; }
  function GetSent() { return $this->sent; }
  function SetDeleted($value) { $this->deleted=$value; }
  function GetDeleted() { return $this->deleted; }
  function SetCreated($value) { $this->created=$value; }
  function GetCreated() { return $this->created; }

}

?>
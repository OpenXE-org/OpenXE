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

class ObjGenWebmail_Mails
{

  private  $id;
  private  $webmail;
  private  $subject;
  private  $sender;
  private  $cc;
  private  $bcc;
  private  $replyto;
  private  $plaintext;
  private  $htmltext;
  private  $empfang;
  private  $anhang;
  private  $gelesen;
  private  $checksum;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM webmail_mails WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->webmail=$result[webmail];
    $this->subject=$result[subject];
    $this->sender=$result[sender];
    $this->cc=$result[cc];
    $this->bcc=$result[bcc];
    $this->replyto=$result[replyto];
    $this->plaintext=$result[plaintext];
    $this->htmltext=$result[htmltext];
    $this->empfang=$result[empfang];
    $this->anhang=$result[anhang];
    $this->gelesen=$result[gelesen];
    $this->checksum=$result[checksum];
  }

  public function Create()
  {
    $sql = "INSERT INTO webmail_mails (id,webmail,subject,sender,cc,bcc,replyto,plaintext,htmltext,empfang,anhang,gelesen,checksum)
      VALUES('','{$this->webmail}','{$this->subject}','{$this->sender}','{$this->cc}','{$this->bcc}','{$this->replyto}','{$this->plaintext}','{$this->htmltext}','{$this->empfang}','{$this->anhang}','{$this->gelesen}','{$this->checksum}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE webmail_mails SET
      webmail='{$this->webmail}',
      subject='{$this->subject}',
      sender='{$this->sender}',
      cc='{$this->cc}',
      bcc='{$this->bcc}',
      replyto='{$this->replyto}',
      plaintext='{$this->plaintext}',
      htmltext='{$this->htmltext}',
      empfang='{$this->empfang}',
      anhang='{$this->anhang}',
      gelesen='{$this->gelesen}',
      checksum='{$this->checksum}'
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

    $sql = "DELETE FROM webmail_mails WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->webmail="";
    $this->subject="";
    $this->sender="";
    $this->cc="";
    $this->bcc="";
    $this->replyto="";
    $this->plaintext="";
    $this->htmltext="";
    $this->empfang="";
    $this->anhang="";
    $this->gelesen="";
    $this->checksum="";
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
  function SetWebmail($value) { $this->webmail=$value; }
  function GetWebmail() { return $this->webmail; }
  function SetSubject($value) { $this->subject=$value; }
  function GetSubject() { return $this->subject; }
  function SetSender($value) { $this->sender=$value; }
  function GetSender() { return $this->sender; }
  function SetCc($value) { $this->cc=$value; }
  function GetCc() { return $this->cc; }
  function SetBcc($value) { $this->bcc=$value; }
  function GetBcc() { return $this->bcc; }
  function SetReplyto($value) { $this->replyto=$value; }
  function GetReplyto() { return $this->replyto; }
  function SetPlaintext($value) { $this->plaintext=$value; }
  function GetPlaintext() { return $this->plaintext; }
  function SetHtmltext($value) { $this->htmltext=$value; }
  function GetHtmltext() { return $this->htmltext; }
  function SetEmpfang($value) { $this->empfang=$value; }
  function GetEmpfang() { return $this->empfang; }
  function SetAnhang($value) { $this->anhang=$value; }
  function GetAnhang() { return $this->anhang; }
  function SetGelesen($value) { $this->gelesen=$value; }
  function GetGelesen() { return $this->gelesen; }
  function SetChecksum($value) { $this->checksum=$value; }
  function GetChecksum() { return $this->checksum; }

}

?>
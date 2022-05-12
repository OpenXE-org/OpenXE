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

class ObjGenEmailbackup_Mails
{

  private  $id;
  private  $webmail;
  private  $subject;
  private  $sender;
  private  $action;
  private  $action_html;
  private  $empfang;
  private  $anhang;
  private  $gelesen;
  private  $checksum;
  private  $adresse;
  private  $spam;
  private  $antworten;
  private  $phpobj;
  private  $flattenedparts;
  private  $attachment;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM emailbackup_mails WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->webmail=$result[webmail];
    $this->subject=$result[subject];
    $this->sender=$result[sender];
    $this->action=$result[action];
    $this->action_html=$result[action_html];
    $this->empfang=$result[empfang];
    $this->anhang=$result[anhang];
    $this->gelesen=$result[gelesen];
    $this->checksum=$result[checksum];
    $this->adresse=$result[adresse];
    $this->spam=$result[spam];
    $this->antworten=$result[antworten];
    $this->phpobj=$result[phpobj];
    $this->flattenedparts=$result[flattenedparts];
    $this->attachment=$result[attachment];
  }

  public function Create()
  {
    $sql = "INSERT INTO emailbackup_mails (id,webmail,subject,sender,action,action_html,empfang,anhang,gelesen,checksum,adresse,spam,antworten,phpobj,flattenedparts,attachment)
      VALUES('','{$this->webmail}','{$this->subject}','{$this->sender}','{$this->action}','{$this->action_html}','{$this->empfang}','{$this->anhang}','{$this->gelesen}','{$this->checksum}','{$this->adresse}','{$this->spam}','{$this->antworten}','{$this->phpobj}','{$this->flattenedparts}','{$this->attachment}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE emailbackup_mails SET
      webmail='{$this->webmail}',
      subject='{$this->subject}',
      sender='{$this->sender}',
      action='{$this->action}',
      action_html='{$this->action_html}',
      empfang='{$this->empfang}',
      anhang='{$this->anhang}',
      gelesen='{$this->gelesen}',
      checksum='{$this->checksum}',
      adresse='{$this->adresse}',
      spam='{$this->spam}',
      antworten='{$this->antworten}',
      phpobj='{$this->phpobj}',
      flattenedparts='{$this->flattenedparts}',
      attachment='{$this->attachment}'
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

    $sql = "DELETE FROM emailbackup_mails WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->webmail="";
    $this->subject="";
    $this->sender="";
    $this->action="";
    $this->action_html="";
    $this->empfang="";
    $this->anhang="";
    $this->gelesen="";
    $this->checksum="";
    $this->adresse="";
    $this->spam="";
    $this->antworten="";
    $this->phpobj="";
    $this->flattenedparts="";
    $this->attachment="";
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
  function SetAction($value) { $this->action=$value; }
  function GetAction() { return $this->action; }
  function SetAction_Html($value) { $this->action_html=$value; }
  function GetAction_Html() { return $this->action_html; }
  function SetEmpfang($value) { $this->empfang=$value; }
  function GetEmpfang() { return $this->empfang; }
  function SetAnhang($value) { $this->anhang=$value; }
  function GetAnhang() { return $this->anhang; }
  function SetGelesen($value) { $this->gelesen=$value; }
  function GetGelesen() { return $this->gelesen; }
  function SetChecksum($value) { $this->checksum=$value; }
  function GetChecksum() { return $this->checksum; }
  function SetAdresse($value) { $this->adresse=$value; }
  function GetAdresse() { return $this->adresse; }
  function SetSpam($value) { $this->spam=$value; }
  function GetSpam() { return $this->spam; }
  function SetAntworten($value) { $this->antworten=$value; }
  function GetAntworten() { return $this->antworten; }
  function SetPhpobj($value) { $this->phpobj=$value; }
  function GetPhpobj() { return $this->phpobj; }
  function SetFlattenedparts($value) { $this->flattenedparts=$value; }
  function GetFlattenedparts() { return $this->flattenedparts; }
  function SetAttachment($value) { $this->attachment=$value; }
  function GetAttachment() { return $this->attachment; }

}

?>
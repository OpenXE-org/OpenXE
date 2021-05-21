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

class ObjGenInhalt
{

  private  $id;
  private  $sprache;
  private  $inhalt;
  private  $kurztext;
  private  $html;
  private  $title;
  private  $description;
  private  $keywords;
  private  $inhaltstyp;
  private  $sichtbarbis;
  private  $datum;
  private  $aktiv;
  private  $shop;
  private  $template;
  private  $finalparse;
  private  $navigation;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM inhalt WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->sprache=$result[sprache];
    $this->inhalt=$result[inhalt];
    $this->kurztext=$result[kurztext];
    $this->html=$result[html];
    $this->title=$result[title];
    $this->description=$result[description];
    $this->keywords=$result[keywords];
    $this->inhaltstyp=$result[inhaltstyp];
    $this->sichtbarbis=$result[sichtbarbis];
    $this->datum=$result[datum];
    $this->aktiv=$result[aktiv];
    $this->shop=$result[shop];
    $this->template=$result[template];
    $this->finalparse=$result[finalparse];
    $this->navigation=$result[navigation];
  }

  public function Create()
  {
    $sql = "INSERT INTO inhalt (id,sprache,inhalt,kurztext,html,title,description,keywords,inhaltstyp,sichtbarbis,datum,aktiv,shop,template,finalparse,navigation)
      VALUES('','{$this->sprache}','{$this->inhalt}','{$this->kurztext}','{$this->html}','{$this->title}','{$this->description}','{$this->keywords}','{$this->inhaltstyp}','{$this->sichtbarbis}','{$this->datum}','{$this->aktiv}','{$this->shop}','{$this->template}','{$this->finalparse}','{$this->navigation}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE inhalt SET
      sprache='{$this->sprache}',
      inhalt='{$this->inhalt}',
      kurztext='{$this->kurztext}',
      html='{$this->html}',
      title='{$this->title}',
      description='{$this->description}',
      keywords='{$this->keywords}',
      inhaltstyp='{$this->inhaltstyp}',
      sichtbarbis='{$this->sichtbarbis}',
      datum='{$this->datum}',
      aktiv='{$this->aktiv}',
      shop='{$this->shop}',
      template='{$this->template}',
      finalparse='{$this->finalparse}',
      navigation='{$this->navigation}'
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

    $sql = "DELETE FROM inhalt WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->sprache="";
    $this->inhalt="";
    $this->kurztext="";
    $this->html="";
    $this->title="";
    $this->description="";
    $this->keywords="";
    $this->inhaltstyp="";
    $this->sichtbarbis="";
    $this->datum="";
    $this->aktiv="";
    $this->shop="";
    $this->template="";
    $this->finalparse="";
    $this->navigation="";
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
  function SetSprache($value) { $this->sprache=$value; }
  function GetSprache() { return $this->sprache; }
  function SetInhalt($value) { $this->inhalt=$value; }
  function GetInhalt() { return $this->inhalt; }
  function SetKurztext($value) { $this->kurztext=$value; }
  function GetKurztext() { return $this->kurztext; }
  function SetHtml($value) { $this->html=$value; }
  function GetHtml() { return $this->html; }
  function SetTitle($value) { $this->title=$value; }
  function GetTitle() { return $this->title; }
  function SetDescription($value) { $this->description=$value; }
  function GetDescription() { return $this->description; }
  function SetKeywords($value) { $this->keywords=$value; }
  function GetKeywords() { return $this->keywords; }
  function SetInhaltstyp($value) { $this->inhaltstyp=$value; }
  function GetInhaltstyp() { return $this->inhaltstyp; }
  function SetSichtbarbis($value) { $this->sichtbarbis=$value; }
  function GetSichtbarbis() { return $this->sichtbarbis; }
  function SetDatum($value) { $this->datum=$value; }
  function GetDatum() { return $this->datum; }
  function SetAktiv($value) { $this->aktiv=$value; }
  function GetAktiv() { return $this->aktiv; }
  function SetShop($value) { $this->shop=$value; }
  function GetShop() { return $this->shop; }
  function SetTemplate($value) { $this->template=$value; }
  function GetTemplate() { return $this->template; }
  function SetFinalparse($value) { $this->finalparse=$value; }
  function GetFinalparse() { return $this->finalparse; }
  function SetNavigation($value) { $this->navigation=$value; }
  function GetNavigation() { return $this->navigation; }

}

?>
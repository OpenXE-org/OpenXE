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

class ObjGenShopexport_Kampange
{

  private  $id;
  private  $name;
  private  $banner;
  private  $unterbanner;
  private  $von;
  private  $bis;
  private  $link;
  private  $firma;
  private  $views;
  private  $clicks;
  private  $aktiv;
  private  $shop;
  private  $artikel;
  private  $aktion;
  private  $geloescht;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM shopexport_kampange WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->name=$result[name];
    $this->banner=$result[banner];
    $this->unterbanner=$result[unterbanner];
    $this->von=$result[von];
    $this->bis=$result[bis];
    $this->link=$result[link];
    $this->firma=$result[firma];
    $this->views=$result[views];
    $this->clicks=$result[clicks];
    $this->aktiv=$result[aktiv];
    $this->shop=$result[shop];
    $this->artikel=$result[artikel];
    $this->aktion=$result[aktion];
    $this->geloescht=$result[geloescht];
  }

  public function Create()
  {
    $sql = "INSERT INTO shopexport_kampange (id,name,banner,unterbanner,von,bis,link,firma,views,clicks,aktiv,shop,artikel,aktion,geloescht)
      VALUES('','{$this->name}','{$this->banner}','{$this->unterbanner}','{$this->von}','{$this->bis}','{$this->link}','{$this->firma}','{$this->views}','{$this->clicks}','{$this->aktiv}','{$this->shop}','{$this->artikel}','{$this->aktion}','{$this->geloescht}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE shopexport_kampange SET
      name='{$this->name}',
      banner='{$this->banner}',
      unterbanner='{$this->unterbanner}',
      von='{$this->von}',
      bis='{$this->bis}',
      link='{$this->link}',
      firma='{$this->firma}',
      views='{$this->views}',
      clicks='{$this->clicks}',
      aktiv='{$this->aktiv}',
      shop='{$this->shop}',
      artikel='{$this->artikel}',
      aktion='{$this->aktion}',
      geloescht='{$this->geloescht}'
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

    $sql = "DELETE FROM shopexport_kampange WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->name="";
    $this->banner="";
    $this->unterbanner="";
    $this->von="";
    $this->bis="";
    $this->link="";
    $this->firma="";
    $this->views="";
    $this->clicks="";
    $this->aktiv="";
    $this->shop="";
    $this->artikel="";
    $this->aktion="";
    $this->geloescht="";
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
  function SetName($value) { $this->name=$value; }
  function GetName() { return $this->name; }
  function SetBanner($value) { $this->banner=$value; }
  function GetBanner() { return $this->banner; }
  function SetUnterbanner($value) { $this->unterbanner=$value; }
  function GetUnterbanner() { return $this->unterbanner; }
  function SetVon($value) { $this->von=$value; }
  function GetVon() { return $this->von; }
  function SetBis($value) { $this->bis=$value; }
  function GetBis() { return $this->bis; }
  function SetLink($value) { $this->link=$value; }
  function GetLink() { return $this->link; }
  function SetFirma($value) { $this->firma=$value; }
  function GetFirma() { return $this->firma; }
  function SetViews($value) { $this->views=$value; }
  function GetViews() { return $this->views; }
  function SetClicks($value) { $this->clicks=$value; }
  function GetClicks() { return $this->clicks; }
  function SetAktiv($value) { $this->aktiv=$value; }
  function GetAktiv() { return $this->aktiv; }
  function SetShop($value) { $this->shop=$value; }
  function GetShop() { return $this->shop; }
  function SetArtikel($value) { $this->artikel=$value; }
  function GetArtikel() { return $this->artikel; }
  function SetAktion($value) { $this->aktion=$value; }
  function GetAktion() { return $this->aktion; }
  function SetGeloescht($value) { $this->geloescht=$value; }
  function GetGeloescht() { return $this->geloescht; }

}

?>
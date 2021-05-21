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

class ObjGenShopnavigation
{

  private  $id;
  private  $bezeichnung;
  private  $position;
  private  $parent;
  private  $bezeichnung_en;
  private  $plugin;
  private  $pluginparameter;
  private  $shop;
  private  $target;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM shopnavigation WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result[id];
    $this->bezeichnung=$result[bezeichnung];
    $this->position=$result[position];
    $this->parent=$result[parent];
    $this->bezeichnung_en=$result[bezeichnung_en];
    $this->plugin=$result[plugin];
    $this->pluginparameter=$result[pluginparameter];
    $this->shop=$result[shop];
    $this->target=$result[target];
  }

  public function Create()
  {
    $sql = "INSERT INTO shopnavigation (id,bezeichnung,position,parent,bezeichnung_en,plugin,pluginparameter,shop,target)
      VALUES('','{$this->bezeichnung}','{$this->position}','{$this->parent}','{$this->bezeichnung_en}','{$this->plugin}','{$this->pluginparameter}','{$this->shop}','{$this->target}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE shopnavigation SET
      bezeichnung='{$this->bezeichnung}',
      position='{$this->position}',
      parent='{$this->parent}',
      bezeichnung_en='{$this->bezeichnung_en}',
      plugin='{$this->plugin}',
      pluginparameter='{$this->pluginparameter}',
      shop='{$this->shop}',
      target='{$this->target}'
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

    $sql = "DELETE FROM shopnavigation WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bezeichnung="";
    $this->position="";
    $this->parent="";
    $this->bezeichnung_en="";
    $this->plugin="";
    $this->pluginparameter="";
    $this->shop="";
    $this->target="";
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
  function SetPosition($value) { $this->position=$value; }
  function GetPosition() { return $this->position; }
  function SetParent($value) { $this->parent=$value; }
  function GetParent() { return $this->parent; }
  function SetBezeichnung_En($value) { $this->bezeichnung_en=$value; }
  function GetBezeichnung_En() { return $this->bezeichnung_en; }
  function SetPlugin($value) { $this->plugin=$value; }
  function GetPlugin() { return $this->plugin; }
  function SetPluginparameter($value) { $this->pluginparameter=$value; }
  function GetPluginparameter() { return $this->pluginparameter; }
  function SetShop($value) { $this->shop=$value; }
  function GetShop() { return $this->shop; }
  function SetTarget($value) { $this->target=$value; }
  function GetTarget() { return $this->target; }

}

?>
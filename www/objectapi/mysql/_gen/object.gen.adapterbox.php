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

class ObjGenAdapterbox
{

  private  $id;
  private  $bezeichnung;
  private  $verwendenals;
  private  $baudrate;
  private  $model;
  private  $seriennummer;
  private  $ipadresse;
  private  $netmask;
  private  $gateway;
  private  $dns;
  private  $dhcp;
  private  $wlan;
  private  $ssid;
  private  $passphrase;
  private  $letzteverbindung;
  private  $tmpip;

  public $app;            //application object 

  public function __construct($app)
  {
    $this->app = $app;
  }

  public function Select($id)
  {
    if(is_numeric($id))
      $result = $this->app->DB->SelectArr("SELECT * FROM adapterbox WHERE (id = '$id')");
    else
      return -1;

$result = $result[0];

    $this->id=$result['id'];
    $this->bezeichnung=$result['bezeichnung'];
    $this->verwendenals=$result['verwendenals'];
    $this->baudrate=$result['baudrate'];
    $this->model=$result['model'];
    $this->seriennummer=$result['seriennummer'];
    $this->ipadresse=$result['ipadresse'];
    $this->netmask=$result['netmask'];
    $this->gateway=$result['gateway'];
    $this->dns=$result['dns'];
    $this->dhcp=$result['dhcp'];
    $this->wlan=$result['wlan'];
    $this->ssid=$result['ssid'];
    $this->passphrase=$result['passphrase'];
    $this->letzteverbindung=$result['letzteverbindung'];
    $this->tmpip=$result['tmpip'];
  }

  public function Create()
  {
    $sql = "INSERT INTO adapterbox (id,bezeichnung,verwendenals,baudrate,model,seriennummer,ipadresse,netmask,gateway,dns,dhcp,wlan,ssid,passphrase,letzteverbindung,tmpip)
      VALUES('','{$this->bezeichnung}','{$this->verwendenals}','{$this->baudrate}','{$this->model}','{$this->seriennummer}','{$this->ipadresse}','{$this->netmask}','{$this->gateway}','{$this->dns}','{$this->dhcp}','{$this->wlan}','{$this->ssid}','{$this->passphrase}','{$this->letzteverbindung}','{$this->tmpip}')"; 

    $this->app->DB->Insert($sql);
    $this->id = $this->app->DB->GetInsertID();
  }

  public function Update()
  {
    if(!is_numeric($this->id))
      return -1;

    $sql = "UPDATE adapterbox SET
      bezeichnung='{$this->bezeichnung}',
      verwendenals='{$this->verwendenals}',
      baudrate='{$this->baudrate}',
      model='{$this->model}',
      seriennummer='{$this->seriennummer}',
      ipadresse='{$this->ipadresse}',
      netmask='{$this->netmask}',
      gateway='{$this->gateway}',
      dns='{$this->dns}',
      dhcp='{$this->dhcp}',
      wlan='{$this->wlan}',
      ssid='{$this->ssid}',
      passphrase='{$this->passphrase}',
      letzteverbindung='{$this->letzteverbindung}',
      tmpip='{$this->tmpip}'
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

    $sql = "DELETE FROM adapterbox WHERE (id='{$this->id}')";
    $this->app->DB->Delete($sql);

    $this->id="";
    $this->bezeichnung="";
    $this->verwendenals="";
    $this->baudrate="";
    $this->model="";
    $this->seriennummer="";
    $this->ipadresse="";
    $this->netmask="";
    $this->gateway="";
    $this->dns="";
    $this->dhcp="";
    $this->wlan="";
    $this->ssid="";
    $this->passphrase="";
    $this->letzteverbindung="";
    $this->tmpip="";
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
  function SetVerwendenals($value) { $this->verwendenals=$value; }
  function GetVerwendenals() { return $this->verwendenals; }
  function SetBaudrate($value) { $this->baudrate=$value; }
  function GetBaudrate() { return $this->baudrate; }
  function SetModel($value) { $this->model=$value; }
  function GetModel() { return $this->model; }
  function SetSeriennummer($value) { $this->seriennummer=$value; }
  function GetSeriennummer() { return $this->seriennummer; }
  function SetIpadresse($value) { $this->ipadresse=$value; }
  function GetIpadresse() { return $this->ipadresse; }
  function SetNetmask($value) { $this->netmask=$value; }
  function GetNetmask() { return $this->netmask; }
  function SetGateway($value) { $this->gateway=$value; }
  function GetGateway() { return $this->gateway; }
  function SetDns($value) { $this->dns=$value; }
  function GetDns() { return $this->dns; }
  function SetDhcp($value) { $this->dhcp=$value; }
  function GetDhcp() { return $this->dhcp; }
  function SetWlan($value) { $this->wlan=$value; }
  function GetWlan() { return $this->wlan; }
  function SetSsid($value) { $this->ssid=$value; }
  function GetSsid() { return $this->ssid; }
  function SetPassphrase($value) { $this->passphrase=$value; }
  function GetPassphrase() { return $this->passphrase; }
  function SetLetzteverbindung($value) { $this->letzteverbindung=$value; }
  function GetLetzteverbindung() { return $this->letzteverbindung; }
  function SetTmpip($value) { $this->tmpip=$value; }
  function GetTmpip() { return $this->tmpip; }

}

?>
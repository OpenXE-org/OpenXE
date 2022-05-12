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

class ApiErrorException extends RuntimeException {}
class EmptyResultException extends RuntimeException {}

class Umkreissuche
{
  /** @var ApplicationCore $app */
  var $app;

  // Deutschland Mitte als Default
  var $myLat = 51.133481;
  var $myLng = 10.018343;

  /**
   * Umkreissuche constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    //$this->PLZ();

    $this->app->ActionHandler("list","UmkreissucheList");
    $this->app->ActionHandler("activate","UmkreissucheActivate");
    $this->app->ActionHandler("einstellungen","UmkreissucheEinstellungen");
    $this->app->DefaultActionHandler("list");

    $this->app->ActionHandlerListen($app);

  }

  function UmkreissucheEinstellungen()
  {
    $this->UmkreissucheMenu();

    $this->app->YUI->AutoSaveKonfiguration('apikey','umkreissuche_einstellungen_apikey');
    $this->app->YUI->AutoSaveKonfiguration('googleapikey','umkreissuche_einstellungen_googleapikey');
    $apikey = $this->app->erp->GetKonfiguration('umkreissuche_einstellungen_apikey');
    $googleapikey = $this->app->erp->GetKonfiguration('umkreissuche_einstellungen_googleapikey');
    $this->app->Tpl->Set('APIKEY',$apikey);
    $this->app->Tpl->Set('GOOGLEAPIKEY',$googleapikey);

    $this->app->Tpl->Parse('PAGE','umkreissuche_einstellungen.tpl');
  }

  function UmkreissucheMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=umkreissuche&action=list","&Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=umkreissuche&action=einstellungen","Einstellungen");
  }

  
  function UmkreissucheActivate(){
    $query = $this->app->DB->SelectArr("SELECT * FROM prozessstarter WHERE parameter='umkreissuche'");
    if(!empty($query)) {
      $this->app->DB->Update("UPDATE prozessstarter SET aktiv=1 WHERE parameter='umkreissuche'");
    }
    else {
      $this->app->DB->Insert("INSERT INTO prozessstarter (bezeichnung, art, typ, parameter, aktiv) VALUES ('Umkreissuche', 'periodisch', 'cronjob', 'umkreissuche', 1)");
    }
    $this->UmkreissucheList();
  }

  function UmkreissucheList()
  {
    $this->app->YUI->AutoComplete('projekt','projektname',1);
    $this->app->YUI->AutoComplete('adr','adresse');
    $this->app->YUI->AutoSaveUserParameter('projekt','umkreissuche_list_projekt');

    $googleapikey = $this->app->erp->GetKonfiguration('umkreissuche_einstellungen_googleapikey');
    $this->app->Tpl->Set('GOOGLEAPIKEY',$googleapikey);

    $this->apiKey = $this->app->erp->GetKonfiguration('umkreissuche_einstellungen_googleapikey');
    $this->searchRadius = $this->app->Secure->GetPOST('radius');
    if($this->searchRadius == ''){
      $this->searchRadius = 10;
    }

    $this->tableName = 'adresse';

    $this->UmkreissucheMenu();

    $this->plz = $this->app->Secure->GetPOST('plz');
    $tmp = $this->app->Secure->GetPOST('adr');
    $tmp1 = explode(' ',$tmp);
    $this->adId = trim($tmp1[0]);

    if(strlen($this->plz) > 1){
      try {
        $plzundland = explode(' ', $this->plz);
        if(count($plzundland) > 1) {
          $res = $this->plzToCoord($plzundland[0], $plzundland[1]);
        }
        else {
          $res = $this->plzToCoord($plzundland[0]);
        }
        //Such nach Postleitzahl
        $this->myLat = $res['lat'];
        $this->myLng = $res['lng'];
        $this->adId = '';
      }
      catch (EmptyResultException $e) {
        $this->app->Tpl->Set('MESSAGE', '<div class="warning">' . htmlspecialchars($e->getMessage()) . '</div>');
      }
      catch (ApiErrorException $e) {
        $this->app->Tpl->Set('MESSAGE', '<div class="error">' . htmlspecialchars($e->getMessage()) . '</div>');
      }

    }
    elseif($this->adId != '' && $this->adId != '1'){
      //Such nach Adresse
      $searchCoords = $this->app->DB->SelectArr("SELECT plz, strasse, lat, lng FROM adresse WHERE id=" . $this->adId . " LIMIT 1");
      $searchCoords = $searchCoords[0];
      if($searchCoords['lat'] == '' || $searchCoords['lat'] == 'NULL' || $searchCoords['lat'] == null){
        //Keine Angaben -> Keine Suche
      }
      else{
        $this->myLat = $searchCoords['lat'];
        $this->myLng = $searchCoords['lng'];
      }
    }//else{
      //Such nach gar nichts 
    //}


    $projekt = $this->app->User->GetParameter('umkreissuche_list_projekt');
    $this->app->Tpl->Set('PROJEKT',$projekt);

    $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' AND abkuerzung!='' LIMIT 1");
    $subwhere = '';
    if($projekt > 0){
      $subwhere = " AND projekt='$projekt' ";
    }

    //$query = "SELECT name, strasse, SUBSTRING(a.plz,1,2) as plz, CONCAT('<a href=\"\">Info (',COUNT(a.id),')</a>') as anz FROM adresse a WHERE a.kundennummer!='' $subwhere GROUP BY SUBSTRING(a.plz,1,2)";
 
    if(($this->adId != '' && $this->adId != '1') || $this->plz){ 
      $this->adresses = array();
      $this->query('Kunde: ', $subwhere);
    }

    $this->DisplayMapData('TAB1');

    $vorhanden = $this->app->DB->Select("SELECT COUNT(*) FROM " . $this->tableName . " WHERE (lat <> 0)  AND (lng <> 0) AND geloescht!=1 ".$subwhere);
    $gesamt = $this->app->DB->SELECT("SELECT COUNT(*) FROM " . $this->tableName." WHERE geloescht!=1 ".$subwhere);

    $offen = $gesamt - $vorhanden;

    $this->displayInfo("Bei $vorhanden von $gesamt Adressen sind die Geodaten vorhanden.");

    if(isset($this->info)){
      $this->app->Tpl->Set('INFO', $this->info);
    }
    $this->app->Tpl->Parse('PAGE', 'umkreissuche_list.tpl');
  }

  //$target: in welcher ausgabe die daten angezeigt werden sollen
  function DisplayMapData($target, $hoehe="1000px", $breite="100%", $plz="plz", $anz="anz")
  {
    if(isset($this->errorMsg)){
      $this->app->Tpl->Set('ERRORMSG', $this->errorMsg);
      $entry = '[]';
    }
    else {
      //var_dump($this->adresses);
      $entry = "[";
      //echo "<pre>" . var_dump($coordArray) . "</pre>";
      $first = true;

      foreach($this->adresses as $adress){      
        //$coord = $this->adressToCoord($plzkey, 'Germany');
        if(!$first){
          $entry .= ',';
        }
        $first = false;
        $entry .= json_encode($adress);
      }

      $entry .= "]";
    }
    //echo $entry;
    if($target !== 'return' && $target !== 'echo') {
      $this->app->Tpl->Set('CITIES', $entry);
      //echo "$entry<br>";

      $this->app->Tpl->Set('WIDTH', "$breite");
      $this->app->Tpl->Set('HEIGHT', "$hoehe");
      //$center =  $this->adressToCoord($this->searchPlz, 'Germany');
      //echo "center:";
      //var_dump($center);
      //echo $this->searchPlz . "<br>";
      $this->app->Tpl->Set('LAT', $this->myLat); 
      $this->app->Tpl->Set('LNG', $this->myLng); 
      $this->app->Tpl->Set('PLZ', $this->plz);
      if($this->adId > 0)
        $this->app->Tpl->Set('ADR', $this->app->DB->Select("SELECT CONCAT(id,' ',name) FROM adresse WHERE id='".$this->adId."' LIMIT 1"));
//echo "UHUH";exit;
      $this->app->Tpl->Set('RADIUS', $this->searchRadius);
    }
    elseif($target === 'echo'){
      echo $entry;
      $this->app->ExitXentral();
    }
    else {
      return $entry;
    }
  }


  
  public function query($bezeichnung, $subwhereprojekt="", $plz="strasse", $anz="name")
  {

    $key1 = $plz;
    $key2 = $anz;
    $subwhereid = '';
    if($this->adId){
      $subwhereid = "AND id != $this->adId"; 
    }

    $latFrom = deg2rad($this->myLat);
    $lonFrom = deg2rad($this->myLng);
    $latTo = 'RADIANS(lat)';
    $lonTo = 'RADIANS(lng)';

    $latDelta = "RADIANS(lat - ".$this->myLat.")";
    $lonDelta = "RADIANS(lng - ".$this->myLng.")";
    $sqrt = "sin($latDelta / 2)*sin($latDelta / 2)+   
       cos($latFrom) * cos($latTo) * sin($lonDelta/2) * sin($lonDelta/2)";
    $dist = " 6371000 * 2 * 
    atan2(
      sqrt($sqrt), sqrt(1 - $sqrt)
    )
    AS distance ";
    $query1 = "SELECT $dist, id, plz, lat, lng, $key1, $key2 
      FROM " . $this->tableName . " 
      WHERE (lat <> 0 OR lng <> 0) AND lat IS NOT NULL AND lng IS NOT NULL $subwhereid $subwhereprojekt";

    $dataArr = $this->app->DB->SelectArr($query1);
    if($this->app->DB->error()) {
      $query1 = "SELECT id, plz, lat, lng, $key1, $key2 
      FROM " . $this->tableName . " 
      WHERE (lat <> 0 OR lng <> 0) AND lat IS NOT NULL AND lng IS NOT NULL  $subwhereid $subwhereprojekt";
      $dataArr = $this->app->DB->SelectArr($query1);
    }
    foreach($dataArr as $data) {
      if(!empty($data[$key1])) {
        $lat = $data['lat'];
        $lng = $data['lng'];
        if($lat != 0 && $lng != 0 && $lat != 'null' && $lng != 'null') {
          if(isset($data['dist'])) {
            $dist = $data['dist'];
          }
          else{
            $dist = $this->latLngDistance2($this->myLat, $this->myLng, $lat, $lng);
          }
          if($dist < ($this->searchRadius * 1000)){
            $adress = array(
                'lat' => $lat,
                'lng' => $lng,
                'id' => $data['id'],
                'info' => $bezeichnung . $data[$key2] . " (" . ((int)($dist / 1000)) . " km)",
                'multiple' => 'false'
                );
            $this->adresses[] = $adress;
          }
          //$this->dataArr[$data[$key1]][] = $bezeichnung . $data[$key2] . "(" . $data['plz'] . ")";
          //$this->dataArr[$data[$key1]][] = $data['id'];
        }
      }
    }
/*
    $query2 = "SELECT name, id, plz, strasse FROM " . $this->tableName . " WHERE land='DE' AND (lat IS NULL OR lat = '' OR lng IS NULL OR lng = '') AND plz != '' AND plz like '" . $this->searchPLZ[0] . $this->searchPLZ[1] . "%' GROUP BY(name) ORDER BY plz";
    //echo "\n$query2<br>";
    $dataArr2 = $this->app->DB->SelectArr($query2);
    if(sizeof($dataArr2) > 0){
      $plzs = array();
      $coordArray = array();
      $new = 0;
      foreach($dataArr2 as $data){
        if($this->geocode){
          $coords = $this->adressToCoord($data['strasse'], $data['plz'], 'Germany', $data['id']);
        }
        if($this->geocode && !$coords->error && !$coords->skip){
          $lat = $coords->coords['lat'];
          $lng = $coords->coords['lng'];
          if($lat != 0 && $lng != 0){
            $dist = $this->latLngDistance2($this->myLat, $this->myLng, $lat, $lng);  
            $adress = array(
                'lat' => $lat,
                'lng' => $lng,
                'id' => $data['id'],
                'info' => $bezeichnung . $data[$key2] . " (" . (int)($dist / 1000) . ") km",
                'multiple' => 'false'
                );
            $this->adresses[] = $adress;
            $new++;
            $this->app->DB->Update("UPDATE " . $this->tableName . " set lat='$lat', lng='$lng' where id='" . $data['id'] . "';");
          }
        }else{
          $plzs[] = $data['plz'];
        }
      }
      $conv = new PlzConverter();
      //var_dump($plzs);
      $result = $conv->convertArrayToCoords($plzs);
      //var_dump($result);
      foreach($dataArr2 as $data){
        $code = '<b onclick="window.open(\'index.php?module=adresse&action=edit&id=' . $data['id'] . '\');">' . $data['name'] . "</b>";
        if(in_array($data['plz'], array_keys($coordArray))){
          $coordArray[$data['plz']][] = $code;
        }else{
          $coordArray[$data['plz']] = array($code);
        }
      }
      $shortInfoAcc = '';
      $longInfoAcc = '';
      $oldLat = 0;
      $oldLng = 0;
      $c = 0;
      foreach($coordArray as $key => $entry){
        //echo "$key: " . print_r($entry, true) . "<br><br>";
        $info = implode("<br>", $entry);
        $lat = $result[$key][0];
        $lng = $result[$key][1]; 
        $dist = $this->latLngDistance2($this->myLat, $this->myLng, $lat, $lng);  
        if($dist > ($this->searchRadius * 1000)){
          //echo "t: $dist my: " . $this->myLat . " " . $this->myLng . "lat: $lat lng: $lng<br>";

        continue;} 
        if($result[$key][0] != $oldLat || $result[$key][1] != $oldLng){
          $adress = array(
              'lat' => $lat,
              'lng' => $lng,
              'id' => -1,
              'info' => $shortInfoAcc . ($shortInfoAcc == '' ? '' : "<br><br>") . "Unter dieser PLZ (" . $key . "): " . sizeof($entry),
              'expanded' => $longInfoAcc . ($longInfoAcc == '' ? '' : "<br><br>") . "Unter dieser PLZ (" . $key . "):<br>" . $info,
              'count' => strval($c + sizeof($entry))
              );
          //var_dump($c);
          //echo "<br>";
          $c = 0;
          $this->adresses[] = $adress;
          $shortInfoAcc = $longInfoAcc = '';
          $oldLat = $result[$key][0];
          $oldLng = $result[$key][1];
        }else{ 
          $shortInfoAcc .= ($shortInfoAcc == '' ? '' : "<br><br>") . "Unter dieser PLZ (" . $key . "): " . sizeof($entry);
          $longInfoAcc .= ($longInfoAcc == '' ? '' : "<br><br>") . "Unter dieser PLZ (" . $key . "):<br>" . $info;
          $c += sizeof($entry);
        }
      }
    }
*/
    if(count($this->adresses) > 200){
      $this->errorMsg = 'Zu viele Treffer (' . count($this->adresses) . '), bitte Suchradius verringern';
    }
    if($new > 0){
      $this->displayInfo("$new adressen neu konvertiert");
    }
  }

  function displayInfo($info){
    if(!isset($this->info)){
      $this->info = $info;
      return;
    }
    $this->info .= "<br>" . $info;
  }

  function latLngDistance($lat1, $lat2, $lng1, $lng2){
    if($lat1 == $lat2 && $lng1 == $lng2){
      return 0;
    }
    $dltLat = abs($lat1 - $lat2);
    $dltLng = abs($lng1 - $lng2);

    $distLat = 110.574 * $dltLat;
    $distLng = 111.320 * $dltLng;

    $dist = sqrt($distLat * $distLat + $distLng * $distLng);
    return $dist;
  }

  function latLngDistance2($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000)
  {
    // convert from degrees to radians
    $latFrom = deg2rad($latitudeFrom);
    $lonFrom = deg2rad($longitudeFrom);
    $latTo = deg2rad($latitudeTo);
    $lonTo = deg2rad($longitudeTo);

    $latDelta = $latTo - $latFrom;
    $lonDelta = $lonTo - $lonFrom;

    $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
          cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
    return $angle * $earthRadius;
  }

  /**
   * @param string $plz
   * @param string $land
   *
   * @throws RuntimeException Wenn Google-Maps-API einen Fehler liefert
   *
   * @return array
   */
  function plzToCoord($plz, $land = 'DE')
  {
    $antwort = array();
    //Postleitzahl auffüllen
    if($land === 'DE'){
      while (strlen($plz) < 5) {
        $plz .= '9';
      }
    }

    $suchanfrage = 'https://maps.googleapis.com/maps/api/geocode/json?address='.$plz.'+'.$land.'&key='.$this->app->erp->GetKonfiguration("umkreissuche_einstellungen_googleapikey");

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $suchanfrage);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $result = json_decode(curl_exec($ch));

    if ($result->status === 'ZERO_RESULTS') {
      throw new EmptyResultException('Die Suchanfrage hat keine Ergebnisse zurückgeliefert.');
    }
    if ($result->status !== 'OK') {
      throw new ApiErrorException(sprintf(
        'Fehler beim Abrufen der Daten aus der Google-Maps-API. Status=%s Message=%s',
        $result->status,
        $result->error_message
      ));
    }

    foreach ($result->results as $key => $value) {
      //Für jede zurückgegebene Adresse, durchsuche Komponenten nach Länderkennung
      $cadress_components = count($value->address_components);
      for ($i=0; $i < $cadress_components; $i++) {
        if($value->address_components[$i]->short_name == $land){
          //Bei Fund der Länderkennung Geodatenspeichern und zurückgeben
          $antwort = array('lat' => $value->geometry->location->lat, 'lng' => $value->geometry->location->lng);
          break 2;
        }
      }
    }
    if (empty($antwort)) {
      throw new EmptyResultException('Die Suchanfrage hat keine passenden Ergebnisse zurückgeliefert.');
    }

    return $antwort;
  }
}

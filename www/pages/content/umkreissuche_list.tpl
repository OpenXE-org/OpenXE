<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
    [MESSAGE]
    [TAB1]
    <form method="POST">
      
<div class="row">
  <div class="row-height">
  
  <div class="col-xs-3 col-md-1 col-md-height">
  <div class="inside inside-full-height">
    <fieldset>
      <legend>{|Projekt|}</legend>
      <table>
        <tr>
          <td><input type="text" id="projekt" name="projekt" value="[PROJEKT]" size="40"/></td>
        </tr>
      </table>
    </fieldset>
  </div>
  </div>
  <div class="col-xs-3 col-md-1 col-md-height">
  <div class="inside inside-full-height">
    <fieldset>
      <legend>{|Filter|}</legend>
      <table>
        <tr>
          <td>Suchadresse:</td><td><input type="text" id="adr" name="adr" id="adr" value="[ADR]" size="40"/> </td> 
        </tr>
        <tr>
        <td colspan="2"><center>oder</center></td>
        </tr>
        <tr>
          <td>PLZ:</td><td><input type="text" id="plz" name="plz" id="plz" value="[PLZ]" size="20"/> </td> 
        </tr>
      </table>
    </fieldset>
  </div>
  </div>
  <div class="col-xs-3 col-md-1 col-md-height">
  <div class="inside inside-full-height">
    <fieldset>
      <legend>{|Umkreis|}</legend>
      <table>
        <tr>
          <td><input type="text" id="radius" name="radius" value="[RADIUS]" size="8" /> in Km</td>
        </tr>
      </table>
    </fieldset>
  </div>
  </div>
  </div>
  </div>
<input type="submit" value="suchen" style="width:10em;"> [INFO]


    </form>
    <div id="map" style="height:[HEIGHT]; margin:10px; ">
    </div>
    [TAB1NEXT]

  <script>
    // Error-Methode überschreiben, damit Meldung verarbeitet werden kann
    console.error = function (message) {
      var alertMessage =
              'Es besteht ein Problem mit der Google Maps API. ' +
              'Bitte überprüfen Sie ihre Einstellungen: https://xentral.biz/helpdesk/kurzanleitung-umkreissuche' +
              '\n\n' + message;
      alert(alertMessage);
      throw Error(message);
    };

    /**
     * @see https://developers.google.com/maps/documentation/javascript/events#auth-errors
     */
    function gm_authFailure() {
      alert('Es besteht ein Problem mit der Google Maps API. Bitte überprüfen Sie ihre Einstellungen: https://xentral.biz/helpdesk/kurzanleitung-umkreissuche');
    }

    function initMap(){
      var errorMsg = "[ERRORMSG]";
      if(errorMsg != ""){
        alert(errorMsg);
        return;
      }
      var cities = [CITIES];
      var map = new google.maps.Map(document.getElementById('map'), {
        center: new google.maps.LatLng([LAT], [LNG]),
        zoom: 10
      });
      //map.addListener('zoom_changed', function(){
      //  document.title = "zoom: " + map.getZoom();
      //});

      cities.forEach(function(entry) {
      if(entry.id == -1){
      var coordInfoWindow = new google.maps.InfoWindow({
             content: entry.expanded
          });
          var position = new google.maps.LatLng(entry.lat, entry.lng);
          var div = document.createElement('div');
          div.innerHTML = entry.info;
          var marker = new google.maps.Marker({
            position: position,
            map: map,
            title: entry.count
          });
          marker.addListener('click', function(){
            coordInfoWindow.open(map, marker);
          });
        }else{
          var position = new google.maps.LatLng(entry.lat, entry.lng);
          var div = document.createElement('div');
          div.innerHTML = entry.info;
          div.onclick = function(){
            window.open("index.php?module=adresse&action=edit&id=" + entry.id);
          };
          var coordInfoWindow = new google.maps.InfoWindow({
            content: div
          });
          var marker = new google.maps.Marker({
            position: position,
            map: map,
            title: entry.info
          });
          marker.addListener('click', function(){
            coordInfoWindow.open(map, marker);
          }); 
        }
        //coordInfoWindow.setPosition(new google.maps.LatLng(entry.lat, entry.lng));
        //var div = document.createElement('div');
        //div.innerHTML = entry.info;
        //if(entry.id != -1){
        //  div.onclick = function(){
        //    window.open("/wawision/16.4/www/index.php?module=adresse&action=edit&id=" + entry.id);
        //  };
        //}else{
        //  div.onclick = function(){
        //    div.innerHTML = entry.expanded;
        //  };
        //}
        //coordInfoWindow.setContent(div);
        //coordInfoWindow.open(map);
      });
      var centerMarker = new google.maps.InfoWindow();
      centerMarker.setPosition(new google.maps.LatLng([LAT], [LNG]));
      centerMarker.setContent("SIE");
      centerMarker.open(map);

      var circle = new google.maps.Circle({
        map: map,
        radius: [RADIUS] * 1000,    // 10 miles in metres
        fillColor: '#FFFFFF'
      });
      circle.bindTo('center', centerMarker, 'position');
/*
      map.addListener('zoom_changed', function() {
        coordInfoWindow.setContent(createInfoWindowContent(augsburg[0], map.getZoom(), augsburg[1]));
          coordInfoWindow.setContent(createInfoWindowContent(muenchen[0], map.getZoom(), muenchen[1]));
          coordInfoWindow.open(map);
      });
*/
    }
    var TILE_SIZE = 256;

    function createInfoWindowContent(latLng, zoom,numberof) {
      
      var scale = 1 << zoom;

      var worldCoordinate = project(latLng);

      var pixelCoordinate = new google.maps.Point(
        Math.floor(worldCoordinate.x * scale),
        Math.floor(worldCoordinate.y * scale));

      var tileCoordinate = new google.maps.Point(
        Math.floor(worldCoordinate.x * scale / TILE_SIZE),
        Math.floor(worldCoordinate.y * scale / TILE_SIZE));

      return [
      numberof// + ' St&uuml;ck'
      ].join('<br>');
    }

    // The mapping between latitude, longitude and pixels is defined by the web
    // mercator projection.
    function project(latLng) {
      var siny = Math.sin(latLng.lat() * Math.PI / 180);

      // Truncating to 0.9999 effectively limits latitude to 89.189. This is
      // about a third of a tile past the edge of the world tile.
      siny = Math.min(Math.max(siny, -0.9999), 0.9999);

      return new google.maps.Point(
        TILE_SIZE * (0.5 + latLng.lng() / 360),
        TILE_SIZE * (0.5 - Math.log((1 + siny) / (1 - siny)) / (4 * Math.PI)));
    }
  </script>
  <script src="https://maps.googleapis.com/maps/api/js?key=[GOOGLEAPIKEY]&callback=initMap" async defer></script>
</div>

<!-- tab view schließen -->
</div>



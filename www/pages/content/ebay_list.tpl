<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT1]</a></li>
        <li><a href="#tabs-2">[TABTEXT2]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->

<div id="tabs-1">
  [MESSAGE]

<div class="row">
<div class="row-height">
  <div class="col-xs-12 col-md-10 col-md-height">
  <div class="inside inside-full-height">
    <fieldset><legend>Filter</legend>
      <table width="100%">
        <tr>
          <td>
            <input type="checkbox" name="auktionenohneartikelausblenden" id="auktionenohneartikelausblenden" onchange="positionenmoredata()" [AUKTIONENOHNEARTIKELAUSBLENDEN]><label for="auktionenohneartikelausblenden">Auktionen ohne zugehörige Artikel ausblenden</label>
          </td>
          <td>
            <input type="checkbox" name="nurinaktiveauktionen" id="nurinaktiveauktionen" onchange="positionenmoredata()" [NURINAKTIVEAUKTIONEN]><label for="nurinaktiveauktionen">Nur abgelaufene Auktionen anzeigen</label>
          </td>
          <td>
            <input type="checkbox" name="nuraktiveauktionen" id="nuraktiveauktionen" onchange="positionenmoredata()" [NURAKTIVEAUKTIONEN]><label for="nuraktiveauktionen">Nur aktive Auktionen anzeigen</label>
          </td>
        </tr>
      </table>
    </fieldset>
  </div>
  </div>
  <div class="col-xs-12 col-md-2 col-md-height">
  <div class="inside inside-full-height">
  <fieldset>
    <legend>Aktionen</legend>
    <table width="100%">
      <tr>
        <td>
          <input class="btnGreenNew" type="button" name="auktionendownload" id="auktionendownload" value="Anzeige aktualisieren" onclick="auktionendownload();">
        </td>
      </tr>
    </table>
  </fieldset>
  </div>
  </div>
</div>
</div>

<div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-md-10 col-md-height">
  <div class="inside_white inside-full-height">

    <fieldset class="white">
      <legend>&nbsp;</legend>
      [TAB1]
        <fieldset>
          <legend>{|Stapelverarbeitung|}</legend>
          <input type="checkbox" value="1" id="allemarkieren" name="allemarkieren" onchange="markierealle();" /><label for="allemarkieren">&nbsp;alle markieren&nbsp;</label>
          <input class="btnGreen" type="button" name="auktionenaktualisieren" id="auktionenaktualisieren" value="Auktionen aktualisieren / neu einstellen" onclick="auktionenaktualisieren();">
        </fieldset>
      [TAB1NEXT]
    </fieldset>

  </div>
  </div>
</div>
</div>
</div>

  <div id="tabs-2">
    [MESSAGE]

    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-10 col-md-height">
          <div class="inside inside-full-height">
            <fieldset><legend>Filter</legend>
              <table width="100%">
                <tr>
                  <input type="checkbox" id="leereeintraegeausblenden" name="leereeintraegeausblenden" onchange="positionengebuehrmoredata()" [LEEREEINTRAEGEAUSBLENDEN]> <label>Leere Einträge ausblenden</label>
                </tr>
                <tr>
                  <td>&nbsp;</td>
                </tr>
                <tr>
                  <td>
                    <b>GRUPPIERUNG</b>
                  </td>
                </tr>
                <tr>
                  <td>
                    <input type="radio" name="gruppierung" id="nichtgruppieren" checked="checked" value="nicht" onchange="positionengebuehrmoredata()"> <label for="nichtgruppieren">Nicht gruppieren</label>
                  </td>
                  <td>
                    <input type="radio" name="gruppierung" id="typgruppieren" value="typ" onchange="positionengebuehrmoredata()"><label for="typgruppieren">Nach Typ</label>
                  </td>
                  <td>
                    <input type="radio" name="gruppierung" id="monatgruppieren" value="monat" onchange="positionengebuehrmoredata()"><label for="monatgruppieren">Nach Monat</label>
                  </td>
                  <td>
                    <input type="radio" name="gruppierung" id="artikelgruppieren"  value="artikel" onchange="positionengebuehrmoredata()"><label for="artikelgruppieren">Nach Artikel</label>
                  </td>
                </tr>
              </table>
            </fieldset>
          </div>
        </div>
        <div class="col-xs-12 col-md-2 col-md-height">
          <div class="inside inside-full-height">
            <fieldset>
              <legend>Aktionen</legend>
              <table width="100%">
                <tr>
                  <td>
                    <input class="btnGreenNew" type="button" name="gebuehrendownload" id="gebuehrendownload" value="Gebühren aktualisieren" onclick="gebuehrendownload();">
                  </td>
                </tr>
              </table>
            </fieldset>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-10 col-md-height">
          <div class="inside_white inside-full-height">

            <fieldset class="white">
              <legend>&nbsp;</legend>
              [TAB2]
              [TAB2NEXT]
            </fieldset>

          </div>
        </div>
      </div>
    </div>
  </div>

  </div>
<!-- tab view schließen -->
</div>


<script>
  function auktionenaktualisieren(){
    eaids = [];
    var checkboxen = $(":input[id^='ebayauktionen_']:checked");
    for (var i = 0; i < checkboxen.length; i++) {
      eaidstmp = checkboxen[i].id.split('_');
      eaids.push(eaidstmp[1]);
    }

    if(eaids.length < 1){
      alert("Es wurde kein Artikel ausgewählt.");
    }else{
      document.getElementById('auktionenaktualisieren').disabled = true;
      $.ajax({
        url: 'index.php?module=ebay&action=list&cmd=auktionenaktualisieren',
        type: 'POST',
        dataType: 'json',
        data: {
          eaids: eaids
        },
        success: function(data) {
          alert('Auktionen wurden aktualisiert.');
          //updateLiveTable();
          document.getElementById('auktionenaktualisieren').disabled = false;
        },
        beforeSend: function() {

        }
      });
    }
  }

  function auktionendownload(){
    document.getElementById('auktionendownload').disabled = true;
    $.ajax({
      url: 'index.php?module=ebay&action=list&cmd=auktionendownload',
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        alert('Auktionen wurden aktualisiert.');
        updateLiveTable();
        document.getElementById('auktionendownload').disabled = false;
      },
      beforeSend: function() {

      }
    });
  }

  function markierealle()
  {
    var checked = $('#allemarkieren').prop('checked');
    $('#auktionen input').prop('checked', checked);
  }

  function positionenmoredata(){
    oMoreData1auktionen = $('#auktionenohneartikelausblenden').prop("checked")?1:0;
    oMoreData2auktionen = $('#nurinaktiveauktionen').prop("checked")?1:0;
    oMoreData3auktionen = $('#nuraktiveauktionen').prop("checked")?1:0;
    oMoreData4auktionen = 1; //"erzwingt" Anwendung der Filter fuer Verwendung in Kombination mit gespeicherter Konfiguration
    updateLiveTable();
  }

  function updateLiveTable() {
    var oTableL = $('#auktionen').dataTable();
    oTableL.fnFilter('a');
    oTableL.fnFilter('');
  }

  function gebuehrendownload(){
    document.getElementById('gebuehrendownload').disabled = true;
    $.ajax({
      url: 'index.php?module=ebay&action=list&cmd=gebuehrendownload',
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        alert('Gebühren wurden aktualisiert.');
        updateLiveTable();
        document.getElementById('gebuehrendownload').disabled = false;
      },
      beforeSend: function() {

      }
    });
  }

  function positionengebuehrmoredata(){
    oMoreData1gebuehren = $('#leereeintraegeausblenden').prop("checked")?1:0;
    oMoreData2gebuehren = $("input[type='radio'][name='gruppierung']:checked").val();
    oMoreData4gebuehren = 1;
    updateGebuehrLiveTable();
  }

  function updateGebuehrLiveTable() {
    var oTableL = $('#gebuehren').dataTable();
    oTableL.fnFilter('a');
    oTableL.fnFilter('');
  }
</script>
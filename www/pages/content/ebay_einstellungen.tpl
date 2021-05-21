<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
        <li><a href="#tabs-2">[TABTEXT2]</a></li>
        <li><a href="#tabs-3">[TABTEXT3]</a></li>
        <li><a href="#tabs-4">[TABTEXT4]</a></li>
        <li><a href="#tabs-5">[TABTEXT5]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
  <div id="tabs-1">
    <div id="templateedit" style="display:none" title="Template">
      <input type="hidden" name="templateid" id="templateid" />
      <fieldset><legend>Template</legend>
        <table>
          <tr>
            <td width="100em;"> </td><td width="100%"> </td>
          </tr>
          <tr>
            <td>
              Bezeichnung:</td>
              <td><input type="text" name="bezeichnung" id="bezeichnung" value=""><br />
            </td>
          </tr>
          <tr>
            <td colspan="2">
              <textarea name="templatetext" id="templatetext" cols="160" rows="20"></textarea>
            </td>
          </tr>
          <tr>
            <td>
              Aktiv:
            </td>
            <td>
              <input type="checkbox" name="aktiv" id="aktiv" checked="" />
            </td>
          </tr>
          <tr>
            <td>
              Wildcards: 
            </td>
            <td>
              {EAN}, {NUMMER}, {HERSTELLER}, {HERSTELLERNUMMER}, {HERKUNFTSLAND}, {ZOLLTARIFNUMMER}, {LISTINGID}, {PREIS}, {WAEHRUNG}, {LAENGE}, {BREITE}, {HOEHE}, {GEWICHT}, {EINHEIT}, {PSEUDOPREIS}, {ZUSTAND}, {FIRMA}, {EBAYUSERID}, {EIGENSCHAFT_Eigenschaftname_XX}, {ARTIKELNAME_XX}, {KURZTEXT_XX}, {ARTIKELBESCHREIBUNG_XX}, {ARTIKELSHOPTEXT_XX}, {METATITEL_XX}, {METADESCRIPTION_XX}, {METAKEYWORDS_XX}, {FREIFELD1_XX} ... {FREIFELD40_XX}, {BILDURL1} ... {BILDURL12};
            </td>
          </tr>
        </table>
      </fieldset>
    </div>
      
     [MESSAGE]
     <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-10 col-md-height">
          <div class="inside_white inside-full-height">
            <fieldset class="white">
              <legend>&nbsp;</legend>
              [TAB1]
              [TAB1NEXT]
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
                    <input class="btnGreenNew" type="button" name="neuedit" value="&#10010; Neues Template anlegen" onclick="neuedit(0);">
                  </td>
                </tr>
              </table>
            </fieldset>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="tabs-2">
    <div id="bupoedit" style="display:none" title="Rahmenbedingung">
      <input type="hidden" name="bupoid" id="bupoid" />
      <fieldset><legend>Einstellungen</legend>
        <table>
          <tr>
            <td width="100em;"> </td><td width="100%"> </td>
          </tr>
          <tr>
            <td>
              Bezeichnung:</td>
              <td><input type="text" name="bupobezeichnung" id="bupobezeichnung" value="" size="35"><br />
            </td>
          </tr>
          <tr>
            <td>
              Standard:
            </td>
            <td>
              <input type="checkbox" name="bupostandard" id="bupostandard" checked="" />
            </td>
          </tr>
          <tr>
            <td>
              Aktiv:
            </td>
            <td>
              <input type="checkbox" name="bupoaktiv" id="bupoaktiv" checked="" />
            </td>
          </tr>
        </table>
      </fieldset>
    </div>

    [MESSAGE]
    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-10 col-md-height">
          <div class="inside_white inside-full-height">
            <fieldset class="white">
              <legend>&nbsp;</legend>
              <table>
                <tr>
                  <td>
                    Importerschnittstelle:
                  </td>
                  <td>
                    <select name="ebayshops2" id="ebayshops2" onchange="ebayshopschange2();">[EBAYSHOPS]</select>
                  </td>
                </tr>
              </table>
              [TAB2]
              [TAB2NEXT]
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
                    <input class="btnGreenNew" type="button" name="rahmenbedingungendownload" id="rahmenbedingungendownload" value="Rahmenbedingungen aktualisieren" onclick="downloadbupo();">
                  </td>
                </tr>
              </table>
            </fieldset>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div id="tabs-3">
    <div id="zuweisungedit" style="display:none" title="Artiklezuweisung">
      <input type="hidden" name="zuweisungid" id="zuweisungid" />
      <fieldset><legend>Einstellungen</legend>
        <table>
          <tr>
            <td width="100em;"> </td><td width="100%"> </td>
          </tr>
          <tr>
            <td>
              Bezeichnung:
            </td>
            <td>
              <input type="text" name="zuweisungbezeichnung" id="zuweisungbezeichnung" size="80em" disabled="">
            </td>
          </tr>
          <tr>
            
          </tr>
          <tr>
            <td>
              Artikel:</td>
              <td><input type="text" name="zuweisungartikel" id="zuweisungartikel"  value=""><br />
            </td>
          </tr>
          <tr>
            <td>
              Ignorieren:
            </td>
            <td>
              <input type="checkbox" name="zuweisungerledigt" id="zuweisungerledigt" checked="" />
            </td>
          </tr>
        </table>
      </fieldset>
    </div>


  [MESSAGE]
    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-10 col-md-height">
          <div class="inside_white inside-full-height">
            <fieldset class="white">
              <legend>&nbsp;</legend>
              <table>
                <tr>
                  <td>
                    <label for="ebayshops3">Importerschnittstelle:</label>
                  </td>
                  <td>
                    <select name="ebayshops3" id="ebayshops3" onchange="ebayshopschange3();">[EBAYSHOPS]</select>
                  </td>
                </tr><tr>
                  <td>
                    <label for="ebayshops3ignorierteausblenden">Ignorierte Listings ausblenden:</label>
                  </td>
                  <td>
                     <input type="checkbox" id="ebayshops3ignorierteausblenden" onchange="ebayshopschange3();">
                  </td>
                </tr><tr>
                  <td>
                    <label for="ebayshops3zugeordneteausblenden">Zugeordnete Listings ausblenden:</label>
                  </td>
                  <td>
                     <input type="checkbox" id="ebayshops3zugeordneteausblenden" onchange="ebayshopschange3();">
                  </td>
                </tr>
              </table>
              [TAB3]
              [TAB3NEXT]
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
                    <input class="btnGreenNew" type="button" name="artikeldownload" id="artikeldownload" value="Artikelliste aktualisieren" onclick="artikeldownload();">
                  </td>
                </tr>
                <tr>
                  <td>
                    <input class="btnGreenNew" type="button" name="artikelzuordnen" id="artikelzuordnen" value="Artikel automatisch zuordnen" onclick="artikelzuordnen();">
                  </td>
                </tr>
              </table>
            </fieldset>
          </div>
        </div>
      </div>
    </div>
  </div>

<div id="tabs-4">
    <div id="versandedit" style="display:none" title="Versandzuweisung">
      <input type="hidden" name="versandid" id="versandid" />
      <fieldset><legend>Einstellungen</legend>
        <table>
          <tr>
            <td width="100em;"></td><td width="100%"> </td>
          </tr>
          <tr>
            <td>
              Versandart:
            </td>
            <td>
              <SELECT id="versandart">[VERSANDART]</SELECT>
            </td>
          </tr>
          <tr>
            <td>
              Carrier überschreiben:
            </td>
            <td>
              <input type="text" name="carrierueberschreiben" id="carrierueberschreiben" checked="" />
            </td>
          </tr>
        </table>
      </fieldset>
    </div>

  [MESSAGE]
    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-10 col-md-height">
          <div class="inside_white inside-full-height">
            <fieldset class="white">
              <legend>&nbsp;</legend>
              <table>
                <tr>
                  <td>
                    Importerschnittstelle:
                  </td>
                  <td>
                    <select name="ebayshops4" id="ebayshops4" onchange="ebayshopschange4();">[EBAYSHOPS]</select>
                  </td>
                </tr>
              </table>
              [TAB4]
              [TAB4NEXT]
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
                    <input class="btnGreenNew" type="button" name="versanddownload" id="versanddownload" value="Shipping Carrier herunterladen" onclick="holeversand();">
                  </td>
                </tr>
              </table>
            </fieldset>
          </div>
        </div>
      </div>
    </div>
</div>

<div id="tabs-5">
  [MESSAGE]
    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-10 col-md-height">
          <div class="inside_white inside-full-height">
            <fieldset class="white">
              <legend>&nbsp;</legend>
              <table>
                <tr>
                  <td>
                    Importerschnittstelle:
                  </td>
                  <td>
                    <select name="ebayshops5" id="ebayshops5" onchange="ebayshopschange5();">[EBAYSHOPS]</select>
                  </td>
                </tr>
              </table>
              [TAB5]
              [TAB5NEXT]
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
                    <input class="btnGreenNew" type="button" name="storedownload" id="storedownload" value="Storekategorien herunterladen" onclick="holestore();">
                  </td>
                </tr>
              </table>
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
$(document).ready(function() {
  $("#templateedit").dialog({
  modal: true,
  bgiframe: true,
  closeOnEscape:false,
  minWidth:1200,
  autoOpen: false,
  buttons: {
    ABBRECHEN: function() {
      $(this).dialog('close');
    },
    SPEICHERN: function() {
      templatespeichern();
    }
  }
  });

  $("#templateedit").dialog({
    close: function( event, ui ){}
  });

  $("#bupoedit").dialog({
  modal: true,
  bgiframe: true,
  closeOnEscape:false,
  minWidth:400,
  autoOpen: false,
  buttons: {
    ABBRECHEN: function() {
      $(this).dialog('close');
    },
    SPEICHERN: function() {
      bupospeichern();
    }
  }
  });

  $("#bupoedit").dialog({
    close: function( event, ui ){}
  });

  $("#zuweisungedit").dialog({
  modal: true,
  bgiframe: true,
  closeOnEscape:false,
  minWidth:800,
  autoOpen: false,
  buttons: {
    ABBRECHEN: function() {
      $(this).dialog('close');
    },
    SPEICHERN: function() {
      zuweisungspeichern();
    }
  }
  });

  $("#zuweisungedit").dialog({
    close: function( event, ui ){}
  });

  $("#versandedit").dialog({
  modal: true,
  bgiframe: true,
  closeOnEscape:false,
  minWidth:800,
  autoOpen: false,
  buttons: {
    ABBRECHEN: function() {
      $(this).dialog('close');
    },
    ZUWEISEN: function() {
      versandzuweisungspeichern();
    }
  }
  });

  $("#versandedit").dialog({
    close: function( event, ui ){}
  });

});

  function ebayshopschange2(){
    oMoreData1rahmenbedingungen=$('#ebayshops2').val();
    $('#rahmenbedingungen').dataTable().fnFilter('A',1,0,0);
  }
  function ebayshopschange3(){
    oMoreData1artikelzuordnungen=$('#ebayshops3').val();
    oMoreData2artikelzuordnungen = $('#ebayshops3ignorierteausblenden').prop("checked") ? 1 : 0;
    oMoreData3artikelzuordnungen = $('#ebayshops3zugeordneteausblenden').prop("checked") ? 1 : 0;
    $('#artikelzuordnungen').dataTable().fnFilter('A',1,0,0);
  }
  function ebayshopschange4(){
    oMoreData1versand=$('#ebayshops4').val();
    $('#versand').dataTable().fnFilter('A',1,0,0);
  }
  function ebayshopschange5(){
    oMoreData1store=$('#ebayshops5').val();
    $('#store').dataTable().fnFilter('A',1,0,0);
  }

  function holestore(){
    document.getElementById('storedownload').disabled = true;
    var e = document.getElementById("ebayshops5");
    var shopid = e.options[e.selectedIndex].value;
    $.ajax({
      url: 'index.php?module=ebay&action=einstellungen&cmd=holestore&id='+shopid,
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        alert('Storekategorien wurden heruntergeladen.');
        updateLiveTable(5);
        document.getElementById('storedownload').disabled = false;
      },
      beforeSend: function() {

      }
    });
  }

  function holeversand(){
    document.getElementById('versanddownload').disabled = true;
    var e = document.getElementById("ebayshops4");
    var shopid = e.options[e.selectedIndex].value;
    $.ajax({
      url: 'index.php?module=ebay&action=einstellungen&cmd=holeversand&id='+shopid,
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        alert('Versanddienstleister wurden heruntergeladen.');
        updateLiveTable(4);
        document.getElementById('versanddownload').disabled = false;
      },
      beforeSend: function() {

      }
    });
  }
  function neueditversandzuordnung(nr){
    var e = document.getElementById("ebayshops4");
    var shopid = e.options[e.selectedIndex].value;
    $.ajax({
      url: 'index.php?module=ebay&action=einstellungen&cmd=getversand&id='+nr+'&shop='+shopid,
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        $('#versandid').val(nr);
        if(data.status == '1'){
          document.getElementById("versandart").value =data.id;
          $('#carrierueberschreiben').val(data.customcarrier);
        }else{
          document.getElementById("versandart").value = 1;
          $('#carrierueberschreiben').val('');
        }
        $('#versandedit').dialog('open');
      },
      beforeSend: function() {

      }
    });
  }
  function versandzuweisungspeichern(){
    var e = document.getElementById("ebayshops4");
    var shopid = e.options[e.selectedIndex].value;
    var e = document.getElementById("versandart");
    var versandartid = e.options[e.selectedIndex].value;
    customcarrier = $('#carrierueberschreiben').val();

    $.ajax({
      url: 'index.php?module=ebay&action=einstellungen&cmd=speichereversandzuordnung&id='+$('#versandid').val()+'&shop='+shopid+'&versandart='+versandartid+'&customcarrier='+customcarrier,
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        $('#versandedit').dialog('close');
        updateLiveTable(4);
      },
      beforeSend: function() {

      }
    });
  }



  function artikeldownload(){
    document.getElementById('artikeldownload').disabled = true;
    var e = document.getElementById("ebayshops3");
    var shopid = e.options[e.selectedIndex].value;
    $.ajax({
      url: 'index.php?module=ebay&action=einstellungen&cmd=artikeldownload&id='+shopid,
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        alert('Listingdownload abgeschlossen.');
        updateLiveTable(3);
        document.getElementById('artikeldownload').disabled = false;
      },
      beforeSend: function() {

      }
    });
  }
  function artikelzuordnen(){
    var e = document.getElementById("ebayshops3");
    var shopid = e.options[e.selectedIndex].value;
    document.getElementById('artikelzuordnen').disabled = true;
    $.ajax({
      url: 'index.php?module=ebay&action=einstellungen&cmd=artikelzuordnen&id='+shopid,
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        alert('Artikelaktualisierung abgeschlossen.');
        document.getElementById('artikelzuordnen').disabled = false;
        updateLiveTable(3);
      },
      beforeSend: function() {

      }
    });
  }

  function neueditartikelzuordnung(nr)
  {
    var e = document.getElementById("ebayshops3");
    var shopid = e.options[e.selectedIndex].value;
    $.ajax({
      url: 'index.php?module=ebay&action=einstellungen&cmd=getzuweisung&id='+nr+'&shop='+shopid,
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {

        $('#zuweisungid').val(data.id);
        $('#zuweisungbezeichnung').val(data.bezeichnung);
        
        if(data.erledigt == 1){
          $('#zuweisungedit').find('#zuweisungerledigt').prop("checked", true);  
        }else{
          $('#zuweisungedit').find('#zuweisungerledigt').prop("checked", false);  
        }
        $('#zuweisungartikel').val(data.nummer);
        $('#zuweisungedit').dialog('open');
      },
      beforeSend: function() {

      }
    });
  }

  function zuweisungspeichern(){
    $.ajax({
      url: 'index.php?module=ebay&action=einstellungen&cmd=savezuweisung',
      type: 'POST',
      dataType: 'json',
      data: {
        id: $('#zuweisungid').val(),  
        artikel: $('#zuweisungartikel').val(),
        erledigt: $('#zuweisungerledigt').prop("checked")?1:0
      },
      success: function(data) {
        $("#zuweisungedit").dialog('close'); 
        updateLiveTable(3);
       },
      beforeSend: function() {

      }
    });
  }  


  function neuedit(nr)
  {
    if(nr == 0){
      $('#templateid').val('0');
      $('#templatetext').val('');
      $('#bezeichnung').val('');
      $('#templateedit').find('#aktiv').prop("checked", true);  

      $("#templateedit").dialog('open');
    }else{
      $.ajax({
        url: 'index.php?module=ebay&action=einstellungen&cmd=gettemplate&id='+nr,
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function(data) {
          $('#templateid').val(data.id);
          if(data.aktiv == 1){
            $('#templateedit').find('#aktiv').prop("checked", true);  
          }else{
            $('#templateedit').find('#aktiv').prop("checked", false);  
          }
          $('#templatetext').val(data.template);
          $('#bezeichnung').val(data.bezeichnung);
          $('#templateedit').dialog('open');
        },
        beforeSend: function() {

        }
      });
    } 
  }

  function neueditbupo(nr)
  {
    $.ajax({
      url: 'index.php?module=ebay&action=einstellungen&cmd=getrahmenbedingung&id='+nr,
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        $('#bupoid').val(data.id);
        if(data.aktiv == 1){
          $('#bupoedit').find('#bupoaktiv').prop("checked", true);  
        }else{
          $('#bupoedit').find('#bupoaktiv').prop("checked", false);  
        }
        if(data.defaultwert == 1){
          $('#bupoedit').find('#bupostandard').prop("checked", true);  
        }else{
          $('#bupoedit').find('#bupostandard').prop("checked", false);  
        }
        $('#bupobezeichnung').val(data.bezeichnung);
        $('#bupoedit').dialog('open');
      },
      beforeSend: function() {

      }
    });
  }

  function bupospeichern(){
    $.ajax({
      url: 'index.php?module=ebay&action=einstellungen&cmd=saverahmenbedingung',
      type: 'POST',
      dataType: 'json',
      data: {
        id: $('#bupoid').val(),  
        bezeichnung: $('#bupobezeichnung').val(),
        aktiv: $('#bupoaktiv').prop("checked")?1:0,
        standard: $('#bupostandard').prop("checked")?1:0
      },
      success: function(data) {
        if(data == 'fail'){
          alert("Bezeichnung darf nicht leer sein.");
        }else{
          $("#bupoedit").dialog('close'); 
          updateLiveTable(2);
        }
       },
      beforeSend: function() {

      }
    });
  }


  function templatespeichern(){
    $.ajax({
      url: 'index.php?module=ebay&action=einstellungen&cmd=savetemplate',
      type: 'POST',
      dataType: 'json',
      data: {
        id: $('#templateid').val(),  
        bezeichnung: $('#bezeichnung').val(),
        template: $('#templatetext').val(),
        aktiv: $('#aktiv').prop("checked")?1:0
      },
      success: function(data) {
        if(data == 'fail'){
          alert("Bezeichnung darf nicht leer sein.");
        }else{
          $("#templateedit").dialog('close'); 
          updateLiveTable(1);
        }
       },
      beforeSend: function() {

      }
    });
  }


  function deleteversandzuordnung(nr)
  {
    if(!confirm("Soll die Zuordnung wirklich gelöscht werden?")) return false;
    $.ajax({
        url: 'index.php?module=ebay&action=einstellungen&cmd=deleteversandzuordnung&id='+nr,
        data: { 
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
          updateLiveTable(4);
        }
    });
  }
  function deleteeintrag(nr)
  {
    if(!confirm("Soll das Template wirklich gelöscht werden?")) return false;
    $.ajax({
        url: 'index.php?module=ebay&action=einstellungen&cmd=deletetemplate&id='+nr,
        data: { 
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
          updateLiveTable(1);
        }
    });
  }
  function deleteeintragbupo(nr)
  {
    if(!confirm("Soll die Rahmenbediungen wirklich gelöscht werden?")) return false;
    $.ajax({
        url: 'index.php?module=ebay&action=einstellungen&cmd=deleterahmenbedingung&id='+nr,
        data: { 
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
          updateLiveTable(2);
        }
    });
  }


  function downloadbupo(){
    document.getElementById('rahmenbedingungendownload').disabled = true;
    var e = document.getElementById("ebayshops2");
    var shopid = e.options[e.selectedIndex].value;
    
    $.ajax({
        url: 'index.php?module=ebay&action=einstellungen&cmd=erneuererahmenbedingungen&id='+shopid,
        data: {
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
          updateLiveTable(2);
          alert("Die Rahmenbedingungen wurden aktualisiert.");
          document.getElementById('rahmenbedingungendownload').disabled = false;
        }
    });
  }

  function updateLiveTable(id) {
    if(id == 1){
      var oTableL = $('#templateuebersicht').dataTable();
    }else if(id == 2){
      var oTableL = $('#rahmenbedingungen').dataTable();  
    }else if(id == 3){
      var oTableL = $('#artikelzuordnungen').dataTable();
    }else if(id == 4){
      var oTableL = $('#versand').dataTable();
    }else if(id == 5){
      var oTableL = $('#store').dataTable();
    }

    oTableL.fnFilter('a');
    oTableL.fnFilter('');
  }
</script>
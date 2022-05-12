<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Jahresbestand</a></li>
        <li><a href="#tabs-2">Bestelleingang</a></li>
    </ul>
<div id="tabs-1">

<div id="korrekturedit" style="display:none" title="Korrektureinstellungen">
  <input type="hidden" name="kid" id="kid" value ="0">
  <fieldset>
    <legend>{|Einstellungen|}</legend>
    <table>
      <tr>
        <td>
          {|Jahr|}:
        </td>
        <td>
          <input type="text" name="jahr" id="jahr" size="30" />
        </td>  
      </tr>  
      <tr>
        <td>
          {|Menge|}: 
        </td>
        <td>
          <input type="text" name="menge" id="menge" size="30" />
        </td>
      </tr>
      <tr>
        <td width="80">
          {|Kommentar|}:
        </td>
        <td>
          <input type="text" name="kommentar" id="kommentar" size="30" />
        </td>
      </tr>
      <tr>
        <td>
          {|Art|}:
        </td>
        <td>
          <select name="art" id="art"><option value="wein">Wein</option><option value="schaumwein">Schaumwein</option><option value="spiritus" selected>Spirituosen</select>
        </td>
      </tr> 
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">
          <small><i>{|Korrekturwerte beziehen sich auf den Endbestand des Jahres.|}</i></small>
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
  </fieldset>
    
</div>
</div>
<div class="col-xs-12 col-md-2 col-md-height">
<div class="inside inside-full-height">

  <fieldset>
    <legend>{|Aktionen|}</legend>
    <input class="btnGreenNew" type="button" name="neuedit" value="&#10010; Neue Korrektur eintragen" onclick="neuedit(0);">
  </fieldset>

</div>
</div>
</div>
</div>

[TAB1NEXT]



</div>


<div id="tabs-2">

<div id="korrektureditbewegung" style="display:none" title="Korrektureinstellungen">
  <input type="hidden" name="bid" id="bid" value ="0">
  <fieldset>
    <legend>{|Einstellungen|}</legend>
    <table>
      <tr>
        <td>
          {|Artikel|}:
        </td>
        <td>
          <input type="text" name="artikelbewegung" id="artikelbewegung" size="30" readonly="" />
        </td> 
      </tr> 
      <tr>
        <td>
          {|Gebuchte Menge|}:
        </td>
        <td>
          <input type="text" name="gebuchtemengebewegung" id="gebuchtemengebewegung" size="30" readonly="" />
        </td>
      </tr>
      <tr>
      <tr>
        <td>
          {|Korrigierte Menge|}:
        </td>
        <td>
          <input type="text" name="mengebewegung" id="mengebewegung" size="30" />
        </td>
      </tr>
      <tr>
        <td width="80">
          {|Kommentar|}:
        </td>
        <td>
          <input type="text" name="kommentarbewegung" id="kommentarbewegung" size="30" />
        </td>
      </tr>
      <tr>
        <td>&nbsp;</td>
      </tr>
      <tr>
        <td colspan="2">
          <small><i>{|Korrekturwerte überschreiben die Werte der jeweiligen Zeile.|}</i></small>
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
    [TAB2]
  </fieldset>
</div>
</div>
</div>
</div>

[TAB2NEXT]



</div>

<!-- tab view schließen -->
</div>


<script>
$(document).ready(function() {
  $("#korrekturedit").dialog({
  modal: true,
  bgiframe: true,
  closeOnEscape:false,
  minWidth:370,
  autoOpen: false,
  buttons: {
    ABBRECHEN: function() {
      $(this).dialog('close');
    },
    SPEICHERN: function() {
      korrekturhinzufuegen();
    }
  }
  });

  $("#korrekturedit").dialog({
    close: function( event, ui ){}
  });

  $("#korrektureditbewegung").dialog({
  modal: true,
  bgiframe: true,
  closeOnEscape:false,
  minWidth:370,
  autoOpen: false,
  buttons: {
    ABBRECHEN: function() {
      $(this).dialog('close');
    },
    SPEICHERN: function() {
      korrekturhinzufuegenbewegung();
    }
  }
  });

  $("#korrektureditbewegung").dialog({
    close: function( event, ui ){}
  });

});


  function neuedit(nr)
  {
    if(nr == 0){
      document.getElementById('kid').value = '0';
      document.getElementById('jahr').value = '';
      document.getElementById('menge').value = '';
      document.getElementById('kommentar').value = '';
      document.getElementById('art').value = 'spiritus';
      $("#korrekturedit").dialog('open');
    }else{
      $.ajax({
        url: 'index.php?module=alkoholsteuerrechner&action=korrektur&cmd=editkorrektur&id='+nr,
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function(data) {
          document.getElementById('kid').value = data.id;
          document.getElementById('jahr').value = data.jahr;
          document.getElementById('menge').value = data.menge.replace(".",",");
          document.getElementById('kommentar').value = data.kommentar;
          document.getElementById('art').value = data.art;
          $('#korrekturedit').dialog('open');
        },
        beforeSend: function() {

        }
      });
      //fnFilterColumn1(nr);  
    } 
  }

  function neueditbewegung(nr)
  {
    $.ajax({
      url: 'index.php?module=alkoholsteuerrechner&action=korrektur&cmd=editkorrekturbewegung&id='+nr,
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        document.getElementById('bid').value = data.id;
        document.getElementById('artikelbewegung').value = data.artikel;
        document.getElementById('gebuchtemengebewegung').value = data.gebuchtemenge;
        document.getElementById('mengebewegung').value = data.menge;
        document.getElementById('kommentarbewegung').value = data.kommentar;
        $('#korrektureditbewegung').dialog('open');
      },
      beforeSend: function() {

      }
    });
  }
  function korrekturhinzufuegenbewegung() {
    $.ajax({
      url: 'index.php?module=alkoholsteuerrechner&action=korrektur&cmd=korrekturspeichernbewegung',
      type: 'POST',
      dataType: 'json',
      data: {
        id: $('#bid').val(),
        menge: $('#mengebewegung').val(),
        kommentar: $('#kommentarbewegung').val(),
      },
      success: function(data) {
        $("#korrektureditbewegung").dialog('close'); 
        updateLiveTable2();
      },
      beforeSend: function() {

      }
    });
  }

  function deleteeintragbewegung(nr)
  {
    if(!confirm("Soll die Korrektur wirklich gelöscht werden?")) return false;
    $.ajax({
        url: 'index.php?module=alkoholsteuerrechner&action=korrektur&cmd=deletebewegung&id='+nr,
        data: { 
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
          updateLiveTable2();
        }
    });
  }  

  function korrekturhinzufuegen() {
    $.ajax({
      url: 'index.php?module=alkoholsteuerrechner&action=korrektur&cmd=korrekturspeichern',
      type: 'POST',
      dataType: 'json',
      data: {
        kid: $('#kid').val(),
        jahr: $('#jahr').val(),
        menge: $('#menge').val(),
        kommentar: $('#kommentar').val(),
        art: $('#art').val()
      },
      success: function(data) {
        if(data != 'success'){
          alert(data);
        }else{
          $("#korrekturedit").dialog('close'); 
          updateLiveTable();
        } 
      },
      beforeSend: function() {

      }
    });
  }


  function deleteeintrag(nr)
  {
    if(!confirm("Soll die Korrektur wirklich aus der Auflistung entfernt werden?")) return false;
    $.ajax({
        url: 'index.php?module=alkoholsteuerrechner&action=korrektur&cmd=delete&id='+nr,
        data: { 
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
          updateLiveTable();
        }
    });
  }

  function updateLiveTable() {
      var oTableL = $('#korrekturuebersicht').dataTable();
      oTableL.fnFilter('%');
      oTableL.fnFilter('');
  }
    function updateLiveTable2() {
      var oTableL = $('#korrekturuebersichtbestellung').dataTable();
      oTableL.fnFilter('%');
      oTableL.fnFilter('');
  }
</script>

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">

<div class="row">
<div class="row-height">
  <div class="col-xs-12 col-md-10 col-md-height">
  <div class="inside inside-full-height">

    <div class="filter-box filter-usersave">
      <div class="filter-block filter-inline">
        <div class="filter-title">{|Filter|}</div>
        <ul class="filter-list">
          <li class="filter-item">
            <label for="eigene" class="switch">
              <input type="checkbox" name="eigene" id="eigene">
              <span class="slider round"></span>
            </label>
            <label for="eigene">{|Eigene Kunden|}</label>
          </li>
          <li class="filter-item">
            <label for="geplant" class="switch">
              <input type="checkbox" name="geplant" id="geplant">
              <span class="slider round"></span>
            </label>
            <label for="geplant">{|Nur geplante Kunden|}</label>
          </li>
          <li class="filter-item">
            <label for="abgeschlossen" class="switch">
              <input type="checkbox" name="abgeschlossen" id="abgeschlossen">
              <span class="slider round"></span>
            </label>
            <label for="abgeschlossen">{|Nur abgeschlossene Kunden|}</label>
          </li>
        </ul>
      </div>
    </div>

  </div>
  </div>
  <div class="col-xs-12 col-md-2 col-md-height">
  <div class="inside inside-full-height">
    <fieldset>
      <legend>{|Aktionen|}</legend>
      <input class="btnGreenNew" type="button" name="neuedit" value="&#10010; Neue Einrichtung anlegen" onclick="neuedit(0);">
    </fieldset>
  </div>
  </div>
</div>
</div>



<div id="einrichtungedit" style="display:none" title="Einrichtung">
  <input type="hidden" name="einrichtungid" id="einrichtungid" value="0"/>
  <fieldset>
    <legend>{|Kunde|}</legend>
    <table>
      <tr>
        <td>
          {|Kunde|}:
        </td>
        <td colspan="3">
          <input type="text" name="kunde" id="kunde" size="40" />
        </td>
      </tr>
      <tr>
        <td>
          {|Mitarbeiter|}:
        </td>
        <td colspan="3">
          <input type="text" name="mitarbeiter" id="mitarbeiter" size="40"/>
        </td>
      </tr>
      <tr>
        <td>
          {|Version|}:
        </td>
        <td colspan="3">
          <input type="text" name="version" id="version" size="20"/>
        </td>
      </tr>
      <tr>
        <td>
          {|Status|}:
        </td>
        <td>
          <select id="status"><option value="geplant">geplant</option><option value="gestartet">gestartet</option><option value="abgeschlossen">abgeschlossen</option></select>
        </td>
        <td>
          {|Intervall|}:
        </td>
        <td>
          <input type="text" name="intervall" id="intervall" size="5"/>
        </td>
      </tr>
      <tr>
        <td>
          {|Startdatum|}:
        </td>
        <td>
          <input type="text" name="startdatum" id="startdatum" size="10"/>
        </td>
        <td>
          {|Zeit Geplant (in h)|}:
        </td>
        <td>
          <input type="text" name="zeitgeplant" id="zeitgeplant" size="5"/>
        </td>
      </tr>
      <tr>
        <td>
          {|Phase|}:
        </td>
        <td colspan="3">
         <select id="phase"><option value="beginn">Beginn</option><option value="mitte">Mitte</option><option value="kurzdavor">Kurz vor Abschluss</option><option value="uebergabe">Übergabe</option></select>
        </td>
        </td>
      </tr>
      <tr>
        <td>
          {|Bemerkung|}:
        </td>
        <td colspan="3">
         <textarea name="bemerkung" id="bemerkung" rows="10" cols="75"></textarea>
        </td>
      </tr>
    </table>
  </fieldset>
</div>





[MESSAGE]
<table width="100%">
  <tr>
    <td style="vertical-align: top;">
[FILTERBOX]
    </td>
    <td>
[TAB1]
    </td>
  </tr>
</table>

[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>


<script>

$(document).ready(function() {
$("#einrichtungedit").dialog({
  modal: true,
  bgiframe: true,
  closeOnEscape:false,
  minWidth: 700,
  autoOpen: false,
  buttons: {
    SCHLIEßEN: function() {
      $(this).dialog('close');
    },
    SPEICHERN: function() {
      einrichtunghinzufuegen();
    }
  }
  });
  $("#einrichtungedit").dialog({
    close: function( event, ui ){}
  });
});

  function einrichtunghinzufuegen() {
    $.ajax({
      url: 'index.php?module=supportapp&action=list&cmd=einrichtungspeichern',
      type: 'POST',
      dataType: 'json',
      data: {
        einrichtungid: $('#einrichtungid').val(),
        kunde: $('#kunde').val(),
        mitarbeiter: $('#mitarbeiter').val(),
        version: $('#version').val(),
        status: $('#status').val(),
        intervall: $('#intervall').val(),
        startdatum: $('#startdatum').val(),
        zeitgeplant: $('#zeitgeplant').val(),
        phase: $('#phase').val(),
        bemerkung: $('#bemerkung').val()
      },
      success: function(data) {
      $("#einrichtungedit").dialog('close');
      updateLiveTable();
       },
      beforeSend: function() {

      }
    });
  }

  function updateLiveTable() {
    var oTableL = $('#supportapp_list').dataTable();
    oTableL.fnFilter('%');
    oTableL.fnFilter('');
  }


  function neuedit(einrichtungid)
  {
    if(einrichtungid == 0){
      document.getElementById('einrichtungid').value = 0;
      document.getElementById('kunde').value = '';
      document.getElementById('mitarbeiter').value = '';
      document.getElementById('version').value = '';
      document.getElementById('status').value = 'geplant';
      document.getElementById('intervall').value = '';
      document.getElementById('startdatum').value = '';
      document.getElementById('zeitgeplant').value = '';
      document.getElementById('phase').value = 'beginn';
      $('#bemerkung').val('');

      $("#einrichtungedit").dialog('open');
    }else{
      $.ajax({
        url: 'index.php?module=supportapp&action=list&cmd=geteinrichtung&id='+einrichtungid,
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function(data) {
          document.getElementById('einrichtungid').value = einrichtungid;
          document.getElementById('kunde').value = data.kunde;
          document.getElementById('mitarbeiter').value = data.mitarbeiter;
          document.getElementById('version').value = data.version;
          document.getElementById('status').value = data.status;
          document.getElementById('intervall').value = data.intervall;
          document.getElementById('startdatum').value = data.startdatum;
          document.getElementById('zeitgeplant').value = data.zeitgeplant;
          document.getElementById('phase').value = data.phase;
          $('#bemerkung').val(data.bemerkung);
          $('#einrichtungedit').dialog('open');
        },
        beforeSend: function() {

        }
      });
    }
  }
</script>
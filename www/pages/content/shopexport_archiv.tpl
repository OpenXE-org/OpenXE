<script type="text/javascript">
  $(document).ready(function() {
  
    $('#popuparchiv').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 940,
      title:'Alte Aufträge abholen',
      buttons: {
        '{|ABHOLEN|}': function()
        {
          $.ajax({
              url: 'index.php?module=onlineshops&action=edit&cmd=archivspeichern&id=[ID]',
              type: 'POST',
              dataType: 'json',
              data: { 
                     von: $('#archiv_von').val()
                     ,bis: $('#archiv_bis').val()
                     ,typ: $('#archiv_typval').val()
                     ,zeitvon: $('#archiv_zeitvon').val(),zeitbis: $('#archiv_zeitbis').val()
                     ,auftrag_abschliessen:($('#archiv_aufrag_abschliessen').prop('checked')?1:0)
                     ,rechnung_erzeugen:($('#archiv_rechnung_erzeugen').prop('checked')?1:0)
                     ,rechnung_bezahlt:($('#archiv_rechnung_bezahlt').prop('checked')?1:0)
                     ,stornierte_abholen:($('#archiv_storniert_abholen').prop('checked')?1:0)
                     ,donotimport:($('#donotimport').prop('checked')?1:0)
                    },
              success: function(data) {
                if(data.status == 1) {
                  window.location.href=window.location.href.split('#')[ 0 ];
                }
                else{
                  alert("Angegebene Daten unzureichend.");
                }
              },
              beforeSend: function() {

              }
          });
        },
        '{|ABBRECHEN|}': function() {
          $(this).dialog('close');
        }
      },
      close: function(event, ui){
        
      }
    });    
  });
</script>
<div id="popuparchiv" style="display:none;">
<fieldset><legend>{|Einstellungen|}</legend>
  <table>
    <tr>
      <td><label for="archiv_von">{|[ARCHIVTYP] von|}:</label></td>
      <td><input type="hidden" id="archiv_typval" value="[ARCHIVTYPVAL]"><input type="text" id="archiv_von" /> <span style="[ARCHIVZEITSTYPE]"><label for="archiv_zeitvon">{|Zeit|}:</label> <input type="text" id="archiv_zeitvon" /></span></td>
    </tr>
    <tr>
      <td><label for="archiv_bis">{|[ARCHIVTYP] bis|}:</label></td>
      <td><input type="text" id="archiv_bis" /> <span style="[ARCHIVZEITSTYPE]"><label for="archiv_zeitbis">{|Zeit|}:</label> <input type="text" id="archiv_zeitbis" /></span></td>
    </tr>
    <tr>
      <td><label for="archiv_aufrag_abschliessen">{|Auftrag abschlie&szlig;en|}:</label></td>
      <td><input type="checkbox" value="1" id="archiv_aufrag_abschliessen" /></td>
    </tr>
    <tr style="display:none;">
      <td><label for="archiv_rechnung_erzeugen">{|Rechnung erzeugen|}:</label></td>
      <td><input type="checkbox" value="1" id="archiv_rechnung_erzeugen" /></td>
    </tr>
    <tr style="display:none;">
      <td><label for="archiv_rechnung_bezahlt">{|Rechnung als Bezahlt markieren|}:</label></td>
      <td><input type="checkbox" value="1" id="archiv_rechnung_bezahlt" /></td>
    </tr>
    <tr>
      <td><label for="archiv_storniert_abholen">{|Stornierte Auftr&auml;ge auch abholen|}:</label></td>
      <td><input type="checkbox" value="0" id="archiv_storniert_abholen" /></td>
    </tr>
    <tr>
      <td><label for="donotimport">{|in Zwischentabelle laden|}:</label></td>
      <td><input type="checkbox" value="0" id="donotimport" /></td>
    </tr>
  </table>
</fieldset>  
</div>
<div id="editVerbindlichkeit" style="display:none;overflow:hidden;overflow-y:auto;" title="{|Regelmäßige Verbindlichkeiten|}">
<fieldset><legend>{|Auswahl|}</legend>
  <table>
    <tr>
      <td style="width:10em;">{|Auswahl|}:</td><td><select name="auswahl" id="auswahl" style="width:20em" onchange="auswahlaendern(this);">
          <option value="zahlungseingang">{|Per Zahlungseingang anlegen|}</option>
          <option value="datum">{|Automatisch nach Datum|}</option>
        </select></td>
    </tr> 
    <tr>
      <td style="width:10em;">{|Art|}:</td><td><select name="edittyp" id="edittyp" style="width:20em" onchange="artaendern(this);">
          <option value="0">{|Verbindlichkeit|}</option>
          <option value="1">{|Kontenrahmen|}</option>
          <option value="2">{|Importfehler|}</option>
        </select></td>
    </tr>
    <tr>
      <td>{|aktiv|}:</td><td><input type="checkbox" id="aktiv" checked="checked" /></td>
    </tr> 
  </table>
</fieldset>

<fieldset><legend>{|Filter|}</legend>  
  <input type="hidden" id="editid" value="0">
  <table id="zahlungseingangfilter">
    <tr>
      <td style="width:10em;">{|Buchungstext|}:</td><td><input type="text" id="buchungstext" value="" style="width:20em" /></td>
    </tr>    
    <tr>
      <td>{|Soll|}:</td><td><input type="text" id="soll" value="" /></td>
    </tr> 
    <tr>
      <td>{|Haben|}:</td><td><input type="text" id="haben" value="" /></td>
    </tr> 
    <tr>
      <td>{|Geb&uuml;hr|}:</td><td><input type="text" id="gebuehr" value="" /></td>
    </tr> 
    <tr>
      <td>{|W&auml;hrung|}:</td><td><input type="text" id="waehrung" value="EUR" /></td>
    </tr>
  </table>
  <table id="datumsfilter" style="display:none";>
    <tr>
      <td style="width:10em;">{|Tag|}:</td><td><select name="tag" id="tag">
        [DATUMSFILTERTAG]
        </select></td>
    </tr>
  </table>
  </fieldset>

<fieldset><legend id="legendverbindlichkeit">{|Verbindlichkeit|}</legend><legend id="legendkontenrahmen">{|Kontenrahmen|}</legend><legend id="legendimportfehler">{|Importfehler|}</legend>
  <table>
    <tr id="rowlieferant" style="">
      <td style="width:10em;">{|Lieferant|}:</td><td><input type="text" id="lieferant" value="[LIEFERANT]" style="width:20em" /></td>
    </tr> 
    <tr id="rowgegenkonto" style="display:none">
      <td style="width:10em;">{|Gegenkonto|}:</td><td><input type="text" id="gegenkonto" value="[GEGENKONTO]" style="width:20em" /></td>
    </tr> 
    <tr id="rowgrund" style="display:none">
      <td style="width:10em;">{|Grund|}:</td><td><input type="text" id="grund" value="[GRUND]" style="width:20em" /></td>
    </tr>
    <tr id="rowrechnungnr">
      <td>{|Rechnung Nr|}:</td><td><input type="text" id="rechnungnr" style="width:20em" value="" /></td>
    </tr>
    <tr id="rowverwendungszweck">
      <td>{|Verwendungszweck|}:</td><td><input type="text" id="verwendungszweck" style="width:20em" value="" /></td>
    </tr> 
    <tr id="rowkostenstelle">
      <td>{|Kostenstelle|}:</td><td><input type="text" id="kostenstelle" style="width:20em" value="" /></td>
    </tr>
    <tr id="rowzahlungsweise">
      <td>{|Zahlweise|}:</td><td><select id="zahlungsweise" style="width:20em" ><option value="">{|- bitte w&auml;hlen -|}</option>[SELZAHLUNGSWEISE]</select></td>
    </tr>
    <tr id="rowwareneingangspruefung">
      <td>{|Freigabe|}:</td><td><input type="checkbox" id="wareneingangspruefung" /> {|Wareneingangspr&uuml;fung|}<br /><input type="checkbox" id="rechnungseingangspruefung" /> {|Rechnungseingangspr&uuml;fung|}</td>
    </tr>
    <tr id="rowverbindlichkeitbetrag">
      <td>{|Betrag|}:</td><td><input type="text" id="verbindlichkeitbetrag" value="" /></td>
    </tr>
    <tr id="rowverbindlichkeitwaehrung">
      <td>{|W&auml;hrung|}:</td><td><input type="text" id="verbindlichkeitwaehrung" value="EUR" /></td>
    </tr>
    <tr>
      <td>&nbsp;</td><td>&nbsp;</td>
    </tr> 
    <tr>
      <td>&nbsp;</td><td>&nbsp;</td>
    </tr>
  </table>
</fieldset>

</div>


<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<div id="tabs-1">

[MESSAGE]

  <div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-md-10 col-md-height">
  <div class="inside_white inside-full-height">

      [TAB1]
    
  </div>
  </div>
  <div class="col-xs-12 col-md-2 col-md-height">
  <div class="inside inside-full-height">

    <fieldset class="usersave">
      <legend>{|Aktionen|}</legend>
      <input class="btnGreenNew" type="button" name="neuedit" value="&#10010; {|Neuen Eintrag anlegen|}" onclick="neuedit(0);">
    </fieldset>

  </div>
  </div>
  </div>
  </div>


[TAB1NEXT]

</div>

</div>

<script>
$(document).ready(function() {
  $("#editVerbindlichkeit").dialog({
  modal: true,
  bgiframe: true,
  closeOnEscape:false,
  minWidth:490,
  autoOpen: false,
  buttons: {
    '{|ABBRECHEN|}': function() {
      $(this).dialog('close');
    },
    '{|SPEICHERN|}': function() {
      verbindlichkeitensave();
    }
  }
  });

  $("#editVerbindlichkeit").dialog({
    close: function( event, ui ){}
  });

});

  function artaendern(art){
    if(art.value == 0){
      document.getElementById('rowlieferant').style.display = '';
      document.getElementById('rowgegenkonto').style.display = '';
      document.getElementById('rowzahlungsweise').style.display = '';
      document.getElementById('rowgrund').style.display = 'none';

      document.getElementById('rowrechnungnr').style.display = '';  
      document.getElementById('rowverwendungszweck').style.display = '';  
      document.getElementById('rowkostenstelle').style.display = '';  
      document.getElementById('rowwareneingangspruefung').style.display = '';  

      document.getElementById('legendverbindlichkeit').style.display = '';  
      document.getElementById('legendkontenrahmen').style.display = 'none';  
      document.getElementById('legendimportfehler').style.display = 'none';  
    }
    if(art.value == 1){
      document.getElementById('rowlieferant').style.display = 'none';
      document.getElementById('rowgegenkonto').style.display = '';
      document.getElementById('rowgrund').style.display = 'none';
      document.getElementById('rowzahlungsweise').style.display = 'none';
      document.getElementById('rowrechnungnr').style.display = 'none';  
      document.getElementById('rowverwendungszweck').style.display = 'none';  
      document.getElementById('rowkostenstelle').style.display = 'none';  
      document.getElementById('rowwareneingangspruefung').style.display = 'none';       

      document.getElementById('legendverbindlichkeit').style.display = 'none';  
      document.getElementById('legendkontenrahmen').style.display = '';   
      document.getElementById('legendimportfehler').style.display = 'none';  
    }
    if(art.value == 2){
      document.getElementById('rowlieferant').style.display = 'none';
      document.getElementById('rowgegenkonto').style.display = 'none';
      document.getElementById('rowgrund').style.display = '';
      document.getElementById('rowzahlungsweise').style.display = 'none';
      document.getElementById('rowrechnungnr').style.display = 'none';  
      document.getElementById('rowverwendungszweck').style.display = 'none';  
      document.getElementById('rowkostenstelle').style.display = 'none';  
      document.getElementById('rowwareneingangspruefung').style.display = 'none';  

      document.getElementById('legendverbindlichkeit').style.display = 'none';  
      document.getElementById('legendkontenrahmen').style.display = 'none';             
      document.getElementById('legendimportfehler').style.display = '';  
   }
  }

  function auswahlaendern(auswahl){
    if(auswahl.value == 'zahlungseingang'){
      document.getElementById('zahlungseingangfilter').style.display = '';
      document.getElementById('datumsfilter').style.display = 'none';
      document.getElementById('rowverbindlichkeitbetrag').style.display = 'none';
      document.getElementById('rowverbindlichkeitwaehrung').style.display = 'none';
      $("#edittyp").append('<option value="1">{|Kontenrahmen|}</option>');
      $("#edittyp").append('<option value="2">{|Importfehler|}</option>');
    }
    if(auswahl.value == 'datum'){
      document.getElementById('zahlungseingangfilter').style.display = 'none';
      document.getElementById('datumsfilter').style.display = '';
      document.getElementById('rowverbindlichkeitbetrag').style.display = '';
      document.getElementById('rowverbindlichkeitwaehrung').style.display = '';
      $("#edittyp option[value='1']").remove();
      $("#edittyp option[value='2']").remove();
    }
  }

  function neuedit(nr)
  {
    if(nr == 0){
      document.getElementById("tag").options[0].selected = true;
      document.getElementById("edittyp").options[0].selected = true;
      document.getElementById("auswahl").options[0].selected = true;
      $("#edittyp option[value='1']").remove();
      $("#edittyp option[value='2']").remove();
      $("#edittyp").append('<option value="1">{|Kontenrahmen|}</option>');
      $("#edittyp").append('<option value="2">{|Importfehler|}</option>');

      document.getElementById('zahlungseingangfilter').style.display = '';
      document.getElementById('datumsfilter').style.display = 'none';
      document.getElementById('rowlieferant').style.display = '';
      document.getElementById('rowgegenkonto').style.display = '';
      document.getElementById('rowgrund').style.display = 'none';      

      document.getElementById('rowrechnungnr').style.display = '';  
      document.getElementById('rowverwendungszweck').style.display = '';  
      document.getElementById('rowkostenstelle').style.display = '';  
      document.getElementById('rowwareneingangspruefung').style.display = '';
      document.getElementById('rowzahlungsweise').style.display = '';
      document.getElementById('legendverbindlichkeit').style.display = '';  
      document.getElementById('legendimportfehler').style.display = 'none';  
      document.getElementById('legendkontenrahmen').style.display = 'none';
      document.getElementById('rowverbindlichkeitbetrag').style.display = 'none';
      document.getElementById('rowverbindlichkeitwaehrung').style.display = 'none';
      $('#editVerbindlichkeit').find('#editid').val('0');
      $('#editVerbindlichkeit').find('#buchungstext').val('');
      $('#editVerbindlichkeit').find('#soll').val('');
      $('#editVerbindlichkeit').find('#haben').val('');
      $('#editVerbindlichkeit').find('#gebuehr').val('');
      $('#editVerbindlichkeit').find('#waehrung').val('');
      $('#editVerbindlichkeit').find('#lieferant').val('');
      $('#editVerbindlichkeit').find('#gegenkonto').val('');
      $('#editVerbindlichkeit').find('#grund').val('');
      $('#editVerbindlichkeit').find('#rechnungnr').val('');
      $('#editVerbindlichkeit').find('#verwendungszweck').val('');
      $('#editVerbindlichkeit').find('#kostenstelle').val('');
      $('#editVerbindlichkeit').find('#zahlungsweise').val('');
      $('#editVerbindlichkeit').find('#aktiv').prop("checked", true);  
      $('#editVerbindlichkeit').find('#wareneingangspruefung').prop("checked", false);
      $('#editVerbindlichkeit').find('#rechnungseingangspruefung').prop("checked", false);
      $("#editVerbindlichkeit").dialog('open');
    }else{
      $.ajax({
        url: 'index.php?module=verbindlichkeit&action=automatisch&cmd=get&id='+nr,
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function(data) {
          $('#editVerbindlichkeit').dialog('open');
          $('#editid').val(data.id);
          $('#rechnungnr').val(data.rechnungnr);
          $('#verwendungszweck').val(data.verwendungszweck);
          $('#kostenstelle').val(data.kostenstelle);

          if(data.typ == 'zahlungseingang'){
            document.getElementById('zahlungseingangfilter').style.display = '';
            document.getElementById('datumsfilter').style.display = 'none';
            document.getElementById("auswahl").options[0].selected = true;
            document.getElementById("tag").options[0].selected = true;
            $('#buchungstext').val(data.filter);
            $('#soll').val(data.soll);
            $('#haben').val(data.haben);
            $('#waehrung').val(data.waehrung);
            $('#gebuehr').val(data.gebuehr);
            $('#verbindlichkeitbetrag').val('');
            $('#verbindlichkeitwaehrung').val('');
            document.getElementById('rowverbindlichkeitbetrag').style.display = 'none';
            document.getElementById('rowverbindlichkeitwaehrung').style.display = 'none';
          }
          if(data.typ == 'datum'){
            document.getElementById("auswahl").options[1].selected = true;
            document.getElementById('zahlungseingangfilter').style.display = 'none';
            document.getElementById('datumsfilter').style.display = '';
            $('#buchungstext').val('');
            $('#soll').val('');
            $('#haben').val('');
            $('#waehrung').val('');
            $('#gebuehr').val('');
            $('#verbindlichkeitbetrag').val(data.soll);
            $('#verbindlichkeitwaehrung').val(data.waehrung);
            document.getElementById("tag").options[data.filter-1].selected = true;
            document.getElementById('rowverbindlichkeitbetrag').style.display = '';
            document.getElementById('rowverbindlichkeitwaehrung').style.display = '';
          }

          if(data.art == 0){
            document.getElementById("edittyp").options[0].selected = true;
            $('#lieferant').val(data.wert);
            $('#gegenkonto').val(data.gegenkonto);
            $('#zahlungsweise').val(data.zahlungsweise);
            $('#grund').val('');
            document.getElementById('rowlieferant').style.display = '';
            document.getElementById('rowgegenkonto').style.display = '';
            document.getElementById('rowzahlungsweise').style.display = '';
            document.getElementById('rowgrund').style.display = 'none';
          }
          if(data.art == 1){
            document.getElementById("edittyp").options[1].selected = true;
            $('#lieferant').val('');
            $('#gegenkonto').val(data.wert);
            $('#grund').val('');
            document.getElementById('rowlieferant').style.display = 'none';
            document.getElementById('rowgegenkonto').style.display = '';
            document.getElementById('rowzahlungsweise').style.display = 'none';
            document.getElementById('rowgrund').style.display = 'none';
          }
          if(data.art == 2){
            document.getElementById("edittyp").options[2].selected = true;
            $('#lieferant').val('');
            $('#gegenkonto').val('');
            $('#grund').val(data.wert);
            document.getElementById('rowlieferant').style.display = 'none';
            document.getElementById('rowgegenkonto').style.display = 'none';
            document.getElementById('rowzahlungsweise').style.display = 'none';
            document.getElementById('rowgrund').style.display = '';      
          }

          if(data.repruefung == 1){
            $('#editVerbindlichkeit').find('#rechnungseingangspruefung').prop("checked", true);  
          }else{
            $('#editVerbindlichkeit').find('#rechnungseingangspruefung').prop("checked", false);  
          }
          if(data.wepruefung == 1){
            $('#editVerbindlichkeit').find('#wareneingangspruefung').prop("checked", true);  
          }else{
            $('#editVerbindlichkeit').find('#wareneingangspruefung').prop("checked", false);  
          }
          if(data.aktiv == 1){
            $('#editVerbindlichkeit').find('#aktiv').prop("checked", true);  
          }else{
            $('#editVerbindlichkeit').find('#aktiv').prop("checked", false);  
          }


        },
        beforeSend: function() {

        }
      });
    } 
  }

  function deleteeintrag(nr)
  {
    if(!confirm("Soll die regelmäßige Verbindlichkeit wirklich gelöscht werden?")) return false;
    $.ajax({
        url: 'index.php?module=verbindlichkeit&action=automatisch&cmd=delete&id='+nr,
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
  function verbindlichkeitensave() {

    $.ajax({
        url: 'index.php?module=verbindlichkeit&action=automatisch&cmd=save',
        data: {
          id: $('#editid').val(),
          auswahl: $('#auswahl').val(),
          buchungstext: $('#buchungstext').val(),
          soll: $('#soll').val(),
          haben: $('#haben').val(),
          gebuehr: $('#gebuehr').val(),
          waehrung: $('#waehrung').val(),
          verbindlichkeitbetrag: $('#verbindlichkeitbetrag').val(),
          verbindlichkeitwaehrung: $('#verbindlichkeitwaehrung').val(),
          tag: $('#tag').val(),
          edittyp: $('#edittyp').val(),
          lieferant: $('#lieferant').val(),
          gegenkonto: $('#gegenkonto').val(),
          zahlungsweise:$('#zahlungsweise').val(),
          grund: $('#grund').val(),
          rechnungnr: $('#rechnungnr').val(),
          verwendungszweck: $('#verwendungszweck').val(),
          kostenstelle: $('#kostenstelle').val(),
          rechnungseingangspruefung: $('#rechnungseingangspruefung').prop("checked")?1:0,
          wareneingangspruefung: $('#wareneingangspruefung').prop("checked")?1:0,
          aktiv: $('#aktiv').prop("checked")?1:0
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
        },
        success: function(data) {
          $('#editVerbindlichkeit').find('#editid').val('');
          $('#editVerbindlichkeit').find('#buchungstext').val('');
          $('#editVerbindlichkeit').find('#soll').val('');
          $('#editVerbindlichkeit').find('#haben').val('');
          $('#editVerbindlichkeit').find('#gebuehr').val('');
          $('#editVerbindlichkeit').find('#waehrung').val('');
          $('#editVerbindlichkeit').find('#lieferant').val('');
          $('#editVerbindlichkeit').find('#gegenkonto').val('');
          $('#editVerbindlichkeit').find('#grund').val('');
          $("#editVerbindlichkeit").dialog('close'); 
          updateLiveTable();
        }
    });

  }

function updateLiveTable() {
  var oTable = $('#verbindlichkeit_regelmaessig').DataTable( );
  oTable.ajax.reload();
}


</script>

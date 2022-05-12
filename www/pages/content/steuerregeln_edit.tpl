<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">[TABTEXT]</a></li>
  </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab --> 
  
    <div id="tabs-1">
  <form method="post">
      [MESSAGE]
      <fieldset><legend>{|Bedingung|}</legend>
        <table>
          <tr><td width="200">Bezeichnung: </td><td><input type="text" size="40" name="bezeichnung" value="[BEZEICHNUNG]" /></td></tr>
          <tr><td>Beschreibung: </td><td><textarea name="beschreibung">[BESCHREIBUNG]</textarea></td></tr>
          <tr><td>Aktiv: </td><td> <input type="checkbox" value="1" name="aktiv" [AKTIV] /></td></tr>
          <tr><td>Prio: </td><td><input type="text" name="prio" size="6" value="[PRIO]" /></td></tr>
        </table>
      </fieldset>
      <fieldset><legend>{|Bedingung|}</legend>
      <table>
          <tr><td width="200">Bezug: </td><td>
          <select name="bedingung_quelle" id="bedingung_quelle" onchange="changebedingung_quelle(this);">
            [BEDINGUNG_QUELLE]
          </select>
          
          </td></tr>
          <tr><td>Lieferland: </td><td><input type="text" name="bedingung_lieferland" value="[BEDINGUNG_LIEFERLAND]"/></td></tr>
          <tr><td>Besteuerung: </td><td><select name="bedingung_ust">
           [BEDINGUNG_UST] 
          </select></td></tr>
          <tr><td>Lieferschwelle erreicht:</td><td><input type="checkbox" value="1" name="lieferschwelle" [LIEFERSCHWELLE] /></td></tr>
          <tr><td>Summe erreicht: </td><td><input type="checkbox" value="1" name="summeereicht" [SUMMEEREICHT] />&nbsp;<select name="summetyp">[SUMMETYP]</select></td></tr>
          <tr class="trsumme"><td>Summe: </td><td><input type="text" name="bedingung_summe" value="[BEDINGUNG_SUMME]" /></td></tr>
          
      </table>
      </fieldset>
      <fieldset><legend>{|Ergebnis|}</legend>
      <table>
      <tr class="trerloes"><td>Erl&ouml;skonto: </td><td><input type="text" name="ergebnis_erloesfest" value="[ERGEBNIS_ERLOESFEST]" /></td></tr>
      <tr class="trsteuersatz"><td>Steuersatz (als Zahl): </td><td><input type="text" name="ergebnis_steuersatzfest" value="[ERGEBNIS_STEUERSATZFEST]" /></td></tr>
      <tr class="trsteuertext"><td>Steuertext (§ Satz): </td><td><input type="text" name="ergebnis_steuertextfest" value="[ERGEBNIS_STEUERTEXTFEST]" /></td></tr>
      <tr><td width="200">Erl&ouml;skonto aus Quelle: </td><td> <input type="checkbox" value="1" name="erloesausquelle" id="erloesausquelle" [ERLOESAUSQUELLE] /> <i>z.B. aus Rechnung oder bei Versandkosten</i> </td></tr>
      <tr><td>Steuersatz aus Quelle: </td><td> <input type="checkbox" value="1"  name="steuersatzausquelle" id="steuersatzausquelle" [STEUERSATZAUSQUELLE] /> <i>z.B. aus Rechnung, &Uuml;bergeordneten Artikel (bei Bezug: &Uuml;bergeordneter Artikel oder Hauptsteuersatz) oder bei Versandkosten</i></td></tr>
      <tr><td>Steuertext aus Erl&ouml;squelle: </td><td> <input type="checkbox" value="1"  name="steuertextausquelle" id="steuertextausquelle" [STEUERTEXTAUSQUELLE] /></td></tr>
      </table>
      </fieldset>
      <div><input type="submit" value="Speichen" style="float:right;" name="speichern" /></div>
      [TAB1]
      [TAB1NEXT]
    </form>
    </div>
<!-- tab view schließen -->
</div>



<div id="editSteuersaetze" style="display:none;" title="Bearbeiten">
  <input type="hidden" id="id" name="id">
  <table>
    <tr>
      <td>Steuersatz:</td>
      <td><input type="text" id="satz" name="satz"></td>        
    </tr>
    <tr>
      <td>Bezeichnung:</td>
      <td><input type="text" id="bezeichnung" name="bezeichnung"></td>
    </tr>
    <tr>
      <td>Aktiv:</td>
      <td><input type="checkbox" id="aktiv" name="aktiv" value="1"></td>
     </tr>
  </table>
  
  
</div>

</form>

<script type="text/javascript">

$(document).ready(function() {
    $('#steuersatz').focus();
    changebedingung_quelle($('#bedingung_quelle'));
    $("#editSteuersaetze").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:300,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        steuersaetzeEditSave();
      }
    }
  });

});

function steuersaetzeEditSave() {

    $.ajax({
        url: 'index.php?module=steuersaetze&action=save',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            id: $('#id').val(),
            satz: $('#satz').val(),
            bezeichnung: $('#bezeichnung').val(),
            aktiv: $('#aktiv').prop("checked")?1:0,
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                $('#editSteuersaetze').find('#id').val('');
                $('#editSteuersaetze').find('#satz').val('');
                $('#editSteuersaetze').find('#bezeichnung').val('');
                $('#editSteuersaetze').find('#aktiv').val('');
                updateLiveTable();
                $("#editSteuersaetze").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function SteuersaetzeEdit(id) {

    $.ajax({
        url: 'index.php?module=steuersaetze&action=edit&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editSteuersaetze').find('#id').val(data.id);
            $('#editSteuersaetze').find('#satz').val(data.satz);
            $('#editSteuersaetze').find('#bezeichnung').val(data.bezeichnung);
            $('#editSteuersaetze').find('#aktiv').prop("checked",data.aktiv==1?true:false);
            App.loading.close();
            $("#editSteuersaetze").dialog('open');
        }
    });

}

function updateLiveTable(i) {
    var oTableL = $('#steuersaetze_list').dataTable();
    oTableL.fnFilter('%');
    oTableL.fnFilter('');   
}

function SteuersaetzeDelete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({ 
            url: 'index.php?module=steuersaetze&action=delete',
            data: {
                id: id
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                App.loading.open();
            },
            success: function(data) {
                if (data.status == 1) {
                    updateLiveTable();
                } else {
                    alert(data.statusText);
                }
                App.loading.close();
            }
        });
    }

    return false;

}
function changebedingung_quelle(el)
{
  var wert = $(el).val();
  switch (wert){
    case 'gesamtbeleg':
      $('#erloesausquelle').prop('disabled', true);
      $('#steuersatzausquelle').prop('disabled', true);
      $('#steuertextausquelle').prop('disabled', true);
      if($('#erloesausquelle').prop('checked'))
      {
        $('#erloesausquelle').prop('checked', false);
        $('#erloesausquelle').trigger('change');
      }
      if($('#steuersatzausquelle').prop('checked'))
      {
        $('#steuersatzausquelle').prop('checked', false);
        $('#steuersatzausquelle').trigger('change');
      }
      if($('#steuertextausquelle').prop('checked'))
      {
        $('#steuertextausquelle').prop('checked', false);
        $('#steuertextausquelle').trigger('change');
      }
    break;
    default:
      $('#erloesausquelle').prop('disabled', false);
      $('#steuersatzausquelle').prop('disabled', false);
      $('#steuertextausquelle').prop('disabled', false);
    break;
  }

}


</script>


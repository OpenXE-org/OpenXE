<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-8 col-md-height">
<div class="inside_white inside-full-height">

  <fieldset class="white">
    <legend>&nbsp;</legend>
    <center><input class="btnGreenNew" type="button" name="anlegen" value="&#10010; Neuen Eintrag anlegen" onclick="VBestellungenEdit(0);"></center>
    [TAB2]
  </fieldset>

</div>
</div>
<div class="col-xs-12 col-md-4 col-md-height">
<div class="inside inside-full-height">

  <fieldset>
    <legend>{|Vorschau|}</legend>
  <iframe width="100%" height="100%" style="height:calc(100vh - 100px)"  class="preview" data-src="./js/production/generic/web/viewer.html?file=[FILE]&bestellung"></iframe>
  </fieldset>


</div>
</div>
</div>
</div>


<div id="editVBestellungen" style="display:none;" title="Bearbeiten">
<form method="post">
  <input type="hidden" id="e_id">
  <fieldset>
  	<legend>{|Zuordnung Bestellungen|}</legend>
  	<table>
      <tr>
        <td width="80">{|Bestell-Nr.|}:</td>
        <td><input type="text" name="e_bestellnr" id="e_bestellnr" size="40"></td>
      </tr>
      <tr>
        <td>{|Teilbetrag (brutto)|}:</td>
        <td><input type="text" name="e_teilbetrag" id="e_teilbetrag" size="40"></td>
      </tr>
      <tr>
        <td>{|Teilbetrag (netto)|}:</td>
        <td><input type="text" name="e_teilbetragnetto" id="e_teilbetragnetto" size="40"></td>
      </tr>
      <tr>
        <td>{|Projekt|}:</td>
        <td><input type="text" name="e_projekt" id="e_projekt" size="40"></td>
      </tr>
      <tr>
      	<td>{|Auftrag|}:</td>
      	<td><input type="text" name="e_auftrag" id="e_auftrag" size="40"></td>
      </tr>   
      <tr>
        <td>{|Kostenstelle|}:</td>
        <td><input type="text" name="e_kostenstelle" id="e_kostenstelle" size="40"></td>
      </tr>
      <tr>
      	<td>{|Bemerkung|}:</td>
      	<td><input type="text" name="e_bemerkung" id="e_bemerkung" size="40"></td>
      </tr>
    </table>
  </fieldset>
  
</div>


</form>
<script type="text/javascript">

$(document).ready(function() {
  $('#e_bestellnr').focus();

  $("#editVBestellungen").dialog({
  	modal: true,
   	bgiframe: true,
    closeOnEscape:false,
    minWidth:440,
  	maxHeight:900,
   	autoOpen: false,
   	buttons: {
     	ABBRECHEN: function() {
       	VBestellungenReset();
       	$(this).dialog('close');
     	},
     	SPEICHERN: function() {
       	VBestellungenEditSave();
     	}
   	}
	});

	$("#editVBestellungen").dialog({
  	close: function( event, ui ) { VBestellungenReset();}
	});

});


function VBestellungenReset()
{
  $('#editVBestellungen').find('#e_id').val('');
  $('#editVBestellungen').find('#e_bestellnr').val('');
  $('#editVBestellungen').find('#e_teilbetrag').val('');
  $('#editVBestellungen').find('#e_teilbetragnetto').val('');
  $('#editVBestellungen').find('#e_projekt').val('');
  $('#editVBestellungen').find('#e_auftrag').val('');
  $('#editVBestellungen').find('#e_kostenstelle').val('');
  $('#editVBestellungen').find('#e_bemerkung').val('');  
}

function VBestellungenEditSave() {
	$.ajax({
    url: 'index.php?module=verbindlichkeit&action=edit&cmd=bestellungsave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      bestellnr: $('#e_bestellnr').val(),
      teilbetrag: $('#e_teilbetrag').val(),
      teilbetragnetto: $('#e_teilbetragnetto').val(),
      projekt: $('#e_projekt').val(),
      auftrag: $('#e_auftrag').val(),
      kostenstelle: $('#e_kostenstelle').val(),
      bemerkung: $('#e_bemerkung').val(),
      verbindlichkeit: '[ID]'
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        VBestellungenReset();
        updateLiveTable2();
        $("#editVBestellungen").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });
}

function VBestellungenEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=verbindlichkeit&action=edit&cmd=bestellungedit',
      data: {
          id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        if(data.id > 0){
          $('#editVBestellungen').find('#e_id').val(data.id);
          $('#editVBestellungen').find('#e_bestellnr').val(data.bestellung);
          $('#editVBestellungen').find('#e_teilbetrag').val(data.bestellung_betrag);
          $('#editVBestellungen').find('#e_teilbetragnetto').val(data.bestellung_betrag_netto);
          $('#editVBestellungen').find('#e_projekt').val(data.bestellung_projekt);
          $('#editVBestellungen').find('#e_auftrag').val(data.bestellung_auftrag);
          $('#editVBestellungen').find('#e_kostenstelle').val(data.bestellung_kostenstelle);
          $('#editVBestellungen').find('#e_bemerkung').val(data.bestellung_bemerkung);                          
        }
        App.loading.close();
        $("#editVBestellungen").dialog('open');
      }
    });
  } else {
    VBestellungenReset(); 
    $("#editVBestellungen").dialog('open');
  }

}

function updateLiveTable2() {
  var oTableL = $('#verbindlichkeit_bestellungen').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function VBestellungenDelete(id) {

  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=verbindlichkeit&action=edit&cmd=bestellungdelete',
      data: {
        eid: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if (data.status == 1) {
          updateLiveTable2();
        } else {
          alert(data.statusText);
        }
        App.loading.close();
      }
    });
  }

  return false;

}

</script>

<script type='text/javascript'>
    $("input#e_bestellnr").autocomplete({
        source: "index.php?module=ajax&action=filter&filtername=bestellunggesamtsumme",
         select: function( event, ui ) {
                $('#editVBestellungen').find('#e_teilbetrag').val(ui.item.value.split(' ')[1]);
        }
    });

</script>


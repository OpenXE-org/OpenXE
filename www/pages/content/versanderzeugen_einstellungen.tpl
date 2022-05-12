<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]
		<div class="row">
		<div class="row-height">
		<div class="col-xs-12 col-md-10 col-md-height">
		<div class="inside-white inside-full-height">
			[TAB1]
			[TAB1NEXT]
		</div>
		</div>
		<div class="col-xs-12 col-md-2 col-md-height">
		<div class="inside inside-full-height">
			<fieldset>
				<legend>{|Aktionen|}</legend>
				<input type="button" class="btnGreenNew" name="neuergrund" value="&#10010; Neuen Kl&auml;rfall-Grund anlegen" onclick="VersanderzeugenEinstellungenEdit(0);">
				<input type="button" class="btnBlueNew" name="einstellungen_alleabschliessen" id="einstellungen_alleabschliessen" value="Alle offenen abschlie&szlig;en" onclick="AlleOffenenAbschliessen()">
			</fieldset>
		</div>
		</div>
		</div>
		</div>
	</div>

	<!-- tab view schließen -->
</div>


<div id="editVersanderzeugenEinstellungen" style="display:none;" title="Bearbeiten">
	<form method="post">
		<input type="hidden" id="versandeinstellungen_id">
		<fieldset>
			<legend>{|Einstellungen|}</legend>
			<table>
				<tr>
					<td width="150">{|Grund|}:</td>
					<td><input type="text" name="versandeinstellungen_grund" id="versandeinstellungen_grund" size="60"></td>
				</tr>
				<tr>
					<td>{|Sortierung|}:</td>
					<td><input type="text" name="versandeinstellungen_sortierung" id="versandeinstellungen_sortierung" size="10"></td>
				</tr>
			</table>
		</fieldset>
</div>
</form>


<script type="text/javascript">
	$(document).ready(function() {
    $('#versandeinstellungen_grund').focus();

    $("#editVersanderzeugenEinstellungen").dialog({
    	modal: true,
    	bgiframe: true,
    	closeOnEscape:false,
    	minWidth:600,
    	maxHeight:700,
    	autoOpen: false,
    	buttons: {
      	ABBRECHEN: function() {
        	VersanderzeugenEinstellungenReset();
        	$(this).dialog('close');
      	},
      	SPEICHERN: function() {
        	VersanderzeugenEinstellungenEditSave();
      	}
    	}
  	});

  	$("#editVersanderzeugenEinstellungen").dialog({
    	close: function( event, ui ) { VersanderzeugenEinstellungenReset();}
  	});

  });


  function VersanderzeugenEinstellungenReset()
  {
    $('#editVersanderzeugenEinstellungen').find('#versandeinstellungen_id').val('');
    $('#editVersanderzeugenEinstellungen').find('#versandeinstellungen_grund').val('');
    $('#editVersanderzeugenEinstellungen').find('#versandeinstellungen_sortierung').val('');
  }

  function VersanderzeugenEinstellungenEditSave() {
    $.ajax({
      url: 'index.php?module=versanderzeugen&action=einstellungen&cmd=save',
      data: {
        //Alle Felder die fürs editieren vorhanden sind
        id: $('#versandeinstellungen_id').val(),
        grund: $('#versandeinstellungen_grund').val(),
        sortierung: $('#versandeinstellungen_sortierung').val()
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        App.loading.close();
        if (data.status == 1) {
					VersanderzeugenEinstellungenReset();
          updateLiveTable();
          $("#editVersanderzeugenEinstellungen").dialog('close');
        } else {
          alert(data.statusText);
        }
      }
    });
  }

  function VersanderzeugenEinstellungenEdit(id) {
    if(id > 0)
    {
      $.ajax({
        url: 'index.php?module=versanderzeugen&action=einstellungen&cmd=edit',
        data: {
          id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
          App.loading.open();
        },
        success: function(data) {
          $('#editVersanderzeugenEinstellungen').find('#versandeinstellungen_id').val(data.id);
          $('#editVersanderzeugenEinstellungen').find('#versandeinstellungen_grund').val(data.problemcase);
          $('#editVersanderzeugenEinstellungen').find('#versandeinstellungen_sortierung').val(data.sort);

          App.loading.close();
          $("#editVersanderzeugenEinstellungen").dialog('open');
        }
      });
    } else {
        VersanderzeugenEinstellungenReset();
      $("#editVersanderzeugenEinstellungen").dialog('open');
    }

  }

  function updateLiveTable(i) {
    var oTableL = $('#versanderzeugen_einstellungen').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);
  }

  function VersanderzeugenEinstellungenDelete(id) {
    var conf = confirm('Wirklich löschen?');
    if (conf) {
      $.ajax({
  	    url: 'index.php?module=versanderzeugen&action=einstellungen&cmd=delete',
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

  function AlleOffenenAbschliessen() {
    var conf = confirm('Sind Sie sicher? Alle offenen Positionen im Versandzentrum werden auf abgeschlossen gesetzt. Es erfolgt keine Chargen-/MHD-Verwaltung und keine Seriennummernerfassung.');
    if (conf) {
      $.ajax({
        url: 'index.php?module=versanderzeugen&action=einstellungen&cmd=abschliessen',
        data: {
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
          App.loading.open();
        },
        success: function(data) {
          if (data.status == 1) {
            alert('Alle offenen Positionen wurden auf abgeschlossen gesetzt.');
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
<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

[MESSAGE]

<!-- erstes tab -->
<div id="tabs-1">
    <fieldset>
		<legend>{|Anlegen|}</legend>
		<form method="post">
			<table>
				<tr>
					<td width="85">{|Bezeichnung|}:</td>
					<td width="250"><input type="text" id="bezeichnung" name="bezeichnung" size="30" /></td>
					<td width="35">{|Aktiv|}:</td>
					<td width="50"><input type="checkbox" name="aktiv" value="1" /></td>
					<td><input type="submit" name="speichern" value="Speichern" /></td>
				</tr>
			</table>
		</form>
	</fieldset>

[TAB1]

[TAB1NEXT]


</div>
<!-- tab view schließen -->
</div>

<div id="editVorlage" style="display:none;" title="Bearbeiten">
  <fieldset>
    <legend>{|Vorlage|}</legend>
    <input type="hidden" id="id">
    <table>
      <tr>
        <td>{|Bezeichnung|}:</td>
        <td><input type="text" id="editbezeichnung" size="30"/></td>        
      </tr>
      <tr>
        <td>{|Aktiv|}:</td>
        <td><input type="checkbox" id="editaktiv" /></td>
      </tr>
    </table>
  </fieldset>  
</div>

<script type="text/javascript">

$(document).ready(function() {
    $('#editbezeichnung').focus();

    $("#editVorlage").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:380,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        EigenschaftenVorlagenEditSave();
      }
    }
  });

});

function EigenschaftenVorlagenEditSave() {

    $.ajax({
        url: 'index.php?module=eigenschaften_vorlagen&action=save',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            editid: $('#id').val(),
            editbezeichnung: $('#editbezeichnung').val(),
            editaktiv: $('#editaktiv').prop("checked")?1:0,
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                $('#editVorlage').find('#id').val('');
                $('#editVorlage').find('#editbezeichnung').val('');
                $('#editVorlage').find('#editaktiv').val('');
                updateLiveTable();
                $("#editVorlage").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });

}

function EigenschaftenVorlagenEdit(id) {

    $.ajax({
        url: 'index.php?module=eigenschaften_vorlagen&action=edit&cmd=get',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editVorlage').find('#id').val(data.id);
            $('#editVorlage').find('#editbezeichnung').val(data.bezeichnung);
            $('#editVorlage').find('#editaktiv').prop("checked",data.aktiv==1?true:false);
            App.loading.close();
            $("#editVorlage").dialog('open');
        }
    });

}

function updateLiveTable(i) {
    var oTableL = $('#eigenschaften_vorlagen_list').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);     
}

function EigenschaftenVorlagenDelete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({
            url: 'index.php?module=eigenschaften_vorlagen&action=delete',
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


</script>

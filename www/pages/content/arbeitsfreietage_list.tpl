<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs1"></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs1">
		[MESSAGE]
		[TAB1]

	</div>

	<!-- tab view schließen -->
</div>


<div id="editArbeitsfreieTage" style="display:none;" title="Bearbeiten">
	<form method="post">
		<input type="hidden" id="arbeitsfreietage_id">
		<fieldset>
			<legend>{|Einstellungen|}</legend>
			<table width="100%">
				<tr>
					<td width="150">{|Bezeichnung|}:</td>
					<td><input type="text" name="arbeitsfreietage_bezeichnung" id="arbeitsfreietage_bezeichnung" size="40">&nbsp;</td>
				</tr>
				<tr>
					<td width="150">{|Datum|}:</td>
					<td><input type="text" name="arbeitsfreietage_datum" id="arbeitsfreietage_datum" size="40"></td>
				</tr>
				<tr>
					<td width="150">{|Typ|}:</td>
					<td><select name="arbeitsfreietage_typ" id="arbeitsfreietage_typ">
								<option value="feiertag">Feiertag</option>
								<option value="brueckentag">Br&uuml;ckentag</option>
								<option value="betriebsferien">Betriebsferien</option>
						</select>
					</td>
				</tr>
				<tr>
					<td width="150">{|Projekt|}:</td>
					<td><input type="text" name="arbeitsfreietage_projekt" id="arbeitsfreietage_projekt" size="40">&nbsp;</td>
				</tr>
				<tr>
					<td width="150">{|Land|}:</td>
					<td><select name="arbeitsfreietage_land" id="arbeitsfreietage_land">
							[ARBEITSFREIETAGELAENDER]
						</select></td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>


<script type="text/javascript">

    $(document).ready(function() {
        $('#arbeitsfreietage_bezeichnung').focus();

        $("#editArbeitsfreieTage").dialog({
            modal: true,
            bgiframe: true,
            closeOnEscape:false,
            minWidth:650,
            maxHeight:700,
            autoOpen: false,
            buttons: {
                ABBRECHEN: function() {
                    ArbeitsfreieTageReset();
                    $(this).dialog('close');
                },
                SPEICHERN: function() {
                    ArbeitsfreieTageEditSave();
                }
            }
        });

        $("#editArbeitsfreieTage").dialog({
            close: function( event, ui ) { ArbeitsfreieTageReset();}
        });
    });

    function ArbeitsfreieTageReset()
    {
        $('#editArbeitsfreieTage').find('#arbeitsfreietage_id').val('');
        $('#editArbeitsfreieTage').find('#arbeitsfreietage_bezeichnung').val('');
        $('#editArbeitsfreieTage').find('#arbeitsfreietage_datum').val('');
        $('#editArbeitsfreieTage').find('#arbeitsfreietage_typ').val('feiertag');
        $('#editArbeitsfreieTage').find('#arbeitsfreietage_projekt').val('');
        $('#editArbeitsfreieTage').find('#arbeitsfreietage_land').val('');
    }

    function ArbeitsfreieTageEditSave() {
        $.ajax({
            url: 'index.php?module=arbeitsfreietage&action=save',
            data: {
                //Alle Felder die fürs editieren vorhanden sind
                id: $('#arbeitsfreietage_id').val(),
                bezeichnung: $('#arbeitsfreietage_bezeichnung').val(),
                datum: $('#arbeitsfreietage_datum').val(),
                typ: $('#arbeitsfreietage_typ').val(),
                projekt: $('#arbeitsfreietage_projekt').val(),
								land: $('#arbeitsfreietage_land').val()

            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                App.loading.open();
            },
            success: function(data) {
                App.loading.close();
                if (data.status == 1) {
                    ArbeitsfreieTageReset();
                    updateLiveTable();
                    $("#editArbeitsfreieTage").dialog('close');
                } else {
                    alert(data.statusText);
                }
            }
        });
    }

    function ArbeitsfreieTageEdit(id) {
        if(id > 0)
        {
            $.ajax({
                url: 'index.php?module=arbeitsfreietage&action=edit&cmd=get',
                data: {
                    id: id
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function() {
                    App.loading.open();
                },
                success: function(data) {
                    $('#editArbeitsfreieTage').find('#arbeitsfreietage_id').val(data.id);
                    $('#editArbeitsfreieTage').find('#arbeitsfreietage_bezeichnung').val(data.bezeichnung);
                    $('#editArbeitsfreieTage').find('#arbeitsfreietage_datum').val(data.datum);
                    $('#editArbeitsfreieTage').find('#arbeitsfreietage_typ').val(data.typ);
                    $('#editArbeitsfreieTage').find('#arbeitsfreietage_projekt').val(data.projekt);
                    $('#editArbeitsfreieTage').find('#arbeitsfreietage_land').val(data.land);

                    App.loading.close();
                    $("#editArbeitsfreieTage").dialog('open');
                }
            });
        } else {
            ArbeitsfreieTageReset();
            $("#editArbeitsfreieTage").dialog('open');
        }
    }

    function updateLiveTable(i) {
        var oTableL = $('#arbeitsfreietage_list').dataTable();
        var tmp = $('.dataTables_filter input[type=search]').val();
        oTableL.fnFilter('%');
        oTableL.fnFilter(tmp);
    }

    function ArbeitsfreieTageDelete(id) {
        var conf = confirm('Wirklich löschen?');
        if (conf) {
            $.ajax({
                url: 'index.php?module=arbeitsfreietage&action=delete',
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

<script>
	$(document).ready(function() {
		$("#belegpositionenberechnen").dialog({
			modal: true,
			bgiframe: true,
			closeOnEscape:false,
			minWidth:1024,
			maxHeight:800,
			autoOpen: false,
			buttons: {
				ABBRECHEN: function() {
					$(this).dialog('close');
				},
                Übernehmen: function() {
	                BelegpositionberechnungBerechne();
                    $(this).dialog('close');
                }
			}
		});

		// $('#belegpositionen_list > thead:first-child').hide();
	});

    function BelegpositionberechnungBerechne(){
	    $.ajax({
			    url: 'index.php?module=belegpositionberechnung&action=berechneData',
			    data: {
			        belegtype: $.urlParam('module'),
					belegtypeposition: $.urlParam('id')
			    },
			    type: 'post',
			    dataType: 'json',
			    beforeSend: function () {
				    App.loading.open();
			    },
			    success: function (data) {
				    if (data.status == 1) {
				    	// Wir fühlen das Basisiformular Menge und Beschreibung
                        var aktuellerWert = $('textarea#beschreibung').val();
						var pos = aktuellerWert.search("<br /><strong>Die gesamt Menge");
						if(pos > 0) {
                            var alterWert = aktuellerWert.substring(0, pos);
                        } else {
							if(pos == '-1') {
                                var alterWert = aktuellerWert;
                            }else{
                                var alterWert = '';
                            }
                        }
						$('textarea#beschreibung').ckeditor().editor.updateElement();
						$('textarea#beschreibung').val(alterWert + data.beschreibung);
                        $('#menge').val(data.menge);
					} else {
					    alert(data.statusText);
				    }
				    // App.loading.close();
			    }
		})
    }

	function BelegpositionberechnungDelete(belegpositionid){
			$.ajax({
				url: 'index.php?module=belegpositionberechnung&action=deleteData',
				data: {
				 belegpositionberechnungid: belegpositionid
				},
				type: 'post',
				dataType: 'json',
				beforeSend: function() {
					App.loading.open();
				},
				success: function(data) {
					if (data.status == 1) {
						updateLiveTable();
						formReset();
						$('#editfieldset').addClass('editfieldsethide');
					} else {
						alert(data.statusText);
					}
					App.loading.close();
				}
			});
	}

    function BelegpositionberechnungEdit(belegpositionid){
		$('#editfieldset').removeClass('editfieldsethide');
	    $.ajax({
		    url: 'index.php?module=belegpositionberechnung&action=getData',
		    data: {
			    belegpositionberechnungid: belegpositionid
		    },
		    type: 'post',
		    dataType: 'json',
		    beforeSend: function() {
			    App.loading.open();
		    },
		    success: function(data) {
		    	formReset();
			    if (data.status == 1) {
                  $('#editbelegpositionid').val(belegpositionid);
                  $('#editname').val(data.name);
                  $('#editlaenge').val(data.laenge);
                  $('#editbreite').val(data.breite);
                  $('#editmenge').val(data.menge);
				} else {
			    	if(data.status == 'new') {
					  $('#editbelegpositionid').val('0');
					} else {
                        alert(data.statusText);
                    }
			    }
			    App.loading.close();
		    }
	    });
    }

    function BelegpositionberechnungSave(){
	    $.ajax({
		    url: 'index.php?module=belegpositionberechnung&action=saveData',
		    data: {
			    belegpositionberechnungid: $('#editbelegpositionid').val(),
			    belegtype: $.urlParam('module'),
				belegtypeposition: $.urlParam('id'),
                name: $('#editname').val(),
                laenge: $('#editlaenge').val(),
                breite: $('#editbreite').val(),
                menge: $('#editmenge').val(),
                artikelnummer: $('#nummer').val()
		    },
		    type: 'post',
		    dataType: 'json',
		    beforeSend: function() {
			    App.loading.open();
		    },
		    success: function(data) {
			    if (data.status == 1) {
				    updateLiveTable();
					formReset();
					$('#editfieldset').addClass('editfieldsethide');
			    } else {
				    alert(data.statusText);
			    }
			    App.loading.close();
		    }
	    });
    }

    function formReset(){
	    $('#editbelegpositionid').val('');
	    $('#editname').val('');
	    $('#editlaenge').val('');
	    $('#editbreite').val('');
	    $('#editmenge').val('');
    }

    function updateLiveTable(i) {
        var oTableL = $('#belegpositionen_list').dataTable();
        if (oTableL) {
            oTableL.fnFilter('%');
            oTableL.fnFilter('');
        }
    }

    $.urlParam = function(name){
        var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
        return results[1] || 0;
    }
</script>
<style>
    #belegpositionenberechnen{ display: none; }
    .editfieldsethide{ display: none; }
</style>
<div id="belegpositionenberechnen" title="Bearbeiten">
    <table width="100%" border="0">
        <tr>
            <td valign="top">
                [MESSAGE]
                [TAB1]
                [TABNEXT]
            </td>
            <td valign="top">
                <fieldset>
                    <legend>{|Aktionen|}</legend>
                    <div style="text-align: center;"><input class="btnGreenNew" type="button" name="anlegen" value="&#10010; Neuen Eintrag anlegen" onclick="BelegpositionberechnungEdit(0);"></div>
                </fieldset>
                <fieldset id="editfieldset" class="editfieldsethide">
                    <input type="hidden" id="editbelegtype" name="editbelegtype" value="0"/>
                    <input type="hidden" id="editbelegtypeposition" name="editbelegtypeposition" value="0"/>
                    <input type="hidden" id="editbelegpositionid" name="editbelegpositionid" value="0"/>
                    <legend>Name:</legend>
                    <input type="text" id="editname" name="editname" style="width: 100%;"/>
                    <legend>L&auml;nge:</legend>
                    <input type="text" id="editlaenge" name="editlaenge" style="width: 100%;" />
                    <legend>Breite:</legend>
                    <input type="text" id="editbreite" name="editbreite" style="width: 100%;" />
                    <legend>Menge:</legend>
                    <input type="text" id="editmenge" name="editmenge" style="width: 100%;" />
                    <br /><br />
                    <div style="text-align: center;"><input type="button" value="Speichern" onclick="BelegpositionberechnungSave();"/></div>
                </fieldset>
            </td>
        </tr>
    </table>
</div>
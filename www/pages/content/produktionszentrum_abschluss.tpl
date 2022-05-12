<style>
  .auftraginfo_cell {
    color: #636363;border: 1px solid #ccc;padding: 5px;
  }

  .auftrag_cell {
    color: #636363;border: 1px solid #fff;padding: 0px; margin:0px;
  }
</style>
<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1">[TABTEXT]</a></li>
  </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
	<div id="tabs-1">
		<form method="POST" id="frmabschluss">
			[PRODMESSAGE]
			[MESSAGE]
			<table width="100%">
				<tr valign="top">
					<td width="50%">
						<fieldset style="height:430px">
							<legend>{|Produktion abschliessen|}</legend>

                                                            <table class="mkTableFormular">
								<tr>
									<td width="200"{|>Menge Ausschuss|}:</td>
									<td><input type="text" name="mengeausschuss" value="[MENGEAUSSCHUSS]" size="30"></td>
									<td>&nbsp;<i>({|Menge fehlerhafte Produktionen|})</i></td>
								</tr>
								<tr>
									<td>{|Menge Erfolgreich|}: </td>
									<td><input type="text" name="mengeerfolgreich" id="mengeerfolgreich" value="[MENGEERFOLGREICH]" size="30"></td>
									<td>&nbsp;<i>({|Menge erfolgreicher Produktionen|}[EFOLGREICHAUSGELAGERT])</i></td>
								</tr>
								<tr>
									<td>{|Automatisch einlagern|}: </td>
									<td><input type="radio" value="nein" name="same"> {|Nein|}</td>
								</tr>
								<tr>
									<td></td>
									<td><input type="radio" value="ja" name="same" checked="checked"> {|Ja|}</td>
								</tr>
								<tr>
									<td>{|Lagerplatz|}: </td>
									<td><input type="text" name="lagerplatz" id="lagerplatz" value="[LAGERPLATZ]" size="30"></td>
								</tr>
								<tr>
									<td>{|Als Artikel-Nr.|}: </td>
									<td><input type="text" name="artikel" value="[VORSCHLAGARTIKEL]" id="artikel" size="30"></td>
								</tr>

								[VORCHARGE]
								<tr>
									<td>{|Erzeugte Charge|}: </td>
									<td><input type="text" name="charge" value="[CHARGE]" id="charge" size="30"></td>
									<td>&nbsp;<i>{|f&uuml;r Artikel mit eigenen erzeugten Chargen|}</i></td>
								</tr>
								[NACHCHARGE]

								[VORMHD]
								<tr>
									<td>{|MHD|}: </td>
									<td><input type="text" name="mhd" value="[MHD]" id="mhd" size="30"></td>
									<td><i>Mindesthaltbarkeitsdatum</i></td>
								</tr>
								[NACHMHD]

								<tr>
									<td>{|Bemerkung|}:</td>
									<td colspan="2"><textarea rows="8" name="bemerkung" id="bemerkung" cols="70">[BEMERKUNG]</textarea></td>
								</tr>
								
							
								[VORAUFTRAGMENGENANPASSEN]
								<tr>
									<td>{|Menge im Auftrag anpassen|}:</td>
									<td><input type="checkbox" name="auftragmengenanpassen" [AUFTRAGMENGENANPASSEN] id="auftragmengenanpassen" size="30"></td>
								</tr>
								[NACHAUFTRAGMENGENANPASSEN]
							</table>
						</fieldset>
							<fieldset>
							<legend>{|Transport Etiketten Drucken|}</legend>
                                                            <table class="mkTableFormular">
                                                                <tr>
									<td>{|Drucken|}:</td>
									<td colspan="2"><input type="checkbox" name="printlabeltransport" value="1" id="printlabeltransport" [PRINTLABELTRANSPORT]></td>
								</tr>

			                                        <tr>
									<td>{|Menge|}:</td>
									<td colspan="2"><input type="text" name="amountoflabeltransport" size="10" value="1"></td>
								</tr>

		                                                <tr>
									<td colspan="3"><br></td>
								</tr>
	
		                                                <tr>
									<td>{|Etiketten|}:</td>
									<td colspan="2"><select name="labeltransport" id="labeltransport">[LABELTRANSPORT]</select></td>
								</tr>
			                                        <tr>
									<td>{|Drucker|}:</td>
									<td colspan="2"><select name="printerlabeltransport" id="printerlabeltransport">[PRINTERLABELTRANSPORT]</select></td>
								</tr>
							</table>
						</fieldset>
					</td>
					<td>
						<fieldset style="min-height:200px">
							<legend>{|Gebuchte Zeiten|}</legend>
							[ZEITGEBUCHT]
						</fieldset>
						<fieldset style="min-height:190px">
							<legend>{|Eingelagert|}</legend>
							[EINLAGERUNG]
						</fieldset>
					</td>
				</tr>
			</table>
			<br>
			<center>
				<input type="submit" style="min-width:250px;" name="teilmengeeinlagern" id="teilmengeeinlagern" value="{|Teilmenge einlagern|}" />&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="submit" style="min-width:250px;" id="submitabschliessen" name="submitabschliessen" value="{|Einlagern + Produktion abschliessen|}">
				<input type="button" class="btnBlueNew" onclick='ProduktionslabelEdit();' value="Produktionslabel" style="width:300px">
			</center>
		</form>
	</div>
</div>
[PRODUKTIONSZENTRUM_ABSCHLUSS_HOOK1]


<div id="editProduktionslabel" style="display:none;" title="Bearbeiten">
	<form method="post">
		<fieldset>
			<legend>{|Produktionslabel|}</legend>
			<table>
				<tr>
					<td width="70">{|Etikett|}:</td>
					<td><select name="etikettproduktionslabel" id="etikettproduktionslabel">
						</select></td>
				</tr>
				<tr>
					<td>{|Drucker|}:</td>
					<td><select name="druckerproduktionslabel" id="druckerproduktionslabel">
						</select></td>
				</tr>
				<tr class="trmenge">
					<td>{|Menge|}:</td>
					<td><input type="text" size="5" name="mengeproduktionslabel" id="mengeproduktionslabel"></td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>

<script>
  $(document).ready(function() {
    $('#etikettproduktionslabel').focus();

		$('#etikettproduktionslabel').on('change', function(){
        $.ajax({
            url: 'index.php?module=produktion&action=getproduktionslabel&cmd=getvariables',
            data: {
                etikett:$('#etikettproduktionslabel').val()
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                App.loading.open();
            },
            success: function(data) {
                if($('#editProduktionslabel tr.vars').length) {
                    $('#editProduktionslabel tr.vars').remove();
                }
                if(typeof data.html != 'undefined') {
                    $('#editProduktionslabel tr.trmenge').after(data.html);
                }
                App.loading.close();
            }
        });
		});

    $("#editProduktionslabel").dialog({
      modal: true,
      bgiframe: true,
      closeOnEscape:false,
      minWidth:500,
      maxHeight:700,
      autoOpen: false,
      buttons: {
        ABBRECHEN: function() {
          ProduktionslabelReset();
          $(this).dialog('close');
        },
        DRUCKEN: function() {
          ProduktionslabelEditSave();
        }
      }
    });

    $("#editProduktionslabel").dialog({
      close: function( event, ui ) { ProduktionslabelReset();}
    });

	});

  function ProduktionslabelReset()
  {
		$('#editProduktionslabel').find('#etikettproduktionslabel').val('');
    $('#editProduktionslabel').find('#druckerproduktionslabel').val('');
    $('#editProduktionslabel').find('#mengeproduktionslabel').val('');
  }

  function ProduktionslabelEdit() {
    $.ajax({
      url: 'index.php?module=produktion&action=getproduktionslabel',
      data: {
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        document.getElementById("etikettproduktionslabel").innerHTML = data.etikettenhtml;
        document.getElementById("druckerproduktionslabel").innerHTML = data.druckerhtml;

        App.loading.close();
        $("#editProduktionslabel").dialog('open');
	$('#etikettproduktionslabel').trigger('change');
      }
    });
  }

  function ProduktionslabelEditSave(){
    var dataObj = {
        //Alle Felder die fürs editieren vorhanden sind
        etikett: $('#etikettproduktionslabel').val(),
        drucker: $('#druckerproduktionslabel').val(),
        menge: $('#mengeproduktionslabel').val()
    };
    $('#editProduktionslabel input').each(function () {
			if(typeof this.id != 'undefined' && this.id.substr(0,4) === 'var_') {
			    dataObj[this.id] = $(this).val();
      }
    });
    $.ajax({
      url: 'index.php?module=produktion&action=etikettendrucken&id=[ID]&cmd=produktionslabel',
      data: dataObj,
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        App.loading.close();
        if(data.status == 1){
          ProduktionslabelReset();
          $("#editProduktionslabel").dialog('close');
        }else{
          alert(data.statusText);
        }
      }
    });
  }

</script>

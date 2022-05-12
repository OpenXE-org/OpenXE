<!-- gehort zu tabview -->

<div id="tabs">
    <ul>
        <li><a href="#tabs-7">&Uuml;bersicht</a></li>
        <li><a href="#tabs-5">Kalkulation</a></li>
        <!--<li><a href="#tabs-2">Neuen Einkaufspreis anlegen</a></li>-->
    </ul>



<!-- erstes tab -->
<div id="tabs-7">
[OPENDISABLE]
<table height="80" width="100%">
  <tr>
    <td>
      <div class="row">
      <div class="row-height">
      <div class="col-xs-12 col-md-10 col-md-height">
      <div class="inside inside-full-height">
				<fieldset>
					<div class="filter-box filter-usersave">
						<div class="filter-block filter-inline">
							<div class="filter-title">{|Filter|}</div>
							<ul class="filter-list">
								<li class="filter-item">
									<label for="alteeinkaufspreise" class="switch">
										<input type="checkbox" id="alteeinkaufspreise" />
										<span class="slider round"></span>
									</label>
									<label for="alteeinkaufspreise">{|alte Einkaufspreise anzeigen|}</label>
								</li>
							</ul>
						</div>
					</div>
				</fieldset>
      </div>
      </div>
      <div class="col-xs-12 col-md-2 col-md-height">
      <div class="inside inside-full-height">
        <fieldset>
          <legend>{|Aktionen|}</legend>
          <input type="button" class="btnGreenNew" name="neuereinkaufspreis" value="&#10010; Neuer Einkaufspreis" onclick="EinkaufspreiseEdit(0);">
        </fieldset>
      </div>
      </div>
      </div>
      </div>
    </td>
  </tr>
</table>
[CLOSEDISABLE]

[MESSAGE]
[TAB7]
</div>


<div id="tabs-5">

<h2 class="greyh2">Einkaufspreis Min/Max</h2>
[TAB5KALKULATION]
[TAB5KALKEK]
<br>
<h2 class="greyh2">Staffelpreise</h2>
<div style="overflow-x: scroll; width:100%;">
<table border="0" cellpadding="5" width="100%">
[TABELLE]
</table>
</div>
<br>
<h2 class="greyh2">Fehlende Einzelpreise</h2>
[TAB5]
</div>

<div id="tabs-2">
<!--[TAB2]-->
</div>


<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->


<div id="editEinkaufspreis" style="display:none;" title="Bearbeiten"> 
  <form action="" method="post" name="eprooform" >
	  <input type="hidden" id="e_id">
	  <input type="hidden" name = "e_artikelid" id="e_artikelid" value="[ID]">
	  [FORMHANDLEREVENT]
    <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
      <tbody>
        <tr valign="top" colspan="3">
          <td colspan="3">

						[MESSAGE]
					<div class="row">
						<div class="row-height">
							<div class="col-xs-12 col-md-12 col-md-height">
								<div class="inside inside-full-height">

				  <fieldset>
				  	<legend>&nbsp;Lieferant&nbsp;</legend>
				  	<table cellspacing="5" border="0" width="700">
				  		<tr>
				  			<td width="170">{|Standardlieferant|}:</td>
				  			<td colspan="4"><input type="checkbox" name="standard" id="standard" value ="1"></td>
				  		</tr>
				  		<tr>
				  			<td width="170"><b>Lieferant:</b></td>
				  			<td colspan="3">[ADRESSESTART]<input type="text" size="45" name="adresse" id="adresse" rule="notempty" msg="Pflichtfeld!"><div id="pflicht1" style="float:right"><font color="red"><p>Pflichtfeld!</p></font></div>[ADRESSEENDE]</td>
				  			<td>[BUTTONLADEN]</td>
				  		</tr>
				  		<tr>
				  			<td><b>Bezeichnung bei Lieferant:</b></td>
				  			<td colspan="4"><input name="bezeichnunglieferant" id="bezeichnunglieferant" type="text" size="70"></td>
				  		</tr>
				  		<tr>
				  			<td>{|Artikelnummer bei Lieferant|}:</td>
				  			<td colspan="4"><input name="bestellnummer" id="bestellnummer" type="text" size="70"></td>
				  		</tr>
				  	</table>
				  </fieldset>
								</div>
							</div>
						</div>
					</div>

						<div class="row">
							<div class="row-height">
								<div class="col-xs-12 col-md-12 col-md-height">
									<div class="inside inside-full-height">

  				<fieldset>
  					<legend>&nbsp;Einkaufspreis&nbsp;</legend>
  					<table cellspacing="5" border="0" width="900">
  						<tr>
  							<td width="170"><b>Ab Menge:</b></td>
  							<td width="180"><input name="ab_menge" id="ab_menge" rule="notempty" msg="Pflichtfeld!" type="text" size="10"><div id="pflicht2" style="float:right"><font color="red"><p>Pflichtfeld!</p></font></div>&nbsp;</td>
  							<td width="10">&nbsp;</td>
  							<td width="210" nowrap>{|Verpackungseinheit (Menge in VPE)|}:</td>
  							<td><input type="text" size="10" name="vpe" id="vpe">&nbsp;[VPEPREIS]</td>
  						</tr>

  						<tr>
  							<td width="170"><b>Preis pro St&uuml;ck:</b><br><i>(Immer Einzelst&uuml;ckpreis!)</i></td>
  							<td width="180"><input name="preis" id="preis" type="text" size="10" rule="notempty" msg="Pflichtfeld!"><div id="pflicht3" style="float:right"><font color="red"><p>Pflichtfeld!</p></font></div>&nbsp;
  															<select name="waehrung" id="waehrung">
				                          <!--<option value="EUR">EUR</option><option value="USD">USD</option>
				                          <option value="CAD">CAD</option>
				                          <option value="CHF">CHF</option>
				                          <option value="GBP">GBP</option>-->
                                  [WAEHRUNGEINKAUF]

                          			</select></td>
                <td width="10">&nbsp;</td>
                <td width="160" norwap>{|Preis f&uuml;r VPE|}:</td>
                <td><span id="livepreisvpe"></span></td>
              </tr>

  						<tr valign="top">
  							<td width="170"></td>
  							<td width="180" rowspan="2">[PREISRECHNER]</td>
  							<td width="10">&nbsp;</td>
  							<td width="150" valign="top">Preis nicht berechnet aus W&auml;hrungstabelle:</td>
  							<td><input type="checkbox" name="nichtberechnet" id="nichtberechnet" value="1" /></td>
  						</tr>
  						<tr>
          			<td width="10">&nbsp;</td><td colspan="2" align="right">[PREISTABELLE]</td>
          		</tr>

  						[DISABLEOPENSTOCK]
  						<tr>
  							<td width="170">{|Preisanfrage vom|}:</td>
  							<td width="180"><input name="preis_anfrage_vom" id="preis_anfrage_vom" type="text" size="10">&nbsp;</td>
  							<td width="10">&nbsp;</td>
  							<td width="150">{|G&uuml;ltig bis|}:</td>
  							<td><input name="gueltig_bis" id="gueltig_bis" type="text" size="10"></td>
  						</tr>
  						[DISABLECLOSESTOCK]
  					</table></fieldset>
									</div>
								</div>
							</div>
						</div>
					  [DISABLEOPENSTOCK]
						<div class="row">
							<div class="row-height">
								<div class="col-xs-12 col-md-12 col-md-height">
									<div class="inside inside-full-height">
					  <fieldset>
					  	<legend>&nbsp;Weitere Informationen&nbsp;</legend>
					  	<table cellspacing="5" border="0" width="800">
  							<tr>
  								<td width="170">{|Lagerbestand Lieferant|}:</td>
  								<td width="180"><input name="lager_lieferant" id="lager_lieferant" type="text" size="5"> am <input name="datum_lagerlieferant" id="datum_lagerlieferant" type="text" size="10"></td>
  								<td width="210">{|Sicherheitslager|}:</td
  								><td><input name="sicherheitslager" id="sicherheitslager" type="text" size="10"></td>
  							</tr>

  							<tr>
  								<td width="150">{|Lieferzeit Standard|}:</td>
  								<td width="190"><input name="lieferzeit_standard" id="lieferzeit_standard" type="text" size="10">&nbsp;
																	<select name="lieferzeit_standard_einheit" id="lieferzeit_standard_einheit">
																		<option value="wochen">Wochen</option>
																		<option value="tage">Tage</option>
																	</select>
									</td>
  								<td width="160">{|Lieferzeit Aktuell|}:</td>
  								<td width="190"><input name="lieferzeit_aktuell" id="lieferzeit_aktuell" type="text" size="10">
											<select name="lieferzeit_aktuell_einheit" id="lieferzeit_aktuell_einheit">
												<option value="wochen">Wochen</option>
												<option value="tage">Tage</option>
											</select></td>
  							</tr>

  						</table>
  					</fieldset>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="row-height">
								<div class="col-xs-12 col-md-12 col-md-height">
									<div class="inside inside-full-height">
  					<fieldset>
  						<legend>&nbsp;Rahmenvertrag&nbsp;</legend>
  						<table cellspacing="5" border="0" width="700">
  							<tr>
  								<td width="170">{|Rahmenvertrag|}:</td>
  								<td width="180"><input name="rahmenvertrag" id="rahmenvertrag" type="checkbox" value="1"></td>
  								<td width="10">&nbsp;</td>
  								<td width="210">{|Menge|}:</td>
  								<td><input type="text" name="rahmenvertrag_menge" id="rahmenvertrag_menge" size="10"></td>
  							</tr>
  							<tr>
  								<td width="170">{|Von|}:</td>
  								<td width="180"><input name="rahmenvertrag_von" id="rahmenvertrag_von" type="text" size="10">&nbsp;</td>
  								<td width="10">&nbsp;</td>
  								<td width="150">{|Bis|}:</td>
  								<td><input name="rahmenvertrag_bis" id="rahmenvertrag_bis" type="text" size="10"></td>
  							</tr>
  						</table>
  					</fieldset>
									</div>
								</div>
							</div>
						</div>

  					[DISABLECLOSESTOCK]
						<div class="row">
							<div class="row-height">
								<div class="col-xs-12 col-md-12 col-md-height">
									<div class="inside inside-full-height">
  					<fieldset>
  						<legend>&nbsp;Interne Bemerkung&nbsp;</legend>
						  <table cellspacing="5" border="0" width="700">
							  <tr>
							  	<td width="170">{|Interner Kommentar|}:</td>
							  	<td colspan="4"><textarea name="bemerkung" id="bemerkung" rows="3" cols="70"></textarea></td>
							  </tr>
						  </table>

  					</fieldset>
									</div>
								</div>
							</div>
						</div>
         	</td>
        </tr>
      	<tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
      		<td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
		      <!--<input type="submit"
		      value="Speichern" name="submit" />--> [ABBRECHEN]</td>
      	</tr>
      </tbody>
  	</table>
  </form>
  [PREISTABELLEPOPUP]
</div>


<script type="text/javascript">

function recalcvpe()
{
  var span = document.getElementById("livepreisvpe");
  var preis = document.getElementById("preis").value;
  var vpe = document.getElementById("vpe").value;

  preis = preis.replace(',', '.');
  vpe= vpe.replace(',', '.');

  span.textContent = parseFloat(preis*vpe).toFixed(2);
}

window.setInterval(recalcvpe, 300);

$(document).ready(function() {
  $('#standard').focus();

  $("#editEinkaufspreis").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:1000,
    maxHeight:800,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        EinkaufspreiseReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        EinkaufspreiseEditSave();
      }
    }
  });

  $("#editEinkaufspreis").dialog({
    close: function( event, ui ) {EinkaufspreiseReset();}
  });

});

function EinkaufspreiseReset(){
  $('#editEinkaufspreis').find('#e_id').val('');
  //$('#editEinkaufspreis').find('#e_artikelid').val('');
  $('#editEinkaufspreis').find('#standard').prop("checked", false);
  $('#editEinkaufspreis').find('#adresse').val('');
  $('#editEinkaufspreis').find('#bezeichnunglieferant').val('');
  $('#editEinkaufspreis').find('#bestellnummer').val('');
  $('#editEinkaufspreis').find('#ab_menge').val('');
  $('#editEinkaufspreis').find('#vpe').val('');
  $('#editEinkaufspreis').find('#preis').val('');
  $('#editEinkaufspreis').find('#waehrung').val('[STANDARDWAEHRUNG]');
  $('#editEinkaufspreis').find('#livepreisvpe').val('');
  $('#editEinkaufspreis').find('#nichtberechnet').prop("checked", false);
  $('#editEinkaufspreis').find('#preis_anfrage_vom').val('');
  $('#editEinkaufspreis').find('#gueltig_bis').val('');
  $('#editEinkaufspreis').find('#lager_lieferant').val('');
  $('#editEinkaufspreis').find('#datum_lagerlieferant').val('');
  $('#editEinkaufspreis').find('#sicherheitslager').val('');
  $('#editEinkaufspreis').find('#lieferzeit_standard').val('');
  $('#editEinkaufspreis').find('#lieferzeit_standard_einheit').val('wochen');
  $('#editEinkaufspreis').find('#lieferzeit_aktuell').val('');
  $('#editEinkaufspreis').find('#lieferzeit_aktuell_einheit').val('wochen');
  $('#editEinkaufspreis').find('#rahmenvertrag').prop("checked", false);
  $('#editEinkaufspreis').find('#rahmenvertrag_menge').val('');
  $('#editEinkaufspreis').find('#rahmenvertrag_von').val('');
  $('#editEinkaufspreis').find('#rahmenvertrag_bis').val('');
  $('#editEinkaufspreis').find('#bemerkung').val('');
}


function EinkaufspreiseEditSave() {

  $.ajax({
    url: 'index.php?module=artikel&action=einkauf&cmd=popupsave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      eid: $('#e_id').val(),
      eartikelid: $('#e_artikelid').val(),
      estandard: $('#standard').prop("checked")?1:0,
      eadresse: $('#adresse').val(),
      ebezeichnunglieferant: $('#bezeichnunglieferant').val(),
      ebestellnummer: $('#bestellnummer').val(),
      eab_menge: $('#ab_menge').val(),
      evpe: $('#vpe').val(),
      epreis: $('#preis').val(),
      ewaehrung: $('#waehrung').val(),
      elivepreisvpe: $('#livepreisvpe').val(),
      enichtberechnet: $('#nichtberechnet').prop("checked")?1:0,
      epreis_anfrage_vom: $('#preis_anfrage_vom').val(),
      egueltig_bis: $('#gueltig_bis').val(),
      elager_lieferant: $('#lager_lieferant').val(),
      edatum_lagerlieferant: $('#datum_lagerlieferant').val(),
      esicherheitslager: $('#sicherheitslager').val(),
      elieferzeit_standard: $('#lieferzeit_standard').val(),
			elieferzeit_standard_einheit: $('#lieferzeit_standard_einheit').val(),
      elieferzeit_aktuell: $('#lieferzeit_aktuell').val(),
			elieferzeit_aktuell_einheit: $('#lieferzeit_aktuell_einheit').val(),
      erahmenvertrag: $('#rahmenvertrag').prop("checked")?1:0,
      erahmenvertrag_menge: $('#rahmenvertrag_menge').val(),
      erahmenvertrag_von: $('#rahmenvertrag_von').val(),
      erahmenvertrag_bis: $('#rahmenvertrag_bis').val(),
      ebemerkung: $('#bemerkung').val()
            
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        EinkaufspreiseReset();
        updateLiveTable();
        $("#editEinkaufspreis").dialog('close');
      } else {
        if(data.statusText.includes("Adressef") || data.statusText.includes("Mengef") || data.statusText.includes("Preisf")){
          if(data.statusText.includes("Adressef")){
            $('#pflicht1').show();
          }                  
          if(data.statusText.includes("Mengef")){
            $('#pflicht2').show();
          }
          if(data.statusText.includes("Preisf")){
            $('#pflicht3').show();
          }                 
        }else{
          alert(data.statusText);
        }
                              
      }
    }
  });


}

function EinkaufspreiseEdit(id) {
	$('#pflicht1').hide();
  $('#pflicht2').hide();
  $('#pflicht3').hide();
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=artikel&action=einkauf&cmd=popupedit',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if(data.id > 0)
        {
          $('#editEinkaufspreis').find('#e_id').val(data.id);
          $('#editEinkaufspreis').find('#e_artikelid').val([ID]);
          $('#editEinkaufspreis').find('#standard').prop("checked",data.standard==1?true:false);
          $('#editEinkaufspreis').find('#adresse').val(data.adresse);
          $('#editEinkaufspreis').find('#bezeichnunglieferant').val(data.bezeichnunglieferant);
          $('#editEinkaufspreis').find('#bestellnummer').val(data.bestellnummer);
          $('#editEinkaufspreis').find('#ab_menge').val(data.ab_menge);
          $('#editEinkaufspreis').find('#vpe').val(data.vpe);
          $('#editEinkaufspreis').find('#preis').val(data.preis);
          $('#editEinkaufspreis').find('#waehrung').val(data.waehrung);
          $('#editEinkaufspreis').find('#livepreisvpe').val(data.waehrung);
          $('#editEinkaufspreis').find('#nichtberechnet').prop("checked",data.nichtberechnet==1?true:false);
          $('#editEinkaufspreis').find('#preis_anfrage_vom').val(data.preis_anfrage_vom);
          $('#editEinkaufspreis').find('#gueltig_bis').val(data.gueltig_bis);
          $('#editEinkaufspreis').find('#lager_lieferant').val(data.lager_lieferant);
          $('#editEinkaufspreis').find('#datum_lagerlieferant').val(data.datum_lagerlieferant);
          $('#editEinkaufspreis').find('#sicherheitslager').val(data.sicherheitslager);
          $('#editEinkaufspreis').find('#lieferzeit_standard').val(data.lieferzeit_standard);
          $('#editEinkaufspreis').find('#lieferzeit_standard_einheit').val(data.lieferzeit_standard_einheit);
          $('#editEinkaufspreis').find('#lieferzeit_aktuell').val(data.lieferzeit_aktuell);
          $('#editEinkaufspreis').find('#lieferzeit_aktuell_einheit').val(data.lieferzeit_aktuell_einheit);
          $('#editEinkaufspreis').find('#rahmenvertrag').prop("checked",data.rahmenvertrag==1?true:false);
          $('#editEinkaufspreis').find('#rahmenvertrag_menge').val(data.rahmenvertrag_menge);
          $('#editEinkaufspreis').find('#rahmenvertrag_von').val(data.rahmenvertrag_von);
          $('#editEinkaufspreis').find('#rahmenvertrag_bis').val(data.rahmenvertrag_bis);
          $('#editEinkaufspreis').find('#bemerkung').val(data.bemerkung);
            
        } 
        App.loading.close();
        $("#editEinkaufspreis").dialog('open');
      }
    });
  } else {
    EinkaufspreiseReset(); 
    $("#editEinkaufspreis").dialog('open');
  }

}

function updateLiveTable(i) {
    var oTableL = $('#einkaufspreise').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);  
}

function EinkaufspreiseDelete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({
            url: 'index.php?module=artikel&action=einkauf&cmd=delete',
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


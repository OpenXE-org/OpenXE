<div id="editGruppen" style="display:none;" title="Bearbeiten">
<form method="post">
  <input type="hidden" id="e_id">
  
  	<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    	<tbody>
      	<tr valign="top" colspan="3">
        	<td>
						<div class="row">
							<div class="row-height">
								<div class="col-xs-12 col-md-12 col-md-height">
									<div class="inside inside-full-height">
										<fieldset>
											<legend>{|Einstellung|}</legend>
											<table width="100%">
												<tr>
													<td width="200">{|Aktiv|}:</td><td><input type="checkbox" name="aktiv" id="aktiv" value="1"></td>
												</tr>
												<tr>
													<td width="200">{|Bezeichnung|}:</td><td><input type="text" name="name" id="name" size="80" rule="notempty" msg="Pflichfeld!" tabindex="2"></td>
												</tr>
												<tr>
													<td width="200">{|Kennziffer|}:</td><td><input type="text" name="kennziffer" id="kennziffer" size="80" tabindex="2" rule="notempty" msg="Pflichfeld!">&nbsp;<i>{|z.B.|} 01, 02, ...</i></td>
												</tr>
												<tr>
													<td width="200">{|Interne Bemerkung|}:</td><td><textarea rows="10" cols="130" name="internebemerkung" id="internebemerkung"></textarea></td><td>
												</tr>
												<tr>
													<td width="200">{|Art|}:</td><td><select name="art" id="art">[ART]<!--<option value="gruppe">Gruppe</option><option value="preisgruppe">Preisgruppe</option>
													<option value="verband">Verband</option>--></select></td>
												</tr>
												<tr>
													<td width="200">{|Projekt|}:</td><td><input type="text" name="projekt" id="projekt" size="80" tabindex="2">&nbsp;<i>{|optionale Angabe|}</i></td>
												</tr>
												<tr>
													<td width="200">{|Kategorie|}:</td><td><input type="text" name="kategorie" id="kategorie" size="80" tabindex="2">&nbsp;<i>{|optionale Angabe|}</i></td>
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
										<div id="rabatte">
											<fieldset>
												<legend>{|Rabatte / Zahlungen|}</legend>
												<table width="100%">
													<tr>
														<td width="200">{|Grundrabatt|}:</td><td><input type="text" name="grundrabatt" id="grundrabatt" size="20" tabindex="2">&nbsp;%&nbsp;<i>{|z.B.|} 20 {|f&uuml;r|} 20% ({|der Rabatt gilt nur f&uuml;r Standardpreise, nicht f&uuml;r Gruppen- oder Kundenspezifische Preise|}.)</i></td>
													</tr>
													<tr>
														<td width="200">{|Zahlungszieltage|}:</td><td><input type="text" name="zahlungszieltage" id="zahlungszieltage" size="20" tabindex="2">&nbsp;{|Tage|}&nbsp;<i>{|z.B.|} 30</i></td>
													</tr>
													<tr>
														<td width="200">{|Skonto|}:</td><td><input type="text" name="zahlungszielskonto" id="zahlungszielskonto" size="20" tabindex="2">&nbsp;%&nbsp;<i>{|z.B.|} 2</i></td>
													</tr>
													<tr>
														<td width="200">{|Skonto Tage|}:</td><td><input type="text" name="zahlungszieltageskonto" id="zahlungszieltageskonto" size="20" tabindex="2">&nbsp;{|Tage|}&nbsp;<i>{|z.B.|} 10</i></td>
													</tr>
													<tr>
														<td>{|Porto frei aktiv|}:</td><td><input type="checkbox" name="portofrei_aktiv" id="portofrei_aktiv" value="1">&nbsp;{|ab|}&nbsp;<input type="text" name="portofreiab" id="portofreiab" size="12">&nbsp;&euro;&nbsp;<i>{|Porto frei ab bestimmtem Umsatz (netto)|}</i></td>
													</tr>
												</table>
											</fieldset>
										</div>
									</div>
								</div>
							</div>
						</div>


						<div id="rabatte2">
							<fieldset>
								<legend>{|Verbandsoptionen|}</legend>
    						<table width="100%">
          				<tr>
          					<td width="200">{|Rabatte|}*:</td><td>

											<table>
												<tr>
													<td>{|Rabatt 1|}:</td><td><input type="text" name="rabatt1" id="rabatt1" size="5"> %</td><td width="100">&nbsp;</td>
													<td>{|Bonus 1|}:</td><td><input type="text" name="bonus1" id="bonus1" size="5"> % {|ab|} <input type="text" name="bonus1_ab" id="bonus1_ab" size="10"> &euro;</td><td width="50">&nbsp;</td>
													<td>{|Bonus 6|}:</td><td><input type="text" name="bonus6" id="bonus6" size="5"> % {|ab|} <input type="text" name="bonus6_ab" id="bonus6_ab" size="10"> &euro;</td>
												</tr>
												<tr>
													<td>{|Rabatt 2|}:</td><td><input type="text" name="rabatt2" id="rabatt2" size="5"> %</td><td width="100">&nbsp;</td>
													<td>{|Bonus 2|}:</td><td><input type="text" name="bonus2" id="bonus2" size="5"> % {|ab|} <input type="text" name="bonus2_ab" id="bonus2_ab" size="10"> &euro;</td><td width="50">&nbsp;</td>
													<td>{|Bonus 7|}:</td><td><input type="text" name="bonus7" id="bonus7" size="5"> % {|ab|} <input type="text" name="bonus7_ab" id="bonus7_ab" size="10"> &euro;</td>
												</tr>
												<tr>
													<td>{|Rabatt 3|}:</td><td><input type="text" name="rabatt3" id="rabatt3" size="5"> %</td><td width="100">&nbsp;</td>
													<td>{|Bonus 3|}:</td><td><input type="text" name="bonus3" id="bonus3" size="5"> % {|ab|} <input type="text" name="bonus3_ab" id="bonus3_ab" size="10"> &euro;</td><td width="50">&nbsp;</td>
													<td>{|Bonus 8|}:</td><td><input type="text" name="bonus8" id="bonus8" size="5"> % {|ab|} <input type="text" name="bonus8_ab" id="bonus8_ab" size="10"> &euro;</td>
												</tr>
												<tr>
													<td>{|Rabatt 4|}:</td><td><input type="text" name="rabatt4" id="rabatt4" size="5"> %</td><td width="100">&nbsp;</td>
													<td>{|Bonus 4|}:</td><td><input type="text" name="bonus4" id="bonus4" size="5"> % {|ab|} <input type="text" name="bonus4_ab" id="bonus4_ab" size="10"> &euro;</td><td width="50">&nbsp;</td>
													<td>{|Bonus 9|}:</td><td><input type="text" name="bonus9" id="bonus9" size="5"> % {|ab|} <input type="text" name="bonus9_ab" id="bonus9_ab" size="10"> &euro;</td>
												</tr>
												<tr>
													<td>{|Rabatt 5|}:</td><td><input type="text" name="rabatt5" id="rabatt5" size="5"> %</td><td width="100">&nbsp;</td>
													<td>{|Bonus 5|}:</td><td><input type="text" name="bonus5" id="bonus5" size="5"> % {|ab|} <input type="text" name="bonus5_ab" id="bonus5_ab" size="10"> &euro;</td><td width="50">&nbsp;</td>
													<td>{|Bonus 10|}:</td><td><input type="text" name="bonus10" id="bonus10" size="5"> % {|ab|} <input type="text" name="bonus10_ab" id="bonus10_ab" size="10"> &euro;</td>
												</tr>
												<tr>
													<td>{|Provision|}:</td><td><input type="text" name="provision" id="provision" size="5"> %</td><td width="100">&nbsp;</td>
												  <td></td><td></td><td width="50">&nbsp;</td>
												  <td></td><td></td>
												</tr>
												<tr>
													<td>{|Sonderrabatt|}:</td><td colspan="2"><input type="text" name="sonderrabatt_skonto" id="sonderrabatt_skonto" size="5"> % ({|bei Skonto|})</td>
												  <td></td><td></td><td width="50">&nbsp;</td>
												  <td></td><td></td>
												</tr>
												<tr>
													<td colspan="8">* {|der Rabatt gilt nur f&uuml;r Standardpreise, nicht f&uuml;r Gruppen- oder Kundenspezifische Preise|}</td>
												</tr>
											</table>
										</td><td>
									</tr>
								</table>
							</fieldset>

							<fieldset>
								<legend>{|Buchhaltung Einstellungen|}</legend>
							  <table width="100%">
							  	<tr>
							  		<td width="200">{|Zentralregulierung|}:</td><td><input type="checkbox" name="zentralregulierung" id="zentralregulierung" value="1"></td>
							  	</tr>
							   	<!--<tr><td width="200">{|Zentrale Rechnungsadresse|}:</td><td><input type="checkbox" name="zentralerechnung" value="1"></td></tr>-->
									<tr>
										<td>{|Periode der Rechnung|}:</td><td><select name="rechnung_periode" id="rechnung_periode"><option value="1">t&auml;glich</option><option value="2">w&ouml;chentlich</option><option value="4">14t&auml;gig</option><option value="5">monatlich</option><option value="6">einzel</option></select></td>
									</tr>
							    <tr>
							    	<td width="200">{|Anzahl Papierrechnungen|}:</td><td><input type="text" name="rechnung_anzahlpapier" id="rechnung_anzahlpapier" size="5" tabindex="2"></td>
							    </tr>
							    <tr>
							    	<td width="200">{|Rechnung per Mail|}:</td><td><input type="checkbox" name="rechnung_permail" id="rechnung_permail" value="1" tabindex="2"></td>
							    </tr>
							    <tr>
							    	<td width="200">{|Name / Firma|}:</td><td><input type="text" name="rechnung_name" id="rechnung_name" size="50" tabindex="2"></td>
							    </tr>
							    <tr>
							    	<td width="200">{|Abteilung|}:</td><td><input type="text" name="rechnung_abteilung" id="rechnung_abteilung" size="50" tabindex="2"></td>
							    </tr>
							    <tr>
							    	<td width="200">Strasse + Hausnummer:</td><td><input type="text" name="rechnung_strasse" id="rechnung_strasse" size="50" tabindex="2"></td>
							    </tr>
							    <tr>
							    	<td width="200">{|PLZ / Ort|}:</td><td><input type="text" name="rechnung_plz" id="rechnung_plz" size="10">&nbsp;<input type="text" name="rechnung_ort" id="rechnung_ort" size="40"></td>
							    </tr>
							    <tr>
							    	<td width="200">{|Land|}:</td><td><input type="text" name="rechnung_land" id="rechnung_land"></td>
							    </tr>
							    <tr>
							    	<td width="200">{|E-Mail|}:</td><td><input type="text" name="rechnung_email" id="rechnung_email"></td>
							    </tr>
							    <tr>
							    	<td width="200">{|Kundennummer im Verband|}:</td><td><input type="text" name="kundennummer" id="kundennummer"></td>
							    </tr>
								</table>
							</fieldset>


							<fieldset>
								<legend>{|DTA - Datentr&auml;ger Austausch Einstellungen|}</legend>
							  <table width="100%">
							  	<tr>
							  		<td width="200">{|Aktiv|}:</td><td><input type="checkbox" name="dta_aktiv" id="dta_aktiv" value="1"></td>
							  	</tr>
									<tr>
										<td>{|Variante|}:</td><td><select name="dta_variante" id="dta_variante">
													<option value="1">Variante 1</option>
													<option value="2">Variante 2</option>
													<option value="3">Variante 3</option>
													<option value="4">Variante 4</option>
													<option value="5">Variante 5</option>
													<option value="6">Variante 6</option>
													<option value="7">Variante 7</option>
													<option value="8">Variante 8</option>
													<option value="9">Variante 9</option>
													</select></td>
									</tr>
							    <tr>
							    	<td width="200">{|DTA Variablen|}:</td><td><textarea name="dtavariablen" id="dtavariablen" rows="10" cols="50"></textarea></td>
							    </tr>
									<tr>
										<td>{|Periode|}:</td><td><select name="dta_periode" id="dta_periode"><option value="1">15,30</option>
													<option value="2">7,15,22,30</option>
													<option value="3">Dienstag</option>
													<option value="4">Montag</option>
													<option value="5">2,11,27</option>
													<option value="6">2</option>
													<option value="7">Freitags</option>
													</select></td>
									</tr>
							    <tr>
							    	<td width="200">{|Partner ID f&uuml;r DTA|}:</td><td><input type="text" name="partnerid" id="partnerid" size="50"></td>
							    </tr>
							    <tr>
							    	<td width="200">{|Dateiname|}:</td><td><input type="text" name="dta_dateiname" id="dta_dateiname" size="50" tabindex="2"></td>
							    </tr>
							    <tr>
							    	<td width="200">{|E-Mail Empf&auml;nger|}:</td><td><input type="text" name="dta_mail" id="dta_mail" size="50" tabindex="2"></td>
							    </tr>
							    <tr>
							    	<td width="200">{|E-Mail Betreff|}:</td><td><input type="text" name="dta_mail_betreff" id="dta_mail_betreff" size="50" tabindex="2"></td>
							    </tr>
							    <tr>
							    	<td width="200">{|E-Mail Textvorlage|}:</td><td><textarea name="dta_mail_text" id="dta_mail_text" rows="10" cols="50"></textarea></td>
							    </tr>
								</table>
							</fieldset>


						</div>

					</td>
				</tr>

    		<!--<tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    			<td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    			<input type="submit" value="Speichern" />
    		</tr>-->
  
    	</tbody>
  	</table>
</form>

</div>



<script type="text/javascript">

$(document).ready(function() {

	art = document.getElementById('art');
  rabatt = document.getElementById('rabatte');
  rabatt2 = document.getElementById('rabatte2');
  if(art){
    // Hide the target field if priority isn't critical
    
    if(typeof art.options[art.selectedIndex] != 'undefined' && art.options[art.selectedIndex].value =='gruppe'){
		  rabatt.style.display='none';
			rabatt2.style.display='none';
    }else if(typeof art.options[art.selectedIndex] != 'undefined' && art.options[art.selectedIndex].value =='preisgruppe'){
    	rabatt.style.display='';
		  rabatt2.style.display='none';
		}else if(typeof art.options[art.selectedIndex] != 'undefined' && art.options[art.selectedIndex].value =='verband'){
			rabatt.style.display='';
		  rabatt2.style.display='';
		}else{
			rabatt.style.display='none';
			rabatt2.style.display='none';
		}

		art.onchange=function(){
    	if(typeof art.options[art.selectedIndex] != 'undefined' && art.options[art.selectedIndex].value == 'gruppe'){             
				rabatt.style.display='none';
				rabatt2.style.display='none';
    	}else if(typeof art.options[art.selectedIndex] != 'undefined' && art.options[art.selectedIndex].value == 'preisgruppe'){
				rabatt.style.display='';
				rabatt2.style.display='none';
    	}else if(typeof art.options[art.selectedIndex] != 'undefined' && art.options[art.selectedIndex].value == 'verband'){
				rabatt.style.display='';
				rabatt2.style.display='';
    	}else{
				rabatt.style.display='none';
		  	rabatt2.style.display='none';
			}
  	}
  }





  $('#aktiv').focus();

  $("#editGruppen").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:1200,
    maxHeight:900,
    autoOpen: false,
    buttons: {
      '{|ABBRECHEN|}': function() {
        GruppenReset();
        $(this).dialog('close');
      },
      '{|SPEICHERN|}': function() {
        GruppenEditSave();
      }
    }
  });

    $("#editGruppen").dialog({

  close: function( event, ui ) { GruppenReset();}
});

});


function GruppenReset()
{
  $('#editGruppen').find('#e_id').val('');
  $('#editGruppen').find('#aktiv').prop("checked",true);
  $('#editGruppen').find('#name').val('');
  $('#editGruppen').find('#kennziffer').val('');
  $('#editGruppen').find('#internebemerkung').val('');
  $('#editGruppen').find('#art').val('gruppe');
  $('#editGruppen').find('#projekt').val('');
  $('#editGruppen').find('#kategorie').val('');
  $('#editGruppen').find('#grundrabatt').val('');
  $('#editGruppen').find('#zahlungszieltage').val('');
  $('#editGruppen').find('#zahlungszielskonto').val('');
  $('#editGruppen').find('#zahlungszieltageskonto').val('');
  $('#editGruppen').find('#portofrei_aktiv').prop("checked",false);  
  $('#editGruppen').find('#portofreiab').val('');
  $('#editGruppen').find('#rabatt1').val('');
  $('#editGruppen').find('#bonus1').val('');
  $('#editGruppen').find('#bonus1_ab').val('');
  $('#editGruppen').find('#bonus6').val('');
  $('#editGruppen').find('#bonus6_ab').val('');
  $('#editGruppen').find('#rabatt2').val('');
  $('#editGruppen').find('#bonus2').val('');
  $('#editGruppen').find('#bonus2_ab').val('');
  $('#editGruppen').find('#bonus7').val('');
  $('#editGruppen').find('#bonus7_ab').val('');
  $('#editGruppen').find('#rabatt3').val('');
  $('#editGruppen').find('#bonus3').val('');
  $('#editGruppen').find('#bonus3_ab').val('');
  $('#editGruppen').find('#bonus8').val('');
  $('#editGruppen').find('#bonus8_ab').val('');
  $('#editGruppen').find('#rabatt4').val('');
  $('#editGruppen').find('#bonus4').val('');
  $('#editGruppen').find('#bonus4_ab').val('');
  $('#editGruppen').find('#bonus9').val('');
  $('#editGruppen').find('#bonus9_ab').val('');
  $('#editGruppen').find('#rabatt5').val('');
  $('#editGruppen').find('#bonus5').val('');
  $('#editGruppen').find('#bonus5_ab').val('');
  $('#editGruppen').find('#bonus10').val('');
  $('#editGruppen').find('#bonus10_ab').val('');
  $('#editGruppen').find('#provision').val('');
  $('#editGruppen').find('#sonderrabatt_skonto').val('');
  $('#editGruppen').find('#zentralregulierung').prop("checked",false);  
  $('#editGruppen').find('#rechnung_periode').val('1');
  $('#editGruppen').find('#rechnung_anzahlpapier').val('');
  $('#editGruppen').find('#rechnung_permail').prop("checked",false);  
  $('#editGruppen').find('#rechnung_name').val('');
  $('#editGruppen').find('#rechnung_abteilung').val('');
  $('#editGruppen').find('#rechnung_strasse').val('');
  $('#editGruppen').find('#rechnung_plz').val('');
  $('#editGruppen').find('#rechnung_ort').val('');
  $('#editGruppen').find('#rechnung_land').val('');
  $('#editGruppen').find('#rechnung_email').val('');
  $('#editGruppen').find('#kundennummer').val('');
  $('#editGruppen').find('#dta_aktiv').prop("checked",false);  
  $('#editGruppen').find('#dta_variante').val('1');
  $('#editGruppen').find('#dtavariablen').val('');
  $('#editGruppen').find('#dta_periode').val('1');
  $('#editGruppen').find('#partnerid').val('');
  $('#editGruppen').find('#dta_dateiname').val('');
  $('#editGruppen').find('#dta_mail').val('');
  $('#editGruppen').find('#dta_mail_betreff').val('');
  $('#editGruppen').find('#dta_mail_text').val('');
  


  art = document.getElementById('art');
  rabatt = document.getElementById('rabatte');
  rabatt2 = document.getElementById('rabatte2');
  if(art){
    // Hide the target field if priority isn't critical
    if(art.options[art.selectedIndex].value =='gruppe'){
		  rabatt.style.display='none';
			rabatt2.style.display='none';
    }else if(art.options[art.selectedIndex].value =='preisgruppe'){
    	rabatt.style.display='';
		  rabatt2.style.display='none';
		}else if(art.options[art.selectedIndex].value =='verband'){
			rabatt.style.display='';
		  rabatt2.style.display='';
		}else{
			rabatt.style.display='none';
			rabatt2.style.display='none';
		}
  }

}

function GruppenEditSave() {
	$.ajax({
    url: 'index.php?module=gruppen&action=edit&cmd=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      eaktiv: $('#aktiv').prop("checked")?1:0,
      ename: $('#name').val(),
      ekennziffer: $('#kennziffer').val(),
      einternebemerkung: $('#internebemerkung').val(),
      eart: $('#art').val(),
      eprojekt: $('#projekt').val(),
      ekategorie: $('#kategorie').val(),
      egrundrabatt: $('#grundrabatt').val(),
      ezahlungszieltage: $('#zahlungszieltage').val(),
      ezahlungszielskonto: $('#zahlungszielskonto').val(),
      ezahlungszieltageskonto: $('#zahlungszieltageskonto').val(),
      eportofrei_aktiv: $('#portofrei_aktiv').prop("checked")?1:0,
      eportofreiab: $('#portofreiab').val(),
      erabatt1: $('#rabatt1').val(),
      ebonus1: $('#bonus1').val(),
      ebonus1_ab: $('#bonus1_ab').val(),
      ebonus6: $('#bonus6').val(),
      ebonus6_ab: $('#bonus6_ab').val(),
      erabatt2: $('#rabatt2').val(),
      ebonus2: $('#bonus2').val(),
      ebonus2_ab: $('#bonus2_ab').val(),
      ebonus7: $('#bonus7').val(),
      ebonus7_ab: $('#bonus7_ab').val(),
      erabatt3: $('#rabatt3').val(),
      ebonus3: $('#bonus3').val(),
      ebonus3_ab: $('#bonus3_ab').val(),
      ebonus8: $('#bonus8').val(),
      ebonus8_ab: $('#bonus8_ab').val(),
      erabatt4: $('#rabatt4').val(),
      ebonus4: $('#bonus4').val(),
      ebonus4_ab: $('#bonus4_ab').val(),
      ebonus9: $('#bonus9').val(),
      ebonus9_ab: $('#bonus9_ab').val(),
      erabatt5: $('#rabatt5').val(),
      ebonus5: $('#bonus5').val(),
      ebonus5_ab: $('#bonus5_ab').val(),
      ebonus10: $('#bonus10').val(),
      ebonus10_ab: $('#bonus10_ab').val(),
      eprovision: $('#provision').val(),
      esonderrabatt_skonto: $('#sonderrabatt_skonto').val(),
      ezentralregulierung: $('#zentralregulierung').prop("checked")?1:0,
      erechnung_periode: $('#rechnung_periode').val(),
      erechnung_anzahlpapier: $('#rechnung_anzahlpapier').val(),
      erechnung_permail: $('#rechnung_permail').prop("checked")?1:0,
      erechnung_name: $('#rechnung_name').val(),
      erechnung_abteilung: $('#rechnung_abteilung').val(),
      erechnung_strasse: $('#rechnung_strasse').val(),
      erechnung_plz: $('#rechnung_plz').val(),
      erechnung_ort: $('#rechnung_ort').val(),
      erechnung_land: $('#rechnung_land').val(),
      erechnung_email: $('#rechnung_email').val(),
      ekundennummer: $('#kundennummer').val(),
      edta_aktiv: $('#dta_aktiv').prop("checked")?1:0,
      edta_variante: $('#dta_variante').val(),
      edtavariablen: $('#dtavariablen').val(),
      edta_periode: $('#dta_periode').val(),
      epartnerid: $('#partnerid').val(),
      edta_dateiname: $('#dta_dateiname').val(),
      edta_mail: $('#dta_mail').val(),
      edta_mail_betreff: $('#dta_mail_betreff').val(),
      edta_mail_text: $('#dta_mail_text').val()

                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        GruppenReset();
        updateLiveTable();
        $("#editGruppen").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });


}

function GruppenEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=gruppen&action=edit&cmd=get',
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
        	$('#editGruppen').find('#e_id').val(data.id);
        	$('#editGruppen').find('#aktiv').prop("checked", data.aktiv==1?true:false);
        	$('#editGruppen').find('#name').val(data.name);
        	$('#editGruppen').find('#kennziffer').val(data.kennziffer);
        	$('#editGruppen').find('#internebemerkung').val(data.internebemerkung);
        	$('#editGruppen').find('#art').val(data.art);
        	$('#editGruppen').find('#projekt').val(data.projekt);
        	$('#editGruppen').find('#kategorie').val(data.kategorie);
        	$('#editGruppen').find('#grundrabatt').val(data.grundrabatt);
        	$('#editGruppen').find('#zahlungszieltage').val(data.zahlungszieltage);
        	$('#editGruppen').find('#zahlungszielskonto').val(data.zahlungszielskonto);
        	$('#editGruppen').find('#zahlungszieltageskonto').val(data.zahlungszieltageskonto);
        	$('#editGruppen').find('#portofrei_aktiv').prop("checked", data.portofrei_aktiv==1?true:false);
        	$('#editGruppen').find('#portofreiab').val(data.portofreiab);
        	$('#editGruppen').find('#rabatt1').val(data.rabatt1);
        	$('#editGruppen').find('#bonus1').val(data.bonus1);
        	$('#editGruppen').find('#bonus1_ab').val(data.bonus1_ab);
        	$('#editGruppen').find('#bonus6').val(data.bonus6);
        	$('#editGruppen').find('#bonus6_ab').val(data.bonus6_ab);
        	$('#editGruppen').find('#rabatt2').val(data.rabatt2);
        	$('#editGruppen').find('#bonus2').val(data.bonus2);
        	$('#editGruppen').find('#bonus2_ab').val(data.bonus2_ab);
        	$('#editGruppen').find('#bonus7').val(data.bonus7);
        	$('#editGruppen').find('#bonus7_ab').val(data.bonus7_ab);
        	$('#editGruppen').find('#rabatt3').val(data.rabatt3);
        	$('#editGruppen').find('#bonus3').val(data.bonus3);
        	$('#editGruppen').find('#bonus3_ab').val(data.bonus3_ab);
        	$('#editGruppen').find('#bonus8').val(data.bonus8);
        	$('#editGruppen').find('#bonus8_ab').val(data.bonus8_ab);
        	$('#editGruppen').find('#rabatt4').val(data.rabatt4);
        	$('#editGruppen').find('#bonus4').val(data.bonus4);
        	$('#editGruppen').find('#bonus4_ab').val(data.bonus4_ab);
        	$('#editGruppen').find('#bonus9').val(data.bonus9);
        	$('#editGruppen').find('#bonus9_ab').val(data.bonus9_ab);
        	$('#editGruppen').find('#rabatt5').val(data.rabatt5);
        	$('#editGruppen').find('#bonus5').val(data.bonus5);
        	$('#editGruppen').find('#bonus5_ab').val(data.bonus5_ab);
        	$('#editGruppen').find('#bonus10').val(data.bonus10);
        	$('#editGruppen').find('#bonus10_ab').val(data.bonus10_ab);
        	$('#editGruppen').find('#provision').val(data.provision);
        	$('#editGruppen').find('#sonderrabatt_skonto').val(data.sonderrabatt_skonto);
        	$('#editGruppen').find('#zentralregulierung').prop("checked", data.zentralregulierung==1?true:false);
        	$('#editGruppen').find('#rechnung_periode').val(data.rechnung_periode);
        	$('#editGruppen').find('#rechnung_anzahlpapier').val(data.rechnung_anzahlpapier);
        	$('#editGruppen').find('#rechnung_permail').prop("checked", data.rechnung_permail==1?true:false);
        	$('#editGruppen').find('#rechnung_name').val(data.rechnung_name);
        	$('#editGruppen').find('#rechnung_abteilung').val(data.rechnung_abteilung);
        	$('#editGruppen').find('#rechnung_strasse').val(data.rechnung_strasse);
        	$('#editGruppen').find('#rechnung_plz').val(data.rechnung_plz);
        	$('#editGruppen').find('#rechnung_ort').val(data.rechnung_ort);
        	$('#editGruppen').find('#rechnung_land').val(data.rechnung_land);
        	$('#editGruppen').find('#rechnung_email').val(data.rechnung_email);
        	$('#editGruppen').find('#kundennummer').val(data.kundennummer);
        	$('#editGruppen').find('#dta_aktiv').prop("checked", data.dta_aktiv==1?true:false);
        	$('#editGruppen').find('#dta_variante').val(data.dta_variante);
        	$('#editGruppen').find('#dtavariablen').val(data.dtavariablen);
        	$('#editGruppen').find('#dta_periode').val(data.dta_periode);
        	$('#editGruppen').find('#partnerid').val(data.partnerid);
        	$('#editGruppen').find('#dta_dateiname').val(data.dta_dateiname);
        	$('#editGruppen').find('#dta_mail').val(data.dta_mail);
        	$('#editGruppen').find('#dta_mail_betreff').val(data.dta_mail_betreff);
        	$('#editGruppen').find('#dta_mail_text').val(data.dta_mail_text);


	        art = document.getElementById('art');
				  rabatt = document.getElementById('rabatte');
				  rabatt2 = document.getElementById('rabatte2');
				  if(art){
				    // Hide the target field if priority isn't critical
				    if(typeof art.options[art.selectedIndex] != 'undefined' && art.options[art.selectedIndex].value =='gruppe'){
						  rabatt.style.display='none';
							rabatt2.style.display='none';
				    }else if(typeof art.options[art.selectedIndex] != 'undefined' && art.options[art.selectedIndex].value =='preisgruppe'){
				    	rabatt.style.display='';
						  rabatt2.style.display='none';
						}else if(typeof art.options[art.selectedIndex] != 'undefined' && art.options[art.selectedIndex].value =='verband'){
							rabatt.style.display='';
							rabatt2.style.display='';
						}else{
							rabatt.style.display='none';
							rabatt2.style.display='none';
						}
				  }
				}else{

				}


                
        App.loading.close();
        $("#editGruppen").dialog('open');
      }
    });
  } else {
    GruppenReset(); 
    $("#editGruppen").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#gruppenlist').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

/*function GruppenDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=gruppen&action=delete',
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

}*/

</script>
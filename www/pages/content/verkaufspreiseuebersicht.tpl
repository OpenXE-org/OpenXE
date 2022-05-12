<!-- gehort zu tabview -->

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
        <!--<li><a href="#tabs-2">Neuen Verkaufspreis anlegen</a></li>-->
    </ul>



<!-- erstes tab -->
<div id="tabs-1">
[OPENDISABLE]
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
					<label for="alteverkaufspreise" class="switch">
						<input type="checkbox" id="alteverkaufspreise">
						<span class="slider round"></span>
					</label>
					<label for="alteverkaufspreise">{|alte Verkaufspreise anzeigen|}</label>
				</li>
				<li class="filter-item">
					<label for="nurkunde" class="switch">
						<input type="checkbox" id="nurkunde">
						<span class="slider round"></span>
					</label>
					<label for="nurkunde">{|nur Kundenpreise|}</label>
				</li>
				<li class="filter-item">
					<label for="nurgruppe" class="switch">
						<input type="checkbox" id="nurgruppe">
						<span class="slider round"></span>
					</label>
					<label for="nurgruppe">{|nur Gruppenpreise|}</label>
				</li>
				<li class="filter-item">
					<label for="nurstandard" class="switch">
						<input type="checkbox" id="nurstandard">
						<span class="slider round"></span>
					</label>
					<label for="nurstandard">{|nur Standardpreise|}</label>
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
    <input type="button" class="btnGreenNew" name="neuerverkaufspreis" value="&#10010; Neuer Verkaufspreis" onclick="VerkaufspreiseEdit(0);">
  </fieldset>
</div>
</div>
</div>
</div>  
[CLOSEDISABLE]

[MESSAGE]
[TAB1]
</div>

<!--<div id="tabs-2">
<!--[TAB2]-->
<!--</div>-->


<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->


<script type="text/javascript">

$(document).ready(function(){

  art = document.getElementById('art');
  adressediv = document.getElementById('adressediv');
  gruppediv = document.getElementById('gruppediv');

  if (art) {
      // Hide the target field if priority isn't critical
      if (art.options[art.selectedIndex].value =='Kunde') {
        adressediv.style.display='';
        gruppediv.style.display='none';
      }
      if (art.options[art.selectedIndex].value =='Gruppe') {
            adressediv.style.display='none';
        gruppediv.style.display='';
      }

      art.onchange=function() {
          if (art.options[art.selectedIndex].value == 'Kunde') {             
            adressediv.style.display='';
            gruppediv.style.display='none';
          } else if(art.options[art.selectedIndex].value == 'Gruppe') {
            adressediv.style.display='none';
            gruppediv.style.display='';
          } else {
            adressediv.style.display='';
            gruppediv.style.display='';
          }
      }
  }
});
</script>

<div id="editVerkaufspreis" style="display:none;" title="Bearbeiten">
	<form action="" method="post" name="eprooform">
    <input type="hidden" id="e_id">
    <input type="hidden" name = "e_artikelid" id="e_artikelid" value="[ID]">
	  [FORMHANDLEREVENT]
	  <div class="row">
			<div class="row-height">
				<div class="col-xs-12 col-md-12 col-md-height">
					<div class="inside inside-full-height">
						<fieldset>
							<legend>{|Kunde / Gruppe|}</legend>

							<table>
								<tr>
									<td width="170">{|Konditionen|}:</td>
									<td width="180" colspan="3">
										<select name="art" id="art">
											<option value="Kunde">Kunde</option>
											<option value="Gruppe">Gruppe</option>
										</select>&nbsp;
									</td>
									<td width="170"></td>
								</tr>

								<tr id="adressediv">
									<td width="170">{|Kunde|}:</td>
									<td colspan="4"><i>F&uuml;r Standardpreis leer lassen</i>[ADRESSESTART]<input type="text" size="70" name="adresse" id="adresse">[ADRESSEENDE]<br></td>
								</tr>
								<tr id="gruppediv">
									<td width="170"><b>{|Gruppe|}</b>:</td>
									<td colspan="4">[GRUPPESTART]<input type="text" size="70" name="gruppe" id="gruppe">[GRUPPEENDE]</td>
								</tr>
								<tr>
									<td width="170">{|Artikelnummer bei Kunde|}:</td>
									<td colspan="4"><input name="kundenartikelnummer" id="kundenartikelnummer" type="text" size="30">&nbsp;<i>(wenn vorhanden)</i></td>
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
							<legend>{|Verkaufspreis|}</legend>
							<table>
								<tr>
									<td width="170"><b>Ab Menge:</b></td>
									<td width="180"><input name="ab_menge" id="ab_menge" rule="notempty" msg="Pflichfeld!" type="text" size="10"><div id="pflicht1" style="float:right;display:table"><font color="red"><span>Pflichtfeld!</span></font></div>&nbsp;</td>
									<td width="20">&nbsp;</td>
									<td width="150">Menge in VPE:</td>
									<td>[VPESTART]<input type="text" size="15" name="vpe" id="vpe">[VPEENDE]</td>
								</tr>

								<tr>
									<td width="170" rowspan="3" valign="top"><b>Preis:</b></td>
									<td width="180" rowspan="3"><input name="preis" id="preis" type="text" size="10" rule="notempty" msg="Pflichfeld!"><div id="pflicht2" style="float:right;display:table"><font color="red">Pflichtfeld!</font></div>&nbsp;<br>[PREISRECHNER]</td>
									<td width="20">&nbsp;</td>
									<td width="150" valign="top">W&auml;hrung:</td>
									<td>
										<select name="waehrung" id="waehrung">
											[WAEHRUNGVERKAUF]
										</select>
									</td>
								</tr>

								<tr>
									<td width="20">&nbsp;</td>
									<td width="150" valign="top">Preis nicht berechnet aus W&auml;hrungstabelle:</td>
									<td nowrap><input type="checkbox" name="nichtberechnet" id="nichtberechnet" value="1" />
										<span class="spkurs">Kurs: <input type="text" disabled size="10" id="kurs" /> vom <input type="text" size="10" disabled id="kursdatum"></span>
									</td>
								</tr>

								<tr>
									<td width="20">&nbsp;</td>
									<td>[PREISTABELLE]</td>
								</tr>

								<tr>
									<td width="170">{|G&uuml;ltig ab|}:</td>
									<td width="180"><input name="gueltig_ab" id="gueltig_ab" type="text" size="10">&nbsp;</td>
									<td width="20">&nbsp;</td><td width="150"><label for="inbelegausblenden">{|In Staffelpreisen in Belegen ausblenden|}:</label></td>
									<td><input type="checkbox" name="inbelegausblenden" id="inbelegausblenden"></td>
								</tr>

								<tr>
									<td width="170">{|G&uuml;ltig bis|}:</td>
									<td width="180"><input name="gueltig_bis" id="gueltig_bis" type="text" size="10">&nbsp;</td>
									<td width="20">&nbsp;</td>
									<td width="150"></td>
									<td></td>
								</tr>

								<tr>
									<td>{|Interner Kommentar|}:</td>
									<td colspan="4"><textarea name="bemerkung" id="bemerkung" rows="3" cols="70"></textarea></td>
								</tr>

							</table>

						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</form>
	[PREISTABELLEPOPUP]
</div>


<script type="text/javascript">

$(document).ready(function() {
  $('#art').focus();

  $("#editVerkaufspreis").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:850,
    maxHeight:800,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        VerkaufspreiseReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        VerkaufspreiseEditSave();
      }
    }
  });

  $("#editVerkaufspreis").dialog({
    close: function( event, ui ) {VerkaufspreiseReset();}
  });

});

function VerkaufspreiseReset(){
  $('#editVerkaufspreis').find('#e_id').val('');
  //$('#editVerkaufspreis').find('#e_artikelid').val('');
  $('#editVerkaufspreis').find('#art').val('Kunde');
  $('#editVerkaufspreis').find('#adresse').val('');
  $('#editVerkaufspreis').find('#gruppe').val('');
  $('#editVerkaufspreis').find('#kundenartikelnummer').val('');
  $('#editVerkaufspreis').find('#ab_menge').val('');
  $('#editVerkaufspreis').find('#vpe').val('');
  $('#editVerkaufspreis').find('#preis').val('');
  $('#editVerkaufspreis').find('#waehrung').val('[STANDARDWAEHRUNGV]');
  $('#editVerkaufspreis').find('#nichtberechnet').prop("checked", false);
  $('#editVerkaufspreis').find('#inbelegausblenden').prop("checked", false);
  $('#editVerkaufspreis').find('#gueltig_ab').val('');
  $('#editVerkaufspreis').find('#gueltig_bis').val('');
  $('#editVerkaufspreis').find('#bemerkung').val('');

  art = document.getElementById('art');
  adressediv = document.getElementById('adressediv');
  gruppediv = document.getElementById('gruppediv');
  if (art.options[art.selectedIndex].value =='Kunde') {
    adressediv.style.display='';
    gruppediv.style.display='none';
  }

  if (art.options[art.selectedIndex].value =='Gruppe') {
    adressediv.style.display='none';
    gruppediv.style.display='';
  }
}


function VerkaufspreiseEditSave() {

  $.ajax({
    url: 'index.php?module=artikel&action=verkauf&cmd=popupsave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      eid: $('#e_id').val(),
      eartikelid: $('#e_artikelid').val(),
      eart: $('#art').val(),
      eadresse: $('#adresse').val(),
      egruppe: $('#gruppe').val(),
      ekundenartikelnummer: $('#kundenartikelnummer').val(),
      eab_menge: $('#ab_menge').val(),
      evpe: $('#vpe').val(),
      epreis: $('#preis').val(),
      ewaehrung: $('#waehrung').val(),
      enichtberechnet: $('#nichtberechnet').prop("checked")?1:0,
		  inbelegausblenden: $('#inbelegausblenden').prop("checked")?1:0,
      egueltig_ab: $('#gueltig_ab').val(),
      egueltig_bis: $('#gueltig_bis').val(),
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
        VerkaufspreiseReset();
        updateLiveTable();
        $("#editVerkaufspreis").dialog('close');
      } else {
        if(data.statusText.includes("Mengef") || data.statusText.includes("Preisf")){
          if(data.statusText.includes("Mengef")){
            $('#pflicht1').show();
          }                  
          if(data.statusText.includes("Preisf")){
            $('#pflicht2').show();
          }          
        }else{
          alert(data.statusText);
        }
                              
      }
    }
  });


}

function VerkaufspreiseEdit(id) {
		$('#pflicht1').hide();
    $('#pflicht2').hide();
    if(id > 0)
    { 
      $.ajax({
        url: 'index.php?module=artikel&action=verkauf&cmd=popupedit',
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
            $('#editVerkaufspreis').find('#e_id').val(data.id);
            $('#editVerkaufspreis').find('#e_artikelid').val([ID]);
            $('#editVerkaufspreis').find('#art').val(data.art);
            $('#editVerkaufspreis').find('#adresse').val(data.adresse);
            $('#editVerkaufspreis').find('#gruppe').val(data.gruppe);
            $('#editVerkaufspreis').find('#kundenartikelnummer').val(data.kundenartikelnummer);
            $('#editVerkaufspreis').find('#ab_menge').val(data.ab_menge);
            $('#editVerkaufspreis').find('#vpe').val(data.vpe);
            $('#editVerkaufspreis').find('#preis').val(data.preis);
            $('#editVerkaufspreis').find('#waehrung').val(data.waehrung);
            $('#editVerkaufspreis').find('#livepreisvpe').val(data.waehrung);
            $('#editVerkaufspreis').find('#nichtberechnet').prop("checked",data.nichtberechnet==1?true:false);
            $('#editVerkaufspreis').find('#inbelegausblenden').prop("checked",data.inbelegausblenden==1?true:false);
            $('#editVerkaufspreis').find('#gueltig_ab').val(data.gueltig_ab);
            $('#editVerkaufspreis').find('#gueltig_bis').val(data.gueltig_bis);
            $('#editVerkaufspreis').find('#bemerkung').val(data.bemerkung);
						if(data.kurs !== '')
						{
              $('.spkurs').show();
              $('#editVerkaufspreis').find('#kurs').val(data.kurs);
              $('#editVerkaufspreis').find('#kursdatum').val(data.kursdatum);
						}else{
						  $('.spkurs').hide();
            }
            adressediv = document.getElementById('adressediv');
            gruppediv = document.getElementById('gruppediv');

            if (data.art == 'Kunde') {
              adressediv.style.display='';
              gruppediv.style.display='none';
            }
            if (data.art == 'Gruppe') {
              adressediv.style.display='none';
              gruppediv.style.display='';
            }
            
          } else {
            
          }
          App.loading.close();
          $("#editVerkaufspreis").dialog('open');
        }
      });
    } else {
      VerkaufspreiseReset(); 
      $("#editVerkaufspreis").dialog('open');
    }

}

function updateLiveTable(i) {
    var oTableL = $('#verkaufspreise').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);     
}

function VerkaufspreiseDelete(id) {

    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({
            url: 'index.php?module=artikel&action=verkauf&cmd=delete',
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



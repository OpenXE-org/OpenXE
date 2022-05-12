<script>

	function anlegen(id, sid) {
		document.location.href = 'index.php?module=adresse&action=addposition&id=' + id + '&sid=' + sid + '&menge=' + document.getElementById('menge' + sid).value +
			'&datum=' + document.getElementById('datum' + sid).value + '&art=' + document.getElementById('art' + sid).value;
	}
</script>
<!-- gehort zu tabview -->



<style>
    .expertenmodus{
        display: none;
    }
</style>
<div id="tabs" class="ui-tabs ui-widget ui-widget-content ui-corner-all">
  <ul>
    [VORTAB1]<li><a href="#tabs-1">Artikel</a></li>[NACHTAB1]
    <li><a href="#tabs-2">Gruppen</a></li>
    [VORTAB4]<li><a href="#tabs-4">Sammelrechnungen</a></li>[NACHTAB4]
  </ul>

  <!-- erstes tab -->
	[VORTAB1]
  <div id="tabs-1" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-10 col-md-height">
          <div class="inside_white inside-full-height">

						<div class="filter-box filter-usersave">
							<div class="filter-block filter-inline">
								<div class="filter-title">{|Filter|}</div>
								<ul class="filter-list">
									<li class="filter-item">
										<label for="nur_aktive_abos" class="switch">
											<input type="checkbox" id="nur_aktive_abos">
											<span class="slider round"></span>
										</label>
										<label for="nur_aktive_abos">{|nur aktive|}</label>
									</li>
								</ul>
							</div>
						</div>

            <fieldset class="white">
              <legend>&nbsp;</legend>
              [TAB1]
            </fieldset>
          </div>
        </div>
      	<div class="col-xs-12 col-md-2 col-md-height">
        	<div class="inside inside-full-height">
            <fieldset>
              <legend>{|Aktionen|}</legend>
              <input type="button" class="btnGreenNew" name="neueAboPosition" value="&#10010; Neue Position einfügen" onclick="AboartikelEdit(0,[ADRESSE]);">
              <input type="button" class="btnGreenNew" name="AboPositionSortieren" value="Sortieren" onclick="Aboartikelsortieren();">
							<table width="100%">
								<tr>
									<td>{|Monatlich / aktuell|}</td>
								</tr>
								<tr>
									<td class="greybox">[MONATLICH]</td>
								</tr>
							</table>
            </fieldset>
          </div>
        </div>
      </div>
    </div>
  </div>
  [NACHTAB1]
  <div id="tabs-2">
    <div class="row">
    <div class="row-height">
    <div class="col-xs-12 col-md-10 col-md-height">
    <div class="inside_white inside-full-height">
    	<fieldset class="white">
      [TAB2]
      </fieldset>
    </div>
    </div>
    <div class="col-xs-12 col-md-2 col-md-height">
  	<div class="inside inside-full-height">
     	<fieldset>
       	<legend>{|Aktionen|}</legend>
       	<center>
       		<input type="button" class="btnGreenNew" name="anlegen" value="&#10010; Neuen Eintrag anlegen" onclick="AbogruppeEdit(0);">
       	</center>
     	</fieldset>
    </div>
    </div>
    </div>
  	</div>
	</div>
  [VORTAB4]
	<div id="tabs-4">
		<div class="row">
		<div class="row-height">
		<div class="col-xs-12 col-md-10 col-md-height">
		<div class="inside_white inside-full-height">
			<fieldset class="white">
			[TAB4]
			</fieldset>
		</div>
		</div>
		<div class="col-xs-12 col-md-2 col-md-height">
  	<div class="inside inside-full-height">
  		<fieldset>
  			<legend>{|Aktionen|}</legend>
  			<center>
  				<input type="button" class="btnGreenNew" name="neuesmlrg" id="neuesmlrg" value="&#10010; Neuen Eintrag anlegen" onclick="AboSammelrechnungEdit(0);">
  			</center>
  		</fieldset>
  	</div>
  	</div>
  	</div>
  	</div>

	</div>
  [NACHTAB4]
    <!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->


<script type='text/javascript'>
	$(document).ready(function () {

		editrechnung = document.getElementById('editrechnung');
	  abwrechnungsempfaengertr = document.getElementById('abwrechnungsempfaengertr');
	  sammelrechnungtr = document.getElementById('sammelrechnungtr');
	  sammelrechnungtr2 = document.getElementById('sammelrechnungtr2');
	  sammelrechnungtr3 = document.getElementById('sammelrechnungtr3');

	  if(editrechnung){
	    // Hide the target field if priority isn't critical
	    if(editrechnung.options[editrechnung.selectedIndex].value == '0'){
	    	abwrechnungsempfaengertr.style.display='none';
	    	sammelrechnungtr.style.display='none';
	    	sammelrechnungtr2.style.display='';
	    	sammelrechnungtr3.style.display='';
	    }
	    if(editrechnung.options[editrechnung.selectedIndex].value =='1'){
	      abwrechnungsempfaengertr.style.display='';
	      sammelrechnungtr.style.display='none';
	      sammelrechnungtr2.style.display='';
	      sammelrechnungtr3.style.display='';
	    }
	    if(editrechnung.options[editrechnung.selectedIndex].value =='2'){
	      abwrechnungsempfaengertr.style.display='none';
	      sammelrechnungtr.style.display='';
	      sammelrechnungtr2.style.display='none';
	      sammelrechnung.tr3.style.display='none';
	    }

	    editrechnung.onchange=function() {
	      if (editrechnung.options[editrechnung.selectedIndex].value == '1') {             
	        abwrechnungsempfaengertr.style.display='';
	        sammelrechnungtr.style.display='none';
	        sammelrechnungtr2.style.display='';
	        sammelrechnungtr3.style.display='';
	      } else if(editrechnung.options[editrechnung.selectedIndex].value == '2') {
	        abwrechnungsempfaengertr.style.display='none';
	        sammelrechnungtr.style.display='';
	        sammelrechnungtr2.style.display='none';
	        sammelrechnungtr3.style.display='none';
	      } else {
	        abwrechnungsempfaengertr.style.display='none';
	        sammelrechnungtr.style.display='none';
	        sammelrechnungtr2.style.display='';
	        sammelrechnungtr3.style.display='';
	      }
	    }
	  }




		$("#experte").on("click", function() {
			$('.expertenmodusfields').toggleClass('expertenmodus');
		});


        $("#sortAboartikel").dialog({
            modal: true,
            bgiframe: true,
            closeOnEscape: false,
            minWidth: 1020,
            maxHeight: 800,
            autoOpen: false,
            buttons: {
                ABBRECHEN: function () {
	                updateLiveTable('#adressealleabos');
                    $(this).dialog('close');
                }
            }
        });

		$("#editAbogruppe").dialog({
			modal: true,
			bgiframe: true,
			closeOnEscape: false,
			minWidth: 720,
			maxHeight: 700,
			autoOpen: false,
			buttons: {
				ABBRECHEN: function () {
					AbogruppeReset();
					$(this).dialog('close');
				},
				SPEICHERN: function () {
					AbogruppeSave();
				}
			}
		});

		$("#editAbogruppe").dialog({
			close: function (event, ui) {
				AbogruppeReset();
			}
		});


		$("#editAboartikel").dialog({
			modal: true,
			bgiframe: true,
			closeOnEscape: false,
			minWidth: 900,
			maxHeight: 850,
			autoOpen: false,
			buttons: {
				ABBRECHEN: function () {
					AboartikelReset();
					$(this).dialog('close');
				},
				SPEICHERN: function () {
					AboartikelSave();
				}
			}
		});

		$("#editAboartikel").dialog({
			close: function (event, ui) {
				AboartikelReset();
			}
		});
	});



	function Aboartikelsortieren(){
			updateLiveTable('#adressealleabossort');
			$("#sortAboartikel").dialog('open');
	}

	function AboartikelSave(){
			$.ajax({
				url: 'index.php?module=adresse&action=artikel&cmd=saveAboartikel',
				data: $('#editAboartikelForm').serialize(),
				method: 'post',
				dataType: 'json',
				beforeSend: function () {
					App.loading.open();
				},
				success: function (data) {
					if (data.status == 1) {
						updateLiveTable('#adressealleabos');
						$("#editAboartikel").dialog('close');
						// location.reload();
					} else {
						alert(data.statusText);
					}
					App.loading.close();
				}
			});
		}

	function AboartikelEdit(id, adresse) {
			AboartikelReset();
                $.ajax({
                    url: 'index.php?module=adresse&action=artikel&cmd=getAboartikel',
                    data: {
                        id: id,
                        adresse: adresse
                    },
                    method: 'post',
                    dataType: 'json',
                    beforeSend: function () {
                        App.loading.open();
                    },
                    success: function (data) {
                    	if(data.experte == 1){
                    	    $('#expertenmodus').show();
                        }
	                    if(id == 0){
		                    $('#editAboartikel').find('#artikelneuanlage').val('1');
	                    }
                    	$('#editAboartikel').find('#adresse').val(adresse);
                    	$('#editAboartikel').find('#id').val(data.id);
                        $('#editAboartikel').find('#artikelid').val(data.artikel);
                        $('#editAboartikel').find('#bezeichnung').val(data.bezeichnung);
                        if(id != 0){
	                        $('#editAboartikel').find('#artikel').val(data.nummer + ' ' + data.bezeichnung);
                        }
                        $('#editAboartikel').find('#beschreibung').val(data.beschreibung);
                        $('#editAboartikel').find('#beschreibungersetzten').prop("checked", data.beschreibungersetzten == 1 ? true : false);
	                    $('#editAboartikel').find('#menge').val(data.menge.replace(".",","));
	                    $('#editAboartikel').find('#preis').val(data.preis.replace(".",","));
	                    $('#editAboartikel').find('#preisart').val(data.preisart);
	                    $('#editAboartikel').find('#rabatt').val(data.rabatt);
	                    $('#editAboartikel').find('#dokument').val(data.dokument);
	                    $('#editAboartikel').find('#gruppe').val(data.gruppe);
	                    $('#editAboartikel').find('#sort').val(data.sort);
	                    $('#editAboartikel').find('#startdatum').val(data.startdatum);
	                    $('#editAboartikel').find('#zahlzyklus').val(data.zahlzyklus);
	                    $('#editAboartikel').find('#enddatum').val(data.enddatum);
	                    $('#editAboartikel').find('#abgerechnetbis').val(data.abgerechnetbis);
	                    $('#editAboartikel').find('#bemerkung').val(data.bemerkung);
	                    $('#editAboartikel').find('#gruppe').html(data.gruppen);
	                    $('#editAboartikel').find('#gruppe').val(data.gruppe);
                        App.loading.close();
	                    $("#editAboartikel").dialog('open');
                    }
                });
	}

	function AboartikelMove(id,direction){
		    if(direction > 0){
			    var directionNew = 'down';
		    } else {
                var directionNew = 'up';
		    }

			$.ajax({
				url: 'index.php?module=adresse&action=artikel&cmd=moveAboArtikel',
				data: {
					id: id,
					direction: directionNew
				},
				method: 'post',
				dataType: 'json',
				success: function (data) {
				    updateLiveTable('#adressealleabossort');
				}
			});
	}

	function AboartikelReset() {
			$('#editAboartikel').find('#artikel').val('');
			$('#editAboartikel').find('#artikelneuanlage').val('');
			$('#editAboartikel').find('#bezeichnung').val('');
			$('#editAboartikel').find('#beschreibung').val('');
			$('#editAboartikel').find('#menge').val('');
			$('#editAboartikel').find('#preis').val('');
			$('#editAboartikel').find('#preisart').val('abo');
			$('#editAboartikel').find('#rabatt').val('');
			$('#editAboartikel').find('#dokument').val('');
			$('#editAboartikel').find('#gruppe').val('');
			$('#editAboartikel').find('#sort').val('');
			$('#editAboartikel').find('#startdatum').val('');
			$('#editAboartikel').find('#zahlzyklus').val('');
			$('#editAboartikel').find('#enddatum').val('');
			$('#editAboartikel').find('#abgerechnetbis').val('');
			$('#editAboartikel').find('#bemerkung').val('');
			$('#editAboartikel').find('#gruppe').html('');
	}

	function AbogruppeReset() {
		$('#editAbogruppe').find('#editid').val('');
		$('#editAbogruppe').find('#editbeschreibung').val('');
		$('#editAbogruppe').find('#editbeschreibung2').val('');
		$('#editAbogruppe').find('#editrabatt').val('');
		$('#editAbogruppe').find('#editbetrag').val('');
		$('#editAbogruppe').find('#editart').val(0);
		$('#editAbogruppe').find('#editrechnung').val(0);
		$('#editAbogruppe').find('#editrechnungadresse').val('');
		$('#editAbogruppe').find('#editsammelrechnung').val('');
		$('#editAbogruppe').find('#editprojekt').val('');
		$('#editAbogruppe').find('#editsort').val('');
		$('#editAbogruppe').find('#editansprechpartner').val('');
		$('#editAbogruppe').find('#editgruppensumme').val('');


		editrechnung = document.getElementById('editrechnung');
	  abwrechnungsempfaengertr = document.getElementById('abwrechnungsempfaengertr');
	  sammelrechnungtr = document.getElementById('sammelrechnungtr');
	  sammelrechnungtr2 = document.getElementById('sammelrechnungtr2');
	  sammelrechnungtr3 = document.getElementById('sammelrechnungtr3');
	  if (editrechnung.options[editrechnung.selectedIndex].value =='0') {
	    abwrechnungsempfaengertr.style.display='none';
	    sammelrechnungtr.style.display='none';
	    sammelrechnungtr2.style.display = '';
	    sammelrechnungtr3.style.display = '';
	  }
	  if (editrechnung.options[editrechnung.selectedIndex].value =='1') {
	    abwrechnungsempfaengertr.style.display='';
	    sammelrechnungtr.style.display='none';
	    sammelrechnungtr2.style.display = '';
	    sammelrechnungtr3.style.display = '';
	  }
	  if (editrechnung.options[editrechnung.selectedIndex].value =='2') {
	    abwrechnungsempfaengertr.style.display='none';
	    sammelrechnungtr.style.display='';
	    sammelrechnungtr2.style.display = 'none';
	    sammelrechnungtr3.style.display = 'none';
	  }



	}

	function AbogruppeSave() {
		$.ajax({
			url: 'index.php?module=adresse&action=artikel&cmd=savegruppe&id=[ID]',
			data: {
				//Alle Felder die fürs editieren vorhanden sind
				id: $('#editid').val(),
				beschreibung: $('#editbeschreibung').val(),
				beschreibung2: $('#editbeschreibung2').val(),
				rabatt: $('#editrabatt').val(),
				ansprechpartner: $('#editansprechpartner').val(),
				rechnungadresse: $('#editrechnungadresse').val(),
				projekt: $('#editprojekt').val(),
				sort: $('#editsort').val(),
				gruppensumme: $('#editgruppensumme').prop("checked") ? 1 : 0,
				rechnung: $('#editrechnung').val(),
				sammelrechnung: $('#editsammelrechnung').val()
			},
			method: 'post',
			dataType: 'json',
			beforeSend: function () {
				App.loading.open();
			},
			success: function (data) {
				App.loading.close();
				if (data.status == 1) {
					AbogruppeReset();
					updateLiveTable('#abrechnungsartikel_gruppe');
					$("#editAbogruppe").dialog('close');
				} else {
					alert(data.statusText);
				}
			}
		});
	}

	function AbogruppeEdit(id) {

		if (id > 0) {
			$.ajax({
				url: 'index.php?module=adresse&action=artikel&cmd=getgruppe&id=[ID]',
				data: {
					id: id
				},
				method: 'post',
				dataType: 'json',
				beforeSend: function () {
					App.loading.open();
				},
				success: function (data) {
					$('#editAbogruppe').find('#editid').val(data.id);
					$('#editAbogruppe').find('#editbeschreibung').val(data.beschreibung);
					$('#editAbogruppe').find('#editbeschreibung2').val(data.beschreibung2);
					$('#editAbogruppe').find('#editrabatt').val(data.rabatt);
					$('#editAbogruppe').find('#editansprechpartner').val(data.ansprechpartner);
					$('#editAbogruppe').find('#editrechnungadresse').val(data.rechnungadresse);
					$('#editAbogruppe').find('#editprojekt').val(data.projekt);
					$('#editAbogruppe').find('#editsort').val(data.sort);
					$('#editAbogruppe').find('#editgruppensumme').prop("checked", data.gruppensumme == 1 ? true : false);
					$('#editAbogruppe').find('#editrechnung').val(data.extrarechnung);
					$('#editAbogruppe').find('#editsammelrechnung').val(data.sammelrechnung);


					abwrechnungsempfaengertr = document.getElementById('abwrechnungsempfaengertr');
          sammelrechnungtr = document.getElementById('sammelrechnungtr');
          sammelrechnungtr2 = document.getElementById('sammelrechnungtr2');
          sammelrechnungtr3 = document.getElementById('sammelrechnungtr3');

          if(data.extrarechnung == '0'){
            abwrechnungsempfaengertr.style.display='none';
            sammelrechnungtr.style.display='none';
            sammelrechnungtr2.style.display = '';
            sammelrechnungtr3.style.display = '';
          }
          if(data.extrarechnung == '1'){
            abwrechnungsempfaengertr.style.display='';
            sammelrechnungtr.style.display='none';
            sammelrechnungtr2.style.display='';
            sammelrechnungtr3.style.display='';
          }
          if(data.extrarechnung == '2'){
          	abwrechnungsempfaengertr.style.display='none';
          	sammelrechnungtr.style.display='';
          	sammelrechnungtr2.style.display = 'none';
          	sammelrechnungtr3.style.display = 'none';
          }
            

					App.loading.close();
					$("#editAbogruppe").dialog('open');
				}
			});

		} else {
			$("#editAbogruppe").dialog('open');
		}
	}

	function AbogruppeDelete(id) {

		var conf = confirm('Wirklich löschen?');
		if (conf) {
			$.ajax({
				url: 'index.php?module=adresse&action=artikel&cmd=deletegruppe&id=[ID]',
				data: {
					id: id
				},
				method: 'post',
				dataType: 'json',
				beforeSend: function () {
					App.loading.open();
				},
				success: function (data) {
					if (data.status == 1) {
						updateLiveTable('#abrechnungsartikel_gruppe');
					} else {
						alert(data.statusText);
					}
					App.loading.close();
				}
			});
		}

		return false;

	}

	function updateLiveTable(selector) {
		var oTableL = $(selector).dataTable();
		oTableL.fnFilter('%');
		oTableL.fnFilter('');
	}


</script>






<script type="text/javascript">

$(document).ready(function() {
  $("#editAboSammelrechnung").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:690,
    maxHeight:700,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        AboSammelrechnungReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        AboSammelrechnungEditSave();
      }
    }
  });

    $("#editAboSammelrechnung").dialog({

  close: function( event, ui ) { AboSammelrechnungReset();}
});

});


function AboSammelrechnungReset()
{
  $('#editAboSammelrechnung').find('#e_id').val('');
  $('#editAboSammelrechnung').find('#e_bezeichnung').val('');
  $('#editAboSammelrechnung').find('#e_smlrabatt').val('');
  $('#editAboSammelrechnung').find('#e_abwrechnungsadresse').val('');
  $('#editAboSammelrechnung').find('#e_projekt').val('');
}

function AboSammelrechnungEditSave() {
	$.ajax({
    url: 'index.php?module=adresse&action=artikel&cmd=smlsave',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      bezeichnung: $('#e_bezeichnung').val(),
      smlrabatt: $('#e_smlrabatt').val(),
      abwrechnungsadresse: $('#e_abwrechnungsadresse').val(),
      projekt: $('#e_projekt').val(),
      aid: $('#a_id').val()                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
     	App.loading.close();
      if (data.status == 1) {
        AboSammelrechnungReset();
        updateLiveTableSmlrg();
        $("#editAboSammelrechnung").dialog('close');
      } else {
        alert(data.statusText);
      }
    }
  });


}

function AboSammelrechnungEdit(id) {
  if(id > 0)
  { 
    $.ajax({
      url: 'index.php?module=adresse&action=artikel&cmd=smledit',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
      	App.loading.open();
      },
      success: function(data) {
        $('#editAboSammelrechnung').find('#e_id').val(data.id);
        $('#editAboSammelrechnung').find('#e_bezeichnung').val(data.bezeichnung);
        $('#editAboSammelrechnung').find('#e_smlrabatt').val(data.rabatt);
        $('#editAboSammelrechnung').find('#e_abwrechnungsadresse').val(data.abweichende_rechnungsadresse);
        $('#editAboSammelrechnung').find('#e_projekt').val(data.projekt);
        $('#editAboSammelrechnung').find('#a_id').val(data.adresse);
                
        App.loading.close();
        $("#editAboSammelrechnung").dialog('open');
      }
    });
  } else {
    updateLiveTableSmlrg(); 
    $("#editAboSammelrechnung").dialog('open');
  }

}

function updateLiveTableSmlrg(i) {
  var oTableL = $('#abosammelrechnungen').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  //oTableL.fnFilter('');
  oTableL.fnFilter(tmp);   
}

function AboSammelrechnungDelete(id) {
  var conf = confirm('Wirklich löschen?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=adresse&action=artikel&cmd=smldelete',
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
          updateLiveTableSmlrg();
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














<div id="sortAboartikel" style="display:none;" title="Abo Artikel sortieren">
    <div>
        [SORTPOPUP]
    </div>
</div>




<div id="editAboartikel" style="display:none;" title="Abo Artikel bearbeiten">
  <form id="editAboartikelForm" name="editAboartikelForm">
    <table class="tableborder" border="0" width="100%">
      <tbody>
        <tr valign="top" colspan="3">
          <td>

            <fieldset>
              <legend>{|Allgemein|}</legend>
              <input type="hidden" id="id" name="id" value="" />
              <input type="hidden" id="adresse" name="adresse" value="" />
              <input type="hidden" id="artikelid" name="artikelid" value="" />
              <input type="hidden" id="artikelueberschreiben" name="artikelueberschreiben" value="0" />
              <input type="hidden" id="artikelneuanlage" name="artikelneuanlage" value="0" />
              <table border="0" width="100%">
                <tr>
                  <td width="200">{|Artikel|}:</td>
                  <td><input type="text" id="artikel" class="" tabindex="" name="artikel" value="" size="50" placeholder="" maxlength="50"></td>
                </tr>
                <tr>
                  <td width="200">{|Bezeichnung|}:</td>
                  <td><input type="text" id="bezeichnung" class="" tabindex="" name="bezeichnung" value="" size="50" placeholder="" maxlength=""></td>
                </tr>
                <tr>
                  <td>{|Beschreibung|}:</td>
                  <td><textarea rows="5" id="beschreibung" class="0" name="beschreibung" cols="50"></textarea></td>
                </tr>
                <tr>
                  <td width="200">{|Beschreibungstext ersetzen|}:</td>
                  <td><input type="checkbox" id="beschreibungersetzten" class="" tabindex="0" name="beschreibungersetzten" value="1" >&nbsp;<i>{|Es wird nur die Beschreibung von hier ohne Artikelbeschreibung aus den Stammdaten angezeigt|}</i>.</td>
                </tr>
                <tr>
                  <td>{|Menge|}:</td>
                  <td><input type="text" id="menge" class="" tabindex="" name="menge" value="" size="50" placeholder="" maxlength=""></td>
                </tr>
                <tr>
                  <td>{|Preis (netto)|}:</td>
                  <td><input type="text" id="preis" class="" tabindex="" name="preis" value="" size="30" placeholder="" maxlength="">&nbsp;
                        <select name="preisart" size="0" tabindex="0" id="preisart" class="" onchange="">
                          <option value="monat">{|Monatspreis|}</option>
                          <option value="monatx">{|Preis f&uuml;r x Monate|}</option>
                          <option value="jahr">{|Jahrespreis|}</option>
                          <option value="wochen">{|Wochenpreis (Beta)|}</option>
													<option value="30tage">{|30 Tage|}</option>
													<option value="einmalig">{|Einmalig|}</option>
                        </select>
                  </td>
                </tr>
                <tr>
                  <td>{|Rabatt|}:</td>
                  <td><input type="text" id="rabatt" class="" tabindex="" name="rabatt" value="" size="30" placeholder="" maxlength="">&nbsp;<i>(Optional in %)</i></td>
                </tr>
                <tr>
                  <td>{|Automatisch anlegen als|}:</td>
                  <td><select name="dokument" size="0" tabindex="0" id="dokument" class="" onchange="">
                        <option value="rechnung">{|Rechnung|}</option>
                        <option value="auftrag">{|Auftrag|}</option>
                      </select>&nbsp;{|in Gruppe|}
                      <select name="gruppe" size="0" tabindex="" id="gruppe" class="" onchange="">
                      </select>&nbsp;{|Reihenfolge|}:&nbsp;<input type="text" id="sort" class="" tabindex="" name="sort" value="" size="4" placeholder="" maxlength="">&nbsp;<i>Optional</i>
                  </td>
                </tr>
              </table>
            </fieldset>
            <fieldset>
              <legend>{|Wiederholende Zahlung Einstellungen|}</legend>
              <table border="0" width="100%">
                <!--	      <tr><td width="200">wiederholender Artikel:</td><td><input type="checkbox" id="wiederholend"  class="" tabindex="0"
                              name="wiederholend"  value="1"  onchange="" onclick="0"
                                ></td></tr>-->
                <tr>
                  <td width="200">{|Erstes Startdatum|}:</td>
                  <td><input type="text" id="startdatum" class="" tabindex="" name="startdatum" value="" size="10" placeholder="" maxlength="">&nbsp;<i>Feld <b>nicht</b> nach ersten Abolauf &auml;ndern.</i></td>
                </tr>
                <tr>
                  <td>{|Zahlzyklus|}:</td>
                  <td><input type="text" id="zahlzyklus" class="" tabindex="" name="zahlzyklus" value="" size="10" placeholder="" maxlength="">&nbsp;<i>(in Wochen, Monaten oder Jahren)</i></td>
                </tr>
                <tr>
                  <td width="200">{|Enddatum|}:</td>
                  <td><input type="text" id="enddatum" class="" tabindex="" name="enddatum" value="" size="10" placeholder="" maxlength="">&nbsp;<i>fr&uuml;hester beachteter Zeitpunkt: 01.03.2018 bzw. muss es nach "Abgerechnet bis" sein</i></td>
                </tr>

              </table>
            </fieldset>
            <fieldset>
              <legend>{|Wiederholende Zahlung Einstellungen|}</legend>
              <table border="0" width="100%">
                <tr>
                  <td width="200">{|Expertenmodus|}:</td>
                  <td><input type="checkbox" id="experte" class="" tabindex="" name="experte" value="1" ></td>
                </tr>
                <tr class="expertenmodusfields expertenmodus">
                  <td>{|Abgerechnet bis|}:</td>
                  <td><input type="text" id="abgerechnetbis" class="" tabindex="" name="abgerechnetbis" value="" size="10" placeholder="" maxlength="">&nbsp;<i>{|Feld nicht nach ersten Abolauf &auml;ndern|}!</i></td>
                </tr>
                <tr class="expertenmodusfields expertenmodus">
                  <td>{|Bemerkung (intern)|}</td>
                  <td><textarea rows="5" id="bemerkung" class="0" name="bemerkung" cols="50" ></textarea></td>
                </tr>
              </table>
            </fieldset>

          </td>
        </tr>
      </tbody>
    </table>
  </form>

</div>


<div id="editAbogruppe" style="display:none;" title="Abo Gruppe bearbeiten">
  <input type="hidden" id="editid">
  <fieldset>
  	<legend>{|Abo Gruppe|}</legend>

  	<table>
    	<tr>
      	<td width="180">{|Bezeichnung|}:</td>
      	<td><input type="text" name="editbeschreibung" id="editbeschreibung" size="40"></td>
   	  </tr>
    	<tr>
      	<td>{|Beschreibung|}:</td>
      	<td><textarea rows="5" id="editbeschreibung2" class="0"
          name="editbeschreibung2" cols="50">
      	</textarea></td>
    	</tr>
    	<tr>
    		<td>{|Rabatt|}:</td>
    		<td><input type="text" name="editrabatt" id="editrabatt" size="40">&nbsp;({|optional in %|})</td>
    	</tr> 
    	<tr>
      	<!--<td>{|Eigene Rechnung|}:</td>
      	<td><input type="checkbox" value="1" name="editrechnung" id="editrechnung"></td>-->
      	<td>{|Rechnung|}:</td>
      	<td><select name="editrechnung" id="editrechnung">
      				<option value="0">Gemeinsame Rechnung</option>
      				<option value="1">Eigene Rechnung</option>
      				<option value="2">Sammelrechnung</option>
      			</select>
      	</td>
    	</tr>

    	<tr id="sammelrechnungtr2">
      	<td width="180">{|Abweichender Ansprechpartner|}:</td>
      	<td><input type="text" name="editansprechpartner" id="editansprechpartner" size="40"></td>
    	</tr>
    	<tr id="sammelrechnungtr3">
      	<td>{|Ziel-Projekt|}:</td>
      	<td><input type="text" name="editprojekt" id="editprojekt" size="40"></td>
    	</tr>
    	<tr>
      	<td>{|Reihenfolge|}:</td>
      	<td><input type="text" name="editsort" id="editsort" size="4">&nbsp;<i>({|Optional|})</i></td>
    	</tr>
    	<tr>
      	<td>{|Gruppensumme|}:</td>
      	<td><input type="checkbox" value="1" name="editgruppensumme" id="editgruppensumme"></td>
    	</tr>
    	<tr id="abwrechnungsempfaengertr">
      	<td width="180">{|Abw. Rechnungsempfänger|}:</td>
      	<td><input type="text" name="editrechnungadresse" id="editrechnungadresse" size="40"></td>
    	</tr>
    	<tr id="sammelrechnungtr">
    		<td>{|Sammelrechnung|}:</td>
    		<td><input type="text" name="editsammelrechnung" id="editsammelrechnung" size="40"></td>
    	</tr>

  	</table>
  </fieldset>
</div>


<div id="editAboSammelrechnung" style="display:none;" title="Bearbeiten">
  <input type="hidden" id="e_id">
  <input type="hidden" id="a_id" value="[ADRESSE]">
  <fieldset>
  	<legend>{|Abo Sammelrechnung|}</legend>
  	<table>
    	<tr>
      	<td width="200">{|Bezeichnung|}:</td>
      	<td><input type="text" name="e_bezeichnung" id="e_bezeichnung" size="40"></td>
   	  </tr>
   	  <tr>
   	  	<td>{|Rabatt|}:</td>
   	  	<td><input type="text" name="e_smlrabatt" id="e_smlrabatt" size="40">&nbsp;<i>(optional in %)</i></td>
   	  </tr>
    	<tr>
      	<td>{|Abweichende Rechnungsadresse|}:</td>
      	<td><input type="text" name="e_abwrechnungsadresse" id="e_abwrechnungsadresse" size="40"></td>
    	</tr> 
    	<tr>
    	 	<td>{|Ziel-Projekt|}:</td>
      	<td><input type="text" name="e_projekt" id="e_projekt" size="40"></td>
    	</tr>
    </table>
  </fieldset>
</div>


<script type='text/javascript'>
	$("input#editprojekt").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=projektname",
	});

	$("input#editrechnungadresse").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=kunde",
	});

    $("input#artikel").autocomplete({
        source: "index.php?module=ajax&action=filter&filtername=artikelnummer",
	    select: function( event, ui ) {
        	var artikeldata = ui.item.value.split(' ')[0];
			    $.ajax({
				    url: 'index.php?module=adresse&action=artikel&cmd=getArtikelData',
				    data: {
					    artikel: artikeldata,
                        adresse: $('#adresse').val()
				    },
				    method: 'post',
				    dataType: 'json',
				    success: function (data) {
					    $('#editAboartikel').find('#artikelueberschreiben').val(1);
						$('#editAboartikel').find('#artikelid').val(data.id);
					    $('#editAboartikel').find('#bezeichnung').val(data.name_de);
					    $('#editAboartikel').find('#beschreibung').val(data.anabregs_text);
						$('#editAboartikel').find('#beschreibungersetzten').prop("checked",true);
						$('#editAboartikel').find('#zahlzyklus').val('1');
					    $('#editAboartikel').find('#menge').val('1');
					    $('#editAboartikel').find('#rabatt').val('0,00');
					    $('#editAboartikel').find('#preis').val(data.nettopreis);
				    }
			    });

        }

    });

</script>


<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1"></a></li>
  </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
	<form method="post">
		<div id="tabs-1">
			<div class="row">
		  <div class="row-height">
		  <div class="col-xs-12 col-md-10 col-md-height">
		  <div class="inside_white inside-full-height">
		    <fieldset class="white">
		      <legend> </legend>
		      [MESSAGE]
		      [TAB1]
		    </fieldset>
		  </div>
		  </div>
		  <div class="col-xs-12 col-md-2 col-md-height">
		  <div class="inside inside-full-height">
		    <fieldset>
		      <legend>{|Aktionen|}</legend>
		      <input type="button" class="btnGreenNew" name="neuereintrag" value="&#10010; Neuer Eintrag" onclick="UstprfEdit(0);">
		    </fieldset>
		  </div>
		  </div>
		  </div>
		  </div>
		</div>

		<div id="tabs-2">
		[TAB2]
		</div>

<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->

[DATEIENPOPUP]
<div id="editUstprf" style="display:none;" title="Bearbeiten">
  <input type="hidden" id="e_ustprfid">
  <input type="hidden" id="e_adressid" value="[ADRESSID]">
  <input type="hidden" id="e_googlesuche" value="">

  <div class="row">
  <div class="row-height">
  <div class="col-xs-12 col-md-7 col-md-height">
  <div class="inside inside-full-height">

	  <fieldset>
	  	<table width="100%" border="0">
	  		<tr>
	  			<td>[STATUSMELDUNG]</td>
	  		</tr>
	  	</table>
	    
	    <legend>{|Daten|}</legend>
	    <table class="mkTableFormular">
	      <tr>    
	        <td>{|Ust-ID|}:</td>
	        <td><input type="text" name="e_ustid" id="e_ustid" size="40" value="[USTID]"></td>
	      </tr>	        
	      <tr>
	        <td>{|Name|}:</td>
	        <td><input type="text" name="e_name" id="e_name" size="40" value="[NAME]"></td><td><font color="red"><b>[ERG_NAME]</b></font></td>
	      </tr>
	      <tr>
	        <td>{|Ort|}:</td>
	        <td><input type="text" name="e_ort" id="e_ort" size="40" value="[ORT]"></td><td><font color="red"><b>[ERG_ORT]</b></font></td>
	      </tr>
	      <tr>
	      	<td>{|PLZ|}:</td>
	      	<td><input type="text" name="e_plz" id="e_plz" size="40" value="[PLZ]"></td><td><font color="red"><b>[ERG_PLZ]</b></font></td></tr>
	      </tr>
	      <tr>
	      	<td>{|Stra&szlig;e|}:</td>
	      	<td><input type="text" name="e_strasse" id="e_strasse" size="40" value="[STRASSE]"></td><td><font color="red"><b>[ERG_STR]</b></font></td></tr>
	      </tr>
	      <tr>
	      	<td>{|Land|}:</td>
	      	<td><select name="e_land" id="e_land">
	      				[LAENDER]
	      			</select>
	      	</td>
	      </tr>
	      <!--<tr><td colspan="2"><br><br><input type="submit" name="aendern" value="Adresse und USTID bei Kunden und offene Auftr&auml;ge &auml;ndern"></td></tr> WOHIN?-->

	    </table>
	  </fieldset>

	  <fieldset>
	  	<legend>{|Status|}</legend>
	    <table class="mkTableFormular">
	      <tr>
	  			<td style="vertical-align:bottom">{|Status|}:</td>
	  			<td id="status" style="vertical-align:bottom"></td>
	  		</tr>
	  		<tr>
	  			<td style="vertical-align:bottom">{|Online Prüfung|}:</td>
	  			<td id="onlinestatus" style="vertical-align:bottom"></td>
	  		</tr>
	  		<tr>
	  			<td style="vertical-align:bottom">{|Schriftliche Mitteilung|}:</td>
	  			<td id="briefbestellt" style="vertical-align:bottom"></td>
	  		</tr>
	  	</table>
	  </fieldset>


	</div>
	</div>
	<div class="col-xs-12 col-md-5 col-md-height">
  <div class="inside inside-full-height">
  	<fieldset>
  		<legend>{|Aktionen|}</legend>
  		<input type="button" class="btnGreenNew" name="online" id="online" value="Online Pr&uuml;fung" onclick="UstprfOnline();">
  		<input type="button" class="btnBlueNew" name="brief" id="brief" value="Schriftliche Mitteilung anfordern" onclick="UstprfBrief();">
  <!--		<input type="button" class="btnBlueNew" name="fehlgeschlagen" id="fehlgeschlagen" value="Abfrage als fehlgeschlagen markieren" onclick="UstprfFehlgeschlagen();">
  		<input type="button" class="btnBlueNew" name="manuellok" id="manuellok" value="Manuell auf OK setzen" onclick="UstprfManuellok();">-->
<!--  		<input type="button" class="btnBlueNew" name="mail" id="mail" value="Kunden benachrichtigen" onclick="UstprfMail();">-->
<!--  		<input type="button" class="btnBlueNew" name="aendern" id="aendern" value="Stammdaten und offene Auftr&auml;ge updaten" onclick="UstprfAendern();">-->
<br>
			<input type="button" class="btnBlueNew" name="stammdaten" id="stammdaten" value="Stammdaten aktualisieren" onclick="UstprfStammdaten();">
  		<input type="button" class="btnBlueNew" name="google" id="google" value="Google Suche" onclick="UstprfGoogle();">
  	</fieldset>
  </div>
  </div>
  </div>
  </div>

  <fieldset>
	 	<legend>{|Protokoll|}</legend>
	 	[PROTOKOLLTABELLE]
	</fieldset>
	
      
<div id="editUstprfMail" style="display:none;" title="Bearbeiten">
  <input type="hidden" id="e_mid">
  <fieldset>
  	<legend>{|Mailtext|}</legend>
  	<table>
  		<tr>
  			<td>{|Betreff|}</td>
  			<td><input type="text" name="e_betreff" id="e_betreff" size="40"></td>
  		</tr>
  		<tr>
  			<td>{|Mailtext an Kunden|}:</td>
  			<td><textarea name="e_mailtext" id="e_mailtext" rows="10" cols="10"></textarea></td>
  		</tr>
  		<tr>
  			<td></td>
  			<td>({|Signatur wird automatisch angeh&auml;ngt.|})</td>
  		</tr>
  	</table>  	
  </fieldset>
</div>

</form>


<script type="text/javascript">

	$(document).ready(function() {
	  $('#e_ustid').focus();

	  $("#editUstprf").dialog({
	    modal: true,
	    bgiframe: true,
	    closeOnEscape:false,
	    minWidth:900,
	    autoOpen: false,
	    buttons: {
	    	[DATEIBUTTON]

	      SCHLIESSEN: function() {
	      	UstprfReset();
	        $(this).dialog('close');
	      }/*,
	      SPEICHERN: function() {
	        UstprfEditSave();
	      }*/
	    },
	    open: function(event, ui){
	      if(auxid > 0){
	        UstprfEdit(auxid);
	        auxid = 0;
	      }
	    }

	  });

	  $('#editUstprfMail').dialog({
	  	modal: true,
	  	bgiframe: true,
	  	closeOnEscape:false,
	  	minWidth:500,
	  	autoOpen: false,
	  	buttons: {
	  		ABBRECHEN: function(){
	  			UstprfMailReset();
	  			$(this).dialog('close');
	  		},
	  		SPEICHERN: function(){
	  			UstprfEditSaveMail();
	  		}
	  	}
	  });

	  $("#editUstprf").dialog({
		  close: function( event, ui ) { UstprfReset();}
		});

	  $("#editUstprfMail").dialog({
		  close: function( event, ui ) { UstprfMailReset();}
		});

	});

  function UstprfBestaetigenReset(){
    $('#editUstbestaetigen').find('#ustprf_meldung').val('');
  }

	function UstprfReset(){
		$('#editUstprf').find('#e_ustprfid').val('');
		$('#editUstprf').find('#land').val('');
		$('#editUstprf').find('#status').val('');
		$('#editUstprf').find('#e_ustid').val('');
		$('#editUstprf').find('#e_name').val('');
		$('#editUstprf').find('#e_ort').val('');
		$('#editUstprf').find('#e_plz').val('');
		$('#editUstprf').find('#e_strasse').val('');
		$('#editUstprf').find('#e_land').val('');
		$('#editUstprf').find('#e_googlesuche').val('');
		
	}

	function UstprfGoogle(){
    window.open($('#e_googlesuche').val(), '_blank'); 
	}


	function UstprfMailReset(){
		$('#editUstprfMail').find('#e_betreff').val('');
	  $('#editUstprfMail').find('#e_mailtext').val('');
	}


	function UstprfEditSave(){
	  $.ajax({
	    url: 'index.php?module=adresse&action=ustprf&cmd=save',
	    data: {
	      //Alle Felder die fürs editieren vorhanden sind
	      ustprfid: $('#e_ustprfid').val(),
	      adressid: $('#e_adressid').val(),
	      ustid: $('#e_ustid').val(),
	      name: $('#e_name').val(),
	      ort: $('#e_ort').val(),
	      plz: $('#e_plz').val(),
	      strasse: $('#e_strasse').val(),
	      land: $('#e_land').val()
	    },
	    method: 'post',
	    dataType: 'json',
	    beforeSend: function() {
	      App.loading.open();
	    },
	    success: function(data) {
	      App.loading.close();
	      if (data.status == 1) {
	        UstprfReset();
          updateLiveTableust();
	        $("#editUstprf").dialog('close');
	      } else {
	        alert(data.statusText);
	      }
	    }
	  });
	}

	function UstprfEdit(id) {
		$('body').loadingOverlay();
		if(id > 0){ 
	  	auxid = 0;
			$.ajax({
			  url: 'index.php?module=adresse&action=ustprf&cmd=get',
			  data: {
			    ustprfid: id
			  },
			  method: 'post',
			  dataType: 'json',
			  beforeSend: function() {
			    App.loading.open();
			  },
			  success: function(data) {
			    $('#editUstprf').find('#e_ustprfid').val(data.ustprfid);
			    $('#editUstprf').find('#e_ustid').val(data.ustid);
			    $('#editUstprf').find('#e_name').val(data.name);
			    $('#editUstprf').find('#e_ort').val(data.ort);
			    $('#editUstprf').find('#e_plz').val(data.plz);
			    $('#editUstprf').find('#e_strasse').val(data.strasse);
		      $('#editUstprf').find('#e_land').val(data.land);
		      $('#editUstprf').find('#e_googlesuche').val(data.url);
          var statusErgebnis = data.uststatus;
          if(statusErgebnis == 'online'){
            statusErgebnis = 'erfolgreich geprüft';
          }
          if(statusErgebnis == 'gueltigmarkieren'){
            statusErgebnis = 'manuell als gültig markiert';
          }
          document.getElementById("status").innerHTML = statusErgebnis;
		      document.getElementById("onlinestatus").innerHTML = data.datum_online;
		      document.getElementById("briefbestellt").innerHTML = data.datum_brief;
		      
		      oMoreData1adresse_ustprf_protokoll = data.ustprfid; //"erzwingt" Anwendung der Filter fuer Verwendung in Kombination mit gespeicherter Konfiguration
    		  updateLiveTableprotokoll();

		      App.loading.close();
		      $("#editUstprf").dialog('open');
		    },
		    complete: function () {
        	$('body').loadingOverlay('remove');
      	}
		  });
		} else {
	    UstprfReset();

	    $.ajax({
	      url: 'index.php?module=adresse&action=ustprf&cmd=startwerte',
	      data: {
	        adressid: $('#e_adressid').val(),
	      },
	      method: 'post',
	      dataType: 'json',
	      beforeSend: function() {
	      },
	      success: function(data) {
	        $('#editUstprf').find('#e_ustprfid').val(data.ustprfid);
	        $('#editUstprf').find('#e_ustid').val(data.ustid);
	        $('#editUstprf').find('#e_name').val(data.name);
	        $('#editUstprf').find('#e_ort').val(data.ort);
	        $('#editUstprf').find('#e_plz').val(data.plz);
	        $('#editUstprf').find('#e_strasse').val(data.strasse);
	        $('#editUstprf').find('#e_land').val(data.land);
	        $('#editUstprf').find('#e_googlesuche').val(data.url);

          var statusErgebnis = data.uststatus;
          if(statusErgebnis == 'online'){
            statusErgebnis = 'erfolgreich geprüft';
          }
          if(statusErgebnis == 'gueltigmarkieren'){
            statusErgebnis = 'manuell als gültig markiert';
          }
          document.getElementById("status").innerHTML = statusErgebnis;
	        document.getElementById("onlinestatus").innerHTML = "-";
					document.getElementById("briefbestellt").innerHTML = "-";
		 

	        oMoreData1adresse_ustprf_protokoll = data.ustprfid; //"erzwingt" Anwendung der Filter fuer Verwendung in Kombination mit gespeicherter Konfiguration
          updateLiveTableprotokoll();
          updateLiveTableust();
          document.getElementById("brief").className = "btnBlueNew";
					document.getElementById("online").className = "btnGreenNew";

	        $("#editUstprf").dialog('open');
	      },
	      complete: function () {
	        $('body').loadingOverlay('remove');
	      }
   	  });
	  }
	}

	function updateLiveTableust(i) {
	  var oTableL = $('#adresse_ustprf').dataTable();
	  var tmp = $('.dataTables_filter input[type=search]').val();
		oTableL.fnFilter('%');
		//oTableL.fnFilter('');
		oTableL.fnFilter(tmp);   
	}

	function updateLiveTableprotokoll() {
	  var oTableL = $('#adresse_ustprf_protokoll').dataTable();
	  var tmp = $('.dataTables_filter input[type=search]').val();
		oTableL.fnFilter('%');
		//oTableL.fnFilter('');
		oTableL.fnFilter(tmp);   
	}

	function UstprfDelete(id) {
	  var conf = confirm('Wirklich löschen?');
	  if (conf) {
	    $.ajax({ 
	      url: 'index.php?module=adresse&action=ustprf&cmd=delete',
	      data: {
	        ustprfid: id
	      },
	      method: 'post',
	      dataType: 'json',
	      beforeSend: function() {
	        App.loading.open();
	      },
	      success: function(data) {
	        if (data.status == 1) {
            updateLiveTableust();
	        } else {
	          alert(data.statusText);
	        }
	        App.loading.close();
	      }
	    });
	  }
	  return false;
	}

	function UstprfAendern(){
		var conf = confirm('Wirklich übernehmen?');
		if(conf){			
			$.ajax({
		    url: 'index.php?module=adresse&action=ustprf&cmd=aendern',
		    data: {
		      //Alle Felder die fürs editieren vorhanden sind
		      ustprfid: $('#e_ustprfid').val(),
		      adressid: $('#e_adressid').val(),
		      ustid: $('#e_ustid').val(),
		      name: $('#e_name').val(),
		      ort: $('#e_ort').val(),
		      plz: $('#e_plz').val(),
		      strasse: $('#e_strasse').val(),
		      land: $('#e_land').val()
		    },
		    method: 'post',
		    dataType: 'json',
		    beforeSend: function() {
		      App.loading.open();
		    },
		    success: function(data) {
		      App.loading.close();
		      if (data.status == 1) {
		        $('#editUstprf').find('#e_ustprfid').val('');
		        $('#editUstprf').find('#e_ustid').val('');
		        $('#editUstprf').find('#e_name').val('');
		        $('#editUstprf').find('#e_ort').val('');
		        $('#editUstprf').find('#e_plz').val('');
		        $('#editUstprf').find('#e_strasse').val('');
		        $('#editUstprf').find('#e_land').val('');
		        alert(data.statusText);
            updateLiveTableust();
		        $("#editUstprf").dialog('close');
		      } else {
		        alert(data.statusText);
		      }
		    }
		  });
		}
	}

	function UstprfFehlgeschlagen(){
		var conf = confirm('Wirklich als fehlgeschlagen markieren?');

		if(conf){
			$.ajax({
		    url: 'index.php?module=adresse&action=ustprf&cmd=fehlgeschlagen',
		    data: {
		      //Alle Felder die fürs editieren vorhanden sind
		      ustprfid: $('#e_ustprfid').val(),
		      adressid: $('#e_adressid').val()
		      
		    },
		    method: 'post',
		    dataType: 'json',
		    beforeSend: function() {
		      App.loading.open();
		    },
		    success: function(data) {
		      App.loading.close();
		      if (data.status == 1) {
		        alert(data.statusText);
            updateLiveTableust();
		        document.getElementById("status").innerHTML = data.antwortstatus;
			      document.getElementById("onlinestatus").innerHTML = data.onlinestatus;
		      } else {
		        alert(data.statusText);
		      }
		    }
		  });
		}
	}

	function UstprfManuellok(){
		var conf = confirm('Wirklich manuell auf OK setzen?');
		if(conf){
			$.ajax({
		    url: 'index.php?module=adresse&action=ustprf&cmd=manuellok',
		    data: {
		      //Alle Felder die fürs editieren vorhanden sind
		      ustprfid: $('#e_ustprfid').val(),
		      adressid: $('#e_adressid').val()
		      
		    },
		    method: 'post',
		    dataType: 'json',
		    beforeSend: function() {
		      App.loading.open();
		    },
		    success: function(data) {
		      App.loading.close();
		      if (data.status == 1) {
		        alert(data.statusText);
            updateLiveTableust();
	          document.getElementById("status").innerHTML = data.uststatus;
						document.getElementById("onlinestatus").innerHTML = data.datum_online;
						document.getElementById("briefbestellt").innerHTML = data.datum_brief;
		      } else {
		        alert(data.statusText);
		      }
		    }
		  });
		}
	}

	function UstprfMail(){
		UstprfMailReset();
		$('#editUstprfMail').dialog('open');
	}

	function UstprfEditSaveMail(){
    $('body').loadingOverlay();
		$.ajax({
		  url: 'index.php?module=adresse&action=ustprf&cmd=benachrichtigen',
		  data: {
		    //Alle Felder die fürs editieren vorhanden sind
		    ustprfid: $('#e_ustprfid').val(),
		    adressid: $('#e_adressid').val(),
		    betreff: $('#e_betreff').val(),
		    mailtext: $('#editUstprfMail').find('#e_mailtext').val()  
		  },
		  method: 'post',
		  dataType: 'json',
		  beforeSend: function() {
		  },
		  success: function(data) {
		  	if(data.status == 1){
		  		alert(data.statusText);
		  		UstprfMailReset();
          updateLiveTableust();
			    document.getElementById("status").innerHTML = data.antwortstatus;
			    document.getElementById("onlinestatus").innerHTML = data.onlinestatus;
			    $("#editUstprfMail").dialog('close');			    
			  }else{
		      alert(data.statusText);
		    }
		  },
      complete: function () {
        $('body').loadingOverlay('remove');
      }
		});		
	}

	function UstprfBrief(){
		$('#editUstprf').loadingOverlay();
		$.ajax({
		  url: 'index.php?module=adresse&action=ustprf&cmd=brief',
      data: {
		    ustprfid: $('#e_ustprfid').val(),
		    adressid: $('#e_adressid').val(),
		    ustid: $('#e_ustid').val(),
		    name: $('#e_name').val(),
		    ort: $('#e_ort').val(),
		    plz: $('#e_plz').val(),
		    strasse: $('#e_strasse').val(),
		    land: $('#e_land').val()		      
		  },

		  method: 'post',
		  dataType: 'json',
		  beforeSend: function() {
		    App.loading.open();
		  },
		  success: function(data) {
		  	$('#editUstprf').loadingOverlay('remove');
		    App.loading.close();
		    if (data.status == 1) {
		      alert(data.statusText);
          updateLiveTableust();
		      document.getElementById("briefbestellt").innerHTML = data.datum_brief;
          document.getElementById("brief").className = "btnBlueNew";
		      document.getElementById("online").className = "btnGreenNew";
		    } else {
		      alert(data.statusText);
		    }
		  }
		});
	}

	function UstprfStammdaten(){
		$.ajax({
		  url: 'index.php?module=adresse&action=ustprf&cmd=stammdaten',
      data: {
      	ustprfid: $('#e_ustprfid').val(),
		    adressid: $('#e_adressid').val(),
		    ustid: $('#e_ustid').val(),
		    name: $('#e_name').val(),
		    ort: $('#e_ort').val(),
		    plz: $('#e_plz').val(),
		    strasse: $('#e_strasse').val(),
		    land: $('#e_land').val()		      
		  },

		  method: 'post',
		  dataType: 'json',
		  beforeSend: function() {
		    App.loading.open();
		  },
		  success: function(data) {
		    App.loading.close();
		    if (data.status == 1) {
		      alert(data.statusText);
          updateLiveTableust();
		      /*document.getElementById("briefbestellt").innerHTML = data.datum_brief;
          document.getElementById("brief").className = "btnBlueNew";
		      document.getElementById("online").className = "btnGreenNew";*/
		    } else {
		      alert(data.statusText);
		    }
		  }
		});
	}

	function UstprfOnline(){
		$('#editUstprf').loadingOverlay(); 

		$.ajax({
		  url: 'index.php?module=adresse&action=ustprf&cmd=online',
		  data: {
		    //Alle Felder die fürs editieren vorhanden sind
		    ustprfid: $('#e_ustprfid').val(),
		    adressid: $('#e_adressid').val(),
		    ustid: $('#e_ustid').val(),
		    name: $('#e_name').val(),
		    ort: $('#e_ort').val(),
		    plz: $('#e_plz').val(),
		    land: $('#e_land').val(),
		    strasse: $('#e_strasse').val()		      
		  },
		  method: 'post',
		  dataType: 'json',
		  beforeSend: function() {
		    App.loading.open();
		  },
		  success: function(data) {
		  	$('#editUstprf').loadingOverlay('remove');
		    App.loading.close();
		    var statusErgebnis = data.uststatus;
		    if(statusErgebnis == 'online'){
          statusErgebnis = 'erfolgreich geprüft';
        }
		    if(statusErgebnis == 'gueltigmarkieren'){
          statusErgebnis = 'manuell als gültig markiert';
        }
	      document.getElementById("status").innerHTML = statusErgebnis;
	      document.getElementById("onlinestatus").innerHTML = data.datum_online;
		    document.getElementById("briefbestellt").innerHTML = data.datum_brief;
	
		    if (data.status == 1) {
		      alert(data.statusText);

		      document.getElementById("brief").className = "btnGreenNew";
		      document.getElementById("online").className = "btnBlueNew";
		      //$("#editUstprf").dialog('close');
		    } else {
		      document.getElementById("brief").className = "btnBlueNew";
		      document.getElementById("online").className = "btnGreenNew";
          if(data.statusText != '' && (data.error_code == '200' || data.error_code == '222')){
            UstprfMeldungBestaetigen(data.ustprfid, data.statusText);
          }else{
            alert(data.statusText);
          }
		    }
        updateLiveTableust();
        updateLiveTableprotokoll();
		  }
		});
	}

	function UstprfMeldungBestaetigen(id, meldung){
    var conf = confirm('Rückmeldung vom BZSt:\n\n'+meldung+'\n\nTrotzdem als gültig markieren?\nEs wird keine schriftl. Mitteilung angefordert');
    if (conf) {
      $.ajax({
        url: 'index.php?module=adresse&action=ustprf&cmd=gueltigmarkieren',
        data: {
          ustprfid: id,
					ustid: $('#editUstprf').find('#e_ustid').val(),
					name: $('#editUstprf').find('#e_name').val(),
					ort: $('#editUstprf').find('#e_ort').val(),
					plz: $('#editUstprf').find('#e_plz').val(),
					strasse: $('#editUstprf').find('#e_strasse').val(),
					land:	$('#editUstprf').find('#e_land').val()
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
          App.loading.open();
        },
        success: function(data) {
          if (data.status == 1) {
            var statusErgebnis = data.uststatus;
            if(statusErgebnis == 'online'){
              statusErgebnis = 'erfolgreich geprüft';
            }
            if(statusErgebnis == 'gueltigmarkieren'){
              statusErgebnis = 'manuell als gültig markiert';
            }
            document.getElementById("status").innerHTML = statusErgebnis;
            document.getElementById("onlinestatus").innerHTML = data.datum_online;
            updateLiveTableust();
            updateLiveTableprotokoll();
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


 <script>
  $(function() {
    $( "#accordion" ).accordion({
      heightStyle: "content"
    });


  });



function schnelleingabeteilprojekt()
{
  $('[name=arbeitspaket]').val($('#schnell_teilprojekt').val());
  $('[name=aufgabe]').val($('#schnell_beschreibung').val());
  $('[name=datum]').val($('#teilprojekt_datum').val());
  $('[name=beschreibung]').val($('#teilprojekt_beschreibung').val());
  dauer = $('#schnell_dauer').val();
  dauer = dauer.replace(',','.'); if(dauer > 0) BerechneEndzeitManuell(dauer*60);
  $('input[type="submit"]:last').trigger('click');//selects the first one
}

function schnelleingabekunde()
{
  $('[name=adresse_abrechnung]').val($('#schnell_kunde').val());
  $('[name=aufgabe]').val($('#schnell_kundebeschreibung').val());
  $('[name=datum]').val($('#kunde_datum').val());
  if($('#kunde_abrechnen').prop('checked')) {
    $('[name=abrechnen]').prop('checked', true);
  } else {
    $('[name=abrechnen]').prop('checked', false);
  }
  $('[name=beschreibung]').val($('#kunde_beschreibung').val());
  dauer = $('#schnell_kundedauer').val();
  dauer = dauer.replace(',','.'); if(dauer > 0) BerechneEndzeitManuell(dauer*60);
  $('input[type="submit"]:last').trigger('click');//selects the first one
}


var location2;
function successHandler(location) {
		//.push("<p>Longitude: ", location.coords.longitude, "</p>");
		//.push("<p>Latitude: ", location.coords.latitude, "</p>");
		//.push("<p>Accuracy: ", location.coords.accuracy, " meters</p>");

   var message = document.getElementById("message"), html = [];
    html.push("<img width='180' height='180' src='http://maps.google.com/maps/api/staticmap?center=", location.coords.latitude, ",", location.coords.longitude, "&markers=size:small|color:blue|", location.coords.latitude, ",", location.coords.longitude, "&zoom=14&size=180x180&sensor=false' />");
    message.innerHTML = html.join("");

		document.getElementById("gps").value = location.coords.latitude + ";" + location.coords.longitude;// + ";"+location.coords.accuracy;
}
function errorHandler(error) {
    alert('Attempt to get location failed: ' + error.message);
}

function Standpunkt()
{
	navigator.geolocation.getCurrentPosition(successHandler,errorHandler);
}


$(document).ready(function () {



$( "#art" ).change(function() {
    if( this.value !='Arbeit')
      $('#aufgabe').val(this.value);
    else 
      $('#aufgabe').val('');
  
});

    $('#c1').on('change', function(){
        if ($(this).prop('checked')) {
            $('#mitarbeiterrow').show();
        }
        else {
            $('#mitarbeiterrow').hide();
            $('#mitarbeiter').val('');
        }
    });
});

$(document).ready(function () {
    $('#c2').on('change', function(){
        if ($(this).prop('checked')) {
            $('#teilprojektrow').show();
            $('#projektrow').hide();
						$('input[name=c3]').attr('checked', false);
        }
        else {
            $('#teilprojektrow').hide();
        }
    });
});


$(document).ready(function () {
    $('#c3').on('change', function(){
        if ($(this).prop('checked')) {
            $('#projektrow').show();
            $('#teilprojektrow').hide();
						$('input[name=c2]').attr('checked', false);
        }
        else {
            $('#projektrow').hide();
        }
    });
});

// Methode zum addieren/subtrahieren einer Menge an Minuten auf eine Uhrzeit
// time = Uhrzeit im Format HH:MM
// offset = Zeit in Minuten
function addMinutes2(time, offset){
	// Uhrzeit wird in Stunden und Minuten geteilt
	var elements = time.split(":");
	var hours = elements[0];	
	var minutes = elements[1];
	// Aufrunden des Offsets fuer den Fall, dass eine Fliesskommazahl uebergeben wird
	var roundOffset = Math.ceil(offset);
	
	// Umrechnen der Uhrzeit in Minuten seit Tagesbeginn
	var timeSince24 = (hours * 60) + parseInt(minutes);
	// Addieren des uebergebenen Offsets
        timeSince24 = timeSince24 + parseInt(roundOffset);

	// Ueberlaufbehandlung
	if(timeSince24 < 0)
		timeSince24 = timeSince24 + 1440;
	else if(timeSince24 > 1440)
		timeSince24 = timeSince24 - 1440;
	
	// Errechnen von Stunden und Minuten aus dem Gesamtzeit seit Tagesbeginn
	var resMinutes = timeSince24 % 60;
	var resHours = (timeSince24 - resMinutes)/60;
	
	// Sicherstellen, dass der Wert fuer Minuten immer zweistellig ist
	if(resMinutes < 10)
		resMinutes = "0" + resMinutes;
	
        if(resHours>23)
        {
          resHours=23;
          resMinutes=59;
        }	

        if(resMinutes>59)
        {
          resMinutes=59;
        }	
	// Ausgabe des formatierten Ergebnisses
	return resHours + ":" + resMinutes;
}


function BerechneEndzeitManuell(minuten)
{
  var vonzeit = $('#ZeiterfassungDialogManuell').find('#vonZeit').val();
  $('#ZeiterfassungDialogManuell').find('#bisZeit').val(addMinutes2(vonzeit,minuten));
}


</script>

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-6 col-md-height">
<div class="inside inside-full-height" style="background:none">

<form action="" method="post" name="eprooform">
<!--<br><div class="info">Bitte geben Sie max. Einheiten von 1-3 Stunden an.</div><br>-->
<input type="hidden" name="art" value="arbeit">


<div id="accordion" style="width:100%">
<h2 class="grey2">{|Zeiterfassung|}</h2>
<div id="ZeiterfassungDialogManuell">

<table class="mkTableFormular" style="width:100%">
 <tr><td>{|Am|}:</td>
	<td colspan="3">
 <input type="text" name="datum" id="datum" size="10" value="[DATUM]" class="pflicht" maxlength="">&nbsp;{|von|}&nbsp;
<input type="text" name="vonZeit" id="vonZeit" size="5" value="[VONZEIT]" class="pflicht">&nbsp;{|Uhr|} &nbsp;{|Bis|}:&nbsp;
 <input type="text" name="bisZeit" id="bisZeit" size="5"  value="[BISZEIT]" class="pflicht">&nbsp;{|Uhr|} (HH:MM)
</td></tr>
 <tr><td></td>
	<td colspan="3">
<input type="button" value="15 Min" onclick="BerechneEndzeitManuell(15);">&nbsp;
	<input type="button" value="30 Min" onclick="BerechneEndzeitManuell(30);">&nbsp;
	<input type="button" value="45 Min" onclick="BerechneEndzeitManuell(45);">&nbsp;
<input type="button" value="1 Std" onclick="BerechneEndzeitManuell(60);">&nbsp;
<input type="button" value="2 Std" onclick="BerechneEndzeitManuell(120);">
<input type="button" value="Dauer" onclick="var dauer = prompt('Dauer eingeben z.B. 3,5 für 3,5 Stunden:',''); dauer = dauer.replace(',','.'); if(dauer > 0) BerechneEndzeitManuell(dauer*60);">
</td></tr>

 <tr><td></td><td colspan="3"><i>{|Bitte die Pausen gesondert als Pausen (nicht Arbeit) buchen.|}</i></td></tr>

 <tr><td>{|Art/Tätigkeit|}:</td><td colspan="3"><select name="art" id="art">[ART]</select>&nbsp;<input type="text" name="aufgabe" id="aufgabe" size="40" value="[AUFGABE]" class="pflicht"></td></tr>

[STARTANDEREERLAUBEN]
 <tr><td></td><td colspan="3">
<input type="checkbox" id="c1" [ANDERERMITARBEITER]>&nbsp;{|f&uuml;r anderen Mitarbeiter Zeit buchen|}
</td></tr>
<tr id="mitarbeiterrow" style="display:[DISPLAYANDERERMITARBEITER]"><td>{|Auswahl Mitarbeiter|}:</td><td>[MITARBEITERSTART]<input type="text" id="mitarbeiter" size="50" name="mitarbeiter" value="[MITARBEITER]">[MITARBEITER_END]</td></tr>
[ENDEANDEREERLAUBEN]

 <tr><td>{|Details|}:</td><td colspan="2" nowrap><textarea type="text" id="beschreibung" name="beschreibung" cols="62" rows="5" >[BESCHREIBUNG]</textarea></td><td></td></tr>
[STARTKOMMENTAR]
 <tr><td>{|Interner Kommentar|}:</td><td colspan="2" nowrap><textarea type="text" name="internerkommentar" cols="62" rows="3">[INTERNERKOMMENTAR]</textarea></td><td></td></tr>
[ENDEKOMMENTAR]
[STARTORT]
 <tr><td>{|Ort (wenn extern)|}:</td><td colspan="3"><input type="text" id="ort" name="ort" size="62" value="[ORT]"></td></tr>
 <tr><td></td><td><input type="hidden" id="gps" name="gps"  value="[GPS]">&nbsp;[GPSBUTTON]<div id="message">[GPSIMAGE]</div></td></tr>
[ENDEORT]
<tr><td>{|Projekt|}:</td><td>[PROJEKT_MANUELLAUTOSTART]<input type="text" id="projekt_manuell" size="50" name="projekt_manuell" value="[PROJEKT_MANUELL]">[PROJEKT_MANUELLAUTOEND]</td></tr>
<tr id="teilprojektrow" style="display:"><td>{|Teilprojekt|}:</td><td><input type="text" name="arbeitspaket" id="arbeitspaket" value="[PAKETAUSWAHL]" size="50"></td></tr>
[STARTERWEITERT]
<tr><td></td><td colspan="3"><br></td></tr>
<tr><td>{|Kunde|}:</td><td>[ADRESSE_ABRECHNUNGAUTOSTART]<input type="text" id="adresse_abrechnung" size="50" name="adresse_abrechnung" value="[ADRESSE_ABRECHNUNG]">[ADRESSE_ABRECHNUNGAUTOEND]</td></tr>
<tr><td>{|Auftrag|}:</td><td><input type="text" id="auftrag" size="50" name="auftrag" value="[AUFTRAG]"></td></tr>
<tr><td>{|Auftragsposition|}:</td><td><input type="text" id="auftragpositionid" size="50" name="auftragpositionid" value="[AUFTRAGPOSITIONID]"></td></tr>
<tr><td>{|Produktion|}:</td><td><input type="text" id="produktion" size="50" name="produktion" value="[PRODUKTION]"></td></tr>
[VORSERVICEAUFTRAG]<tr><td>{|Serviceauftrag|}:</td><td><input type="text" id="serviceauftrag" size="50" name="serviceauftrag" value="[SERVICEAUFTRAG]"></td></tr>[NACHSERVICEAUFTRAG]
<tr><td>{|Abrechnen|}:</td><td><input type="checkbox" name="abrechnen" id="abrechnen" value="1" [ABRECHNEN]>&nbsp;<i>{|Bitte ausw&auml;hlen, wenn Zeit abgerechnet werden soll.|}</i></td></tr>
[ENDEERWEITERT]



<!--<tr><td>Verrechnungsart:</td><td><input type="text" id="verrechnungsart" size="50" name="verrechnungsart" value="[VERRECHNUNGSART]"></td></tr>-->


</table>
<table width="100%"><tr><td align="right">[BUTTON]</td></tr></table>
</div>
<!--
<h2 class="grey2">Schnelleingabe Teilprojekt</h2>
<div>
<table class="mkTableFormular">
<tr><td>Teilprojekt:</td><td><input type="text" size="50" name="schnell_teilprojekt" id="schnell_teilprojekt"></td></tr>
<tr><td>Beschreibung:</td><td><input type="text" size="50" id="schnell_beschreibung"></td></tr>
<tr><td>Details:</td><td><textarea type="text" id="teilprojekt_beschreibung" cols="50" rows="5"></textarea></td></tr>
<tr><td>Datum / Dauer:</td><td><input type="text" name="teilprojekt_datum" id="teilprojekt_datum" size="10" value="[DATUM]">&nbsp;<input type="text" size="20" id="schnell_dauer">&nbsp;(in h z.B. <b>0,5</b> für 30 min)</td></tr>
<tr><td></td><td align="right"><input type="button" value="Buchen" onclick="schnelleingabeteilprojekt();"></td></tr>
</table>
</div>
-->
<!--
<h2 class="grey2">Schnelleingabe Kunde</h2>
<div>
<table class="mkTableFormular">
<tr><td>Kunde:</td><td><input type="text" size="50" name="schnell_kunde" id="schnell_kunde"></td></tr>
<tr><td>Beschreibung:</td><td><input type="text" size="50" id="schnell_kundebeschreibung"></td></tr>
<tr><td>Details:</td><td><textarea type="text" id="kunde_beschreibung" cols="50" rows="5"></textarea></td></tr>
<tr><td>Datum / Dauer:</td><td><input type="text" name="kunde_datum" id="kunde_datum" size="10" value="[DATUM]">&nbsp;<input type="text" size="20" id="schnell_kundedauer">&nbsp;(in h z.B. <b>0,5</b> für 30 min)</td></tr>
<tr><td>Abrechnen:</td><td><input type="checkbox" id="kunde_abrechnen" value="1">&nbsp;<i>Bitte ausw&auml;hlen, wenn Zeit abgerechnet werden soll.</i></td></tr>
<tr><td></td><td align="right"><input type="button" value="Buchen" onclick="schnelleingabekunde();"></td></tr>
</table>
</div>
-->

</div>

[ZEITERFASSUNG_CREATE_GUIHOOK1]
</form>


</div>
</div>

<div class="col-xs-12 col-md-6 col-md-height further-grid"><div class="inside inside-full-height" style="background:none">
<fieldset><legend>&Uuml;bersicht</legend>

<table width="100%">
<tr>
        <td>{|Arbeit|} [ANZEIGEDATUMZEITERFASSUNG]</td>
        <td>{|Woche IST / SOLL|}</td>
        <td>{|Monat IST / SOLL|}</td>
        <td>{|Woche offen|}</td>
        <td>{|Pause|} [ANZEIGEDATUMZEITERFASSUNG]</td>
        <td>{|Urlaub offen / genommen|}</td>
</tr>

<tr>
  <td class="greybox" width="16%">[HEUTE]</td>
  <td class="greybox" width="16%">[WOCHEIST]/[WOCHESOLL]</td>
  <td class="greybox" width="16%">[MONATIST]/[MONATSOLL]</td>
  <td class="greybox" width="16%">[OFFEN]</td>
  <td class="greybox" width="16%">[PAUSE]</td>
  <td class="greybox" width="20%">[URLAUBOFFEN]/[URLAUBGENOMMEN]</td>
</tr>
</table>
</fieldset>
<fieldset><legend>{|Datum|}: [ANZEIGEDATUMZEITERFASSUNG]</legend>

[BUCHUNGEN]
<br>
<center>
<form action="index.php?module=zeiterfassung&action=create#tabs-1" method="post"><input type="submit" value="Zurück"><input type="hidden" name="datumzeiterfassung" value="[ZURUECKDATUM]"></form>
<form action="index.php?module=zeiterfassung&action=create#tabs-1" method="post"><input type="text" size="12" name="datumzeiterfassung" id="datumzeiterfassung" value="[DATUMZEITERFASSUNG]" onchange="this.form.submit()"></form>
<form action="index.php?module=zeiterfassung&action=create#tabs-1" method="post"><input type="submit" value="Vorwärts"><input type="hidden" name="datumzeiterfassung" value="[VORWAERTSDATUM]"></form>&nbsp;

</center>
</fieldset>

</div>
</div>
</div>
</div>








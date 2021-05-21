<script type="text/javascript">

	function OpenSponsor(feld)
	{
		var sponsor = document.getElementById(feld).value;	

 		var res = sponsor.split(" "); 

		if(res[0]!="")
			window.open('index.php?module=adresse&action=open&cmd=multilevel&kundennummer='+res[0], '_blank');	
	}

</script>

<form action="" method="post">
<fieldset><legend>{|Einstellungen|}</legend>
<table>
<tr><td width="200">Sponsor</td><td>[SPONSORSTART]<input type="text" size="40" name="sponsor" id="sponsor" value="[SPONSOR]">[SPONSOREND]&nbsp;<input type="button" value="&ouml;ffnen" onclick="OpenSponsor('sponsor');"></td></tr>
<tr><td>Geworben von</td><td>[GEWORBENVONSTART]<input type="text" size="40" name="geworbenvon" id="geworbenvon" value="[GEWORBENVON]">[GEWORBENVONEND]&nbsp;<input type="button" value="&ouml;ffnen" onclick="OpenSponsor('geworbenvon');"><br><i>(Dient nur für Auswertungen)</i></td></tr>
</table>
</fieldset>
<fieldset><legend>{|Lizennehmer Einstellungen|}</legend>
<table>
<tr><td width="200">Aktiver Lizenznehmer</td><td><input type="checkbox" size="20" name="mlmaktiv" value="1" [MLMAKTIV]>&nbsp;</td></tr>
<tr><td>Intranet komplette Struktur</td><td><input type="checkbox" size="20" name="mlmintranetgesamtestruktur" value="1" [MLMINTRANETGESAMTESTRUKTUR]>&nbsp;</td></tr>
<tr><td>Vertragsbeginn</td><td><input type="text" size="10" name="mlmvertragsbeginn" id="mlmvertragsbeginn" value="[MLMVERTRAGSBEGINN]"></td></tr>
<tr><td>Lizenzgeb&uuml;hr bezahlt bis</td><td><input type="text" size="10" name="mlmlizenzgebuehrbis" id="mlmlizenzgebuehrbis" value="[MLMLIZENZGEBUEHRBIS]"></td></tr>
<tr><td>Positionierung Festsetzen</td><td><input type="checkbox" size="20" name="mlmfestsetzen" value="1" [MLMFESTSETZEN]>&nbsp;<select name="mlmpositionierung">[MLMPOSITIONIERUNG]</select>&nbsp;bis&nbsp;<input type="text" size="10" name="mlmfestsetzenbis" id="mlmfestsetzenbis" value="[MLMFESTSETZENBIS]"></td></tr>
<!--<tr><td>Datum letzte Positionierung</td><td><input type="text" size="10" name="rolledatum" id="rolledatum" value="[ROLLEDATUM]"></td></tr>-->

<tr><td>Auto-Qualifizierung</td><td><input type="checkbox" name="mlmmindestpunkte" value="1" [MLMMINDESTPUNKTE]>&nbsp;<i>Lizenznehmer erh&auml;lt Auszahlung auch ohne erreichen der mindest Punkte</i></td></tr>
<!--<tr><td>Betrag im Wartekonto</td><td><input type="text" size="20" name="mlmwartekonto" readonly value="[MLMWARTEKONTO]" readonly></td></tr>-->
</table></fieldset>
<fieldset><legend>{|Auszahlung|}</legend>
<table>
<tr><td width="200">Steuernummer</td><td><input type="text" size="20" name="steuernummer" value="[STEUERNUMMER]"></td></tr>
<tr><td>Auszahlung</td><td><select name="mlmabrechnung">[MLMABRECHNUNG]</select></td></tr>
<!--<tr><td>W&auml;hrung f&uuml;r Auszahlung</td><td><select name="mlmwaehrungauszahlung">[MLMWAEHRUNGAUSZAHLUNG]</select></td></tr>-->
<tr><td>Projekt f&uuml;r Auszahlung</td><td><input type="text" name="mlmauszahlungprojekt" value="[MLMAUSZAHLUNGPROJEKT]" id="mlmauszahlungprojekt"></td></tr>
<tr><td>MWSt</td><td><input type="checkbox" size="20" name="mlmmitmwst" value="1" [MLMMITMWST]>&nbsp;<i>bei Provisionsauszahlung</i></td></tr>
</table>
</fieldset>
<br><center><input type="submit" name="mlmsubmit" value="Speichern"></center>
<br>
<fieldset><legend>{|Positionierung Historie|}</legend>
[HISTORIE]
</fieldset>

<fieldset><legend>{|Direkte Downline|}</legend>
[DOWNLINETABELLE]
</fieldset>

<fieldset><legend>{|Geworben von|}</legend>
[GEWORBENVONTABELLE]
</fieldset>

</form>

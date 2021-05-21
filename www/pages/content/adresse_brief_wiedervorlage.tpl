<form action="" method="POST" name="brief_erstellen_form" class="brief_erstellen_form">
	<input type="hidden" name="adresse" id="adresse" value="[ADRESSE]">
	<input type="hidden" name="adressid" id="adressid" value="[ID]">

	<input type="hidden" name="type" value="wiedervorlage">
	<input type="hidden" name="eintragId" value="[EINTRAGID]">

	<table class="adresse_brief_tab" width="100%" border="0" cellpadding="2" cellspacing="0">
		<tr>
			<td width="100">Angelegt am:</td>
			<td>
				<input type="hidden" name="datum" value="[DATUM]" style="width: 100px;" id="datum">[DATUM] um
				<input type="hidden" name="uhrzeit" value="[UHRZEIT]" style="width: 100px;" id="uhrzeit">[UHRZEIT]
			</td>
		</tr>
		<tr>
			<td width="100">Bearbeiter:</td>
			<td><input type="text" name="bearbeiter" id="bearbeiter" value="[BEARBEITER]" style="width: 370px;"></td>
		</tr>
		<tr>
			<td width="100">{|Ansprechpartner|}:</td>
			<td><input type="text" name="ansprechpartner" id="ansprechpartner" value="[ANSPRECHPARTNER]" style="width: 370px;"></td>
		</tr>
		<tr>
			<td width="100">Projekt:</td>
			<td><input type="text" name="projekt" value="[PROJEKT]" id="projekt" style="width: 370px;"></td>
		</tr>
		<tr>
			<td width="100">Betreff:</td>
			<td><input type="text" name="betreff" value="[BETREFF]" style="width: 370px;"></td>
		</tr>
		<tr>
			<td>Text:</td>
			<td><textarea name="content" id="content" style="min-height: 180px;">[CONTENT]</textarea></td>
		</tr>
		<tr>
			<td width="">{|Volumen|}:</td>
			<td><input type="text" name="betrag" size="20" value="[BETRAG]">&nbsp;{|in EUR|}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{|Chance|}
				:&nbsp;<select name="chance">
					[CHANCE]
				</select>
			</td>
		</tr>
		<tr>
			<td width="">{|Stage|}:</td>
			<td><input type="text" name="stages" style="width: 370px;" id="stages" value="[STAGES]"></td>
		</tr>
		<tr>
			<td>Erinnerung-Datum:</td>
			<td>
				<input type="text" name="datumerinnerung" value="[DATUM_ERINNERUNG]" style="width: 100px;" id="datum_erinnerung">&nbsp;&nbsp;&nbsp;
				Uhrzeit:&nbsp;
				<input type="text" name="uhrzeiterinnerung" value="[UHRZEIT_ERINNERUNG]" style="width: 100px;" id="uhrzeit_erinnerung">&nbsp;
				<i>(Wiedervorlage)</i>
			</td>
		</tr>
		<tr>
			<td width="100">Mitarbeiter:</td>
			<td><input type="text" name="adresse_mitarbeiter" id="adresse_mitarbeiter" value="[MITARBEITER]" style="width: 370px;"></td>
		</tr>
		<tr>
			<td width="100">Prio:</td>
			<td><input type="checkbox" name="prio" id="prio" value="1" [PRIO]></td>
		</tr>
		<tr>
			<td width="100">Abgeschlossen:</td>
			<td><input type="checkbox" name="abgeschlossen" id="abgeschlossen" value="1" [ABGESCHLOSSEN]></td>
		</tr>
		<tr>
			<td colspan="2">
				<table width="100%" border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td valign="bottom">
							<input type="submit" name="save" value="Speichern" class="brief_save">
							<input type="submit" name="save" value="Speichern / Schließen" class="brief_save_close">
							<input type="button" onclick="briefDrucken([EINTRAGID]);" value="Vorschau-Druck">
							[DATEIENBUTTON]
						</td>
						<td align="right" valign="bottom">
							<input type="button" name="close" value="Schließen" class="anlegen_close">
						</td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

</form>

<script type="text/javascript">
$("input#projekt").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=projektname",
});
$("#datum").datepicker({dateFormat: "dd.mm.yy"});
$("#uhrzeit").timepicker();
$("#datum_erinnerung").datepicker({
    dateFormat: 'dd.mm.yy',
    dayNamesMin: ['SO', 'MO', 'DI', 'MI', 'DO', 'FR', 'SA'],
    firstDay: 1,
    showWeek: true,
    monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
});
$("#uhrzeit_erinnerung").timepicker();
$("#adresse_mitarbeiter").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=mitarbeiter"
});
$("#bearbeiter").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=mitarbeiter"
});
$("#adresse").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=kunde"
});

$("#stages").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=wiedervorlage_stages"
});

$("input#ansprechpartner").autocomplete({
    source: "index.php?module=ajax&action=filter&filtername=ansprechpartneradresse&adresse="+document.getElementById("adressid").value,
});

[JQUERY2]
</script>

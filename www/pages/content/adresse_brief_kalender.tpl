<form action="" method="POST" name="brief_erstellen_form" class="brief_erstellen_form">

	<input type="hidden" name="adressid" id="adressid" value="[ID]">
	<input type="hidden" name="type" value="kalender">
	<input type="hidden" name="eintragId" value="[EINTRAGID]">

	<table class="adresse_brief_tab" width="100%" border="0" cellpadding="2" cellspacing="0">
		<tr>
			<td width="100">Datum:</td>
			<td><input type="text" name="datum" value="[DATUM]" style="width: 100px;" id="datum"></td>
		</tr>
		<tr>
			<td width="100">Uhrzeit:</td>
			<td><input type="text" name="uhrzeit" value="[UHRZEIT]" style="width: 100px;" id="uhrzeit"></td>
		</tr>
		<tr>
			<td width="100">Datum bis:</td>
			<td><input type="text" name="datumbis" value="[DATUMBIS]" style="width: 100px;" id="datumbis"></td>
		</tr>
		<tr>
			<td width="100">Uhrzeit bis:</td>
			<td><input type="text" name="uhrzeitbis" value="[UHRZEITBIS]" style="width: 100px;" id="uhrzeitbis"></td>
		</tr>
		<tr>
			<td width="100">ganztags:</td>
			<td><input type="checkbox" value="1" name="allDay" [ALLDAY]" /></td>
		</tr>
		<tr>
			<td width="100">&ouml;ffentlich:</td>
			<td><input type="checkbox" value="1" name="public" [PUBLIC]" /></td>
		</tr>

		<tr>
			<td width="100">Angelegt von:</td>
			<td><input type="text" name="angelegtvon" id="angelegtvon" value="[BEARBEITER]" style="width: 370px;"></td>
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
			<td width="100">Verantwortlicher:</td>
			<td><input type="text" name="adresseintern" id="adresseintern" value="[ADRESSEINTERN]" style="width: 370px;"></td>
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
			<td colspan="2">
				<table width="100%" border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td align="left" valign="bottom">
							<input type="submit" name="save" value="Speichern" class="brief_save">
							<input type="submit" name="save" value="Speichern / Schließen" class="brief_save_close">
							<input type="button" onclick="briefDrucken([EINTRAGID]);" value="Vorschau-Druck">
							[LAYOUTBUTTON]
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
$("#datum").datepicker({
  dateFormat: 'dd.mm.yy',
  dayNamesMin: ['SO', 'MO', 'DI', 'MI', 'DO', 'FR', 'SA'],
  firstDay: 1,
  showWeek: true,
  monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
});
$("#uhrzeit").timepicker();
$("#datumbis").datepicker({
  dateFormat: 'dd.mm.yy',
  dayNamesMin: ['SO', 'MO', 'DI', 'MI', 'DO', 'FR', 'SA'],
  firstDay: 1,
  showWeek: true,
  monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
});
$("#uhrzeitbis").timepicker();

$("input#projekt").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=projektname",
});

$("#angelegtvon").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=mitarbeiter"
});
$("#adresseintern").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=adressemitvertrieb"
});

$("input#ansprechpartner").autocomplete({
    source: "index.php?module=ajax&action=filter&filtername=ansprechpartneradresse&adresse="+document.getElementById("adressid").value,
});

[JQUERY2]
</script>

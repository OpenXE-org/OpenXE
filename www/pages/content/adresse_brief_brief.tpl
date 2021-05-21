<form action="" method="POST" name="brief_erstellen_form" class="brief_erstellen_form">

	<input type="hidden" name="type" value="brief">
	<input type="hidden" name="eintragId" value="[EINTRAGID]">

	<table class="adresse_brief_tab" width="100%" border="0" cellpadding="2" cellspacing="0">
		<tr>
			<td>{|An|}:</td>
			<td colspan="3"><input type="text" name="an" value="[EMPFAENGER]" style="width: 370px;"></td>
		</tr>
		<tr>
			<td>{|Ansprechpartner|}:</td>
			<td colspan="3"><input type="text" name="ansprechpartner" value="[ANSPRECHPARTNER]" style="width: 370px;" id="ansprechpartner"></td>
		</tr>
		<tr>
			<td>{|Straße/HausNr.|}:</td>
			<td colspan="3"><input type="text" name="adresse" value="[STRASSE]" style="width: 370px;"></td>
		</tr>
		<tr>
			<td>{|PLZ/Ort|}:</td>
			<td colspan="3">
				<input type="text" name="plz" value="[PLZ]" style="width: 66px;">&nbsp;
				<input type="text" name="ort" value="[ORT]" style="width: 287px;"></td>
		</tr>
		<tr>
			<td>{|Land|}:</td>
			<td colspan="3">
				<select name="land">[LAND]</select>
			</td>
		</tr>
		<tr>
			<td colspan="4"><br></td>
		</tr>
		<tr>
			<td>Bearbeiter:</td>
			<td colspan="3"><input type="text" name="von" id="von" value="[SENDER]" style="width: 370px;"></td>
		</tr>
		<tr>
			<td width="100">{|Projekt|}:</td>
			<td colspan="3"><input type="text" name="projekt" value="[PROJEKT]" id="projekt" style="width: 370px;"></td>
		</tr>
		<tr>
			<td colspan="4"><br></td>
		</tr>
		<tr>
			<td width="100">{|Tags|}:</td>
			<td colspan="3"><input type="text" name="internebezeichnung" value="[INTERNEBEZEICHNUNG]" id="internebezeichnung" style="width: 370px;"></td>
		</tr>
		<tr>
			<td>{|Betreff|}:</td>
			<td colspan="3"><input type="text" name="betreff" style="width: 370px;" value="[BETREFF]"></td>
		</tr>
		<tr>
			<td>Datum:</td>
			<td colspan="3"><input type="text" name="datum" value="[DATUM]" style="width: 120px;" id="datum"></td>
		</tr>
		<tr>
			<td valign="top">{|Text|}:</td>
			<td colspan="3" style="width:100%;">
				<textarea name="content" id="content" size="" style="min-height: 150px;">[CONTENT]</textarea>
			</td>
		</tr>
		<tr>
			<td>{|Drucker|}</td>
			<td colspan="3">
				<select name="drucker">
					[DRUCKERSELECT]
				</select>
				<a href="javascript:;" class="brief_drucken">
					<img src="themes/new/images/icons_druck.png" valign="middle" width="20">
				</a>
				<a href="javascript:;" class="brief_pdf">
					<img src="themes/new/images/pdf.svg" valign="middle">
				</a>
				<!--
				<input type="submit" name="send" value="Drucken" class="brief_drucken">
				<input type="submit" name="send" value="PDF" class="brief_pdf">
				-->
			</td>
		</tr>
		<tr>
			<td colspan="4">
				<table width="100%" border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td>
							<input type="submit" name="save" value="Speichern" class="brief_save">
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
$("#von").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=mitarbeitername"
});
$("input#projekt").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=projektname",
});

$("#datum").datepicker({
		dateFormat: 'dd.mm.yy',
		dayNamesMin: ['SO', 'MO', 'DI', 'MI', 'DO', 'FR', 'SA'],
		firstDay: 1,
		showWeek: true,
		monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni', 'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember']
});

$("input#ansprechpartner").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=ansprechpartner&adresse=[ID]",
});
[JQUERY2]
$('#internebezeichnung').tagEditor();
</script>

<form action="" enctype="multipart/form-data" method="POST" name="brief_erstellen_form" class="brief_erstellen_form" id="brief_erstellen_form">

	<input type="hidden" name="type" value="email">
	<input type="hidden" name="eintragId" value="[EINTRAGID]">

	<div style="position: absolute; margin-top:27em; margin-left:30%; min-width: 90px; background: url(./themes/new/images/loading.gif) no-repeat; background-position: 50% 0px; background-size: 150px; padding-top: 90px; display:none; " id="mailworking">
	</div>
	<table class="adresse_brief_tab" width="100%" border="0" cellpadding="2" cellspacing="0">
		<tr>
			<td>Von:</td>
			<td><select name="von" style="min-width: 370px;">[EMAIL_SENDER]</select></td>
		</tr>
		<tr>
			<td>An:</td>
			<td><input type="text" name="email_an" value="[EMAIL_AN]" id="an" style="width: 370px;"></td>
		</tr>
		<tr>
			<td>CC:</td>
			<td><input type="text" name="email_cc" value="[EMAIL_CC]" id="cc" style="width: 370px;"></td>
		</tr>
		<tr>
			<td>BCC:</td>
			<td><input type="text" name="email_bcc" value="[EMAIL_BCC]" id="bcc" style="width: 370px;"></td>
		</tr>
		<tr>
			<td width="100">Projekt:</td>
			<td><input type="text" name="projekt" value="[PROJEKT]" id="projekt" style="width: 370px;"></td>
		</tr>
		<tr>
			<td colspan="3"><br></td>
		</tr>
		<tr>
			<td>Betreff:</td>
			<td><input type="text" name="betreff" value="[BETREFF]" style="width: 370px;"></td>
		</tr>
		<tr>
			<td>Text:</td>
			<td><textarea name="content" id="content" style="min-height: 180px;">[CONTENT]</textarea><br><i>(Signatur für E-Mail wird automatisch angehängt)</i></td>
		</tr>
		[ANHAENGEHERAUFLADEN]
		<tr>
			<td colspan="3"><br></td>
		</tr>
		<tr valign="top">
			<td>Anh&auml;nge:</td>
			<td>
				<table width="100%" class="mkTable" cellpadding="0" cellspacing="0">
					<tr>
						<th width="20"></th>
						<th>Datei</th>
						<th width=20></th>
					</tr>
					[ANHAENGE]
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3"><br></td>
		</tr>
		<tr>
			<td colspan="3">
				<table width="100%" border="0" cellpadding="2" cellspacing="0">
					<tr>
						<td valign="bottom">
							<input type="submit" name="save" value="Speichern" id="save"/>
							<input type="submit" name="send" value="Absenden" id="emailsend" class="brief_email_send">
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

$('#file').change(function () {
		$('input[type=submit]#save').click();
});

$("#an").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=emailname",
		select: function (event, ui) {
				var i = $("#an").val() + ui.item.value;
				var zahl = i.indexOf(",");
				var text = i.slice(0, zahl);
				if (zahl <= 0) {
						$("#an").val(ui.item.value);
				} else {
						var j = $("#an").val();
						var zahlletzte = j.lastIndexOf(",");
						var text2 = j.substring(0, zahlletzte);
						$("#an").val(text2 + "," + ui.item.value);
				}
				return false;
		}
});

$("#cc").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=emailname",
		select: function (event, ui) {
				var i = $("#cc").val() + ui.item.value;
				var zahl = i.indexOf(",");
				var text = i.slice(0, zahl);
				if (zahl <= 0) {
						$("#cc").val(ui.item.value);
				} else {
						var j = $("#cc").val();
						var zahlletzte = j.lastIndexOf(",");
						var text2 = j.substring(0, zahlletzte);
						$("#cc").val(text2 + "," + ui.item.value);
				}
				return false;
		}
});

$("#bcc").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=emailname",
		select: function (event, ui) {
				var i = $("#bcc").val() + ui.item.value;
				var zahl = i.indexOf(",");
				var text = i.slice(0, zahl);
				if (zahl <= 0) {
						$("#bcc").val(ui.item.value);
				} else {
						var j = $("#bcc").val();
						var zahlletzte = j.lastIndexOf(",");
						var text2 = j.substring(0, zahlletzte);
						$("#bcc").val(text2 + "," + ui.item.value);
				}
				return false;
		}
});

function remdatei(datei) {
		if (window.confirm("Datei wirklich löschen?")) {
				$.ajax({
						url: 'index.php',
						type: 'GET',
						data: {
								module: 'adresse',
								action: 'removeemailanhang',
								id: datei
						},
						success: function (data) {
								$('#trdatei_' + datei).remove();
						}
				});
		}
}

[JQUERY2]
</script>

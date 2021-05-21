<style>
	* {
	color: #000000;
font-family: "Arial", "Helvetica", sans-serif;
font-size: 8pt;
line-height: 1.4;
	}

	input[type=text] {
		width: 100%;
	}

	input[type=text].art, select.art {
    width: 200px;
  }

	textarea {
		width: 100%;
		height: 250px;
	}
</style>

			[MESSAGE]
			<form action="" method="POST" name="korrespondenzForm">
				<table border="0" width="460" id="korrForm">
					<tr><td width="100">Von:</td><td><input type="text" name="von" value="[VON]"></td><td width="20px"></td><td>Datum:</td><td><input type="text" name="datum" value="[DATUM]"></td></tr>
					<tr><td>Firma:</td><td colspan="4"><input type="text" name="firma" value="[FIRMA]"></td><tr>
					<!--<tr><td>Ansprechpartner:</td><td colspan="4">[ANSPRECHPARTNERAUTOSTART]<input type="text" id="ansprechpartner" name="ansprechpartner" value="[ANSPRECHPARTNER]">[ANSPRECHPARTNERAUTOEND]</td></tr>-->
					<tr><td>&nbsp;</td><td colspan="4"></td></tr>

					<tr><td>An / Firma:</td><td colspan="4">[ANSTART]<input type="text" name="an" id="an" value="[AN]" style="width: 100%">[ANDEND]</td></tr>
					<tr><td>E-Mail:</td><td colspan="4"><input type="text" name="email_an" id="an" value="[EMAILAN]" style="width: 100%"></td></tr>

					<!--<tr><td>Firma:</td><td colspan="4"><input type="text" name="firma_an" value="[FIRMAAN]" style="width: 100%"></td></tr>-->
					<tr><td>Stra&szlig;e / Nr.:</td><td colspan="4"><input type="text" name="adresse" value="[ADRESSE]"></td></tr>
					<tr><td>Plz:</td><td><input type="text" name="plz" value="[PLZ]"></td><td></td><td>Ort:</td><td><input type="text" name="ort" value="[ORT]"></td></tr>
					<tr><td>Land:</td><td><input type="text" name="land" value="[LAND]"></td></tr>

					<tr><td>&nbsp;</td><td colspan="4"></td></tr>
					<tr><td>Betreff:</td><td colspan="4"><input type="text" name="betreff" value="[BETREFF]"></td></tr>
					<tr><td valign="top">Text:</td><td colspan="4"><textarea name="content">[CONTENT]</textarea></td></tr>
					<!--<tr><td>Signatur:</td><td><input type="radio" name="signatur" value="1" [SIGNATURYES]><label>Ja</label><input type="radio" name="signatur" value="0" [SIGNATURNO]><label>Nein</label></td></tr>-->
					<tr><td valign="top">Senden:</td>
							<td colspan="4">
								<table>
									<tr><td><input type="radio" name="art" value="email" [ARTEMAIL]></td><td>per E-Mail</td><td><select name="email" class="art">[EMAILSELECT]</td></tr>
									<tr><td><input type="radio" name="art" value="mail" [ARTMAIL]></td><td>manuell per Brief</td><td><select name="mail" class="art">[DRUCKERSELECT]</select></td></tr>
									<tr><td><input type="radio" name="art" value="fax" [ARTFAX]></td><td>manuell per Fax</td><td><select name="fax" class="art">[FAXSELECT]</select></td></tr>
								</table>
							</td>
					</tr>
					<tr><td colspan="5">&nbsp;</td></tr>
					<tr><td colspan="5" align="right">
								<input type="submit" name="pdf" value="PDF">
								<input type="submit" name="save" value="Speichern" onclick="window.opener.location.reload(true);">
								<input type="submit" name="send" value="Senden" onclick="window.opener.location.reload(true);">
							</td>
					</tr>
				</table>
				<input type="hidden" name="prefill" value="[PREFILL]">
			</form>

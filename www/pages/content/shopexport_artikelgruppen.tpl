<div id="tabs">
    <ul>
        <li><a href="#tabs-1">&Uuml;bersicht</a></li>
        <li><a href="#tabs-2">Artikelgruppe anlegen</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->

<div id="tabs-1">
	<table width="100%" style="background-color: #fff; border: solid 1px #000;" align="center">
		<tr><td align="center"><br><b style="font-size: 14pt">Artikelgruppen</b><br><br>Einträge können angelegt, bearbeitet und gelöscht werden.<br><br></td></tr>
	</table>
	[TABLE]

</div>

<div id="tabs-2">
	<table width="100%" style="background-color: #fff; border: solid 1px #000;" align="center">
		<tr><td align="center"><br><b style="font-size: 14pt">Artikelgruppen:</b><br><br>Artikelgruppen f&uuml;r die Online-Shops.<br><br></td></tr>
	</table>
	[MESSAGE]
	<form action="" method="post" name="eprooform">
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
  	<tr valign="top" colspan="3">
    	<td>
				<fieldset><legend>{|Einstellung|}</legend>
    			<table width="100%">
          	<tr><td width="150">Name DE:</td><td><input type="text" name="bezeichnung" value="[BEZEICHNUNG]" size="40"></td><td></tr>
          	<tr><td width="150">Name EN:</td><td><input type="text" name="bezeichnung_en" value="[BEZEICHNUNGEN]" size="40"></td><td></tr>
          	<tr><td width="150">Beschreibung DE:</td><td><textarea name="beschreibung_de" id="beschreibung_de" rows="5" cols="40">[BESCHREIBUNG_DE]</textarea></td><td></tr>
          	<tr><td width="150">Beschreibung EN:</td><td><textarea name="beschreibung_en" id="beschreibung_en" rows="5" cols="40">[BESCHREIBUNG_EN]</textarea></td><td></tr>
          	<tr><td width="150">aktiv:</td><td><input type="checkbox" name="aktiv" value="1" [AKTIVCHECKED]></td><td></tr>
					</table>
				</fieldset>
			</td>
		</tr>
    <tr class="klein"><td align="right" colspan="3" class="orange2"><input type="submit" value="Speichern" name="anlegen"/></tr>
  </table>
	</form>
</div>
</div>


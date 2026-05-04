<form action="" method="post" name="eprooform">

	<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
		<tbody>
		<tr valign="top" colspan="3">
			<td>
				<fieldset>
					<legend>{|Allgemein|}</legend>
					<table border="0" width="100%">
						<tbody>
						<tr valign="top">
							<td width="200">
								<label for="kurzbezeichnung">{|Bezeichnung|}:</label>
							</td>
							<td>
								<input name="kurzbezeichnung" id="kurzbezeichnung" type="text" size="80" value="[KURZBEZEICHNUNG]"><br><i>{|Nur Buchstaben
									und Ziffern. Keine Sonder- und Leerzeichen! z.B. HL001, HL002|}</i><br><br>
							</td>
						</tr>
						<tr>
							<td>
								<label for="adresse">{|Adresse|}:</label>
							</td>
							<td>
								<input type="text" name="adresse" id="adresse" size="40" value="[ADRESSE]">&nbsp;<i>{|Optional Angabe
									Adresse für Kommissionslager oder externe Produktion|}</i>
							</td>
						</tr>
						</tbody>
					</table>
				</fieldset>
				<fieldset>
					<legend>{|Lagertyp|}</legend>
					<table border="0" width="100%">
						<tbody>
						<tr>
							<td width="200"><label for="autolagersperre">{|Nachschublager|}:</label></td>
							<td><input id="autolagersperre" name="autolagersperre" type="checkbox" value="1" [AUTOLAGERSPERRE]> <i>Im verfügbaren Bestand, wird nicht f&uuml;r Auslagerung im Auto-Versand verwendet. </i></td>
						</tr>
						<tr>
        					<td><label for="kommissionierlager">{|Kommissionierlager|}:</label></td>
							<td><input name="kommissionierlager" id="kommissionierlager" type="checkbox" value="1" [KOMMISSIONIERLAGER]> <i>Nicht im verfügbaren Bestand, wird nicht f&uuml;r Auslagerung im Auto-Versand verwendet. (F&uuml;r den Versand vorbereitete Ware)</i></td>
						</tr>
						<tr>
							<td><label for="sperrlager">{|Sperrlager|}:</label></td>
							<td><input name="sperrlager" id="sperrlager" type="checkbox" value="1" [SPERRLAGER]><i>&nbsp;Nicht im verfügbaren Bestand, wird nicht f&uuml;r Auslagerung im Auto-Versand verwendet. (I.d.R. wertlos)</i></td>
						</tr>
<!--						<tr>
    						<td><label for="allowproduction">{|Produktionslager|}:</label></td>
							<td><input name="allowproduction" id="allowproduction" type="checkbox" value="1" [ALLOWPRODUCTION]></td>
						</tr>
						<tr class="trsperrlager">
							<td><label for="poslager">{|POS Lager|}:</label></td>
							<td><input name="poslager" id="poslager" type="checkbox" value="1" [POSLAGER]> <i>{|Die POS darf den Bestand f&uuml;r
									diese Lager ver&auml;ndern.|}</i></td>
						</tr>--!>
						</tbody>
					</table>
				</fieldset>
				<fieldset>
					<legend>{|Lagereigenschaften|}</legend>
					<table width="100%">
						<tbody>
						<tr>
							<td width="200"><label for="laenge">{|Abmessungen|}:</label></td>
							<td><input type="text" id="laenge" name="laenge" size="10" value="[LAENGE]" />({|L&auml;nge|})&nbsp;
								<input type="text" name="breite" size="10" value="[BREITE]" />({|Breite|})&nbsp;
								<input type="text" name="hoehe" size="10" value="[HOEHE]" />({|H&ouml;he|})
							</td>
						</tr>
						<tr>
							<td><label for="regalart">{|Regalart|}:</label></td>
							<td nowrap>
								<select name="regalart" id="regalart">
									<option value="">[REGALART]
								</select>
							  <label for="rownumber">{|Sortierung|}:</label>&nbsp;<input type="text" id="rownumber" name="rownumber" size="10" value="[ROWNUMBER]"></td>
						</tr>
						<tr>
							<td><label for="abckategorie">{|Kategorie|}:</label></td>
							<td>
								<select name="abckategorie" id="abckategorie">
									<option value="">[ABCKATEGORIE]
								</select>
							</td>
						</tr>
						</tbody>
					</table>
				</fieldset>
			</td>
		</tr>

		<tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
			<td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2"
					class="orange2">
				<input type="submit" name="speichern" value="Speichern" /> [ABBRECHEN]
			</td>
		</tr>
		</tbody>
	</table>
</form>

	<fieldset>
		<legend>{|Projekt anlegen|}</legend>
		<table class="mkTableFormular">
			<tr>
				<td><label for="name">{|Bezeichnung|}:</label></td>
				<td><input type="text" size="52" id="name" name="name" value="[NAME]"></td>
				<td><i>(Pflichtfeld)</i></td>
			</tr>
			<tr>
				<td><label for="abkuerzung">{|Kennung|}:</label></td>
				<td>
					<input type="radio" name="typ" id="manuell" value="manuell" [MANUELL] />&nbsp;
					<input type="text" size="20"
							name="abkuerzung"
							id="abkuerzung"
							value="[ABKUERZUNG]" />&nbsp;<label for="manuell"><i>(Manuell vergeben)&nbsp;(Pflichtfeld)</i></label>
				</td>
				<td>
			</tr>
			<tr>
				<td></td>
				<td><input type="radio" name="typ" id="kundennummer" value="kundennummer" [KUNDENNUMMER] />&nbsp;
					<label for="kundennummer">{|Kundennummer + Fortlaufende Nummer|}</label>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="radio" name="typ" id="fortlaufend" value="fortlaufend" [FORTLAUFEND] />&nbsp;
					<label for="fortlaufend">{|fortlaufende Projekt Nr.|}</label>
				</td>
			</tr>
			<tr>
				<td><label for="kunde">{|Kunde|}:</label></td>
				<td><input type="text" size="45" name="kunde" id="kunde" value="[KUNDE]">&nbsp;</td>
				<td><i>(optional)</i></td>
			</tr>
			<tr>
				<td><label for="verantwortlicher">{|Verantwortlicher|}:</label></td>
				<td><input type="text" size="45" name="verantwortlicher" id="verantwortlicher" value="[VERANTWORTLICHER]"></td>
				<td><i>(optional)</i></td>
			</tr>
			<tr>
				<td><label for="beschreibung">{|Beschreibung|}:</label></td>
				<td><textarea rows="5" cols="50" id="beschreibung" name="beschreibung">[BESCHREIBUNG]</textarea></td>
			</tr>
			<tr>
				<td><label for="status">{|Status|}:</label></td>
				<td><select id="status" name="status">[STATUSSEL]</select></td>
			</tr>
		</table>
	</fieldset>



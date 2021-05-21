	<fieldset>
		<legend>{|Projekt anlegen|}</legend>
		<table class="mkTableFormular">
			<tr>
				<td><label for="projecttitle">{|Bezeichnung|}:</label></td>
				<td><input type="text" size="52" id="projecttitle" name="projecttitle" value=""></td>
				<td><i>(Pflichtfeld)</i></td>
			</tr>
			<tr>
				<td><label for="projectshortcode">{|Kennung|}:</label></td>
				<td>
					<input type="text" size="20"
							name="projectshortcode"
							id="projectshortcode"
							value="" />
				</td>
				<td>
			</tr>
			<tr>
				<td><label for="projectcustomer">{|Kunde|}:</label></td>
				<td><input type="text" size="45" name="projectcustomer" id="projectcustomer" />&nbsp;</td>
				<td><i>(optional)</i></td>
			</tr>
			<tr>
				<td><label for="projectleader">{|Verantwortlicher|}:</label></td>
				<td><input type="text" size="45" name="projectleader" id="projectleader" value="[PROJECTLEADER]"></td>
				<td><i>(optional)</i></td>
			</tr>
			<tr>
				<td><label for="projectdescription">{|Beschreibung|}:</label></td>
				<td><textarea rows="5" cols="50" id="projectdescription" name="projectdescription"></textarea></td>
			</tr>
			<tr>
				<td><label for="projectstatus">{|Status|}:</label></td>
				<td><select id="projectstatus" name="projectstatus">[STATUSSEL]</select></td>
			</tr>
			<tr><td><label for="projectcolor">{|Farbe|}:</label></td><td><input type="text" size="15" id="projectcolor" name="projectcolor" /></td></tr>
		</table>
	</fieldset>



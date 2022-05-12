<form action="" method="post">
	<fieldset>
		<legend>{|Neue Pinnwand|}</legend>
		<table>
			<tr>
				<td width="100">{|Name|}:</td>
				<td><input type="text" size="40" name="name"></td>
			</tr>
			<tr>
				<td>{|Freigeben f&uuml;r|}:</td>
				<td>
					<select name="personen[]" size="10" style="width:250px" multiple="">
						[PERSONEN]
					</select>
					<br><br>
					<span style="font-size: 10px; color: #A0A0A0">
						{|Strg-Taste gedrückt halten um mehrere Personen auszuw&auml;hlen.|}
					</span>
				</td>
			</tr>
			<tr height="50">
				<td></td>
				<td>
					<input type="submit" value="{|Neue Pinnwand anlegen|}" name="submit">
				</td>
			</tr>
		</table>
	</fieldset>
</form>

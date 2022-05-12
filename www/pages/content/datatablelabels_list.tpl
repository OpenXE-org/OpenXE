<div id="tabs">
	<ul>
		<li><a href="#tabs-1">[TABTEXT]</a></li>
	</ul>

	<div id="tabs-1">
		<form method="post">
			[MESSAGE]
		</form>

		[TAB1]
		[TAB1NEXT]
	</div>
</div>

<div id="datatablelabels_edit" class="hide" title="Bearbeiten">
	<form method="post">
		<input type="hidden" id="datatablelabel_id">
		<fieldset>
			<legend>{|Labeltyp|}</legend>
			<table>
				<tr>
					<td width="100">{|Bezeichnung|}:</td>
					<td><input type="text" name="datatablelabel_title" id="datatablelabel_title"></td>
				</tr>
				<tr>
					<td width="100">{|Kennung|}:</td>
					<td>
						<input type="text" name="datatablelabel_type" id="datatablelabel_type" maxlength="24">
						<small>Nur Kleinbuchstaben und Zahlen [a-z, 0-9]</small>
					</td>
				</tr>
				<tr>
					<td width="100">{|Gruppe|}:</td>
					<td>
						<select name="datatablelabel_group" id="datatablelabel_group">
							[DATATABLE_GROUP_OPTIONS]
						</select>
					</td>
				</tr>
				<tr>
					<td>{|Farbe|}:</td>
					<td><input type="text" name="datatablelabel_hexcolor" id="datatablelabel_hexcolor"></td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>

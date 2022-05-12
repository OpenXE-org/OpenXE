<div id="tabs">
	<ul>
		<li><a href="#tabs-1">[TABTEXT]</a></li>
	</ul>

	<div id="tabs-1">
		<form method="post">
			[MESSAGE]
		</form>
		<div class="row">
			<div class="row-height">
				<div class="col-xs-12 col-md-10 col-md-height">
					<div class="inside-white inside-full-height">
						[TAB1]
					</div>
				</div>
				<div class="col-xs-12 col-md-2 col-md-height">
					<div class="inside inside-full-height">
						<fieldset>
							<legend>{|Aktionen|}</legend>
							<input type="button" class="btnGreenNew" name="datatablelabel_automaticlabelnew" value="&#10010; Neuer Eintrag" onclick="DataTableLabelsAutomaticLabelsUi.createItem();">
						</fieldset>
					</div>
				</div>
			</div>
		</div>

		[TAB1NEXT]
	</div>
</div>

<div id="datatablelabels_automaticlabelsedit" class="hide" title="Bearbeiten">
	<form method="post">
		<input type="hidden" id="datatablelabel_automaticlabelid">
		<fieldset>
			<legend>{|Automatisches Label|}</legend>
			<table>
				<tr>
					<td width="90">{|Label|}:</td>
					<td><input type="text" name="datatablelabel_automaticlabelname" id="datatablelabel_automaticlabelname" size="40"></td>
				</tr>
				<tr>
					<td width="90">{|Aktion|}:</td>
					<td><select name="datatablelabel_automaticlabelaction" id="datatablelabel_automaticlabelaction">
								[AUTOMATICLABELACTION]
							</select>
					</td>
				</tr>
				<tr>
					<td>{|Auswahl|}:</td>
					<td><select name="datatablelabel_automaticlabelselection" id="datatablelabel_automaticlabelselection">
								[AUTOMATICLABELSELECTION]
							</select>
					</td>
				</tr>
				<tr>
					<td>{|Projekt|}:</td>
					<td><input type="text" name="datatablelabel_automaticlabelproject" id="datatablelabel_automaticlabelproject" size="40"></td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>

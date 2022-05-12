<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">[TABTEXT]</a></li>
	</ul>
	<!-- ende gehort zu tabview -->
	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]
		[TAB1]
		[TAB1NEXT]
		<div class="row">
			<div class="row-height">
				<div class="col-xs-12 col-md-6 col-md-height">
					<div class="inside inside-full-height">
						<fieldset>
							<legend>Für Beleg ausblenden</legend>
							<br />
							<table>
								<tr>
									<td width="120"><label for="lieferschein">Lieferschein:</label></td>
									<td><input type="checkbox" name="lieferschein" id="lieferschein" [LIEFERSCHEIN] /></td>
								</tr>
								<tr>
									<td><label for="produktion">Produktion:</label></td>
									<td><input type="checkbox" name="produktion" id="produktion" [PRODUKTION] /></td>
								</tr>
								<tr>
									<td><label for="bestellung">Bestellung:</label></td>
									<td><input type="checkbox" name="bestellung" id="bestellung" [BESTELLUNG] /></td>
								</tr>
							</table>
						</fieldset>
					</div>
				</div>
				<div class="col-xs-12 col-md-6 col-md-height">
					<div class="inside inside-full-height">
						<fieldset></fieldset>
					</div>
				</div>
			</div>
		</div>

	</div>

	<!-- tab view schließen -->
</div>

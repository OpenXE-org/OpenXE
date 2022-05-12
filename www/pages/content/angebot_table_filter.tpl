<fieldset data-filter="angebot" class="table_filter" style="display:none">
	<legend>{|Filter|}</legend>
	<table width="100%">
		<tr>
			<td colspan="2">
				<!-- <input type="text" name="suche" value="" style="width: 48.35%"> -->
			</td>
		</tr>
		<tr>
			<td width="50%" valign="top">
				<!-- Spalte1 -->
				<div class="table_filter_container table_filter_container_left">
					<table>
						<tr>
							<td width="150">Kundennummer:</td>
							<td><input type="text" name="kundennummer" value="" id="kundennummer"></td>
						</tr>
						<tr>
							<td>Name:</td>
							<td><input type="text" name="name" value=""></td>
						</tr>
						<tr>
							<td>Ansprechpartner:</td>
							<td><input type="text" name="ansprechpartner" value="" id=""></td>
						</tr>
						<tr>
							<td>Abteilung:</td>
							<td><input type="text" name="abteilung" value=""></td>
						</tr>
						<tr>
							<td>Straße/HausNr.:</td>
							<td><input type="text" name="strasse" value=""></td>
						</tr>
						<tr>
							<td>PLZ:</td>
							<td><input type="text" name="plz" value=""></td>
						</tr>
						<tr>
							<td>Ort:</td>
							<td><input type="text" name="ort" value=""></td>
						</tr>
						<tr>
							<td>Land:</td>
							<td>
								<select name="land">
									<option value="">Alle</option>
									[LAENDER]
								</select>
							</td>
						</tr>
						<tr>
							<td>UST-ID:</td>
							<td><input type="text" name="ustid" value=""></td>
						</tr>

						<tr>
							<td>Telefon:</td>
							<td><input type="text" name="telefon" value=""></td>
						</tr>
						<tr>
							<td>E-Mail:</td>
							<td><input type="text" name="email" value=""></td>
						</tr>
					</table>
				</div>
				<!--
				<div class="table_filter_container">
					<table width="100%">
						<tr>
							<td><input type="checkbox" name="durchsuchenRechnung" value=""></td>
							<td>Rechnungsadresse mit durchsuchen</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="durchsuchenLieferadresse" value=""></td>
							<td>Lieferadresse mit durchsuchen</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="durchsuchenAnsprechpartner" value=""></td>
							<td>Ansprechpartner mit durchsuchen</td>
						</tr>
					</table>
				</div>
				-->

			</td>
			<td valign="top">
				<!-- Spalte 2 -->

				<div class="table_filter_container table_filter_container_right">
					<table width="100%">
						<tr>
							<td width="150">Datum:</td>
							<td>
								<input type="text" name="datumVon" id="datumVon" class="smallInput"> bis <input type="text" name="datumBis" id="datumBis" class="smallInput">
							</td>
						</tr>
						<tr>
							<td>Betrag:</td>
							<td>
								<input type="text" name="betragVon" class="smallInput"> bis <input type="text" name="betragBis" class="smallInput">
							</td>
						</tr>
					</table>
				</div>

				<div class="table_filter_container table_filter_container_right">
					<table width="100%">
						<tr>
							<td width="150">Projekt:</td>
							<td><input type="text" name="projekt" value="" id="projekt"></td>
						</tr>
						<tr>
							<td>Belegnummer:</td>
							<td><input type="text" name="belegnummer" value="" id="angebotsnummer"></td>
						</tr>
						<tr>
							<td>Freitext:</td>
							<td><input type="text" name="freitext" value=""></td>
						</tr>
						<tr>
							<td>Interne Bemerkung:</td>
							<td><input type="text" name="internebemerkung" value=""></td>
						</tr>
						<tr>
							<td>Aktionscodes:</td>
							<td><input type="text" name="aktion" value=""></td>
						</tr>
						<tr>
							<td>Zahlungsweise:</td>
							<td>
								<select name="zahlungsweise">
									<option value="">Alle</option>
									[ZAHLUNGSWEISEN]
								</select>
							</td>
						</tr>
						<tr>
							<td>Status:</td>
							<td>
								<select name="status">
									<option value="">Alle</option>
									[STATUS]
								</select>
							</td>
						</tr>
						<tr>
							<td>Versandart:</td>
							<td>
								<select name="versandart">
									<option value="">Alle</option>
									[VERSANDARTEN]
								</select>
							</td>
						</tr>
						<tr>
							<td width="150">Artikel:</td>
							<td><input type="text" name="artikel" value="" id="artikel"></td>
						</tr>
					</table>
				</div>

			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<button onclick="table_filter.clearParameters('angebot');">Alles zurücksetzen</button>
				<button onclick="table_filter.setParameters('angebot');">Filter anwenden</button>
			</td>
		</tr>
	</table>
</fieldset>

<style>
.table_filter_container {
	border: 1px solid #d7d7d7;
	margin: 0 5px 10px 0;
	padding: 5px;
}

.table_filter_container_right {
	margin: 0 0 10px 5px;
}
</style>












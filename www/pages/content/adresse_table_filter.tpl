<fieldset data-filter="adresse" class="table_filter" style="display:none">
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
							<td>{|Name|}:</td>
							<td><input type="text" name="name" id="name" value=""></td>
						</tr>
						<tr>
							<td>{|Ansprechpartner|}:</td>
							<td><input type="text" name="ansprechpartner" value="" id=""></td>
						</tr>
						<tr>
							<td>{|Adresszusatz|}:</td>
							<td><input type="text" name="adresszusatz" value="" id=""></td>
						</tr>
	
						<tr>
							<td>{|Abteilung|}:</td>
							<td><input type="text" name="abteilung" value=""></td>
						</tr>
						<tr>
							<td>{|Straße/HausNr.|}:</td>
							<td><input type="text" name="strasse" value=""></td>
						</tr>
						<tr>
							<td>{|PLZ|}:</td>
							<td><input type="text" name="plz" value=""></td>
						</tr>
						<tr>
							<td>{|Ort|}:</td>
							<td><input type="text" name="ort" value=""></td>
						</tr>
						<tr>
							<td>{|Land|}:</td>
							<td>
								<select name="land">
									<option value="">{|Alle|}</option>
									[LAENDER]
								</select>
							</td>
						</tr>
						<tr>
							<td>{|UST-ID|}:</td>
							<td><input type="text" name="ustid" value=""></td>
						</tr>

						<tr>
							<td>{|Telefon|}:</td>
							<td><input type="text" name="telefon" value=""></td>
						</tr>
						<tr>
							<td>{|E-Mail|}:</td>
							<td><input type="text" name="email" value=""></td>
						</tr>
            <tr>
              <td>{|Kunde hat Abo|}</td><td><input type="checkbox" name="abo" value="1" /> {|Marketingsperre|}: <input type="checkbox" name="marketingsperre" value="1" /> {|Lead|}: <input type="checkbox" name="lead" value="1" />
              </td>
            </tr>
					</table>
				</div>

				<!--
				<div class="table_filter_container">
					<table width="100%">
						<tr>
							<td width="30"><input type="checkbox" name="durchsuchenAnsprechpartner" value="ON"></td>
							<td>Ansprechpartner mit durchsuchen</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="durchsuchenLieferadresse" value="ON"></td>
							<td>Lieferadresse mit durchsuchen</td>
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
							<td width="150">{|Kundennummer|}:</td>
							<td><input type="text" name="kundennummer" value="" id="kundennummer"></td>
						</tr>
						<tr>
							<td width="150">{|Lieferantennummer|}:</td>
							<td>
								<input type="text" name="lieferantennummer" class="" value=""> 
							</td>
						</tr>
						<tr>
							<td>{|Mitarbeiternummer|}:</td>
							<td><input type="text" name="mitarbeiternummer" value=""></td>
						</tr>
						[VERBANDSNUMMER]
					</table>
				</div>
				<div class="table_filter_container table_filter_container_right">
					<table width="100%">
						<tr>
							<td width="150">{|Vertrieb|}:</td>
							<td><input type="text" name="vertrieb" value="" id="vertrieb"></td>
						</tr>
						<tr>
							<td>{|Innendienst|}:</td>
							<td><input type="text" name="innendienst" id="innendienst" value=""></td>
						</tr>
					</table>
				</div>
				<div class="table_filter_container table_filter_container_right">
					<table width="100%">
						<tr>
							<td width="150">{|Projekt|}:</td>
							<td colspan="3"><input type="text" name="projekt" value="" id="projekt"></td>
						</tr>
						<tr>
							<td>{|Sonstiges|}:</td>
							<td colspan="3"><input type="text" name="sonstiges" value=""></td>
						</tr>
						<tr>
							<td>{|Info für Auftragserfassung|}:</td>
							<td colspan="3"><input type="text" name="infoAuftragserfassung" value=""></td>
						</tr>
						<tr>
							<td>{|Zahlungsweise|}:</td>
							<td colspan="3">
								<select name="zahlungsweise">
									<option value="">{|Alle|}</option>
									[ZAHLUNGSWEISEN]
								</select>
							</td>
						</tr>
						<tr>
							<td>{|Rollen|}:</td>
							<td>
								<select name="rolle">
									<option value="">{|Alle|}</option>
									[ROLLEN]
								</select>
							</td>
              <td>Gruppen:</td>
              <td>
								<select name="gruppe">
									<option value="">{|Alle|}</option>
									[GRUPPEN]
								</select>
              </td>
						</tr>
					</table>
				</div>

			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<button onclick="table_filter.clearParameters('adresse');">{|Alles zur&uuml;cksetzen|}</button>
				<button onclick="table_filter.setParameters('adresse');">{|Filter anwenden|}</button>
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


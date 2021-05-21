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
		<div class="row">
			<div class="row-height">
				<div class="col-xs-12 col-md-4 col-md-height">
					<div class="inside inside-full-height">
						<form action="" method="post" id="">
							<fieldset><legend>{|SQL-Statements|}</legend>
								<table>
									<tr>
										<td>
											<label for="locationsql">{|Location-SQL|}:</label>
										</td>
										<td>
											<textarea name="locationsql" id="locationsql" style="min-height: 250px;min-width: 400px">[LOCATIONSQL]</textarea>
										</td>
										<td></td>
									</tr>
									<tr>
										<td>
											<label for="groupsql">{|Group-SQL|}:</label>
										</td>
										<td>
											<textarea name="groupsql" id="groupsql" style="min-height: 250px;min-width: 400px">[GROUPSQL]</textarea>
										</td>
										<td></td>
									</tr>
									<tr>
										<td>
											<label for="suppliersql">{|Supplier-SQL|}:</label>
										</td>
										<td>
											<textarea name="suppliersql" id="suppliersql" style="min-height: 250px;min-width: 400px">[SUPPLIERSQL]</textarea>
										</td>
										<td></td>
									</tr>
									<tr>
										<td>
											<label for="mastersql">{|Master-SQL|}:</label></td>
										<td>
											<textarea name="mastersql" id="mastersql" style="min-height: 250px;min-width: 400px">[MASTERSQL]</textarea>
										</td>
										<td></td>
									</tr>
									<tr>
										<td>
											<label for="stocksql">{|Stock-SQL|}:</label>
										</td>
										<td>
											<textarea name="stocksql" id="stocksql" style="min-height: 250px;min-width: 400px">[STOCKSQL]</textarea>
										</td>
										<td>
											Platzhalter<br>
											{lager_beistellung}: Lager für Intern (Beistellungen) <br>
										</td>
										<td></td>
									</tr>
									<tr>

									</tr>
									<tr>
										<td>
											<label for="salessql">{|Sales-SQL|}:</label>
										</td>
										<td>
											<textarea name="salessql" id="salessql" style="min-height: 250px;min-width: 400px">[SALESSQL]</textarea>
										</td>
										<td>
											Platzhalter:<br>
                                            {lager_beistellung}: Lager für Intern (Beistellungen) <br>
										</td>
									</tr>
									<tr>
										<td>
											<label for="posql">{|Po-SQL|}:</label>
										</td>
										<td>
											<textarea name="posql" id="posql" style="min-height: 250px;min-width: 400px">[POSSQL]</textarea>
										</td>
										<td>
											Platzhalter:<br>
                                            {lager_beistellung}: Lager für Intern (Beistellungen) <br>
										</td>
									</tr>
									<tr>
										<td>
											<label for="cosql">{|Co-SQL|}:</label>
										</td>
										<td>
											<textarea name="cosql" id="cosql" style="min-height: 250px;min-width: 400px">[COSSQL]</textarea>
										</td>
										<td>
											Platzhalter:<br>
                                            {lager_beistellung}: Lager für Intern (Beistellungen) <br>
										</td>
									</tr>
									<tr>
										<td>
											<label for="pohistsql">{|Pohist-SQL|}:</label>
										</td>
										<td>
											<textarea name="pohistsql" id="pohistsql" style="min-height: 250px;min-width: 400px">[POHISTSQL]</textarea>
										</td>
										<td>
											Platzhalter:<br>
                                            {lager_beistellung}: Lager für Intern (Beistellungen) <br>
										</td>
									</tr>
									<tr>
										<td>
											<label for="bomsql">{|Bom-SQL|}:</label>
										</td>
										<td>
											<textarea name="bomsql" id="bomsql" style="min-height: 250px;min-width: 400px">[BOMSQL]</textarea>
										</td>
										<td>
											Platzhalter:<br>
                                            {lager_beistellung}: Lager für Intern (Beistellungen) <br>
                                            {lager}: Lager für Extern <br>
										</td>
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="submit" value="Speichern" name="speichern">
										</td>
										<td></td>
									</tr>
								</table>
							</fieldset>

						</form>
					</div>
				</div>
			</div>
		</div>
		[TAB1NEXT]
	</div>

	<!-- tab view schließen -->
</div>
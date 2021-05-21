<div id="tabs" class="report">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]

			<form action="" method="post" name="reportEditForm" enctype="multipart/form-data" id="report_edit_form">
				[FORMMESSAGE]
				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-sm-12 col-sm-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>{|Einstellung|}</legend>
									<table class="mkTableFormular" width="95%" style="box-sizing: border-box;">
										<tr>
											<td class="input-label"><label for="reportEditName">{|Name|}:</label></td>
											<td><input type="text" value="[NAME]" name="name" id="reportEditName" [READONLY]></td>
											<td class="input-label"><label for="reportEditProject">{|Projekt|}:</label></td>
											<td><input type="text" name="project" value="[PROJECT]" id="reportEditProject" [READONLY]></td>
										</tr>
										<tr class="textarea-label">
											<td class="input-label"><label for="reportEditCategory">{|Kategorie|}:</label></td>
											<td><input type="text" value="[CATEGORY]" name="category" id="reportEditCategory" [READONLY]></td>
											<td><label for="reportEditName">{|Variablen|}:</label></td>
											<td rowspan="2">
												<div class="button-bar">
													<input class="button-add" type="button" id="btnAddParam" name="add" value="{|&#10010; Neue Variable|}" [DISABLED]>
												</div>
												<div id="paramListing" class="button-bar">
													[PARAMTABLE]
												</div>
											</td>
										</tr>
										<tr>
											<td><label for="reportEditDescription">{|Beschreibung|}:</label></td>
											<td><textarea rows="6" cols="85" name="description" id="reportEditDescription" [READONLY]>[DESCRIPTION]</textarea></td>
										</tr>
										<tr>
											<td><label for="btnSubmitImport">{|Aus Datei laden|}:</label></td>
											<td><input type="file" name="submit_import" id="btnSubmitImport" [DISABLED]/></td>
										</tr>
									</table>
								</fieldset>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-sm-10 col-sm-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>{|Struktur|}</legend>
										<table class="mkTableFormular" width="100%">
											<tr class="textarea-label">
												<td class="input-label"><label for="reportEditQuery">{|SQL Statement|}:</label></td>
												<td>
													<textarea rows="8" name="sql_query" id="reportEditQuery" [READONLY]>[SQLQUERY]</textarea>
													<a class="playbutton" id="structureRun" href="#">
														<img src="./themes/[THEME]/images/play.png" alt="run query">
													</a>
												</td>
											</tr>
											<tr>
												<td></td>
												<td><small><span id="dirtyWarning" for="reportEditQuery" dirty="false">{|Achtung: ungespeicherte Änderungen|}!</span></small></td>
											</tr>
											<tr class="textarea-label">
												<td><label for="reportEditResult">{|Ergebnis|}:</label></td>
												<td><textarea rows="5" id="reportEditResult" readonly>[RESULT]</textarea></td>
											</tr>
										</table>
								</fieldset>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-sm-12 col-sm-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>{|Spalten|}</legend>
												[COLUMNTABLE]
								</fieldset>
							</div>
						</div>
						<div class="col-xs-12 col-md-2 col-md-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>{|Aktionen|}</legend>
									<input class="button button-block button-add" type="button" id="btnAddColumn" name="add" value="{|&#10010; Neue Spalten|}" [DISABLED]>
									<input class="button button-block button-add" type="button" id="btnAutoCreateColumns" name="autocreate" value="{|Spalten erzeugen|}" [DISABLED]>
									<input class="button button-block button-add" type="button" id="btnDeleteAllColumns" name="alldelete" value="{|Alle Spalten löschen|}" [DISABLED]>

									<!--<input type="button" class="button-add" id="btnAddColumn" name="add" value="{|+ Neu|}" [DISABLED]>
											<input type="button" class="button-add" id="btnAutoCreateColumns" name="autocreate" value="{|Spalten erzeugen|}" [DISABLED]>
											<input type="button" class="button-add" id="btnDeleteAllColumns" name="alldelete" value="{|Alle löschen|}" [DISABLED]>-->

								</fieldset>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-md-2 col-md-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>{|CSV Eigenschaften|}</legend>
									<table class="mkTableFormular" width="100%">
										<tr class="textarea-label">
											<td class="input-label"><label for="report_csv_delimiter">{|CSV Trennzeichen|}:</label></td>
											<td>
												<select name="csv_delimiter" id="report_csv_delimiter" >
													[CSV_DELIMITER_OPTIONS]
												</select>
											</td>
										</tr>
										<tr class="textarea-label">
											<td class="input-label"><label for="report_csv_enclosure">{|Feldtrennzeichen|}:</label></td>
											<td>
												<select name="csv_enclosure" id="report_csv_enclosure" >
													[CSV_ENCLOSURE_OPTIONS]
												</select>
											</td>
										</tr>
									</table>
								</fieldset>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-md-2 col-md-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>{|Notiz|}</legend>
									<table class="mkTableFormular" width="100%">
										<tr class="textarea-label">
											<td class="input-label"><label for="reportEditRemark">{|Interne Bemerkung|}:</label></td>
											<td>
												<textarea rows="3" name="remark" id="reportEditRemark" [READONLY]>[REMARK]</textarea>
											</td>
										</tr>
									</table>
								</fieldset>
							</div>

							<!--<input type="hidden" id="reportEditId" name="reportId" value="[ID]" data-form-id="[ID]"/>
							<a class="button" href="?module=report&action=download&format=json&id=%value%">{|Bericht Herunterladen|}</a>
							<input class="rightside" type="submit" name="submit" id="btnSave" value="Speichern" [DISABLED]/>-->

						</div>
					</div>
				</div>

				<input type="hidden" id="reportEditId" name="reportId" value="[ID]" data-form-id="[ID]"/>


				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-md-6 col-md-height">
							<div class="button-bar">
								[JSON_EXPORT_BUTTON]
							</div>
						</div>

						<div class="col-xs-12 col-md-6 col-md-height">
							<div class="button-bar rightside">
								<input type="submit" name="submit" id="btnSave" value="Speichern" [DISABLED]/>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
		[TAB1NEXT]
	</div>


<div id="editParamDialog" style="display:none;" title="Parameter bearbeiten">
	<form method="post">
		<input type="hidden" id="editParamId">
		<fieldset>

			<div class="dialog-message" id="editParamMessage"></div>

			<table class="mkTableFormular">
				<tr>
					<td><label for="editParamVarname">{|Variablenname|}:</label></td>
					<td><input type="text" id="editParamVarname"><small><i>Pflichtfeld</i></small></td>
				</tr>
				<tr>
					<td><label for="editParamValue">{|Standardwert|}:</label></td>
					<td><input type="text" id="editParamValue"><small><i>Pflichtfeld</i></small></td>
				</tr>
				<tr>
					<td><label for="editParamLabel">{|Dialogtext|}:</label></td>
					<td><input type="text" id="editParamLabel"></td>
				</tr>
				<tr>
					<td><label for="editParamDescription">{|Beschreibung|}:</label></td>
					<td><input type="text" id="editParamDescription"></td>
				</tr>
				<tr>
					<td><label for="editParamControl">{|Eingabe|}:</label></td>
					<td>
						<select id="editParamControl">
							<option value="text">Textfeld</option>
							<option value="combobox">Dropdown</option>
							<option value="date">Date-Picker</option>
							<option value="autocomplete_project">Auswahl Projekt</option>
							<option value="autocomplete_group">Auswahl Gruppe</option>
							<option value="autocomplete_address">Auswahl Adresse</option>
							<option value="autocomplete_article">Auswahl Artikel</option>
						</select>
				</tr>
				<tr>
					<td><label for="editParamOptions">{|Werteauswahl|}:</label></td>
					<td><input type="text" id="editParamOptions"></td>
				</tr>
				<tr>
					<td><label for="chkParamEditable">{|Manuell bearbeiten|}:</label></td>
					<td><input type="checkbox" id="chkParamEditable"></td>
				</tr>

			</table>
		</fieldset>
	</form>
</div>

<div id="editColumnDialog" style="display:none;" title="Spalte bearbeiten">
	<form method="post">
		<input type="hidden" id="editColId">
		<fieldset>
			<div class="dialog-message" id="editColMessage"></div>
			<table class="mkTableFormular">
				<tr>
					<td><label for="editColKey">{|Spaltenname SQL|}:</label></td>
					<td><input type="text" id="editColKey"><small><i>Pflichtfeld</i></small></td>
				</tr>
				<tr>
					<td><label for="editColTitle">{|Bezeichnung|}:</label></td>
					<td><input type="text" id="editColTitle"><small><i>Pflichtfeld</i></small></td>
				</tr>
				<tr>
					<td><label for="editColWidth">{|Spaltenbreite|}:</label></td>
					<td><input type="text" id="editColWidth"></td>
				</tr>
				<tr>
					<td><label for="editColAlignment">{|Ausrichtung|}:</label></td>
					<td><select id="editColAlignment">
								<option value="right">Rechts</option>
								<option value="center">Mitte</option>
								<option value="left">Links</option>
							</select>
					</td>
				</tr>
				<tr>
					<td><label for="editColSort">{|Sortierung|}:</label></td>
					<td><select id="editColSort">
							<option value="numeric">numerisch</option>
							<option value="alphabetic">alphabetisch</option>
						</select>
					</td>
				</tr>
				<tr>
					<td><label for="edit-col-format">{|Formatierung|}:</label></td>
					<td><select id="edit-col-format">
							<option value="">Keine</option>
							<option value="sum_money_de">Geldbetrag (DE)</option>
							<option value="sum_money_en">Geldbetrag (EN)</option>
							<option value="date_dmy">Datum (dd.mm.YYYY)</option>
							<option value="date_ymd">Datum (YYYY.mm.dd)</option>
							<option value="date_dmyhis">Datum und Uhrzeit (dd.mm.YYYY HH:ii:ss)</option>
							<option value="date_ymdhis">Datum und Uhrzeit (YYYY.mm.dd HH:ii:ss)</option>
							<option value="custom">{|Benutzerdefiniert|}</option>
						</select>
					</td>
				</tr>
				<tr id="edit-col-format-statement-row" class="report-edit-inactive">
					<td><label for="edit-col-format-statement"></label></td>
					<td><input type="text" id="edit-col-format-statement"/></td>
				</tr>
				<tr>
					<td><label for="chkColSum">{|Spalte summieren|}:</label></td>
					<td><input type="checkbox" id="chkColSum"></td>
				</tr>
				<tr>
					<td><label for="editColSequence">{|Reihenfolge|}:</label></td>
					<td><input type="text" id="editColSequence"></td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>

<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]
			<form action="" method="post" name="reportTransferForm">
				[FORMMESSAGE]
				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-md-10 col-md-height">
							<div class="inside inside-full-height">

								<fieldset>
									<legend>{|Freigaben|}</legend>

									[USERTABLE]
								</fieldset>
							</div>
						</div>
						<div class="col-xs-12 col-sm-2 col-sm-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>{|Aktionen|}</legend>
									<input class="button button-block button-add" type="button" id="shareUserAddBtn" value="{|&#10010; Neue Freigabe|}"/>
								</fieldset>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-sm-6 col-sm-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>1. {|Graph im Dashboard|}</legend>

									<table class="form-table">
										<tr>
											<td colspan="2"><label class="input-label" for="chkChartPublic">{|Öffentlich freigeben|}:</label></td>
											<td>
												<input type="checkbox" name="chart_public" id="chkChartPublic" [CHART_PUBLIC_CHECKED]/>
												<span>Überschreibt persönliche Freigaben</span>
											</td>
										</tr>

										<tr>
											<td colspan="2"><label class="input-label" for="chartType">{|Typ|}:</label></td>
											<td>
												<select name="chart_type" id="chartType">
													[CHART_TYPE_OPTIONS]
												</select>
											</td>
										</tr>

										<tr>
											<td colspan="2"><label class="input-label" for="chartAxisLabel">{|Y-Achsenbeschriftung|}:</label></td>
											<td><input type="text" name="chart_axislabel" id="chartAxisLabel" value="[CHART_AXISLABEL]"/></td>
										</tr>
										<tr>
											<td colspan="2"><label class="input-label" for="chartXColumn">{|Achsenspalte|}:</label></td>
											<td><input type="text" name="chart_x_column" id="chartXColumn" value="[CHART_X_COLUMN]"/></td>
										</tr>
										<tr>
											<td colspan="2"><label class="input-label" for="dataColumns">{|Daten-Spalte(n)|}:</label></td>
											<td><input type="text" name="data_columns" id="dataColumns" value="[DATA_COLUMNS]"/></td>
										</tr>
										<tr>
											<td colspan="2"><label class="input-label" for="chartGroupColumn">{|Gruppier-Spalte|}:</label></td>
											<td><input type="text" name="chart_group_column" id="chartGroupColumn" value="[CHART_GROUP_COLUMN]"/></td>
										</tr>
										<tr>
											<td colspan="2"><label class="input-label" for="chartDateFormat">{|Datumsformat|}:</label></td>
											<td>
												<select name="chart_dateformat" id="chartDateFormat">
													[CHART_DATEFORMAT_OPTIONS]
												</select>
											</td>
										</tr>

										<tr>
											<td><label class="input-label" for="chartIntervalValue">{|Datumsformat|}:</label></td>
											<td>alle</td>
											<td>
												<input type="text" name="chart_interval_value" id="chartIntervalValue" value="[CHART_INTERVAL_VALUE]"/>
												<select name="chart_interval_mode" id="chartIntervalMode">
													[CHART_INTERVAL_MODE_OPTIONS]
												</select>
											</td>
										</tr>
									</table>
									<br>
									<i>Die X-Achsenbeschriftung wird der Tabelle entnommen.</i><br>
									<i>Tipp: Verwenden Sie gut verständliche Aliase als Spaltenbeschriftung.</i>
								</fieldset>
							</div>
						</div>

						<div class="col-xs-12 col-sm-6 col-sm-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>2. {|Datei-Download im Dashboard|}</legend>
									<table class="form-table">
										<tr>
											<td><label class="input-label" for="chkFilePublic">{|Öffentlich freigeben|}:</label></td>
											<td colspan="3">
												<input type="checkbox" name="file_public" id="chkFilePublic" [FILE_PUBLIC_CHECKED]/>
												<span>Überschreibt persönliche Freigaben</span>
											</td>
										</tr>
										<tr>
											<td><label class="input-label">{|Formate|}:</label></td>
											<td>
												<input type="checkbox" name="file_pdf_enabled" id="chkPdfFileEnabled" [FILE_PDF_ENABLED_CHECKED]/>
												<span>PDF</span>
											</td>

											<td>
												<input type="checkbox" name="file_csv_enabled" id="chkCsvFileEnabled" [FILE_CSV_ENABLED_CHECKED]/>
												<span>CSV</span>
											</td>

											<!--<td>
												<input type="checkbox" name="file_xls_enabled" id="chkXLSFileEnabled" [FILE_XLS_ENABLED_CHECKED]/>
												<span>XLS</span>
											</td>-->
										</tr>
									</table>
								</fieldset>
							</div>
						</div>
					</div>
				</div>

				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-md-6 col-md-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>3. {|Aktionsmenü in Belegen|}</legend>
									<table class="form-table">
										<tr>
											<td><label class="input-label" for="chkMenuPublic">{|Öffentlich freigeben|}:</label></td>
											<td>
												<input type="checkbox" name="menu_public" id="chkMenuPublic" [MENU_PUBLIC_CHECKED]/>
												<span>Überschreibt persönliche Freigaben</span>
											</td>
										</tr>

										<tr>
											<td><label class="input-label" for="menuDocType">{|Beleg|}:</label></td>
											<td>
												<select name="menu_doctype" id="menuDocType">
													[DOCTYPE_OPTIONS]
												</select>
											</td>
										</tr>

										<tr>
											<td><label class="input-label" for="menuLabel">{|Menü-Beschriftung|}:</label></td>
											<td><input type="text" name="menu_label" id="menuLabel" value="[MENU_LABEL]"/></td>
										</tr>

										<tr>
											<td><label class="input-label" for="menuFormat">{|Format|}:</label></td>
											<td>
												<select name="menu_format" id="menuFormat">
													[MENU_FORMAT_OPTIONS]
												</select>
											</td>
										</tr>

									</table>
								</fieldset>
							</div>
						</div>
						<div class="col-xs-12 col-md-6 col-md-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>4. {|Tab (Reiter) in Software|}</legend>
									<table class="form-table">
										<tr>
											<td><label class="input-label" for="chkTabPublic">{|Öffentlich freigeben|}:</label></td>
											<td>
												<input type="checkbox" name="tab_public" id="chkTabPublic" [TAB_PUBLIC_CHECKED]/>
												<span>Überschreibt persönliche Freigaben</span>
											</td>
										</tr>

										<tr>
											<td><label class="input-label" for="tabModule">{|Modul|}:</label></td>
											<td>
												<select name="tab_module" id="tabModule">
													[TAB_MODULE_OPTIONS]
												</select>
											</td>
										</tr>

										<tr>
											<td><label class="input-label" for="tabAction">{|Action|}:</label></td>
											<td>
												<input name="tab_action" id="tabAction" value="[TAB_ACTION]">
											</td>
										</tr>

										<tr>
											<td><label class="input-label" for="tabLabel">{|Tab-Beschriftung|}:</label></td>
											<td><input type="text" name="tab_label" id="tabLabel" value="[TAB_LABEL]"/></td>
										</tr>

										<tr>
											<td><label class="input-label" for="tabPosition">{|Position|}:</label></td>
											<td>
												<select name="tab_position" id="tabPosition">
													[TAB_POSITION_OPTIONS]
												</select>
											</td>
										</tr>

									</table>
								</fieldset>
							</div>
							<input type="hidden" id="reportShareId" name="id" value="[ID]" data-form-id="[ID]"/>
							<input class="rightside" type="submit" name="submit" id="btnSave" value="Speichern"/>
						</div>

					</div>
				</div>
			</form>

		</div>
		[TAB1NEXT]
	</div>

	<div id="editSharingDialog" style="display:none;" title="{|Integration in Xentral|}">
		<form method="post">
			<input type="hidden" id="inputDialogReportId" name="report_id" value="[REPORT_ID]">
			<input type="hidden" id="inputDialogUserId" name="report_id" value="[USER_ID]">
			<div class="inside">
				<fieldset>
					<legend>{|Freigeben für Mitarbeiter|}</legend>

					<div class="dialog-message" id="dialogMessage"></div>

					<table class="form-table dialog-table">
						<tr class="dialog-row">
							<td><label for="inputDialogUser">{|Mitarbeiter|}:</label></td>
							<td colspan="2"><input type="text" id="inputDialogUser"></td>
						</tr>

						<tr>
							<td>{|Freigeben|}:</td>
							<td>
								<input type="checkbox" id="chkDialogShareChart">
								<label for="chkDialogShareChart">{|Graph|}</label>
							</td>
							<td>
								<input type="checkbox" id="chkDialogShareFile">
								<label for="chkDialogShareFile">{|Download|}</label>
							</td>
						</tr>

						<tr>
							<td></td>
							<td>
								<input type="checkbox" id="chkDialogShareMenu">
								<label for="chkDialogShareMenu">{|Aktionsmenü|}</label>
							</td>
							<td>
								<input type="checkbox" id="chkDialogShareTab">
								<label for="chkDialogShareTab">{|Tab (Reiter)|}</label>
							</td>
						</tr>
					</table>

				</fieldset>
			</div>
		</form>
	</div>

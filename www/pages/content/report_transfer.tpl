<div id="tabs" class="report">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]
			<form action="" method="post" name="reportTransferForm" id="report_transfer_form">
				[FORMMESSAGE]
				<div class="row">
					<div class="row-height">
						<div class="col-xs-12 col-sm-6 col-sm-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>{|Per FTP Übertragen|}</legend>
									<table class="mkTableFormular">
										<tr>
											<td class="input-label"><label for="transferFtpActive">{|Aktivieren|}:</label></td>
											<td><input type="checkbox" [FTP_ACTIVE_CHECKED] name="ftp_active" id="transferFtpActive"></td>
										</tr>
										<tr>
											<td class="input-label"><label for="transferFtpType">{|Typ|}:</label></td>
											<td>
												<select value="[FTP_TYPE]" name="ftp_type" id="transferFtpType">
													<option value="ftp" [FTP_TYPE_FTP_SELECTED]>FTP</option>
													<option value="ftpssl" [FTP_TYPE_FTPSSL_SELECTED]>FTP mit SSL</option>
													<option value="sftp" [FTP_TYPE_SFTP_SELECTED]>SFTP</option>
												</select>
											</td>
										</tr>
										<tr>
											<td class="input-label"><label for="transferFtpType">{|Passiver Modus|}:</label></td>
											<td><input type="checkbox" [FTP_PASSIVE_CHECKED] name="ftp_passive" id="transferFtpPassive"></td>
										</tr>
										<tr>
											<td class="input-label"><label for="transferFtpHost">{|FTP-Host|}:</label></td>
											<td colspan="2">
												<input type="text" value="[FTP_HOST]" name="ftp_host" id="transferFtpHost"/>
											</td>
											<td style="float: right">
												<label style="display: inline-block">{|Port|}:</label>
												<input style="display:inline-block;max-width: 70px" type="text" value="[FTP_PORT]" name="ftp_port" id="transferFtpPort"/>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferFtpUser">{|FTP-Benutzer|}:</label></td>
											<td colspan="3">
												<input class="double-col" type="text" value="[FTP_USER]" name="ftp_user" id="transferFtpUser"/>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferFtpPassword">{|FTP-Passwort|}:</label></td>
											<td colspan="3">
												<input class="double-col" type="password" value="[FTP_PASSWORD]" name="ftp_password" id="transferFtpPassword"/>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferFtpIntervalMode">{|Übertragungsintervall|}:</label></td>
											<td colspan="2">
												<select value="[FTP_INTERVAL_MODE]" name="ftp_interval_mode" id="transferFtpIntervalMode">
													<option value="day" [FTP_INTERVAL_MODE_DAY_SELECTED]>{|alle X Tage|}</option>
													<option value="week" [FTP_INTERVAL_MODE_WEEK_SELECTED]>{|Wöchentlich am|}</option>
													<option value="month" [FTP_INTERVAL_MODE_MONTH_SELECTED]>{|Monatlich am|}</option>
												</select>
											</td>
											<td>
												<input type="text" value="[FTP_INTERVAL_VALUE]" name="ftp_interval_value" id="transferIntervalValue"/>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferFtpTime">{|Uhrzeit|}:</label></td>
											<td colspan="1">
												<input type="text" value="[FTP_DAYTIME]" name="ftp_daytime" id="transferFtpTime"/>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferFtpFormat">{|Format|}:</label></td>
											<td>
												<select value="[FTP_FORMAT]" name="ftp_format" id="transferFtpFormat">
													<option value="csv" [FTP_FORMAT_CSV_SELECTED]>CSV</option>
													<option value="pdf" [FTP_FORMAT_PDF_SELECTED]>PDF</option>
												</select>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferFtpFilename">{|Dateiname|}:</label></td>
											<td colspan="3">
												<input class="double-col" type="text" value="[FTP_FILENAME]" name="ftp_filename" id="transferFtpFilename"/>
											</td>
										</tr>
										<tr>
											<td></td>
											<td><i>Variablen: {TIMESTAMP}, {DATUM}, {BERICHTNAME}, Falls leer, wird der Standardname verwendet.</i></td>
										</tr>
									</table>
								</fieldset>
							</div>
						</div>

						<div class="col-xs-12 col-sm-6 col-sm-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>{|E-Mail Versand|}</legend>

									<table class="form-table">
										<tr>
											<td class="input-label"><label for="transferEmailActive">{|Aktivieren|}:</label></td>
											<td><input type="checkbox" [EMAIL_ACTIVE_CHECKED] name="email_active" id="transferEmailActive"></td>
											<td></td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferEmailRecipient">{|E-Mail-Empfänger|}:</label></td>
											<td colspan="2">
												<input class="double-col" type="text" value="[EMAIL_RECIPIENT]" name="email_recipient" id="transferEmailRecipient"/>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferEmailSubject">{|Betreff|}:</label></td>
											<td colspan="2">
												<input class="double-col" type="text" value="[EMAIL_SUBJECT]" name="email_subject" id="transferEmailSubject"/>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferEmailIntervalMode">{|Übertragungsintervall|}:</label></td>
											<td>
												<select value="[EMAIL_INTERVAL_MODE]" name="email_interval_mode" id="transferEmailIntervalMode">
													<option value="day" [EMAIL_INTERVAL_MODE_DAY_SELECTED]>{|alle X Tage|}</option>
													<option value="week" [EMAIL_INTERVAL_MODE_WEEK_SELECTED]>{|Wöchentlich am|}</option>
													<option value="month" [EMAIL_INTERVAL_MODE_MONTH_SELECTED]>{|Monatlich am|}</option>
												</select>
											</td>
											<td>
												<input type="text" value="[EMAIL_INTERVAL_VALUE]" name="email_interval_value" id="transferEmailIntervalValue"/>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferEmailTime">{|Uhrzeit|}:</label></td>
											<td>
												<input type="text" value="[EMAIL_DAYTIME]" name="email_daytime" id="transferEmailTime"/>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferEmailFormat">{|Format|}:</label></td>
											<td>
												<select value="[EMAIL_FORMAT]" name="email_format" id="transferEmailFormat">
													<option value="csv" [EMAIL_FORMAT_CSV_SELECTED]>CSV</option>
													<option value="pdf" [EMAIL_FORMAT_PDF_SELECTED]>PDF</option>
												</select>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferEmailFilename">{|Dateiname|}:</label></td>
											<td colspan="2">
												<input class="double-col" type="text" value="[EMAIL_FILENAME]" name="email_filename" id="transferEmailFilename"/>
											</td>
										</tr>
										<tr>
											<td></td>
											<td><i>Variablen: {TIMESTAMP}, {DATUM}, {BERICHTNAME}, Falls leer, wird der Standardname verwendet.</i></td>
										</tr>
									</table>
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
									<legend>{|URL teilen|}</legend>
									<table class="form-table">

										<tr>
											<td class="input-label"><label for="transferUrlFormat">{|Format|}:</label></td>
											<td>
												<select value="[URL_FORMAT]" name="url_format" id="transferUrlFormat">
													<option value="csv" [URL_FORMAT_CSV_SELECTED]>CSV</option>
													<option value="pdf" [URL_FORMAT_PDF_SELECTED]>PDF</option>
												</select>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferUrlBegin">{|Verfügbarkeit|}:</label></td>
											<td colspan="2">
												<input class="half-col" type="text" value="[URL_BEGIN]" name="url_begin" id="transferUrlBegin"/>
												<label for="transferUrlEnd">{|bis|}</label>
												<input class="half-col" type="text" value="[URL_END]" name="url_end" id="transferUrlEnd"/>
											</td>
										</tr>

										<tr>
											<td>
											<td>
											<input type="submit" name="create_url" id="transferUrlCreate" value="{|Neue URL erzeugen|}">
											</td>
											<td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferUrlAdress">{|Abruf über URL|}:</label></td>
											<td colspan="2" >
												<input type="text" value="[URL_ADDRESS]" name="url_address" id="transferUrlAdress"/>
												<a class="playbutton" id="transferUrlClipboard" href="javascript:;">
													<img src="./themes/[THEME]/images/copy.svg" alt="copy to clipboard">
												</a>
											</td>
										</tr>
										<tr>
											<td></td>
											<td><i><b>Hinweis: </b>Durch das Generieren einer neuen URL werden alle bisherigen gelöscht.</i></td>
										</tr>
									</table>
								</fieldset>
							</div>
						</div>

						<div class="col-xs-12 col-sm-6 col-sm-height">
							<div class="inside inside-full-height">
								<fieldset>
									<legend>{|API-Zugriff Freigeben|}</legend>
									<table class="form-table">
										<tr>
											<td class="input-label"><label for="transferApiActive">{|Aktivieren|}:</label></td>
											<td><input type="checkbox" [API_ACTIVE_CHECKED] name="api_active" id="transferApiActive"></td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferApiAccount">{|API-Account|}:</label></td>
											<td colspan="2">
												<input type="text" value="[API_ACCOUNT_NAME]" name="api_account_name" id="transferApiAccount"/>
											</td>
										</tr>

										<tr>
											<td class="input-label"><label for="transferApiFormat">{|Format|}:</label></td>
											<td>
												<select value="[API_FORMAT]" name="api_format" id="transferApiFormat">
													<option value="csv" [API_FORMAT_CSV_SELECTED]>CSV</option>
													<option value="pdf" [API_FORMAT_PDF_SELECTED]>PDF</option>
												</select>
											</td>
										</tr>
									</table>
								</fieldset>
							</div>
							<input type="hidden" id="reportTransferId" name="id" value="[ID]" data-form-id="[ID]"/>
							<input class="rightside" type="submit" name="submit" id="btnSave" value="Speichern"/>
						</div>
					</div>
				</div>
			</div>
		</form>
		[TAB1NEXT]
	</div>

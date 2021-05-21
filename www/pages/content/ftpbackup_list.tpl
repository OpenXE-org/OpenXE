<div id="tabs">
	<ul>
		<li><a href="#tabs-1">[TABTEXT]</a></li>
	</ul>

	<div id="tabs-1">
		[MESSAGE]
		[TAB1]

		<div class="row">
			<div class="row-height">

				<div class="col-xs-12 col-md-4 col-md-height">
					<div class="inside inside-full-height">

						<!--<fieldset>
							<legend>{|Prozessstarter|}</legend>
							<form action="index.php?module=ftpbackup&action=togglecronjob" method="post">
								<input type="submit" class="btnBlueNew" value="[CRONJOB_ACTIVATE_BUTTON_TEXT]">
							</form>
						</fieldset>-->

						<fieldset>
							<legend>{|Zugangsdaten|}</legend>
							<form action="" method="post">
								<table>
									<tr>
										<td width="200">FTP-Server:</td>
										<td><input type="text" size="30" name="server" value="[SERVER]" id="server"></td>
									</tr>
									<tr>
										<td width="200">FTP-Port:</td>
										<td><input type="text" size="30" name="port" value="[PORT]" id="port"></td>
									</tr>
									<tr>
										<td width="200">FTP-Benutzer:</td>
										<td><input type="text" size="30" name="benutzer" value="[BENUTZER]" id="benutzer"></td>
									</tr>
									<tr>
										<td width="200">FTP-Passwort:</td>
										<td><input type="password" size="30" name="passwort" value="[PASSWORT]" id="passwort"></td>
									</tr>
									<tr>
										<td width="200">FTP-Verzeichnis:</td>
										<td>
											<input type="text" size="30" name="verzeichnis" value="[VERZEICHNIS]" id="verzeichnis">
											<br><span>Verzeichnis auf dem FTP-Server. Muss mit <code>/</code> beginnen.</span>
										</td>
									</tr>
									<tr>
										<td colspan="2">
											<input type="submit" value="{|Speichern|}" name="save-settings" id="save-settings">
										</td>
									</tr>
								</table>
							</form>
						</fieldset>
						<fieldset>
							<legend>Über FTP-Backup</legend>
							<p>Ansicht der Dateien auf dem FTP-Backup Server. Es sollte jeden Tag eine Datei "userdata_". und
								"mysqldump_" erstellt werden. Jeden Tag sollte die Datei userdata tendenziell etwas größer werden.
								Die Datei mysql_complete_ wird ebenso regelmäßig größer, bis auf ab und zu kann es sein, dass die
								Dateigröße kleiner als am Vortrag ist (wenn z.B. Caches oder Druckerspooler automatisch gelöscht
								wurden), aber zwischen diesen Zeiten sollte sie größer werden.
							</p>
						</fieldset>

					</div>
				</div>
				<div class="col-xs-12 col-md-8 col-md-height">
					<div class="inside inside-full-height">

						<fieldset>
							<legend>{|Backups|}</legend>
							<table class="mkTable">
								<tr>
									<th>Nr.</th>
									<th>Datum</th>
									<th>Name</th>
									<th>Größe</th>
									<th>Datum</th>
									<th>Name</th>
									<th>Größe</th>
								</tr>
								[BACKUPROW]
							</table>
						</fieldset>

					</div>
				</div>

			</div>
		</div>

		[TAB1NEXT]
	</div>
</div>

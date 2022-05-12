<!-- gehort zu tabview -->
<div id="tabs" class="backup-template">
	<ul>
		<li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE_RUNNING]
		[MESSAGE]
		<div class="error2 backup-create">
			Backup f&uuml;r Umzug erstellen <a class="button button-neutral" role="button" href="#"><span id="create-backup">Backup erstellen</span></a>
		</div>
		[MESSAGE_DOWNLOAD]
		[MESSAGE_ERROR]
		[TAB1]
		[TAB1NEXT]
	</div>
	<!-- tab view schließen -->
</div>

<div id="backupModal" class="main-container" style="display:none;" title="Backup Manager"
		 data-ps="[PROCESS_STARTER_STATUS]">
	<div id="bck-message" class=""></div>
	<div id="bck-ps-message" class=""></div>
</div>

<div id="backupModalTimer" class="invisible" title="Backup Manager">
	<div id="bck-status-message" class="invisible"></div>
</div>

<div id="add-backup" style="display:none;" title="Backup erstellen">

	<div class="bck-server-move warning"><p>Bitte machen Sie eine lokale Sicherung der Backupdatei, falls Sie einen Umzug
			planen. Zudem sollten Sie nach dem Download, die Datei entpacken um sicher zu stellen, dass diese vollständig
			heruntergeladen wurden.</p></div>

	<div class="bck-server-move warning">Bitte beachten Sie, dass während des Backup Prozesses alle laufende Jobs beendet,
		alle angemeldeten User zwangsausgeloggt, sowie das Login kurzzeitig gesperrt werden.
	</div>

	<form method="post">
		<fieldset>
			<table>
				<tr>
					<td>{|Name|}:</td>
					<td><input type="text" name="name" id="b_name" size="40"></td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>

<div class="invisible" id="recovery-migration">
	<fieldset>
		<legend>{|Migration Einstellungen|}</legend>
		<br>
		<table id="settings-container">
			<tbody>
			<tr>
				<td width="100"><strong>{|Keine Migration|}</strong>:</td>
				<td>
					<label class="no-switch">
						<input id="no-migration" name="no_migration" type="radio" checked>
						<span class="slider round"></span>
					</label>
					<label for="no-migration">Wiederherstellung ist am gleichen Server und der Datenbankname ist gleich
						geblieben</label>
				</td>
			</tr>
			<tr>
				<td width="100"><strong>Migration</strong></td>
				<td>
					<label class="no-switch">
						<input id="do-migration" name="do_migration" type="radio">
						<span class="slider round"></span>
					</label>
					<label for="is-migration">Der Datenbankname wurde geändert</label>
				</td>
			</tr>
			<tr class="invisible" id="tr-old-dbname">
				<td width="150">Alter Datenbankname:</td>
				<td>
					<input type="text" name="old_dbname" id="old-dbname" style="width: 43%" placeholder="wawision">
				</td>
			</tr>
			</tbody>
		</table>
	</fieldset>
</div>

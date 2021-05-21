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
<!--
<div class="row">
<div class="row-height">
<div class="col-xs-12 col-sm-height">
<div class="inside inside-full-height">

<fieldset><legend>{|Status|}</legend>
<form id="meiappsform" action="" method="post" enctype="multipart/form-data">
<div class="info">Das letzte Backup wurde am 24.12.2001 um 06:12 erfolgreich durchgeführt.</div>
</form>

</fieldset>
</div>
</div>
</div>
</div>
-->

		<fieldset>
			<legend>{|Zugangsdaten DocuVita|}</legend>
			<form action="" method="post" id="">
				<table>
					<tr><td width="200">Session-key:</td><td><input type="password" size="30" name="password" value="[PASSWORD]"></td></tr>
					<tr><td width="200">URL (endet auf "/services"):</td><td><input type="text" size="30" name="url" value="[URL]"></td></tr>
					<tr><td width="200">ObjectTypeID Ordner:</td><td><input type="text" size="30" name="class_id_folder" value="[CLASS_ID_FOLDER]"></td></tr>
					<tr><td width="200">ObjectTypeID Lieferantenbeleg:</td><td><input type="text" size="30" name="class_id_receipt" value="[CLASS_ID_RECEIPT]"></td></tr>
				</table>
				<input type="submit" value="Speichern" name="speichern">
			</form>
		</fieldset>

		[TAB1NEXT]
	</div>

<!-- tab view schließen -->
</div>


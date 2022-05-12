<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<div id="tabs-1">
		[MESSAGE]
		<div class="row">
			<div class="row-height">
				<div class="col-xs-12 col-sm-10 col-sm-height">
					<div class="inside">
						[WIDGET_IMPORTVORLAGE_TABLE]
						[WIDGET_IMPORTVORLAGE_SEARCH]
					</div>
				</div>
				<div class="col-xs-12 col-sm-2 col-sm-height">
					<div class="inside inside-full-height">
						<fieldset><legend>{|Aktionen|}</legend>
							<a class="neubuttonlink" href="index.php?module=importvorlage&amp;action=create"><input type="button" value="&#10010; {|Neuer Eintrag|}" class="btnGreenNew"></a>
							<a class="neubuttonlink" id="jsonUploadDialog" href="#"><input type="button" value="&#10010; {|Neuer Eintrag mit Vorlagendatei|}" class="btnGreenNew"></a>
						</fieldset>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="jsonEditUploadDialog" style="display:none;" title="Vorlagendatei hochladen">
	<form action="" method="post" enctype="multipart/form-data">
		<table class="mkTableFormular" width="100%">
			<tr>
				<td><input type="file" name="jsonfile" id="jsonfile"/></td>
			</tr>
			<tr>
				<td><input type="submit" value="{|Speichern|}" name="jsonupload" class="btnGreen pull-right" /></td>
			</tr>
		</table>
	</form>
</div>
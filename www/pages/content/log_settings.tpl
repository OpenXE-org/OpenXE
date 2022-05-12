<!-- gehort zu tabview -->
<div id="tabs" class="log">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]

		<div class="row">
			<div class="row-height">
				<div class="col-xs-10 col-sm-10 col-sm-height">
					<div class="inside inside-full-height">
						<form action="" method="post" name="logSettings" enctype="multipart/form-data" id="log_settings_form">
							<fieldset>
								<legend>{|Einstellungen|}</legend>
								<table class="mkTableFormular">
									<tr>
										<td class="input-label"><label for="settings_level">{|Log-Level|}:</label></td>
										<td>
											<select type="text" name="level" id="settings_level">
												<option value="debug" [DEBUG_SELECTED]>DEBUG</option>
												<option value="info" [INFO_SELECTED]>INFO</option>
												<option value="notice" [NOTICE_SELECTED]>NOTICE</option>
												<option value="warning" [WARNING_SELECTED]>WARNING</option>
												<option value="error" [ERROR_SELECTED]>ERROR</option>
												<option value="critical" [CRITICAL_SELECTED]>CRITICAL</option>
												<option value="alert" [ALERT_SELECTED]>ALERT</option>
												<option value="emergency" [EMERGENCY_SELECTED]>EMERGENCY</option>
											</select>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
										<td>
											<input class="button button-primary" type="submit" name="submit" id="btn_save" value="Speichern" [DISABLED]/>
										</td>
									</tr>
								</table>
							</fieldset>
						</form>
					</div>
				</div>
				<div class="col-xs-2 col-sm-2 col-sm-height">
					<div class="inside inside-full-height">
						<form action="" method="post" name="logSettings" enctype="multipart/form-data" id="log_settings_form">
							<fieldset>
								<form action="#">
									<legend>{|Aktion|}</legend>
										<input type="button" class="button button-block button-primary" id="btn_delete" value="{|Log-Einträge löschen|}"/>
								</form>
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

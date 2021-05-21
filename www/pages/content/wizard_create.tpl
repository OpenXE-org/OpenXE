<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<div id="tabs-1">
		[MESSAGE]
		<div class="row" id="docscan-module">
			<div class="row-height">
				<div class="col-xs-12 col-md-8 col-md-height">
					<div class="inside-full-height">
						<div>
							<fieldset>
								<legend>Wizard anlegen</legend>
								<form action="./index.php?module=wizard&action=create&cmd=jsoninput" method="post">
									<div>
										<table>
											<tr>
												<td>Benutzer:</td>
												<td>
													<select name="createwizard_user">
														<option value="">bitte wählen&hellip;</option>
														[CREATEWIZARDUSEROPTIONS]
													</select>
												</td>
											</tr>
											<tr>
												<td valign="top" width="200">JSON-Daten:</td>
												<td><textarea name="createwizard_json" cols="120" rows="32">[CREATEWIZARDJSON]</textarea></td>
											</tr>
											<tr>
												<td></td>
												<td><input type="submit" value="Speichern" name="speichern"></td>
											</tr>
										</table>
									</div>
								</form>
							</fieldset>
						</div>
					</div>
				</div>
				<div class="col-xs-12 col-md-4 col-md-height">
					<div class="inside-full-height">
						<div>
							<fieldset>
								<legend>Wizard laden</legend>
								<form action="./index.php?module=wizard&action=create&cmd=loadwizard" method="post">
									<table>
										<tr>
											<td>
												<select name="loadwizard_selected">
													<option value="">bitte wählen&hellip;</option>
													[LOADWIZARDOPTIONS]
												</select>
											</td>
										</tr>
										<tr>
											<td>
												<input type="submit" value="Laden" name="loadwizard_button">
												<a class="button" href="./index.php?module=wizard&action=create&cmd=loadexample">Beispiel-Wizard laden</a>
											</td>
										</tr>
									</table>
								</form>
							</fieldset>
						</div>
						<div>
							<fieldset>
								<legend>Wizard löschen</legend>
								<form action="./index.php?module=wizard&action=create&cmd=deletewizard" method="post">
									<table>
										<tr>
											<td>
												<select name="deletewizard_selected">
													<option value="">bitte wählen&hellip;</option>
													[LOADWIZARDOPTIONS]
												</select>
											</td>
										</tr>
										<tr>
											<td><input type="submit" value="Löschen" name="deletewizard_button"></td>
										</tr>
									</table>
								</form>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
		</div>
		[TAB1NEXT]
	</div>
</div>

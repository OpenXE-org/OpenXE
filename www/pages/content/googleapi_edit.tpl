<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]

		<div class="col-xs-12 col-md-2 col-md-height">
			<div class="inside inside-full-height">
				<div>
					<form action="?module=googleapi&action=edit" method="post">
						<fieldset>
							<legend>OAuth 2.0 Zugangsdaten</legend>
							<table width="100%" border="0" class="mkTableFormular">
								<tbody>
								<tr>
									<td>Client ID:</td>
									<td><input type="text" name="client_id" value="[CLIENT_ID]" size="80%"></td>
								</tr>
								<tr>
									<td>Client Schlüssel:</td>
									<td><input type="text" name="secret" value="[SECRET]" size="80%"></td>
								</tr>
								<tr>
									<td>Redirect URI:</td>
									<td>
										<input id="googleapi_redirectvalue" type="text" name="redirect_uri" value="[REDIRECT_URI]" size="80%">
									</td>
								</tr>
								</tbody>
							</table>
							<input type="submit" name="save" value="Speichern">
						</fieldset>
					</form>
				</div>
			</div>
		</div>

		<!--<div class="col-xs-12 col-md-2 col-md-height">
			<div class="inside inside-full-height actionpane">
				<form action="?module=googleapi&action=edit" method="POST">
				<fieldset>
					<legend>{|Aktionen|}</legend>
					<input type="submit" name="authorize_mail" value="{|Mail|}"/>
					<input type="submit" name="authorize_cal" value="{|Kalender|}"/>
					<input type="submit" name="unauthorize" value="{|Trennen|}"/>
				</fieldset>
				</form>
			</div>
		</div>-->

		[TAB1NEXT]
	</div>

	<!-- tab view schließen -->
</div>




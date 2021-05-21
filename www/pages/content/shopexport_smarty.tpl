<div id="smartyinput">
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-8 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Smarty Template|}</legend>
						<textarea rows="30" cols="100" id="textareasmartyincomming">[TEXTAREASMARTYINCOMMING]</textarea>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-md-4 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>Einstellungen</legend>
						<table>
							<tr>
								<td>
									<label for="transferactive">{|Warenkorb transformieren aktiv|}:</label></td>
								<td><input type="checkbox" id="transferactive" value="1" [TRANSFERACTIVE]/></td>
							</tr>
							<tr>
								<td><label for="smartyinputtype">{|Format|}:</label></td>
								<td><select id="smartyinputtype">
											<option value="xml" [OPTIONXML]>XML</option>
											<option value="json" [OPTIONJSON]>JSON</option>
										</select>
								</td>
							</tr>
							<tr>
								<td><label for="replacecart">{|Warenkorb ersetzen|}:</label></td>
								<td><input type="checkbox" id="replacecart" value="1" [REPLACECART]/></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-sm-8 col-sm-height">
				<div class="inside-full-height">
					<label for="cart">{|Warenkorb|}:</label> <input type="text" id="cart" name="cart" />
					<input type="button" value="{|Warenkorb laden|}" id="loadCart" data-shopid="[SHOPID]" />
					<input type="button" value="{|Standard Template laden|}" class="hidden" id="loadDefaultTemplate" />
				</div>
			</div>
			<div class="col-xs-12 col-sm-4 col-sm-height">
				<div class="inside-full-height">
					<input type="button" class="buttonsave" value="Ausf&uuml;hren" id="runincomming"/>
					<input type="button" class="buttonsave" value="Speichern" id="saveincomming"/>
				</div>
			</div>
		</div>
	</div>

	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-sm-4 col-sm-height">
				<div class="inside inside-full-height">
					<div class="smartyincommingoutput">
						<fieldset>
							<legend>{|Eingang|}</legend>
							<textarea id="textareasmartyincomminginput"></textarea>
						</fieldset>
					</div>
				</div>
			</div>

			<div class="col-xs-12 col-sm-4 col-sm-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Struktur|}: $cart</legend>
						<div id="dataincommingobject" class="smartyincommingoutput">
						</div>
					</fieldset>
				</div>
			</div>
			<div class="col-xs-12 col-sm-4 col-sm-height">
				<div class="inside inside-full-height">
					<fieldset>
						<legend>{|Ergebnis|}</legend>
						<textarea id="dataincommingpreview"></textarea>
					</fieldset>
				</div>
			</div>

		</div>
	</div>
</div>
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">{|Einstellungen|}</a></li>
	</ul>
	<div id="tabs-1">
		[MESSAGE]
		<form action="./index.php?module=paymentslip_swiss&action=list&id=[ID]" method="post">
			<input type="hidden" name="cmd" value="settings">
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-8 col-md-height">
						<div class="inside-full-height">

							<fieldset><legend>Gültigkeitsbereich</legend>
								[INFOTEXT]
							</fieldset>

							<fieldset>
								<legend>{|Einstellungen|}</legend>
								<table>
									<tr>
										<td width="200">{|Modul aktivieren|}:</td>
										<td>
											<select name="module_active">[PROJEKTOPTIONS]</select>
										</td>
									</tr>
									<tr>
										<td width="200">{|ESR-Identifikationsnummer|}:</td>
										<td>
											<input type="text" name="identifikationsnummer" id="identifikationsnummer"
														 size="20" value="[EINSTELLUNGEN_IDENTIFIKATIONSNUMMER]">
											<em>Ihre Identifikationsnummer (sechsstellig)</em>
										</td>
									</tr>
								</table>
							</fieldset>
							<fieldset>
								<legend>{|Begünstigtenbank|}</legend>
								<table>
									<tr>
										<td width="200">{|ESR-Teilnehmernummer|}:</td>
										<td>
											<input type="text" name="teilnehmernummer" id="teilnehmernummer"
														 size="20" value="[EINSTELLUNGEN_TEILNEHMERNUMMER]">
											<em>ESR-Teilnehmernummer ihrer Bank; z.b.: 01-023456-7</em>
										</td>
									</tr>
									<tr>
										<td width="200">{|Bankname|}:</td>
										<td>
											<input type="text" name="bankname" id="bankname" size="40" value="[EINSTELLUNGEN_BANKNAME]">
										</td>
									</tr>
									<tr>
										<td>{|Bank|} {|PLZ/Ort|}:</td>
										<td nowrap="">
											<input type="text" id="bankplz" name="bankplz" size="6" value="[EINSTELLUNGEN_BANKPLZ]">
											<input type="text" id="bankort" name="bankort" size="30" value="[EINSTELLUNGEN_BANKORT]">
										</td>
									</tr>
								</table>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<input type="submit" id="speichern" name="speichern" value="Speichern">
		</form>
	</div>
</div>

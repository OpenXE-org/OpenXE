<div id="tabs">
	<ul>
		<li><a href="#tabs-1">{|Einstellungen|}</a></li>
		<li><a href="#tabs-2">{|Logs|}</a></li>
	</ul>
	<div id="tabs-1">
		[MESSAGE]
		<form method="post">
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside inside-full-height">
							<fieldset>
								<legend>{|Einstellungen|}</legend>
								<table>
									<tr>
										<td>
											<label for="firmkeyid">{|AmaInvoice Kundennummer|}:</label>
										</td>
										<td>
											<input type="text" id="firmkeyid" name="firmkeyid" value="[FIRMKEYID]" size="40"/>
										</td>
									</tr>
									<tr>
										<td>
											<label for="clientidentifier">{|Xentral Auth Token|}:</label>
										</td>
										<td>
											<input type="password" id="clientidentifier" name="clientidentifier" value="[CLIENTIDENTIFIER]"
														 size="40"/>
										</td>
									</tr>
									<tr>
										<td>
											<label for="startdate">{|Rechnungen abholen ab|}:</label>
										</td>
										<td>
											<input type="text" id="startdate" name="startdate" value="[STARTDATE]" size="12"/>
										</td>
									</tr>
									<tr>
										<td>
											<label for="projectfba">{|Projekt FBA|}:</label>
										</td>
										<td>
											<input type="text" id="projectfba" name="projectfba" value="[PROJECTFBA]" size="30"/>
										</td>
									</tr>
									<tr>
										<td>
											<label for="projectfbm">{|Projekt FBM|}:</label>
										</td>
										<td>
											<input type="text" id="projectfbm" name="projectfbm" value="[PROJECTFBM]" size="30"/>
										</td>
									</tr>
									<tr>
										<td>
											<label for="paymentmethod">{|Zahlungweise|}:</label>
										</td>
										<td>
											<select id="paymentmethod" name="paymentmethod">
												<option value=""></option>
												[SELPAYMENTMETHOD]
											</select>
										</td>
									</tr>
									<tr>
										<td>
											<label for="expert">{|Experten Modus|}:</label>
										</td>
										<td>
											<input type="checkbox" id="expert"/>
										</td>
									</tr>
									<tr class="trexpert">
										<td>
											<label for="createorder">{|Auftr&auml;ge|}:</label>
										</td>
										<td>
											<input type="checkbox" value="1" name="createorder" id="createorder" [CREATEORDER]/>
										</td>
									</tr>
								</table>
							</fieldset>
							<table width="100%">
								<tr>
									<td align="right">
										<input type="submit" class="button-primary" name="save" value="{|Speichern|}"/>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
			</div>
		</form>
		[TAB1]
		[TAB1NEXT]
	</div>
	<div id="tabs-2">
		[MESSAGE]
		[TAB2]
		<input type="button" class="button-primary" id="import" value="Rechnungen / Gutschriften nachladen"/>
	</div>
</div>


<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<div id="tabs-1">
		[MESSAGE]
		<div class="row">
			<div class="row-height">
				<div class="col-md-12 col-md-height">
					<div class="inside inside-full-height">
						<form action="" method="post">
							<fieldset>
								<legend>Einstellungen</legend>
								<table class="mkTableFormular">
									<tbody>
									<tr>
										<td><label for="surcharge-article">{|Kupferzuschlagsartikel|}*</label></td>
										<td><input type="text" id="surcharge-article" name="surcharge-article" size="30" value="[ARTICLEID]"></td>
									</tr>
									<tr>
										<td><label for="surcharge-position-type">{|Position einfügen|}*</label></td>
										<td>
											<select type="text" id="surcharge-position-type" name="surcharge-position-type" >
												<option value="0" [POSITIONTYPE0]>{|pro Position eine Kupferzuschlagsposition einfügen|}</option>
												<option value="1"[POSITIONTYPE1]>{|nur eine Sammelposition einfügen|}</option>
												<option value="2" [POSITIONTYPE2]>{|pro Gruppe eine Kupferzuschlagsposition einfügen|}</option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label for="surcharge-document-conversion">{|Kupferzuschlag - Angebot zu Auftrag|}*</label></td>
										<td>
											<select type="text" id="surcharge-document-conversion" name="surcharge-document-conversion">
												<option value="0" [DOCUMENTCONVERSION0]>{|Kupferkurs vom Angebotsdatum|}</option>
												<option value="1" [DOCUMENTCONVERSION1]>{|Kupferkurs vom Auftragsdatum|}</option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label for="surcharge-invoice">{|Kupferzuschlag Rechnung erstellen|}*</label></td>
										<td>
											<select type="text" name="surcharge-invoice" id="surcharge-invoice">
												<option value="0" [INVOICE0]>{|Kupferkurs vom Auftragsdatum|}</option>
												<option value="1" [INVOICE1]>{|Kupferkurs vom Lieferschein/Lieferdatum|}</option>
												<option value="2" [INVOICE2]>{|Kupferkurs vom Rechnungsdatum (taggenau)|}</option>
												<option value="3" [INVOICE2]>{|Kupferkurs vom Angebotsdatum|}</option>
											</select>
										</td>
									</tr>
									<tr>
										<td><label for="surcharge-maintenance-type">{|Wie sollen die Daten gespeichert werden?|}*</label></td>
										<td>
											<label>
												<input type="radio" name="surcharge-maintenance-type" value="0" [MAINTENANCE_APP_CHECK]>
											</label> {|mit der App Rohstoffliste|}
											<label>
												<input type="radio" name="surcharge-maintenance-type" value="1" [MAINTENANCE_ARTICLE_CHECK]>
											</label> {|mit Artikelzusatzfeldern|}
										</td>
									</tr>
									<tr class="surcharge-optional">
										<td><label for="surcharge-copper-number">{|Artikelspezifische Kupferzahl (kg/km)|}</label></td>
										<td>
											<select type="text" name="surcharge-copper-number" id="surcharge-copper-number">
												<option value=""></option>
												[COPPERNUMBEROPTIONS]
											</select>
										</td>
									</tr>
									<tr>
										<td><label for="surcharge-delivery-costs">{|Bezugskosten (in Prozent)|}*</label></td>
										<td><input type="text" name="surcharge-delivery-costs" id="surcharge-delivery-costs" size="30" value="[DELIVERYCOSTS]"></td>
									</tr>
									<tr>
										<td><label for="surcharge-copper-base-standard">{|Standard Kupferbasis (in EUR pro 100kg)|}*</label></td>
										<td><input type="text" name="surcharge-copper-base-standard" id="surcharge-copper-base-standard" size="30" value="[COPPERBASESTANDARD]"></td>
									</tr>
									<tr>
										<td><label for="surcharge-copper-base">{|Artikelspezifische Kupferbasis (in EUR pro 100kg)|}</label></td>
										<td>
											<select type="text" name="surcharge-copper-base" id="surcharge-copper-base">
												<option value=""></option>
												[FREEFIELDOPTIONS]
											</select>
										</td>
									</tr>
									<tr>
										<td></td>
										<td>
											<button type="submit" name="coppersurcharge-save" class="button button-primary">Änderungen speichern
											</button>
										</td>
									</tr>
									</tbody>
								</table>
							</fieldset>
						</form>
					</div>
				</div>
			</div>
		</div>
		[TAB1NEXT]
	</div>
</div>
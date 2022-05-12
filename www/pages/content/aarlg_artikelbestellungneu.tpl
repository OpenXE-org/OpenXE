[MESSAGE]

<div class="row">
	<div class="row-height">
		<div class="col-md-12 col-lg-5 col-lg-height">
			<div class="inside inside-full-height">

				<fieldset>
					<legend>{|Artikel Neu|}</legend>
					<form method="post" action="">
						<table width="100%" cellpadding="0" cellspacing="10" border="0">
							<tr>
								<td width="25%">Artikel (DE):</td>
								<td width="75%"><input type="text" id="name_de" name="name_de" value="[NAME_DE]" size="40" maxlength="50" maxlength="" style="width: 100%;"></td>
							</tr>
							<tr>
								<td>Bestellnummer:</td>
								<td><input type="text" id="bestellnummer" name="bestellnummer" value="[BESTELLNUMMER]" size="20" maxlength=""></td>
							</tr>
							<tr>
								<td>Bezeichnung</td>
								<td><input name="bezeichnunglieferant" id="bezeichnungliefeant" value="[BEZEICHNUNGLIEFERANT]" type="text" size="20">
								</td>
							</tr>
							<tr>
								<td>Artikelgruppe</td>
								<td><select name="typ" size="0" id="" class="" onchange="">[ARTIKELGRUPPE]</select></td>
							</tr>
							<tr>
								<td>Standardlieferant:</td>
								<td>[LIEFERANTSTART]<input name="adresse" value="[ADRESSE]" type="text" id="adresse" size="20">[LIEFERANTENDE]
								</td>
							</tr>
							<tr>
								<td>Projekt</td>
								<td>[PROJEKTSTART]<input name="projekt" id="projekt" value="[PROJEKT]" type="text" size="20">[PROJEKTENDE]
								</td>
							</tr>
							<tr>
								<td>Menge:</td>
								<td><input type="text" id="menge" class="0" name="menge" value="[MENGE]" size="20" maxlength=""></td>
							</tr>
							[LIEFERSCHEINIF]
							[LIEFERSCHEINELSE]
							<tr>
								<td>Preis (Einkauf):</td>
								<td><input type="text" id="preis" class="0" name="preis" value="[PREIS]" size="20" maxlength=""></td>
							</tr>
							<tr>
								<td>Steuersatz:</td>
								<td><select id="umsatzsteuerauswahl" name="umsatzsteuer">[UMSATZSTEUERAUSWAHL]</select></td>
							</tr>
							<tr>
								<td><label for="steuersatz-individuell-switch">individuellen Steuersatz verwenden</label></td>
								<td><input type="checkbox" name="anderersteuersatz" id="steuersatz-individuell-switch" [STEUERSATZEINBLENDEN]"></td>
							</tr>
							<tr id="steuersatz-individuell-container">
								<td>Individueller Steuersatz:</td>
								<td><input name="steuersatz" id="steuersatz-individuell" value="[INDIVIDUELLERSTEUERSATZ]" type="text" size="20">&nbsp;in&nbsp;Prozent</td>
							</tr>
							<tr>
								<td>Einkauf bei allen Lieferanten:</td>
								<td><input type="checkbox" name="allelieferanten" value="1"></td>
							</tr>
							[LIEFERSCHEINENDIF]
							<tr>
								<td>Kurztext (DE):</td>
								<td><textarea rows="2" id="kurztext_de" class="" name="kurztext_de" cols="70">[KURZTEXT_DE]</textarea></td>
							</tr>
							<tr>
								<td>Interner Kommentar:</td>
								<td><textarea rows="2" id="internerkommentar" class="" name="internerkommentar" cols="70">[INTERNERKOMMENTAR]</textarea></td>
							<tr>
							<tr>
								<td></td>
								<td><input type="submit" value="Artikel anlegen" name="anlegen"></td>
							<tr>
						</table>
					</form>
				</fieldset>

			</div>
		</div>
		<div class="col-md-12 col-lg-7 col-lg-height">
			<div class="inside inside-full-height">

				<fieldset>
					<legend>Artikel-/Preistabelle von [KUNDE]</legend>
					<table width="100%" cellpadding="0" cellspacing="10" border="0">
						<tr>
							<td>
								<form id="lieferantartikelpreiseform" data-process-id="[PROCESSID]" data-process-type="[PROCESSTYPE]">
									[ARTIKEL]
									<fieldset>
										<legend>{|Stapelverarbeitung|}</legend>
										<label><input type="checkbox" id="articlematrixselection-selectall-checkbox">&nbsp;{|alle auswählen|}</label>
										<input type="submit" value="Ausgewählte Artikel übernehmen" id="articlematrixselection-insert-button">
										<input type="button" value="Auswahl zurücksetzen" id="articlematrixselection-reset-button">
									</fieldset>
								</form>
							</td>
						</tr>
					</table>
				</fieldset>

			</div>
		</div>
	</div>
</div>

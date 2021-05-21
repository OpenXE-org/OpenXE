<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]
		<form action="" method="post" name="eprooform" id="eprooform">
			[FORMHANDLEREVENT]
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside inside-full-height">
							<fieldset>
								<legend>{|Einstellungen|}</legend>
								<table width="100%">
									<tr>
										<td width="150">{|Bezeichnung|}:</td>
										<td><input type="text" name="bezeichnung" id="bezeichnung" size="40" rule="notempty"
															 msg="Pflichfeld!" tabindex="2" value="[BEZEICHNUNG]"></td>
									</tr>
									<tr>
										<td>{|Typ|}:</td>
										<td>
											<select name="type" id="type">
												[TYPE]
											</select>
										</td>
									</tr>
									<tr>
										<td>{|Projekt|}:</td>
										<td><input type="text" size="30" name="projekt" id="projekt" value="[PROJEKT]"></td>
									</tr>
									<tr>
										<td>{|Aktiv|}:</td>
										<td><input type="checkbox" name="aktiv" id="aktiv" value="1"
															 [AKTIV]><i>{|Aktiv. Nicht mehr verwendete Konten k&ouml;nnen deaktiviert werden.|}</i>
										</td>
									</tr>
									<tr>
										<td>{|Keine E-Mail|}:</td>
										<td><input type="checkbox" name="keineemail" id="keineemail" value="1"
															 [KEINEEMAIL]><i>{|Normalerweise wird beim Zahlungseingang eine Mail an den Kunden gesendet. Soll dies unterdr&uuml;ckt werden muss diese Option gesetzt werden.|}</i>
										</td>
									</tr>
									<tr>
										<td>{|&Auml;nderungen  erlauben|}:</td>
										<td><input type="checkbox" name="schreibbar" id="schreibbar" value="1"
															 [SCHREIBBAR]><i>&nbsp;{|Es d&uuml;rfen nachtr&auml;glich Kontobuchungen ver&auml;ndert werden|}</i>
										</td>
									</tr>
								</table>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside inside-full-height">
							<fieldset>
								<legend>{|Bankverbindung (bei Typ Bank)|}</legend>
								<table width="100%">
									<tr>
										<td width="150">{|Inhaber|}:</td>
										<td><input type="text" name="inhaber" id="inhaber" size="40" value="[INHABER]"></td>
										<td>
									</tr>
									<tr>
										<td>{|BIC|}:</td>
										<td><input type="text" name="swift" id="swift" size="40" value="[SWIFT]"></td>
										<td>
									</tr>
									<tr>
										<td>{|IBAN|}:</td>
										<td><input type="text" name="iban" id="iban" size="40" value="[IBAN]"></td>
										<td>
									</tr>
									<tr>
										<td>{|BLZ|}:</td>
										<td><input type="text" name="blz" id="blz" size="40" value="[BLZ]"></td>
										<td>
									</tr>
									<tr>
										<td>{|Konto|}:</td>
										<td><input type="text" name="konto" id="konto" size="40" value="[KONTO]"></td>
										<td>
									</tr>
									<tr>
										<td>{|Gl&auml;ubiger ID|}:</td>
										<td><input type="text" name="glaeubiger" id="glaeubiger" size="40" value="[GLAEUBIGER]"></td>
										<td>
									</tr>
									<tr>
										<td>{|Lastschrift|}:</td>
										<td><input type="checkbox" name="lastschrift" id="lastschrift" value="1" [LASTSCHRIFT]></td>
									</tr>
								</table>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside inside-full-height">
							<fieldset>
								<legend>{|DATEV|}</legend>
								<table width="100%">
									<tr>
										<td width="150">{|Konto|}:</td>
										<td><input type="text" name="datevkonto" id="datevkonto" size="40" value="[DATEVKONTO]"></td>
										<td>
									</tr>
								</table>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside inside-full-height">
							<fieldset>
								<legend>{|CSV-Import|}</legend>
								<table width="100%">
									<tr>
										<td width="150">{|Erste Datenzeile|}:</td>
										<td><input type="text" name="importerstezeilenummer" id="importerstezeilenummer" size="15"
															 value="[IMPORTERSTEZEILENUMMER]">&nbsp;
											<i>{|Zeilennummer in der echte Daten stehen (Erste Zeile: 1)|}</i></td>
									</tr>
									<tr>
										<td width="150">{|Kodierung|}:</td>
										<td><select name="codierung" id="codierung">[CODIERUNG]</select></td>
									</tr>
									<tr>
										<td width="150">{|Trennzeichen|}:</td>
										<td><select name="importtrennzeichen" id="importtrennzeichen">[IMPORTTRENNZEICHEN]</select></td>
									</tr>
									<tr>
										<td width="150">{|Maskierung|}:</td>
										<td>
											<select name="importdatenmaskierung" id="importdatenmaskierung">
												[IMPORTDATENMASKIERUNG]
											</select>
										</td>
									</tr>
									<tr>
										<td width="150">{|Nullbytes entfernen|}:</td>
										<td><input type="checkbox" name="importnullbytes" id="importnullbytes" value="1" [IMPORTNULLBYTES]>
										</td>
									</tr>
									<tr>
										<td width="150">{|Letzte Zeilen ignorieren|}:</td>
										<td><input type="text" size="15" name="importletztenzeilenignorieren"
															 id="importletztenzeilenignorieren" value="[IMPORTLETZTENZEILENIGNORIEREN]"></td>
									</tr>
								</table>
								<br><br>
								<table>
									<tr>
										<td width="150">{|Spalte in CSV|}</td>
										<td>{|Spalten 1 bis n (Spaltennummer in CSV).|}</tr>
									<tr>
										<td width="150">{|Datum|}:</td>
										<td><input type="text" name="importfelddatum" id="importfelddatum" size="15"
															 value="[IMPORTFELDDATUM]">
											&nbsp;{|Eingabeformat|}:&nbsp;<input type="text" name="importfelddatumformat"
																													 id="importfelddatumformat" size="20"
																													 value="[IMPORTFELDDATUMFORMAT]">
											&nbsp;{|Ausgabeformat|}:&nbsp;<input type="text" name="importfelddatumformatausgabe"
																													 id="importfelddatumformatausgabe" size="20"
																													 value="[IMPORTFELDDATUMFORMATAUSGABE]">
											<br><i>{|Bsp. 24.12.2016 in CSV entspricht Eingabeformat %1.%2.%3 und Ausgabeformat %3-%2-%1 (Ausgabe muss immer auf diese Format gebracht werden YYYY-MM-DD)|}</i>
										</td>
									</tr>
									<tr>
										<td width="150">{|Betrag|}:</td>
										<td><input type="text" name="importfeldbetrag" id="importfeldbetrag" size="15"
															 value="[IMPORTFELDBETRAG]"></td>
									</tr>
									<tr>
										<td width="150">{|Extra Haben u. Soll|}:</td>
										<td><input type="checkbox" name="importextrahabensoll" id="importextrahabensoll" value="1" size="15"
															  [IMPORTEXTRAHABENSOLL] /></td>
									</tr>
									<tr>
										<td></td>
										<td>
											<table>
												<tr>
													<td width="150">{|Haben|}:</td>
													<td><input type="text" name="importfeldhaben" id="importfeldhaben" size="15"
																		 value="[IMPORTFELDHABEN]"></td>
												</tr>
												<tr>
													<td width="150">{|Soll|}:</td>
													<td><input type="text" name="importfeldsoll" id="importfeldsoll" size="15"
																		 value="[IMPORTFELDSOLL]"></td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td width="150">{|Buchungstext|}:</td>
										<td><input type="text" name="importfeldbuchungstext" id="importfeldbuchungstext" size="15"
															 value="[IMPORTFELDBUCHUNGSTEXT]">&nbsp;<i> {|Mit + mehre Spalten zusammenf&uuml;gen (aus dem Inhalt wird eine Pr&uuml;fsumme berechnet, daher so eindeutig wie m&ouml;glich machen.)|}</i>
										</td>
									</tr>
									<tr>
										<td width="150">{|W&auml;hrung|}:</td>
										<td><input type="text" name="importfeldwaehrung" id="importfeldwaehrung" size="15"
															 value="[IMPORTFELDWAEHRUNG]">&nbsp;<i>{|Ziel: EUR, USD|}</i></td>
									</tr>
									<tr>
										<td width="150">{|Haben/Soll Kennung|}:</td>
										<td><input type="text" name="importfeldhabensollkennung" id="importfeldhabensollkennung" size="15"
															 value="[IMPORTFELDHABENSOLLKENNUNG]">&nbsp;<i>{|Extra Spalte in der steht was der Betrag ist.|}</i>
										</td>
									</tr>
									<tr>
										<td width="150"></td>
										<td>
											<table>
												<tr>
													<td width="150">{|Markierung Eingang|}:</td>
													<td><input type="text" name="importfeldkennunghaben" id="importfeldkennunghaben" size="15"
																		 value="[IMPORTFELDKENNUNGHABEN]">&nbsp;<i>{|z.B. H oder +|}</i></td>
												</tr>
												<tr>
													<td>{|Markierung Ausgang|}:&nbsp;</td>
													<td><input type="text" name="importfeldkennungsoll" id="importfeldkennungsoll" size="15"
																		 value="[IMPORTFELDKENNUNGSOLL]">&nbsp;<i>{|z.B. S oder -|}</i></td>
												</tr>
											</table>
										</td>
									</tr>
								</table>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside inside-full-height">
							<fieldset>
								<legend>{|Live-Import|}</legend>
								<table id="liveimport-table">
									<tr>
										<td>{|Live-Import aktiv|}:</td>
										<td><input type="checkbox" name="liveimport_online" id="liveimport_online" value="1"
															 [LIVEIMPORT_ONLINE]></td>
									</tr>
									<tr>
										<td>{|Zeitraum|}:</td>
										<td>
											<select name="importperiode_in_hours" id="importperiode_in_hours">
												[IMPORTPERIODE_IN_HOURS]
											</select> {|Stunden|}
										</td>
									</tr>
									<tr>
										<td>
                        {|zu Zeiten|}:
										</td>
										<td>
											[SCHEDULER]
										</td>
									</tr>
									[BEFORELIVEIMPORT]
									<tr>
										<td width="150">{|Zugangsdaten|}:</td>
										<td><textarea rows="5" cols="100" name="liveimport" id="liveimport">[LIVEIMPORT]</textarea></td>
									</tr>
									[AFTERLIVEIMPORT]
									<tr>
										<td>{|Passwort Tresor|}:</td>
										<td><input type="button" value="{|Passwort setzen|}"
															 id="setpassword"/>
											<i>{|Der Inhalt des Passwort-Tresors kann in der Datenstruktur der Zugangsdaten über die Variable {PASSWORT} genutzt werden.|}</i>
										</td>
									</tr>
								</table>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside inside-full-height">
							<fieldset><legend>{|Kontenspezifische Einstellungen|}</legend>
								<table id="specific-settings">
									[SPECIFIC_SETTINGS]
								</table>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside inside-full-height">
							<fieldset>
								<legend>{|Prozessstarter|}</legend>
								<table>
									<tr>
										<td>{|Zahlungseing&auml;nge automatisch abholen|}:</td>
										<td><input type="checkbox" name="cronjobaktiv" id="cronjobaktiv" value="1" [CRONJOBAKTIV]></td>
									</tr>
									<tr>
										<td>{|Zahlungseing&auml;nge automatisch verbuchen|}:</td>
										<td><input type="checkbox" name="cronjobverbuchen" id="cronjobverbuchen" value="1"
															 [CRONJOBVERBUCHEN]></td>
									</tr>
								</table>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside inside-full-height">
							<fieldset>
								<legend>{|Startwert für Konto|}</legend>
								<table width="100%">
									<tr>
										<td width="150">{|Summieren|}:</td>
										<td><input type="checkbox" name="saldo_summieren" id="saldo_summieren" value="1" [SALDO_SUMMIEREN]>
										</td>
										<td>
									</tr>
									<tr>
										<td width="150">{|Datum Saldo|}:</td>
										<td><input type="text" name="saldo_datum" id="saldo_datum" size="40" value="[SALDO_DATUM]"></td>
										<td>
									</tr>
									<tr>
										<td width="150">{|Betrag Saldo|}:</td>
										<td><input type="text" name="saldo_betrag" id="saldo_betrag" size="40" value="[SALDO_BETRAG]"></td>
										<td>
									</tr>
								</table>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<input type="hidden" name="save" value="1" />
			<input type="submit" value="{|Speichern|}" style="float:right"/>
		</form>
	</div>
	<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->

<form method="post">
	<div id="editPasswortTresor" class="hide">
		<input type="hidden" id="e_id" value="[ID]"/>
		<table width="" cellspacing="0" cellpadding="0">
			<tr>
				<td width="110"><label for="e_passwort">{|Passwort|}:</label></td>
				<td><input type="password" id="e_passwort" name="e_passwort" size="40"/></td>
			</tr>
		</table>
	</div>
</form>

<script>

</script>

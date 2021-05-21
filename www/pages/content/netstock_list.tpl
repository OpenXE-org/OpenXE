<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">[TABTEXT]</a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]
		[TAB1]
		<div class="row">
			<div class="row-height">


				<div class="col-xs-12 col-md-4 col-md-height">
					<div class="inside inside-full-height">

						<fieldset><legend>{|Zugangsdaten|}</legend>
							<table>
								<tr><td colspan="2"><p>Entweder als FTP-Upload</p></td></tr>
								<tr><td width="200">FTP-Server:</td><td><input type="text" size="30" name="server" value="[SERVER]" id="server"></td></tr>
								<tr><td width="200">FTP-Benutzer:</td><td><input type="text" size="30" name="benutzer" value="[BENUTZER]" id="benutzer"></td></tr>
								<tr><td width="200">FTP-Passwort:</td><td><input type="password" size="30" name="passwort" value="[PASSWORT]" id="passwort"></td></tr>
								<tr><td colspan="2"><p>Oder als Ordner auf dem Server im Userdata-Verzeichnis</p></td></tr>
								<tr><td width="200">Name des Ordners:</td><td><input type="text" size="30" name="ordner" value="[ORDNER]" id="ordner"></td></tr>
							</table>
						</fieldset>

						<fieldset><legend>{|Konfiguration|}</legend>
							<table>
								<tr><td width="200">Lager f&uuml;r Intern (Beistellungen):</td><td><input type="text" size="30" name="lager" value="[LAGER]" id="lager"></td></tr>
								<tr><td width="200">Lager f&uuml;r Extern:</td><td><input type="text" size="30" name="lagerproduktion" value="[LAGERPRODUKTION]" id="lagerproduktion"></td></tr>
							</table>
						</fieldset>

						<fieldset><legend>Über Netstock CSV Export</legend>
							<p>Die Anwendung erzeugt csv-Dateien für: Lager/Filialen (location.csv), Lieferanten (supplier.csv), Artikelstamm (master.csv), Bestand je Lagerort (stock.csv), Verkauf & Verbrauch (sales.csv), Offene Lieferanten oder Produktionsbestellungen (po.csv)
								Diese werden entweder per FTP übertragen oder in einen Ablageordner auf dem Server hinterlegt.
							</p>
							<p>
								Die Anbindung an Netstock wird direkt durch die Netstock Europe GmbH unterstützt.
								Bei Fragen, wenden Sie sich daher bitte direkt an Ihren Ansprechpartner bei Netstock oder das Support-Team unter support@netstock-europe.com oder Telefon +49 231 5869 7109
							</p>
						</fieldset>


					</div>
				</div>

				<div class="col-xs-12 col-md-8 col-md-height">
					<div class="inside inside-full-height">
						<fieldset><legend>{|Live Monitor FTP|}</legend>


							<table class="mkTable">

								<tr>
									<th>Nr.</th>
									<th>Datum</th>
									<th>Name</th>
									<th>Größe</th>
									<th>Datum</th>
									<th>Name</th>
									<th>Größe</th>
								</tr>
								[BACKUPROW]

							</table>

						</fieldset>
					</div>
				</div>

			</div>
		</div>

		[TAB1NEXT]
	</div>

	<!-- tab view schließen -->
</div>


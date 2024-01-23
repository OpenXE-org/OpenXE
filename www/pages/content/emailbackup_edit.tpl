<div id="tabs">
	<ul>
		<li>
			<a href="#tabs-1"></a>
		</li>
	</ul>
	<!-- Example for multiple tabs
    <ul hidden">
        <li><a href="#tabs-1">First Tab</a></li>
        <li><a href="#tabs-2">Second Tab</a></li>
    </ul>
    -->
	<div id="tabs-1"> [MESSAGE]
		<form action="" method="post"> [FORMHANDLEREVENT]
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside inside-full-height">
							<fieldset>
								<legend>{|Allgemein|}</legend>
								<input type="submit" name="submit" value="Speichern" style="float:right" />
								<table width="100%" border="0" class="mkTableFormular">
									<tr>
										<td width="200">{|E-Mail-Adresse|}:</td>
										<td>
											<input type="text" name="email" value="[EMAIL]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|Angezeigter Name|}:</td>
										<td>
											<input type="text" name="angezeigtername" value="[ANGEZEIGTERNAME]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|Interne Beschreibung|}:</td>
										<td>
											<input type="text" name="internebeschreibung" value="[INTERNEBESCHREIBUNG]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|Benutzername|}:</td>
										<td>
											<input type="text" name="benutzername" value="[BENUTZERNAME]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|Passwort|}:</td>
										<td>
											<input type="password" name="passwort" value="[PASSWORT]" size="40">
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
								<legend>{|SMTP|}</legend>
								<table width="100%" border="0" class="mkTableFormular">
									<tr>
										<td width="200">{|SMTP benutzen|}:</td>
										<td>
                                            <input type="checkbox" name="smtp_extra" value="1" [SMTP_EXTRA]>
                                        </td>
									</tr>
									<tr>
										<td>{|Server|}:</td>
										<td>
											<input type="text" name="smtp" value="[SMTP]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|Verschl&uuml;sselung|}:</td>
										<td>
											<select name="smtp_ssl">
											    [SMTP_SSL_SELECT]
                                            </select>
                                        </td>
									</tr>
									<tr>
										<td>{|Port|}:</td>
										<td>
											<input type="text" name="smtp_port" value="[SMTP_PORT]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|Authtype|}:</td>
										<td>
                                            <select name="smtp_authtype">
											    [SMTP_AUTHTYPE_SELECT]
                                            </select>
                                        </td>
									</tr>
									<tr>
										<td>{|Authparam|}:</td>
										<td>
											<input type="text" name="smtp_authparam" value="[SMTP_AUTHPARAM]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|Client alias|}:</td>
										<td>
											<input type="text" name="client_alias" value="[CLIENT_ALIAS]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|SMTP Debug|}:</td>
										<td>
											<input type="checkbox" name="smtp_loglevel" value="1" [SMTP_LOGLEVEL]>
										</td>
									</tr>
									<tr>
										<td width="50">Testmail:</td>
										<td>
											<input type="submit" form="smtp_test" value="Testmail senden" id="testmail-senden-button">&nbsp;<i>Bitte erst speichern und dann senden!</i>
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
								<legend>{|IMAP|}</legend>
								<table width="100%" border="0" class="mkTableFormular">
									<tr>
										<td width="200">{|IMAP server|}:</td>
										<td>
											<input type="text" name="server" value="[SERVER]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|Gesendete Mails in IMAP-Ordner legen|}:</td>
										<td>
											<input type="checkbox" name="imap_sentfolder_aktiv" value="1" [IMAP_SENTFOLDER_AKTIV]>
										</td>
									</tr>
									<tr>
										<td>{|IMAP-Ordner|}:</td>
										<td>
											<input type="text" name="imap_sentfolder" value="[IMAP_SENTFOLDER]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|IMAP-Port|}:</td>
										<td>
											<input type="text" name="imap_port" value="[IMAP_PORT]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|IMAP-Typ|}:</td>
										<td>
                                            <select name="imap_type">
                                                [IMAP_TYPE_SELECT]
                                            </select>
                                        </td>
									</tr>
									<tr>
										<td width="50">Testmail:</td>
										<td>
											<input type="submit" form="imap_test" value="IMAP testen" id="testimap-button">&nbsp;<i>Bitte erst speichern und dann testen!</i>
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
								<legend>{|Archiv|}</legend>
								<table width="100%" border="0" class="mkTableFormular">
									<tr>
										<td width="200">{|E-Mailarchiv aktiv|}:</td>
										<td>
                                            <input type="checkbox" name="emailbackup" value="1" [EMAILBACKUP]>
										</td>
									</tr>
									<tr>
										<td>{|Löschen nach wievielen Tagen?|}:</td>
										<td>
											<input type="text" name="loeschtage" value="[LOESCHTAGE]" size="40">
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
								<legend>{|Ticketsystem|}</legend>
								<table width="100%" border="0" class="mkTableFormular">
									<tr>
										<td width="200">{|Mails als Ticket importieren|}:</td>
										<td>
                                            <input type="checkbox" name="ticket" value="1" [TICKET]>
										</td>
									</tr>
									<tr>
										<td>{|Projekt f&uuml;r Ticket|}:</td>
										<td>
											<input type="text" id="ticketprojekt" name="ticketprojekt" value="[TICKETPROJEKT]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|Warteschlange f&uuml;r Ticket|}:</td>
										<td>
											<input type="text" id="ticketqueue" name="ticketqueue" value="[TICKETQUEUE]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|E-Mails ab Datum importieren|}:</td>
										<td>
											<input type="text" name="abdatum" id="abdatum" value="[ABDATUM]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|E-Mail nach Import l&ouml;schen|}:</td>
										<td>
                                            <input type="checkbox" name="ticketloeschen" value="1" [TICKETLOESCHEN]>
										</td>
									</tr>
									<tr>
										<td>{|Ticket auf abgeschlossen setzen|}:</td>
										<td>
                                            <input type="checkbox" name="ticketabgeschlossen" value="1" [TICKETABGESCHLOSSEN]>
										</td>
									</tr>
									<tr>
										<td>{|Ausgehende E-Mailadresse|}:</td>
										<td>
                                        	<input type="checkbox" name="ticketemaileingehend" value="1" [TICKETEMAILEINGEHEND]>
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
								<legend>{|Sonstiges|}</legend>
								<table width="100%" border="0" class="mkTableFormular">
									<tr>
										<td>{|Automatisch antworten|}:</td>
										<td>
                                            <input type="checkbox" name="autoresponder" value="1" [AUTORESPONDER]>
										</td>
									</tr>
                                	<tr>
										<td width="200">{|Nur eine Antwort pro Tag|}:</td>
										<td>
                                            <input type="checkbox" name="autosresponder_blacklist" value="1" [AUTOSRESPONDER_BLACKLIST]>
										</td>
									</tr>								
									<tr>
										<td>{|Automatische Antwort Betreff|}:</td>
										<td>
											<textarea id="autoresponderbetreff" name="autoresponderbetreff" rows="6" style="width:100%;">[AUTORESPONDERBETREFF]</textarea>
										</td>
									</tr>
									<tr>
										<td>{|Automatische Antwort Text|}:</td>
										<td>
											<textarea id="autorespondertext" name="autorespondertext" rows="6" style="width:100%;">[AUTORESPONDERTEXT]</textarea>
										</td>
									</tr>
									<tr>
										<td>{|Eigene Signatur verwenden|}:</td>
										<td>
                                            <input type="checkbox" name="eigenesignatur" value="1" [EIGENESIGNATUR]>
										</td>
									</tr>
									<tr>
										<td>{|Signatur|}:</td>
										<td>
											<textarea id="signatur" name="signatur" rows="6" style="width:100%;">[SIGNATUR]</textarea>
										</td>
									</tr>								
                                	<tr>
										<td>{|Adresse|}:</td>
										<td>
											<input type="text" id="adresse" name="adresse" value="[ADRESSE]" size="40">
										</td>
									</tr>
									<tr>
										<td>{|Projekt|}:</td>
										<td>
											<input type="text" id="projekt" name="projekt" value="[PROJEKT]" size="40">
										</td>
									</tr>
									<tr>
    									<td>{|Firma|}:</td>
										<td>
											<input type="text" name="firma" value="[FIRMA]" size="40">
										</td>
                                	<tr>
										<td>{|Gesch&auml;ftsbriefvorlage|}:</td>
										<td>
											<input type="text" name="geschaeftsbriefvorlage" value="[GESCHAEFTSBRIEFVORLAGE]" size="40">
										</td>
									</tr>
									</tr>																	
								</table>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<input type="submit" name="submit" value="Speichern" style="float:right" />
	    </form>
	</div>	
<form id="smtp_test" action="index.php">
	<input type="text" name="module" value="emailbackup" style="display:none">
	<input type="text" name="action" value="test_smtp" style="display:none">
	<input type="text" name="id" value="[ID]" style="display:none"> 
</form>
<form id="imap_test" action="index.php">
	<input type="text" name="module" value="emailbackup" style="display:none">
	<input type="text" name="action" value="test_imap" style="display:none">
	<input type="text" name="id" value="[ID]" style="display:none">
</form>

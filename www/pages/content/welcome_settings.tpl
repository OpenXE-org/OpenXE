<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1">{|Übersicht|}</a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]

		<div class="row">
			<div class="row-height">

				<div class="col-xs-12 col-md-6 col-md-height">
					<div class="inside inside-full-height">
            <fieldset>
              <legend>{|Passwort|}</legend>
              <form action="index.php?module=welcome&action=settings&cmd=password-change" method="post">
                <table>
                  <tr>
                    <td>{|Passwort|}:</td>
                    <td><input type="password" id="password" name="password" value="[PASSWORD]" size="30"></td>
                  </tr>
                  <tr>
                    <td>{|Passwort wiederholen|}:</td>
                    <td><input type="password" id="repassword" name="passwordre" size="30" value="[PASSWORD]"></td>
                  </tr>
                  <tr>
                    <td></td>
                    <td><input type="submit" id="submit_password" value="{|Passwort &auml;ndern|}" name="submit_password"></td>
                  </tr>
                </table>
              </form>
            </fieldset>
					</div>
				</div>

				<div class="col-xs-12 col-md-6 col-md-height">
					<div class="inside inside-full-height">
            <fieldset>
              <legend>{|Profilbild|}</legend>

              <form action="index.php?module=welcome&action=settings&cmd=picture-delete" method="post">
                <table>
                  <tr>
                    <td valign="bottom">
                      <div>[DATEI]</div>
                    </td>
                    <td valign="bottom">
                      [VORPROFILBILDLOESCHEN]
                      <input class="btnBlue" type="submit" value="{|Aktuelles Profilbild löschen|}" name="delete_datei">
                      [NACHPROFILBILDLOESCHEN]
                    </td>
                  </tr>
                </table>
              </form>

              <form action="index.php?module=welcome&action=settings&cmd=picture-upload" method="post" enctype="multipart/form-data">
                [UPLOADERROR]
                <table>
                  <tr>
                    <td><input type="file" name="upload"></td>
                  </tr>
                  <tr>
                    <td><input type="submit" value="{|Neues Profilbild hochladen|}" name="submit_datei"></td>
                  </tr>
                </table>
              </form>

            </fieldset>
					</div>
				</div>

			</div>
		</div>
		<div class="row">
			<div class="row-height">

        <div class="col-xs-12 col-md-6 col-md-height">
					<div class="inside inside-full-height">
            <form action="index.php?module=welcome&action=settings&cmd=settings-save" method="post">
              <fieldset>
                <legend>{|Einstellungen|}</legend>
                <table class="mkTableFormular">
                  <tr>
                    <td>{|Startseite|}:</td>
                    <td><input type="text" name="startseite" value="[STARTSEITE]" size="40"></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td></td>
                    <td>{|Beispiel|}: <i>index.php?module=welcome&amp;action=pinwand</i> ({|f&uuml;r Pinnwand|})</td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>{|Sprache|}:</td>
                    <td><select name="sprachebevorzugen" id="sprachebevorzugen">[SPRACHEBEVORZUGEN]</select></td>
                  </tr>
                  <tr>
                    <td>{|Eigene Kalenderfarbe|}:</td>
                    <td><input type="text" name="defaultcolor" id="defaultcolor" value="[DEFAULTCOLOR]" size="80"></td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>{|Chat|}:</td>
                    <td>
                      <label>
                        <input type="checkbox" value="1" name="chat_popup" [CHAT_POPUP]>
                        {|in eigenem Fenster öffnen|}
                      </label>
                    </td>
                    <td></td>
                  </tr>
                  <tr>
                    <td>{|Telefon|}:</td>
                    <td>
                      <label>
                        <input type="checkbox" value="1" name="callcenter_notification" [CALLCENTER_NOTIFICATION]>
                        {|Benachrichtigungen aktivieren|}
                      </label>
                    </td>
                    <td></td>
                  </tr>
									<tr>
                    <td></td>
                    <td><input type="submit" value="{|Einstellungen speichern|}" name="submit_startseite"></td>
                    <td></td>
                  </tr>
                </table>
              </fieldset>
            </form>
					</div>
				</div>

				<div class="col-xs-12 col-md-6 col-md-height" style="width: 25%">
					<div class="inside inside-full-height">
						<fieldset>
							<legend>{|Mobile Apps|}</legend>
							<table width="350" border="0">
								<tr>
									<td valign="bottom">[MOBILE_APP_QRCODE]</td>
									<td valign="bottom">&nbsp;</td>
									<td valign="bottom">[MOBILE_APP_DESCRIPTION]</td>
								</tr>
								<tr>
									<td colspan="3">
										<form action="index.php?module=welcome&action=settings&cmd=mobile-apps-account" method="post">
										[MOBILE_APP_BUTTON]
										</form>
									</td>
								</tr>
								<tr>
									<td colspan="3">
										<p>Sie finden unsere Apps in diesen App-Stores:</p>
										<p>
											<a href="https://apps.apple.com/us/app/id1505513991" target="_blank" rel="noopener noreferrer"><img src="images/appstore-badge-apple.svg" alt="Apple App Store" width="120" height="40"></a>
											<a href="https://play.google.com/store/apps/details?id=com.xentral.boss_app&hl=de" target="_blank" rel="noopener noreferrer"><img src="images/appstore-badge-android.png" alt="Android App Store" height="40"></a>
										</p>
									</td>
								</tr>
							</table>
						</fieldset>
					</div>
				</div>



				<div class="col-xs-12 col-md-6 col-md-height" style="width: 25%">
					<div class="inside inside-full-height">
						<fieldset>
							<legend>{|Zwei-Faktor-Authentifizierung|}</legend>
							<div style="width: 100%">
								<div style="display: inline-block;">
									[TOTP_QR_HTML]
								</div>
								<br>
								<div style="display: inline-block; vertical-align: top">
									<button class="button button-primary" id="[TOTP_TOGGLE_ID]" >[TOTP_TOGGLE_VALUE]</button>
									<br>
									<!-- <button style="width: 200px;" class="button button-secondary" id="totp_regenerate" >Schl&uuml;ssel neu generieren</button> -->
									<br>

									[TOTP_KEY_HTML]

									<br><br>
									<div>
										<a href="https://apps.apple.com/de/app/google-authenticator/id388497605" target="_blank" rel="noopener noreferrer"><img src="images/appstore-badge-apple.svg" alt="Apple App Store" width="120" height="40" ></a>
										<a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=de" target="_blank" rel="noopener noreferrer"><img src="images/appstore-badge-android.png" alt="Android App Store" height="40" ></a>
									</div>
								</div>
							</div>

						</fieldset>
					</div>
				</div>

			</div>
		</div>

		<div class="row">
			<div class="row-height">

				<div class="col-xs-12 col-md-6 col-md-height">
					<div class="inside inside-full-height">
						<form action="index.php?module=welcome&action=settings&cmd=googlecalendar-save" method="post">
							<fieldset>
								<legend>{|Mein Google Kalender|}</legend>
								<table class="mkTableFormular">
									[MSG_GOOGLE_CALENDAR]
									<tr>
										<td>{|Google Kalender|}:</td>
										<td><input type="text" id="txt_selected_calendar" name="google_calendar" value="[GOOGLE_CALENDAR]" disabled size="40"></td>
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="submit" value="(1) {|Autorisieren|}" name="authorize_google_calendar" [GOOGLE_AUTH_DISABLE]>
											<input type="submit" value="(2) {|Termine Importieren|}" name="import_google_calendar" [GOOGLE_SYNC_DISABLE]>
										</td>
										<td></td>
									</tr>
								</table>
							</fieldset>
						</form>
					</div>
				</div>

				<div class="col-xs-12 col-md-6 col-md-height">
					<div class="inside inside-full-height">
						<form action="index.php?module=welcome&action=settings&cmd=gmail-save" method="post">
							<fieldset>
								<legend>{|Mein GoogleMail Konto|}</legend>
								<table class="mkTableFormular">
									[MSG_NO_GMAILAPI]
									<tr>
										<td>{|Google E-Mail|}:</td>
										<td><input type="text" id="txt_gmail_address" name="gmail_address" value="[GMAIL_ADDRESS]" [GMAIL_ADDRESS_DISABLE] size="40"></td>
									</tr>
									<tr>
										<td></td>
										<td>
											<input type="submit" value="(1) {|Autorisieren|}" name="submit_authorize_gmail" [GMAIL_AUTH_DISABLE]>
											<input type="submit" value="(2) {|Test Email|}" name="submit_testmail_gmail" [GMAIL_TESTMAIL_DISABLE]>
											<!--<input type="submit" value="{|Trennen|}" name="submit_disconnect_gmail" [GMAIL_DISCONNECT_DISABLE]>-->
										</td>
										<td></td>
									</tr>
								</table>
							</fieldset>
						</form>
					</div>
				</div>


			</div>
		</div>

	</div>
</div>

<div id="dialog-confirm" title="Code scannen" style="display:none">
	<p><span class="ui-icon ui-icon-alert" style="float:left; margin:12px 12px 20px 0;"></span>Bitte scannen Sie den QR-Code mit Google Authenticator oder einer anderen TOTP-App, da Sie sich sonst nicht anmelden können werden!</p>

	<div style="text-align: center">
		<div id="div_qr" style="display: inline-block"></div>
		<br>
	</div>

</div>

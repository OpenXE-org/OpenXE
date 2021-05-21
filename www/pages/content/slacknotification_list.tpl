<div id="tabs">
	<ul>
		<li><a href="#tabs-1"></a></li>
	</ul>
	<div id="tabs-1">
		[MESSAGE]
		<form action="" method="post" name="eprooform">

			<fieldset>
				<legend>{|Auftrag versendet|}</legend>
				<table width="100%" border="0" class="mkTableFormular">
					<tr>
						<td>Webhook-URL:</td>
						<td><input type="text" name="slacknotification_order_sent_webhook_url" value="[ORDERSENTWEBHOOKURL]" size="60"></td>
					</tr>
					<tr>
						<td>Benachrichtigungstext:</td>
						<td>
							<input type="text" name="slacknotification_order_sent_text_template" value="[ORDERSENTTEXTTEMPLATE]" size="60">
							<i>Beispiel: <code>Auftrag &#123;BELEGNUMMER&#125; für _&#123;KUNDENNAME&#125;_ (&#123;KUNDENNUMMER&#125;)
							  im Wert von *&#123;BETRAG&#125;* versendet.</code></i>
						</td>
					</tr>
					<tr>
						<td>Bild-URL:</td>
						<td><input type="text" name="slacknotification_order_sent_image_url" value="[ORDERSENTIMAGEURL]" size="60"></td>
					</tr>
				</table>
			</fieldset>

			<fieldset>
				<legend>{|Angebot versendet|}</legend>
				<table width="100%" border="0" class="mkTableFormular">
					<tr>
						<td>Webhook-URL:</td>
						<td><input type="text" name="slacknotification_offer_sent_webhook_url" value="[OFFERSENTWEBHOOKURL]" size="60"></td>
					</tr>
					<tr>
						<td>Benachrichtigungstext:</td>
						<td>
							<input type="text" name="slacknotification_offer_sent_text_template" value="[OFFERSENTTEXTTEMPLATE]" size="60">
							<i>Beispiel: <code>Angebot {BELEGNUMMER} für _{KUNDENNAME}_ ({KUNDENNUMMER}) im Wert von *{BETRAG}*
								versendet.</code></i>
						</td>
					</tr>
					<tr>
						<td>Bild-URL:</td>
						<td><input type="text" name="slacknotification_offer_sent_image_url" value="[OFFERSENTIMAGEURL]" size="60"></td>
					</tr>
				</table>
			</fieldset>

			<fieldset>
				<legend>{|Variablen und Formatierung|}</legend>
				<table width="100%" border="0" class="mkTableFormular">
					<tr>
						<td>Variablen</td>
						<td>
							<code>&#123;BELEGNUMMER&#125;</code>,
							<code>&#123;KUNDENNAME&#125;</code>,
							<code>&#123;KUNDENNUMMER&#125;</code>,
							<code>&#123;BEARBEITER&#125;</code>,
							<code>&#123;BETRAG&#125;</code>
						</td>
					</tr>
					<tr>
						<td>Formatierung</td>
						<td>
							<p>
								<strong><code>*fett*</code></strong>,
								<em><code>_kursiv_</code></em> und
								<strike><code>~durchgestrichen~</code></strike>
							</p>
						</td>
					</tr>
					<tr>
						<td>&nbsp;</td>
						<td><input type="submit" name="submit" value="Speichern"/></td>
					</tr>
				</table>
			</fieldset>

		</form>
		[TAB1NEXT]
	</div>
</div>

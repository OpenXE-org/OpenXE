<div id="ocr-settings-dialog">
	<table>
		<tr>
			<th></th>
			<th>Suche nach &hellip;</th>
			<th>und nimm den Wert &hellip;</th>
		</tr>
		<tr>
			<td><label for="setting-invoice-number-term">{|Rechnungsnummer|}</label></td>
			<td><input id="setting-invoice-number-term" name="invoice_number[term]" type="text" value=""></td>
			<td>
				<select name="invoice_number[direction]" id="setting-invoice-number-direction">
					<option value="right">Rechts davon</option>
					<option value="left">Links davon</option>
					<option value="above">Oberhalb davon</option>
					<option value="below">Unterhalb davon</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="setting-invoice-date-term">{|Rechnungsdatum|}</label></td>
			<td><input id="setting-invoice-date-term" name="invoice_date[term]" type="text" value=""></td>
			<td>
				<select name="invoice_date[direction]" id="setting-invoice-date-direction">
					<option value="right">Rechts davon</option>
					<option value="left">Links davon</option>
					<option value="above">Oberhalb davon</option>
					<option value="below">Unterhalb davon</option>
				</select>
			</td>
		</tr>
		<tr>
			<td><label for="setting-total-gross-term">{|Betrag/Total (Brutto)|}</label></td>
			<td><input id="setting-total-gross-term" name="total_gross[term]" type="text" value=""></td>
			<td>
				<select name="total_gross[direction]" id="setting-total-gross-direction">
					<option value="right">Rechts davon</option>
					<option value="left">Links davon</option>
					<option value="above">Oberhalb davon</option>
					<option value="below">Unterhalb davon</option>
				</select>
			</td>
		</tr>
	</table>
</div>

<div id="ocr-result-dialog">
	<p>Folgende Daten wurden erkannt:</p>
	<input type="hidden" id="ocr-val-result-handle" value="">
	<table>
		<tr id="ocr-row-nummer" class="ocr-row">
			<td><label for="ocr-val-nummer">{|Rechnungsnummer|}</label></td>
			<td><input id="ocr-val-nummer" type="text" value=""></td>
			<td><input id="ocr-unchanged-nummer" type="hidden" value=""></td>
		</tr>
		<tr id="ocr-row-datum" class="ocr-row">
			<td><label for="ocr-val-datum">{|Rechnungsdatum|}</label></td>
			<td><input id="ocr-val-datum" type="text" value=""></td>
			<td><input id="ocr-unchanged-datum" type="hidden" value=""></td>
		</tr>
		<tr id="ocr-row-gesamt" class="ocr-row">
			<td><label for="ocr-val-gesamt">{|Betrag/Total (Brutto)|}</label></td>
			<td><input id="ocr-val-gesamt" type="text" value=""></td>
			<td><input id="ocr-unchanged-gesamt" type="hidden" value=""></td>
		</tr>
		<tr id="ocr-row-steuer" class="ocr-row">
			<td><label for="ocr-val-steuer">{|USt. normal|}</label></td>
			<td><input id="ocr-val-steuer" type="text" value=""></td>
			<td><input id="ocr-unchanged-steuer" type="hidden" value=""></td>
		</tr>
		<tr id="ocr-row-waehrung" class="ocr-row">
			<td><label for="ocr-val-waehrung">{|Währung|}</label></td>
			<td><input id="ocr-val-waehrung" type="text" value=""></td>
		</tr>
		<tr id="ocr-row-empty-result" class="ocr-row">
			<td colspan="2"><strong>Keine Zuordnung gefunden</strong></td>
		</tr>
	</table>
	<input id="ocr-val-result-handle" type="hidden" value="">
</div>

<div id="ocr-api-registration-dialog">
	<div id="ocr-api-registration-error" class="error"></div>
	<p>Für die automatische Erkennung von Belegen ist eine Registrierung notwendig. Bitte bestätigen Sie hier Ihre Daten.</p>
	<p>Mit der Registrierung akzeptieren Sie dass Dokumente über die <a href="https://scanbot.io/sdk" target="_blank">Scanbot API</a>
		verarbeitet werden dürfen. Aktuell befindet sich das Feature in der Erprobungsphase und kann daher kostenlos genutzt werden.
	</p>
	<table>
		<tr>
			<td>E-Mail</td>
			<td><input type="text" id="scanbot-registration-mail" value="[FIRMAMAIL]" size="30"></td>
		</tr>
		<tr>
			<td>Firma</td>
			<td><input type="text" id="scanbot-registration-name" value="[FIRMANAME]" size="30"></td>
		</tr>
	</table>
</div>

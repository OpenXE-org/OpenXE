<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		<div id="msgwrapper">
		[MESSAGE]
		</div>
		<form method="post" id="frmappnew">
			<fieldset>
				<legend></legend>
				<table>
					<tr>
						<td>
							<label for="data">{|Daten aus Shop|}:</label>
						</td>
						<td>
							<textarea id="data" name="data" cols="50" rows="20"></textarea>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" name="create" class="button-primary" />
						</td>
					</tr>
				</table>
			</fieldset>

			[TAB1]
			<fieldset>
				<legend>{|Beispiel Shopware6|}:</legend>
					<div>{"token":"token123","shoptype":"shopimporter_shopware6","url":"https:\/\/example.com\/backurl","data":{"shopwareUserName":"username","shopwarePassword":"userpassword","shopwareUrl":"https:\/\/example.com\/shopapi"}}</div>
			</fieldset>
			<fieldset>
				<legend>{|Beispiel Shopify|}:</legend>
			<div>{"token":"tokenABC","shoptype":"shopimporter_shopify","url":"https:\/\/example.com\/backurl","data":{"ShopifyURL":"https:\/\/example.com\/shopapi","ShopifyAPIKey":"ABC","ShopifyPassword":"****"}}</div>
			</fieldset>
			<fieldset>
				<legend>{|Bespiel Rückgabe apiKey|}:</legend>
			<div>
				{"success":true,"shopid":"8","api_account_id":"2","apiname":"Shopware6 2","apikey":"c3e3a80d2bbf55f236d82fad421ac08c6e2bf3ec","server":"http:\/\/localhost:80\/git\/20.2\/www","api_auth":"http:\/\/localhost:80\/git\/20.2\/www\/api\/shopimport\/auth","api_syncstorage":"http:\/\/localhost:80\/git\/20.2\/www\/api\/shopimport\/syncstorage\/{articlenumber_base64}","api_articletoxentral":"http:\/\/localhost:80\/git\/20.2\/www\/api\/shopimport\/articletoxentral\/{articlenumber_base64}","api_articletoshop":"http:\/\/localhost:80\/git\/20.2\/www\/api\/shopimport\/articletoshop\/{articlenumber_base64}","item_link":"http:\/\/localhost:80\/git\/20.2\/www\/index.php?module=onlineshops\u0026action=itemlink\u0026id=8\u0026sid={articlenumber_base64}","order_link":"http:\/\/localhost:80\/git\/20.2\/www\/index.php?module=onlineshops\u0026action=orderlink\u0026id=8\u0026sid={ordernumber_base64}"}
			</div>
			</fieldset>
		</form>
		[TAB1NEXT]
	</div>

	<!-- tab view schließen -->
</div>


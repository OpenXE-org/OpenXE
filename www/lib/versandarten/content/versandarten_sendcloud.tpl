<div class="container-fluid">
	<div class="row">
		<div>
			<form action="" method="post">
			[ERROR]
			<h1>{|Paketmarken Drucker f&uuml;r|} SendCloud</h1>
		</div>
		<div class="col-md-6">
			<h2>{|Empf&auml;nger|}</h2>
			<table>
			<tr><td>{|Name|}:</td><td><input type="text" size="36" value="[NAME]" name="name" id="name"><script type="text/javascript">document.getElementById("name").focus(); </script></td></tr>
			<tr><td>{|Name 2|}:</td><td><input type="text" size="36" value="[NAME2]" name="name2"></td></tr>
			<tr><td>{|Name 3|}:</td><td><input type="text" size="36" value="[NAME3]" name="name3"></td></tr>

				<tr><td>{|Land|}:</td><td><select name="land">[LAND]</select></td></tr>
			<tr><td>{|PLZ/Ort|}:</td><td><input type="text" name="plz" size="5" value="[PLZ]">&nbsp;<input type="text" size="30" name="ort" value="[ORT]"></td></tr>
			<tr><td>{|Strasse/Hausnummer|}:</td><td><input type="text" size="30" value="[STRASSE]" name="strasse">&nbsp;<input type="text" size="5" name="hausnummer" value="[HAUSNUMMER]"></td></tr>

			<tr><td>{|E-Mail|}:</td><td><input type="text" size="36" value="[EMAIL]" name="email"></td></tr>
			<tr><td>{|Telefon|}:</td><td><input type="text" size="36" value="[TELEFON]" name="phone"></td></tr>

			<tr><td>{|Bundesland|}:</td><td><input type="text" size="36" value="[BUNDESLAND]" name="state"></td></tr>
			</table>
		</div>
		<div class="col-md-6">
			<h2>{|Paket|}</h2>
			<table>
				<tr><td>{|Gewicht (in kg)|}:</td><td><input type="text" value="[WEIGHT]" name="weight"></td></tr>
				<tr><td>{|H&ouml;he (in cm)|}:</td><td><input type="text" size="10" value="[HEIGHT]" name="height"></td></tr>
				<tr><td>{|Breite (in cm)|}:</td><td><input type="text" size="10" value="[WIDTH]" name="width"></td></tr>
				<tr><td>{|L&auml;nge (in cm)|}:</td><td><input type="text" size="10" value="[LENGTH]" name="length"></td></tr>
				<tr><td>{|Produkt|}:</td><td>[METHODS]</td></tr>
			</table>
		</div>
		<div class="clearfix"></div>
		<div class="col-md-12">
			<h2>{|Bestellung|}</h2>
			<table>
				<tr><td>{|Bestellnummer|}:</td><td><input type="text" size="36" value="[ORDERNUMBER]" name="bestellnummer"></td></tr>
				<tr><td>{|Rechnungsnummer|}:</td><td><input type="text" size="36" value="[INVOICENUMBER]" name="rechnungsnummer"></td></tr>
				<tr><td>{|Sendungsart|}:</td><td><select name="sendungsart">
							<option value="0">{|Geschenk|}</option>
							<option value="1">{|Dokumente|}</option>
							<option value="2" selected>{|Kommerzielle Waren|}</option>
							<option value="3">{|Erprobungswaren|}</option>
							<option value="4">{|Rücksendung|}</option>
						</select></td></tr>
				<tr><td>{|Versicherungssumme|}:</td><td><input type="text" size="10" id="versicherungssumme" name="versicherungssumme" value="[VERSICHERUNGSSUMME]" /></td></tr>
			</table>
		</div>
		<div>
			<input class="btnGreen" type="submit" value="{|Paketmarke drucken|}" name="drucken">&nbsp;
			[TRACKINGMANUELL]
			<input type="button" value="{|Andere Versandart auswählen|}" name="anders">&nbsp;
		</div>
	</div>
</div>
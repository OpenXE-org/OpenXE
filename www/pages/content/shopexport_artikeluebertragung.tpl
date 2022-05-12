<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
	<div id="tabs-1">
		<form action="" method="post">
			[MESSAGE]
			[IMPORTERINFO]
			[IMPORTERINFO2]
			<div class="row">
				<div class="row-height">
					<div class="col-xs-12 col-md-12 col-md-height">
						<div class="inside_white inside-full-height">
							<fieldset><legend>Best&auml;tigung</legend>
							<div class="warning"><input type="checkbox" valie="1" name="bestaetigen" />
								{|Ich best&auml;tige, dass ich wirklich die Artikel &uuml;bertragen will. Bei falsch eingetragenen Artikeldaten kann dies zu <b>fehlerhaften Shopartikel</b> f&uuml;hren.<br />Pr&uuml;fen Sie erst verschiedene Artikelkonfigurationen manuell, bevor Sie einen Massenexport ausf&uuml;hren.|}

							</div>
							</fieldset>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
			<div class="row-height">
			<div class="col-xs-12 col-md-9 col-md-height">
			<div class="inside_white inside-full-height">
				<fieldset class="white">
					<legend></legend>
					[TAB1]
				</fieldset>
			</div>
			</div>
			<div class="col-xs-12 col-md-3 col-md-height">
			<div class="inside inside-full-height">
				<fieldset>
					<legend>{|Aktionen|}</legend>
					
						<input type="submit" class="btnBlueNew" value="{|Lagerzahlcache zur&uuml;cksetzen|}" name="delcache"><br>
						<input type="submit" class="btnBlueNew" value="{|Lagerzahlcache für Shopartikel mit Menge 0 zur&uuml;cksetzen|}" name="delzerostockcache"><br>
						<input type="submit" class="btnBlueNew" value="{|Artikelcache zur&uuml;cksetzen|}" name="delarticlecache"><br>
						<input type="submit" class="btnBlueNew" value="{|Alle Artikel laden|}" name="alle" onclick="if(!confirm('{|Wollen Sie wirklich alle Artikel an den Shop übertragen? Eventuell werden hier auch Artikeltexte, Preise, Bilder, Eigenschaften, Kategorien, etc. übertragen und überschrieben. Bitte prüfen Sie das Verhalten vorher an einigen Artikel. Bitte nehmen Sie in jedemfall vorab eine Sicherung im Shop vor.|}')) return false;"><br>
  					<input type="submit" class="btnBlueNew" value="{|Alle ge&auml;nderten Artikel laden|}" name="allchanged" onclick="if(!confirm('{|Wollen Sie wirklich alle Artikel an den Shop übertragen? Eventuell werden hier auch Artikeltexte, Preise, Bilder, Eigenschaften, Kategorien, etc. übertragen und überschrieben. Bitte prüfen Sie das Verhalten vorher an einigen Artikel. Bitte nehmen Sie in jedemfall vorab eine Sicherung im Shop vor.|}')) return false;"><br>
						<input type="submit" class="btnBlueNew" value="{|&Uuml;bertragung komplett abbrechen|}" name="abbrechen"><br>
					

				</fieldset>
				<fieldset>
					<legend>{|Artikel laden|}</legend>
					<table class="mkTableFormular">
						<tr>
							<td>{|Artikel|}:</td>
						</tr>
						<tr>
							<td nowrap><input type="text" name="artikel" id="artikel" size="18">&nbsp;<input type="submit" class="btnBlue" name="artikelladen" value="{|laden|}">[VORMATRIXPRODUKT]<br><input type="checkbox" name="unterartikel" value="1">&nbsp;{|inkl. Matrix / Varianten|}[NACHMATRIXPRODUKT]</td>
						</tr>
						<tr>
							<td>{|Kategorie|}:</td>
						</tr>
						<tr>
							<td><input type="text" name="kategorie" id="kategorie" size="18">&nbsp;<input type="submit" class="btnBlue" name="kategorieladen" value="{|laden|}">[VORMATRIXPRODUKT]<br><input type="checkbox" name="unterartikelkategorie" value="1">&nbsp;{|inkl. Matrix / Varianten|}[NACHMATRIXPRODUKT]</td>
						</tr>
					</table>
				</fieldset>
			</div>
			</div>
			</div>
			</div>

		</form>
	</div>

<!-- tab view schließen -->
</div>


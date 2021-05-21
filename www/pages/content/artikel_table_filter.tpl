<fieldset data-filter="artikel" class="table_filter" style="display:none">
	<legend>{|Filter|}</legend>
	<table width="100%">
		<tr>
			<td colspan="2">
				<!-- <input type="text" name="suche" value="" style="width: 48.35%"> -->
			</td>
		</tr>
		<tr>
			<td width="50%" valign="top">
				<!-- Spalte1 -->
				<div class="table_filter_container table_filter_container_left">
					<table>
						<tr>
							<td width="150">{|Name|}:</td>
							<td><input type="text" name="name" value=""></td>
						</tr>
						<tr>
							<td>{|Artikelnummer|}:</td>
							<td><input type="text" name="nummer" value=""></td>
						</tr>
						<tr>
							<td width="150">{|Hersteller|}:</td>
							<td><input type="text" name="hersteller" value="" id="hersteller"></td>
						</tr>
						<tr>
							<td>{|Standardlieferant|}:</td>
							<td><input type="text" name="standardlieferant" value="" id="lieferantname"></td>
						</tr>
					</table>
				</div>
				
				<div class="table_filter_container">
					<table width="100%">
						<tr>
							<td width="30"><input type="checkbox" name="abverkauf" value="ON"></td>
							<td>{|Abverkauf|}</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="lagerartikel" value="ON"></td>
							<td>{|Lagerartikel|}</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="variante" value="ON"></td>
							<td>{|Nur Varianten|}</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="freigabenotwendig" value="ON"></td>
							<td>{|Freigabe notwendig|}</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="stueckliste" value="ON"></td>
							<td>{|Stückliste|}</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="justintimestueckliste" value="ON"></td>
							<td>{|Just in time Stückliste|}</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="inventursperre" value="ON"></td>
							<td>{|Inventursperre EK|}</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="gesperrt" value="ON"></td>
							<td>{|Gesperrt|}</td>
						</tr>
						<tr>
							<td><input type="checkbox" name="unikat" value="ON"></td>
							<td>{|auch kundenspezifische Artikel|}</td>
						</tr>
					</table>
				</div>
				
			</td>
			<td valign="top">
				<!-- Spalte 2 -->

				<!--
				<div class="table_filter_container table_filter_container_right">
					<table width="100%">
						<tr>
							<td width="150">Datum:</td>
							<td>
								<input type="text" name="datumVon" id="datumVon" size="10"> bis <input type="text" name="datumBis" id="datumBis" size="10">
							</td>
						</tr>
						<tr>
							<td>Betrag:</td>
							<td>
								<input type="text" name="betragVon" size="10"> bis <input type="text" name="betragBis" size="10">
							</td>
						</tr>
					</table>
				</div>
				-->

				<div class="table_filter_container table_filter_container_right">
					<table width="100%">
						<tr>
							<td width="150">{|Projekt|}:</td>
							<td><input type="text" name="projekt" value="" id="projekt"></td>
						</tr>
						<tr>
							<td width="150">{|Artikelkategorie|}:</td>
							<td><input type="text" name="typ" value="" id="typ"></td>
						</tr>            
            <tr>
							<td>{|EAN|}:</td>
							<td><input type="text" name="ean" value=""></td>
						</tr>
            <tr>
							<td>[FREIFELD1BEZEICHNUNG]:</td>
							<td><input type="text" name="freifeld1" value=""></td>
						</tr>
						<tr>
							<td>{|Herstellernummer|}:</td>
							<td><input type="text" name="herstellernummer" value=""></td>
						</tr>
						<tr>
							<td>{|Interne Bemerkung|}:</td>
							<td><input type="text" name="internebemerkung" value=""></td>
						</tr>
						<tr>
							<td>{|Sperrgrund|}:</td>
							<td><input type="text" name="sperrgrund" value=""></td>
						</tr>
					</table>
				</div>

			</td>
		</tr>
		<tr>
			<td colspan="2" align="right">
				<button onclick="table_filter.clearParameters('artikel');">{|Alles zurücksetzen|}</button>
				<button onclick="table_filter.setParameters('artikel');">{|Filter anwenden|}</button>
			</td>
		</tr>
	</table>
</fieldset>

<style>
.table_filter_container {
	border: 1px solid #d7d7d7;
	margin: 0 5px 10px 0;
	padding: 5px;
}

.table_filter_container_right {
	margin: 0 0 10px 5px;
}
</style>

<div class="filter-box filter-usersave">
	<div class="filter-block filter-inline">
		<div class="filter-title">{|Filter|}</div>
		<ul class="filter-list">
			<li class="filter-item">
				<label for="f_lagerartikel" class="switch">
					<input type="checkbox" value="1" id="f_lagerartikel" />
					<span class="slider round"></span>
				</label>
				<label for="f_lagerartikel">{|Lagerartikel|}</label>
			</li>
			<li class="filter-item">
				<label for="f_keinlagerartikel" class="switch">
					<input type="checkbox" value="1" id="f_keinlagerartikel" />
					<span class="slider round"></span>
				</label>
				<label for="f_keinlagerartikel">{|Kein Lagerartikel|}</label>
			</li>
			<li class="filter-item">
				<label for="f_variante" class="switch">
					<input type="checkbox" value="1" id="f_variante" />
					<span class="slider round"></span>
				</label>
				<label for="f_variante">{|Nur Varianten|}</label>
			</li>
			<li class="filter-item">
				<label for="f_variantekeine" class="switch">
					<input type="checkbox" value="1" id="f_variantekeine" />
					<span class="slider round"></span>
				</label>
				<label for="f_variantekeine">{|Keine Varianten|}</label>
			</li>
			<li class="filter-item">
				<label for="f_stueckliste" class="switch">
					<input type="checkbox" value="1" id="f_stueckliste" />
					<span class="slider round"></span>
				</label>
				<label for="f_stueckliste">{|St&uuml;ckliste|}</label>
			</li>
			<li class="filter-item">
				<label for="f_keinestueckliste" class="switch">
					<input type="checkbox" value="1" id="f_keinestueckliste" />
					<span class="slider round"></span>
				</label>
				<label for="f_keinestueckliste">{|Keine St&uuml;ckliste|}</label>
			</li>
			<li class="filter-item">
				<label for="f_gesperrt" class="switch">
					<input type="checkbox" value="1" id="f_gesperrt" />
					<span class="slider round"></span>
				</label>
				<label for="f_gesperrt">{|gesperrt|}</label>
			</li>
			<li class="filter-item">
				<label for="f_alleartikel" class="switch">
					<input type="checkbox" value="1" id="f_alleartikel" />
					<span class="slider round"></span>
				</label>
				<label for="f_alleartikel">{|alle Artikel|}</label>
			</li>
			<li class="filter-item">
				<label for="f_nurlagerndeartikel" class="switch">
					<input type="checkbox" value="1" id="f_nurlagerndeartikel" />
					<span class="slider round"></span>
				</label>
				<label for="f_nurlagerndeartikel">{|nur lagernde Artikel|}</label>
			</li>
		</ul>
	</div>
</div>

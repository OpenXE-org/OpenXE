<!-- gehort zu tabview -->
<div id="tabs">
	<ul>
		<li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1" class="listview">
		[MESSAGE]
		<div class="row">
			<div class="row-height">
				<div class="col-xs-12 col-sm-9 col-sm-height">
					<div class="inside inside-full-height">
						[TAB1]
						<fieldset>
							<legend>{|Artikel|}</legend>
							<table>
								<tr>
									<td>
										<input type="checkbox" id="changeall" />&nbsp;
										<label for="changeall">{|alle Artikel ausw&auml;hlen|}</label>
									</td>
									<td align="center">
										<input type="button" value="Massenbearbeitung öffnen" id="massedit" />
									</td>
									<td>
										<input type="button" value="Fehlende Artikel erzeugen" id="createmissingarticles" />
									</td>
									<td>
										<input type="button" value="alle Fehlende Artikel erzeugen" id="createallmissingarticles" />
									</td>
								</tr>
							</table>
						</fieldset>
					</div>
				</div>
				<div class="col-xs-12 col-sm-3 col-sm-height">
					<div class="inside inside-full-height">
						<form method="post">
							<input type="submit" name="changetostandard"
																			 class="button button-secondary button-block"
																			 value="Zu Standardansicht wechseln"/>
						</form>
						<input type="button" id="generatelist" value="Liste neu generieren"  class="button button-secondary button-block" />


						[TAB2]
						<input type="button" id="newGroup" value="Neue Gruppe"  class="button button-primary button-block" />

						[TAB3]
						<input type="button" id="newOption" value="Neue Option"  class="button button-secondary button-block" />
					</div>
				</div>
			</div>
		</div>
		[TAB1NEXT]
	</div>

	<!-- tab view schließen -->
</div>

<div id="popupgroup" class="hidden" data-articleid="[ID]">
	<fieldset>
		<legend>{|Gruppe|}</legend>
		<table>
			<!--<tr>
				<td>
					<input type="radio" id="newgroupoption" name="grouptype" />
					<label for="newgroupoption">{|Neue Gruppe|}</label>
				</td>
				<td>
					<input type="radio" id="groupfromtable" name="grouptype" />
					<label for="grouptable">{|Grundtabelle|}:</label>
					<select id="grouptable">
						 [GROUPTABLEOPTIONS]
					</select>
				</td>
			</tr>-->
			<tr>
				<td>
					<label for="groupname">{|Bezeichnung|}:</label>
				</td>
				<td>
					<input type="hidden" id="groupid" /><input type="text" id="groupname" size="30" />
				</td>
			</tr>
		</table>
	</fieldset>
</div>
<div id="popupoption" class="hidden">
	<fieldset>
		<legend>{|Option|}</legend>
		<table>
		<tr>
			<td>
				<label for="optiongroup">{|Gruppe|}:</label>
			</td>
			<td>
				<select id="optiongroup">
					[SELOPTIONGROUP]
				</select>
			</td>
		</tr>
		<tr>
			<td>
				<label for="optionname">{|Bezeichnung|}:</label>
			</td>
			<td>
				<input type="hidden" id="optionid" /><input type="text" id="optionname" size="30" />
			</td>
		</tr>
		</table>
	</fieldset>
</div>
<div id="popuparticle" class="hidden">
	<input type="hidden" id="articlelistid" />
	<fieldset>
		<legend>{|Artikeloptionen|}</legend>
		<div id="options">

		</div>
	</fieldset>
</div>
<div id="popuparcticlecreate" class="hidden">
	<input type="hidden" id="listids" />
		<input type="hidden" id="artikelerzeugendo" name="do" value="create" />
		<input type="hidden" id="nextprefixnumber" />
		<input type="hidden" id="artikelerzeugenart" name="art" value="" />
		<table>
			<tr>
				<td><input type="radio" checked="checked" id="fromcategory" value="auskategorie" name="numbertype" /></td>
				<td><label for="fromcategory">{|Artikelnummern aus Nummerkreis von Hauptkategorie verwenden|}</label></td>
			</tr>
			<tr>
				<td><input type="radio" id="fromoption" value="fromoption" name="numbertype" /></td>
				<td><label for="fromoption">{|Artikelnummern aus Optionen an Hauptnummer anf&uuml;gen|}</label>
				<td></td>
			</tr>
			<tr>
				<td><input type="radio" id="fromsuffix" value="fromsuffix" name="numbertype" /></td>
				<td><label for="fromsuffix">{|Artikelnummern aus Hauptnummer und Anhang an Artikelnummern bilden. Keine zus&auml;tzlichen Trennzeichen.|}</label>
				<td></td>
			</tr>
			<tr>
				<td>
					<input type="radio" id="fromprefix" value="fromprefix" name="numbertype"/>
				</td>
				<td>
					<label for="fromprefix">{|Artikelnummern von Hauptartikel mit Suffix|}</label>
					<label for="prefixseparator">{|Trennzeichen|}:</label>
					<input type="text" id="prefixseparator" name="prefixseparator" value="[PREFIXSEPARATOR]" size="1" />
					<label for="prefixcount">{|Anzahl Stellen|}:</label>
					<input type="text" id="prefixcount" name="prefixcount" value="[PREFIXCOUNT]" size="3" />
					<label for="prefixnextnumber">{|N&auml;chste Nummer|}:</label>
					<input type="text" id="prefixnextnumber" name="prefixnextnumber" value="[PREFIXNEXTNUMBER]" size="5" />
				<td></td>
			</tr>
			<tr>
				<td>
					<input type="checkbox" id="appendname" name="appendname" value="1" [APPENDNAME] />
				</td>
				<td>
					<label for="appendname">{|Optionen an Artikelbezeichnung der Unterartikel h&auml;ngen|}</label>
				</td>
			</tr>
		</table>
</div>
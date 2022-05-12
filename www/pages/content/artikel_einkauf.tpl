<tr><td colspan="4">
<!-- gehort zu tabview -->
<div id="tabview" class="yui-navset">
    <ul class="yui-nav">
        <li class="selected"><a href="#tab1"><em>&Uuml;bersicht</em></a></li>
        <li class=""><a href="#tab2"><em>Preis hinzuf&uuml;gen</em></a></li>
        <li class=""><a href="#tab3"><em>Historie</em></a></li>
    </ul>
    <div class="yui-content">
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div>
[UEBERSICHT]
</div>

<div>
 <table border="0" width="100%">
            <tbody>
              <tr><td colspan="2"><br></td></tr>
              <tr><td colspan="2"><b>Preis anlegen</b></td></tr>
		      <tr><td>Lieferant</td><td><input name="lieferant" type="text" size="20"></td></tr>
		      <tr><td>Bestellnummer</td><td><input name="bestellnummer" type="text" size="20"></td></tr>
		      <tr><td>Preis Art</td><td>
		      <select name="preisart">
			      <option>Standard Bestellung</option>
			      <option>Spezial Projektpreis</option>
			      <option>Rahmenvetrag</option><option>Abrufbestellung</option>
		      </select>
		      </td></tr>
		      <tr><td>Zuordnung</td><td><input name="zuordnung" type="text" size="10"> Projekt / Rahmenvertrag / Abrufbestellung</td></tr>

              <tr><td colspan="2"><br></td></tr>
		      <tr><td>Ab Menge</td><td><input name="mindestmenge" type="text" size="10"></td></tr>
		      <tr><td>VPE</td><td><select name="vpe"><option>Einzeln</option><option>Tray</option><option>Rolle</option><option>St&uuml;ckgut</option><option>Stange</option><option>Palette</option></select></td></tr>
		      <tr><td>Preis</td><td><input name="preis" type="text" size="10"><input type="button" value="Kalkulator"></td></tr>
		      <tr><td>W&auml;hrung</td><td><select name="waehrung"><option>EUR</option><option>USD</option></select></td></tr>
              <tr><td colspan="2"><br></td></tr>
		      <tr><td>Preisanfrage vom</td><td><input name="preisanfragevom" type="text" size="10"></td></tr>
		      <tr><td>G&uuml;ltig bis</td><td><input name="gueltigbis" type="text" size="10"></td></tr>
		      <tr><td>Lieferzeit Standard</td><td><input name="lieferzeitstandard" type="text" size="10"></td></tr>
		      <tr><td>Lieferzeit Aktuell</td><td><input name="lieferzeitaktuell" type="text" size="10"></td></tr>
              <tr><td colspan="2"><br></td></tr>
		      <tr><td>Lagerbestand Lieferant</td><td><input name="lagerlieferant" type="text" size="10"> am <input name="datumlagerlieferant" type="text" size="10"></td></tr>
		      <tr><td>Sicherheitslager</td><td><input name="sicherheitslager" type="text" size="10"></td></tr>
              <tr><td colspan="2"><br></td></tr>
	      	  <tr><td>Bemerkung</td><td><textarea name="bemerkung" rows="5" cols="40"></textarea></td></tr>
              <tr><td colspan="2"><br></td></tr>

	      
</tbody></table>
</div>

<div>
[HISTORIE]
<div>
<!-- tab view schließen -->
</div></div>
<!-- ende tab view schließen -->
</td></tr>

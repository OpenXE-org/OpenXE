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
              <tr><td>Beschreibung</td><td>
              <select name="beschreibung">
              <option value="standard">Standardpreis</option>
              <option value="wiedervekaufspreis">Wiedervekaufspreis</option>
              <option value="spezialpreis">Spezialpreis</option>
              </select>
              </td></tr>
              <tr><td>Kunde</td><td>
              <select name="kunde">
              <option value="standard">Standard</option>
              <option value="projekt">Projekt</option>
              <option value="kunde">Kunde</option>
              <option value="export">Export</option>
              </select>
              </td></tr>
	      <tr><td>Zuordnung</td><td><input name="zuordnung" type="text" size="10"> Kunde / Projekt </td></tr>

              <tr><td colspan="2"><br></td></tr>
	      <tr><td>Ab Menge</td><td><input name="mindestmenge" type="text" size="10"></td></tr>
	      <tr><td>Preis</td><td><input name="preis" type="text" size="10"><input type="button" value="Kalkulator"></td></tr>
              <tr><td colspan="2"><br></td></tr>
	      <tr><td>G&uuml;ltig bis</td><td><input name="gueltigbis" type="text" size="10"></td></tr>
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

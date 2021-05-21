<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]

<table>
<tr><td valign="top">
<fieldset style="height:100px;"><legend>{|Filter Zeitraum|}</legend>
<table><tr>
<td>von:&nbsp;</td><td><input type="text" id="von" name="von" value="[VON]" /></td>
<td>bis:&nbsp;</td><td><input type="text" id="bis" name="bis" value="[BIS]" /></td>
</tr></table>

</td>
<td valign="top"><fieldset style="height:100px;"><legend>{|Zusatzspalten|}</legend>
<form method="POST">
<table>
<tr><td>Spalte 1</td><td><input type="text" name="spalte1bezeichnung" id="spalte1bezeichnung" value="[SPALTE1BEZEICHNUNG]"></td><td>Wert</td><td><input type="text" name="spalte1wert" id="spalte1wert" value="[SPALTE1WERT]"></td><td>Artikel summiert</td><td><input type="checkbox" value="1" id="artikelgruppiert" name="artikelgruppiert" [ARTIKELGRUPPIERT] /></td></tr>
<tr><td>Spalte 2</td><td><input type="text" name="spalte2bezeichnung" id="spalte2bezeichnung" value="[SPALTE2BEZEICHNUNG]"></td><td>Wert</td><td><input type="text" name="spalte2wert" id="spalte2wert" value="[SPALTE2WERT]"></td><td></td><td></td></tr>
<tr><td>Spalte 3</td><td><input type="text" name="spalte3bezeichnung" id="spalte3bezeichnung" value="[SPALTE3BEZEICHNUNG]"></td><td>Wert</td><td><input type="text" name="spalte3wert" id="spalte3wert" value="[SPALTE3WERT]"></td><td><input type="submit" value="anwenden" /></td></tr>
</table>
</form>
</td>
</tr>
</table>
</fieldset>
[TAB1]
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>


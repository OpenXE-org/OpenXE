<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
  [MESSAGE]
  [TAB1]
  <fieldset>
    <legend>{|CSV DATEI HOCHLADEN|}</legend>
   	<form action="#" method="post" name="csv" enctype="multipart/form-data">
  		<input type="file" name="datei">
  		<input type="submit" name="speichern" value="Importieren">
  		<br />
    </form>
    <table>
      <tr>
        <td width="100">Kodierung: </td><td>UTF-8</td><td></td>
      </tr>
      <tr>
        <td>Format: </td><td width="300">"konto";"beschriftung";"bemerkung";"art";"aktiv";"projekt";</td><td><i>(ab "art" optional, bei "projekt" Kennung verwenden, mögliche Inhalte f&uuml;r "art": Aufwendungen, Erl&ouml;se, Geldtransit, Saldo)</i></td>
      </tr>
    </table>
  </fieldset>
  [TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>


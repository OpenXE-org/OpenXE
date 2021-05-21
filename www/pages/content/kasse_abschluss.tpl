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
[TAB1NEXT]
<fieldset><legend>{|Kassenbuch nach Tagesabschluss|}</legend>
<form action="" method="post">
<table>
<tr>
        <td>Von:</td><td><input type="text" size="10" id="von" name="von"></td>
        <td>Bis:</td><td><input type="text" size="10" id="bis" name="bis"></td>
        <td><input type="submit" value="Download" name="download" onclick="this.form.action += '#tabs-2';" ></td>
        </tr>
</table>
</form>
</fieldset>
<fieldset><legend>Kassenbuch im Entwurfsmodus (ohne Berücksichtigung des Tagesabschlusses)</legend>
<form action="" method="post">
<table>
<tr>
        <td>Von:</td><td><input type="text" size="10" id="vone" name="vone"></td>
        <td>Bis:</td><td><input type="text" size="10" id="bise" name="bise"></td>
        <td><input type="submit" value="Download" name="downloade" onclick="this.form.action += '#tabs-2';" ></td>
        </tr>
</table>
</form>
</fieldset>

</div>
</div>

<!-- tab view schließen -->
</div>


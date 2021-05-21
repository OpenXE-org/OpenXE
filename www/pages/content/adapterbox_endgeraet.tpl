<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<form action="" method="post">
<div id="tabs-1">
[MESSAGE]

<fieldset><legend>{|Adapterbox Seriennummer|}</legend>
<table width="100%">
<tr><td width="200">Verwenden als:</td><td><select name="verwendenals">[VERWENDENALS]</select>&nbsp;</td></tr>
<tr><td width="200">Bezeichnung:</td><td><input type="text" size="40" name="bezeichnung" value="[BEZEICHNUNG]">&nbsp;<i>z.B. Etikettendrucker 1 oder Waage 1</i></td></tr>
<tr><td width="200">Seriennummer:</td><td><input type="text" name="seriennummer" size="40" value="[SERIENNUMMER]">&nbsp;<i>Siehe Barcode auf der Adapterbox</i></td></tr>
<!--<tr><td width="200">Format:</td><td><select>
  <option value="30x15x3">30x15x3</option><option value="50x18x3">50x18x3</option><option value="100x50x5">100x50x5</option>
  </select></td></tr>-->
</table>
</fieldset>

<fieldset><legend>{|Waage|}</legend>
<table width="100%">
<tr><td width="200">Model:</td><td><select name="model">[MODEL]</select>&nbsp;</td></tr>
<tr><td width="200">Baudrate:</td><td><select name="baudrate">[BAUDRATE]</select>&nbsp;</td></tr>
</table>
</fieldset>



<table width="100%"><tr><td align="right"><input type="submit" value="Speichern" name="submit"></td></tr></table>
</form>

</div>

<!-- tab view schließen -->
</div>


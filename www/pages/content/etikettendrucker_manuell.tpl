<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Standard 2-zeilig</a></li>
        <li><a href="#tabs-2">XML</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[TAB1]
[TAB1NEXT]

<form action="" method="post">
<input type="text" name="bezeichnung1" value="[BEZEICHNUNG1]" size="20"><br>
<input type="text" name="bezeichnung2" value="[BEZEICHNUNG2]" size="20">&nbsp;
<br>{|Menge|}:&nbsp;<input type="text" name="menge" value="[MENGE]" size="5">&nbsp;<input type="submit" value="{|Drucken|}" name="drucken">
</form>
</div>

<!-- erstes tab -->
<div id="tabs-2">
<form action="" method="post">
<textarea name="xml" rows="10" cols="80">[XML]</textarea>
<br><br>{|Menge|}:&nbsp;<input type="text" name="menge" value="[MENGE]" size="5">&nbsp;<input type="submit" value="{|Drucken|}" name="xmltest" onclick="this.form.action += 'tabs-2';">
<br><br>
{|Beispiele|}:<br>
<textarea rows="10" cols="80">
<label>
<line x="5" y="1" size="3">Test</line>
<barcode x="5" y="4" size="8" type="2">1234567</barcode>
</label>
</textarea>
</pre>
</form>
</div>


<!-- tab view schließen -->
</div>



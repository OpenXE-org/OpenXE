<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tab1">offene Rechnungen</a></li>
        <li><a href="#tab2">Lastschriften</a></li>
        <li><a href="#tab3">DTA Dateien</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tab1">
[TAB1]
<br>
<center>
<form action="" method="post">
<table width="60%" style="background-color: #fff; border: solid 1px #000;" align="center">
<tr>
<td align="center">
<br><b>Lastschriften erzeugen:</b>
<br>
<br>Konto:&nbsp;<select name="konto">[KONTO]</select>
&nbsp;<input type="submit" value="Lastschriften erzeugen" name="erzeugen">
<br>
<br>
</td>
</tr>
</table>
</center>
<br>
</div>

<div id="tabs2">
[TAB2]
<br>
<center>
<table width="60%" style="background-color: #fff; border: solid 1px #000;" align="center">
<tr>
<td align="center">
<br><b>Sammellastschrift erzeugen:</b>
<br>
<br>Konto:&nbsp;<select name="konto">[KONTO]</select>
&nbsp;<input type="submit" value="DTA Datei erzeugen" name="lastschrift">
<br>
<br>
</td>
</tr>
</table>
</center>
<br>

</div>


<div id="tab3">
[TAB3]
</div>






<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->
</form>


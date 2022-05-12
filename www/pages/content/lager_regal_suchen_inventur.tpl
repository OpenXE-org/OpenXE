<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Inventur</a></li>
        <li><a href="#tabs-3">Spezialfunktionen</a></li>
        <li><a href="#tabs-2">Abschluss</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<div id="ajaxmessage"></div>
<form action="" method="post">
<table width="100%"><tr><td align="center">Regal:&nbsp;[REGALAUTOSTART]<input type="text" name="regal" id="regal" value="" style="background-color: red;color:#000;">[REGALAUTOEND]&nbsp;Jetzt Regal abscannen!
&nbsp;<input type="submit" value="Suchen" name="submit">
</td><td align="right">
</td></tr>

</table>

<script type="text/javascript">document.getElementById("regal").focus();</script>
<br><br>
<!--<table width="100%"><tr><td align="right"><input type="submit" value="Speichern" name="inventurspeichern"></td></tr></table>-->
[TAB1]
<!--<table width="100%"><tr><td align="right"><input type="submit" value="Speichern" name="inventurspeichern"></td></tr></table>-->
</form>
</div>

<div id="tabs-2">
<form action="" method="post">
<table width="100%"><tr>
<td align="center">
[PERMISSIONINVENTURSTART]
&nbsp;<input type="button" onclick="if(!confirm('Soll die Inventur jetzt abgeschlossen werden? Alle Anpassungen werden &uuml;bernommen und der anschlie&szlig;end neue Lagerbestand gesondert als Inventur Stand: [STAND] gespeichert. Es kann nach dem Klick einige Minuten dauern bis die &Auml;nderungen &uuml;bernommen worden sind. Bitte unterbrechen Sie den Vorgang nicht. Sollte es zu einem Abbruch kommen k&ouml;nnen Sie den Vorgang wiederholen. Es werden alle fehlenden Buchungen automatisch nachgeholt.')) return false; else window.location.href='index.php?module=lagerinventur&action=inventur&cmd=einfrieren&id=[ID]';" value="[KURZUEBERSCHRIFT2] jetzt anpassen">
[PERMISSIONINVENTURENDE]
</td></tr>
</table>
[FEHLENDEINVENTUR]
</div>



<div id="tabs-3">
<center><input type="button" onclick="if(!confirm('Soll die Inventur jetzt aufgrund des Lagerbestandes vorausgef&uuml;llt werden?')) return false; else window.location.href='index.php?module=lagerinventur&action=inventurladen&id=[ID]';" value="Inventur f&uuml;r [KURZUEBERSCHRIFT2] jetzt aus Lagerbestand laden">

[PERMISSIONINVENTURSTART]

&nbsp;<input type="button" onclick="if(!confirm('Soll die Inventur jetzt zur&uuml;ckgesetzt werden? Alle Anpassungen werden gel&ouml;scht.')) return false; else window.location.href='index.php?module=lagerinventur&action=inventur&cmd=resetalle&id=[ID]';" value="Inventur [LAGERNAME] (komplett) zur&uuml;cksetzen">

</center>

[PERMISSIONINVENTURENDE]
</div>

<!-- tab view schlieï¿½en -->
</div>




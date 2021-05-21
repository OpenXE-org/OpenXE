<div style="padding:10px;"><center>
[BUTTONAUSLAGERN]<br>
<input type="button" class="btnBlue" onclick="window.location.href='index.php?module=produktion&action=pdf&id=[ID]'" value="Produktionsanweisung als PDF" style="width:300px"><br>
<input type="button" class="btnBlue" onclick="window.location.href='index.php?module=produktion&action=pdfanhang&id=[ID]'" value="Datenbl&auml;tter als ZIP" style="width:300px"><br>
<br>
<input type="button" class="btnBlue" onclick="if(printquantity = prompt('Anzahl ?', '[BAUGRUPPENANZ]')) window.location.href='index.php?module=produktion&action=etikettendrucken&id=[ID]&cmd=sets&menge='+printquantity" value="Etikettensets für alle Produkte" style="width:300px"><br>
<input type="button" class="btnBlue" onclick="if(printquantity = prompt('Anzahl ?', '[BAUGRUPPENANZ]')) window.location.href='index.php?module=produktion&action=etikettendrucken&id=[ID]&cmd=seriennummern&menge='+printquantity" value="Seriennummern-Etiketten für alle Produkte" style="width:300px"><br>
<input type="button" class="btnBlue" onclick="window.location.href='index.php?module=produktion&action=etikettendrucken&id=[ID]&cmd=stueckliste'" value="St&uuml;ckliste als Etiketten drucken" style="width:300px"><br>
<br>
[PRODUKTIONSZENTRUM]
</center>
</div>



<style>


.auftraginfo_cell {
  color: #636363;border: 1px solid #ccc;padding: 5px;
}

.auftrag_cell {
  color: #636363;border: 1px solid #fff;padding: 0px; margin:0px;
}

</style>
<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">


<table width="100%" border="0" cellpadding="10" cellspacing="5">
<tr valign="top"><td width="">

<h2 class="greyh2">St&uuml;ckliste</h2>
<div style="padding:10px;">[ARTIKEL]</div>



</td><td width="550">  

<div style="overflow:scroll; height:550px; width:500px;">
<div style="background-color:white">
<h2 class="greyh2">Funktionen</h2>
<div style="padding:10px;"><center>
[BUTTONAUSLAGERN]<br>
<input type="button" onclick="window.location.href='index.php?module=produktion&action=pdf&id=[ID]'" value="Produktionsanweisung als PDF" style="width:300px"><br>
<input type="button" onclick="window.location.href='index.php?module=produktion&action=pdfanhang&id=[ID]'" value="Anh&auml;nge als PDF" style="width:300px"><br>
<input type="button" onclick="window.location.href='index.php?module=produktion&action=etiketten&cmd=stueckliste&id=$id'" value="St&uuml;ckliste als Etiketten drucken" style="width:300px"><br>
<input type="button" onclick="window.location.href='index.php?module=produktion&action=etiketten&cmd=artikel&id=$id'" value="Alle Artikelaufkleber drucken" style="width:300px"><br>
<!--<input type="button" onclick="window.location.href='index.php?module=produktion&action=etiketten&cmd=seriennummern&id=$id'" value="Alle Seriennummernaufkleber drucken" style="width:300px"><br>-->
<br>
<input type="button" onclick="window.location.href='index.php?module=produktion&action=edit&id=[ID]'" value="Zur&uuml;ck zur Produktion" style="width:300px"><br>
</center>
</div>


<h2 class="greyh2">Datei Anhang</h2>
<div style="padding:10px;">[DATEIANHANG]</div>




<br><br>

</div>
</div>

</td></tr>
</table>

</div>
</div>

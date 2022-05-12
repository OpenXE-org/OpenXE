  
<style>

.auftraginfo_cell {
  color: #636363;border: 1px solid #ccc;padding: 5px;
}

.auftrag_cell {
  color: #636363;border: 1px solid #fff;padding: 0px; margin:0px;
}

</style>

<div style="float:left; width:49%;padding-right:1%;">

<br><center>[MENU]</center>
<br>
<table height="250" width="100%">
<tr valign="top"><td>
<table cellspacing="0" cellpadding="0" style="font-size: 8pt; background: white; color: #333333; border-collapse: collapse;" width="100%">
<tr><td class="auftraginfo_cell">{|Lieferant|}:</td><td colspan="4" class="auftraginfo_cell">[LIEFERANT]</td></tr>
<tr><td class="auftraginfo_cell" colspan="2" width="50%"><b>{|Allgemein|}</b></td><td class="auftraginfo_cell" colspan="2"><b>{|Zahlung|}</b></td></tr>
<tr><td class="auftraginfo_cell">{|Status|}:</td><td class="auftraginfo_cell">[STATUS]</td><td class="auftraginfo_cell" width="25%">{|Zahlweise|}:</td><td class="auftraginfo_cell">[ZAHLWEISE]</td></tr>
<tr><td class="auftraginfo_cell">{|Projekt|}:</td><td class="auftraginfo_cell">[PROJEKT]</td><td class="auftraginfo_cell">{|Wunsch Liefertermin|}:</td><td class="auftraginfo_cell">[WUNSCHLIEFERDATUM]</td></tr>
<tr><td class="auftraginfo_cell" colspan="2" width="50%"><b>{|Dokumente|}</b></td><td class="auftraginfo_cell">{|Best&auml;tigtes Lieferdatum|}:</td><td class="auftraginfo_cell">[BESTAETIGTESLIEFERDATUM]</td></tr>
<tr><td class="auftraginfo_cell">{|Preisanfrage|}:</td><td class="auftraginfo_cell">[PREISANFRAGE]</td><td class="auftraginfo_cell">{|AB Nr. vom Lieferant|}:</td><td class="auftraginfo_cell">[ABLIEFERANT]</td></tr>
<tr valign="bottom"><td class="auftraginfo_cell">{|Lieferschein|}:</td><td class="auftraginfo_cell" nowrap>[LIEFERSCHEIN]</td><td class="auftraginfo_cell">{|Gewicht (netto)|}:</td><td class="auftraginfo_cell">[GEWICHT]</td></tr>
<tr><td class="auftraginfo_cell">{|Rechnung|}:</td><td class="auftraginfo_cell">[RECHNUNG]</td><td class="auftraginfo_cell">{|Versteuerung|}:</td><td class="auftraginfo_cell">[STEUER]</td></tr>
<tr><td class="auftraginfo_cell">{|Auftrag|}:</td><td class="auftraginfo_cell">[AUFTRAG]</td><td class="auftraginfo_cell">{|Status|}:</td><td class="auftraginfo_cell">[STATUS]</td></tr>
<tr><td class="auftraginfo_cell">{|Wareneingangsbeleg|}:</td><td class="auftraginfo_cell">[WARENEINGANGSBELEG]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
</table>

<table width="100%" cellpadding="5">
<tr><td>
<div style="background-color:white">
<div style="padding:10px">
  [RECHNUNGLIEFERADRESSE]
</div>
</div>
</td></tr></table>

</td></tr>
</table>

</div><div style="float:left; width:50%">

<div style="overflow:auto;height:550px">
<div style="background-color:white">
<div width="100%" style="background-color:#999;"><h2 class="greyh2">{|Artikel|}</h2></div>
<div style="padding:10px;">
<div>
 [ARTIKEL]
</div>
<br><br>

<div class="info">
{|Verbindlichkeiten sind ab der Version Enterprise verf&uuml;gbar!|}
</div>
</div>
</div>

<!--
<div style="background-color:white">
<h2 class="greyh2">Zahlungseingang</h2>
<div style="padding:10px">
  [ZAHLUNGEN]
</div>
</div>
-->

<div style="background-color:white">
<h2 class="greyh2">{|Protokoll|}</h2>
<div style="padding:10px;overflow:auto; width:500px;">
  [PROTOKOLL]
</div>
</div>
<div style="background-color:white">
<h2 class="greyh2">{|PDF-Archiv|}</h2>
<div style="padding:10px;overflow:auto; width:500px;">
  [PDFARCHIV]
</div>
</div>
</div>

</div>


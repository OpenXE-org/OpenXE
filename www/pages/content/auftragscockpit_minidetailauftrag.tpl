

<style>

.auftraginfo_cell {
  color: #636363;border: 1px solid #ccc;padding: 5px; 
}

.auftrag_cell {
  color: #636363;border: 1px solid #fff;padding: 0px; margin:0px;
}

</style>


<table width="100%" border="0" cellpadding="10" cellspacing="5">

<tr valign="top"><td>
<br><center>[MENU]</center>
<br>


<table height="250" width="100%" cellpadding="5">
<tr valign="top"><td>
<table style="font-size: 8pt; background: white; color: #333333; border-collapse: collapse;" width="100%" cellspacing="10" cellpadding="10">
<tr><td class="auftraginfo_cell">Kunde:</td><td colspan="4" class="auftraginfo_cell">[KUNDE]</td></tr>
<tr><td class="auftraginfo_cell">Ihre Bestellnummer:</td><td colspan="4" class="auftraginfo_cell">[IHREBESTELLNUMMER]</td></tr>
<tr><td class="auftraginfo_cell" colspan="2" width="50%"><b>Allgemein</b></td><td width="10" rowspan="11" class="auftraginfo_cell"></td><td class="auftraginfo_cell" colspan="2"><b>Zahlung</b></td></tr>
<tr><td class="auftraginfo_cell">Status:</td><td class="auftraginfo_cell">[STATUS]</td><td class="auftraginfo_cell" width="25%">Zahlweise:</td><td class="auftraginfo_cell">[ZAHLWEISE]</td></tr>
<tr><td class="auftraginfo_cell">Projekt:</td><td class="auftraginfo_cell">[PROJEKT]</td><td class="auftraginfo_cell" colspan="2"></td></tr>
<tr><td class="auftraginfo_cell" colspan="2" width="50%"><b>Dokumente</b></td><td class="auftraginfo_cell" colspan="2"></td></tr>
<tr><td class="auftraginfo_cell">Internet:</td><td class="auftraginfo_cell">[INTERNET]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">Transaktion:</td><td class="auftraginfo_cell">[TRANSAKTIONSNUMMER]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">Angebot:</td><td class="auftraginfo_cell" nowrap>[ANGEBOT]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">Lieferschein:</td><td class="auftraginfo_cell" nowrap>[LIEFERSCHEIN]</td><td class="auftraginfo_cell">Gewicht (netto):</td><td class="auftraginfo_cell">[GEWICHT]</td></tr>
<tr><td class="auftraginfo_cell">Rechnung:</td><td class="auftraginfo_cell">[RECHNUNG]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">Gutschrift:</td><td class="auftraginfo_cell" nowrap>[GUTSCHRIFT]</td><td class="auftraginfo_cell">Besteuerung:</td><td class="auftraginfo_cell">[STEUER]</td></tr>
[PRODUKTIONEN]
<tr><td class="auftraginfo_cell">Tracking:</td><td class="auftraginfo_cell" >[TRACKING]</td><td class="auftraginfo_cell">Status:</td><td class="auftraginfo_cell">[STATUS]</td></tr>
</table>

<table width="100%">
<tr>
[RMAIF]
  <td style="background:[RMAFARBE]; padding: 5px; color: white; font-weight: bold;">RMA:<br>[RMATEXT]</td>
[RMAELSE]
  <td style="background:[VERSANDFARBE]; padding: 5px; color: white; font-weight: bold;" width="50%">Versand:<br>[VERSANDTEXT]</td>
[RMAENDIF]
</tr>

</table>

<table width="100%" cellpadding="5">
<tr><td>
<div style="background-color:white">
<h2 class="greyh2">Rechnungs-/Lieferadresse</h2>
<div style="padding:10px">
  [RECHNUNGLIEFERADRESSE]
</div>
</div>
</td></tr></table>

</td></tr>
</table>

</td><td width="550">
 <div style="overflow:scroll; height:650px"> 
<div style="background-color:white">
<div width="100%" style="background-color:#999;"><h2 class="greyh2">Artikel</h2></div>
<div style="padding:10px">
 [ARTIKEL]
</div>
</div>

<div style="background-color:white">
<h2 class="greyh2">Zahlungseingang</h2>
<div style="padding:10px">
  [ZAHLUNGEN]
</div>
</div>

<div style="background-color:white">
<h2 class="greyh2">Protokoll</h2>
<div style="padding:10px;overflow:auto; width:500px;">
  [PROTOKOLL]
</div>
</div>

<div style="background-color:white">
<h2 class="greyh2">RMA Prozess</h2>
<div style="padding:10px">
  [RMA]
</div>
</div>
<div style="background-color:white">
<h2 class="greyh2">PDF-Archiv</h2>
<div style="padding:10px;overflow:auto; width:500px;">
  [PDFARCHIV]
</div>
</div>
<div style="background-color:white">
<h2 class="greyh2">Deckungsbeitrag</h2>
<div style="padding:10px">

<div class="info">Dieses Modul ist erst ab Version Professional verfügbar</div>


</div>
</div>

</div>
</td></tr>

</table>

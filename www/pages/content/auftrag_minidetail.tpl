

<style>

.auftraginfo_cell {
  color: #636363;border: 1px solid #ccc;padding: 5px; 
}

.auftrag_cell {
  color: #636363;border: 1px solid #fff;padding: 0px; margin:0px;
}

</style>


<div style="float:left; width:49%; padding-right:1%;">


<br><center>[MENU]</center>
<br>


<table style="font-size: 8pt; background: white; color: #333333; border-collapse: collapse;" width="100%" cellspacing="10" cellpadding="10">
<tr><td class="auftraginfo_cell">{|Kunde|}:</td><td colspan="4" class="auftraginfo_cell">[KUNDE]</td></tr>
<tr><td class="auftraginfo_cell">{|Ihre Bestellnummer|}:</td><td colspan="4" class="auftraginfo_cell">[IHREBESTELLNUMMER]</td></tr>
<tr><td class="auftraginfo_cell" colspan="2" width="50%"><b>{|Allgemein|}</b></td><td class="auftraginfo_cell" colspan="2"><b>{|Zahlung|}</b></td></tr>
<tr><td class="auftraginfo_cell">{|Status|}:</td><td class="auftraginfo_cell">[STATUS]</td><td class="auftraginfo_cell" width="25%">{|Zahlweise|}:</td><td class="auftraginfo_cell">[ZAHLWEISE]</td></tr>
<tr><td class="auftraginfo_cell">{|Projekt:|}</td><td class="auftraginfo_cell">[PROJEKT]</td><td class="auftraginfo_cell">{|Wunschlieferdatum|}:</td><td class="auftraginfo_cell">[WUNSCHLIEFERDATUM]</td></tr>
<tr><td class="auftraginfo_cell" colspan="2" width="50%"><b>{|Dokumente|}</b></td><td class="auftraginfo_cell">{|Kundensaldo|}:</td><td class="auftraginfo_cell">[KUNDENSALDO]</td></tr>
<tr><td class="auftraginfo_cell">{|Internet|}:</td><td class="auftraginfo_cell">[INTERNET]</td><td class="auftraginfo_cell">{|Onlineshop|}:</td><td class="auftraginfo_cell">[ONLINESHOP]</td></tr>
<tr><td class="auftraginfo_cell">{|Transaktion|}:</td><td class="auftraginfo_cell">[TRANSAKTIONSNUMMER]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">{|Angebot|}:</td><td class="auftraginfo_cell" >[ANGEBOT]</td><td class="auftraginfo_cell">{|Versandart|}:</td><td class="auftraginfo_cell">[VERSANDART]</td></tr>
<tr><td class="auftraginfo_cell">{|Lieferschein|}:</td><td class="auftraginfo_cell" >[LIEFERSCHEIN]</td><td class="auftraginfo_cell">{|Gewicht (netto)|}:</td><td class="auftraginfo_cell">[GEWICHT]</td></tr>
<tr><td class="auftraginfo_cell">{|Rechnung|}:</td><td class="auftraginfo_cell">[RECHNUNG]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">{|Gutschrift|}:</td><td class="auftraginfo_cell" >[GUTSCHRIFT]</td><td class="auftraginfo_cell">{|Besteuerung|}:</td><td class="auftraginfo_cell">[STEUER]</td></tr>
[PRODUKTIONEN]
<tr><td class="auftraginfo_cell">{|Bestellung|}:</td><td class="auftraginfo_cell" >[BESTELLUNG]</td><td class="auftraginfo_cell">{|Eigene Umsatzsteuer ID|}:</td><td class="auftraginfo_cell">[DELIVERYTHRESHOLDVATID]</td></tr>
<tr><td class="auftraginfo_cell">{|Retoure|}:</td><td class="auftraginfo_cell" >[RETOURE]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">{|Preisanfrage|}:</td><td class="auftraginfo_cell" >[PREISANFRAGE]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">{|Tracking|}:</td><td class="auftraginfo_cell" >[TRACKING]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
</table>

<table width="100%">
<tr>
[RMAIF]
  <td style="background:[RMAFARBE]; padding: 5px; color: white; font-weight: bold;">RMA:<br>[RMATEXT]</td>
[RMAELSE]
  <td style="" width="50%">[VERSANDTEXT]</td>
[RMAENDIF]
</tr>

</table>

<div style="background-color:white">
<div style="padding:10px">
  [RECHNUNGLIEFERADRESSE]
</div>
</div>


</div>
<div style="float:left; width:50%">

<div style="overflow:auto;max-height:550px;">
<div style="background-color:white;">
<h2 class="greyh2">Artikel</h2>
<div style="padding:10px;">
 [ARTIKEL]
<i style="color:#999">* Die linke Zahl zeigt die für den Kunden reservierten Einheiten und die rechte Zahl die global reservierte Anzahl.</i>
</div>
</div>

[MINIDETAILNACHARTIKEL]
<!--    
<div style="background-color:white;">
<h2 class="greyh2">Lieferkette</h2>
<div style="padding:10px">
<table class="mkTable">
<tr><th>Art</th><th>Beleg</th><th>Datum</th><th>Lieferant</th><th>Status</th></tr>
<tr><td>Bestellung</td><td>ENTWURF</td><td></td><td>In-Circuit<br>200 x 777777 SMT USBprog 1)</td><td>-</td></tr>
<tr><td>Anlieferung</td><td>200000</td><td></td><td>In-Circuit<br>200 x 777777 ATMEGA32</td><td>offen</td></tr>
<tr><td>Bestellung</td><td>100023</td><td></td><td>Instanet<br>200 x USBprog 5.0 Spezial Elektor<br>200 x 777777 Flashen + Montieren</td><td>offen</td></tr>
<tr><td>Anlieferung</td><td>200002</td><td></td><td>Instanet<br>200 x 777777 SMT USBprog 1)<br>200 x 122222 Gehäuse</td><td>offen</td></tr>
</table>
</div>
</div>
-->

<div style="background-color:white">
<h2 class="greyh2">{|Zahlungseingang|}</h2>
<div style="padding:10px">
  [ZAHLUNGEN]
</div>
</div>

<div style="background-color:white">
<h2 class="greyh2">{|Protokoll|}</h2>
<div style="padding:10px;">
  [PROTOKOLL]
</div>
</div>

[VORPRODUKTIONPROTOKOLL]
[PRODUKTIONPROTOKOLL]
[NACHPRODUKTIONPROTOKOLL]

<div style="background-color:white">
<h2 class="greyh2">{|RMA Prozess|}</h2>
<div style="padding:10px">
  [RMA]
</div>
</div>
<div style="background-color:white">
<h2 class="greyh2">{|PDF-Archiv|}</h2>
<div style="padding:10px;overflow:auto;">
  [PDFARCHIV]
</div>
</div>
<div style="background-color:white">
<h2 class="greyh2">{|Deckungsbeitrag|}</h2>
<div style="padding:10px">

<div class="info">{|Dieses Modul ist erst ab Version Professional verfügbar|}</div>


</div>
</div>
[INTERNEBEMERKUNGEDIT]

</div>
</div>

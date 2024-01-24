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
<table cellspacing="0" cellpadding="0" style="font-size: 8pt; background: white; color: #333333; border-collapse: collapse;" width="100%">
<tr><td class="auftraginfo_cell">Kunde: </td><td colspan="4"  class="auftraginfo_cell">[KUNDE]</td></tr>
<tr><td class="auftraginfo_cell">Ihre Anfrage:</td><td colspan="4"  class="auftraginfo_cell">[ANFRAGE]</td></tr>
<tr><td class="auftraginfo_cell" colspan="2" width="50%"><b>Allgemein</b></td><td width="10" rowspan="5" class="auftraginfo_cell"></td><td class="auftraginfo_cell" colspan="2"><b>Zahlung</b></td></tr>
<tr><td class="auftraginfo_cell">Status:</td><td class="auftraginfo_cell">[STATUS]</td><td class="auftraginfo_cell" width="25%">Zahlweise:</td><td class="auftraginfo_cell">[ZAHLWEISE]</td></tr>
<tr><td class="auftraginfo_cell">Projekt:</td><td class="auftraginfo_cell">[PROJEKT]</td><td class="auftraginfo_cell">Angebotssumme:</td><td class="auftraginfo_cell">[GESAMTSUMME]</td></tr>
<tr><td class="auftraginfo_cell">Auftrag:</td><td class="auftraginfo_cell">[AUFTRAG]</td><td class="auftraginfo_cell">Versteuerung:</td><td class="auftraginfo_cell">[STEUER]</td></tr>
<tr><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell">Gewicht (netto):</td><td class="auftraginfo_cell">[GEWICHT]</td></tr>
</table>

<table width="100%">
<tr>
  <td >[ANGEBOTTEXT]</td>
</tr>
</table>

[DOKUMENTETEXT]




</div>
<div style="float:left; width:50%">

<div style="background-color:white">
<h2 class="greyh2">Artikel</h2>
<div style="padding:10px;">
  [ARTIKEL]
</div>
</div>

<div style="background-color:white" [DBHIDDEN]>
    <h2 class="greyh2">{|Deckungsbeitrag (netto)|}</h2>
    <table width="100%">
        <tbody>
            <tr>
                <td>Umsatz EUR</td>
                <td>Kosten EUR</td>
                <td>Deckungsbeitrag EUR</td>
                <td>DB %</td>
            </tr>
            <tr>
              <td class="greybox" width="25%">[NETTOGESAMT]</td>
              <td class="greybox" width="25%">[KOSTEN]</td>
              <td class="greybox" width="25%">[DECKUNGSBEITRAG]</td>
              <td class="greybox" width="25%">[DBPROZENT]</td>
            </tr>
        </tbody>
    </table>
</div>

<div style="background-color:white">
<h2 class="greyh2">Protokoll</h2>
<div style="padding:10px;">
  [PROTOKOLL]
</div>
</div>
<div style="background-color:white">
<h2 class="greyh2">PDF-Archiv</h2>
<div style="padding:10px;">
  [PDFARCHIV]
</div>
</div>

</div>


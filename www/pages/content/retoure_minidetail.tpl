<style>


.auftraginfo_cell {
  color: #636363;border: 1px solid #ccc;padding: 5px;
}

.auftrag_cell {
  color: #636363;border: 1px solid #fff;padding: 0px; margin:0px;
}

</style>


<div style="float:left; width:50%">
<br><center>[MENU]</center>
<br>
<table cellspacing="0" cellpadding="0" style="font-size: 8pt; background: white; color: #333333; border-collapse: collapse;;" width="100%">
<tr><td class="auftraginfo_cell">{|Kunde|}:</td><td colspan="4" class="auftraginfo_cell">[KUNDE]</td></tr>
<tr><td class="auftraginfo_cell" colspan="2" width="50%"><b>{|Allgemein|}</b></td><td width="10" class="auftraginfo_cell"></td><td class="auftraginfo_cell" colspan="2"><b>{|Zahlung|}</b></td></tr>
<tr><td class="auftraginfo_cell">{|Status|}:</td><td class="auftraginfo_cell">[STATUS]</td><td class="auftraginfo_cell" width="25%">{|Zahlweise|}:</td><td class="auftraginfo_cell">[ZAHLWEISE]</td></tr>
<tr><td class="auftraginfo_cell">{|Projekt|}:</td><td class="auftraginfo_cell">[PROJEKT]</td><td class="auftraginfo_cell">{|Angebotssumme|}:</td><td class="auftraginfo_cell">[GESAMTSUMME]</td></tr>
<tr><td class="auftraginfo_cell">{|Auftrag|}:</td><td class="auftraginfo_cell">[AUFTRAG]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">{|Lieferschein|}:</td><td class="auftraginfo_cell">[LIEFERSCHEIN]</td><td class="auftraginfo_cell">{|Versteuerung|}:</td><td class="auftraginfo_cell">[STEUER]</td></tr>
<tr><td class="auftraginfo_cell">{|Rechnung|}:</td><td class="auftraginfo_cell">[RECHNUNG]</td><td class="auftraginfo_cell">{|Gewicht (netto)|}:</td><td class="auftraginfo_cell">[GEWICHT]</td></tr>
<tr><td class="auftraginfo_cell">{|Gutschrift|}:</td><td class="auftraginfo_cell">[GUTSCHRIFT]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">{|Ersatzauftrag|}:</td><td class="auftraginfo_cell">[REPLACEMENTORDER]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">{|Wareneingangsbeleg|}:</td><td class="auftraginfo_cell">[WARENEINGANGSBELEG]</td><td class="auftraginfo_cell"></td><td class="auftraginfo_cell"></td></tr>
<tr><td class="auftraginfo_cell">{|Tracking|}:</td><td class="auftraginfo_cell">[TRACKING]</td><td class="auftraginfo_cell">{|Versandart|}:</td><td class="auftraginfo_cell">[VERSANDART]</td></tr>
</table>

<div style="background-color:white">
<div style="padding:10px">
  [LIEFERADRESSE]
</div>
</div>


</div><div style="float:left; width:50%">

<div style="background-color:white">
<h2 class="greyh2">{|Artikel|}</h2>
<div style="padding:10px">
 [ARTIKEL]
</div>
</div>

<div style="background-color:white">
<h2 class="greyh2">{|Protokoll|}</h2>
<div style="padding:10px;">
  [PROTOKOLL]
</div>
</div>
<div style="background-color:white">
<h2 class="greyh2">{|PDF-Archiv|}</h2>
<div style="padding:10px;">
  [PDFARCHIV]
</div>
</div>
  <div style="background-color:white">
    <h2 class="greyh2">{|Lager|}</h2>
    <div style="padding:10px;">
      [LAGERBEWEGUNG]
    </div>
  </div>
<br><br>
[LIEFERANTENRETOUREINFOSTART]
<form action="" method="post">
<input type="hidden" name="retoureid" value="[RETOUREID]">
<table><tr><td>
{|Hinweise f&uuml;r Lieferanten Lieferungen|}:<br>
<textarea rows="5" cols="100" name="lieferantenretoureinfo">[LIEFERANTENRETOUREINFO]</textarea>
</td></tr><tr><td align="right"><input type="submit" value="Speichern" name="speichern"></td></tr></table>
</form>
[LIEFERANTENRETOUREINFOENDE]

</div>
</div>
</div>



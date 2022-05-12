<script>
  [JAVASCRIPT]
  [DATATABLES]
  [AUTOCOMPLETE]
  [JQUERY]
  
  $(document).ready(function() {
    $('#zubeleg').on('change',function(){
    $('#belegradiobestehend').prop('checked',true);
  });
  $('#adresse').on('click',function(){
    $('#belegradioneu').prop('checked',true);
  });
  });
</script>
<style>
[YUICSS]
</style>
<form id="frmpopupdiv" method="POST">
<input type="hidden" value="[BELEG]" name="belegtyp" id="belegtyp" />
<input type="hidden" value="teilprojekt_zu_beleg" name="typ" id="typ" />
<input type="hidden" value="1" name="saveartikel_zu_beleg" id="saveartikel_zu_beleg" />
[MESSAGE]
[TAB1]
<table width="100%" class="mkTable">
<tr>
<th></th>
<th>Pos</th>
<th>Artikel-Nr</th>
<th>Bezeichnung</th>
<th>BE</th>
<th>PR</th>
<th>AN</th>
<th>AB</th>
<th>LS</th>
<th>RE</th>
<th>GS</th>
[VORVKEK]<th>[VKEK]</th>[NACHVKEK]
<th>Menge</th>
</tr>
[POSITIONEN]

<!--
<tr>
<td>1</td>
<td>122222</td>
<td>Schraube xxx</td>
<td>-</td>
<td>-</td>
<td>-</td>
<td>-</td>
<td>-</td>
<td>-</td>
<td>-</td>
<td><i>1:12,20 EUR<br>10:11,15 EUR</i></td>
<td><input type="text" size="10"></td>
</tr>

<tr>
<td>1</td>
<td>122222</td>
<td>Schraube xxx</td>
<td>-</td>
<td>-</td>
<td>-</td>
<td>-</td>
<td>-</td>
<td>-</td>
<td>-</td>
<td><i><input type="radio">1:12,20 EUR Farnell<br><input type="radio">10:11,15 EUR Farnell<br><input type="radio">10:11,15 EUR Digikey</i></td>
<td><input type="text" size="10"></td>
</tr>-->

</table>
<br>
<fieldset><legend>{|Auswahl|}</legend>

<table>
<tr>
  <td style="width:200px" align="right">
  <input type="radio" checked="checked" id="belegradioneu" name="belegradio" value="neu" />
  </td><td width="200">
   [BELEG] neu anlegen
  </td><td>[KUNDELIEFERANT]:</td><td><input type="text" name="adresse" id="artzubelegadresse" value="[ADRESSE]" size="50"></td></tr>
  [VORBESTEHEND]
  <tr><td align="right">
  <input type="radio" id="belegradiobestehend" name="belegradio" value="bestehend" /></td><td>zu bestehenden [BELEG] hinzuf&uuml;gen 
  </td><td>Auswahl:</td><td><input type="text" name="zubeleg" id="zubeleg" /></td>
[NACHBESTEHEND]
</tr>
</table>

</fieldset>
</form>

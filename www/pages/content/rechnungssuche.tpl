<script type="text/javascript">
var Tastencode;
function TasteGedrueckt (Ereignis) {
  if (!Ereignis)
    Ereignis = window.event;
  if (Ereignis.which) {
    Tastencode = Ereignis.which;
  } else if (Ereignis.keyCode) {
    Tastencode = Ereignis.keyCode;
  }
  if(Tastencode=="13")
  {
    document.eprooforms.submit();
  }

}
document.onkeydown = TasteGedrueckt;

</script>



<br><br><table height="" align="center">
<tr><td>Name:</td><td><input type="text" name="name" size="20" id="erstes"></td></tr>
<tr><td>Kundennummer:</td><td><input type="text" name="kundennummer" size="20"></td></tr>
<tr><td>PLZ:</td><td><input type="text" name="plz" size="20" value=""></td></tr>
<tr><td>Rechnung:</td><td><input type="text" name="auftrag" size="20" value=""></td></tr>

</table>
<br><br>
<script type="text/javascript">document.getElementById("erstes").focus(); </script>

[ERGEBNISSE]

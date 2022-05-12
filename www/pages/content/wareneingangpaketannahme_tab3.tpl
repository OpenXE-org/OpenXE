<form action="" method="post" name="eprooform">
<br> 
<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr classname="orange1" class="orange1" bordercolor="" align="" bgcolor="" height="" valign="">
        <td colspan="3" bordercolor="" class="" align="" bgcolor="" height="" valign=""><br></td>
      </tr>

      <tr valign="top" colspan="3">
        <td>
<fieldset><legend>{|Zustand / Kosten|}</legend>
        <table width="100%">
          <tr valign="top"><td>Zustand der Verpackung:</td><td><select name="verpackungszustand"><option value="1">In Ordnung</option><option value="0">besch&auml;digt</option></select></td>
          <td>&nbsp;</td><td>Bemerkung:</td><td><textarea name="bemerkung" rows="5" cols="40"></textarea></td></tr>
          <tr><td>Annahmekosten:</td><td><select name="zahlung"><option value="keine">keine</option><option value="nachnahme">Nachnahme</option>
	    <option value="zoll">Zoll</option><option value="porto">Porto</option></select></td>
            <td>&nbsp;</td><td>Betrag in EUR:</td><td><input type="text" name="betrag" size="10"></td></tr>
        </table>
</fieldset>
<fieldset><legend>{|Gewicht / Foto |}</legend>
        <table width="100%" border="0">
          <tr valign="top"><td>[MELDUNG]
	  <br><br>Gewicht in Gramm:&nbsp;<input type="text" name="gewicht" size="10" value="[GEWICHT]">
	  </td>
          <td align="right" width="400">[LIVEFOTO]<input type="hidden" name="foto" value="[FOTO]"></td></tr>
        </table>


</fieldset>

</td>
      </tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="center" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="button" onclick="window.location.reload();" value="Foto nochmal schie&szlig;en">
    <input type="submit" name="submit"
    value="Weiter" /></td>
    </tr>

    </tbody>
  </table>
</form>


<form action="" method="post" id="eprooform" name="eprooform">
<br> 
<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td>

<div class="info">Der Artikel ist f&uuml;r einen Mitarbeiter.</div>
[MESSAGE]
<br><br>
<table width="60%" style="background-color: #fff; border: solid 1px #000;" align="center">
<tr>
<td align="center">
<br>

<table height="200" border="0" width="450">
<tr valign="top"><td><b>Artikel:</b></td><td><u>[NAME]</u></td></tr> 
<tr valign="top"><td>Lieferant:</td><td>[LIEFERANT]</td></tr> 
<tr valign="top"><td><br></td><td align="center"></td></tr> 
<tr valign="top"><td><b>Bemerkung:</b></td><td><textarea cols="35" rows="2" name="bemerkung">[BEMERKUNG]</textarea>
</td></tr>
<tr valign="top"><td><br></td><td align="center"></td></tr>
<tr valign="top"><td nowrap><b>1. Schritt:</b></td><td>Artikel zu Mitarbeiter [MITARBEITER] bringen
[DISPLAY_WARENEINGANG_RMA_HOOK1]
<tr valign="top"><td><br></td><td align="center"><input type="submit" name="submit" value="Speichern" />&nbsp;<input type="button" onclick="window.location.href='index.php?module=wareneingang&action=distriinhalt&id=[ID]'"      value="Abbrechen" /></td></tr>
</td></tr> 
</table>

<br>
<br>
</td>
</tr>
</table>
<br><br>

</td>
      </tr>
<!--
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="button" onclick="window.location.href='index.php?module=wareneingang&action=distriinhalt&id=[ID]'"
      value="Zur&uuml;ck" />
    <input type="submit" name="submit"
    value="Weiter" /></td>
    </tr>
-->
    </tbody>
  </table>
</form>

<script type="text/javascript">
 var firstsubmit = false;

  $(document).ready(function() {
    $( "#eprooform" ).submit(function( event ) {
      if(firstsubmit)
      {
        event.preventDefault();
        return false;
      }
      firstsubmit = true;
      return true;
    });

  });
</script>

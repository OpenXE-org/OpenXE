<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<fieldset>
<form action="" method="post" id="eprooform" name="eprooform">
<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td>
[MESSAGELAGER]<br>
<table width="80%" align="center">
<tr valign="top">
<td align="center">
<table width="90%">
  <tr><td colspan="2">[MESSAGE]</td></tr>
  <tr><td><b>{|Menge|}:</b></td><td align="left"><input type="text" name="menge" value="1"  size="27" style="width:200px" id="menge">[MSGMENGE]</td></tr>
  <tr valign="top"><td><b>{|Artikelnummer|}:</b></td><td align="left">[NUMMERAUTOSTART]<input type="text" name="nummer" style="width:200px" id="nummer" value="[NUMMER]" [ARTIKELSTYLE]  size="27">[NUMMERAUTOEND][MSGARTIKEL]</td></tr>
  <tr><td>{|Grund|}:</td><td><input type="text" id="grundreferenz" name="grundreferenz" value="[GRUNDREFERENZ]"  size="27" style="width:200px"></td></tr>
  <tr><td><b>{|Ziellager|}:</b></td><td><input type="text" id="ziellager" name="ziellager" value="[ZIELLAGER]"  size="27" style="width:200px"></td></tr>
</table>
<br>
</td>
<td><div style="height: 200px; overflow: auto;"><table>[SRNINFO]</table></div></td>
</tr>
</table>
<br><br>
</td>
      </tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
<table width="100%"><tr><td>
</td><td align="right">
       <input type="submit" name="submit" value="{|Weiter|}" />
</td></tr></table>
</td>
    </tr>

    </tbody>
  </table>
</form>
</fieldset>
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
<script type="text/javascript">
      document.getElementById("[FOCUS]").focus();
</script>


</div>
<!-- tab view schließen -->
</div>



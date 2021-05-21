<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs1">Aktionscode</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs1">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>{|Einstellung|}</legend>
    <table width="100%">
          <tr><td width="150">{|Code|}:</td><td>[CODE][MSGCODE]&nbsp;</td></tr>
   	  <tr><td width="150">{|Beschriftung|}:</td><td>[BESCHRIFTUNG][MSGBESCHRIFTUNG]</td></tr>
        <tr><td width="150">{|inaktiv|}:</td><td>[AUSBLENDEN][MSGAUSBLENDEN]</td></tr>
          <tr><td width="150">{|Bemerkung|}:</td><td>[BEMERKUNG][MSGBEMERKUNG]</td><td></tr>
</table></fieldset>

</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" name="submit"/>
    </tr>
  
    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
</div>



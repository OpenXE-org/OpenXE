<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Ticket Vorlagen</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>{|Einstellung|}</legend>
    <table width="100%">
          <tr><td width="150">{|Vorlage|}:</td><td>[VORLAGENNAME][MSGVORLAGENNAME]</td><td></tr>
          <tr><td>{|In-Aktiv|}:</td><td>[SICHTBAR][MSGSICHTBAR]</td></tr>
          <tr><td>{|Projekt|}:</td><td>[PROJEKTSTART][PROJEKT][MSGPROJEKT][PROJEKTENDE]</td></tr>
          <tr><td>{|Text|}:</td><td>[VORLAGE][MSGVORLAGE]</td><td></tr>
</table></fieldset>

</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" />
    </tr>
  
    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
</div>

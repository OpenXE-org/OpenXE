<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs1">Vorlage</a></li>
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
    <table width="100%" class="mkTableFormular">
   	<tr><td>{|Name|}:</td><td>[NAME][MSGNAME]</td></tr>
   	<tr><td>{|Mitarbeiter|}:</td><td>[ADRESSE][MSGADRESSE]</td></tr>
   	<tr><td>{|Datum|}:</td><td>[DATUM][MSGDATUM]</td></tr>
   	<tr><td>{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]</td></tr>
    <tr><td>{|Bemerkung|}:</td><td>[BEMERKUNG][MSGBEMERKUNG]</td><td></tr>
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

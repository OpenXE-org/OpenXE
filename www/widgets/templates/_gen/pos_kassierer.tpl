<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs1"></a></li>
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
<fieldset><legend>{|Kassierer|}</legend>
    <table width="100%">
          <tr><td width="150">{|Mitarbeiter|}:</td><td>[ADRESSE][MSGADRESSE]&nbsp;</td></tr>
          <tr><td width="150">{|Kasse / Filliale / Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]&nbsp;</td></tr>
          <tr><td width="150">{|Kassierernummer|}:</td><td>[KASSENKENNUNG][MSGKASSENKENNUNG]</td></tr>
          <tr><td width="150">{|Inaktiv|}:</td><td>[INAKTIV][MSGINAKTIV]</td></tr>
</table>


</fieldset>
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



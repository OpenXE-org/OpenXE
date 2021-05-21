<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Kassenbuchung</a></li>
    </ul>



<div id="tabs-1">
[MESSAGE]

<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>{|Kasse|}</legend>
    <table width="100%">
          <tr><td>{|Datum|}:</td><td>[DATUM][MSGDATUM]</td></tr>
          <tr><td>{|Auswahl|}:</td><td>[AUSWAHL][MSGAUSWAHL]</td></tr>
	  <tr><td>{|Betrag|}:</td><td>[BETRAG][MSGBETRAG]</td><td></tr>
	  <tr><td>{|Steuer|}:</td><td>[STEUERGRUPPE][MSGSTEUERGRUPPE]</td><td></tr>
	  <tr><td>{|Kundenbuchung|}:</td><td>[KUNDENBUCHUNG][MSGKUNDENBUCHUNG]</td><td></tr>
	  <tr><td>{|Kunde (wenn Kundenbuchung)|}:</td><td>[ADRESSEAUTOSTART][ADRESSE][MSGADRESSE][ADRESSEAUTOEND]</td><td></tr>
          <tr><td>{|Projekt|}:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
          <tr><td>{|Belegfeld|}:</td><td>[GRUND][MSGGRUND]</td></tr>
          <tr><td>{|Suchfeld|}:</td><td>[STORNIERT_GRUND][MSGSTORNIERT_GRUND]</td></tr>
</table></fieldset>
</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="anlegen"
    value="Speichern" /> </td>
    </tr>
  
    </tbody>
  </table>
</form>
</div>

</div>

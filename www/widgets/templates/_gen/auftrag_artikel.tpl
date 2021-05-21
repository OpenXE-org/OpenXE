 
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

 <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="70%">
    <tbody>
      <tr classname="orange1" class="orange1" bordercolor="" align="" bgcolor="" height="" valign="">
        <td colspan="3" bordercolor="" class="" align="" bgcolor="" height="" valign="">St&uuml;ckliste<br></td>
      </tr>

      <tr valign="top" colspan="3">
        <td>

<table border="0" width="100%">
            <tbody>
	      <tr><td>Artikel Nr.:</td><td>[AUTOSTART][ARTIKEL][MSGARTIKEL][AUTOEND]nur nicht lagerartikel</td></tr>
	      <tr><td>{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td></tr>
	      <tr><td>{|Menge|}:</td><td>[MENGE][MSGMENGE]</td></tr>
	      <tr><td>{|Preis (netto)|}:</td><td>[PREIS][MSGPREIS]</td></tr>
	      <tr><td>{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]</td></tr>
	      <tr><td>Beschreibung</td><td>[BEMERKUNG][MSGBEMERKUNG]</td></tr>
	      <tr><td>{|wiederholender Artikel|}:</td><td>[WIEDERHOLEND][MSGWIEDERHOLEND]</td></tr>
	      <tr><td><br><b>wiederholender Artikel</b></td><td></td></tr>
	      <tr><td>{|Startdatum|}:</td><td>[STARTDATUM][MSGSTARTDATUM]</td></tr>
	      <tr><td>{|Enddatum|}:</td><td>[ENDDATUM][MSGENDDATUM]</td></tr>
	      <tr><td>{|Zahlzyklus (in Monaten)|}:</td><td>[ZAHLZYKLUS][MSGZAHLZYKLUS]</td></tr>
	      <tr><td>{|Abgrechnet bis|}:</td><td>[ABGERECHNETBIS][MSGABGERECHNETBIS]</td></tr>
	      <tr><td>Bemerkung (intern)</td><td>[BEMERKUNG][MSGBEMERKUNG]</td></tr>
              <tr><td colspan="2"><br></td></tr>
</tbody></table>
</td>
      </tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Speichern" /> <input type="button" value="Abbrechen" /></td>
    </tr>


    </tbody>
  </table>

</form>

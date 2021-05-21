 <script type="text/javascript"><!--

      jQuery(document).ready(function() {
        expertetogl();
      });

      function expertetogl(cmd)
      {
        var inp = 'tr';
        jQuery('table.expertenmodus').find(inp).prop('hidden', true);
        jQuery('table.expertenmodus').find(inp).first().prop('hidden', false);
        if(document.getElementById('experte').checked)
        {
          jQuery('table.expertenmodus').find(inp).prop('hidden', false);
        }
      }

      //-->
  </script>


<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]
 <table class="tableborder" border="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td>

<fieldset><legend>{|Allgemein|}</legend>
<table border="0" width="100%">
              <tr><td width="200">{|Artikel|}:</td><td>[ARTIKEL][MSGARTIKEL]</td></tr>
              <tr><td width="200">{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td></tr>
              <tr><td>{|Beschreibung|}:</td><td>[BESCHREIBUNG][MSGBESCHREIBUNG]</td></tr>
	      <tr><td width="200">{|Beschreibungstext ersetzen|}:</td><td>[BESCHREIBUNGERSETZTEN][MSGBESCHREIBUNGERSETZTEN]&nbsp;<i>Es wird nur die Beschreibung von hier ohne Artikelbeschreibung aus den Stammdaten angezeigt.</td></tr>
	      <tr><td>{|Menge|}:</td><td>[MENGE][MSGMENGE]</td></tr>
	      <tr><td>{|Preis (netto)|}:</td><td>[PREIS][MSGPREIS]&nbsp;[PREISART][MSGPREISART]</td></tr>
	      <tr><td>{|Rabatt|}:</td><td>[RABATT][MSGRABATT]&nbsp;<i>(Optional in %)</i></td></tr>
	      <tr><td>{|Automatisch anlegen als|}:</td><td>[DOKUMENT][MSGDOKUMENT]&nbsp;{|in Gruppe |} [GRUPPE][MSGGRUPPE]&nbsp;{|Reihenfolge|}:&nbsp;[SORT][MSGSORT]&nbsp;<i>Optional</i></td></tr>
</table>
</fieldset>
<fieldset><legend>{|Wiederholende Zahlung Einstellungen|}</legend>
<table border="0" width="100%">
<!--	      <tr><td width="200">{|wiederholender Artikel|}:</td><td>[WIEDERHOLEND][MSGWIEDERHOLEND]</td></tr>-->
	      <tr><td width="200">{|Erstes Startdatum|}:</td><td>[STARTDATUM][MSGSTARTDATUM][DATUM_START]&nbsp;<i>Feld <b>nicht</b> nach ersten Abolauf &auml;ndern.</i></td></tr>
	      <tr><td>{|Zahlzyklus|}:</td><td>[ZAHLZYKLUS][MSGZAHLZYKLUS]&nbsp;<i>(in Wochen, Monaten oder Jahren)</i></td></tr>

	      <tr><td width="200">{|Enddatum|}:</td><td>[ENDDATUM][MSGENDDATUM][DATUM_START]&nbsp;<i>fr&uuml;hester beachteter Zeitpunkt: [ZEITPUNKT] bzw. muss es nach "Abgerechnet bis" sein</i></td></tr>

</table>
</fieldset>
<fieldset><legend>{|Wiederholende Zahlung Einstellungen|}</legend>
<table border="0" width="100%" class="expertenmodus">
	      <tr><td width="200">{|Expertenmodus|}:</td><td>[EXPERTE][MSGEXPERTE]</td></tr>
	      <tr><td>{|Abgerechnet bis|}:</td><td>[ABGERECHNETBIS][MSGABGERECHNETBIS]&nbsp;<i>Feld <b>nicht</b> nach ersten Abolauf &auml;ndern!</i></td></tr>
	      <tr><td>Bemerkung (intern)</td><td>[BEMERKUNG][MSGBEMERKUNG]</td></tr>
</table>
</fieldset>

</td>
      </tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Speichern" />[ABBRECHEN]</td>
    </tr>
   

    </tbody>
  </table>
</form>


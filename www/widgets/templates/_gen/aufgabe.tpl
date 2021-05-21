<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>

<div id="tabs-1">

<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">


<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]
<fieldset><legend>{|Allgemein|}</legend>
          <table border="0">
	    [MESSAGE]
            <tbody>
	      <tr><td width="200">{|Aufgabe|}:</td><td>[AUFGABE][MSGAUFGABE]</td></tr>
	      <tr><td>{|Mitarbeiter|}:</td><td>[ADRESSE][MSGADRESSE]</td></tr>
	      <tr><td>{|f&uuml;r Kunde|}:</td><td>[KUNDE][MSGKUNDE][KUNDEBUTTON]</td></tr>
	      <tr><td>Beschreibung:<br><i>(Optional Text auf Pinwand)</i></td><td>[BESCHREIBUNG][MSGBESCHREIBUNG]</td></tr>
	      <tr><td>{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT][PROJEKTBUTTON]</td></tr>
	      <tr><td>{|Teilprojekt|}:</td><td>[TEILPROJEKT][MSGTEILPROJEKT]</td></tr>
	      <tr><td></td><td><br></td></tr>
	      <tr><td>{|Prio|}:</td><td>
		  <table cellspacing="0" width="100%" cellpadding="0" border="0"><tr><td>[PRIO][MSGPRIO]
                  </td><td align="right">Geplante Dauer in h:&nbsp;[STUNDEN][MSGSTUNDEN]</td></tr></table>
	      </td></tr>
    
            <tr><td width="200">{|Datum / Abgabe bis|}:</td><td><table cellspacing="0" cellpadding="0" width="100%" border="0"><tr><td>
                    [ABGABE_BIS][MSGABGABE_BIS]</td><td align="right">Uhrzeit:&nbsp;[ABGABE_BIS_ZEIT][MSGABGABE_BIS_ZEIT]</td></tr></table>

</td></tr>

	      <tr><td>{|Regelm&auml;&szlig;ig (Intervall)|}:</td><td>
<table cellspacing="0" cellpadding="0" border="0"><tr><td>
		  [INTERVALL_TAGE][MSGINTERVALL_TAGE]
</td><td align="right">
&nbsp;Zeiterfassung Pflicht:&nbsp;
[ZEITERFASSUNG_PFLICHT][MSGZEITERFASSUNG_PFLICHT]
Zeit wird abgerechnet:&nbsp;
[ZEITERFASSUNG_ABRECHNUNG][MSGZEITERFASSUNG_ABRECHNUNG]

</td></tr></table>
</td></tr></table>
</fieldset>

   </div>    </div>

    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">


<fieldset><legend>{|Einstellungen|}</legend>

          <table border="0">
        <tr><td width="200">{|E-Mail Erinnerung|}:</td><td>[EMAILERINNERUNG][MSGEMAILERINNERUNG]&nbsp;</td></tr>
        <tr><td>{|E-Mail Anzahl Tage zuvor|}:</td><td>[EMAILERINNERUNG_TAGE][MSGEMAILERINNERUNG_TAGE]&nbsp;<i>(in Tagen)</i></td></tr>

	    <tr><td>{|Countdown auf Startseite|}:</td><td>[VORANKUENDIGUNG][MSGVORANKUENDIGUNG]&nbsp;<i>(in Tagen)</i></td></tr>
 
	      <tr><td>{|&Ouml;ffentlich|}:</td><td>[OEFFENTLICH][MSGOEFFENTLICH]</td></tr>
	      <tr><td>{|Auf Startseite|}:</td><td>[STARTSEITE][MSGSTARTSEITE]</td></tr>

	      <tr><td>{|Auf Pinwand|}:</td><td>[PINWAND][MSGPINWAND]&nbsp;Farbe:&nbsp;[NOTE_COLOR][MSGNOTE_COLOR]&nbsp;Pinwand:&nbsp;[PINWAND_ID][MSGPINWAND_ID]
</td></tr>
</table>
</fieldset>
<fieldset><legend>{|Notizen|}</legend>

          <table border="0" width="100%">
	      <tr valign="top"><td colspan="2">[SONSTIGES][MSGSONSTIGES]</td></tr>
	</table>
</fieldset>

<fieldset><legend>{|Status|}</legend>
          <table border="0" width="100%">
	      <tr><td>{|Status|}:</td><td>[STATUS][MSGSTATUS]</td></tr>
	</table>
</fieldset>


</div></div>
</div></div>

<center>
     <table border="0" width="100%">
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
[AUFGABEABSCHLIESSEN]
    <input type="submit"
    value="Speichern" />[ABBRECHEN]</td>
    </tr>
    </tbody>
  </table>
</center>

</form>
</div>
</div>


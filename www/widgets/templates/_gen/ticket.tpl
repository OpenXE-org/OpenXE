<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]


<table border="0" width="100%">
<tr><td><table width="100%"><tr><td>

[MELDUNG]
<br>

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr classname="orange1" class="orange1" bordercolor="" align="" bgcolor="" height="" valign="">
        <td colspan="4" bordercolor="" class="" align="" bgcolor="" height="" valign="">Ticket [SCHLUESSEL]<br></td>
      </tr>


<tr valign="top"><td colspan="4">

 <table>
  <tr><td width="70">{|Von|}:</td><td>[NAME] &lt;[EMAIL]&gt;</td></tr>
  <tr><td>{|Betreff|}:</td><td><b>[BETREFF]</b></td></tr>
  </table>
  <br>
[TEXT]
<table width="100%"><tr valign="bottom"><td>{|
Kunden zuordnen|}:</td><td width="80%">&nbsp;[KUNDEAUTOSTART][KUNDE][KUNDEAUTOEND]
</td></tr></table>
<br>
<fieldset><legend>Anh&auml;nge:</legend>
[ANHAENGE]
</fieldset>

</td></tr>

<tr valign="top"><td colspan="4">

<table width="100%"><tr><td><fieldset><legend>{|Kunde|}</legend>
<table width="100%" height="140">
<tr><td >Kunde: </td><td><b>[NAME]</b></td></tr>
<tr><td>Kontakt: </td><td>[EMAIL]</td></tr>
<tr><td>Zeit: </td><td>[ZEIT]</td></tr>
<tr><td>Wartezeit: </td><td><b>[WARTEZEIT]</b></td></tr>
<tr><td>Quelle: </td><td>[QUELLE]</td></tr>
</table>
</fieldset></td><td><fieldset><legend>{|Zuordnung|}</legend>
<table height="140">
<tr><td>Prio: </td><td><b>[PRIO]</b></td></tr>
<tr><td>Warteschlange: </td><td>[WARTESCHLANGE]</td></tr>
<tr><td>Projekt: </td><td>[PROJEKT]</td></tr>
</table>
</fieldset></td></tr></table>

</td></tr>

<tr valign="top">
[TEST][MSGTEST]
<tr><td>
<fieldset><legend>Gerspr&auml;chsverlauf</legend>
[TABLE]
</fieldset>
</td>
</tr>
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="4" bordercolor="" classname="orange2" class="orange2">

<table width="100%"><tr><td>[ZURUECK]</td><td align="right">
[LESENSTART]
<input type="button" value="Ticket als beantwortet markieren" onclick="window.location.href='index.php?module=ticket&action=beantwortet&id=[TICKETNACHRICHTID]'">
<input type="button" name="abschicken" onclick="javascript:window.open('index.php?module=ticket&action=antwort&message=[LASTMESSAGE]','popup','location=no,menubar=no,toolbar=no,status=no,resizable=yes,scrollbars=yes,width=1000,height=800')"
    value="Ticket beantworten" />
<input type="submit" name="abschicken"
    value="&Auml;nderung &uuml;bernehmen" /> 
[LESENENDE]
</td></tr></table>

    </tr>
  
    </tbody>
  </table>
  </form>
</td></tr></table></td></tr>
</table>


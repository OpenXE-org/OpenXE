[SAVEPAGEREALLY]

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-2">Kalkulation</a></li>
        <li><a href="#tabs-4" onclick="callCursor();">Positionen</a></li>
       <li><a href="index.php?module=kalkulation&action=inlinepdf&id=[ID]&frame=true#tabs-3">Vorschau</a></li>
    </ul>


<div id="tabs-2">
[MESSAGE]
<form action="" method="post" name="eprooform" id="eprooform">
[FORMHANDLEREVENT]

<center>
<table width="100%" cellpadding="0" cellspacing="5" border="0" align="center">
<!-- // rate anfang -->
<tr><td>

<!-- // ende anfang -->
<table width="100%" align="center" style="background-color:#cfcfd1;">
<tr>
<td width="33%"></td>
<td align="center"><b style="font-size: 14pt">Kalkulation <font color="blue">[NUMMER]</font></b>[KUNDE]</td>
<td width="33%" align="right" nowrap>[ICONMENU]&nbsp;[SAVEBUTTON]</td>
</tr>
</table>

<table width="100%"><tr valign="top"><td width="50%">

<fieldset><legend>{|Kunde|}</legend>
<table width="100%">
  <tr><td width="120">Kunden-Nr.:</td><td nowrap>[KUNDEAUTOSTART][ADRESSE][MSGADRESSE][KUNDEAUTOEND]&nbsp;
[BUTTON_UEBERNEHMEN]
</td></tr>
   <tr><td>{|Kundenname|}:</td><td>[NAME][MSGNAME]</td>

</table>
</fieldset>


<fieldset><legend>{|Allgemein|}</legend>
<table width="100%">
  <tr><td>{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td>
  <tr><td width="120">{|Projekt|}:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
  <tr><td>{|Status|}:</td><td>[STATUS]</td></tr>
  <tr><td>{|Datum|}:</td><td>[DATUM][MSGDATUM]</td></tr>
  <tr><td>{|Schreibschutz|}:</td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>


</table>
</fieldset>



</td><td>



</td></tr></table>

<!--
<table width="100%"><tr><td>
<fieldset><legend>{|Positionen|}</legend>
[POSITIONEN]
</fieldset>
</td></tr></table>
-->

<table width="100%"><tr><td>
<fieldset><legend>{|Freitext|}</legend>
[FREITEXT][MSGFREITEXT]
</fieldset>
</td></tr></table>


<table width="100%"><tr valign="top"><td width="50%">


<fieldset><legend>{|Kalkulation|}</legend>
<table width="100%">
<tr><td width="120">{|Bearbeiter|}:</td><td>[BEARBEITER][MSGBEARBEITER]</td></tr>
<tr><td>{|Kein Briefpapier und Logo|}:</td><td>[OHNE_BRIEFPAPIER][MSGOHNE_BRIEFPAPIER]</td></tr>

</table>
</fieldset>


</td><td>




</td></tr></table>
<table width="100%"><tr><td>
<fieldset><legend>{|Interne Bemerkung|}</legend>
[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]
</fieldset>
</td></tr></table>

</center>


</td>
</table>

<br><br>
<table width="100%">
<tr><td align="right">
    <input type="submit" name="speichern" name="submit"
    value="Speichern" />
</td></tr></table>
</div>


</form>

<div id="tabs-4">
<div class="overflow-scroll">
<!-- // rate anfang -->
<table width="100%" cellpadding="0" cellspacing="5" border="0" align="center">
<tr><td>


<!-- // ende anfang -->
<table width="100%" style="" align="center">
<tr>
<td width="33%"></td>
<td align="center"><b style="font-size: 14pt">Kalkulation <font color="blue">[NUMMER]</font></b>[KUNDE]</td>
<td width="33%" align="right">[ICONMENU2]</td>
</tr>

</table>


[POS]

</td></tr></table>




</div>
</div>
<div id="tabs-3"></div>


 <!-- tab view schließen -->
</div>


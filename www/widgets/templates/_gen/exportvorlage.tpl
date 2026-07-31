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
        <td>
<fieldset><legend>{|Einstellung|}</legend>
    <table width="100%" border="0">
   <tr><td width="130">{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td><td></td></tr>

   <tr><td width="130">{|Quelle|}:</td><td colspan="2">
		[ZIEL][MSGZIEL]&nbsp;<i>(Auswahl Quelltabelle f&uuml;r Daten)</i></td></tr>
   <tr><td width="130">{|CSV Beschriftung|}:</td><td>[EXPORTERSTEZEILENUMMER][MSGEXPORTERSTEZEILENUMMER]&nbsp;<i>Erste Zeile = Beschriftung</i></td><td></td></tr>
    <tr><td width="130">{|CSV Trennzeichen|}:</td><td>[EXPORTTRENNZEICHEN][MSGEXPORTTRENNZEICHEN]</td><td></td></tr>
    <tr><td width="130">{|CSV Maskierung|}:</td><td>[EXPORTDATENMASKIERUNG][MSGEXPORTDATENMASKIERUNG]</td><td></td></tr>
   <tr><td width="130">{|Filter Datum|}:</td><td>[FILTERDATUM][MSGFILTERDATUM]&nbsp;<i>Bei der Ausgabe kann man ein Datumsbereich angeben</i></td><td></td></tr>
   <tr><td width="130">{|Filter Projekt|}:</td><td>[FILTERPROJEKT][MSGFILTERPROJEKT]&nbsp;<i>Bei der Ausgabe kann man ein Projekt angeben</i></td><td></td></tr>

   <tr><td width="130">{|API Freigabe|}:</td><td>[APIFREIGABE][MSGAPIFREIGABE]&nbsp;<i>Abfrage &uuml;ber API freigeben</i></td><td></td></tr>
<tr valign="top"><td width="130">{|CSV Felder|}:</td><td><table><tr valign="top"><td>[FIELDS][MSGFIELDS]</td><td><i>Feldname;<br>Feldname;<br></td></tr>
</table>

</td><td align="center">
</td></tr>
<tr valign="top"><td width="130">{|Filter|}:</td><td><table><tr valign="top"><td>[FIELDS_WHERE][MSGFIELDS_WHERE]</td><td><i>Feldname > 1;<br>Feldname LIKE '8%';<br></td></tr>
</table>

</td><td align="center">
</td></tr>
<tr><td width="130">{|Interne Bemerkung|}:</td><td>[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]</td><td></td></tr>
<!--   <tr><td width="130">{|Letzter Export|}:</td><td><input type="text" name="letzterexport" size="40"</td></tr>
   <tr><td width="130">{|Von Mitarbeiter|}:</td><td><input type="text" name="mitarbeiterletzterexport" size="40"</td></tr>-->

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



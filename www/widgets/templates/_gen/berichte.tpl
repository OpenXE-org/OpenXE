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
<fieldset><legend>{|Einstellung|}</legend>
    <table width="100%">
          <tr><td width="150">{|Name|}:</td><td>[NAME][MSGNAME]</td></tr>
          <tr><td width="150">{|Projekt|}:</td><td>[PROJECT][MSGPROJECT]</td></tr>
          <tr><td width="150">{|Beschreibung|}:</td><td>[BESCHREIBUNG][MSGBESCHREIBUNG]</td><td></tr>
          <tr><td width="150">{|Variablen|}:</td><td>[VARIABLEN][MSGVARIABLEN]</td><td></tr>
</table></fieldset>
<fieldset><legend>{|Struktur|}</legend>
    <table width="100%">
          <tr><td width="150">{|SQL-Statement|}:</td><td>[STRUKTUR][MSGSTRUKTUR]</td><td></tr>
          <tr><td width="150">{|Spaltennamen|}:</td><td>[SPALTENNAMEN][MSGSPALTENNAMEN]<br><i>Mit Semikolon getrennt Spaltennamen angeben.</i></td></tr>
          <tr><td width="150">{|Spaltenbreite|}:</td><td>[SPALTENBREITE][MSGSPALTENBREITE]<br><i>Mit Semikolon getrennt in Millimeter Spaltenbreite angeben. Gesamtbreite: 190 mm)</i></td></tr>
          <tr><td width="150">{|Spaltenausrichtung|}:</td><td>[SPALTENAUSRICHTUNG][MSGSPALTENAUSRICHTUNG]<br><i>Mit Semikolon getrennt Ausrichtung je Spalte (R,L,C) angeben.</i></td></tr>
          <tr><td width="150">{|Summenspalten|}:</td><td>[SUMCOLS][MSGSUMCOLS]<br><i>Mit Semikolon getrennt Spaltennummern angeben.</i></td></tr>
          <tr><td width="150">{|Interne Bemerkung|}:</td><td>[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]</td><td></tr>

</table></fieldset>

<div class="row">
<div class="row-height">
  <div class="col-xs-12 col-md-6 col-md-height" style="padding-left:10px;">
  <div class="inside inside-full-height">

    <fieldset><legend>{|FTP Übertragung|}</legend>
    <table width="100%">
          <tr><td width="150">{|aktivieren|}:</td><td>[FTPUEBERTRAGUNG][MSGFTPUEBERTRAGUNG]</td></tr>
          <tr><td width="150">{|Passive Mode verwenden|}:</td><td>[FTPPASSIVEMODE][MSGFTPPASSIVEMODE]</td></tr>
          <tr>
            <td>{|Typ|}:</td>
            <td>
              [TYP][MSGTYP]
            </td>
          </tr>
          <tr><td>{|FTP Host|}:</td><td>[FTPHOST][MSGFTPHOST]</td><td>{|FTP Port|}:</td><td>[FTPPORT][MSGFTPPORT]<small><i>Falls leer wird Port 21 verwendet.</i></small></td></tr>
          <tr><td>{|FTP Benutzer|}:</td><td>[FTPUSER][MSGFTPUSER]</td></tr>
          <tr><td>{|FTP Passwort|}:</td><td>[FTPPASSWORD][MSGFTPPASSWORD]</td></tr>
          <tr><td>{|Uhrzeit|}:</td><td>[FTPUHRZEIT][MSGFTPUHRZEIT]</td></tr>
          <tr><td>{|Dateiname|}:</td><td>[FTPNAMEALTERNATIV][MSGFTPNAMEALTERNATIV]</td></tr>
          <tr><td colspan="2"><small><i>Wildcards:{TIMESTAMP}{DATUM}{BERICHTNAME}. Falls leer wird der Standardname verwendet.</i></small></td></tr>
    </table></fieldset>

  </div>
  </div>
  <div class="col-xs-12 col-md-6 col-md-height">
  <div class="inside inside-full-height">

    <fieldset><legend>{|Per E-Mail versenden|}</legend>
    <table width="100%">
          <tr><td width="150">{|aktivieren|}:</td><td>[EMAILUEBERTRAGUNG][MSGEMAILUEBERTRAGUNG]</td></tr>
          <tr><td width="150">{|E-Mail Empf&auml;nger|}:</td><td>[EMAILEMPFAENGER][MSGEMAILEMPFAENGER]<i>Mehrere Angaben mit Semikolon getrennt m&ouml;glich.</i></td></tr>
          <tr><td width="150">{|E-Mail Betreffzeile|}:</td><td>[EMAILBETREFF][MSGEMAILBETREFF]</td></tr>
          <tr><td>&nbsp;</td></tr>
          <tr><td width="150">{|Uhrzeit|}:</td><td>[EMAILUHRZEIT][MSGEMAILUHRZEIT]</td></tr>
          <tr><td width="150">{|Dateiname|}:</td><td>[EMAILNAMEALTERNATIV][MSGEMAILNAMEALTERNATIV]</td></tr>
          <tr><td colspan="4"><small><i>Wildcards:{TIMESTAMP}{DATUM}{BERICHTNAME}. Falls leer wird der Standardname verwendet.</i></small></td></tr>
    </table></fieldset>

  </div>
  </div>
</div>
</div>



<div class="row">
<div class="row-height">
  <div class="col-xs-12 col-md-6 col-md-height" style="padding-left:10px;">
  <div class="inside inside-full-height">

    <fieldset><legend>{|Aktions Men&uuml;|}</legend>
    <table width="100%">
          <tr><td width="150">{|aktivieren|}:</td><td>[DOCTYPE_ACTIONMENU][MSGDOCTYPE_ACTIONMENU]</td></tr>
          <tr>
            <td>{|Beleg|}:</td>
            <td>
              [DOCTYPE][MSGDOCTYPE]
            </td>
          </tr>
          <tr>
            <td>{|Format|}:</td>
            <td>
              [DOCTYPE_ACTIONMENUFILETYPE][MSGDOCTYPE_ACTIONMENUFILETYPE]
            </td>
          </tr>
          <tr><td>{|Beschriftung|}:</td><td>[DOCTYPE_ACTIONMENUNAME][MSGDOCTYPE_ACTIONMENUNAME]</td></tr>
    </table></fieldset>

  </div>
  </div>
  <div class="col-xs-12 col-md-6 col-md-height">
  <div class="inside inside-full-height">

  </div>
  </div>
</div>
</div>





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



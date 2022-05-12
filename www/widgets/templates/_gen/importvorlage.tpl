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

  <div class="row">
    <div class="row-height">
      <div class="col-xs-12 col-md-10 col-md-height">
        <div class="inside inside-full-height">

          <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
            <tbody>
              <tr valign="top" colspan="3">
                <td>
        <fieldset><legend>{|Einstellung|}</legend>
            <table width="100%" border="0">
           <tr><td width="130">{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td><td></td></tr>

           <tr><td width="130">{|Ziel|}:</td><td colspan="2">
            [ZIEL][MSGZIEL]&nbsp;<i>(Auswahl Zieltabelle f&uuml;r die Daten)</i></td></tr>
           <tr><td width="130">{|CSV Daten ab Zeile|}:</td><td>[IMPORTERSTEZEILENUMMER][MSGIMPORTERSTEZEILENUMMER]&nbsp;<i>Erste Zeile = 1 (Falls Daten in CSV nicht ab Zeile 1 starten, da Feldbezeichnungen o.&auml;. in Dokument vorhanden sind.)</i></td><td></td></tr>
            <tr><td width="130">{|CSV Trennzeichen|}:</td><td>[IMPORTTRENNZEICHEN][MSGIMPORTTRENNZEICHEN]</td><td></td></tr>
            <tr><td width="130">{|CSV Maskierung|}:</td><td>[IMPORTDATENMASKIERUNG][MSGIMPORTDATENMASKIERUNG]</td><td></td></tr>
            <tr><td width="130">{|Auswahl Charset|}:</td><td>[SELCHARSET]</td><td></td></tr>
            <tr><td width="130">{|Charset|}:</td><td>[CHARSET][MSGCHARSET]</td><td></td></tr>
            <!--<tr><td width="130">{|UTF8 Decode|}:</td><td>[UTF8DECODE][MSGUTF8DECODE]</td><td></td></tr>-->
            <tr valign="top">
                <td width="130">{|CSV Felder|}:</td>
                <td>
                    <div style="float:left;">
                    [FIELDS][MSGFIELDS]<br>
                    </div>
                    <div style="float:left;">
                    <i>
                        Spaltennummer:Feldname;<br>
                        Spaltennummer:Feldname;<br><br>
                        z.B.<br><br>
                        1:lieferantennummer;<br>
                        2:name;<br><br>
                        Spalte 1 aus der CSV Datei soll das Feld lieferantennummer werden. Spalte 2 aus der CSV Datei soll das Feld name werden.
                    </i>
                    </div>
                </td>
                <td></td>
            </tr>
            <tr>
                <td width="130">{|Interne Bemerkung|}:</td><td>[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]</td><td></td></tr>
            <!--   <tr><td width="130">{|Letzter Import|}:</td><td><input type="text" name="letzterimport" size="40"</td></tr>
           <tr><td width="130">{|Von Mitarbeiter|}:</td><td><input type="text" name="mitarbeiterletzterimport" size="40"</td></tr>-->
        </table>
        </fieldset>

        </td></tr>

            <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
            <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
              <a href="[CSVDOWNLOADLINK]" class="button">{|Download CSV-Vorlage|}</a><input type="submit" value="Speichern" name="submit"/>
            </tr>

            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</form>

</div>

<!-- tab view schließen -->
</div>



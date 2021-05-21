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
<table><tr valign="top"><td>
    <table width="100%">
          <tr><td width="150">{|Name|}:</td><td>[NAME][MSGNAME]&nbsp;</td></tr>
          <tr><td width="150">{|XML|}:</td><td>[XML][MSGXML]</td><td></tr>
          <tr><td width="150">{|Bemerkung|}:</td><td>[BEMERKUNG][MSGBEMERKUNG]</td><td></tr>
          <tr><td width="150">{|Verwenden als|}:</td><td>[VERWENDENALS][MSGVERWENDENALS]&nbsp;
          [FORMAT][MSGFORMAT]</td></tr>

          <tr><td width="150">{|Etikett in mm angeben|}:</td><td>[MANUELL][MSGMANUELL]
                Breite:&nbsp;[LABELBREITE][MSGLABELBREITE]&nbsp;H&ouml;he:&nbsp;
                [LABELHOEHE][MSGLABELHOEHE]&nbsp;
                Abstand:&nbsp;[LABELABSTAND][MSGLABELABSTAND]&nbsp;Offset Links:&nbsp;[LABELOFFSETX][MSGLABELOFFSETX]&nbsp;
                Offset-Oben:&nbsp;[LABELOFFSETY][MSGLABELOFFSETY]
           </td><td></tr>
          <tr><td width="150">{|Etiketten pro Zeile|}:</td><td>[ANZAHLPROZEILE][MSGANZAHLPROZEILE]&nbsp;</td></tr>
</table>
<br>
<table>
<tr><td width="150">{|Textbaustein|}:</td><td>&lt;line x="5" y="1" size="3"&gt;Test&lt;/line&gt;</td></tr>
<tr><td width="150">{|Barcode|}:</td><td>&lt;barcode x="5" y="1" size="3" type="1"&gt;Test&lt;/barcode&gt;</td></tr>
<tr><td width="150" valign="top">{|QR-Code|}:</td><td>&lt;qrcode x="5" y="1" size="3" type="3"&gt;Test&lt;/qrcode&gt;<br /><br /></td></tr>
<tr><td width="150" valign="top">{|Artikel klein|}:</td><td>
  <label>
&lt;label&gt;<br>
            &nbsp;&lt;barcode x="3" y="1" size="8" type="1"&gt;{NUMMER}&lt;/barcode&gt;<br>
            &nbsp;&lt;line x="3" y="10" size="3"&gt;NR {NUMMER}&lt;/line&gt;<br>
            &nbsp;&lt;line x="3" y="13" size="3"&gt;{NAME_DE}&lt;/line&gt;<br>
  &lt;/label&gt;<br /><br />
</td></tr>

<tr><td width="150" valign="top">{|Lager klein|}:</td><td>
&lt;label&gt;<br>
            &nbsp;&lt;barcode x="3" y="1" size="8" type="1"&gt;{KURZBEZEICHNUNG}&lt;/barcode&gt;<br>
            &nbsp;&lt;line x="3" y="10" size="4"&gt;Lager: {KURZBEZEICHNUNG}&lt;/line&gt;<br>
            &lt;/label&gt;<br><br />
</td></tr>

<tr><td width="150" valign="top">{|EAN13 Barcode|}:</td><td>
&lt;label&gt;<br>
            &nbsp;&lt;barcode x="3" y="1" size="8" type="E30"&gt;{EAN}&lt;/barcode&gt;<br>
            &nbsp;&lt;/label&gt;<br>
</td></tr>
<tr><td width="150" valign="top">{|Etikettenbild|}:</td><td>
&lt;label&gt;<br>
            &nbsp;&lt;image x="3" y="1" size="8" width="10" height="20"&gt;{ETIKETTENBILD}&lt;/image&gt;<br>
            &lt;/label&gt;<br><br />
</td></tr>
<tr><td width="150" valign="top">{|Rechteck|}:</td><td>
&lt;label&gt;<br>
            &nbsp;&lt;rectangle x="3" y="1" width="10" height="20" size="1"&gt;&lt;/rectangle&gt;<br>
            &lt;/label&gt;<br>
</td></tr>


</table>
<br><br>

</td><td>Variablen:
<br>
<ul>
<li>{NUMMER}</li>
<li>{SERIENNUMMER}</li>
<li>{VERKAUFSPREISBRUTTO}</li>
<li>{ARTIKEL_NAME_DE}</li>
<li>{LAGER_PLATZ_NAME}</li>
<li>{LAGER_PLATZ_ID}</li>
<li>{CHARGE}</li>
<li>{MHD} (Scanner bzw. engl. Format)</li>
<li>{MHD2} (Deutsches Format)</li>
<li>{MHD3} (jjmmtt z.B. bei NVE)</li>
<li>{BELEGNR}</li>
<li>{BELEGID}</li>
<li>{BELEGART}</li>
<li>{PROJEKT}</li>
<li>{PROJEKTNAME}</li>
<li>{EIGENSCHAFT:NAME}</li>
<li>{ETIKETTENBILD} <i>aktuell nur in PDF</i></li>
<li>{BEZEICHNUNG1},{BEZEICHNUNG2}<i>Etikettendrucker 2-Zeilig</i></li>
</ul>
Produktion:
<br>
<ul>
<li>{SERIENNUMMER}</li>
<li>{CHARGENNUMMER}</li>
<li>{MHD} (Scanner bzw. engl. Format)</li>
<li>{MHD2} (Deutsches Format)</li>
<li>{CHARGEMHDALL}</li>
<li>{PRUEFER}</li>
<li>{KOMMENTAR}</li>
<li>{ZEITSTEMPEL}</li>
<li>{BELEGNR}</li>
</ul>
Produktionslabel:
<br>
<ul>
  <li>{BELEGNR}</li>
  <li>{KUNDENNAME}</li>
  <li>{KUNDENNUMMER}</li>
  <li>{DATUMAUSLIEFERUNG}</li>
  <li>{ARTIKELNAME}</li>
  <li>{ARTIKELNUMMER}</li>
  <li>{ARTIKELNUMMERKUNDE}</li>
  <li>{MENGE}</li>
  <li>{LAGERPLATZ}</li>
  <li>{CHARGE}</li>
  <li>{MHD} (Scanner bzw. engl. Format)</li>
  <li>{MHD2} (Deutsches Format)</li>
</ul>

</td></tr></table>

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



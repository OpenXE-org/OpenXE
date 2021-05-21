<!-- gehort zu tabview -->
<div id="tabview" class="yui-navset">
    <ul class="yui-nav">
        <li class="[AKTIV_GEN_TAB1]"><a href="#tab1"><em>Automatischer Prozessstarter</em></a></li>
    </ul>
    <div class="yui-content">
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div>
<table width="100%" style="background-color: #fff; border: solid 1px #000;" align="center">
<tr>
<td align="center">
<br><b style="font-size: 14pt">Prozess:</b>
<br>
<br>
Bitte einstellen - wann welcher Prozess automatisch gestartet werden soll.<br>
<br>
</td>
</tr>
</table>
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>{|Prozess|}</legend>
    <table width="100%">
          <tr><td>{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td></tr>
          <tr><td>{|Art|}:</td><td>[ART][MSGART]</td></tr>
	  <tr><td>{|Startzeit|}:</td><td>[STARTZEIT][MSGSTARTZEIT]</td><td></tr>
	  <tr><td>{|Letzte Ausf&uuml;hrung|}:</td><td>[LETZTEAUSFUERHUNG][MSGLETZTEAUSFUERHUNG]</td><td></tr>
	  <tr><td>{|Periode|}:</td><td>[PERIODE][MSGPERIODE] (in Minuten)</td><td></tr>
          <tr><td>{|Typ|}:</td><td>[TYP][MSGTYP]</td></tr>
          <tr><td>{|Parameter|}:</td><td>[PARAMETER][MSGPARAMETER]</td></tr>
          <tr><td>{|Aktiv|}:</td><td>[AKTIV][MSGAKTIV]</td></tr>
</table></fieldset>
</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" />
    </tr>
  
    </tbody>
  </table>
</form>

</div>

<!-- tab view schließen -->
</div></div>
<!-- ende tab view schließen -->

<div id="win">
    <div class="hd"></div>
    <div class="bd"></div>
</div>



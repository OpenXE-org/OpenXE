<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

<!-- gehort zu tabview -->
<div id="tabview" class="yui-navset">
    <ul class="yui-nav">
        <li class="[AKTIV_TAB1]"><a href="#tab1"><em>Beschreibung</em></a></li>
    </ul>

    <div class="yui-content">
<!-- ende gehort zu tabview -->

<div>
 <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr classname="orange1" class="orange1" bordercolor="" align="" bgcolor="" height="" valign="">
        <td colspan="3" bordercolor="" class="" align="" bgcolor="" height="" valign="">Konditionen<br></td>
      </tr>

      <tr valign="top" colspan="3">
        <td>

[MESSAGE]
<table>
<tr valign="top"><td colspan="2">

<fieldset><legend>E-Mail</legend>
<table width="100%">
  <tr><td>{|Lieferant|}:</td><td width="70%">[LIEFERANTAUTOSTART][ADRESSE][MSGADRESSE][LIEFERANTAUTOEND]</td></tr>
  <tr><td>{|Status|}:</td><td>[STATUS][MSGSTATUS]</td></tr>
</table>
</fieldset>
<!--<br>
<fieldset><legend>{|Lieferant|}</legend>
<table width="100%"><tr><td>
[LIEFERANT]
</td></tr></table>
</fieldset>
-->
</td><td>
<fieldset><legend>{|Positionen|}</legend>
[POSITIONEN]
</fieldset>

</td></tr>
<tr valign="top"><td>
<fieldset><legend>{|Anhaenge|}</legend>
<table width="100%" height="240">
<tr><td>{|Projekt|}:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
<tr><td>{|Bearbeiter|}:</td><td>[BEARBEITER]</td></tr>
<tr><td>{|Datum|}:</td><td>[DATUM][MSGDATUM]</td></tr>
<tr><td>{|Lieferdatum|}:</td><td>[LIEFERDATUM][MSGLIEFERDATUM]</td></tr>
<tr><td>{|Freitext|}:</td><td>[FREITEXT][MSGFREITEXT]</td></tr>
<tr><td>{|Eink&auml;ufer|}:</td><td>[EINKAEUFER]</td></tr>
<tr><td>{|Bestellbest&auml;stigung|}:</td><td>[BESTELLBESTAETIGUNG][MSGBESTELLBESTAETIGUNG]</td></tr>
<tr><td>{|Freigabe|}:</td><td>[FREIGABE][MSGFREIGABE]</td></tr>
</table>
</fieldset>


</td><td colspan="2">

<fieldset><legend>{|Konditionen|}</legend>
<table width="100%" height="240">
<tr><td>{|Betreff|}:</td><td>[BETREFF][MSGBETREFF]</td></tr>
<tr><td>{|Kundennummer|}:</td><td>[KUNDENNUMMER][MSGKUNDENNUMMER]</td></tr>
<tr><td>{|Lieferantennummer|}:</td><td>[LIEFERANTENNUMMER][MSGLIEFERANTENNUMMER]</td></tr>
<tr><td>{|Versandart|}:</td><td>[VERSANDART][MSGVERSANDART]</td></tr>
<tr><td>{|Zahlungsweise|}:</td><td>[ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]</td></tr>
<tr><td>{|Zahlungsziel (in Tagen)|}:</td><td>[ZAHLUNGSZIELTAGE][MSGZAHLUNGSZIELTAGE]</td></tr>
<tr><td nowrap>{|Zahlungsziel Skonto (in Tagen)|}:</td><td>[ZAHLUNGSZIELTAGESKONTO][MSGZAHLUNGSZIELTAGESKONTO]</td></tr>
<tr><td>{|Skonto|}:</td><td>[ZAHLUNGSZIELSKONTO][MSGZAHLUNGSZIELSKONTO]</td></tr>
</table>


</fieldset>
</td></tr>



</table>




        </td>
      </tr>

     <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Speichern" /> <input type="button" value="Abbrechen" /></td>
    </tr>


    </tbody>
  </table>

</div>

 <!-- tab view schließen -->
</div></div>
<!-- ende tab view schließen -->
  
  </form>
</body></html>

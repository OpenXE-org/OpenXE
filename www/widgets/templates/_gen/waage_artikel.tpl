<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs1">Produkte f&uuml;r Waage</a></li>
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
          <tr><td width="150">{|Beschriftung|}:</td><td>[BESCHRIFTUNG][MSGBESCHRIFTUNG]&nbsp;</td></tr>
	  <tr><td width="150">{|Artikel|}:</td><td>[ARTIKEL][MSGARTIKEL]</td></tr>
	  <tr><td width="150">{|Button Nummer|}:</td><td>[REIHENFOLGE][MSGREIHENFOLGE]&nbsp;<i>aktuell 1 bis 4</i></td></tr>
	  <tr><td width="150">MHD Datum + in Tage:</td><td>[MHDDATUM][MSGMHDDATUM]</td></tr>
	  <tr><td width="150">{|Etikettendrucker|}:</td><td>[ETIKETTENDRUCKER][MSGETIKETTENDRUCKER]</td></tr>
<!--	  <tr><td width="150">{|Etikett|}:</td><td>[ETIKETT][MSGETIKETT]</td></tr>-->
	  <tr><td width="150">{|Etikett XML|}:</td><td>[ETIKETTXML][MSGETIKETTXML]</td></tr>
<!--	  <tr><td width="150">{|Waage|}:</td><td>[WAAGE][MSGWAAGE]</td></tr>-->
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



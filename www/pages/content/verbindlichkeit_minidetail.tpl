
[FORMHANDLEREVENT]
[MESSAGE]

<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>{|Rechnungsdaten|}</legend>

    <table width="100%" border="0">
	  <tr valign="top"><td width="150">Lieferant:</td><td>[ADRESSEAUTOSTART][ADRESSE][MSGADRESSE][ADRESSEAUTOEND]</td>
          <td>&nbsp;</td>
            <td colspan="2" rowspan="2" align="center"><b style="color:green">[MELDUNG]</b>
<br><font size="7">[VERBINDLICHKEIT]</font>
</td></tr>

          <tr><td><br><br>Rechnungs Nr.:</td><td><br><br>[RECHNUNG][MSGRECHNUNG]</td>
          <td>&nbsp;</td>
            </tr>
 
						<tr><td>Bestellung:</td><td width="250">[DISABLESTART]<a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID]" target="_blank">[BESTELLUNG]</a>[MSGBESTELLUNG][MULTIBESTELLUNG][DISABLEENDE]</td>
          <td>&nbsp;</td>
            <td width="200">Zahlweise:</td><td>[ZAHLUNGSWEISE][MSGZAHLUNGSWEISE]</td></tr>

          <tr><td>Rechnungsdatum:</td><td width="250">[RECHNUNGSDATUM][MSGRECHNUNGSDATUM]</td>
          <td>&nbsp;</td>
            <td width="200">Zahlbar bis:</td><td>[ZAHLBARBIS][MSGZAHLBARBIS][DATUM_ZAHLBARBIS]</td></tr>

	  			<tr><td>Betrag/Total (Brutto):</td><td>[BETRAG][MSGBETRAG]&nbsp;[WAEHRUNG][MSGWAEHRUNG]</td><td>&nbsp;</td>
						<td>Skonto in %:</td><td>[SKONTO][MSGSKONTO]</td>
				</tr>

	  			<tr><td>USt. 19%:</td><td>[SUMMENORMAL][MSGSUMMENORMAL]</td><td>&nbsp;</td>
            <td>Skonto bis:</td><td>[SKONTOBIS][MSGSKONTOBIS][DATUM_SKONTOBIS]</td>
					</tr>

          <tr>
           <td>USt. 7%:</td><td>[SUMMEERMAESSIGT][MSGSUMMEERMAESSIGT]</td>
          <td>&nbsp;</td>
           <td>Umsatzsteuer</td><td>[UMSATZSTEUER][MSGUMSATZSTEUER]</td>

					</tr>
          <tr>
           <td>[STEUERSATZNAME3]</td><td>[SUMMESATZ3][MSGSUMMESATZ3]</td>
          <td>&nbsp;</td>
           <td>[STEUERSATZNAME4]</td><td>[SUMMESATZ4][MSGSUMMESATZ4]</td>
					</tr>
          <tr>
           <td>Verwendungszweck:</td><td>[VERWENDUNGSZWECK][MSGVERWENDUNGSZWECK]</td>
          <td>&nbsp;</td>
           <td>Frachtkosten:</td><td>[FRACHTKOSTEN][MSGFRACHTKOSTEN]</td>
					</tr>

          <tr>
           <td>Projekt:</td><td>[PROJEKT][MSGKOSTENSTELLE]</td>
          <td>&nbsp;</td>
						<td></td><td></td>
					</tr>

 
          <tr>
           <td>Kostenstelle:</td><td>[KOSTENSTELLE][MSGKOSTENSTELLE]</td>
          <td>&nbsp;</td>
	<td>Freigabe:</td><td>[MSGFREIGABE]&nbsp;<i>Wareneingangspr&uuml;fung:</i>&nbsp;[FREIGABE]&nbsp;[MSGRECHNUNGSFREIGABE]&nbsp;<i>Rechnungseingangspr&uuml;fung:</i>&nbsp;[RECHNUNGSFREIGABE]</td>
					</tr>

          <tr>
           <td>Sachkonto:</td><td>[SACHKONTO][MSGSACHKONTO]</td>
          <td>&nbsp;</td>
	  <td>Aktion:</td><td>[BUTTONBEZAHLT]</td>
	  </tr>
          <tr>
           <td>Interne Bemerkung:</td><td colspan="4">[INTERNEBEMERKUNG]</td>
	  </tr>




</table>



</fieldset>
</td></tr>

    </tbody>
  </table>


<table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
<tr valign="top"><td width="50%">
<table cellspacing="5" width="100%">
<!--<tr><td><b>Bestellung</b></td><td><b>Bestell-Nr.</b></td><td><b>Teilbetrag</b></td><td><b>Projekt</b></td><td><b>Kostenstelle</b></td><td><b>Bemerkung</b></td></tr>
<tr><td>Nr. 1</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID1]" target="_blank">[BESTELLUNG1]</a>[MSGBESTELLUNG1]</td><td>[BESTELLUNG1BETRAG][MSGBESTELLUNG1BETRAG]</td>
<td>[BESTELLUNG1PROJEKT]</td><td>[BESTELLUNG1KOSTENSTELLE]</td>
<td>[BESTELLUNG1BEMERKUNG][MSGBESTELLUNG1BEMERKUNG]</td></tr>
<tr><td>Nr. 2</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID2]" target="_blank">[BESTELLUNG2]</a>[MSGBESTELLUNG2]</td><td>[BESTELLUNG2BETRAG][MSGBESTELLUNG2BETRAG]</td>
<td>[BESTELLUNG2PROJEKT]</td><td>[BESTELLUNG2KOSTENSTELLE]</td>
<td>[BESTELLUNG2BEMERKUNG][MSGBESTELLUNG2BEMERKUNG]</td></tr>
<tr><td>Nr. 3</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID3]" target="_blank">[BESTELLUNG3]</a>[MSGBESTELLUNG3]</td><td>[BESTELLUNG3BETRAG][MSGBESTELLUNG3BETRAG]</td>
<td>[BESTELLUNG3PROJEKT]</td><td>[BESTELLUNG3KOSTENSTELLE]</td>
<td>[BESTELLUNG3BEMERKUNG][MSGBESTELLUNG3BEMERKUNG]</td></tr>
<tr><td>Nr. 4</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID4]" target="_blank">[BESTELLUNG4]</a>[MSGBESTELLUNG4]</td><td>[BESTELLUNG4BETRAG][MSGBESTELLUNG4BETRAG]</td>
<td>[BESTELLUNG4PROJEKT]</td><td>[BESTELLUNG4KOSTENSTELLE]</td>
<td>[BESTELLUNG4BEMERKUNG][MSGBESTELLUNG4BEMERKUNG]</td></tr>
<tr><td>Nr. 5</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID5]" target="_blank">[BESTELLUNG5]</a>[MSGBESTELLUNG5]</td><td>[BESTELLUNG5BETRAG][MSGBESTELLUNG5BETRAG]</td>
<td>[BESTELLUNG5PROJEKT]</td><td>[BESTELLUNG5KOSTENSTELLE]</td>
<td>[BESTELLUNG5BEMERKUNG][MSGBESTELLUNG5BEMERKUNG]</td></tr>
<tr><td>Nr. 6</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID6]" target="_blank">[BESTELLUNG6]</a>[MSGBESTELLUNG6]</td><td>[BESTELLUNG6BETRAG][MSGBESTELLUNG6BETRAG]</td>
<td>[BESTELLUNG6PROJEKT]</td><td>[BESTELLUNG6KOSTENSTELLE]</td>
<td>[BESTELLUNG6BEMERKUNG][MSGBESTELLUNG6BEMERKUNG]</td></tr>
<tr><td>Nr. 7</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID7]" target="_blank">[BESTELLUNG7]</a>[MSGBESTELLUNG7]</td><td>[BESTELLUNG7BETRAG][MSGBESTELLUNG7BETRAG]</td>
<td>[BESTELLUNG7PROJEKT]</td><td>[BESTELLUNG7KOSTENSTELLE]</td>
<td>[BESTELLUNG7BEMERKUNG][MSGBESTELLUNG7BEMERKUNG]</td></tr>
<tr><td>Nr. 8</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID8]" target="_blank">[BESTELLUNG8]</a>[MSGBESTELLUNG8]</td><td>[BESTELLUNG8BETRAG][MSGBESTELLUNG8BETRAG]</td>
<td>[BESTELLUNG8PROJEKT]</td><td>[BESTELLUNG8KOSTENSTELLE]</td>
<td>[BESTELLUNG8BEMERKUNG][MSGBESTELLUNG8BEMERKUNG]</td></tr>
<tr><td>Nr. 9</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID9]" target="_blank">[BESTELLUNG9]</a>[MSGBESTELLUNG9]</td><td>[BESTELLUNG9BETRAG][MSGBESTELLUNG9BETRAG]</td>
<td>[BESTELLUNG9PROJEKT]</td><td>[BESTELLUNG9KOSTENSTELLE]</td>
<td>[BESTELLUNG9BEMERKUNG][MSGBESTELLUNG9BEMERKUNG]</td></tr>
<tr><td>Nr. 10</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID10]" target="_blank">[BESTELLUNG10]</a>[MSGBESTELLUNG10]</td><td>[BESTELLUNG10BETRAG][MSGBESTELLUNG10BETRAG]</td>
<td>[BESTELLUNG10PROJEKT]</td><td>[BESTELLUNG10KOSTENSTELLE]</td>
<td>[BESTELLUNG10BEMERKUNG][MSGBESTELLUNG10BEMERKUNG]</td></tr>
<tr><td>Nr. 11</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID11]" target="_blank">[BESTELLUNG11]</a>[MSGBESTELLUNG11]</td><td>[BESTELLUNG11BETRAG][MSGBESTELLUNG11BETRAG]</td>
<td>[BESTELLUNG11PROJEKT]</td><td>[BESTELLUNG11KOSTENSTELLE]</td>
<td>[BESTELLUNG11BEMERKUNG][MSGBESTELLUNG11BEMERKUNG]</td></tr>
<tr><td>Nr. 12</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID12]" target="_blank">[BESTELLUNG12]</a>[MSGBESTELLUNG12]</td><td>[BESTELLUNG12BETRAG][MSGBESTELLUNG12BETRAG]</td>
<td>[BESTELLUNG12PROJEKT]</td><td>[BESTELLUNG12KOSTENSTELLE]</td>
<td>[BESTELLUNG12BEMERKUNG][MSGBESTELLUNG12BEMERKUNG]</td></tr>
<tr><td>Nr. 13</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID13]" target="_blank">[BESTELLUNG13]</a>[MSGBESTELLUNG13]</td><td>[BESTELLUNG13BETRAG][MSGBESTELLUNG13BETRAG]</td>
<td>[BESTELLUNG13PROJEKT]</td><td>[BESTELLUNG13KOSTENSTELLE]</td>
<td>[BESTELLUNG13BEMERKUNG][MSGBESTELLUNG13BEMERKUNG]</td></tr>
<tr><td>Nr. 14</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID14]" target="_blank">[BESTELLUNG14]</a>[MSGBESTELLUNG14]</td><td>[BESTELLUNG14BETRAG][MSGBESTELLUNG14BETRAG]</td>
<td>[BESTELLUNG14PROJEKT]</td><td>[BESTELLUNG14KOSTENSTELLE]</td>
<td>[BESTELLUNG14BEMERKUNG][MSGBESTELLUNG14BEMERKUNG]</td></tr>
<tr><td>Nr. 15</td><td><a href="index.php?module=bestellung&action=edit&id=[BESTELLUNGID15]" target="_blank">[BESTELLUNG15]</a>[MSGBESTELLUNG15]</td><td>[BESTELLUNG15BETRAG][MSGBESTELLUNG15BETRAG]</td>
<td>[BESTELLUNG15PROJEKT]</td><td>[BESTELLUNG15KOSTENSTELLE]</td>
<td>[BESTELLUNG15BEMERKUNG][MSGBESTELLUNG15BEMERKUNG]</td></tr>-->

[TABELLEBESTELLUNGEN]

</table>
</td>


<td>

<table width="100%>">                              
<tr><td>Summe Verbindlichkeit</td><td>Summe Kontierung</td></tr>
<tr>                                                           
  <td class="greybox" width="25%">[SUMMEVERBINDLICHKEIT]</td> 
  <td class="greybox" width="25%">[SUMMEKONTIERUNG]</td>
</tr>                                                                                                                                                                                              
</table>  

[MESSAGEVORKONTIERUNG]
[VORKONTIERUNG]
[ZAHLUNGEN]

<div style="background-color:white">
<h2 class="greyh2">{|Protokoll|}</h2>
<div style="padding:10px">
  [PROTOKOLL]
</div>
</div>

</td>


</tr>

  
    </tbody>
  </table>


<script>

function recalcvpe()
{
	var span = document.getElementById("livepreisvpe");
	var preis = document.getElementById("preis").value;
	var vpe = document.getElementById("vpe").value;

	preis = preis.replace(',', '.');
	vpe= vpe.replace(',', '.');

	span.textContent = parseFloat(preis*vpe).toFixed(2);
}

window.setInterval(recalcvpe, 300);

</script>


 
<form action="" method="post" name="eprooform" >
[FORMHANDLEREVENT]
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>

      <tr valign="top" colspan="3">
        <td colspan="3">

[MESSAGE]
<fieldset><legend>&nbsp;Lieferant&nbsp;</legend>

<table cellspacing="5" border="0" width="700">
<tr><td width="170">{|Standardlieferant|}:</td><td colspan="4">[STANDARD][MSGSTANDARD]</td></tr>
<tr><td width="170"><b>Lieferant:</b></td><td colspan="3">[ADRESSESTART][ADRESSE][MSGADRESSE][ADRESSEENDE]</td><td>[BUTTONLADEN]</td></tr>
<tr><td><b>Bezeichnung bei Lieferant:</b></td><td colspan="4">[BEZEICHNUNGLIEFERANT][MSGBEZEICHNUNGLIEFERANT]</td></tr>
<tr><td>{|Artikelnummer bei Lieferant|}:</td><td colspan="4">[BESTELLNUMMER][MSGBESTELLNUMMER]</td></tr>
</table></fieldset>

<fieldset><legend>&nbsp;Einkaufspreis&nbsp;</legend>
<table cellspacing="5" border="0" width="900">
<tr><td width="170"><b>Ab Menge:</b></td><td width="180">[AB_MENGE][MSGAB_MENGE]&nbsp;</td><td width="10">&nbsp;</td><td width="150" nowrap>{|Verpackungseinheit (Menge in VPE)|}:</td><td>

[VPE][MSGVPE]&nbsp;[VPEPREIS]
</td></tr>

<tr><td width="170"><b>Preis pro St&uuml;ck:</b><br><i>(Immer Einzelst&uuml;ckpreis!)</i></td>
<td width="180">[PREIS][MSGPREIS]&nbsp;[WAEHRUNG][MSGWAEHRUNG]</td><td width="10">&nbsp;</td><td width="160" norwap>{|Preis f&uuml;r VPE|}:</td><td>
<span id="livepreisvpe"></span>
												</td></tr>

<tr valign="top"><td width="170"></td>
<td width="180" rowspan="2">[PREISRECHNER]</td><td width="10">&nbsp;</td><td width="150" valign="top">Preis nicht berechnet aus W&auml;hrungstabelle</td><td>[NICHTBERECHNET][MSGNICHTBERECHNET]
												</td></tr>
        <td width="10">&nbsp;</td><td colspan="2" align="right">[PREISTABELLE]
</td></tr>

[DISABLEOPENSTOCK]
<tr><td width="170">{|Preisanfrage vom|}:</td><td width="180">[PREIS_ANFRAGE_VOM][MSGPREIS_ANFRAGE_VOM]&nbsp;</td><td width="10">&nbsp;</td><td width="150">{|G&uuml;ltig bis|}:</td><td>[GUELTIG_BIS][MSGGUELTIG_BIS]</td></tr>
[DISABLECLOSESTOCK]
</table></fieldset>
[DISABLEOPENSTOCK]
<fieldset><legend>&nbsp;Weitere Informationen&nbsp;</legend>
<table cellspacing="5" border="0" width="700">

<tr><td width="170">{|Lagerbestand Lieferant|}:</td><td width="180">[LAGER_LIEFERANT][MSGLAGER_LIEFERANT] am [DATUM_LAGERLIEFERANT][MSGDATUM_LAGERLIEFERANT]</td><td width="10">&nbsp;</td><td width="150">{|Sicherheitslager|}:</td><td>[SICHERHEITSLAGER][MSGSICHERHEITSLAGER]</td></tr>

<tr><td width="170">{|Lieferzeit Standard (Wochen)|}:</td><td width="180">[LIEFERZEIT_STANDARD][MSGLIEFERZEIT_STANDARD]&nbsp;</td><td width="10">&nbsp;</td><td width="150">{|Lieferzeit Aktuell (Wochen)|}:</td><td>[LIEFERZEIT_AKTUELL][MSGLIEFERZEIT_AKTUELL]</td></tr>

</table></fieldset>
<fieldset><legend>&nbsp;Rahmenvertrag&nbsp;</legend>
<table cellspacing="5" border="0" width="700">

<tr><td width="170">{|Rahmenvertrag|}:</td><td width="180">[RAHMENVERTRAG][MSGRAHMENVERTRAG]</td><td width="10">&nbsp;</td><td width="150">{|Menge|}:</td><td>[RAHMENVERTRAG_MENGE][MSGRAHMENVERTRAG_MENGE]</td></tr>
<tr><td width="170">{|Von|}:</td><td width="180">[RAHMENVERTRAG_VON][MSGRAHMENVERTRAG_VON]&nbsp;</td><td width="10">&nbsp;</td><td width="150">{|Bis|}:</td><td>[RAHMENVERTRAG_BIS][MSGRAHMENVERTRAG_BIS]</td></tr>
</table></fieldset>

[DISABLECLOSESTOCK]
<fieldset><legend>&nbsp;Interne Bemerkung&nbsp;</legend>
<table cellspacing="5" border="0" width="700">

<tr><td width="170">{|Interner Kommentar|}:</td><td colspan="4">[BEMERKUNG][MSGBEMERKUNG]</td></tr>

</table>

</fieldset>
       </td>
      </tr>
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit"
    value="Speichern" name="submit" /> [ABBRECHEN]</td>
    </tr>

    </tbody>
</table>
</form>
[PREISTABELLEPOPUP]

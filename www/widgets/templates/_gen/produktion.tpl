<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-2">Produktion</a></li>
        <li><a href="#tabs-4" onclick="callCursor();">Positionen</a></li>
       <li><a href="index.php?module=produktion&action=inlinepdf&id=[ID]&frame=true#tabs-3">Vorschau</a></li>
                        [FURTHERTABS]
    </ul>



<div id="tabs-2">
[MESSAGE]
<form action="" method="post" name="eprooform" id="eprooform">
[FORMHANDLEREVENT]


<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-12 col-sm-height">
      <div class="inside inside-full-height">

<!-- // ende anfang -->
<table align="center" width="100%">
<tr>
<td>&nbsp;<b style="font-size: 14pt">Produktion <font color="blue">[NUMMER]</font></b>[KUNDE]</td>
<td>[STATUSICONS]</td>
<td align="right" nowrap>[ICONMENU]&nbsp;[SAVEBUTTON]</td>
</tr>
</table>

</div>
</div>
</div>
</div>


<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-6 col-sm-height">
      <div class="inside inside-full-height">

<fieldset><legend>{|Allgemein|}</legend>
<table width="100%" height="160">
  <tr><td width="200">{|Kunde|}:</td><td width="">[ADRESSE][MSGADRESSE]
[BUTTON_UEBERNEHMEN]
</td></tr>
  <tr><td>{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]</td></tr>
  <tr><td>{|Status|}:</td><td>[STATUS]</td></tr>
  <tr><td>{|Auftrag|}:</td><td>[AUFTRAGID][MSGAUFTRAGID]</td></tr>
  <tr><td>{|Interne Bezeichnung|}:</td><td>[INTERNEBEZEICHNUNG][MSGINTERNEBEZEICHNUNG]</td></tr>
  <tr><td>{|Angelegt am|}:</td><td>[DATUM][MSGDATUM]</td></tr>
  <tr><td>[VORWUNSCHLAGER]Bevorzugtes Lager:[NACHWUNSCHLAGER]</td><td>[VORWUNSCHLAGER][STANDARDLAGER][MSGSTANDARDLAGER][NACHWUNSCHLAGER]</td></tr>
  <tr><td>{|Schreibschutz|}:</td><td>[SCHREIBSCHUTZ][MSGSCHREIBSCHUTZ]&nbsp;</td></tr>
</table>
</fieldset>


</div>
</div>
<div class="col-xs-12 col-sm-6 col-sm-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Aktionen|}</legend>
[EXTRA]
</fieldset>
</div>
</div>
</div>
</div>


<!--
<table width="100%"><tr><td>
<fieldset><legend>{|Positionen|}</legend>
[POSITIONEN]
</fieldset>
</td></tr></table>
-->

<!--
<table width="100%"><tr><td>
<fieldset><legend>{|Bezeichnung|}</legend>
[BEZEICHNUNG][MSGBEZEICHNUNG]
</fieldset>
</td></tr></table>
-->

<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-12 col-sm-height">
      <div class="inside inside-full-height">


<fieldset><legend>{|Einstellung Produktion|}</legend>
<table>
<tr><td width="200">{|Reservierung Material|}:</td><td>
[RESERVIERART][MSGRESERVIERART]
</td></tr>
<tr><td>{|Entnahme im Lager|}:</td><td>[AUSLAGERART][MSGAUSLAGERART]</td></tr>
<tr><td>{|Unterst&uuml;cklisten aufl&ouml;sen|}:</td><td>[UNTERLISTENEXPLODIEREN][MSGUNTERLISTENEXPLODIEREN]&nbsp;<i>alle St&uuml;cklisten vollst&auml;ndig aufl&ouml;sen</i></td></tr>
<tr><td>{|Funktionstest|}:</td><td>[FUNKTIONSTEST][MSGFUNKTIONSTEST]&nbsp;<i>f&uuml;r jeden produzierten Artikel</i></td></tr>
<tr><td>{|Beschreibungen von Arbeitsschritten anzeigen|}:</td><td>[ARBEITSSCHRITTETEXTANZEIGEN][MSGARBEITSSCHRITTETEXTANZEIGEN]&nbsp;<i>im PDF</i></td></tr>
<tr><td>{|Seriennummer anlegen|}:</td><td>[SERIENNUMMER_ERSTELLEN][MSGSERIENNUMMER_ERSTELLEN]&nbsp;<i>f&uuml;r jeden produzierten Artikel</i></td></tr>
<tr><td>{|Unterseriennummer erfassen|}:</td><td>[UNTERSERIENNUMMERN_ERFASSEN][MSGUNTERSERIENNUMMERN_ERFASSEN]&nbsp;<i>f&uuml;r verbaute Artikel aus St&uuml;ckliste mit Seriennummern</i></td></tr>
<tr><td>{|Auslieferung Lager|}:</td><td>[DATUMAUSLIEFERUNG][MSGDATUMAUSLIEFERUNG]</td></tr>
<tr><td>{|Bereitstellung Start|}:</td><td>[DATUMBEREITSTELLUNG][MSGDATUMBEREITSTELLUNG]</td></tr>
<tr><td>{|Produktion Start|}:</td><td>[DATUMPRODUKTION][MSGDATUMPRODUKTION]</td></tr>
<tr><td>{|Produktion Ende|}:</td><td>[DATUMPRODUKTIONENDE][MSGDATUMPRODUKTIONENDE]</td></tr>
[VORCHARGE]<tr><td>{|Charge|}:</td><td>[CHARGE][MSGCHARGE]</td></tr>[NACHCHARGE]
[VORMHD]<tr><td>{|Mindesthaltbarkeitsdatum|}:</td><td>[MHD][MSGMHD]</td></tr>[NACHMHD]
</table>
</fieldset>
</td></tr></table>

</div>
</div>
</div>
</div>

<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-12 col-sm-height">
      <div class="inside inside-full-height">


<table width="100%"><tr><td>
<fieldset><legend>{|Freitext|}</legend>
[FREITEXT][MSGFREITEXT]
</fieldset>
</td></tr></table>
</div>
</div>
</div>
</div>


<div class="row">
  <div class="row-height">
    <div class="col-xs-12 col-sm-12 col-sm-height">
      <div class="inside inside-full-height">



<table width="100%"><tr><td>
<fieldset><legend>{|Interne Bemerkung|}</legend>
[INTERNEBEMERKUNG][MSGINTERNEBEMERKUNG]
</fieldset>
</td></tr></table>
</div>
</div>
</div>
</div>


<table width="100%">
<tr><td align="right">
    <input type="submit" name="speichern"
    value="Speichern" />
</td></tr></table>
</div>

</form>

<div id="tabs-4">
<div class="overflow-scroll">

<!-- // rate anfang -->
<table width="100%" style="" align="center">
<tr>
<td width="33%">[STATUSICONS]</td>
<td align="center"><b style="font-size: 14pt">Produktion <font color="blue">[NUMMER]</font></b>[KUNDE]</td>
<td width="33%" align="right">[ICONMENU2]</td>
</tr>
</table>


[POS]


</div>
</div>

<div id="tabs-3">
</div>
 <!-- tab view schlieÃn -->
</div>

                     [FURTHERTABSDIV]

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
          <tr><td width="150">{|Typ|}:</td><td>[SUBJEKT][MSGSUBJEKT]</td><td rowspan="3"><b>Typ</b><ul>
<li>Rechnung <i>Variablen: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
<li>Angebot <i>Variablen: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
<li>Auftrag <i>Variablen: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
<li>Gutschrift <i>Variablen: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
<li>Lieferschein <i>Variablen: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
<li>Bestellung <i>Variablen: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
<li>Arbeitsnachweis <i>Variablen: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {DATUM}</i></li>
<li>Korrespondenz <i>Variablen: {BETREFF}, {NAME}, {ANSCHREIBEN}</i></li>
<li>ZahlungOK <i>Variablen: {AUFTRAG}, {DATUM}, {GESAMT}</i></li>
<li>ZahlungDiff <i>Variablen: {AUFTRAG}, {DATUM}, {GESAMT}, {REST}</i> </li>
<li>Stornierung <i>Variablen: {AUFTRAG}, {DATUM}</i></li>
<li>ZahlungMiss <i>Variablen: {AUFTRAG}, {DATUM}, {GESAMT}, {REST}</i></li>
<li>Versand <i>Variablen: {VERSAND}, {NAME}, {ANSCHREIBEN}, {BELEGNR}, {IHREBESTELLNUMMER}, {INTERNET}, {AUFTRAGDATUM}</i></li>
<li>VersandMailDokumente <i>Variablen: {NAME}, {ANSCHREIBEN}, {BELEGNR}, {IHREBESTELLNUMMER}, {INTERNET}, {AUFTRAGDATUM}</i></li>
<li>Selbstabholer</li>
</ul></td></tr>
          <tr><td>{|Sprache|}:</td><td>[SPRACHE][MSGSPRACHE]</td></tr>
          <tr><td>{|Projekt|}:</td><td>[PROJEKT][MSGPROJEKT]</td></tr>
</table></fieldset>
<fieldset><legend>{|Einstellung|}</legend>
    <table width="100%">
          <tr><td width="150">{|Betreff|}:</td><td>[BETREFF][MSGBETREFF]</td><td></tr>
          <tr><td>{|Text|}:</td><td>[TEXT][MSGTEXT]</td><td></tr>
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
</div>



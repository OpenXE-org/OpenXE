<!-- gehort zu tabview -->
<div id="tabs">
    <ul class="yui-nav">
        <li><a href="#tabs-1">Partner</a></li>
        <li><a href="#tabs-2">Umsatz</a></li>
    </ul>
<!-- ende gehort zu tabview -->
<!-- erstes tab -->
<div id="tabs-1">
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >

<fieldset><legend>{|Partner|}</legend>
    <table width="100%">
	      <tr><td width="150">{|Adresse|}:</td><td>[ADRESSEAUTOSTART][ADRESSE][MSGADRESSE][ADRESSEAUTOEND]</td></tr>
</table></fieldset>

<fieldset><legend>{|Einstellungen|}</legend>
    <table width="100%">
	<tr><td width="150">{|Bezeichnung|}:</td><td>[BEZEICHNUNG][MSGBEZEICHNUNG]</td></tr>
	      <tr><td>{|Ref ID|}:</td><td>[REF][MSGREF]</td></tr>
	      <tr><td>{|Prozentsatz netto|}:</td><td>[NETTO][MSGNETTO]</td></tr>
	      <tr><td>{|Tage nach versendet|}:</td><td>[TAGE][MSGTAGE]</td></tr>
        <tr><td>{|Onlineshop|}:</td><td>[SHOP][MSGSHOP]</td></tr>
	      <tr><td>{|Standardprojekte|}:</td><td>[PROJEKTAUTOSTART][PROJEKT][MSGPROJEKT][PROJEKTAUTOEND]</td></tr>
</table></fieldset>


    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" />
    </tr>

    </tbody>
  </table>
</form>
</div>


<div id="tabs-2">
[MONAT]
[TAGE30]
[GESAMT]
[GESAMTOFFEN]
[TAGE30LETZTEN]
</div>
<!-- tab view schließen -->
</div>




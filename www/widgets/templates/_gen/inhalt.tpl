<!-- gehort zu tabview -->
<div id="tabs" >
    <ul>
        <li><a href="#tab-1">Online-Shop Seiten</a></li>
        <li><a href="#tab-2">Meta-Tags</a></li>
    </ul>
<!-- ende gehort zu tabview -->
<form action="" method="post" name="eprooform">
[FORMHANDLEREVENT]

<!-- erstes tab -->
<div id="tab-1">
<table width="100%" style="background-color: #fff; border: solid 1px #000;" align="center">
<tr>
<td align="center">
<br><b style="font-size: 14pt">Online-Shop Seiten:</b>
<br>
<br>
Text- und Internseiten der Online-Shops.<br>
<br>
</td>
</tr>
</table>
[MESSAGE]
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >
<fieldset><legend>{|Allgemein|}</legend>
    <table width="100%">
          <tr><td width="150">{|Shop|}:</td><td>[SHOP][MSGSHOP]</td></tr>
          <tr><td>{|Inhaltstyp|}:</td><td>[INHALTSTYP][MSGINHALTSTYP]</td></tr>
          <tr><td>{|Sprache|}:</td><td>[SPRACHE][MSGSPRACHE]</td></tr>
          <tr><td width="150">{|Interner Bezeichnung|}:</td><td>[INHALT][MSGINHALT]<i>(z.B. artikel, agb,impressum, E-Mail: bestellung, vorkasse)</i></td></tr>
          <tr><td width="150">{|aktiv|}:</td><td>[AKTIV][MSGAKTIV]</td></tr>
	</table>
</fieldset>
<fieldset><legend>{|Einstellung|}</legend>
    <table width="100%">
          <tr><td width="150">{|Template|}:</td><td>[TEMPLATE][MSGTEMPLATE]<i>(Optional um Inhalt, bei Artikelgruppe Item)</i></td></tr>
          <tr><td width="150">{|Final-Parse|}:</td><td>[FINALPARSE][MSGFINALPARSE]<i>(Seitenrahmen)</i></td></tr>
          <tr><td width="150">{|Navigation|}:</td><td>[NAVIGATION][MSGNAVIGATION]<i>(Optional bei HTML Seite, Nach Shop-Wechsel Reload bei falscher Anzeige)</i></td></tr>


          <tr><td width="150">{|Datum|}:</td><td>[DATUM][MSGDATUM]</td></tr>
          <tr><td width="150">{|Sichtbar bis|}:</td><td>[SICHTBARBIS][MSGSICHTBARBIS]</td></tr>
</table></fieldset>
<fieldset><legend>{|Inhalt|}</legend>
    <table width="100%">
          <tr><td>{|Titel|}:</td><td>[TITLE][MSGTITLE]<br><i>(Bei HTML Fenstertitel, bei News und Teaser &Uuml;berschrift, bei E-Mail Betreff)</i></td></tr>
          <tr><td>Kurztext (Bei Gruppe SQL, Bei Newsmeldung Eintrag fuer Liste):</td><td>[KURZTEXT][MSGKURZTEXT]</td></tr>
          <tr><td>{|Langtext (HTML Darstellend)|}:</td><td>[HTML][MSGHTML]</td></tr>
</table></fieldset>


</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" />
    </tr>
  
    </tbody>
  </table>

</div>

<div id="tab-2">
<table width="100%" style="background-color: #fff; border: solid 1px #000;" align="center">
<tr>
<td align="center">
<br><b style="font-size: 14pt">Online-Shop Seiten:</b>
<br>
<br>
Text- und Internseiten der Online-Shops.<br>
<br>
</td>
</tr>
</table>

  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td >

<fieldset><legend>Suchw&ouml;rter</legend>
    <table width="100%">
          <tr><td>{|Beschreibung|}:</td><td>[DESCRIPTION][MSGDESCRIPTION]</td></tr>
          <tr><td>{|Keywords|}:</td><td>[KEYWORDS][MSGKEYWORDS]</td></tr>
</table></fieldset>



</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" value="Speichern" />
    </tr>
  
    </tbody>
  </table>


</div>
</form>
<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->

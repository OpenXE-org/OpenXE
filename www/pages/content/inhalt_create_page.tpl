[INHALTJAVASCRIPT]
<style>
input[type=text] {
	width: 200px;
}
</style>

<!-- gehort zu tabview -->
<div id="tabs" >
    <ul>
        <li><a href="#tab-1">Online-Shop Seiten</a></li>
        <li><a href="#tab-2">Meta-Tags</a></li>
    </ul>
<!-- ende gehort zu tabview -->
<form action="" method="post" name="inhaltform" id="inhaltform">
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
          <tr><td>Inhaltstyp:</td><td><select name="inhaltstyp" id="inhaltstyp">[INHALTSTYPSELECT]</select></td></tr>
          <tr><td width="150">Shop:</td><td>[SHOPSTART]<input type="text" name="shop" id="shop" value="[SHOP]">[SHOPENDE]</td></tr>
          <tr><td width="150">Interne Bezeichnung:</td><td><input type="text" name="inhalt" value="[INHALT]">&nbsp;<i>(z.B. artikel, agb,impressum, E-Mail: bestellung, vorkasse)</i></td></tr>
          <tr><td>Sprache:</td><td><select name="sprache">[SPRACHESELECT]</select></td></tr>
          <tr><td width="150">aktiv:</td><td><input type="checkbox" name="aktiv" value="1" [AKTIVCHECKED]></td></tr>
	</table>
</fieldset>
<fieldset><legend>{|Einstellung|}</legend>
    <table width="100%">
          <tr><td width="150">Template:</td><td><input type="text" name="template" value="[TEMPLATE]">&nbsp;<i>(Optional um Inhalt, bei Artikelgruppe Item)</i></td></tr>
          <tr><td width="150">Final-Parse:</td><td><input type="text" name="finalparse" value="[FINALPARSE]">&nbsp;<i>(Seitenrahmen)</i></td></tr>
          <tr><td width="150">Navigation:</td><td><select name="navigation">[NAVIGATIONSELECT]</select>&nbsp;<i>(Optional bei HTML Seite, Nach Shop-Wechsel Reload bei falscher Anzeige)</i></td></tr>
          <tr><td width="150">Datum:</td><td><input type="text" name="datum" id="datum" value="[DATUM]"></td></tr>
          <tr><td width="150">Sichtbar bis:</td><td><input type="text" name="sichtbarbis" id="sichtbarbis" value="[SICHTBARBIS]"></td></tr>
</table></fieldset>
<fieldset><legend>{|Inhalt|}</legend>
    <table width="100%">
          <tr><td width="150" valign="top">Titel:</td><td><input type="text" name="title" value="[TITLE]" style="width: 600px"><br></td></tr>
          <tr><td valign="top">Kurztext:</td><td><textarea rows="5" name="kurztext" style="width: 600px">[KURZTEXT]</textarea></td></tr>
          <tr><td valign="top">Langtext:</td><td><textarea rows="20" id="html" name="html" style="width: 612px">[LANGTEXT]</textarea></td></tr>
</table></fieldset>


</td></tr>

<form action="" method="post" name="eprooform">
    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
		<input type="hidden" name="saveform" id="saveform" value="1">
    <input type="submit" name="inhalt_submit" id="inhalt_submit" value="Speichern"/>
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
          <tr><td>Beschreibung:</td><td><textarea rows="5" id="description" class="" name="description" cols="70"/>[DESCRIPTION]</textarea></td></tr>
          <tr><td>Keywords:</td><td><textarea rows="5" id="keywords" class="" name="keywords" cols="70"/>[KEYWORDS]</textarea></td></tr>
</table></fieldset>



</td></tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="submit" name="inhalt_submit" value="Speichern" />
    </tr>
  
    </tbody>
  </table>
</div>
</form>
<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->

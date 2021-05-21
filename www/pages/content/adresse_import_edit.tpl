<script type="text/javascript">
$(document).ready(function(){
    var vorname = $('#vorname').val();
    var typ = $('select[name=typ]').val();

    if(typ=='herr' || typ=='frau') {
      document.getElementById('ansprechpartner_label').innerHTML="";      document.getElementById('ansprechpartner').style.visibility="hidden";
      document.getElementById('name_label').innerHTML="Vor- & Nachname:";
    }
 document.getElementById('abweichenderechnungsadressestyle').style.display="none";    if(document.getElementById('abweichende_rechnungsadresse').checked)
      document.getElementById('abweichenderechnungsadressestyle').style.display="";

  });


  function onchange_typ(el)  {
    if(el=='herr' || el=='frau') {
      document.getElementById('ansprechpartner_label').innerHTML="";
      document.getElementById('ansprechpartner').style.visibility="hidden";
      document.getElementById('name_label').innerHTML="Vor- & Nachname:";
    } else {
      document.getElementById('ansprechpartner_label').innerHTML="Ansprechpartner:";
      document.getElementById('ansprechpartner').style.visibility= "";
      document.getElementById('name_label').innerHTML="Firmenname:";
    } 
  }
</script>


<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Neuen Kunden anlegen</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post">
<fieldset><legend>{|Stammdaten|}</legend>
    <input type="hidden" name="vorname" id="vorname">
    <table width="100%">
    <tr><td width="110">Anrede:</td><td><select name="typ" tabindex="1" id="typ" onchange="onchange_typ(this.form.typ.options[this.form.typ.selectedIndex].value);">
<option value="firma" [FIRMA]>Firma</option><option value="herr" [HERR]>Herr</option><option value="frau" [FRAU]>Frau</option>
    </select></td>
          <td>&nbsp;</td>
            <td></td><td></td></tr>

          <tr><td><span id="name_label">Name:*</span></td><td><input type="text" name="name" size="30" value="[NAME]" tabindex="2"></td>
          <td>&nbsp;</td>
            <td>Telefon:</td><td><input type="text" name="telefon" size="30" value="[TELEFON]"></td></tr>

          <tr><td><span id="ansprechpartner_label">Ansprechpartner:</span></td><td><input type="text" id="ansprechpartner" name="ansprechpartner" size="30" value="[ANSPRECHPARTNER]"></td>
          <td>&nbsp;</td>
            <td>Telefax:</td><td><input type="text" name="telefax" size="30" value="[TELEFAX]"></td></tr>

          <tr><td>Abteilung:</td><td><input type="text" name="abteilung" size="30" value="[ABTEILUNG]"></td><td>&nbsp;</td>
          <td>E-Mail:</td><td><input type="text" name="email" size="30" value="[EMAIL]"></td></tr>

          <tr><td>Unterabteilung:</td><td><input type="text" name="unterabteilung" size="30" value="[UNTERABTEILUNG]"></td><td>&nbsp;</td>
           <td>Mobil:</td><td><input type="text" name="mobil" size="30" value="[MOBIL]"></td></tr>

    			<tr><td>Adresszusatz:</td><td><input type="text" name="adresszusatz" size="30" value="[ADRESSZUSATZ]"></td><td>&nbsp;</td>
          <td>Internetseite:</td><td><input type="text" name="internetseite" size="30" value="[INTERNETSEITE]"></td></tr>

          <tr><td>Stra&szlig;e:</td><td><input type="text" name="strasse" size="30" value="[STRASSE]"></td><td>&nbsp;</td>
            <td>USt-IdNr.:</td><td><input type="text" name="ustid" size="30" value="[USTID]">
      		</td></tr>
          <tr><td>PLZ/Ort:</td><td nowrap><input type="text" name="plz" size="4" value="[PLZ]">&nbsp;<input type="text" name="ort" size="23" value="[ORT]"></td><td>&nbsp;</td>
            <td></td><td></td></tr>

          <tr valign="top"><td>Land:</td><td colspan="2">[EPROO_SELECT_LAND]</td>
            <td colspan="2" align="right"></td></tr>
       </tr>

</table></fieldset>
<br>
<center><input type="submit" name="speichern" value="Kunden jetzt anlegen" />&nbsp;</center>
</form>
</div>



<!-- tab view schließen -->
</div>


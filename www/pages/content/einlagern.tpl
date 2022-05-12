<fieldset>
  <form action="" method="post" name="eprooform" id="eprooform"><input type="hidden" name="suggestedbatchbefore" value="[SUGGESTEDBATCHBEFORE]" />
    <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
      <tbody>
        <tr>
          <td>
            [MESSAGELAGER]<br>
            <table width="80%" align="center">
              <tr>
                <td align="center">
                  <table width="90%">
                    <tr><td width="300"><label for="woher">{|Von|}:</label></td><td><select id="woher" name="woher">
                    [WOHERREADONLYSTART]		<option name="differenz" [DIFFERENZ]>{|Manuelle Lageranpassung|}</option>[WOHERREADONLYENDE]
                    [WOHERREADONLYSTART2] 	<option name="zwischenlager" [ZWISCHENLAGER]>{|Zwischenlager|}</option> [WOHERREADONLYENDE2]
                    </select>
                    </td></tr>
                    <tr><td><br></td><td></td></tr>
                    <tr><td><b><label for="menge">{|Menge|}:</label></b></td><td><input type="text" id="menge" name="menge" value="[MENGE]" size="22" [MENGEREADONLY] style="width:200px"></td></tr>
                    <tr valign="top"><td><b><label for="nummer">{|Artikelnummer|}:</label></b></td><td>[NUMMERAUTOSTART]<input type="text" name="nummer" id="nummer" value="[NUMMER]" [ARTIKELSTYLE] size="22" style="width:200px">[NUMMERAUTOEND][MSGARTIKEL]</td></tr>
                    [ZWISCHENLAGERINFO]
                    <tr valign="top"><td><br></td><td align="center"></td></tr>
                    [SHOWMHDSTART]<tr valign="top"><td><b style="color:red"><label for="mhd">{|MHD|}:</label></b></td><td><input type="text" name="mhd" id="mhd" value="[MHDVALUE]" style="width:200px">&nbsp;<br><i>({|Mindesthaltbarkeitsdatum|})</i></td></tr>
                    <tr valign="top"><td><br></td><td align="center"></td></tr>[SHOWMHDEND]
                    [SHOWCHRSTART]<tr valign="top"><td><b style="color:red"><label for="charge">{|Charge|}:</label></b></td><td><input type="text" name="charge" value="[CHARGEVALUE]" id="charge" style="width:200px">&nbsp;<br><i>({|Chargennummer von Hersteller|})</i></td></tr>
                    <tr valign="top"><td><label for="chargesnmhdbemerkung">{|Bemerkung|}:</label></td><td><input type="text" name="chargesnmhdbemerkung" value="[CHARGESNMHDBEMERKUNG]" id="chargesnmhdbemerkung" style="width:200px">&nbsp;<br><i>({|Infos zur Charge|})</i></td></tr>
                    <tr valign="top"><td><br></td><td align="center"></td></tr>[SHOWCHREND]
                    [SHOWSRNSTART]<tr valign="top"><td><b style="color:red">{|Seriennummern|}:</b></td><td><input type="button" onclick="seriennummern_assistent([MENGE])" value="Assistent verwenden"><br>[SERIENNUMMERN]<i>({|Pro Artikel eine Nummer|})</i></td></tr>
                    <tr valign="top"><td><br></td><td align="center"></td></tr> [SHOWSRNEND]
                    <tr><td><br></td><td></td></tr>
                    <tr><td><label for="projekt">{|Projekt|}:</label></td><td>[PROJEKTAUTOEND]<input type="text" id="projekt" name="projekt" value="[PROJEKT]" size="22" style="width:200px">[PROJEKTAUTOEND]</td></tr>
                    <tr><td><label for="grundreferenz">{|Grund|}:</label></td><td><input type="text" id="grundreferenz" name="grundreferenz" value="[GRUNDREFERENZ]" size="22" style="width:200px"></td></tr>
                    <tr><td><br></td><td></td></tr>
                  [SRNINFO]
                  </table>
                  <br>
                </td>
              </tr>
            </table>
            <br><br>
<!--	


        <table width="100%">
          <tr><td>Kundennummer:</td><td><input type="text" name="kundeadressid" size="20" value="[KUNDENNUMMER]"></td>
            <td>&nbsp;</td><td>Lieferantenummer:</td><td><input type="text" name="lieferantadressid" size="20" value="[LIEFERANTENNUMMER]">
	    </td></tr>
          <tr><td>Name/Firma:</td><td><input type="text" name="name" size="20" value="[NAME]"></td>
          <td>&nbsp;</td>
            <td>Vorname:</td><td><input type="text" name="vorname" size="20" value="[VORNAME]"></td></tr>
          <tr><td>Abteilung:</td><td><input type="text" name="abteilung" size="20" value="[ABTEILUNG]"></td><td>&nbsp;</td>
            <td>Unterabteilung:</td><td><input type="text" name="unterabteilung" value="[UNTERABTEILUNG]" size="20"></td></tr>
          <tr><td>Strasse:</td><td><input type="text" name="strasse" size="20" value="[STRASSE]"></td>
          <td>&nbsp;</td>
            <td>Adresszusatz:</td><td><input type="text" name="adresszusatz" size="20" value="[ADRESSZUSATZ]"></td></tr>
          <tr><td>PLZ:</td><td><input type="text" name="plz" size="20"></td><td>&nbsp;</td>
            <td>Ort:</td><td><input type="text" name="ort" size="20"></td></tr>
          <tr><td>Land:</td><td colspan="3">[EPROO_SELECT_LAND]<input type="hidden" name="land"></td>
            </tr>
          <tr><td>USt-ID:</td><td><input type="text" name="ustid" size="20" value="[USTID]"></td><td>&nbsp;</td>
            <td>E-Mail:</td><td><input type="text" name="email" size="20" value="[EMAIL]"></td></tr>
          <tr><td>Telefon:</td><td><input type="text" name="telefon" size="20" value="[TELEFON]"></td><td>&nbsp;</td>
            <td>Telefax:</td><td><input type="text" name="telefax" size="20" value="[TELEFAX]"></td></tr>
        </table>
-->
        </td>
      </tr>

      <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
        <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
        [NEINDOCHNICHTSTART]<input type="button" name="zurueck" onclick="window.history.back();"
        value="{|Nein, doch nicht|}" />[NEINDOCHNICHTENDE]
        <input type="submit" name="submit"
        value="{|Weiter|}" /></td>
      </tr>
    </tbody>
  </table>
</form>
</fieldset>
<script type="text/javascript">
 var firstsubmit = false;
  $(document).ready(function() {
    $( "#eprooform" ).submit(function( event ) {
      if(firstsubmit)
      {
        event.preventDefault();
        return false;
      }
      firstsubmit = true;
      return true;
    });

  });

  [SERIENNUMMERNENTERJUMP]

</script>

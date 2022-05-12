<form action="" method="post" name="eprooform" id="frmWareneingangDistribution">
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td>
          [MESSAGE]
          <table border="0" width="100%">
            <tr valign="top"><td colspan="2">
              <fieldset><legend>{|Allgemein|}</legend>
                <table width="100%" height="100">
                  <tr><td>{|Adresse|}:</td><td width="70%">[ADRESSE]</td></tr>
                  <tr><td>{|Paket Nr.|}:</td><td width="70%">[ID]</td></tr>
                </table>
              </fieldset>
            </td><td rowspan="2">
              <fieldset><legend>{|Posteingang|}</legend>
                <table width="100%" height="180">
                  <tr><td>{|Posteingang|}:</td><td>[DATUM]</td></tr>
                  <tr><td>{|Paketannahme|}:</td><td>[BEARBEITER]</td></tr>
                  <tr><td>{|Gewicht|}:</td><td>[GEWICHT] (in Gramm)</td></tr>
               </table>
              </fieldset>
            </td>
            </tr>
            <tr valign="top"><td>
            <fieldset><legend>{|Paket|}</legend>
              <table height="280" border="0" align="center">
                <tr><td colspan=2 align="center">{|Foto vom Paket|}</td></tr>
                <tr><td colspan=2><img src="index.php?module=dateien&action=send&id=[FOTO]&ext=.jpg" width="300"></td></tr>
              </table>
            </fieldset>
          </td>
          </tr>
          </table>
        </td>
      </tr>

    <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
    <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
    <input type="button" onclick="window.location.href='index.php?module=wareneingang&action=distribution'"
      value="Zur&uuml;ck" />
    <input type="submit" value="{|weiter zur Inhaltszuordnung|}" name="submit"></td>
    </tr>

    </tbody>
  </table>
</form>


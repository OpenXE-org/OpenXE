<form action="" method="post" id="eprooform" name="eprooform">
  <br />
  <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
    <tbody>
      <tr valign="top" colspan="3">
        <td>
          <div class="info">{|Der Artikel ist ein Lagerartikel.|}</div>
          [MESSAGE]
          <br /><br />
          <table width="60%" style="background-color: #fff; border: solid 1px #000;" align="center">
            <tr>
              <td align="center">
                <br />
                <table height="200" border="0" width="450">
                  <tr valign="top"><td><b>{|Artikel|}:</b></td><td align="center"><u>[NAME]</u></td></tr>
                  <tr valign="top"><td><br></td><td align="center"></td></tr>
                  [SHOWIMGSTART]<tr valign="top"><td><b>{|Bild|}:</b></td><td><img src="index.php?module=dateien&action=send&id=[DATEI]" width="200"></td></tr> [SHOWIMGEND]
                  <tr valign="top"><td><br></td><td align="center"></td></tr>
                  <tr valign="top"><td nowrap><b>{|1. Schritt|}:</b></td><td><input type="radio" name="anzahlauswahl" checked>&nbsp;<input type="text" size="5" value="[MENGE]" readonly>&nbsp;{|Etiketten drucken (VPE: [VPE]).|}<br>
                    <input type="radio" name="anzahlauswahl">&nbsp;<input type="text" size="5" value="[ANZAHL]">&nbsp;{|St&uuml;ck weil anders geliefert|}
                    </td>
                  </tr>
                  <tr valign="top"><td><br></td><td align="center"></td></tr>
                  <tr valign="top"><td><b>{|2. Schritt|}:</b></td><td>{|Etikettarten|}&nbsp;<select name="etikett"><option value="artikel_klein">{|Artikel klein|}</option></select>
                  <tr valign="top"><td><br></td><td align="center"></td></tr>
                  <tr valign="top"><td><b>{|3. Schritt|}:</b></td><td>{|Einlagern in|}&nbsp;<select><option>{|Zwischenlager|}</option><option>{|Manuell: Produktionslager|}</option>
                  <option>{|Manuell: Unishop-Lager|}</option></select>
                  </td></tr>
                </table>
                <br>
                <br>
              </td>
            </tr>
          </table>
          <br><br>
        </td>
      </tr>
      <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
        <td width="" valign="" height="" bgcolor="" align="right" colspan="3" bordercolor="" classname="orange2" class="orange2">
          <input type="button" onclick="window.location.href='index.php?module=wareneingang&action=distriinhalt&id=[ID]'"
            value="{|Zur&uuml;ck|}" />
          <input type="submit" name="submit"
            value="{|Etiketten drucken und Artikel einlagern|}" />
        </td>
      </tr>
    </tbody>
  </table>
</form>


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
</script>

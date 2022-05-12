<script type="application/javascript">
    $( "#artikel" ).autocomplete({
        source: "index.php?module=ajax&action=filter&filtername=artikelnummer",
        select: function( event, ui ) {
            var i = $( "#artikel" ).val()+ui.item.value;
            var zahl = i.indexOf(",");
            var text = i.slice(0, zahl);
            if(zahl <=0) {
                $( "#artikel" ).val( ui.item.value );
            } else {
                var j = $( "#artikel" ).val();
                var zahlletzte = j.lastIndexOf(",");
                var text2 = j.substring(0,zahlletzte);
                $( "#artikel" ).val( text2 + "," + ui.item.value );
            }
            return false;
        }
    });
</script>

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

  <!-- erstes tab -->
  <div id="tabs-1">

    <form action="" method="POST">
      <table width="100%">
        <tr>
          <td valign="top">
            <fieldset>
              <legend>{|Filter|}</legend>
              <table height="50">
                <tr>
                  <td>
                    [AUSWAHLFILTER]
                    <select name="beleg">
                      [BELEGART]
                    </select>
                  </td>
                </tr>
                <tr>
                  <td>
                    <label>{|Gruppieren nach|} &nbsp; <input type="checkbox" value="1" name="statistiken_artikel_gruppieren" data-checked="[STATISTIKEN_ARTIKEL_GRUPPIEREN]" id="statistiken_artikel_gruppieren" /></label>
                    <select id="gruppierungsart">
                      <option value="artikel" [SELARTIKEL]>Artikel</option>
                      <option value="kat" [SELKAT]>Artikelkategorie</option>
                      <option value="kundeartikel" [SELKUNDEARTIKEL]>Kunde: Artikel</option>
                      <option value="kundekat" [SELKUNDEKAT]>Kunde: Artikelkategorie</option>
                    </select>
                  </td>
                </tr>
              </table>
            </fieldset>
          </td>
          <td style="width:auto;" valign="top">
            <fieldset>
              <legend>{|Optional|}</legend>
              <table height="50">
                <tr>
                  <td>
                    <label for="projekt">{|Projekt|}: </label><br>
                    <input type="text" name="projekt" id="projekt" value="[PROJEKT]" />
                  </td>
                  <td>
                    <label for="artikel">{|Artikel|}: </label><br>
                    <input type="text" name="artikel" id="artikel" value="[ARTIKEL]" />
                  </td>
                  <td>
                    <label for="kategorie">{|Kategorie|}: </label><br>
                    <input type="text" name="kategorie" id="kategorie" value="[KATEGORIE]" />
                  </td>
                  <td>
                    <label for="adresse">{|Standard Lieferant|}: </label><br>
                    <input type="text" name="adresse" id="adresse" value="[ADRESSE]" />
                  </td>
                  <td>
                    <input type="submit" name="" value="Filter">
                  </td>
                </tr>
                <tr><td colspan="5"></td></tr>
                <tr>
                  <td>
                    <label for="gruppe">{|Gruppe|}: </label><br>
                    <input type="text" name="gruppe" id="gruppe" value="[GRUPPE]" />
                  </td>
                  <td>
                    <label for="gruppenkategorie">{|Gruppenkategorie|}: </label><br>
                    <input type="text" name="gruppenkategorie" id="gruppenkategorie" value="[GRUPPENKATEGORIE]" />
                  </td>
                  <td>
                    <label for="kunde">{|Kunde|}: </label><br>
                    <input type="text" name="kunde" id="kunde" value="[KUNDE]" />
                  </td>
                  <td></td>
                  <td></td>
                </tr>
              </table>
            </fieldset>
          </td>
        </tr>
      </table>
    </form>


          
    <table width="100%">
      <tr>
        <td>{|Artikel|}</td>
        <td>{|Belege|}</td>
        <td>{|Anz. Belege|}</td>
        <td>{|Gesamtmenge|}</td>
        <td>{|Gesamtkosten|}</td>
        <td>{|Gesamtumsatz|}</td>
      </tr>
      <tr>
        <td class="greybox" width="15%">[ARTIKEL]</td>
        <td class="greybox" width="15%">[BELEG]</td>
        <td class="greybox" width="15%">[GESAMTMENGE]</td>
        <td class="greybox" width="15%">[GESAMTANZAHL]</td>
        <td class="greybox" width="15%">[GESAMTKOSTEN]</td>
        <td class="greybox" width="15%">[GESAMTUMSATZ]</td>
      </tr>
    </table>
    [MESSAGE]
    [TAB1]
    [TAB1NEXT]
  </div>
<!-- tab view schließen -->
</div>
<script type="application/javascript">
  $(document).ready(function(){
    if($('#statistiken_artikel_gruppieren').data('checked')) {
      $('#statistiken_artikel_gruppieren').prop('checked', true);
      //$('#gruppieren').trigger('change');
    }
  });

</script>
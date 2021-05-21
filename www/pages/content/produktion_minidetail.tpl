<style>


.auftraginfo_cell {
  color: #636363;border: 1px solid #ccc;padding: 5px;
}

.auftrag_cell {
  color: #636363;border: 1px solid #fff;padding: 0px; margin:0px;
}

</style>


<table width="100%" border="0" cellpadding="10" cellspacing="5">
  <tr valign="top">
    <td width="">

      <br>
      <center>[MENU]</center>
      <br>

      <h2 class="greyh2">Artikel für Produktion</h2>
      <div style="padding:10px;">[ARTIKEL]</div>
      [MINIDETAILNACHARTIKEL]
      <h2 class="greyh2">Externe Produktionsschritte</h2>
      <div style="padding:10px;" id="[RAND]bestellungen">[BESTELLUNGEN]</div>


      <h2 class="greyh2">Zeiterfassung</h2>
      <div style="padding:10px;">[ZEITERFASSUNG]</div>
      <h2 class="greyh2">Arbeitsschritte</h2>
      <div style="padding:10px;">[ARBEITSSCHRITTE]</div>
      <h2 class="greyh2">Abschlussbemerkung</h2>
      <div style="padding:10px;">[ABSCHLUSSBEMERKUNG]</div>
      <div style="padding:10px;">
        <table width="100%>">
          <tr>
            <td>Zeit Geplant in Stunden</td>
            <td>Zeit Tatsächlich in Stunden</td>
            <td>Arbeitsplatzgruppe Kosten Geplant in EUR</td>
            <td>Arbeitsplatzgruppe Kosten Tatsächlich in EUR</td>
          </tr>
          <tr>
            <td class="greybox" width="25%">[ZEITGEPLANT]</td>
            <td class="greybox" width="25%">[ZEITGEBUCHT]</td>
            <td class="greybox" width="25%">[KOSTENGEPLANT]</td>
            <td class="greybox" width="25%">[KOSTENGEBUCHT]</td>  
          </tr>
        </table>
      </div>
    </td>
    <td width="450">
      <div style="overflow:auto; min-width:450px; max-height:550px;">
        <div style="background-color:white">

          <h2 class="greyh2">Produktionszentrum</h2>
          <div style="padding:10px;">
            <center>
            [BUTTONS]
            </center>
          </div>

<div style="background-color:white">
<h2 class="greyh2">Protokoll</h2>
<div style="padding:10px;">
  [PROTOKOLL]
</div>
</div>
<!--
<h2 class="greyh2">Datei Anhang</h2>
<div style="padding:10px;">[DATEIANHANGLISTE]</div>
-->

<!--
<h2 class="greyh2">Arbeitsschritte</h2>
<div style="padding:10px">
<table width="100%>">
<tr><td>Fertigstellung in %</td><td>Anzahl Schritte</td></tr>
<tr>
  <td class="greybox" width="25%">[DECKUNGSBEITRAG]</td>
  <td class="greybox" width="25%">[DBPROZENT]</td>
</tr>
</table>
</div>

-->


          <h2 class="greyh2">Abschluss Bericht</h2>
          <div style="padding:10px;">
            <table width="100%>">
              <tr>
                <td>Menge Geplant</td>
                <td>Menge Ausschuss</td>
              </tr>
              <tr>
                <td class="greybox" width="25%">[MENGEGEPLANT]</td>
                <td class="greybox" width="25%">[MENGEAUSSCHUSS]</td>
              </tr>
            </table>
          </div>

          <h2 class="greyh2">Deckungsbeitrag</h2>
          <div style="padding:10px">
            <table width="100%>">
              <tr>
                <td>Deckungsbeitrag in EUR</td>
                <td>DB in %</td>
              </tr>
              <tr>
                <td class="greybox" width="25%">[DECKUNGSBEITRAG]</td>
                <td class="greybox" width="25%">[DBPROZENT]</td>
              </tr>
            </table>

            <br>
            <br>

          </div>
        </div>

    </td>
  </tr>
</table>
<div id="[RAND]createdocdialog">
  <form id="[RAND]createdocfrom">
  <fieldset><legend>{|Artikel|}</legend>
    <div id="[RAND]createdocdialoginfo"></div>

    <input type="hidden" name="[RAND]createdocsid" id="[RAND]createdocsid" />
    <table class="mkTable" width="100%" id="[RAND]createdocdialogtable">
    </table>
  </fieldset>
  <fieldset><legend>{|Bestellung bei externen Produzent|}</legend>
    <table>
      <tr>
        <td nowrap>
          <input type="radio" id="[RAND]createdoctypecreatebestellung" name="[RAND]createdoctypebestellungtyp" value="createbestellung" checked="checked" />
          <label for="[RAND]createdoctypecreatebestellung">{|neue Bestellung anlegen|}</label>
          <label for="[RAND]createdocadresse">{|bei Lieferant|}:</label>
        </td><td nowrap>
          <input type="text" id="[RAND]createdocadresse" name="[RAND]createdocadresse" size="30" />
        </td>
      </tr>
      <tr class="[RAND]createdoctypetrlieferant"><td></td></tr>
      <tr>
        <td>
          <input type="radio" id="[RAND]createdoctypeaddtobestellung" name="[RAND]createdoctypebestellungtyp" value="addtobestellung" /> <label for="[RAND]createdoctypeaddtobestellung">{|zu bestehender Bestellung anlegen|}:</label>
        </td><td>
          <input id="[RAND]createdocbestellung" name="[RAND]createdocbestellung" type="text" size="30"/>
        </td>
      </tr>
      <tr id="[RAND]createdoctrbestellungnoprice">
        <td colspan="2">
          <input type="checkbox" id="[RAND]createdoctypebestellungnoprice" name="[RAND]createdoctypebestellungnoprice" value="1" [NEWSUPPLIERSORDERHASPRICE]/>
          <label for="[RAND]createdoctypebestellungnoprice">{|Preis für Hauptprodukt nicht setzen|}</label>
        </td>
      </tr>
    </table>
  </fieldset>
  <fieldset><legend>{|Beistellen des Materials|}</legend>
    <table>
      <tr id="[RAND]createdoctrtypebestellung">
        <td colspan="2">
          <input type="radio" id="[RAND]createdoctypebestellung" name="[RAND]createdoctype" value="bestellung" checked="checked" />
          <label for="[RAND]createdoctypebestellung">{|Artikel sofort auslagern (basierend auf Bestellung)|}</label>
        </td>
      </tr>
      <tr id="[RAND]createdoctrtypeauftrag">
        <td colspan="2">
          <input type="radio" id="[RAND]createdoctypeauftrag" name="[RAND]createdoctype" value="auftrag" />
          <label for="[RAND]createdoctypeauftrag">{|Auftrag für Beistellung anlegen|}</label>
        </td>
      </tr>
    </table>
  </fieldset>

  <fieldset><legend>{|Produktion|}</legend>
    <table>
      <tr id="[RAND]createdoctrprodstarten">
        <td colspan="2">
          <input type="checkbox" id="[RAND]createdocstartprod" name="[RAND]createdocstartprod" value="1" />
          <label for="[RAND]createdocstartprod">{|Produktion starten|}</label>
        </td>
      </tr>
    </table>
  </fieldset>
  </form>
</div>
<script type="application/javascript">
  [AUTOCOMPLETE]
  $(document).ready(function() {
    $('#[RAND]createdocdialog').dialog(
            {
              modal: true,
              autoOpen: false,
              minWidth: 940,
              title: 'Auftrag/Bestellung anlegen',
              buttons: {
                'OK': function () {
                  var $addr = $('#[RAND]createdocadresse');
                  var $best = $('#[RAND]createdocbestellung');
                  var allempty = ($($addr).val()+'' === '') &&
                          ($($best).length === 0 || $($best).val()+'' === '');
                  if(allempty) {
                    alert('Bitte eine Adresse oder Bestellung auswählen');
                  } else {
                    if($('#[RAND]createdoctypebestellung').prop('checked') || $('#[RAND]createdoctypeauftrag').prop('checked')) {
                      $.ajax({
                        url: 'index.php?module=produktion&action=edit&cmd=createdocument&id=[ID]&frame=[FRAMEPARAM]',
                        type: 'POST',
                        dataType: 'json',
                        data: $('#[RAND]createdocfrom').serialize(),
                        success: function (data) {
                          //$('#[RAND]createdocdialog').dialog('close');
                          $('#[RAND]createdocdialoginfo').html(typeof data.html != 'undefined'? data.html:'');
                          if(data.html != '') {
                            $('input.[RAND]createdoc').remove();
                          }
                          $('#[RAND]createdocdialog').dialog('close');
                          if(typeof data.deliverynoteinfo != 'undefined') {
                            $('#[RAND]bestellungen').html(data.deliverynoteinfo);
                            bindButton[RAND]();
                          }
                        },
                        beforeSend: function () {

                        }
                      });
                    }
                  }
                },
                'ABBRECHEN': function () {
                  $(this).dialog('close');
                }
              },
              close: function (event, ui) {

              }
            });
    bindButton[RAND]();
  });

  function bindButton[RAND](){
    $('input.[RAND]createdoc').on('click', function () {
      $('#[RAND]createdocsid').val($(this).data('sid'));

      $.ajax({
        url: 'index.php?module=produktion&action=edit&cmd=gettableforcreatedocument&id=[ID]',
        type: 'POST',
        dataType: 'json',
        data: {sid: $(this).data('sid'),rand:'[RAND]'},
        success: function (data) {
          $('#[RAND]createdocdialoginfo').html(typeof data.info != 'undefined'? data.info:'');
          $('#[RAND]createdocdialogtable').html(data.html);
          $('#[RAND]createdocadresse').val(data.adresse);
          $('#[RAND]createdocbestellung').html(data.bestellung);
          $('#[RAND]createdocauftrag').html(data.auftrag);
          $('#[RAND]createdocdialog').dialog('open');
          if(data.displayprodstart) {
            $('#[RAND]createdoctrprodstarten').show();
          } else {
            $('#[RAND]createdoctrprodstarten').hide();
          }
          addClicklupe();
          lupeclickevent();

          // AutoComplete-Ergebnisboxen an Dialog-Fenster anhängen.
          // Ansonsten wird AutoComplete-Ergebnis evtl. nicht sichtbar unterhalb des Dialog-Fensters angezeigt.
          var $uiDialog = $('#[RAND]createdocdialog').first();
          $($uiDialog).find('input.ui-autocomplete-input').autocomplete('option', 'appendTo', $uiDialog);
        },
        beforeSend: function () {

        }
      });
    });
  }

</script>

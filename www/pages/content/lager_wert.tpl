<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
  </ul>
<!-- ende gehort zu tabview -->
<!-- erstes tab -->
<style>
  table#lager_wert > tfoot > tr > th:nth-child(14)
  ,table#lager_wert > thead > tr >th:nth-child(14)
  ,table#lager_wert > tbody > tr >td:nth-child(14)
  {
    display:none;
  }
  table#lager_wert > tfoot > tr > th:nth-child(12) > span > input
  ,table#lager_wert > tfoot > tr > th:nth-child(10) > span > input
  ,table#lager_wert > tfoot > tr > th:nth-child(7) > span > input
  {
  max-width:50px;
  }

  .option-table td {
    display: inline-block;
    margin-right: 5px;
  }
</style>
  <div id="tabs-1">
    [MESSAGE]
    <div class="warning" id="datumsinfobox" style="display:none;"></div>
    <fieldset><legend>{|Filter|}</legend>
      <form method="POST">
        <table class="option-table">
          <tr>
            <td>{|Datum|}:</td><td><input type="text" [DATUMDISABLED] id="datum" name="datum" value="[DATUM]" onchange="holedatum()"/></td>
            <td>{|Artikel|}:</td><td><input type="text" id="artikel" name="artikel" value="[ARTIKEL]" size="40"></td>
            <td>{|Artikelkategorie|}:</td><td><input type="text" id="artikelkategorie" name="artikelkategorie" value="[ARTIKELKATEGORIE]" size="40"></td>
            <td>{|Preis|}:</td>
                <td>
                  <select id="preisart" name="preisart">
                    <option value="letzterek" [LETZTEREK]>{|Letzter EK (live mit aktuellem Wert)|}</option>
                    <option value="kalkulierterek" [KALKULIERTEREK]>{|kalkulierter EK (live mit aktuellem Wert)|}</option>
                    <option value="inventurwert" [INVENTURWERT]>{|Inventurwert (live mit aktuellem Wert)|}</option>
                    <option value="letzterekarchiv" [LETZTEREKARCHIV]>{|Letzter EK (nur aus Archiv)|}</option>
                    <option value="kalkulierterekarchiv" [KALKULIERTEREKARCHIV]>{|kalkulierter EK (nur aus Archiv)|}</option>
                    <option value="inventurwertarchiv" [INVENTURWERTARCHIV]>{|Inventurwert (nur aus Archiv)|}</option>
                  </select>
                </td>
            <td>
              <input type="checkbox" value="1" id="gruppierenlager" name="gruppierenlager" [GRUPPIERENLAGER]/>
              <label for="gruppierenlager">{|Gruppieren Lager|}</label>
            </td>
            <td>
              <input type="checkbox" value="1" id="preiseineuro" name="preiseineuro" [PREISEINEURO]/>
              <label for="preiseineuro">{|alle Preise in EUR anzeigen|}</label>
            </td>
            <td>
              <input type="submit" value="{|laden|}" name="laden"/>
            </td>
          </tr>
        </table>
      </form>
    </fieldset>
    [TAB1]
    [TAB1NEXT]
  </div>
<!-- tab view schließen -->
</div>

<script>
  function holedatum(){
    var datum = $('#datum').val();
    $.ajax({
        url: 'index.php?module=lager&action=wert&cmd=datumpruefen&datum='+datum,
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function(data) {
          if(data == ''){
            document.getElementById('datumsinfobox').style.display = 'none';
          }else{
            document.getElementById('datumsinfobox').style.display = '';
            document.getElementById('datumsinfobox').innerHTML = '<div id="infoberechnung">Vor dem '+data+' liegen keine Berechnungen f&uuml;r Lagerbewegungen vor.</div>';
          }
        },
        beforeSend: function() {

        }
    });
  }

</script>

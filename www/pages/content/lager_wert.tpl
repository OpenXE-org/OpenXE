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
            <td>{|Datum|}:</td><td><input type="text" id="datum" name="datum" value="[DATUM]"/></td>
            <td>{|Preis|}:</td>
                <td>
                  <select id="preisart" name="preisart">
                    <option value="letzterek" [LETZTEREK]>{|EK aus Einkaufspreisen|}</option>
                    <option value="kalkulierterek" [KALKULIERTEREK]>{|Kalkulierter EK (wenn vorhanden)|}</option>
                    <option value="inventurwert" [INVENTURWERT]>{|Inventurwert (wenn vorhanden)|}</option>
                  </select>
                </td>
            <td>
              <input type="checkbox" value="1" id="preiseineuro" name="preiseineuro" [PREISEINEURO]/>
              <label for="preiseineuro">{|Preise in EUR|}</label>
            </td>
            <td>
              <input type="checkbox" value="1" id="sperrlager_nicht_bewerten" name="sperrlager_nicht_bewerten" [SPERRLAGER_NICHT_BEWERTEN]/>
              <label for="sperrlager_nicht_bewerten">{|Sperrlager nicht bewerten|}</label>
            </td>
            <td>
              <input type="checkbox" value="1" id="konsignationslager_nicht_bewerten" name="konsignationslager_nicht_bewerten" [KONSIGNATIONSLAGER_NICHT_BEWERTEN]/>
              <label for="konsignationslager_nicht_bewerten">{|Konsignationslager nicht bewerten|}</label>
            </td>
            <td>
              <input type="submit" value="{|Laden|}" name="laden"/>
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


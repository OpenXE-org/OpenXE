<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
  <div id="tabs-1">
    <form action="" method="post">
      [MESSAGE]
      <div class="row">
        <div class="row-height">
          <div class="col-xs-12 col-md-12 col-md-height">
            <div class="inside_white inside-full-height">
              <fieldset><legend>{|Best&auml;tigung|}</legend>
              <div class="warning"><input type="checkbox" valie="1" name="bestaetigen" />
                {|Ich best&auml;tige, dass ich die Adressen &uuml;bertragen will. Bei falsch eingetragenen Adressedaten kann dies zu <b>Verlust von Kundendaten</b> f&uuml;hren.<br />Pr&uuml;fen Sie erst eine &Uuml;bertragung manuell, bevor Sie einen Massenexport ausf&uuml;hren.|}

              </div>
              </fieldset>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
      <div class="row-height">
      <div class="col-xs-12 col-md-10 col-md-height">
      <div class="inside_white inside-full-height">
        <fieldset class="white">
          <legend></legend>
          [TAB1]
        </fieldset>
      </div>
      </div>
      <div class="col-xs-12 col-md-2 col-md-height">
      <div class="inside inside-full-height">
        <fieldset>
          <legend>{|Aktionen|}</legend>
            <input type="submit" class="btnBlueNew" value="{|Alle Adressen exportieren|}" name="alle" onclick="if(!confirm('{|Wollen Sie wirklich alle Adressen an den Shop übertragen? Eventuell werden hierbei auch Daten überschrieben. Bitte prüfen Sie das Verhalten vorher an einigen Adressen. Bitte nehmen Sie in jedemfall vorab eine Sicherung im Shop vor.|}')) return false;"><br>
            <input type="submit" class="btnBlueNew" value="{|&Uuml;bertragung komplett abbrechen|}" name="abbrechen"><br>
        </fieldset>
        <fieldset>
          <legend>{|Adressen laden|}</legend>
          <table class="mkTableFormular">
            <tr>
              <td>{|Adresse|}:</td>
            </tr>
            <tr>
              <td nowrap><input type="text" name="adresse" id="adresse" size="18">&nbsp;<input type="submit" class="btnBlue" name="adresseladen" value="{|laden|}"></td>
            </tr>
            <tr>
              <td>{|Gruppe|}:</td>
            </tr>
            <tr>
              <td><input type="text" name="gruppe" id="gruppe" size="18">&nbsp;<input type="submit" class="btnBlue" name="gruppeladen" value="{|laden|}"></td>
            </tr>
          </table>
        </fieldset>
      </div>
      </div>
      </div>
      </div>

    </form>
  </div>

<!-- tab view schließen -->
</div>


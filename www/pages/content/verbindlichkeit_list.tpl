<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
            <li><a href="#tabs-2">[TABTEXT2]</a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]

         <div class="filter-box filter-usersave">
            <div class="filter-block filter-inline">
              <div class="filter-title">{|Filter|}</div>
              <ul class="filter-list">
                <li class="filter-item">
                  <label for="anhang" class="switch">
                    <input type="checkbox" id="anhang">
                    <span class="slider round"></span>
                  </label>
                  <label for="anhang">{|Anhang fehlt|}</label>
                </li>
                 <li class="filter-item">
                  <label for="wareneingang" class="switch">
                    <input type="checkbox" id="wareneingang">
                    <span class="slider round"></span>
                  </label>
                  <label for="wareneingang">{|Wareingang/Leistungspr&uuml;fung fehlt|}</label>
                </li>                        
                <li class="filter-item">
                  <label for="rechnungsfreigabe" class="switch">
                    <input type="checkbox" id="rechnungsfreigabe">
                    <span class="slider round"></span>
                  </label>
                  <label for="rechnungsfreigabe">{|Rechnungseingangspr&uuml;fung fehlt|}</label>
                </li>
                <li class="filter-item">
                  <label for="nichtbezahlt" class="switch">
                    <input type="checkbox" id="nichtbezahlt">
                    <span class="slider round"></span>
                  </label>
                  <label for="nichtbezahlt">{|Nicht bezahlt|}</label>
                </li>
                <li class="filter-item">
                  <label for="stornierte" class="switch">
                    <input type="checkbox" id="stornierte">
                    <span class="slider round"></span>
                  </label>
                  <label for="stornierte">{|Inkl. stornierte|}</label>
                </li>
                <li class="filter-item">
                  <label for="abgeschlossen" class="switch">
                    <input type="checkbox" id="abgeschlossen">
                    <span class="slider round"></span>
                  </label>
                  <label for="abgeschlossen">{|Inkl. abgeschlossene|}</label>
                </li>
                <li class="filter-item">
                    <label for="zahlbarbis">{|Zahlbar bis|}:</label>
                    <input type="text" name="zahlbarbis" id="zahlbarbis" size="10">
                </li>
                <li class="filter-item">
                    <label for="skontobis">{|Skonto bis|}:</label>
                    <input type="text" name="skontobis" id="skontobis" size="10">
                </li>
              </ul>
              <form method="post" action="#">
                <button name="submit" value="status_berechnen" class="ui-button-icon">{|Status auffrischen|}</button>
              </form>              
            </div>    
          </div>

        <form method="post" action="#">
            [TAB1]
            <fieldset><legend>{|Stapelverarbeitung|}</legend>
                <input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();" />&nbsp;{|alle markieren|}&nbsp;
                <select id="sel_aktion" name="sel_aktion">
                    <option value="">{|bitte w&auml;hlen|} ...</option>
                    [MANUELLFREIGABEEINKAUF]                    
                    [MANUELLFREIGABEBUCHHALTUNG]
                    [ALSBEZAHLTMARKIEREN]
                    <option value="drucken">{|drucken|}</option>
                </select>
                &nbsp;{|Drucker|}: <select name="seldrucker">[SELDRUCKER]</select>&nbsp;
                <button name="submit" value="ausfuehren" class="ui-button-icon">{|Ausf&uuml;hren|}</button>                
            </fieldset>
        </form>
        [TAB1NEXT]
    </div>
    <div id="tabs-2">
      [TAB2]
    </div>
</div>

<script>
function alleauswaehlen()
{
  var wert = $('#auswahlalle').prop('checked');
  $('#verbindlichkeit_list').find(':checkbox').prop('checked',wert);
}

</script>

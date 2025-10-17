<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]

        <div class="filter-box filter-usersave">
            <div class="filter-block filter-inline">
              <div class="filter-title">{|Filter|}</div>
              <ul class="filter-list">
                <li class="filter-item">
                  <label for="bezahlt" class="switch">
                    <input type="checkbox" id="bezahlt">
                    <span class="slider round"></span>
                  </label>
                  <label for="bezahlt">{|Inkl. bezahlte|}</label>
                </li>
                <li class="filter-item">
                  <label for="imzahllauf" class="switch">
                    <input type="checkbox" id="imzahllauf">
                    <span class="slider round"></span>
                  </label>
                  <label for="imzahllauf">{|Inkl. bereits im Zahllauf|}</label>
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
            </div>
        </div>

        <form action="#tabs-1" id="frmauto" name="frmauto" method="post">
            [TAB1]
            [TAB1NEXT]
            <table>
                <legend>Stapelverarbeitung</legend>
                <tr>
                    <td>
                        <input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;
                    </td>
                    <td>
                        <button name="submit" value="anlegen" class="ui-button-icon">{|Anlegen|}</button>
                        <button name="submit" value="ausfuehren" class="ui-button-icon">{|Ausf&uuml;hren|}</button>
                    </td>
                </tr>
            </table>
        </form>
    </div>
</div>

<script>

    $('#autoalle').on('change',function(){
      var wert = $(this).prop('checked');
      $('#zahlungsverkehr_ueberweisung').find('input[type="checkbox"]').prop('checked',wert);
      $('#zahlungsverkehr_ueberweisung').find('input[type="checkbox"]').first().trigger('change');
    });

</script>

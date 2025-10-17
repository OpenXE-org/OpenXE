<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <form action="#tabs-1" id="frmauto" name="frmauto" method="post">
            <div class="filter-box filter-usersave">
               <div class="filter-block filter-inline">
                  <div class="filter-title">{|Filter|}</div>
                  <ul class="filter-list">
                     [STATUSFILTER]
                     <li class="filter-item">
                        <label for="offene" class="switch">
                        <input type="checkbox" id="offene">
                        <span class="slider round"></span>
                        </label>
                        <label for="offene">{|Offene|}</label>
                     </li>
                     <li class="filter-item">
                        <label for="ausgef&uuml;hrte" class="switch">
                        <input type="checkbox" id="ausgef&uuml;hrte">
                        <span class="slider round"></span>
                        </label>
                        <label for="ausgef&uuml;hrte">{|Ausgef&uuml;hrte|}</label>
                     </li>
                     <li class="filter-item">
                        <label for="fehlgeschlagene" class="switch">
                        <input type="checkbox" id="fehlgeschlagene">
                        <span class="slider round"></span>
                        </label>
                        <label for="fehlgeschlagene">{|Fehlgeschlagene|}</label>
                     </li>
                  </ul>
               </div>
            </div>
            [TAB1]
            [TAB1NEXT]
            <table>
                <legend>Stapelverarbeitung</legend>
                <tr>
                    <td>
                        <input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;
                    </td>
                    <td>
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
      $('#zahlungsverkehr_list').find('input[type="checkbox"]').prop('checked',wert);
      $('#zahlungsverkehr_list').find('input[type="checkbox"]').first().trigger('change');
    });

</script>

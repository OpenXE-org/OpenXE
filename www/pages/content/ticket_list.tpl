<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
           [VORTABS2UEBERSCHRIFT]<li><a href="#tabs-2">[TABTEXT2]</a></li>[NACHTABS2UEBERSCHRIFT]
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
                  <label for="meinetickets" class="switch">
                    <input type="checkbox" id="meinetickets">
                    <span class="slider round"></span>
                  </label>
                  <label for="meinetickets">{|Meine|}</label>
                </li>           
                <li class="filter-item">
                  <label for="prio" class="switch">
                    <input type="checkbox" id="prio">
                    <span class="slider round"></span>
                  </label>
                  <label for="prio">{|Prio|}</label>
                </li>
                <li class="filter-item">
                  <label for="geschlossene" class="switch">
                    <input type="checkbox" id="geschlossene">
                    <span class="slider round"></span>
                  </label>
                  <label for="geschlossene">{|Zzgl. abgeschlossen|}</label>
                </li>
                <li class="filter-item">
                  <label for="spam" class="switch">
                    <input type="checkbox" id="spam">
                    <span class="slider round"></span>
                  </label>
                  <label for="spam">{|Zzgl. Papierkorb|}</label>
                </li>
              </ul>
            </div>
          </div>
      [TAB1]
            <fieldset>
                <table>
                    <legend>Stapelverarbeitung</legend>
                    <tr><td>{|Status|}:</td><td><select name="status">[STATUS]</select></td></tr>
                     <tr><td>{|Verantwortlich|}:</td><td><input type="text" name="warteschlange" id="warteschlange" value="[WARTESCHLANGE]" size="20"></td></tr>
                    <tr>
                        <td>                            
                            <input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;
                        </td>
                        <td>
                            <button name="submit" value="zuordnen" class="ui-button-icon" style="width:100%;">{|Zuordnen|}</button>
                        </td>
                    </tr>
                    <tr [SPAM_HIDDEN]>
                        <td>
                        </td>
                        <td>
                            <button name="submit" value="spam_filter" class="ui-button-icon" title="Ticket auf Status 'Papierkorb' setzen und Absender-Adresse in Ticketregel eintragen" style="width:100%;" onclick="if(confirm('Wirklich Ticketregel erstellen?'))document.getElementById('form-id').submit(); else return false;">{|Spamregel erstellen|}</button>
                        </td>
                    </tr>
                </table>

            </fieldset>
        </form>
      [TAB1NEXT]
    </div>
</div>

<script>

    $('#autoalle').on('change',function(){
      var wert = $(this).prop('checked');
      $('#ticket_list').find('input[type="checkbox"]').prop('checked',wert);
      $('#ticket_list').find('input[type="checkbox"]').first().trigger('change');
    });
  
</script>

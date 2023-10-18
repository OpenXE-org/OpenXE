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
                    [STATUSFILTER]                    
                    <li class="filter-item">
                        <label for="geschlossene" class="switch">
                            <input type="checkbox" id="geschlossene">
                            <span class="slider round"></span>
                        </label>
                        <label for="geschlossene">{|Zzgl. abgeschlossen|}</label>
                    </li>
                    <li class="filter-item">
                        <label for="stornierte" class="switch">
                            <input type="checkbox" id="stornierte">
                            <span class="slider round"></span>
                        </label>
                        <label for="stornierte">{|Zzgl. Papierkorb|}</label>
                    </li>
                </ul>
            </div>
        </div>
        <form action="index.php?module=versandpakete&action=stapelverarbeitung" id="frmauto" name="frmauto" method="post">
            [TAB1]
            <fieldset>
                <table>
                    <legend>Stapelverarbeitung</legend>
                    <tr>
                       <tr><td>{|Status|}:</td><td><select name="status">[STATUS_OPTIONS]</select></td></tr>
                       <td><input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;</td><td><input type="submit" class="btnBlue" name="status_setzen" value="{|Status setzen|}" /></td>
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
      $('#versandpakete_list').find('input[type="checkbox"]').prop('checked',wert);
      $('#versandpakete_list').find('input[type="checkbox"]').first().trigger('change');
    });
  
</script>

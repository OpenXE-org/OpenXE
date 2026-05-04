<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">
        <form action="#tabs-1" id="frmauto" name="frmauto" method="post">
            [MESSAGE]
            [TAB1]
            [TAB1NEXT]
            <fieldset>
                <table>
                    <legend>Stapelverarbeitung</legend>
                    <tr>
                        <td>
                            <input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;
                        </td>
                        <td>
                            <button name="submit" value="stornieren" class="ui-button-icon" onclick="if(confirm('Wirklich stornieren? Lagerbuchungen m&uuml;ssen manuell korrigiert werden!'))document.getElementById('form-id').submit(); else return false;">{|Stornieren|}</button>
                        </td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
</div>

<script>

    $('#autoalle').on('change',function(){
      var wert = $(this).prop('checked');
      $('#kommissionierung_list').find('input[type="checkbox"]').prop('checked',wert);
      $('#kommissionierung_list').find('input[type="checkbox"]').first().trigger('change');
    });

</script>

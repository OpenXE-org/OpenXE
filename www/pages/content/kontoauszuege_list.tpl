<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">
        <form action="#tabs-1" id="frmauto" name="frmauto" method="post">
            [MESSAGE]
            <legend>[INFO]</legend>
            [TAB1]
            <fieldset>
                <table>
                    <legend>Stapelverarbeitung</legend>
                    <tr>
                        <td><input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;</td>
                        <td><input type="submit" class="btnBlue" name="ausfuehren" value="{|Importfehler|}" /></td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
</div>

<script>

    $('#autoalle').on('change',function(){
      var wert = $(this).prop('checked');
      $('#kontoauszuege_list').find('input[type="checkbox"]').prop('checked',wert);
      $('#kontoauszuege_list').find('input[type="checkbox"]').first().trigger('change');
    });
  
</script>

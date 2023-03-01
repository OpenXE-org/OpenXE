<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">
        <form action="#tabs-1" id="frmauto" name="frmauto" method="post">
        [MESSAGE]
        [TAB1]
            <fieldset>
                <table>
                    <legend>Stapelverarbeitung</legend>
                    <tr>
                        <td><input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;</td><td><input type="submit" class="btnBlue" name="ausfuehren" value="{|L&ouml;schen|}" /></td>
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
      $('#kontorahmen_list').find('input[type="checkbox"]').prop('checked',wert);
      $('#kontorahmen_list').find('input[type="checkbox"]').first().trigger('change');
    });
  
</script>

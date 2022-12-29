<div id="tabs-1">
    [MESSAGE]
        <form>
           [TAB1]
           <fieldset>
                <table>
                    <legend>Stapelverarbeitung</legend>
                    <tr>
                        <td><input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;</td>
                        <td><input type="submit" class="btnBlue" name="ausfuehren" value="{|Bestellung erzeugen|}"/></td>
                    </tr>
                </table>
            </fieldset>
        </form>
    [TAB1NEXT]
</div>

<script>

    $('#autoalle').on('change',function(){
      var wert = $(this).prop('checked');
      $('#bestellvorschlag_list').find('input[type="checkbox"]').prop('checked',wert);
      $('#bestellvorschlag_list').find('input[type="checkbox"]').first().trigger('change');
    });
  
</script>

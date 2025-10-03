<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">
       <div class="row">
        	<div class="row-height">
        		<div class="col-xs-12 col-md-12 col-md-height">
        			<div class="inside inside-full-height">
        				<fieldset>
                            <legend>{|<b>Pakete zu <font color="blue"><a href="index.php?module=lieferschein&action=edit&id=[BELEGID]">Lieferschein [BELEGNR]</a></font></b>|}</legend>
                        </fieldset>
                    </div>
           		</div>
           	</div>	
        </div>
        [MESSAGE]
        <div class="row">
        	<div class="row-height">
        		<div class="col-xs-12 col-md-12 col-md-height">
        			<div class="inside inside-full-height">
                        <form action="index.php?module=versandpakete&action=stapelverarbeitung&from=lieferung&id=[FROMID]" id="frmauto" name="frmauto" method="post">
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
                    </div>
           		</div>
           	</div>	
        </div>
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

<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        [FORMHANDLEREVENT]
        <div class="row">
        	<div class="row-height">
        		<div class="col-xs-12 col-md-12 col-md-height">
        			<div class="inside inside-full-height">	     
           				<fieldset>
                            <legend>{|Offene Einzelsalden|}</legend>                              
                            <form action="" method="post" id="buchungenform">                      
                                [TAB1]                            
                            </form> 
                        </fieldset>    
                        <fieldset>
                            <table>
                                <legend>Stapelverarbeitung</legend>
                                <tr>
                                    <td><input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;</td>
                                </tr>
                                <form action="" method="post">                      
                                    <td><button name="submit" value="neuberechnen" class="ui-button-icon" style="width:100%;">{|Buchungen neu berechnen|}</button></td></tr>                                       
                                </form> 
                                <td><button name="submit" form = "buchungenform" value="BUCHEN" class="ui-button-icon" style="width:100%;">{|Markierte BUCHEN|}</button></td></tr>
                            </table>
                        </fieldset>  				
                    </div>
           		</div>
           	</div>	
        </div>
    </div>
</div>

<script>

    $('#autoalle').on('change',function(){
      var wert = $(this).prop('checked');
      $('#fibu_buchungen_zuordnen').find('input[type="checkbox"]').prop('checked',wert);
      $('#fibu_buchungen_zuordnen').find('input[type="checkbox"]').first().trigger('change');
    });
  
</script>

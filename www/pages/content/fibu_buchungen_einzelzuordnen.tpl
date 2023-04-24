<form action="" method="post" id="buchungenform">                             				                           
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
    <div id="tabs-1">        
        [MESSAGE]
        [FORMHANDLEREVENT]
        <legend>{|Einzelsaldo zuordnen und auf mehrere Gegenbelege oder Sachkonto verbuchen.|}</legend>      
        <div class="row">
        	<div class="row-height">
        		<div class="col-xs-12 col-md-6 col-md-height">
        			<div class="inside inside-full-height">	     
                        <fieldset>     
                            <table>    
                                <tr>                            
                                    <td>
                                        "[DOC_ZUORDNUNG]"                                            
                                    </td>                                       
                                </tr>           
                                    <tr>                                           
                                    <td>
                                        Saldo: <u>[DOC_SALDO]</u>
                                    </td>
                                </tr>           
                            </table>
                        </fieldset>    
                    </div>
           		</div>
                <div class="col-xs-12 col-md-6 col-md-height">
        			<div class="inside inside-full-height">	     
                        <fieldset>     
                            <table>    
                                <tr>                            
                                    <td>
                                        Multifilter f&uuml;r "Info" (Trennzeichen ',; ')
                                    </td>
                                </tr>           
                                <tr>                                           
                                    <td>
                                        <textarea type="text" name="multifilter" id="multifilter" style="width:100%;">[MULTIFILTER]</textarea>
                                    </td>
                                    <td>
                                        <button name="submit" value="multifilter" class="ui-button-icon">{|Filtern|}</button>
                                    </td>
                                </tr>           
                            </table>
                        </fieldset>    
                    </div>
           		</div>
           	</div>	
        </div>
        <div class="row">
        	<div class="row-height">
        		<div class="col-xs-12 col-md-12 col-md-height">
        			<div class="inside inside-full-height">	     
                        [TAB1]                            
                        <fieldset>                           
                            <table>
                                <legend>Stapelverarbeitung</legend>                          
                                <tr>                            
                                    <td><input type="checkbox" value="1" name="override" form="buchungenform" />&nbsp;Mit Abweichung buchen&nbsp;</td>
                                </tr>
                                <tr>
                                    <td><input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;
                                        <select id="sel_aktion" name="sel_aktion">
                                            <option value="buchen">{|auf Ausgew&auml;hlte buchen|}</option>
                                            <option value="buchen_diff_sachkonto">{|auf Ausgew&auml;hlte buchen, Gegenbeleg auf Sachkonto ausgleichen|}</option>
                                        </select>&nbsp;Sachkonto:
                                        <input type="text" id="sachkonto" name="sachkonto" value="">
                                        <button name="submit" value="BUCHEN" class="ui-button-icon">{|BUCHEN|}</button>
                                    </td>
                                </tr>                             
                                <tr>                                           
                                    <td>
                                        <input type="number" name="abschlag" id="abschlag" value=[ABSCHLAG] />% Abschlag auf Buchungsbetrag</td>
                                    </td>
                                </tr>           
                                <tr>
                                    <td>
                                        <button name="submit" value="neuberechnen" class="ui-button-icon" style="width:100%;">
                                            {|Buchungen neu berechnen|}
                                        </button>
                                    </td>                                
                                </tr>                                                         
                            </table>
                        </fieldset>  				
                    </div>
           		</div>
           	</div>	
        </div>
    </div>
</div>

</form> 

<script>

    $('#autoalle').on('change',function(){
      var wert = $(this).prop('checked');
      $('#fibu_buchungen_einzelzuordnen').find('input[type="checkbox"]').prop('checked',wert);
      $('#fibu_buchungen_einzelzuordnen').find('input[type="checkbox"]').first().trigger('change');
    });
  
</script>

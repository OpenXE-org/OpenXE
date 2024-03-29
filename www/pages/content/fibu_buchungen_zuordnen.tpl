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
                            <legend>{|Einzelsalden zuordnen und auf Gegenbelege oder Sachkonto verbuchen.|}</legend>                              
                            <div class="filter-box filter-usersave">
                               <div class="filter-block filter-inline">
                                  <div class="filter-title">{|Filter|}</div>
                                  <ul class="filter-list">
                                     <li class="filter-item">
                                        <label for="vorschlagfilter" class="switch">
                                        <input type="checkbox" id="vorschlagfilter">
                                        <span class="slider round"></span>
                                        </label>
                                        <label for="vorschlagfilter">{|Vorschl&auml;ge|}</label>
                                     </li>                 
                                     <li class="filter-item">
                                        <label for="checkedfilter" class="switch">
                                        <input type="checkbox" id="checkedfilter">
                                        <span class="slider round"></span>
                                        </label>
                                        <label for="checkedfilter">{|Betrag korrekt|}</label>
                                     </li>                 
                                  </ul>
                               </div>
                            </div>
                            <form action="" method="post" id="buchungenform">                      
                                [TAB1]                            
                            </form> 
                        </fieldset>    
                        <fieldset>
                            <table>
                                <legend>Stapelverarbeitung</legend>
                                <tr>
                                    <td><input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;
                                        <select form="buchungenform" id="sel_aktion" name="sel_aktion">
                                            <option value="vorschlag">{|auf Vorschlag buchen|}</option>
                                            <option value="vorschlag_diff_sachkonto">{|auf Vorschlag buchen, Gegenbeleg auf Sachkonto ausgleichen|}</option>
                                            <option value="sachkonto">{|auf Sachkonto buchen|}</option>
                                        </select>&nbsp;Sachkonto:
                                        <input type="text" form="buchungenform" id="sachkonto" name="sachkonto" value="">
                                        <button name="submit" form="buchungenform" value="BUCHEN" class="ui-button-icon">{|BUCHEN|}</button>
                                    </td>
                                </tr>                             
                                <tr>
                                   <form action="" method="post">                      
                                        <td>
                                            <button name="submit" value="neuberechnen" class="ui-button-icon" style="width:100%;">
                                                {|Buchungen neu berechnen|}
                                            </button>
                                        </td>                                
                                    </form> 
                                </tr>                                                         
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

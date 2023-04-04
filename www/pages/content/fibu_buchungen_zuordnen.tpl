<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        [FORMHANDLEREVENT]
        <div class="row">
        	<div class="row-height">
        		<div class="col-xs-12 col-md-10 col-md-height">
        			<div class="inside inside-full-height">	     
           				<fieldset>
                            <legend>{|Offene Einzelsalden|}</legend>                              
                            <form action="" method="post" id="buchungenform">                      
                                [TAB1]                            
                            </form> 
                        </fieldset>      				
                    </div>
           		</div>
        		<div class="col-xs-12 col-md-2 col-md-height">
        			<div class="inside inside-full-height">	        
               				<fieldset>				
                            <table width="100%" border="0" class="mkTableFormular">            
                                <legend>{|Aktionen|}</legend>       
                                <form action="index.php?module=fibu_buchungen&action=list" method="post">                      
                                    <td><button name="submit" value="neuberechnen" class="ui-button-icon" style="width:100%;">{|Buchungen neu berechnen|}</button></td></tr>                                       
                                </form> 
                                <td><button name="submit" form = "buchungenform" value="BUCHEN" class="ui-button-icon" style="width:100%;">{|BUCHEN|}</button></td></tr>
                            </table>
                        </fieldset>      				                            
                    </div>
           		</div>
           	</div>	
        </div>
    </div>
</div>


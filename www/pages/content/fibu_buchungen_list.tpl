<div id="tabs">
    <ul>
            <li><a href="#tabs-1">Salden</a></li>
            <li><a href="#tabs-2">Buchungen</a></li>
    </ul>
    <div id="tabs-1">
        <legend>Salden m&uuml;ssen &uuml;ber Gegenbuchungen ausgeglichen werden.</legend>
        <form action="" enctype="multipart/form-data" method="POST">   
            [MESSAGE]
            <div class="row">
            	<div class="row-height">
            		<div class="col-xs-12 col-md-10 col-md-height">
            			<div class="inside inside-full-height">	     
               				<fieldset>
                                <legend>{|Offene Einzelsalden|}</legend>                              
                                [TAB1]                            
                            </fieldset>      				
                        </div>
               		</div>
            		<div class="col-xs-12 col-md-2 col-md-height">
            			<div class="inside inside-full-height">	        
                   				<fieldset>				
                                <table width="100%" border="0" class="mkTableFormular">
                                    <legend>{|Aktionen|}</legend>                          
                                    <td><button name="submit" value="neuberechnen" class="ui-button-icon" style="width:100%;">Buchungen neu berechnen</button></td></tr>
                                </table>
                            </fieldset>      				                            
                        </div>
               		</div>
               	</div>	
            </div>        
        </form>
        [TAB1NEXT]
    </div>
    <div id="tabs-2">
        <form action="#tabs-2" enctype="multipart/form-data" method="POST">   
            [MESSAGE]
            <div class="row">
            	<div class="row-height">
            		<div class="col-xs-12 col-md-10 col-md-height">
            			<div class="inside inside-full-height">	     
               				<fieldset>
                                <legend>{|Einzelbuchungen|}</legend>                              
                                [TAB2]                            
                            </fieldset>      				
                        </div>
               		</div>
            		<div class="col-xs-12 col-md-2 col-md-height">
            			<div class="inside inside-full-height">	        
                   				<fieldset>				
                                <table width="100%" border="0" class="mkTableFormular">
                                    <legend>{|Aktionen|}</legend>                          
                                    <td><button name="submit" value="neuberechnen" class="ui-button-icon" style="width:100%;">Buchungen neu berechnen</button></td></tr>
                                </table>
                            </fieldset>      				                            
                        </div>
               		</div>
               	</div>	
            </div>        
        </form>
        [TAB2NEXT]
    </div>
</div>

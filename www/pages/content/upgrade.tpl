<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <!-- Example for multiple tabs
    <ul hidden">
        <li><a href="#tabs-1">First Tab</a></li>
        <li><a href="#tabs-2">Second Tab</a></li>
    </ul>
    -->
    <div id="tabs-1">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-14 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
                            <div class="row">
	                        	<div class="row-height">
	                        		<div class="col-xs-14 col-md-12 col-md-height">
	                        			<div class="inside inside-full-height">
	                        				<fieldset>
                                                <legend>{|Aktuelle Version|}</legend>
                                                <table width="100%" border="0" class="mkTableFormular">
                                                    <b>OpenXE [CURRENT]</b>
                                                </table>
                                            </fieldset>            
                                        </div>
                               		</div>           
                           		</div>
                       		</div>
                            <div [PENDING_VISIBLE] class="row">
	                        	<div class="row-height">
	                        		<div class="col-xs-14 col-md-12 col-md-height">
	                        			<div class="inside inside-full-height">
	                        				<fieldset>
                                                <legend>{|Verf&uuml;gbare Upgrades|}</legend>
                                                <table width="100%" border="0" class="mkTableFormular">
                                                     <pre style="white-space:pre-line;">
[OUTPUT_FROM_CLI]
                                                    </pre>
                                                </table>
                                            </fieldset>            
                                        </div>
                               		</div>           
                           		</div>
                       		</div>
                            <div [PROGRESS_VISIBLE] class="row">
	                        	<div class="row-height">
	                        		<div class="col-xs-12 col-md-12 col-full-height">
	                        			<div class="inside inside-full-height">
	                        				<fieldset>
                                                <legend>{|Upgrade-Fortschritt|}</legend>
                                                <table width="100%" border="0" class="mkTableFormular">
                                                    <pre style="white-space:pre-line;">
                [OUTPUT_FROM_CLI]
                                                    </pre>
                                                </table>
                                            </fieldset>            
                                        </div>
                               		</div>
                               	</div>
                            </div>	
                   		</div>
               		</div>               	
	            	<div class="col-xs-14 col-md-2 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>{|Aktionen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Details anzeigen|}:</td><td><input type="checkbox" name="details_anzeigen" value=1 size="20"></td></tr>
                                    <tr><td colspan=2><button name="submit" value="refresh" class="ui-button-icon" style="width:100%;">Auffrischen</button></td></tr>
                                    <tr><td>{|Erzwingen (-f)|}:</td><td><input type="checkbox" name="erzwingen" value=1 size="20"></td></tr>
                                    <tr><td colspan=2><button name="submit" value="do_upgrade" class="ui-button-icon" style="width:100%;">UPGRADE!</button></td></tr>
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
         </form>
    </div>    
    <!-- Example for 2nd tab
    <div id="tabs-2">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
            	<div class="row-height">
            		<div class="col-xs-12 col-md-12 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>{|...|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    ...
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
            <input type="submit" name="submit" value="Speichern" style="float:right"/>
        </form>
    </div>    
    -->
</div>


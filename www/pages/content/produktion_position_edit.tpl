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
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|<a href="index.php?module=produktion&action=edit&id=[PRODUKTIONID]#tabs-3">PRODUKTION [PRODUKTIONBELEGNR]<a>|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Artikel|}:</td><td><input type="text" id="artikel" name="artikel" value="[ARTIKEL]" size="40"></td></tr>
                                    <tr><td>{|Menge|}:</td><td><input type="text" name="menge" value="[MENGE]" size="40"></td></tr>
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
            <input type="submit" name="submit" value="Speichern" style="float:right"/>
        </form>
    </div>    
</div>        

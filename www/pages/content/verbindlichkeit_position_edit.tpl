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
                                <legend>{|Position bearbeiten|}</legend><i></i>
                                <table width="100%" border="0" class="mkTableFormular">                                    
                                    <tr>
                                        <td>
                                            {|Menge|}:
                                        </td>
                                        <td>
                                            <input type="number" name="menge" id="menge" value="[MENGE]" size="20" [SAVEDISABLED]>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Preis|}:
                                        </td>
                                        <td>
                                            <input type="number" name="preis" id="preis" step="0.0000000001" value="[PREIS]" size="20" [SAVEDISABLED]>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Steuersatz %|}:
                                        </td>
                                        <td>
                                            <input type="number" name="steuersatz" id="steuersatz" value="[STEUERSATZ]" size="20" [SAVEDISABLED]>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Sachkonto|}:
                                        </td>
                                        <td>
                                            <input type="text" name="sachkonto" id="sachkonto" value="[SACHKONTO]" size="20" [SACHKONTOSAVEDISABLED]>
                                        </td>
                                    </tr>                                    
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


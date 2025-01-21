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
                                <legend>{|<!--Legend for this form area goes here>-->Kostenstellen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Nummer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="nummer" id="nummer" value="[NUMMER]" required size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Beschreibung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="beschreibung" id="beschreibung" value="[BESCHREIBUNG]" required size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Internebemerkung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="internebemerkung" id="internebemerkung" value="[INTERNEBEMERKUNG]" size="20">
                                        </td>
                                    </tr>
                                    
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div>
            <!-- Example for 2nd row            
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Another legend|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Nummer|}:
                                        </td>
                                        <td>
                                            <input type="text" name="nummer" id="nummer" value="[NUMMER]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Beschreibung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="beschreibung" id="beschreibung" value="[BESCHREIBUNG]" size="20">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Internebemerkung|}:
                                        </td>
                                        <td>
                                            <input type="text" name="internebemerkung" id="internebemerkung" value="[INTERNEBEMERKUNG]" size="20">
                                        </td>
                                    </tr>
                                    
                                </table>
                            </fieldset>            
                        </div>
               		</div>
               	</div>	
            </div> -->
            <input type="submit" name="submit" value="Speichern" style="float:right"/>
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


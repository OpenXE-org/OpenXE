<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>    
    <div id="tabs-1">
        [MESSAGE]
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|<!--Legend for this form area goes here>-->kontorahmen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Sachkonto|}:</td><td><input type="text" name="sachkonto" id="sachkonto" value="[SACHKONTO]" size="20"></td></tr>
                                    <tr><td>{|Beschriftung|}:</td><td><input type="text" name="beschriftung" id="beschriftung" value="[BESCHRIFTUNG]" size="20"></td></tr>
                                    <tr><td>{|Bemerkung|}:</td><td><input type="text" name="bemerkung" id="bemerkung" value="[BEMERKUNG]" size="20"></td></tr>
                                    <tr><td>{|Ausblenden|}:</td><td><input type="checkbox" name="ausblenden" id="ausblenden" value="1" [AUSBLENDEN] size="20"></td></tr>
                                    <tr><td>{|Art|}:</td><td><select name="art">[ART]</select></td></tr>
                                    <tr><td>{|Projekt|}:</td><td><input type="text" name="projekt" id="projekt" value="[PROJEKT]" size="20"></td></tr>
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


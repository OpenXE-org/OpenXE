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
                                <legend>{|&Uuml;bersetzung|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td>{|Label|}:</td><td><input type="text" name="label" id="label" value="[LABEL]" size="20"></td></tr>
                                    <!---
                                    <tr>
                                        <td>{|Sprache|}:</td>
                                        <td>
                                            <select name="sprache" size="0" tabindex="1" id="sprache" class="" onchange="">
                                            [SPRACHENSELECT]
                                            </select>
                                        </td>
                                    --!>
                                    <tr><td>{|Sprache|}:</td><td><input type="text" name="sprache" id="sprache" value="[SPRACHE]" size="20"></td></tr>
                                    </tr>
                                    <tr><td>{|&Uuml;bersetzung|}:</td><td><textarea name="beschriftung" id="beschriftung" rows="6" style="width:100%;">[BESCHRIFTUNG]</textarea></td></tr>
                                    <tr><td>{|Original|}:</td><td><textarea  name="original" id="original" rows="6" style="width:100%;">[ORIGINAL]</textarea></td></tr>


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
                                    <tr><td>{|Label|}:</td><td><input type="text" name="label" id="label" value="[LABEL]" size="20"></td></tr>
<tr><td>{|Beschriftung|}:</td><td><input type="text" name="beschriftung" id="beschriftung" value="[BESCHRIFTUNG]" size="20"></td></tr>
<tr><td>{|Sprache|}:</td><td><input type="text" name="sprache" id="sprache" value="[SPRACHE]" size="20"></td></tr>
<tr><td>{|Original|}:</td><td><input type="text" name="original" id="original" value="[ORIGINAL]" size="20"></td></tr>

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


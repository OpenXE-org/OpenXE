<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT1]</a></li>           
    </ul>
    <div id="tabs-1">
        [MESSAGE]             
        <form action="" method="post">   
            [FORMHANDLEREVENT]
            <div class="row" [ARTIKEL_HIDDEN]>
	        	<div class="row-height">
	        		<div class="col-xs-14 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Lagermenge|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[ANZLAGER]" size="40" disabled>
                                        </td>
                                    </tr>                                   
                                    <tr>
                                        <td>
                                            {|Seriennummern eingelagert|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[ANZVORHANDEN]" size="40" disabled>
                                        </td>
                                    </tr>                                   
                                    <tr>
                                        <td>
                                            {|Seriennummern fehlen|}:
                                        </td>
                                        <td>
                                            <input type="text" value="[ANZFEHLT]" size="40" disabled>
                                        </td>
                                    </tr>                                                                                           
                                </table>
                            </fieldset>            
                        </div>
               		</div>                
                    <div class="col-xs-14 col-md-2 col-md-height">
            			<div class="inside inside-full-height">
            				<fieldset>
                                <legend>{|<!--Legend for this form area goes here>-->Aktionen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            <button onclick="window.location.href='index.php?module=seriennummern&action=enter&artikel=[ARTIKEL_ID]';" form="" class="ui-button-icon" style="width:100%;">Hinzuf&uuml;gen</button>
                                        </td>
                                    </tr>                                                
                                </table>
                            </fieldset>            
                        </div>                   	
                	</div>
               	</div>	
            </div>
            <div class="filter-box filter-usersave">
                <div class="filter-block filter-inline">
                    <div class="filter-title">{|Filter|}
                    </div>
                    <ul class="filter-list">
                        <li class="filter-item">
                            <label for="verfuegbar" class="switch">
                                <input type="checkbox" id="verfuegbar" />
                                <span class="slider round">
                                </span>
                            </label>
                            <label for="verfuegbar">
                                {|Nur eingelagerte|}
                            </label>
                        </li>
                        <li class="filter-item">
                            <label for="ausgelagert" class="switch">
                                <input type="checkbox" id="ausgelagert" />
                                <span class="slider round">
                                </span>
                            </label>
                            <label for="ausgelagert">
                                {|Nur ausgelagerte|}
                            </label>
                        </li>
                        <li class="filter-item">
                            <label for="versendet" class="switch">
                                <input type="checkbox" id="versendet" />
                                <span class="slider round">
                                </span>
                            </label>
                            <label for="versendet">
                                {|Nur versendete|}
                            </label>
                        </li>
                    </ul>                        
                </div>    
            </div>                       
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-14 col-md-14 col-md-height">
	        			<div class="inside inside-full-height">
    				        [TAB1]
                            [TAB1NEXT]
                        </div>
               		</div>                   
               	</div>	
            </div>
        </form>                
    </div>
</div>

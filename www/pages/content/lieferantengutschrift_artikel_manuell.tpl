[POSITIONENMESSAGE]
<form method="post" action="#tabs-2">   
    <div class="row" [POSITIONHINZUFUEGENHIDDEN]>        
        <div class="row-height">
            <div class="col-xs-14 col-md-12 col-md-height">                
                <div class="inside inside-full-height">
                    <fieldset>
                        <legend style="float:left">Artikel hinzuf&uuml;gen:</legend>
                        <div class="filter-box filter-usersave" style="float:right;">
                            <div class="filter-block filter-inline">
                                <div class="filter-title">{|Filter|}</div>
                                <ul class="filter-list">
                                    <li class="filter-item">
                                        <label for="passende" class="switch">
                                        <input type="checkbox" id="passende">
                                        <span class="slider round"></span>
                                      </label>
                                        <label for="passende">{|Nur passende (Bestellung/Rechnungsnummer)|}</label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        [ARTIKELMANUELL]
                    </fieldset>
                </div>
            </div>
            <div class="col-xs-14 col-md-2 col-md-height">
                <div class="inside inside-full-height">
                    <fieldset>
                        <table width="100%" border="0" class="mkTableFormular">
                            <legend>{|Aktionen|}</legend>
                            <tr [HINZUFUEGENHIDDEN]>
								<td>
                                    {|Multifilter|}:&nbsp;<img src="./themes/new/images/tooltip_grau.png" border="0" style="position: relative; left: 1px; top: 3px; z-index: 8;" class="wawitooltipicon" title="Auswahl mehrerer Artikel &uuml;ber Name oder Nummer">
                                </td>
                            </tr>
                            <tr>
                                <td><input type="checkbox" name="bruttoeingabe" value="1" />Bruttopreise eingeben</td>
                            </tr>                                  
                            <tr [HINZUFUEGENHIDDEN]>
								<td>
                                    <input type="text" name="multifilter" id="multifilter" value="[MULTIFILTER]" size="20" style="width:98%;" form="">
								</td>
							</tr>   
                            <tr>
                                <td><button [SAVEDISABLED] name="submit" value="artikel_manuell_hinzufuegen" class="ui-button-icon" style="width:100%;">Hinzuf&uuml;gen</button></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</form>


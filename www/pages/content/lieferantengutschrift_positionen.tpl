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
                        [PAKETDISTRIBUTION]
                    </fieldset>
                </div>
            </div>
            <div class="col-xs-14 col-md-2 col-md-height">
                <div class="inside inside-full-height">
                    <fieldset>
                        <table width="100%" border="0" class="mkTableFormular">
                            <legend>{|Aktionen|}</legend>
                            <tr>
                                <td><input type="checkbox" id="auswahlallewareneingaenge" onchange="allewareneingaengeauswaehlen();" />{|alle markieren|}</td>
                            </tr>                          
                            <tr>
                                <td><input type="checkbox" name="bruttoeingabe" value="1" />Bruttopreise eingeben</td>
                            </tr>                                  
                            <tr>
                                <td><button [SAVEDISABLED] name="submit" value="positionen_hinzufuegen" class="ui-button-icon" style="width:100%;">Hinzuf&uuml;gen</button></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</form>
<form method="post" action="#tabs-2">
    <div class="row">
        <div class="row-height">
            <div class="col-xs-14 col-md-12 col-md-height">
                <div class="inside inside-full-height">
                    [POSITIONEN]                 
                </div>
            </div>
            <div class="col-xs-14 col-md-2 col-md-height">
                <div class="inside inside-full-height">
                    <fieldset>
                        <table width="100%" border="0" class="mkTableFormular">
                            <legend>{|Aktionen|}</legend>
                            <tr [SACHKONTOCHANGEHIDDEN]>
                                <td><input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();" />{|alle markieren|}</td>
                            </tr>                          
                            <tr [POSITIONHINZUFUEGENHIDDEN]>
                                <td><button [SAVEDISABLED] name="submit" value="positionen_entfernen" class="ui-button-icon" style="width:100%;">Entfernen</button></td>
                            </tr>
                            <tr [SACHKONTOCHANGEHIDDEN]>
                                <td><input type="text" name="positionen_sachkonto" id="positionen_sachkonto" value="" size="20"></td>
                            </tr>
                            <tr [SACHKONTOCHANGEHIDDEN]>
                                <td><button name="submit" value="positionen_kontorahmen_setzen" class="ui-button-icon" style="width:100%;">Sachkonto setzen</button></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    function allewareneingaengeauswaehlen()
    {
      var wert = $('#auswahlallewareneingaenge').prop('checked');
      $('#verbindlichkeit_positionen').find(':checkbox').prop('checked',wert);
    }
    function alleauswaehlen()
    {
      var wert = $('#auswahlalle').prop('checked');
      $('#lieferantengutschrift_positionen').find(':checkbox').prop('checked',wert);
    }
</script>

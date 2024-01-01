[POSITIONENMESSAGE]
<form method="post" action="#tabs-2">   
    <div class="row" [POSITIONHINZUFUEGENHIDDEN]>        
        <div class="row-height">
            <div class="col-xs-14 col-md-12 col-md-height">                
                <div class="inside inside-full-height">
                    <fieldset>
                        <legend style="float:left">Offene Artikel aus Wareneing&auml;ngen:</legend>
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
                            <tr>
                                <td><input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();" />&nbsp;{|alle markieren|}</td>
                            </tr>
                            <tr>
                                <td>Sachkonto:&nbsp;<input type="text" id="positionen_sachkonto" name="positionen_sachkonto" value=""></td>
                            </tr>
                            <tr>
                                <td><button [SAVEDISABLED] name="submit" value="positionen_sachkonto_speichern" class="ui-button-icon" style="width:100%;">Anpassen</button></td>
                            </tr>
                            <tr>
                                <td>Steuersatz:&nbsp;<input type="text" id="positionen_steuersatz" name="positionen_steuersatz" value=""></td>
                            </tr>
                            <tr>
                                <td><button [SAVEDISABLED] name="submit" value="positionen_steuersatz_speichern" class="ui-button-icon" style="width:100%;">Anpassen</button></td>
                            </tr>
                        </table>
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
    function alleauswaehlen()
    {
      var wert = $('#auswahlalle').prop('checked');
      $('#verbindlichkeit_positionen').find(':checkbox').prop('checked',wert);
    }
</script>

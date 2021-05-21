<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <div class="row" id="snapaddy-module">
            <div class="row-height">
                <div class="col-xs-12 col-md-10 col-md-height">
                    <div class="inside-full-height">
                        <form id="snapADDYList" method="post" action="#">
                            [TAB1]
                            <fieldset>
                                <legend>{|Stapelverarbeitung|}</legend>
                                <input type="checkbox" id="bulkSelect"/>
                                <label for="bulkSelect">{|alle markieren|}</label>
                                <select id="sel_action" name="sel_action">
                                    <option value="">{|bitte w&auml;hlen|} ...</option>
                                    <option value="customer">{|als Kunde anlegen|}</option>
                                    <option value="vendor">{|als Lieferant anlegen|}</option>
                                    <option value="lead">{|als Lead anlegen|}</option>
                                </select>
                                <input type="submit" class="btnBlue" name="ausfuehren" value="ausf&uuml;hren" />
                            </fieldset>
                        </form>
                    </div>
                </div>
                <div class="col-xs-12 col-md-2 col-md-height">
                    <fieldset>
                        <legend>{|Aktionen|}</legend>
                        <input type="button"
                               id="snapImport"
                               class="btnGreenNew"
                               name="neuereintrag"
                               value="&#10010; Importieren">
                    </fieldset>
                </div>
            </div>
        </div>
        [TAB1NEXT]
    </div>
</div>

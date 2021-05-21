<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
<!-- ende gehort zu tabview -->

    <!-- erstes tab -->
    <div id="tabs-1">
        [WIKISUBMENU]
    [MESSAGE]
        <div class="row">
            <div class="row-height">
                <div class="col-xs-12 col-sm-10 col-sm-height">
                    <div class="inside-full-height">
                        <fieldset class="white">
                        [TAB1]
                        </fieldset>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-2 col-sm-height">
                    <div class="inside-full-height">
                        <fieldset class="white">
                            <legend>{|Aktion|}</legend>
                            <input type="button" value="{|+ Frage|}" id="newfaq" class="btnGreenNew" />
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- tab view schließen -->
</div>

<div id="popupfaq">
    <fieldset><legend>{|Frage anlegen / &auml;ndern|}</legend>
        <table>
            <tr>
                <td width="150">
                    <label for="popupquestion">{|Frage|}:</label>
                </td>
                <td>
                    <input type="text" size="80" id="popupquestion" />
                    <input type="hidden" id="popupid" value="[ID]" />
                    <input type="hidden" id="popupwikifaqid" value="" />
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <label for="popupanswer">{|Antwort|}:</label>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <textarea id="popupanswer"></textarea>
                </td>
            </tr>
        </table>
    </fieldset>
</div>


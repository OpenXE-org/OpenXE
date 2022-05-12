<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
    <div id="tabs-1">
        [WIKISUBMENU]
            <form method="post" id="frmwikiedit">
                <div class="row">
                    <div class="row-height">
                        <div class="col-xs-12 col-sm-10 col-sm-height">
                            <div class="inside-full-height">
                                [MESSAGE]
                                <div id="wikicontent" data-site="[WIKISITE]">
                                    [TAB1]
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-2 col-sm-height">
                            <div class="inside inside-full-height">
                                <fieldset><legend>{|Aktion|}</legend>
                                    <table id="tabwikiedit">
                                        <tr class="labeltr">
                                            <td><label for="tags">{|Labels|}:</label></td>
                                            <td></td>
                                            <td><a href="#" class="label-manager" data-label-column-number="2" data-label-reference-id="[ID]" data-label-reference-table="wiki"><img src="./themes/new/images/label.svg"></a></td>
                                        </tr>
                                        <tr class="hidden">
                                            <td>{|Workspace|}:</td><td data-workspace="[WORKSPACEID]" colspan="2">[WORKSPACE]</td>
                                        </tr>
                                        <tr><td>{|Sprache|}:</td><td data-language="[LANUAGEISO]" colspan="2">[LANGUAGE]</td></tr>
                                        <tr>
                                            <td colspan="3">
                                                <input class="btnGreenNew" type="button" id="wikieditsubmit" value="Speichern" name="submit">
                                            </td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

</div>

<!-- tab view schließen -->
</div>
<div id="changepopup">
    <fieldset>
        <legend>{|Was haben Sie ge&auml;ndert?|}</legend>
        <table>
            <tr>
                <td><label for="comment">{|Grund|}:</label></td>
                <td><input type="text" id="comment" size="100" /></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="checkbox" value="1" id="notify" />
                    <label for="notify">{|Beobachter benachrichtigen|}</label>
                </td>
            </tr>
        </table>
    </fieldset>
</div>

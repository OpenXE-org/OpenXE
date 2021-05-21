<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">{|Vorlagen|}</a></li>
        <li><a href="#tabs-2">{|Warteschlange|}</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
    <div id="tabs-1">
        [MESSAGE]
        <div class="row">
            <div class="row-height">
                <div class="col-xs-12 col-sm-10 col-sm-height">
                    <div class="inside">
                        [TAB1]
                        [TAB1NEXT]
                    </div>
                </div>
                <div class="col-xs-12 col-sm-2 col-sm-height">
                    <div class="inside inside-full-height">
                        <fieldset><legend>{|Aktionen|}</legend>
                            <a class="neubuttonlink" href="index.php?module=importvorlage&amp;action=create"><input type="button" value="&#10010; {|Neuer Eintrag|}" class="btnGreenNew"></a>
                            <a class="neubuttonlink" id="jsonUploadDialog" href="#"><input type="button" value="&#10010; {|Neuer Eintrag mit Vorlagendatei|}" class="btnGreenNew"></a>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="jsonEditUploadDialog" style="display:none;" title="Vorlagendatei hochladen">
        <form action="" method="post" enctype="multipart/form-data">
            <table class="mkTableFormular" width="100%">
                <tr>
                    <td><input type="file" name="jsonfile" id="jsonfile"/></td>
                </tr>
                <tr>
                    <td><input type="submit" value="{|Speichern|}" name="jsonupload" class="btnGreen pull-right" /></td>
                </tr>
            </table>
        </form>
    </div>
<!-- erstes tab -->
<div id="tabs-2">
[MESSAGE]
    <fieldset>
        <legend>{|Filter|}</legend>
        <div class="filter-box">
            <div class="filter-block filter-inline">
                <ul class="filter-list">
                    <li class="filter-item">
                        <label for="importvorlage-filter-complete" class="switch">
                            <input type="checkbox" id="importvorlage-filter-complete">
                            <span class="slider round"></span>
                        </label>
                        <label for="importvorlage-filter-complete">{|abgeschlossen|}</label>
                    </li>
                    <li class="filter-item">
                        <label for="importvorlage-filter-cancelled" class="switch">
                            <input type="checkbox" id="importvorlage-filter-cancelled">
                            <span class="slider round"></span>
                        </label>
                        <label for="importvorlage-filter-cancelled">{|abgebrochen|}</label>
                    </li>
                </ul>
            </div>
        </div>
    </fieldset>
    [TAB2]
    <label for="selectall">{|alle auswälen|}</label>
    <input type="checkbox" id="selectall" />
    <label for="selaction">{|Aktion|}</label>
    <select id="selaction">
        <option value="activate">{|aktivieren|}</option>
        <option value="delete">{|l&ouml;schen|}</option>
    </select>
    <input type="button" value="{|ausf&uuml;hren|}" id="send" />
    [TAB2NEXT]
</div>

<!-- tab view schließen -->
</div>


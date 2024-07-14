<!--
SPDX-FileCopyrightText: 2023 Andreas Palm

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->

<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Gruppen/Optionen</a></li>
        <li><a href="#tabs-2">Variantenzuordnung</a></li>
    </ul>
    <div id="tabs-1">
        <div class="row">
            <div class="col-xs-12 col-md-10 col-md-height">
                <div class="inside-white inside-full-height">
                    [MESSAGE]
                    [TAB1]
                </div>
            </div>
            <div class="col-xs-12 col-md-2 col-md-height">
                <div class="inside inside-full-height">
                    <fieldset>
                        <legend>{|Aktionen|}</legend>
                        <input type="button" class="btnGreenNew vueAction" data-action="addGlobalToArticle" data-article-id="[ID]" value="&#10010; Optionen übernehmen">
                        <input type="button" class="btnGreenNew vueAction" data-action="[ADDEDITFUNCTION]" data-group-id="[SID]" data-article-id="[ID]" value="&#10010; Neuer Eintrag">
                        <input type="button" class="btnGreenNew" name="matrixprodukt_module"
                               value="&#10140; Vordefinierte Gruppen/Optionen" onclick="window.location.href='index.php?module=matrixprodukt&action=list';">
                    </fieldset>
                </div>
            </div>
        </div>
    </div>
    <div id="tabs-2">
        <div class="row">
            <div class="row-height">
                <div class="col-xs-12 col-md-10 col-md-height">
                    <div class="inside-white inside-full-height">
                        [MESSAGE2]
                        [TAB2]
                    </div>
                </div>
                <div class="col-xs-12 col-md-2 col-md-height" [TAB2HIDEACTIONS]>
                    <div class="inside inside-full-height">
                        <fieldset>
                            <legend>{|Aktionen|}</legend>
                            <input type="button" class="btnGreenNew vueAction" data-action="variantEdit" data-article-id="[ID]" value="&#10010; Neue Variante">
                            <input type="button" class="btnGreenNew vueAction" data-action="createMissing" data-article-id="[ID]" value="&#10010; Erzeuge fehlende Varianten">
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->

<div id="vueapp"></div>
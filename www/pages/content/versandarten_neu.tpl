<!--
SPDX-FileCopyrightText: 2022 Andreas Palm
SPDX-FileCopyrightText: 2019 Xentral (c) Xentral ERP Software GmbH, Fuggerstrasse 11, D-86150 Augsburg, Germany

SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->
<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
    <!-- ende gehort zu tabview -->

    <!-- erstes tab -->
    <div id="tabs-1">
        [MESSAGE]
        [TAB1]
        <fieldset class="moduleList">
            <legend>{|Auswahl|}</legend>
            <div id="searchdiv">
                <label for="suche">{|Suche|}:</label> <input type="text" id="createSearchInput" />
            </div>
            [MODULEINSTALLIERT]
        </fieldset>
        [TAB1NEXT]
        <form id="neu" action="index.php?module=versandarten&action=neusonstige" method="post">
            <button name="submit" value="neusonstige" class="ui-button-icon">Versandart ohne Modul anlegen</button>
        </form>
    </div>

<!-- tab view schließen -->
</div>

<click-by-click-assistant
        id="shipment-create"
        v-if="showAssistant"
        @close="showAssistant = false"
        :pages="pages"
        :allowClose="allowClose"
        :pagination="pagination">
</click-by-click-assistant>

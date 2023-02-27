<!--
SPDX-FileCopyrightText: 2022 Andreas Palm
SPDX-License-Identifier: LicenseRef-EGPL-3.1
-->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Rechnungen</a></li>
        <li><a href="#tabs-2">Aufträge</a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE_INVOICES]
        <form method="post" action="#">
            [TAB_INVOICES]
            <fieldset>
                <legend>{|Stapelverarbeitung|}</legend>
                <input type="checkbox" id="auswahlalle" onchange="alleauswaehlen('#rechnungslauf_invoices');" />&nbsp;{|alle markieren|}
                <input type="submit" class="btnBlue" name="createInvoices" value="{|ausf&uuml;hren|}" />
            </fieldset>
        </form>
    </div>
    <div id="tabs-2">
        [MESSAGE_ORDERS]
        <form method="post" action="#">
            [TAB_ORDERS]
            <fieldset>
                <legend>{|Stapelverarbeitung|}</legend>
                <input type="checkbox" id="auswahlalle" onchange="alleauswaehlen('#rechnungslauf_orders');" />&nbsp;{|alle markieren|}
                <input type="submit" class="btnBlue" name="createOrders" value="{|ausf&uuml;hren|}" />
            </fieldset>
        </form>
    </div>
</div>

<script>
    function alleauswaehlen(target)
    {
        var wert = $('#auswahlalle').prop('checked');
        $(target).find(':checkbox').prop('checked',wert);
    }
</script>

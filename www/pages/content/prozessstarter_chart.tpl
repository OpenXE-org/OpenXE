<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
    </ul>
    <div id="tabs-1">
        <fieldset><legend>{|Filter|}</legend>
            <form method="POST" onsubmit="return checkFormTime(this);">
            <table>
                <tr>
                    <td>
                        <label for="prozessstarter_chart_from">{|von|}:</label>
                    </td>
                    <td>
                        <input type="text" id="prozessstarter_chart_from" name="prozessstarter_chart_from" value="[PROZESSSTARTER_CHART_FROM]" />
                    </td>
                    <td>
                        <input type="text" id="prozessstarter_chart_fromtime" name="prozessstarter_chart_fromtime" value="[PROZESSSTARTER_CHART_FROMTIME]" />
                    </td>
                    <td>
                        <label for="prozessstarter_chart_to">{|bis|}:</label>
                    </td>
                    <td>
                        <input type="text" id="prozessstarter_chart_to" name="prozessstarter_chart_to" value="[PROZESSSTARTER_CHART_TO]" />
                    </td>
                    <td>
                        <input type="text" id="prozessstarter_chart_totime" name="prozessstarter_chart_totime" value="[PROZESSSTARTER_CHART_TOTIME]" />
                    </td>
                    <td>
                        <label for="prozessstarter_chart_limit">{|nur 4 aktivste Prozessstarter|}:</label>
                    </td>
                    <td>
                        <input type="checkbox" id="prozessstarter_chart_limit" name="prozessstarter_chart_limit" value="1" [PROZESSSTARTER_CHART_LIMIT] />
                    </td>
                    <td>
                        <label for="prozessstarter_chart_cronjob">{|Prozesstarter|}:</label>
                    </td>
                    <td>
                        <input type="text" id="prozessstarter_chart_cronjob" name="prozessstarter_chart_cronjob" value="[PROZESSSTARTER_CHART_CRONJOB]" />
                    </td>
                    <td>
                        <input type="submit" name="load" value="{|Filtern|}" />
                    </td>
                    <td>
                        <input type="submit" name="last12h" value="{|letzte 12 Stunden|}" />
                    </td>
                    <td>
                        <input type="submit" name="last6h" value="{|letzte 6 Stunden|}" />
                    </td>
                </tr>
            </table>
            </form>
        </fieldset>
        [MESSAGE]
        [TAB1]
        [TAB1NEXT]
    </div>
</div>

<script type="text/javascript">

    function checkFormTime(form)
    {
        datumsformat = /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/;

        if(form.prozessstarter_chart_totime.value != '' && !form.prozessstarter_chart_fromtime.value.match(datumsformat)) {
             alert("Von Zeit bitte im Format hh:ss");
            form.prozessstarter_chart_fromtime.focus();
            return false;
        }

        if(form.prozessstarter_chart_totime.value != '' && !form.prozessstarter_chart_totime.value.match(datumsformat)) {
            alert("Bis Zeit bitte im Format hh:ss");
            form.prozessstarter_chart_totime.focus();
            return false;
        }

        return true;
    }

</script>

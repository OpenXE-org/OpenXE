<div class="inside inside-full-height">
    <fieldset>
        <h2>{|Drittanbieter|}</h2>
        <p>
            Um unsere Dienstleistungen anbieten und kontinuierlich verbessern zu können,
            setzen wir auf die in unsere Datenschutzerklärung aufgeführten Drittanbieter.
            Für einige dieser Drittanbieter benötigen wir Ihre Einwilligung bzw. steht Ihnen ein Widerspruchsrecht zu.
            Sie können hier über die Datenverarbeitung der folgenden Drittanbieter bestimmen.
        </p>
        <p class="dataprotection-hint">Sie können hier über die Datenverarbeitung der folgenden Drittanbieter bestimmen:</p>
        <form method="post">
            <table>

                <tr>
                    <td>{|Anbieter|}</td>
                    <td>{|Zweck|}</td>
                    <td>{|Datenverarbeitung|}</td>
                </tr>
                <tr class="border-bottom">
                    <td>
                        <label for="dataprotection_googleanalytics">Google Analytics</label>
                    </td>
                    <td>
                        {|Verbesserung Ihrer Nutzererfahrung durch anonymes Feedback und Nutzerauswertung|}
                    </td>
                    <td>
                        <input type="checkbox"
                               id="dataprotection_googleanalytics"
                               name="dataprotection_googleanalytics"
                               [DATAPROTECTION_GOOGLEANALYTICS]
                               value="1"/>
                    </td>
                </tr>
                <tr class="border-bottom">
                    <td>
                        <label for="dataprotection_improvement">Xentral</label>
                    </td>
                    <td>
                        {|Teilnahme am Verbesserungsprogramm|}
                    </td>
                    <td>
                        <input type="checkbox"
                               id="dataprotection_improvement"
                               name="dataprotection_improvement"
                               [DATAPROTECTION_IMPROVEMENT]
                               value="1"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="dataprotection_hubspot">Hubspot</label>
                    </td>
                    <td>
                        {|Auswertung Kundenverhalten in Software|}
                    </td>
                    <td>
                        <input type="checkbox"
                               id="dataprotection_hubspot"
                               name="dataprotection_hubspot"
                               [DATAPROTECTION_HUBSPOT]
                               [DISABLED_HUBSPOT]
                               value="1"/>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="dataprotection_zendesk">ZenDesk</label>
                    </td>
                    <td>
                        {|Help-Desk Software|}
                    </td>
                    <td>
                        <input type="checkbox"
                               id="dataprotection_zendesk"
                               name="dataprotection_zendesk"
                               [DATAPROTECTION_ZENDESK]
                               value="1"/>
                    </td>
                </tr>
            </table>
            <input type="submit" name="save" value="{|Speichern|}"/>
        </form>
    </fieldset>
</div>



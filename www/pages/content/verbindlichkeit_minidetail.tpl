[FORMHANDLEREVENT]
[MESSAGE]
<style>
    .auftraginfo_cell {
    color: #636363;border: 1px solid #ccc;padding: 5px;
    }
    .auftrag_cell {
    color: #636363;border: 1px solid #fff;padding: 0px; margin:0px;
    }
</style>
<div style="float:left; width:49%; padding-right:1%;">   
    <table width="100%" border="0">
        <tr valign="top">
            <td width="150">Lieferant:</td>
            <td colspan="3">[ADRESSEAUTOSTART][ADRESSE][MSGADRESSE][ADRESSEAUTOEND]</td>
        </tr>
        <tr>
            <td>Rechnungs-Nr.:</td>
            <td>[RECHNUNG][MSGRECHNUNG]</td>
        </tr>
        <tr>
            <td>Rechnungsdatum:</td>
            <td width="250">[RECHNUNGSDATUM][MSGRECHNUNGSDATUM]</td>
        <tr>
        </tr>
        </tr>
            <td width="200">Zahlbar bis:</td>
            <td>[ZAHLBARBIS][MSGZAHLBARBIS][DATUM_ZAHLBARBIS]</td>
        </tr>
            <td>Betrag/Total (Brutto):</td>
            <td>[BETRAG][MSGBETRAG]&nbsp;[WAEHRUNG][MSGWAEHRUNG]</td>
        <tr>
            <td>Skonto in %:</td>
            <td>[SKONTO][MSGSKONTO]</td>
        </tr>
        <tr>
            <td>Skonto bis:</td>
            <td>[SKONTOBIS][MSGSKONTOBIS][DATUM_SKONTOBIS]</td>
        </tr>
        <tr>
            <td>Projekt:</td>
            <td>[PROJEKT][MSGKOSTENSTELLE]</td>
            <td>&nbsp;</td>                            
        </tr>
        <tr>
            <td>Kostenstelle:</td>
            <td>[KOSTENSTELLE][MSGKOSTENSTELLE]</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>Interne Bemerkung:</td>
            <td colspan="4">[INTERNEBEMERKUNG]</td>
        </tr>
    </table>
</div>
<div style="float:left; width:50%">
    <div style="background-color:white">
        <h2 class="greyh2">Artikel</h2>
        <div style="padding:10px">
            [ARTIKEL]
        </div>
    </div>
    <div style="background-color:white">
        <h2 class="greyh2">Buchungen</h2>
        <div style="padding:10px">
            [ZAHLUNGEN]
        </div>
    </div>
    <div style="background-color:white">
        <h2 class="greyh2">Protokoll</h2>
        <div style="padding:10px;">
            [PROTOKOLL]
        </div>
    </div>
</div>


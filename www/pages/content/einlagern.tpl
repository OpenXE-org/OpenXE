<fieldset>
    <form action="" method="post" name="eprooform" id="eprooform">
        <input type="hidden" name="suggestedbatchbefore" value="[SUGGESTEDBATCHBEFORE]" />
        <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
            <tbody>
                <tr>
                    <td colspan="2">
                        [MESSAGELAGER]
                        <table width="80%" align="center">
                            <tr>
                                <td align="center">
                                    <table width="90%">
                                        <tr>
                                            <td width="300">
                                                <label for="woher">{|Lagerbewegung|}:</label>
                                            </td>
                                            <td>
                                                <select id="woher" name="woher">
                                                    [WOHERREADONLYSTART]
                                                    <option name="differenz" [DIFFERENZ]>{|Manuelle Lageranpassung|}</option>[WOHERREADONLYENDE] [WOHERREADONLYSTART2]
                                                    <option name="zwischenlager" [ZWISCHENLAGER]>{|Zwischenlager|}</option> [WOHERREADONLYENDE2]
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><b><label for="menge">{|Menge|}:</label></b></td>
                                            <td>
                                                <input type="text" id="menge" name="menge" value="[MENGE]" size="22" [MENGEREADONLY] style="width:200px">
                                            </td>
                                        </tr>
                                        <tr valign="top">
                                            <td><b><label for="nummer">{|Artikelnummer|}:</label></b></td>
                                            <td>[NUMMERAUTOSTART]
                                                <input type="text" name="nummer" id="nummer" value="[NUMMER]" [ARTIKELSTYLE] size="22" style="width:200px">[NUMMERAUTOEND][MSGARTIKEL]</td>
                                        </tr>
                                        [ZWISCHENLAGERINFO] [SHOWMHDSTART]
                                        <tr valign="top">
                                            <td><b style="color:red"><label for="mhd">{|MHD|}:</label></b></td>
                                            <td>
                                                <input type="text" name="mhd" id="mhd" value="[MHDVALUE]" style="width:200px">&nbsp;
                                                <br><i>({|Mindesthaltbarkeitsdatum|})</i></td>
                                        </tr>
                                        <tr valign="top">
                                            <td>
                                                <br>
                                            </td>
                                            <td align="center"></td>
                                        </tr>[SHOWMHDEND] [SHOWCHRSTART]
                                        <tr valign="top">
                                            <td><b style="color:red"><label for="charge">{|Charge|}:</label></b></td>
                                            <td>
                                                <input type="text" name="charge" value="[CHARGEVALUE]" id="charge" style="width:200px">&nbsp;
                                                <br><i>({|Chargennummer von Hersteller|})</i></td>
                                        </tr>
                                        <tr valign="top">
                                            <td>
                                                <label for="chargesnmhdbemerkung">{|Bemerkung|}:</label>
                                            </td>
                                            <td>
                                                <input type="text" name="chargesnmhdbemerkung" value="[CHARGESNMHDBEMERKUNG]" id="chargesnmhdbemerkung" style="width:200px">&nbsp;
                                                <br><i>({|Infos zur Charge|})</i></td>
                                        </tr>
                                        <tr valign="top">
                                            <td>
                                                <br>
                                            </td>
                                            <td align="center"></td>
                                        </tr>[SHOWCHREND]
                                        <tr>
                                            <td>
                                                <label for="projekt">{|Projekt|}:</label>
                                            </td>
                                            <td>[PROJEKTAUTOEND]
                                                <input type="text" id="projekt" name="projekt" value="[PROJEKT]" size="22" style="width:200px">[PROJEKTAUTOEND]</td>
                                        </tr>
                                        <tr>
                                            <td>{|Kunde|} / {|Lieferant|} / {|Mitarbeiter|}:</td>
                                            <td align="left">[ADRESSESTART]
                                                <input type="text" name="adresse" value="[ADRESSE]" style="width:200px" id="adresse" size="27">[ADRESSEEND][MSGADRESSE]</td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <label for="grundreferenz">{|Grund|}:</label>
                                            </td>
                                            <td>
                                                <input type="text" id="grundreferenz" name="grundreferenz" value="[GRUNDREFERENZ]" size="22" style="width:200px">
                                            </td>
                                        </tr>
                                        [SRNINFO]
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <tr valign="" height="" bgcolor="" align="" bordercolor="" class="klein" classname="klein">
                    <td width="" valign="" height="" bgcolor="" bordercolor="" classname="orange2" class="orange2">
                        [NEINDOCHNICHTSTART]
                        <input type="button" name="zurueck" onclick="window.history.back();" value="{|Nein, doch nicht|}" />[NEINDOCHNICHTENDE]
                    </td>
                    <td align="right">
                        <input type="submit" name="submit" value="{|Weiter|}" />
                    </td>
                </tr>
            </tbody>
        </table>
    </form>
</fieldset>
<script type="text/javascript">
    // Artikel rescan
    function regalchange() {
        regal = document.getElementById('regal').value;
        artikel = document.getElementById('nummer').value;
        if ((artikel == regal) && (artikel != '')) {
            document.getElementById('menge').value = Number(document.getElementById('menge').value) + 1;
            document.getElementById('menge').style.fontSize = "200%";
            document.getElementById('eprooform').addEventListener('submit', eprooform_submit);
        }
    };

    function eprooform_submit(event) {
        regal = document.getElementById('regal').value;
        artikel = document.getElementById('nummer').value;
        if ((artikel == regal) && (artikel != '')) {
            document.getElementById('regal').value = '';
            event.preventDefault();
        }
    };
</script>

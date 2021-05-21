<script type='text/javascript' src='./js/jquery.dateFormat-1.0.js'></script>
<script type='text/javascript' src='./plugins/fullcalendar-1.6.7/fullcalendar.min.js?v=1'></script>
<script type='text/javascript' src='./js/nocie.js'></script>

<link rel='stylesheet' type='text/css' href='./plugins/fullcalendar-1.6.7/fullcalendar.css'/>
<link rel='stylesheet' type='text/css' href='./plugins/fullcalendar-1.6.7/fullcalendar.print.css' media='print'/>

[STARTSMALLKALENDER]
<div class="row calendar-wrapper">
    <div class="row-height">
        <div class="col-xs-12 col-md-10 col-md-height calendar-content">
            <div class="inside_white inside-full-height">
                <fieldset class="white">
                    [ENDESMALLKALENDER]
                    <div id="calendar" data-autoscroll="[AUTOSCROLLTO]" data-default-color="[DEFAULTKALENDERCOLOR]"></div>
                    [STARTSMALLKALENDER]
                </fieldset>
            </div>
        </div>
        <div class="col-xs-12 col-md-2 col-md-height auswahl-content">
            <div class="inside inside-full-height">
                <fieldset id="auswahl">
                    <legend class="auswahl">{|Auswahl|}</legend>
                    <input type="checkbox" class="auswahl" value="1" name="aufgaben" id="aufgaben" [AUFGABENCHECKED]>
                    <label id="aufgaben" class="auswahl" for="aufgaben">{|Aufgaben|}</label>
                    <input type="checkbox" class="auswahl" value="1" name="termine" id="termine" [TERMINECHECKED]>
                    <label id="termine" class="auswahl" for="termine">{|Nur eigene Termine|}</label>
                    <input type="checkbox" class="auswahl" value="1" name="urlaub" id="urlaub" [URLAUBCHECKED]>
                    <label id="urlaub" class="auswahl" for="urlaub">{|Urlaub/Abwesend|}</label>
                    <input type="checkbox" class="auswahl" value="1" name="projekte" id="projekte" [PROJEKTECHECKED]>
                    <label id="projekte" class="auswahl" for="projekte">{|Teilprojekt|}</label>
                    [SERVICEAUFTRAGKALENDER]
                </fieldset>
                <fieldset id="gruppenkalender">
                    <legend class="gruppenkalender">{|Gruppenkalender|}</legend>
                    [GRUPPENKALENDERAUSWAHL]
                </fieldset>
            </div>
        </div>
    </div>
</div>
[ENDESMALLKALENDER]

<div id="TerminDialogEinladung" title="Einladung versenden">
    <input type="hidden" name="einladungeventid" id="einladungeventid" value="">
    <table width="100%" border="0">
        <tr>
            <td>{|Betreff|}:</td>
            <td><input type="text" name="einladungbetreff" id="einladungbetreff" style="width:100%"></td>
        </tr>
        <tr>
            <td>{|Text|}:</td>
            <td><textarea name="einladungtext" id="einladungtext" cols="40" rows="4"></textarea></td>
        </tr>
        <tr valign="top">
            <td>{|Empfänger|}:</td>
            <td><textarea name="einladungcc" id="einladungcc" style="width:100%" rows="3"></textarea>
            </td>
        </tr>
    </table>
</div>

<div id="TerminDialog" title="Termin erstellen / bearbeiten">
    <form id="TerminForm" action="" method="POST">
        <input type="hidden" name="noRedirect" id="noRedirect" value="0"/>
        <div class="row">
            <div class="row-height">
                <div class="col-xs-12 col-md-6 col-md-height">
                    <div class="inside inside-full-height">
                        <fieldset>
                            <legend>&nbsp;</legend>
                            <table width="100%" border="0">
                                <tr>
                                    <td></td>
                                    <td colspan="3">
                                        <div id="submitError" style="color:red;"></div>
																			  <div id="googleStatus"></div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{|Titel|}:</td>
                                    <td colspan="3"><input type="text" name="titel" id="titel" size="40" [GOOGLE_EVENT_EDIT]></td>
                                </tr>
                                <tr>
                                    <td>{|Beschreibung|}:</td>
                                    <td colspan="3"><textarea name="beschreibung" id="beschreibung" cols="40" rows="4"></textarea></td>
                                </tr>
                                <tr>
                                    <td>{|Ort|}:</td>
                                    <td colspan="3"><input type="text" name="ort" id="ort" size="40"></td>
                                </tr>
                                <tr>
                                    <td>{|Termin mit|}:</td>
                                    <td colspan="3"><input type="text" name="adresse" id="adresse" size="40">[LINKADRESSE]</td>
                                </tr>
                                <tr>
                                    <td>{|Ansprechpartner|}:</td>
                                    <td colspan="3"><input type="text" name="ansprechpartner" id="ansprechpartner" size="40">[LINKANSPRECHPARTNER]</td>
                                </tr>
                                <tr>
                                    <td>{|Verantwortlicher|}:</td>
                                    <td colspan="3"><input type="text" name="adresseintern" id="adresseintern" size="40"></td>
                                </tr>
                                <tr>
                                    <td>{|Projekt|}:</td>
                                    <td colspan="3"><input type="text" name="projekt" id="projekt" size="40"></td>
                                </tr>

                                <tr>
                                    <td colspan="4">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>{|Datum|}:</td>
                                    <td class="ganztags"><input type="text" name="datum" id="datum" size="10"></td>
                                    <td>{|Bis|}:</td>
                                    <td><input type="text" name="datum_bis" id="datum_bis" size="10"></td>
                                </tr>
                                <tr>
                                    <td>{|Ganztags|}:</td>
                                    <td><input type="checkbox" name="allday" id="allday" value="1"></td>
                                    <td class="erinnerung">{|Erinnerung|}:</td>
                                    <td><input type="checkbox" name="erinnerung" id="erinnerung" value="1"></td>
                                </tr>
                                <tr>
                                    <td>{|Von|}:</td>
                                    <td><input type="text" name="von" id="von" size="10"></td>
                                    <td>{|Bis|}:</td>
                                    <td><input type="text" name="bis" id="bis" size="10"></td>
                                </tr>
                                <tr>
                                    <td colspan="4">&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>{|Farbe|}:</td>
                                    <td><input type="text" name="color" id="color" value="[DEFAULTKALENDERCOLOR]"></td>
                                </tr>
                                <tr>
                                    <td>{|&Ouml;ffentlich|}:</td>
                                    <td><input type="checkbox" name="public" id="public" value="1"></td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                </div>
                <div class="col-xs-12 col-md-6 col-md-height">
                    <div class="inside inside-full-height">
                        <fieldset>
                            <legend>&nbsp;</legend>
                            <table width="100%" border="0">
                                <tr>
                                    <td valign="top">{|Personen|}:</td>
                                    <td colspan="3">
                                        <select name="personen[]" id="personen" style="width: 250px" multiple>
                                            [PERSONEN]
                                        </select><br>
                                    </td>
                                </tr>
                                <tr>
                                    <td valign="top">{|Gruppenkalender|}:</td>
                                    <td colspan="3">
                                        <select name="gruppenkalender[]" id="gruppenkalender" style="width: 250px"
                                                multiple>
                                            [GRUPPENKALENDER]
                                        </select><br>
                                    </td>
                                </tr>
                            </table>
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
        <input type="hidden" name="submitForm" value="1">
        <input type="hidden" name="mode" id="mode" value="">
        <input type="hidden" name="eventid" id="eventid" value="">
    </form>
</div>

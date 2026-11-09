<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Abschlagsrechnung</a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <div class="row">
            <div class="row-height">
                <div class="col-xs-12 col-md-10 col-md-height">
                    <div class="inside inside-full-height">
                        <table class="tableborder" border="0" cellpadding="3" cellspacing="0" width="100%">
                            <thead>
                                <tr class="orange1">
                                    <th align="left">Rechnungs-Nr.</th>
                                    <th align="left">Name</th>
                                    <th align="left">Datum</th>
                                    <th align="left">Bezeichnung</th>
                                    <th align="right">Netto</th>
                                    <th align="right">Brutto</th>
                                    <th align="center">Status</th>
                                    <th align="center">Men&uuml;</th>
                                </tr>
                            </thead>
                            <tbody>
                                [RECHNUNGSLISTE]
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xs-12 col-md-2 col-md-height">
                    <div class="inside inside-full-height">
                        <fieldset>
                            <legend>Aktion</legend>
                            <input type="button" id="btnTeilrechnung" class="btnGreenNew" style="width:100%; margin-bottom:10px; padding:8px;" value="Teilrechnung erzeugen" onclick="openTeilrechnungModal();" />
                            <input type="button" id="btnSchlussrechnung" class="btnGreenNew" style="width:100%; padding:8px;" value="Schlussrechnung erzeugen" onclick="createSchlussrechnung();" />
                        </fieldset>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Dialog: Teilrechnung erstellen -->
<div id="dialog_teilrechnung" title="Teilrechnung erstellen" style="display:none;">
    <p style="font-size:12px; color:#555; margin-bottom:12px;">Bitte vergeben Sie einen entsprechenden Namen.</p>
    <form id="formTeilrechnung" method="post" action="index.php?module=abschlagsrechnung&action=teilrechnung&id=[AUFTRAG_ID]">
        <table width="100%" cellpadding="4" cellspacing="0" border="0">
            <tr>
                <td colspan="2">
                    <label for="bezeichnung_text_teilrechnung" style="font-weight:bold; font-size:11px;">BEZEICHNUNG F&Uuml;R TEILZAHLUNG-RECHNUNG</label><br>
                    <input type="text" name="bezeichnung" id="bezeichnung_text_teilrechnung" value="[DEFAULT_BEZEICHNUNG]" style="width:100%; box-sizing:border-box;" />
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:10px;">
                    <label for="PosNulluebernehmen" style="font-weight:bold; font-size:11px;">POSITIONEN &Uuml;BERNEHMEN</label><br>
                    <label style="font-weight:normal; font-size:11px;">
                        <input type="checkbox" name="posnull" id="PosNulluebernehmen" value="1" /> alle Positionen mit Menge 0 aus Ursprungsrechnung &uuml;bernehmen.
                    </label>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:10px;">
                    <label for="artikel_teilzahlung" style="font-weight:bold; font-size:11px;">ARTIKEL F&Uuml;R TEILZAHLUNG</label><br>
                    <input type="text" name="artikel_teilzahlung" id="artikel_teilzahlung" placeholder="Artikel suchen..." style="width:100%; box-sizing:border-box;" />
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:10px;">
                    <label for="artikel_teilzahlung_bezeichnung" style="font-weight:bold; font-size:11px;">ARTIKELBEZEICHNUNG</label><br>
                    <input type="text" name="artikel_teilzahlung_bezeichnung" id="artikel_teilzahlung_bezeichnung" value="" style="width:100%; box-sizing:border-box;" />
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:10px;">
                    <label for="artikel_teilzahlung_beschreibung" style="font-weight:bold; font-size:11px;">ARTIKELBESCHREIBUNG</label><br>
                    <textarea name="artikel_teilzahlung_beschreibung" id="artikel_teilzahlung_beschreibung" rows="4" style="width:100%; box-sizing:border-box;"></textarea>
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:10px;">
                    <label for="artikel_teilzahlung_menge" style="font-weight:bold; font-size:11px;">MENGE</label><br>
                    <input type="text" name="artikel_teilzahlung_menge" id="artikel_teilzahlung_menge" value="1" style="width:100%; box-sizing:border-box;" />
                </td>
            </tr>
            <tr>
                <td colspan="2" style="padding-top:10px;">
                    <label for="artikel_teilzahlung_preis" style="font-weight:bold; font-size:11px;">PREIS</label><br>
                    <input type="text" name="artikel_teilzahlung_preis" id="artikel_teilzahlung_preis" value="" placeholder="0.00" style="width:100%; box-sizing:border-box;" />
                </td>
            </tr>
        </table>
    </form>
</div>

<script type="text/javascript">
$(document).ready(function() {
    $("#dialog_teilrechnung").dialog({
        modal: true,
        autoOpen: false,
        width: 520,
        resizable: false,
        buttons: [
            {
                text: "ABBRECHEN",
                click: function() {
                    $(this).dialog("close");
                }
            },
            {
                text: "ERSTELLEN",
                class: "btnGreenNew",
                click: function() {
                    $("#formTeilrechnung").submit();
                }
            }
        ]
    });

    $("#artikel_teilzahlung").autocomplete({
        source: "index.php?module=ajax&action=filter&filtername=artikelnummer",
        minLength: 1,
        select: function(event, ui) {
            var fullVal = ui.item.value;
            var artNr = fullVal.split(' ')[0];
            $("#artikel_teilzahlung").val(artNr);
            
            $.getJSON("index.php?module=abschlagsrechnung&action=data&cmd=getarticle&nummer=" + encodeURIComponent(artNr), function(res) {
                if(res && res.success) {
                    if($("#artikel_teilzahlung_bezeichnung").val() == "") {
                        $("#artikel_teilzahlung_bezeichnung").val(res.name_de);
                    }
                    if($("#artikel_teilzahlung_beschreibung").val() == "") {
                        $("#artikel_teilzahlung_beschreibung").val(res.beschreibung_de);
                    }
                    if($("#artikel_teilzahlung_preis").val() == "") {
                        $("#artikel_teilzahlung_preis").val(res.preis);
                    }
                }
            });
            return false;
        }
    });
});

function openTeilrechnungModal() {
    $("#dialog_teilrechnung").dialog("open");
}

function createSchlussrechnung() {
    if(confirm("Möchten Sie wirklich die Schlussrechnung für diesen Auftrag erzeugen? Alle vorherigen Abschlagsrechnungen werden automatisch in Abzug gebracht.")) {
        window.location.href = "index.php?module=abschlagsrechnung&action=schlussrechnung&id=[AUFTRAG_ID]";
    }
}
</script>

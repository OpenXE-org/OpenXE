<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
    <div id="tabs-1">
        [MESSAGE]
        <form id="eprooform" action="index.php?module=versandpakete&action=add&id=[ID]&lieferschein=[LIEFERSCHEIN_ID]" method="post">
            [FORMHANDLEREVENT]
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-14 col-md-7 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Hinzuf&uuml;gen von Artikeln aus <a href="index.php?module=lieferschein&action=edit&id=[LIEFERSCHEIN_ID]"><b>Lieferschein [LIEFERSCHEIN]</b></a> zu Versandpaket <b>Nr. [ID]</b>|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td>
                                            {|Artikel|}:
                                        </td>
                                        <td>
                                            <input type="text" name="artikel" id="artikel" value="" size="22" autofocus onchange="artikelscan();" style="width:200px">
                                            <p id="gescannterartikeltext">[GESCANNTERARTIKELTEXT]</p>
                                            <input hidden type="text" name="gescannterartikel" id="gescannterartikel" value="[GESCANNTERARTIKEL]">
                                        </td>
                                    </tr>
                                    [ARTIKELBILD]
                                    <tr>
                                        <td>
                                            {|Menge|}:
                                        </td>
                                        <td>
                                            <input type="number" name="menge" id="menge" value="[MENGE]" min="1" size="22" style="width:200px;[ZOOMSTYLE]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Restmenge|}:
                                        </td>
                                        <td>
                                            <p id="restmenge" style="[ZOOMSTYLE]">[GESCANNTERARTIKELRESTMENGE]</p>
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
                    <div class="col-xs-14 col-md-3 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Gewichtsinformationen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr>
                                        <td colspan="2">
                                            <h4>{|Dieses Paket|}</h4>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            {|Gewicht|}:
                                        </td>
                                        <td>
                                            <input type="text" disabled value="[DIESESPAKETGEWICHT]">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="2">
                                            <h4>{|Lieferschein [LIEFERSCHEIN]|}</h4>
                                        </td>
                                    </tr>
                                        <td>
                                            {|Bereits verpackt|}:
                                        </td>
                                        <td>
                                            <input type="text" disabled value="[PAKETEGEWICHT]">
                                        </td>
                                    </tr>
                                    </tr>
                                        <td>
                                            {|Zu verpacken|}:
                                        </td>
                                        <td>
                                            <input type="text" disabled value="[RESTGEWICHT]">
                                        </td>
                                    </tr>
                                    </tr>
                                        <td>
                                            {|Gesamt|}:
                                        </td>
                                        <td>
                                            <input type="text" disabled value="[GESAMTGEWICHT]">
                                        </td>
                                    </tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
                    <div class="col-xs-14 col-md-2 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Aktionen|}</legend>
                                <table width="100%" border="0" class="mkTableFormular">
                                    <tr><td><button name="submit" value="hinzufuegen" class="ui-button-icon" style="width:100%;">Hinzuf&uuml;gen</button></td></tr>
                                    <tr><td><button name="submit" value="lieferschein_komplett_hinzufuegen" class="ui-button-icon" style="width:100%;">Alle hinzuf&uuml;gen</button></td></tr>
                                    <tr><td><button form="back" name="submit" value="fertig" class="ui-button-icon" style="width:100%;">Fertig</button></td></tr>
                                </table>
                            </fieldset>
                        </div>
               		</div>
               	</div>	
            </div>
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Lieferscheininhalt|}</legend>
                                [LIEFERSCHEININHALT]
                            </fieldset>
                        </div>
               		</div>
               	</div>	
            </div>
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-12 col-md-12 col-md-height">
	        			<div class="inside inside-full-height">
	        				<fieldset>
                                <legend>{|Paketinhalt|}</legend>
                                [PAKETINHALT]
                            </fieldset>
                        </div>
               		</div>
               	</div>	
            </div>
            <input type="text" name="lieferschein" id="lieferschein" value="[LIEFERSCHEIN]" size="40" hidden>
        </form>
        <form action="index.php?module=versandpakete&action=edit&id=[VERSANDPAKET_ID]" id="back" method="post">
        </form>
    </div>
</div>

<script type="text/javascript">

    var gescannterartikelrestmenge = [GESCANNTERARTIKELRESTMENGE]+0;

    function artikelscan() {
        gescannterartikel = document.getElementById('gescannterartikel').value;
        artikel = document.getElementById('artikel').value;
        if ((artikel == gescannterartikel)) {
            document.getElementById('menge').value = Number(document.getElementById('menge').value) + 1;
            document.getElementById('menge').style.fontSize = "200%";
            document.getElementById('restmenge').style.fontSize = "200%";
            document.getElementById('eprooform').addEventListener('submit', eprooform_submit);
        } else if (gescannterartikel != '') {
            alert("Scanvorgang abgebrochen!");
            document.getElementById('gescannterartikel').value = '';
            document.getElementById('menge').value = '';
            document.getElementById('eprooform').addEventListener('submit', eprooform_submit);
        }
    };

    function eprooform_submit(event) {
        gescannterartikel = document.getElementById('gescannterartikel').value;
        artikel = document.getElementById('artikel').value;
        if ((artikel == gescannterartikel)) {
            if (gescannterartikelrestmenge != document.getElementById('menge').value) {
                document.getElementById('artikel').value = '';
                event.preventDefault();
            }
        } else if (artikel == '') {
            document.getElementById('artikel').value = gescannterartikel; // Final submit
        }
    };

</script>


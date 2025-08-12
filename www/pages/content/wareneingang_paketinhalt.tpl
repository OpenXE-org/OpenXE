<!-- gehort zu tabview -->
<div id="tabs">
	<ul> [BEFORETAB1]
		<li><a href="#tabs-1">[TAB1TEXT]</a></li>[AFTERTAB1] [BEFORETAB2]
		<li><a href="#tabs-2">[TAB2TEXT]</a></li>[AFTERTAB2] [BEFORETAB3]
		<li><a href="#tabs-3">[TAB3TEXT]</a></li>[AFTERTAB3] </ul>
	<!-- ende gehort zu tabview -->
	<!-- erstes tab -->
	<input type="hidden" id="paketannahme_id" value="[ID]" /> [BEFORETAB1]

	<div id="tabs-1"> [TAB1START] [MESSAGE1] [MESSAGE]
        <div class="row">            	
            <form action="" method="post" id="speichern">
                <div class="row-height">
                    <div class="col-xs-12 col-md-5 col-md-height">
                        <div class="inside inside-full-height">
	                        <fieldset>
		                        <legend>{|[LEGENDE]|}</legend>
		                        <table>
			                        <tr>
                                        <td>{|Projekt|}:</td>
				                        <td>
					                        <input type=text name="projekt" id="projekt" size="40" value="[PROJEKT]">
				                        </td>
			                        </tr>
			                        <tr>
				                        <td>{|Status|}:</td>
				                        <td>
					                        <input type=text size="40" value="[STATUS]" disabled>
				                        </td>
			                        </tr>
			                        <tr [ABGESCHLOSSENHIDDEN]>
				                        <td></td>
				                        <td><i>Abgeschlossen am [DATUM_ABGESCHLOSSEN] durch [BEARBEITER_ABGESCHLOSSEN]</i></td>
			                        </tr>				
                                </table>
	                        </fieldset>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-5 col-md-height">
                        <div class="inside inside-full-height">
	                        <fieldset>
		                        <table>
		                            <tr>
				                        <td>{|Lieferschein-Nr.|}:</td>
				                        <td>
					                        <input type=text size="40" name="lsnr" value="[LSNR]">
				                        </td>
			                        </tr>
			                        <tr>
				                        <td>{|Rechnung-Nr.|}:</td>
				                        <td>
					                        <input type=text size="40" name="renr" value="[RENR]">
				                        </td>
			                        </tr>
			                        <tr>
				                        <td>{|Bemerkung|}:</td>
				                        <td>
					                        <textarea rows="5" cols="40" name="bemerkung">[BEMERKUNG]</textarea>
				                        </td>
			                        </tr>
			                        [ISLIEFERANTSTART]
			                        [ISLIEFERANTENDE]
                                </table>
	                        </fieldset>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-2 col-md-height">
					    <div class="inside inside-full-height">
						    <fieldset> [BUTTONS] [BEFOREFRM] [AFTERFRM] [DISTRIINHALTBUTTONS] [BEFOREFRM] [AFTERFRM]
							    <button name="submit" class="ui-button-icon" style="width:100%;" value="speichern" hidden="true"></button>
							    <table width="100%" border="0" class="mkTableFormular">
								    <legend>{|Aktionen|}</legend>
								    <tr>
									    <td>
										    <button name="submit" class="ui-button-icon" style="width:100%;" value="speichern" form="speichern">{|Speichern|}</button>
									    </td>
								    </tr>
							    </table>
						    </fieldset>
					    </div>
				    </div>
                </div>
            </form>
        </div>
        <div class="row" [HINZUFUEGENHIDDEN]>
            <form action="" method="post" id="hinzufuegen">
                <div class="row-height">
                    <div class="col-xs-12 col-md-10 col-md-height">
                        <div class="inside-white inside-full-height">
                    	    [ISLIEFERANTSTART]
                            <div class="row">
                                <div class="row-height">
	                                <div class="col-xs-12 col-md-10 col-md-height">
		                                <div class="inside-white inside-full-height">
		                                    <fieldset>
                                                <legend>{|Erfassen von Artikeln aus Paket <b>Nr. [ID]</b>|}</legend>
                                                <table width="100%" border="0" class="mkTableFormular">
                                                    <tr>
                                                        <td>
                                                            {|Artikel|}:
                                                        </td>
                                                        <td>
                                                            <input type="checkbox" name="ignoreprefixpostfix" id="ignoreprefixpostfix" [IGNOREPREFIXPOSTFIXCHECKED] title="Sucht nach Nummern im Scantext">Teiltextsuche</input><br>
                                                            <input type="text" name="artikel" id="artikel" value="" size="22" autofocus onchange="artikelscan();" style="width:200px">
                                                             <img src="./themes/new/images/tooltip_grau.png" title="Artikelnummer, EAN, Herstellernummer oder Bestellnummer bei Lieferant">
                                                            <p id="gescannterartikeltext">[GESCANNTERARTIKELTEXT]</p>
                                                            <input hidden type="text" name="gescannterartikel" id="gescannterartikel" value="[GESCANNTERARTIKEL]" form="hinzufuegen">
                                                        </td>
                                                    </tr>
                                                    [ARTIKELBILD]
                                                    <tr>
                                                        <td>
                                                            {|Menge|}:
                                                        </td>
                                                        <td>
                                                            <input type="number" name="menge" id="menge" value="[MENGE]" min="1" size="22" style="width:200px;[ZOOMSTYLE]" form="hinzufuegen">
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
                                </div>
                            </div>
						    [ISLIEFERANTENDE]
                            <div class="row" [HINZUFUEGENHIDDEN]>
                                <div class="row-height">
                                    <div class="col-xs-12 col-md-10 col-md-height">
                                        <div class="inside-white inside-full-height">
                                            <div class="filter-box filter-usersave" style="float:right;">
                                                <div class="filter-block filter-inline">
                                                  <div class="filter-title">{|Filter|}</div>
                                                  <ul class="filter-list">
                                                    <li class="filter-item">
                                                      <label for="ausfuellen" class="switch">
                                                        <input type="checkbox" id="ausfuellen">
                                                        <span class="slider round"></span>
                                                      </label>
                                                      <label for="ausfuellen">{|Ausf&uuml;llen|}</label>
                                                    </li>
                                                  </ul>
                                                </div>
                                            </div>
                                            [TAB1]
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-md-2 col-md-height">
					    <div class="inside inside-full-height">
						    <fieldset> [BUTTONS] [BEFOREFRM] [AFTERFRM] [DISTRIINHALTBUTTONS] [BEFOREFRM] [AFTERFRM]
							    <table width="100%" border="0" class="mkTableFormular">
								    <legend>{|Aktionen|}</legend>
								    [ISLIEFERANTSTART]
							        <tr [HINZUFUEGENHIDDEN]>
								        <td>
                                            <input type="checkbox" name="etikettendruckenhinzufuegen" id="etikettendruckenhinzufuegen" [ETIKETTENDRUCKENHINZUFUEGENCHECKED] form="hinzufuegen">Etiketten drucken</input>
                                            <p>[ETIKETTENEINSTELLUNGEN]<p>
								        </td>
							        </tr>
								    <tr [HINZUFUEGENHIDDEN]>
									    <td>
										    <button name="submit" class="ui-button-icon" style="width:100%;" value="hinzufuegen" form="hinzufuegen">{|Erfassen|}</button>
									    </td>
								    </tr>
								    [ISLIEFERANTENDE]
								    [ISNOTLIEFERANTSTART]
                                    <tr [HINZUFUEGENHIDDEN]>
									    <td>
                                            {|Multifilter|}:
                                        </td>
                                    </tr>
                                    <tr [HINZUFUEGENHIDDEN]>
									    <td>
                                            <input type="text" name="multifilter" id="multifilter" value="[MULTIFILTER]" size="20" style="width:98%;" form="">
									    </td>
								    </tr>
                                    <tr [HINZUFUEGENHIDDEN]>
									    <td>
										    <button name="submit" class="ui-button-icon" style="width:100%;" value="manuell_hinzufuegen">{|Hinzuf&uuml;gen|}</button>
									    </td>
								    </tr>
								    [ISNOTLIEFERANTENDE]
							    </table>
						    </fieldset>
					    </div>
				    </div>
                </div>
            </div>
		    <div class="row">
			    <div class="row-height">
				    <div class="col-xs-12 col-md-10 col-md-height">
					    <div class="inside-white inside-full-height"> [TAB1_SECOND] </div>
				    </div>
				    <div class="col-xs-12 col-md-2 col-md-height">
					    <div class="inside inside-full-height">
						    <fieldset>
							    <button name="submit" class="ui-button-icon" style="width:100%;" value="speichern" hidden="true"></button>
							    <table width="100%" border="0" class="mkTableFormular">
								    <legend>{|Aktionen|}</legend>
                                    <tr [ETIKETTENDRUCKENHIDDEN]>
									    <td>
										    <button name="submit" class="ui-button-icon" style="width:100%;" value="etikettendrucken" title="Etiketten gem&auml;&szlig; Projekteinstellungen drucken [ETIKETTENEINSTELLUNGEN]">{|Alle Etiketten drucken|}</button>
                                        </td>
								    </tr>
								    <tr [BUCHENHIDDEN]>
									    <td>
										    <button name="submit" class="ui-button-icon" style="width:100%;" value="vorlaeufige_buchen">{|Buchen|}</button>
									    </td>
								    </tr>
                                    <tr [BUCHENHIDDEN]>
									    <td>
                                            {|Ziellager|}:&nbsp;<img src="./themes/new/images/tooltip_grau.png" border="0" style="position: relative; left: 1px; top: 3px; z-index: 8;" class="wawitooltipicon" title="Wenn nicht angegeben, wird das Standardlager des Artikels bebucht.">
                                        </td>
                                    </tr>
                                    <tr [BUCHENHIDDEN]>
									    <td>
										    <input type=text name="ziellager" id="ziellager" value="[LAGER]" placeholder="Standardlager" class="placeholder_warning" style="width:98%;">
                                        </td>
								    </tr>
								    <tr [ABSCHLIESSENHIDDEN]>
									    <td>
										    <button name="submit" class="ui-button-icon" style="width:100%;" value="abschliessen">{|Abschlie&szlig;en|}</button>
									    </td>
								    </tr>
								    <tr [ABGESCHLOSSENHIDDEN]>
									    <td>
								        	<button name="submit" class="ui-button-icon" style="width:100%;" value="oeffnen" form="oeffnen">{|&Ouml;ffnen|}</button>
									    </td>
								    </tr>
							    </table>
						    </fieldset>
					    </div>
				    </div>
			    </div>			
            </form>
		</div>
        [TAB1ENDE]
    </div> [AFTERTAB1] [BEFORETAB2]
    <div id="tabs-2"> [TAB2START] [MESSAGE2]
        <form action="" method="post">
	        <div class="row">
		        <div class="row-height">
			        <div class="col-xs-12 col-md-10 col-md-height">
				        <div class="inside-white inside-full-height"> [TAB2]
					        <div class="center">[BUTTONS2]</div>
				        </div>
			        </div>
                    <div class="col-xs-12 col-md-2 col-md-height">
				        <div class="inside inside-full-height">
					        <fieldset>
						        <button name="submit" class="ui-button-icon" style="width:100%;" value="speichern" hidden="true"></button>
						        <table width="100%" border="0" class="mkTableFormular">
							        <legend>{|Aktionen|}</legend>
                                    <tr [HINZUFUEGENHIDDEN]>
									    <td>
                                            {|Multifilter|}:&nbsp;<img src="./themes/new/images/tooltip_grau.png" border="0" style="position: relative; left: 1px; top: 3px; z-index: 8;" class="wawitooltipicon" title="Auswahl mehrerer Artikel &uuml;ber Name oder Nummer">
                                        </td>
                                    </tr>
                                    <tr [HINZUFUEGENHIDDEN]>
									    <td>
                                            <input type="text" name="multifilter" id="multifilter" value="[MULTIFILTER]" size="20" style="width:98%;" form="">
									    </td>
								    </tr>
							        <tr [HINZUFUEGENHIDDEN]>
								        <td>
									        <button name="submit" class="ui-button-icon" style="width:100%;" value="manuell_hinzufuegen">{|Hinzuf&uuml;gen|}</button>
								        </td>
							        </tr>
						        </table>
					        </fieldset>
				        </div>
                    </div>
		        </div>
	        </div>
	    </form> [TAB2ENDE]
    </div> [AFTERTAB2]
</div>
<form action="index.php?module=wareneingang&action=oeffnen" id="oeffnen" method="POST">
    <input name="id" value="[ID]" hidden></input>
</form>
<script type="text/javascript">
$(document).ready(function() {
	$("#tabs").tabs("option", "active", [TABINDEX]);
	if($('#frmWareneingangDistribution').length) {
		$('#btnabschliessen').on('click', function() {
			$('#frmWareneingangDistribution').append('<input type="hidden" value="1" name="abschliessen" />');
			$('#frmWareneingangDistribution').find('[name="submit"]').trigger('click');
		});
	}
});

    var gescannterartikelrestmenge = [GESCANNTERARTIKELRESTMENGE]+0;

    function artikelscan() {
        gescannterartikel = document.getElementById('gescannterartikel').value;
        artikel = document.getElementById('artikel').value;
        if ((artikel == gescannterartikel)) {
            document.getElementById('menge').value = Number(document.getElementById('menge').value) + 1;
            document.getElementById('menge').style.fontSize = "200%";
            document.getElementById('restmenge').style.fontSize = "200%";
            document.getElementById('hinzufuegen').addEventListener('submit', eprooform_submit);
        } else if (gescannterartikel != '') {
            alert("Scanvorgang abgebrochen!");
            document.getElementById('gescannterartikel').value = '';
            document.getElementById('menge').value = '';
            document.getElementById('hinzufuegen').addEventListener('submit', eprooform_submit);
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

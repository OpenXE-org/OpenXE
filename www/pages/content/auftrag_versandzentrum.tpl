<div id="tabs">
	<ul>
		<li><a href="#tabs-1">[TABTEXT1]</a></li>[VORTABS2UEBERSCHRIFT]
		<li><a href="#tabs-2">[TABTEXT2]</a></li>[NACHTABS2UEBERSCHRIFT] 
    </ul>
	<div id="tabs-1"> [MESSAGE] [AUTOVERSANDBERECHNEN]
		<form action="#tabs-1" id="frmauto" name="frmauto" method="post">
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-14 col-md-14 col-md-height">
	        			<div class="inside inside-full-height">
                            <fieldset>
				                <input type="text" id="kommissionierlagerplatz" name="kommissionierlagerplatz" value="" placeholder="Lagerplatz f&uuml;r Vorkommissionierung" size="30" title="Wenn kein Lagerplatz angegeben ist, wird der Kommissionierlagerplatz aus dem Projekt gew&auml;hlt">
                                <button name="submit" value="vorkommissionieren" class="ui-button-icon">Vorkommissionieren</button>
                                <button name="submit" value="vorkommissionieren_ohne_etiketten" class="ui-button-icon">Vorkommissionieren ohne Etiketten</button>
                                <button name="submit" value="versandstarten" class="ui-button-icon" title="Lieferschein, Rechnung und Lagerbuchung erzeugen">Autoversand ausf&uuml;hren</button>
                                <a class="button" href="index.php?module=versandpakete&action=lieferungen">Zum Versand</a>
                            </fieldset> 
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
	        	<div class="row-height">
	        		<div class="col-xs-14 col-md-14 col-md-height">
	        			<div class="inside inside-full-height">
                            <div class="filter-box filter-usersave">
				                <div class="filter-block filter-inline">
					                <div class="filter-title">{|Filter|}</div>
					                <ul class="filter-list">
						                <li class="filter-item">
							                <label for="fastlane" class="switch">
								                <input type="checkbox" id="fastlane"> <span class="slider round"></span> </label>
							                <label for="fastlane">{|Fast-Lane|}</label>
						                </li>
						                <li class="filter-item">
							                <label for="auftrag_kundemehrereauftraege" class="switch">
								                <input type="checkbox" id="auftrag_kundemehrereauftraege"> <span class="slider round"></span> </label>
							                <label for="auftrag_kundemehrereauftraege">{|nur Kunden mit mehreren Auftr&auml;gen|}</label>
						                </li>
						                <li class="filter-item">
							                <label for="auftrag_lieferdatum" class="switch">
								                <input type="checkbox" id="auftrag_lieferdatum"> <span class="slider round"></span> </label>
							                <label for="auftrag_lieferdatum">{|inkl. Auftr&auml;ge mit zukünftigem Lieferdatum|}</label>
						                </li>
						                <li class="filter-item">
							                <label for="auftrag_kommissionierte" class="switch">
								                <input type="checkbox" id="auftrag_kommissionierte"> <span class="slider round"></span> </label>
							                <label for="auftrag_kommissionierte">{|nur kommissionierte|}</label>
						                </li>
					                </ul>
				                </div>
			                </div> [TAB1]
			                <fieldset>
				                <legend>Stapelverarbeitung</legend>
				                <input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;
				                <input type="button" value="Anzahl x markieren" onclick="var anzahl = prompt('Anzahl zu markierender Aufträge:', ''); if( anzahl > 0) anzahlxmarkieren(anzahl);" class="btnBlue">
                            </fieldset>
                        </div>
                    </div>
                </div>
            </div>
		</form>
	</div>
	<div id="tabs-2"> [VORTABS2UEBERSCHRIFT]
		<form action="#tabs-2" method="post"> [TAB2]
			<table width="100%">
				<tr>
					<td>
						<input type="submit" value="Gew&auml;hlte Auftr&auml;ge zurücksetzen" name="entfernen">
					</td>
				</tr>
			</table>
		</form> [NACHTABS2UEBERSCHRIFT]
    </div>
</div>
<script>
function kommissionierfrage() {
	var bezeichnung = prompt('Bitte Kommissionierbezeichnung eingeben');
	$('#bezeichnung').val(bezeichnung);
	$('#auftrag_versandauswahl').val('versandstarten');
	$('#submit').trigger('click');
}

function anzahlxmarkieren(anzahl) {
	$('#auftraegeoffeneauto').find('input[type="checkbox"]').prop('checked', false);
	$('#auftraegeoffeneauto').find('input[type="checkbox"]').each(function(index, el) {
		//$('#auftraegeoffeneauto').find('input[type="checkbox"]').prop('checked',true);
		if(index < anzahl) $(el).prop('checked', true);
	});
}
$(document).ready(function() {
	$('#bezeichnung').val('');
	$("#auftraegeoffeneauto > tfoot:nth-child(3) > tr:nth-child(1) > th:nth-child(1) > span:nth-child(1) > input:nth-child(1)").hide();
	$("#auftraegeoffeneauto > tfoot:nth-child(3) > tr:nth-child(1) > th:nth-child(2) > span:nth-child(1) > input:nth-child(1)").hide();
	$("#auftraegeoffeneauto > tfoot:nth-child(3) > tr:nth-child(1) > th:nth-child(14) > span:nth-child(1) > input:nth-child(1)").hide();
	$("#auftraegeoffeneauto > tfoot:nth-child(3) > tr:nth-child(1) > th:nth-child(15) > span:nth-child(1) > input:nth-child(1)").hide();
	$('#autoalle').on('change', function() {
		var wert = $(this).prop('checked');
		$('#auftraegeoffeneauto').find('input[type="checkbox"]').prop('checked', wert);
		$('#auftraegeoffeneauto').find('input[type="checkbox"]').first().trigger('change');
	});
	$('#autoallewartend').on('change', function() {
		var wert = $(this).prop('checked');
		$('#auftraegeoffeneautowartend').find('input[type="checkbox"]').prop('checked', wert);
		$('#auftraegeoffeneautowartend').find('input[type="checkbox"]').first().trigger('change');
	});
	$('#auftrag_versandauswahl').on('change', function() {
		if($('#auftrag_versandauswahl').val() == 'drucken') {
			$('#druckerauswahl').show();
		} else {
			$('#druckerauswahl').hide();
		}
	});
	document.getElementById('frmauto').onsubmit = function(evt) {
		if($('#auftrag_versandauswahl').val() == 'versandstartenmit') {
			kommissionierfrage();
		}
	}
});
</script>

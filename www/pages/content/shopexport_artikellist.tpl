<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
	<div id="tabs-1">
		<form action="" method="post">
			[MESSAGE]
			<div class="row-height">
			    <div class="col-xs-12 col-md-10 col-md-height">
			        <div class="inside_white inside-full-height">
				        <fieldset class="white">
					        <legend></legend>
					        [TAB1]
				        </fieldset>
                        <fieldset>
                            <table>
                                <legend>Stapelverarbeitung</legend>
                                <tr>
                                    <td><input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;</td><td></td>
                                </tr>
                            </table>
                        </fieldset>
    			    </div>
	    		</div>
	    		<div class="col-xs-12 col-md-2 col-md-height">
        			<div class="inside inside-full-height">
        				<fieldset>
        					<legend>{|Aktionen|}</legend>
        					<input type="submit" class="btnBlueNew" value="{|Lagerzahlencache zur&uuml;cksetzen|}" name="delcacheselected">
        					<input type="submit" class="btnBlueNew" value="{|Lagerzahlencache gesamt zur&uuml;cksetzen|}" name="delcache">
        					<legend>{|Artikel&uuml;bertragung|}</legend>
        					<input type="submit" class="btnBlueNew" value="{|Gew&auml;hlte Artikel hinzuf&uuml;gen|}" name="artikelsend">
				            <input type="submit" class="btnBlueNew" value="{|Alle Artikel hinzuf&uuml;gen|}" name="alle"><br>
         					<input type="submit" class="btnBlueNew" value="{|Alle ge&auml;nderten Artikel hinzuf&uuml;gen|}" name="allchanged"><br>
        					<input type="submit" class="btnBlueNew" value="{|Gew&auml;hlte Artikel entfernen|}" name="artikelremove">
        				</fieldset>
        			</div>
	    		</div>
    		</div>
		</form>
	</div>
<!-- tab view schließen -->
</div>

<script>

    $('#autoalle').on('change',function(){
      var wert = $(this).prop('checked');
      $('#shopexport_artikellist').find('input[type="checkbox"]').prop('checked',wert);
      $('#shopexport_artikellist').find('input[type="checkbox"]').first().trigger('change');
    });

</script>

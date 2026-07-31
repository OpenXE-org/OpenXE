<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
	<div id="tabs-1">		
		[MESSAGE]
		[IMPORTERINFO]
		[IMPORTERINFO2]
		<div class="row">
		    <div class="row-height">
		        <div class="col-xs-12 col-md-9 col-md-height">
		            <div class="inside_white inside-full-height">
			            <fieldset class="white">
				            <legend></legend>
				            [TAB1]
			            </fieldset>
		            </div>
    		    </div>
    		    <div class="col-xs-12 col-md-3 col-md-height">
        			<div class="inside inside-full-height">
            			<form action="" method="post">
            				<fieldset>
            					<legend>{|Aktionen|}</legend>				
					            <input type="submit" class="btnBlueNew" value="{|Lagerzahlencache zur&uuml;cksetzen|}" name="delcache"><br>
					            <input type="submit" class="btnBlueNew" value="{|Lagerzahlencache für Shopartikel mit Menge 0 zur&uuml;cksetzen|}" name="delzerostockcache"><br>
					            <input type="submit" class="btnBlueNew" value="{|Artikelcache zur&uuml;cksetzen|}" name="delarticlecache"><br>
					            <input type="submit" class="btnBlueNew" value="{|Alle Artikel entfernen|}" name="abbrechen"><br>
					            <input type="submit" class="btnBlueNew" value="{|Prozesstarter deaktivieren|}" name="deaktivieren"><br>
            				</fieldset>
            			</form>
        			</div>
			    </div>
		    </div>
		</div>
	</div>
<!-- tab view schließen -->
</div>


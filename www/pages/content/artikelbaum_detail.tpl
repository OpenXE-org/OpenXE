<div>
<form method="POST" id="artikelbaumfrm">
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-12 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<table>
							<legend>Einstellungen</legend>
							<tr>
								<td>Name:</td>
								<td><span class="beforeglobe"></span><input type="text" name="bezeichnung" id="bezeichnung" value="[BEZEICHNUNG]" size="40" data-lang="kategorie_[ID]"><div class="globe"><img src="themes/new/images/web.png" /></div>
&nbsp;									<input type="submit" value="speichern" name="speichern" /></td>
								<td>[VORLOESCHEN]<input type="hidden" name="loeschen" id="loeschen" value="0" /><input type="button" value="l&ouml;schen" onclick="katloeschen();" />[NACHLOESCHEN]</td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>

[VORUNTERKATEGORIE]
	<div class="row">
		<div class="row-height">
			<div class="col-xs-12 col-md-12 col-md-height">
				<div class="inside inside-full-height">
					<fieldset>
						<table>
							<legend>Neue Unterkategorie:</legend>
							<tr>
								<td>Name:</td>
								<td><input type="text" name="bezeichnungunterkategorie" id="bezeichnungunterkategorie" size="40"/><input type="hidden" name="kat" value="[ID]" />
&nbsp;									<input type="submit" value="speichern" name="speichernunter" /></td>
								<td></td>
							</tr>
						</table>
					</fieldset>
				</div>
			</div>
		</div>
	</div>
[NACHUNTERKATEGORIE]
</form>
<div class="row">
	<div class="row-height">
		<div class="col-xs-12 col-md-12 col-md-height">
			<div class="inside inside-full-height">
				<fieldset height="30">
					<legend>Artikel in der Kategorie</legend>
					[TABELLE]
				</fieldset>
			</div>
		</div>
	</div>
</div>

</div>
<style>

        .ex_highlight #artikelbaum_artikel tbody tr.even:hover, #example tbody tr.even td.highlighted {
        background-color: [TPLFIRMENFARBEHELL]; 
        }

        .ex_highlight_row #artikelbaum_artikel tr.even:hover {
        background-color: [TPLFIRMENFARBEHELL];
        }

        .ex_highlight_row #artikelbaum_artikel tr.even:hover td.sorting_1 {
        background-color: [TPLFIRMENFARBEHELL];
        }

        .ex_highlight_row #artikelbaum_artikel tr.odd:hover {
        background-color: [TPLFIRMENFARBEHELL];
        }

        .ex_highlight_row #artikelbaum_artikel tr.odd:hover td.sorting_1 {
        background-color: [TPLFIRMENFARBEHELL];
        }
                /*
                 * Row highlighting example
                 */
                .ex_highlight #artikelbaum_artikel tbody tr.even:hover, #artikelbaum_artikel tbody tr.even td.highlighted {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight #artikelbaum_artikel tbody tr.odd:hover, #artikelbaum_artikel tbody tr.odd td.highlighted {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #artikelbaum_artikel tr.even:hover {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #artikelbaum_artikel tr.even:hover td.sorting_1 {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #artikelbaum_artikel tr.even:hover td.sorting_2 {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #artikelbaum_artikel tr.even:hover td.sorting_3 {
                background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #artikelbaum_artikel tr.odd:hover {
                  background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #artikelbaum_artikel tr.odd:hover td.sorting_1 {
                  background-color: [TPLFIRMENFARBEHELL];
                }
                .ex_highlight_row #artikelbaum_artikel tr.odd:hover td.sorting_2 {
                  background-color: #E0FF84;
                }
                .ex_highlight_row #artikelbaum_artikel tr.odd:hover td.sorting_3 {
                  background-color: #DBFF70;
                }
</style>
<script>
var oTableartikelbaum_artikel; var oMoreData1artikelbaum_artikel=0; var oMoreData2artikelbaum_artikel=0; var oMoreData3artikelbaum_artikel=0; var oMoreData4artikelbaum_artikel=0; var oMoreData5artikelbaum_artikel=0;  var oMoreData6artikelbaum_artikel=0; var oMoreData7artikelbaum_artikel=0; var oMoreData8artikelbaum_artikel=0; var oMoreData9artikelbaum_artikel=0; var oMoreData10artikelbaum_artikel=0; var oMoreData11artikelbaum_artikel=0; var oMoreData12artikelbaum_artikel=0; var oMoreData13artikelbaum_artikel=0; var aData;
                     oTableartikelbaum_artikel = $('#artikelbaum_artikel').dataTable( {
                      "bAutoWidth": false,
                      "bProcessing": true,
fnRowCallback: function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
},
                      "iCookieDuration": 600, //60*60*24,// 1 day (in seconds)
                      "aLengthMenu": [[10, 25, 50,200,1000], [10, 25, 50, 200,1000]],
                      "iDisplayLength": 10,
                      "bStateSave": false,
                      "aaSorting": [[ 1, "desc" ]],
                      "bServerSide": true,
                      "dom": 'lfrtipB',
"buttons": [
            'copy', 'csv', 'excel', 'pdf', 'print'
        ],

                      "fnInitComplete": function (){
                        $(oTableartikelbaum_artikel.fnGetNodes()).click(function (){
                            var nTds = $('td', this);
                            
                            });},
                      

                        "fnServerData": function ( sSource, aoData, fnCallback ) {
                          /* Add some extra data to the sender */
                          aoData.push( { "name": "more_data1", "value": oMoreData1artikelbaum_artikel } );
                          aoData.push( { "name": "more_data2", "value": oMoreData2artikelbaum_artikel } );
                          aoData.push( { "name": "more_data3", "value": oMoreData3artikelbaum_artikel } );
                          aoData.push( { "name": "more_data4", "value": oMoreData4artikelbaum_artikel } );
                          aoData.push( { "name": "more_data5", "value": oMoreData5artikelbaum_artikel } );
                          aoData.push( { "name": "more_data6", "value": oMoreData6artikelbaum_artikel } );
                          aoData.push( { "name": "more_data7", "value": oMoreData7artikelbaum_artikel } );
                          aoData.push( { "name": "more_data8", "value": oMoreData8artikelbaum_artikel } );
                          aoData.push( { "name": "more_data9", "value": oMoreData9artikelbaum_artikel } );
                          aoData.push( { "name": "more_data10", "value": oMoreData10artikelbaum_artikel } );
                          aoData.push( { "name": "more_data11", "value": oMoreData11artikelbaum_artikel } );
                          aoData.push( { "name": "more_data12", "value": oMoreData12artikelbaum_artikel } );
                          aoData.push( { "name": "more_data13", "value": oMoreData13artikelbaum_artikel } );
                          $.getJSON( sSource, aoData, function (json) { 
                              /* Do whatever additional processing you want on the callback, then tell DataTables */
                              fnCallback(json)
                              } );
                        },

                      "sAjaxSource": "./index.php?module=ajax&action=table&smodule=artikelbaum&cmd=artikelbaum_artikel&id=[ID]&iframe=&sid=[ID]&frommodule=artikelbaum.php&fromclass=Artikelbaum"
                    } );
                    

                    
                    
</script>




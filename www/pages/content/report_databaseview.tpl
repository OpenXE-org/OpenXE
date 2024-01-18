<div id="tabs" class="report">
	<ul>
		<li><a href="#tabs-1">Tabellen</a></li>
		<li><a href="#tabs-2">Struktur</a></li>
		<li><a href="#tabs-3">Vorschau</a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">
		[MESSAGE]
		<div class="row" id="report_list_main">
			<div class="row-height">
				<div class="col-xs-12 col-sm-10 col-sm-height">
					<div>
					    [TAB1]  
					</div>
				</div>
			</div>
		</div>
		[TAB1NEXT]
	</div>
    <div id="tabs-2">
		[MESSAGE]
		<div class="row" id="report_list_main">
			<div class="row-height">
				<div class="col-xs-12 col-sm-10 col-sm-height">
                    <legend style="float:left">
                        Tabelle&nbsp;[TABLENAME]
                    </legend>
                    <form method="post" action="#tabs-3">
                        <fieldset style="float: right;">
                            <input type="text" name="table" value="[TABLENAME]" hidden></input>
                             <table width="100%" border="0" class="mkTableFormular">
                                <tr>
                                    <td style="padding-right:10px;"><input type="checkbox" id="auswahlalle" onchange="alleauswaehlen();"/>{|alle markieren|}</td>
                                    <td><button name="submit" value="vorschau" class="ui-button-icon" style="width:100%;float:right;">Vorschau</button></td>
                                </tr>                                                          
                                </table>
                        </fieldset>
                        <p></p>
					    <div id="columnstab">
					        [TAB2]  
					    </div>
                    </form>
				</div>
			</div>
		</div>
		[TAB2NEXT]
	</div>
    <div id="tabs-3">
		[MESSAGE]
		<div class="row" id="report_list_main">
			<div class="row-height">
				<div class="col-xs-12 col-sm-10 col-sm-height">
                    <legend>
                        Tabelle&nbsp;[TABLENAME]
                    </legend>
					<div>
					    [TAB3]  
					</div>
				</div>
			</div>
		</div>
		[TAB3NEXT]
	</div>
	<!-- tab view schließen -->
</div>
<script>
    function alleauswaehlen()
    {
      var wert = $('#auswahlalle').prop('checked');
      $('#columnstab').find(':checkbox').prop('checked',wert);
    }
</script>

<script>
function updateLiveTable(i) {
    var oTableL = $('#verbindlichkeit_kontierungen').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
	  oTableL.fnFilter('%');
	  //oTableL.fnFilter('');
	  oTableL.fnFilter(tmp);  
}
</script>

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
	<table height="80" width="100%">
		<tr>
			<td width="70%">
				<fieldset class="usersave">
					<legend>{|Filter|}</legend>
					<center>
						<table width="100%" height="50" cellspacing="5">
							<tr>
 								<td>&nbsp;</td>
							</tr>
						</table>
				</fieldset>
			</td>
			<td>
				<fieldset>
					<legend>{|Datum|}</legend>
					<table cellspacing="5" height="50" width="100%">
						<tr>
  						<td>{|Von|}:</td><td><input type="text" id="von" name="von" value="[VON]" size="12" onchange="updateLiveTable()">&nbsp;</td>
  						<td>{|Bis|}:</td><td><input type="text" id="bis" name="bis" value="[BIS]" size="12" onchange="updateLiveTable()">&nbsp;</td>
						</tr>
					</table>
					</center>
				</fieldset>
			</td>
		</tr>
	</table>
[MESSAGE]
[TAB1]
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>


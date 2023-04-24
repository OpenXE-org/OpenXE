<div id="tabs">
    <ul>
            <li><a href="#tabs-1">[TABTEXT1]</a></li>
    </ul>
    <div id="tabs-1">
        <form action="#tabs-1" id="frmauto" name="frmauto" method="post">
            [MESSAGE]
            <legend>[INFO]</legend>
			<div class="filter-box filter-usersave">
				<div class="filter-block filter-inline">
					<div class="filter-title">{|Filter|}</div>
					<ul class="filter-list">
						[STATUSFILTER]
						<li class="filter-item">
							<label for="importfehler" class="switch">
								<input type="checkbox" id="importfehler" />
								<span class="slider round"></span>
							</label>
							<label for="meinetickets">{|Inkl. Importfehler|}</label>
						</li>
					</ul>
				</div>
			</div>
            [TAB1]
            <fieldset>
                <table>
                    <legend>Stapelverarbeitung</legend>
                    <tr>
                        <td><input type="checkbox" value="1" id="autoalle" />&nbsp;alle markieren&nbsp;</td>
                        <td><input type="submit" class="btnBlue" name="ausfuehren" value="{|Importfehler|}" /></td>
                    </tr>
                </table>
            </fieldset>
        </form>
    </div>
</div>

<script>

    $('#autoalle').on('change',function(){
      var wert = $(this).prop('checked');
      $('#kontoauszuege_list').find('input[type="checkbox"]').prop('checked',wert);
      $('#kontoauszuege_list').find('input[type="checkbox"]').first().trigger('change');
    });
  
</script>

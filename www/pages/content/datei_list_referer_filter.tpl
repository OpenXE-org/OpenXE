<div class="filter-box filter-usersave">
	<div class="filter-block filter-inline">
		<div class="filter-title">{|Filter|}</div>
		<ul class="filter-list">
			<li class="filter-item">
				<label for="sortmodus" class="switch">
					<input type="checkbox" id="sortmodus" name="sortmodus" value="1" />
					<span class="slider round"></span>
				</label>
				<label for="sortmodus">{|Sortierungen &auml;ndern|}</label>
			</li>
		</ul>
	</div>
</div>

<script type="application/javascript">
	function dateiup(id)
	{
		$.ajax({
			url: 'index.php?module=[MODULE]&action=dateien&cmd=up&id=[ID]',
			type: 'POST',
			dataType: 'json',
			data: { sid: id},
			success: function(data) {
				if(data)
				{
          if(typeof data.status != 'undefined' && data.status == 1) {
            var oTable = $('#datei_list_referer').DataTable();
            oTable.ajax.reload();
          }
				}
			}
    });
	}
	function dateidown(id)
	{
		$.ajax({
			url: 'index.php?module=[MODULE]&action=dateien&cmd=down&id=[ID]',
			type: 'POST',
			dataType: 'json',
			data: { sid: id},
			success: function(data) {
				if(data)
				{
				  if(typeof data.status != 'undefined' && data.status == 1) {
            var oTable = $('#datei_list_referer').DataTable();
            oTable.ajax.reload();
          }
				}
			}
    });
	}
</script>

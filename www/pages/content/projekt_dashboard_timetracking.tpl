
<script>

    function zeiterfassungstatusaendern()
    {
        var zeiterfassung_zu_beleg_select = $('#zeiterfassung_zu_beleg_select').val();
        if(zeiterfassung_zu_beleg_select == 'abgerechnet' || zeiterfassung_zu_beleg_select == 'abgeschlossen')
        {
            var anz = 0;
            var items = '';
            $('#abrechnungszeitprojektdashboard td input:checked').each(function(){
                anz++;
                var ida = this.id.split('_');
                var eins = 1;
                if(typeof ida[eins] != 'undefined')
                {
                    if(items != '')items = items + ',';
                    items = items + ''+ida[eins];
                }
            });

            if(anz > 0)
            {
                $.ajax({
                    url: 'index.php?module=projekt&action=dashboard&cmd=open&id=[ID]',
                    type: 'POST',
                    dataType: 'json',
                    data: { zeitenliste: items, cmd: 'zeitstatusaendern',status: zeiterfassung_zu_beleg_select},
                    success: function(data) {
                        $('#zeiterfassung_zu_beleg_select').val('');
                        $('#abrechnungszeitprojektdashboard').DataTable( ).ajax.reload();
                    },
                    beforeSend: function() {

                    }
                });
            }else{
                alert("Keine Buchungen ausgewählt");
                $('#zeiterfassung_zu_beleg_select').val('');
            }
        }
    }

    $(document).ready(function() {
        $('#zeiterfassungalle').on('change',function(){
            $('#abrechnungszeitprojektdashboard').find('input[type="checkbox"]').prop('checked',$(this).prop('checked'));
        });
    });

</script>

[MESSAGE]

<div style="padding:10px;">

	<div class="filter-box filter-usersave">
		<div class="filter-block filter-inline">
			<div class="filter-title">{|Filter|}</div>
			<ul class="filter-list">
				<li class="filter-item">
					<label for="zeiterfassungabgeschlossene" class="switch">
						<input type="checkbox" id="zeiterfassungabgeschlossene">
						<span class="slider round"></span>
					</label>
					<label for="zeiterfassungabgeschlossene" class="switch">{|nur abgerechnet|}</label>
				</li>
			</ul>
		</div>
	</div>

	[TABLE]

	<fieldset>
		<legend>{|ausgew&auml;hlte Zeiten|}</legend>
		<table width="100%">
			<tr>
				<td width="50%">
					<input id="zeiterfassungalle" type="checkbox">&nbsp;{|alle markieren|}&nbsp;
					<select style="margin-left:3px" id="zeiterfassung_zu_beleg_select" onchange="zeiterfassungstatusaendern();" [SELECTDISABLED]>
						<option value="">{|bitte w&auml;hlen ...|}</option>
						<option value="abgerechnet">{|als abgerechnet markieren|}</option>
					</select>
				</td>
				<td>
				</td>
			</tr>
		</table>
	</fieldset>
</div>

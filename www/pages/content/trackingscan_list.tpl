<div id="tabs">
	<ul>
		<li><a href="#tabs-1">[TABTEXT]</a></li>
	</ul>
	<!-- ende gehort zu tabview -->

	<!-- erstes tab -->
	<div id="tabs-1">

		<form method="post">
			[MESSAGE]
		</form>
		[TAB1]
		[TAB1NEXT]

		<fieldset><legend><label for="trackingnumber">{|Scan|}</label></legend>
		<table style="width: 100%;max-width: 500px;">
			<tr>
				<td colspan="3">
					<input type="hidden" id="sendingtracking" /><input type="text" id="trackingnumber" value="" style="max-width: 450px; width:100%;" />
				</td>
				<td rowspan="7"></td>
			</tr>
			<tr><td colspan="3"></td></tr>
			<tr>
				<td><label for="lasttrackingnumber">{|Zuletzt gescannte Trackingnummer|}:</label></td>
				<td>&nbsp;</td>
				<td id="lasttrackingnumber">-</td>
			</tr>
			<tr>
				<td><label for="deliverynote">{|Lieferschein|}:</label></td>
				<td>&nbsp;</td>
				<td id="deliverynote">-</td>
			</tr>
			<tr>
				<td><label for="customer">{|Kunde|}:</label></td>
				<td>&nbsp;</td>
				<td id="customer">-</td>
			</tr>
			<tr><td colspan="3"></td></tr>
			<tr>
				<td colspan="3" id="scanmessage"></td>
			</tr>
		</table>
		<div id="rightinfo"></div>

		</fieldset>
	</div>

	<!-- tab view schließen -->
</div>


<script>
    $("#trackingnumber").focus();

    $('#trackingnumber').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            $('#tabs').loadingOverlay('show');
            $('#rightinfo').html('');
            let trackingNumber = $('#trackingnumber').val();
            $('#sendingtracking').val(trackingNumber);
            document.getElementById("lasttrackingnumber").innerHTML = trackingNumber;
            $('#trackingnumber').trigger('before_sent');
            $.ajax({
                url: 'index.php?module=trackingscan&action=list&cmd=trackingscan&trackingnumber='+trackingNumber,
                data: {
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function() {
                },
                success: function(data) {
                    $('#sendingtracking').val('');
                    if(typeof data.url != 'undefined' && data.url != '') {
                        window.location = data.url;
                    }
                    if(typeof data.rightinfo != 'undefined') {
                        $('#rightinfo').html(data.rightinfo);
                    }
                    document.getElementById("scanmessage").innerHTML = data.message;
                    document.getElementById("deliverynote").innerHTML = data.deliverynote;
                    document.getElementById("customer").innerHTML = data.customer;
                    $('#tabs').loadingOverlay('remove');
                    $('#trackingnumber').trigger('ajax_complete');
                    $('#trackingnumber').focus();
                },
                error: function (jqXHR, textStatus, errorThrown) {
                    $('#sendingtracking').val('');
                    $('#tabs').loadingOverlay('remove');
                    $('#trackingnumber').focus();
								},
                complete: function () {
                    $('#sendingtracking').val('');
                }
            });

            $('#trackingnumber').val('');
        }
    });
</script>



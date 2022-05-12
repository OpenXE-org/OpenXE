<!-- gehort zu tabview -->
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



		<br>
			<table>
				<tr>
					<td>
						<label for="actionradiook">OK</label> <input type="radio" name="actionradio" id="actionradiook" value="ok" checked="">
					</td>
					<td>
						<label for="actionradiobad">Zurückstellen</label> <input type="radio" name="actionradio" id="actionradiobad" value="bad">
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="2">
						<input type="text" id="ordernumber" value="" size="60">
					</td>
				</tr>
			</table>

		  <table>
				<tr>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td colspan="3">Zuletzt gescannte Auftragsnummer:</td>
				</tr>
				<tr></tr>
				<tr>
					<td>Auftrag:</td>
					<td>&nbsp;</td>
					<td id="order">-</td>
				</tr>
				<tr>
					<td>Lieferschein:</td>
					<td>&nbsp;</td>
					<td id="delivery">-</td>
				</tr>
				<tr>
					<td>Kunde:</td>
					<td>&nbsp;</td>
					<td id="customer">-</td>
				</tr>
				<tr></tr>
				<tr>
					<td colspan="3" id="scanmessage">
						&nbsp;
					</td>
				</tr>
			</table>
	</div>

	<!-- tab view schließen -->
</div>


<script>
    $("#ordernumber").focus();

    $('#ordernumber').keypress(function(event){
        var keycode = (event.keyCode ? event.keyCode : event.which);
        if(keycode == '13'){
            let orderNumber = $('#ordernumber').val();
            if(!orderNumber){
                return '';
            }
            if($('#actionradiook').prop("checked")){
                saveOrder(orderNumber);
            }else{
                goToOrder(orderNumber);
            }
            $('#ordernumber').val('');
        }
    });

		function saveOrder(orderNumber){
				$.ajax({
						url: 'index.php?module=singleshipment&action=list&cmd=saveorder&ordernumber='+orderNumber,
						data: {
						},
						method: 'post',
						dataType: 'json',
						beforeSend: function() {
						},
						success: function(data) {
								if(data.success){
										document.getElementById("scanmessage").innerHTML = data.message;
								}else{
										document.getElementById("scanmessage").innerHTML = '<b>'+data.message+'</b>';
								}

								if(data.data.orderid){
										document.getElementById("order").innerHTML = data.data.ordernumber;
								}else{
										document.getElementById("order").innerHTML = '-';
								}

								if(data.data.deliverynumber){
										document.getElementById("delivery").innerHTML = data.data.deliverynumber;
								}else{
										document.getElementById("delivery").innerHTML = '-';
								}

								if(data.data.customer){
										document.getElementById("customer").innerHTML = data.data.customer;
								}else{
										document.getElementById("customer").innerHTML = '-';
								}
						},
						complete: function () {
						}
				});
		}


		function goToOrder(orderNumber){
				$.ajax({
						url: 'index.php?module=singleshipment&action=list&cmd=getorderidbynumber&ordernumber='+orderNumber,
						data: {
						},
						method: 'post',
						dataType: 'json',
						beforeSend: function() {
						},
						success: function(data) {
								if(data.success){
										window.open("index.php?module=auftrag&action=edit&id="+data.orderid);
										document.getElementById("scanmessage").innerHTML = '';
								}else{
										document.getElementById("scanmessage").innerHTML = data.message;
								}

								document.getElementById("order").innerHTML = '-';
								document.getElementById("delivery").innerHTML = '-';
								document.getElementById("customer").innerHTML = '-';
						},
						complete: function () {
						}
				});
		}
</script>



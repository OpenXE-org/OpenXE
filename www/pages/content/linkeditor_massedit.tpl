<script>
	function SetStatus(el, id) {
		var out = '0';
		if(el.checked) out = '1';

		$.ajax({
			url: './index.php?module=linkeditor&action=status&id='+id+'&status='+out,
			success: function() {location.reload();}
		});
	}	

	function LoadShop() {
		var shop = $('#shop').val();
		location.href= './index.php?module=linkeditor&action=massedit&shop='+shop;
	}
</script>


<form action="" method="POST" name="linkeditorform">
<div class="mybox blueborder">
	[ARTICLELINKS]
</div>

<table width="100%" cellspacing='0' cellpadding='0' id='textareas' border="0">
</table>

<table>
	<tr><td width="70px">Shop:</td>
			<td width="250px">[SHOPSTART]<input type="text" id="shop" name="shop" style="width: 230px" value="[SHOP]">[SHOPENDE]</td>
			<td><input type="button" name="search" value="Suchen" onclick='LoadShop()'></td></tr>
</table>

<div class="rules">Regeln</div>
<div>
	<table width="100%" cellspacing='2' cellpadding='0' border="0" class="linkedit">
		<tr style="background-color: #e0e0e0; font-weight: 700;">
			<td>Regex</td>
			<td>Ersetzen durch</td>
			<td>Aktiv</td>
			<td></td>
		</tr>
		[RULES]
		<tr class="tablefooter">	
			<td><input type="text" name="rule_regex" style="width:90%"></td>
			<td><input type="text" name="rule_replace" style="width:90%"></td>
			<td colspan="2"><input type="submit" name="rule_submit" value="Hinzufügen"></td>
		</tr>
	</table>
</div>

<div class="rules">Links</div>
<div>
	<table width="100%" cellspacing='2' cellpadding='0' border="0" class="linkedit">
    <tr style="background-color: #e0e0e0; font-weight: 700;">
      <td>Bestellnr.</td>
      <td>Link</td>
      <td>Ersetzen durch</td>
      <td align="center">Ersetzen</td>
    </tr>
		[LINKS]
		<tr class="tablefooter">
      <td></td>
      <td></td>
      <td></td>
      <td><input type="submit" name="replace_submit" value="Ersetzen"></td>
    </tr>
	</table>
</div>
</form>


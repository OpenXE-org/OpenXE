<script>
	function SetStatus(el, id) {
		var out = '0';
		if(el.checked) out = '1';

		$.ajax({
			url: './index.php?module=linkeditor&action=status&id='+id+'&status='+out,
			success: function() {location.reload();}
		});
	}	

</script>


<form action="" method="POST" name="linkeditorform">
<div class="mybox blueborder">
	[ARTICLELINKS]
</div>

<table width="100%" cellspacing='0' cellpadding='0' id='textareas' border="0">
	<tr >
		<td>
			<h2 class="fieldheader">&Uuml;bersicht</h2>
			<textarea id='le_uebersicht' class="cols3">
				[LEUEBERSICHT]
			</textarea>
		</td>
		<td>
			<h2 class="fieldheader">Beschreibung</h2>
			<textarea id='le_beschreibung' class="cols3">
				[LEBESCHREIBUNG]
			</textarea>
		</td>
		<td>
			<h2 class="fieldheader">Links</h2>
			<textarea id='le_links' class="cols3">
				[LELINKS]
			</textarea>
    </td>
	</tr>
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
      <td>Link</td>
      <td>Ersetzen durch</td>
      <td align="center">Ersetzen</td>
    </tr>
		[LINKS]
		<tr class="tablefooter">
      <td></td>
      <td></td>
      <td><input type="submit" name="replace_submit" value="Ersetzen"></td>
    </tr>
	</table>
</div>
</form>


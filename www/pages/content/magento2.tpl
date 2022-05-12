<div id="magento2editextendedmapping" style="display:none" title="Erweiteres Mapping bearbeiten">
	<input type="hidden" name="m2emid" id="m2emid" value="0"/>
	<fieldset>
		<legend>{|Attribut|}</legend>
		<table width="100%">
			<tr><td>{|Name|}:</td>
		  <td><input type="text" name="magento2attributename" id="magento2attributename" size="20"  /></td></tr>
			<tr><td>{|Typ|}:</td>
				<td><select id="magento2attributetype">[MAGENTO2EXTENDEDMAPPINGTYPEOPTIONS]</select></td></tr>
			<tr><td>{|Parameter|}:</td>
				<td><input type="text" name="magento2attributeparameter" id="magento2attributeparameter" size="40"  /></td></tr>
			<tr><td>{|Sichtbar|}:</td>
				<td><input type="checkbox" name="magento2attributesichtbar" id="magento2attributesichtbar"/></td></tr>
			<tr><td>{|Filterbar|}:</td>
				<td><input type="checkbox" name="magento2attributefilterbar" id="magento2attributefilterbar"/></td></tr>
			<tr><td>{|Suchbar|}:</td>
				<td><input type="checkbox" name="magento2attributesuchbar" id="magento2attributesuchbar"/></td></tr>
		</table>
	</fieldset>
</div>

<script>
    $("#magento2editextendedmapping").dialog({
        modal: true,
        bgiframe: true,
        closeOnEscape:false,
        minWidth:500,
        autoOpen: false,
        buttons: {
            ABBRECHEN: function() {
                $(this).dialog('close');
            },
            SPEICHERN: function() {
                magento2attributeeditsave();
            }
        }
    });
    $("#magento2editextendedmapping").dialog({
        close: function( event, ui ){}
    });

    function magento2attributedelete(m2emid){
        $.ajax({
            url: 'index.php?module=shopimporter_magento2&action=extendedmapping&cmd=delete&id='+m2emid,
            data: {},
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function() {
               $('#magento2_extended_mapping').dataTable().fnFilter('%');
               $('#magento2_extended_mapping').dataTable().fnFilter('');
            }
        });
    }

    function magento2attributeedit(m2emid) {
        $.ajax({
            url: 'index.php?module=shopimporter_magento2&action=extendedmapping&cmd=get&id='+m2emid,
            data: {},
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function(data) {
                document.getElementById('m2emid').value = m2emid;
                document.getElementById('magento2attributename').value = data.name;
                document.getElementById('magento2attributetype').value = data.type;
                document.getElementById('magento2attributeparameter').value = data.parameter;
                $('#magento2attributesichtbar').prop("checked",data.visible==1?true:false);
                $('#magento2attributefilterbar').prop("checked",data.filterable==1?true:false);
                $('#magento2attributesuchbar').prop("checked",data.filterable==1?true:false);
                $("#magento2editextendedmapping").dialog('open');
            }
        });
    }

    function magento2attributeeditsave(){
        $.ajax({
            url: 'index.php?module=shopimporter_magento2&action=extendedmapping&cmd=save&id='+$('#m2emid').val(),
            data: {
								name:$('#magento2attributename').val(),
								type:$('#magento2attributetype').val(),
								parameter:$('#magento2attributeparameter').val(),
                visible: $('#magento2attributesichtbar').prop("checked")?1:0,
                filterable: $('#magento2attributefilterbar').prop("checked")?1:0,
                searchable: $('#magento2attributesuchbar').prop("checked")?1:0
						},
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function(data) {
                $('#magento2_extended_mapping').dataTable().fnFilter('%');
                $('#magento2_extended_mapping').dataTable().fnFilter('');
                $("#magento2editextendedmapping").dialog('close');
            }
        });
    }

</script>
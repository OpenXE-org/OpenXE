<div id="sprykercountrytaxmapping" style="display:none" title="Länder-/Steuermapping bearbeiten">
    <input type="hidden" name="sprykerid" id="sprykerid" value="0"/>
    <fieldset>
        <legend>{|Attribut|}</legend>
        <form id="sprykercountrytaxmappingform">
            <table width="100%">
                <tr><td>{|Land|}:</td>
                    <td><input type="text" name="sprykercountryname" id="sprykercountryname" size="5"/></td></tr>
                <tr><td>{|Normal|}:</td>
                    <td><input type="text" name="sprykernormalrate" id="sprykernormalrate" size="5"/></td></tr>
                <tr><td>{|Ermäßigt|}:</td>
                    <td><input type="text" name="sprykerreducedrate" id="sprykerreducedrate" size="5"/></td></tr>
            </table>
        </form>
    </fieldset>
</div>

<script>
    var sprykerCountryTaxMappingForm = $('#sprykercountrytaxmappingform');
    var sprykerCountryTaxMappingDialog = $("#sprykercountrytaxmapping");
    var sprykerCountryTaxMappingNameField = $('#sprykercountryname');
    var sprykerCountryTaxMappingNormalRateField = $('#sprykernormalrate');
    var sprykerCountryTaxMappingReducedRateField = $('#sprykerreducedrate');

    sprykerCountryTaxMappingForm.submit(function(e){
        e.preventDefault();
        sprykercountrytaxmappingsave();
    });

    sprykerCountryTaxMappingDialog.dialog({
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
                sprykerCountryTaxMappingForm.submit();
            }
        }
    });

    function sprykerTaxDelete(sprykerid){
        $.ajax({
            url: 'index.php?module=shopimporter_spryker&action=countrytaxmapping&shopid=[ID]&cmd=delete&id='+sprykerid,
            data: {},
            method: 'post',
            dataType: 'json',
            success: function() {
                refreshSprykerCountryTaxMappingLiveTable();
            }
        });
    }

    function sprykerTaxEdit(sprykerid) {
        $.ajax({
            url: 'index.php?module=shopimporter_spryker&action=countrytaxmapping&shopid=[ID]&cmd=get&id='+sprykerid,
            data: {},
            method: 'post',
            dataType: 'json',
            success: function(data) {
                document.getElementById('sprykerid').value = sprykerid;
                sprykerCountryTaxMappingNameField.val(data.country);
                sprykerCountryTaxMappingNormalRateField.val(data.normal_rate);
                sprykerCountryTaxMappingReducedRateField.val(data.reduced_rate);
                sprykerCountryTaxMappingDialog.dialog('open');
            }
        });
    }

    function sprykercountrytaxmappingsave(){
        $.ajax({
            url: 'index.php?module=shopimporter_spryker&action=countrytaxmapping&shopid=[ID]&cmd=save&id='+$('#sprykerid').val(),
            data: {
                country: sprykerCountryTaxMappingNameField.val(),
                normal_rate: sprykerCountryTaxMappingNormalRateField.val(),
                reduced_rate: sprykerCountryTaxMappingReducedRateField.val()
            },
            method: 'post',
            dataType: 'json',
            success: function(data) {
                refreshSprykerCountryTaxMappingLiveTable();
                sprykerCountryTaxMappingDialog.dialog('close');
            }
        });
    }

    function refreshSprykerCountryTaxMappingLiveTable() {
        $('#spryker_country_tax_mapping').dataTable().fnFilter('%');
        $('#spryker_country_tax_mapping').dataTable().fnFilter('');
    }
</script>
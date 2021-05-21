<div id="realarticlemapping" style="display:none" title="Artikel Mapping bearbeiten">
    <input type="hidden" name="realmappingid" id="realmappingid" value="0"/>
    <fieldset>
        <legend>{|Artikelmapping|}</legend>
        <table width="100%">
            <tr><td>{|Artikel|}:</td>
                <td><input type="text" name="realartikelmappingartikel" id="realartikelmappingartikel" size="20"  /></td></tr>
        </table>
    </fieldset>
</div>


<fieldset>
    <legend>Artikelmapping</legend>
    <input type="button" class="btnGreen" value="Artikelliste abholen" onclick="realGetAllItems();">
    <input type="button" class="btnGreen" value="Artikel zuordnen" onclick="realMapAllItems();">
</fieldset>


<script>
    $("#realarticlemapping").dialog({
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
                realartikelmappingsave();
            }
        }
    });
    $("#realarticlemapping").dialog({
        close: function( event, ui ){}
    });

    function realartikelmappingedit(mappingid) {
        $.ajax({
            url: 'index.php?module=shopimporter_real&action=articlemapping&cmd=getarticle&id='+mappingid,
            data: {},
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function(data) {
                document.getElementById('realmappingid').value = mappingid;
                document.getElementById('realartikelmappingartikel').value = data.artikel;
                $("#realarticlemapping").dialog('open');
            }
        });
    }

    function realartikelmappingsave(){
        $.ajax({
            url: 'index.php?module=shopimporter_real&action=articlemapping&cmd=savemapping&id='+$('#realmappingid').val(),
            data: {
                artikel:$('#realartikelmappingartikel').val()
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function(data) {
                $('#real_article_mapping').dataTable().fnFilter('%');
                $('#real_article_mapping').dataTable().fnFilter('');
                $("#realarticlemapping").dialog('close');
            }
        });
    }

    function realGetAllItems(){
        var $dialog = $('#page_container');
        $dialog.loadingOverlay();
        $.ajax({
            url: 'index.php?module=shopimporter_real&action=articlemapping&cmd=getall&id='+[ID],
            data: {},
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function() {
                $('#real_article_mapping').dataTable().fnFilter('%');
                $('#real_article_mapping').dataTable().fnFilter('');
                $dialog.loadingOverlay('remove');
            }
        });
    }

    function realMapAllItems(){
        var $dialog = $('#page_container');
        $dialog.loadingOverlay();
        $.ajax({
            url: 'index.php?module=shopimporter_real&action=articlemapping&cmd=mapall&id='+[ID],
            data: {},
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
            },
            success: function() {
                $('#real_article_mapping').dataTable().fnFilter('%');
                $('#real_article_mapping').dataTable().fnFilter('');
                $dialog.loadingOverlay('remove');
            }
        });
    }
</script>
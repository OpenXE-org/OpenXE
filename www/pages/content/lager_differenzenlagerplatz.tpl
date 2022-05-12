<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[TAB1]
[TAB1NEXT]
</div>

<!-- tab view schließen -->
</div>


<div id="editLagerplatzdifferenzenEdit" style="display:none">

    <input type="hidden" id="b_id">
    <table>
        <tr>
            <td>Artikel:</td>
            <td><input type="text" id="b_artikel_name" readonly></td>
        </tr>
        <tr>
            <td>Lager Regal:</td>
            <td><input type="text" id="b_kurzbezeichnung" readonly></td>
        </tr>
        <tr>
            <td>Menge:</td>
            <td><input type="text" id="b_menge"></td>
        </tr>
    </table>
    <input type="hidden" id="b_artikel">
    <input type="hidden" id="b_lager_platz">
</div>
<script type="text/javascript">



$(document).ready(function() {


    $("#editLagerplatzdifferenzenEdit").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      ABBRECHEN: function() {
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        editLagerplatzdifferenzenEditSave();
      }
    }
  });

});

function editLagerplatzdifferenzenEditSave() {

    $.ajax({
        url: 'index.php?module=lager&action=differenzenlagerplatzsave',
        data: {
            id: $('#b_id').val(),
            menge: $('#b_menge').val(),
            artikel: $('#b_artikel').val(),
            lager_platz: $('#b_lager_platz').val()
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            if (data.status == 1) {
                $('#editLagerplatzdifferenzenEdit').find('#b_id').val('');
                $('#editLagerplatzdifferenzenEdit').find('#b_artikel').val('');
                $('#editLagerplatzdifferenzenEdit').find('#b_menge').val('');
                $('#editLagerplatzdifferenzenEdit').find('#b_lager_platz').val('');
                $('#editLagerplatzdifferenzenEdit').find('#b_artikel_name').val('');
                updateLiveTable();
                $("#editLagerplatzdifferenzenEdit").dialog('close');
            } else {
                alert(data.statusText);
            }
            App.loading.close();
        }
    });

}

function LagerplatzdifferenzenEdit(id) {
    $.ajax({
        url: 'index.php?module=lager&action=differenzenlagerplatzedit',
        data: {
            id: id
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            $('#editLagerplatzdifferenzenEdit').find('#b_id').val(data.id);
            $('#editLagerplatzdifferenzenEdit').find('#b_lager_platz').val(data.lager_platz);
            $('#editLagerplatzdifferenzenEdit').find('#b_kurzbezeichnung').val(data.kurzbezeichnung);
            $('#editLagerplatzdifferenzenEdit').find('#b_menge').val(data.menge);
            $('#editLagerplatzdifferenzenEdit').find('#b_artikel').val(data.artikel);
            $('#editLagerplatzdifferenzenEdit').find('#b_artikel_name').val(data.artikel_name);
            App.loading.close();
            $("#editLagerplatzdifferenzenEdit").dialog('open');

            $('#editLagerplatzdifferenzenEdit').find('#b_menge').focus(); 
        }
    });

}

function updateLiveTable(i) {
    var oTableL = $('#lagerdifferenzenlagerplatz').dataTable();
    oTableL.fnFilter('%');
    oTableL.fnFilter('');   
}


</script>

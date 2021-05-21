<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
    <div id="tabs-1">
      <form action="index.php?module=layoutvorlagen&action=list" method="POST" enctype="multipart/form-data" id="importSubmit">
        <input type="hidden" name="cmd" value="import" />
        <input type="file" name="importfile" /> &uuml;berschreiben bei existierenden Namen? <input type="checkbox" id="ueberschreiben" name="ueberschreiben" value="1" />
        <input type="submit" name="layoutimportieren" value="Hochladen" />
      </form>
      <div>
      [ERRORMSG]
      </div>
      [TABELLE]
    </div>
</div>

<script type="text/javascript">

function updateLiveTable() {
    var oTableL = $('#layoutvorlagen_list').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    //oTableL.fnFilter('');
    oTableL.fnFilter(tmp);
}

function deleteLayoutvorlage(layoutvorlageId) {

    var check = confirm('Wirklich löschen?');
    if (!check) {
        return false;
    }

    $.ajax({
        url: 'index.php?module=layoutvorlagen&action=delete',
        type: 'GET',
        dataType: 'json',
        data: { id: layoutvorlageId },
        success: function(data) {
            if (data.status == 1) {
                updateLiveTable();
            } else {
                alert(data.statusText);
            }
            App.loading.close();
        },
        beforeSend: function() {
            App.loading.open();
        }
    });

}
</script>

<div id="tabs">
    <ul>
        <li><a href="#tabs-1"><!--[TABTEXT]--></a></li>
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

<script type="text/javascript">
  function deletevorlage(belegid)
  {
    if(confirm('Vorlage wirklich löschen?'))
    {
      $('#belegevorlagendiv').dialog('close');
      $.ajax({
        url: 'index.php?module=belegevorlagen&action=list&cmd=delvorlage',
        type: 'POST',
        dataType: 'json',
        data: {lid:belegid},
        success: function(data) {
          var oTable = $('#belegevorlagen_list').DataTable( );
          oTable.ajax.reload();
        },
        beforeSend: function() {

        }
      });
    }
  }
</script>

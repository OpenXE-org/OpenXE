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

<script type="application/javascript">

    function ExtendedApprovalSetApproval(id)
		{
        $.ajax({
            url: 'index.php?module=extendedapproval&action=approval',
            data: {
                //Alle Felder die fürs editieren vorhanden sind
                id: id,
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                App.loading.open();
            },
            success: function(data) {
                App.loading.close();
                var oTable = $('#extendedapproval_list').DataTable( );
                oTable.ajax.reload();
            }
        });
    }

</script>
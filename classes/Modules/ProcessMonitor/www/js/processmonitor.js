$( function() {
    $('table#prozess_monitor_list').on('afterreload',function() {
        $(this).find('img.multibutton').on('click',function() {
            if(confirm('Soll der Status des Auftrags wirklich umgestellt werden?')) {
                $.ajax({
                    url: 'index.php?module=prozess_monitor&action=multibuttonchange&cmd=multibuttonclick',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        multibuttonId: $(this).data('multibuttonid'),
                        processmontorId: $(this).data('processmontorid'),
                        orderId: $(this).data('orderid'),
                    },
                    success: function(data) {
                        $('table#prozess_monitor_list').DataTable( ).ajax.reload();
                    }
                });
            }
        });
    });
});
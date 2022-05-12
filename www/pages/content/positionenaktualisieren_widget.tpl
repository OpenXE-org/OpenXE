<script type="text/javascript">
  $(document).ready(function() {
    $('#positionenaktualisieren').on('click',function(){
      $('#positionenaktualisierendiv').dialog('open');
      if(confirm('Sollen die Belegtext wirklich aktualisiert werden?'))
      {
        $.ajax({
          url: 'index.php?module=[MODULE]&action=positionen&cmd=positionenaktualisieren&id=[ID]',
          type: 'POST',
          dataType: 'json',
          data: { },
          success: function(data) {
            var urla = window.location.href.split('#');
            window.location.href=urla[ 0 ];
          },
          beforeSend: function() {

          }
        });
      }
    });
  });
</script>


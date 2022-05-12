<script type="text/javascript">
function Getgewicht(waagenr)
{
  $.ajax({
    url: 'index.php?module=ajax&action=getgewicht',
    type: 'POST',
    dataType: 'json',
    data: { seriennummer: '[SERIENNUMMER]', mindestgewicht:[MINDESTGEWICHT]},
    success: function(data) {
      if(typeof data.gewicht != 'undefined')
        $('#kg'+waagenr).val(data.gewicht);
    }
  }); 
}
</script>


<div id="divfunktionstest" style="display:none;">
<table><tr><td><form method="POST">
[INNER]
</form>
</td></tr></table>
</div>
<script type="text/javascript">
$(document).ready(function() {
  $('#divfunktionstest').dialog({
          modal: true,
          minWidth: 940,
          title:'Funktionstest [FTSERIENNUMMER] Schritt [FTSTEP]',
          close: function(event, ui){
            $('#produktionfunktionstest').focus();
          }
  });
});
</script>

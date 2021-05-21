<style>
ul.ui-autocomplete{
z-index:2999;
}
</style>

<script type="text/javascript">
  function loadbelegvorlage(belegeid)
  {
    if(confirm('Vorlage wirklich laden?'))
    {
      $('#belegevorlagendiv').dialog('close');
      $.ajax({
        url: 'index.php?module=[MODULE]&action=positionen&cmd=loadbelegvorlage&id=[ID]',
        type: 'POST',
        dataType: 'json',
        data: {lid:belegeid},
        success: function(data) {
          var urla = window.location.href.split('#');
          window.location.href=urla[ 0 ];
        },
        beforeSend: function() {

        }
      });
    }
  }

  function deletevorlage(belegeid)
  {
    if(confirm('Vorlage wirklich löschen?'))
    {
      $('#belegevorlagendiv').dialog('close');
      $.ajax({
        url: 'index.php?module=[MODULE]&action=positionen&cmd=deletebelegvorlage&id=[ID]',
        type: 'POST',
        dataType: 'json',
        data: {lid:belegeid},
        success: function(data) {
          var oTable = $('#belegevorlagen_list2').DataTable( );
          oTable.ajax.reload();
        },
        beforeSend: function() {

        }
      });
    }
  }
  
  $(document).ready(function() {
    
    $('#belegevorlagendiv').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 940,
      title:'Belege Vorlagen',
      buttons: {
        SPEICHERN: function()
        {
          if(!$('#vorlagetypbestehend').prop('checked') || confirm('Vorlage wirklich überschreiben?'))
          {
            $.ajax({
              url: 'index.php?module=[MODULE]&action=positionen&cmd=savebelegevorlage&id=[ID]',
              type: 'POST',
              dataType: 'json',
              data: { bestehendevorlage:$('#bestehendevorlage').val(), bezeichnung:$('#vorlagename').val(), vorlagetyp:$('#vorlagetypbestehend').prop('checked')?'bestehend':'neu'},
              success: function(data) {
                var urla = window.location.href.split('#');
                window.location.href=urla[ 0 ];
              },
              beforeSend: function() {

              }
            });
          }
          $(this).dialog('close');
         
        },
        ABBRECHEN: function() {
          $(this).dialog('close');
        }
      },
      close: function(event, ui){
        
      }
    });
    $('#belegevorlagen').on('click',function(){
      $('#belegevorlagendiv').dialog('open');
    });
  });
</script>
<div id="belegevorlagendiv" style="display:none">
  <table>
    <tr><td><input type="radio" checked="checked" value="neu" id="vorlagetypneu" name="vorlagetyp" /></td><td>&nbsp;als Vorlage anlegen:&nbsp;</td><td><input type="text" id="vorlagename" name="vorlagename" size="40"/>&nbsp;<i>(z.B. Standardvorlage bzw. entsprechenden Namen angeben)</i></td></tr>
    <tr><td><input type="radio" value="bestehend" id="vorlagetypbestehend" name="vorlagetyp" /></td><td>&nbsp;bestehende Vorlage &uuml;berschreiben:&nbsp;</td><td><input type="text" id="bestehendevorlage" name="bestehendevorlage" size="40" />&nbsp;<i>(bestehende Vorlage hier aussuchen)</i></td></tr>
  </table>
  [BELEGEVORLAGENTABELLE]
</div>

<table id="table[MD5]">
<tr><td>{|Lieferschein|}:</td><td><input type="checkbox" onchange="lsclick[MD5]();" id="beiback_lieferschein[MD5]" disabled value="1" [BEIPACK_LIEFERSCHEIN] /></td><td><span id="lsnr[MD5]">[LSNR]</span></td><td><input type="button" value="{|Belege &auml;ndern|}" style="cursor:hand;" onclick="editclick[MD5]();" /></td></tr>
<tr><td>{|Rechnung|}:</td><td><input type="checkbox" onchange="reclick[MD5]();" id="beiback_rechnung[MD5]" disabled value="1" [BEIPACK_RECHNUNG] /></td><td><span id="renr[MD5]">[RENR]</span></td><td></td></tr>
</table>

<table width="100%">
<tr><td width="100%">[ARTIKEL]</td></tr>
</table>
<div id="belegpopup[MD5]" style="display:none">
<table id="table[MD5]">
<tr><td>{|Lieferschein|}:</td><td><input type="checkbox" onchange="lsclick[MD5]();" id="pbeiback_lieferschein[MD5]"  value="1" [BEIPACK_LIEFERSCHEIN] /></td><td><input type="text" id="plsnr[MD5]"  value="[LSNR]"></td></tr>
<tr><td>{|Rechnung|}:</td><td><input type="checkbox" onchange="reclick[MD5]();" id="pbeiback_rechnung[MD5]"  value="1" [BEIPACK_RECHNUNG] /></td><td><input type="text" id="prenr[MD5]"  value="[RENR]"></td></tr>
</table>
</div>
<script>
function editclick[MD5](){
  $('#belegpopup[MD5]').dialog('open');
}
function reclick[MD5]()
{
  if($('#beiback_rechnung[MD5]').prop('checked'))
  {
    $('#renr[MD5]').show()
  }else{
    $('#renr[MD5]').hide();
  }
  if($('#pbeiback_rechnung[MD5]').prop('checked'))
  {
    $('#prenr[MD5]').show()
  }else{
    $('#prenr[MD5]').hide();
  }
}
function lsclick[MD5]()
{
  if($('#beiback_lieferschein[MD5]').prop('checked'))
  {
    $('#lsnr[MD5]').show()
  }else{
    $('#lsnr[MD5]').hide();
  }
  if($('#pbeiback_lieferschein[MD5]').prop('checked'))
  {
    $('#plsnr[MD5]').show()
  }else{
    $('#plsnr[MD5]').hide();
  }
}
function change[MD5]()
{
  $.ajax({
      url: 'index.php?module=wareneingang&action=minidetail&cmd=change&id=[ID]',
      type: 'POST',
      dataType: 'json',
      data: { 
      beipack_lieferschein:($('#beiback_lieferschein[MD5]').prop('checked'))?1:0,
      beipack_rechnung:($('#beiback_rechnung[MD5]').prop('checked'))?1:0,
      lsnr:$('#lsnr[MD5]').val(),
      renr:$('#renr[MD5]').val()
      },
      success: function(data) {
        
      }
    });
}
$(document).ready(function() {
  $('#table[MD5] input').on('change', function(){change[MD5]();});
  lsclick[MD5]();
  reclick[MD5]();
  
    $('#belegpopup[MD5]').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 940,
      title:'{|Belege ändern|}',
      buttons: {
        SPEICHERN: function()
        {
          $.ajax({
              url: 'index.php?module=wareneingang&action=minidetail&cmd=change&id=[ID]',
              type: 'POST',
              dataType: 'json',
              data: { 
                beipack_lieferschein:($('#pbeiback_lieferschein[MD5]').prop('checked'))?1:0,
                beipack_rechnung:($('#pbeiback_rechnung[MD5]').prop('checked'))?1:0,
                lsnr:$('#plsnr[MD5]').val(),
                renr:$('#prenr[MD5]').val()
              },
              success: function(data) {
                $('#lsnr[MD5]').html($('#plsnr[MD5]').val());
                $('#renr[MD5]').html($('#prenr[MD5]').val());
                $('#beiback_lieferschein[MD5]').prop('checked',$('#pbeiback_lieferschein[MD5]').prop('checked'));
                $('#beiback_rechnung[MD5]').prop('checked',$('#pbeiback_rechnung[MD5]').prop('checked'));
                $('#belegpopup[MD5]').dialog('close');
                lsclick[MD5]();
                reclick[MD5]();
                var oTable = $('#wareneingangarchiv').DataTable( );
                oTable.ajax.reload();
              }
            });
        },
        ABBRECHEN: function() {
          $(this).dialog('close');
        }
      },
      close: function(event, ui){
        
      }
    });
  
});
</script>
<script>
  $(document).ready(function() {
    $('#dateipopupiframediv').dialog(
      {
        modal: true,
        autoOpen: false,
        minWidth: 320,
        Width: 1480,
        maxWidth: 1480,
        title:'{|Dateien|}',
        buttons: {

          '{|OK|}': function() {
            $(this).dialog('close');
          }
        },
        open: function(event, ui){
          $(this).parent().css('width','calc(100vw - 150px)');
          $(this).parent().css('max-width','1480px');
          
          $(this).parent().css("left", (($(window).width() - $(this).parent().outerWidth()) / 2) + $(window).scrollLeft() + "px");
    
          
          //$('#dateipopupiframe').css('max-width','1220px');
          //$('#dateipopupiframe').css('min-width','300px');
          //$('#dateipopupiframe').css('width','calc(100vw - 260px)');
        },
        close: function(event, ui){
          if(![ISPOPUPBUTTON])updatefilecount();
          [ONCLOSE]
        }
      }
    );
    if(![ISPOPUPBUTTON])updatefilecount();
  });
  auxid = 0;
  function opendateipopup() {

    $('#dateipopupiframediv').dialog('open');
    [VORTYPID1]var el = $('[TYPID]').val();
    [VORTYPID2]var el = '[TYPID]';
    auxid = $('[TYPID]').val();
    $('#dateipopupiframe').attr('src', "index.php?module=dateien&action=popup&typ=[TYP]&typid="+el+"&id="+el);
    [ONOPEN]
    var oTable = $('#datei_list_referer').DataTable( );
    oTable.ajax.reload();
  }
  function updatefilecount() {
    [VORTYPID1]var el = $('[TYPID]').val();
    [VORTYPID2]var el = '[TYPID]';
    $.ajax({
      url: 'index.php?module=dateien&action=popup&cmd=getanz&typ=[TYP]&typid='+el,
      type: 'POST',
      dataType: 'json',
      data: { },
      success: function(data) {
        if([ISPOPUPBUTTON])
        {
          //window.console.log($('#[FROMPOPUP]').parent().find('div.ui-dialog-buttonpane > div.ui-dialog-buttonset'));
          var button = $('#[FROMPOPUP]').parent().find('div.ui-dialog-buttonpane > div.ui-dialog-buttonset > button > span').first();
          var wert = $(button).html();
          if(wert.indexOf('(') > -1)wert = trim(wert.substr(0, wert.indexOf('(')));
          $(button).html(wert+' ('+data.anz+')');
        }else{
          var button = $('#opendateipopupbutton');
          var wert = $(button).val();
          if(wert.indexOf('(') > -1)wert = trim(wert.substr(0, wert.indexOf('(')));
          $(button).val(wert+' ('+data.anz+')');
        }
      }
    });
  }
</script>
[VORDATEIENBUTTON]<input type="button" value="{|Dateien|}" id="opendateipopupbutton" onclick="opendateipopup();" />[NACHDATEIENBUTTON]
<div id="dateipopupiframediv" style="display:none;">
  <iframe id="dateipopupiframe" src="" style="border:none;min-height:640px;" border="0" width="100%"></iframe>
</div>
$(document).ready(function() {
  $('#artikel_onlineshops').on('afterreload',function(){
    var tab = $('#artikel_onlineshops');
    if(tab)
    {
      var trshops = $('#artikel_onlineshops tbody tr');
      if(trshops)
      {
        var idsstring = '';
        var firstsid = null;
        $(trshops).each(function(){
          $(this).find('img.exportbutton').on('click',function(){
            $(this).prev('input').val('export');
            $(this).parents('form').first().attr('action',window.location.href.split('#')[ 0 ] + '#tabs-4');
            $(this).parents('form').first().submit();
          });
          $(this).find('img.importbutton').on('click',function(){
            $(this).prev('input').val('import');
            $(this).parents('form').first().attr('action',window.location.href.split('#')[ 0 ] + '#tabs-4');
            $(this).parents('form').first().submit();
          });

          var trs = $(this).find('td span.aftershop');
          if(trs)
          {
            $(trs).each(function(){
              var sid = $(this).html();
              if(sid != '') {
                firstsid = sid;
              }
              var button = $('.onlinshopbuttonONLINESHOPBUTTON'+sid).first();
              if(button && button != null && button.length > 0)
              {
                var newbutton = $(button).clone();
                $(newbutton).toggleClass('onlinshopbuttonONLINESHOPBUTTON'+sid,false);
                $(newbutton).insertAfter(this);
                $(newbutton).show();
                $(newbutton).toggleClass('hidden', false);
                $(this).parent().find('.placeholderaftershop').toggleClass('hidden', true);
                $(newbutton).after('&nbsp;');
                $(this).html('');
              }else{
                $(this).remove();
                if(idsstring != '')
                {
                  idsstring+=',';
                }
                idsstring+=''+sid;
              }
            });
          }
        });
        $('#artikel_onlineshops').loadingOverlay('show');
        $.ajax({
          url: 'index.php?module=artikel&action=edit&cmd=getshopbuttons',
          type: 'POST',
          dataType: 'json',
          data: {ids:idsstring,firstid:firstsid},
          success: function(data) {
            $('#artikel_onlineshops').loadingOverlay('remove');
            if(typeof data.html != 'undefined' && data.html != '')
            {
              $('#shoptabelleafter').after(data.html);
              var oTable = $('#artikel_onlineshops').DataTable( );
              oTable.ajax.reload();
            }else {
              var shopafter = $('.placeholderimport').length;
              if(shopafter > 0 && shopafter === $('.placeholderimport').next('.placeholderaftershop').length)
              {
                $('.placeholderaftershop').remove();
              }

              if(typeof data.hideallimportplaceholder != 'undefined')
              {
                $('#artikel_onlineshops tbody tr').find('.placeholderimport').toggleClass('hidden', true);
              }
              if(typeof data.hideallexportplaceholder != 'undefined')
              {
                $('#artikel_onlineshops tbody tr').find('.placeholderexport').toggleClass('hidden', true);
              }
              if (typeof data.canimport != 'undefined' || typeof data.canexport != 'undefined'
              ) {
                $('#artikel_onlineshops tbody tr > td  tr > td.idtd').each(function () {
                  var data_id = $(this).data('id')+'';
                  if (data_id) {
                    if (typeof data.canimport != 'undefined' && typeof data.hideallimportplaceholder == 'undefined') {
                      if (data.canimport.indexOf(data_id) > -1) {
                        $(this).find('.importbutton').toggleClass('hidden', false);
                        $(this).find('.placeholderimport').toggleClass('hidden', true);
                      }else{
                        $(this).find('.placeholderimport').toggleClass('hidden', false);
                      }
                    }
                    if (typeof data.canexport != 'undefined' && typeof data.hideallexportplaceholder == 'undefined') {
                      if (data.canexport.indexOf(data_id) > -1) {
                        $(this).find('.exportbutton').toggleClass('hidden', false);
                        $(this).find('.placeholderexport').toggleClass('hidden', true);
                      }else{
                        $(this).find('.placeholderexport').toggleClass('hidden', false);
                      }
                    }
                  }
                });
              }
            }
          },fail:function() {

          }
        });

      }
    }
  });
  $('#artikel_onlineshops').trigger('afterreload');
});
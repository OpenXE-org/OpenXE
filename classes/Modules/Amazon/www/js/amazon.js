var cartonfocus = null;
$(document).ready(function() {
    if($('form#frmcreateshipment').length)
    {
        $('#fartikel').trigger('change');
    }
    $('input.cartoninput').on('change',function(){
        $.ajax({
            url: 'index.php?module=amazon&action=carton&cmd=changedimensions',
            dataType: 'json',
            type: 'POST',
            data: {
                plan:$(this).data('plan'),
                nr:$(this).data('nr'),
                weight:$(this).parents('tr').first().find('input.cartonweight').val(),
                length:$(this).parents('tr').first().find('input.cartonlength').val(),
                height:$(this).parents('tr').first().find('input.cartonheight').val(),
                width:$(this).parents('tr').first().find('input.cartonwidth').val(),
            },
            success: function (data) {
                if(typeof data.max_nr != 'undefined') {
                    if($('#dimension_'+data.max_nr).hasClass('hide')) {
                        $("#amazon_anlieferungcreate_carton").DataTable( ).ajax.reload();
                    }
                }
            }
        });
    });
    $('input.cartoncopywithamount').on('click',function(){
        $.ajax({
            url: 'index.php?module=amazon&action=carton&cmd=copycartonwithamount',
            dataType: 'json',
            type: 'POST',
            data: {
                plan:$(this).data('plan'),
                nr:$(this).data('nr'),
                weight:$(this).parents('tr').first().find('input.cartonweight').val(),
                length:$(this).parents('tr').first().find('input.cartonlength').val(),
                height:$(this).parents('tr').first().find('input.cartonheight').val(),
                width:$(this).parents('tr').first().find('input.cartonwidth').val(),
            },
            success: function (data) {
                if(typeof data.max_nr != 'undefined') {
                    for(var i = 1; i <= data.max_nr; i++) {
                        $('input.cartonweight').val(data.weight);
                        $('input.cartonlength').val(data.length);
                        $('input.cartonheight').val(data.height);
                        $('input.cartonwidth').val(data.width);
                    }
                    if($('#dimension_'+data.max_nr).hasClass('hide')) {
                        $("#amazon_anlieferungcreate_carton").DataTable( ).ajax.reload();
                    }
                    if(typeof data.success != 'undefined' && data.success) {
                        $("#amazon_anlieferungcreate_carton").DataTable( ).ajax.reload();
                    }
                }
            }
        });
        var nr = $(this).data('nr');
        var weight = $(this).parents('tr').first().find('input.cartonweight').val();
        var length = $(this).parents('tr').first().find('input.cartonlength').val();
        var height = $(this).parents('tr').first().find('input.cartonheight').val();
        var width = $(this).parents('tr').first().find('input.cartonwidth').val();
    });
    $('input.cartoncopy').on('click',function(){
        $.ajax({
            url: 'index.php?module=amazon&action=carton&cmd=changealldimensions',
            dataType: 'json',
            type: 'POST',
            data: {
                plan:$(this).data('plan'),
                nr:$(this).data('nr'),
                weight:$(this).parents('tr').first().find('input.cartonweight').val(),
                length:$(this).parents('tr').first().find('input.cartonlength').val(),
                height:$(this).parents('tr').first().find('input.cartonheight').val(),
                width:$(this).parents('tr').first().find('input.cartonwidth').val(),
            },
            success: function (data) {
                if(typeof data.max_nr != 'undefined') {
                    for(var i = 1; i <= data.max_nr; i++) {
                        $('input.cartonweight').val(data.weight);
                        $('input.cartonlength').val(data.length);
                        $('input.cartonheight').val(data.height);
                        $('input.cartonwidth').val(data.width);
                    }
                    if($('#dimension_'+data.max_nr).hasClass('hide')) {
                        $("#amazon_anlieferungcreate_carton").DataTable( ).ajax.reload();
                    }
                }
            }
        });
       var nr = $(this).data('nr');
       var weight = $(this).parents('tr').first().find('input.cartonweight').val();
       var length = $(this).parents('tr').first().find('input.cartonlength').val();
       var height = $(this).parents('tr').first().find('input.cartonheight').val();
       var width = $(this).parents('tr').first().find('input.cartonwidth').val();
    });


  $('#amazon_anlieferungcreate_nonparcel').on('afterreload',function(){
    $('#amazon_anlieferungcreate_nonparcel .mhd').each(function() {
      $( this).autocomplete({
        source: function( request, response ) {
          $.ajax( {
            url: 'index.php?module=ajax&action=filter&rmodule=amazon&raction=new&rid=&filtername=lagermhdcharge&artikel='+encodeURI($(this.element).first().data('artikel')),
            dataType: 'json',
            data: {
              term: request.term
            },
            success: function( data ) {
              if(data == null)
              {
                response ([]);
              }else
                response( data.length === 1 && data[ 0 ].length === 0 ? [] : data );
            }
          });
        },select: function( event, ui ) {
          var i = ui.item.value;
          var zahl = i.indexOf(" ");
          var text = i.slice(0, zahl);
          $( this ).val( text );
          return false;
        }
      });
    });


      $('#amazon_anlieferungcreate_nonparcel .mhd').on('change', function() {
          $.ajax({
              url: 'index.php?module=amazon&action=new&cmd=change',
              dataType: 'json',
              type: 'POST',
              data: {
                  bestbefore: $(this).parents('tr').first().find('.mhd').val(),
                  numberofcases: $(this).parents('tr').first().find('.numberofcases').val(),
                  unitspercase: $(this).parents('tr').first().find('.unitspercase').val(),
                  article_id: $(this).data('artikel'),
                  prep_polybagging: $(this).parents('tr').first().find('.prep_Polybagging:checked').lengh,
                  prep_bubblewrapping: $(this).parents('tr').first().find('.prep_BubbleWrapping:checked').lengh,
                  prep_taping: $(this).parents('tr').first().find('.prep_Taping:checked').lengh,
                  prep_blackshrinkwrapping: $(this).parents('tr').first().find('.prep_BlackShrinkWrapping:checked').lengh,
                  prep_labeling: $(this).parents('tr').first().find('.prep_Labeling:checked').lengh,
                  prep_hangharment: $(this).parents('tr').first().find('.prep_HangGarment:checked').lengh
              },
              success: function (data) {

              }
          });
      });
        $('#amazon_anlieferungcreate_nonparcel .numberofcases').on('change', function() {
            $.ajax({
                url: 'index.php?module=amazon&action=new&cmd=change',
                dataType: 'json',
                type: 'POST',
                data: {
                    bestbefore: $(this).parents('tr').first().find('.mhd').val(),
                    numberofcases: $(this).parents('tr').first().find('.numberofcases').val(),
                    unitspercase: $(this).parents('tr').first().find('.unitspercase').val(),
                    article_id: $(this).data('artikel'),
                    prep_polybagging: $(this).parents('tr').first().find('.prep_Polybagging:checked').lengh,
                    prep_bubblewrapping: $(this).parents('tr').first().find('.prep_BubbleWrapping:checked').lengh,
                    prep_taping: $(this).parents('tr').first().find('.prep_Taping:checked').lengh,
                    prep_blackshrinkwrapping: $(this).parents('tr').first().find('.prep_BlackShrinkWrapping:checked').lengh,
                    prep_labeling: $(this).parents('tr').first().find('.prep_Labeling:checked').lengh,
                    prep_hangharment: $(this).parents('tr').first().find('.prep_HangGarment:checked').lengh
                },
                success: function (data) {

                }
            });
        });
      $('#amazon_anlieferungcreate_nonparcel .prep').on('change', function() {
          $.ajax({
              url: 'index.php?module=amazon&action=new&cmd=change',
              dataType: 'json',
              type: 'POST',
              data: {
                  bestbefore: $(this).parents('tr').first().find('.mhd').val(),
                  numberofcases: $(this).parents('tr').first().find('.numberofcases').val(),
                  unitspercase: $(this).parents('tr').first().find('.unitspercase').val(),
                  article_id: $(this).data('artikel'),
                  prep_polybagging: $(this).parents('tr').first().find('.prep_Polybagging:checked').lengh,
                  prep_bubblewrapping: $(this).parents('tr').first().find('.prep_BubbleWrapping:checked').lengh,
                  prep_taping: $(this).parents('tr').first().find('.prep_Taping:checked').lengh,
                  prep_blackshrinkwrapping: $(this).parents('tr').first().find('.prep_BlackShrinkWrapping:checked').lengh,
                  prep_labeling: $(this).parents('tr').first().find('.prep_Labeling:checked').lengh,
                  prep_hangharment: $(this).parents('tr').first().find('.prep_HangGarment:checked').lengh
              },
              success: function (data) {

              }
          });
      });

        $('#amazon_anlieferungcreate_nonparcel .unitspercase').on('change', function() {
            $.ajax({
                url: 'index.php?module=amazon&action=new&cmd=change',
                dataType: 'json',
                type: 'POST',
                data: {
                    bestbefore: $(this).parents('tr').first().find('.mhd').val(),
                    numberofcases: $(this).parents('tr').first().find('.numberofcases').val(),
                    unitspercase: $(this).parents('tr').first().find('.unitspercase').val(),
                    article_id: $(this).data('artikel'),
                    prep_polybagging: $(this).parents('tr').first().find('.prep_Polybagging:checked').lengh,
                    prep_bubblewrapping: $(this).parents('tr').first().find('.prep_BubbleWrapping:checked').lengh,
                    prep_taping: $(this).parents('tr').first().find('.prep_Taping:checked').lengh,
                    prep_blackshrinkwrapping: $(this).parents('tr').first().find('.prep_BlackShrinkWrapping:checked').lengh,
                    prep_labeling: $(this).parents('tr').first().find('.prep_Labeling:checked').lengh,
                    prep_hangharment: $(this).parents('tr').first().find('.prep_HangGarment:checked').lengh
                },
                success: function (data) {

                }
            });
        });
  });
  $('#amazon_anlieferungcreate_nonparcel').trigger('afterreload');
  /*$('#typ').on('change',function(){
    if($(this).val()==='palette')
    {
      $('.adresse').show();
    }else{
      $('.adresse').hide();
    }
  });
  $('#typ').trigger('change');
  */

  $('#amazon_anlieferungcreate_carton').on('afterreload',function() {
      if(cartonfocus) {
          var num = $('#'+cartonfocus).val();
          $('#'+cartonfocus).val('').trigger('focus').val(num);
      }
      for(var i = 2; i <= 20; i++) {
          if($("#amazon_anlieferungcreate_carton").find("input.showcol[data-nr='" + i + "']").length)
          {
              $('#amazon_anlieferungcreate_carton > thead > tr > th:nth-child('+(i+1)+')').show();
              $('#amazon_anlieferungcreate_carton > tfoot > tr > th:nth-child('+(i+1)+')').show();
              $('#amazon_anlieferungcreate_carton > tbody > tr > td:nth-child('+(i+1)+')').show();
              $('#dimension_'+i).show();
              $('#dimension_'+i).toggleClass('hide', false);
              if($('#dimension_'+i+'.empty').length) {
                  $.ajax({
                      url: 'index.php?module=amazon&action=carton&cmd=getdimension',
                      dataType: 'json',
                      type: 'POST',
                      data: {
                          plan:$(this).data('plan'),
                          nr:i
                      },
                      success: function (data) {
                          //$('input.cartonlength[data-]')
                      }
                  });
              }
          }
          if($("#amazon_anlieferungcreate_carton").find("input.hidecol[data-nr='" + i + "']").length)
          {
              $('#amazon_anlieferungcreate_carton > thead > tr > th:nth-child('+(i+1)+')').hide();
              $('#amazon_anlieferungcreate_carton > tfoot > tr > th:nth-child('+(i+1)+')').hide();
              $('#amazon_anlieferungcreate_carton > tbody > tr > td:nth-child('+(i+1)+')').hide();
              $('#dimension_'+i).hide();
          }
      }
    $('#amazon_anlieferungcreate_carton .cartonmenge').on('change',function() {
      $.ajax( {
        url: 'index.php?module=amazon&action=carton&cmd=change',
        dataType: 'json',
        type:'POST',
        data: {
          el: this.id,value:$(this).val()
        },
        success: function( data ) {
          var oTable = $('#amazon_anlieferungcreate_carton').DataTable( );
          oTable.ajax.reload();

          /*if(typeof data.arr != 'undefined' && data.arr.length > 0)
          {
            var ths = $('#amazon_anlieferungcreate_carton tfoot tr').first().find('th');
            var i = 0;
            for(i = 0; i < data.arr.length; i++)
            {
              if(data.arr[i].anz != data.arr[i].menge)
              {
                $(ths[i + 1]).html('<span style="color:red">'+data.arr[i].anz + ' / ' + data.arr[i].menge+'</span>');
              }else {
                $(ths[i + 1]).html(data.arr[i].anz + ' / ' + data.arr[i].menge);
              }

            }
          }*/
          if(typeof data.ok != 'undefined' && data.ok == 1)
          {
            $('#weiter').prop('disabled', false);
          } else{
            $('#weiter').prop('disabled', true);
          }
        }
      });
    });
      $('#amazon_anlieferungcreate_carton .cartonmenge').on('focus',function(){
          cartonfocus = this.id;
      });
      $('#amazon_anlieferungcreate_carton .cartonmenge').on('focusout',function(){
          cartonfocus = null;
      });
  });
  $('#amazon_anlieferungcreate_carton').trigger('afterreload');
  setTimeout(function() {
    $('#amazon_anlieferungcreate_carton .cartonmenge').first().trigger('change');
  },200);
  if($('form#frmAmazonNew').length) {
      var $thead = $('#amazon_anlieferungcreate_nonparcel').find('thead');
      var thlength = $($thead).find('tr').first().find('th').length;
      if(thlength > 13) {
          $($thead).find('tr').last().after(
              '<tr class="checkall"><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td><td></td></tr>'
          );
          $trall = $($thead).find('tr.checkall');
          for(var i = 13; i < thlength; i++) {
              $($trall).html(
                  $($trall).html() + '<td><input type="checkbox" class=\"call\" /></td>'
              );
          }
          $($trall).find('input.call').on('change',function(){
              var nr = $(this).parents('td').first().prevAll().length;
              var value = $(this).prop('checked');
              $('#amazon_anlieferungcreate_nonparcel').find('tbody > tr').each(function(){
                  var $tds = $(this).find('td')[nr];
                  $($tds).find('input').prop('checked', value);
              });
          });
      }
  }
});

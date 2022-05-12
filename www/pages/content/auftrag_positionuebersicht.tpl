<script>
$( document ).ready(function() {
  
  $('img').on('click', function(){
    if(typeof savescrollpos != 'undefined' && typeof savescrollpos.savescrollpos != 'undefined') {
        savescrollpos.savescrollpos();
    }
  });
  
  var artel = $('#artikel');
  var iframe = window.parent.document.getElementById('framepositionen');
  if(iframe && $(iframe).is(':visible'))  {
    if(artel != null)$('#artikel').focus();
  }

    $('#menge').on('blur', function() {
        if(typeof aktuallisierePreise != 'undefined') {
            setTimeout(
                function() {
                    aktuallisierePreise();
                },
                20
            );
        }
    });

    $('#menge').on('keydown',function(event) {
        if(typeof aktuallisierePreise != 'undefined' && $('#preis').length) {
            if(typeof event.keyCode != 'undefined' && event.keyCode == 13
                || typeof event.which != 'undefined' && event.which == 13) {
                var $oldPrice = $('#preis').val();
                if(typeof aktuallisierePreise != 'undefined') {
                    aktuallisierePreise();
                }
                setTimeout(function() {
                    $('#preis').focus();
                    $('#menge').trigger('blur');
                    if($oldPrice != $('#preis').val()) {
                        var $form = $('#menge').parents('tr').find('input[type="submit"]');
                        if ($form.length) {
                            $($form).trigger('click');
                        } else {
                            var $form = $('#menge').parents('form');
                        }
                        if (!$form.lenth) {
                            $form = $('#menge').parents('tr').first().find('form');
                        }
                        if ($form.length) {
                            $($form).submit();
                        }
                    }
                }, 100);
            }
        }
        else if($('#preis').length === 0 || $('#preis').attr('type')=='hidden') {
            if($('#menge').length && $('#menge').val()+'' != '') {

                if(typeof event.keyCode != 'undefined' && event.keyCode == 13
                    || typeof event.which != 'undefined' && event.which == 13) {
                    var $form = $('#menge').parents('tr').find('input[type="submit"]');
                    $('#menge').trigger('blur');
                    if($form.length) {
                        setTimeout(function() {
                            var $form = $('#menge').parents('tr').find('input[type="submit"]');
                            $($form).trigger('click');
                        },100);
                    }
                }
    				}
        }
    });

    if($('#menge').length && $('#preis').length && $('#preis').attr('type')!=='hidden') {

        var $form = $('#menge').parents('form');
        if(!$form.lenth) {
            $form = $('#menge').parents('tr').first().find('form');
        }
        if($form.length) {
            if(typeof $form[ 0 ] !== 'undefined') {
                $form = $($form).first();
            }
            $($($form)).on(
                'submit',
								function(event) {
										if($('#menge:focus').length) {
												event.preventDefault();
										}
            		}
            );
        }

    }

    $('table#tableone select.selgrund').on('change',function(){
        $.ajax({
            url: 'index.php?module=retoure&action=editable',
            type: 'POST',
            dataType: 'text',
            data: {value:$(this).val(), id: $(this).data('id')+'split9'},
            success: function(data) {

            }
        });
    });



});
  function alleauswaehlen(el)
  {
    var wert = $(el).prop('checked');
    $('table#tableone').find("[type=checkbox]").prop('checked',wert);
  }
  
  function aktionpositionen(el)
  {
    var wert = $(el).val();
    var checkboxen = $('table#tableone').find(':checked');
    if(wert != '')
    {
      if(checkboxen.length)
      {
        if(wert == 'loeschen')
        {
          if(confirm('{|Wirklich löschen?|}'))
          {
            var parameter = '';
            $(checkboxen).each(function(){
              if(parameter != '')parameter += ',';
              if($(this).attr('name') == 'belegsort[]')
              {
                parameter += 'b'+$(this).val();
              }else{
                parameter += 'z'+$(this).val();
              }
            });
            window.location.href='index.php?module=[MODULE]&action=del[MODULE]position&id=[ID]&sid='+parameter;
          }else{
            $(el).val('');
          }
        }else{
          [AUFTAG_POSITIONUEBERSICHT_HOOK1]
        }
      }
      else {
        $(el).val('');
        alert('{|Es wurden keine Positionen ausgewählt|}');
      }
    }
  }
</script>
<style>
  input.ui-autocomplete-input {background:#D5ECF2}

</style>
[MESSAGE]

[TAB1]


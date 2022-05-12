var ShopCreate = function ($) {
    'use strict';

    var me = {
        storage: {
          vueElement: null
        },
        search:function (el)
        {
            var wert = $(el).val();
            $.ajax({
                url: 'index.php?module=onlineshops&action=create&cmd=suche',
                type: 'POST',
                dataType: 'json',
                data: { val: wert}})
             .done( function(data) {
                 if(typeof data != 'undefined' && data != null)
                 {
                     if(typeof data.ausblenden != 'undefined' &&  data.ausblenden != null)
                     {
                         var ausblenden = data.ausblenden.split(';');
                         $.each(ausblenden, function(k,v){
                             if(v != '')$('#'+v).hide();
                         });

                     }
                     if(typeof data.anzeigen != 'undefined' &&  data.anzeigen != null)
                     {
                         var anzeigen = data.anzeigen.split(';');
                         $.each(anzeigen, function(k,v){
                             if(v != '')$('#'+v).show();
                         });
                     }
                 }
             });
        },

        init:function () {
            $('#suche').on('keyup', function(){
               me.search(this);
            });
            me.storage.vueElement = $('#onlineshop-create').clone();
            $('.createbutton').on('click', function(){
                $.ajax({
                    url: 'index.php?module=onlineshops&action=create&cmd=getassistant',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        shopmodule: $(this).data('module'),
                    }
                }).done( function(data) {
                    if(typeof data.pages == 'undefined' && typeof data.location != 'undefined') {
                        window.location = data.location;
                        return;
                    }
                     if($('#onlineshop-create').length === 0) {
                         $('body').append(me.storage.vueElement);
                     }
                     new Vue({
                         el: '#onlineshop-create',
                         data: {
                             showAssistant: true,
                             pagination: true,
                             allowClose: true,
                             pages: data.pages
                         }
                     });
                 });
            });
            $('.autoOpenModule').first().each(function(){
                $(".createbutton[data-module='"+ $(this).data('module') +"']").first().trigger('click');
                $(this).remove();
            });
            $('.booster').first().each(function(){
                $.ajax({
                    url: 'index.php?module=onlineshops&action=create&cmd=getbooster',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        shopmodule: $(this).data('module'),
                    }
                }).done( function(data) {
                    if(typeof data.pages == 'undefined' && typeof data.location != 'undefined') {
                        window.location = data.location;
                        return;
                    }
                    if($('#onlineshop-create').length === 0) {
                        $('body').append(me.storage.vueElement);
                    }
                    new Vue({
                        el: '#onlineshop-booster',
                        data: {
                            showAssistant: true,
                            pagination: true,
                            allowClose: true,
                            pages: data.pages
                        }
                    });
                });
                //$(".createbutton[data-module='"+ $(this).data('module') +"']").first().trigger('click');
                $(this).remove();
            });
        }

    };
    return {
        init: me.init
    };
}(jQuery);

$(document).ready(function () {
    ShopCreate.init();
});

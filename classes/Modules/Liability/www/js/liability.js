$("input#sachkonto").autocomplete({
 source: function(request, response) {
        $.ajax({
            url: "index.php?module=ajax&action=filter&filtername=sachkonto",
            dataType: "json",
            data: {
                term : request.term,
                cmd : $("#projekt").val()
            },
            success: function(data) {
                response(data);
            }
        });
  },
  select: function( event, ui ) {
    var selected  = ui.item.value;
    var positionspace = selected.indexOf(" ");
    var firststring = selected.slice(0, positionspace);
    $( "input#sachkonto" ).val( firststring );
    return false;
  }

});

function UndoPayedLiability(value)
{

  if(!confirm("Soll der Status: bezahlt wirklich auf offen zurückgesetzt werden?")) return false;
  else window.location.href=value;

}



function BezahltDialog(value)
{

  if(!confirm("Soll der Eintrag manuell ohne SEPA Überweisung als bezahlt markiert werden?")) return false;
  else window.location.href=value;
}

var Liability = function ($) {
    "use strict";

    var me = {

        elem: {},

        init:function(){
            $('#tabs').on('tabsactivate', function( event, ui ) {
                me.activateTabs();
            });
            me.activateTabs();
        },
        activateTabs:function() {
            $('#tabs').find('div.ui-tabs-panel:visible iframe.preview').each(function(){
                $(this).attr('src', $(this).data('src'));
                $(this).toggleClass('preview', false);
            });
        },
    };

    return {
        init: me.init,
    };

}(jQuery);

$(document).ready(function () {
    Liability.init();
});

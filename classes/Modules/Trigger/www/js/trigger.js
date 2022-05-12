var Trigger = function ($) {
    "use strict";

    var me = {
        init: function(){
            $('#trigger_list').on('afterreload', function(){
                $('#trigger_list input.alias').on('change',function(){
                    $.ajax({
                        url: 'index.php?module=trigger&action=list&cmd=changealias',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id:$(this).data('id'),
                            value:$(this).val()
                        },
                        success: function(data){
                            if(data.status == 0) {
                                alert(data.error);
                            }
                        }
                    });
                });
                $('#trigger_list input.description').on('change',function(){
                    $.ajax({
                        url: 'index.php?module=trigger&action=list&cmd=changedescription',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            id:$(this).data('id'),
                            value:$(this).val()
                        },
                        success: function(data){
                            if(data.status == 0) {
                                alert(data.error);
                            }
                        }
                    });
                });
            });
        }
    };

    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    Trigger.init();
});
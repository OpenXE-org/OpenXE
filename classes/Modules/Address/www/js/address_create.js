var AddressDuplicates = (function ($) {
    'use strict';

    var me = {

        storage: {
            $fields: [],
            fieldConfig: [],
            debugging: false
        },

        init: function () {
            me.registerEvents();
        },

        registerEvents: function () {
            var isValid = true;

            $('#name, #strasse, #plz, #ort').on('blur', function () {
                me.checkDuplicate($(this)).done(function (validationResult) {
                    if(validationResult){
                        if ($('#name').hasClass('duplicated_address_error')) {
                            return; // Fehlermeldung wird schon angezeigt
                        }
                        $('<span class="duplicated_address" style="color:red">').html('M&ouml;glicherweise doppelt').insertAfter($('#name'));
                        $('#name').addClass('duplicated_address_error');
                    }else{
                        $('#name').removeClass('duplicated_address_error');
                        $('#name').next('span.duplicated_address').remove();
                    }

                });
            });
        },

        checkDuplicate: function () {
            var nameValue = $('#name').val();
            var streetValue = $('#strasse').val();
            var zipcodeValue = $('#plz').val();
            var placeValue = $('#ort').val();

            return jQuery.ajax({
                type: 'POST',
                url: 'index.php?module=adresse&action=create&cmd=duplicate',
                data: {name: nameValue, street: streetValue, zipcode: zipcodeValue, place: placeValue},
                dataType: 'json',
            });
        },

    };

    return {
        init: me.init
    };

})(jQuery);


$(document).ready(function () {
    AddressDuplicates.init();
});
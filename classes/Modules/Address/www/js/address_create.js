var AddressDuplicates = (function ($) {
    'use strict';

    var me = {

        storage: {
            $fields: [],
            fieldConfig: [],
            debugging: false
        },

        init: function () {
            me.registerZipcodeSanitizer();
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

        registerZipcodeSanitizer: function () {
            var $zipcodeFields = $('#plz, #rechnung_plz');

            if(!$zipcodeFields.length){
                return;
            }

            me.sanitizeZipcodeField($zipcodeFields);

            $zipcodeFields.on('blur change', function () {
                me.sanitizeZipcodeField($(this));
            });
        },

        sanitizeZipcodeField: function ($field) {
            if(!$field || !$field.length){
                return;
            }

            $field.each(function () {
                var $currentField = $(this);
                var currentValue = $currentField.val();
                if(typeof currentValue !== 'string'){
                    return;
                }

                // Entfernt Leerzeichen am Anfang oder Ende, die keine eigentlichen Zeichen flankieren
                var sanitizedValue = currentValue.replace(/^\s+|\s+$/g, '');
                if(sanitizedValue !== currentValue){
                    $currentField.val(sanitizedValue);
                }
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

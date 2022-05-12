var PaymentAccount = function ($) {
    'use strict';

    var me = {
        selector: {
            listTable: '#kontenlist',
            passwordPopup: '#editPasswortTresor'
        },
        savePassword: function () {
            $.ajax({
                url: 'index.php?module=konten&action=passworttresor',
                data: {
                    //Alle Felder die fürs editieren vorhanden sind
                    editid: $('#e_id').val(),
                    editpasswort: $('#e_passwort').val()
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    App.loading.close();
                    if (data.status == 1) {
                        $(me.selector.passwordPopup).find('#e_passwort').val('');
                        $(me.selector.passwordPopup).dialog('close');
                    } else {
                        alert(data.statusText);
                    }
                }
            });
        },
        openPopup: function () {
            $('#e_name').focus();

            $(me.selector.passwordPopup).dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 480,
                autoOpen: true,
                buttons: {
                    ABBRECHEN: function () {
                        $(this).dialog('close');
                    },
                    SPEICHERN: function () {
                        me.savePassword();
                    }
                }
            });
        },
        initPasswordPopup: function () {
            $('#setpassword').on('click',
                function () {
                    me.openPopup();
                }
            );
        },
        initList: function () {
            $(me.selector.listTable).on('afterreload', function () {
                $(me.selector.listTable).find('.deletelink').on('click', function () {
                    if (confirm('Soll der Eintrag wirklich gelöscht oder Storniert werden?')) {

                    }
                    $.ajax({
                        url: 'index.php?module=konten&action=delete',
                        data: {
                            id: $(this).data('id')
                        },
                        method: 'post',
                        dataType: 'json',
                        beforeSend: function () {
                            App.loading.open();
                        },
                        success: function () {

                        }
                    });
                });
            });
            $(me.selector.listTable).trigger('afterreload');
        },
        init: function () {
            if ($(me.selector.passwordPopup).length > 0) {
                me.initPasswordPopup();
            }
            if ($(me.selector.listTable).length > 0) {
                me.initList();
            }
        }
    };

    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    PaymentAccount.init();
});

var BetaProgram = function ($) {
    'use strict';

    var me = {

        storage: {
            actualType: null,
            oldValue: null,
            newValue: null
        },
        updateKey: function () {
            $.ajax({
                url: 'index.php?module=welcome&action=start&cmd=updatekey',
                type: 'POST',
                dataType: 'text',
                data: {},
                success: function () {
                    window.location.href = 'index.php?module=betaprogram&action=list';
                }
            });
        },

        init: function () {
            $('#modalbeta').dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title: '',
                    buttons: {
                        'Ja ich möchte immer Zugriff auf die nächste Beta Version haben': function () {
                            $('#modalbeta').parent().loadingOverlay();
                            $.ajax({
                                url: 'index.php?module=betaprogram&action=list&cmd=activatebeta',
                                type: 'POST',
                                dataType: 'json',
                                data: {},
                                success: function (data) {
                                    if (data.status === 'OK') {
                                        me.updateKey();
                                    } else {
                                        $('#modalbeta').parent().loadingOverlay('remove');
                                    }
                                },
                                beforeSend: function () {

                                },
                                error: function () {
                                    $('#modalbeta').parent().loadingOverlay('remove');
                                }
                            });
                        }
                    }
                }
            );

            $('input#activeatebeta').on('click', function () {
                $('#modalbeta').dialog('open');
            });
            $('input#deactiveatebeta').on('click', function () {
                if (!confirm('Wollen Sie wirklich nicht mehr am Beta-Programm teilnehmen?')) {
                    return;
                }
                $('input#deactiveatebeta').parent().loadingOverlay();
                $.ajax({
                    url: 'index.php?module=betaprogram&action=list&cmd=deactivatebeta',
                    type: 'POST',
                    dataType: 'json',
                    data: {},
                    success: function (data) {
                        if (data.status) {
                            me.updateKey();
                        }
                    },
                    beforeSend: function () {

                    },
                    error: function () {
                        $('input#deactiveatebeta').parent().loadingOverlay('remove');
                    }
                });
            });
        }
    };
    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    BetaProgram.init();
});

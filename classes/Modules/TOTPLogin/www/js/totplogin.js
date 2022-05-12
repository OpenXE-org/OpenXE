var welcomeUi = (function ($) {
        var me = {
            storage: {
                qrDiv: null,
                secret: null
            },

            init: function () {
                $(document).ready(function () {
                    let enableButtons = $('#totp_enable');
                    if (enableButtons.length > 0) {
                        enableButtons[0].onclick = me.handleOnClickEnable;
                    }

                    let disableButtons = $('#totp_disable');
                    if (disableButtons.length > 0) {
                        disableButtons[0].onclick = me.handleOnClickDisable;
                    }
                });
            },

            handleOnClickDisable: function () {
                $.post('index.php?module=totp&action=disable', {'action': 'disable'}, me.handleDisablePOST);
            },

            handleOnClickEnable: function () {
                $('#dialog-confirm').dialog({
                    resizable: false,
                    height: 'auto',
                    width: 400,
                    modal: true,
                    buttons: [
                        {
                            text: 'Abbrechen',
                            click: function () {
                                $(this).dialog('close');
                            },
                            class: "button button-secondary"
                        },
                        {
                            text: 'Ich habe den Code gescannt!',
                            click: function () {
                                $(this).dialog('close');
                                $.post('index.php?module=totp&action=enable', {'secret': me.storage.secret},
                                    me.handleEnablePOST);
                            },
                            class: "button button-primary"
                        }
                    ]
                });
                $('.ui-button-text').removeClass()
                $.get(
                    'index.php?module=totp&action=generate',
                    me.handleSecretGenerateGET
                );
            },

            handleSecretGenerateGET: function (data, status) {
                if (status !== 'success') {
                    alert('Leider ist etwas schief gelaufen');
                    return;
                }
                $('#div_qr').html(data.qr);
                // $('#secret').text(data.secret);
                me.storage.secret = data.secret;
            },

            handleEnablePOST: function (data, status) {
                if (status !== 'success') {
                    alert('Fehlgeschlagen: ' + data.msg);
                    return;
                }
                location.reload();
            },

            handleDisablePOST: function (data, status) {
                if (status !== 'success') {
                    alert('Fehlgeschlagen: ' + data.msg);
                    return;
                }
                location.reload();
            }
        };

        return {
            init: me.init
        };
    }
)(jQuery);

welcomeUi.init();

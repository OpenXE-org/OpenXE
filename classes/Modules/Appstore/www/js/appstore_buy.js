var AppstoreBuy = function ($) {
    'use strict';

    var me = {

        storage: {
            actualType: null,
            oldValue: null,
            newValue: null,
            hubspot: null,
            sent: null,
            conversationValue: null,
            campaignLabel: null
        },

        elem: {
            $landingTotalPrice: null,
            $landingTotalPriceTimespan: null,
            $landingUserCounter: null,
            $landingUserCounterPopup: null
        },

        updateKey: function () {
            $.ajax({
                url: 'index.php?module=welcome&action=start&cmd=updatekey',
                type: 'POST',
                dataType: 'text',
                data: {},
                success: function () {
                    $.ajax({
                        url: 'index.php?module=appstore&action=buy&cmd=getbuyinfo',
                        type: 'POST',
                        dataType: 'text',
                        data: {},
                        success: function () {
                            window.location.href = 'index.php?module=appstore&action=buy';
                        },
                        beforeSend: function () {

                        },
                        error: function () {
                            $('#modalbeta').parent().loadingOverlay('remove');
                            $('#tabs-1').loadingOverlay('remove');
                        }
                    });
                }
            });
        },

        bindUnBuyButton: function ($element) {
            me.storage.oldValue = $($element).data('oldvalue');
            me.storage.newValue = $($element).data('newvalue');
            $('#unnewvalue').val(me.storage.newValue);
            me.storage.price = parseFloat($($element).data('price'));
            me.storage.actualType = $($element).data('type');
            if (me.storage.actualType === 'delete_module' || me.storage.actualType === 'delete_all') {
                $('#unnewvalue').hide();
            } else {
                $('#unnewvalue').show();
            }
            $('#modulunbuytext').html($($element).data('info'));
            $('#modalunbuy').dialog('open');
        },

        buyFromDemoSend: function () {
            $.ajax({
                url: 'index.php?module=appstore&action=buy&cmd=buyfromdemo',
                type: 'POST',
                dataType: 'json',
                data: {
                    company: $('#customercompany').val(),
                    email: $('#customeremail').val(),
                    name: $('#customername').val(),
                    street: $('#customerstreet').val(),
                    street2: $('#customeraddress2').val(),
                    zip: $('#customerzip').val(),
                    city: $('#customercity').val(),
                    country: $('#customercountry').val(),
                    bank: $('#customerbank').val(),
                    bankname: $('#customerbankname').val(),
                    iban: $('#customeriban').val(),
                    bic: $('#customerbic').val(),
                    user: $('#customeruser').val(),
                    lightuser: $('#customerlightuser').val(),
                    agreement: $('#customeragreement').prop('checked') ? 1 : 0,
                    change: ($('#buyfromdemo').length === 0) ? 1 : 0
                },
                success: function (data) {
                    me.storage.sent = true;
                    if (data.errorMessage !== undefined) {
                        me.setError(data);
                    }
                    if (data.status === 'OK') {
                        me.hubspotSend($('#buyversion').data('hubspoteventok'));
                        me.updateKey();
                    } else {
                        me.hubspotSend($('#buyversion').data('hubspoteventerror'));
                        //TODO remove loading
                    }
                },
                beforeSend: function () {

                },
                error: function () {
                    //TODO remove loading
                }
            });
            me.hubspotSend($('#buyversion').data('hubspotevent'));
        },

        setError: function (error) {
            var current, i, inputField,
                errorMessage = 'Bitte ergänze die markierten Pflichtfelder',
                $errorContainer = $('#buyversion').next('div'),
                $errorHint = $errorContainer.find('.error-hint');

            if (error.length === 0) {
                return;
            }

            if (typeof error.errorMessage === 'string' || error.errorMessage instanceof String) {
                errorMessage = error.errorMessage;
            }

            if ($errorHint.length > 0) {
                $errorHint.html(errorMessage);
            } else {
                $errorContainer.append('<p class="error-hint">' + errorMessage + '</p>');
            }

            if (error.invalidFields === undefined || error.invalidFields.length === 0) {
                return;
            }

            $('[id^="customer"]').removeClass('input-error');

            for (i = 0; i < error.invalidFields.length; i++) {
                current = error.invalidFields[i];
                inputField = $('#customer' + current);

                if (inputField.length > 0) {
                    inputField.addClass('input-error');
                }
            }
        },
        updateDataLayerScript: function() {
            if($('#datalayerscript').length > 0) {
                $('#datalayerscript').remove();
            }
            $('body').append(
                '<script id="datalayerscript">\n' +
                '  dataLayer = [{\n' +
                '    \'conversionValue\': \'' + (me.storage.conversationValue) + '\'\n' +
                '  },' +
                '  {' +
                '    \'campaign\': \'' + (me.storage.campaignLabel) + '\'\n' +
                '  }];\n' +
                '</script>'
            );
        },
        initBuyVersion: function () {
            var buyFromDemo = $('#buyfromdemo'),
                buyVersionContainer = $('#buyversion');

            if ($('.buy-licence-landingpage').length) {
                buyVersionContainer.dialog(
                    {
                        modal: true,
                        autoOpen: false,
                        width: '70%',
                        title: '',
                        buttons: {
                            'Jetzt mieten': function () {

                                me.buyFromDemoSend();
                            }
                        },
                        close: function () {
                            if (me.storage.sent === null) {
                                me.hubspotSend($('#buyversion').data('hubspoteventabort'));
                            }
                        }
                    }
                );
            }

            buyFromDemo.on('click', function () {
                buyVersionContainer.dialog('open');
                me.hubspotSend($(this).data('hubspotevent'), $('#customeruserdemo').val());
            });

            $('#changecustomerinfos').on('click', function () {
                if ($('#buy-licence-landingpage').length > 0) {
                    me.storage.sent = null;
                    $('#buyversion').dialog('open');
                } else {
                    me.storage.oldValue = $(this).data('oldvalue');
                    var diffuser = parseInt($('#customeruser').val());// - parseInt(me.storage.oldValue);
                    me.storage.newValue = diffuser;
                    $('#newvalue').val(me.storage.newValue);
                    me.storage.price = parseFloat($(this).data('price'));
                    me.storage.actualType = 'add_user';
                    $('#newvalue').hide();
                    $('#modulbuytext').html(
                        'Weitere ' + diffuser + ' mieten für ' + (me.storage.price * diffuser) + ' EUR pro Monat?');
                    $('#modalbuy').dialog('open');
                }
            });
            if ($('#customerinfocontent').length === 0) {
                $('#buyversion + .ui-dialog-buttonpane').append('<div class="buy-version-legal">' +
                    '                <input type="checkbox" id="customeragreement">' +
                    '                <label for="customeragreement"> Ich habe die' +
                    '                    <a href="index.php?module=dataprotection&action?list" title="Datenschutzbestimmungen" target="_blank">Datenschutzbestimmungen</a> gelesen und akzeptiere die' +
                    '                    <a href="https://xentral.com/agb" title="Allgemeine Geschäftsbedingungen" target="_blank">AGB</a>.' +
                    '                </label>' +
                    '            </div>');
            }
            me.hubspotSend($('#buyversion').data('hubspoteventinit'));
        },

        initLandingpage: function () {
            if (me.elem.$landingUserCounter.length === 0) {
                return;
            }
            me.storage.campaignLabel = me.elem.$landingTotalPrice.data('campaign') + '';
            var initialCostPerUser = parseInt(me.elem.$landingTotalPrice.data('userprice')),
                cloudPrice = parseInt(me.elem.$landingCloudPricePopup.data('cloudprice')),
                numberOfUser = parseInt($('#customeruserdemo').val());

            me.setUserLicencePrice(numberOfUser, initialCostPerUser, cloudPrice);

            me.elem.$landingUserCounter.on('change', function () {
                numberOfUser = parseInt(this.value);
                me.setUserLicencePrice(numberOfUser, initialCostPerUser, cloudPrice);
            });
            me.elem.$landingUserCounterPopup.on('change', function () {
                numberOfUser = parseInt(this.value);
                me.setUserLicencePrice(numberOfUser, initialCostPerUser, cloudPrice);
            });
        },

        /**
         *
         * @param {Number} numberOfUser
         * @param {Number} initialCostPerUser
         * @param {Number} cloudPrice
         */
        setUserLicencePrice: function (numberOfUser, initialCostPerUser, cloudPrice) {
            if (isNaN(numberOfUser)) {
                numberOfUser = 0;
            }
            if (isNaN(initialCostPerUser)) {
                initialCostPerUser = 0;
            }
            if (isNaN(cloudPrice)) {
                cloudPrice = 0;
            }
            me.storage.conversationValue = numberOfUser * initialCostPerUser + cloudPrice;
            me.elem.$landingTotalPrice.html(me.storage.conversationValue);
            if (me.elem.$landingTotalPricePopup !== null) {
                me.elem.$landingTotalPricePopup.html(me.storage.conversationValue);
            }
            me.elem.$landingTotalPriceTimespan.html(numberOfUser + ' User/Monat');
            me.elem.$landingTotalPriceTimespanPopup.html(numberOfUser + ' User/Monat');
            me.updateDataLayerScript();
        },

        hubspotSend: function (eventName, value) {
            if (me.storage.hubspot === null) {
                return;
            }
            if (value === undefined || value === null) {
                me.storage.hubspot.push(['trackEvent', {id: eventName}]);
                return;
            }

            me.storage.hubspot.push(['trackEvent', {id: eventName, value: value}]);
        },

        init: function () {
            $('#fieldsetmodules').hide();
            if ($('.buy-licence-landingpage').length > 0
                && $('.buy-licence-landingpage').data('hubspotactive') + '' === '1') {
                me.storage.hubspot = window._hsq = window._hsq || [];
            }
            me.elem.$landingTotalPrice = $('#landing-total-price');
            me.elem.$landingTotalPricePopup = $('#landing-total-price-popup');
            me.elem.$landingTotalPriceTimespan = $('#landing-total-price-timespan');
            me.elem.$landingTotalPriceTimespanPopup = $('#landing-total-price-timespan-popup');
            me.elem.$landingCloudPricePopup = $('#landing-cloud-price');
            me.elem.$landingUserCounter = $('.buy-licence-landingpage .counter-component input');
            me.elem.$landingUserCounterPopup = $('#buyversion .counter-component input');

            $('#customeruserdemo').on('change', function () {
                $('#customeruser').val($(this).val());
                $('#customeruser').trigger('change');
            });
            $('#customeruser').on('change', function () {
                if ($('#customeruserdemo').length > 0) {
                    $('#customeruserdemo').val($(this).val());
                }
            });


            me.initLandingpage();

            if ($('#buyversion').length > 0) {
                me.initBuyVersion();
            }

            if ($('#changecustomerinfos').length > 0) {
                $('#customerInfoTable').append(
                    '<input checked hidden style="display: none" type="checkbox" id="customeragreement">');
            }
            $('#unbuylightuser').hide();
            $('#unbuyuser').hide();
            $.ajax({
                url: 'index.php?module=appstore&action=buy&cmd=getbuyinfo',
                type: 'POST',
                dataType: 'json',
                data: {},
                success: function (data) {
                    $('#buyed').html(data.data);
                    $('#buyed').find('input.unbuybutton').on('click', function () {
                        me.bindUnBuyButton($(this));
                    });
                    if (typeof data.user != 'undefined') {
                        $('#unbuyuser').show();
                        $('#unbuyuser').data('oldvalue', data.user);
                        if (data.user !== data.maxuser) {
                            $('#usercount').attr('disabled', 'disabled');
                            $('#unbuyuser').attr('disabled', 'disabled');
                        }
                    } else {
                        $('#unbuyuser').hide();
                    }
                    if (typeof data.lightuser != 'undefined') {
                        $('#unbuylightuser').show();
                        $('#unbuylightuser').data('oldvalue', data.lightuser);
                        if (data.lightuser !== data.maxlightuser) {
                            $('#lightusercount').attr('disabled', 'disabled');
                            $('#unbuylightuser').attr('disabled', 'disabled');
                        }
                    } else {
                        $('#unbuylightuser').hide();
                    }
                    if (typeof data.customerinfo != 'undefined') {
                        $('#customercompany').val(data.name);
                        $('#customeremail').val(data.email);
                        $('#customerstreet').val(data.strasse);
                        $('#customername').val(data.ansprechpartner);
                        $('#customeraddress2').val(data.adresszusatz);
                        $('#customerzip').val(data.plz);
                        $('#customercity').val(data.ort);
                        $('#customercountry').val(data.land);
                        $('#customerbankname').val(data.inhaber);
                        $('#customerbank').val(data.bank);
                        $('#customerbic').val(data.swift);
                        $('#customeriban').val(data.iban);
                        //$('#customeruser').val(data.maxuser);
                        //$('#customerinfocontent').html(data.customerinfo);
                    }
                    if (data.data !== '') {
                        $('#buyedmodule').html(data.data);
                        $('#fieldsetmodules').show();
                    }
                }
            });
            $('#modalbeta').dialog(
                {
                    modal: true,
                    autoOpen: false,
                    width: '70%',
                    title: '',
                    buttons: {
                        'Ja ich möchte immer Zugriff auf die nächste Beta Version haben': function () {
                            $('#modalbeta').parent().loadingOverlay();
                            $.ajax({
                                url: 'index.php?module=appstore&action=buy&cmd=activatebeta',
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

            $('#modalunbuy').dialog(
                {
                    modal: true,
                    autoOpen: false,
                    width: '70%',
                    title: '',
                    buttons: {
                        Abbrechen: function () {
                            $(this).dialog('close');
                        },
                        'Kündigen': function () {
                            if (me.storage.actualType !== 'delete_module') {
                                me.storage.newValue = parseInt($('#unnewvalue').val());
                            }
                            if (me.storage.actualType === 'delete_module' || me.storage.actualType === 'delete_all' ||
                                me.storage.newValue > 0) {
                                if (confirm('Wirklich kündigen?')) {
                                    $('#modalunbuy').parent().loadingOverlay();
                                    $.ajax({
                                        url: 'index.php?module=appstore&action=buy&cmd=sendbuy',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {
                                            old: me.storage.oldValue,
                                            new: me.storage.actualType !== 'delete_module'
                                                ? me.storage.oldValue - me.storage.newValue : me.storage.newValue,
                                            field: me.storage.actualType
                                        },
                                        success: function (data) {
                                            if (typeof data.error != 'undefined') {
                                                alert(data.error);
                                                $('#modalunbuy').parent().loadingOverlay('remove');
                                                return;
                                            }
                                            if (typeof data.url != 'undefined') {
                                                me.updateKey();
                                            }
                                        },
                                        beforeSend: function () {

                                        },
                                        error: function () {
                                            $('#modalunbuy').parent().loadingOverlay('remove');
                                        }
                                    });
                                }
                            }
                        }
                    },
                    close: function (event, ui) {

                    }
                });

            $('#modalbuy').dialog(
                {
                    modal: true,
                    autoOpen: false,
                    width: '50%',
                    title: '',
                    buttons: {
                        Abbrechen: function () {
                            $(this).dialog('close');
                        },
                        Mieten: function () {
                            if (me.storage.actualType !== 'add_module') {
                                me.storage.newValue = parseInt($('#newvalue').val());
                            }
                            if (me.storage.actualType === 'add_module' || me.storage.newValue > 0) {
                                if (confirm('Wirklich für ' + (
                                    Number.parseFloat(me.storage.price
                                        * (me.storage.actualType === 'add_module' ? 1 : me.storage.newValue)).toFixed(2)
                                ) + ' EUR mieten?')) {
                                    $('#modalbuy').parent().loadingOverlay();
                                    $.ajax({
                                        url: 'index.php?module=appstore&action=buy&cmd=sendbuy',
                                        type: 'POST',
                                        dataType: 'json',
                                        data: {
                                            old: me.storage.oldValue,
                                            new: me.storage.actualType !== 'add_module'
                                                ? me.storage.oldValue + me.storage.newValue : me.storage.newValue,
                                            field: me.storage.actualType
                                        },
                                        success: function (data) {
                                            if (typeof data.error != 'undefined') {
                                                alert(data.error);
                                                $('#modalbuy').parent().loadingOverlay('remove');
                                                return;
                                            }
                                            if (typeof data.url != 'undefined') {
                                                me.updateKey();
                                            }
                                        },
                                        beforeSend: function () {

                                        },
                                        error: function () {
                                            $('#modalbuy').parent().loadingOverlay('remove');
                                        }
                                    });
                                }
                            }
                        }
                    },
                    close: function (event, ui) {

                    }
                });

            $('input.buybutton').on('click', function () {
                me.bindBuyButton(this);
            });
            $('a.buybutton').on('click', function () {
                me.bindBuyButton(this);
            });
            $('input.unbuybutton').on('click', function () {
                me.bindUnBuyButton($(this));
            });

            $('input.buybutton.autoopen').trigger('click');
        },
        bindBuyButton: function(element)
        {
            me.storage.oldValue = $(element).data('oldvalue');
            me.storage.newValue = $(element).data('newvalue');
            $('#newvalue').val(me.storage.newValue);
            me.storage.price = parseFloat($(element).data('price'));
            me.storage.actualType = $(element).data('type');
            if (me.storage.actualType === 'add_module') {
                $('#newvalue').hide();
            } else {
                $('#newvalue').show();
            }
            $('#modulbuytext').html($(element).data('info'));
            $('#modalbuy').dialog('open');
        }
    };
    return {
        init: me.init
    };

}(jQuery);

$(document).ready(function () {
    AppstoreBuy.init();
});

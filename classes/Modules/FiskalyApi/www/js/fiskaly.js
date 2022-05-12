fiskaly = (function ($) {
    self = {
        selector: {
            tseSelect: 'select[name=\'tse\']',
            clientSelect: 'select[name=\'client\']',
            organizationSelect: 'select[name=\'organization\']',
            trainingButton: '#training',
            createCashRegisterDiv: '#createcashregisterdiv',
            trCashRegister: '#tr-cash-register',
            tseForm: '#tseform',
            buttonNewOrg: '#create-org',
            popupNewOrg: '#popup-new-org',
            trSmaEndPoint: '#trsmaendpoint',
            tableOrg: '#fiskaly_organisation',
            formSetting: '#form-setting'
        },
        'hookTable': function () {
            let table = $('#fiskaly_pos_mapping').get(0);
            let observer = new MutationObserver(function (mutations) {
                self.hookButtons();
            });
            observer.observe(table, {attributes: true, childList: true, characterData: true});
        },
        'hookButtons': function () {
            var deleteButtons = document.getElementsByClassName('button-delete');
            console.log(deleteButtons);
            for (let button of deleteButtons) {
                button.addEventListener('click', function () {
                    if(!confirm('Zuordnung wirklich löschen?')) {
                        return;
                    }
                    let location = document.location;

                    let path = 'index.php?module=fiskaly&action=delete_tse';
                    let id = button.id.substr(7);
                    let request = new XMLHttpRequest();
                    request.open('POST', path, true);
                    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    request.onreadystatechange = function () {
                        if (this.readyState !== 4) {
                            return;
                        }
                        if (this.status === 200) {
                            document.location.reload();
                        } else {
                            alert('Beim löschen ist ein Fehler passiert.');
                        }
                    };
                    request.send('id=' + id);
                });
            }
        },
        'initExport': function () {
            let path = 'index.php?module=fiskaly&action=transaction_export&cmd=update_export';
            let request = new XMLHttpRequest();
            request.open('POST', path, true);
            request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            request.send();
        },
        'initPosList': function () {
            $(self.selector.trainingButton).on('change', function () {
                if ($(self.selector.trainingButton).prop('checked')) {
                    if (!confirm('Training Modus wirklich starten?')) {
                        $(self.selector.trainingButton).prop('checked', false);
                        return;
                    }
                    let path = 'index.php?module=pos&action=list&cmd=activate_training_modus';
                    let request = new XMLHttpRequest();
                    request.open('POST', path, true);
                    request.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                    request.onreadystatechange = function () {
                        if (this.readyState !== 4) {
                            return;
                        }
                        if (this.status === 200) {
                            document.location.reload();
                        } else {
                            alert('Fehler beim aktivieren des Training-modus.');
                        }
                    };
                    request.send();
                }
            });
        },
        'initTseOption': function () {
            $(self.selector.tseSelect).on('change', function () {
                if ($(self.selector.tseSelect).find('option:selected').filter(function () {
                    return $(this).data('status') === 'UNINITIALIZED';
                }).length > 0) {
                    $('#initialtse').toggleClass('hidden', false);
                    $('#createtse').toggleClass('hidden', true);
                    $('#createclient').toggleClass('hidden', true);
                } else {
                    $('#initialtse').toggleClass('hidden', true);
                    $('#createtse').toggleClass('hidden', false);
                    if ($(self.selector.tseSelect).find('option:selected').filter(function () {
                        return $(this).data('hasclient') == '0';
                    }).length > 0) {
                        $('#createclient').toggleClass('hidden', false);
                    } else {
                        $('#createclient').toggleClass('hidden', true);
                    }
                    var $clientsWithTssId = $(self.selector.clientSelect).find('option').filter(function () {
                        return $(this).data('tssid') === $(self.selector.tseSelect).val();
                    });
                    if ($clientsWithTssId.length === 1) {
                        $(self.selector.clientSelect).val($clientsWithTssId.val());
                    }
                }
                $(self.selector.clientSelect).find('option').each(function () {
                    if (
                        $(this).data('tssid') + '' !== ''
                        && $(this).data('tssid') !== $(self.selector.tseSelect).val()
                    ) {
                        $(this).hide();
                        if ($(self.selector.clientSelect).val() === $(this).val()) {
                            $(self.selector.clientSelect).val('');
                            $(self.selector.clientSelect).trigger('change');
                        }
                    } else {
                        $(this).show();
                    }
                });
            });
            $(self.selector.clientSelect).on('change', function () {
                if ($(this).find('option:selected').data('cashregisterid') + '' !== '') {
                    $(self.selector.createCashRegisterDiv).hide();
                    $(self.selector.trCashRegister).hide();
                    return;
                }
                if ($(this).val() + '' === '') {
                    $(self.selector.createCashRegisterDiv).hide();
                    $(self.selector.trCashRegister).hide();
                    return;
                }
                $(self.selector.createCashRegisterDiv).show();
                $(self.selector.trCashRegister).show();
                $(self.selector.createCashRegisterDiv).data('clientid', $(this).val());
            });
            $(self.selector.tseSelect).trigger('change');
            $(self.selector.clientSelect).trigger('change');
            $('#create-cashregister').on('click', function () {
                if (!confirm('Kasse wirklich Registrieren')) {
                    return;
                }
                $('#tabs').loadingOverlay('show');
                $.ajax({
                    url: 'index.php?module=fiskaly&action=settings_tse&cmd=createcashregister&id='
                        + $(self.selector.tseForm).data('tseid'),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        client_id: $(self.selector.createCashRegisterDiv).data('clientid')
                    },
                    success: function (data) {
                        if (data.status == 1) {
                            window.location.href = window.location.href.split('#')[0];
                        } else {
                            $('#tabs').loadingOverlay('remove');
                            alert(data.error);
                        }
                    },
                    error: function (event) {
                        $('#tabs').loadingOverlay('remove');
                        alert(event.responseJSON.error);
                    },
                    beforeSend: function () {

                    }
                });
            });
            $('#createtse').on('click', function (){
                if (!confirm('TSE wirklich erstellen?')) {
                    return;
                }
                $('#tabs').loadingOverlay('show');
                $.ajax({
                    url: 'index.php?module=fiskaly&action=settings_tse&cmd=createtse&id='
                        + $(self.selector.tseForm).data('tseid'),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        organization: $(self.selector.organizationSelect).val(),
                        project: $('#project').val()
                    },
                    success: function (data) {
                        if (data.status == 1) {
                            if(typeof data.url != 'undefined') {
                                window.location.href = data.url;
                                return;
                            }
                            window.location.href = window.location.href.split('#')[0];
                        } else {
                            $('#tabs').loadingOverlay('remove');
                            alert(data.error);
                        }
                    },
                    error: function (event) {
                        $('#tabs').loadingOverlay('remove');
                        alert(event.responseJSON.error);
                    },
                    beforeSend: function () {

                    }
                });
            });
            $('#initialtse').on('click', function () {
                if (!confirm('TSE wirklich initialisieren?')) {
                    return;
                }
                $('#tabs').loadingOverlay('show');
                $.ajax({
                    url: 'index.php?module=fiskaly&action=settings_tse&cmd=initialtse&id='
                        + $(self.selector.tseForm).data('tseid'),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        tse_id: $(self.selector.tseSelect).val()
                    },
                    success: function (data) {
                        if (data.status == 1) {
                            window.location.href = window.location.href.split('#')[0];
                        } else {
                            $('#tabs').loadingOverlay('remove');
                            alert(data.error);
                        }
                    },
                    error: function (event) {
                        $('#tabs').loadingOverlay('remove');
                        alert(event.responseJSON.error);
                    },
                    beforeSend: function () {

                    }
                });
            });
            $('#createclient').on('click', function () {
                if (!confirm('Client wirklich erstellen?')) {
                    return;
                }
                $('#tabs').loadingOverlay('show');
                $.ajax({
                    url: 'index.php?module=fiskaly&action=settings_tse&cmd=createclient&id='
                        + $(self.selector.tseForm).data('tseid'),
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        tse_id: $(self.selector.tseSelect).val()
                    },
                    success: function (data) {
                        if (data.status == 1) {
                            window.location.href = window.location.href.split('#')[0];
                        } else {
                            $('#tabs').loadingOverlay('remove');
                            alert(data.error);
                        }
                    },
                    error: function (event) {
                        $('#tabs').loadingOverlay('remove');
                        alert(event.responseJSON.error);
                    },
                    beforeSend: function () {

                    }
                });
            });
        },
        'editOrg': function (id) {
            $('#tabs').loadingOverlay('show');
            $.ajax({
                url: 'index.php?module=fiskaly&action=settings&cmd=getorg',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: id
                },
                success: function (data) {
                    $('#org-id').val(data.id);
                    $('#org-name').val(data.name);
                    $('#org-display-name').val(data.display_name);
                    $('#org-address-line1').val(data.address_line1);
                    $('#org-address-line2').val(data.address_line2);
                    $('#org-zip').val(data.zip);
                    $('#org-town').val(data.town);
                    $('#org-state').val(data.state);
                    $('#org-vat-id').val(data.vat_id);
                    $('#org-tax-number').val(data.tax_number);
                    $('#org-economy-id').val(data.economy_id);
                    $('#org-country-code').val(data.country_code);
                    $('#tabs').loadingOverlay('remove');
                    $(self.selector.popupNewOrg).dialog('open');
                },
                error: function (event) {
                    $('#tabs').loadingOverlay('remove');
                    alert(event.responseJSON.error);
                }
            });
        },
        'initSettings': function () {
            $(self.selector.tableOrg).on('afterreload',function (){
               $(self.selector.tableOrg).find('img.edit').on('click', function (){
                   self.editOrg($(this).data('id'));
               });
            });
            $(self.selector.buttonNewOrg).on('click', function (){
                self.editOrg(0);
            });
            $(self.selector.popupNewOrg).dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title:'',
                    buttons: {
                        'ABBRECHEN': function() {
                            $(this).dialog('close');
                        },
                        'ANLEGEN / SPEICHERN': function()
                        {
                            if(!confirm('Wirklich Anlegen / Erstellen?')) {
                                return;
                            }
                            $(self.selector.popupNewOrg).loadingOverlay('show');
                            $.ajax({
                                url: 'index.php?module=fiskaly&action=settings&cmd=createorg',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id: $('#org-id').val(),
                                    name: $('#org-name').val(),
                                    display_name: $('#org-display-name').val(),
                                    address_line1: $('#org-address-line1').val(),
                                    address_line2: $('#org-address-line2').val(),
                                    zip: $('#org-zip').val(),
                                    town: $('#org-town').val(),
                                    state: $('#org-state').val(),
                                    vat_id: $('#org-vat-id').val(),
                                    tax_number: $('#org-tax-number').val(),
                                    economy_id: $('#org-economy-id').val(),
                                    country_code: $('#org-country-code').val(),
                                },
                                success: function() {
                                    window.location.href = window.location.href.split('#')[ 0 ];
                                },
                                error: function (event) {
                                    $(self.selector.popupNewOrg).loadingOverlay('remove');
                                    alert(event.responseJSON.error);
                                }
                            });

                        },
                    },
                    close: function(event, ui){

                    }
                });
            $(self.selector.tableOrg).trigger('afterreload');
        },
        'init': function () {
            $(document).ready(self.hookTable);
            if ($(self.selector.tseSelect).length) {
                self.initTseOption();
            }
            if ($(self.selector.trainingButton).length) {
                self.initPosList();
            }
            if ($('#fiskaly_transaction_export').length) {
                self.initExport();
            }
            if($(self.selector.popupNewOrg).length) {
                self.initSettings();
            }
        }
    };
    return {
        'init': self.init
    };
})($);

fiskaly.init();

var ApiAccount = function ($) {
    'use strict';

    var me = {
        storage: {
            actualId: null
        },
        selector: {
            listTable: '#api_account_list',
            editPopup: '#apiAccountPopup'
        },
        updateLiveTable: function () {
            $('#api_account_list').DataTable().ajax.reload();
        },
        open: function (id) {
            me.storage.actualId = id;

            $.ajax({
                url: 'index.php?module=api_account&action=list&cmd=get',
                type: 'POST',
                dataType: 'json',
                data: {
                    id: me.storage.actualId
                },
                success: function (data) {
                    $('#api-account-id').text(data.id);
                    $('#aktiv').prop('checked', data.aktiv === '1');
                    $('#bezeichnung').val(data.bezeichnung);
                    $('#projekt').val(data.projekt);
                    $('#remotedomain').val(data.remotedomain);
                    $('#initkey').val(data.initkey);
                    $('#apitempkey').html(data.apitempkey);
                    $('#event_url').val(data.event_url);
                    $('#importwarteschlange_name').val(data.importwarteschlange_name);
                    $('#importwarteschlange').prop('checked', data.importwarteschlange === '1');
                    $('#cleanutf8').prop('checked', data.cleanutf8==='1');
                    $('#ishtmltransformation').prop('checked', data.ishtmltransformation === '1');
                    $('#apiAccountPopup').dialog('open');
                    var permissions = JSON.parse(data.permissions);
                    $('.permission-checkbox').each(function () {
                        var self = $(this)
                        if(permissions.indexOf(self.attr('name')) > -1){
                            self.prop('checked', true)
                        }
                    })
                },
                error: function (e) {
                    if (typeof e.responseJSON !== 'undefined' && typeof e.responseJSON.error !== 'undefined') {
                        alert(e.responseJSON.error);
                    }
                },
                beforeSend: function () {

                }
            });
        },
        init: function () {
            if ($(me.selector.editPopup).length === 0) {
                return;
            }
            $('#submenu-wrapper div.new a').on('click', function () {
                me.open(0);
            });
            $(me.selector.listTable).on('afterreload', function () {
                $('#api_account_list .get').on('click', function () {
                    me.open($(this).data('id'));
                });
            });
            $(me.selector.listTable).trigger('afterreload');
            $(me.selector.editPopup).dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title: '',
                    buttons: {
                        'SPEICHERN': function () {
                            var permissions = {};
                            $('.permission-checkbox').each(function () {
                                var self = $(this)
                                permissions[self.attr('name')] = self.is(':checked')
                            })

                            $.ajax({
                                url: 'index.php?module=api_account&action=list&cmd=save',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    id: me.storage.actualId,
                                    aktiv: $('#aktiv').prop('checked') ? 1 : 0,
                                    bezeichnung: $('#bezeichnung').val(),
                                    projekt: $('#projekt').val(),
                                    remotedomain: $('#remotedomain').val(),
                                    initkey: $('#initkey').val(),
                                    event_url: $('#event_url').val(),
                                    importwarteschlange_name: $('#importwarteschlange_name').val(),
                                    importwarteschlange: $('#importwarteschlange').prop('checked') ? 1 : 0,
                                    cleanutf8: $('#cleanutf8').prop('checked') ? 1 : 0,
                                    ishtmltransformation: $('#ishtmltransformation').prop('checked') ? 1 : 0,
                                    api_permissions: permissions,

                                },
                                success: function () {
                                    me.storage.actualId = null;
                                    me.updateLiveTable();
                                    $(me.selector.editPopup).dialog('close');
                                },
                                error: function (e) {
                                    if (typeof e.responseJSON !== 'undefined' && typeof e.responseJSON.error !==
                                        'undefined') {
                                        alert(e.responseJSON.error);
                                    }
                                },
                                beforeSend: function () {

                                }
                            });
                        },
                        'ABBRECHEN': function () {
                            $(this).dialog('close');
                            me.storage.actualId = null;
                        }
                    },
                    close: function (event, ui) {

                    }
                });
            $(me.selector.editPopup).toggleClass('hidden', false);
        }
    };
    return {
        init: me.init
    };
}(jQuery);

$(document).ready(function () {
    ApiAccount.init();
});

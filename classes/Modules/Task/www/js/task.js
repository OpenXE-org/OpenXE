var Task = function ($) {
    'use strict';

    var me = {
        storage: {
            projectShortCut: null,
            lastHistoryKeyCode: null
        },
        selector: {
            createProjectPopup: '#createprojectpopup',
            createSubProjectPopup: '#createsubprojectpopup',
            createProjectButton: '#createproject',
            createSubProjectButton: '#createsubproject',
            createProjectTable: '#task_project_create_employee',
            createSubProjectTable: '#task_subproject_create_employee'
        },
        openCreateProjectPopup: function () {
            if ($(me.selector.createProjectPopup).length === 0) {
                return;
            }
            $('#projecttitle').val('');
            $('#projectshortcode').val('');
            $('#projectcustomer').val('');
            $('#projectleader').val('');
            $('#projectdescription').val('');
            $('#projectstatus').val('gestartet');
            $(me.selector.createProjectTable).on('afterreload', function () {
                $(me.selector.createProjectTable).find('.projectcreateemployee').on('change', function () {
                    $.ajax({
                        url: 'index.php?module=aufgaben&action=list&cmd=changeemployee',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            address_id: $(this).data('id'),
                            value: $(this).prop('checked') ? 1 : 0
                        },
                        success: function () {

                        }
                    });
                });
            });
            $(me.selector.createProjectTable).trigger('afterreload');
            $(me.selector.createProjectPopup).dialog('open');
        },
        subProjectProjectValChanged: function () {
            if ($('#subprojectproject').val() + '' === '') {
                $('#subprojectparent').html('');
                $(me.selector.createSubProjectTable).DataTable().ajax.reload();
                return;
            }
            $.ajax({
                url: 'index.php?module=aufgaben&action=list&cmd=getsubprojectsbyproject',
                type: 'POST',
                dataType: 'json',
                data: {
                    projectshortcode: $('#subprojectproject').val()
                },
                success: function (data) {
                    $('#subprojectparent').html(data.html);
                    $(me.selector.createSubProjectTable).DataTable().ajax.reload();
                }
            });
        },
        openCreateSubProjectPopup: function () {
            if ($(me.selector.createSubProjectPopup).length === 0) {
                return;
            }
            $('#subprojecttitle').val('');
            $('#subprojectdescription').val('');
            $('#subprojectstartdate').val('');
            $('#subprojectenddate').val('');
            $('#subprojectproject').val(me.storage.projectShortCut);
            $('#subprojectproject').trigger('change');
            $(me.selector.createSubProjectPopup).dialog('open');
        },
        dateTimeRewrite: function (value) {
            if (value === null) {
                return null;
            }
            value.forEach(function (item, index) {
                value[index].time = new Date(value[index].time.replace(' ', 'T'));
            });
            return value;
        },
        init: function () {
            if ($(me.selector.createProjectPopup).length > 0) {
                $(me.selector.createProjectPopup).dialog({
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title: '',
                    buttons: {
                        'ABBRECHEN': function () {
                            $(this).dialog('close');
                        },
                        'ANLEGEN': function () {
                            $.ajax({
                                url: 'index.php?module=aufgaben&action=list&cmd=createproject&fromjson=1',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    schritt3: 1,
                                    save: 1,
                                    name: $(me.selector.createProjectPopup).find('#projecttitle').val(),
                                    abkuerzung: $(me.selector.createProjectPopup).find('#projectshortcode').val(),
                                    typ: 'manuell',
                                    kunde: $(me.selector.createProjectPopup).find('#projectcustomer').val(),
                                    verantwortlicher: $(me.selector.createProjectPopup).find('#projectleader').val(),
                                    beschreibung: $(me.selector.createProjectPopup).find('#projectdescription').val(),
                                    status: $(me.selector.createProjectPopup).find('#projectstatus').val(),
                                    farbe: $(me.selector.createProjectPopup).find('#projectcolor').val()
                                },
                                success: function (data) {
                                    if (typeof data.error != 'undefined') {
                                        $('#createprojectmessage').html('<div class="error">' + data.error + '</div>');
                                    }
                                    if (typeof data.id != 'undefined' && data.id > 0) {
                                        $(me.selector.createProjectPopup).dialog('close');
                                    }
                                }
                            });

                        }
                    },
                    close: function (event, ui) {

                    }
                });
            }
            if ($(me.selector.createSubProjectPopup).length > 0) {
                $(me.selector.createSubProjectPopup).dialog({
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title: '',
                    buttons: {
                        'ABBRECHEN': function () {
                            $(this).dialog('close');
                        },
                        'ANLEGEN': function () {
                            $.ajax({
                                url: 'index.php?module=aufgaben&action=list&cmd=createsubproject',
                                type: 'POST',
                                dataType: 'json',
                                data: {
                                    project: $('#subprojectproject').val(),
                                    title: $('#subprojecttitle').val(),
                                    description: $('#subprojectdescription').val(),
                                    color: $('#subprojectcolor').val(),
                                    leader: $('#subprojectleader').val(),
                                    parentid: $('#subprojectparent').val(),
                                    startdate: $('#subprojectstartdate').val(),
                                    enddate: $('#subprojectenddate').val(),
                                    positiontype: $('#subprojectposition').val(),
                                    status: $('#subprojectstatus').val()
                                },
                                success: function (data) {
                                    if (typeof data.error != 'undefined') {
                                        $('#createsubprojectmessage').html(
                                            '<div class="error">' + data.error + '</div>');
                                    }
                                    if (typeof data.id != 'undefined' && data.id > 0) {
                                        $(me.selector.createSubProjectPopup).dialog('close');
                                    }
                                }
                            });
                        }
                    },
                    close: function (event, ui) {

                    }
                });
            }
            $(me.selector.createProjectPopup).find('#projecttitle').on('keyup', function () {
                me.storage.projectShortCut = ($(this).val() + '').toUpperCase().replace(/[^A-Z0-9]/g, '').substring(0,
                    5);
                $(me.selector.createProjectPopup).find('#projectshortcode').val(me.storage.projectShortCut);
                if (me.storage.projectShortCut.length === 5) {
                    $.ajax({
                        url: 'index.php?module=aufgaben&action=list&cmd=getprojectshortcode',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            projectshortcode: me.storage.projectShortCut
                        },
                        success: function (data) {
                            me.storage.projectShortCut = data.projectshortcode;
                            $(me.selector.createProjectPopup).find('#projectshortcode').val(me.storage.projectShortCut);
                        }
                    });
                }

            });
            $(me.selector.createProjectButton).on('click', function () {
                me.openCreateProjectPopup();
            });
            $(me.selector.createSubProjectButton).on('click', function () {
                me.openCreateSubProjectPopup();
            });
            $('#subprojectproject').on('change', function () {
                me.subProjectProjectValChanged();
            });
            $('#subprojectproject').on('blur', function () {
                me.subProjectProjectValChanged();
            });
            $(me.selector.createSubProjectTable).on('afterreload', function () {
                $(me.selector.createSubProjectTable).find('.subprojectemployee').on('change', function () {
                    var $tr = $(this).parents('tr').first();
                    $.ajax({
                        url: 'index.php?module=aufgaben&action=list&cmd=changesubprojectemployee',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            address_id: $(this).data('id'),
                            hours: $($tr).find('.hours').val(),
                            hourlyrate: $($tr).find('.hourlyrate').val(),
                            title: $($tr).find('.title').val()
                        },
                        success: function () {

                        }
                    });
                });
            });
            $('#tasks-tabs').tabs(
                {
                    active: 0
                }
            );
        },
        addTimeline: function (timelines) {
            if ($('#element ul.timeline').length) {
                $('#element').html('');
                $('#element').timeline('destroy', [
                    {
                        key: 'value'
                    }
                ]);
            }
            if (timelines !== null) {
                $('#element').timeline({
                    data: me.dateTimeRewrite(timelines)
                });
                $('#element .timeline-buttons .EditTimeLineBtn').remove();
                $('#element .timeline-buttons .DeleteTimeLineBtn').remove();
                $('#element .timeline-buttons .SaveTimeLineBtn').removeAttr('onclick');
                var $next = $('.tl-item').first().nextAll();
                if ($($next).length > 0) {
                    $($next).find('.SaveTimeLineBtn').remove();
                }
                $('.tl-item').first().find('.SaveTimeLineBtn').on('click', function () {
                    if (trim(($('#editTimeline_0').val() + '')) === '') {
                        return;
                    }
                    $.ajax({
                        url: 'index.php?module=aufgaben&action=edit&cmd=addtotimeline',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            task_id: $('#editAufgaben').find('#e_id').val(),
                            text: $('#editTimeline_0').val()
                        },
                        success: function (data) {
                            me.addTimeline(data.timeline);
                        }
                    });
                });
                $('#editTimeline_0').val('');

                $('.tl-item').first().find('textarea').show();
                $('.tl-item').first().find('textarea').on('keyup', function (event) {
                    if (typeof event.keyCode === 'undefined') {
                        return;
                    }
                    if (me.storage.lastHistoryKeyCode === 81 && event.keyCode === 225) {

                    }
                    me.storage.lastHistoryKeyCode = event.keyCode;
                    if (event.keyCode === 27) {
                        $(this).val('');
                        me.storage.lastHistoryKeyCode = null;
                        return;
                    }
                    if (event.keyCode === 13) {
                        if (event.shiftKey === true) {
                            return;
                        }
                        me.storage.lastHistoryKeyCode = null;
                    }
                });
            }
        }
    };

    return {
        init: me.init,
        dateTimeRewrite: me.dateTimeRewrite,
        addTimeline: me.addTimeline
    };

}(jQuery);

$(document).ready(function () {
    Task.init();
});

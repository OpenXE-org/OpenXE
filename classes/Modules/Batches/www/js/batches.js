$(document).on('ready',function() {
   // setTimeout(function() {
    $('#batches_overview').on('afterreload', function(){
        $(this).find('input.createfirstbatch').on('click', function(){
            $.ajax({
                url: 'index.php?module=batches&action=overview&cmd=createfirstbatch',
                dataType: 'json',
                type: 'POST',
                data: {
                    id: $(this).data('id')
                },
                success: function (data) {
                    $('#batches_overview').DataTable( ).ajax.reload();
                }
            });
        });
        $(this).find('input.createbatches').on('click', function(){
            $.ajax({
                url: 'index.php?module=batches&action=overview&cmd=createbatches',
                dataType: 'json',
                type: 'POST',
                data: {
                    id: $(this).data('id')
                },
                success: function (data) {
                    $('#batches_overview').DataTable( ).ajax.reload();
                }
            });
        });
    });
    $('#batches_queue').on('afterreload', function() {
        $(this).find('img.deletebatch').on('click', function () {
            if(confirm('Batch wirklich löschen?')) {
                $.ajax({
                    url: 'index.php?module=batches&action=overview&cmd=deletebatch',
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        id: $(this).data('id')
                    },
                    success: function (data) {
                        $('#batches_queue').DataTable().ajax.reload();
                    }
                });
            }
        });
    });

    $('#calcbatches').on('click', function(){
        $('#tabs').loadingOverlay('show');
        $.ajax({
            url: 'index.php?module=batches&action=overview&cmd=calcbatches',
            dataType: 'json',
            type: 'POST',
            data: {
                batchid: $('#batchid').val()
            },
            success: function (data) {
                window.location = 'index.php?module=batches&action=overview';
            }
        });
    });

        $.each(
            [
                'project','article','attribute','freefield','group',
                'shop','payment','shipping','articlecategory','deliverycountry','storagelocation',
                'time'
            ],
            function(k, field)
            {
                setTimeout(function() {
                    $('#batches_rule_'+field+'_length').after(
                        '<img alt="Filter hinzuf&uuml;gen" src="./themes/new/images/icons_neu_klein.png" class="addfilter" data-type="'+field+'" />'
                    );
                    $('#batches_rule_'+field+'_wrapper .addfilter').on('click',function(){
                        $.ajax({
                            url: 'index.php?module=batches&action=edit&cmd=addfilter&id='+$('#name').data('id'),
                            dataType: 'json',
                            type: 'POST',
                            data: {
                                type: $(this).data('type')
                            },
                            success: function (data) {
                                $('#batches_rule_' + data.type).DataTable( ).ajax.reload();
                            }
                        });
                    });
                }, 1000);

                $('#batches_rule_'+field).on('afterreload',function(){
                    $('#'+this.id+' img.delefilter').on('click',function(){
                        if(confirm('Eintrag wirklich löschen?')) {
                            $.ajax({
                                url: 'index.php?module=batches&action=edit&cmd=deletefilter',
                                dataType: 'json',
                                type: 'POST',
                                data: {
                                    id: $(this).data('id')
                                },
                                success: function (data) {
                                    var oTable = $('#batches_rule_' + data.field).DataTable();
                                    oTable.ajax.reload();
                                }
                            });
                        }
                    });
                    $('#'+this.id+' input.active').on('change',function(){
                        var active = 0;
                        if ($(this).prop('checked') === true) {
                            active = 1;
                        }
                        $.ajax({
                            url: 'index.php?module=batches&action=edit&cmd=changeactive',
                            dataType: 'json',
                            type: 'POST',
                            data: {
                                id: $(this).data('id'),
                                value: active
                            },
                            success: function (data) {

                            }
                        });
                    });
                    $('#'+this.id+' input.filter').on('change',function(){
                        console.log('#'+this.id+' input.filter');
                        $.ajax({
                            url: 'index.php?module=batches&action=edit&cmd=changefilter',
                            dataType: 'json',
                            type: 'POST',
                            data: {
                                id: $(this).data('id'),
                                value: $(this).val()
                            },
                            success: function (data) {

                            }
                        });
                    });
                    $('#'+this.id+' select.filter').on('change',function(){
                        $.ajax({
                            url: 'index.php?module=batches&action=edit&cmd=changefilter',
                            dataType: 'json',
                            type: 'POST',
                            data: {
                                id: $(this).data('id'),
                                value: $(this).val()
                            },
                            success: function (data) {

                            }
                        });
                    });
                    $('#'+this.id+' input.filter2').on('change',function(){
                        $.ajax({
                            url: 'index.php?module=batches&action=edit&cmd=changefilter2',
                            dataType: 'json',
                            type: 'POST',
                            data: {
                                id: $(this).data('id'),
                                value: $(this).val()
                            },
                            success: function (data) {

                            }
                        });
                    });
                    if(typeof this.id != 'undefined') {
                        var tabletype = this.id.substr(13);

                        if(tabletype === 'project') {
                            $( '#'+this.id+' input.filter' ).autocomplete({
                                source: "index.php?module=ajax&action=filter&rmodule=batches&raction=edit&rid=&filtername=projektname",
                                select: function( event, ui ) {
                                    var i = ui.item.value;
                                    var zahl = i.indexOf(' ');
                                    var text = i.slice(0, zahl);
                                    $( this ).val( text );
                                    return false;
                                }
                            });
                            return;
                        }
                        if(tabletype === 'article')
                        {
                            $( '#'+this.id+' input.filter' ).autocomplete({
                                source: "index.php?module=ajax&action=filter&rmodule=batches&raction=edit&rid=&filtername=artikelnummer",
                                select: function( event, ui ) {
                                    var i = ui.item.value;
                                    var zahl = i.indexOf(' ');
                                    var text = i.slice(0, zahl);
                                    $( this ).val( text );
                                    return false;
                                }
                            });
                            return;
                        }
                        if(tabletype === 'shop')
                        {
                            $( '#'+this.id+' input.filter' ).autocomplete({
                                source: "index.php?module=ajax&action=filter&rmodule=batches&raction=edit&rid=&filtername=shopnameid",
                            });
                            return;
                        }
                        if(tabletype === 'shipping')
                        {
                            $( '#'+this.id+' input.filter' ).autocomplete({
                                source: "index.php?module=ajax&action=filter&rmodule=batches&raction=edit&rid=&filtername=versandartentype",
                            });
                            return;
                        }
                        if(tabletype === 'payment')
                        {
                            $( '#'+this.id+' input.filter' ).autocomplete({
                                source: "index.php?module=ajax&action=filter&rmodule=batches&raction=edit&rid=&filtername=zahlungsweisetype",
                            });
                            return;
                        }
                        if(tabletype === 'group')
                        {
                            $( '#'+this.id+' input.filter' ).autocomplete({
                                source: "index.php?module=ajax&action=filter&rmodule=batches&raction=edit&rid=&filtername=gruppekennziffer",
                                select: function( event, ui ) {
                                    var i = ui.item.value;
                                    var zahl = i.indexOf(' ');
                                    var text = i.slice(0, zahl);
                                    $( this ).val( text );
                                    return false;
                                }
                            });
                            return;
                        }
                        if(tabletype === 'storagelocation')
                        {
                            $( '#'+this.id+' input.filter' ).autocomplete({
                                source: "index.php?module=ajax&action=filter&rmodule=batches&raction=edit&rid=&filtername=lagerplatz"
                            });
                            return;
                        }
                        if(tabletype === 'articlecategory') {

                            $( '#'+this.id+' input.filter' ).autocomplete({
                                source: "index.php?module=ajax&action=filter&rmodule=batches&raction=edit&rid=&filtername=artikelkategorien",
                                select: function( event, ui ) {
                                    var i = ui.item.value;
                                    var zahl = i.indexOf(' ');
                                    if(zahl > 0) {
                                        var text = i.slice(0, zahl);
                                        $(this).val(text);
                                        return false;
                                    }
                                }
                            });
                            return;
                        }
                        if(tabletype === 'time') {
                            $( '#'+this.id+' input.filter' ).timepicker();
                        }
                        if(tabletype === 'attribute')
                        {
                            $( '#'+this.id+' input.filter' ).autocomplete({
                                source: "index.php?module=ajax&action=filter&rmodule=batches&raction=edit&rid=&filtername=eigenschaftname",
                            });

                            $('#'+this.id+' input.filter2').each(function(){
                                var elid = $(this).data('id');
                                $(this).autocomplete({
                                    source: "index.php?module=ajax&action=filter&rmodule=batches&raction=edit&rid=&filtername=eigenschaftwert&eigenschaftname="+
                                        encodeURI($('input.filter2[data-id='+elid+']').val()),
                                });
                            });
                            return;
                        }
                    }
                });
            });
});


/**
 * Für die Bedienung der Modul-Oberfläche
 */
var editFilter = (function ($) {
    'use strict';

    var me = {
        $editDialog: null,

        /**
         * @return void
         */
        init: function () {
            me.registerEvents();
        },

        openDialog: function() {
            me.$editDialog.dialog('open');
        },

        closeDialog: function() {
            me.$editDialog.dialog('close');
        },

        updateShortText: function(id, filterType) {
            id = parseInt(id);
            if (isNaN(id) || id <= 0) {
                return;
            }
            $.ajax({
                url: 'index.php?module=batches&action=edit&cmd=filtertext',
                data: {
                    id: id,
                    filtertype: filterType
                },
                method: 'post',
                dataType: 'json',
                beforeSend: function () {
                    App.loading.open();
                },
                success: function (data) {
                    $('#displayFilterShort_' + data.filtertype).val(data.value);
                }
            });
        },

        // /**
        //  * @return {void}
        //  */
        initDialog: function (filterType) {

            me.$editDialog = $('#dialogEditFilter_' + filterType);

            me.$editDialog.dialog({
                modal: true,
                bgiframe: true,
                closeOnEscape: false,
                minWidth: 500,
                minHeight: 420,
                maxHeight: 700,
                autoOpen: false,
                buttons: [{
                    text: 'Speichern',
                    click: function () {
                        me.closeDialog();
                    }
                }],
                open: function () {},
                close: function () {
                    me.updateShortText($(this).data('id'), $(this).data('filtertype'));
                }
            });
        },

        // /**
        //  * @return {void}
        //  */
        registerEvents: function () {
            $(document).on('click', 'a.filter-edit', function (e) {
                e.preventDefault();
                var filter = $(this).data('filtertype');
                me.initDialog(filter);
                me.openDialog();
            });
        }
    };

    return {
        init: me.init
    };

})(jQuery);


$(document).ready(function () {
    editFilter.init();
});
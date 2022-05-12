var TicketTemplate = function ($) {
    "use strict";

    var me = {
        storage: {
          api : null,
        },
        selector: {
            overviewTable: '#ticket_vorlagenlist',
            itemPopup: '#tickettemplatepopup',
            newButton: 'input#newtemplate',
        },
        updateOverviewTable: function() {
          $(me.selector.overviewTable).DataTable().ajax.reload();
        },
        saveItem: function()
        {
            $.ajax({
                type: 'POST',
                url: 'index.php?module=ticket_vorlage&action=list&cmd=saveitem',
                data: {
                    itemId: $('#itemid').val(),
                    name:$('#itemname').val(),
                    text:$('#itemtext').val(),
                    project:$('#itemproject').val(),
                    category:$('#itemcategory').val(),
                    sort:$('#itemsort').val(),
                    visible:$('#itemvisible').prop('checked')?1:0,
                },
                success: function () {
                    $(me.selector.itemPopup).dialog('close');
                    me.updateOverviewTable();
                }
            });
        },
        resetItem: function()
        {
            $('#itemid').val('');
            $('#itemname').val('');
            $('#itemtext').val('');
            $('#itemsort').val('');
            $('#itemcategory').val('');
            $('#itemvisible').prop('checked', false);
        },

        getItem: function(itemId)
        {
            $.ajax({
                type: 'POST',
                url: 'index.php?module=ticket_vorlage&action=list&cmd=getitem',
                data: {
                    itemId: itemId,
                    categoryId: $('#categoryid').val(),
                    name:$('#newtemplatename').val()
                },
                success: function (data) {
                    if(typeof data.id == 'undefined') {
                        me.resetItem();
                        $(me.selector.itemPopup).dialog('open');
                        return;
                    }
                    $('#itemid').val(data.id);
                    $('#itemname').val(data.name);
                    $('#itemtext').val(data.text);
                    $('#itemsort').val(data.sort);
                    $('#itemcategory').val(data.category);
                    $('#itemvisible').prop('checked', data.visible == '1');
                    $(me.selector.itemPopup).dialog('open');
                }
            });
        },

        initTree: function() {
            $('#mlmTree').aciTree({
                autoInit: false,
                checkboxChain: false,
                ajax: {
                    url: 'index.php?module=ticket_vorlage&action=list&cmd=gettree'
                },
                checkbox: true,
                itemHook: function(parent, item, itemData, level) {
                    //console.log(itemData);
                },
                filterHook: function(item, search, regexp) {

                    if (search.length) {
                        var parent = this.parent(item);

                        if (parent.length) {
                            var label = this.getLabel(parent);
                            if (regexp.test(String(label))) {
                                this.setVisible(item);
                                return true;
                            }
                            this.setVisible(item);
                        }

                        if (regexp.test(String(this.getLabel(item)))) {
                            item.addClass('searched');
                            return true;
                        } else {
                            return false;
                        }

                        //return regexp.test(String(this.getLabel(item)));
                    } else {
                        return true;
                    }
                }
            });
        },


        initOverview: function() {
            $(me.selector.newButton).on('click',function () {
                me.getItem(0);
            });
            $(me.selector.overviewTable).on('afterreload',function() {
               $(this).find('img.deleteitem').on('click', function() {
                  if(confirm('Wirklich löschen?')) {
                      $.ajax({
                          type: 'POST',
                          url: 'index.php?module=ticket_vorlage&action=list&cmd=deleteitem',
                          data: {
                              itemId: $(this).data('id')
                          },
                          success: function () {
                              me.updateOverviewTable();
                          }
                      });
                  }
               });
               $(this).find('img.edititem').on('click', function() {
                   me.getItem($(this).data('id'));
               });
            });

            $(me.selector.itemPopup).toggleClass('hidden', false);
            $(me.selector.itemPopup).dialog(
                {
                    modal: true,
                    autoOpen: false,
                    minWidth: 940,
                    title:'',
                    buttons: {
                        'ABBRECHEN': function() {
                            $(this).dialog('close');
                        },
                        'SPEICHERN': function()
                        {
                            me.saveItem();
                        },
                    },
                    close: function(event, ui){

                    }
                });

            $('#savesubcategory').on('click',function(){
                $.ajax({
                    type: 'POST',
                    url: 'index.php?module=ticket_vorlage&action=list&cmd=savenewsubcategory',
                    data: {
                        categoryId: $('#categoryid').val(),
                        name: $('#subcategoriename').val(),
                    },
                    success: function () {
                        $('#mlmTree').aciTree('api').unload(null, {
                            success: function() {
                                this.ajaxLoad(null);
                            }
                        });
                        me.updateOverviewTable();
                        $('#subcategoriename').val('');
                    }
                });
            });
            $('#savecategory').on('click',function(){
                $.ajax({
                    type: 'POST',
                    url: 'index.php?module=ticket_vorlage&action=list&cmd=savecategory',
                    data: {
                        categoryId: $('#categoryid').val(),
                        name: $('#categoriename').val(),
                    },
                    success: function () {
                        $('#mlmTree').aciTree('api').unload(null, {
                            success: function() {
                                this.ajaxLoad(null);
                            }
                        });
                        me.updateOverviewTable();
                    }
                });
            });

            $('#deletecategory').on('click',function(){
               if(!confirm('Wirklich löschen?')) {
                   return;
               }
                $.ajax({
                    type: 'POST',
                    url: 'index.php?module=ticket_vorlage&action=list&cmd=deletecategory',
                    data: {
                        categoryId: $('#categoryid').val()
                    },
                    success: function () {
                        $('#categoryid').val('');
                        $('#categoriename').val('');
                        $('#mlmTree').aciTree('api').unload(null, {
                            success: function() {
                                this.ajaxLoad(null);
                            }
                        });

                        me.updateOverviewTable();
                    }
                });
            });

            me.initTree();


            me.storage.api = $('#mlmTree').aciTree('api');


            $('#search').val('');
            var last = '';

            $('#search').on('keyup', function() {
                if ($(this).val() === last) {
                    return;
                }

                $('.aciTreeLi').removeClass('searched');

                last = $(this).val();
                me.storage.api.filter(null, {
                    search: $(this).val(),
                    callback: function() {

                    },
                    success: function(item, options) {

                        if (!options.first) {
                            //alert('No results found!');
                        }
                    }
                });
            });


            $('#mlmTree').on('acitree', function(event, api, item, eventName, options){
                switch (eventName){
                    case 'checked':
                        break;
                    case 'unchecked':
                        break;
                    case 'selected':

                        var ajaxData = {
                            id: api.getId(item),
                            name: api.getLabel(item)
                        }

                        $.ajax({
                            url: 'index.php?module=ticket_vorlage&action=list&cmd=gettreedetail',
                            data: ajaxData,
                            type: 'POST',
                            dataType: 'json',
                            success: function(data) {
                                $('#categoryid').val(data.id);
                                $('#categoriename').val(data.name);
                                me.updateOverviewTable();
                            }
                        });

                        break;
                    default:
                        if (api.isItem(item)){
                            //console.log('the event is: ' + eventName + ' for the item ID: ' + api.getId(item));
                        } else {
                            //console.log('the event is: ' + eventName + ' for the tree ROOT');
                        }
                }
            });

            $('#mlmTree').aciTree('init');
        },

        init: function () {
            if($(me.selector.overviewTable).length) {
                me.initOverview();
            }
        }
    };
    return {
        init: me.init,

    };

}(jQuery);

$(document).ready(function () {
    TicketTemplate.init();
});

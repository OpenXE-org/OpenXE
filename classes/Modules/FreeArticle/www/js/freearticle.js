$(document).ready(function() {
    $('#free_article_article').focus();

    $(document).on('click', '.freearticle-edit', function(e){
        e.preventDefault();

        var labelId = $(this).data('freearticle-id');
        FreeArticleEdit(labelId);
    });

    $(document).on('click', '.freearticle-delete', function(e){
        e.preventDefault();

        var labelId = $(this).data('freearticle-id');
        FreeArticleDelete(labelId);
    });

    $("#editFreeArticle").dialog({
        modal: true,
        bgiframe: true,
        closeOnEscape:false,
        minWidth:580,
        maxHeight:700,
        autoOpen: false,
        buttons: {
            ABBRECHEN: function() {
                FreeArticleReset();
                $(this).dialog('close');
            },
            SPEICHERN: function() {
                FreeArticleEditSave();
            }
        }
    });

    $("#editFreeArticle").dialog({
        close: function( event, ui ) { FreeArticleReset();}
    });
});


function FreeArticleReset(){
    var $editFreeArticle = $('#editFreeArticle');
    $editFreeArticle.find('#free_article_entry_id').val('');
    $editFreeArticle.find('#free_article_article').val('');
    $editFreeArticle.find('#free_article_project').val('');
    $editFreeArticle.find('#free_article_amount').val('');
    $editFreeArticle.find('#free_article_condition').val('never');
    $editFreeArticle.find('#free_article_while_stocks_last').prop("checked",false);
    $editFreeArticle.find('#free_article_stock_option').hide();
}

function FreeArticleEditSave(){
    $.ajax({
        url: 'index.php?module=freearticle&action=save',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            id: $('#free_article_entry_id').val(),
            article: $('#free_article_article').val(),
            project: $('#free_article_project').val(),
            amount: $('#free_article_amount').val(),
            condition: $('#free_article_condition').val(),
            whilestockslast: $('#free_article_while_stocks_last').prop("checked") ? 1 : 0
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                FreeArticleReset();
                updateLiveTable();
                $("#editFreeArticle").dialog('close');
            } else {
                alert(data.statusText);
            }
        }
    });
}

function FreeArticleEdit(id){
    var $editFreeArticle = $('#editFreeArticle');

    if(id > 0)
    {
        $.ajax({
            url: 'index.php?module=freearticle&action=edit&cmd=get',
            data: {
                id: id
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                App.loading.open();
            },
            success: function(data) {
                $editFreeArticle.find('#free_article_entry_id').val(data.id);
                $editFreeArticle.find('#free_article_article').val(data.article);
                $editFreeArticle.find('#free_article_project').val(data.project);
                $editFreeArticle.find('#free_article_amount').val(data.amount);
                $editFreeArticle.find('#free_article_condition').val(data.condition);
                $editFreeArticle.find('#free_article_while_stocks_last').prop("checked", data.while_stocks_last==1?true:false);
                if (data.condition !== 'never') {
                    $editFreeArticle.find('#free_article_stock_option').show();
                }

                App.loading.close();
                $editFreeArticle.dialog('open');
            }
        });
    } else {
        FreeArticleReset();
        $editFreeArticle.dialog('open');
    }
}

function updateLiveTable(i){
    var oTableL = $('#freearticle_list').dataTable();
    var tmp = $('.dataTables_filter input[type=search]').val();
    oTableL.fnFilter('%');
    oTableL.fnFilter(tmp);
}

function FreeArticleDelete(id){
    var conf = confirm('Wirklich löschen?');
    if (conf) {
        $.ajax({
            url: 'index.php?module=freearticle&action=delete',
            data: {
                id: id
            },
            method: 'post',
            dataType: 'json',
            beforeSend: function() {
                App.loading.open();
            },
            success: function(data) {
                if (data.status == 1) {
                    updateLiveTable();
                } else {
                    alert(data.statusText);
                }
                App.loading.close();
            }
        });
    }
    return false;
}

function changeVisibiltyOfStockOption(){
    var $freeArticleStockOption = $('#free_article_stock_option');

    if($('#free_article_condition').val() === 'never'){
        $freeArticleStockOption.hide();
    } else {
        $freeArticleStockOption.show();
    }
}

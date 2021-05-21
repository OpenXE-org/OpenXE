$(document).ready(function(){
    $('#pos_article_popup').dialog(
    {
        modal: true,
        autoOpen: false,
        minWidth: 940,
        title:'Artikel',
        buttons: {
            ABBRECHEN: function() {
                $(this).dialog('close');
            }
        },
        close: function(event, ui){

        }
    });

    $('#posarticlespopup').on('click',function(){
      $('#pos_article_popup').dialog('open');
    });

    $('#pos_article_content div.articles').on('click',function(){
      $('#pos_article_popup').dialog('close');
      $('#artikelnummerprojekt').val($(this).data('nummer'));
      $('#loadart').submit();
    });

});
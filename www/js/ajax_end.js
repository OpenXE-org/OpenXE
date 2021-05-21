function addClicklupe()
{
  $('.ui-autocomplete-input').each(function(){
    var elnext = $(this).next();
    if($(elnext).is('a') && $(elnext).html() == 'X')
    {
      $(elnext).after('<img  onclick="clicklupe(this);" style="right:10px;top:5px;position:absolute;cursor:pointer;" src="images/icon_lupe_plus_transparent.png" class="autocomplete_lupe" />');
    } else {
      $(this).after('<img  onclick="clicklupe(this);" style="left:-27px;top:6px;margin-right:-22px;margin-top:-6px;position:relative;cursor:pointer;max-heigth:12px;" src="images/icon_lupe_plus_transparent.png" class="autocomplete_lupe" />');
    }
  });
}

function lupeclickevent()
{
  $('.ui-autocomplete-input').each(function(){
    if($(this).css('display') == 'none')$(this).next('.autocomplete_lupe').hide();
  });
  $('*').each(function(){
    $(this).on('click',function(){
      if($(this).hasClass('autocomplete_lupe'))
      {

        $('.ui-autocomplete-input').each(function(){
          if($(this).val() === ' ')
          {
            $(this).val('');
            $(this).trigger('keydown');
          }
        });
        blockclick = true;
        lastlupe = this;
        var el = this;
        //var height = $(window).scrollTop();
        var found = false;
        $(el).prev('.ui-autocomplete-input').each(function(){
          //var v = $(this).val();
          found = true;
          aktlupe = this;
          $(this).val(' ');
          $(this).trigger('keydown');
          //if(v !== '')setTimeout(trimel, 1500,this);
          //setTimeout(function(){$(window).scrollTop(height);},100);
        });
        if(!found)
        {
          $(el).prev('a').prev('.ui-autocomplete-input').each(function(){
            found = true;
            aktlupe = this;
            $(this).val(' ');
            $(this).trigger('keydown');
          });
        }
        setTimeout(function(){blockclick = false;},200);
      } else {
        if(this !== lastlupe)
        {
          if(!blockclick)
          {
            $('.ui-autocomplete-input').each(function(){
              if($(this).val() === ' ')
              {
                $(this).val('');
                $(this).trigger('keydown');
              }
            });
          }
        }
      }
    });
  });
}

/**
 * Hat Browser-Fenster den Focus?
 *
 * @return {boolean}
 */
function windowHasFocus() {
    return (typeof document.hasFocus === 'function') ? document.hasFocus() : false;
}

/**
 * Ist Browser-Fenster minimiert?
 *
 * @return {boolean}
 */
function windowIsMinimized() {
    var documentHiddenPropAvailable = typeof document.hidden !== 'undefined';
    if (documentHiddenPropAvailable) {
        return document.hidden;
    }

    var msDocumentHiddenPropAvailable = typeof document.msHidden !== 'undefined';
    if (msDocumentHiddenPropAvailable) {
        return document.msHidden;
    }

    var webkitDocumentHiddenPropAvailable = typeof document.webkitHidden !== 'undefined';
    if (webkitDocumentHiddenPropAvailable) {
        return document.webkitHidden;
    }

    return false;
}

$(document).ready(function() {
  var $popupAttributes = $('#popupattributes');
  if ($popupAttributes.length === 0) {
      return;
  }
  var popupattributes = JSON.parse($popupAttributes.html());

  $('a.popup').click(function (e) {
    e.preventDefault();
    var $this = $(this);
    var horizontalPadding = 30;
    var verticalPadding = 30;
    $('<iframe id="externalSite" class="externalSite" src="' + this.href + '" />').dialog({
      title: ($this.attr('title')) ? $this.attr('title') : 'External Site',
      autoOpen: true,
      width: popupattributes.popupwidth,
      height: popupattributes.popupheight,
      modal: true,
      resizable: true
    }).width(popupattributes.popupwidth - horizontalPadding).height(popupattributes.popupheight - verticalPadding);
  });
});

$(document).ready(function() {

  $('#tabs ul.ui-tabs-nav li a').each(function(){
    if($(this).text() == '')
    {
      var paract = $(this).parent().first();
      $(paract).toggleClass('active',false);
      $(paract).css('background','transparent');
      $(paract).css('background-color','transparent');
    }
  });

  addClicklupe();
  lupeclickevent();

  // Wenn Popup-Dialog: AutoComplete-Ergebnisboxen an Dialog-Fenster anhängen.
  // Ansonsten wird AutoComplete-Ergebnis evtl. nicht sichtbar unter dem Dialog-Fenster angezeigt.
  $('div').on('dialogopen', function (event, ui) {
    var $uiDialog = $(this).parents('.ui-dialog').first();
    $(this).find('input.ui-autocomplete-input').autocomplete('option', 'appendTo', $uiDialog);
  });
});
$(document).ready(function() {
  $('td.radiobutton label').on('click',function(){
    $(this).prev('input').prop('checked',true);
  });
  $('input[data-ajax]').on('click',function() {
    $.ajax({
      url: $(this).data('ajax'),
      type: 'POST',
      dataType: 'json',
      data: { }
    });
  });
  if($('#addfavpopup').length) {
    $('#addfavpopup').dialog(
        {
          modal: true,
          autoOpen: false,
          minWidth: 550,
          title:'',
          buttons: {
            'ABBRECHEN': function() {
              $(this).dialog('close');
            },
            'ANLEGEN': function()
            {
              $.ajax({
                url: 'index.php?module=welcome&action=start',
                type: 'POST',
                dataType: 'json',
                data: {
                  addfav:1,
                  title: $('#addfavname').val(),
                  link: $('#addfavlink').val(),
                  newlink: $('#addfavnewlink').prop('checked')?1:0
                },
                success: function(data) {
                  $('#addfavpopup').dialog('close');
                },
                beforeSend: function() {

                }
              });
            }
          },
          close: function(event, ui){

          }
        });
    $('#addfavpopup').toggleClass('hide', false);
    $('input#addfav').on('click', function(){
      $('#addfavname').val($(this).data('name'));
      $('#addfavlink').val($(this).data('link'));
      $('#addfavpopup').dialog('open');
    });
  }
  if($('#XentralAlert').length) {
    $('#XentralAlert').show();
  }
  if($('#XentralNotification').length) {
    $('#XentralNotification').show();
    $('#XentralNotification').toggleClass('show', true);
  }

  $('#tabs').find('div.ui-tabs-panel:visible table.defferloading').each(function () {
    $(this).toggleClass('defferloading', false);
    $(this).dataTable().fnSettings().sAjaxSource = $(this).dataTable().fnSettings().sAjaxSource.replace('&deferLoading=1','');
    $(this).DataTable().ajax.reload();
  });

  $('#tabs').on('tabsactivate', function( event, ui ) {
    $('#tabs').find('div.ui-tabs-panel:visible table.defferloading').each(function(){
      $(this).toggleClass('defferloading', false);
      $(this).dataTable().fnSettings().sAjaxSource = $(this).dataTable().fnSettings().sAjaxSource.replace('&deferLoading=1','');
      $(this).DataTable().ajax.reload();
    });
  });

});

/**
 * Zendesk Web Widget
 *
 * @type {{init: init}}
 */

var ZendeskWebWidget = function ($) {
  'use strict';

  var me = {

    storage:{
      currentModule: null
    },

    init: function () {
      if(typeof zE !== 'function'){
        return;
      }

      me.storage.currentModule = $('body').data('module');

      me.setArticleSuggestions();
    },

    // makes sure that the web widget suggests articles based on the current module
    setArticleSuggestions: function () {
      zE('webWidget', 'helpCenter:setSuggestions', {search: me.storage.currentModule});
    }
  };

  return {
    init: me.init
  };

}(jQuery);

/**
 * profile menu
 */
var ProfileMenu = function ($) {
  'use strict';

  var me = {

    elem: {
      $profileMenu: null,
      $profileInfoName: null,
      $wrapper: null,
      $document: null
    },

    init: function () {
      me.elem.$profileMenu = $('#profile-menu');
      me.elem.$profileInfoName = $('#profile-info-name');
      me.elem.$wrapper = $('#profile-info-wrapper, #profile-picture-wrapper');
      me.elem.$document = $(document);

      me.registerEvents();
    },

    registerEvents: function () {
      me.elem.$wrapper.on('click touch', function (e) {
        e.stopPropagation();

        me.elem.$profileMenu.toggleClass('visible');
        me.elem.$profileInfoName.toggleClass('menu-visible');
      });

      me.elem.$document.on('click touch', function () {
        me.elem.$profileMenu.removeClass('visible');
        me.elem.$profileInfoName.removeClass('menu-visible');
      })
    }


  };

  return {
    init: me.init
  };

}(jQuery);

$(function () {
    ZendeskWebWidget.init();
    ProfileMenu.init();
});
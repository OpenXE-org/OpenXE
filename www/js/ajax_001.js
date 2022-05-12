/**
 * Sends JS errors to google analytics
 * Based on this answer: https://stackoverflow.com/a/21718577
 */

if (typeof window.onerror === 'object') {

    window.onerror = function (err, url, line) {

        if (typeof ga === "undefined") {
            return;
        }

        var xentralModule,
            xentralVersion;

        if(document.body !== null && document.body.dataset !== undefined){
            xentralModule = document.body.dataset.module;
            xentralVersion = document.body.dataset.version;
        }

        ga('send', 'exception', {
            'exDescription':
                'xentral-module: ' + xentralModule +
                ' xentral-version: ' + xentralVersion +
                ' url: ' + url +
                ' line: ' + line +
                ' error: ' + err,
            'appVersion': xentralVersion
        });
    };
}

document.head || (document.head = document.getElementsByTagName('head')[0]);
var aktlupe = null;
var lastlupe = null;
var blockclick = false;

function checkautocomplete()
{
  $('.json_autocomplete').each(function() {
    var jsontext = $(this).html();
    if(jsontext) {
      var obj = JSON.parse(jsontext);
      if (typeof obj.data != 'defined' && typeof obj.element != 'undefined') {
        $('#' + obj.element).autocomplete(obj.data);
      }
      $(this).remove();
    }
  });


  $('.autocomplete_json').each(function() {
    var jsontext = $(this).html();
    if(jsontext) {
      var json = JSON.parse(jsontext);
      var obj = new Object();
      obj.source = json.source;
      if (typeof json.onlyfirst != 'undefined' && json.onlyfirst == '1') {
        obj.select = function (event, ui) {
          var i = ui.item.value;
          var zahl = i.indexOf(" ");
          var text = i.slice(0, zahl);
          $("input#" + json.element).val(text);
          return false;
        }
      }
      if (typeof json.appendTo != 'undefined' && json.appendTo != '') {
        obj.appendTo = "#" + json.appendTo;
      }
      if (typeof json.element != 'undefined') {
        $("input#" + json.element).autocomplete(obj);
        $(this).remove();
      }
    }
  });

}

function generate(type, text) {
  if(type === 'chatbox')
  {
    var anzchat = $('ul#topmenu').find('.chatbox').first().text();
    if(anzchat != text)
    {
      $('ul#topmenu').find('.chatbox').first().text(text);
      $('ul#topmenu').find('.chatbox').first().toggleClass('nachrichtenboxzahl_red', true);
      changeFavicon("./themes/new/images/favicon/favicon_message.ico");
      $('#shortcuticon').attr('href', "./themes/new/images/favicon/favicon_message.ico");
      $('#favicon').attr('href', "./themes/new/images/favicon/favicon_message.ico");
      $('ul#topmenu').find('.chatbox').first().toggleClass('nachrichtenboxzahl', false);
    }
  }
}

function LoadGeschaeftsbriefvorlage(sid,type)
{
  //alert('s ' + sid + ' t ' + type);
  $( "#geschaeftsbriefvorlage-confirm" ).dialog({
      resizable: false,
      height: "auto",
      width: 400,
      modal: true,
      buttons: {
        "OK": function() {

          $.ajax({
            url: 'index.php?module='+type+'&action=abschicken&cmd=getvorlage',
            data: {sid: sid, type: type},
            success: function(data) {
              result = JSON.parse(data);
              if(result.status==1)
              {
                $('input#betreff').val(result.subject);
                $('textarea#text').ckeditor().editor.setData(result.body);
              } else {
                alert('Keine passende Vorlage gefunden. Bitte in den Geschaeftsbriefvorlagen definieren.');
              }
            }
          });

          $( this ).dialog( "close" );
        },
        Cancel: function() {
          $( this ).dialog( "close" );
        }
      }
    });
}

function openchatbox()
{
  $('#chatpopupcontent').html('<iframe src="index.php?module=chat&action=list" style="border:0;width:100%;height:550px;"></iframe>');
  $('#chatpopup').dialog('open');
}

function clicklupe(el)
{
  lastlupe = el;



}

function aktualisiereLupe()
{
  $('.ui-autocomplete-input').each(function(){
    if($(this).css('display') == 'none')
    {
      $(this).next('.autocomplete_lupe').hide();
    } else {
      $(this).next('.autocomplete_lupe').show();
    }
  });
}

function trimel(el)
{
  $(el).val($(el).val().trim());
}

var table_filter = {
init: function() {

        $('.table_filter').find('input[type="text"]').css({
width: 200
});

$('.table_filter').find('select').css({
padding: '5px',
width: 200,
'border-radius': 0
});


$('.table_filter').find('.smallInput').css({
width: 125
});

$('.table_filter').each(function() {

    $(this).find('table').first().wrap('<div class="table_filter_inner"/>');

    var data_filter = $(this).attr('data-filter');

    table_filter.getParameters($(this));
    table_filter.addTop(data_filter);


    var htmlLink = '';
    htmlLink += '<a href="javascript:;" onclick="table_filter.toggle(\'' + data_filter + '\');" style="position: relative; display: inline-block; width: 30px; height: 15px;">';
    htmlLink += '<img src="images/icon_filter_plus.svg" style="position: absolute; right: 0; top: 0;width:20px;height:20px;">';

    window.setTimeout(function() {
        $('.dataTables_wrapper')
        .find('input')
        .first()
        .parent()
        .after(htmlLink);
        }, 500);

    $(this).find('input').on('keypress',function(event) {
        if ( event.which == 13 ) {
        table_filter.setParameters(data_filter);
        event.preventDefault();
        }
        });

});

addDeleteInput('.table_filter');

},
setParameters: function(data_selector, dontReload) {
                 var container = $('fieldset[data-filter="'+data_selector+'"]');
                 var set = container.serialize();

                 if ( container.hasClass('open') ) {
                   set += '&filterIsOpen=1';
                 }

                 $.ajax({
url: 'index.php?module=ajax&action=tablefilter&do=setParameters&filter=' + data_selector,
data: set,
success: function(data) {
if (!dontReload) {
window.setTimeout(function() {
    window.location.reload();
    });
}
// Filter gesetzt... DataTable aktualisieren
}
});
},
getParameters: function(container) {
                 $.ajax({
url: 'index.php?module=ajax&action=tablefilter',
dataType: 'json',
data: {
do: 'getParameters',
filter: container.attr('data-filter')
},
success: function(data) {

var countSetFilters = 0;

if (data == null) {
return false;
}

if (typeof data.filter != 'undefined') {
delete data.filter;            
}

if (typeof data.action != 'undefined') {
delete data.action;            
}

if (typeof data.do != 'undefined') {
  delete data.do;            
}

if (typeof data.module != 'undefined') {
  delete data.module;            
}

var filterOpen = false;
if (typeof data.filterIsOpen != 'undefined' && data.filterIsOpen >= 1) {
  filterOpen = true;
  delete data.filterIsOpen;
} 

$.each(data, function(key,value) {
    var input = container.find('[name="'+key+'"]');
    switch(input.attr('type')) {
    case 'checkbox':
    if (value.length > 0) {
    input.prop('checked', true);
    }
    break;
    default:
    input.val(value);
    break;
    }

    if (value.length > 0) {
    countSetFilters++;
    }

    });

if (countSetFilters > 0) {

  container.css({
display: 'block'
});

if (filterOpen) {
  $('.table_filter_inner').css({
display: 'block'
});
container.addClass('open');
$('.sizeToggle').html('<img src="images/icon_min.png">');
} else {
  $('.table_filter_inner').css({
display: 'none'
});
$('.sizeToggle').html('<img src="images/icon_max.png">');
}

container.find('.table_filter_hinweis').html('<div class="warning">Achtung, es sind Filter aktiv!</div>');

}

}
});
},

addTop: function(data_selector) {

          var container = $('fieldset[data-filter="'+data_selector+'"]');
          var beforeHtml = '';

          beforeHtml += '<div class="iOpen" style="position: relative;">';

          beforeHtml += '<div class="table_filter_hinweis"></div>';
          beforeHtml += ' <a style="position: absolute; right: 35px; top: 14px;" href="javascript:;" class="sizeToggle" onclick="table_filter.sizeToggle(\''+container.attr('data-filter')+'\');"><!-- LEER --></a>';
          beforeHtml += ' <a style="position: absolute; right: 10px; top: 14px;" href="javascript:;" onclick="table_filter.clearParameters(\''+container.attr('data-filter')+'\');"><img src="themes/new/images/delete.svg" border="0"></a>';

          beforeHtml += '</div>';

          container
            .find('.table_filter_inner')
            .before(beforeHtml);

        },

clearParameters: function(data_selector) {
                   var container = $('fieldset[data-filter="'+data_selector+'"]');
                   var set = container.serialize();

                   container.find('input[type="text"]').val('');
                   container.find('input[type="checkbox"]').prop('checked', false);
                   container.find('input[type="radio"]').prop('checked', false);
                   container.find('select option').prop('selected', false);

                   $.ajax({
url: 'index.php?module=ajax&action=tablefilter&do=clearParameters&filter=' + data_selector,
data: set,
success: function(data) {
// Filter gelöscht... DataTable aktualisieren
window.setTimeout(function() {
    window.location.reload();
    });
}
});
},

open: function(data_selector) {
        var container = $('fieldset[data-filter="'+data_selector+'"]');
        container.find('.iOpen').remove();
        container.find('.table_filter_inner').css({
display: 'block'
});

},

sizeToggle: function(data_selector) {

              var container = $('fieldset[data-filter="'+data_selector+'"]');

              if (container.hasClass('open')) {

                container.removeClass('open');
                container.find('.table_filter_inner').css({
display: 'none'
});

container.find('.sizeToggle').html('<img src="images/icon_max.png">');

} else {

  container.addClass('open');
  container.find('.table_filter_inner').css({
display: 'block'
});

container.find('.sizeToggle').html('<img src="images/icon_min.png">');

}

table_filter.setParameters(data_selector, true);

},

toggle: function(data_selector) {

          var container = $('fieldset[data-filter="'+data_selector+'"]');

          if (container.hasClass('smallPreview')) {
            table_filter.open(data_selector);
            return true;
          }

          if (container.is(':visible')) {
            container.slideUp();
            container.removeClass('open');
          } else {
            container.slideDown();
            container.addClass('open');
          }

        }
};


var App = {
loading: {
open: function(callback) {

        if ( $('.loader_app').length == 0 ) {
          App.loading.create();
        }

        if (typeof callback == 'function') {
          callback();
        }
        $('.loader_app').show();

      },
close: function() {

         $('.loader_app').hide();

       },
create: function() {
          $('#scroller2').append('<div class="loader_app"><img src="themes/new/images/icon_grau.png?v=1" width="18"></div>');
        }
         }
};


$(document).ready(function() {
    $('.table_filter').css({
display: 'none'
});
    table_filter.init();
    });

/**
 * Vorgangspositionen > Artikelsuche: Keine Ergebnisse + ENTER-Taste > Profisuche öffnen
 */
$(document).ready(function () {
    var artikelAutoCompleteResultCount = 0;
    var $artikelAutoComplete = $('#tableone #artikel');

    // Wir befinden uns nicht in den Vorgangspositionen > Nichts tun
    if ($artikelAutoComplete.length === 0) {
        return;
    }

    // Merken wieviele Artikel gefunden wurden
    $artikelAutoComplete.on('autocompleteresponse', function (event, ui) {
        artikelAutoCompleteResultCount = typeof ui.content !== 'undefined' && ui.content !== null ? ui.content.length : 0;
    });

    // Profisuche öffnen, wenn es keine Ergebnisse gibt und ENTER gedrückt wird
    $artikelAutoComplete.on('keydown', function (e) {
        if (typeof e.keyCode !== 'undefined' && e.keyCode === 13) { // 13 = ENTER
            // Es wurde ein Artikel eingegeben, es gibt aber keine Ergebnisse > Profisuche öffnen
            var artikelAutocompleteValue = $artikelAutoComplete.val();
            if (artikelAutocompleteValue !== '' && artikelAutoCompleteResultCount === 0) {
                e.preventDefault();
                var $artikelProfisucheButton = $('#artikel-profisuche-button');
                var location = $artikelProfisucheButton.data('location');

                // Profisuche öffnen
                if (typeof location === 'undefined' || location === 'undefined') {
                    // Suchbegriff fehlt > Profisuche einfach öffnen
                    $artikelProfisucheButton.trigger('click');
                } else {
                    // Suchbegriff in Profisuche übernehmen
                    location += '&name_de=' + artikelAutocompleteValue;
                    window.location.href = location;
                }
            }
        }
    });
});

/**
 * Vorgangspositionen > Profisuche: Bei Drücken der ESC-Taste zurück zu den Positionen
 */
$(document).ready(function() {

    // Wir befinden uns nicht in der Profisuche > Nichts tun
    if ($('#profisuche-back-button').length === 0) {
        return;
    }

    // Bei ESC > Zurück-Button auf Seite anklicken
    $(document).on('keyup', function (e) {
        if (typeof e.keyCode !== 'undefined' && e.keyCode === 27) {
            document.getElementById('profisuche-back-button').click();
        }
    });
});

/**
 * Vorgangspositionen > Profisuche: Individueller Steuersatz ein-/ausblenden
 */
$(document).ready(function() {

    // Wir befinden uns nicht in der Profisuche > Nichts tun
    if ($('#steuersatz-individuell-switch').length === 0) {
        return;
    }

    var $customTaxRateContainer = $('#steuersatz-individuell-container');
    var $customTaxRateInput = $('#steuersatz-individuell');
    var $customTaxRateSwitch = $('#steuersatz-individuell-switch');
    var $taxRateDropdown = $('#umsatzsteuerauswahl');

    // Funktion prüft anhand von Checkbox ob Eingabefeld ein- oder ausgeblendet werden soll
    var toggleCustomTaxRateInput = function () {
        var customTaxRateActive = $customTaxRateSwitch.prop('checked');
        $customTaxRateContainer.toggle(customTaxRateActive);

        if (customTaxRateActive === false) {
            $customTaxRateInput.val('');
            $taxRateDropdown.prop('disabled', false);
        } else {
            $taxRateDropdown.prop('disabled', true);
        }
    };

    // Einmal prüfen wenn Seite geladen wird
    toggleCustomTaxRateInput();

    // Bei jeder Änderung der Checkbox erneut prüfen
    $(document).on('change', '#steuersatz-individuell-switch', function () {
        toggleCustomTaxRateInput();
    });
});


(function() {

 if (window.matchMedia) {
 var mediaQueryList = window.matchMedia('print');
 mediaQueryList.addListener(function(mql) {
     if (mql.matches) {
     beforePrint();
     } else {
     afterPrint();
     }
     });
 }

 window.onbeforeprint = beforePrint;
 window.onafterprint = afterPrint;

 }());

function beforePrint() {
  /*
     $('.mce-edit-area iframe').each(function() {
     $(this).attr('original-height', $(this).height());
     $(this).height($(this).contents().find("html").height());
     });
   */
}

function afterPrint() {
  $(this).height($(this).attr('original-height'));
}

function wawisionPrint() {

  App.loading.open();

  $('.mce-edit-area iframe').each(function() {
      $(this).attr('original-height', $(this).height());
      $(this).height( $(this).contents().find("html").height() );
      }); 

  window.setTimeout(function() {
      window.print();
      App.loading.close();
      window.setTimeout(function() {
          $('.mce-edit-area iframe').each(function() {
              $(this).height( $(this).attr('original-height') );
              }); 
          }, 500);
      }, 500);

}


function printdiv(iddiv) 
{
  var divToPrint=document.getElementById(iddiv);
  var newWin=window.open('','Print-Window');
  newWin.document.open();
  newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
  newWin.document.close();
  setTimeout(function(){newWin.close();},10);
}

function addDeleteInput(selector) {
  $(selector).find('input[type="text"]').wrap('<div class="inputwrapper" style="position: relative; display: inline-block;">');
  $('.inputwrapper').each(function(key,inputContainer) {
      if ( !$(inputContainer).hasClass('isWrappedInput') ) {
      $(inputContainer).addClass('inputContainer_' + key);
      $(inputContainer).addClass('isWrappedInput');
      $(inputContainer).append('<a href="javascript:;" onclick="deleteInput(' + key + ');" style="position: absolute; right: 5px; top: 5px; color: #999;">X</a>');
      }
      });
}

function deleteInput(key) {
  $('.inputContainer_' + key).find('input').val('');
}


function generatePass(plength){

  var keylistalpha="abcdefghijklmnopqrstuvwxyz";
  var keylistint="123456789";
  var keylistspec="!@#_";
  var temp='';
  var len = plength/2;
  var len = len - 1;
  var lenspec = plength-len-len;

  for (i=0;i<len;i++)
    temp+=keylistalpha.charAt(Math.floor(Math.random()*keylistalpha.length));

  for (i=0;i<lenspec;i++)
    temp+=keylistspec.charAt(Math.floor(Math.random()*keylistspec.length));

  for (i=0;i<len;i++)
    temp+=keylistint.charAt(Math.floor(Math.random()*keylistint.length));

  temp=temp.split('').sort(function(){return 0.5-Math.random()}).join('');

  return temp;
}

function copyTextToClipboard(text) {
  var textArea = document.createElement("textarea");

  // Place in top-left corner of screen regardless of scroll position.
  textArea.style.position = 'fixed';
  textArea.style.top = 0;
  textArea.style.left = 0;

  // Ensure it has a small width and height. Setting to 1px / 1em
  // doesn't work as this gives a negative w/h on some browsers.
  textArea.style.width = '2em';
  textArea.style.height = '2em';

  // We don't need padding, reducing the size if it does flash render.
  textArea.style.padding = 0;

  // Clean up any borders.
  textArea.style.border = 'none';
  textArea.style.outline = 'none';
  textArea.style.boxShadow = 'none';

  // Avoid flash of white box if rendered for any reason.
  textArea.style.background = 'transparent';

  textArea.value = text;

  document.body.appendChild(textArea);

  textArea.select();

  try {
    var successful = document.execCommand('copy');
    var msg = successful ? 'successful' : 'unsuccessful';
    console.log('Copying text command was ' + msg);
  } catch (err) {
    console.log('Oops, unable to copy');
  }

  document.body.removeChild(textArea);
}

function adresse_gruppen(adresse,gruppe,value)
{
  if(value) value=1; else value=0;
  ajaxRequest= $.ajax({
url: "index.php?module=adresse&action=gruppen&id="+adresse,
type: "post",
data: "gid="+gruppe+"&value="+value
});
}

function isTouchDevice()
{
  var ua = navigator.userAgent;
  var isTouchDevice = (
      ua.match(/iPad/i) ||
      ua.match(/iPhone/i) ||
      ua.match(/iPod/i) ||
      ua.match(/Android/i)
      );

  return isTouchDevice;
}

function callCursorArbeitsnachweis()
{
  setTimeout(continueExecutionArbeitsnachweis, 200) //wait ten seconds before continuing
}

function callCursor()
{
  setTimeout(continueExecution, 200) //wait ten seconds before continuing
}

function continueExecutionArbeitsnachweis()
{
  document.getElementById('framepositionen').contentWindow.document.getElementById('adresse').value = ""; 
  document.getElementById('framepositionen').contentWindow.document.getElementById('adresse').focus();
}

function continueExecution()
{
  if((document.getElementById('framepositionen') !== null) && (document.getElementById('framepositionen').contentWindow.document.getElementById('artikel') !== null))
  {
    document.getElementById('framepositionen').contentWindow.document.getElementById('artikel').value = ""; 
    document.getElementById('framepositionen').contentWindow.document.getElementById('artikel').focus();
  }
}

function AdresseAnsprechpartner(value)
{
  var strSource = "./index.php";
  var strData = "module=ajax&action=ansprechpartner&id="+value;
  var intType= 0; //GET
  var intID = 0;
  command = 'getAdresseAnsprechpartner';
  sendRequest(strSource,strData,intType,intID);
}


function AnsprechpartnerLieferadresse(value)
{
  var strSource = "./index.php";
  var strData = "module=ajax&action=ansprechpartner&id="+value;
  var intType= 0; //GET
  var intID = 0;
  command = 'getAnsprechpartnerLieferadresse';
  sendRequest(strSource,strData,intType,intID);
}

function Verzolladresse(value)
{
  var strSource = "./index.php";
  var strData = "module=ajax&action=verzolladresse&id="+value;
  var intType= 0; //GET
  var intID = 0;
  command = 'getVerzolladresse';
  sendRequest(strSource,strData,intType,intID);
  parent.closeIframe();
}

function Ansprechpartner(value)
{

  var strSource = "./index.php";
  var strData = "module=ajax&action=ansprechpartner&id="+value;
  var intType= 0; //GET
  var intID = 0;
  command = 'getAnsprechpartner';
  sendRequest(strSource,strData,intType,intID);

}

function Lieferadresse(value)
{

  var strSource = "./index.php";
  var strData = "module=ajax&action=lieferadresse&id="+value;
  var intType= 0; //GET
  var intID = 0;
  command = 'getLieferadresse';
  sendRequest(strSource,strData,intType,intID);
}

function AdresseStammdatenIframe(value, postfix)
{
  var strSource = "./index.php";
  var strData = "module=ajax&action=adressestammdaten&id="+value;
  var intType= 0; //GET
  var intID = 0;
  commandpostfix = "";
  if(typeof postfix != 'undefined')commandpostfix = postfix;
  command = 'getAdresseStammdaten';
  sendRequest(strSource,strData,intType,intID);
  parent.closeIframe();
}


function AdresseStammdatenLieferscheinIframe(value, postfix)
{
  var strSource = "./index.php";
  var strData = "module=ajax&action=adressestammdaten&id="+value;
  var intType= 0; //GET
  var intID = 0;
  commandpostfix = "";
  if(typeof postfix != 'undefined')commandpostfix = postfix;
  command = 'getAdresseStammdatenLieferschein';
  sendRequest(strSource,strData,intType,intID);
  parent.closeIframe();
}

function AnsprechpartnerLieferscheinIframe(value, postfix)
{
  var strSource = "./index.php";
  var strData = "module=ajax&action=ansprechpartner&id="+value;
  var intType= 0; //GET
  var intID = 0;
  commandpostfix = "";
  if(typeof postfix != 'undefined')commandpostfix = postfix;
  command = 'getAnsprechpartnerLieferschein';
  sendRequest(strSource,strData,intType,intID);
  parent.closeIframe();
}



function AnsprechpartnerIframe(value, postfix)
{

  var strSource = "./index.php";
  var strData = "module=ajax&action=ansprechpartner&id="+value;
  var intType= 0; //GET
  var intID = 0;
  commandpostfix = "";
  if(typeof postfix != 'undefined')commandpostfix = postfix;
  command = 'getAnsprechpartner';
  sendRequest(strSource,strData,intType,intID);
  parent.closeIframe();
}

function LieferadresseIframe(value, postfix)
{
  var strSource = "./index.php";
  var strData = "module=ajax&action=lieferadresse&id="+value;
  var intType= 0; //GET
  var intID = 0;
  commandpostfix = "";
  if(typeof postfix != 'undefined')commandpostfix = postfix;
  command = 'getLieferadresse';
  sendRequest(strSource,strData,intType,intID);
  parent.closeIframe();
}



function LieferadresseLS(value)
{

  var strSource = "./index.php";
  var strData = "module=ajax&action=lieferadresse&id="+value;
  var intType= 0; //GET
  var intID = 0;
  command = 'getLieferadresseLS';
  sendRequest(strSource,strData,intType,intID);
}

function AjaxCall(value)
{
  $.get( value, function( data ) {});
}


function InfoBox(value)
{
  if(value=="aufgabe_bondrucker")
    alert("Es wurde kein Bondrucker gefunden. Ist ein Bondrucker vorhanden können kleine Aufgabenzettel für ein Scrumboard o.ä. gedruckt werden.");
}





function CopyDialog(value)
{

  if(!confirm("Soll der Eintrag wirklich kopiert werden?")) return false;
  else window.location.href=value;

}


function PrintDialog(value)
{
  if(!confirm("Soll der Eintrag gedruckt werden?")) return false;
  else window.location.href=value;
}

function PrintDialogMenge(value, vorbelegtmenge)
{
  if(typeof vorbelegtmenge == 'undefined')vorbelegtmenge = 1;
  var menge = prompt("Anzahl Etiketten:",vorbelegtmenge);
  if(!menge) return false;
  else window.location.href=value + '&menge=' + menge;
}

function InsertDialog(value)
{

  if(!confirm("Soll der Eintrag wirklich eingefügt werden?")) return false;
  else window.location.href=value;

}

function DisableDialog(value)
{
  if(!confirm("Soll der Eintrag wirklich deaktiviert werden?")) return false;
  else window.location.href=value;

}

function FinalDialog(value)
{
  if(!confirm("Soll der Eintrag wirklich abgeschlossen werden?")) return false;
  else window.location.href=value;
}

function FinalDialog(value)
{
    if(!confirm("Soll der Eintrag wirklich abgeschlossen werden?")) return false;
    else window.location.href=value;
}

function AdressExportDialog(value)
{
    if(!confirm("Soll die Adresse wirklich an den Shop übertragen werden?")) return false;
    else window.location.href=value;
}

function AdressImportDialog(value)
{
    if(!confirm("Soll die Adresse wirklich vom Shop importiert werden?")) return false;
    else window.location.href=value;
}

function UndoDialog(value)
{
  if(!confirm("Soll der Eintrag wirklich rückgängig gemacht werden?")) return false;
  else window.location.href=value;
}


function StornoDialog(value)
{

  if(!confirm("Soll der Eintrag wirklich storniert werden?")) return false;
  else window.location.href=value;

}

function DeleteAufloesen(value)
{

  if(!confirm("Soll die Verknüpfung aufgelöst werden?")) return false;
  else window.location.href=value;
}

function ConfirmVertriebDialog(value)
{

  if(!confirm("Soll der Vertreter / Verkäufer ausgewählt werden?")) return false;
  else window.location.href=value;

}

function ImportfehlerDialog(value)
{

  if(!confirm("Soll der Eintrag wirklich als Importfehler markiert werden?")) return false;
  else window.location.href=value;

}


function DeleteDialog(value)
{

  if(!confirm("Soll der Eintrag wirklich gelöscht oder storniert werden?")) return false;
  else window.location.href=value;

}


function DeleteDialogLieferschein(value)
{
   if(confirm('Wirklich stornieren?')) if(!confirm('Artikel wieder einlagern?')) window.location.href='index.php?module=lieferschein&action=delete&id='+value;else window.location.href='index.php?module=lieferschein&action=delete&cmd=einlagern&id='+value;
}

function DialogGutschrift(value)
{

  if(!confirm("Soll die Rechnung storniert oder gut geschrieben werden?")) return false;
  else window.location.href=value;

}

function DialogAnfrageStatus(value)
{

  if(!confirm("Soll der nächste Status aktiviert werden?")) return false;
  else window.location.href=value;

}

function DialogAnfrageStart(value)
{

  if(!confirm("Soll die Anfrage gestartet werden?")) return false;
  else window.location.href=value;

}


function DialogAnfrageAbschluss(value)
{

  if(!confirm("Soll die Anfrage abgeschlossen werden?")) return false;
  else window.location.href=value;
}




function DialogForderungsverlust(value)
{

  if(!confirm("Soll der Betrag als Forderungsverlust gebucht werden?")) return false;
  else window.location.href=value;

}



function DialogDifferenz(value)
{

  if(!confirm("Soll der fehlende Betrag als Skonto gebucht werden?")) return false;
  else window.location.href=value;

}

function DialogMahnwesen(value)
{

  if(!confirm("Soll die Rechnung vorrübergehend aus dem Mahnwesen genommen werden?")) return false;
  else window.location.href=value;

}


function DialogZwischenlager(value)
{
  var menge =  prompt('Anzahl aus Zwischenlager nehmen:',1); 
  if(parseFloat(menge.replace(',','.')) > 0) { window.location.href='index.php?module=lager&action=bucheneinlagern&cmd=zwischenlager&id='+value+'&menge='+menge;}
  else return false;
}

function VerbandAbrechnen(value)
{
  var today = new Date();
  var month = today.getMonth()+1;
  var year = today.getYear();
  var day = today.getDate();
  if(day<10) day = "0" + day;
  if(month<10) month= "0" + month;
  if(year<1000) year+=1900;

  var vorschlag = year+ "-" + month + "-" + day;

  var termin = prompt("Abrechnung für Rechnungen bis zum YYYY-MM-DD starten:",vorschlag);

  if (termin != '' && termin != null) 
    window.location.href=value+"&tag="+termin;

}

function BackupDialog(value)
{

  if(!confirm("Achtung: Es existieren neuere Datensicherungen. Möchten Sie wirklich alle bisherigen Einstellungen löschen/zurücksetzen?\n\nAlle nach diesem Zeitpunkt getätigten Einstellungen und Importvorlagen gehen verloren.\n\nDas Snapshot Tool ist nicht für den laufenden Betrieb geeignet.")) return false;
  else window.location.href=value;

}

function ResetDialog()
{

  if(!confirm("Wollen Sie die Datenbank wirklich zurücksetzen?")) return false;
  else return true;

}

function getXMLRequester( )
{
  var xmlHttp = false; //Variable initialisieren

  try
  {
    // Der Internet Explorer stellt ein ActiveXObjekt zur Verfügung
    if( window.ActiveXObject )
    {
      // Versuche die neueste Version des Objektes zu laden
      for( var i = 5; i; i-- )
      {
        try
        {
          //Wenn keine neuere geht, das alte Objekt verwenden
          if( i == 2 )
          {
            xmlHttp = new ActiveXObject( "Microsoft.XMLHTTP" );    
          }
          // Sonst die neuestmögliche Version verwenden
          else
          {

            xmlHttp = new ActiveXObject( "Msxml2.XMLHTTP." + i + ".0" );
          }
          break; //Wenn eine Version geladen wurde, unterbreche Schleife
        }
        catch( excNotLoadable )
        {                        
          xmlHttp = false;
        }
      }
    }
    // alle anderen Browser
    else if( window.XMLHttpRequest )
    {
      xmlHttp = new XMLHttpRequest();
    }
  }
  // loading of xmlhttp object failed
  catch( excNotLoadable )
  {
    xmlHttp = false;
  }
  return xmlHttp ;
}
// Konstanten
var REQUEST_GET        = 0;
var REQUEST_POST        = 2;
var REQUEST_HEAD    = 1;
var REQUEST_XML        = 3;

function sendRequest( strSource, strData, intType, intID )
{
  // Falls strData nicht gesetzt ist, als Standardwert einen leeren String setzen
  if( !strData )
    strData = '';

  // Falls der Request-Typ nicht gesetzt ist, standardmäßig auf GET setzen
  if( isNaN( intType ) )
    intType = 0;

  // wenn ein vorhergehender Request noch nicht beendet ist, beenden
  if( xmlHttp && xmlHttp.readyState )
  {
    xmlHttp.abort( );
    xmlHttp = false;
  }

  // wenn möglich, neues XMLHttpRequest-Objekt erzeugen, sonst abbrechen
  if( !xmlHttp )
  {
    xmlHttp = getXMLRequester( );
    if( !xmlHttp )
      return;
  }

  // Falls die zu sendenden Daten mit einem & oder einem ? beginnen, erstes Zeichen abschneiden
  if( intType != 1 && ( strData && strData.substr( 0, 1 ) == '&' || strData.substr( 0, 1 ) == '?' ))
    strData = strData.substring( 1, strData.length );

  // Als Rückgabedaten die gesendeten Daten, oder die Zieladresse setzen
  var dataReturn = strData ? strData : strSource;

  switch( intType )
  {
    case 1:    //Falls Daten in XML-Form versendet werden, xml davorschreiben
      strData = "xml=" + strData;
    case 2: // falls Daten per POST versendet werden
      // Verbindung öffnen 
      xmlHttp.open( "POST", strSource, true );
      xmlHttp.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
      xmlHttp.setRequestHeader( 'Content-length', strData.length );
      break;
    case 3: // Falls keine Daten versendet werden
      // Verbindung zur Seite aufbauen
      xmlHttp.open( "HEAD", strSource, true );
      strData = null;
      break;
    default: // Falls Daten per GET versendet werden
      //Zieladresse zusammensetzen aus Adresse und Daten
      var strDataFile = strSource + (strData ? '?' + strData : '' );
      // Verbindung aufbauen
      xmlHttp.open( "GET", strDataFile, true );
      strData = null;
  }

  // die Funktion processResponse als Event-handler setzen, wenn sich der Verarbeitungszustand der 
  xmlHttp.onreadystatechange = new Function( "", "processResponse(" + intID + ")" ); ;

  // Anfrage an den Server setzen
  xmlHttp.send( strData );    //strData enthält nur dann Daten, wenn die Anfrage über POST passiert

  // gibt die gesendeten Daten oder die Zieladresse zurück
  return dataReturn;
}


function processResponse( intID )
{
  //aktuellen Status prüfen
  switch( xmlHttp.readyState )
  {
    //nicht initialisiert
    case 0:
      // initialisiert
    case 1:
      // abgeschickt
    case 2:
      // ladend
    case 3:
      break;
      // fertig
    case 4:    
      // Http-Status überprüfen
      if( xmlHttp.status == 200 )    // Erfolg
      {
        processData( xmlHttp, intID ); //Daten verarbeiten
      }
      //Fehlerbehandlung
      else
      {
        if( window.handleAJAXError )
          handleAJAXError( xmlHttp, intID );
        else
          alert( "ERROR\n HTTP status = " + xmlHttp.status + "\n" + xmlHttp.statusText ) ;
      }
  }
}

// handle response errors
function handleAJAXError( xmlHttp, intID )
{
  //alert("AJAX Fehler!");
}

var command;
var commandpostfix = "";
var lastartikelnummer;

var once;

function Select_Value_Set(SelectName, Value) {
  eval('SelectObject = parent.document.' + 
      SelectName + ';');
  if(typeof SelectObject != 'undefined')
  {
    for(index = 0; 
        index < SelectObject.length; 
        index++) {
      if(SelectObject[index].value == Value)
        SelectObject.selectedIndex = index;
    }
  }
}

function SelectCountry(selector, value) {
    var $select = $(selector, parent.document);
    if ($select.length === 0) {
        console.warn('Could not find selector "' + selector + '" for country select.');
        return;
    }
    $select.val(value);
    window.parent.$(selector).trigger('change');
}

function processData( xmlHttp, intID )
{
  // process text data
  //updateMenu( xmlHttp.responseText );
  var render=0;
  switch(command)
  {
    case 'getVerzolladresse': 
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}    
      if(typeof mySplitResult[0] != 'undefined')parent.document.getElementById('verzollungname'+commandpostfix).value=trim(mySplitResult[0]);
      if(typeof mySplitResult[1] != 'undefined')parent.document.getElementById('verzollungabteilung'+commandpostfix).value=trim(mySplitResult[1]);
      if(typeof mySplitResult[2] != 'undefined')parent.document.getElementById('verzollungunterabteilung'+commandpostfix).value=trim(mySplitResult[2]);
      //parent.document.getElementById('lieferland').options[parent.document.getElementById('lieferland').selectedIndex].value=trim(mySplitResult[3]);
      if(typeof mySplitResult[3] != 'undefined')Select_Value_Set('eprooform.verzollungland'+commandpostfix,trim(mySplitResult[3]));
      if(typeof mySplitResult[4] != 'undefined')parent.document.getElementById('verzollungstrasse'+commandpostfix).value=trim(mySplitResult[4]);
      if(typeof mySplitResult[5] != 'undefined')parent.document.getElementById('verzollungort'+commandpostfix).value=trim(mySplitResult[5]);
      if(typeof mySplitResult[6] != 'undefined')parent.document.getElementById('verzollungplz'+commandpostfix).value=trim(mySplitResult[6]);
      if(typeof mySplitResult[7] != 'undefined')parent.document.getElementById('verzollungadresszusatz'+commandpostfix).value=trim(mySplitResult[7]);
      if(typeof mySplitResult[8] != 'undefined')parent.document.getElementById('verzollungansprechpartner'+commandpostfix).value=trim(mySplitResult[8]);
      if(typeof mySplitResult[9] != 'undefined')parent.document.getElementById('verzollungtitel'+commandpostfix).value=trim(mySplitResult[9]);
      if(typeof mySplitResult[10] != 'undefined' && mySplitResult[10] != '')
      {
        parent.document.getElementById('verzollinformationen'+commandpostfix).value=Base64.decode(mySplitResult[10]);
        var verzollinformationen = mySplitResult[10];
        var iframeverzollinformationen = parent.document.getElementById('verzollinformationen'+commandpostfix);
        iframeverzollinformationen = $(iframeverzollinformationen).next('div').find('iframe').first();
        if(iframeverzollinformationen)
        {
          iframeverzollinformationen = $(iframeverzollinformationen).contents();
          $(iframeverzollinformationen).find('*').first().html(Base64.decode(mySplitResult[10]));
        }else{
          parent.document.getElementById('verzollinformationen'+commandpostfix).value=Base64.decode(mySplitResult[10]);
        }
        
      }
    break;
    
    case 'getAnsprechpartner':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(trim(mySplitResult[0])!="") parent.document.getElementById('ansprechpartner').value=trim(mySplitResult[0]);
      if(trim(mySplitResult[1])!="") parent.document.getElementById('email').value=trim(mySplitResult[1]);
      if(trim(mySplitResult[2])!="") parent.document.getElementById('telefon').value=trim(mySplitResult[2]);
      if(trim(mySplitResult[3])!="") parent.document.getElementById('telefax').value=trim(mySplitResult[3]);
      if(trim(mySplitResult[4])!="") parent.document.getElementById('abteilung').value=trim(mySplitResult[4]);
      if(trim(mySplitResult[5])!="") parent.document.getElementById('unterabteilung').value=trim(mySplitResult[5]);
      //Select_Value_Set('eprooform.land',trim(mySplitResult[6]));
      SelectCountry('#land',trim(mySplitResult[6]));
      if(trim(mySplitResult[7])!="") parent.document.getElementById('strasse').value=trim(mySplitResult[7]);
      if(trim(mySplitResult[8])!="") parent.document.getElementById('plz').value=trim(mySplitResult[8]);
      if(trim(mySplitResult[9])!="") parent.document.getElementById('ort').value=trim(mySplitResult[9]);
      if(trim(mySplitResult[10])!="") parent.document.getElementById('adresszusatz').value=trim(mySplitResult[10]);
      // soll aktiv nicht umgestellt werden da der typ von der hauptadresse verwendet werden soll
      //Select_Value_Set('eprooform.typ',trim(mySplitResult[11]));
      parent.document.getElementById('anschreiben').value=trim(mySplitResult[12]);
      if(trim(mySplitResult[13])!=""){
        parent.document.getElementById('titel').value=trim(mySplitResult[13]);
      }else{
        parent.document.getElementById('titel').value='';
      } 
      parent.document.getElementById('ansprechpartnerid').value=trim(mySplitResult[14]);
      break;
    case 'getAnsprechpartnerLieferschein':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(parent.document.getElementById('abweichendelieferadresse'+commandpostfix) != null)parent.document.getElementById('abweichendelieferadresse'+commandpostfix).checked = true;
      if(typeof mySplitResult[0] != 'undefined')parent.document.getElementById('liefername'+commandpostfix).value=trim(mySplitResult[0]);
      if(typeof mySplitResult[1] != 'undefined')parent.document.getElementById('lieferemail'+commandpostfix).value=trim(mySplitResult[1]);
      if(typeof mySplitResult[4] != 'undefined')parent.document.getElementById('lieferabteilung'+commandpostfix).value=trim(mySplitResult[4]);
      if(typeof mySplitResult[5] != 'undefined')parent.document.getElementById('lieferunterabteilung'+commandpostfix).value=trim(mySplitResult[5]);
      //parent.document.getElementById('lieferland').options[parent.document.getElementById('lieferland').selectedIndex].value=trim(mySplitResult[3]);
      //if(typeof mySplitResult[6] != 'undefined')Select_Value_Set('eprooform.lieferland'+commandpostfix,trim(mySplitResult[6]));
      SelectCountry('#lieferland',trim(mySplitResult[6]));
      if(typeof mySplitResult[7] != 'undefined')parent.document.getElementById('lieferstrasse'+commandpostfix).value=trim(mySplitResult[7]);
      if(typeof mySplitResult[9] != 'undefined')parent.document.getElementById('lieferort'+commandpostfix).value=trim(mySplitResult[9]);
      if(typeof mySplitResult[8] != 'undefined')parent.document.getElementById('lieferplz'+commandpostfix).value=trim(mySplitResult[8]);
      if(typeof mySplitResult[10] != 'undefined')parent.document.getElementById('lieferadresszusatz'+commandpostfix).value=trim(mySplitResult[10]);
      if(typeof mySplitResult[13] != 'undefined')parent.document.getElementById('liefertitel'+commandpostfix).value=trim(mySplitResult[13]);
      if(typeof mySplitResult[14] != 'undefined')parent.document.getElementById('ansprechpartnerid'+commandpostfix).value=trim(mySplitResult[14]);
      window.parent.abweichend2();
      //			parent.document.getElementById('lieferansprechpartner').value=trim(mySplitResult[0]);
      break;
    case 'getAdresseStammdatenLieferschein':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(parent.document.getElementById('abweichendelieferadresse'+commandpostfix) != null)parent.document.getElementById('abweichendelieferadresse'+commandpostfix).checked = true;
      if(typeof mySplitResult[0] != 'undefined')parent.document.getElementById('liefername'+commandpostfix).value=trim(mySplitResult[0]);
      if(typeof mySplitResult[1] != 'undefined')parent.document.getElementById('lieferabteilung'+commandpostfix).value=trim(mySplitResult[1]);
      if(typeof mySplitResult[2] != 'undefined')parent.document.getElementById('lieferunterabteilung'+commandpostfix).value=trim(mySplitResult[2]);
      //parent.document.getElementById('lieferland').options[parent.document.getElementById('lieferland').selectedIndex].value=trim(mySplitResult[3]);
      //if(typeof mySplitResult[3] != 'undefined')Select_Value_Set('eprooform.lieferland'+commandpostfix,trim(mySplitResult[3]));
      if(typeof mySplitResult[3] != 'undefined')SelectCountry('#lieferland',trim(mySplitResult[3]));
      if(typeof mySplitResult[4] != 'undefined')parent.document.getElementById('lieferstrasse'+commandpostfix).value=trim(mySplitResult[4]);
      if(typeof mySplitResult[5] != 'undefined')parent.document.getElementById('lieferort'+commandpostfix).value=trim(mySplitResult[5]);
      if(typeof mySplitResult[6] != 'undefined')parent.document.getElementById('lieferplz'+commandpostfix).value=trim(mySplitResult[6]);
      if(typeof mySplitResult[7] != 'undefined')parent.document.getElementById('lieferadresszusatz'+commandpostfix).value=trim(mySplitResult[7]);
      if(typeof mySplitResult[8] != 'undefined')parent.document.getElementById('lieferansprechpartner'+commandpostfix).value=trim(mySplitResult[8]);
      if(typeof mySplitResult[9] != 'undefined')parent.document.getElementById('liefertitel'+commandpostfix).value=trim(mySplitResult[9]);
      if(typeof mySplitResult[10] != 'undefined')parent.document.getElementById('ansprechpartnerid'+commandpostfix).value=trim(mySplitResult[10]);
      if(typeof mySplitResult[15] != 'undefined')parent.document.getElementById('liefergln'+commandpostfix).value=trim(mySplitResult[15]);
      if(typeof mySplitResult[11] != 'undefined')parent.document.getElementById('lieferemail'+commandpostfix).value=trim(mySplitResult[11]);
      window.parent.abweichend2();
      //			parent.document.getElementById('lieferansprechpartner').value=trim(mySplitResult[0]);
      break;
    case 'getAdresseStammdaten':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");

      if(typeof mySplitResult[0] != 'undefined')parent.document.getElementById('name'+commandpostfix).value=trim(mySplitResult[0]);
      if(typeof mySplitResult[1] != 'undefined')parent.document.getElementById('abteilung'+commandpostfix).value=trim(mySplitResult[1]);
      else parent.document.getElementById('abteilung'+commandpostfix).value="";

      if(typeof mySplitResult[2] != 'undefined')parent.document.getElementById('unterabteilung'+commandpostfix).value=trim(mySplitResult[2]);
      else parent.document.getElementById('unterabteilung'+commandpostfix).value="";

      //parent.document.getElementById('lieferland').options[parent.document.getElementById('lieferland').selectedIndex].value=trim(mySplitResult[3]);
      //if(typeof mySplitResult[3] != 'undefined')Select_Value_Set('eprooform.land'+commandpostfix,trim(mySplitResult[3]));
      if(typeof mySplitResult[3] != 'undefined')SelectCountry('#lieferland',trim(mySplitResult[3]));

      if(typeof mySplitResult[4] != 'undefined')parent.document.getElementById('strasse'+commandpostfix).value=trim(mySplitResult[4]);
      else parent.document.getElementById('strasse'+commandpostfix).value="";

      if(typeof mySplitResult[5] != 'undefined')parent.document.getElementById('ort'+commandpostfix).value=trim(mySplitResult[5]);
      else parent.document.getElementById('ort'+commandpostfix).value="";

      if(typeof mySplitResult[6] != 'undefined')parent.document.getElementById('plz'+commandpostfix).value=trim(mySplitResult[6]);
      else parent.document.getElementById('plz'+commandpostfix).value="";

      if(typeof mySplitResult[7] != 'undefined')parent.document.getElementById('adresszusatz'+commandpostfix).value=trim(mySplitResult[7]);
      else parent.document.getElementById('adresszusatz'+commandpostfix).value="";

      if(typeof mySplitResult[8] != 'undefined')parent.document.getElementById('ansprechpartner'+commandpostfix).value=trim(mySplitResult[8]);
      else parent.document.getElementById('ansprechpartner'+commandpostfix).value="";

      if(typeof mySplitResult[9] != 'undefined')parent.document.getElementById('titel'+commandpostfix).value=trim(mySplitResult[9]);
      else parent.document.getElementById('titel'+commandpostfix).value="";

      if(trim(mySplitResult[11])!="") parent.document.getElementById('email').value=trim(mySplitResult[11]); 
      else parent.document.getElementById('email'+commandpostfix).value="";

      if(trim(mySplitResult[12])!="") parent.document.getElementById('telefon').value=trim(mySplitResult[12]);
      else parent.document.getElementById('telefon'+commandpostfix).value="";

      if(trim(mySplitResult[13])!="") parent.document.getElementById('telefax').value=trim(mySplitResult[13]);
      else parent.document.getElementById('telefax'+commandpostfix).value="";

      if(trim(mySplitResult[14])!="") parent.document.getElementById('anschreiben').value=trim(mySplitResult[14]);
      else parent.document.getElementById('anschreiben'+commandpostfix).value="";

      if(trim(mySplitResult[15])!="") parent.document.getElementById('gln').value=trim(mySplitResult[15]);
      else parent.document.getElementById('gln'+commandpostfix).value="";
//      if(typeof mySplitResult[10] != 'undefined')parent.document.getElementById('ansprechpartnerid'+commandpostfix).value=trim(mySplitResult[10]);
//      window.parent.abweichend2();
      //			parent.document.getElementById('lieferansprechpartner').value=trim(mySplitResult[0]);

      break;


    case 'getLieferadresse':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(parent.document.getElementById('liefername'+commandpostfix))
      {
        if(parent.document.getElementById('abweichendelieferadresse'+commandpostfix) != null)parent.document.getElementById('abweichendelieferadresse'+commandpostfix).checked = true;

        if(typeof mySplitResult[0] != 'undefined')parent.document.getElementById('liefername'+commandpostfix).value=trim(mySplitResult[0]);
        if(typeof mySplitResult[1] != 'undefined')parent.document.getElementById('lieferabteilung'+commandpostfix).value=trim(mySplitResult[1]);
        if(typeof mySplitResult[2] != 'undefined')parent.document.getElementById('lieferunterabteilung'+commandpostfix).value=trim(mySplitResult[2]);
        //parent.document.getElementById('lieferland').options[parent.document.getElementById('lieferland').selectedIndex].value=trim(mySplitResult[3]);
        //if(typeof mySplitResult[3] != 'undefined')Select_Value_Set('eprooform.lieferland'+commandpostfix,trim(mySplitResult[3]));
        if(typeof mySplitResult[3] != 'undefined')SelectCountry('#lieferland',trim(mySplitResult[3]));
        if(typeof mySplitResult[4] != 'undefined')parent.document.getElementById('lieferstrasse'+commandpostfix).value=trim(mySplitResult[4]);
        if(typeof mySplitResult[5] != 'undefined')parent.document.getElementById('lieferort'+commandpostfix).value=trim(mySplitResult[5]);
        if(typeof mySplitResult[6] != 'undefined')parent.document.getElementById('lieferplz'+commandpostfix).value=trim(mySplitResult[6]);
        if(typeof mySplitResult[7] != 'undefined')parent.document.getElementById('lieferadresszusatz'+commandpostfix).value=trim(mySplitResult[7]);
        if(typeof mySplitResult[8] != 'undefined')parent.document.getElementById('lieferansprechpartner'+commandpostfix).value=trim(mySplitResult[8]);
        if(typeof mySplitResult[11] != 'undefined' && mySplitResult[11]!='')parent.document.getElementById('ustid'+commandpostfix).value=trim(mySplitResult[11]);
        if(typeof mySplitResult[12] != 'undefined' && mySplitResult[12]!='')Select_Value_Set('eprooform.ust_befreit'+commandpostfix,trim(mySplitResult[12]));
        if(typeof mySplitResult[11] != 'undefined' && mySplitResult[13]!='')parent.document.getElementById('lieferbedingung').value=trim(mySplitResult[13]);
        if(typeof mySplitResult[14] != 'undefined' )parent.document.getElementById('lieferemail').value=trim(mySplitResult[14]);
        if(typeof mySplitResult[10] != 'undefined' && parent.document.getElementById("liefergln"+commandpostfix)!=null)parent.document.getElementById('liefergln'+commandpostfix).value=trim(mySplitResult[10]);
        if(typeof mySplitResult[9] != 'undefined'  && parent.document.getElementById("lieferid"+commandpostfix)!=null)parent.document.getElementById('lieferid'+commandpostfix).value=trim(mySplitResult[9]);

        window.parent.abweichend2();
      } else {

        parent.document.getElementById('name').value=trim(mySplitResult[0]);
        parent.document.getElementById('abteilung').value=trim(mySplitResult[1]);
        parent.document.getElementById('unterabteilung').value=trim(mySplitResult[2]);
        //parent.document.getElementById('lieferland').options[parent.document.getElementById('lieferland').selectedIndex].value=trim(mySplitResult[3]);
        Select_Value_Set('eprooform.land',trim(mySplitResult[3]));
        parent.document.getElementById('strasse').value=trim(mySplitResult[4]);
        parent.document.getElementById('ort').value=trim(mySplitResult[5]);
        parent.document.getElementById('plz').value=trim(mySplitResult[6]);
        parent.document.getElementById('adresszusatz').value=trim(mySplitResult[7]);
        parent.document.getElementById('ansprechpartner').value=trim(mySplitResult[8]);
        if($("selector").is("#lieferid"))parent.document.getElementById('lieferid').value=trim(mySplitResult[9]);
        if($("selector").is("#gln"))parent.document.getElementById('gln').value=trim(mySplitResult[10]);
        parent.document.getElementById('ustid').value=trim(mySplitResult[11]);
        Select_Value_Set('eprooform.ust_befreit',trim(mySplitResult[12]));
        parent.document.getElementById('lieferbedingung').value=trim(mySplitResult[13]);
      }
      break;

    case 'fillArtikel':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(myString.length>3)
      {
        render=1;
        var tmp = document.getElementById("artikel").value;

        document.getElementById("artikel").value=trim(mySplitResult[0]);
        document.getElementById("nummer").value=mySplitResult[1];

        if(mySplitResult[1]=="" && tmp!="") {
          alert('In der Schnelleingabe können nur Artikel aus den Stammdaten eingefügt werden. Klicken Sie auf Artikel manuell suchen / neu anlegen.');
        } else if( mySplitResult[1]!="") {

          document.getElementById("projekt").value=mySplitResult[2];
          document.getElementById("preis").value=mySplitResult[3];
          document.getElementById("menge").value=mySplitResult[4];

          document.getElementById("waehrung").value=mySplitResult[5];
          var warnung = 0;
          if(typeof mySplitResult[6] != 'undefined')warnung = parseInt(mySplitResult[6]);
          if((document.getElementById("preis").value==0 || document.getElementById("preis").value=="")&& warnung == 1) {
            document.getElementById('preis').style.background ='#F88687';
            if(once!=1)
              alert('Achtung: Es ist kein Verkaufspreis hinterlegt!');
            once = 1;
            document.getElementById('preis').focus();
          }  else {

            document.getElementById('preis').style.background ='';
          }

            //document.getElementById('preis').setAttribute("readonly", "readonly");
            if(lastartikelnummer!=mySplitResult[1])
            {
              document.getElementById('menge').focus();
              document.getElementById('menge').select();
            }
        }
        lastartikelnummer = mySplitResult[1];
      }
      break;


    case 'fillArtikelBestellung':

      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(myString.length>3)
      {
        render=1;
        document.getElementById("artikel").value=trim(mySplitResult[0]);
        document.getElementById("nummer").value=mySplitResult[1];
        if(mySplitResult[1]=="") { 
          alert('In der Schnelleingabe können nur Artikel aus den Stammdaten eingefügt werden. Klicken Sie auf Artikel manuell suchen / neu anlegen.');
        } else {
          document.getElementById("projekt").value=mySplitResult[2];
          document.getElementById("preis").value=mySplitResult[3];
          document.getElementById("menge").value=mySplitResult[4];
          document.getElementById("bestellnummer").value=mySplitResult[5];
          document.getElementById("bezeichnunglieferant").value=mySplitResult[6];
          document.getElementById("vpe").value=mySplitResult[7];
          document.getElementById("waehrung").value=mySplitResult[8];

          if(lastartikelnummer!=mySplitResult[1]){
            document.getElementById('menge').focus();
            document.getElementById('menge').select();
          }
        }
        lastartikelnummer = mySplitResult[1];
      }
      break;


    case 'fillArtikelLieferschein':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(myString.length>3)
      {
        render=1;
        document.getElementById("artikel").value=trim(mySplitResult[0]);
        document.getElementById("nummer").value=mySplitResult[1];
        if(mySplitResult[1]=="") { 
          alert('In der Schnelleingabe können nur Artikel aus den Stammdaten eingefügt werden. Klicken Sie auf Artikel manuell suchen / neu anlegen.');
        } else {
          document.getElementById("projekt").value=mySplitResult[2];
          document.getElementById("menge").value=mySplitResult[4];

          // Mengefeld selektieren
          if(lastartikelnummer!=mySplitResult[1]){
            document.getElementById('menge').focus();
            document.getElementById('menge').select();
          }
        }
      }
      break;
    case 'fillArtikelProduktion':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(myString.length>3)
      {
        render=1;
        document.getElementById("artikel").value=trim(mySplitResult[0]);
        document.getElementById("nummer").value=mySplitResult[1];
        if(mySplitResult[1]=="") { 
          alert('In der Schnelleingabe können nur Artikel aus den Stammdaten eingefügt werden. Klicken Sie auf Artikel manuell suchen / neu anlegen.');
        } else {
          document.getElementById("projekt").value=mySplitResult[2];
          document.getElementById("menge").value=mySplitResult[4];

          // Mengefeld selektieren
          if(lastartikelnummer!=mySplitResult[1]){
            document.getElementById('menge').focus();
            document.getElementById('menge').select();
          }
        }
      }
      break;

    case 'fillArtikelInventur':
      var myString = xmlHttp.responseText;
      var mySplitResult = myString.split("#*#");
      if(myString.length>3)
      {
        render=1;
        document.getElementById("artikel").value=trim(mySplitResult[0]);
        document.getElementById("nummer").value=mySplitResult[1];
        if(mySplitResult[1]=="") { 
          alert('In der Schnelleingabe können nur Artikel aus den Stammdaten eingefügt werden. Klicken Sie auf Artikel manuell suchen / neu anlegen.');
        } else {


          document.getElementById("projekt").value=mySplitResult[2];
          document.getElementById("preis").value=mySplitResult[3];
          document.getElementById("menge").value=mySplitResult[4];
        }
      }
      break;

  }
  if(render<=0 && command!='getAnsprechpartner' && command!='getAnsprechpartnerLieferschein' && command!='getAdresseStammdatenLieferschein' && command!='getLieferadresse' && command!='getVerzolladresse')
  {
    if(document.getElementById('menge') !=null) document.getElementById("menge").value="";
    if(document.getElementById('nummer') !=null) document.getElementById("nummer").value="";
    if(document.getElementById('projekt') !=null) document.getElementById("projekt").value="";
    if(command!='fillArtikelProduktion')
      if(document.getElementById('preis') !=null) document.getElementById("preis").value="";
  }
}

function trim (zeichenkette) {
  // Erst führende, dann Abschließende Whitespaces entfernen
  // und das Ergebnis dieser Operationen zurückliefern
  return zeichenkette.replace (/^\s+/, '').replace (/\s+$/, '');
}





// globales XMLHttpRequest-Objekt erzeugen
var xmlHttp = getXMLRequester();

/**
 * @param {string} fieldname
 * @param {string} rulename
 * @param {string} mandatoryids
 *
 * @return {number}
 */
function AjaxValidator(fieldname,rulename,mandatoryids)
{
    var fieldvalue = $(fieldname).val();
    var result = 0;

    jQuery.ajax({
        type: 'POST',
        url: 'index.php?module=ajax&action=validator',
        data: { rule: rulename, value: fieldvalue, mandatoryid: mandatoryids },
        dataType: 'json',
        async: false,
        success: function(data) {
            var $field = $(fieldname);
            if(data.error > 0)
            {
              $('<span class="validator_message">').text(data.message).insertAfter($field);
              $field.addClass('validator_field_error');
              $field.data('validated', null);
              result = 1;

              // Scroll to last failed mandatory field
              var offsetTop = $field.offset().top;
              var windowHeight = $(window).height();
              $('html, body').clearQueue().animate({
                  scrollTop: offsetTop - (windowHeight / 2)
              }, 'slow');
            } else {
              $field.removeClass('validator_field_error');
              $field.next('span.validator_message').remove();
              $field.data('validated', 1);
              result = 0;
            }
        }
    });

    return result;
}


function downloadURL(url) {
  //$.get( url, function( data ) {}); 
}


function seriennummern_assistent(menge=1)
{
  var start = prompt('Startnummer:','');
  if(start!='')
  {
    menge = prompt('Menge:',menge);
  }
  menge = parseInt(menge);

  var startlist = start.match(/[0-9]+/g);
  var prefix="";

  if(start.match(/[a-z]/i))
  {
    var startnumber = startlist[startlist.length-1];
    prefix=start.replace(startnumber,'');
    start = startnumber;
  }

  if(menge > 0 && start!='')
  {
    i=0;
    $('input[name^="seriennummer"]').each(function() {
      if(i > menge) return false;
      seriennummer = parseInt(start) + i;
      if($(this).val()=='')
      {
        $(this).val(prefix+seriennummer);
        i++;
      }
    });
  }
}

function DokumentAbschicken(modul,id=0,action='edit')
{
var horizontalPadding = 30;
var ref = 'index.php?module='+modul+'&action='+action;
if(id != 0){
    ref += '&id='+id;
}

    var verticalPadding = 30; $('<iframe id="externalSite" class="externalSite" src="index.php?module='+modul+'&action=abschicken&id='+id+'" width="1000"/>').dialog({
title: 'Abschicken',
      autoOpen: true,
      width:1100,
      height: 800,
      modal: true,
      resizable: true,
      close: function(ev, ui) {window.location.href=ref;}
  }).width(1100 - horizontalPadding).height(800 - verticalPadding);
}

function abopopup(id, pid){
  console.log(id);
  console.log(pid);
  /* e.preventDefault();
            var $this = $(this);
            var horizontalPadding = 30;
            var verticalPadding = 30;
            $('<iframe id="externalSite" class="externalSite" src="' + this.href + '" />').dialog({
                title: ($this.attr('title')) ? $this.attr('title') : 'External Site',
                autoOpen: true,
                width: [POPUPWIDTH],
                height: [POPUPHEIGHT], 
                modal: true,
                resizable: true
            }).width([POPUPWIDTH] - horizontalPadding).height([POPUPHEIGHT] - verticalPadding); */
            
            var horizontalPadding = 30;
    var verticalPadding = 30; $('<iframe id="externalSite" class="externalSite" src="index.php?module=adresse&action=positioneneditpopup&id='+id+'&pid='+pid+'" width="1000"/>').dialog({
title: 'Abschicken',
      autoOpen: true,
      width:1100,
      height: 800,
      modal: true,
      resizable: true,
      close: function(ev, ui) {window.location.href='index.php?module=adresse&action=artikel&id='+pid;}
  }).width(1100 - horizontalPadding).height(800 - verticalPadding);



}

  function changeFavicon(src) {
    var link = document.createElement('link'),
      oldLink = document.getElementById('dynamic-favicon');
    link.id = 'dynamic-favicon';
    link.rel = 'icon';
    link.href = src;
    if (oldLink) {
      document.head.removeChild(oldLink);
    }
    document.head.appendChild(link);
  }
var oTables = {};
$(document).ready(function() {
  $('#print').on('click',function(){ wawisionPrint();});

  servertime = parseFloat( $("#servertime").val() ) * 1000;
  var clockel = $("#clock");
  if(clockel && typeof $("#clock").clock != 'undefined') {
    $("#clock").clock({
      "timestamp": servertime,
      "dateFormat": "\\<\\s\\p\\a\\n\\>\\K\\W W / 52\\<\\/\\s\\p\\a\\n\\> d.m.Y | ",
      "langSet": "de"
    });
  }
  $('head').append('<link id="shortcuticon" rel="shortcut icon" href="./themes/new/images/favicon/favicon.ico" type="image/x-icon">');
  changeFavicon("./themes/new/images/favicon/favicon.ico");
  $('#chatpopup').dialog(
      {
        modal: true,
        autoOpen: false,
        minWidth: 1150,
        height:650,
        title:'Chat',
        buttons: {
          'OK': function() {
            $(this).dialog('close');
          }
        },
        close: function(event, ui){
          $('#chatpopupcontent').html('');
        }
      });

  $('li.hamburger').on('click',function(){
    var aktiv = false;
    var liaktivel = $('#jsddm').find('li.aktiv');
    if(liaktivel.length)aktiv = true;
    if(aktiv)
    {
      $(liaktivel).toggleClass('aktiv',false);
    }else{
      $('#jsddm > li').each(function(){
        if(!$(this).hasClass('hamburger'))$(this).toggleClass('aktiv',true);
      });
    }
  });

  $('#jsddm > li').on('click',function(){
    if($(this).hasClass('aktiv2'))
    {
      $(this).toggleClass('aktiv2',false);
    }else{
      $('#jsddm li ').toggleClass('aktiv2',false);
      $(this).toggleClass('aktiv2',true);
      $(this).children('ul').each(function(){$(this).css('visibility','visible'); });
    }

  });

  $(window).on('scroll', function() {
    var topPos = $(window).scrollTop();
    var newTopPos = 0;
    if (topPos > 80) {
      newTopPos = topPos - 80;
    } else {
      newTopPos = 0;
    }
    $('.toolbarleftInner').css({
      top: newTopPos
    })
  });

  //Fix problem with ckeditor dialogs
  $.ui.dialog.prototype._allowInteraction = function(event) {
    return true;
  };

  checkautocomplete();

    $('.tablesearch_json').each(function(){
        var html = $(this).html();
        var json = JSON.parse(html);
        oTables[json.element] = $('#'+json.element).dataTable(json.config);
    });


    $(window).on('beforeunload', function ()
    {
        var uids = '';
        $('table.dataTable[data-uid]').each(function() {
            if($(this).data('uid')) {
                uids += $(this).data('uid')+',';
            }
        });
        if(uids !== '') {
            $.ajax({
                url: 'index.php?module=ajax&action=killquery',
                method:'post',
                data: {uid:uids},
                success: function(data) {
                    window.console('ok');
                }
            });
        }
    });
});

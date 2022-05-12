var Base64={_keyStr:"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/=",encode:function(e){var t="";var n,r,i,s,o,u,a;var f=0;e=Base64._utf8_encode(e);while(f<e.length){n=e.charCodeAt(f++);r=e.charCodeAt(f++);i=e.charCodeAt(f++);s=n>>2;o=(n&3)<<4|r>>4;u=(r&15)<<2|i>>6;a=i&63;if(isNaN(r)){u=a=64}else if(isNaN(i)){a=64}t=t+this._keyStr.charAt(s)+this._keyStr.charAt(o)+this._keyStr.charAt(u)+this._keyStr.charAt(a)}return t},decode:function(e){var t="";var n,r,i;var s,o,u,a;var f=0;e=e.replace(/[^A-Za-z0-9\+\/\=]/g,"");while(f<e.length){s=this._keyStr.indexOf(e.charAt(f++));o=this._keyStr.indexOf(e.charAt(f++));u=this._keyStr.indexOf(e.charAt(f++));a=this._keyStr.indexOf(e.charAt(f++));n=s<<2|o>>4;r=(o&15)<<4|u>>2;i=(u&3)<<6|a;t=t+String.fromCharCode(n);if(u!=64){t=t+String.fromCharCode(r)}if(a!=64){t=t+String.fromCharCode(i)}}t=Base64._utf8_decode(t);return t},_utf8_encode:function(e){e=e.replace(/\r\n/g,"\n");var t="";for(var n=0;n<e.length;n++){var r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r)}else if(r>127&&r<2048){t+=String.fromCharCode(r>>6|192);t+=String.fromCharCode(r&63|128)}else{t+=String.fromCharCode(r>>12|224);t+=String.fromCharCode(r>>6&63|128);t+=String.fromCharCode(r&63|128)}}return t},_utf8_decode:function(e){var t="";var n=0;var r=c1=c2=0;while(n<e.length){r=e.charCodeAt(n);if(r<128){t+=String.fromCharCode(r);n++}else if(r>191&&r<224){c2=e.charCodeAt(n+1);t+=String.fromCharCode((r&31)<<6|c2&63);n+=2}else{c2=e.charCodeAt(n+1);c3=e.charCodeAt(n+2);t+=String.fromCharCode((r&15)<<12|(c2&63)<<6|c3&63);n+=3}}return t}}
var youtubeifr;
var tooltips = false;

function showwawitooltip(element, ind) {
    $('#wawitooltipdivhtml' + ind).html(Base64.decode(tooltips[ind].wert));
    var left = $(element).offset().left;
    var top = $(element).offset().top;
    var img = $(element).children('img').first();
    if (img) $(img).attr('src', "./themes/new/images/tooltip_blau.png");

    // Marco
    $('#wawitooltipdivhtml' + ind).css({
        'background': '#53BED0',
        'color': '#fff',
        'font-size': '12px',
        'border': '0px',
        'border-radius': '0 5px 5px 5px',
        'position': 'relative',
        'top': '-17px',
        'left': '23px',
        'padding': '12px',
        'min-width': '150px'
    });
    $('#wawitooltipdivhtml' + ind).show();
    var elem = $('#wawitooltipdivhtml' + ind).parents('div.ui-dialog').first();
    if (elem.length) {
        var offsetdiv = elem.offset();
        var widthdiv = elem.width();
        var offsetelem = $('#wawitooltipdivhtml' + ind).offset();
        if (offsetdiv.left + widthdiv - 200 < offsetelem.left) {
            $('#wawitooltipdivhtml' + ind).css({
                'background': '#53BED0',
                'color': '#fff',
                'font-size': '12px',
                'border': '0px',
                'border-radius': '5px 0px 5px 5px',
                'position': 'relative',
                'top': '7px',
                'left': '-170px',

                'padding': '12px',
                'min-width': '150px'
            });
        }
    } else {
        elem = $('#page_container');
        var offsetdiv = elem.offset();
        var widthdiv = elem.width();
        var offsetelem = $('#wawitooltipdivhtml' + ind).offset();
        if (offsetdiv.left + widthdiv - 200 < offsetelem.left) {
            $('#wawitooltipdivhtml' + ind).css({
                'background': '#53BED0',
                'color': '#fff',
                'font-size': '12px',
                'border': '0px',
                'border-radius': '5px 0px 5px 5px',
                'position': 'relative',
                'top': '7px',
                'left': '-170px',

                'padding': '12px',
                'min-width': '150px'
            });
        }
    }
}

function hidewawitooltip(ind, element) {
    $('#wawitooltipdivhtml' + ind).fadeOut(300); // Marco: fadeOut(300) statt hide();
    var img = $(element).children('img').first();
    if (img) $(img).attr('src', "./themes/new/images/tooltip_grau.png");
}

function showinlinehelp() {
    //$("#inlinehelp").dialog('open');
    $('#inlinehelp img.wikigetfile').each(function(){
       $(this).toggleClass('wikigetfile', false);
       $(this).attr(
           'src',
           'index.php?module=wiki&action=getfile&workspacefolder=XentralHandbuch&article='
           +$(this).data('article')
           +'&fileid='+$(this).data('fileid')
       );
    });

    $('#inlinehelp img[data-src]').each(function(){
        $(this).attr(
            'src',
            $(this).data('src')
        );
        $(this).removeData('src');
    });
    $('#inlinehelp').show();
    $('#inlinehelpcontent img').each(function(){
       if($(this).css('height')+'' !== '' && $(this).css('width')+'' !== '') {
           $(this).css('height','auto');
       }

       $(this).on('click',function(){
           if(this.clientWidth >= 670) {
               $('#inlinehelpimg').html($(this).clone());
               $('#inlinehelpimg').dialog('open');
           }
       });

    });
    $('#inlinehelp').find('iframe').each(function () {
        var ifrid = this.id;
        if (typeof youtubeifr != 'undefined' && typeof youtubeifr[ifrid] != 'undefined') $(this).attr('src', youtubeifr[ifrid]);
    });
    $('#inlinehelp').css('box-shadow', '0 0 21px rgba(0, 0, 1, 0.3)');
    $('#inlinehelp').css('background-color', '#fff');
    $('#inlinehelp').css('z-index', 9000);
    $('#inlinehelp').on('actionancor',function(){
        if($('#inlinetab').data('action')+'' != '') {
            var ancor = $('#inlinetab').data('action')+'';
            if($('#inlinetab #'+ancor).length) {
                var elpos = $('#inlinetab #'+ancor).position();
                elpos = typeof elpos.top != 'undefined'?elpos.top:0;
                if(elpos == 0) {
                    return;
                }

                setTimeout(function(ancor){
                    $('#inlinehelpcontent').animate(
                        {scrollTop :$('#inlinetab #'+ancor).position().top - $('#inlinehelpcontent').scrollTop() - 100},(1000)
                    ).animate(
                        {scrollTop :$('#inlinetab #'+ancor).position().top - $('#inlinehelpcontent').scrollTop() - 100},(1000)
                    );
                },1000, ancor);

            }
        }

        $('ul.firstlvl li a').on('click',function(){
            var datalvl = $(this).data('lvl');
            if(typeof datalvl == 'undefined') {
                return false;
            }
            var hs =  $('#inlinehelpcontent').find('h'+datalvl);
            if(hs.length === 0) {
                return false;
            }

            if(hs.length === 1) {
                $('#inlinehelpcontent').animate(
                    {scrollTop :$(hs).position().top - $('#inlinehelpcontent').scrollTop() - 100},(1000)
                ).animate(
                    {scrollTop :$(hs).position().top - $('#inlinehelpcontent').scrollTop() - 100},(1000)
                );

                return false;
            }

            for(i = 0; i < hs.length; i++) {
                if($(hs[i]).html() == $(this).html()) {
                    $('#inlinehelpcontent').animate(
                        {scrollTop :$(hs[i]).position().top - $('#inlinehelpcontent').scrollTop() - 100},(1000)
                    ).animate(
                        {scrollTop :$(hs[i]).position().top - $('#inlinehelpcontent').scrollTop() - 100},(1000)
                    );
                    return false;
                }
            }
        });
    });

    $('#inlinehelp').animate({
        width: '475px'
    }).trigger('actionancor');

    $.ajax({
        url: 'index.php?module=ajax&action=autosaveuserparameter',
        type: 'POST',
        dataType: 'json',
        data: {
            name: 'tooltipinline_autoopen',
            value: Base64.encode('1')
        }
    });
}

function wawitooltipicon(ind, zindex) {
    if (!zindex) zindex = 1000;
    var zindexContent = zindex + 1;
    var html =
        '<div id="wawitooltipdiv' + ind + '" style="position: absolute; display: inline; padding-left: 5px; " onmouseover="showwawitooltip(this, ' + ind + ')" onmouseleave="hidewawitooltip(' + ind + ', this);"  onclick="hidewawitooltip(' + ind + ')" >' +
        '<img src="./themes/new/images/tooltip_grau.png" border="0" style="position: relative; left: 1px; top: 3px; z-index: ' + zindex + ';" class="wawitooltipicon" />' +
        '<div id="wawitooltipdivhtml' + ind + '" style="z-index: ' + zindexContent + ';"></div>' +
        '</div>' +
        '<span style="width:25px;position:relative;height:20px;display:inline-block;">&nbsp;</span>';
    return html;
}

$(document).ready(function () {
    var jsontext = $('#youtubejson').html();

    if (typeof jsontext != 'undefined' && jsontext)
        youtubeifr = JSON.parse(jsontext);
    var tooltipjson = $('#tooltipjson').html();
    if (tooltipjson)
        tooltips = JSON.parse(tooltipjson);
    if (typeof $("#inlinehelp").dialog != 'undefined') {
        $('#inlinetab2').hide();
        $('#inlinetab3').hide();

        $('#inlinehelpclose').on('click', function () {
            $('#inlinehelp').animate({
                height: '100vh',
                width: '0'
            });
        });

        $('#inlinehelpmenu div.rTabs a.inlinecontentlink').on('click',function(){
            $('#inlinetab2').hide();
            $('#inlinetab3').hide();
            $('#inlinetab').show();
            $(this).parent().parent().find('li').toggleClass('aktiv', false);
            $(this).parent().toggleClass('aktiv', true);
        });
        $('#inlinehelpmenu div.rTabs a.inlinefaqlink').on('click',function(){
            $('#inlinetab').hide();
            $('#inlinetab3').hide();
            $('#inlinetab2').show();
            $(this).parent().parent().find('li').toggleClass('aktiv', false);
            $(this).parent().toggleClass('aktiv', true);
        });
        $('#inlinehelpmenu div.rTabs a.inlinehandbooklink').on('click',function(){
            $('#inlinetab').hide();
            $('#inlinetab2').hide();
            $('#inlinetab3').show();
            $(this).parent().parent().find('li').toggleClass('aktiv', false);
            $(this).parent().toggleClass('aktiv', true);
        });
        //WAWIIF VERSION=DEV
        $('#inlinehelpmenu div.rTabs a.inlinehandbooklink').trigger('click');
        //WAWIEND


        if($( "#faqaccordion" ).length) {
            $("#faqaccordion")
            .accordion({

                header: "> div > h2",
                heightStyle: "content"
            })
            .sortable({
                axis: "y",
                handle: "h2",
                stop: function (event, ui) {
                    // IE doesn't register the blur when sorting
                    // so trigger focusout handlers to remove .ui-state-focus
                    ui.item.children("h2").triggerHandler("focusout");

                    // Refresh accordion to handle new order
                    $(this).accordion("refresh");
                }
            });
        }
    }

    if (tooltips) {
        $(tooltips).each(function (kk, vv) {
            if (typeof vv.key != 'undefined' && typeof vv.wert != 'undefined') {
                var k = Base64.decode(vv.key);
                if (k.substring(0, 1) == '#') {
                    $(k).each(function () {
                        var elem = $(this).parents('div.ui-dialog').first();
                        $(this).after(wawitooltipicon(kk, (elem.length ? 1000 : 8)));
                    });
                } else if (k.substring(0, 1) == '.') {
                    $(k).each(function () {
                        var elem = $(this).parents('div.ui-dialog').first();
                        $(this).after(wawitooltipicon(kk, (elem.length ? 1000 : 8)));
                    });
                } else {
                    if (k.indexOf('>') >= 0 || k.indexOf(']') >= 0 || k.indexOf(' ') >= 0 || k.indexOf(',') >= 0 || k.indexOf('+') >= 0) {
                        $(k).each(function () {
                            var elem = $(this).parents('div.ui-dialog').first();
                            $(this).after(wawitooltipicon(kk, (elem.length ? 1000 : 8)));
                        });
                    } else {
                        $('input[name="' + k + '"]').each(function () {
                            var elem = $(this).parents('div.ui-dialog').first();
                            $(this).after(wawitooltipicon(kk, (elem.length ? 1000 : 8)));
                        });
                        $('select[name="' + k + '"]').each(function () {
                            var elem = $(this).parents('div.ui-dialog').first();
                            $(this).after(wawitooltipicon(kk, (elem.length ? 1000 : 8)));
                        });
                        $('textarea[name="' + k + '"]').each(function () {
                            var elem = $(this).parents('div.ui-dialog').first();
                            $(this).after(wawitooltipicon(kk, (elem.length ? 1000 : 8)));
                        });
                    }
                }
            }
        });
    }

    $('.youtubeoverlay').on('click', function () {
        var hauptsrc = $('.youtubehaupt').find('iframe').first().attr('src');
        var klicksrc = $(this).parent().find('iframe').first().attr('src');
        var haupttitel = $('.youtubehaupttitel').first().html();
        var subtitel = $(this).parents('.youtubeweitere').first().find('.youtubesubtitel').first().html();
        if (klicksrc.indexOf('&autoplay=1') < 0) klicksrc += '&autoplay=1';
        if (hauptsrc.indexOf('&autoplay=1') >= 0) hauptsrc = hauptsrc.replace('&autoplay=1', '');
        if (hauptsrc && klicksrc) {
            $('.youtubehaupt').find('iframe').first().attr('src', klicksrc);
            $(this).parent().find('iframe').first().attr('src', hauptsrc);
            $('.youtubehaupttitel').first().html(subtitel);
            $(this).parents('.youtubeweitere').first().find('.youtubesubtitel').first().html(haupttitel);
        }
    });

    $('.pfeillinks').on('click', function () {
        var youtubeiframes = $('.youtubeweiterecontainer').find('iframe');
        var anzyoutoube = youtubeiframes.length;
        var firstpos = $(youtubeiframes[0]).offset();
        var maxpos = $(youtubeiframes[1]).offset();
        var aktleft = $(youtubeiframes[0]).position();
        var aktleft = $('.youtubeweiterecontainerinner').position();
        var newleft = aktleft.left + 400;
        if (newleft > 0) newleft = 0;
        $('.youtubeweiterecontainerinner').animate({
            left: newleft
        }, 500);
    });

    $('.pfeilrechts').on('click', function () {
        var youtubeiframes = $('.youtubeweiterecontainer').find('iframe');
        var anzyoutoube = youtubeiframes.length;
        var firstpos = $(youtubeiframes[0]).offset();
        var maxpos = $(youtubeiframes[1]).offset();
        var aktleft = $('.youtubeweiterecontainerinner').position();
        var newleft = aktleft.left - 400;
        if (newleft < 560 - ((maxpos.left - firstpos.left) * (anzyoutoube))) {
            newleft = 560 - ((maxpos.left - firstpos.left) * (anzyoutoube));
        }

        $('.youtubeweiterecontainerinner').animate({
            left: newleft
        }, 500);
    });
    if($('div#inlinehelp.inlineautoopen').length) {
        showinlinehelp();
    }

    $('#inlinehelpclose').on('click',function(){
        $.ajax({
            url: 'index.php?module=ajax&action=autosaveuserparameter',
            type: 'POST',
            dataType: 'json',
            data: {
                name: 'tooltipinline_autoopen',
                value: Base64.encode('0')
            }
        });
    });

    $('#inlinehelpimg').dialog(
        {
            modal: true,
            autoOpen: false,
            minWidth: 1140,
            title:'',

            close: function(event, ui){

            },
            open: function(event,ui) {
                $(this).parent().css('z-index','9990');
            }
        });

    if($('#inlinehelphonenumber').length > 0 && $('#inlinehelphonenumber').data('sipgate')+'' === '1') {
        $('#inlinehelphonenumber').on('click', function() {
            $.ajax({
                url: 'index.php?module=sipgate&action=call&target=' + $('#inlinehelphonenumber').data('phonenumber'),
                type: 'POST',
                dataType: 'json',
                data: {},
                success: function (data) {
                    if (data) {

                    }
                }
            });
        });
    }
    $('#showinlinehelplink').on('click', function(){
        showinlinehelp();
    });
});

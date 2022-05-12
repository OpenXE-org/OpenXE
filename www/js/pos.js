var randnumber = (Math.random() * (99999999 - 10000000)) + 10000000;
var belegabrechnenaktiv = false;
var grabatt = 0;
var grabatteur = 0;
var lkaddr = '0';
var waehrung = 'EUR';
var zahlungselzwang = false;
var kassiererId = 0;
var grabattSaved = 0;
var steuerfrei = false;
var ustid = '';
var saveCust = false;
var logouttimer = null;
var logouttimerabschluss = null;

var is_chrome = navigator.userAgent.toLowerCase().indexOf('chrome') > -1;

function calcrueckgeld() {
  var isTip = !!$('#tip:visible').length;

  var rueckgeld = '';
  if(isTip) {
    var betrag2 = $('#betrag').val().replace(',', '.');
    var tip = $('#tip').val().replace(',', '.');
    if (!isNaN(betrag2) && !isNaN(tip)) {
      rueckgeld = (parseFloat(tip) + parseFloat(betrag2)).toFixed(2);
      if (rueckgeld < 0 || isNaN(rueckgeld)) {
        rueckgeld = '';
      } else {
        rueckgeld = rueckgeld.replace('.', ',');
      }
      $('#zahlbetrag').val(rueckgeld);
    }
  }
  else {
    var betrag = $('#zahlbetrag').val().replace(',', '.');
    var gezahlt = $('#gezahlt').val().replace(',', '.');

    if (!isNaN(betrag) && !isNaN(gezahlt)) {
      rueckgeld = (parseFloat(gezahlt) - parseFloat(betrag)).toFixed(2);
    }
    if (rueckgeld < 0 || isNaN(rueckgeld)) {
      rueckgeld = '';
    } else {
      rueckgeld = rueckgeld.replace('.', ',');
    }
    $('#rueckgeld').val(rueckgeld);
  }
}

function kontoauswahl() {
  $('#kontoauswahldiv').dialog('close');
  var wert = $('#kontoauswahlwert').val();
  var grund = $('#kontoauswahlgrund').val();
  var typ = $('#kontoauswahltyp').val();
  kontoauswahlbuchen(wert,grund,typ);
}

function zvtcheckConnection()
{
  var zvtConnectionOk = false;
  $.ajax({
    url: 'index.php?module=pos&action=list&cmd=checkconnection',
    type: 'POST',
    dataType: 'json',
    async:false,
    data: {
      uid: Math.floor((Math.random() * 90000000) + 10000000)
    },
    success: function (jdata) {
      zvtConnectionOk = jdata.status;
      if(typeof jdata.message != 'undefined') {
        alert(jdata.message);
      }
    }
  });

  return zvtConnectionOk;
}

function zvtsend()
{
  var ptype = $('input[name=ptype]:checked').val();
  var zahlbetrag = parseFloat($('#zahlbetrag').val().replace(',', '.'));
  var intervalzvt = setInterval(function(){
    $.ajax({
      url: 'index.php?module=pos&action=list&cmd=getpaymentstatus&uid=',
      type: 'POST',
      dataType: 'json',
      data: {
      },
      success: function (jdata) {
        if(typeof jdata.intermediatestatus != 'undefined') {
          $('#zvtinfo').html(jdata.intermediatestatus);
        }
      }
    });
  },1000);
  if(zahlbetrag > 0 && (ptype === 'ec' || ptype === 'kredit')) {
    $('#zvtinfo').html('');

    $.ajax({
      url: 'index.php?module=pos&action=list&cmd=updatetotals&uid=',
      type: 'POST',
      dataType: 'json',
      data: {total: zahlbetrag, uid: Math.floor((Math.random() * 90000000) + 10000000)},
      success: function (jdata) {
        $('#zvtinfo').html('');
        if(intervalzvt) {
          clearInterval(intervalzvt);
        }
      }
    });
  }
}

function finsale_click()
{
  var logouttimeabschluss = parseInt($('#logoutkas').data('logouttimeabschluss'));

  var ktype = $('input[name=ktype]:checked').val();
  var ptype = $('input[name=ptype]:checked').val();

  var totalAmount = $('#sutotal').text().replace(',', '.');

  totalAmount = parseFloat(totalAmount);
  if (totalAmount < 0) {
    alert('Die Gesamtsumme darf nicht kleiner als 0,00 EUR sein.');
    return false;
  }

  if ( ktype === 'sk' && $('#adresse').val().length == 0 ) {
    $("#skempty").dialog('open');
    return false;
  }
  else if ( ktype === 'sk' && $('#adresse').val().length > 0 && !checkStammkunde($('#adresse').val(), kassiererId, totalAmount)) {
    alert('Stammkunde nicht korrekt.');
    return false;
  }
  else if(ktype === 'lk' && !checkLaufkundschaft(kassiererId)) {
    alert('Es ist keine Adresse als Laufkundschaft eingetragen.');
    return false;
  }
  else if(ktype === 'lk' && $('#loadlauf').data('checkamountthreshold') == '1' && totalAmount >= 200) {
    alert('Bei Summen größer als 200 EUR muss eine Kundenadresse angegeben werden');
    return false;
  }
  if(ptype === 'ec' || ptype === 'kredit') {
    if($('tr.zvtrow').length) {
      $('#zvtinfo').html('');
      $('tr.zvtrow').show();
    }
    /*$.ajax({
      url: 'index.php?module=pos&action=list&cmd=updatetotals&uid=',
      type: 'POST',
      dataType: 'json',
      data: {total: totalAmount,uid:Math.floor((Math.random() * 90000000) + 10000000)},
      success: function (jdata) {

      }
    });*/
  }
  else {
    if($('tr.zvtrow').length) {
      $('tr.zvtrow').hide();
    }
  }
  if((ktype==='nk' && !$("#t_name").html()) || $('#wk > tbody > tr').length == 0) {
    $("#emptywarn").dialog('open');
  }
  else if(zahlungselzwang && !$('#payment input[name=ptype]:checked').val()) {
    $('#zahlwarn').dialog('open');
  }
  else {
    $("#gezahlt").val('');
    $("#rueckgeld").val('');
    $('#tip').val('0');
    $("#finconf").dialog('open');

    //$.post( "index.php?module=pos&action=display", { total: $('#sutotal').html() } ); 

    $('#zahlbetrag').val($('#sutotal').html());
    if(ptype==='bar')
    {
      $('tr.trbar').css('display','');
      $('tr.trec').css('display','none');
      $('table.numblock').css('display','');
      $('#gezahlt').focus();
    } else {
      if(ptype === 'ec' || ptype === 'kredit') {
        $('table.numblock').css('display','');
        $('tr.trec').css('display','');
        $('#tip').focus();
        $('#betrag').val($('#sutotal').html());
      }
      else {
        $('table.numblock').css('display','none');
        $('#gezahlt').focus();
      }
      $('tr.trbar').css('display','none');



      var btn = $("#finconf").next().find('button').first().next();
      if(btn !== null)
      {
        btn.focus();
        setTimeout(function(){btn.focus();},50);
        $("#finconf").parent().find('*').on('keypress',function(event){
          if (event.which == 13) {
            $("#finconfconf").dialog('close');
            $('#artikelnummerprojekt').focus();
          }
        });
      }
    }
  }
  return false;
}

function kontoauswahlbuchen(wert,grund,typ) {
  if(typ === 'entnahme')
  {
    if(!grund) {
      $('#entnahme').prop('checked', false);
      alert('Sie müssen einen Grund eingeben. Entnahme wurde nicht gebucht.');
      return;
    }
    randnumber = (Math.random() * (99999999 - 10000000)) + 10000000;
    var storeobj = {};
    storeobj['kasid'] = $('#tabs-1').data('on');

    storeobj['addr'] = {};

    storeobj['addrid'] = '';

    storeobj['wk'] = [];
    var nart = {};
    nart['id']      = "entnahme";
    nart['artikel'] = "entnahme";
    nart['kurztext_de'] = 'Entnahme: ' + grund;
    nart['nummer']  = "entnahme";
    nart['tax']     = '0';
    nart['preis']   = wert;

    nart['amount']  = '1';
    nart['rabatt']  = '0';

    storeobj['wk'].push(nart);

    storeobj['ptype']   = "bar";
    storeobj['rtype']   = "entnahme";

    storeobj['inbem']   = $('#inbem').val();
    storeobj['freit']   = $('#freit').val();

    storeobj['grabatt'] = '0';
    storeobj['grabatteur'] = '0';

    storeobj['kassiererId'] = kassiererId;
    storeobj['randnumber'] = randnumber;
    var jsonString = JSON.stringify(storeobj);

    $.ajax({
      url: 'index.php?module=pos&action=finsess',
      type: 'POST',
      dataType: 'json',
      data: { sessdata: jsonString},
      success: function(jdata) {
        if(typeof jdata.error != 'undefined')
        {
          alert(jdata.error);
        }else{
          $('#entconf').dialog('open');
        }
        $('#artikelnummerprojekt').focus();
        $('#entnahme').prop('checked', false);
        if(typeof reloadsite != 'undefined'){
          reloadsite();
        }
      }
    });
  }else if(typ === 'einlage')
  {
    if(!grund) {
      $('#einlage').prop('checked', false);
      alert('Sie müssen einen Grund eingeben. Einlage wurde nicht gebucht.');
      return;
    }
    randnumber = (Math.random() * (99999999 - 10000000)) + 10000000;
    var storeobj = {};
    storeobj['kasid'] = $('#tabs-1').data('on');

    storeobj['addr'] = {};

    storeobj['addrid'] = "";

    storeobj['wk'] = [];
    var nart = {};
    nart['id']      = "einnahme";
    nart['artikel'] = "einnahme";
    nart['kurztext_de'] = 'Einlage: ' + grund;
    nart['nummer']  = "einnahme";
    nart['tax']     = '0';
    nart['preis']   = wert;

    nart['amount']  = '1';
    nart['rabatt']  = '0';

    storeobj['wk'].push(nart);

    storeobj['ptype']   = "bar";
    storeobj['rtype']   = "einlage";

    storeobj['inbem']   = $('#inbem').val();
    storeobj['freit']   = $('#freit').val();

    storeobj['grabatt'] = '0';
    storeobj['grabatteur'] = '0';

    storeobj['kassiererId'] = kassiererId;
    storeobj['randnumber'] = randnumber;
    var jsonString = JSON.stringify(storeobj);

    $.ajax({
      url: 'index.php?module=pos&action=finsess',
      type: 'POST',
      dataType: 'json',
      data: { sessdata: jsonString},
      success: function(jdata) {
        $('#einconf').dialog('open');
        //$('#artikelnummerprojekt').focus();
        $('#einlage').prop('checked', false);
        if(typeof reloadsite != 'undefined')reloadsite();
      }
    });
  }
}

function getKonto(el)
{
  var grund = $(el).parents('tr').first().find('td').first().text();
  if(grund+'' !== '') {
    $('#kontoauswahlgrund').val(grund);
    kontoauswahl();
  }
}

function entnahmeclick(wert)
{
  if($('#kontoauswahldiv').length > 0) {
    $('#kontoauswahltyp').val('entnahme');
    $('#kontoauswahlwert').val(wert);
    $('#kontoauswahllegend').text('Betrag für Entnahme: ' + wert + ' EUR');
    $('#kontoauswahldiv').dialog('open');
    $('#kontoauswahlgrund').focus();
  }else{
    var grund = prompt('Entnahme(' + wert + ' EUR): Grund?');
    kontoauswahlbuchen(wert, grund, 'entnahme');
  }
}

function einlageclick(wert)
{
  if($('#kontoauswahldiv').length > 0) {
    $('#kontoauswahltyp').val('einlage');
    $('#kontoauswahlwert').val(wert);
    $('#kontoauswahllegend').text('Betrag für Einlage: ' + wert + ' EUR');
    $('#kontoauswahldiv').dialog('open');
    $('#kontoauswahlgrund').focus();
  }else {
    var grund = prompt('Einlage(' + wert + ' EUR): Grund?');
    kontoauswahlbuchen(wert, grund, 'einlage');
  }

}

function checkLogoutTimeout() {
  if (logouttimer) {
    clearTimeout(logouttimer);
  }
  var logouttime = parseInt($('#logoutkas').data('logouttime'));
  if(logouttime > 0) {
    logouttimer = setTimeout(function () {
      if(kassiererId && !logouttimerabschluss) {
        $('#logoutkas').trigger('click');
      } else{
        if(kassiererId) {
          checkLogoutTimeout();
        }
      }
    }, logouttime * 1000);
  }
}

$(document).ready(function(){
  checkLogoutTimeout();
  $('*').on('click',function() {
    checkLogoutTimeout();
  });
  $('*').on('keydown',function() {
    if(logouttimerabschluss) {
      clearTimeout(logouttimerabschluss);
    }
    checkLogoutTimeout();
  });
  $('input[name=ptype]').on('change',function() {
    var $trinkgeld = $('#trinkgeld');
    if($trinkgeld !== undefined) {
      var eccredit = $($trinkgeld).data('eccredit');
      var ptype = $('input[name=ptype]:checked').val();
      if(typeof ptype == 'undefined') {
        ptype = '';
      }
      if(typeof eccredit != 'undefined' && eccredit == '1') {
        if(ptype !== 'ec' && ptype !== 'kredit') {
          $($trinkgeld).prop('disabled', true);
          $($trinkgeld).toggleClass('grey', true);
        } else {
          $($trinkgeld).prop('disabled', false);
          $($trinkgeld).toggleClass('grey', false);
        }
      }
    }
  });

  $('input[name=ptype]').first().trigger('change');

  $('#grabatt').css({
    display: 'none'
  });

  // dialoge
  $("#storeconfconf, #entconf, #emptywarn, #zahlwarn, #einconf").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      'OK': function() {
        $(this).dialog('close');
        $('#artikelnummerprojekt').focus();
      }
    }
  });

  $("#finconfconf").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      'OK': function() {
        $(this).dialog('close');
        $('#artikelnummerprojekt').focus();
        setAbschlusstimer();
      }
    },open: function( event, ui ) {
      var btn = $(this).next().find('button').first();
      if(btn !== null)
      {
        btn.focus();
        setTimeout(function(){btn.focus();},50);
        $(this).parent().find('*').on('keypress',function(event){
          if (event.which == 13) {
            $("#finconfconf").dialog('close');
            $('#artikelnummerprojekt').focus();
            setAbschlusstimer();
          }
        });
      }
    }
  });

  $.ajax({
    url: 'index.php?module=pos&action=list&cmd=getuserinfo',
    type: 'POST',
    dataType: 'json',
    data: {},
    success: function(data) {
      if(data === 'admin'){

        $("#kashin").dialog({
          modal: true,
          bgiframe: true,
          closeOnEscape:false,
          autoOpen: false,
          width:"350px",
          dialogClass: "no-close",
          open: function () {
            $(this).off('submit').on('submit', function () {
              var kanr = $('#kanr2').val();
              loginkas(kanr);
              $(this).dialog('close');
              return false;
            });
          },
          buttons: {
            'OK': function() {
              loginkas($('#kanr2').val());
              $(this).dialog('close');
              $('#artikelnummerprojekt').focus();
            },
            'ABBRECHEN': function() {
              $(this).dialog('close');
              window.location.href = 'index.php';
            }
          }
        });
      }else{
        $("#kashin").dialog({
          modal: true,
          bgiframe: true,
          closeOnEscape:false,
          autoOpen: false,
          dialogClass: "no-close",
          open: function () {
            $(this).off('submit').on('submit', function () {
              var kanr = $('#kanr2').val();
              loginkas(kanr);
              $(this).dialog('close');
              $('#artikelnummerprojekt').focus();
              return false;
            });
          },
          buttons: {
            'OK': function() {
              loginkas($('#kanr2').val());
              $(this).dialog('close');
              $('#artikelnummerprojekt').focus();
            }
          }
        });
      }
    },
    beforeSend: function() {

    }
  });


  $("#resetconf").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      'NEIN': function() {
        $(this).dialog('close');
      },
      'JA': function() {

        storePOSSession("resetsess");

        $(this).dialog('close');
        $('#artikelnummerprojekt').focus();
      }
    }
  });

  $("#skempty").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      'OK': function() {
        $(this).dialog('close');
        $('#artikelnummerprojekt').focus();
      }
    }
  });
  
  $("#finconf").dialog({
    modal: true,
    minWidth:800,
    minHeight:400,
    bgiframe: true,
    closeOnEscape:false,
    autoOpen: false,
    buttons: {
      'NEIN': function() {
        $(this).dialog('close');
        $('#artikelnummerprojekt').focus();
      },
      'JA': function() {
        var ptype = $('input[name=ptype]:checked').val();
        if(ptype === 'bar')
        {
          var betrag = $('#zahlbetrag').val().replace(',','.');
          var gezahlt = $('#gezahlt').val().replace(',','.');
          if(gezahlt == '') {
            gezahlt = 0;
          }
          var rueckgeld = (parseFloat(gezahlt) - parseFloat(betrag));
          if(rueckgeld < 0 || isNaN(gezahlt) || isNaN(rueckgeld))
          {
            alert('Es wurde zu wenig eingezahlt - der Vorgang kann nicht abgeschlossen werden!');
            return;
          }
          $.ajax({
            url: '',
            dataType: 'json',
            async: false,
            data: {
              module: 'pos',
              action: 'loadkassstand',
              kassiererId: kassiererId
            },
            success: function(jdata) {
              kontostand = parseFloat(jdata.kontostand);
            }
          });
          if(kontostand < rueckgeld)
          {
            alert('Sie haben nicht ausreichend Rückgeld in der Kasse.');
            return;
          }
        }
        if(ptype === 'ec' || ptype === 'kredit') {
          var tip = $('#tip').val().replace(',','.');
          if(tip == '') {
            tip = 0;
          } else {
            tip = parseFloat(tip);
          }

          if(tip > 0) {
            var kontostand = 0;
            var konto = 0;
            $.ajax({
              url: '',
              dataType: 'json',
              async: false,
              data: {
                module: 'pos',
                action: 'loadkassstand',
                kassiererId: kassiererId
              },
              success: function(jdata) {
                kontostand = parseFloat(jdata.kontostand);
                konto = jdata.konto;
              }
            });
            if(kontostand < tip) {
              if(konto == 0) {
                alert('Es ist kein Kassenbuch hinterlegt.');
                return;
              }
              else {
                alert('Sie haben nicht ausreichend Bargeld in der Kasse.');
                return;
              }
            }
          }
          if(tip < 0 || isNaN(tip))
          {
            alert('Es wurde ein negatives Trinkgeld ausgewählt - der Vorgang kann nicht abgeschlossen werden!');
            return;
          }
        }
        storePOSSession(belegabrechnenaktiv?'belegabrechnen':'finsess');
        resetFields();
        $(this).dialog('close');
      }
    }
  });
  
  
  resetFields();
  
  // check for active session
  $.getJSON( '', {
    module: "pos",
    action: "checkkass"
  }).done(function( jdata ) {

    if(jdata.check!=='ERR') {
      kassiererId = jdata.kid;
      $('#kassiererId').val(kassiererId);
      kanr = jdata.kid;
      $('#tabs-1 input').removeAttr('disabled');
      $('#tabs-1').data('on',kanr);
      lkaddr = jdata.lkadresse; 
      // $('#logoutkas').show();
      $('#loggedinkas').html(' ' + jdata.kname);
      $('#filiale').html(' ' + jdata.filiale);
      //$('#kanr').html(kanr);
      loadPOSSession(kanr);
      $('#artikelnummerprojekt').focus();
    } else {
      $('#kashin').dialog('open');
    }
  });

  waehrung = $('#waehrung').html();
  
  // felder sperren bis sich Kassierer anmeldet
  //$('#tabs-1 input').prop('disabled','disabled'); -- by bene, duke 29.03.
  $('#loadkass input').removeAttr('disabled');
  
  
  $('#rabatt2').on('click',function() {
    var ggrabatt = prompt("Rabatt in %");
    if(!ggrabatt) {
      ggrabatt = 0;
    } else if (ggrabatt > 100) {
      ggrabatt = 0;
    }

    grabattSaved = ggrabatt;

    $('#wk tbody .rabatt').val(ggrabatt);
    $('#grabatt').html(ggrabatt+'%');
    
    grabatt = 0;
    $('#wk > tbody > tr').each(function() {
      updatearttotal($(this));
    });
    updatetotals();
    grabatt = 0;

  });

  // @todo
  $('#rabatteur').on('click',function() {
    grabatteur = prompt("Rabatt in " + waehrung);
    if(!grabatteur) {
      grabatteur = '0,00';
    }
    grabatteur = parseFloat(grabatteur.replace(',','.'));
    
    if (grabattSaved != 0) {
      grabatt = 0;
      ggrabatt = grabattSaved;

    }
    $('#wk > tbody > tr').each(function() {
      updatearttotal($(this));
    });
    updatetotals();
    grabatt = 0;
  });


  $('#schublade').on('click',function() {

    $.ajax({
      url: '',
      dataType: 'json',
      async: false,
      data: {
        module: 'pos',
        action: 'ladeoeffnen',
        kassiererId: kassiererId,
        rueckgeld: $('#rueckgeld').val()
      },
      success: function(jdata) {
        //kontostand = jdata.kontostand;
      }
    });

      $('#schublade').prop('checked', false);

  });

  $('#einlage').on('click',function(){
    var wert = prompt("Wert in " + waehrung + "?");
    if(!wert || wert <= 0) {
      $('#einlage').prop('checked', false);
      return;
    }
    wert = parseFloat(wert.replace(',','.'));

    einlageclick(wert);
    
    /*$.getJSON( '', {
      module: "pos",
      action: "finsess",
      sessdata: jsonString
    }).done(function( jdata ) {
      $('#einconf').dialog('open');
      //$('#artikelnummerprojekt').focus();
      $('#einlage').prop('checked', false);
      if(typeof reloadsite != 'undefined')reloadsite();
    });*/
  });

  $('#entnahme').on('click',function() {
    var wert = prompt("Wert in " + waehrung + "?");
    if(!wert || wert <= 0) {
      $('#entnahme').prop('checked', false);
      return;
    }

    var kontostand = 0;
    $.ajax({
      url: '',
      dataType: 'json',
      async: false,
      data: {
        module: 'pos',
        action: 'loadkassstand',
        kassiererId: kassiererId
      },
      success: function(jdata) {
        kontostand = jdata.kontostand;
      }
    });

    if (parseFloat(kontostand) < parseFloat(wert)) {
      alert('Sie haben nicht ausreichend Bargeld in der Kasse.');
      $('#entnahme').prop('checked', false);
      return;
    }

    wert = parseFloat(wert.replace(',','.'));

    entnahmeclick(wert);
    
    /*
    $.getJSON( '', {
      module: "pos",
      action: "finsess",
      sessdata: jsonString
    }).done(function( jdata ) {
      $('#entconf').dialog('open');
      $('#artikelnummerprojekt').focus();
      $('#entnahme').prop('checked', false);
      if(typeof reloadsite != 'undefined')reloadsite();
    });*/
    
  });

  $('#trinkgeld').on('click',function() {
    var eccredit = $(this).data('eccredit');
    var ptype = $('input[name=ptype]:checked').val()+'';
    if(typeof ptype == 'undefined') {
      ptype = '';
    }
    if(typeof eccredit != 'undefined' && eccredit) {
      if(ptype !== 'ec' && ptype !== 'kredit') {
        $(this).prop('checked', false);
        return;
      }
    }
    var wert = prompt("Wert in " + waehrung + "?");
    if(!wert || wert <= 0) {
      $('#trinkgeld').prop('checked', false);
      $(this).prop('checked', false);
      return;
    }

    var kontostand = 0;
    $.ajax({
      url: '',
      dataType: 'json',
      async: false,
      data: {
        module: 'pos',
        action: 'loadkassstand',
        kassiererId: kassiererId
      },
      success: function(jdata) {
        kontostand = jdata.kontostand;
      }
    });

    if (parseFloat(kontostand) < parseFloat(wert)) {
      alert('Sie haben nicht ausreichend Bargeld in der Kasse.');
      $('#trinkgeld').prop('checked', false);
      return;
    }

    wert = parseFloat(wert.replace(',','.'));
    var grund = prompt('Trinkgeld(' + wert + ' EUR): Kunde?');
    if(!grund) {
      $('#trinkgeld').prop('checked', false);
      alert('Sie müssen einen Grund eingeben. Trinkgeld wurde nicht gebucht.');
      return;
    }
    randnumber = (Math.random() * (99999999 - 10000000)) + 10000000;
    var storeobj = {};
    storeobj['kasid'] = $('#tabs-1').data('on');
    storeobj['addr'] = {};
    storeobj['addrid'] = "";
    
    storeobj['wk'] = [];
    var nart = {};
    nart['id']      = "entnahme";
    nart['artikel'] = "entnahme";
    nart['kurztext_de'] = 'Trinkgeld: ' + grund;
    nart['nummer']  = "entnahme";
    nart['tax']     = '0';
    nart['preis']   = wert;
    
    nart['amount']  = '1';
    nart['rabatt']  = '0';

    storeobj['wk'].push(nart);

    storeobj['ptype']   = ptype;
    storeobj['rtype']   = "entnahme";
    
    storeobj['inbem']   = $('#inbem').val();
    storeobj['freit']   = $('#freit').val();

    storeobj['grabatt'] = '0';
    storeobj['grabatteur'] = '0';
    
    storeobj['kassiererId'] = kassiererId;
    storeobj['randnumber'] = randnumber;
    var jsonString = JSON.stringify(storeobj);


    $.ajax({
        url: 'index.php?module=pos&action=finsess',
        type: 'POST',
        dataType: 'json',
        data: { sessdata: jsonString},
        success: function(jdata) {
          if(typeof jdata.error != 'undefined')
          {
            alert(jdata.error);
          }else{
            $('#entconf').dialog('open');
          }
          $('#artikelnummerprojekt').focus();
          $('#trinkgeld').prop('checked', false);
        }});
    
    /*$.getJSON( '', {
      module: "pos",
      action: "finsess",
      sessdata: jsonString
    }).done(function( jdata ) {
      $('#entconf').dialog('open');
      $('#artikelnummerprojekt').focus();
      $('#trinkgeld').prop('checked', false);
    });*/
  });
  
  
  $('#abortsale').on('click',function() {
    $("#resetconf").dialog('open');
  });

  
  $('#finsale').on('click',function() {
    return finsale_click();
  });
  
  $('table.numblock').find('td').on('click',function(){
    var nr = $(this).html()
    {
      var el = '#gezahlt';
      if($('#tip:visible').length) {
        el = '#tip';
      }
      if(nr != '')
      {
        if(nr === 'DEL' || nr === '&crarr;')
        {
          $(el).val('');
          calcrueckgeld();
          $(el).focus();
        }else if(nr == ','){
          if($(el).val() != '' && ($(el).val().indexOf(',') == -1))
          {
            $(el).val($(el).val()+''+nr);
            calcrueckgeld();
            $(el).focus();
          }
        }else if(nr == parseInt(nr)){
          $(el).val($(el).val()+''+nr);
          calcrueckgeld();
          $(el).focus();
        }else{
          $(el).val('');
          calcrueckgeld();
          $(el).focus();
        }
      }
      
    }
    
    
  });
  

  
  $('#storesale').on('click',function() {
      vorgangspeichern();
      //storePOSSession('storesess');
      return false;
  });
  
  
  
  $('#loadkass').on('submit',function() {
    var kanr = $('#kanr').html();
    if(!kanr) return false;
    loginkas(kanr);
    return false;
  }); 
  
  $('#logoutkas').on('click',function() {
    resetFields();
    logoutkas();
    return false;
  });
  
  
  $('#loadaddr').on('submit',function(event) {
    event.preventDefault();
    var kunr = $('#adresse').val();
    if(!kunr) return false;
    loadAddr(kunr);
    return false;
  });


  $('#adresse').on('focus',function() {
    $('input[name=ktype]').prop('checked', false);
    $('input[name=ktype]').first().prop('checked',true);
    $('input[name=ktype]').first().attr('checked','checked');
    var skInput = $('input[value="sk"]');
    skInput.prop('checked', true);

    // $('input[name=ktype]').prop('checked', false);
    // $('input[name=ktype]').removeProp('checked');
    // $('input[name=ktype][value=sk]').prop('checked',true);
    // $('input[value="sk"]').prop('checked', true);

  });

  $('#adresse').on('focusout',function() {
    var kunr = $('#adresse').val();
    if(!kunr) return false;
    loadAddr(kunr);
  });
  
  
  
  $('input[name=ktype]').on('change',function() {
    var ktype = $(this).val();

    $('.rechnungsadresse_container').find('#t_name').text('');
    $('#adjcust').hide();

    if(ktype==='lk' || ktype==='nk'){
      $('#modalcont input[type=text]').val('');
      $('#ob1 span').html('');
      $('#adresse').val('');
    }
    
    if (ktype === 'lk') {
      $('.rechnungsadresse_container').find('#t_name').text('Laufkundschaft');
       // $('#adjcust').show();
    }

    if(ktype==='nk'){
      //tinyMCE.get('infoauftragserfassung_pos').setContent('');
      $('.rechnungsadresse_container').find('#t_name').text('');
      $('#modaloverlay').show(300);
      $('#adjcust').show();
    }
  });
  
  
  $('#adjcust').on('click',function() {
    $('#ob1 span').each(function(i,e) {
      var tid = $(this).prop('id').split('t_');
      if (tid[1] !== 'typ' && tid[0] != '') {
        $('#'+tid[1]).val($(this).html());
      }
    });
    $('#modaloverlay').show(300); 
  });

  $('#storecust').on('click',function() {

    $('.mkErrors').remove();

    if ($('#modalcont').find('input#name').val().length == 0) {
      if (!$('#modalcont').hasClass('mkerror')) {
        $('#modalcont').addClass('mkerror');
        $('#modalcont').find('input#name').after('<span style="color: red;" class="mkErrors"><br>Bitte Name angeben.</span>');
      }
      return false;
    }

    $('#modalcont').removeClass('mkerror');

    var storeobj = {}; 

    storeobj['kassierer'] = kassiererId;

    storeobj['kundennummer'] = $('#adresse').val();
    storeobj['addr'] = {};

    $('#ob1 span').each(function(i,e) {
      var tid = $(this).prop('id').split('t_');
      storeobj['addr'][tid[1]] = $('#'+tid[1]).val();
    });

    storeobj['addr']['infoauftragserfassung'] = $.base64Encode( $('#infoauftragserfassung_pos').val() );

    $.ajax({
      url: '',
      data: {
        module: 'pos',
        action: 'storecust',
        obj: storeobj
      },
      dataType: 'json',
      beforeSend: function() {
        //App.loading.open();
      },
      success: function(data) {
        if (data.status == 1) {
          $('#adresse').val(data.kundennummer);
          //$('#loadaddr input[type="radio"]').prop('checked', false);
          //$('#loadaddr input[type="radio"]').removeAttr('checked');
          //$('#loadaddr input[type="radio"]').first().prop('checked', true);
          $('input[name=ktype][value=sk]').prop('checked',true);
          $('input[name=ktype][value=sk]').attr('checked','checked');
          loadAddr(data.kundennummer);
          $('#modaloverlay').hide(300); 



        } else {
          // TODO: Fehlermeldungen?
        }
        //App.loading.close();

      }
    });


    /*

    $('#ob1 span').each(function(i,e) {
      var tid = $(this).prop('id').split('t_');
      $(this).html($('#'+tid[1]).val());
    });

    */

    saveCust = true;

  });
  
  $('#xbutt, #abortcust').on('click',function() { $('#modaloverlay').hide(300); });

  
  
  $('#typ').on('change',function() {
    var namein = $('#name').parent();
    var nameinfo = namein.prev();
    var ansprow = namein.parent().next();
    if($(this).val() === 'firma') {
      nameinfo.html('Firmenname');
      $('#ansprtit').show();
      $('#ansprechpartner').show();
    } else {
      nameinfo.html('Vor- und Nachname');
      $('#ansprtit').hide();
      $('#ansprechpartner').hide();
    }
  });
  
  
  $('#loadart').on('submit',function() {

    var adresseId = $('#adresse').val();
    var artean = $('#artikelnummerprojekt').val();
    artean = artean.split(' ');
    artean = artean[ 0 ];
    if(!artean) return false;
    $.getJSON( '', {
      module: "pos",
      action: "loadart",
      artean: artean,
      kassenkennung: kassiererId,
      addrid: adresseId
    }).done(function( jdata ) {
      if(jdata.check) {alert('Artikel nicht gefunden'); $('#artikelnummerprojekt').val(''); return false; }
      //if(typeof jdata.rabattartikel != 'undefined' && jdata.rabattartikel == 1){alert('Rabattartikel sind nicht zulässig'); return false; }
      addarticle(jdata);
      var tabletbody = $('table#wk > tbody > tr').last();
      checkseriennummern($(tabletbody).find('.amount'));
      $('#artikelnummerprojekt').val('');
    });
    return false;
  });
  
  // @TODO: Chrome hat scheinbar probleme mit .prop()
  if (is_chrome) {
    //$('input[value="sk"]').attr('checked', '');
  }

  //tinyMCEsetup();

  $('#kontoauswahlgrund').on('keydown', function (event) {

    if (event.which == 13) {
      kontoauswahl();
    }});

  // Ende document.ready
  });

  function checkLaufkundschaft(a_kassiererId)
  {
    var output;

    $.ajax({
      url: '',
      async: false,
      data: {
        module: 'pos',
        action: 'list',
        cmd: 'checklaufkundschaft',
        kassierer: a_kassiererId
      },
      dataType: 'json'
    }).done(function(jdata) {
      output = jdata;
    });

    if (typeof output.status && output.status == true) {
      return true;
    }

    return false;
  }
  
  function checkStammkunde(a_kundennummer, a_kassiererId, totalAmount) {

    var output;

    $.ajax({
      url: '',
      async: false,
      data: {
        module: 'pos',
        action: 'checkstammkunde',
        kundennummer: a_kundennummer,
        kassierer: a_kassiererId,
        amount: totalAmount
      },
      dataType: 'json'
    }).done(function(jdata) {
      output = jdata;
    });

    if (typeof output.status && output.status == true) {
      return true;
    }

    return false;

  }

  function loadAddr(kunr) {
    $.getJSON( '', {
      module: "pos",
      action: "loadaddr",
      kunr: kunr,
      kanr: kassiererId
    }).done(function( jdata ) {
      var ktype = $('input[name=ktype]:checked').val();
      if(ktype==='sk' && (typeof jdata.check == 'undefined' || jdata.check!=='ERR')) {
        $.each(jdata, function(i, e) {        
          $('#ob1 #t_'+i).html(e);
        $('#adjcust').show();

          if (i === 'infoauftragserfassung') {
            $('#infoauftragserfassung_pos').val(e);
            if(e!='')
            {
              //tinyMCE.get('infoauftragserfassung_pos').setContent(e);
            }
          } else {
            $('#modalcont').find('#' + i).val(e);            
          }

          if(i==='typ')
          {
            var namein = $('#name').parent();
            var nameinfo = namein.prev();
            if(e==='firma')
              nameinfo.html('Firmenname');
            else
              nameinfo.html('Vor- und Nachname');
          }
        });
        // $('#inbem').val(jdata['infoauftragserfassung']);
        $('#artikelnummerprojekt').focus();
      } else {
        $('#adresse').val('');
        if(ktype==='sk')
        {
          alert('Kein Kunde gefunden');
        }
      }
    });  
  }  
  
  function addarticlestorno(jdata)
  {
    var pos = $('#stornotab table tbody').find('tr').length + 1;
    var html = '<tr><td class="pos">'+pos+'</td><td class="artikel">'+jdata['artikel']+'</td><td class="nummer">'+jdata['nummer']+'</td><td><input type="text" size="5" class="menge"></td><td class="amount">'+jdata['amount']+'<input type="hidden" class="tax" value="'+jdata['tax']+'" /><input type="hidden" class="preis" value="'+jdata['preis']+'" /><input type="hidden" class="rabatt" value="'+jdata['rabatt']+'" /><input type="hidden" class="artid" value="'+jdata['id']+'" />';
    if(typeof jdata['preis_genau'] != 'undefined')
    {
      html += '<input type="hidden" class="preisgenau" value="'+jdata['preis_genau']+'" />';
    }
    if(typeof jdata['erloes'] != 'undefined')
    {
      html += '<input type="hidden" class="erloes" value="'+jdata['erloes']+'" />';
    }
    html += '</td></tr>';
    $('#stornotab table tbody').append(html);
  }
  
  function teilstornieren()
  {
    if($('#rechnungid').val() != '')
    {
      var ktype = $('input[name=ktype]:checked').val();
      var ptype = $('input[name=ptype]:checked').val();
      var totalAmount = $('#sutotal').text().replace(',', '.');
      totalAmount = parseFloat(totalAmount);
      if (totalAmount < 0) {
        alert('Die Gesamtsumme darf nicht kleiner als 0 sein.');
        return false;
      }

      if ( ktype === 'sk' && $('#adresse').val().length == 0 ) {
        $("#skempty").dialog('open');
        return false;
      } else if ( ktype === 'sk' && $('#adresse').val().length > 0 && !checkStammkunde($('#adresse').val(), kassiererId)) {
          alert('Stammkunde nicht korrekt.');
          return false;
      }

      if((ktype==='nk' && !$("#t_name").html()) || $('#wk > tbody > tr').length == 0) {
        $("#emptywarn").dialog('open');
      } else if(zahlungselzwang && !$('#payment input[name=ptype]:checked').val()) {
        $('#zahlwarn').dialog('open');
      } else {
        if(ptype === 'bar')
        {
          $.ajax({
            url: 'index.php?module=pos&action=loadkassstand&kassiererId='+kassiererId,
            type: 'POST',
            dataType: 'json',
            data: { },
            success: function(data) {
              if(parseFloat(data.kontostand) < totalAmount)
              {
                alert('Der Kassenstand ist kleiner als der Stornierbetrag!');
              }else{
                var anz = $('#wk > tbody > tr').length;
                if(anz > 0)
                {
                  var betrag = $('#sutotal').html();
                  if(confirm('Es wird eine Gutschrift erstellt. Sicher weitermachen?'))
                  {
                    storePOSSession('teilstornieren');
                  }
                }else{
                  
                }
              }
          }});
        }else{
          var anz = $('#wk > tbody > tr').length;
          if(anz > 0)
          {
            var betrag = $('#sutotal').html();
            if(confirm('Es wird eine Gutschrift in Höhe von '+betrag+' erstellt. Sicher weitermachen?'))
            {
              storePOSSession('teilstornieren');
            }
          }else{
            
          }
        }
      }
    }
  }
  
  function stornieren()
  {
    var ktype = $('input[name=ktype]:checked').val();
    var ptype = $('input[name=ptype]:checked').val();
    var totalAmount = $('#sutotal').text().replace(',', '.');
    totalAmount = parseFloat(totalAmount);
    if (totalAmount < 0) {
      alert('Die Gesamtsumme darf nicht kleiner als 0 sein.');
      return false;
    }

    if ( ktype === 'sk' && $('#adresse').val().length == 0 ) {
      $("#skempty").dialog('open');
      return false;
    } else if ( ktype === 'sk' && $('#adresse').val().length > 0 && !checkStammkunde($('#adresse').val(), kassiererId)) {
        alert('Stammkunde nicht korrekt.');
        return false;
    }

    if((ktype==='nk' && !$("#t_name").html()) || $('#wk > tbody > tr').length == 0) {
      $("#emptywarn").dialog('open');
    } else if(zahlungselzwang && !$('#payment input[name=ptype]:checked').val()) {
      $('#zahlwarn').dialog('open');
    } else {
      if(ptype === 'bar')
      {
        $.ajax({
          url: 'index.php?module=pos&action=loadkassstand&kassiererId='+kassiererId,
          type: 'POST',
          dataType: 'json',
          data: { },
          success: function(data) {
            if(parseFloat(data.kontostand) < totalAmount)
            {
              alert('Der Kassenstand ist kleiner als der Stornierbetrag!');
            }else{
              var anz = $('#wk > tbody > tr').length;
              if(anz > 0)
              {
                var betrag = $('#sutotal').html();
                if(confirm('Es wird eine Gutschrift in Höhe von '+betrag+' erstellt. Sicher weitermachen?'))
                {
                  storePOSSession('stornieren');
                }
              }else{
                
              }
            }
        }});
      }else{
        var anz = $('#wk > tbody > tr').length;
        if(anz > 0)
        {
          var betrag = $('#sutotal').html();
          if(confirm('Es wird eine Gutschrift in Höhe von '+betrag+' erstellt. Sicher weitermachen?'))
          {
            storePOSSession('stornieren');
          }
        }else{
          
        }
      }
    }
  }
  
  function komplettstornieren()
  {
    if($('#rechnungid').val() != '')
    {        
      var ktype = $('input[name=ktype]:checked').val();
      var ptype = $('input[name=ptype]:checked').val();
      var totalAmount = $('#sutotal').text().replace(',', '.');
      totalAmount = parseFloat(totalAmount);
      if (totalAmount < 0) {
        alert('Die Gesamtsumme darf nicht kleiner als 0 sein.');
        return false;
      }

      if ( ktype == 'sk' && $('#adresse').val().length == 0 ) {
        $("#skempty").dialog('open');
        return false;
      } else if ( ktype == 'sk' && $('#adresse').val().length > 0 && !checkStammkunde($('#adresse').val(), kassiererId)) {
          alert('Stammkunde nicht korrekt.');
          return false;
      }

      if((ktype==='nk' && !$("#t_name").html()) || $('#wk > tbody > tr').length == 0) {
        $("#emptywarn").dialog('open');
      } else if(zahlungselzwang && !$('#payment input[name=ptype]:checked').val()) {
        $('#zahlwarn').dialog('open');
      } else {
        if(ptype === 'bar')
        {
          $.ajax({
            url: 'index.php?module=pos&action=loadkassstand&kassiererId='+kassiererId,
            type: 'POST',
            dataType: 'json',
            data: { },
            success: function(data) {
              if(parseFloat(data.kontostand) < totalAmount)
              {
                alert('Der Kassenstand ist kleiner als der Stornierbetrag!');
              }else{
                var anz = $('#wk > tbody > tr').length;
                if(anz > 0)
                {
                  var betrag = $('#sutotal').html();
                  if(confirm('Es wird eine Gutschrift in Höhe von '+betrag+' erstellt. Sicher weitermachen?'))
                  {
                    storePOSSession('komplettstornieren');
                  }
                }else{
                  
                }
              }
          }});
        }else{
          var anz = $('#wk > tbody > tr').length;
          if(anz > 0)
          {
            var betrag = $('#sutotal').html();
            if(confirm('Es wird eine Gutschrift in Höhe von '+betrag+' erstellt. Sicher weitermachen?'))
            {
              storePOSSession('komplettstornieren');
            }
          }else{
            
          }
        }
      }
    }
  }
  
  function teilstornouebernehmen()
  {
    resetFields();
    steuerfrei = ($('#teilstornosteuerfrei').val()==1)?true:false;
    ustid = $('#teilstornoustid').val();
    var anzteile = $('#stornotab table tbody').find('tr').length;
    $('#stornotab table tbody').find('tr').each(function(){
      var storeart = {};
      storeart['artikel'] = $(this).find('.artikel').text();
      storeart['id'] = $(this).find('.artid').val();
      storeart['tax'] = $(this).find('.tax').val();
      if($(this).find('.taxorig').length) {
        storeart['taxorig'] = $(this).find('.taxorig').val();
      }
      storeart['nummer'] = $(this).find('.nummer').text();
      storeart['amount'] = $(this).find('.menge').val()+'';
      storeart['preis'] = $(this).find('.preis').val();
      storeart['rabatt'] = $(this).find('.rabatt').val();
      var preisgenau = $(this).find('.preisgenau').val();
      if(typeof preisgenau != 'undefined')
      {
        storeart['preis_genau'] = preisgenau;
      }
      var erloes = $(this).find('.erloes').val();
      if(typeof erloes != 'undefined')
      {
        storeart['erloes'] = erloes;
      }
      if(storeart['amount'] != '' && storeart['amount'] != '0')addarticle(storeart, anzteile);
    });
    $('#wk .editname').remove();
    $('#wk .preisEditLink').remove();
    $('#wk .delwkart').remove();
    $('#wl input').prop('disabled', true);
    $('#wl input').prop('readonly', true);
    $('#finsale').hide();
    $('#stornobutton').hide();
    
    //$('#abortsale').hide();
    $('#stornoabbrechen').show();
    $('#teilstornobutton').show();
    $('#stornoabbrechen').css('display','inline-block');
    $('#teilstornobutton').css('display','inline-block');
    $('#belegeladendiv').dialog('close');
    $('#stornotab').hide();
    $('#abbrechnenbutton').hide();
  }

 
  if(document.getElementById('gezahlt') != null){
     document.getElementById('gezahlt').addEventListener ('keydown', function (event) {

     if (event.which == 13) {
       calcrueckgeld();
       $('#finconf').next('div').find('button').first().next().trigger('click');
     }
    });
  }

  if(document.getElementById('tip') != null){
    document.getElementById('tip').addEventListener ('keydown', function (event) {

      if (event.which == 13) {
        calcrueckgeld();
        $('#finconf').next('div').find('button').first().next().trigger('click');
      }
    });
    document.getElementById('tip').addEventListener ('keyup', function (event) {
      if (event.which != 13) {
        calcrueckgeld();
      }
    });
  }

  function belegabrechnen()
  {
    if($('#rechnungid').val() != '')
    {
      var totalAmount = $('#sutotal').text().replace(',', '.');
      totalAmount = parseFloat(totalAmount);
      if (totalAmount < 0) {
        alert('Die Gesamtsumme darf nicht kleiner als 0 sein.');
        return false;
      }
      if(confirm('Soll der Beleg wirklich über die Kasse abgerechnet werden?'))
      {
        var ptype = $('input[name=ptype]:checked').val();

        if(ptype==='bar')
        {
          belegabrechnenaktiv = true;
          $('tr.trbar').css('display','');
          $('tr.trec').css('display','none');
          $("#gezahlt").val('');
          $('#tip').val('0');
          $("#rueckgeld").val('');
          $("#finconf").dialog('open');
         // $.post( "index.php?module=pos&action=display", { total: $('#sutotal').html() } ); 
          $('#zahlbetrag').val($('#sutotal').html());
          $('tr.trbar').css('display','');
          $('table.numblock').css('display','');
          $('#gezahlt').focus();
        }
        else {
          if(ptype === 'ec' || ptype === 'kredit') {
            $('table.numblock').css('display','');
            $('tr.trec').css('display','');
          }
          else {
            $('table.numblock').css('display','none');
          }
          $('tr.trbar').css('display','none');
          $('#tip').val('0');
          storePOSSession('belegabrechnen');
        }
      }
    }
  }
  
  function stornoabbr()
  {
    $('#stornoabbrechen').hide();
    $('#stornobutton').show();
    $('#stornobutton').css('display','inline-block');
    $('#teilstornobutton').hide();
    $('#abbrechnenbutton').hide();
    $('#komplettstornobutton').hide();
    $('#finsale').show();
    $('#abortsale').show();
    $('#finsale').css('display','inline-block');
    $('#abortsale').css('display','inline-block');
    resetFields();
  }
  
  /*
  function teilstornieren()
  {
    stornieren();
    return;
    var storeobj = {};
    storeobj['wk'] = [];
    $('#wk > tbody > tr').each(function(i,e) {
      var nart = {};
      nart['id']      = $(this).prop('id');
      nart['artikel'] = $(this).find('.artikel').html().replace(/\"/g,'&quot;');
      nart['kurztext_de'] = $(this).prop('title').replace(/\"/g,'&quot;');
      nart['nummer']  = $(this).find('.nummer').html();
      nart['tax']     = $(this).find('.tax').html();
      nart['preis']   = $(this).find('.preisinp').val();
      
      nart['amount']  = $(this).find('.amount').val().replace(',','.');
      nart['rabatt']  = $(this).find('.rabatt').val();

      storeobj['wk'].push(nart);
    });
    
    $.ajax({
      url: 'index.php?module=pos&action=loadsess&cmd=stornorechnung&kasid='+kassiererId,
      type: 'POST',
      dataType: 'json',
      data: { artarr : JSON.stringify(storeobj)},
      success: function(data) {
        $('#buttonabbrechen').hide();
        $('#stornoabbrechen').hide();
        $('#teilstornobutton').hide();
        $('#stornobutton').show();    
        $('#finsale').css('display','inline-block');
        $('#abortsale').css('display','inline-block');
        $('#stornobutton').css('display','inline-block');
        resetFields();
      },
      beforeSend: function() {

      }
    });
    
  }*/
  
  function seriennummernuebernehmen()
  {
    var artikelnr = $('#seriennummernartikelnr').val();
    var html = '';
    $('#seriennummerndiv table tbody').children('tr').each(function(){
      if(html != '')html += ',';
      html += $(this).find('.seriennummereingabe').val();
    });
    $('#wk > tbody > tr').each(function(i,e) {
      if($(e).find('.nr').html() == artikelnr)
      {
        $(e).find('.seriennummernliste').val(html);
        return;
      }
    });
  }
  
  function seriennummernopen(elem)
  {
    var name = $(elem).find('.artikel').html();
    var menge = ($(elem).find('.amount').val()+'').replace(',','.');
    var artikelnr = $(elem).find('.nr').html();
    var seriennummern = $(elem).find('.seriennummern').val();
    var artikelid = $(elem).prop('id');
    $('#seriennummernartikelid').val(artikelid);
    $('#seriennummernartikelnr').val(artikelnr);
    var html = '';
    var html2 = '<script>';
    if(seriennummern)
    {
      var seriennummernliste = ($(elem).find('.seriennummernliste').val()+'').split(',');
      $('#seriennummernartikel').html(name);
      $('#seriennummerndiv table tbody').find('tr').remove();
      var first = true;
      for(i = 0; i < menge; i++)
      {
        html = '<tr><td><input type="text" class="seriennummereingabe" id="seriennummer_'+i+'" value="'
        if(typeof seriennummernliste[ i ] != 'undefined')
        {
          html += seriennummernliste[ i ];
          if(seriennummernliste[ i ] == '' && first)
          {
            html2 += '$("#seriennummer_'+i+'").focus();';
            first = false;
          }
        }else{
          if(first)
          {
            html2 += '$("#seriennummer_'+i+'").focus();';
            first = false;
          }
        }
        html += '" /></td></tr>';
        $('#seriennummerndiv table tbody').append(html);
        html2 += '$( "#seriennummer_' + i + '").autocomplete({ source: "index.php?module=ajax&action=filter&rmodule=pos&raction=list&rid=&filtername=lagerseriennummern&artikel='+artikelid+'", select: function( event, ui ) { var text = ui.item.value;  $( "#seriennummer_' + i + '" ).val( text );              return false;              }            });';
        
      }
      html2 += '</script>';
      $('#seriennummerndiv table tbody').find('td').first().append(html2);
      $('#seriennummerndiv').dialog('open');
    }
  }
  
  function artikeltextuebernehmen()
  {
    var nr = $('#artikeltextdivnr').val();
    var artikel = $('#artikeltextdivartikel').val();
    var anabregs_text = $('#artikeltextdivanabregs_text').val();
    $('#wk > tbody > tr').each(function(i,e) {
      if($(e).find('.nr').html() == nr)
      {
        $(e).find('.artikel').html(artikel);
        $(e).find('.anabregs_text').html(anabregs_text);
        return;
      }
    });
  }
  
  function changeArtikelName(elem)
  {
    var par = $(elem).parents('tr').first();
    var nr = $(par).find('.nr').html();
    var artikel = $(par).find('.artikel').html();
    var anabregs_text = $(par).find('.anabregs_text').html();
    $('#artikeltextdivnr').val(nr);
    $('#artikeltextdivartikel').val(artikel);
    $('#artikeltextdivanabregs_text').val(anabregs_text);
    $('#artikeltextdiv').dialog('open');
  }
  
  function checkseriennummern(elem)
  {
    var par = $(elem).parents('tr').first();
    var seriennummern = $(par).find('.seriennummern').val();
    if(seriennummern)
    {
      var seriennummernliste = ($(par).find('.seriennummernliste').val()+'').split(',');
      var anzbelegt = 0;
      $(seriennummernliste).each(function(i,e) {if(e != '')anzbelegt++});
      var menge = ($(elem).val()+'').replace(',','.');
      if(anzbelegt < menge)seriennummernopen(par);
    }
  }
  
  function updateRabattartikel()
  {
    var preis = 0;
    $('#wk > tbody > tr').each(function(i,e) {
      var porto = parseInt($(this).find('.porto').first().html());
      var rabattartikel = parseInt($(this).find('.rabattartikel').first().html());
      var keinrabatterlaubt = parseInt($(this).find('.keinrabatterlaubt').first().html());
      if(porto == 0 && keinrabatterlaubt == 0 && rabattartikel == 0)
      {
        if(brutto)
        {
          var tbrutto = parseFloat($(this).data('total')); // find('.gesamt').html());
          // var tmwst = parseFloat($(this).find('.tax').html());
          // var tara = (tbrutto / 100) * tmwst;
          var tmwst = parseFloat($(this).find('.tax').html());
          var ltmwst = (tmwst + 100) / 100;
          var tnetto = tbrutto / ltmwst;
          var trabatt = parseFloat($(this).find('.rabatt').val().replace(',','.'));
          preis = preis + tbrutto;
        }else{
          var tnetto = parseFloat($(this).data('total')); // find('.gesamt').html());
          // var tmwst = parseFloat($(this).find('.tax').html());
          // var tara = (tbrutto / 100) * tmwst;
          var tmwst = parseFloat($(this).find('.tax').html());
          var ltmwst = (tmwst + 100) / 100;
          var tbrutto = tnetto * ltmwst;
          var trabatt = parseFloat($(this).find('.rabatt').val().replace(',','.'));
          preis = preis + tnetto;
        }

      }
    });
    $('#wk > tbody > tr').each(function(i,e) {
      var porto = parseInt($(this).find('.porto').first().html());
      var rabattartikel = parseInt($(this).find('.rabattartikel').first().html());
      var keinrabatterlaubt = parseInt($(this).find('.keinrabatterlaubt').first().html());
      if(rabattartikel == 1)
      {
        var prozent = $(this).find('.rabatt_prozent').html()+'';
        $(this).data('total', -preis * parseFloat(prozent.replace(',','.')) / 100);
        $(this).find('.preisNormal td').first().html(((-preis * parseFloat(prozent.replace(',','.')) / 100).toFixed(2)+'').replace('.',','));
        $(this).find('.preisinp').val(((-preis * parseFloat(prozent.replace(',','.')) / 100).toFixed(2)+'').replace('.',','));
        $(this).find('.rabatt').val('0');
        $(this).find('.amount').val('1');
      }
    });
  }
  
  
  function getRabattBetrag(prozent, tax)
  {
    var preis = 0;
    $('#wk > tbody > tr').each(function(i,e) {
      var porto = parseInt($(this).find('.porto').first().html());
      if(isNaN(porto))porto = 0;
      var rabattartikel = parseInt($(this).find('.rabattartikel').first().html());
      if(isNaN(rabattartikel))rabattartikel = 0;
      var keinrabatterlaubt = parseInt($(this).find('.keinrabatterlaubt').first().html());
      if(isNaN(keinrabatterlaubt))keinrabatterlaubt = 0;
      if(porto == 0 && keinrabatterlaubt == 0 && rabattartikel == 0)
      {
        if(brutto)
        {
          var tbrutto = parseFloat($(this).data('total')); // find('.gesamt').html());
          if(isNaN(tbrutto))tbrutto = 0;
          // var tmwst = parseFloat($(this).find('.tax').html());
          // var tara = (tbrutto / 100) * tmwst;
          var tmwst = parseFloat($(this).find('.tax').html());
          if(isNaN(tmwst))tmwst = 0;
          var ltmwst = (tmwst + 100) / 100;
          var tnetto = tbrutto / ltmwst;
          var trabatt = parseFloat($(this).find('.rabatt').val().replace(',','.'));
          preis = preis + tbrutto;
        }else{
          var tnetto = parseFloat($(this).data('total')); // find('.gesamt').html());
          if(isNaN(tnetto))tnetto = 0;
          // var tmwst = parseFloat($(this).find('.tax').html());
          // var tara = (tbrutto / 100) * tmwst;
          var tmwst = parseFloat($(this).find('.tax').html());
          if(isNaN(tmwst))tmwst = 0;
          var ltmwst = (tmwst + 100) / 100;
          var tbrutto = tnetto * ltmwst;
          var trabatt = parseFloat($(this).find('.rabatt').val().replace(',','.'));
          preis = preis + tnetto;
        }

      }
    });
    
    var erg = -preis * parseFloat((prozent+'').replace(',','.')) / 100;
    if(isNaN(erg))erg = 0;
    return erg;
  }
  
  function addarticle(jdata, anz_teile) {
    var trs = $('#wk > tbody > tr');
    if(trs.length) {
      var $lastRow = trs[trs.length - 1];
      if(sumarticles == '1' && $lastRow.id == jdata['id']) {
        $($lastRow).find('.amount').val(parseFloat(($($lastRow).find('.amount').val()+'').replace(',','.'))+1);
        grabatt = 0;
        $('#wk > tbody > tr').each(function() {
          updatearttotal($(this));
        });
        updatetotals();
        grabatt = 0;

        $('#artikelnummerprojekt').focus();
        return;
      }
    }

    var newart = $('#defart').clone();
    newart.attr('id', jdata['id']);
    delete(jdata['id']);
    var tpreis = jdata['preis'];
    var seriennummern = 0;
    if(typeof jdata['seriennummern'] != 'undefined')seriennummern = jdata['seriennummern'];
    if(typeof jdata['rabattartikel'] != 'undefined' && typeof jdata['rabatt_prozent'] != 'undefined'
        && parseFloat(jdata['rabattartikel']) == 1)
    {
      jdata['preis'] = getRabattBetrag(jdata['rabatt_prozent'], jdata['tax']);
      tpreis = jdata['preis']+'';
      jdata['rabatt'] = 0;
    }else{
      tpreis = tpreis + '';
    }
    jdata['preis'] = tpreis.replace(',','.');
    jdata['preis'] = Math.round(jdata['preis'] * 100) / 100;
    if('amount' in jdata) {
      newart.find('.amount').val(jdata['amount'].replace(',','.'));
      delete(jdata['amount']);
    }
    if('rabatt' in jdata) {
      newart.find('.rabatt').val(parseFloat(jdata['rabatt']).toFixed(0));
      delete(jdata['rabatt']);
    }
    
    jQuery.each(jdata, function(i,e) {
      newart.find('.'+i).html(e);
    });
    if(newart.find('.taxorig').length) {
      newart.find('.taxorig').html(jdata['tax']);
    }
    if(steuerfrei) {
      newart.find('.tax').html('0%');
    }

    var preisAusgabe = parseFloat(newart.find('.preis').html()).toFixed(2).replace('.',',');
    var preisHtml = '';
    preisHtml += '<table width="100%" cellspacing="0" cellpadding="0">';

      preisHtml += '<tr class="preisNormal">';
        preisHtml += '<td>';
          preisHtml += preisAusgabe;
        preisHtml += '</td>';

        preisHtml += '<td>';
          preisHtml += '<a class="preisEditLink" href="javascript:;" onclick="changeArtikelPreis(this)">';
            preisHtml += '<img alt="ändern" src="themes/new/images/edit.svg" border="0" valign="middle">';
          preisHtml += '</a>';
          preisHtml += '<input class="seriennummern" type="hidden" value="' + seriennummern + '" />';
          preisHtml += '<input class="seriennummernliste" type="hidden" value="" />';
        preisHtml += '</td>';
      preisHtml += '</tr>';

      preisHtml += '<tr class="preisEdit" style="display: none;">';
        preisHtml += '<td>';
          preisHtml += '<input type="text" class="preisinp" name="preis" value="' + preisAusgabe + '" size="6">';
        preisHtml += '</td>';

        preisHtml += '<td>';
          //preisHtml += '<a class="preisSaveLink" href="javascript:;" onclick="saveArtikelPreis(this);"><img src="themes/new/images/edit.svg" border="0" valign="middle"></a>';
        preisHtml += '</td>';
      preisHtml += '</tr>';
      

    preisHtml += '</table>';
    newart.find('.preis').html(preisHtml);

    // newart.find('.preis').html('<span class="preisNormal">' + preisAusgabe + '</span>');
    // newart.find('.preis').append('<a class="preisEditLink"><img src="themes/new/images/edit.svg" border="0" valign="middle"></a>');
    // newart.find('.preis').append('<span class="preisEdit"><input type="text" name="preis" value="' + preisAusgabe + '" size="6" style="display:none;"></span>');

    var ges = ''+jdata['preis'];
    newart.find('.gesamt').html(ges.replace('.',','));
    newart.find('.nr').html($('#wk > tbody > tr').length + 1);
    newart.attr('title',jdata['kurztext_de']);

    if(typeof jdata['preis_genau'] != 'undefined')
    {
      newart.find('.preisgenau').html(jdata['preis_genau']);
      newart.find('.preisgenauex').html(1);
      newart.find('.gesamtpreisgenau').html(1);
    }
    if(typeof jdata['erloes'] != 'undefined')
    {
      newart.find('.erloes').html(jdata['erloes']);
    }
    
    $('#wk > tbody ').append(newart);
    
    if(typeof anz_teile == 'undefined')anz_teile = $('#wk > tbody > tr').length;
    
    if(anz_teile < 8)
    {
      $('#wkcontainer').animate({scrollTop : ($('#wk > tbody > tr').length+1)*27},(100 ));
    }else{
      
      $('#wkcontainer').animate({scrollTop : ($('#wk > tbody > tr').length+1)*27},(10 ));
    }
    
    grabatt = 0;
    $('#wk > tbody > tr').each(function() {
      updatearttotal($(this));
    });
    updatetotals();
    grabatt = 0;
    
    $('#artikelnummerprojekt').focus();
    
    
    // löschen eines Eintrags
    $('.delwkart').on('click', function() {
      // Zeilen neu nummerieren
      var row = $(this).parent().parent();
      var table = row.parent();
      row.remove();
      table.find('.nr').each(function(i) {
        $(this).html(i+1);
      });
      
      grabatt = 0;
      $('#wk > tbody > tr').each(function() {
        updatearttotal($(this));
      });
      updatetotals();
      grabatt = 0;
      
      $('#artikelnummerprojekt').focus();
      return false;
    });
    
    
    
    // Verändern der Produktmenge
    $('.amount').on('keyup', function() {
      if(!$(this).val()) return;
      grabatt = 0;
      $('#wk > tbody > tr').each(function() {
        updatearttotal($(this));
      });
      updatetotals();
      grabatt = 0;
    });
    
    $('.amount').on('change', function() {
      checkseriennummern(this);
    });

    $('.preisinp').on('keyup', function() {
      if(!$(this).val()) return;
      $(this).parent('table').first().parents('tr').first().find('.preisgenauex').html('');
      grabatt = 0;
      $('#wk > tbody > tr').each(function() {
        updatearttotal($(this));
      });
      updatetotals();
      grabatt = 0;
    });

    // Verändern des Rabatts
    $('.rabatt').on('keyup', function(event) {

      var newval;
      var oldval = parseFloat($(this).val());

      if (event.which == 38) {
        newval = oldval+1;
      } else if (event.which == 40) {
        newval = oldval-1;
      } else {
       newval = $(this).val().replace(/[^0-9\.]/g,'');
      }

      if (newval < 0 || newval.length == 0) {
        newval = 0;
      }

      if (newval > 100) {
        newval = 0;
      }

      $(this).val(newval);
    });

    $('.rabatt').on('keyup', function() {
      if(!$(this).val()) return;
      grabatt = 0;
      $('#wk > tbody > tr').each(function() {
        updatearttotal($(this));
      });
      updatetotals();
      grabatt = 0;
    });
  }

  
  function updatearttotal(art) {
    var rabatt = parseFloat(art.find('.rabatt').val()) / 100;
    // var total = parseFloat(art.find('.preis').html().replace(',','.')) * parseInt(art.find('.amount').val());

    var preis = art.find('.preisinp').val();
    var preis_genau = 0;
    var preisgenauex = ''+art.find('.preisgenauex').html();
    var preisgenau = ''+parseFloat(art.find('.preisgenau').html());
    if(preisgenauex != '')
    {
      preis = preisgenau;
    }
    
    if (typeof preis == "undefined") {
      return;
    }

    // wenn menge < 0 mache künstlich 1 draus hauptsache nicht negativ
    var amount = art.find('.amount').val();

    if(parseFloat(amount.replace(',', '.'))<0 || amount.search("-") >= 0)
    {
      grabatt = 0;
      amount=1;
      art.find('.amount').val(1);
      $('#wk > tbody > tr').each(function() {
        updatearttotal($(this));
      });
      updatetotals();
      grabatt = 0;
    }

    var total = parseFloat( parseFloat(preis.replace(',', '.')) * parseFloat(amount.replace(',', '.')) );
    lrabatt = total * rabatt;
    if(typeof brutto == 'undefined' || !brutto)
    {
      // basierend auf org wert
      lrabatt = total * rabatt;
      var total = parseFloat( parseFloat(preis.replace(',', '.')) * parseFloat(amount.replace(',', '.')) -   (parseFloat(preis.replace(',', '.')) * parseFloat(amount.replace(',', '.')) / 100 * rabatt * 100) );
    }

    grabatt += lrabatt;

    art.data('total',total);
    total -= lrabatt;

    if(brutto)
    {
      if(preisgenauex!='')art.find('.gesamtpreisgenau').html(total);
      total = total.toFixed(2);
    } else {
      total = total + (parseFloat(preis.replace(',', '.')) * parseFloat(amount.replace(',', '.')) / 100 * rabatt * 100);
      if(preisgenauex!='')art.find('.gesamtpreisgenau').html(total);
      total = total.toFixed(2);
    }
    art.find('.gesamt').html(total.replace('.',','));
  }
  
  function updatetotals() {
    totaltaraNorm = 0.0;
    totaltaraErm = 0.0;
    totalbrutto = 0.0;
    totalnetto = 0.0;

    updateRabattartikel();
    var grabattbrutto = 0;
    $('#wk > tbody > tr').each(function(i,e) {
      var rabatt = parseFloat($(this).find('.rabatt').val()) / 100;
      if(typeof brutto != 'undefined' &&  brutto)
      {
        var tbrutto = parseFloat($(this).data('total')); // find('.gesamt').html());
        // var tmwst = parseFloat($(this).find('.tax').html());
        // var tara = (tbrutto / 100) * tmwst;
        var taxContent = $(this).find('.tax').html();
        var tmwst = parseFloat(
            taxContent.replace("%", "").replace(",", ".")
        );
        if(steuerfrei)tmwst = 0;
        var ltmwst = (tmwst + 100) / 100;
        var tnetto = tbrutto / ltmwst;
      }else{
        var tnetto = parseFloat($(this).data('total')); // find('.gesamt').html());
        // var tmwst = parseFloat($(this).find('.tax').html());
        // var tara = (tbrutto / 100) * tmwst;
        var tmwst = parseFloat($(this).find('.tax').html());
        if(steuerfrei)tmwst = 0;
        var ltmwst = (tmwst + 100) / 100;
        var tbrutto = tnetto * ltmwst;
        grabattbrutto += tbrutto * rabatt;
      }
      
      tnetto = Math.round(tnetto * 100) / 100;
      var tara = tbrutto - tnetto;

      var taxnorm = parseFloat($('#taxnorm').html());
      var taxerm  = parseFloat($('#taxerm').html());
      if(tmwst == taxnorm)      totaltaraNorm += tara;
      else if(tmwst == taxerm)  totaltaraErm += tara;
      totalbrutto += tbrutto;
      totalnetto += tnetto;
    });


    if(typeof brutto == 'undefined' || !brutto)
    {
      totalbrutto = parseFloat((Math.round(totalbrutto*100, 2)/100.0).toFixed(2));
    }

    grabatt     = parseFloat(grabatt);
    //if(typeof brutto == 'undefined' || !brutto)grabatt = parseFloat(grabattbrutto);
    grabatteur  = parseFloat(grabatteur);


    // Minimal 0
    if (grabatteur.length == 0 || isNaN(grabatteur)) {
      grabatteur = 0;
    }

    // Minimal 0
    if (grabatt.length == 0 || isNaN(grabatt)) {
      grabatt = 0;
    }


    if(typeof brutto != 'undefined' &&  brutto)
      var totaltotal = totalbrutto - grabatt - grabatteur;
    else
      var totaltotal = totalbrutto - grabatteur;
    
    /*
    totaltara19 = Math.round(totaltara19 * 100) / 100;
    totaltara7 = Math.round(totaltara7 * 100) / 100;
    totalbrutto = Math.round(totalbrutto * 100) / 100;
    totalnetto = Math.round(totalnetto * 100) / 100;
    */
    
    if(steuerfrei)
    {
      totaltaraNorm = 0;
      totaltaraErm = 0;
    }
    
    totaltaraNorm = totaltaraNorm.toFixed(2);
    totaltaraErm  = totaltaraErm.toFixed(2);
    totalbrutto = totalbrutto.toFixed(2);
    totalnetto  = totalnetto.toFixed(2);
    totaltotal  = totaltotal.toFixed(2);
    grabatt     = grabatt.toFixed(2);
    grabatteur  = grabatteur.toFixed(2);
    
    
    $('#sunetto').  html(totalnetto.replace('.',','));
    $('#taranorm').   html(totaltaraNorm.replace('.',','));
    $('#taraerm').    html(totaltaraErm.replace('.',','));
    $('#subrutto'). html(totalbrutto.replace('.',','));
    $('#trabatt').  html('-' + grabatt.replace('.',','));
    $('#trabatteur').html('-' + grabatteur.replace('.',','));
    $('#sutotal').  html(totaltotal.replace('.',','));
    
    
    var anzteile = 0;
    $('#wk > tbody > tr').each(function(i,e) {
      var portoartikel = parseInt($(this).find('.porto').html());
      var rabattartikel = parseInt($(this).find('.rabattartikel').html());
      if(portoartikel == 0 && rabattartikel == 0)
      {
        var anzt = parseFloat( ($(this).find('.amount').val()+'').replace(',','.') );
        if(!isNaN(anzt))anzteile += anzt;
      }
    });
    $('#anzteile').html((anzteile+'').replace('.',','));
  }
  
  function VorgangNeu()
  {
    if(vorgangname = prompt('Bitte neue Bezeichnung eingeben'))
    {
      $('#vorgangname').val(vorgangname);
      storePOSSession('neuervorgang');
    }
    
  }
  
  function LadeVorgang(vorgangid)
  {
    resetFields();
    $('#tabs-1').removeData('on');
    $('#vorgangladendiv').dialog('close');
    loadPOSSession(kassiererId, vorgangid);
  }

  function vorgangspeichern()
  {
    if(true || $('#vorgangname').val()+'' != '')
    {
      storePOSSession('neuervorgang');
    }else{
      storePOSSession('storesess');
    }
    resetFields();
  }
  
  function Deletevorgang(vorgangid)
  {
    if(confirm('Wollen Sie den Vorgang wirklich löschen?'))
    {
      $.ajax({
          url: 'index.php?module=pos&action=list&action=loadsess&cmd=deletevorgang',
          type: 'POST',
          dataType: 'json',
          data: { vorgang: vorgangid},
          success: function(jdata) {
            var oTable = $('#pos_vorgaenge').DataTable( );
            oTable.ajax.reload();
          }
      });
    }
    
  }

  function getPositions()
  {
    var pos = [];
    $('#wk > tbody > tr').each(function(i,e) {
      var nart = {};
      nart['id']      = $(this).prop('id');
      nart['artikel'] = $(this).find('.artikel').html().replace(/\"/g,'&quot;');
      nart['kurztext_de'] = $(this).prop('title').replace(/\"/g,'&quot;');
      nart['nummer']  = $(this).find('.nummer').html();
      nart['tax']     = $(this).find('.tax').html();
      if($(this).find('.tax').length) {
        nart['taxorig'] = $(this).find('.tax').html();
      }
      nart['preis']   = $(this).find('.preisinp').val();
      nart['preisgenauex']   = $(this).find('.preisgenauex').html();
      nart['preisgenau']   = $(this).find('.preisgenau').html();
      nart['erloes']   = $(this).find('.erloes').html();
      nart['amount']  = $(this).find('.amount').val().replace(',','.');
      nart['rabatt']  = $(this).find('.rabatt').val();
      nart['anabregs_text'] = $(this).find('.anabregs_text').html().replace(/\"/g,'&quot;');
      nart['rabattartikel'] = $(this).find('.rabattartikel').html();
      nart['rabatt_prozent'] = $(this).find('.rabatt_prozent').html();
      nart['keinrabatterlaubt'] = $(this).find('.keinrabatterlaubt').html();
      nart['porto'] = $(this).find('.porto').html();
      nart['seriennummernliste'] = $(this).find('.seriennummernliste').val();

      pos.push(nart);
    });
    return pos;
  }
  
  function storePOSSession(actioncomm) {
    var storeobj = {};
    storeobj['kasid'] = $('#tabs-1').data('on');
    if ($("#tabs-1").data('sid')) storeobj['sid'] = $("#tabs-1").data('sid');
    var gezahlt = 0;
    gezahlt = $('#gezahlt').val();
    var tip = $('#tip').val();
    storeobj['addr'] = {};
    if(steuerfrei) {
      storeobj['steuerfrei'] = 1;
    }
    if(ustid != '')storeobj['ustid'] = ustid;
    $('#ob1 span').each(function(i,e) {
      var tid = $(this).prop('id').split('t_');
      storeobj['addr'][tid[1]] = $(this).html();
    });
    // storeobj['addr']['land'] = $('#ob1 #t_land').html();
   
    if(actioncomm==="finsess")
    storeobj['addr']['infoauftragserfassung'] = $.base64Encode( $('#infoauftragserfassung_pos').val() );
    else
    storeobj['addr']['infoauftragserfassung'] = $('#infoauftragserfassung_pos').val();
    // ENDE ANPASSUNG FUER Uebertragung HTML in JSON 

    //storeobj['addr']['infoauftragserfassung'] = $('#infoauftragserfassung_pos').val();
    
    storeobj['addrid'] = $('#adresse').val();
    var ktype = $('input[name=ktype]:checked').val();
    
    if(ktype === 'lk') storeobj['addrid'] = lkaddr;
    else if(ktype === 'nk') storeobj['addrid'] = 'NEW';
    
    storeobj['addrstore'] = "nostore";
    if($('#storenewcust').is(':checked')) storeobj['addrstore'] = "store";
    if (saveCust) {
      storeobj['addrstore'] = "store";
      saveCust = false;
    }
    storeobj['wk'] = [];
    $('#wk > tbody > tr').each(function(i,e) {
      var nart = {};
      nart['id']      = $(this).prop('id');
      nart['artikel'] = $(this).find('.artikel').html().replace(/\"/g,'&quot;');
      nart['kurztext_de'] = $(this).prop('title').replace(/\"/g,'&quot;');
      nart['nummer']  = $(this).find('.nummer').html();
      nart['tax']     = $(this).find('.tax').html();
      if($(this).find('.taxorig').length) {
        nart['taxorig']     = $(this).find('.taxorig').html();
      }
      nart['preis']   = $(this).find('.preisinp').val();
      nart['preisgenauex']   = $(this).find('.preisgenauex').html();
      nart['preisgenau']   = $(this).find('.preisgenau').html();
      nart['amount']  = $(this).find('.amount').val().replace(',','.');
      nart['rabatt']  = $(this).find('.rabatt').val();
      nart['anabregs_text'] = $(this).find('.anabregs_text').html().replace(/\"/g,'&quot;');
      nart['rabattartikel'] = $(this).find('.rabattartikel').html();
      nart['rabatt_prozent'] = $(this).find('.rabatt_prozent').html();
      nart['keinrabatterlaubt'] = $(this).find('.keinrabatterlaubt').html();
      nart['porto'] = $(this).find('.porto').html();
      nart['seriennummernliste'] = $(this).find('.seriennummernliste').val();

      storeobj['wk'].push(nart);
    });
    
    
    storeobj['ptype']   = $('#payment input[name=ptype]:checked').val();
    storeobj['rtype']   = $('#retyp input[name=rtype]:checked').val();
    
    storeobj['inbem']   = $('#inbem').val();
    storeobj['freit']   = $('#freit').val();
    grStr = $('#grabatt').html();
    grArr = grStr.split('%');
    storeobj['grabatt'] = grArr[0];
    storeobj['grabatteur'] = grabatteur;
    storeobj['gezahlt'] = gezahlt;
    storeobj['tip'] = tip;
    storeobj['soll'] = $('#sutotal').text().replace(',', '.');
    storeobj['kassiererId'] = kassiererId;
    var actioncomm2 = actioncomm;
    if(actioncomm === 'belegabrechnen')
    {
      storeobj['cmd'] = 'belegabrechnen';
      storeobj['rechnungid'] = $('#rechnungid').val();
      storeobj['belegtyp'] = $('#belegtyp').val();
      actioncomm2 = 'finsess';
    }
    if(actioncomm === 'teilstornieren')
    {
      storeobj['cmd'] = 'teilstornieren';
      storeobj['rechnungid'] = $('#rechnungid').val();
      storeobj['belegtyp'] = $('#belegtyp').val();
      actioncomm2 = 'finsess';
    }
    if(actioncomm === 'komplettstornieren')
    {
      storeobj['cmd'] = 'komplettstornieren';
      storeobj['rechnungid'] = $('#rechnungid').val();
      storeobj['belegtyp'] = $('#belegtyp').val();
      actioncomm2 = 'finsess';
    }
    if(actioncomm === 'stornieren')
    {
      storeobj['cmd'] = 'stornieren';
      actioncomm2 = 'finsess';
    }
    if(actioncomm === 'neuervorgang')
    {
      storeobj['cmd'] = 'neuervorgang';
      storeobj['vorgangname'] = storeobj['addr']['name'];//$('#vorgangname').val();
      actioncomm2 = 'storesess';
    }    
    
    var vorgangid = $('#vorgangid').val();
    if(vorgangid)storeobj['vorgangid'] = vorgangid;
    storeobj['randnumber'] = randnumber;
    var jsonString = JSON.stringify(storeobj);
    
    $.ajax({
        url: 'index.php?module=pos&action='+actioncomm2,
        type: 'POST',
        dataType: 'json',
        data: { sessdata: jsonString},
        success: function(jdata) {
          if(actioncomm === 'finsess')
          {
            $('#finconfconf').dialog('open');
          }
          else if(actioncomm === 'resetsess') resetFields();
          else if(actioncomm === 'stornieren')
          {
            resetFields();
            $('#finconfconf').dialog('open');
          }
          else if(actioncomm === 'belegabrechnen')
          {
            stornoabbr();
            $('#finconfconf').dialog('open');
          }
          else if(actioncomm === 'teilstornieren')
          {
            stornoabbr();
            $('#finconfconf').dialog('open');
          }
          else if(actioncomm === 'komplettstornieren')
          {
            stornoabbr();
            $('#fauftrag').each(function(){$(this).val('');});
            $('#frechnung').each(function(){$(this).val('');});
            $('#finconfconf').dialog('open');
          }
          else $('#storeconfconf').dialog('open');
          
          $('#artikelnummerprojekt').focus();
          setAbschlusstimer();
        }
    });

    /*$.getJSON( '', {
      module: "pos",
      action: actioncomm,
      sessdata: jsonString
    }).done(function( jdata ) {
      if(actioncomm == 'finsess') $('#finconfconf').dialog('open');
      else if(actioncomm == 'resetsess') resetFields();
      else $('#storeconfconf').dialog('open');
      
      $('#artikelnummerprojekt').focus();
    });*/
  }


  function setAbschlusstimer(){
    var logouttimeabschluss = parseInt($('#logoutkas').data('logouttimeabschluss'));
    if(logouttimeabschluss > 0) {
      if(logouttimerabschluss) {
        clearTimeout(logouttimerabschluss);
      }
      logouttimerabschluss = setTimeout(function () {
        if(kassiererId) {
          $('#logoutkas').trigger('click');
        }
      }, logouttimeabschluss * 1000);
    }
  }

  function showteilstorno(jdata)
  {
    $('#stornotab table tbody').find('tr').remove();
    if(typeof jdata['Fehler'] != 'undefined')
    {
      stornoabbr();
      alert(jdata['Fehler']);
      return;
    }
    
    if(typeof jdata['zahlungsweise'] != 'undefined')
    {
      switch(jdata['zahlungsweise'])
      {
        case 'bar':
          $('#bar').each(function(){$(this).prop('checked',true)});
        break;
        case 'ec':
        case 'eckarte':
          $('#ec').each(function(){$(this).prop('checked',true)});
        break;
        case 'kredit':
        case 'kreditkarte':
          $('#kredit').each(function(){$(this).prop('checked',true)});
        break;        
        case 'ueb':
        case 'rechnung':
          $('#ueb').each(function(){$(this).prop('checked',true)});
        break;
        default:
          $('#bar').each(function(){$(this).prop('checked',true)});
        break;
      }
    }
    
    if(typeof jdata['belegtyp'] != 'undefined')$('#belegtyp').val(jdata['belegtyp']);
    if(typeof jdata['belegid'] != 'undefined')$('#rechnungid').val(jdata['belegid']);
    if(typeof jdata['steuerfrei'] != 'undefined')$('#teilstornosteuerfrei').val(jdata['steuerfrei']);
    if(typeof jdata['ustid'] != 'undefined')$('#teilstornoustid').val(jdata['ustid']);
    
    if(typeof jdata['originkundennummer'] != 'undefined')
    {
      $('#adresse').val(jdata['originkundennummer']);
      $('#loadaddr').prop('checked',true);
    }
    $.each(jdata['wk'], function(i,e) {
      addarticlestorno(e);
    });
    $('#stornotab').show();
  }
    
  function showdata(jdata)
  {
    resetFields();
    
    if(typeof jdata['steuerfrei'] != 'undefined' && (jdata['steuerfrei'] == '1' || jdata['steuerfrei'] == 1)) {
      steuerfrei = true;
    }
    else{
      steuerfrei = false;
    }
    if(typeof jdata['ustid'] != 'undefined')
    {
      ustid = jdata['ustid'];
    }else{
      ustid = '';
    }
    
    if(typeof jdata['Fehler'] != 'undefined')
    {
      stornoabbr();
      alert(jdata['Fehler']);
      return false;
    }

    if(typeof jdata['zahlungsweise'] != 'undefined')
    {
      switch(jdata['zahlungsweise'])
      {
        case 'bar':
          $('#bar').each(function(){$(this).prop('checked',true)});
        break;
        case 'ec':
        case 'eckarte':
          $('#ec').each(function(){$(this).prop('checked',true)});
        break;
        case 'kredit':
        case 'kreditkarte':
          $('#kredit').each(function(){$(this).prop('checked',true)});
        break;        
        case 'ueb':
        case 'rechnung':
          $('#ueb').each(function(){$(this).prop('checked',true)});
        break;
        default:
          $('#bar').each(function(){$(this).prop('checked',true)});
        break;
      }
    }
    if(typeof jdata['belegtyp'] != 'undefined')$('#belegtyp').val(jdata['belegtyp']);
    $('#rechnungid').val(jdata['belegid']);
    $.each(jdata['addr'], function(i, e) {
      $('#ob1 #t_'+i).html(e);
    });
    var anzteile = $(jdata['wk']).length;
    $.each(jdata['wk'], function(i,e) {
      addarticle(e, anzteile);
    });
    
    if(typeof jdata['status'] != 'undefined' && jdata['status'] == 'abgeschlossen')
    {
      $('#wkcontainer input.amount').prop('disabled', true);
      $('#wkcontainer input.rabatt').prop('disabled', true);
      $('#loadartsubmit').prop('disabled', true);
    }else{
      $('#loadartsubmit').prop('disabled', false);
      $('#wkcontainer input.amount').prop('disabled', false);
      $('#wkcontainer input.rabatt').prop('disabled', false);
    }
    
    if(typeof jdata['originkundennummer'] != 'undefined')
    {
      $('#adresse').val(jdata['originkundennummer']);
      $('#loadaddr').prop('checked',true);
    }
    $('#wk .editname').remove();
    $('#wk .preisEditLink').remove();
    $('#wk .delwkart').remove();
    $('#wl input').prop('disabled', true);
    $('#wl input').prop('readonly', true);
    $('#finsale').hide();
    //$('#abortsale').hide();
    $('#abbrechnenbutton').show();
    $('#stornoabbrechen').show();
    $('#abbrechnenbutton').css('display','inline-block');
    $('#stornoabbrechen').css('display','inline-block');
    $('#komplettstornobutton').hide();
    $('#stornobutton').hide();
    $('#belegeladendiv').dialog('close');
    $('#stornotab').hide();
    return true;
  }
    
  function loadPOSSession(kasid, vorgangid) {
    if(typeof vorgangid == 'undefined')
    {
      vorgangid = 0;
      $('#vorgangid').val('');
    }else{
      if(vorgangid > 0)
      {
        $('#vorgangid').val(vorgangid);
      }else{
        $('#vorgangid').val('');
      }
    }
    
    // check for pending sessions
    $.getJSON( '', {
      module: "pos",
      action: "loadsess",
      kasid: kasid, vorgang:vorgangid
    }).done(function( jdata ) {
      randnumber = (Math.random() * (99999999 - 10000000)) + 10000000;
      // no stored session found
      if('check' in jdata && jdata['check'] == "noss") {
        return false;
      }
    
      if('sid' in jdata) {
        $("#tabs-1").data('sid', jdata['sid']);
      }
  
      if (jdata.kassiererId != kassiererId) {
        kassiererId = jdata.kassiererId;
      }

      $.each(jdata['addr'], function(i, e) {
        $('#ob1 #t_'+i).html(e);
      });
      // set address radio button accordingly
      //$('input[name=ktype]').removeProp('checked');
      // Kundenid aus input "Stammkunde" löschen, falls neuer kunde oder laufkundschaft
      $('#adresse').val('');
      if(jdata['addrid'] == "NEW") {
        $('input[name=ktype][value=nk]').prop('checked',true);
        $('input[name=ktype][value=nk]').attr('checked',true);
      } else if(jdata['addrid'] == lkaddr) {
        //$('#adjcust').show();
        $('input[name=ktype][value=lk]').prop('checked',true);
        $('input[name=ktype][value=lk]').attr('checked',true);
      } else {
        $('#adjcust').show();
        $('input[name=ktype][value=sk]').prop('checked',true);
        $('input[name=ktype][value=sk]').attr('checked',true);
        $('#adresse').val(jdata['addrid']);
      }

      // Warenkorb Anfang
      var anzteile = $(jdata['wk']).length
      $.each(jdata['wk'], function(i,e) {
        addarticle(e, anzteile);
      });
      
      if(jdata['grabatt']) {
        if(jdata['grabatt']>0)
          $('#wk tbody .rabatt').val(jdata['grabatt']);
        $("#grabatt").val(jdata['grabatt']+'%');
      }
      if(jdata['grabatteur']) {
        grabatteur = jdata['grabatteur'];
      }
      
      grabatt = 0;
      $('#wk > tbody > tr').each(function() {
        updatearttotal($(this));
      });
      updatetotals();
      grabatt = 0;
      // Warenkorb Ende
      
      $("#"+jdata['ptype']).prop('checked','checked');
      $("#"+jdata['rtype']).prop('checked','checked');

      if (typeof jdata.inbem != "undefined") {
        $("#inbem").val(jdata.inbem);  
      }
      if (typeof jdata.freit != "undefined") {
        $("#freit").val(jdata.freit);  
      }
      /*
      $("#inbem").val(jdata['inbem']);
      $("#freit").val(jdata['freit']);
      */
      if(typeof jdata['sesssionbezeichnung'] != "undefined")
      {
        $('#vorgangname').val(jdata['sesssionbezeichnung']);
      }
      $('#artikelnummerprojekt').focus();
    });

  }
    
    
  function logoutkas() {
    $.getJSON( '', {
      module: "pos",
      action: "logoutkass"
    }).done(function( jdata ) {
      $('#tabs-1 input').prop('disabled','disabled');
      $('#loadkass input').removeAttr('disabled');
      $('#tabs-1').removeData('on');
      // $('#logoutkas').hide();
      $('#loggedinkas').html('');
      $('#filiale').html('');
      $('#kashin').dialog('open');
      randnumber = (Math.random() * (99999999 - 10000000)) + 10000000;
    });
  }
  
  
  function loginkas(kanr) {

    if ($("#tabs-1").data('on')) {
      if($("#tabs-1").data('on') == kanr) {
        alert('Kassierer ist bereits angemeldet');
        return false;
      }
      storePOSSession('storesess');
      resetFields();
      $('#tabs-1').removeData('on');
    }
    
    $.getJSON( '', {
      module: "pos",
      action: "loadkass",
      kanr: kanr
    }).done(function( data ) {

      if(data.check != "ERR") {
        $('#tabs-1 input').removeAttr('disabled');
        $('#tabs-1').data('on',kanr);
        lkaddr = data.lkadresse; 
        // $('#logoutkas').show();
        $('#loggedinkas').html(' ' + data.kname);
        $('#filiale').html(' ' + data.filiale);
        //$('#kanr').html(kanr);
        kassiererId = kanr;

        loadPOSSession(kanr);
        window.location.reload();
      } else {
        kassiererId = null;
        alert('Kassierer nicht vorhanden');
        logoutkas();
        randnumber = (Math.random() * (99999999 - 10000000)) + 10000000;
      }
    });
  }
  
    
  function resetFields() {
    steuerfrei = false;
    ustid = '';
    randnumber = (Math.random() * (99999999 - 10000000)) + 10000000;
    $('#loadartsubmit').prop('disabled', false);
    $('#wkcontainer input.amount').prop('disabled', false);
    $('#wkcontainer input.rabatt').prop('disabled', false);
    belegabrechnenaktiv = false;
    $('#vorgangid').val('');
    // Adressen Felder
    $('#modalcont input[type=text]').val('');
    $('#ob1 span').html('');
    $('#adresse').val('');
    $('input[name=ktype]').removeProp('checked');


    $('input[name=ktype][value=lk]').prop('checked',true);
    $('.rechnungsadresse_container').find('#t_name').text('Laufkundschaft');
    //$('input[name=ktype][value=sk]').prop('checked',true);

    $('#wk tbody').html('');
    $('#grabatt').text('0%');

    grabatteur = '0.00';
    grabatteur = parseFloat(grabatteur.replace(',','.'));

    grabatt = '0.00';
    grabatt = parseFloat(grabatt.replace(',','.'));

    // $('#trabatteur').val(0.0);
    // $('#trabatteur').text('0.0);

    grabatt = 0;
    updatetotals();
    $('#artikelnummerprojekt').val('');
    
    
    $('#inbem').val('');
    $('#freit').val('');
    
    
    $('#payment input:checked').prop('checked', false);
    $('#retyp input:checked').prop('checked', false);
    if(!zahlungselzwang) $($('#payment input[name=ptype]')[0]).prop('checked', true);
    $($('#retyp input[name=rtype]')[0]).prop('checked', true);
    
    $("#tabs-1").removeData('sid');

  }

function tinyMCEsetup() {

  

  tinyMCE.init({
  selector: '#infoauftragserfassung_pos',
  mode: "textareas",
  theme: "modern",
  menubar: false,
  statusbar: false,
  toolbar_items_size: 'small',
  width: "100%",
  entity_encoding: "raw",
  element_format: "html",
  force_br_newlines: true,
  force_p_newlines: false,
  plugins: [ "textcolor" ],
  toolbar1: "bold italic underline strikethrough |  styleselect formatselect fontsizeselect | searchreplace | forecolor backcolor | restoredraft",
  toolbar2: "",
  toolbar3: "",
  setup: function (editor) {
      editor.on('keyup', function () {

          $('textarea#infoauftragserfassung_pos').val(tinyMCE.get('infoauftragserfassung_pos').getContent());


      });
    }
  });

}
 
function changeArtikelPreis(container) {

  var elem = $(container);
  elem.parent().parent().next().show();
  elem.parent().parent().hide();

  elem.parent().parent().next().find('input').focus();
  elem.parent().parent().next().find('input').keydown(function(e) {


    if (e.which == 13) {

      var newVal = $(this).val();
      if (newVal == '') {
        newVal = 0;
      }

      newVal = newVal.replace(',', '.');
      newVal = parseFloat(newVal).toFixed(2);
      newVal = newVal.replace('.', ',');

      $(this).parent().parent().prev().children('td').first().text( newVal );
      $(this).parent().parent().hide();
      $(this).parent().parent().prev().show();

    } else if ($.inArray(e.keyCode, [45, 46, 8, 9, 27, 13, 110, 188,109]) !== -1 ||
       // Allow: Ctrl+A, Command+A
      (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) || 
       // Allow: home, end, left, right, down, up
      (e.keyCode >= 35 && e.keyCode <= 40)) {
       // let it happen, don't do anything
       return;
    }

    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105) && e.keyCode != 173) { // 173 = minus
        e.preventDefault();
    }

  });

  elem.parent().parent().next().find('input').on('focusout',function() {

    var newVal = $(this).val();
    if (newVal == '') {
      newVal = 0;
    }


    newVal = newVal.replace(',', '.');
    newVal = parseFloat(newVal).toFixed(2);
    newVal = newVal.replace('.', ',');

    $(this).parent().parent().prev().children('td').first().text( newVal );
    $(this).parent().parent().hide();
    $(this).parent().parent().prev().show();
    
  });
} 


function PosAbschlussZellenwert(zelle)
{
  var anzahl = $("[name="+zelle+"]").val();

  //TODO nur ganze zahlen oder nichts erlauben

  var wert = zelle.replace('eur00','0.0');
  wert = wert.replace('eur0','0.');
  wert = wert.replace('eur','');
  wert = wert * anzahl;

  wert = parseFloat(wert).toFixed(2);
  wert = wert.replace('.', ',');

  $("[name="+zelle+"label]").html(wert);
  PosAbschlussCalcall();
}

function PosAbschlussCalcall()
{
  var scheine = 0;
  var muenzen = 0;
  $('div.SCHEINE').each(function(){
    var schein = $(this).html().replace(',','.');
    if(schein == '')schein = 0;
    scheine = scheine + parseFloat(schein);
  });
  $('div.MUENZEN').each(function(){
    var muenze = $(this).html().replace(',','.');
    if(muenze == '')muenze = 0;
    muenzen = muenzen + parseFloat(muenze);
  });
  var gesamt = scheine + muenzen;
  scheine =  addCommas(scheine.toFixed(2));
  muenzen = addCommas(muenzen.toFixed(2));
  var gesamt2 = addCommas(gesamt.toFixed(2));
  $('#gesamtscheine').html(scheine +' EUR');
  $('#gesamtmuenzen').html(muenzen +' EUR');
  $('#gesamt').html(gesamt2 + ' EUR');
  var soll = $('#soll').html().replace(' EUR','');
  soll = soll.replace('.','');
  soll = soll.replace(',','.');
  soll = parseFloat(soll);
  var diff = gesamt - soll;
  if(diff == 0)
  {
    $('#differenz').html('0,00 EUR');
    $('#korrekturtext').html('Es ist keine Differenz vorhanden');
  }else{
    diff = addCommas(diff.toFixed(2));
    $('#differenz').html('<span style="color:red">'+diff+' EUR</span>');
    $('#korrekturtext').html('Es ist eine Differenz von '+diff+' EUR vorhanden. Beim Festschreiben wird diese als Differenzbuchung automatisch angelegt und verbucht.');
  }  
}

function addCommas(nStr)
{
    nStr += '';
    var x = nStr.split('.');
    var x1 = x[0];
    var x2 = x.length > 1 ? ',' + x[1] : '';
    var rgx = /(\d+)(\d{3})/;
    while (rgx.test(x1)) {
        x1 = x1.replace(rgx, '$1' + '.' + '$2');
    }
    return x1 + x2;
}

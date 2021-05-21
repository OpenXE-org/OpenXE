<div id="zuordnungAuftragZuBestellung[MD5]" style="display:none;" title="Zuordnen eines Auftrages">
    <h4 id="error"></h4>
    <input type="text" id="zuordnungAuftragZuBestellungValue[MD5]" name="zuordnungAuftragZuBestellungValue[MD5]" value="[AUFTRAGZUBESTELLUNG]" style="width: 100%;"/>
</div>
<div id="zuordnungAuftragZuBestellungRueckmeldung" style="display:none;" title="Information">
    <p class="rueckmeldung">Hier die Rückmeldung</p>
</div>

<div id="popup[MD5]" style="display:none;">
  <table>
    <tr><td nowrap>neuer Preis:</td><td><input type="hidden" id="bpid[MD5]" /><input type="text" size="8" id="preis[MD5]" /></td></tr>
    <tr><td nowrap>W&auml;hrung:</td><td><input type="text" size="8" id="waehrung[MD5]" /></td></tr>
    <tr><td nowrap>ab Menge:</td><td><input type="text" size="20" id="ab_menge[MD5]" /><input type="hidden" id="ab_mengeorig[MD5]" /></td></tr>
    <tr><td nowrap>Bestellnummer:</td><td><input type="text" size="20" id="bestellnummer[MD5]" /></td></tr>
    <tr><td nowrap>Bezeichnung:</td><td><input type="text" size="20" id="bezeichnung[MD5]" /></td></tr>
    <tr><td nowrap>In Stammdaten anpassen:</td><td><input type="checkbox" value="1" id="auchinstammdaten[MD5]" /></td></tr>
  </table>
</div>
<div id="confirmpopup[MD5]" style="display:none;">
<p>Die Menge <span id="spanmenge[MD5]"></span> unterscheidet sich zu der im Einkaufspreis hinterlegten Menge.<br />
Soll ein neuer Einkaufspreis mit dieser Menge angelegt werden, oder der Einkaufspreis ab Menge <span id="spanab_menge[MD5]"></span> angepasst werden?</p>
</div>
<script>

  function changepreis[MD5](bpid)
  {
    $.ajax({
        url: 'index.php?module=bestellung&action=minidetail&cmd=getpreis&id=[ID]',
        type: 'POST',
        dataType: 'json',
        data: { bp: bpid},
        success: function(data) {
          $('#bpid[MD5]').val(data.id);
          $('#preis[MD5]').val(data.preis);
          $('#waehrung[MD5]').val(data.waehrung);
          $('#bezeichnung[MD5]').val(data.bezeichnunglieferant);
          $('#bestellnummer[MD5]').val(data.bestellnummer);
          $('#ab_menge[MD5]').val(data.menge);
          if(typeof data.ab_menge != 'undefined')
          {
            $('#spanab_menge[MD5]').html(data.ab_menge);
            $('#ab_mengeorig[MD5]').val(data.ab_menge);
            $('#spanmenge[MD5]').html(data.menge);
            if(data.ab_menge != data.menge)
            {
              
              
              //$('#confirmpopup[MD5]').dialog('open');
            }else{
              //$('#ab_menge[MD5]').val(data.ab_menge);
              //
            }
            $('#popup[MD5]').dialog('open');
          }else{
            $('#ab_mengeorig[MD5]').val('');
            
            $('#popup[MD5]').dialog('open');
          }
          $('#auchinstammdaten[MD5]').prop(data.auchinstammdaten?true:false);
          
        },
        beforeSend: function() {

        }
    });
  }
  
  $(document).ready(function() {
	  $("input#zuordnungAuftragZuBestellungValue[MD5]").autocomplete({
		  source: "index.php?module=ajax&action=filter&filtername=auftrag",
	  });

	  $("#zuordnungAuftragZuBestellung[MD5]").dialog({
		  modal: true,
		  bgiframe: true,
		  minWidth:420,
		  autoOpen: false,
		  closeOnEscape: false,
		  open: function(event, ui) {
		  	$(".ui-dialog-titlebar-close").hide();
		  	},
		  buttons: {
			  ABBRECHEN: function() {
				$('#zuordnungAuftragZuBestellung[MD5]').dialog('close');
			  },
			  SPEICHERN: function() {
							  $.ajax({
								  url: 'index.php?module=bestellung&action=minidetail&cmd=zuordnungAuftragZuBestellung&id=[ID]',
								  type: 'POST',
								  dataType: 'json',
								  data: { auftrag: $('#zuordnungAuftragZuBestellungValue[MD5]').val()},
								  success: function(data) {
								  	if(data.zugeordnet == '0'){
									  $(".rueckmeldung").html("Es konnte keine Position zugeordnet werden.");
									}else if(data.zugeordnet != data.gesamtpositionen){
									  $(".rueckmeldung").html("Von " + data.gesamtpositionen + " Positionen konnten nur " + data.zugeordnet + " zugeordnet werden.");
									}else if(data.zugeordnet == data.gesamtpositionen){
									  $(".rueckmeldung").html("Es konnten alle Positionen zugeordnet werden.");
									}
									$('#zuordnungAuftragZuBestellung[MD5]').dialog('close');
									$('#zuordnungAuftragZuBestellungRueckmeldung').dialog('open');
								  },
								  beforeSend: function() {
								  }
							  });
			  }
		  }
	  });

	  $("#zuordnungAuftragZuBestellungRueckmeldung").dialog({
		  modal: true,
		  bgiframe: true,
		  minWidth:420,
		  autoOpen: false,
		  closeOnEscape: false,
		  open: function(event, ui) {
			  $(".ui-dialog-titlebar-close").hide();
		  },
		  buttons: {
			  OK: function() {
				$('#zuordnungAuftragZuBestellungRueckmeldung').dialog('close');
			  }
		  }
	  });



    $('#confirmpopup[MD5]').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 600,
      title:'Preis ändern',
      buttons: {
        'MENGE AUS EINKAUFSPREIS ÜBERNEHMEN': function()
        {
          $.ajax({
              url: 'index.php?module=bestellung&action=minidetail&cmd=savepreis&id=[ID]',
              type: 'POST',
              dataType: 'json',
              data: {
                bp:$('#bpid[MD5]').val(),
                preis:$('#preis[MD5]').val(),
                waehrung:$('#waehrung[MD5]').val(),
                bezeichnung:$('#bezeichnung[MD5]').val(),
                bestellnummer:$('#bestellnummer[MD5]').val(),
                ab_menge:$('#spanab_menge[MD5]').text(),
                menge:$('#ab_menge[MD5]').val(),
                md5:'[MD5]',
                auchinstammdaten:$('#auchinstammdaten[MD5]').prop('checked')?1:0
              },
              success: function(data) {
                if(typeof data.preis != 'undefined')
                {
                  $('#spanpreis[MD5]'+data.id).html(data.preis);
                }
                if(typeof data.bestellnummer != 'undefined')
                {
                  $('#spanbestellnummer[MD5]'+data.id).html(data.bestellnummer);
                }
                if(typeof data.menge != 'undefined')
                {
                  $('#spanmenge[MD5]'+data.id).html(data.menge);
                }
                $('#confirmpopup[MD5]').dialog('close');
                $('#popup[MD5]').dialog('close');
              },
              beforeSend: function() {

              }
          });
        },
        'MENGE AUS BESTELLUNG ÜBERNEHMEN': function() {
          $('#ab_menge[MD5]').val($('#spanmenge[MD5]').text());
          $.ajax({
              url: 'index.php?module=bestellung&action=minidetail&cmd=savepreis&id=[ID]',
              type: 'POST',
              dataType: 'json',
              data: {
                bp:$('#bpid[MD5]').val(),
                preis:$('#preis[MD5]').val(),
                waehrung:$('#waehrung[MD5]').val(),
                bezeichnung:$('#bezeichnung[MD5]').val(),
                bestellnummer:$('#bestellnummer[MD5]').val(),
                ab_menge:$('#ab_menge[MD5]').val(),
                menge:$('#ab_menge[MD5]').val(),
                md5:'[MD5]',
                auchinstammdaten:$('#auchinstammdaten[MD5]').prop('checked')?1:0
              },
              success: function(data) {
                if(typeof data.preis != 'undefined')
                {
                  $('#spanpreis[MD5]'+data.id).html(data.preis);
                }
                if(typeof data.bestellnummer != 'undefined')
                {
                  $('#spanbestellnummer[MD5]'+data.id).html(data.bestellnummer);
                }
                if(typeof data.menge != 'undefined')
                {
                  $('#spanmenge[MD5]'+data.id).html(data.menge);
                }
                $('#confirmpopup[MD5]').dialog('close');
                $('#popup[MD5]').dialog('close');
              },
              beforeSend: function() {

              }
          });
        }
      },
      close: function(event, ui){
        
      }
    });
  
    $('#popup[MD5]').dialog(
    {
      modal: true,
      autoOpen: false,
      minWidth: 400,
      title:'Preis ändern',
      buttons: {
        SPEICHERN: function()
        {
          if($('#auchinstammdaten[MD5]').prop('checked'))
          {
            $.ajax({
                url: 'index.php?module=bestellung&action=minidetail&cmd=checkmenge&id=[ID]',
                type: 'POST',
                dataType: 'json',
                data: {
                  bp:$('#bpid[MD5]').val(),
                  ab_menge:$('#ab_menge[MD5]').val(),
                  md5:'[MD5]'
                },
                success: function(data) {
                  if(data.menge != data.ab_menge)
                  {
                    $('#spanmenge[MD5]').html(data.menge);
                    $('#spanab_menge[MD5]').html(data.ab_menge);
                    $('#confirmpopup[MD5]').dialog('open');
                    $('#popup[MD5]').dialog('close');
                  }else{
                    $.ajax({
                        url: 'index.php?module=bestellung&action=minidetail&cmd=savepreis&id=[ID]',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                          bp:$('#bpid[MD5]').val(),
                          preis:$('#preis[MD5]').val(),
                          waehrung:$('#waehrung[MD5]').val(),
                          bezeichnung:$('#bezeichnung[MD5]').val(),
                          bestellnummer:$('#bestellnummer[MD5]').val(),
                          ab_menge:$('#ab_menge[MD5]').val(),
                          md5:'[MD5]',
                          auchinstammdaten:$('#auchinstammdaten[MD5]').prop('checked')?1:0
                        },
                        success: function(data) {
                          if(typeof data.preis != 'undefined')
                          {
                            $('#spanpreis[MD5]'+data.id).html(data.preis);
                          }
                          if(typeof data.bestellnummer != 'undefined')
                          {
                            $('#spanbestellnummer[MD5]'+data.id).html(data.bestellnummer);
                          }
                          if(typeof data.menge != 'undefined')
                          {
                            $('#spanmenge[MD5]'+data.id).html(data.menge);
                          }
                          $('#popup[MD5]').dialog('close');
                        },
                        beforeSend: function() {

                        }
                    });
                  }
                },
                beforeSend: function() {

                }
            });
          }else{
            $.ajax({
                url: 'index.php?module=bestellung&action=minidetail&cmd=savepreis&id=[ID]',
                type: 'POST',
                dataType: 'json',
                data: {
                  bp:$('#bpid[MD5]').val(),
                  preis:$('#preis[MD5]').val(),
                  waehrung:$('#waehrung[MD5]').val(),
                  bezeichnung:$('#bezeichnung[MD5]').val(),
                  bestellnummer:$('#bestellnummer[MD5]').val(),
                  ab_menge:$('#ab_menge[MD5]').val(),
                  menge:$('#ab_menge[MD5]').val(),
                  md5:'[MD5]',
                  auchinstammdaten:$('#auchinstammdaten[MD5]').prop('checked')?1:0
                },
                success: function(data) {
                  if(typeof data.preis != 'undefined')
                  {
                    $('#spanpreis[MD5]'+data.id).html(data.preis);
                  }
                  if(typeof data.bestellnummer != 'undefined')
                  {
                    $('#spanbestellnummer[MD5]'+data.id).html(data.bestellnummer);
                  }
                  if(typeof data.menge != 'undefined')
                  {
                    $('#spanmenge[MD5]'+data.id).html(data.menge);
                  }
                  $('#popup[MD5]').dialog('close');
                },
                beforeSend: function() {

                }
            });
          }
        },
        ABBRECHEN: function() {
          $(this).dialog('close');
        }
      },
      close: function(event, ui){
        
      }
    });
  });
</script>

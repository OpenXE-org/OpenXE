<!-- gehort zu tabview -->
<div id="tabs">
  <ul>
    <li><a href="#tabs-1"><!--{|Offene Zahlungen|}--></a></li>
    <!--<li><a href="#tabs-2">{|Abgeschlossene Zahlungen|}</a></li>-->
  </ul>
<!-- ende gehort zu tabview -->

  <!-- erstes tab -->
  <div id="tabs-1">
    <form method="post">
      [MESSAGE]
      <div class="row">
        <div class="row-height">
          <div class="col-xs-12 col-md-10 col-md-height">
            <div class="inside inside-full-height">
              <fieldset>
                <div class="filter-box filter-usersave">
                  <div class="filter-block filter-inline">
                    <div class="filter-title">{|Filter|}</div>
                    <ul class="filter-list">
                      <li class="filter-item">
                        <label for="open" class="switch">
                          <input type="checkbox" id="open" />
                          <span class="slider round"></span>
                        </label>
                        <label for="open">{|nur Offene|}</label>
                      </li>
                      <li class="filter-item">
                        <label for="failed" class="switch">
                          <input type="checkbox" id="failed" />
                          <span class="slider round"></span>
                        </label>
                        <label for="failed">{|nur Fehlgeschlagene|}</label>
                      </li>
                      <li class="filter-item">
                        <label for="ok" class="switch">
                          <input type="checkbox" id="ok" />
                          <span class="slider round"></span>
                        </label>
                        <label for="ok">{|nur Ausgef&uuml;hrte|}</label>
                      </li>
                      <li class="filter-item">
                        <label for="onlyliability" class="switch">
                          <input type="checkbox" id="onlyliability" />
                          <span class="slider round"></span>
                        </label>
                        <label for="onlyliability">{|nur Verbindlichkeiten|}</label>
                      </li>
                      <li class="filter-item">
                        <label for="onlyreturnorder" class="switch">
                          <input type="checkbox" id="onlyreturnorder" />
                          <span class="slider round"></span>
                        </label>
                        <label for="onlyreturnorder">{|nur Gutschriften|}</label>
                      </li>
                    </ul>
                  </div>
                </div>
              </fieldset>

            </div>
          </div>
          <div class="col-xs-12 col-md-2 col-md-height">
            <div class="inside inside-full-height">
              <div class="clearfix">
                <fieldset>
                  <legend>{|&Uuml;bersicht|}</legend>
                  <table width="100%>">
                    <tr>
                      <td>{|Gesamt offen|}</td>
                    </tr>
                    <tr>
                      <td class="greybox" width="20%">[GESAMTOFFEN]</td>
                    </tr>
                  </table>
                </fieldset>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <div class="row-height">
          <div class="col-xs-12 col-md-10 col-md-height">
            <div class="inside_white inside-full-height">
              <fieldset class="white">
              <legend> </legend>
                [TAB1]
              </fieldset>
            </div>
          </div>
          <div class="col-xs-12 col-md-2 col-md-height">
            <div class="inside inside-full-height">
              <fieldset>
                <legend>{|Aktion|}</legend>
                <table width="100%>">
                  <tr>
                    <td>
                      <input type="button" class="btnGreenNew" value="&#10010; {|Neue Überweisung|}" data-editpaymenttransaction="0" >
                    </td>
                  </tr>
                  <tr>
                    <td>
                      [VERBINDLICHKEITENLADEN]
                    </td>
                  </tr>
                </table>
              </fieldset>
            </div>
          </div>
        </div>
      </div>

      <fieldset>
        <legend>{|Stapelverarbeitung|}</legend>
        [SAMMELDRUCK]
      </fieldset>
    </form>
  </div>
  <!--<div id="tabs-2">
    [TAB2]
  </div>-->
<!-- tab view schließen -->
</div>
<!-- ende tab view schließen -->
<div id="editReturnOrder" style="display: none;">
  <form id="editReturnOrderForm" name="editReturnOrderForm" method="post">
    <fieldset>
      <legend>&nbsp;</legend>
      <table>
        <tr>
          <td>
            <input type="hidden" name="save" id="save" value="" />
            <input type="hidden" name="payment_transaction_id" id="payment_transaction_id" value="" />
            <label for="payment_transaction_address">{|Zahlungsempf&auml;nger|}:</label>
          </td>
          <td>
            <input type="text" name="payment_transaction_address" id="payment_transaction_address" size="40" />
          </td>
        </tr>
      </table>
    </fieldset>

    <div id="editReturnOrderContent">

    </div>
  </form>
</div>

<div id="editUeberweisung" style="display:none;" title="Überweisung bearbeiten">
  <form id="editUeberweisungForm" name="editUeberweisungForm">
    <input type="hidden" name="entryid" id="entryid" value="" />
    <input type="hidden" name="adresseid" id="adresseid" value="" />
    <fieldset class="ueberweisungstraeger">
      <table class="mkTableFormular">
        <tr>
          <td class="headline">{|Zahlungsempf&auml;nger:|}</td>
          <td><input type="text" name="adresse" id="adresse" value="" size="50"/></td>
        </tr>
      </table>
    </fieldset>

    <fieldset class="ueberweisungstraeger">
      <legend>{|Angaben zum Zahlungsempf&auml;nger|}</legend>
      <table class="mkTableFormular">
        <tr>
          <td class="headline">{|Name:|}</td>
          <td><input type="text" id="empfaenger" name="empfaenger" value="" placeholder="" size="50"/></td>
        </tr>
        <tr>
          <td class="headline">{|IBAN:|}</td>
          <td><input id="iban" name="iban" type="text" value="" placeholder="" size="50"/></td>
        </tr>
        <tr>
          <td class="headline">{|BIC:|}</td>
          <td><input id="bic" name="bic" type="text" value="" placeholder="" size="50"/></td>
        </tr>
      </table>
    </fieldset>
    <fieldset class="ueberweisungstraeger">
      <legend>{|Betrag|}</legend>
      <table class="mkTableFormular">
        <tr>
          <td class="headline">{|Euro, Cent:|}</td>
          <td style="vertical-align: top;"><input type="text" id="betrag" name="betrag" value="" placeholder="" size="50"/></td>
        </tr>
        <tr>
          <td class="headline">{|W&auml;hrung:|}</td>
          <td><input type="text" id="waehrung" name="waehrung" value="" placeholder="" size="50"/></td>
        </tr>
      </table>
    </fieldset>
    <fieldset class="ueberweisungstraeger">
      <legend>{|Verwendungszweck|}</legend>
      <table class="mkTableFormular">
        <tr>
          <td class="headline">{|Zeile 1:|}</td>
          <td><input type="text" id="vz1" name="vz1" value="" placeholder="" size="50"/></td>
        </tr>
        <tr>
          <td class="headline">{|Zeile 2:|}</td>
          <td><input type="text" id="vz2" name="vz2" value="" placeholder="" size="50"/></td>
        </tr>
      </table>
    </fieldset>
    <fieldset class="ueberweisungstraeger">
      <legend>{|Datum|}</legend>
      <table class="mkTableFormular">
        <tr>
          <td class="headline">{|Datum:|}</td>
          <td><input type="text" id="datumueberweisung" name="datumueberweisung" value="" placeholder="" /></td>
        </tr>
      </table>
    </fieldset>
  </form>
</div>

<div id="pdfvorschaudiv" style="display:none;">
  <button id="pdfclosebutton" role="button" aria-disabled="false" title="close">
<svg xmlns="http://www.w3.org/2000/svg" width="19px" height="19px" viewBox="0 0 401.68 401.66">
  <path fill="#333" d="M401.69 60.33L341.33 0 200.85 140.5 60.35 0 0 60.33l140.5 140.5L0 341.33l60.35 60.33 140.5-140.5 140.48 140.5 60.36-60.33-140.51-140.5 140.51-140.5z"></path>
</svg>
</button>
  <iframe id="pdfiframe" src="" width="890;" style="border:none;margin-top:30px;margin-left:5px;" border=""></iframe>
</div>



<script>
  /*var pdfinterval = null;
  function pdfleave()
  {
    if(pdfinterval != null)clearTimeout(pdfinterval);
    pdfinterval = setInterval(function(){ 
      $('#pdfvorschaudiv').hide();
    
    },2000);
  }*/
  /*
	$(document).ready(function() {
		$("#editUeberweisung").dialog({
			modal: true,
			bgiframe: true,
			closeOnEscape:false,
			minWidth:700,
			maxHeight:800,
			autoOpen: false,
			buttons: {
				'ABBRECHEN': function() {
					editUeberweisungReset();
					$(this).dialog('close');
				},
				'SPEICHERN': function() {
					editUeberweisungSave();
				}
			},
			close: function( event, ui ) {
				editUeberweisungReset();
				$(this).dialog('close');
			}
		});
    $("#editReturnOrder").dialog({
      modal: true,
      bgiframe: true,
      closeOnEscape:false,
      minWidth:700,
      maxHeight:800,
      autoOpen: false,
      buttons: {
        'ABBRECHEN': function() {
          $(this).dialog('close');
        },
        'SPEICHERN': function() {
          editReturnOrderSave();
        }
      },
      close: function( event, ui ) {
        $(this).dialog('close');
      }
    });
    $('#pdfclosebutton').on('click',function(){
      if(pdfinterval != null)clearTimeout(pdfinterval);
      $('#pdfvorschaudiv').hide();
    });
    $('#pdfvorschaudiv').on('mouseover', function(){
      if(pdfinterval != null)clearTimeout(pdfinterval);
    });
    $('#pdfvorschaudiv').on('mouseleave', function(){
      if(pdfinterval != null)clearTimeout(pdfinterval);
      pdfinterval = setInterval(function(){ 
        $('#pdfvorschaudiv').hide();

      },1000);
    });
	});
  */
/*
  function pdfvorschau(el, element)
  {
    var pos = $(element).position();
    if(pdfinterval != null)clearTimeout(pdfinterval);
    $.ajax({
        url: 'index.php?module=zahlungsverkehr&action=ueberweisung&cmd=pdfvorschau&aktion=verbindlichkeit&parameter='+ el ,
        type: 'POST',
        dataType: 'json',
        data: {},
        success: function(data) {
          $('#pdfiframe').prop('src',data.src);
          $('#pdfvorschaudiv').show();
          $('#pdfvorschaudiv').css('top', pos.top + 25);
          $('#pdfvorschaudiv').css('left', pos.left > 900? pos.left - 900.0:pos.left);
        },
        beforeSend: function() {

        }
    });
  }*/
/*
  function pdfvorschaugutschrift(el, element)
  {
    var pos = $(element).position();
    if(pdfinterval != null) {
      clearTimeout(pdfinterval);
    }
    $.ajax({
      url: 'index.php?module=zahlungsverkehr&action=ueberweisung&cmd=pdfvorschaugutschrift&aktion=gutschrift&parameter='+ el ,
      type: 'POST',
      dataType: 'json',
      data: {},
      success: function(data) {
        $('#pdfiframe').prop('src',data.src);
        $('#pdfvorschaudiv').show();
        $('#pdfvorschaudiv').css('top', pos.top + 25);
        $('#pdfvorschaudiv').css('left', pos.left > 900? pos.left - 900.0:pos.left);
      },
      beforeSend: function() {

      }
    });
  }
*/
  /*
	function DeleteUeberweisungDialog(id, type){
        type = typeof type == 'undefinded'?'1':type;
        if (confirm('Wollen Sie den Eintrag wirklich löschen?')) {
	        $.ajax({
		        url: 'index.php?module=zahlungsverkehr&action=editUeberweisung&cmd=delete',
		        data: {
			        editid: id, type:type
		        },
		        method: 'post',
		        dataType: 'json',
		        success: function (data) {
			        updateLiveTable();
			        $("#editUeberweisung").dialog('close');
			        editUeberweisungReset();
		        }
	        });
        }
    }*/
    /*
    function EditUeberweisungDialog(id, type){
      type = typeof type == 'undefinded'?'1':type;
	    $.ajax({
		    url: 'index.php?module=zahlungsverkehr&action=editUeberweisung&cmd=get',
		    data: {
			    editid: id, type:type
		    },
		    method: 'post',
		    dataType: 'json',
		    success: function (data) {
		      if(typeof data.type != 'undefined' && data.type === 'returnorder') {
            $("#editReturnOrder div#editReturnOrderContent").html('');
            $('input#save').val('');
            $('#payment_transaction_address').val(data.adresse);
            if(typeof data.html != 'undefined') {
              $("#editReturnOrder div#editReturnOrderContent").html(data.html);
              $("#editReturnOrder div#editReturnOrderContent input.datepicker").datepicker(
                      { dateFormat: 'dd.mm.yy',dayNamesMin: ['SO', 'MO', 'DI', 'MI', 'DO', 'FR', 'SA'], firstDay:1,
                showWeek: true, monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai',
                  'Juni', 'Juli', 'August', 'September', 'Oktober',  'November', 'Dezember'], }
                  );
              $("#editReturnOrder div#editReturnOrderContent input.timeicker").timepicker();
            }
            if(typeof data.id != 'undefined') {
              $('#payment_transaction_id').val(data.id);
            }
            $("#editReturnOrder").dialog('open');
		        return;
          }
				editUeberweisungReset();
				// befüllen
                $('#entryid').val(id);
                $('#adresse').val(data.adresse);
                $('#empfaenger').val(data.name);
                $('#iban').val(data.konto);
                $('#bic').val(data.blz);
                $('#betrag').val(data.betrag);
                $('#waehrung').val(data.waehrung);
                $('#vz1').val(data.vz1);
                $('#vz2').val(data.vz2);
                $('#datumueberweisung').val(data.datum);
                $("#editUeberweisung").dialog('open');
		    }
	    });
    }


    function editUeberweisung(id){
	    $('#editUeberweisungForm').find('#entryid').val(id);
	    $("#editUeberweisung").dialog('open');
    }

    function editReturnOrderSave()
    {
      $('input#save').val('1');
      $.ajax({
        url: 'index.php?module=zahlungsverkehr&action=editUeberweisung&cmd=savereturnorder',
        data: $('#editReturnOrderForm').serialize(),
        method: 'post',
        dataType: 'json',
        success: function (data) {
          $('#editReturnOrder').dialog('close');
        }
      });
    }*/
/*
	function editUeberweisungSave(){
			$.ajax({
				url: 'index.php?module=zahlungsverkehr&action=editUeberweisung&cmd=save',
				data: {
				  editid: $('#entryid').val(),
					adresse: $('#adresseid').val(),
          zahlungsempfadr: $('#adresse').val(),
					datumueberweisung: $('#datumueberweisung').val(),
					name: $('#empfaenger').val(),
					konto: $('#iban').val(),
					blz: $('#bic').val(),
					betrag: $('#betrag').val(),
					waehrung: $('#waehrung').val(),
					vz1: $('#vz1').val(),
					vz2: $('#vz2').val()
				},
				method: 'post',
				dataType: 'json',
				success: function (data) {
					updateLiveTable();
          $("#editUeberweisung").dialog('close');
					editUeberweisungReset();
				}
			});
	}*/
/*
    function editUeberweisungReset(){
	    $('#editUeberweisungForm').find('#entryid').val('');
	    $('#editUeberweisungForm').find('#adresseid').val('');
	    $('#editUeberweisungForm').find('#adresse').val('');
	    $('#editUeberweisungForm').find('#empfaenger').val('');
	    $('#editUeberweisungForm').find('#iban').val('');
	    $('#editUeberweisungForm').find('#bic').val('');
	    $('#editUeberweisungForm').find('#betrag').val('');
	    $('#editUeberweisungForm').find('#waehrung').val('');
	    $('#editUeberweisungForm').find('#vz1').val('');
	    $('#editUeberweisungForm').find('#vz2').val('');
	    $('#editUeberweisungForm').find('#datumueberweisung').val('');
	    $('#editUeberweisungForm').find('#absender').val('');
	    $('#editUeberweisungForm').find('#iban_absender').val('');
    }
*/


/*
    function updateLiveTable(i) {
        var oTableL = $('#ueberweisung').dataTable();
        var tmp = $('.dataTables_filter input[type=search]').val();
        oTableL.fnFilter('%');
        //oTableL.fnFilter('');
        oTableL.fnFilter(tmp);

    }*/


</script>

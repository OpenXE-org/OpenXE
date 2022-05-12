<link rel="stylesheet" type="text/css" href="css/jquery.timeline.css?v=3"/>
<script src="js/jquery.timeline.js?v=3"></script>
[DATEIENPOPUP]
<div id="editAufgaben" style="display:none;" title="Bearbeiten">
  <form method="post">
    <input type="hidden" id="e_id">
    <div class="row">
      <div class="row-height">
        <div class="col-xs-12 col-md-6 col-md-height">
          <div class="inside inside-full-height">
            <fieldset>
              <legend>{|Allgemein|}</legend>
              <table width="100%">
                <tr>
                  <td width="200">{|Aufgabe|}:</td>
                  <td colspan="5"><input type="text" name="e_aufgabe" id="e_aufgabe" size="60"></td>
                </tr>
                <tr>
                  <td>{|Mitarbeiter|}:</td>
                  <td colspan="5"><input type="text" name="e_mitarbeiter" id="e_mitarbeiter" size="60"></td>
                </tr>
                <tr>
                  <td>{|f&uuml;r Kunde|}:</td>
                  <td colspan="5"><input type="text" name="e_kunde" id="e_kunde" size="60"><a href="" name="kundenbutton" id="kundenbutton" target="_blank"><img src="./themes/[THEME]/images/forward.svg" class="textfeld_icon"></a></td>
                </tr>
                <tr>
                  <td>{|Ansprechpartner|}:</td>
                  <td colspan="5"><input type="text" name="e_ansprechpartner" id="e_ansprechpartner" size="60"><a href="" name="ansprechpartnerbutton" id="ansprechpartnerbutton" target="_blank"><img src="./themes/[THEME]/images/forward.svg" class="textfeld_icon"></a></td>
                </tr>
                <tr>
                  <td>{|Beschreibung|}:<br />{|(Optional Text auf Pinnwand)|}</td>
                  <td colspan="5"><textarea rows="10" cols="10" name="e_beschreibung" id="e_beschreibung"></textarea></td>
                </tr>
                <tr>
                  <td>{|Projekt|}:</td>
                  <td colspan="5"><input type="text" name="e_projekt" id="e_projekt" size="55"><a href="" name="projektbutton" id="projektbutton" target="_blank"><img src="./themes/[THEME]/images/forward.svg" class="textfeld_icon"></a></td>
                </tr>
                <tr>
                  <td>{|Teilprojekt|}:</td>
                  <td colspan="5"><input type="text" name="e_teilprojekt" id="e_teilprojekt" size="55"></td>
                </tr>

                <tr id="editlabelrow">
                  <td width="">{|Labels|}:</td>
                  <td>
                    <span id="editlabelcontainer" data-label-trigger="#editlabeltrigger"></span>
                    <a href="#" id="editlabeltrigger">{|Labels zuweisen|}</a>
                  </td>
                  <td>{|Status|}:</td>
                  <td colspan="2">
                    <select name="e_status" id="e_status">
                      <option value="offen">offen</option>
                      <option value="inbearbeitung">in Bearbeitung</option>
                      <option value="abgeschlossen">abgeschlossen</option>
                    </select>
                  </td>
                </tr>
                <tr>
                  <td width="200">{|Prio|}:</td>
                  <td width="165">
                      <select name="e_prio" id="e_prio">
                        <option value="-1">Niedrig</option>
                        <option value="0">Mittel</option>
                        <option value="1">Hoch</option>
                      </select>
                  </td>
                  <td width="120">{|Geplante Dauer in h|}:</td>
                  <td colspan="3"><input type="text" name="e_dauer" id="e_dauer" size="17"></td>
                </tr>
                <tr>
                  <td width="200">{|Datum / Abgabe bis|}:</td>
                  <td><input type="text" name="e_datum" id="e_datum" size="12"></td>
                  <td width="120">{|Uhrzeit|}:</td>
                  <td colspan="3"><input type="text" name="e_zeit" id="e_zeit" size="17"></td>
                </tr>
                <tr>
                  <td width="200">{|Regelm&auml;&szlig;ig (Intervall)|}:</td>
                  <td width="120">
                    <select name="e_intervall" id="e_intervall">
                      <option value="0">einmalig</option>
                      <option value="1">t&auml;glich</option>
                      <option value="2">w&ouml;chentlich</option>
                      <option value="3">monatlich</option>
                      <option value="4">j&auml;hrlich</option>
                    </select>
                  </td>
                  <td>{|Zeiterfassung Pflicht|}:</td>
                  <td><input type="checkbox" name="e_pflicht" id="e_pflicht"></td>
                  <td>{|Zeit wird abgerechnet|}:</td>
                  <td><input type="checkbox" name="e_abgerechnet" id="e_abgerechnet"></td>
                </tr>
              </table>
            </fieldset>
          </div>
        </div>
        <div class="col-xs-12 col-md-6 col-md-height">
          <div class="inside inside-full-height">
            <div id="tasks-tabs">
              <ul>
                <li data-type="history"><a href="#tasks-history-tab">{|Verlauf|}</a></li>
                <li data-type="settings"><a href="#tasks-settings-tab">{|Einstellungen|}</a></li>
              </ul>

              <div id="tasks-history-tab">
                <fieldset>
                  <legend>{|Verlauf|}</legend>
                  <div id="scroll" style="max-height:558px; overflow: auto;padding-right:10px;">
                    <div id="element"></div>
                  </div>
                </fieldset>

              </div>
              <div id="tasks-settings-tab">
              <fieldset>
                <legend>{|Einstellungen|}</legend>
                <table>
                  <tr>
                    <td nowrap><label for="e_mailerinnerung">{|E-Mail Erinnerung|}:</label></td>
                    <td colspan="5"><input type="checkbox" name="e_mailerinnerung" id="e_mailerinnerung"></td>
                  </tr>
                  <tr>
                    <td nowrap><label for="e_anzahltage">{|E-Mail Anzahl Tage zuvor|}:</label></td>
                    <td colspan="5"><input type="text" name="e_anzahltage" id="e_anzahltage">&nbsp;(in Tagen)</td>
                  </tr>
                  <tr>
                    <td nowrap><label for="e_countdown">{|Countdown auf Startseite|}:</label></td>
                    <td colspan="5"><input type="text" name="e_countdown" id="e_countdown">&nbsp;(in Tagen)</td>
                  </tr>
                  <tr>
                    <td nowrap><label for="e_oeffentlich">{|&Ouml;ffentlich|}:</label></td>
                    <td colspan="5"><input type="checkbox" name="e_oeffentlich" id="e_oeffentlich"></td>
                  </tr>
                  <tr>
                    <td nowrap><label for="e_startseite">{|Auf Startseite|}:</label></td>
                    <td colspan="5"><input type="checkbox" name="e_startseite" id="e_startseite"></td>
                  </tr>
                  <tr>
                    <td nowrap><label for="e_aufpinwand">{|Auf Pinnwand|}:</label></td>
                    <td width="80"><input type="checkbox" name="e_aufpinwand" id="e_aufpinwand"></td>
                    <td><label for="e_farbe">{|Farbe|}:</label></td>
                    <td width="90">
                      <select name="e_farbe" id="e_farbe">
                        <option value="yellow">Gelb</option>
                        <option value="blue">Blau</option>
                        <option value="green">Gr&uuml;n</option>
                        <option value="coral">Rosa</option>
                      </select>
                    </td>
                    <td>{|Pinnwand|}:</td>
                    <td>
                      <select name="e_pinwand" id="e_pinwand">
                        <!--<option value="0">Eigene Pinnwand</option>-->
                        [PINNWAND]
                      </select>
                    </td>
                  </tr>
                </table>
              </fieldset>
              <fieldset id="e_abgeschlossentext">
                <legend>{|Status|}</legend>
                <table>
                  <tr>
                    <td>
                      <label for="e_abgeschlossentext2">{|Text f&uuml;r E-Mail Benachrichtigung|}<br />{|an Mitarbeiter|}:</label>
                    </td>
                    <td><textarea rows="10" cols="58" id="e_abgeschlossentext2"></textarea></td>
                  </tr>
                </table>
              </fieldset>
              <fieldset>
                <legend><label for="e_notizen">{|Notizen|}</label></legend>
                <textarea rows="10" cols="89" name="e_notizen" id="e_notizen"></textarea>
              </fieldset>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

<script type="text/javascript">

$(document).ready(function() {
  $('#e_aufgabe').focus();

  $("input#e_teilprojekt").autocomplete({
    source: "index.php?module=ajax&action=filter&filtername=arbeitspaket&projekt="+0,
  });

  $("input#e_projekt").autocomplete({
    source: "index.php?module=ajax&action=filter&filtername=projektname", 
    select: function( event, ui ) { 
      if(ui.item){
        $("input#e_teilprojekt").autocomplete({
          source: "index.php?module=ajax&action=filter&filtername=arbeitspaket&projekt="+ui.item.value,
        });
      } 
    }  
  });

  $("input#e_ansprechpartner").autocomplete({
    source: "index.php?module=ajax&action=filter&filtername=ansprechpartneradresse&adresse="+0,
  });

  $("input#e_kunde").autocomplete({
    source: "index.php?module=ajax&action=filter&filtername=adresse",
    select: function( event, ui ) {
      if(ui.item){
        $("input#e_ansprechpartner").autocomplete({
          source: "index.php?module=ajax&action=filter&filtername=ansprechpartneradresse&adresse="+ui.item.value,
        });
      }
    }
  });


  statusbox = document.getElementById('e_status');
  abgeschlossentext = document.getElementById('e_abgeschlossentext');
        
  if (statusbox) {
    // Hide the target field if priority isn't critical
    if (statusbox.options[statusbox.selectedIndex].value == 'offen' || statusbox.options[statusbox.selectedIndex].value == 'inbearbeitung') {
      abgeschlossentext.style.display='none';
    }    
    if (statusbox.options[statusbox.selectedIndex].value =='abgeschlossen') {
      abgeschlossentext.style.display='';
    }

    statusbox.onchange=function() {
      if (statusbox.options[statusbox.selectedIndex].value == 'offen' || statusbox.options[statusbox.selectedIndex].value == 'inbearbeitung') {             
        abgeschlossentext.style.display='none';
      } else if(statusbox.options[statusbox.selectedIndex].value == 'abgeschlossen') {
        abgeschlossentext.style.display='';
      }
    }
  }



  $("#editAufgaben").dialog({
    modal: true,
    bgiframe: true,
    closeOnEscape:false,
    minWidth:1450,
    maxHeight:800,
    autoOpen: false,
    buttons: {
      [DATEIBUTTON]
      
      'PINNWAND BONDRUCK': function(){
        AufgabenBon();
      },
      'BENACHRICHTIGUNGSMAIL SENDEN': function(){
        AufgabenMail();
      },
      ABBRECHEN: function() {
        AufgabenReset();
        $(this).dialog('close');
      },
      SPEICHERN: function() {
        AufgabenEditSave();
      }
    },
    open: function(event, ui){
      if(auxid > 0){
        AufgabenEdit(auxid);
        auxid = 0;
      }
    }
  });

  $("#editAufgaben").dialog({
    close: function( event, ui ) { AufgabenReset();}
  });
  $('#e_status').on('change', function(){
    $.ajax({
      url: 'index.php?module=aufgaben&action=edit&cmd=changestatus',
      data: {
        task_id: $('#editAufgaben').find('#e_id').val(),
        status: $(this).val()
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if(typeof data.timeline !== 'undefined') {
          Task.addTimeline(data.timeline);
        }
      }
    });
  });
});


function AufgabenReset()
{
  $('#editAufgaben').find('#e_id').val('');
  $('#editAufgaben').find('#e_aufgabe').val('');
  $('#editAufgaben').find('#e_mitarbeiter').val('');
  $('#editAufgaben').find('#e_kunde').val('');
  $('#editAufgaben').find('#e_ansprechpartner').val('');
  $('#editAufgaben').find('#e_beschreibung').val('');
  $('#editAufgaben').find('#e_projekt').val('');
  $('#editAufgaben').find('#e_teilprojekt').val('');
  $('#editAufgaben').find('#e_prio').val(0);
  $('#editAufgaben').find('#e_dauer').val('');
  $('#editAufgaben').find('#e_datum').val('');
  $('#editAufgaben').find('#e_zeit').val('');
  $('#editAufgaben').find('#e_intervall').val(0);
  $('#editAufgaben').find('#e_pflicht').prop("checked",false);
  $('#editAufgaben').find('#e_abgerechnet').prop("checked",false);
  $('#editAufgaben').find('#e_mailerinnerung').prop("checked",false);
  $('#editAufgaben').find('#e_anzahltage').val('');
  $('#editAufgaben').find('#e_countdown').val('');
  $('#editAufgaben').find('#e_oeffentlich').prop("checked",false);
  $('#editAufgaben').find('#e_startseite').prop("checked",false);
  $('#editAufgaben').find('#e_aufpinwand').prop("checked",false);
  $('#editAufgaben').find('#e_farbe').val('yellow');
  $('#editAufgaben').find('#e_pinwand').val(0);
  $('#editAufgaben').find('#e_status').val('offen');
  $('#editAufgaben').find('#e_notizen').val('');
  $('#editAufgaben').find('#e_abgeschlossentext2').val('');
  $('#element').html('');
  $('#newtimeline').val();
  kundenbutton.style.display='none';
  ansprechpartnerbutton.style.display='none';
  projektbutton.style.display='none';

  statusbox2 = document.getElementById('e_status');
  abgeschlossentext = document.getElementById('e_abgeschlossentext');
  if (statusbox2.options[statusbox2.selectedIndex].value == 'offen' || statusbox2.options[statusbox2.selectedIndex].value == 'inbearbeitung') {
    abgeschlossentext.style.display='none';
  }

  if (statusbox2.options[statusbox2.selectedIndex].value =='abgeschlossen') {
    abgeschlossentext.style.display='';
  }

  $("input#e_ansprechpartner").autocomplete({
    source: "index.php?module=ajax&action=filter&filtername=ansprechpartneradresse&adresse="+0,
  });
  
}

function AufgabenBon(){
  //var conf = confirm('Benachrichtigung per Mail senden? Wurde zuvor die Aufgabe gespeichert?');
  //if (conf) {
    $.ajax({
      url: 'index.php?module=aufgaben&action=bon&id='+$('#e_id').val(),
      data: {
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if(data.status == 1){
          AjaxCall("index.php?module=aufgaben&action=bondrucker&id="+$('#e_id').val());
        }else{
          InfoBox("aufgabe_bondrucker");
        }
        App.loading.close();
      }
    });
  //}

  return false;
}

function AufgabenMail(){
  var conf = confirm('Benachrichtigung per Mail senden? Wurde zuvor die Aufgabe gespeichert?');
  if (conf) {
    $.ajax({
      url: 'index.php?module=aufgaben&action=mail&id='+$('#e_id').val(),
      data: {
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        if (data.status == 1) {
          alert(data.statusText);
        } else {
          alert(data.statusText);
        }
        App.loading.close();
      }
    });
  }

  return false;
}

function AufgabenEditSave() {
  $.ajax({
    url: 'index.php?module=aufgaben&action=edit&cmd=save',
    data: {
      //Alle Felder die fürs editieren vorhanden sind
      id: $('#e_id').val(),
      aufgabe: $('#e_aufgabe').val(),
      mitarbeiter: $('#e_mitarbeiter').val(),
      kunde: $('#e_kunde').val(),
      ansprechpartner: $('#e_ansprechpartner').val(),
      beschreibung: $('#e_beschreibung').val(),
      projekt: $('#e_projekt').val(),
      teilprojekt: $('#e_teilprojekt').val(),
      prio: $('#e_prio').val(),
      dauer: $('#e_dauer').val(),
      datum: $('#e_datum').val(),
      zeit: $('#e_zeit').val(),
      intervall: $('#e_intervall').val(),
      pflicht: $('#e_pflicht').prop("checked")?1:0,
      abgerechnet: $('#e_abgerechnet').prop("checked")?1:0,
      mailerinnerung: $('#e_mailerinnerung').prop("checked")?1:0,
      anzahltage: $('#e_anzahltage').val(),
      countdown: $('#e_countdown').val(),
      oeffentlich: $('#e_oeffentlich').prop("checked")?1:0,
      startseite: $('#e_startseite').prop("checked")?1:0,
      aufpinwand: $('#e_aufpinwand').prop("checked")?1:0,
      farbe: $('#e_farbe').val(),
      pinwand: $('#e_pinwand').val(),
      status: $('#e_status').val(),
      notizen: $('#e_notizen').val(),
      abgeschlossentext: $('#e_abgeschlossentext2').val()
                      
    },
    method: 'post',
    dataType: 'json',
    beforeSend: function() {
      App.loading.open();
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {
        AufgabenReset();
        $("#editAufgaben").dialog('close');
        updateLiveTable();
        $('#calendar').fullCalendar('refetchEvents');
      } else {
        alert(data.statusText);
      }
    }
  });
}

function AufgabenEdit(id, projekt) {
  $('#kundenbutton').hide();
  $('#ansprechpartnerbutton').hide();
  $('#projektbutton').hide();

  if(id > 0)
  { 
    auxid = 0;
    $.ajax({
      url: 'index.php?module=aufgaben&action=edit&cmd=get',
      data: {
        id: id
      },
      method: 'post',
      dataType: 'json',
      beforeSend: function() {
        App.loading.open();
      },
      success: function(data) {
        $('#editAufgaben').find('#e_id').val(data.id);
        $('#editAufgaben').find('#e_aufgabe').val(data.aufgabe);
        $('#editAufgaben').find('#e_mitarbeiter').val(data.adresse);
        $('#editAufgaben').find('#e_kunde').val(data.kunde);
        $('#editAufgaben').find('#e_ansprechpartner').val(data.ansprechpartner);
        $('#editAufgaben').find('#e_beschreibung').val(data.beschreibung);
        $('#editAufgaben').find('#e_projekt').val(data.projekt);
        $('#editAufgaben').find('#e_teilprojekt').val(data.teilprojekt);
        $('#editAufgaben').find('#e_prio').val(data.prio);
        $('#editAufgaben').find('#e_dauer').val(data.stunden);
        $('#editAufgaben').find('#e_datum').val(data.abgabe_bis);
        $('#editAufgaben').find('#e_zeit').val(data.abgabe_bis_zeit);
        $('#editAufgaben').find('#e_intervall').val(data.intervall_tage);
        $('#editAufgaben').find('#e_pflicht').prop("checked", data.zeiterfassung_pflicht==1?true:false);
        $('#editAufgaben').find('#e_abgerechnet').prop("checked", data.zeiterfassung_abrechnung==1?true:false);
        $('#editAufgaben').find('#e_mailerinnerung').prop("checked", data.emailerinnerung==1?true:false);
        $('#editAufgaben').find('#e_anzahltage').val(data.emailerinnerung_tage);
        $('#editAufgaben').find('#e_countdown').val(data.vorankuendigung);
        $('#editAufgaben').find('#e_oeffentlich').prop("checked", data.oeffentlich==1?true:false);
        $('#editAufgaben').find('#e_startseite').prop("checked", data.startseite==1?true:false);
        $('#editAufgaben').find('#e_aufpinwand').prop("checked", data.pinwand==1?true:false);
        $('#editAufgaben').find('#e_farbe').val(data.note_color);
        $('#editAufgaben').find('#e_pinwand').val(data.pinwand_id);
        $('#editAufgaben').find('#e_status').val(data.status);
        $('#editAufgaben').find('#e_notizen').val(data.sonstiges);
        $('#editAufgaben').find('#e_abgeschlossentext2').val(data.abgeschlossentext);
                
        if (data.kundenbutton == '1' && data.kundenid > 0) {
          kundenbutton.style.display='';
          $("a#kundenbutton").attr('href', 'index.php?module=adresse&action=edit&id='+data.kundenid);
        }
        if (data.ansprechpartnerbutton == '1' && data.ansprechpartnerid > 0) {
          ansprechpartnerbutton.style.display = '';
          $("a#ansprechpartnerbutton").attr('href', 'index.php?module=adresse&action=ansprechpartner&id='+data.kundenid);
        }
        if (data.projektbutton == '1' && data.projektid > 0) {
          projektbutton.style.display='';
          $("a#projektbutton").attr('href', 'index.php?module=projekt&action=dashboard&id='+data.projektid);
        }

        abgeschlossentext = document.getElementById('e_abgeschlossentext');
        
        if (data.status == 'offen' || data.status == 'inbearbeitung') {
          abgeschlossentext.style.display='none';
        }
        if (data.status == 'abgeschlossen') {
          abgeschlossentext.style.display='';
        }

        $("input#e_ansprechpartner").autocomplete({
          source: "index.php?module=ajax&action=filter&filtername=ansprechpartneradresse&adresse="+data.kundenid,
        });


        App.loading.close();

        [AFTERPOPUPOPEN]

        if (!isNaN(data.id) || data.id > 0) {
          $('#editAufgaben').find('#editlabelrow').show();
          $('#editlabelcontainer').labels({
            referenceTable: 'aufgabe',
            referenceId: data.id
          });
        }
        // Wiedervorlage anlegen > Labels ausblenden
        if (isNaN(data.id) || data.id === 0) {
          $('#editAufgaben').find('#editlabelrow').hide();
        }


        Task.addTimeline(data.timeline);

        $("#editAufgaben").dialog('open');
      }
    });
  } else {
    AufgabenReset();
    if(typeof projekt != 'undefined')
    {

      $('#editAufgaben').find('#e_projekt').val(projekt);
    }
    Task.addTimeline(null);
    $("#editAufgaben").dialog('open');
  }

}

function updateLiveTable(i) {
  var oTableL = $('#aufgaben_meine').dataTable();
  var tmp = $('.dataTables_filter input[type=search]').val();
  oTableL.fnFilter('%');
  oTableL.fnFilter(tmp);

  var $projectTasks = $('#projekt_aufgaben');
  if ($projectTasks.length > 0) {
    var projectTaskTable = $projectTasks.dataTable();
    tmp = $('.dataTables_filter input[type=search]').val();
    projectTaskTable.fnFilter('%');
    projectTaskTable.fnFilter(tmp);
  }
}



</script>




<style>
.drag_drop_relative {
  position: relative !important;
}
</style>

<script type="text/javascript">
$(document).ready(function() { 

  buildDragDrop();

});

function buildDragDrop() {

    $('.drag_drop_aufgabe').draggable({ 
      connectToSortable: 'ul.drag_drop_list',
      scroll:true,
      revert: 'invalid',
      start: function() {
        $(this).data("startingScrollTop",$(this).parent().scrollTop());
      },
      drag: function(event,ui){
        var st = parseInt($(this).data("startingScrollTop"));
        ui.position.top -= $(this).parent().scrollTop() - st;
      }
    });


    $('ul.drag_drop_list').sortable({
      connectWith: ".drag_drop_aufgabe",
      revert: 1, 
      placeholder: "ui-state-highlight",
      stop: function (event, ui) {

        var sortIds = [];
        $(this).find('li').each(function() {
          if ( typeof($(this).attr('data-id')) != 'undefined' ) {
            sortIds.push( $(this).attr('data-id') );
          }
        });

        var aufgabeId = ui.item.attr('data-id');
        var aufgabeDatum = ui.item.parent().parent().attr('data-datum');

        $.ajax({
          url: 'index.php?module=aufgaben&action=dragdropaufgabe',
          method: 'POST',
          dataType: 'json',
          data: {
            aufgabeId: aufgabeId,
            aufgabeDatum: aufgabeDatum
          },
          success: function(data) {
            App.loading.close();
            if (data.status == 1) {
              sortAufgaben(sortIds);
            } else {
              alert(data.statusText);
            }

            $('ul.drag_drop_list').sortable( 'destroy' );

            buildDragDrop();
          },
          beforeSend: function() {
            App.loading.open();
          }
        });

      }
    });
  }

function sortAufgaben(idList) {

  $.ajax({
    url: 'index.php?module=aufgaben&action=sortaufgabe',
    method: 'POST',
    dataType: 'json',
    data: {
      idList: idList
    },
    success: function(data) {
      App.loading.close();
      if (data.status == 1) {

      } else {
        alert(data.statusText);
      }

    },
    beforeSend: function() {
      App.loading.open();
    }
  });
}
</script>

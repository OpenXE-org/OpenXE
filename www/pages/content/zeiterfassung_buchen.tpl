<!-- gehort zu tabview -->
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<!-- gehort zu tabview -->
<div id="tabs">
<ul>
[ZEITERFASSUNGTABS]
</ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
[TAB1]
[TAB1NEXT]
</div>

<div id="tabs-2">

<div class="row">
<div class="row-height">
<div class="col-xs-12 col-md-10 col-md-height">
<div class="inside_white inside-full-height">
<fieldset class="white"><legend>&nbsp;</legend>
<div id='calendar'></div>
</fieldset>
</div>
</div>
<div class="col-xs-12 col-md-2 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Übersicht|}</legend>
<input type="checkbox" name="zeiterfassung_buchen_termine" id="zeiterfassung_buchen_termine" value="1" [CHECKEDZEITERFASSUNGBUCHENTERMINE]>&nbsp;{|Termine aus Kalender|}
<br><input type="checkbox" name="zeiterfassung_buchen_stechuhr" id="zeiterfassung_buchen_stechuhr" value="1" [CHECKEDZEITERFASSUNGBUCHENSTECHUHR]>&nbsp;{|Stechuhr Zeiten einblenden|}
</fieldset>

<fieldset><legend>{|Aktionen|}</legend>
<input type="button" value="{|Neue Zeit buchen|}" style="width:100%" class="btnGreenNew" onclick="OpenMode($.format.date(jQuery.now(), 'yyyy-MM-dd HH:mm:ss'),$.format.date(jQuery.now(), 'yyyy-MM-dd HH:mm:ss'))"><br>
[FORMULARANSICHT]
</fieldset>

</div>
</div>
</div>
</div>

<script type='text/javascript' src='./js/jquery.dateFormat-1.0.js'></script>
<script type='text/javascript' src='./plugins/fullcalendar-1.6.7/fullcalendar.min.js?v=1'></script>
<script type='text/javascript' src='./js/nocie.js'></script>

<link rel='stylesheet' type='text/css' href='./plugins/fullcalendar-1.6.7/fullcalendar.css' />
<link rel='stylesheet' type='text/css' href='./plugins/fullcalendar-1.6.7/fullcalendar.print.css' media='print' />



<script type='text/javascript'>
var projektname = 'hh';
$(document).ready(function() {


	$("#aufgabe").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=zeiterfassungvorlage",
		select: function( event, ui ) {
            $.ajax({
                url: 'index.php?module=ajax&action=filter&filtername=zeiterfassungvorlagedetail',
                data: {
                    vorlage: ui.item.label
                },
                method: 'post',
                dataType: 'json',
                success: function(data) {
                    $("#beschreibung").val(data[0]);
                    $("#art").val(data[1]);
                    $("#projekt_manuell").val(data[2]);
                    $("#arbeitspaket").val(data[3]);
                    if(data[4]==0){
                        data[4] = '';
                    }
                    $("#adresse_abrechnung").val(data[4]);
                    $("#abrechnen").prop("checked", data[5]==1?true:false);
                }
            });
		}
	});
	$("#aufgabe2").autocomplete({
		source: "index.php?module=ajax&action=filter&filtername=zeiterfassungvorlage",
        select: function( event, ui ) {
            $.ajax({
                url: 'index.php?module=ajax&action=filter&filtername=zeiterfassungvorlagedetail',
                data: {
                    vorlage: ui.item.label
                },
                method: 'post',
                dataType: 'json',
                success: function(data) {
                    $("#beschreibung2").val(data[0]);
                    $("#art2").val(data[1]);
                    $("#projekt_manuell2").val(data[2]);
                    $("#arbeitspaket2").val(data[3]);
                    if(data[4]==0){
                        data[4] = '';
                    }
                    $("#adresse_abrechnung2").val(data[4]);
                    $("#abrechnen2").prop("checked", data[5]==1?true:false);
                }
            });
        }
	});


    $("#projekt_manuell").autocomplete({
        source: "index.php?module=ajax&action=filter&filtername=projektname",
        select: function( event, ui ) {
            $.ajax({
                url: 'index.php?module=ajax&action=filter&filtername=zeiterfassungprojektdetail',
                data: {
                    projekt: ui.item.label
                },
                method: 'post',
                dataType: 'json',
                success: function(data) {
                    if(data[0]){
                        $("#adresse_abrechnung").val(data[0]);
                    }
                }
            });
        }
    });
    $("#projekt_manuell2").autocomplete({
        source: "index.php?module=ajax&action=filter&filtername=projektname",
        select: function( event, ui ) {
            $.ajax({
                url: 'index.php?module=ajax&action=filter&filtername=zeiterfassungprojektdetail',
                data: {
                    projekt: ui.item.label
                },
                method: 'post',
                dataType: 'json',
                success: function(data) {
                    if(data[0]){
                        $("#adresse_abrechnung2").val(data[0]);
                    }
                }
            });
        }
    });



$( "#art2" ).change(function() {
    if( this.value !='Arbeit')
      $('#aufgabe2').val(this.value);
    else 
      $('#aufgabe2').val('');
      $('#beschreibung2').val('');
  
});


    $("#list").on("click", function() {
      $('#calendar').fullCalendar('changeView','month');
      $('#calendar').fullCalendar('changeView','agendaWeek');
    });


    $("#editZeiterfassung").dialog({
      autoOpen: false,
      height: 750,
      width: 650,
      modal: true,
      buttons: {
        "Speichern": function() {
          var errMsg = '';

          if($('#editZeiterfassung').find('#datum').val()=="") errMsg = "Geben Sie bitte ein Datum ein."
          if($('#editZeiterfassung').find('#aufgabe2').val()=="") errMsg = "Geben Sie bitte eine Aufgabe ein.";
          if($('#editZeiterfassung').find('#vonZeit').val()=="") errMsg = "Geben Sie bitte Von-Zeit ein.";
          if($('#editZeiterfassung').find('#bisZeit').val()=="") errMsg = "Geben Sie bitte Bis-Zeit ein.";

          if(errMsg!="")
            $("#submitError").html('<div class="error">'+errMsg+'</div>');	
          else{
            //$('#TerminForm').submit();
            ZeiterfassungSave();
          }
        },
/*
   "Kopieren": function() {
   $("#mode").val("copy");
   $('#TerminForm').submit();
   },
   "Löschen": function() {
   if(confirm("Soll der Termin wirklich gelöscht werden?"))
   {
   $("#mode").val("delete");
   $('#TerminForm').submit();
   }
   },*/
  "Löschen": function() {
        if(confirm("Soll dieser Eintrag wirklich gelöscht werden?"))
        {
          $.ajax({
            url: 'index.php?module=zeiterfassung&action=create&cmd=delzeiterfassung',
            data: {
            //Alle Felder die fürs editieren vorhanden sind
              id: $('#editZeiterfassung').find('#eventid').val()
            },
            method: 'post',
            dataType: 'json',
            success: function(data) {
            if (data.status == 1) {
                $("#editZeiterfassung").dialog('close');
                $('#calendar').fullCalendar('refetchEvents');
            } else {
                alert(data.statusText);
            }
        }
    });

          $(this).dialog("close");
        }
        },
	"Kopieren": function() {
    	if(confirm("Soll dieser Eintrag wirklich kopiert werden?")) {
        	$.ajax({
            	url: 'index.php?module=zeiterfassung&action=create&cmd=copyzeiterfassung',
            	data: {
                	id: $('#editZeiterfassung').find('#eventid').val()
            	},
            	method: 'post',
            	dataType: 'json',
            	success: function (data) {
                	if (data.status == 1) {
                    	$("#editZeiterfassung").dialog('close');
                    	$('#calendar').fullCalendar('refetchEvents');
                	} else {
                    	alert(data.statusText);
                	}
            	}
        	});
        	$(this).dialog("close");
    	}
  },

        "Abbrechen": function() {
          $(this).dialog("close");
        }
          },
      close: function() {
          ResetMode();
       }
});
var date = new Date();
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();
$('#calendar').fullCalendar({
  theme: true,
  defaultView: 'agendaWeek',
  header: {
    left: 'prev,next today tasks',
    center: 'title',
    right: 'month,agendaWeek,agendaDay'
  },
  allDayText: '{|Ganzt&auml;gig|}',
  firstDay: 1,
  dayNamesShort: ['{|Sonntag|}', '{|Montag|}', '{|Dienstag|}', '{|Mittwoch|}', '{|Donnerstag|}', '{|Freitag|}', '{|Samstag|}'],
  dayNames: ['{|Sonntag|}', '{|Montag|}', '{|Dienstag|}', '{|Mittwoch|}', '{|Donnerstag|}', '{|Freitag|}', '{|Samstag|}'],
  monthNames: ['{|Januar|}', '{|Februar|}', '{|März|}', '{|April|}', '{|Mai|}',
    '{|Juni|}', '{|Juli|}', '{|August|}', '{|September|}', '{|Oktober|}',  '{|November|}', '{|Dezember|}'],
  monthNamesShort: ['{|Januar|}', '{|Februar|}', '{|März|}', '{|April|}', '{|Mai|}',
    '{|Juni|}', '{|Juli|}', '{|August|}', '{|September|}', '{|Oktober|}',  '{|November|}', '{|Dezember|}'],
  timeFormat: 'H:mm',
  buttonText: {
    prev: "<span class='fc-text-arrow'>&lsaquo;</span>",
    next: "<span class='fc-text-arrow'>&rsaquo;</span>",
    prevYear: "<span class='fc-text-arrow'>&laquo;</span>",
    nextYear: "<span class='fc-text-arrow'>&raquo;</span>",
    today: '{|Heute|}',
    month: '{|Monat|}',
    week: '{|Woche|}',
    day: '{|Tag|}'
  },
  axisFormat: 'HH:mm',
  columnFormat: {
    month: 'ddd',
    week: 'ddd d.M',
    day: 'dddd d.M'
  },
  weekNumbers: true,
  weekNumberTitle: 'W',
  selectable: true,
  loading: function(isLoading, view) {
    //var myView = $.cookie('currentView');
    //var myViewDate = $.cookie('currentViewDate');

    var myView = Nocie.Get('currentView')
    var myViewDate = Nocie.Get('currentViewDate')


    if(isLoading && myView!=null) {
      $('#calendar').fullCalendar('changeView', myView);
      if(myViewDate!=null){
        var mydate = Date.parse(myViewDate);
        var year = $.format.date(mydate, 'yyyy'); 
        var month = $.format.date(mydate, 'M') - 1; 
        var day = $.format.date(mydate, 'd'); 
        $('#calendar').fullCalendar( 'gotoDate', year, month, day);

        Nocie.Remove('currentViewDate');
        Nocie.Remove('currentView');
      }
    }
  },
  select: function(start, end, allDay) {
    var myView = $('#calendar').fullCalendar('getView');

    Nocie.Set('currentViewDate', myView.start);
    Nocie.Set('currentView', myView.name);

    //alert(start);

    OpenMode(start,end);
  },
  eventDrop: function (event, dayDelta, minuteDelta, allDay, revertFunc) 
  {
    var task = '';
    var task2 = '';

    if(event.task!=undefined)
      task = '&task='+event.task;
    if(event.task2!=undefined)
      task2 = '&task2='+event.task2;


    if(event.id > -1 && event.task > 0){
      $.getJSON('./index.php?module=zeiterfassung&action=create&cmd=updatezeiterfassung&id=[ID]&eid='+event.id+'&start='+$.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH-mm-ss')
        +'&end='+$.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm:ss')+'&allDay='+event.allDay+task+task2,
        function(data) { $('#calendar').fullCalendar('refetchEvents'); if(data.status!=1) alert(data.statusText); });
    } 
    else {
      alert("Eintrag kann nicht verschoben werden");
      revertFunc();
    }
  },
  eventResize: function (event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view ) {
    //eventResize: function(event, element) {
    if(event.id > -1){
      $.get('./index.php?module=zeiterfassung&action=create&cmd=updatezeiterfassung&id=[ID]&eid='+event.id+'&start='+$.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH-mm-ss')
                     +'&end='+$.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm:ss'),
                     function() {$('#calendar').fullCalendar('updateEvent', event);});
    } else {
       alert("Eintrag kann nicht verlängert werden");
       revertFunc();
    }
  },

  eventClick: function(calEvent, jsEvent, view) {
    var myView = $('#calendar').fullCalendar('getView');
    //$.cookie("currentView",myView.name);
    //$.cookie("currentViewDate", myView.visStart);

    Nocie.Set('currentView', myView.name);
    Nocie.Set('currentViewDate', calEvent.start);
    if($("#editZeiterfassung").SetFormData(calEvent.id, calEvent.start, calEvent.end, calEvent.allDay,calEvent.task,calEvent.task2))
      $("#editZeiterfassung").dialog("open");
  },
  editable: true,
  businessHours: true, // display business hours
  events: "./index.php?module=zeiterfassung&action=create&cmd=data" 
  });
});


$(function() {
    $.fn.SetFormData = function(id, start, end, allDay,task,task2) {
      if(task > 0) {
        $.ajax({
          url: './index.php?module=zeiterfassung&action=create&cmd=getzeiterfassung&id='+id,
          dataType: 'json',
          success: function(data) {
            
            $('#editZeiterfassung').find('input, textarea').attr('disabled',true);
            if(data.write == 1)
            {
              $('#editZeiterfassung').find('input, textarea').attr('disabled',false);
            }else{
              $('#editZeiterfassung').find('#titel').attr('disabled',true);
            }
           
            $('#editZeiterfassung').dialog('open');
            EditMode(data);
          },
          error: function (request, statusCode, error) { $("#submitError").html("Keine Event-Daten gefunden"); }
        });
        //window.open('index.php?module=projekt&action=edit&id='+task+'&back=kalender','_blank');
        return false;
}else{
  //$(":button:contains('Löschen')").prop("disabled",true).addClass( 'ui-state-disabled' );
  //			//window.open('index.php?module=projekt&action=edit&id='+id,'_blank');
  /*
  $("#mode").val("new");
  $("#datum").val($.format.date(start, "dd.MM.yyyy"));
  $("#datum_bis").val($.format.date(end, "dd.MM.yyyy"));
  $("#projekt").val(projektname);

  $("#public").attr('checked', true);
  $("#erinnerung").attr('checked', true);

  // Von & Bis
  $("#von").val($.format.date(start, "HH:mm"));
  $("#bis").val($.format.date(end, "HH:mm"));
  */
  return false;
}
return true;
}


});
var ResetMode = function() {
  $('#editZeiterfassung').find('#editid').val('');
  $('#editZeiterfassung').find('#internerkommentar').val('');
};

var OpenMode = function(start,end) {

    $("#submitError").html('');
    if(start==end)
    {
      $('#editZeiterfassung').find('#bisZeit').val('');
    }
    else {
      $('#editZeiterfassung').find('#bisZeit').val($.format.date(end, "HH:mm"));
    }

    $('#editZeiterfassung').find('#eventid').val('');
    $('#editZeiterfassung').find('#datum').val($.format.date(start, "dd.MM.yyyy"));
    $('#editZeiterfassung').find('#vonZeit').val($.format.date(start, "HH:mm"));

    $('#editZeiterfassung').find('#aufgabe2').val('');
    $('#editZeiterfassung').find('#beschreibung2').val('');
    $('#editZeiterfassung').find('#ort').val('');
    $('#editZeiterfassung').find('#art2').val('Arbeit');
    $('#editZeiterfassung').find('#internerkommentar').val('');

    $('#editZeiterfassung').find('#projekt_manuell2').val('');
    $('#editZeiterfassung').find('#arbeitspaket2').val('');
    $('#editZeiterfassung').find('#adresse_abrechnung2').val('');
    $('#editZeiterfassung').find('#auftrag').val('');
    $('#editZeiterfassung').find('#auftragpositionid').val('');
    $('#editZeiterfassung').find('#produktion').val('');
    $('#editZeiterfassung').find('#serviceauftrag').val('');

    $(":button:contains('Löschen')").prop("disabled",true).addClass( 'ui-state-disabled' );
    $(":button:contains('Kopieren')").prop("disabled",true).addClass( 'ui-state-disabled' );
    $("#editZeiterfassung").dialog("open");
};

var EditMode = function(data) {

  $(":button:contains('Löschen')").prop("disabled",false).removeClass( 'ui-state-disabled' );
  $(":button:contains('Kopieren')").prop("disabled",false).removeClass( 'ui-state-disabled' );
  $("#submitError").html('');
//  $("#mode").val("edit");
  $('#editZeiterfassung').find('#eventid').val(data.id);
  $('#editZeiterfassung').find('#aufgabe2').val(data.aufgabe);
  $('#editZeiterfassung').find('#beschreibung2').val(data.beschreibung);
  $('#editZeiterfassung').find('#ort').val(data.ort);
  $('#editZeiterfassung').find('#art2').val(data.art);

  $('#editZeiterfassung').find('#internerkommentar').val(data.internerkommentar);
  $('#editZeiterfassung').find('#vonZeit').val(data.vonzeit);
  $('#editZeiterfassung').find('#bisZeit').val(data.biszeit);
  $('#editZeiterfassung').find('#datum').val(data.datum);

  $('#editZeiterfassung').find('#projekt_manuell2').val(data.projekt_manuell);
  $('#editZeiterfassung').find('#arbeitspaket2').val(data.arbeitspaket);
  $('#editZeiterfassung').find('#adresse_abrechnung2').val(data.adresse_abrechnung);
  $('#editZeiterfassung').find('#auftrag').val(data.auftrag);
  $('#editZeiterfassung').find('#auftragpositionid').val(data.auftragpositionid);
  $('#editZeiterfassung').find('#produktion').val(data.produktion);
  $('#editZeiterfassung').find('#serviceauftrag').val(data.serviceauftrag);

  // Öffentlich

  if(data.abrechnen == 1) {
      $('#editZeiterfassung').find('#abrechnen2').prop('checked', true);
  }else {
      $('#editZeiterfassung').find('#abrechnen2').prop('checked', false);
  }
};

function ZeiterfassungSave() {        
    $.ajax({
        url: 'index.php?module=zeiterfassung&action=create&cmd=savezeiterfassung',
        data: {
            //Alle Felder die fürs editieren vorhanden sind
            id: $('#editZeiterfassung').find('#eventid').val(),
            datum: $('#editZeiterfassung').find('#datum').val(),
            start: $('#editZeiterfassung').find('#vonZeit').val(),
            end: $('#editZeiterfassung').find('#bisZeit').val(),
            aufgabe: $('#editZeiterfassung').find('#aufgabe2').val(),
            beschreibung: $('#editZeiterfassung').find('#beschreibung2').val(),
            ort: $('#editZeiterfassung').find('#ort').val(),
            art: $('#editZeiterfassung').find('#art2').val(),
            internerkommentar: $('#editZeiterfassung').find('#internerkommentar').val(),

            projekt_manuell: $('#editZeiterfassung').find('#projekt_manuell2').val(),
            arbeitspaket: $('#editZeiterfassung').find('#arbeitspaket2').val(),
            adresse_abrechnung: $('#editZeiterfassung').find('#adresse_abrechnung2').val(),
            auftrag: $('#editZeiterfassung').find('#auftrag').val(),
            auftragpositionid: $('#editZeiterfassung').find('#auftragpositionid').val(),
            produktion: $('#editZeiterfassung').find('#produktion').val(),
            serviceauftrag: $('#editZeiterfassung').find('#serviceauftrag').val(),
            abrechnen: $('#editZeiterfassung').find('#abrechnen2').prop("checked")?1:0
        },
        method: 'post',
        dataType: 'json',
        beforeSend: function() {
            App.loading.open();
        },
        success: function(data) {
            App.loading.close();
            if (data.status == 1) {
                $("#editZeiterfassung").dialog('close');
                $('#calendar').fullCalendar('refetchEvents');
                $('#editZeiterfassung').find('#beschreibung2').val('');
            } else {
                alert(data.statusText);
            }
        }
    });
}

// Methode zum addieren/subtrahieren einer Menge an Minuten auf eine Uhrzeit
// time = Uhrzeit im Format HH:MM
// offset = Zeit in Minuten
function addMinutes(time, offset){
  // Uhrzeit wird in Stunden und Minuten geteilt
  var elements = time.split(":");
  var hours = elements[0];	
  var minutes = elements[1];
  // Aufrunden des Offsets fuer den Fall, dass eine Fliesskommazahl uebergeben wird
  var roundOffset = Math.ceil(offset);
	
  // Umrechnen der Uhrzeit in Minuten seit Tagesbeginn
  var timeSince24 = (hours * 60) + parseInt(minutes);
  // Addieren des uebergebenen Offsets
  timeSince24 = timeSince24 + parseInt(roundOffset);

  // Ueberlaufbehandlung
  if(timeSince24 < 0)
    timeSince24 = timeSince24 + 1440;
  else if(timeSince24 > 1440)
    timeSince24 = timeSince24 - 1440;
	
  // Errechnen von Stunden und Minuten aus dem Gesamtzeit seit Tagesbeginn
  var resMinutes = timeSince24 % 60;
  var resHours = (timeSince24 - resMinutes)/60;
	
  // Sicherstellen, dass der Wert fuer Minuten immer zweistellig ist
  if(resMinutes < 10)
    resMinutes = "0" + resMinutes;
	
       
  if(resHours>23)
  {
    resHours=23;
    resMinutes=59;
  }       

  if(resMinutes>59)
  {
    resMinutes=59;
  }   
  // Ausgabe des formatierten Ergebnisses
  return resHours + ":" + resMinutes;
}



function BerechneEndzeit(minuten)
{
  var vonzeit = $('#editZeiterfassung').find('#vonZeit').val();
  $('#editZeiterfassung').find('#bisZeit').val(addMinutes(vonzeit,minuten));
}


</script>
<div id='calendar'></div>
<div id="editZeiterfassung" title="Zeiterfassung">
<div id="submitError"></div>
<input type="hidden" name="eventid" id="eventid" value="">
<table>
 <tr><td>Am:</td>
	<td colspan="3">
 <input type="text" name="datum" id="datum" size="10" value="[DATUM]" class="pflicht" maxlength="">&nbsp;{|von|}&nbsp;
<input type="text" name="vonZeit" id="vonZeit" size="4" value="[VONZEIT]" class="pflicht">&nbsp;{|Uhr|} &nbsp;{|Bis|}:&nbsp;
 <input type="text" name="bisZeit" id="bisZeit" size="4"  value="[BISZEIT]" class="pflicht">&nbsp;{|Uhr|} (HH:MM)
</td></tr>
 <tr><td></td>
	<td colspan="3">
    <input type="button" value="15 Min" onclick="BerechneEndzeit(15);">&nbsp;
	<input type="button" value="30 Min" onclick="BerechneEndzeit(30);">&nbsp;
	<input type="button" value="45 Min" onclick="BerechneEndzeit(45);">&nbsp;
<input type="button" value="1 Std" onclick="BerechneEndzeit(60);">&nbsp;
<input type="button" value="2 Std" onclick="BerechneEndzeit(120);">
<input type="button" value="Dauer" onclick="var dauer = prompt('Dauer eingeben z.B. 3,5 für 3,5 Stunden:',''); dauer = dauer.replace(',','.'); if(dauer > 0) BerechneEndzeit(dauer*60);">
</td></tr>

 <tr><td></td><td colspan="3"><i>{|Bitte die Pausen gesondert als Pausen (nicht Arbeit) buchen.|}</i></td></tr>

 <tr><td>{|Art/Tätigkeit|}:</td><td colspan="3" nowrap><select name="art" id="art2">[ART]</select>&nbsp;<input type="text" name="aufgabe2" id="aufgabe2" size="40" value="[AUFGABE]" class="pflicht"></td></tr>

 <tr><td>{|Details|}:</td><td colspan="2" nowrap><textarea type="text" name="beschreibung2" cols="62" rows="5" id="beschreibung2"></textarea></td><td></td></tr>
[STARTKOMMENTAR]
 <tr><td>{|Interner Kommentar|}:</td><td colspan="2" nowrap><textarea type="text" name="internerkommentar" id="internerkommentar" cols="62" rows="3"></textarea></td><td></td></tr>
[ENDEKOMMENTAR]
[STARTORT]
 <tr><td>{|Ort (wenn extern)|}:</td><td colspan="3"><input type="text" id="ort" name="ort" size="62" value="[ORT]"></td></tr>
 <tr><td></td><td><input type="hidden" id="gps" name="gps"  value="[GPS]">&nbsp;[GPSBUTTON]<div id="message">[GPSIMAGE]</div></td></tr>
[ENDEORT]
<tr><td>{|Projekt|}:</td><td>[PROJEKT_MANUELLAUTOSTART]<input type="text" id="projekt_manuell2" size="50" name="projekt_manuell2" value="[PROJEKT_MANUELL]">[PROJEKT_MANUELLAUTOEND]</td></tr>
<tr id="teilprojektrow" style="display:"><td>{|Teilprojekt|}:</td><td><input type="text" name="arbeitspaket" id="arbeitspaket2" value="[PAKETAUSWAHL]" size="50"></td></tr>
[STARTERWEITERT]
<tr><td></td><td colspan="3"><br></td></tr>

<tr><td>{|Kunde|}:</td><td>[ADRESSE_ABRECHNUNGAUTOSTART]<input type="text" id="adresse_abrechnung2" size="50" name="adresse_abrechnung" value="[ADRESSE_ABRECHNUNG]">[ADRESSE_ABRECHNUNGAUTOEND]</td></tr>
<tr><td>{|Auftrag|}:</td><td><input type="text" id="auftrag" size="50" name="auftrag" value="[AUFTRAG]"></td></tr>
<tr><td>{|Auftragsposition|}:</td><td><input type="text" id="auftragpositionid" size="50" name="auftragpositionid" value="[AUFTRAGPOSITIONID]"></td></tr>
<tr><td>{|Produktion|}:</td><td><input type="text" id="produktion" size="50" name="produktion" value="[PRODUKTION]"></td></tr>
[VORSERVICEAUFTRAG]<tr><td>{|Serviceauftrag|}:</td><td><input type="text" id="serviceauftrag" size="50" name="serviceauftrag" value="[SERVICEAUFTRAG]"></td></tr>[NACHSERVICEAUFTRAG]
<tr><td>{|Abrechnen|}:</td><td><input type="checkbox" name="abrechnen" id="abrechnen2" value="1" [ABRECHNEN]>&nbsp;<i>{|Bitte ausw&auml;hlen, wenn Zeit abgerechnet werden soll.|}</i></td></tr>

[ENDEERWEITERT]



<!--<tr><td>Verrechnungsart:</td><td><input type="text" id="verrechnungsart" size="50" name="verrechnungsart" value="[VERRECHNUNGSART]"></td></tr>-->


</table>



</div>

</div>
<!-- tab view schließen -->
</div>




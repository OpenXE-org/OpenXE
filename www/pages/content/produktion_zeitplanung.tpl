<script type='text/javascript' src='./js/jquery.dateFormat-1.0.js'></script>
<script type='text/javascript' src='./plugins/fullcalendar-1.6.7/fullcalendar.min.js?v=1'></script>
<script type='text/javascript' src='./js/nocie.js'></script>

<link rel='stylesheet' type='text/css' href='./plugins/fullcalendar-1.6.7/fullcalendar.css' />
<link rel='stylesheet' type='text/css' href='./plugins/fullcalendar-1.6.7/fullcalendar.print.css' media='print' />


<script type='text/javascript'>
	$(document).ready(function() {
		var date = new Date();
		var d = date.getDate();
		var m = date.getMonth();
		var y = date.getFullYear();
		
		$('#calendar').fullCalendar({
			theme: true,
			header: {
				left: 'prev,next today tasks',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
  		allDayText: 'Ganzt&auml;gig',
			firstDay: 1,
		  dayNamesShort: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
		  dayNames: ['Sonntag', 'Montag', 'Dienstag', 'Mittwoch', 'Donnerstag', 'Freitag', 'Samstag'],
      monthNames: ['Januar', 'Februar', 'März', 'April', 'Mai',
        'Juni', 'Juli', 'August', 'September', 'Oktober',  'November', 'Dezember'],	
  		monthNamesShort: ['Januar', 'Februar', 'März', 'April', 'Mai',
        'Juni', 'Juli', 'August', 'September', 'Oktober',  'November', 'Dezember'],	
			timeFormat: 'H:mm',
  buttonText: {
    prev: "<span class='fc-text-arrow'>&lsaquo;</span>",
    next: "<span class='fc-text-arrow'>&rsaquo;</span>",
    prevYear: "<span class='fc-text-arrow'>&laquo;</span>",
    nextYear: "<span class='fc-text-arrow'>&raquo;</span>",
    today: 'Heute',
    month: 'Monat',
    week: 'Woche',
    day: 'Tag'
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
                                                if(myView=='month')
                                                  var day = 15;
                                                else var day = $.format.date(mydate, 'd');
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

				$("#TerminDialog").SetFormData(-1, start, end, allDay);
				$("#TerminDialog").dialog("open");
			},


			eventDrop: function (event, dayDelta, minuteDelta, allDay, revertFunc) 
		//	eventDrop: function(event, element) 
			{
				var task = '';
				var task2 = '';
				
				if(event.task!=undefined)
					task = '&task='+event.task;
				if(event.task2!=undefined)
					task2 = '&task2='+event.task2;

				if(event.id > -1){
				$.get('./index.php?module=produktion&action=update&id='+event.id+'&start='+$.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH-mm-ss')
								+'&end='+$.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm:ss')+'&allDay='+event.allDay+task+task2,
							function() {$('#calendar').fullCalendar('updateEvent', event);});
				} 
	                        else if(event.task > -1){
				$.get('./index.php?module=produktion&action=update&id='+event.id+'&start='+$.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH-mm-ss')
								+'&end='+$.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm:ss')+'&allDay='+event.allDay+task+task2,
							function() {$('#calendar').fullCalendar('updateEvent', event);});
				} 

	                        else if(event.task2 > -1){
				$.get('./index.php?module=produktion&action=update&id='+event.id+'&start='+$.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH-mm-ss')
								+'&end='+$.fullCalendar.formatDate(event.end,'yyyy-MM-dd HH:mm:ss')+'&allDay='+event.allDay+task+task2,
							function() {$('#calendar').fullCalendar('updateEvent', event);});
				} 



                                else {
									alert("Eintrag kann nicht verschoben werden");
					        revertFunc();
				}
    	},
			eventResize: function (event, dayDelta, minuteDelta, revertFunc, jsEvent, ui, view ) {
			//eventResize: function(event, element) {
				if(event.id > -1){
        	$.get('./index.php?module=produktion&action=update&id='+event.id+'&start='+$.fullCalendar.formatDate(event.start,'yyyy-MM-dd HH-mm-ss')
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

				if($("#TerminDialog").SetFormData(calEvent.id, calEvent.start, calEvent.end, calEvent.allDay,calEvent.task,calEvent.task2))
					$("#TerminDialog").dialog("open");
			},
			editable: true,
			events: "./index.php?module=produktion&action=data" 
		});
		
	});

function AllDay(el) {
	if(el.checked) {
		$("#von").attr('disabled', true);
    $("#bis").attr('disabled', true);
	}else{
		$("#von").attr('disabled', false);
    $("#bis").attr('disabled', false);
	}
}


$(function() {
	$.fn.SetFormData = function(id, start, end, allDay,task,task2) {
		if(task > 0) {
			window.open('index.php?module=produktion&action=edit&id='+task+'&back=kalender','_blank');
			return false;
		}
		else if(task2 > 0) {
			//alert("Eintrag kann nicht editiert werden");
			window.open('index.php?module=produktion&action=edit&id='+task2+'&back=kalender','_blank');
			return false;
		}
                else if(id > -1) {
			window.open('index.php?module=produktion&action=edit&id='+id,'_blank');
                        return false;
		}else{
//			window.open('index.php?module=produktion&action=edit&id='+id,'_blank');
			return false;
		}
		return true;
	}


});

function getDialogButton( jqUIdialog, button_names )
{
    if (typeof button_names == 'string')
        button_names = [button_names];
    var buttons = jqUIdialog.parent().find('.ui-dialog-buttonpane button');
    for (var i = 0; i < buttons.length; i++)
    {
        var jButton = $( buttons[i] );
        for (var j = 0; j < button_names.length; j++)
            if ( jButton.text() == button_names[j] )
                return jButton;
    }

    return null;
}
</script>


<div id='calendar'></div>

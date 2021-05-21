<div id="tabs">
    <ul>
        <li><a href="#tabs-2">{|Zeitkonto Kunden|}</a></li>
<!--        <li><a href="#tabs-3">Zeitkonto Projekte</a></li>-->
        <li><a href="#tabs-1">{|Zeitkonto Mitarbeiter|}</a></li>
        <li><a href="#tabs-3" data-toggle="tab" >{|Zeitkonto Kalenderansicht|}</a></li>
    </ul>
<div id="tabs-1">

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="offen" class="switch">
            <input type="checkbox" id="offen">
            <span class="slider round"></span>
          </label>
          <label for="offen">{|offen|}</label>
        </li>
        <li class="filter-item">
          <label for="projekt">{|Projekt|}:</label>
          <input type="text" id="projekt" size="30"/>
        </li>
        <li class="filter-item">
          <label for="mitarbeiter">{|Mitarbeiter|}:</label>
          <input type="text" id="mitarbeiter" size="30" />
        </li>
        <li class="filter-item">
          <label for="von">{|von|}:</label>
          <input type="text" id="von" size="12"/>
        </li>
        <li class="filter-item">
          <label for="bis">{|bis|}:</label>
          <input type="text" id="bis" size="12"/>
        </li>
      </ul>
    </div>
  </div>

[MESSAGE]
[TAB1]
</div>


<div id="tabs-2">

  <div class="filter-box filter-usersave">
    <div class="filter-block filter-inline">
      <div class="filter-title">{|Filter|}</div>
      <ul class="filter-list">
        <li class="filter-item">
          <label for="kunden" class="switch">
            <input type="checkbox" id="kunden" title="auf Kundenkonto gebucht">
            <span class="slider round"></span>
          </label>
          <label for="kunden">{|Nur auf Kundenkonto gebuchte Zeiten|}</label>
        </li>
      </ul>
    </div>
  </div>

[MESSAGE]
[TAB2]
</div>


<div id="tabs-3">
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
<form action="#tabs-3" method="post">
{|Auswahl Mitarbeiter|}:
<br><input type="text" size="25" name="mitarbeiterkalenderansicht" id="mitarbeiterkalenderansicht" value="[MITARBEITERKALENDERANSICHT]"><input type="submit" value="{|übernehmen|}">
</form>
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

    $("#list").on("click", function() {
      $('#calendar').fullCalendar('changeView','month');
      $('#calendar').fullCalendar('changeView','agendaWeek');
    });




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
  selectable: false,
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
  editable: false,
  businessHours: true, // display business hours
  events: "./index.php?module=zeiterfassung&action=create&cmd=mitarbeiteransichtdata" 
  });

  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  $('#calendar').fullCalendar('render');
  });

});






</script>

</div>




</div>


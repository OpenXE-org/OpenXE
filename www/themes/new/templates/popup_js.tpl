<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <!-- <meta name="viewport" content="width=device-width, initial-scale=1" /> -->
		[ADDITIONALHEADER]
    <meta http-equiv="Content-Security-Policy" content="default-src 'self' 'unsafe-inline' 'unsafe-eval' xentral.com *.xentral.com xentral.biz *.xentral.biz *.wawision.de *.embedded-projects.net maps.googleapis.com maps.gstatic.com [ADDITIONALCSPHEADER];">
    <title>[HTMLTITLE]</title>
    <link rel="stylesheet" href="./themes/[THEME]/css/normalize.min.css?v=6">
    <link rel="stylesheet" href="./themes/[THEME]/css/[COLORCSSFILE]?v=7">
<style>
:root {
 [COLORCSS]
}
</style>

    <link rel="stylesheet" href="./themes/[THEME]/css/styles.css?v=32">
    <link rel="stylesheet" href="./themes/[THEME]/css/resp-menu.css?v=5">

	<script type="text/javascript" src="./js/event.js"></script>
	<script type="text/javascript" src="./js/jquery/jquery-3.5.0.min.js"></script>
	<script type="text/javascript" src="[JQUERYMIGRATESRC]"></script>
	<script type="text/javascript" src="./js/ajax_001.js?v=13"></script>
	<script type="text/javascript" src="./js/jquery.tablehover.min.js"></script>
	<script type="text/javascript" src="./js/jquery.jeditable.js" ></script>
	<script type="text/javascript" src="./js/jquery.cookie.js" ></script>
	<script type="text/javascript" src="./js/jquery-multidownload.js" ></script>

	<link rel="stylesheet" type="text/css" href="./js/datatables/datatables.min.css"/>
	<link rel="stylesheet" type="text/css" href="./themes/[THEME]/css/datatables_custom.css?v=2"/>
	<link href="./themes/[THEME]/css/jquery.dataTables.min.css" rel="stylesheet" type="text/css" media="screen">
	<link href="./themes/[THEME]/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" media="screen">
	<script type="text/javascript" src="./js/datatables/datatables.min.js?v=2"></script>
	<script type="text/javascript" src="./js/jquery.dataTables.columnFilter.js"></script>

	<script type="text/javascript" src="./js/jquery.inputhighlight.min.js"></script>
	<script type="text/javascript" src="./js/jquery.base64.min.js"></script>
	<script type="text/javascript" src="./js/grider.js" ></script>
	<script type="text/javascript" src="./js/jqclock_201.js"></script>
	<script type="text/javascript" src="./js/chart.min.js" ></script>
	<script type="text/javascript" src="./js/chart-plugins.js?v=2" ></script>
    <script type="text/javascript" src="./js/chart-helper.js?v=2"></script>
    <script type="text/javascript" src="./js/textvorlagen.js"></script>

	<link href="./css/bootstrap.min.css?v=4" rel="stylesheet" type="text/css" media="screen">
	<link rel="stylesheet" href="./themes/[THEME]/css/calendar.css?v=3">
	<script type="text/javascript" src="./js/bootstrap.min.js" ></script>

	<link id="shortcuticon" rel="shortcut icon" href="./themes/new/images/favicon/favicon.ico" type="image/x-icon">
	<link id="favicon" rel="icon" href="./themes/new/images/favicon/favicon.ico" type="image/x-icon">
	<link rel="icon" type="image/png" href="./themes/new/images/favicon/favicon-16x16.png" sizes="16x16">
	<link rel="icon" type="image/png" href="./themes/new/images/favicon/favicon-32x32.png" sizes="32x32">
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-navbutton-color" content="#ffffff">

	<link type="text/css" href="./themes/[THEME]/css/start/jquery-ui-1.10.3.custom.css?v=3" rel="Stylesheet" />
	<link href="./themes/[THEME]/css/colorPicker.css" rel="stylesheet" type="text/css" />

	<script src="./js/ckeditor/ckeditor.js"></script>
	<script src="./js/ckeditor/adapters/jquery.js"></script>

	<script type="text/javascript" language="javascript" src="./js/keynavigation.js?v=1.3"></script>

	<script type="text/javascript" src="./themes/[THEME]/js/jquery-ui-1.11.4.custom.min.js"></script>
	<script type="text/javascript" src="./js/jquery.ui.touch-punch.js"></script>
	<script type="text/javascript" src="./js/jquery-ui-timepicker-addon.js"></script>
	<script type="text/javascript" src="./js/jquery.colorPicker.js"></script>

[SCRIPTJAVASCRIPT]

<script type="text/JavaScript" language="javascript">
[JAVASCRIPT]

$(document).ready(function() {

  [AUTOCOMPLETE]
  [DATATABLES]
  [SPERRMELDUNG]
  [JQUERY]
  [JQUERYREADY]

  $('a.popup').click(function(e) {
    e.preventDefault();
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
    }).width([POPUPWIDTH] - horizontalPadding).height([POPUPHEIGHT] - verticalPadding);
  });
});

</script>
<script src="./themes/[THEME]/js/scripts.js"></script>   
<style>
[YUICSS]
</style>
[MODULEJAVASCRIPTHEAD]
[MODULESTYLESHEET]
</head>
<body class="[LAYOUTFIXMARKERCLASS]" data-module="[MODULE]" data-action="[ACTION]">
[PAGE]
[MODULEJAVASCRIPTBODY]
<script type="text/javascript" src="./js/download-spooler.js"></script>
<script type="text/javascript" src="./js/lockscreen.js"></script>
<script type="text/javascript" src="./js/ajax_end.js?v=3"></script>
</body>
</html>

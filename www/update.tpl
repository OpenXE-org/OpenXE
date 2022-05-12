<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta name="viewport" content="initial-scale=1, user-scalable=no">
<meta http-equiv="cache-control" content="max-age=0" />
<meta http-equiv="cache-control" content="no-cache" />
<meta http-equiv="expires" content="0" />
<meta http-equiv="expires" content="Tue, 01 Jan 1980 1:00:00 GMT" />
<meta http-equiv="pragma" content="no-cache" />
<script type="text/javascript" src="./jquery-update.js"></script>
<script type="text/javascript" src="./jquery-ui-update.js"></script>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<!--<meta name="viewport" content="width=1200, user-scalable=yes" />-->
<title>xentral Update</title>
<link rel="stylesheet" type="text/css" href="./jquery-ui.min.css">


<style type="text/css">
	@font-face{
		font-family: 'Inter';
		font-style:  normal;
		font-weight: 400;
		font-display: swap;
		src: url('./themes/new/fonts/Inter-Regular.woff2?v=3.13') format("woff2"),
		url('./themes/new/fonts/Inter-Regular.woff?v=3.13') format("woff");
	}
	@font-face {
		font-family: 'Inter';
		font-style:  italic;
		font-weight: 400;
		font-display: swap;
		src: url('./themes/new/fonts/Inter-Italic.woff2?v=3.13') format("woff2"),
		url('./themes/new/fonts/Inter-Italic.woff?v=3.13') format("woff");
	}

	@font-face {
		font-family: 'Inter';
		font-style:  normal;
		font-weight: 700;
		font-display: swap;
		src: url('./themes/new/fonts/Inter-Bold.woff2?v=3.13') format("woff2"),
		url('../themes/new/fonts/Inter-Bold.woff?v=3.13') format("woff");
	}
	@font-face {
		font-family: 'Inter';
		font-style:  italic;
		font-weight: 700;
		font-display: swap;
		src: url('./themes/new/fonts/Inter-BoldItalic.woff2?v=3.13') format("woff2"),
		url('./themes/new/fonts/Inter-BoldItalic.woff?v=3.13') format("woff");
	}


html, body {
	height:100%;
}

body{
	background:#ffffff;
	font-family: 'Inter', Arial, Helvetica, sans-serif;
	font-size: 8pt;
	color: var(--grey);
	margin: 0;
	padding: 0;
	line-height:1.4;
    height: 100vh;
		
	SCROLLBAR-FACE-COLOR: #fff;
	SCROLLBAR-HIGHLIGHT-COLOR: #fff; 
    SCROLLBAR-SHADOW-COLOR: #fff; 
    SCROLLBAR-ARROW-COLOR: #d4d4d4; 
    SCROLLBAR-BASE-COLOR: #d4d4d4; 
	SCROLLBAR-DARKSHADOW-COLOR: #d4d4d4;
	SCROLLBAR-TRACK-COLOR: #fff;
}

h1 {
	color:#000;
	text-align:center;
	width:100%;
	font-size:2em;
	padding-top:10px;
}

DIV#footer {
	height:32px; margin-top:-6px;
width:100%;
	text-align:center; color: #c9c9cb;}
  DIV#footer ul {
  list-style-type:none;width:100%; text-align:center;
  margin: 8px 0 0 0;
padding: 0;
  }
  DIV#footer ul li { color:rgb(73, 73, 73);font-weight:bold;display: inline;
padding-right: 8px;
list-style: none;
font-size: 0.9em;
  }
  DIV#footer ul li  a{ color:rgb(73, 73, 73);font-weight:bold;ext-decoration: none;
  }  
#page_container
{
  /*border: 0px solid rgb(166, 201, 226);
	border-right:8px solid rgb(1, 143, 163);
	border-left:8px solid rgb(1, 143, 163);*/
  background-color:white;
	min-height: calc(100vh - 230px);
	/*border-bottom:8px solid rgb(1, 143, 163);*/
	overflow:auto
}
input[type="button"] {
  cursor:pointer;
}
input[type="submit"] {
  cursor:pointer;
}
img.details {
  cursor:pointer;
}
	.button {
		width: 300px;
		height: 25px;
		background: rgb(120, 185, 93);
		padding: 10px;
		text-align: center;
		border-radius: 3px;
		color: white !important;
		font-weight: bold;
		top: 20px;
		position: relative;
		text-decoration:none;
	}
.button2 {
    width: 300px;
    height: 25px;
    /*background: rgb(1, 143, 163);*/
    text-align: center;
    border-radius: 3px;
    color: white !important;
    font-weight: bold;
    text-decoration:none;
    border:1px solid rgb(120, 185, 93) !important;
    margin-left:5px;
	background: rgb(120, 185, 93);
}


input:disabled {
  background: #dddddd;
}
</style>
[CSSLINKS]


[JAVASCRIPT]
<script type="application/javascript">

var aktprozent = 0;
var updateval = '';


function openPermissionbox(data)
{
  var html = '';
  if(typeof data.FolderError != 'undefined')
	{
		html += '<h3>In folgenden Ordnern fehlen Schreibrechte</h3>';
		$(data.FolderError).each(function(k,v)
		{
		  html += v+'<br />';
    });
	}
  if(typeof data.FileError != 'undefined')
  {
    html += '<h3>In folgenden Dateien fehlen Schreibrechte</h3>';
    $(data.FileError).each(function(k,v)
    {
      html += v+'<br />';
    });
  }
  $('#permissionbox').dialog('open');
	$('#permissionboxcontent').html(html);
}

$(document).ready(function() {
  $('#upgrade').prop('disabled',true);
  updateval = $('input#upgrade').val();
  $('input#upgrade').val('Suche nach Updates. Bitte warten');
  $.ajax({
    url: 'update.php?action=ajax&cmd=checkforupdate',
    type: 'POST',
    dataType: 'json',
    data: { version: '[AKTVERSION]'},
    fail : function(  ) {
                $('#upgrade').prop('disabled',false);
                $('input#upgrade').val(updateval);
            },
    error : function() {
                $('#upgrade').prop('disabled',false);
                $('input#upgrade').val(updateval);
            },
    success: function(data) {
     if(typeof data != 'undefined' && data != null  && typeof data.reload != 'undefined')
     {
       $('input#upgrade').val(updateval);
       window.location = window.location.href;
     }else{
       $('#upgrade').prop('disabled',false);
       $('input#upgrade').val(updateval);
       if(data !== null &&  typeof data.error != 'undefined' && data.error != '') {
           alert(data.error);
       }
     }
    }
  });

  setInterval(function(){
    if(aktprozent > 0)
    {
      var pr = parseInt(aktprozent);
      if(pr > 0)
      {
        var modulo = pr % 10;
        if(modulo < 9)pr++;
        updateprogressbardbupgrade(pr);
      }
    }
  },1000);
  $('#permissionbox').dialog(
      {
        modal: true,
        autoOpen: false,
        minWidth: 940,
        title:'Dateirechte',
        buttons: {
          OK: function() {
            $(this).dialog('close');
          }
        },
        close: function(event, ui){

        }
      });
});

  [DATATABLES]

  [SPERRMELDUNG]
 
  [AUTOCOMPLETE]

  [JQUERY]





</script>

[ADDITIONALJAVASCRIPT]
<style>
  .ui-autocomplete-loading { background: white url('images/ui-anim_basic_16x16.gif') right center no-repeat; }
  input.ui-autocomplete-input { background-color:#D5ECF2; }
  .ui-autocomplete { font-size: 8pt;z-index: 100000 !important ; }
	.ui-widget-header {border:0px;}
	.ui-dialog { z-index: 10000 !important ;}

[YUICSS]
</style>
</head>
<body  class="ex_highlight_row" [BODYSTYLE]>
[SPERRMELDUNGNACHRICHT]
    <div class="container_6" style="height:100%;">
               
 <div class="grid_6 bgstyle" style="  min-height: calc(100vh - 150px);">
<table width="100%"><tr valign="top">
[ICONBAR]
<td>
 <style>
.ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default  {
color:#fff;/*[TPLFIRMENFARBEHELL];*/
background-color:[TPLFIRMENFARBEHELL];
}

.ui-state-highlight, .ui-widget-content .ui-state-highlight, .ui-widget-header .ui-state-highlight {
    border: 1px solid #53bed0;
		background:none;
    background-color: #E5E4E2;
    color: #53bed0;
}

.ui-state-hover a,
.ui-state-hover a:hover,
.ui-state-hover a:link,
.ui-state-hover a:visited {
  color: #53bed0;
  text-decoration: none;
}

.ui-state-hover,
.ui-widget-content .ui-state-hover,
.ui-widget-header .ui-state-hover,
.ui-state-focus,
.ui-widget-content .ui-state-focus,
.ui-widget-header .ui-state-focus {
  border: 1px solid #448dae;
  font-weight: normal;
  color: #53bed0;
}


.ui-tabs-nav {
background: [TPLFIRMENFARBEHELL];
}

.ui-widget-content {
    border-top: 1px solid [TPLFIRMENFARBEHELL];
    border-left: 1px solid [TPLFIRMENFARBEHELL];
    border-right: 1px solid [TPLFIRMENFARBEHELL];
}
.ui-accordion {
    border-bottom: 1px solid [TPLFIRMENFARBEHELL];
}

.ui-state-default, .ui-widget-header .ui-state-default {
    border: 0px solid none;
}

.ui-state-default, .ui-widget-content .ui-state-default, .ui-widget-header .ui-state-default {
    border: 0px solid [TPLFIRMENFARBEHELL];
}

.ui-widget-content .ui-state-default a, .ui-widget-header .ui-state-default a, .ui-button-text {
font-size:8pt;
font-weight:bold;
border: 0px;
}


.ui-widget-content .ui-state-active, .ui-widget-header .ui-state-active  {
color:#53bed0;
}

.ui-widget-content .ui-state-active a, .ui-widget-header .ui-state-active a {
color:#53bed0;
font-weight:bold;
font-size:8pt;
background-color:[TPLFIRMENFARBEHELL];
border: 0px;
}

ul.ui-tabs-nav {
  background: [TPLFIRMENFARBEHELL];
  padding:2px;
}
.ui-widget-header {
  background: [TPLFIRMENFARBEHELL];
}
.ui-button-icon-primary.ui-icon.ui-icon-closethick
{
background-color:[TPLFIRMENFARBEDUNKEL];
color:white;
}


#toolbar {
padding: 4px;
display: inline-block;
}
/* support: IE7 */
*+html #toolbar {
display: inline;
}



#wawilink
{
  display:none;
  font-size:150%;
  text-align:center;

}

#downloadhinweis
{
  display:none;
  font-size:150%;
  color:#000;
}

#installhinweis
{
  display:none;
  font-size:150%;
  color:#000;
}

#upgradediv
{
display:none;
}
#dbhinweis
{
  display:none;
  font-size:150%;
  color:#000;
}

#wawilink a {
  color:#000;
}
  
  
@media screen and (max-width: 767px){
  #tabsul
  {
    float:left;
    display:block;
    width:70%;
    padding-left:0vw;
    min-width:55vw;
  }
  
  #tabsul li a {
  width:100%;
  display:block;
  }
  
  #tabsul li
  {
    display:none;
  }
  #tabsul li.menuaktiv
  {
    display:block;
    
    width:100%;
    padding-top:0px;
  }
  

  
  #tabsul li.opentab
  {
    width:98%;
    display:block;
  }
  
  #tabsul li.opentab a
  {
    width:100%;
    display:block;
    background-color:#53bed0;
  }
  
  #scroller2{
  
  max-width:99vw !important;
  }
  
  .navdirekt{
  min-width:70vw !important;
  }
  
}


</style>
<div id="scroller2" style="margin-top:3px; padding:0px; position:relative; height:53px;">
  
<h1>xentral Update</h1>
</div>
<div id="page_container">
[PAGE]

<div id="progress" style="width:50%;top:100px;left:25%;position:relative;display:block;">
<div id="downloadhinweis">Download:</div>
<div id="progressbardownload"></div>
<div id="installhinweis">Installieren:</div>
<div id="progressbarupdate"></div>
<div id="dbhinweis">Datenbank Update:</div>
<div id="progressbardbupgrade"></div>
<div id="wawilink"><a href="./index.php" class="button">Installation vollst&auml;ndig - Zur&uuml;ck zu xentral</a></div>
<div id="upgradediv"><form id="upgradefrm" method="POST" action="index.php?module=welcome&action=upgradedb"><input type="hidden" name="upgradedb" value="1" /><input type="submit" style="display:none;" value=" "></form></div>
</div>


<script type="application/javascript">
  var aktversion = '[AKTVERSION]';
  var downloadversion = '[AKTVERSION]';
  var ioncubeversion = '[IONCUBEVERSION]';
  var phpversion = '[PHPVERSION]';
  var todownload = null;
  var tocopy = null;
  var anzcheck = 0;
  var runDownloaded = 0;

  function versel()
  {
    downloadversion = $('#verssel').val();
  }
  
  function upgrade()
  {
    if(aktversion && downloadversion)
    {
      var text = 'Wirklich updaten?';
      if(aktversion == downloadversion)
      {
        
      }else{
        text = 'Wirklich auf neue Version upgraden?';
      }
      
      if(confirm(text))
      {
        anzcheck = 0;
        check2();
      }
    }
  }
  
  function check2()
  {
    if(anzcheck > 10)
    {
      alert('Verbindungsproblem beim Updaten. Bitte nochmal das Update starten!');
      return;
    }
    $('#downloadhinweis').show();
    $('#installhinweis').show();
    $('#dbhinweis').show();
    anzcheck++;
        $( "#progressbardownload" ).progressbar({
          value: 0
        });
        $( "#progressbarupdate" ).progressbar({
          value: 0
        });
        $( "#progressbardbupgrade" ).progressbar({
          value: 0
        });
        aktprozent = 0;
        $.ajax({
          url: 'update.php?action=ajax&cmd=checkfiles2',
          type: 'POST',
          dataType: 'json',
          data: { version: downloadversion}})
        .done( function(data) {
            if(typeof data.error != 'undefined')
            {
              alert(data.error);
              return;
            }
            if(typeof data.FolderError != 'undefined' || typeof data.FileError != 'undefined')
						{
						  openPermissionbox(data);
              return;
						}
            if(downloadversion != aktversion)
            {
              $.ajax({
                url: 'update.php?action=ajax&cmd=changeversion',
                type: 'POST',
                dataType: 'json',
                data: { version: downloadversion}})
              .done( function(data) {
                 if(typeof data.version != 'undefined')
                 {
                   if(downloadversion == data.version)
                    aktversion = data.version;
                    check2();
                 }
              });
              return;
            }
            
            if(typeof data.download != 'undefined')
            {
              todownload = data.download;

            }else{
              todownload = null;
            }
            if(typeof data.copy != 'undefined')
            {
              tocopy = data.copy;

            }else{
              tocopy = null;
            }
            if(todownload != null)
            {
              if(typeof todownload != 'undefined' && todownload > 0)
              {
                  runDownloaded = 0;
                	return download2(todownload);
              }
            }else {
                runDownloaded++;
                if(runDownloaded < 3) {
                    return download2(1);
                }
                $( "#progressbardownload" ).progressbar({
                    value: 100
                });
            }
            if(tocopy != null)
            {
              if(typeof tocopy != 'undefined' && tocopy > 0)
              {
                return copy2(tocopy);
              }else {
                  copy2(0);
              }
            }else {
                copy2(0);
            }
          })
        .fail(function( jqXHR, textStatus, errorThrown ) {
            alert('Verbindungsproblem beim Updaten. Bitte nochmal das Update starten!');
          
          }
        );

  }

  function download2(anzahl)
  {
    if(todownload == null)
    {
      $( "#progressbardownload" ).progressbar({
        value: 100
      });
      if(anzahl > 0)check2();
      if(anzahl == 0)copy2();
    }
    else if((typeof todownload == 'undefined' || todownload == 0) )
    {
      $( "#progressbardownload" ).progressbar({
        value: 100
      });
      check2();
    }else if((todownload == 0))
    {
      $( "#progressbardownload" ).progressbar({
        value: 100
      });
      check2();
    }else{
      var len = todownload;
      if(anzahl <= len)
      {
        $( "#progressbardownload" ).progressbar({
          value: false
        });
      }else if(anzahl > len){
        $( "#progressbardownload" ).progressbar({
          value: 100*((anzahl-len)/anzahl)
        });
      }
      if(len > 0)
      {
          var j = 0;
          for(j = 0; j < 250; j++) {
              $.ajax({
                  url: 'update.php?action=ajax&cmd=downloadfiles2',
                  type: 'POST',
                  dataType: 'json',
                  async: false,
                  data: {version: downloadversion}
              })
               .done(
                   function (data) {
                       if (typeof data.todownload !== undefined) {
                           todownload = data.todownload;
                           if (todownload === null) {
                               len = 0;
                           } else {
                               len = todownload;
                               runDownloaded = 0;
                           }
                           $("#progressbardownload").progressbar({
                               value: 100 * ((anzahl - len) / anzahl)
                           });
                       }
                       else {
                           todownload = null;
                       }
                   })
               .fail(function (jqXHR, textStatus) {
                   todownload = null;
                   check2();
               });
              if(todownload === null) {
                  break;
              }
          }
          check2();
      }
    }
  }
  
  function copy2(anzahl)
  {
    if((todownload == null) || (typeof todownload == 'undefined') || (todownload == 0))
    {
      if((tocopy == null) || (typeof tocopy == 'undefined') || (tocopy == 0))
      {
        $( "#progressbarupdate" ).progressbar({
          value: 100
        });
        upgradedb2(1);
      }
      else{
        var len = tocopy;
        if(anzahl <= len)
        {
          $( "#progressbarupdate" ).progressbar({
            value: false
          });
        }else if(anzahl > len){
          $( "#progressbarupdate" ).progressbar({
            value: 100*(len/anzahl)
          });
        }
        if(len > 0)
        {
          $.ajax({
            url: 'update.php?action=ajax&cmd=copyfiles2',
            type: 'POST',
            dataType: 'json',
            data: { version: downloadversion}})
          .done(function(data) {
            if(typeof data.tocopy != 'undefined')
            {
              tocopy = data.tocopy;
              if(tocopy === null)
              {
                len = 0;
              }else{
                len = tocopy;
              }
              $( "#progressbardownload" ).progressbar({
                value: 100*((anzahl-len)/anzahl)
              });
              copy2(anzahl);
            }
          })
            
          .fail(function( jqXHR, textStatus, errorThrown ) {
                check2();
          });
        }
      }
    }else{
      check2();
    }
  }
  
  function updateprogressbardbupgrade(prozent)
  {
    aktprozent = prozent;
    $( "#progressbardbupgrade" ).progressbar({
      value: prozent
    });
  }

  var aktdb = null;
	var aktsubdb = null;
  function upgradedb2(nr)
  {
    if(anzcheck > 12 && nr == 0) {
				return;
    }
    if(todownload == null || typeof todownload == 'undefined' || todownload == 0)
    {
      if(tocopy == null || typeof tocopy == 'undefined' || tocopy == 0)
      {
        if(nr == 1) {
						anzcheck = 0;
        }
        if(nr < 1)
        {
          updateprogressbardbupgrade(1);
        }else{
          updateprogressbardbupgrade(8 * nr - 5);
        }
        aktdb = nr;
        $.ajax({
          url: 'update.php?action=ajax&cmd=upgradedb',
          type: 'POST',
          dataType: 'json',
          data: {
              version: downloadversion,
							nummer: (nr!=10 || aktsubdb == null)?nr:nr+'-'+aktsubdb
          }})
          .done( function(data) {
            if(typeof data.nr != 'undefined')
            {
                var nrar = (data.nr+'').split('-');
                nr = parseInt(nrar[ 0 ]);
                if(typeof nrar[ 1 ] != 'undefined') {
                    aktsubdb = parseInt(nrar[ 1 ]);
                }
                else {
                    aktsubdb = null;
                }
              if(nr > 11 || data.nr == null)
              {
                updateprogressbardbupgrade(100);

                $('#wawilink').show();
              }else{
                updateprogressbardbupgrade(8 * nr);
                upgradedb2(data.nr);
              }
            }
          }).fail(function( jqXHR, textStatus, errorThrown ) {
            if(aktdb < 12)
						{
						    if(aktdb == 10) {
						        if(aktsubdb == null) {
						            aktsubdb = 1;
                    }
						        else {
						            aktsubdb++;
						            if(aktsubdb > 100) {
                            aktdb++;
                            aktsubdb = null;
                        }
                    }
                }
						    else {
						        aktdb++;
						        aktsubdb = null;
                }
              upgradedb2(aktdb);
						}else {
                aktsubdb = null;
								$('#upgradediv').show();
								$('#upgradefrm').submit();
            }
          }
        );
      }else{
        check2();
      }    
    }else{
      check2();
    }
  }

</script>


</div>
</td></tr></table>

<div class="clear"></div>
	    </div>
		<!-- end CONTENT -->
        
		<!-- end RIGHT -->
        
        <div id="footer" class="grid_6">
          <ul>
          	<li><a href="https://xentral.com/akademie-home" target="_blank">Handbuch</a></li>
          	<li><a href="https://xentral.com/" target="_blank">xentral.com</a></li>
    		<li>&copy; [YEAR] xentral ERP Software GmbH | xentral &reg; |
		<a href="index.php?module=welcome&action=info">[WAWIVERSION]</a> [LIZENZHINWEIS]</li>
		  </ul>
		</div>
        <!-- end FOOTER -->
		<div class="clear"></div>

    </div>

[JSSCRIPTS]


[BODYENDE]
<div id="permissionbox" style="display:none;">
	<div id="permissionboxcontent"></div>
</div>
</body>

</html>

<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
        <!--<li><a href="#tabs-2">Navigation</a></li>-->
        <!--<li><a href="#tabs-3">Live-Status</a></li>-->
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->

<form action="" method="post">
<!--<div id="tabs-3">
<br><h1>Live-Status:</h1>
<div class="tabsbutton" align="center">
<a  href="[URL]index.php?module=status&action=main" target="_blank"><table><tr><td width="150">Online-Status</td></tr></table></a>
<a  href="[URL]index.php?module=status&action=image" target="_blank"><table><tr><td width="150">Online-Bilder</td></tr></table></a>
</div>
<br><br>
<center>Hier k&ouml;nnen Sie direkt pr&uuml;fen, welche Artikel im Shop nicht angezeigt werden, bzw. welche Bilder auf dem Server online sind.</center>
</div>-->

<!--<div id="tabs-2">
<br><h1>Navigation:</h1>
<table width="100%">
<tr valign="top"><td width="30%">

<table>
<tr><td><input type="button" onclick='fenster = window.open("index.php?module=shopexport&action=navigation&shop=[ID]", "fenster1", "width=750,toolbar=0,location=0,directories=0,height=600,status=no,scrollbars=no,resizable=no"); fenster.focus();'  name="schritt1" value="Navigation &auml;ndern" style="width:200"></td></tr>
<tr><td><input type="submit" name="navexport" value="Navigation &uuml;bertragen" style="width:200"
onclick="document.getElementById('imageSpace').style.display = '';"></td></tr>
</table>
</td><td>
[NAVEXPORT]
</td></tr>
</table>
</div>-->

<!--
<br><h3>Allgemeiner Export:</h3>
<br>
<table width="100%">
<tr valign="top"><td width="30%">

<table>
<tr><td><input type="submit" name="commonexport" value="Export starten" style="width:200"
onclick="document.getElementById('imageSpace').style.display = '';"></td></tr>
</table>
</td><td>
[COMMONEXPORT]
</td></tr>
</table>
-->


<div id="tabs-1">
<br><h1>Online-Shop-Export:</h1>
<br>
<table width="100%">
<tr valign="top"><td width="30%">
<table>
<tr><td><input type="submit" name="schritt1" value="1. Verbindung pr&uuml;fen" style="width:200" 
  onclick="document.getElementById('imageSpace').style.display = '';"></td></tr>
<!--<tr><td><input type="submit" name="schritt2" value="2. &Auml;nderungen ermitteln" style="width:200" [SCHRITT2] 
  onclick="document.getElementById('imageSpace').style.display = '';"></td></tr>
<tr><td><input type="submit" name="schritt3" value="3. Update starten" onclick="document.getElementById('imageSpace').style.display = '';"
  style="width:200" [SCHRITT3]></td></tr>-->
<tr><td align="center" height="100"><div id="imageSpace" style="display:none"><img src="./themes/[THEME]/images/load.gif"></div> </td></tr>
</table>
[HIDDENSCHRITT2]
[HIDDENSCHRITT3]
</td><td>
Status:
[STATUS]

</td></tr>

<tr><td colspan="2"><div style="OVERFLOW: auto; WIDTH: 100%; HEIGHT: 300px">[UPDATES]</div</td></tr>
</table>

</form>
</div>


</div>

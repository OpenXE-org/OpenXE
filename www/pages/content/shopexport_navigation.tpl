<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Navigation</a></li>
        <!--<li><a href="#tabs-2">Navigation</a></li>-->
        <!--<li><a href="#tabs-3">Live-Status</a></li>-->
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->

<form action="" method="post">

<div id="tabs-1">
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
</div>

</div>

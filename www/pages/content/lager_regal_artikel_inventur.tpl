<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Inventur [LAGERPLATZ]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<div id="ajaxmessage"></div>
<form action="" method="post">
<table width="100%">
<tr>
<td align="center" >Artikel:&nbsp;<input type="text" name="artikel" id="artikel" value="" style="background-color: red;color:#000;">&nbsp;Artikel scannen!
&nbsp;<input type="submit" name="artikelbuchen" value="erfassen">
</td>
<td align="right">
[PERMISSIONINVENTURSTART]
&nbsp;<input type="button" class="btnRed" onclick="if(!confirm('Gezählte Menge für dieses Regal zurücksetzen!')) return false; else window.location.href='index.php?module=lagerinventur&action=inventur&cmd=resetlagerplatz&lager_platz=[LAGERPLATZID]&id=[ID]';" value="Inventur f&uuml;r Regalplatz [LAGERPLATZ] zur&uuml;cksetzen">
[PERMISSIONINVENTURENDE]

&nbsp;<input type="button" onclick="window.location.href='index.php?module=lagerinventur&action=bestand&id=[ID]'" value="Zur&uuml;ck zum Lager" name="back">

<input type="hidden" name="regal" value="[REGAL]">
</td></tr>
</table>

<script type="text/javascript">document.getElementById("artikel").focus();</script>

<!--<table width="100%"><tr><td align="right"><input type="submit" value="Speichern" name="inventurspeichern"></td></tr></table>-->
[TAB1]
<!--<table width="100%"><tr><td align="right"><input type="submit" value="Speichern" name="inventurspeichern"></td></tr></table>-->
</form>
</div>


</div>

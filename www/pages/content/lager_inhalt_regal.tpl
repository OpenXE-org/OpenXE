<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">Inhalt [LAGERPLATZ]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
[MESSAGE]
<form action="" method="post">
<table width="100%"><tr><td align="center">Artikel:&nbsp;[ARTIKELSTART]<input type="text" name="artikel" id="artikel" value="" style="background-color: red;color:#000;">[ARTIKELENDE]&nbsp;Artikel scannen!
&nbsp;<input type="submit" name="artikelbuchen" value="erfassen">
</td><td align="right">
&nbsp;<input type="button" onclick="window.location.href='index.php?module=lager&action=inhalt&id=[ID]'" value="Zur&uuml;ck zum Lager" name="back">

<input type="hidden" name="regal" value="[REGAL]">
</td></tr>
</table>

<script type="text/javascript">document.getElementById("artikel").focus();</script>

[TAB1]
</form>
</div>

</div>

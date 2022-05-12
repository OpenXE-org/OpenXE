<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1">[TABTEXT]</a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<form action="" method="post">
[MESSAGE]
<div class="row">
<div class="row-height">


<div class="col-xs-12 col-md-8 col-md-height">
<div class="inside_white inside-full-height">
<fieldset class="white"><legend>{|Belege|}</legend>
[TAB1]
</fieldset>
</div>
</div>

<div class="col-xs-12 col-md-2 col-md-height">
<div class="inside inside-full-height">
<fieldset><legend>{|Aktionen|}</legend>
<input type="button" class="btnBlueBig" value="Startbeleg erstellen" onclick="if(!confirm('Soll der Startbeleg jetzt erstellt werden?')) return false; else window.location.href='index.php?module=pos&action=rksvstart';" name="delcache"><br>
<input type="button" class="btnBlueBig" value="Nullbeleg erstellen" onclick="if(!confirm('Soll der Nullbeleg jetzt erstellt werden?')) return false; else window.location.href='index.php?module=pos&action=rksvnull';" name="delcache"><br>


</fieldset>
<fieldset><legend>{|Einstellung|}</legend>
<table>
<tr><td>Trainings-Modus:</td><td><input type="checkbox" name="training" id="training" value="1" [TRAINING]></td></tr>
</table>
</fieldset>

</fieldset>
<fieldset><legend>{|DEP Export|}</legend>
<table>
<tr><td>Von:</td><td><input type="text" name="artikel" id="artikel" size="15"></td></tr>
<tr><td>Bis:</td><td><input type="text" name="artikel" id="artikel" size="15">&nbsp;<input type="submit" name="artikelladen" value="Download"></td></tr>
</table>
</fieldset>



</div>
</div>

</div>
</div>
</form>
</div>

<!-- tab view schließen -->
</div>


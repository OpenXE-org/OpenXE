<script type="text/javascript">
function redirectfb(){
window.location.href="./index.php?module=backup&action=list";

}
function makeBackup()
{
	document.getElementById('backupcontent').innerHTML = "<h1>{|Warten Sie bitte solange das Backup erstellt wird.|}</h1><img src=\"./themes/new/images/load.gif\">"
																											 + "<iframe id=\"iframefullbackup\" onload=\"redirectfb()\" src=\"./index.php?module=backup&action=makefull\" border=\"0\" frameborder=\"0\" width=\"40\" height=\"40\"></iframe>";
}
</script>

<center><div id="backupcontent"><h1>{|Das Erstellen des Backup's kann je nach Datenmenge mehrere Minuten dauern.|}</h1>
<input type="button" name="doFullBackup" onclick="makeBackup()" value="{|Backup erstellen|}">
</div></center>

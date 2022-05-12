<!-- gehort zu tabview -->
<div id="tabs">
    <ul>
        <li><a href="#tabs-1"></a></li>
    </ul>
<!-- ende gehort zu tabview -->

<!-- erstes tab -->
<div id="tabs-1">
<form action="" method="post">
<fieldset><legend>{|Netzwerkeinstellungen|}</legend>
<table width="800">
	<tr><td>DHCP:</td><td><input type="checkbox" name="dhcp" value="1" [DHCP]></td><td><i>Falls die IP per DHCP bezogen werden soll.</i></td></tr>
	<tr><td>IP-Adresse:</td><td><input type="text" size="60" value="[IPADRESSE]" name="ipadresse"></td><td><i>z.B. 192.168.0.12</i></td></tr>
	<tr><td>Netzmaske:</td><td><input type="text" size="60" value="[NETMASK]" name="netmask"></td><td><i>z.B. 255.255.255.0</i></td></tr>
	<tr><td>Gateway:</td><td><input type="text" size="60" value="[GATEWAY]" name="gateway"></td><td><i>z.B. 192.168.0.1</i></td></tr>
	<tr><td>DNS:</td><td><input type="text" name="dns" value="[DNS]" size="60"></td><td><i>z.B. 192.168.0.1</i></td></tr>
	<tr><td>WLAN:</td><td><input type="checkbox" name="wlan" value="1" [WLAN]></td><td><i>Falls die Verbindung per Wireless aufgebaut werden soll.</i></td></tr>
	<tr><td>SSID:</td><td><input type="text" name="ssid" value="[SSID]" size="60"></td><td><i>z.B. Mein Netzwerk</i></td></tr>
	<tr><td width="100">Passphrase:</td><td width="400"><input type="text" name="passphrase" value="[PASSPHRASE]" size="60"></td><td><i>WLAN Schl&uuml;ssel</i></td></tr>
</table>
</fieldset>

<fieldset><legend>{|Allgemein|}</legend>
<table width="800">
	<tr><td width="100">Server URL:</td><td width="400"><input type="text" name="url" value="[URL]" size="60"></td><td><i>z.B. http://192.168.0.125:9000/wawision/</i></td></tr>
	<tr><td width="100">Security Key:</td><td width="400"><input type="text" name="devicekey" value="[DEVICEKEY]" size="60"></td><td><i>Security Key aus den Grundeinstellungen</i></td></tr>
</table>
</fieldset>


<fieldset><legend>{|Download|}</legend>
<center><input type="submit" name="submit" value="Datei herunterladen">&nbsp;<br><br><i>Diese Datei muss auf einen USB-Stick mit dem Namen wawision.php gespeichert werden.</i></center>
</fieldset>
</form>



</div>

<!-- tab view schließen -->
</div>


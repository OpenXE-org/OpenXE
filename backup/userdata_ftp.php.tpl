<?php

$ftp_server="";
$ftp_user_name = "";
$ftp_user_pass= "";

$pfad = "/tmp";

$userdata = "/var/www/wawision/userdata";


exec("tar cfz $pfad/userdata.tar.gz $userdata");

$file = $pfad.'/userdata.tar.gz';

$remote_file = "userdata_".date('d').".tar.gz";

// Verbindung aufbauen
$conn_id = ftp_connect($ftp_server);

// Login mit Benutzername und Passwort
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

ftp_pasv($conn_id, true);


// Datei hochladen
if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY)) {
} else {
}

// Verbindung schlie?~_en
ftp_close($conn_id);
unlink($file);
?>

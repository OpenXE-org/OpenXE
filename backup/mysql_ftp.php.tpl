<?php

$WFdbname='';
$WFdbuser='';
$WFdbpass='';

$ftp_server="";
$ftp_user_name = "";
$ftp_user_pass= "";

$pfad = "/tmp";

exec("mysqldump $WFdbname -hlocalhost -u".$WFdbuser." -p".$WFdbpass." | gzip > $pfad/mysql_complete.gz");

$file = $pfad.'/mysql_complete.gz';
$remote_file = "mysql_complete_".date('d').".gz";

// Verbindung aufbauen
$conn_id = ftp_connect($ftp_server);

// Login mit Benutzername und Passwort
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

//ftp_pasv($conn_id, true);

// Datei hochladen
if (ftp_put($conn_id, $remote_file, $file, FTP_BINARY)) {
} else {
}

// Verbindung schlie?~_en
ftp_close($conn_id);
unlink($file);
?>

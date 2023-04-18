<?php 

$version="OSS"; 
$version_revision="P.1.9";
$githash = file_get_contents("../githash.txt");
if (!empty($githash)) {
  $version_revision .= " (".substr($githash,0,8).")";
}

?>

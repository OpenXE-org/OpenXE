<?php 

$version="OSS"; 
$version_revision="1.12";
$gitinfo = file_get_contents("../gitinfo.json");
if (!empty($gitinfo)) {
  $gitinfo = json_decode($gitinfo);
  
  if ($gitinfo->branch != 'master') {
    $version_revision .= " (".substr($gitinfo->hash,0,8)." - ".$gitinfo->branch.")";
  }
  else {
      $version_revision .= " (".substr($gitinfo->hash,0,8).")";
  }
  
} else {
  $version_revision .= " (?)";
}

?>

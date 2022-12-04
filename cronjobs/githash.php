<?php
/* Copyright (c) 2022 OpenXE-org */
/*
  Refresh the githash number in githash.txt
*/
$path = '../.git/';
if (!is_dir($path)) {
  return;
}
$head = trim(file_get_contents($path . 'HEAD'));
$refs = trim(substr($head,0,4));
if ($refs == 'ref:') {
    $ref = substr($head,5);
    echo($path.$ref."\n");
    $hash = trim(file_get_contents($path . $ref));
} else {
    $hash = $head;
}
if (!empty($hash)) {
  file_put_contents("../githash.txt", $hash);
}
?>

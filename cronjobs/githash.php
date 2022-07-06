<?php
/* Copyright (c) 2022 Xenomporio project */
/*
  Refresh the githash number in githash.txt
*/
$path = '../.git/';
if (!is_dir($path)) {
  return;
}
$head = trim(substr(file_get_contents($path . 'HEAD'), 4));
$hash = trim(file_get_contents(sprintf($path . $head)));

if (!empty($hash)) {
  file_put_contents("../githash.txt", $hash);
}
?>

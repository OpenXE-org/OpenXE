<?php
$intern = true;
if(!empty($argv[1]) && strtolower($argv[1]) === 'changeversion'){
  $allowChangeVersion = true;
}

include __DIR__.'/www/update.php';
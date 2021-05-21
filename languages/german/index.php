<?php


include("inline.php");


foreach($inline as $rows)
{
echo "<h1>".$rows['default']['heading']."</h1>"; 
echo "<p>".str_replace('<br><br>','<br>',$rows['default']['description'])."</p>"; 
}

<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php
header_remove();
header("Content-Type:text/xml");

function RunStateMachine($DB, $deviceid)
{
  $deviceid_destination = $_GET['device'];
  $cmd = $_GET['cmd'];

  echo "<xml>";
  $tmpip = $_SERVER['REMOTE_ADDR'];
  $DB->Update("UPDATE adapterbox SET letzteverbindung=NOW(),tmpip='" . $DB->real_escape_string($tmpip) . "' WHERE seriennummer='" . $DB->real_escape_string($deviceid) . "' AND seriennummer!='' LIMIT 1");

  switch ($cmd) {
    case "addJob":
      echo "<cmd>$cmd</cmd>";
      $job = $_POST['job'];
      $request_id = $_GET['request_id'];
      $art = $_GET['art'];
      //file_put_contents("/tmp/bene","add job for $deviceid_destination deviceid $deviceid job $job");

      if ($deviceid != "" && $deviceid_destination != "" && $job != "") {
        $job = base64_encode($job);
        $DB->Insert("INSERT INTO device_jobs (id,deviceidsource,deviceiddest,job,zeitstempel,request_id,art) 
                                  VALUES ('','" . $DB->real_escape_string($deviceid) . "','" . $DB->real_escape_string($deviceid_destination) . "','" . $DB->real_escape_string($job) . "',NOW(),'" . $DB->real_escape_string($request_id) . "','" . $DB->real_escape_string($art) . "')");
        echo "<result>1</result>";
      } else {
        echo "<result>0</result>";
      }
      break;

    case "getJob":
      echo "<cmd>$cmd</cmd>";
      $tmp = $DB->SelectRow(
        sprintf(
          "SELECT `id`, `job`, `art` 
					FROM `device_jobs` 
					WHERE `deviceiddest` = '%s' AND `abgeschlossen` = '0' 
					ORDER BY `zeitstempel` LIMIT 1",
          $DB->real_escape_string($deviceid)
        )
      );
      if (empty($tmp)) {
        echo '<result>0</result>';
        break;
      }
      $DB->Delete(sprintf('DELETE FROM `device_jobs` WHERE `id` = %d LIMIT 1', $tmp['id']));
      echo "<job>" . $tmp['job'] . "</job>";
      echo "<device>" . $tmp['art'] . "</device>";
      echo "<id>" . $tmp['id'] . "</id>";
      echo "<result>1</result>";
      if (rand(0, 1000) === 0) {
        //should be not necessary
        $DB->Delete("DELETE FROM `device_jobs` WHERE `abgeschlossen` = '1'");
      }
      break;

    case "logOut":
      echo "<cmd>$cmd</cmd>";

      break;

    case "state":
      echo "<cmd>$cmd</cmd>";
      if ($deviceid_destination != "")
        $tmp = $DB->Select("SELECT COUNT(id) FROM device_jobs WHERE deviceiddest='" . $DB->real_escape_string($deviceid_destination) . "' AND abgeschlossen!='1'");
      else
        $tmp = $DB->Select("SELECT COUNT(id) FROM device_jobs WHERE deviceiddest='" . $DB->real_escape_string($deviceid) . "' AND abgeschlossen!='1'");
      echo "<numberofjobs>$tmp</numberofjobs>";
      echo "<deviceid>$deviceid</deviceid>";
      break;
    default:
      echo "<cmd>unkown</cmd>";
      echo "<pre>DEVICE ID: $deviceid L1 $L1 L2 $L2 L3 $L3</pre>";
  }

  echo "</xml>";
}


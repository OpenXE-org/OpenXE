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

//include("xtea.class.php");

/*
$serial = "abcdefghijklmNopqrstuvwxyz";
$key = pack('V*', 0x01,0x02,0x03,0x04);
$pad = "4371353838310545596909623831103272086622087173752843453214777855055965572268047010384215";
*/
//print(wawision_pad_verify($pad,$key,$serial));

//print(wawision_pad_verify($pad,$key,$serial));

class WaWisionOTP
{

  function wawision_encode($base64) {
      $output = "";
      for($i = 0; $i < strlen($base64)-1; $i++) {
          $c = ord($base64[$i])-ord('+');

          $output .= chr($c/10 + ord('0'));
          $output .= chr($c%10 + ord('0'));
      }

      return $output;
  }

  function wawision_decode($input)
  {
      $base64_str = "";

      for ($i=0; $i<strlen($input)/2; $i++) {
          $ten = ord($input[2*$i])   - ord('0');
          $one = ord($input[2*$i+1]) - ord('0');

          /* check if input is valid */
          $value = $ten*10+$one;
          if($ten < 0 || $ten > 9 || $one < 0 || $one > 9) {
              return FALSE;
          }

          $base64_str .= chr($value + ord("+"));
      }

      return $base64_str;
  }

  function wawision_pad_verify($pad,$key,$serial)
  {
      $cipher = $this->wawision_decode($pad);
      if($cipher == FALSE) {
          return FALSE;
      }

      $xtea = new XTEA($key);
      $plain = $xtea->decrypt($cipher);

      if($plain == FALSE)
          return FALSE;

      /* check serial */
      if($plain[0] != $serial[0] ||
         $plain[1] != $serial[1] ||
         $plain[2] != $serial[2] ||
         $plain[3] != $serial[3] ||
         $plain[4] != $serial[4] ||
         
         $plain[8]  != $serial[5] ||
         $plain[9]  != $serial[6] ||
         $plain[10] != $serial[7] ||
         $plain[11] != $serial[8] ||
         $plain[12] != $serial[9] ||

         $plain[16] != $serial[10] ||
         $plain[17] != $serial[11] ||
         $plain[18] != $serial[12] ||
         $plain[19] != $serial[13] ||
         $plain[20] != $serial[14]) {
          return FALSE;
      }

      /* check rnd */
      $rnd1  = ord($plain[7]);
      $rnd2  = ord($plain[15]);
      $rnd12 = ord($plain[23]);
      if(($rnd1 + $rnd2) % 256 != $rnd12)
          return FALSE;

      /* extract counter */
      $counter  = ord($plain[5])  << 24;
      $counter += ord($plain[6])  << 16;
      $counter += ord($plain[13]) << 8;
      $counter += ord($plain[14]);

      /* success */
      return $counter;
  }

  function wawision_pad_create($key, $serial, $counter)
  {
      /* 1st block */
      $plain  = $serial[0];
      $plain .= $serial[1];
      $plain .= $serial[2];
      $plain .= $serial[3];
      $plain .= $serial[4];
      $plain .= chr($counter >> 24);
      $plain .= chr($counter >> 16);
      $plain .= chr(rand());

      /* 2nd block */
      $plain .= $serial[5];
      $plain .= $serial[6];
      $plain .= $serial[7];
      $plain .= $serial[8];
      $plain .= $serial[9];
      $plain .= chr($counter >> 8);
      $plain .= chr($counter);
      $plain .= chr(rand());

      /* 3rd block */
      $plain .= $serial[10];
      $plain .= $serial[11];
      $plain .= $serial[12];
      $plain .= $serial[13];
      $plain .= $serial[14];
      $plain .= chr(rand());
      $plain .= chr(rand());
      $plain .= chr((ord($plain[7])+ord($plain[15])) % 256);

      /* encrypt using XTEA CBC */
      $xtea = new XTEA($key);
      $cipher = $xtea->encrypt($plain);

      /* encode using wawision_encode */
      return $this->wawision_encode($cipher);
  }
}


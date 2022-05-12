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
/**
* Login + Accesslayer for OTP Keys
*
* @package    picosafeaes
* @subpackage class.picosafe.php
* @author     WaWision GmbH
* @version    1.0
* ...
*/

class PicosafeLogin {

  var $error_message;
  var $timestamp_valid_password;

	var $user_aes;
	var $user_datablock;
	var $user_counter;

	var $last_valid_counter;

  function __construct($timezone="Europe/Berlin")
  {
    // time zone for server + picosafe aes
    date_default_timezone_set($timezone);

    // 3 minutes
    $this->seconds_valid_password=180;
  }

  // please overload these methods
  function GetUserAES()
  {
		// PLEASE FILL WITH YOUR DATA!!
	
		// 32 signs
    //return "soopu9goBoay9vongooth2ooLu8keed1";
    return $this->user_aes;//"soopu9goBoay9vongooth2ooLu8keed1";
  }

  function GetUserCounter()
  {
		// PLEASE FILL WITH YOUR DATA!!
    return $this->user_counter;//179;
  }

 	function GetUserDatablock()
  {
		// PLEASE FILL WITH YOUR DATA!!
		// 10 signs
    return $this->user_datablock;//"eeng5jo7th";
  }

  function SetUserLastCounter($username,$counter)
  {
		// PLEASE FILL WITH YOUR DATA!!
		// set internal counter from user to new value givn from givenOtp
  }

  function IsPicosafeLocked($username)
  {
		// PLEASE FILL WITH YOUR DATA!!
    return false;
  }


  function GetServerTimestamp()
  {
    // instead of the local time function a server time or something other can be used
		date_default_timezone_set("UTC");
    return time();
  }

	/************************************************/
	// or use set methods to load user values from external

	function SetUserAES($aes)
	{
		$this->user_aes = $aes;
	}

	function SetUserDatablock($datablock)
	{
		$this->user_datablock = $datablock;
	}


	function SetUserCounter($counter)
	{
		$this->user_counter = $counter;
	}


	function GetLastValidCounter()
	{
		return $this->last_valid_counter;
	}


  /************************************************/

  function LoginOTP($givenOtp)//,$aes,$datablock,$counter)//$username)
  {
    $aes = $this->GetUserAES();

 //   $aes  = substr($server_aes, 0, 32);
 //   $data = substr($server_aes, 32, 10);

    $counter = $this->GetUserCounter();
    $data = $this->GetUserDatablock();
    $locked = $this->IsPicosafeLocked();


    // check if device is locked? perhaps the user lost his device ....
    if($locked) 
    {
      $this->error_message = "Picosafe is locked";
      return false;
    }
    $datablock = null;
    $result = $this->ParseOTP($givenOtp,$aes,$datablock);

    //check if is the right aes for the given user
    if($result['datablock']!=$datablock && $datablock!="")
    {
      $this->error_message = "Wrong Key to given username";
      return false;
    }

    // server counter is greater than aes counter
    if($result['counter'] < $counter)
    {
      $this->error_message = "Server counter is greater than aes counter";
      return false;
    }

    // time differences
    $time_diff_abs_between_aes_server = abs($this->GetServerTimestamp() - $result['timestamp']);

    if($time_diff_abs_between_aes_server > $this->seconds_valid_password)
    {
      $this->error_message = "Time difference between server and aes greater than ".$this->seconds_valid_password." seconds";
      return false;
    }

    // Update Counter in server
    //$this->SetUserLastCounter($username,$result['counter']);
		$this->last_valid_counter = $result['counter'];

    return true;
  }

  function ParseOTP($givenOtp,$aes,$datablock = null)
  {
    // base64 Kodierung des Sticks korrigieren
    $givenOtp = rtrim($givenOtp);

    // Sonderzeichen in korrekte Sonderzeichen umwandeln
    // (diese werden vom Stick anders vorgegeben, um von
    // unterschiedl. Tastaturlayouts unabhängig zu werden)
    $cgivenOtp = strlen($givenOtp);
    for($i = 0; $i < $cgivenOtp; $i++) {
      if($givenOtp[$i] == "!")     { $givenOtp[$i] = "/"; }
      elseif($givenOtp[$i] == ".") { $givenOtp[$i] = "="; }
      elseif($givenOtp[$i] == "-") { $givenOtp[$i] = "+"; }
    }

    // erstes Zeichen pruefen ob z oder y
    // abhaengig davon alle y durch z ersetzen und umgekehrt
    $z = $givenOtp[0];
    $crypted = substr($givenOtp, 1);

    if($z == "y" or $z == "Y") {
      $ccrypted = strlen($crypted);
      for($i = 0; $i < $ccrypted; $i++) {
        if    ($crypted[$i] == 'y') { $crypted[$i] = "z"; }
        elseif($crypted[$i] == 'Y') { $crypted[$i] = "Z"; }
        elseif($crypted[$i] == 'z') { $crypted[$i] = "y"; }
        elseif($crypted[$i] == 'Z') { $crypted[$i] = "Y"; }
      }
    }

    if($z == "Y" or $z == "Z") {
      $ccrypted = strlen($crypted);
      for($i = 0; $i < $ccrypted; $i++) {
        if(ctype_upper($crypted[$i])) {
            $crypted[$i] = strtolower($crypted[$i]);
        } else {
            $crypted[$i] = strtoupper($crypted[$i]);
        }
			}
    }

    $crypted = base64_decode($crypted);
    // gegebenes One Time Passwort mit AES entschluesseln
    $td = mcrypt_module_open("rijndael-128", "", "ecb", "");
    $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_DEV_URANDOM);
    mcrypt_generic_init($td, $aes, $iv);
    $plain = mdecrypt_generic($td, $crypted);

    
    // aktueller Zaehlstand
    $i   = substr($plain, 0,1);
    $j   = substr($plain, 1,1);
    $n   = ord($i) + (ord($j) << 8);
    $timestamp = (ord($plain[12]) << 24) + (ord($plain[13]) << 16) + (ord($plain[14]) << 8) + ord($plain[15]);

    // entschluesseltes Passwort aufteilen in
    // und Datenblock
    $plain = substr($plain, 2, 10);
    
    $result['counter']=$n;
    $result['datablock']=$plain;
    $result['timestamp']=$timestamp;
 

		/*	
		echo "<br>"; 
    echo "Nummer: " . $n . "<br>";
    echo "Datenblock: " . $plain . " (" . $data . ") <br>";
    echo "Timestamp: " . $timestamp . "<br>";
    echo "Datetime: " . date("d.m.Y H:i:s",$timestamp) . "<br>";
		*/
    //printf("4 Byte: %x \n", substr($plain,10));
    return $result;
  }

}



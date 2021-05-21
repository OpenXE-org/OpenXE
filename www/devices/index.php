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
// Nur einfache Fehler melden
error_reporting(E_ERROR | E_WARNING | E_PARSE);

include dirname(dirname(__DIR__)) . '/conf/main.conf.php';
include dirname(dirname(__DIR__)) . '/phpwf/plugins/class.mysql.php';
include '../lib/libextern.php';


$config = new Config();
$db = new DB($config->WFdbhost, $config->WFdbname, $config->WFdbuser, $config->WFdbpass,null,$conf->WFdbport);
//$erp = new erpAPI($app);
//$app->erp = $erp;

header('Access-Control-Allow-Origin: *');
$cmd = !empty($_GET['cmd']) ? $_GET['cmd'] : '';
$shouldLog = !empty($db->Select("SELECT `wert` FROM `konfiguration` WHERE `name` = 'adapterbox_logging_active' LIMIT 1"));
$deviceId = !empty($_GET['device']) ? $_GET['device'] : null;
$passwordHash = $_GET['auth'];
$realm = 'DeviceID';
$nonce = uniqid();
$ip = !empty($_SERVER['REMOTE_ADDR']) ? $db->real_escape_string($_SERVER['REMOTE_ADDR']) : '';
$validPass = $db->Select("SELECT `devicekey` FROM `firmendaten` WHERE `devicekey` != '' LIMIT 1");
if ($validPass === '') {
    $validPass = rand() . '' . mt_rand();
}
$guard = new AuthenticationGuard($db, $validPass);
$digest = (string)getDigest();
try {
    $authenticatedDeviceId = $guard->login($passwordHash, $deviceId, $realm, $digest);
} catch (Exception $e) {
    if ($shouldLog) {
        $db->Insert(
            sprintf("
                INSERT INTO `adapterbox_request_log` (`device`, `auth`, `success`, `created_at`, `validpass`, `ip`)
                VALUES ('%s','%s', 0, NOW(),'%s','%s')",
                $db->real_escape_string($deviceId),
                $db->real_escape_string($passwordHash),
                $db->real_escape_string($validPass),
                $ip
            )
        );
    }
    requireLogin($realm, $nonce);
}

include 'statemachine.php';
RunStateMachine($db, $authenticatedDeviceId);


class AuthenticationGuard
{
    /** @var DB */
    private $db;

    /** @var string */
    private $key;

    public function __construct(DB $db, string $key)
    {
        $this->db = $db;
        $this->key = $key;
    }

    /**
     * @param string      $passwordHash
     * @param string|null $deviceId
     * @param string      $realm
     * @param string      $digest
     *
     * @return string
     *
     * @throws Exception
     */
    public function login(
      string $passwordHash,
      ?string $deviceId = null,
      string $realm = '',
      string $digest = ''
    ): string
    {
        // sometimes 000000000 is getting sent instead of the real device serial along while the original hash from the real serial
        // thus, calculating the hash for 000.. will yield a different hash than the sent device hash
        if (!$deviceId || $deviceId == '000000000') {
            $deviceId = $this->loginWithoutDeviceId($passwordHash, $realm, $digest);
        } else {
            $hash = $this->generateHash($this->key, $deviceId);

            if ($hash !== $passwordHash) {
                throw new Exception('Wrong hash');
            }
        }

        return $deviceId;
    }

    /**
     * @var string $passwordHash
     * @var string $realm
     * @var string $digest
     *
     * @return string
     * @throws Exception
     */
    private function loginWithoutDeviceId(string $passwordHash, string $realm, string $digest): string
    {
        $devices = $this->getValidDevices();
        foreach($devices as $deviceId) {
            if ($passwordHash === $this->generateHash($this->key, $deviceId)) {
                return $deviceId;
            }
        }

        $deviceId = $this->loginWithDigest($devices, $realm, $digest);
        if($deviceId === null) {
            throw new Exception('Hash not valid for any device');
        }

        return $deviceId;
    }

  /**
   * @param array  $devices
   * @param string $realm
   * @param string $digest
   *
   * @return string|null
   */
    private function loginWithDigest(array $devices, string $realm, string $digest): ?string
    {
      if ($digest === '') {
          return null;
      }
      $digestParts = digestParse($digest);
      if ($digestParts === false) {
          return null;
      }

      foreach($devices as $deviceId) {
          $validUser = $deviceId;
          // Based on all the info we gathered we can figure out what the response should be
          $A1 = md5("{$validUser}:{$realm}:{$this->key}");
          $A2 = md5("{$_SERVER['REQUEST_METHOD']}:{$digestParts['uri']}");

          $validResponse = md5("{$A1}:{$digestParts['nonce']}:{$digestParts['nc']}:{$digestParts['cnonce']}:{$digestParts['qop']}:{$A2}");
          if ($digestParts['response'] === $validResponse) {
              return $validUser;
          }
      }

      return null;
    }

    /** @return array */
    private function getValidDevices()
    {
        $validDevices = ['000000000'];
        //$validDevices = array('000000000','999999999','123456789');
        $printers = $this->db->SelectFirstCols("
            SELECT `adapterboxseriennummer` 
            FROM `drucker` 
            WHERE `adapterboxseriennummer` != '' 
            AND `aktiv` = '1' 
            AND (`anbindung` = 'adapterbox' OR `anbindung` = 'spooler')
        ");
        $adapterBoxes = $this->db->SelectFirstCols("
            SELECT `seriennummer` as `adapterboxseriennummer` 
            FROM `adapterbox` 
            WHERE `seriennummer` != ''
        ");

        return array_values(array_unique(array_merge($validDevices, $printers, $adapterBoxes)));
    }

    /**
     * @param string $key
     * @param string $deviceId
     *
     * @return string
     */
    private function generateHash($key, $deviceId)
    {
        $hash = '';

        for ($i = 0; $i <= 200; $i++) {
            $hash = sha1($hash . $key . $deviceId);
        }

        return $hash;
    }
}

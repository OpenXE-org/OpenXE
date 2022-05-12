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

use Xentral\Modules\Sipgate\SipgateRequest;
use Xentral\Modules\Sipgate\Exception\UnauthorizedException;

/**
 * Class Sipgate
 *
 * https://developer.sipgate.io/push-api/api-reference/*
 * https://api.sipgate.com/v2/doc#/settings/setSipgateIoUrls
 *
 */
class Sipgate
{
    const MODULE_NAME = 'Sipgate';

    public $javascript = [
        './classes/Modules/Sipgate/www/js/sipgate.js',
    ];

    public $stylesheet = [
        './classes/Modules/Sipgate/www/css/sipgate.css',
    ];

    /**
     * @noinspection PhpUndefinedClassInspection
     * @var Application|app_t
     */
    public $app = null;

    /**
     * Live table for 'Telefonbuch' copied from page Placetel.
     *
     * @param $app
     * @param $name
     * @param $allowedValues
     *
     * @return array|bool
     */
    static function TableSearch(&$app, $name, $allowedValues)
    {
        switch ($name) {
            case "sipgate_list":
                $allowed['laender'] = ['list'];

                $heading = ['Firma', 'Ansprechpartner', 'Telefon', 'Men&uuml;'];
                $width = ['40%', '40%', '15%', '5%'];

                $findcols = ['t.name', 't.ansprechpartner', 't.telefon', 't.did'];
                $searchsql = ['t.name', 't.ansprechpartner', 't.telefon'];
                $doppelteids = true;
                $defaultorder = 1;
                $defaultorderdesc = 0;

                $menu = "<a href=\"#\" onclick=call(\"%value%\",\"index.php?module=placetel&action=call&id=%value%\")><img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/phone.png\" border=\"0\"></a>";

                $where = "";

                $sql = "SELECT SQL_CALC_FOUND_ROWS t.id, t.name, t.ansprechpartner, t.telefon, t.did
      
      FROM   (              
          (
            SELECT 
             a.id, a.name, a.ansprechpartner, a.telefon,concat('1-',a.id) as did FROM adresse a WHERE a.geloescht <> 1 AND a.telefon <> ''
          ) UNION ALL (
            SELECT 
             a.id, a.name, a.ansprechpartner, a.mobil as telefon, concat('2-',a.id) as did FROM adresse a WHERE a.geloescht <> 1 AND a.mobil <> '' 
          )UNION ALL (
          
            SELECT 
             ansp.id, a.name, ansp.name as ansprechpartner, ansp.telefon as telefon, concat('3-',ansp.id) as did FROM adresse a INNER JOIN ansprechpartner ansp ON a.id = ansp.adresse WHERE a.geloescht <> 1 AND ansp.telefon <> '' AND  ansp.geloescht <> 1         
          )UNION ALL (
          
            SELECT 
             ansp.id, a.name, ansp.name as ansprechpartner, ansp.mobil as telefon, concat('4-',ansp.id) as did FROM adresse a INNER JOIN ansprechpartner ansp ON a.id = ansp.adresse WHERE a.geloescht <> 1 AND ansp.mobil <> ''  AND ansp.geloescht <> 1         
          )
          
      )t
      ";

                $count = "SELECT
            SUM(anzahl)
          FROM 
          (
            (
              SELECT 0 as anzahl
            )
            UNION ALL
            (
            SELECT count(a.id) as anzahl FROM adresse a WHERE  a.geloescht <> 1 AND a.telefon <> ''
            )
            UNION ALL
            (
            SELECT count(a.id) as anzahl FROM adresse a   WHERE a.geloescht <> 1 AND a.mobil <> '' 
            )
            UNION ALL
            (
            SELECT  count(ansp.id) as anzahl FROM adresse a INNER JOIN ansprechpartner ansp ON a.id = ansp.adresse WHERE a.geloescht <> 1 AND ansp.telefon <> '' AND ansp.geloescht <> 1         
            )
            UNION ALL
            (
            SELECT  count(ansp.id) as anzahl FROM adresse a INNER JOIN ansprechpartner ansp ON a.id = ansp.adresse WHERE a.geloescht <> 1 AND ansp.mobil <> ''  AND ansp.geloescht <> 1         
            )
            
          ) a  
            ";

                /*
                 * Create here an array with all key -> value
                 * pairs defined above. Note: the keys are
                 * used to filter this array with the allowed
                 * array!
                 */
                $erg = [
                    'allowed'          => $allowed,
                    'heading'          => $heading,
                    'width'            => $width,
                    'findcols'         => $findcols,
                    'searchsql'        => $searchsql,
                    'menu'             => $menu,
                    // 'table' => $table,
                    'where'            => $where,
                    'sql'              => $sql,
                    'count'            => $count,
                    // 'maxrows' => 50,
                    'defaultorderdesc' => $defaultorderdesc,
                    'defaultorder'     => $defaultorder,
                    'doppelteids'      => $doppelteids,
                ];

                /*
                 * $allowed lists all allowed keys so flip
                 * it to use it in array_intersect_key.
                 */
                $allowedValues = (array)$allowedValues;
                $keys = array_flip($allowedValues);
                $erg = array_intersect_key($erg, $keys);

                return $erg;
                break;

            default:
                return false;
        }
    }

    /**
     * Sipgate constructor.
     *
     * @param      $app
     * @param bool $intern
     */
    public function __construct($app, $intern = false)
    {
        $this->app = $app;
        if ($intern) {
            return;
        }


        /*
         * Register all action handlers
         */
        $default = '';
        $this->app->ActionHandlerInit($this);
        $handlers = (array)$this->getActionHandlers();
        foreach ($handlers as $command => $function) {
            if (!$default) {
                $default = $command;
            }
            $this->app->ActionHandler($command, $function);
        }
        if ($default) {
            $this->app->DefaultActionHandler($default);
        }
        $this->app->ActionHandlerListen($app);

        /*
         * Set the action url
         */
        $module = self::MODULE_NAME;
        $module = strtolower($module);
        $url = "index.php?module={$module}";
        $this->app->Tpl->Set('ACTION', $url);

        /*
         * Register all menu entries
         */
        $menus = (array)$this->getMenuEntryList();
        $this->app->erp->MenuEintrag("{$url}&action={$default}", 'zurück');
        foreach ($menus as $action => $menu) {
            $this->app->erp->MenuEintrag("{$url}&action={$action}", $menu);
        }

        $this->app->erp->Headlines(get_class($this));
    }

    /**
     * Register all action handlers.
     *
     * @return array
     */
    protected function getActionHandlers()
    {
        return [
            'list'            => 'SipgateList',
            'call'            => 'SipgateCall',
            'edit'            => 'SipgateEdit',
            //'missed' => 'SipgateMissedCalls',
            'apicheck'        => 'SipgateAPITest',
            'currentIncoming' => 'SipgateIncomingCalls',
        ];
    }

    /**
     * Register all menu entries.
     *
     * @return array
     */
    protected function getMenuEntryList()
    {
        return [
            'list' => 'Adressen',
            // 'missed' => 'verpasste Anrufe',
            'edit' => 'Einstellungen',
        ];
    }

    public function Install()
    {
    }

    /**
     * Uses TableSearch to show the contact list
     */
    public function SipgateList()
    {
        try {
            // If no api user is set, it throws an exception.
            $this->getSipgateRequest();

            $phone = $this->app->Secure->GetPOST("telefon");
            if ($phone != "") {
                $this->SipgateCall($phone);
                $phone = htmlspecialchars($phone);
                $this->app->Tpl->Set("TELEFON", $phone);
            }
        } catch (Exception $e) {
            $this->setTemplateMessage($e->getMessage(), 'error');
        }

        $this->app->YUI->TableSearch('TELEFONBUCH', 'sipgate_list', "show", "", "", basename(__FILE__), __CLASS__);
        $this->app->Tpl->Parse('PAGE', "sipgate_list.tpl");
    }

    /**
     * Start an call via it's contact id or phone number
     *
     * @param $target
     *
     * @return bool
     */
    public function SipgateCall($target = '')
    {
        $internal = true;
        if ($target == "") {
            $internal = false;
            $target = $this->app->Secure->GetGET("target");
        }

        if (!$target) {
            $ida = explode('-', $this->app->Secure->GetGET('id'));
            if (count($ida) == 2) {
                $id = (int)$ida[1];
                switch ($ida[0]) {
                    case '1':
                        $target = $this->app->DB->Select("SELECT telefon FROM adresse WHERE id = '$id' LIMIT 1");
                        break;
                    case '2':
                        $target = $this->app->DB->Select("SELECT mobil FROM adresse WHERE id = '$id' LIMIT 1");
                        break;
                    case '3':
                        $target = $this->app->DB->Select("SELECT telefon FROM ansprechpartner WHERE id = '$id' LIMIT 1");
                        break;
                    case '4':
                        $target = $this->app->DB->Select("SELECT mobil FROM ansprechpartner WHERE id = '$id' LIMIT 1");
                        break;
                }
            }
        }

        // ersetzt ein führendes Plus durch 00
        $target = preg_replace('/\A\+/', '00', $target);
        // entfernt Nicht-Ziffern
        $target = preg_replace('/[^0-9]+/', '', $target);

        // telefon im format 004908125 dann 0 entfernen
        if (substr($target, 0, 2) == "00" && substr($target, 4, 1) == "0") {
            $target = substr($target, 0, 4) . substr($target, 4 + 1);
        }

        $module = strtolower(self::MODULE_NAME);
        $username = $this->app->erp->GetKonfiguration("{$module}_api_user");

        $return = false;
        try {
            do {
                $defaultDevice = '';
                $request = $this->getSipgateRequest();
                $users = $request->getUsers();
                foreach ($users as $user) {
                    if ($user['email'] == $username) {
                        $defaultDevice = $user['defaultDevice'];
                    }
                }
                if (!$defaultDevice) {
                    $defaultDevice = $users[0]['defaultDevice'];
                }

                $request->startCall($defaultDevice, $target);
                $return = true;
            } while (false);
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $this->setTemplateMessage($msg, 'error');
        }

        if ($internal) {
            return $return;
        }

        echo json_encode($return);
        exit;
    }

    /**
     * callback for 'apicheck'
     */
    public function SipgateAPITest()
    {
        // $this->log('notice', 'test api key');
        try {
            $key = $this->app->Secure->GetPOST('key');
            if (!$key || !is_string($key)) {
                // $this->log('notice', 'api key is not valid');
                $this->jsonOutPut([
                    'key'   => 'Passwort fehlt.',
                    'class' => 'api_fail',
                ], 200);
            }

            $request = $this->getSipgateRequest();

            // throws ResponseException if not reachable.
            $request->ping();

            // throws ResponseException if not found or not verified.
            $request->getAccount();

            $this->jsonOutPut([
                'key'   => 'g&uuml;ltig',
                'class' => 'api_success',
            ], 200);
        } catch (Exception $e) {
            // $this->log('error', $e->getMessage());
            // status code 200 to indicate that this method was reached
            $this->jsonOutPut([
                'key'   => $e->getMessage(),
                'class' => 'api_fail',
            ], 200);
        }
        die();
    }

    public function SipgateEdit()
    {
        $type = $this->app->Secure->GetPOST('type');
        if ($type == 'credentials') {
            // save the configuration

            $username = $this->app->Secure->GetPOST('api-user');
            $username = $this->app->DB->real_escape_string($username);
            $this->app->erp->SetKonfigurationValue('sipgate_api_user', $username);

            $password = $this->app->Secure->GetPOST('api-key');
            $password = $this->app->DB->real_escape_string($password);
            $this->app->erp->SetKonfigurationValue('sipgate_api_key', $password);

        } elseif ($type == 'webhook') {

            $webhook = $this->app->Secure->GetPOST('sipgate-webhook');
            if (filter_var($webhook, FILTER_VALIDATE_URL) === false) {
                $webhook = '';
            }
            $webhook = $this->app->DB->real_escape_string($webhook);
            $this->app->erp->SetKonfigurationValue('sipgate_webhook', $webhook);

            $proxy = $this->app->Secure->GetPOST('sipgate-proxy');
            if (filter_var($proxy, FILTER_VALIDATE_URL) === false) {
                $proxy = '';
            }
            $proxy = $this->app->DB->real_escape_string($proxy);
            $this->app->erp->SetKonfigurationValue('sipgate_proxy', $proxy);
            $this->registerWebhookURL();
        }

        // read the configuration
        $username = $this->app->erp->GetKonfiguration("sipgate_api_user");
        $password = $this->app->erp->GetKonfiguration("sipgate_api_key");
        $webhook = $this->app->erp->GetKonfiguration("sipgate_webhook");
        $proxy = $this->app->erp->GetKonfiguration("sipgate_proxy");

        // set the template
        $this->app->Tpl->Set('KEY', htmlspecialchars($password));
        $this->app->Tpl->Set('USER', htmlspecialchars($username));
        $this->app->Tpl->Set('WEBHOOK', htmlspecialchars($webhook));
        $this->app->Tpl->Set('SIPGATE_PROXY', htmlspecialchars($proxy));

        if ($username && $password) {
            $this->listUsers();
        }

        $this->app->Tpl->Parse('PAGE', "sipgate_edit.tpl");
    }

    /**
     *
     */
    private function registerWebhookURL()
    {
        $url = $this->app->Secure->GetPOST('sipgate-webhook');
        $proxy = $this->app->Secure->GetPOST('sipgate-proxy');

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            $url = '';
        }

        if (filter_var($proxy, FILTER_VALIDATE_URL)) {
            $url = $proxy;
        }

        try {
            $this->getSipgateRequest()->registerWebHookUrl($url);
            $this->setTemplateMessage('Der Webhook ist eingerichtet.', 'info');
        } catch (Exception $e) {
            $msg = $e->getMessage();
            $this->setTemplateMessage($msg, 'error');
        }
    }

    /**
     * List all users on the right side of settings page.
     */
    private function listUsers()
    {
        try {
            $request = $this->getSipgateRequest();
            $users = $request->getUsers();
            $i = 0;
            foreach ($users as $user) {
                $i = $i + 1;
                $row = [
                    $i,
                    // $user['id'],
                    trim($user['firstname'] . ' ' . $user['lastname']),
                    $user['email'],
                    // $user['defaultDevice'],
                ];

                $row = '<tr><td>' . implode('</td><td>', $row) . '</td></tr>';
                $this->app->Tpl->Add('USERS', $row);
            }
        } catch (Exception $e) {
            $msg = $this->createTemplateMessage($e->getMessage(), 'error');
            $this->app->Tpl->Set('USERS_ERROR', $msg);
        }
    }

    /**
     * Get the SipgateRequest object,
     * throws exception if no credentials are found.
     *
     * @return SipgateRequest
     * @throws Exception
     */
    private function getSipgateRequest()
    {
        $module = strtolower(self::MODULE_NAME);
        $username = $this->app->erp->GetKonfiguration("{$module}_api_user");
        $password = $this->app->erp->GetKonfiguration("{$module}_api_key");

        if (!$username || !is_string($username) || !$password || !is_string($password)) {
            throw new UnauthorizedException('Kein API Zugang gefunden');
        }

        return new SipgateRequest($username, $password);
    }

    /**
     * Set the template message.
     *
     * @param        $msg
     * @param string $class
     * @param bool   $add
     *
     * @return bool
     */
    private final function setTemplateMessage($msg, $class = '', $add = true)
    {
        $msg = $this->createTemplateMessage($msg, $class);
        if (!$msg) {
            return false;
        }

        if ($add) {
            $this->app->Tpl->Add('MESSAGE', $msg);

            return true;
        }

        $this->app->Tpl->Set('MESSAGE', $msg);

        return true;
    }

    /**
     * Create a new template message nested in a div with class property.
     *
     * @param        $msg
     * @param string $class
     *
     * @return string
     */
    private final function createTemplateMessage($msg, $class = '')
    {
        if (!is_string($msg) || empty($msg)) {
            return '';
        }
        /*
         * convert to array to simplify validation/filtering
         */
        $class = (array)$class;
        $class = array_filter($class);
        $class = array_filter($class, 'is_string');
        $class = implode(' ', $class);
        $class = trim($class);

        $msg = sprintf('<div class="%s">%s</div>', $class, $msg);

        return $msg;
    }

    /**
     * Display the data json decoded and set the required http headers.
     *
     * Dies as last statement.
     *
     * @param array $data
     * @param int   $status
     */
    private final function jsonOutPut($data, $status = 200)
    {
        $status = intval($status);
        if ($status < 200 || $status > 600) {
            $status = 400;
        }
        $data = json_encode($data);
        if (json_last_error()) {
            $status = 500;
            $data = ['error' => json_last_error_msg()];
            $data = json_encode($data);
        }

        http_response_code($status);
        header('Content-Type: application/json');
        header('Accept: application/json');
        echo $data;
        die();
    }
}

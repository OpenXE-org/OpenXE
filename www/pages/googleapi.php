<?php
declare(strict_types=1);

use Xentral\Components\Database\Database;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Session\Session;
use Xentral\Components\Http\Session\SessionHandler;
use Xentral\Modules\GoogleApi\Data\GoogleCredentialsData;
use Xentral\Modules\GoogleApi\Exception\CsrfViolationException;
use Xentral\Modules\GoogleApi\Exception\GoogleAccountNotFoundException;
use Xentral\Modules\GoogleApi\Exception\UserConsentException;
use Xentral\Modules\GoogleApi\GoogleScope;
use Xentral\Modules\GoogleApi\Service\GoogleAccountGateway;
use Xentral\Modules\GoogleApi\Service\GoogleAccountService;
use Xentral\Modules\GoogleApi\Service\GoogleAuthorizationService;
use Xentral\Modules\GoogleApi\Service\GoogleCredentialsService;
use Xentral\Modules\GoogleCalendar\Client\GoogleCalendarClientFactory;
use Xentral\Modules\GoogleCalendar\Service\GoogleCalendarSynchronizer;

class GoogleApi
{
    /** @var string MODULE_NAME */
    public const MODULE_NAME = 'GoogleApi';

    /** @var string GOOGLE_API_TESTURL_PRINT */
    //private const TESTURL_PRINT = 'https://www.google.com/cloudprint/search';

    /** @var string GOOGLE_API_TESTURL_CALENDAR */
    //private const TESTURL_CALENDAR = 'https://www.googleapis.com/calendar/v3/users/me/calendarList';

    /** @var erpooSystem $app */
    private $app;

    /** @var GoogleAccountGateway $gateway */
    private $gateway;

    /** @var GoogleAccountService $service */
    private $service;

    /** @var GoogleCredentialsService $credentialsService */
    private $credentialsService;

    /** @var GoogleAuthorizationService $auh */
    private $auth;

    /**
     *
     * @param erpooSystem $app
     * @param bool        $intern
     */
    public function __construct($app, $intern = false)
    {
        $this->app = $app;
        if ($intern) {
            return;
        }
        $this->gateway = $this->app->Container->get('GoogleAccountGateway');
        $this->service = $this->app->Container->get('GoogleAccountService');
        $this->credentialsService = $this->app->Container->get('GoogleCredentialsService');
        $this->auth = $this->app->Container->get('GoogleAuthorizationService');

        $this->app->ActionHandlerInit($this);

        // ab hier alle Action Handler definieren die das Modul hat
        $this->app->ActionHandler('list', 'GoogleApiEdit');
        $this->app->ActionHandler('edit', 'GoogleApiEdit');
        $this->app->ActionHandler('print', 'GoogleApiPrint');
        $this->app->ActionHandler('redirect', 'GoogleApiRedirect');
        $this->app->ActionHandler('ajaxprinters', 'GoogleApiAjaxPrinters');

        $this->app->ActionHandlerListen($app);
    }

    /**
     * @param erpooSystem $app
     * @param string      $name
     * @param array       $erlaubtevars
     *
     * @return bool
     */
    public static function TableSearch(&$app, $name, $erlaubtevars)
    {
        // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
        switch ($name) {
            case 'googleapi_list':
                $allowed['googleapi'] = ['list'];

                $heading = [
                    'Name (intern)',
                    'Beschreibung',
                    'Art',
                    'Aktiv',
                    'Client ID',
                    'Client Schlüssel',
                    'letzte Registrierung',
                    'Menü',
                ];
                $width = ['10%', '10%', '10%', '5%', '20%', '10%', '10%', '1%'];
                $findcols = [
                    'g.id_name',
                    'g.description',
                    'g.type',
                    'g.active',
                    'g.user',
                    'g.password',
                    'g.last_auth',
                    'g.id',
                ];
                $searchsql = [
                    'g.id_name',
                    'g.description',
                    'g.type',
                    'g.active',
                    'g.user',
                    'g.password',
                    'g.last_auth',
                ];
                $defaultorder = 1;
                $defaultorderdesc = 0;

                $menu = '<table cellpadding=0 cellspacing=0>';
                $menu .= '<tr>';
                $menu .= '<td nowrap>';
                $menu .= '<a href="?module=googleapi&action=edit&id=%value%" class="googleapi-edit" data-googleapi-id="%value%">';
                $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
                $menu .= '</a>&nbsp;';
                $menu .= '<a href="javascript:;" class="googleapi-delete" data-googleapi-id="%value%">';
                $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
                $menu .= '</a>';
                $menu .= '</td>';
                $menu .= '</tr>';
                $menu .= '</table>';

                $where = ' g.id > 0';

                $sql = "SELECT SQL_CALC_FOUND_ROWS g.id, g.id_name, g.description,
                           (CASE g.type
                                WHEN 'print' THEN 'Google Cloud Print'
                                WHEN 'mail' THEN 'Google Mail'
                                WHEN 'calendar' THEN 'Google Calendar'
                                ELSE ''
                            END) AS `type`,
                           IF(g.active = 0, 'nein', 'ja'),
                           g.user, g.password, g.last_auth, g.id
                        FROM googleapi AS g";

                break;
        }

        $erg = false;

        foreach ($erlaubtevars as $k => $v) {
            if (isset($$v)) {
                $erg[$v] = $$v;
            }
        }

        return $erg;
    }

    /**
     * @return void
     */
    public function Install()
    {
        //$this->app->erp->CheckTable('googleapi');
        //$this->app->erp->CheckColumn('id', 'int(11)', 'googleapi', 'NOT NULL AUTO_INCREMENT');
        //$this->app->erp->CheckColumn('id_name', 'varchar(255)', 'googleapi', "NOT NULL DEFAULT ''");
        //$this->app->erp->CheckColumn('description', 'varchar(255)', 'googleapi', 'NULL');
        //$this->app->erp->CheckColumn('type', 'varchar(255)', 'googleapi', 'NULL');
        //$this->app->erp->CheckColumn('active', 'tinyint', 'googleapi', 'NOT NULL DEFAULT 0');
        //$this->app->erp->CheckColumn('user', 'varchar(255)', 'googleapi', 'NULL');
        //$this->app->erp->CheckColumn('password', 'varchar(255)', 'googleapi', 'NULL');
        //$this->app->erp->CheckColumn('redirect_uri', 'varchar(255)', 'googleapi', 'NULL');
        //$this->app->erp->CheckColumn('token', 'varchar(255)', 'googleapi', 'NULL');
        //$this->app->erp->CheckColumn('token_expires', 'datetime', 'googleapi', 'NULL');
        //$this->app->erp->CheckColumn('refresh_token', 'varchar(255)', 'googleapi', 'NULL');
        //$this->app->erp->CheckColumn('last_auth', 'datetime', 'googleapi', 'NULL');

        //$this->app->erp->CheckTable('googleapi_user');
        //$this->app->erp->CheckColumn('id', 'int(11)', 'googleapi_user', 'NOT NULL AUTO_INCREMENT');
        //$this->app->erp->CheckColumn('user_id', 'int(11)', 'googleapi_user', 'NOT NULL DEFAULT 0');
        //$this->app->erp->CheckColumn('googleapi_id_name', 'varchar(255)', 'googleapi_user', "NOT NULL DEFAULT ''");
        //$this->app->erp->CheckColumn('active', 'tinyint', 'googleapi_user', 'NOT NULL DEFAULT 0');
        //$this->app->erp->CheckColumn('auto_sync', 'tinyint', 'googleapi_user', 'NOT NULL DEFAULT 0');
        //$this->app->erp->CheckColumn('identifier', 'varchar(255)', 'googleapi_user', 'NULL');
        //$this->app->erp->CheckColumn('refresh_token', 'varchar(255)', 'googleapi_user', 'NULL');
        //$this->app->erp->CheckColumn('access_token', 'varchar(255)', 'googleapi_user', 'NULL');
        //$this->app->erp->CheckColumn('token_expires', 'datetime', 'googleapi_user', 'NULL');
        //$this->app->erp->CheckIndex('googleapi_user', 'user_id');

        $this->app->erp->CheckTable('google_account');
        $this->app->erp->CheckColumn('id', 'int(11)', 'google_account', 'UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('user_id', 'int(11)', 'google_account', 'UNSIGNED NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('refresh_token', 'varchar(255)', 'google_account');
        $this->app->erp->CheckColumn('identifier', 'varchar(255)', 'google_account');
        $this->app->erp->CheckIndex('google_account', 'user_id');

        $this->app->erp->CheckTable('google_account_scope');
        $this->app->erp->CheckColumn('id', 'int(11)', 'google_account_scope', 'UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('google_account_id', 'int(11)', 'google_account_scope', 'UNSIGNED NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('scope', 'varchar(255)', 'google_account_scope');
        $this->app->erp->CheckIndex('google_account_scope', 'google_account_id');

        $this->app->erp->CheckTable('google_account_property');
        $this->app->erp->CheckColumn('id', 'int(11)', 'google_account_property', 'UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('google_account_id', 'int(11)', 'google_account_property', 'UNSIGNED NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('varname', 'varchar(64)', 'google_account_property');
        $this->app->erp->CheckColumn('value', 'varchar(255)', 'google_account_property');
        $this->app->erp->CheckIndex('google_account_data', 'google_account_id');

        $this->app->erp->CheckTable('google_access_token');
        $this->app->erp->CheckColumn('id', 'int(11)', 'google_access_token', 'UNSIGNED NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('google_account_id', 'int(11)', 'google_access_token', 'UNSIGNED NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('token', 'varchar(255)', 'google_access_token');
        $this->app->erp->CheckColumn('expires', 'datetime', 'google_access_token');
        $this->app->erp->CheckIndex('google_access_token', 'google_account_id');

        $this->app->erp->CheckTable('googleapi_calendar_sync');
        $this->app->erp->CheckColumn('id', 'int(11)', 'googleapi_calendar_sync', 'NOT NULL AUTO_INCREMENT');
        $this->app->erp->CheckColumn('event_id', 'int(11)', 'googleapi_calendar_sync', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('foreign_id', 'varchar(255)', 'googleapi_calendar_sync', "NOT NULL DEFAULT ''");
        $this->app->erp->CheckColumn('owner', 'int(11)', 'googleapi_calendar_sync', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('from_google', 'tinyint', 'googleapi_calendar_sync', 'NOT NULL DEFAULT 0');
        $this->app->erp->CheckColumn('event_date', 'datetime', 'googleapi_calendar_sync', 'NULL');
        $this->app->erp->CheckColumn('html_link', 'varchar(255)', 'googleapi_calendar_sync', 'NULL');
        $this->app->erp->CheckIndex('googleapi_calendar_sync', 'event_id');

        $this->app->erp->RegisterHook('kalender_event_hook','googleapi', 'EventHook');
        $this->app->erp->CheckProzessstarter('Google Kalender Import', 'uhrzeit', '', '2017-01-01 00:00:00', 'cronjob', 'google_calendar_import', 1);

        $this->migrate();
    }

    /**
     * @param string $eventId
     * @param string $actionType
     */
    public function EventHook($eventId, $actionType)
    {
        try {
            /** @var GoogleAccountGateway $gateway */
            $gateway = $this->app->Container->get('GoogleAccountGateway');
            $userId = (int)$this->app->User->GetID();
            /** @var GoogleCalendarClientFactory $factory */
            $factory = $this->app->Container->get('GoogleCalendarClientFactory');
            $client = $factory->createClient($userId);
            /** @var GoogleCalendarSynchronizer $synchronizer */
            $synchronizer = $this->app->Container->get('GoogleCalendarSynchronizer');
            $synchronizer->calendarEventHook($client, $eventId, $actionType);
        } catch (Exception $e) {
            return;
        }
    }

   /**
    * @return void
    */
   public function GoogleApiList(): void
   {
       $this->createMenu();
       $this->app->erp->Headlines('Google APIs');
       $this->existTables();

       $this->app->YUI->TableSearch(
           'TAB1',
           'googleapi_list',
           'show',
           '',
           '',
           basename(__FILE__),
           __CLASS__
       );
       $this->app->Tpl->Parse('PAGE', 'googleapi_list.tpl');
   }

    /**
     * @return void
     */
    public function GoogleApiEdit(): void
    {
        $this->existTables();

        /** @var Request $request */
        $request = $this->app->Container->get('Request');
        if ($request->post->has('save')) {
            $this->saveCredentials($request);
            $this->app->Tpl->Add(
                'MESSAGE',
                '<div class="info">Einstellungen wurden gespeichert.</div>'
            );
        }

        if ($request->post->has('authorize_mail')) {
            $this->authorize(GoogleScope::MAIL);
            return;
        }

        if ($request->post->has('authorize_cal')) {
            $this->authorize(GoogleScope::CALENDAR);
            return;
        }

        if ($request->post->has('unauthorize')) {
            $this->unauthorize();
        }

        $credentials = $this->credentialsService->getCredentials();
        $redirectUri = $credentials->getRedirectUri();
        if (empty($redirectUri)) {
            $redirectUri = $this->auth->getDefaultRedirectUri();
        }
        $this->app->Tpl->Set('CLIENT_ID',$credentials->getClientId());
        $this->app->Tpl->Set('SECRET', $credentials->getClientSecret());
        $this->app->Tpl->Set('REDIRECT_URI', $redirectUri);

        $this->app->erp->Headlines('Google Zugangsdaten');
        $this->createMenu();
        $this->app->Tpl->Parse('PAGE', 'googleapi_edit.tpl');
    }

    /**
     * @return void
     */
    public function GoogleApiPrint(): void
    {
        /** @var Request $request */
        $request = $this->app->Container->get('Request');
        if ($request->post->has('authorize_cloudprint')) {
            /** @var Session $session */
            $session = $this->app->Container->get('Session');
            $redirect = $this->auth->requestScopeAuthorization(
                $session,
                [GoogleScope::CLOUDPRINT],
                'index.php?module=googleapi&action=print'
            );
            SessionHandler::commitSession($session);
            $redirect->send();
            $this->app->ExitXentral();
        }

        if ($request->post->has('unauthorize')) {
            $this->unauthorize();
        }

        $this->app->Tpl->Add(
            'MESSAGE',
            '<div class="warning">
              Hinweis: Google wird den Cloud Print Dienst am 31.12.2020 abschalten.</div>'
        );

        $this->app->erp->Headlines('Google Cloud Print');
        $this->createMenu();
        $this->app->Tpl->Parse('PAGE', 'googleapi_print.tpl');
    }

    /**
     * After user confirmed access to Google API manually, Google API
     * redirects user to this action sending authentication code and API scope
     * as GET parameters.
     */
    public function GoogleApiRedirect()
    {
        /** @var Session $session */
        $session = $this->app->Container->get('Session');
        /** @var Request $request */
        $request = $this->app->Container->get('Request');
        $userId = (int)$this->app->User->GetID();

        try {
            return $this->auth->authorizationCallback($session, $request, $userId);
        } catch (CsrfViolationException $e) {
            $message = '<div class="error">CSRF Token ungültig. Bitte versuchen Sie es erneut.</div>';
        } catch (UserConsentException $e) {
            $message = '<div class="warning">Authorisierung für Google durch den Benutzer abgebrochen.</div>';
        } catch (Exception $e) {
            $message = sprintf('<div class="error">Fehler:<br>%s</div>', $e->getMessage());
        }

        $this->app->Tpl->Set('MESSAGE', $message);
        $this->app->Tpl->Parse('PAGE', 'googleapi_redirect.tpl');
    }


    /**
     * Action for the printer setup gui to get printer options from api connection
     *
     * @return void
     */
    public function GoogleApiAjaxPrinters()
    {
        $data = [];
        $apiName = $this->app->DB->real_escape_string(trim($this->app->Secure->GetPOST('api_name')));
        if(!empty($apiName)) {
            $id = $this->getGoogleApiByName($apiName);
            $token = $this->getAccessToken($id);
            $authHeader = sprintf('Authorization: Bearer %s', $token);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_HTTPHEADER, [$authHeader]);
            curl_setopt( $ch, CURLOPT_FOLLOWLOCATION,true);
            curl_setopt( $ch, CURLOPT_HEADER,false);
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER,true);
            curl_setopt( $ch, CURLOPT_HTTPAUTH,CURLAUTH_ANY);
            curl_setopt( $ch, CURLOPT_URL, 'https://www.google.com/cloudprint/search');
            curl_setopt( $ch, CURLOPT_POST, true );
            curl_setopt ( $ch, CURLOPT_POSTFIELDS, ['printerid' => $id]);

            $response = curl_exec($ch);
            $result = json_decode($response, true);

            $printers = $result['printers'];
            if(!empty($printers)) {
                foreach ($printers as $item) {
                    $data[$item['id']] = sprintf('%s:%s', $item['displayName'], $item['connectionStatus']);
                }
            }
        }

        $responseData = json_encode($data);
        header('Content-Type: application/json');
        echo $responseData;
        $this->app->ExitXentral();
    }

    /**
     * Automatically calls getAccessTokenByRederect if needed.
     *
     * @param int  $id
     *
     * @return string
     */
    public function getAccessToken($id)
    {
        $id = (int)$id;
        $sql = sprintf(
            'SELECT g.id, g.token, TIMESTAMPDIFF(SECOND, NOW(), g.token_expires) AS expires_in
                    FROM googleapi AS g 
                    WHERE g.id = %s;',
            $id
        );

        $data = $this->app->DB->SelectRow($sql);
        if (empty($data) || $data['token'] === '') {
            return '';
        }

        if ((int)$data['expires_in'] < 1) {
            return $this->getAccessTokenByRefresh($id);
        }

        return $data['token'];
    }

    /**
     * Always requests new access token
     * consider using getAccessToken instead
     *
     * @param int $id
     *
     * @return string
     */
    public function getAccessTokenByRefresh($id)
    {
        $id = (int)$id;
        $sql = sprintf(
            'SELECT g.id, g.refresh_token, g.user, g.password FROM googleapi AS g WHERE g.id = %s',
            $id
        );
        $data = $this->app->DB->SelectRow($sql);
        if (empty($data) || (int)$data['id'] !== $id || $data['refresh_token'] === '') {
            return '';
        }

        $postData = [
            'refresh_token' => $data['refresh_token'],
            'client_id'     => $data['user'],
            'client_secret' => $data['password'],
            'grant_type'    => 'refresh_token',
        ];
        $response = $this->sendGoogleCurl(self::GOOGLE_API_URL_TOKEN_REFRESH, $postData);
        if (isset($response['error'])) {
            return '';
        }

        $token = $response['access_token'];
        $expires = (int)$response['expires_in'] -5;

        if(empty($token) || $expires < 0) {
            return '';
        }

        $updateSql = sprintf(
            "UPDATE googleapi 
                    SET token = '%s', token_expires = DATE_ADD(NOW(),INTERVAL %s SECOND)
                    WHERE id = %s",
            $token,
            $expires,
            $id
        );
        $this->app->DB->Update($updateSql);

        return $token;
    }

    /**
     * @param $name
     *
     * @return int
     */
    public function getGoogleApiByName($name)
    {
        $sql = sprintf(
            "SELECT g.id 
                    FROM googleapi AS g 
                    WHERE g.id_name = '%s' AND g.active = 1",
            $name
        );
        $id = (int)$this->app->DB->Select($sql);
        if ($id < 1) {
            return 0;
        }

        return $id;
    }

    /**
     * @param int|string $id
     *
     * @return array
     */
    public function getGoogleApiById($id)
    {
        $id = (int)$id;
        if ($id < 1) {
            return [];
        }

        $sql = sprintf(
            'SELECT g.id, g.id_name, g.description, g.type, g.active, g.user, g.password, g.redirect_uri, g.refresh_token, g.last_auth,
                            g.token, g.token_expires, TIMESTAMPDIFF(SECOND, NOW(), g.token_expires) as expires_in
                    FROM googleapi AS g WHERE g.id = %s ORDER BY g.active DESC LIMIT 1',
            $id
        );
        $result = $this->app->DB->SelectRow($sql);
        if(empty($result)) {
            return [];
        }

        return $result;
    }

    /**
     * @param int|string $id
     *
     * @return array
     */
    public function getGoogleMailApiById($id)
    {
        $id = (int)$id;
        if ($id < 1) {
            return [];
        }

        $sql = sprintf(
            'SELECT g.id, g.id_name, g.description, g.type, g.active, g.user, g.password, g.redirect_uri,
                        gu.refresh_token, g.last_auth,
                        gu.access_token, gu.token_expires, TIMESTAMPDIFF(SECOND, NOW(), gu.token_expires) as expires_in
                    FROM googleapi AS g
                    JOIN googleapi_user AS gu ON gu.googleapi_id_name = g.id_name
                    WHERE g.id = %s ORDER BY g.active DESC LIMIT 1',
            $id
        );
        $result = $this->app->DB->SelectRow($sql);
        if(empty($result)) {
            return [];
        }

        return $result;
    }

    /**
     * @param Request $request
     * @param string  $messageTarget
     *
     * @return void
     */
    public function saveCredentials(Request $request, $messageTarget = 'MESSAGE'): void
    {
        $client = $request->post->get('client_id', null);
        $secret = $request->post->get('secret', null);
        $redirectUri = $request->post->get('redirect_uri', null);

        $credentials = new GoogleCredentialsData($client, $secret, $redirectUri);
        $this->credentialsService->saveCredentials($credentials);
    }

    protected function existTables()
    {
        /** @var Database $db */
        $db = $this->app->Container->get('Database');
        try {
            $result = $db->fetchRow('SELECT `id` FROM `google_account` LIMIT 1');
            $result = $db->fetchRow('SELECT `id` FROM `google_account_property` LIMIT 1');
            $result = $db->fetchRow('SELECT `id` FROM `google_account_scope` LIMIT 1');
            $result = $db->fetchRow('SELECT `id` FROM `google_access_token` LIMIT 1');
            $result = $db->fetchRow('SELECT `id` FROM `googleapi_calendar_sync` LIMIT 1');
        } catch (Exception $e) {
            $this->Install();
        }
    }

    /**
     * If data in the old Googleapi structure exists, migrate to new structure and cancel all
     * client connections
     */
    protected function migrate()
    {
        /** @var Database $db */
        $db = $this->app->Container->get('Database');

        // Try to find api account and take the first active one and save it in the new Structure
        try {
            $credentials = $db->fetchRow(
                'SELECT g.user, g.password, g.redirect_uri FROM `googleapi` AS `g` WHERE g.active = 1 LIMIT 1'
            );
            /** @var GoogleCredentialsService $credentialsService */
            $credentialsService = $this->app->Container->get('GoogleCredentialsService');
            if (!empty($credentials) && !$credentialsService->existCredentials()) {
                $credentialsService->saveCredentials(
                    new GoogleCredentialsData(
                        $credentials['user'],
                        $credentials['password'],
                        $credentials['redirect_uri']
                    )
                );
            }
        } catch (Throwable $e) {
        }

        //Find googleapi_user entries
        try {
            $rows = $db->fetchAll("SELECT u.id, u.refresh_token 
                                         FROM `googleapi_user` AS `u` 
                                         WHERE u.refresh_token != ''");
            $db->perform("UPDATE `googleapi_user` SET refresh_token = '' WHERE 1");
        } catch (Throwable $e) {
            $rows = [];
        }

        //quit the connections
        /** @var GoogleAuthorizationService $authService */
        $authService = $this->app->Container->get('GoogleAuthorizationService');
        foreach ($rows as $row) {
            try {
                if (!empty($row['refresh_token'])) {
                    $authService->revokeToken($row['refresh_token']);
                }
            } catch (Throwable $e) {
                continue;
            }
        }
    }

    /**
     * Sends initial authentification request to Google.
     *
     * Request is GET method and redirects the xentral user to Google to
     * manually allow access to Google API.
     * When user allowed access Google will redirect to xentral (see GoogleApiRedirect)
     *
     * @param string $clientId
     * @param string $type
     * @param string $redirectUri
     *
     * @return void
     */
    protected function requestGoogleAuthCode($clientId, $type, $redirectUri = '')
    {
        if (empty($redirectUri)) {
            $redirectUri = $this->getDefaultRedirectUri();
        }

        switch ($type) {
            case 'print':
                $scope = self::GOOGLE_API_SCOPE_PRINT;

                break;

            case 'mail':
                $scope = self::GOOGLE_API_SCOPE_MAIL;

                break;

            case 'calendar':
                $scope = self::GOOGLE_API_SCOPE_CALENDAR;

                break;

            default:
                $scope = '';
        }

        $params = [
            'client_id' => $clientId,
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $scope,
            'access_type' => 'offline',
        ];

        $redirect = sprintf('location: %s?%s', self::GOOGLE_API_URL_AUTH, http_build_query($params));
        header($redirect);
        $this->app->ExitXentral();
    }

    /**
     * @param $id
     *
     * @return bool
     */
    protected function closeApiConnection($id)
    {
        $data = $this->getGoogleApiById($id);
        if (empty($data)) {
            return false;
        }

        if ($data['type'] === 'mail') {
            $users = $this->gateway->getApiUsersByAccount($data['id_name']);
            if (count($users) > 0) {
                $this->auth->removeUserToken($users[0]);
            }

            return true;
        }

        if (!empty($data['token'])) {
            $this->revokeToken($data['token']);
            $updateSql = sprintf(
                'UPDATE googleapi
                        SET token = NULL, token_expires = NULL
                        WHERE id = %s',
                $id
            );
            $this->app->DB->Update($updateSql);
        }

        if (!empty($data['refresh_token'])) {
            $this->revokeToken($data['refresh_token']);
            $updateSql = sprintf(
                'UPDATE googleapi
                        SET token = NULL, token_expires = NULL, refresh_token = NULL
                        WHERE id = %s',
                $id
            );
            $this->app->DB->Update($updateSql);
        }

        if(!empty($data['token']) || !empty($data['refresh_token'])) {
            $redirect = sprintf('location: ?module=googleapi&action=edit&id=%s', $id);
            header($redirect);
            $this->app->ExitXentral();
        }

        return false;
    }

    /**
     * @return void
     */
    protected function authorize(string $scope): void
    {
        /** @var Session $session */
        $session = $this->app->Container->get('Session');
        $redirect = $this->auth->requestScopeAuthorization(
            $session,
            [$scope],
            'index.php?module=googleapi&action=edit'
        );
        SessionHandler::commitSession($session);
        $redirect->send();
        $this->app->ExitXentral();
    }

    /**
     * @return void
     */
    protected function unauthorize(): void
    {
        try {
            $account = $this->gateway->getAccountByUser((int)$this->app->User->GetID());
        } catch (GoogleAccountNotFoundException $e) {
            $this->app->Tpl->Add('MESSAGE', '<div class="info">Keine Verbindung vorhanden.</div>');
            return;
        }
        $this->auth->revokeAuthorization($account);
        $this->app->Tpl->Add('MESSAGE', '<div class="info">Verbindung zu Google beendet.</div>');
    }

    /**
     * @param string $token
     *
     * @return bool
     */
    protected function revokeToken($token)
    {
        if (empty($token)) {
            return false;
        }

        $url = sprintf('%s?token=%s', self::GOOGLE_API_URL_TOKEN_REVOKE, $token);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ((int)$info['http_code'] === 200) {
            return true;
        }

        return false;
    }

    /**
     * @param int    $id
     * @param string $messageTarget
     *
     * @return bool
     */
    protected function testApiConnection($id, $messageTarget = '')
    {
        $id = (int)$id;
        $api = $this->gateway->getApiAccount();

        if ($api !== null && $api->getType() === 'mail') {
            $users = $this->gateway->getApiUsersByAccount($api->getIdName());
            $token = '';
            if (count($users) > 0) {
                $token = $users[0]->getAccessToken();
            }
        } else {
            $token = $this->getAccessToken($id);
        }

        if ($token === '') {
            $this->app->Tpl->Add(
                $messageTarget,
                '<div class="error">Kein gültiges Access Token vorhanden</div>'
            );

            return false;
        }

        $type = $this->app->DB->Select(sprintf("SELECT g.type FROM googleapi AS g WHERE g.id = '%s';", $id));
        switch ($type) {
            case 'print':
                $testurl = self::GOOGLE_API_TESTURL_PRINT;
                break;

            case 'mail':
                $this->app->Tpl->Add(
                    $messageTarget,
                    '<div class="info">Die Verbindung zu Google Mail kann hier nicht getestet werden. Versenden Sie stattdessen eine Test-Email.</div>'
                );
                return true;
                break;

            case 'calendar':
                $testurl = self::GOOGLE_API_TESTURL_CALENDAR;
                break;

            default:
                $testurl = '';
        }

        $authHeader = sprintf('Authorization: Bearer %s', $token);
        $ch = curl_init($testurl);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [$authHeader]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);

        if ((int)$info['http_code'] !== 200) {
            $error = sprintf('Test fehlgeschlagen: ERROR %s %s', $info['http_code'], $response['error']);
            $this->app->Tpl->Add(
                $messageTarget,
                sprintf('<div class="error">%s</div>', $error)
            );

            return false;
        }

        if ($type === 'print' &&
            (!isset($info['content_type']) || explode(';', $info['content_type'])[0] !== 'text/plain')
        ) {
            $error = 'Test fehlgeschlagen: Fehlerhafte Antwort vom Server';
            $this->app->Tpl->Add(
                $messageTarget,
                sprintf('<div class="error">%s</div>', $error)
            );

            return false;
        }

        if ($type === 'mail' &&
            (!isset($info['content_type']) || explode(';', $info['content_type'])[0] !== 'text/plain')
        ) {
            $error = 'Test fehlgeschlagen: Fehlerhafte Antwort vom Server';
            $this->app->Tpl->Add(
                $messageTarget,
                sprintf('<div class="error">%s</div>', $error)
            );

            return false;
        }

        if ($type === 'calendar' &&
            (!isset($info['content_type']) || explode(';', $info['content_type'])[0] !== 'application/json')
        ) {
            $error = 'Test fehlgeschlagen: Fehlerhafte Antwort vom Server';
            $this->app->Tpl->Add(
                $messageTarget,
                sprintf('<div class="error">%s</div>', $error)
            );

            return false;
        }

        $this->app->Tpl->Add(
            $messageTarget,
            '<div class="info">Test der Verbindung war erfolgreich.</div>'
        );

        return true;
    }

    /**
     * @param string $url
     * @param array  $postData
     *
     * @return array
     */
    protected function sendGoogleCurl($url, $postData = [])
    {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION,true);
        curl_setopt( $ch, CURLOPT_HEADER,false);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER,true);
        //curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt( $ch, CURLOPT_HTTPAUTH,CURLAUTH_ANY);
        curl_setopt( $ch, CURLOPT_URL, $url);
        curl_setopt( $ch, CURLOPT_POST, true );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $postData);

        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        $result = json_decode($response, true);
        if ($response === false) {
            $result = ['error' => 'error in curl request'];
        }

        if((int)$info['http_code'] !== 200){
            $result['http_code'] = $info['http_code'];
        }

        return $result;
    }

    /**
     * @return void
     */
    protected function createMenu()
    {
        $this->app->erp->MenuEintrag('index.php?module=googleapi&action=list',
            'Zur&uuml;ck zur &Uuml;bersicht');
        $this->app->erp->MenuEintrag('index.php?module=googleapi&action=list', 'Übersicht');
        $this->app->erp->MenuEintrag('index.php?module=googleapi&action=print', 'Google Cloud Print');
    }
}

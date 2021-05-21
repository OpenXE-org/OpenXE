<?php

/**
 * Chat-Benachrichtigungen
 *
 * - Cronjob läuft alle 15 Minuten
 * - Cronjob prüft ob es Chat-Nachrichten gibt die seit 30 Minuten ungelesen sind.
 *   Benutzer wird dann per Mail über ungelesene Chat-Nachrichten informiert.
 */
if(!class_exists('app_t2')) {
  class app_t2 extends ApplicationCore
  {
    public $DB;
    public $user;
  }
}
$DEBUG = 0;
if(empty($app) || !class_exists('ApplicationCore') || !($app instanceof ApplicationCore)) {
  $app = new app_t2();
}
if(empty($app->Conf)) {
  $conf = new Config();
  $app->Conf = $conf;
}
if(empty($app->DB) || empty($app->DB->connection)) {
  $app->DB = new DB($app->Conf->WFdbhost, $app->Conf->WFdbname, $app->Conf->WFdbuser, $app->Conf->WFdbpass, $app, $app->Conf->WFdbport);
}
if(empty($app->erp)) {
  if(class_exists('erpAPICustom')) {
    $erp = new erpAPICustom($app);
  }
  else {
    $erp = new erpAPI($app);
  }
  $app->erp = $erp;
}


// Alle Benutzer ermitteln die seit 30 Minuten ungelesene private Nachrichten haben
// u.kalender_ausblenden = "Im Kalender/Chat ausblenden"; außerdem keine Chat-Benachrichtigung empfangen
$usersWithUnreadPrivateMessages = $app->DB->SelectArr(
  'SELECT c.user_to AS id, MAX(c.zeitstempel) AS message_date, COUNT(c.id) AS message_count 
  FROM `user` AS u 
  INNER JOIN `chat` AS c ON c.user_to = u.id AND c.user_to <> 0 
  LEFT JOIN `chat_gelesen` AS g ON c.id = g.message 
  WHERE g.id IS NULL AND u.activ = 1 AND u.kalender_ausblenden != 1 AND DATE_ADD(c.zeitstempel, INTERVAL 30 MINUTE) < NOW()
  GROUP BY c.user_to'
);

// Alle Benutzer ermitteln die seit 30 Minuten ungelesene öffentliche Nachrichten haben
// u.kalender_ausblenden = "Im Kalender/Chat ausblenden"; außerdem keine Chat-Benachrichtigung empfangen
$usersWithUnreadPublicMessages = $app->DB->SelectArr(
  'SELECT u.id, MAX(c.zeitstempel) AS message_date, COUNT(c.id) AS message_count
  FROM `user` AS u 
  INNER JOIN `chat` AS c ON c.zeitstempel > u.logdatei AND c.user_to = 0 
  LEFT JOIN `chat_gelesen` AS g ON c.id = g.message AND g.user = u.id 
  WHERE g.id IS NULL AND u.activ = 1 AND u.kalender_ausblenden != 1 AND DATE_ADD(c.zeitstempel, INTERVAL 30 MINUTE) < NOW()
  GROUP BY u.id'
);

// Benutzer zusammenführen
$usersWithUnreadMessages = combineUserResult(
  $usersWithUnreadPrivateMessages,
  $usersWithUnreadPublicMessages
);

if(empty($usersWithUnreadMessages)){
  return;
}

// PHPMailer konfigurieren
ConfigurePhpMailer();

foreach ($usersWithUnreadMessages as $user) {

  $previousDate = (int)getUserCacheValue($user['id']);
  $currentDate = max((int)$user['private_date'], (int)$user['public_date']);

  // Datum gleich > User hat bereits Benachrichtigung bekommen + keine neuen Nachrichten vorhanden > Benachrichtigung NICHT senden
  // Datum ungleich > User hat bereits Benachrichtigung bekommen + Neue Nachrichten vorhanden > Benachrichtigung senden
  // Kein Datum hinterlegt > User hat noch nie Benachrichtigung bekommen > Benachrichtigung senden
  if($previousDate === false || $previousDate !== $currentDate){
    sendNotificationMail($user['id'], $user['private_count'], $user['public_count']);
    setUserCacheValue($user['id'], $currentDate);
  }
}


/*****************************************
   ENDE: Ab hier nur Funktionen
******************************************/


/**
 * PHPMailer konfigurieren
 *
 * @return void
 */
function ConfigurePhpMailer()
{
  global $app;

  // Mail-Konfiguration laden
  $mailConfig = [
    'benutzername' => $app->erp->Firmendaten("benutzername"),
    'passwort' => $app->erp->Firmendaten("passwort"),
    'host' => $app->erp->Firmendaten("host"),
    'port' => $app->erp->Firmendaten("port"),
    'mailssl' => (int)$app->erp->Firmendaten("mailssl"),
    'noauth' => (int)$app->erp->Firmendaten("noauth"),
    'mailanstellesmtp' => (int)$app->erp->Firmendaten("mailanstellesmtp"),
  ];

  // PHPMailer initialisieren
  $app->mail = new PHPMailer($app);
  $app->mail->CharSet = 'UTF-8';
  $app->mail->PluginDir = 'plugins/phpmailer/';

  if($mailConfig['mailanstellesmtp'] === 1){
    $app->mail->IsMail();
  }else{
    $app->mail->IsSMTP();

    if($mailConfig['noauth'] === 1){
      $app->mail->SMTPAuth = false;
    }else{
      $app->mail->SMTPAuth = true;
    }

    if($mailConfig['mailssl'] === 1){
      $app->mail->SMTPSecure = 'tls';
    }else if($mailConfig['mailssl'] === 2){
      $app->mail->SMTPSecure = 'ssl';
    }

    $app->mail->Host = $mailConfig['host'];
    $app->mail->Port = $mailConfig['port'];
    $app->mail->Username = $mailConfig['benutzername'];
    $app->mail->Password = $mailConfig['passwort'];
  }
}

/**
 * Timestamp der letzten ungelesenen Chat-Nachricht abrufen, zu der eine Mail-Benachrichtigung versendet wurde
 *
 * Soll verhindern dass Mail-Benachrichtigungen mehrfach versendet werden.
 *
 * @param int $userId
 *
 * @return string|false
 */
function getUserCacheValue($userId)
{
  global $app;

  $result = $app->DB->Select(
    "SELECT k.value FROM userkonfiguration AS k 
    WHERE k.name = 'chat_unread_message_date' AND k.user = '{$userId}' LIMIT 1"
  );

  if(empty($result)){
    return false;
  }

  return $result;
}

/**
 * Timestamp wegspeichern, der letzten ungelesenen Chat-Nachricht zu der eine Mail-Benachrichtigung versendet wurde
 *
 * Soll verhindern dass Mail-Benachrichtigungen mehrfach versendet werden.
 *
 * @param int    $userId
 * @param string $cacheValue
 *
 * @return void
 */
function setUserCacheValue($userId, $cacheValue)
{
  global $app;

  $userConfigId = (int)$app->DB->Select(
    "SELECT k.id FROM userkonfiguration AS k 
    WHERE k.name = 'chat_unread_message_date' AND k.user = '{$userId}' LIMIT 1"
  );

  if($userConfigId > 0){
    $app->DB->Update(
      "UPDATE userkonfiguration 
       SET `value` = '{$cacheValue}' 
       WHERE `user` = '{$userId}' AND `name` = 'chat_unread_message_date'
       LIMIT 1"
    );
  }else{
    $app->DB->Insert(
      "INSERT INTO userkonfiguration (`user`, `name`, `value`) 
      VALUES ('{$userId}', 'chat_unread_message_date', '{$cacheValue}')"
    );
  }
}

/**
 * @param array $private
 * @param array $public
 *
 * @return array
 */
function combineUserResult($private = [], $public = [])
{
  if (empty($private)) {
    $private = [];
  }
  if (empty($public)) {
    $public = [];
  }

  $result = [];

  foreach ($private as $user) {
    $userId = $user['id'];
    $result[$userId]['id'] = $userId;
    $result[$userId]['private_date'] = strtotime($user['message_date']);
    $result[$userId]['private_count'] = $user['message_count'];
  }

  foreach ($public as $user) {
    $userId = $user['id'];
    $result[$userId]['id'] = $userId;
    $result[$userId]['public_date'] = strtotime($user['message_date']);
    $result[$userId]['public_count'] = $user['message_count'];
  }

  return $result;
}

/**
 * Mail-Benachrichtung senden
 *
 * @param int   $userId
 * @param array $privateMessages
 * @param array $publicMessages
 *
 * @return bool
 */
function sendNotificationMail($userId, $privateMessages, $publicMessages)
{
  global $app;

  if((int)$userId === 0){
    return false;
  }
  if((int)$publicMessages === 0 && (int)$privateMessages === 0){
    return false;
  }

  $toMail = $app->DB->Select(
    "SELECT a.email FROM `user` AS u 
    INNER JOIN adresse a ON a.id = u.adresse 
    WHERE u.id = '{$userId}' AND u.activ = 1 
    LIMIT 1"
  );
  $toName = $app->DB->Select(
    "SELECT a.name FROM `user` AS u 
    INNER JOIN adresse a ON a.id = u.adresse 
    WHERE u.id = '{$userId}' AND u.activ = 1 
    LIMIT 1"
  );

  if(empty($toMail)){
    return false;
  }

  $app->mail->ClearData();
  $app->mail->AddAddress($toMail, $toName);

  $fromMail = $app->erp->GetFirmaMail();
  $fromName = $app->erp->GetFirmaName();
  $subject = '[Xentral] Ungelesene Chat-Nachrichten';
  $totalMessageCount = (int)$privateMessages + (int)$publicMessages;

  $messages = GetNewestMessagesForUser($userId);
  if (empty($messages)) {
    return false;
  }

  // Xentral-Logo einbinden
  $logoFilePath = dirname(__DIR__) . '/www/themes/new/images/xentral_logo.png';
  $app->mail->AddEmbeddedImage($logoFilePath, 'logo', 'logo.png');

  // Profilbilder einbinden
  $userFromIds = array_unique(array_column($messages, 'user_from'));
  foreach ($userFromIds as $userFromId) {
    $profileImage = GetProfilImage($userFromId);
    $app->mail->AddEmbeddedImage($profileImage['path'], $profileImage['cid'], $profileImage['name']);
  }

  $app->mail->From = $fromMail;
  $app->mail->FromName = $fromName;
  $app->mail->Subject = $subject;
  $app->mail->Body = GetHtmlMessage($toName, $totalMessageCount, $messages);
  $app->mail->IsHTML(true);

  if(!$app->mail->Send()){
    echo $app->mail->ErrorInfo;
    $app->erp->LogFile("Mailer Error: " . $app->mail->ErrorInfo);
    return false;
  }

  return true;
}

/**
 * Chat-Nachrichten für Mail ermitteln
 *
 * Benötigt wird nur die neueste Nachricht pro Absender.
 * Und die neueste Nachricht aus dem öffentlichen Raum.
 *
 * @param int $userId
 *
 * @return array
 */
function GetNewestMessagesForUser($userId)
{
  global $app;

  $lastUnreadMessageTimestamp = getUserCacheValue($userId);
  if (empty($lastUnreadMessageTimestamp)) {
    $lastUnreadMessageTimestamp = time();
  }

  $privateMessages = $app->DB->SelectArr(
    'SELECT c.user_to, c.user_from, a.name AS user_from_name, c.message, c.zeitstempel 
    FROM chat AS c
    INNER JOIN `user` AS u ON c.user_from = u.id 
    INNER JOIN `adresse` AS a ON u.adresse = a.id
    INNER JOIN (
      SELECT MAX(c.id) AS message_id
      FROM chat AS c 
      LEFT JOIN `chat_gelesen` AS g ON c.id = g.message 
      WHERE g.id IS NULL AND c.user_to <> 0 
      GROUP BY c.user_from, c.user_to
    ) AS newest_messages ON c.id = newest_messages.message_id 
    WHERE c.user_to = ' . (int)$userId . ' AND u.activ = 1 
    AND UNIX_TIMESTAMP(c.zeitstempel) > ' . $lastUnreadMessageTimestamp . '
    GROUP BY c.user_from'
  );

  $publicMessages = $app->DB->SelectArr(
    'SELECT c.user_to, c.user_from, a.name AS user_from_name, c.message, c.zeitstempel 
    FROM chat AS c
    INNER JOIN `user` AS u ON c.user_from = u.id 
    INNER JOIN `adresse` AS a ON u.adresse = a.id
    INNER JOIN (
      SELECT MAX(c.id) AS message_id 
      FROM chat AS c 
      WHERE c.user_to = 0 
      GROUP BY c.user_to 
    ) AS newest_messages ON c.id = newest_messages.message_id 
    LEFT JOIN `chat_gelesen` AS g ON c.id = g.message AND  g.user = u.id AND u.id = ' . (int)$userId . '
    WHERE u.activ = 1 AND g.id IS NULL 
    AND UNIX_TIMESTAMP(c.zeitstempel) > ' . $lastUnreadMessageTimestamp
  );

  if (empty($privateMessages)) {
    $privateMessages = [];
  }
  if (empty($publicMessages)) {
    $publicMessages = [];
  }

  return array_merge($privateMessages, $publicMessages);
}

/**
 * Profilbild pro User ermitteln
 *
 * @param int $userId
 *
 * @return array
 */
function GetProfilImage($userId)
{
  global $app;

  if((int)$userId > 0){
    $adresse = (int)$app->DB->Select(
      "SELECT a.id FROM user u 
      LEFT JOIN adresse a ON a.id = u.adresse 
      LEFT JOIN adresse_rolle ar ON ar.adresse = a.id 
      WHERE u.activ = 1 AND u.kalender_ausblenden != 1 AND ar.subjekt = 'Mitarbeiter' AND (ar.bis = '0000-00-00' OR ar.bis <= NOW())
      AND u.id = '{$userId}' AND ((u.hwtoken <> 4 ) OR u.hwtoken IS NULL) LIMIT 1"
    );

    if($adresse > 0){
      $dateiversion = (int)$app->DB->Select(
        "SELECT dv.id FROM datei_stichwoerter ds 
        INNER JOIN datei d ON ds.datei = d.id 
        INNER JOIN datei_version dv ON dv.datei = d.id 
        WHERE d.geloescht = 0 AND objekt LIKE 'Adressen' AND parameter = '{$adresse}' AND subjekt LIKE 'Profilbild' 
        ORDER by dv.id DESC 
        LIMIT 1"
      );

      if($dateiversion > 0){
        $userdata = isset($app->Conf->WFuserdata)
          ? $app->Conf->WFuserdata
          : str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']) . '../userdata';

        $path = $userdata . '/dms/' . $app->Conf->WFdbname;
        $cachefolder = $path . '/cache';
        $cachefolder = $app->erp->GetDMSPath($dateiversion . '_100_100', $cachefolder, true);

        if(file_exists($cachefolder . '/' . $dateiversion . '_100_100')){
          $type = mime_content_type($cachefolder . '/' . $dateiversion . '_100_100');
          switch ($type) {
            case 'image/jpg':
            case 'image/jpeg':
              return [
                'cid' => 'user_' . $userId,
                'name' => 'user_' . $userId . '.jpg',
                'path' => $cachefolder . '/' . $dateiversion . '_100_100',
              ];
            case 'image/png':
              return [
                'cid' => 'user_' . $userId,
                'name' => 'user_' . $userId . '.png',
                'path' => $cachefolder . '/' . $dateiversion . '_100_100',
              ];
            case 'image/gif':
              return [
                'cid' => 'user_' . $userId,
                'name' => 'user_' . $userId . '.gif',
                'path' => $cachefolder . '/' . $dateiversion . '_100_100',
              ];
            case 'application/pdf':
              return [
                'cid' => 'user_' . $userId,
                'name' => 'user_' . $userId . '.png',
                'path' => dirname(__DIR__) . '/themes/new/images/pdf.svg',
              ];
          }
        }
      }
    }
  }

  return [
    'cid' => 'user_' . $userId,
    'name' => 'user_' . $userId . '.png',
    'path' => dirname(__DIR__) . '/www/themes/new/images/profil.png',
  ];
}

/**
 * HTML-Nachrichteninhalt zusammenstellen
 *
 * @param string $receipientName
 * @param int    $messageTotalCount
 * @param array  $messages
 *
 * @return string HTML-Template
 */
function GetHtmlMessage($receipientName, $messageTotalCount, $messages = [])
{
  $template = <<<HTML
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>[Xentral] Ungelesene Chat-Nachrichten</title>
  <style type="text/css">
    body { font-family: "Helvetica Neue", Helvetica, Arial, sans-serif; width: 100%; max-width: 100%; font-size: 17px; line-height: 24px; color: #48494B; background: #F5F5F5; }
    h1, h2, h3, h4 { color: #42B8C4; margin-bottom: 12px; line-height: 26px; }
    p, ul, ul li { font-size: 17px; margin: 0; margin-bottom: 16px; line-height: 24px; }
    ul { margin-bottom: 24px; }
    ul li { margin-bottom: 8px; }
    hr { margin: 1.5rem 0; width: 100%; border: none; border-bottom: 1px solid #ECECEC; }
    a, a:link, a:visited, a:active, a:hover { font-weight: bold; color: #48494B; text-decoration: none; word-break: break-word; }
    a:active, a:hover { text-decoration: underline; }
    body { width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; margin: 0 auto; padding: 0; }
  </style>
</head>
<body style="background: #F5F5F5; color: #48494B; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 17px; line-height: 24px; max-width: 100%; width: 100% !important; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%; margin: 0 auto; padding: 0;">
<table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; line-height: 24px; margin: 0; padding: 0; width: 100%; font-size: 17px; color: #48494B; background: #F5F5F5;">
  <tr>
    <td valign="top" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif !important; border-collapse: collapse;">
      <table width="100%" cellpadding="0" cellspacing="0" border="0" style="border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt;">
        <tr>
          <td valign="bottom" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif !important; border-collapse: collapse; padding: 20px 15px 10px 15px;">
            <div style="text-align: center;">
              <a href="https://xentral.biz/" style="color: #42B8C4; font-weight: bold; text-decoration: none; word-break: break-word;">
                <img src="cid:logo" width="197" height="33" style="-ms-interpolation-mode: bicubic; outline: none; text-decoration: none; border: none;"></a>
            </div>
          </td>
        </tr>
      </table>
    </td>
  </tr>
    <tr>
    <td valign="top" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif !important; border-collapse: collapse;">
      <table cellpadding="32" cellspacing="0" border="0" align="center" style="max-width: 600px; border-collapse: collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; background: white; border-radius: 0.5rem; margin: 0 auto; margin-bottom: 1rem;">
        <tr>
          <td width="546" valign="top" style="font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif !important; border-collapse: collapse;">
            <div style="max-width: 600px; margin: 0 auto;">
              <h2 style="color: #0E8394; line-height: 30px; margin: 0; margin-bottom: 12px;">Hallo {$receipientName}!</h2>
              <p style="font-size: 17px; line-height: 24px; margin: 0; margin-bottom: 16px;">
                Es gibt {$messageTotalCount} neue Chat-Nachrichten im <strong>Xentral ERP</strong>.
              </p>
            </div>
HTML;

  $privateHeaderViewed = false;
  foreach ($messages as $message) {
    $zeitstempel = strtotime($message['zeitstempel']);
    $message['zeitstempel'] = 'vom&nbsp;' . date('d.m.Y', $zeitstempel) . '&nbsp;um&nbsp;' . date('H:i', $zeitstempel) .'&nbsp;Uhr';

    $template .= '<hr style="border: none; border-bottom: 1px solid #ECECEC; margin: 1.5rem 0; width: 100%;">';
    if ((int)$message['user_to'] === 0) {
      $template .= '<h4 style="color: #A2C55A; line-height: 18px; font-size: 18px; margin: 0; margin-bottom: 12px; ">Neueste öffentliche Nachricht</h4>';
    }
    if ((int)$message['user_to'] > 0 && $privateHeaderViewed === false) {
      $template .= '<h4 style="color: #F69E06; line-height: 18px; font-size: 18px; margin: 0; margin-bottom: 12px; ">Ungelesene private Nachrichten</h4>';
      $privateHeaderViewed = true;
    }
    $template .=
      '<div style="margin: 0 0 10px 0; width: 100%; word-break: break-word; clear: left; font-size: 15px; line-height: 18px; color: #48494B; min-height: 36px;">' .
      '<img src="cid:user_' . $message['user_from'] . '" style="-ms-interpolation-mode: bicubic; outline: none; text-decoration: none; border: none; float: left; margin-right: 8px; border-radius: 4px; display: inline-block; width: 40px; height: 40px;">' .
      '<div style="padding-left: 48px;">'.
      '<div>'.
      '<strong>'. $message['user_from_name'] . '</strong>&nbsp;'.
      '<small style="color:#999;">' . $message['zeitstempel'] . '</small>'.
      '</div>'.
      '<div style="max-width: 488px; word-wrap: break-word;overflow-wrap: break-word; word-break: break-word;">'.htmlspecialchars($message['message']).'</div>'.
      '</div>'.
      '</div>';
  }

  $template .=
    '</td></tr></table>' .
    '</td></tr></table>' .
    '</body></html>';

  return $template;
}

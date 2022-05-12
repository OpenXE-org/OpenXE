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

class Chat {
  /** @var Application $app */
  var $app;

  /** @var \Xentral\Components\Template\TemplateInterface $tmpl  */
  protected $tmpl;

  const MODULE_NAME = 'Chat';

  public function __construct($app, $intern=false)
  {
    $this->app=$app;
    if($intern){
      return;
    }

    $this->tmpl = $this->app->Container->get('Template');
    $this->tmpl->setDefaultNamespace('Modules/Chat');

    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler("list","ChatList");
    $this->app->DefaultActionHandler("list");
    $this->app->ActionHandlerListen($app);
  }

  public function Install()
  {
    $this->app->erp->CheckTable("chat");
    $this->app->erp->CheckColumn("id","int(11)","chat","NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("user_from","INT(11)","chat","DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn("user_to","INT(11)","chat","DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn("message","TEXT","chat","DEFAULT '' NOT NULL");
    $this->app->erp->CheckColumn("prio","TINYINT(1)","chat","DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn("zeitstempel","DATETIME","chat");
    $this->app->erp->CheckIndex("chat","user_from");
    $this->app->erp->CheckIndex("chat","user_to");

    if (!$this->app->DB->CheckTableExistence("chat_gelesen")){
      $this->InstallGelesenTable();
    }
  }

  protected function InstallGelesenTable()
  {
    // Neue Gelesen-Tabelle anlegen
    $this->app->erp->CheckTable("chat_gelesen");
    $this->app->erp->CheckColumn("id","int(11)","chat_gelesen","NOT NULL AUTO_INCREMENT");
    $this->app->erp->CheckColumn("user","INT(11)","chat_gelesen","DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn("message","INT(11)","chat_gelesen","DEFAULT '0' NOT NULL");
    $this->app->erp->CheckColumn("zeitstempel","DATETIME","chat_gelesen", "DEFAULT NULL");
    $this->app->erp->CheckIndex("chat_gelesen","message");
    //$this->app->DB->Query("ALTER TABLE chat_gelesen ADD UNIQUE (`user`, `message`)");
    $this->app->erp->CheckIndex("chat_gelesen", array('user','message'), true);

    $this->app->DB->Insert("INSERT INTO chat_gelesen (message, `user`, zeitstempel) SELECT DISTINCT id, user_to, NULL FROM chat WHERE gelesen = 1 OR user_to = 0");
    if($this->app->DB->error())
    {
      // Alte Gelesen-Markierungen in neue Tabelle übernehmen
      $readMessages = $this->app->DB->SelectArr("SELECT c.id, c.user_to FROM chat AS c WHERE c.gelesen = '1' AND c.user_to > 0");
      foreach ($readMessages as $readMessage) {
        $sql  = "INSERT INTO chat_gelesen (message, `user`, zeitstempel) ";
        $sql .= "VALUES ('".$readMessage['id']."', '".$readMessage['user_to']."', NULL)";
        $this->app->DB->Insert($sql);
      }

      // Gelesen-Markierungen für öffentlichen Nachrichten übernehmen
      $publicMessages = $this->app->DB->SelectArr("SELECT c.id, c.user_to FROM chat AS c WHERE c.user_to = 0");
      foreach ($publicMessages as $publicMessage) {
        $sql  = "INSERT INTO chat_gelesen (message, `user`, zeitstempel) ";
        $sql .= "VALUES ('".$publicMessage['id']."', '".$publicMessage['user_to']."', NULL)";
        $this->app->DB->Insert($sql);
      }
    }
    // Alte Gelesen-Spalte löschen
    $this->app->DB->Query("ALTER TABLE chat DROP COLUMN gelesen");
  }

  public function ChatList()
  {
    $cmd = $this->app->Secure->GetGET('cmd');

    switch ($cmd) {
      case 'userlist':
        $data = array();
        $data['users'] = $this->GetUserlist();
        $data['rooms'] = $this->GetRoomlist();
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;

      case 'messages':
        $user_id = (int)$this->app->Secure->GetPOST('user_id');
        $before_message_id = (int)$this->app->Secure->GetPOST('before_message_id');
        $after_message_id = (int)$this->app->Secure->GetPOST('after_message_id');
        $after_readmark_id = (int)$this->app->Secure->GetPOST('after_readmark_id');
        $data = $this->GetMessages($user_id, $after_message_id, $after_readmark_id, $before_message_id);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;

      case 'sendmessage':
        $this->ChatSubmit();
        exit;

      case 'markread':
        $messageIds = $this->app->Secure->GetPOST('messages');
        $data = $this->MarkAsRead($messageIds);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    $this->app->erp->StartseiteMenu();

    if($this->app->DB->Select("SELECT chat_popup FROM `user` WHERE id = '".$this->app->User->GetID()."' LIMIT 1"))
    {
      $this->app->BuildNavigation = false;
      $this->app->PopupJS = true;
      $this->tmpl->assign('chatStandaloneClass', 'chat-standalone');
    }

    $this->tmpl->assign('tabText', 'Chat');
    $this->tmpl->display('list.tpl');
  }

  protected function GetRoomList()
  {
    $userId = $this->app->User->GetID();
    $registrierDatum = $this->app->DB->Select("SELECT u.logdatei FROM `user` AS u WHERE u.id = '".$userId."'");

    // Öffentliche Nachrichten erst ab Registrierdatum zählen
    $ungelesen = $this->app->DB->Select(
      "SELECT COUNT(c.id) 
        FROM chat AS c 
        LEFT JOIN chat_gelesen AS g ON c.id = g.message AND (g.user = '".$userId."' OR g.user = 0)
        WHERE c.user_to='0' AND c.zeitstempel > '".$registrierDatum."' 
        AND g.id IS NULL"
    );

    $data = array(
      array(
        'id' => 0,
        'name' => 'Öffentlich',
        'unread' => $ungelesen,
      )
    );

    return $data;
  }

  protected function GetUserlist()
  {
    $userId = $this->app->User->GetID();

    $users = $this->app->DB->SelectArr(
      "SELECT DISTINCT u.id, a.name, uo.login, DATE_ADD(uo.time, INTERVAL 30 MINUTE) > NOW() AS recently_online 
      FROM user u 
      INNER JOIN adresse a ON a.id = u.adresse 
      INNER JOIN adresse_rolle ar ON ar.adresse = a.id 
      LEFT JOIN useronline uo ON uo.user_id = u.id AND uo.login = 1
      WHERE u.activ = 1 AND u.kalender_ausblenden != 1 AND ar.subjekt = 'Mitarbeiter' 
        AND (ar.bis = '0000-00-00' OR ar.bis <= NOW())
        AND ((u.hwtoken <> 4 ) OR u.hwtoken IS NULL) 
      ORDER BY a.name ASC"
    );

    $data = array();
    $cusers = !empty($users)?count($users):0;
    for($i=0;$i<$cusers;$i++)
    {
      $ungelesen = $this->app->DB->Select(
        "SELECT COUNT(c.id) FROM chat AS c
        LEFT JOIN chat_gelesen AS g ON c.id = g.message  
        WHERE c.user_to='" . $userId . "' 
        AND c.user_from='" . $users[$i]['id'] . "' 
        AND g.id IS NULL" // Kein Eintrag in chat_gelesen = ungelesen
      );

      $data[$i]['id'] = $users[$i]['id'];
      $data[$i]['name'] = $users[$i]['name'];
      $data[$i]['unread'] = (int)$ungelesen;
      $data[$i]['online'] = (int)$users[$i]['login'] === 1 && (int)$users[$i]['recently_online'] === 1 ? true : false;
      $data[$i]['self'] = ($users[$i]['id'] == $this->app->User->GetID()) ? true : false;
    }

    return $data;
  }

  protected function GetMessages($fromUserId, $afterMessageId = null, $afterReadmarkId = null, $beforeMessageId = null)
  {
    $fromUserId = (int)$fromUserId;
    $selfUserId = (int)$this->app->User->GetID();

    $andWhere = '';
    if ($beforeMessageId !== null && $beforeMessageId !== 0) {
      $andWhere = "AND c.id < " . (int)$beforeMessageId . " ";
    }
    if ($afterMessageId !== null && $afterMessageId !== 0) {
      $andWhere = "AND c.id > " . (int)$afterMessageId . " ";
    }

    if ($fromUserId === 0) {
      // Chat mit "Öffentlich"
      $sql = "SELECT c.id, c.user_from, a.name AS user_from_name, g.id AS gelesen_id, g.zeitstempel AS gelesen_zeitstempel, c.message, c.user_to,
          DATE_FORMAT(c.zeitstempel,'%d.%m.%Y') AS datum, DATE_FORMAT(c.zeitstempel,'%H:%i:%s') AS uhrzeit 
        FROM chat AS c  
        LEFT JOIN chat_gelesen AS g ON c.id = g.message AND (g.user = '".$selfUserId."' OR g.user = 0)  
        INNER JOIN `user` AS u ON c.user_from = u.id 
        INNER JOIN adresse AS a ON u.adresse = a.id 
        WHERE c.user_to = 0 ";
      $sql .= $andWhere;
      $sql .= "GROUP BY c.id ";
      $sql .= "ORDER BY c.id DESC LIMIT 0, 20";
      $messages = $this->app->DB->SelectArr($sql);

      // Gelesen-Markierungen gibt es nicht im öffentlichen Chat
      $readmarks = array();

    } else {
      // Chat mit Benutzer

      // Nachrichten abrufen
      $sql = "SELECT c.id, c.user_from, a.name AS user_from_name, g.id AS gelesen_id, c.message, c.prio, c.user_to,
          DATE_FORMAT(c.zeitstempel,'%d.%m.%Y') AS datum, DATE_FORMAT(c.zeitstempel,'%H:%i:%s') AS uhrzeit 
        FROM chat AS c 
        LEFT JOIN chat_gelesen AS g ON c.id = g.message AND c.user_to = g.user 
        INNER JOIN `user` AS u ON c.user_from = u.id 
        INNER JOIN adresse AS a ON u.adresse = a.id 
        WHERE ((c.user_to = '" . $fromUserId . "' AND user_from = '" . $selfUserId . "') 
        OR (c.user_from = '" . $fromUserId . "' AND user_to = '" . $selfUserId . "')) ";
      $sql .= $andWhere;
      $sql .= "ORDER by c.id DESC LIMIT 0, 20";
      $messages = $this->app->DB->SelectArr($sql);

      // Gelesen-Markierungen abrufen
      $sql  = "SELECT g.id, g.message 
        FROM chat_gelesen AS g 
        WHERE g.user = '".$fromUserId."' ";
      if ($afterReadmarkId > 0) {
        $sql .= "AND g.id > '".$afterReadmarkId."' ";
        $sql .= "ORDER BY g.id ASC LIMIT 0, 20";
      } else {
        // Nur die neueste Markierung mitschicken
        $sql .= "ORDER BY g.id DESC LIMIT 0, 1";
      }
      $readmarks = $this->app->DB->SelectArr($sql);
    }

    $data = array(
      'messages' => array(), // Chat-Nachrichten
      'readmark' => array() // Gelesen-Markierungen
    );

    // Nachrichten aufbereiten
    foreach ($messages as $message) {
      $data['messages'][] = array(
        'id' => (int)$message['id'],
        'read' => ((int)$message['gelesen_id'] > 0) ? true : false,
        'prio' => (!empty($message['prio']) && $message['prio'] === '1') ? true : false,
        'text' => $this->MakeLinks($message['message']),
        'user' => (int)$message['user_from'],
        'name' => $message['user_from_name'],
        'date' => $message['datum'],
        'time' => $message['uhrzeit']
      );
    }
    $data['messages'] = array_reverse($data['messages']);

    // Gelesen-Markierungen aufbereiten
    if (is_array($readmarks)) {
      foreach ($readmarks as $readmark) {
        $data['readmark'][] = array(
          'id' => (int)$readmark['id'],
          'message' => (int)$readmark['message'],
        );
      }
    }

    return $data;
  }

  protected function ChatSubmit()
  {
    /** @var \Xentral\Modules\SystemNotification\Service\NotificationServiceInterface $notification */
    $notification = $this->app->Container->get('NotificationService');

    $nachricht = $this->app->Secure->GetPOST("nachricht");
    $empfaenger = $this->app->Secure->GetPOST("empfaenger");
    $prio = $this->app->Secure->GetPOST("prio");

    $nachricht = htmlspecialchars($nachricht);

    if($nachricht!="")
    {
      $this->app->DB->Insert("INSERT INTO chat (id,user_from,user_to,message,zeitstempel,prio) VALUES ('','".$this->app->User->GetID()."','".$empfaenger."','$nachricht',NOW(),'.$prio.')");

      if($prio == "1") {
        $name = $this->app->DB->Select("SELECT a.name FROM user u LEFT JOIN adresse a ON a.id=u.adresse WHERE u.id=".$this->app->User->GetID());
        if($empfaenger)
        {
          $notification->create($empfaenger, 'default', "$name (".date('d.m')." um ".date('H:i').")", $nachricht, true);
          $notification->createPushNotification($empfaenger, 'Neue Chat-Nachricht von ' . $name, $nachricht, false);
        }else{
          $users = $this->app->DB->SelectArr("SELECT DISTINCT u.id, a.name,u.type,
                uo.login FROM user u LEFT JOIN adresse a ON a.id=u.adresse LEFT JOIN useronline uo ON uo.user_id=u.id AND uo.login=1
                LEFT JOIN adresse_rolle ar ON ar.adresse=a.id 
                WHERE u.activ=1 AND uo.login = 1 AND u.kalender_ausblenden!=1 AND ar.subjekt='Mitarbeiter' AND (ar.bis='0000-00-00' OR ar.bis <= NOW())
                AND u.id!='".$this->app->User->GetID()."' AND ((u.hwtoken <> 4) OR u.hwtoken IS NULL) group by u.id");
          if($users)
          {
            foreach($users as $user)
            {
              if($user['type'] == 'admin' || $this->app->DB->Select("SELECT id FROM userrights WHERE user = '".$user['id']."' AND module='chat' && action='list' and permission='1' LIMIT 1"))
              {
                $notification->create($user['id'], 'default', "$name (".date('d.m')." um ".date('H:i').")", $nachricht, true);
                $notification->createPushNotification($user['id'], 'Neue Chat-Nachricht von ' . $name, $nachricht, false);
              }
            }
          }
        }
      }
    }
    exit;
  }

  protected function MarkAsRead($messageIds)
  {
    if (!is_array($messageIds)) {
      return array();
    }

    $data = array();
    $userId = $this->app->User->GetID();
    foreach ($messageIds as $messageId) {
      if ((int)$messageId > 0) {
        $sql = "INSERT INTO chat_gelesen(`user`, message, zeitstempel) VALUES ('".$userId."', '".$messageId."', NOW())";
        $this->app->DB->Insert($sql);
        $data[] = $messageId;
      }
    }

    // IDs der gelesen-gesetzten Nachrichten zurücksenden
    return $data;
  }

  protected function MakeLinks($str) {
    $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
    $urls = array();
    $urlsToReplace = array();
    if(preg_match_all($reg_exUrl, $str, $urls)) {
      $numOfMatches = count($urls[0]);
      $numOfUrlsToReplace = 0;
      for($i=0; $i<$numOfMatches; $i++) {
        $alreadyAdded = false;
        $numOfUrlsToReplace = count($urlsToReplace);
        for($j=0; $j<$numOfUrlsToReplace; $j++) {
          if($urlsToReplace[$j] == $urls[0][$i]) {
            $alreadyAdded = true;
          }
        }
        if(!$alreadyAdded) {
          array_push($urlsToReplace, $urls[0][$i]);
        }
      }
      $numOfUrlsToReplace = count($urlsToReplace);
      for($i=0; $i<$numOfUrlsToReplace; $i++) {
        $str = str_replace($urlsToReplace[$i], "<a href=\"".$urlsToReplace[$i]."\" target=\"_blank\">".$urlsToReplace[$i]."</a> ", $str);
      }
      return $str;
    } else {
      return $str;
    }
  }
}


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

class User 
{
  /** @var array */
  var $cache;

  /**
   * User constructor.
   *
   * @param ApplicationCore $app
   */
  public function __construct($app)
  {
    $this->app = $app;
  }

  /**
   * @return array
   */
  public function getUserProjects()
  {
    return $this->getUserProjectsByParameter($this->GetAdresse(), $this->GetType());
  }

  /**
   * @return array
   */
  public function getPublicProjects()
  {
    $cacheKey = $this->getCacheKey();
    if(!empty($this->cache[$cacheKey]['public_projects'])) {
      return $this->cache[$cacheKey]['public_projects'];
    }
    $this->loadProjectsInCacheProperty();

    return $this->cache[$cacheKey]['public_projects'];
  }

  /**
   * @return array
   */
  public function getAllProjects()
  {
    $cacheKey = $this->getCacheKey();
    if(!empty($this->cache[$cacheKey]['all_projects'])) {
      return $this->cache[$cacheKey]['all_projects'];
    }
    $this->loadProjectsInCacheProperty();

    return $this->cache[$cacheKey]['all_projects'];
  }

  /**
   * @return array
   */
  public function createCache()
  {
    $allProjects = $this->getAllProjects();
    $file = $this->app->getTmpFolder().'cache_useronline';
    $arr = $this->app->DB->SelectArr(
      "SELECT uo.user_id, uo.sessionid, u.type, u.adresse 
      FROM `useronline` AS `uo` 
      INNER JOIN `user` AS `u` ON uo.user_id = u.id AND u.activ = 1 
      WHERE uo.login = 1"
    );
    $ret = [];
    if(is_file($file)) {
      $ret = file_get_contents($file);
      if(empty(!$ret)) {
        $ret = json_decode($ret, true);
      }
      if(empty($ret)) {
        $ret = [];
      }
    }
    $cacheKey = $this->getCacheKey();
    $ret[$cacheKey] = [];
    if(!empty($arr)) {
      foreach($arr as $row) {
        if($row['type'] === 'admin') {
          $projects = $allProjects;
        } else {
          $projects = $this->getUserProjectsByParameter($row['adresse'], $row['type']);
        }
        $sessionId = $row['sessionid'];
        $sha1SessionId = sha1($sessionId);
        $ret[$cacheKey][$sha1SessionId] = ['type'=>$row['type'],'project'=>$projects];
      }
    }
    file_put_contents($file, json_encode($ret));

    return $ret[$cacheKey];
  }

  /**
   * @param bool $createIfEmpty
   *
   * @return array|null
   */
  public function getUserByCache($createIfEmpty = true)
  {
    $file = $this->app->getTmpFolder().'cache_useronline';
    if(isset($_COOKIE['CH42SESSION']) && $_COOKIE['CH42SESSION']!='') {
      $tmp = $_COOKIE['CH42SESSION'];
    } else {
      $tmp = session_id();
    }
    $sha1Tmp = sha1($tmp);
    $content = '';
    if(is_file($file)){
      $content = file_get_contents($file);
    }
    $cacheKey = $this->getCacheKey();
    if(!empty($content)) {
      $content = json_decode($content, true);
      if(empty($content[$cacheKey])) {
        $content[$cacheKey] = $this->createCache();
      }
      $content = $content[$cacheKey];
      if(!empty($content[$sha1Tmp])) {
        return $content[$sha1Tmp];
      }
    } else {
      if(!$createIfEmpty) {
        return null;
      }
    }
    if(!empty($tmp)) {
      $content = $this->createCache();
      if(!empty($content[$sha1Tmp])) {
        return $content[$sha1Tmp];
      }
    }

    return null;
  }

  /**
   * @param int $projektId
   *
   * @return bool
   */
  public function projectOk($projektId): ?bool
  {
    $user = $this->getUserByCache(false);
    if(empty($user)) {
      return null;
    }
    if($projektId <= 0) {
      return true;
    }
    if($user['type'] === 'admin') {
      return true;
    }
    if(empty($user['project'])) {
      return false;
    }
    if(in_array($projektId, $user['project'])) {
      return true;
    }
    //@todo Projekt aus Cache holen
    return false;
  }

  /**
   * @return int
   */
  public function GetID(): int
  { 
    if(isset($_COOKIE['CH42SESSION']) && $_COOKIE['CH42SESSION']!='') {
      $tmp = $_COOKIE['CH42SESSION'];
    } else {
      $tmp = session_id();
    }
    $cacheKey = $this->getCacheKey();
    if(empty($this->cache[$cacheKey])
      || !isset($this->cache[$cacheKey]['time']) || !isset($this->cache[$cacheKey]['tmp'])
      || $this->cache[$cacheKey]['time'] + 10 < microtime(true) || $this->cache[$cacheKey]['tmp'] != $tmp) {
      $this->cache = null;
      $user_id = (int)$this->app->DB->Select(
        sprintf(
          "SELECT `user_id`
          FROM `useronline`
          WHERE `sessionid` != '' AND `sessionid` = '%s' AND `login` = 1
          LIMIT 1",
          $this->app->DB->real_escape_string($tmp)
        )
      );
      if($user_id > 0) {
        $this->cache[$cacheKey]['user_id'] = $user_id;
        $this->cache[$cacheKey]['tmp'] = $tmp;
        $this->cache[$cacheKey]['time'] = microtime(true);
      }

      return $user_id;
    }

    return (int)$this->cache[$cacheKey]['user_id'];
  }

  /**
   * @return string
   */
  public function GetType(): string
  {
    $userId = (int)$this->GetID();
    if($userId <= 0) {
      return (string)$this->app->Conf->WFconf['defaultgroup'];
    }
    $cacheKey = $this->getCacheKey();
    if(!empty($this->cache[$cacheKey]) && isset($this->cache[$cacheKey]['type'])) {
      return (string)$this->cache[$cacheKey]['type'];
    }
    $this->loadUserRowInCacheProperty($userId);

    return (string)$this->cache[$cacheKey]['type'];
  }

  /**
   * @param null|string|array $settings
   */
  function SettingsToUserKonfiguration($settings = null)
  {
    $id = (int)$this->GetID();
    if(!$id) {
      return;
    }
    if($settings === null) {
      $settings = $this->app->DB->Select(sprintf('SELECT `settings` FROM `user` WHERE `id` = %d LIMIT 1', $id));
      $cacheKey = $this->getCacheKey();
      $this->cache[$cacheKey]['settings'] = $settings;
    }
    if(empty($settings)) {
      return;
    }

    if($settings != '') {
      $settings = @unserialize($settings);
    }
    if(empty($settings) || !is_array($settings)) {
      return;
    }

    foreach($settings as $k => $v) {
      $check = $this->app->DB->Select("SELECT `id` FROM `userkonfiguration` WHERE `name` = '".$this->app->DB->real_escape_string($k)."' AND `user` = '$id' LIMIT 1");
      if($check) {
        $this->app->DB->Update("UPDATE `userkonfiguration` set `value` = '".$this->app->DB->real_escape_string($v)."' WHERE `id` = '$check' LIMIT 1");
      }else{
        $this->app->DB->Insert("INSERT INTO `userkonfiguration` (`user`,`name`,`value`) VALUES ('$id','".$this->app->DB->real_escape_string($k)."','".$this->app->DB->real_escape_string($v)."')");
      }
    }
    if(!$this->app->DB->error()) {
      $this->app->DB->Update(sprintf("UPDATE `user` SET `settings` = '' WHERE `id` = %d LIMIT 1", $id));
      $cacheKey = $this->getCacheKey();
      $this->cache[$cacheKey]['settings'] = '';
    }
  }

  /**
   * @var int|null $userId
   *
   * @return string|null
   */
  public function GetSettings($userId = null)
  {
    $cacheKey = $this->getCacheKey();
    if(!empty($this->cache[$cacheKey]) && isset($this->cache[$cacheKey]['settings'])) {
      return $this->cache[$cacheKey]['settings'];
    }
    $this->loadUserRowInCacheProperty($userId);

    return $this->cache[$cacheKey]['settings'];
  }

  /**
   * @param string|array $index
   *
   * @return array|mixed|string|null
   */
  public function GetParameter($index)
  {
    $userId = (int)$this->GetID();
    $settings = $this->GetSettings($userId);
    if(!empty($settings)) {
      $this->SettingsToUserKonfiguration($settings);
    }

    if((is_array($index) && count($index) === 0) || (!is_array($index) && (string)$index === '')) {
      return null;
    }

    if(is_array($index)) {
      $index = array_map('trim', $index);
      $indexNames = array_map([$this->app->DB, 'real_escape_string'], $index);
      $sql = sprintf(
        "SELECT `name`, MAX(`value`) AS `value` 
        FROM `userkonfiguration` 
        WHERE `user` = %d AND `name` IN ('%s') 
        GROUP BY `name`",
        $userId, implode("', '", $indexNames)
      );
      $arr = $this->app->DB->SelectPairs($sql);
      $ret = null;
      foreach($index as $ind)  {
        if(isset($arr[$ind])) {
          $ret[] = [ 'name'=>$ind, 'value'=> $arr[$ind] ];
        }
        else {
          $ret[] = [ 'name'=>$ind, 'value'=> '' ];
        }
      }

      return $ret;
    }

    return $this->app->DB->Select(
      sprintf(
        "SELECT `value` 
        FROM `userkonfiguration` 
        WHERE `name` = '%s' AND `user` = %d 
        LIMIT 1",
        $this->app->DB->real_escape_string($index), $userId
      )
    );
  } 

  // value koennen beliebige Datentypen aus php sein (serialisiert) 

  /**
   * @param string $index
   * @param mixed  $value
   */
  public function SetParameter($index, $value)
  {
    if((string)$index === '' || $value === null) {
      return;
    }
    $id = (int)$this->GetID();

    $settings = $this->GetSettings($id);
    if(!empty($settings)) {
      $this->SettingsToUserKonfiguration($settings);
    }

    $check = $this->app->DB->SelectRow(
      sprintf(
        "SELECT `id`, `value` 
        FROM `userkonfiguration` WHERE `name` = '%s' AND `user` = %d 
        LIMIT 1",
        $this->app->DB->real_escape_string($index), $id
      )
    );
    if(empty($check)) {
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO `userkonfiguration` (`user`, `name`, `value`) VALUES (%d, '%s', '%s')",
          $id, $this->app->DB->real_escape_string($index), $this->app->DB->real_escape_string($value)
        )
      );
      $this->cache = null;
      return;
    }

    if((string)$value === (string)$check['value']) {
      return;
    }
    $this->app->DB->Update(
      sprintf(
        "UPDATE `userkonfiguration` 
        SET `value` = '%s' 
        WHERE `id` = %d 
        LIMIT 1",
        $this->app->DB->real_escape_string($value), $check['id']
      )
    );
    $this->cache = null;
  }

  /**
   * @param string $index
   */
  public function deleteParameter($index)
  {
    if(empty($index)) {
      return;
    }
    $id = $this->GetID();
    $this->app->DB->Delete(
      sprintf(
        'DELETE FROM `userkonfiguration` WHERE `user` = %d AND `name` = \'%s\'',
        $id, $this->app->DB->real_escape_string($index)
      )
    );
  }

  /**
   * @param string $prefix
   */
  public function deleteParameterPrefix($prefix)
  {
    if(empty($prefix)) {
      return;
    }
    $id = $this->GetID();
    $this->app->DB->Delete(
      sprintf(
        'DELETE FROM `userkonfiguration` WHERE `user` = %d AND `name` LIKE \'%s%%\'',
        $id, $this->app->DB->real_escape_string($prefix)
      )
    );
  }

  /**
   * @return string|null
   */
  public function GetUsername()
  {
    $cacheKey = $this->getCacheKey();
    if(!empty($this->cache[$cacheKey]) && isset($this->cache[$cacheKey]['username'])) {
      return $this->cache[$cacheKey]['username'];
    }
    $this->loadUserRowInCacheProperty();

    return $this->cache[$cacheKey]['username'];
  }

  /**
   * @return string|null
   */
  public function GetDescription()
  {
    return $this->GetName();
  }

  /**
   * @return string|null
   */
  public function GetMail()
  {
    $cacheKey = $this->getCacheKey();
    if(!empty($this->cache[$cacheKey]) && isset($this->cache[$cacheKey]['email'])) {
      return $this->cache[$cacheKey]['email'];
    }
    $this->loadAddressRowInCacheProperty();

    return $this->cache[$cacheKey]['email'];
  }

  /**
   * @return string|null
   */
  public function GetName()
  {
    $cacheKey = $this->getCacheKey();
    if(!empty($this->cache[$cacheKey]) && isset($this->cache[$cacheKey]['name'])) {
      return $this->cache[$cacheKey]['name'];
    }
    $this->loadAddressRowInCacheProperty();

    return $this->cache[$cacheKey]['name'];
  }

  /**
   * @return array
   */
  public function GetSprachen()
  {
    $userId = (int)$this->GetId();
    $cacheKey = $this->getCacheKey();
    if(empty($this->cache[$cacheKey]) || !isset($this->cache[$cacheKey]['sprachebevorzugen'])) {
      $this->loadUserRowInCacheProperty($userId);
    }
    $defaultLanguages = ['german','english'];
    $languages = $this->cache[$cacheKey]['sprachebevorzugen'];

    if(empty($languages)) {
      return $defaultLanguages;
    }
    $ret = [];
    $languagesArray = explode(';',str_replace(',',';',$languages));
    foreach($languagesArray as $language) {
      $language = trim($language);
      if($language != '') {
        $ret[] = $language;
      }
    }
    if(empty($ret)) {
      return $ret;
    }

    return $defaultLanguages;
  }

  /**
   * @return string
   */
  public function GetSprache()
  {
    $sprachen = $this->GetSprachen();

    return reset($sprachen);
  }

  /**
   * @return int|null
   */
  public function GetAdresse()
  {
    $cacheKey = $this->getCacheKey();
    if(!empty($this->cache[$cacheKey]) && isset($this->cache[$cacheKey]['adresse'])) {
      return $this->cache[$cacheKey]['adresse'];
    }
    $this->loadUserRowInCacheProperty();

    return $this->cache[$cacheKey]['adresse'];
  }

  /**
   * @return bool
   */
  function GetProjektleiter()
  { 
    $result = $this->app->DB->SelectRow(
      "SELECT `parameter` 
      FROM `adresse_rolle` 
      WHERE `subjekt` = 'Projektleiter' AND (`bis` = '0000-00-00' OR `bis` <= CURDATE()) 
        AND `adresse` = '".$this->app->User->GetAdresse()."'
      LIMIT 1"
    );

    return !empty($result);
  }

  /**
   * @return int
   */
  function DefaultProjekt()
  {
    $adresse = $this->GetAdresse();
    $cacheKey = $this->getCacheKey();
    if(empty($this->cache[$cacheKey]) || !isset($this->cache[$cacheKey]['projekt'])) {
      $this->loadAddressRowInCacheProperty($adresse);
      $projekt = $this->cache[$cacheKey]['projekt'];
    }
    else {
      $projekt = $this->cache[$cacheKey]['projekt'];
    }
    if($projekt <=0){
      $projekt = $this->app->DB->Select(
        "SELECT `standardprojekt` FROM `firma` WHERE `id`='" . $this->app->User->GetFirma() . "' LIMIT 1"
      );
    }

    return $projekt;
  }

  /**
   * @return string|null
   */
  function GetEmail()
  {
    $cacheKey = $this->getCacheKey();
    if(!empty($this->cache[$cacheKey]) && isset($this->cache[$cacheKey]['email'])) {
      return $this->cache[$cacheKey]['email'];
    }
    $this->loadAddressRowInCacheProperty();

    return $this->cache[$cacheKey]['email'];
  }

  /**
   * @return int
   */
  public function GetFirma(): int
  {
    return 1;
  }

  /**
   * @return string
   */
  function GetFirmaName()
  {
    $cacheKey = $this->getCacheKey();
    if(!empty($this->cache[$cacheKey]) && isset($this->cache[$cacheKey]['firmaname'])) {
      return $this->cache[$cacheKey]['firmaname'];
    }
    $name = $this->app->DB->Select(sprintf('SELECT `name` FROM `firma` WHERE `id` = %d', $this->GetFirma()));
    $this->cache[$cacheKey]['firmaname'] = $name;

    return $name;
  }

  /**
   * @param string $field
   *
   * @return mixed
   */
  public function GetField($field)
  { 
    $value = $this->app->DB->Select(
      sprintf(
        'SELECT `%s` FROM `user` WHERE id = %d ',
        $field, $this->GetID()
      )
    );
    if(in_array($value, ['settings', 'type', 'username', 'adresse', 'sprachebevorzugen'])) {
      $cacheKey = $this->getCacheKey();
      $this->cache[$cacheKey][$field] = $value;
    }

    return $value;
  }

  /**
   * @param int|null $userId
   */
  protected function loadUserRowInCacheProperty(?int $userId = null): void
  {
    if($userId === null){
      $userId = (int)$this->GetID();
    }
    $cacheKey = $this->getCacheKey();
    $userData = (array)$this->app->DB->SelectRow(
      sprintf(
        'SELECT `settings`, `type`, `username`, `adresse`, `sprachebevorzugen` FROM `user` WHERE `id` = %d LIMIT 1',
        $userId
      )
    );
    if(!isset($this->cache[$cacheKey])) {
      $this->cache[$cacheKey] = $userData;
    }
    else{
      $this->cache[$cacheKey] = array_merge($this->cache[$cacheKey], $userData);
    }
  }

  /**
   * @param int|null $addressId
   */
  protected function loadAddressRowInCacheProperty(?int $addressId = null): void
  {
    if($addressId === null){
      $addressId = (int)$this->GetAdresse();
    }
    $cacheKey = $this->getCacheKey();
    $addressData = (array)$this->app->DB->SelectRow(
      sprintf('SELECT `name`, `email`, `projekt` FROM `adresse` WHERE `id` = %d LIMIT 1', $addressId)
    );
    if(!isset($this->cache[$cacheKey])) {
      $this->cache[$cacheKey] = $addressData;
    }
    else{
      $this->cache[$cacheKey] = array_merge($this->cache[$cacheKey], $addressData);
    }
  }

  /**
   * @return string
   */
  protected function getCacheKey(): string
  {
    return (string)$this->app->Conf->WFdbname;
  }


  protected function loadProjectsInCacheProperty(): void
  {
    $cacheKey = $this->getCacheKey();
    $projects = $this->app->DB->SelectPairs('SELECT `id`, `oeffentlich` FROM `projekt` WHERE `geloescht` <> 1');
    $this->cache[$cacheKey]['all_projects'] = array_keys($projects);
    $this->cache[$cacheKey]['public_projects'] = [];
    foreach($projects as $projectId => $public) {
      if($public) {
        $this->cache[$cacheKey]['public_projects'][] = $projectId;
      }
    }
  }


  /**
   * @param int    $addressId
   * @param string $type
   *
   * @return array
   */
  protected function getUserProjectsByParameter($addressId, $type)
  {
    if($type==='admin' ||
      $this->app->DB->Select(
        "SELECT `id` 
        FROM `adresse_rolle` 
        WHERE (`bis` IS NULL OR `bis` = '0000-00-00' OR `bis` <= CURDATE()) AND `adresse` = '".$addressId."' 
        AND (`parameter` = '' OR `parameter` = '0')"
      )
    ) {
      return $this->getAllProjects();
    }
    $public = $this->getPublicProjects();
    $roles =  $this->app->DB->SelectFirstCols(
      sprintf(
        "SELECT DISTINCT `parameter` 
        FROM `adresse_rolle` 
        WHERE (`bis` IS NULL OR `bis` = '0000-00-00' OR `bis` <= CURDATE()) AND `adresse` = %d 
          AND `parameter` >= 0 AND `objekt` LIKE 'Projekt'",
        $addressId
      )
    );
    $projects =  $this->app->DB->SelectFirstCols(
      sprintf(
        "SELECT DISTINCT `projekt` 
        FROM `adresse_rolle` 
        WHERE (`bis` IS NULL OR `bis` = '0000-00-00' OR bis <= CURDATE()) AND `adresse` = %d AND `projekt` > 0",
        $addressId
      )
    );

    return array_unique(array_merge($public, $roles, $projects));
  }
}

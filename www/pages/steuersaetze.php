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

use Xentral\Components\Http\JsonResponse;

class Steuersaetze {
  /** @var Application $app */
  protected $app;

  /**
   * @param Application $app
   * @param string      $name
   * @param array       $erlaubtevars
   *
   * @return array
   */
  public static function TableSearch($app, $name, $erlaubtevars)
  {
    // in dieses switch alle lokalen Tabellen (diese Live Tabellen mit Suche etc.) für dieses Modul
    switch($name)
    {
      case 'steuersaetze_list':
      $allowed['steuersaetze'] = ['list'];

        $heading = [
          'Steuersatz', 'Bezeichnung','Land', 'Projekt', 'Typ','gültig von', 'gültig bis', 'Aktiv', 'festsetzen', 'Men&uuml;'
        ];
        $width = ['10%', '20%','10%', '10%', '10%','10%','10%','1%','1%','1%'];

      $findcols = [
        's.satz',
        's.bezeichnung',
        's.country_code',
        "IF(s.project_id = 0,'-',IFNULL(p.abkuerzung,''))",
        "IF(s.type = '','manuell', s.type)",
        's.valid_from',
        's.valid_to',
        "if(s.aktiv=1,'ja','nein')",
        "if(s.set_data=1,'ja','nein')",
        's.id'
      ];
      $searchsql = ['s.satz', 's.bezeichnung', 's.country_code', 's.aktiv'];

      $numbercols = [0];
      $alignright = [1];
      $datecols = [5, 6];

      $defaultorder = 1;
      $defaultorderdesc = 0;

      $menu = '<table cellpadding="0" cellspacing="0">';
        $menu .= '<tr>';
          $menu .= '<td nowrap>';
            $menu .= '<a href="javascript:;" onclick="SteuersaetzeEdit(%value%);">';
              $menu .= '<img src="themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/edit.svg" border="0">';
            $menu .= '</a>&nbsp;';
            $menu .= '<a href="javascript:;" onclick="SteuersaetzeDelete(%value%);">';
              $menu .= '<img src="themes/' . $app->Conf->WFconf['defaulttheme'] . '/images/delete.svg" border="0">';
            $menu .= '</a>';
          $menu .= '</td>';
        $menu .= '</tr>';
      $menu .= '</table>';

      $where = " s.id > 0 ";

      $sql = "SELECT SQL_CALC_FOUND_ROWS s.id, 
                   s.satz, s.bezeichnung, s.country_code, IF(s.project_id = 0,'-',IFNULL(p.abkuerzung,'')),
                           IF(s.type = '','manuell', s.type),
                   IFNULL(DATE_FORMAT(s.valid_from,'%d.%m.%Y'),'-'), IFNULL(DATE_FORMAT(s.valid_to,'%d.%m.%Y'),'-'),
                           if(s.aktiv=1,'ja','nein'), 
                           if(s.set_data=1,'ja','nein'), 
                           s.id 
        FROM `steuersaetze` AS `s`
        LEFT JOIN `projekt` AS `p` ON s.project_id = p.id ";

      $count = "SELECT count(s.id) 
        FROM `steuersaetze` AS `s` 
        LEFT JOIN `projekt` AS `p` ON s.project_id = p.id 
        WHERE $where";
      break;

    }

    $erg = [];

    foreach($erlaubtevars as $k => $v) {
      if(isset($$v)) {
        $erg[$v] = $$v;
      }
    }

    return $erg;
  }

  /**
   * Steuersaetze constructor.
   *
   * @param Application $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false) {
    $this->app=$app;
    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);

    // ab hier alle Action Handler definieren die das Modul hat
    $this->app->ActionHandler('list', 'SteuersaetzeList');
    $this->app->ActionHandler('edit', 'SteuersaetzeEdit');
    $this->app->ActionHandler('save', 'SteuersaetzeSave');
    $this->app->ActionHandler('delete', 'SteuersaetzeDelete');
    
    $this->app->ActionHandlerListen($app);
  }

  public function Install()
  {
    $this->app->erp->RegisterHook('getTaxRatesFromShopOrder','steuersaetze','HookGetTaxRatesFromShopOrder');
  }

  /**
   * @param string $country
   * @param array  $taxes
   */
  public function HookGetTaxRatesFromShopOrder($country, &$taxes)
  {
    $taxesbycountry = $this->getTaxesByCountry($country);
    if(!empty($taxesbycountry)) {
      $taxes = array_merge($taxes, $taxesbycountry);
    }
  }

  /**
   * @param string $country
   *
   * @return array
   */
  public function getTaxesByCountry($country)
  {
    $ret = [];
    $taxes = $this->app->DB->SelectArr(
      sprintf(
        "SELECT DISTINCT `satz` 
        FROM `steuersaetze` 
        WHERE `aktiv` = 1 
          AND (`country_code` = '%s' OR (`bezeichnung` = '%s' AND `country_code` = ''))",
        $country, $country
      )
    );
    if(!empty($taxes)) {
      foreach($taxes as $rates) {
        $ret[] = $rates['satz'];
      }
    }

    return $ret;
  }

  public function SteuersaetzeMenu()
  {
    $this->app->erp->MenuEintrag('index.php?module=steuersaetze&action=list','Zur&uuml;ck zur &Uuml;bersicht');
  }

  public function SteuersaetzeList()
  {
    /** @var Vatreduction2020 $obj */
    $obj = $this->app->loadModule('vatreduction2020');
    if($obj !== null) {
      $createTaxes = !empty($this->app->Secure->GetPOST('createtaxes'));
      $hasTaxes = $obj->hasSpecialTaxes();
      if($createTaxes){
        $obj->createTaxes($hasTaxes);
        $this->app->Location->execute('index.php?module=steuersaetze&action=list');
      }
      $obj->displayTaxMessageIfNeeded($hasTaxes);
    }
    $this->app->erp->MenuEintrag('index.php?module=steuersaetze&action=list','&Uuml;bersicht');
    $this->app->erp->Headlines('Steuers&auml;tze');
    $this->app->YUI->AutoComplete('project_neu', 'projektname');
    $this->app->YUI->AutoComplete('project', 'projektname');
    $this->app->YUI->DatePicker('valid_from');
    $this->app->YUI->DatePicker('valid_from_neu');
    $this->app->YUI->DatePicker('valid_to');
    $this->app->YUI->DatePicker('valid_to_neu');
    $this->app->YUI->TableSearch('TAB1','steuersaetze_list', 'show','','',basename(__FILE__), __CLASS__);
    $this->app->erp->MenuEintrag('javascript:SteuersaetzeEdit(0);', 'Neu');
    $this->app->Tpl->Set('SELCOUNTRYCODE', $this->app->erp->SelectLaenderliste());
    $this->app->Tpl->Parse('PAGE','steuersaetze_list.tpl');
  }

  public function checkTaxesToSet()
  {
    $taxes = $this->app->DB->SelectArr(
      "SELECT s.satz, s.`set_data`, s.project_id, s.type, p.steuersatz_ermaessigt, p.steuersatz_normal
      FROM `steuersaetze` AS `s` 
      LEFT JOIN `projekt` AS `p` ON s.project_id = p.id  
      WHERE s.aktiv = 1 AND s.set_data = 1 AND (s.type = 'normal' OR s.type = 'ermaessigt')
          AND (s.valid_from IS NULL OR s.valid_from = '0000-00-00' OR s.valid_from <= CURDATE())
          AND (s.valid_to IS NULL OR s.valid_to = '0000-00-00' OR s.valid_to >= CURDATE())
      ORDER BY s.`project_id` DESC,
               (s.`valid_to` IS NULL OR s.valid_to = '0000-00-00') 
                 AND (s.`valid_from` IS NULL OR s.valid_from = '0000-00-00') DESC,
               s.`valid_to` IS NULL OR s.valid_to = '0000-00-00' DESC, 
               s.`valid_from` IS NULL OR s.valid_from = '0000-00-00' DESC,
               s.id DESC"
    );
    if(empty($taxes)) {
      return;
    }
    foreach($taxes as $tax) {
      $type = $tax['type'];
      if(!empty($tax['project_id'])) {
        if($type === 'ermaessigt' && $tax['satz'] != $tax['steuersatz_ermaessigt']) {
          $this->app->DB->Update(
            sprintf(
              'UPDATE `projekt` SET `steuersatz_ermaessigt` = %f WHERE `id` = %d',
              $tax['satz'], $tax['project_id']
            )
          );
        }
        elseif($type === 'normal' && $tax['satz'] != $tax['steuersatz_normal']) {
          $this->app->DB->Update(
            sprintf(
              'UPDATE `projekt` SET `steuersatz_normal` = %f WHERE `id` = %d',
              $tax['satz'], $tax['project_id']
            )
          );
        }
      }
      else {
        if($type === 'ermaessigt' && $tax['satz'] != $this->app->erp->Firmendaten('steuersatz_ermaessigt')) {
          $this->app->erp->FirmendatenSet('steuersatz_ermaessigt', $tax['satz']);
        }
        elseif($type === 'normal' && $tax['satz'] != $this->app->erp->Firmendaten('steuersatz_normal')) {
          $this->app->erp->FirmendatenSet('steuersatz_normal', $tax['satz']);
        }
      }
    }
  }

  /**
   * @param int         $id
   * @param float       $satz
   * @param string      $bezeichnung
   * @param int         $projectId
   * @param string|null $validFrom
   * @param string|null $validTo
   * @param string      $countryCode
   *
   * @return string
   */
  public function checkInputForError(
    $id, $satz, $bezeichnung, $type = '', $projectId = 0, $validFrom = null, $validTo = null, $countryCode = ''
  )
  {
    $error = [];
    if($satz === ''){
      $error[] = 'Bitte einen gültigen Steuersatz eingeben';
    }
    if($satz < 0) {
      $error[] = 'Bitte einen positiven Steuersatz eingeben';
    }
    if(trim($bezeichnung) === ''){
      $error[] = 'Bitte Bezeichnung eingeben';
    }

    $validFromTime = $validFrom === null?null:strtotime($validFrom);
    $validToTime = $validTo === null?null:strtotime($validTo);

    if($validFrom !== null && $validTo !== null && $validFromTime > $validToTime) {
      $error[] = 'Datumsbereich ist ungültig';
    }

    if(!empty($error)) {
      return implode("\n", $error);
    }

    $dbEntries = $this->app->DB->Select(
      sprintf(
        "SELECT * 
        FROM `steuersaetze` 
        WHERE `aktiv` = 1 AND `type` = '%s' AND `project_id` = %d AND `country_code` = '%s'",
        $type, $projectId, $countryCode
      )
    );
    if(!empty($dbEntries)) {
      foreach($dbEntries as $dbEntry) {
        if((int)$dbEntry['id'] === (int)$id) {
          continue;
        }
        $dbFrom = $this->dateToDb($dbEntry['valid_from']);
        $dbTo = $this->dateToDb($dbEntry['valid_to']);
        if($dbFrom === null && $dbTo === null) {
          if($validFrom === null && $validTo === null){
            $error[] = 'Es existiert bereits ein Eintrag';
            return implode("\n", $error);
          }
          continue;
        }
        if($dbFrom !== null && $dbTo !== null) {
          if($validFrom === null && $validTo === null){
            continue;
          }
          $dbFromTime = strtotime($dbFrom);
          $dbToTime = strtotime($dbTo);
          if($validFrom !== null && $validTo !== null) {
            if($validFromTime === $dbFromTime && $validToTime === $dbToTime) {
              $error[] = 'Es existiert bereits ein Eintrag mit diesem Zeitbereich';
              return implode("\n", $error);
            }
            if($validFromTime > $dbToTime || $validToTime < $dbFromTime) {
              continue;
            }
            if($validFromTime < $dbToTime && $validToTime > $dbToTime) {
              $error[] = 'Es überschneiden sich Einträge';
              return implode("\n", $error);
            }
            if($validFromTime < $dbFromTime && $validToTime > $dbFromTime) {
              $error[] = 'Es überschneiden sich Einträge';
              return implode("\n", $error);
            }
            if($validFromTime >= $dbFromTime && $validToTime <= $dbToTime) {
              $error[] = 'Es überschneiden sich Einträge';
              return implode("\n", $error);
            }
            if($validFromTime <= $dbFromTime && $validToTime >= $dbToTime) {
              $error[] = 'Es überschneiden sich Einträge';
              return implode("\n", $error);
            }
          }
        }
      }
    }

    return implode("\n", $error);
  }

  /**
   * @return JsonResponse|void
   */
  public function SteuersaetzeEdit()
  {
    if($this->app->Secure->GetGET('cmd')==='get'){
      //$this->app->erp->MenuEintrag("index.php?module=zeiterfassung_kosten&action=edit&id=".$adresse,"Details");
      $id = (int)$this->app->Secure->GetPOST('id');
      $data = null;
      if($id > 0){
        $data = $this->app->DB->SelectRow(
          sprintf(
            "SELECT s.`id`, s.`satz`, s.`bezeichnung`, s.`aktiv`, s.`set_data`, s.type, 
                IFNULL(p.abkuerzung,'') AS `project`, 
            IF(
                s.valid_from IS NULL OR s.valid_from = '0000-00-00',
                '',
                DATE_FORMAT(s.valid_from,'%%d.%%m.%%Y')
            ) AS `valid_from`, 
            IF(
                s.valid_to IS NULL OR s.valid_to = '0000-00-00',
                '',
                DATE_FORMAT(s.valid_to,'%%d.%%m.%%Y')
            ) AS `valid_to`, s.country_code 
            FROM `steuersaetze` AS `s`
            LEFT JOIN `projekt` AS `p` ON s.project_id = p.id
            WHERE s.`id` = %d LIMIT 1",
            $id
          )
        );
      }

      return new JsonResponse($data);
    }

    $this->app->Tpl->Parse('PAGE', 'steuersaetze_list.tpl');
  }

  /**
   * @param string $date
   *
   * @return string|null
   */
  public function dateToDb($date)
  {
    if($date === '..' || $date === '') {
      return null;
    }
    if(strpos($date,'.') !== false) {
      return $this->app->String->Convert($date,'%1.%2.%3','%3-%2-%1');
    }

    return $date;
  }

  /**
   * @return JsonResponse
   */
  public function SteuersaetzeSave()
  {
    $id = (int)$this->app->Secure->GetPOST('id');
    $satz = $this->app->Secure->GetPOST('satz');
    $bezeichnung = $this->app->Secure->GetPOST('bezeichnung');
    $aktiv = $this->app->Secure->GetPOST('aktiv');
    $set_data = $this->app->Secure->GetPOST('set_data');
    $type = $this->app->Secure->GetPOST('type');
    $project = explode(' ', $this->app->Secure->GetPOST('project'));
    $project = reset($project);
    $project = empty($project)?0:(int)$this->app->erp->ReplaceProjekt(1, $project, 1);
    $countryCode = $this->app->Secure->GetPOST('country_code');
    $valid_from = $this->dateToDb($this->app->Secure->GetPOST('valid_from'));
    $valid_to = $this->dateToDb($this->app->Secure->GetPOST('valid_to'));
    $bearbeiter = $this->app->DB->real_escape_string($this->app->User->GetName());
    $satz = (float)str_replace(',','.',$satz);
    $error = $this->checkInputForError(
      $id, $satz, $bezeichnung, $type, $project, $valid_from, $valid_to, $countryCode
    );
    if($error !== '') {
      return new JsonResponse(['status'=>0,'statusText'=>$error]);
    }
    if($id <= 0) {
      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO `steuersaetze` 
            (`satz`, `bezeichnung`, `aktiv`, `bearbeiter`, `project_id`, 
             `type`, `valid_from`, `valid_to`,`set_data`, `country_code`) 
            VALUES (%f, '%s', '%d','%s', %d, '%s', %s, %s, %d, '%s')",
          $satz, $bezeichnung, (int)$aktiv, $bearbeiter, $project, $type,
          $valid_from === null?'NULL':"'".$valid_from."'",
          $valid_to === null?'NULL':"'".$valid_to."'",
          (int)$set_data, $countryCode
        )
      );

      return new JsonResponse(['status' => 1]);
    }

    $id = (int)$this->app->DB->Select(
      sprintf(
        'SELECT `id` FROM `steuersaetze` WHERE `id` = %d LIMIT 1',
        $id
      )
    );
    if($id <= 0) {
      $error = 'Datensatz nicht gefunden';
      return new JsonResponse(['status'=>0,'statusText'=>$error]);
    }

    $this->app->DB->Update(
      sprintf(
        "UPDATE `steuersaetze` 
        SET `satz` = %f, `bezeichnung` = '%s', `aktiv` = %d, `bearbeiter` = '%s', `project_id` = %d, `type` = '%s',
            `valid_from` = %s, `valid_to` = %s, `set_data` = %d, `country_code` = '%s'
        WHERE `id` = %d
        LIMIT 1",
        $satz, $bezeichnung, (int)$aktiv, $bearbeiter, $project, $type,
        $valid_from === null?'NULL':"'".$valid_from."'",
        $valid_to === null?'NULL':"'".$valid_to."'",
        (int)$set_data, $countryCode,
        $id
      )
    );

    return new JsonResponse(['status' => 1]);
  }

  /**
   * @return JsonResponse
   */
  public function SteuersaetzeDelete()
  {
    $id = (int) $this->app->Secure->GetPOST('id');
    $this->app->DB->Delete(
      sprintf(
        'DELETE FROM `steuersaetze` WHERE `id` = %d LIMIT 1',
        $id
      )
    );

    return new JsonResponse(['status' => 1]);
  }

}

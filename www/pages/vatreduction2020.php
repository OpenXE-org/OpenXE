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
class Vatreduction2020
{
  /** @var Application $app */
  var $app;

  /**
   * Vatreduction2020List constructor.
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

    $this->app->ActionHandler("list","Vatreduction2020List");

    $this->app->ActionHandlerListen($app);

    $this->app->erp->Headlines('Auftragsblocker');
  }

  public function Install()
  {
    $this->app->erp->RegisterHook('BelegFreigabe', 'vatreduction2020', 'HookBelegFreigabe');
    $this->app->erp->RegisterHook('ANABREGSNeuberechnen_1', 'vatreduction2020', 'HookRecalcDoctype');
    $this->app->erp->RegisterHook('Shopimport', 'vatreduction2020', 'HookShopimport');
  }

  /**
   * @param string $doctype
   * @param int    $doctypeId
   */
  public function HookRecalcDoctype($doctype, $doctypeId)
  {
    $this->HookBelegFreigabe($doctype, $doctypeId);
  }

  /**
   * @param string $doctype
   * @param int    $tmpauftragid
   * @param int    $shopid
   */
  public function HookShopimport($doctype, $tmpauftragid, $shopid)
  {
    if($doctype !== 'auftrag' || empty($tmpauftragid)
      || empty($this->app->erp->GetKonfiguration('vatreduction2020_active'))
      ) {
      return;
    }
    $this->HookBelegFreigabe($doctype, $tmpauftragid);
    $date = (string)$this->app->erp->GetKonfiguration('vatreduction2020_date');
    if($date === '') {
      $date = '2020-07-01';
    }
    if(strpos($date,'.') !== false) {
      $date = $this->app->String->Convert($date, '%1.%2.%3', '%3-%2-%1');
    }
    $date = (new DateTime($date))->format('Y-m-d');
    $this->app->DB->Update(
      sprintf(
        "UPDATE `auftrag` 
        SET `autoversand` = 0 
        WHERE `id` = %d AND `autoversand` = 1 AND `datum` < '%s'
        AND `status` = 'freigegeben'",
        $tmpauftragid, $date
      )
    );
  }

  /**
   * @param string $doctype
   * @param int    $doctypeId
   */
  public function HookBelegFreigabe($doctype, $doctypeId)
  {
    if(
      $doctype !== 'auftrag'
      || $doctypeId <= 0
      || empty($this->app->erp->GetKonfiguration('vatreduction2020_active'))
    ) {
      return;
    }
    if($this->app->DB->Select(
      sprintf(
        "SELECT `auftrag` 
        FROM `auftrag_protokoll` 
        WHERE `auftrag` = %d AND `grund` = 'Autoversandfreigabe durch Auftragsblocker entfernt' 
        LIMIT 1",
        $doctypeId
      )
    )
    ) {
      return;
    }
    $date = (string)$this->app->erp->GetKonfiguration('vatreduction2020_date');
    if($date === '') {
      $date = '2020-07-01';
    }
    if(strpos($date,'.') !== false) {
      $date = $this->app->String->Convert($date, '%1.%2.%3', '%3-%2-%1');
    }
    $date = (new DateTime($date))->format('Y-m-d');
    $this->app->DB->Update(
      sprintf(
        "UPDATE `auftrag` 
        SET `autoversand` = 0 
        WHERE `id` = %d AND `autoversand` = 1 AND `datum` < '%s'
        AND `status` = 'freigegeben'",
        $doctypeId, $date
      )
    );
    if($this->app->DB->affected_rows() > 0) {
      $this->app->erp->AuftragProtokoll($doctypeId, 'Autoversandfreigabe durch Auftragsblocker entfernt');
    }
  }

  /**
   * @param null|bool $hasSpecialTaxes
   */
  public function displayTaxMessageIfNeeded($hasSpecialTaxes = null)
  {
    $hasSpecialTaxes = $hasSpecialTaxes !== null?$hasSpecialTaxes:$this->hasSpecialTaxes();
    if($hasSpecialTaxes) {
      return;
    }
    $this->app->Tpl->Add(
      'MESSAGE',
      '<div class="warning">
        <form id="frmsubmit" method="post">{|Es extistieren noch keine Steuereinträge|}
            <input type="submit" value="Steuersätze für 2020 laden" name="createtaxes" />
        </form>
      </div>'
    );
  }

  /**
   * @return bool
   */
  public function hasSpecialTaxes()
  {
    return $this->app->DB->Select(
        sprintf(
          "SELECT `id` 
          FROM `steuersaetze` 
          WHERE `set_data` = 1 AND `satz` = 16 AND `type` = 'normal'
              AND (`country_code` = '' OR `country_code` = 'DE') 
          LIMIT 1"
        )
      ) || $this->app->DB->Select(
        sprintf(
          "SELECT `id` 
          FROM `steuersaetze` 
          WHERE `set_data` = 1 AND `satz` = 5 AND `type` = 'ermaessigt'
              AND (`country_code` = '' OR `country_code` = 'DE') 
          LIMIT 1"
        )
      );
  }

  /**
   * @param null|bool $hasSpecialTaxes
   */
  public function createTaxes($hasSpecialTaxes = null)
  {
    $hasSpecialTaxes = $hasSpecialTaxes !== null?$hasSpecialTaxes:$this->hasSpecialTaxes();
    if($hasSpecialTaxes) {
      return;
    }

    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `steuersaetze` 
              (`bezeichnung`, `satz`, `aktiv`, `bearbeiter`, `zeitstempel`, `project_id`, 
               `valid_from`, `valid_to`, `type`, `set_data`, `country_code`) VALUES 
              ('2020 normal', 16, 1, '', NOW(), 0, '2020-07-01', '2020-12-31', 'normal', 1, 'DE')"
      )
    );
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `steuersaetze` 
              (`bezeichnung`, `satz`, `aktiv`, `bearbeiter`, `zeitstempel`, `project_id`, 
               `valid_from`, `valid_to`, `type`, `set_data`, `country_code`) VALUES 
              ('normal', 19, 1, '', NOW(), 0, '2021-01-01', NULL, 'normal', 1, 'DE')"
      )
    );
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `steuersaetze` 
              (`bezeichnung`, `satz`, `aktiv`, `bearbeiter`, `zeitstempel`, `project_id`, 
               `valid_from`, `valid_to`, `type`, `set_data`, `country_code`) VALUES 
              ('2020 ermäßigt', 5, 1, '', NOW(), 0, '2020-07-01', '2020-12-31', 'ermaessigt', 1, 'DE')"
      )
    );
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `steuersaetze` 
              (`bezeichnung`, `satz`, `aktiv`, `bearbeiter`, `zeitstempel`, `project_id`, 
               `valid_from`, `valid_to`, `type`, `set_data`, `country_code`) VALUES 
              ('ermäßigt', 7, 1, '', NOW(), 0, '2021-01-01', NULL, 'ermaessigt', 1, 'DE')"
      )
    );
  }

  public function Vatreduction2020List()
  {
    $createTaxes = !empty($this->app->Secure->GetPOST('createtaxes'));
    $hasTaxes = $this->hasSpecialTaxes();
    if($createTaxes) {
      $this->createTaxes($hasTaxes);
      $this->app->Location->execute('index.php?module=vatreduction2020&action=list');
    }

    $isActive = !empty($this->app->erp->GetKonfiguration('vatreduction2020_active'));
    $date = (string)$this->app->erp->GetKonfiguration('vatreduction2020_date');
    if($date === '') {
      $date = '2020-07-01';
      $this->app->erp->SetKonfigurationValue('vatreduction2020_date', $date);
    }
    if(strpos($date, '-') !== false) {
      $date = $this->app->String->Convert($date, '%1-%2-%3', '%3.%2.%1');
    }
    $this->app->Tpl->Set('VATREDUCTION2020_DATE', $date);
    $this->app->YUI->DatePicker('vatreduction2020_date');
    if($isActive) {
      $this->app->Tpl->Set('ISACTIVE', ' checked="checked" ');
    }
    $this->app->YUI->AutoSaveKonfiguration('vatreduction2020_active', 'vatreduction2020_active');
    $this->app->YUI->AutoSaveKonfiguration('vatreduction2020_date', 'vatreduction2020_date');
    $this->displayTaxMessageIfNeeded($hasTaxes);
    $this->app->erp->Headlines('Auftragsblocker');
    $this->app->erp->MenuEintrag('index.php?module=vatreduction2020&action=list', '&Uuml;bersicht');
    $this->app->Tpl->Parse('PAGE', 'vatreduction2020_list.tpl');
  }
}

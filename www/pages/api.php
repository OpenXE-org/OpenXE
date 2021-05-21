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

use Xentral\Modules\Api\Auth\PermissionGuard;
use Xentral\Modules\ApiAccount\Exception\ApiAccountNotFoundException;
use Xentral\Modules\SystemConfig\SystemConfigModule;

if (!function_exists('getallheaders')) {
    /**
     * @return array
     */
    function getallheaders() {
      $headers = array();
      foreach ($_SERVER as $name => $value) {
        if (substr($name, 0, 5) == 'HTTP_') {
          $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
        }
      }
      return $headers;
    }
  }


  if(!class_exists('SimpleXMLExtended')) {
    class SimpleXMLExtended extends SimpleXMLElement
    {
      /**
       * @param $cdata_text
       */
      public function addCData($cdata_text)
      {
        $node = dom_import_simplexml($this);
        $no = $node->ownerDocument;
        $node->appendChild($no->createCDATASection($cdata_text));
      }
    }
  }

  class Api {
    /** @var Application $app */
  var $app;
  var $api_id;
  var $uebertragung_account;
  var $usejson;
  var $usecdata;
  var $datei_id;
  var $lagerzahlenmonitor;
  var $lagerzahlen;

  /** @var \Xentral\Modules\ApiAccount\Service\ApiAccountService */
  protected $apiAccountService;

  /** @var \Xentral\Modules\ApiAccount\Data\ApiAccountData */
  protected $apiAccount;

  /** @var array $bestBeforeBatchArticleStock */
  protected $bestBeforeBatchArticleStock;

    /**
     * Api constructor.
     *
     * @param Application $app
     * @param bool        $intern
     */
  public function __construct($app, $intern = false) {

//    header("Content-Type: text/html; charset=utf-8");

    $this->app=$app;
    $this->usejson = false;
    $this->usecdata = false;
    $this->api_id = 0;
    $this->datei_id = 0;
    $this->apiAccountService = $this->app->Container->get('ApiAccountService');

    if($intern) {
      return;
    }
    $this->app->ActionHandlerInit($this);
    // eine alte Funktion
    $this->app->ActionHandler("shopimages","ApiShopimages");

    //Stechuhr
    $this->app->ActionHandler("ServerTimeGet","ApiServerTimeGet");
    $this->app->ActionHandler("BenutzerGetRFID","ApiBenutzerGetRFID");
    $this->app->ActionHandler("BenutzerList","ApiBenutzerList");
    $this->app->ActionHandler("StechuhrStatusGet","ApiStechuhrStatusGet");
    $this->app->ActionHandler("StechuhrStatusSet","ApiStechuhrStatusSet");
    $this->app->ActionHandler("StechuhrSummary","ApiStechuhrSummary");

    $this->app->ActionHandler("AdresseCreate","ApiAdresseCreate");
    $this->app->ActionHandler("AdresseEdit","ApiAdresseEdit");
    $this->app->ActionHandler("AdresseGet","ApiAdresseGet");

    $this->app->ActionHandler("AnsprechpartnerCreate","ApiAnsprechpartnerCreate");
    $this->app->ActionHandler("AnsprechpartnerEdit","ApiAnsprechpartnerEdit");

    $this->app->ActionHandler("LieferadresseCreate","ApiLieferadresseCreate");
    $this->app->ActionHandler("LieferadresseEdit","ApiLieferadresseEdit");

    $this->app->ActionHandler("AdresseAboGruppeCreate","ApiAdresseAboGruppeCreate");
    $this->app->ActionHandler("AdresseAboGruppeEdit","ApiAdresseAboGruppeEdit");
    $this->app->ActionHandler("AdresseAboGruppeGet","ApiAdresseAboGruppeGet");
    $this->app->ActionHandler("AdresseAboGruppeList","ApiAdresseAboGruppeList");

    $this->app->ActionHandler("AdresseAboArtikelCreate","ApiAdresseAboArtikelCreate");
    $this->app->ActionHandler("AdresseAboArtikelEdit","ApiAdresseAboArtikelEdit");
    $this->app->ActionHandler("AdresseAboArtikelGet","ApiAdresseAboArtikelGet");
    $this->app->ActionHandler("AdresseAboArtikelList","ApiAdresseAboArtikelList");

    $this->app->ActionHandler("BerichteGet", "ApiBerichteGet");

    $this->app->ActionHandler("ArtikelList", "ApiArtikelList");
    $this->app->ActionHandler("ArtikelStueckliste", "ApiArtikelStueckliste");

    $this->app->ActionHandler("AuftragCreate","ApiAuftragCreate");
    $this->app->ActionHandler("AuftragEdit","ApiAuftragEdit");
    $this->app->ActionHandler("AuftragGet","ApiAuftragGet");

    $this->app->ActionHandler("AngebotCreate","ApiAngebotCreate");
    $this->app->ActionHandler("AngebotEdit","ApiAngebotEdit");
    $this->app->ActionHandler("AngebotGet","ApiAngebotGet");

    $this->app->ActionHandler("GutschriftGet","ApiGutschriftGet");
    $this->app->ActionHandler("GutschriftEdit","ApiGutschriftEdit");
    $this->app->ActionHandler("GutschriftCreate","ApiGutschriftCreate");

    $this->app->ActionHandler("LieferscheinGet","ApiLieferscheinGet");
    $this->app->ActionHandler("LieferscheinEdit","ApiLieferscheinEdit");
    $this->app->ActionHandler("LieferscheinCreate","ApiLieferscheinCreate");

    $this->app->ActionHandler("LieferscheinFreigabe", "ApiLieferscheinFreigabe");
    $this->app->ActionHandler("RechnungFreigabe", "ApiRechnungFreigabe");
    $this->app->ActionHandler("AuftragFreigabe", "ApiAuftragFreigabe");
    $this->app->ActionHandler("AngebotFreigabe", "ApiAngebotFreigabe");
    $this->app->ActionHandler("GutschriftFreigabe", "ApiGutschriftFreigabe");
    $this->app->ActionHandler("BestellungFreigabe", "ApiBestellungFreigabe");

    $this->app->ActionHandler("AuftragAbschliessen", "ApiAuftragAbschliessen");
    $this->app->ActionHandler("RechnungVersendetMarkieren", "ApiRechnungVersendetMarkieren");
    $this->app->ActionHandler("RechnungAlsBezahltMarkieren", "ApiRechnungAlsBezahltMarkieren");

    $this->app->ActionHandler("WeiterfuehrenAuftragZuRechnung", "ApiWeiterfuehrenAuftragZuRechnung");
    $this->app->ActionHandler("WeiterfuehrenRechnungZuGutschrift", "ApiWeiterfuehrenRechnungZuGutschrift");

    $this->app->ActionHandler("RechnungVersenden", "ApiRechnungVersenden");
    $this->app->ActionHandler("AngebotVersenden", "ApiAngebotVersenden");
    $this->app->ActionHandler("GutschriftVersenden", "ApiGutschriftVersenden");
    $this->app->ActionHandler("AuftragVersenden", "ApiAuftragVersenden");
    $this->app->ActionHandler("LieferscheinVersenden", "ApiLieferscheinVersenden");
    $this->app->ActionHandler("ReisekostenVersenden", "ApiReisekostenVersenden");

    $this->app->ActionHandler("AuftragArchivieren", "ApiAuftragArchivieren");
    $this->app->ActionHandler("AngebotArchivieren", "ApiAngebotArchivieren");
    $this->app->ActionHandler("LieferscheinArchivieren", "ApiLieferscheinArchivieren");
    $this->app->ActionHandler("RechnungArchivieren", "ApiRechnungArchivieren");
    $this->app->ActionHandler("GutschriftArchivieren", "ApiGutschriftArchivieren");

    $this->app->ActionHandler("RetoureGet","ApiRetoureGet");
    $this->app->ActionHandler("RetoureEdit","ApiRetoureEdit");
    $this->app->ActionHandler("RetoureCreate","ApiRetoureCreate");

    $this->app->ActionHandler("RechnungGet","ApiRechnungGet");
    $this->app->ActionHandler("RechnungEdit","ApiRechnungEdit");
    $this->app->ActionHandler("RechnungCreate","ApiRechnungCreate");

    $this->app->ActionHandler("BestellungGet","ApiBestellungGet");
    $this->app->ActionHandler("BestellungEdit","ApiBestellungEdit");
    $this->app->ActionHandler("BestellungCreate","ApiBestellungCreate");

    $this->app->ActionHandler("ArtikelCreate","ApiArtikelCreate");
    $this->app->ActionHandler("ArtikelEdit","ApiArtikelEdit");
    $this->app->ActionHandler("ArtikelGet","ApiArtikelGet");

    $this->app->ActionHandler("ArtikelStuecklisteCreate", "ApiArtikelStuecklisteCreate");
    $this->app->ActionHandler("ArtikelStuecklisteEdit", "ApiArtikelStuecklisteEdit");
    $this->app->ActionHandler("ArtikelStuecklisteList", "ApiArtikelStuecklisteList");

    $this->app->ActionHandler("BenutzerCreate","ApiBenutzerCreate");
    $this->app->ActionHandler("BenutzerEdit","ApiBenutzerEdit");
    $this->app->ActionHandler("BenutzerGet","ApiBenutzerGet");

    $this->app->ActionHandler("SessionStart","ApiSessionStart");
    $this->app->ActionHandler("SessionClose","ApiSessionClose");

    $this->app->ActionHandler("AdresseKontaktCreate","ApiAdresseKontaktCreate");
    $this->app->ActionHandler("AdresseKontaktEdit","ApiAdresseKontaktEdit");
    $this->app->ActionHandler("AdresseKontaktGet","ApiAdresseKontaktGet");
    $this->app->ActionHandler("AdresseKontaktList","ApiAdresseKontaktList");

    $this->app->ActionHandler("AdresseListeGet","ApiAdresseListeGet");

    $this->app->ActionHandler("GruppeCreate","ApiGruppeCreate");
    $this->app->ActionHandler("GruppeEdit","ApiGruppeEdit");
    $this->app->ActionHandler("GruppeGet","ApiGruppeGet");

    $this->app->ActionHandler("GruppenList","ApiGruppenList");

    $this->app->ActionHandler("PreiseEdit","ApiPreiseEdit");

    $this->app->ActionHandler("ExportVorlageGet","ApiExportVorlageGet");
    $this->app->ActionHandler("AdresseAccountsGet","ApiAdresseAccountsGet");
    $this->app->ActionHandler("ArtikelkontingenteGet","ApiArtikelkontingenteGet");

    $this->app->ActionHandler("MappingGet","ApiMappingGet");
    $this->app->ActionHandler("MappingSet","ApiMappingSet");

    $this->app->ActionHandler("BelegeList","ApiBelegeList");
    $this->app->ActionHandler("ArtikelkategorienList","ApiArtikelkategorienList");

    $this->app->ActionHandler("AccountList", "ApiAccountList");
    $this->app->ActionHandler("AdresseGruppenList", "ApiAdresseGruppenList");
    $this->app->ActionHandler("AccountLogin", "ApiAccountLogin");
    $this->app->ActionHandler("AccountCreate", "ApiAdresseAccountCreate");
    $this->app->ActionHandler("AccountEdit", "ApiAdresseAccountEdit");

    $this->app->ActionHandler("DateiList", "ApiDateiList");
    $this->app->ActionHandler("DateiVorschau", "ApiDateiVorschau");
    $this->app->ActionHandler("DateiDownload", "ApiDateiDownload");
    $this->app->ActionHandler("DateiHeader", "ApiDateiHeader");

    $this->app->ActionHandler("BelegPDF", "ApiBelegPDF");
    $this->app->ActionHandler("BelegPDFHeader", "ApiBelegPDFHeader");

    $this->app->ActionHandler("ZeiterfassungGet", "ApiZeiterfassungGet");
    $this->app->ActionHandler("ZeiterfassungCreate", "ApiZeiterfassungCreate");
    $this->app->ActionHandler("ZeiterfassungEdit", "ApiZeiterfassungEdit");
    $this->app->ActionHandler("ZeiterfassungDelete", "ApiZeiterfassungDelete");

    $this->app->ActionHandler("ProjektListe", "ApiProjektListe");
    $this->app->ActionHandler("ProjektGet", "ApiProjektGet");
    $this->app->ActionHandler("ProjektCreate", "ApiProjektCreate");
    $this->app->ActionHandler("ProjektEdit", "ApiProjektEdit");

    $this->app->ActionHandler("AngebotZuAuftrag", "ApiAngebotZuAuftrag");
    $this->app->ActionHandler("AuftragZuRechnung", "ApiAuftragZuRechnung");
    $this->app->ActionHandler("Etikettendrucker", "ApiEtikettendrucker");
    $this->app->ActionHandler("Custom", "ApiCustom");

    $this->app->ActionHandler("ApiXMLTest", "ApiXMLTest");

    $allHeaders = getallheaders();
    if (isset($allHeaders['Accept']) && strpos($allHeaders['Accept'], 'json') !== false) {
      $this->usejson = true;
    }

    $this->CheckHash();

    $this->app->ActionHandlerListen($app);
  }

    public function Install()
    {
      $this->app->erp->CheckTable('api_permission');
      $this->app->erp->CheckColumn('key', 'varchar(255)', 'api_permission');
      $this->app->erp->CheckIndex('api_permission', 'key', true);
      $this->app->erp->CheckColumn('group', 'varchar(255)', 'api_permission');
      $this->fillApiPermissions();

      $this->app->erp->CheckColumn('permissions', 'text', 'api_account');
      $this->app->erp->CheckColumn('ishtmltransformation','TINYINT(1)','api_account','DEFAULT \'0\' NOT NULL');
      $this->migrateApiAccounts();
      $this->migrateApiAccountFromCompanyData();

      $this->migrateNewActions();
      $this->renamePermissions();

    }

    /**
     * Migrate the old api account from company data
     */
    protected function migrateApiAccountFromCompanyData(): void
    {
      $initKey = $this->app->erp->Firmendaten('api_initkey');
      $remoteDomain = $this->app->erp->Firmendaten('api_remotedomain');
      $eventUrl = $this->app->erp->Firmendaten('api_eventurl');
      $enable = $this->app->erp->Firmendaten('api_enable');
      $cleanUtf8 = $this->app->erp->Firmendaten('api_cleanutf8');
      $importWarteschlange = $this->app->erp->Firmendaten('api_importwarteschlange');
      $importWarteschlangeName = $this->app->erp->Firmendaten('api_importwarteschlange_name');
      $apiOhneHtmlUmwandlung = $this->app->erp->Firmendaten('apiohnehtmlumwandlung');

      if(empty($remoteDomain)){
        return;
      }

      $sql = "SELECT `id` FROM `api_account` WHERE remotedomain = '" . $remoteDomain . "'";
      $isExisting = $this->app->DB->Select($sql) > 0;

      if($isExisting){
        return;
      }

      $availablePermissions = [];
      foreach ($this->getGroupedPermissions() as $group => $permissions) {
        $availablePermissions = array_merge($availablePermissions, $permissions);
      }

      $sql =
        "INSERT INTO `api_account` (
      `bezeichnung`, 
      `initkey`, 
      `importwarteschlange_name`,
      `event_url`,
      `remotedomain`,
      `aktiv`,
      `importwarteschlange`,
      `cleanutf8`,
      `uebertragung_account`,
      `projekt`,
      `is_legacy`,
      `permissions`,                     
      `ishtmltransformation`
      ) VALUES (
      'Migration - " . $remoteDomain . "',
      '" . $initKey . "',
      '" . $importWarteschlangeName . "',
      '" . $eventUrl . "',
      '" . $remoteDomain . "',
      " . $enable . ",
      " . $importWarteschlange . ",
      " . $cleanUtf8 . ",
      0,
      0,
      0,
      '" . json_encode($availablePermissions) ."',
      " . $apiOhneHtmlUmwandlung . "
      )";

      $isInserted = (bool)$this->app->DB->Insert($sql);

      if($isInserted){
        $apiId = $this->app->DB->GetInsertID();

        /** @var SystemConfigModule $systemConfig */
        $systemConfig = $this->app->Container->get('SystemConfigModule');
        $systemConfig->setValue('apiaccount','migratedapiid',$apiId);

        $this->app->erp->FirmendatenSet('api_initkey','');
        $this->app->erp->FirmendatenSet('api_remotedomain','');
        $this->app->erp->FirmendatenSet('api_eventurl','');
        $this->app->erp->FirmendatenSet('api_enable',0);
        $this->app->erp->FirmendatenSet('api_cleanutf8',0);
        $this->app->erp->FirmendatenSet('api_importwarteschlange',0);
        $this->app->erp->FirmendatenSet('api_importwarteschlange_name','');
        $this->app->erp->FirmendatenSet('apiohnehtmlumwandlung',0);


        $emptyValues = [
          'api_initkey' => '',
          'api_remotedomain' => '',
          'api_eventurl' => '',
          'api_enable' => 0,
          'api_cleanutf8' => 0,
          'api_importwarteschlange' => 0,
          'api_importwarteschlange_name' => '',
          'apiohnehtmlumwandlung' => 0
        ];
        foreach ($emptyValues as $name => $emptyValue){
          $sql = "UPDATE `firmendaten_werte` SET `wert` = '".$emptyValue. "' WHERE `name` = '".$name."'";
          $this->app->DB->Update($sql);
        }
      }
    }

    private function migrateNewActions()
    {
      $customActions = $this->getLegacyApiCustomActions();
      if(empty($customActions)) {
        return;
      }
      $group = 'custom';
      $accounts = $this->app->DB->SelectPairs(
        'SELECT `id`, `permissions` FROM `api_account` WHERE `is_legacy` = 1'
      );
      $accountsWithPermissions = [];
      foreach($accounts as $accountId => $jsonEncodedPermissions) {
        $permissionDecoded = json_decode($jsonEncodedPermissions, true)?:[];
        if(!in_array('standard_custom', $permissionDecoded)) {
          continue;
        }

        $accountsWithPermissions[$accountId] = $permissionDecoded;
      }

      $hasAccountPermissionChanged = false;
      foreach($customActions as $customAction) {
        $permissionKey = 'standard_'.$customAction;
        // check if permission exists in table
        $result = $this->app->DB->Count("SELECT * FROM `api_permission` WHERE `key` = '$permissionKey'");
        if($result !== 0){
          continue;
        }
        // if it does not exist, insert it
        $this->app->DB->InsertArr('api_permission', 'id', [
          'key' => $permissionKey,
          'group' => $group
        ]);
        if(empty($accountsWithPermissions)) {
          continue;
        }
        $hasAccountPermissionChanged = true;
        foreach($accountsWithPermissions as $accountId => $permissions) {
          $accountsWithPermissions[$accountId][] = $permissionKey;
        }
      }

      if(!$hasAccountPermissionChanged) {
        return;
      }
      foreach($accountsWithPermissions as $accountId => $permissions) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE `api_account` SET `permissions` = '%s' WHERE `id` = %d",
            $this->app->DB->real_escape_string(json_encode($permissions)), $accountId
          )
        );
      }
    }

    private function migrateApiAccounts()
    {
      $isMigrated = $this->app->DB->Query("SHOW COLUMNS FROM `api_account` LIKE 'is_legacy'")->num_rows > 0;
      if(!$isMigrated){
        $availablePermissions = [];
        foreach ($this->getGroupedPermissions() as $group => $permissions) {
          $availablePermissions = array_merge($availablePermissions, $permissions);
        }
        $this->app->erp->CheckColumn('is_legacy', 'tinyint(1)', 'api_account',"DEFAULT '1' NOT NULL");
        $apiAccounts = $this->app->DB->SelectArr('SELECT * FROM `api_account`');
        foreach ($apiAccounts as $apiAccount){
          $this->app->DB->Update(
            sprintf(
              "UPDATE `api_account` SET `permissions` = '%s' WHERE `id` = %d",
              json_encode($availablePermissions),
              $apiAccount['id']
            )
          );
        }
        $this->app->erp->UpdateColumn('is_legacy', 'tinyint(1)', 'api_account',"DEFAULT '0' NOT NULL");
      }
    }

    private function getGroupedPermissions()
    {
      return [
        'accounts' => [
          'standard_accountlist',
          'standard_accountlogin',
          'standard_accountcreate',
          'standard_accountedit',
          'standard_adresseaccountsget',
        ],
        'addresses' => [
          'standard_adressecreate',
          'standard_adresseedit',
          'standard_adresseget',
          'standard_adresselisteget',
          'standard_adressegruppenget',
          'create_address', // /v1/adressen - POST
          'list_addresses', // /v1/adressen - GET
          'view_address', // /v1/adressen/{id:\d+} - GET
          'edit_address', // /v1/adressen/{id:\d+} - PUT
        ],
        'address_types' => [
          'create_address_type', // /v1/adresstyp - POST
          'list_address_types', // /v1/adresstyp - GET
          'view_address_type', // /v1/adresstyp/{id:\d+} - GET
          'edit_address_type', // /v1/adresstyp/{id:\d+} - PUT
        ],
        'articles' => [
          'standard_artikelcreate',
          'standard_artikeledit',
          'standard_artikelget',
          'standard_artikellist',
          'standard_artikelstueckliste',
          'standard_artikelstuecklistecreate',
          'standard_artikelstuecklisteedit',
          'standard_artikelstuecklistelist',
          'standard_artikelkontingenteget',
          'standard_preiseedit',
          'list_articles', // /v1/artikel - GET
          'view_article', // /v1/artikel/{id:\d+} - GET
        ],
        'article_categories' => [
          'standard_artikelkategorienlist',
          'create_article_category', // /v1/artikelkategorien - POST
          'list_article_categories', // /v1/artikelkategorien - GET
          'view_article_category', // /v1/artikelkategorien/{id:\d+} - GET
          'edit_article_category', // /v1/artikelkategorien/{id:\d+} - PUT
        ],
        'contacts' => [
          'standard_ansprechpartnercreate',
          'standard_ansprechpartneredit',
          'standard_adressekontaktcreate',
          'standard_adressekontaktedit',
          'standard_adressekontaktget',
          'standard_adressekontaktlist',
        ],
        'countries' => [
          'create_country', // /v1/laender - POST
          'list_countries', // /v1/laender - GET
          'view_country', // /v1/laender/{id:\d+} - GET
          'edit_country', // /v1/laender/{id:\d+} - PUT
        ],
        'credit_memos' => [
          'standard_gutschriftget',
          'standard_gutschriftedit',
          'standard_gutschriftcreate',
          'standard_gutschriftfreigabe',
          'standard_gutschriftversenden',
          'standard_gutschriftarchivieren',
          'list_credit_memos', // /v1/belege/gutschriften - GET
          'view_credit_memo', // /v1/belege/gutschriften/{id:\d+} - GET
        ],
        'crm_documents' => [
          'create_crm_document', // /v1/crmdokumente - POST
          'list_crm_documents', // /v1/crmdokumente - GET
          'view_crm_document', // /v1/crmdokumente/{id:\d+} - GET
          'edit_crm_document', //  /v1/crmdokumente/{id:\d+} - PUT
          'delete_crm_document' // /v1/crmdokumente/{id:\d+} - DELETE
        ],
        'delivery_addresses' => [
          'standard_lieferadressecreate',
          'standard_lieferadresseedit',
          'create_delivery_address', // /v1/lieferadressen - POST
          'list_delivery_addresses', // /v1/lieferadressen - GET
          'view_delivery_address', // /v1/lieferadressen/{id:\d+} - GET
          'edit_delivery_address', // /v1/lieferadressen/{id:\d+} - PUT
          'delete_delivery_address', // /v1/lieferadressen/{id:\d+} - DELETE
        ],
        'delivery_note' => [
          'standard_lieferscheinget',
          'standard_lieferscheinedit',
          'standard_lieferscheincreate',
          'standard_lieferscheinfreigabe',
          'standard_lieferscheinversenden',
          'standard_lieferscheinarchivieren',
          'list_delivery_notes', // /v1/belege/lieferscheine - GET
          'view_delivery_note', // /v1/belege/lieferscheine/{id:\d+} - GET
        ],
        'documents' => [
          'standard_belegelist',
          'standard_belegpdf',
          'standard_belegpdfheader',
          'standard_mappingget',
          'standard_mappingset',
          'list_documents', // /v1/belege - GET
        ],
        'document_actions' => [
          'standard_angebotzuauftrag',
          'standard_auftragzurechnung',
          'standard_weiterfuehrenauftragzurechnung',
          'standard_weiterfuehrenrechnungzugutschrift'
        ],
        'files' => [
          'standard_dateilist',
          'standard_dateivorschau',
          'standard_dateidownload',
          'standard_dateiheader',
          'standard_shopimages',
          'create_file', // /v1/dateien - POST
          'list_files', //  /v1/dateien - GET
          'view_file', // /v1/dateien/{id:\d+} - GET
          'handle_assets', // /assets/{assetfile}
        ],
        'gobnav' => [
          'handle_navision' // /v1/gobnavconnect - POST
        ],
        'groups' => [
          'standard_gruppecreate',
          'standard_gruppeedit',
          'standard_gruppeget',
          'standard_gruppenlist',
          'create_group', // /v1/gruppen - POST
          'list_groups', // /v1/gruppen - GET
          'view_group', // /v1/gruppen/{id:\d+} - GET
          'edit_group', //  /v1/gruppen/{id:\d+} - PUT
        ],
        'invoices' => [
          'standard_rechnungget',
          'standard_rechnungedit',
          'standard_rechnungcreate',
          'standard_rechnungversenden',
          'standard_rechnungfreigabe',
          'standard_rechnungarchivieren',
          'standard_rechnungversendetmarkieren',
          'standard_rechnungalsbezahltmarkieren',
          'list_invoices', // /v1/belege/rechnungen
          'view_invoice', // /v1/belege/rechnungen/{id:\d+} - GET
          'delete_invoice' // /v1/belege/rechnungen/{id:\d+} - DELETE
        ],
        'mobile_app' => [
          'mobile_app_communication' // /v1/mobileapi/dashboard
        ],
        'opentrans' => [
          'handle_opentrans', // all /opentrans endpoints
        ],
        'orders' => [
          'standard_auftragcreate',
          'standard_auftragedit',
          'standard_auftragget',
          'standard_auftragfreigabe',
          'standard_auftragabschliessen',
          'standard_auftragversenden',
          'standard_auftragarchivieren',
          'list_orders', // /v1/belege/auftraege - GET
          'view_order' // /v1/belege/auftraege/{id:\d+} - GET
        ],
        'payment_methods' => [
          'create_payment_method', // /v1/zahlungsweisen - POST
          'list_payment_methods', // /v1/zahlungsweisen - GET
          'view_payment_method', // /v1/zahlungsweisen/{id:\d+} - GET
          'edit_payment_method', // /v1/zahlungsweisen/{id:\d+} - PUT
        ],
        'projects' => [
          'standard_projektliste',
          'standard_projektget',
          'standard_projektcreate',
          'standard_projektedit',
        ],
        'properties' => [
          'create_property',
          'list_property',
          'view_property',
          'edit_property',
          'delete_property',
        ],
        'property_values' => [
          'create_property_value',
          'list_property_value',
          'view_property_value',
          'edit_property_value',
          'delete_property_value',
        ],
        'quotes' => [
          'standard_angebotcreate',
          'standard_angebotedit',
          'standard_angebotget',
          'standard_angebotfreigabe',
          'standard_angebotversenden',
          'standard_angebotarchivieren',
          'list_quotes', // /v1/belege/angebote - GET
          'view_quote', // /v1/belege/angebote/{id:\d+} - GET
        ],
        'reports' => [
          'standard_exportvorlageget',
          'standard_berichteget',
          'view_report' // /v1/reports/{id:\d+}/download
        ],
        'resubmissions' => [
          'create_resubmission', // /v1/wiedervorlagen - POST
          'list_resubmissions', // /v1/wiedervorlagen - GET
          'view_resubmission', // /v1/wiedervorlagen/{id:\d+} - GET
          'edit_resubmission', // /v1/wiedervorlagen/{id:\d+} - PUT
        ],
        'returns' => [
          'standard_retoureget',
          'standard_retoureedit',
          'standard_retourecreate',
        ],
        'scanned_documents' => [
          'create_scanned_document', // /v1/docscan - POST
          'list_scanned_documents', // /v1/docscan - GET
          'view_scanned_document', // /v1/docscan/{id:\d+} - GET
        ],
        'shipping_methods' => [
          'create_shipping_method', // /v1/versandarten - POST
          'list_shipping_methods', // /v1/versandarten - GET
          'view_shipping_method', // /v1/versandarten/{id:\d+} - GET
          'edit_shipping_method', // /v1/versandarten/{id:\d+} - PUT
        ],
        'shop_communication' => [
          'communicate_with_shop' // all routes with /shopimport
        ],
        'storage' => [
          'view_storage_batch', // /v1/lagercharge - GET
          'view_storage_best_before', // /v1/lagermhd - GET
        ],
        'subscriptions' => [
          'standard_adresseaboartikelcreate',
          'standard_adresseaboartikeledit',
          'standard_adresseaboartikelget',
          'standard_adresseaboartikellist',
          'create_subscription', // /v1/aboartikel - POST
          'list_subscriptions', // v1/aboartikel - GET
          'view_subscription', // /v1/aboartikel/{id:\d+} - GET
          'edit_subscription', // /v1/aboartikel/{id:\d+} - PUT
          'delete_subscription', //  /v1/aboartikel/{id:\d+} - DELETE
        ],
        'subscription_groups' => [
          'standard_adresseabogruppecreate',
          'standard_adresseabogruppeedit',
          'standard_adresseabogruppeget',
          'standard_adresseabogruppelist',
          'create_subscription_group', // /v1/abogruppen - POST
          'list_subscription_groups', // /v1/abogruppen - GET
          'view_subscription_group', // /v1/abogruppen/{id:\d+} - GET
          'edit_subscription_group', // /v1/abogruppen/{id:\d+} - PUT
        ],
        'supply_orders' => [
          'standard_bestellungget',
          'standard_bestellungedit',
          'standard_bestellungcreate',
          'standard_bestellungfreigabe',
        ],
        'system_settings' => [
          'standard_etikettendrucker',
          'standard_servertimeget'
        ],
        'tax_rates' => [
          'create_tax_rate', // /v1/steuersaetze - POST
          'list_tax_rates', // /v1/steuersaetze - GET
          'view_tax_rate', // /v1/steuersaetze/{id:\d+} - GET
          'edit_tax_rate', // /v1/steuersaetze/{id:\d+} - PUT
        ],
        'employee_interactions' => [
          'standard_stechuhrstatusget',
          'standard_stechuhrstatusset',
          'standard_stechuhrsummary',
          'standard_zeiterfassungget',
          'standard_zeiterfassungcreate',
          'standard_zeiterfassungedit',
          'standard_zeiterfassungdelete',
          'standard_reisekostenversenden',
          'standard_sessionstart',
          'standard_sessionclose',
        ],
        'tracking_numbers' => [
          'create_tracking_number', // /v1/trackingnummern - POST
          'list_tracking_numbers', // /v1/trackingnummern - GET
          'view_tracking_number', // /v1/trackingnummern/{id:\d+} - GET
          'edit_tracking_number', // /v1/trackingnummern/{id:\d+} - PUT
        ],
        'users' => [
          'standard_benutzercreate',
          'standard_benutzeredit',
          'standard_benutzerget',
          'standard_benutzerlist',
          'standard_benutzergetrfid',
        ],
        'custom' => [
          'standard_custom'
        ]
      ];
    }

    private function renamePermissions()
    {
      $hasLegacyPermissions = $this->app->DB->Count("SELECT `id` FROM `api_permission` WHERE `key` LIKE 'legacy_%'") > 0;
      if(!$hasLegacyPermissions) {
        return;
      }
      // rename permissions in api_permission table with extra condition if legacy and standard keys exist
      $this->app->DB->Delete(
        "DELETE `ap1` 
        FROM `api_permission` AS `ap1`
        INNER JOIN `api_permission` AS `ap2` ON ap1.key = replace(ap2.key, 'standard_', 'legacy_')
        WHERE ap1.key LIKE 'legacy_%' AND ap2.key LIKE 'standard_%'"
      );
      $this->app->DB->Update(
        "UPDATE `api_permission`
        SET `key` = REPLACE(`key`, 'legacy_', 'standard_')
        WHERE `key` LIKE 'legacy_%'"
      );
      // rename permissions in api_account
      $this->app->DB->Update(
        "UPDATE `api_account` SET `permissions` = REPLACE(`permissions`, 'legacy_', 'standard_')"
      );
    }

    /**
     * @return array
     */
    private function getLegacyApiCustomActions()
    {
      if(!is_file(__DIR__.'/api_custom.php')) {
        return [];
      }

      /** @var Benutzer $users **/
      $users = $this->app->loadModule('benutzer');
      $permissions = $users->getActionsFromFile(__DIR__.'/api_custom.php');
      if(empty($permissions)) {
        return [];
      }

      return $permissions;
    }

    private function fillApiPermissions()
    {
      foreach ($this->getGroupedPermissions() as $group => $permissions){
        foreach ($permissions as $permission){
          // check if permission exists in table
          $result = $this->app->DB->Count("SELECT * FROM `api_permission` WHERE `key` = '$permission'");
          if($result !== 0){
            continue;
          }
          // if it does not exist, insert it
          $this->app->DB->InsertArr('api_permission', 'id', [
            'key' => $permission,
            'group' => $group
          ]);
        }
      }
    }

    /**
     * @param int $apiId
     */
    public function setApiId($apiId)
    {
      $this->api_id = $apiId;
    }

  function ApiXMLTest(){
    $xml_data = new SimpleXMLExtended();
    $test = array('adresse' => array( 'name' => 'mustermann', 'vorname' => 'max'));
    echo $xml_data->asXML();
    echo "stop";
    $this->app->ExitXentral();
  }


  /**
   * returns Projekt ID
   *
   * @param string $value
   *
   * @return bool|int
   */
  private function getProjektId($value)
  {
    if($value != ''){
      if($this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$value."' LIMIT 1")){
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$value."' LIMIT 1");
      }
      else if($this->app->DB->Select("SELECT id FROM projekt WHERE id = '".$value."' LIMIT 1")){
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id = '".$value."' LIMIT 1");
      }
      return (int)$projekt;
    }

    return false;
  }

  /**
   * returns Adresse ID
   *
   * @param string $value
   *
   * @return bool|int
   */
  private function getAdressId($value)
  {
    if(!empty($value)){
      if(is_numeric($value))
      {
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE id = '".$value."' LIMIT 1");
      }
      if(empty($adresse))
      {
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer = '".$value."' LIMIT 1");
      }
      return (int)$adresse;
    }
    return false;
  }

  /** loads email settings from firmendaten
   * used in few requests to send documents
   *
   * @return void
   */
  private function getEmailSettings()
  {
      $benutzername = $this->app->erp->Firmendaten('benutzername');
      $passwort = $this->app->erp->Firmendaten('passwort');
      $host = $this->app->erp->Firmendaten('host');
      $port = $this->app->erp->Firmendaten('port');
      $mailssl = $this->app->erp->Firmendaten('mailssl');
      $mailanstellesmtp = $this->app->erp->Firmendaten('mailanstellesmtp');
      $noauth = $this->app->erp->Firmendaten('noauth');

      // mail
      $this->app->mail = new PHPMailer($this->app);
      $this->app->mail->CharSet = 'UTF-8';
      $this->app->mail->PluginDir = 'plugins/phpmailer/';

      if($mailanstellesmtp == '1'){
          $this->app->mail->IsMail();
      }else{
          $this->app->mail->IsSMTP();

          if($noauth == '1') {
              $this->app->mail->SMTPAuth = false;
          } else {
              $this->app->mail->SMTPAuth = true;
          }

          if($mailssl == 1){
              $this->app->mail->SMTPSecure = 'tls';                 // sets the prefix to the servier
          }
          else if($mailssl == 2){
              $this->app->mail->SMTPSecure = 'ssl';                 // sets the prefix to the servier
          }

          $this->app->mail->Host = $host;

          $this->app->mail->Port = $port;                   // set the SMTP port for the GMAIL server

          $this->app->mail->Username = $benutzername;  // GMAIL username
          $this->app->mail->Password = $passwort;            // GMAIL password
      }
  }

  function ApiServerTimeGet(){
    $this->XMLResponse(1,'<servertime>'.time().'</servertime>');
    $this->app->ExitXentral();
  }



  function ApiBenutzerGetRFID()
  {
    $xmldata = $this->XMLPost();
    if ($xmldata['rfid'] == '') {
      $this->XMLResponse(5, "<error>RFID fehlt</error>");
      $this->app->ExitXentral();
    }
    $rfid = $this->app->DB->real_escape_string($xmldata['rfid']);
    $id = $this->app->DB->Select("SELECT id FROM user WHERE activ=1 AND rfidtag='$rfid' AND rfidtag!='' LIMIT 1");
    if($id > 0){
      $this->XMLResponse(1, $this->app->erp->XMLBenutzer($id));
    }
    else{
      $this->XMLResponse(5, '<error>fehlerhafte RFID</error>');
    }
    $this->app->ExitXentral();
  }

  function ApiBenutzerList()
  {
    $user = $this->app->DB->SelectArr("SELECT id,adresse,rfidtag FROM user WHERE activ = 1");
    if($user)
    {
      $xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<user>
</user>
XML;

      $_xmlobj = new SimpleXMLExtended($xmlstr);
      $items = $_xmlobj->AddChild('items');
      foreach($user as $value){
        $item = $items->AddChild('item');
        foreach($value as $key => $data){
          $item->AddChild($key,$data);
        }
      }
      $xml = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<user>",'</user>'),array('','',''),$_xmlobj->asXML());
      $this->XMLResponse(1,$xml);
    }else{
      $this->XMLResponse(8);
    }
    $this->app->ExitXentral();
  }

  function ApiStechuhrSummary()
  {
    $xmldata = $this->XMLPost();
    if ($xmldata['adresse'] == '') {
      $this->XMLResponse(5, '<error>AdresssID fehlt</error>');
      $this->app->ExitXentral();
    }
    $id = $xmldata['adresse'];
    $jahr = date("Y");
    $aktjahr = (int)date('Y');
    $vorjahr = $jahr-1;


    $xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<stechuhrsummary>
</stechuhrsummary>
XML;

    $_xmlobj = new SimpleXMLElement($xmlstr);
    $datum = date("Y", time());

    $mitarbeiterzeiterfassung = $this->app->erp->LoadModul('mitarbeiterzeiterfassung');
    if($mitarbeiterzeiterfassung){
      $ueberstundenwerte = $mitarbeiterzeiterfassung->GetUeberstundenRaw($id);
      $urlaub = $mitarbeiterzeiterfassung->GetRestUrlaub($id, date("Y", time()), true);
      $mitarbeiterSollstunden = $mitarbeiterzeiterfassung->getSollstundenByAdresse($id);
    }

    $items = $_xmlobj->AddChild("mitarbeiter_adresse",$id);
    $items->addAttribute('type', 'documentary');
    $_xmlobj->AddChild("ZeitRequest",date("c"));
    $_xmlobj->AddChild("UrlaubSoll",$urlaub['soll']);
    $_xmlobj->AddChild("UrlaubIst",$urlaub['ist']);

    $_xmlobj->AddChild("UeberstundenMonat",$ueberstundenwerte['aktuell']);
    $_xmlobj->AddChild("UeberstundenGesamt",$ueberstundenwerte['gesamt']);


    $_xmlobj->AddChild("KW", date("N"));
    $einzelauflistung = $_xmlobj->AddChild("Einzelauflistung");
    $arbeitswoche = $einzelauflistung->AddChild("Kalenderwoche");
    $kalenderwoche = $arbeitswoche->AddChild("KW",date("W"));
    $wochentageKw = $this->wochentageinKW(date("d.m.Y"));


    foreach($wochentageKw as $wochentag){
      // Hier brauch ich einfach die entsprechende Soll/Ist Arbeitszeit für den übergebenden Tag
      $arbeitszeitensummary['sollaz'] = round($mitarbeiterSollstunden[$this->app->String->Convert($wochentag,'%1.%2.%3','%3-%2-%1')]['soll'] / 60,2);
      $arbeitszeitensummary['istaz'] = round($mitarbeiterSollstunden[$this->app->String->Convert($wochentag,'%1.%2.%3','%3-%2-%1')]['ist'] / 60,2) ;
      $arbeitszeiten = $arbeitswoche->AddChild("Arbeitstag");
      $arbeitszeiten->AddChild("Datum",$wochentag);
      $arbeitszeiten->AddChild("ArbeitszeitSoll",$arbeitszeitensummary['sollaz']);
      $arbeitszeiten->AddChild("ArbeitszeitIst",$arbeitszeitensummary['istaz']);
      $arbeitsevents = $arbeitszeiten->AddChild("ArbeitsEvents");
      $status = $this->app->DB->SelectArr("Select status, TIME_FORMAT(datum,'%H:%i') as zeit from stechuhr where DATE_FORMAT(datum,'%d.%m.%Y')= '$wochentag' AND adresse = '$id' order by datum asc");
      foreach($status as $item){
        $arbeitsevent = $arbeitsevents->AddChild("Event");
        $arbeitseventtyp = $arbeitsevent->AddChild("typ", $item['status']);
        $arbeitseventzeit = $arbeitsevent->AddChild("zeit", $item['zeit']);

      }
    }
    $xml = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<stechuhrsummary>",'</stechuhrsummary>'),array('','',''),$_xmlobj->asXML());
    $this->XMLResponse(1,$xml);
    $this->app->ExitXentral();
  }


    /**
     * @param string $datumvalue
     *
     * @return mixed
     */
    public function wochentageinKW($datumvalue){
    //aktueller Wochentag im Format d.m.Y in der KW
    $basis = 7; // wochentage
    $datum = new DateTime($datumvalue);
    $wochentag = $datum->format("N");
    $datumBefore = new DateTime($datumvalue);
    $datumAfter = new DateTime($datumvalue);
    $tage[$wochentag] = $datum->format("d.m.Y");
    if($wochentag > 1) {
      $counter = $wochentag - 1;
      while ($counter > 0 ) {
        $datumBefore->modify('-1 days');
        $tage[$counter] = $datumBefore->format("d.m.Y");
        $counter--;
      }
    }
    $counter = $wochentag + 1;
    while($counter <= $basis){
      $datumAfter->modify('+1 days');
      $tage[$counter] = $datumAfter->format("d.m.Y");
      $counter++;
    }
    ksort($tage);

    return $tage;
  }


  public function ApiStechuhrStatusGet()
  {

    $xmldata = $this->XMLPost();
    if ($xmldata['adresse'] == '') {
      $this->XMLResponse(5, "<error>AdresssID fehlt</error>");
      $this->app->ExitXentral();
    }
    $adresse = $xmldata['adresse'];
    $user = $xmldata['user'];
    $status = $this->app->DB->SelectRow("Select status, REPLACE(REPLACE(ROUND(TIMESTAMPDIFF(MINUTE,datum,now()) / 60, 2),'-',''),'.','.') as seit,TIMESTAMPDIFF(HOUR,datum,now()) as dd, kommen  from stechuhr where adresse = ".$adresse." order by datum desc limit 1");

    $xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<stechuhrstatus>
</stechuhrstatus>
XML;

    $_xmlobj = new SimpleXMLElement($xmlstr);
    $_xmlobj->AddChild("status",$status['status']);
    $_xmlobj->AddChild("seit",$status['seit']);
    $_xmlobj->AddChild("dd",$status['dd']);
    $_xmlobj->AddChild("kommen",$status['kommen']);
    $xml = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<stechuhrstatus>",'</stechuhrstatus>'),array('','',''),$_xmlobj->asXML());
    $this->XMLResponse(1,$xml);

    $this->app->ExitXentral();
  }

  function ApiStechuhrStatusSet()
  {

    $xmldata = $this->XMLPost();
    if ($xmldata['cmd'] == '') {
      $this->XMLResponse(5, "<error>CMD fehlt</error>");
      $this->app->ExitXentral();
    }
    $cmd = $xmldata['cmd'];
    $user = (int)$xmldata['user'];
    if($user){
      $user = $this->app->DB->Select("SELECT id FROM user WHERE id='$user' and activ = '1' LIMIT 0,1");
    }

    if(!$user){
      $adresse = (int)$xmldata['adresse'];
      if($adresse) {
        $user = $this->app->DB->Select("SELECT id FROM user WHERE adresse='$adresse' and activ = '1' LIMIT 0,1");
      }
      if(!$user){
        $this->XMLResponse(5, "<error>Userid oder Adressid fehlerhaft</error>");
        $this->app->ExitXentral();
      }
    }else{
      $adresse = $this->app->DB->Select("SELECT adresse FROM user where id='$user' LIMIT 0,1");
    }

//    if($user == ''){
//      $user = $this->app->DB->Select("SELECT id FROM user WHERE adresese='$adresse' AND ")
//    }
    // wir schauen uns denn aktuellen status an um zu prüfen welche Set Meldungen möglich sind
    $currentstatus = $this->app->DB->Select("Select status from stechuhr where adresse = ".$adresse." order by datum desc limit 1");

    if($currentstatus === 'gehen'){
      // es darf nur der Status 'kommen' funktionieren
      if($cmd !== 'kommen'){
        $this->XMLResponse(5, "<error>Fehlerhafter Status wird uebergeben. Es darf aktuell nur der Status \"kommen\" übergeben werden.</error>");
      }
    }

    if($currentstatus === 'kommen'){
        // es darf nur der Status 'gehen' und 'pausestart' funktionieren
      if( ($cmd !== 'gehen') && ($cmd !== 'pausestart') ){
        $this->XMLResponse(5, "<error>Fehlerhafter Status wird uebergeben. Es darf aktuell nur der Status \"gehen\" und \"pausestart\" übergeben werden.</error>");
      }

    }

    if($currentstatus === 'pausestart'){
      // es darf nur der Status 'gehen' und 'pausestart' funktionieren
      if($cmd !== 'pausestop'){
        $this->XMLResponse(5, "<error>Fehlerhafter Status wird uebergeben. Es darf aktuell nur der Status \"pausestop\" übergeben werden.</error>");
      }
    }


    $kommen = 1;
    if($cmd==='pause' || $cmd==='pausestart' || $cmd==='gehen') {
      $kommen=0;
    }
    $status = '';
    switch($cmd)
    {
      case 'pausestart':
      case 'pausestop':
      case 'kommen':
      case 'gehen':
        $status = $cmd;
        break;

    }
    if($status || $cmd === 'arbeit' || $cmd === 'pause')
    {
      $alterstatus = $this->app->DB->SelectArr("Select status, datum as seit, kommen from stechuhr where adresse = ".$adresse." order by datum desc limit 1");
      if($alterstatus)
      {
        $seit = $alterstatus[0]['seit'];
        $altkommen = $alterstatus[0]['kommen'];
        $alterstatus = $alterstatus[0]['status'];
      }
      if((!$alterstatus && $status === 'kommen') ||
          (!$alterstatus && $status === 'gehen') ||
          ($alterstatus === 'kommen' && $status !== 'kommen') ||
          ($alterstatus === 'gehen' && $status === 'kommen') ||
          ($alterstatus === 'pausestart' && $status === 'pausestop') ||
          ($alterstatus === 'pausestop' && $status === 'pausestart') ||
          ($alterstatus === 'pausestop' && $status === 'gehen') ||
          ($alterstatus === 'pausestart' && $status === 'gehen') ||
          ($cmd === 'arbeit') ||
          ($cmd === 'pause')
      )
      {
        if(!(!$status && $alterstatus && $altkommen == 1 && $kommen == 0 ) ||  $cmd === 'arbeit' || $cmd === 'pause')
        {
          if($status == '' && $kommen == 1 && $alterstatus === 'pausestart') {
            $status = 'pausestop';
          }
          if(($alterstatus === false || $alterstatus === 'gehen')&& $kommen == 1 && $status == '') {
            $status = 'kommen';
          }

          $this->app->DB->Insert("INSERT INTO stechuhr (adresse,user,datum,kommen, status) 
            VALUES ('".$adresse."','".$user."',NOW(),'".$kommen."','".($status)."')");
          $insid = $this->app->DB->GetInsertID();
        }
      }
    }
    $this->XMLResponse(1);
  }

  function ApiProjektCreate() {
    $xmldata = $this->XMLPost();
    if (($xmldata['name'] == '') || ($xmldata['abkuerzung'] == '')) {
      $this->XMLResponse(5, "<error>Name und/oder Abkürzung fehlt</error>");
      $this->app->ExitXentral();
    }

    $name       = $this->app->DB->real_escape_string($xmldata['name']);
    $abkuerzung = $this->app->DB->real_escape_string($xmldata['abkuerzung']);

    //prüfen ob es die abkuerzung schon gibt
    $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$this->app->DB->real_escape_string($xmldata['abkuerzung'])."' LIMIT 1");
    if($projekt){
      $this->XMLResponse(5, "<error>Abkürzung schon vorhanden. Bitte eine andere wählen.</error>");
      $this->app->ExitXentral();
    }
    $this->app->DB->Insert("INSERT INTO projekt (id,name,abkuerzung) VALUES ('','".$name."','".$abkuerzung."')");

    $xmldata['id'] = $this->app->DB->GetInsertID();

    $this->ApiProjektEdit(true,$xmldata);
    $this->XMLResponse(1, "<id>".$xmldata['id']."</id>");
    $this->app->ExitXentral();
  }

  public function ApiProjektEdit($internal=false,$data='') {
    $xmldata = $this->XMLPost();
    if ($internal){
      $xmldata = $data;
      $id = $this->app->DB->real_escape_string($xmldata['id']);
    }else{
      if( $this->app->DB->real_escape_string($xmldata['id'] =='') && $this->app->DB->real_escape_string($xmldata['projekt'] == '') ){
        $this->XMLResponse(5,'<error>ID oder Projekt muss gesetzt sein</error>');
      }
      if($xmldata['id'] != ''){
        $id = $this->app->DB->real_escape_string($xmldata['id']);
      }else {
        $id = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '" . $this->app->DB->real_escape_string($xmldata['projekt']) . "' LIMIT 1");
      }
    }
    if($id == ''){
      $this->XMLResponse(5,'<error>falsche ID oder Projektkennung</error>');
    }


    unset($xmldata['id']);
    unset($xmldata['projekt']);

    foreach ($xmldata as $key => $value) {
      $insertKey = $this->app->DB->real_escape_string($key);
      $insertValue = $this->app->DB->real_escape_string($value);
      $this->app->DB->Update("UPDATE projekt SET $insertKey='".$insertValue."' WHERE id='$id' LIMIT 1");
    }
    if($internal){
      return;
    }
    $this->XMLResponse(1);

    $this->app->ExitXentral();
  }
  function ApiProjektListe()
  {
    $xml = $this->XMLPost();

    $where = "geloescht <> 1";
    $offset = 0;
    $limit = 1000000;
    if(!empty($xml['offset'])) {
      $offset = (int)$xml['offset'];
    }
    if($offset < 0) {
      $offset = 0;
    }
    if(!empty($xml['limit'])) {
      $limit = (int)$xml['limit'];
    }
    if($limit <= 0) {
      $limit = 1;
    }

    $xml_obj = $this->CreateXmlObj();
    $projekte = $this->app->DB->SelectArr("SELECT * FROM projekt WHERE $where ORDER by abkuerzung LIMIT $offset, $limit");
    if($projekte)
    {
      $this->AddToXMLObj($xml_obj, 'projekt', 'projekte',$projekte);
    }
    $this->XMLResponse(1,$xml_obj, true, false);
    $this->app->ExitXentral();
  }

  function ApiProjektGet()
  {
    $xml = $this->XMLPost();
    $where = "geloescht <> 1";
    $offset = 0;
    $limit = 1000000;
    if(!empty($xml['offset']))$offset = (int)$xml['offset'];
    if($offset < 0)$offset = 0;
    if(!empty($xml['limit']))$limit = (int)$xml['limit'];
    if($limit <= 0)$limit = 1;
    if(!empty($xml['projekt']))
    {
      if($this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$xml['projekt']."' LIMIT 1")){
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$xml['projekt']."' LIMIT 1");
      }
      if($this->app->DB->Select("SELECT id FROM projekt WHERE id = '".$xml['projekt']."' LIMIT 1")){
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id = '".$xml['projekt']."' LIMIT 1");
      }
      $where .= " AND projekt = '".((int)$xml['projekt'])."' ";
    }elseif(!empty($xml['abkuerzung']))
    {
      $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$this->app->DB->real_escape_string($xml['abkuerzung'])."' LIMIT 1");
      $where .= " AND projekt = '".(int)$projekt."' ";
    }
    $xml_obj = $this->CreateXmlObj();
    $projekte = $this->app->DB->SelectArr("SELECT p.* , '' as arbeitspakete FROM projekt p WHERE id = '$projekt' LIMIT 1");
    $this->AddToXMLObj($xml_obj, 'projekt', '',$projekte);
    $arbeitspakete = $this->app->DB->SelectArr("SELECT * FROM arbeitspaket WHERE $where LIMIT $offset, $limit");
    if($arbeitspakete)
    {
      $this->AddToXMLObj($xml_obj, 'arbeitspaket', '',$arbeitspakete, $xml_obj->xml->projekt->arbeitspakete);
    }
    $this->XMLResponse(1,$xml_obj, true, false);
    $this->app->ExitXentral();
  }


  function ApiAnsprechpartnerCreate(){
    $xmldata = $this->XMLPost();

    if (($xmldata['name'] == '') || ($xmldata['adresse'] == '')) {
      $this->XMLResponse(5, "<error>AdresseId oder Name fehlt</error>");
      $this->app->ExitXentral();
    }
    $newId = $this->app->erp->CreateAnsprechpartner($xmldata['adresse'],$xmldata);
    $this->XMLResponse(1,"<id>$newId</id>");
    $this->app->ExitXentral();
  }

  function ApiAnsprechpartnerEdit(){
    $xmldata = $this->XMLPost();
    if ($xmldata['id'] == '') {
      $this->XMLResponse(5, "<error>AnsprechpartnerId fehlt</error>");
      $this->app->ExitXentral();
    }

    $id = $xmldata['id'];
    unset($xmldata['id']);

    foreach ($xmldata as $key => $value) {
      $insertKey = $this->app->DB->real_escape_string($key);
      $insertValue = $this->app->DB->real_escape_string($value);

      $this->app->DB->Update("UPDATE ansprechpartner SET $insertKey='".$insertValue."' WHERE id='$id' LIMIT 1");
    }
    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }

  function ApiLieferadresseCreate(){
    $xmldata = $this->XMLPost();

    if (($xmldata['name'] == '') || ($xmldata['adresse'] == '')) {
      $this->XMLResponse(5, "<error>AdresseId oder Name fehlt</error>");
      $this->app->ExitXentral();
    }
    $newId = $this->app->erp->CreateLieferadresse($xmldata['adresse'],$xmldata);
    $this->XMLResponse(1,"<id>$newId</id>");
    $this->app->ExitXentral();
  }

  function ApiLieferadresseEdit(){
    $xmldata = $this->XMLPost();
    if ($xmldata['id'] == '') {
      $this->XMLResponse(5, "<error>LieferadresseId fehlt</error>");
      $this->app->ExitXentral();
    }

    $id = $xmldata['id'];
    unset($xmldata['id']);

    foreach ($xmldata as $key => $value) {
      $insertKey = $this->app->DB->real_escape_string($key);
      $insertValue = $this->app->DB->real_escape_string($value);

      $this->app->DB->Update("UPDATE lieferadressen SET $insertKey='".$insertValue."' WHERE id='$id' LIMIT 1");
    }
    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }


  function ApiAdresseAboArtikelCreate(){
    $xmldata = $this->XMLPost();


    $this->app->DB->Insert("INSERT INTO abrechnungsartikel (id,bezeichnung)
          VALUES ('','NEUANLAGE')");

    $xmldata['id'] = $this->app->DB->GetInsertID();
    $xmldata['neu'] = "true";
    $this->ApiAdresseAboArtikelEdit($xmldata);
    $this->app->ExitXentral();
  }

  function ApiAdresseAboArtikelEdit($xmldata = false){


    // prüfen auf Mindestfelder
    if($xmldata['neu'] != '') {
      if (($xmldata['artikel'] == '') || ($xmldata['adresse'] == '')) {
        $this->XMLResponse(5, "<error>ArtikelId oder AdresseId fehlt</error>");
        $this->app->ExitXentral();
      }
      if (($xmldata['menge'] == '') || ($xmldata['menge'] == '0')) {
        $this->XMLResponse(5, "<error>Menge fehlt oder ist 0</error>");
        $this->app->ExitXentral();
      }
      $id = $xmldata['id'];
      $adresse = $this->app->DB->real_escape_string($xmldata['adresse']);
    }else{
      $xmldata = $this->XMLPost();
      if($xmldata['id'] == ''){
        $this->XMLResponse(5, "<error>ID fehlt oder ist 0</error>");
        $this->app->ExitXentral();
      }
      $id = $xmldata['id'];
      $adresse = $this->app->DB->Select("SELECT adresse FROM abrechnungsartikel WHERE id = '" . $id . "' LIMIT 1");
    }

    $artikel = $this->app->DB->real_escape_string($xmldata['artikel']);
    $bezeichnung = $this->app->DB->real_escape_string($xmldata['bezeichnung']);
    $nummer = $this->app->DB->real_escape_string($xmldata['nummer']);


    if($xmldata['neu'] != '') {
      if ($xmldata['angelegtamdatum'] == "") $angelegtamdatum = $this->app->String->Convert(date("d.m.Y"), '%1.%2.%3', '%3-%2-%1');
      // Feldvalidierung
      if (!preg_match("(^abo$|^monat$|^monatx$|^jahr$|^wochen$|^einmalig$|^30tage$)", $xmldata['preisart'])) {
        $this->XMLResponse(5, "<error>fehlerhaftes Feld preisart => (abo|monat|monatx|jahr|wochen|einmalig)</error>");
        $this->app->ExitXentral();
      }
      if (!preg_match("(^rechnung$|^auftrag$)", $xmldata['dokument'])) {
        $this->XMLResponse(5, "<error>fehlerhaftes Feld dokument => (rechnung|auftrag)</error>");
        $this->app->ExitXentral();
      }
    }
    if($xmldata['neu'] != '') {
      //prüfen ob die ArtikelId auch ein Artikel ist
      if ($this->app->DB->Select("SELECT id FROM artikel WHERE id = '" . $artikel . "' LIMIT 1")) {
        $artikeldata = $this->app->DB->SelectArr("SELECT id,name_de,nummer FROM artikel WHERE id = '" . $artikel . "' LIMIT 1");
      } elseif ($this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '" . $artikel . "' LIMIT 1")) {
        $artikeldata = $this->app->DB->SelectArr("SELECT id,name_de,nummer FROM artikel WHERE nummer = '" . $artikel . "' LIMIT 1");
      } else {
        $this->XMLResponse(5, "<error>fehlerhafte ArtikelId</error>");
        $this->app->ExitXentral();
      }
      $sql['artikel'] = $artikeldata[0]['id'];
      $sql['nummer'] = $artikeldata[0]['nummer'];
      if ($xmldata['bezeichnung'] == "") {
        // wir nehmen die Bezeichnung des Stammartikel
        $sql['bezeichnung'] = $artikeldata[0]['name_de'];
      } else {
        $sql['bezeichnung'] = $xmldata['bezeichnung'];
      }
    }else{
      if($xmldata['bezeichnung'] != '') {
        $sql['bezeichnung'] = $this->app->DB->real_escape_string($xmldata['bezeichnung']);
      }
    }

    if($xmldata['menge'] != '') { $sql['menge'] = $this->app->DB->real_escape_string($xmldata['menge']); }
    if($xmldata['preis'] != '') { $sql['preis'] = str_replace(',','.',$this->app->DB->real_escape_string($xmldata['preis'])); }
    if($xmldata['preisart'] != '') { $sql['preisart'] = $this->app->DB->real_escape_string($xmldata['preisart']); }
    if($xmldata['sort'] != '') { $sql['sort'] = $this->app->DB->real_escape_string($xmldata['sort']); }
    if($xmldata['lieferdatum'] != '') { $sql['lieferdatum'] = $this->app->DB->real_escape_string($xmldata['lieferdatum']); }
    if($xmldata['neu'] != '') {
      if ($angelegtamdatum != '') { $sql['angelegtam'] = $angelegtamdatum; }
    }
    if($xmldata['angelegtvon'] != '') { $sql['angelegtvon'] = $this->app->DB->real_escape_string($xmldata['angelegtvon']); }
    if($xmldata['status'] != '') { $sql['status'] = $this->app->DB->real_escape_string($xmldata['status']); }
    if($xmldata['projekt'] != '') { $sql['projekt'] = $this->app->DB->real_escape_string($xmldata['projekt']); }
    if($xmldata['wiederholend'] != '') { $sql['wiederholend'] = $this->app->DB->real_escape_string($xmldata['wiederholend']); }
    if($xmldata['zahlzyklus'] != '') { $sql['zahlzyklus'] = $this->app->DB->real_escape_string($xmldata['zahlzyklus']); }
    if($xmldata['adresse'] != '') { $sql['adresse'] = $this->app->DB->real_escape_string($xmldata['adresse']); }

    if($xmldata['gruppe'] != '') {
      $sql['gruppe'] = $this->app->DB->real_escape_string($xmldata['gruppe']);
    } else {
      // wir holen die erste Gruppe sofern es eine gibt
      $sql['gruppe'] = $this->app->DB->Select("SELECT id FROM abrechnungsartikel_gruppe WHERE adresse = '" . $adresse . "' ORDER BY id LIMIT 1");

    }
    if($xmldata['dokument'] != '') { $sql['dokument'] = $this->app->DB->real_escape_string($xmldata['dokument']); }
    if($xmldata['neu'] != '') {
      if ($xmldata['waehrung'] != '') {
        $sql['waehrung'] = $this->app->DB->real_escape_string($xmldata['waehrung']);
      }else{
        $sql['waehrung'] = "EUR";
      }
    }

    if($xmldata['startdatum'] != '') { $sql['startdatum'] = $this->app->String->Convert($xmldata['startdatum'],'%1.%2.%3','%3-%2-%1'); }
    if($xmldata['enddatum'] != '') { $sql['enddatum'] = $this->app->String->Convert($xmldata['enddatum'],'%1.%2.%3','%3-%2-%1'); }
    if($xmldata['abgerechnetbis'] != '') { $sql['abgerechnetbis'] = $this->app->String->Convert($xmldata['abgerechnetbis'],'%1.%2.%3','%3-%2-%1'); }

    // wir aktualisieren
    foreach($sql as $key => $value) {
      if(is_array($value)) {
        $value = '';
      }
      $this->app->DB->Update("UPDATE abrechnungsartikel SET $key='$value' WHERE id='$id' LIMIT 1");
    }

    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }

  function ApiAdresseAboArtikelGet($intern = false){
    $id = $this->app->Secure->GetGET("id");
    $adresse = $this->app->Secure->GetGET("adresse");
    $projekt = $this->app->Secure->GetGET("projekt");
    $xmldata = $this->XMLPost();

    if($id=="")$id = $xmldata['id'];
    if($adresse=="")$adresse = $xmldata['adresse'];
    if($projekt=="")$projekt = $xmldata['projekt'];


    if( ($id!="") && ($adresse!="") )
    {
      if($projekt!="")
      {
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$this->app->DB->real_escape_string($projekt)."' LIMIT 1");
        $ArtikelDaten = $this->app->DB->SelectArr("SELECT id, bezeichnung, nummer, angelegtam, abgerechnetbis, enddatum, preis, rabatt, waehrung, menge, preisart, gruppe FROM abrechnungsartikel WHERE id='".$this->app->DB->real_escape_string($id)."' AND adresse='".$this->app->DB->real_escape_string($adresse)."' AND projekt='".$this->app->DB->real_escape_string($projekt)."' LIMIT 1");
      } else {
        $ArtikelDaten = $this->app->DB->SelectArr("SELECT id, bezeichnung, nummer, angelegtam, abgerechnetbis, enddatum, preis, rabatt, waehrung, menge, preisart, gruppe FROM abrechnungsartikel WHERE id='".$this->app->DB->real_escape_string($id)."' AND adresse='".$this->app->DB->real_escape_string($adresse)."' LIMIT 1");
      }
    }
    //check
    $id = $this->app->DB->Select("SELECT id FROM abrechnungsartikel WHERE id='".$this->app->DB->real_escape_string($id)."'AND adresse='".$this->app->DB->real_escape_string($adresse)."' LIMIT 1");

    if($id > 0)
    {
      $xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<gruppe>
</gruppe>
XML;
      $_xmlobj = new SimpleXMLExtended($xmlstr);
//      $xmlobj = $_xmlobj->AddChild("xml");
      foreach($ArtikelDaten[0] as $key => $value){
        $_xmlobj->AddChild($key,$value);
      }
      $out = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<gruppe>",'</gruppe>'),array('','',''),$_xmlobj->asXML());
      unset($_xmlobj);
      $this->XMLResponse(1, $out);
    }
    else
    {
      if($intern) {
        return false;
      }
      $this->XMLResponse(5);
    }
    $this->app->ExitXentral();
  }

  function ApiAdresseAboArtikelList($intern = false){
    $adresse = $this->app->Secure->GetGET("adresse");
    $projekt = $this->app->Secure->GetGET("projekt");
    $xmldata = $this->XMLPost();

    if($adresse=="")$adresse = $xmldata['adresse'];
    if($projekt=="")$projekt = $xmldata['projekt'];


    if($adresse!="")
    {
      if($projekt!="")
      {
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$this->app->DB->real_escape_string($projekt)."' LIMIT 1");
        $gruppeArtikelDaten = $this->app->DB->SelectArr("SELECT id, bezeichnung, nummer, angelegtam, abgerechnetbis, enddatum, preis, rabatt, waehrung, menge, preisart, gruppe FROM abrechnungsartikel WHERE adresse='".$this->app->DB->real_escape_string($adresse)."' AND projekt='".$this->app->DB->real_escape_string($projekt)."'");
      } else {
        $gruppeArtikelDaten = $this->app->DB->SelectArr("SELECT id, bezeichnung, nummer, angelegtam, abgerechnetbis, enddatum, preis, rabatt, waehrung, menge, preisart, gruppe FROM abrechnungsartikel WHERE adresse='".$this->app->DB->real_escape_string($adresse)."'");
      }
    }

    //check
    $id = $this->app->DB->Select("SELECT id FROM abrechnungsartikel WHERE adresse='".$this->app->DB->real_escape_string($adresse)."' LIMIT 1");

    if($id > 0)
    {
      $xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<gruppeList>
</gruppeList>
XML;
      $_xmlobj = new SimpleXMLExtended($xmlstr);
      $items = $_xmlobj->AddChild("items");
      foreach($gruppeArtikelDaten as $value){
        $item = $items->AddChild("item");
        foreach($value as $key => $data){
          $item->AddChild($key,$data);
        }
      }
      $out = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<gruppeList>",'</gruppeList>'),array('','',''),$_xmlobj->asXML());
      unset($_xmlobj);
      $this->XMLResponse(1, $out);
    }
    else
    {
      if($intern)return false;
      $this->XMLResponse(5);
    }
    $this->app->ExitXentral();
  }

  function ApiAdresseAboGruppeCreate(){
    $xmldata = $this->XMLPost();
    // prüfen auf Mindestfelder
    if( (!$xmldata['adresse']) || (!$xmldata['beschreibung']) ){
      $this->XMLResponse(5);
      $this->app->ExitXentral();
    }
    $id               = $this->app->DB->real_escape_string($xmldata['adresse']);
    $beschreibung     = $this->app->DB->real_escape_string($xmldata['beschreibung']);
    $beschreibung2    = $this->app->DB->real_escape_string($xmldata['beschreibung2']);
    $ansprechpartner  = $this->app->DB->real_escape_string($xmldata['ansprechpartner']);
    $extrarechnung    = $this->app->DB->real_escape_string($xmldata['extrarechnung']);
    $projekt          = $this->app->DB->real_escape_string($xmldata['projekt']);


    //prüfen wenn Projekt als Id übergeben wird ob es diese gibt
    if($this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$projekt."' LIMIT 1")){
      $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$projekt."' LIMIT 1");
    }elseif($this->app->DB->Select("SELECT id FROM projekt WHERE id = '".$projekt."' LIMIT 1")){
      $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id = '".$projekt."' LIMIT 1");
    } else{
      $projekt = '';
    }

    $sort             = $this->app->DB->real_escape_string($xmldata['sort']);
    $gruppensumme     = $this->app->DB->real_escape_string($xmldata['gruppensumme']);
    $rechnungadresse  = $this->app->DB->real_escape_string($xmldata['rechnungadresse']);


    $this->app->DB->Insert("INSERT INTO abrechnungsartikel_gruppe (id,beschreibung,beschreibung2,ansprechpartner,extrarechnung,projekt,adresse,sort,gruppensumme,rechnungadresse) 
          VALUES (
          '',
          '$beschreibung',
          '$beschreibung2',
          '$ansprechpartner',
          '$extrarechnung',
          '$projekt',
          '$id',
          '$sort',
          '$gruppensumme',
          '$rechnungadresse'
          ) ");
    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }

  function ApiAdresseAboGruppeEdit(){

    $xmldata = $this->XMLPost();
    // prüfen auf Mindestfelder
    if(!$xmldata['id']){
      $this->XMLResponse(5);
      $this->app->ExitXentral();
    }
    $projekt          = $xmldata['projekt'];

    if($projectId = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$projekt."' LIMIT 1")){
      $xmldata['projekt'] = $projectId;
    }elseif($projectId = $this->app->DB->Select("SELECT id FROM projekt WHERE id = '".$projekt."' LIMIT 1")){
      $xmldata['projekt'] = $projectId;
    } else{
      $xmldata['projekt'] = '';
    }

    $id = $xmldata['id'];
    foreach($xmldata as $key=>$value)
    {
      if(is_array($value)) {
        $value='';
      }
      if($key!=="id"){
        $insertKey = $this->app->DB->real_escape_string($key);
        $insertValue = $this->app->DB->real_escape_string($value);
        $this->app->DB->Update("UPDATE abrechnungsartikel_gruppe SET $insertKey='$insertValue' WHERE id='$id' LIMIT 1");
      }
    }
    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }

  function ApiAdresseAboGruppeGet($intern = false){
    $id = $this->app->Secure->GetGET("id");
    $xmldata = $this->XMLPost();
    if($id=="")$id = $xmldata['id'];

    //check
    $id = $this->app->DB->Select("SELECT id FROM abrechnungsartikel_gruppe WHERE id='".$this->app->DB->real_escape_string($id)."' LIMIT 1");
    if($id > 0)
    {
      $gruppeDaten = $this->app->DB->SelectArr("SELECT id, beschreibung, ansprechpartner, extrarechnung, gruppensumme, adresse, projekt, sort, rechnungadresse FROM abrechnungsartikel_gruppe WHERE id='".$this->app->DB->real_escape_string($id)."' LIMIT 1");

      $xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<gruppe>
</gruppe>
XML;
      $_xmlobj = new SimpleXMLExtended($xmlstr);
//      $xmlobj = $_xmlobj->AddChild("xml");
      foreach($gruppeDaten[0] as $key => $value){
        $_xmlobj->AddChild($key,$value);
      }
      $out = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<gruppe>",'</gruppe>'),array('','',''),$_xmlobj->asXML());
      unset($_xmlobj);
      $this->XMLResponse(1, $out);
    }
    else
    {
      if($intern) {
        return false;
      }
      $this->XMLResponse(5);
    }
    $this->app->ExitXentral();
  }

  function ApiAdresseAboGruppeList($intern = false){
    $adresse = $this->app->Secure->GetGET("adresse");
    $projekt = $this->app->Secure->GetGET("projekt");
    $xmldata = $this->XMLPost();

    if($adresse=="")$adresse = $xmldata['adresse'];
    if($projekt=="")$projekt = $xmldata['projekt'];


    if($adresse!="")
    {
      if($projekt!="")
      {
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$this->app->DB->real_escape_string($projekt)."' LIMIT 1");
        $gruppeListDaten = $this->app->DB->SelectArr("SELECT id, beschreibung, ansprechpartner, extrarechnung, gruppensumme, adresse, projekt, sort, rechnungadresse FROM abrechnungsartikel_gruppe WHERE adresse='".$this->app->DB->real_escape_string($adresse)."' AND projekt='".$this->app->DB->real_escape_string($projekt)."'");
      } else {
        $gruppeListDaten = $this->app->DB->SelectArr("SELECT id, beschreibung, ansprechpartner, extrarechnung, gruppensumme, adresse, projekt, sort, rechnungadresse FROM abrechnungsartikel_gruppe WHERE adresse='".$this->app->DB->real_escape_string($adresse)."'");
      }
    }

    //check
    $id = $this->app->DB->Select("SELECT id FROM abrechnungsartikel_gruppe WHERE adresse='".$this->app->DB->real_escape_string($adresse)."' LIMIT 1");

    if($id > 0)
    {
      $xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<gruppeList>
</gruppeList>
XML;
      $_xmlobj = new SimpleXMLExtended($xmlstr);
      $items = $_xmlobj->AddChild("items");
      foreach($gruppeListDaten as $value){
        $item = $items->AddChild("item");
        foreach($value as $key => $data){
          $item->AddChild($key,$data);
        }
      }
      $out = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<gruppeList>",'</gruppeList>'),array('','',''),$_xmlobj->asXML());
      unset($_xmlobj);
      $this->XMLResponse(1, $out);
    }
    else
    {
      if($intern) {
        return false;
      }
      $this->XMLResponse(5);
    }
    $this->app->ExitXentral();
  }

  function ApiArtikelStuecklisteCreate()
  {

    $xml = $this->XMLPost();
    if(isset($xml['stuecklistevonartikel']) && (isset($xml['items']) || isset($xml['item'])))
    {
      $projekt = isset($xml['projekt'])?$xml['projekt']:'';
      if($projekt)
      {
        if(!(is_numeric($projekt) && $this->app->DB->Select("SELECT id FROM projekt WHERE id = '".$projekt."' LIMIT 1")))
        {
          $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$this->app->DB->real_escape_string($projekt)."' LIMIT 1");
        }
      }
      $stuecklistevonartikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht = 0 AND nummer = '".$this->app->DB->real_escape_string($xml['stuecklistevonartikel'])."' ORDER BY projekt = '$projekt' DESC LIMIT 1");
      if($stuecklistevonartikel)
      {
        if(!isset($xml['items']))$xml['items'][0] = $xml['item'];
        if(is_array($xml['items']))
        {
          //alle bisherigen stuecklistenelemente des artikels loeschen
          $this->app->DB->Update("UPDATE artikel SET stueckliste = 0 WHERE id = '$stuecklistevonartikel'");
          $this->app->DB->Delete("DELETE FROM stueckliste WHERE stuecklistevonartikel = '$stuecklistevonartikel'");

          //baut gleiche arraystruktur wenn nur 1 artikel uebergeben wird
          if(isset($xml['items']['item']['art'])){
            $arrayfuer1artikel = $xml['items']['item'];
            $xml['items']['item'] = array();
            $xml['items']['item'][] = $arrayfuer1artikel;
          }

          foreach($xml['items'] as $items => $item)
          {
            foreach($item as $key => $itemwerte)
            {
              $stuecklistenartikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht = 0 AND nummer = '".$itemwerte['nummer']."' ORDER BY projekt = '$projekt' DESC LIMIT 1");
              $stuecklisteitem = $this->app->DB->Select("SELECT id FROM stueckliste WHERE stuecklistevonartikel = '$stuecklistevonartikel' AND artikel = '$stuecklistenartikel' LIMIT 1");

              if($stuecklisteitem){
                // update, id ist in $stuecklisteitem
              } else {
                // insert danach update
                $this->app->DB->Insert("INSERT INTO stueckliste (id,artikel,stuecklistevonartikel) VALUES('','$stuecklistenartikel','$stuecklistevonartikel')");
                $stuecklisteitem = $this->app->DB->GetInsertID();
              }

              if($itemwerte['alternative'] != ''){
                $itemwerte['alternative'] = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht = 0 AND nummer = '".$itemwerte['alternative']."' ORDER BY projekt = '$projekt' DESC LIMIT 1");
              }

              if(is_array($itemwerte)){
                foreach($itemwerte as $itemindex=>$itemwert){
                  $this->app->DB->Update("UPDATE stueckliste SET $itemindex = '$itemwert' where id = '$stuecklisteitem'");
                }
              }
            }
          }
          $this->app->DB->Update("UPDATE artikel SET stueckliste = 1 WHERE id = '$stuecklistevonartikel' LIMIT 1");
          $this->ApiArtikelStuecklisteList($stuecklistevonartikel);
          $this->app->ExitXentral();
        }
      }else{
        $this->XMLResponse(5);
        $this->app->ExitXentral();
      }
    }else{
      $this->XMLResponse(4);
      $this->app->ExitXentral();
    }
  }

  function ApiArtikelStuecklisteEdit($id = null, $intern = false)
  {
    if(!$id)$id = $this->app->Secure->GetGET("id");
    $this->ApiArtikelStuecklisteCreate();

  }

  function ApiArtikelStuecklisteList($id = null, $intern = false)
  {
    if(!$id)$id = $this->app->Secure->GetGET("id");
    $xml = $this->XMLPost();
    if(!$id){
      $id= $xml['id'];
    }
    if($id == ''){
      $this->XMLResponse(5,"<error>Fehlerhafte Artikelnummer</error>");
    }
     if(($id && $sl = $this->app->DB->SelectArr("SELECT s.id, s.stuecklistevonartikel, art.nummer as stuecklistevonartikelnummer, trim(s.menge)+0 as menge, s.art, 
     art2.nummer as artikelnummer, art3.nummer as alternativenummer,s.alternative
    FROM stueckliste s 
    INNER JOIN artikel art ON art.id = s.stuecklistevonartikel 
    INNER JOIN artikel art2 ON art2.id = s.artikel 
    LEFT JOIN artikel art3 ON art3.id = s.alternative
    WHERE s.stuecklistevonartikel = '$id'"))
      || (isset($xml['stuecklistevonartikel']) && $sl = $this->app->DB->SelectArr("SELECT s.id, s.stuecklistevonartikel, art.nummer as stuecklistevonartikelnummer, trim(s.menge)+0 as menge, s.art, 
     art2.nummer as artikelnummer, art3.nummer as alternativenummer,s.alternative
    FROM stueckliste s 
    INNER JOIN artikel art ON art.id = s.stuecklistevonartikel 
    INNER JOIN artikel art2 ON art2.id = s.artikel 
    LEFT JOIN artikel art3 ON art3.id = s.alternative WHERE s.stuecklistevonartikel = '".(int)$xml['stuecklistevonartikel']."'"))
      || (isset($xml['stuecklistevonartikel']) && $sl = $this->app->DB->SelectArr("SELECT s.id, s.stuecklistevonartikel, art.nummer as stuecklistevonartikelnummer, trim(s.menge)+0 as menge, s.art, 
     art2.nummer as artikelnummer, art3.nummer as alternativenummer,s.alternative
    FROM stueckliste s 
    INNER JOIN artikel art ON art.id = s.stuecklistevonartikel 
    INNER JOIN artikel art2 ON art2.id = s.artikel 
    LEFT JOIN artikel art3 ON art3.id = s.alternative 
    WHERE art.geloescht = 0 AND art.nummer = '".$xml['stuecklistevonartikel']."'"))
    )
    {
      $xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<stueckliste1>
</stueckliste1>
XML;
      $_xmlobj = new SimpleXMLExtended($xmlstr);

      $xmlobj = $_xmlobj->AddChild('stueckliste','');
      if(isset($sl[0]))
      {
        $stuecklistevonartikelnummer = $xmlobj->AddChild('stuecklistevonartikelnummer');
        $stuecklistevonartikelnummer->value = $sl[0]['stuecklistevonartikelnummer'];
      }
      //$out = '<beleg_list>';
      $i = 0;
      $j = 0;
      if($sl)
      {
        foreach($sl as $row)
        {
          $j++;
          $artikelobj = $xmlobj->AddChild('item');
          foreach($row as $k2 => $v2)
          {
            if(!is_numeric($k2) && $k2 != 'stuecklistevonartikelnummer')
            {
              $_name = '_'.$k2;
              $$_name = $artikelobj->AddChild($k2);
              $$_name->value = $v2;
            }
          }
        }
      }

      $xmlobj->AddChild('anz_result',$j);
      //$xmlobj->AddChild('anz_result',$sql);
      $out = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<stueckliste1>",'</stueckliste1>'),array('','',''),$_xmlobj->asXML());
      unset($_xmlobj);
      $this->XMLResponse(1, $out);
      $this->app->ExitXentral();
    }
    $this->XMLResponse(5,"<error>Artikelhat keine Stückliste</error>");
    $this->app->ExitXentral();
  }

  function ApiArtikelkategorienList($intern = false)
  {
    $xml = $this->XMLPost();
    $api_id = isset($this->api_id)?$this->api_id:0;
    $exakt = 0;
    if(isset($xml['ID']))$xml['id'] = $xml['ID'];
    if(isset($xml['ID_EXT']))$xml['id_ext'] = $xml['ID_EXT'];
    if(isset($xml['BEZEICHNUNG']))$xml['bezeichnung'] = $xml['BEZEICHNUNG'];
    if(isset($xml['EXAKT']))$xml['exakt'] = $xml['EXAKT'];

    $where = '';
    if($xml['id'])$where .= " AND ak.id = '".(int)$xml['id']."' ";
    if($xml['id_ext'])$where .= " AND am.id_ext = '".$this->app->DB->real_escape_string($xml['id_ext'])."' ";
    if($xml['bezeichnung'])
    {
      if($xml['exakt'] && $xml['exakt'] == 1)
      {
        $where .= " AND ak.bezeichnung like '".$this->app->DB->real_escape_string(base64_decode($xml['bezeichnung']))."' ";
      }else{
        $where .= " AND ak.bezeichnung like '%".$this->app->DB->real_escape_string(base64_decode($xml['bezeichnung']))."%' ";
      }
    }

    $kategorien = $this->app->DB->SelectArr("SELECT ak.*, am.id_ext FROM artikelkategorien ak LEFT JOIN api_mapping am ON am.id_int = ak.id AND am.tabelle = 'artikelkategorien' WHERE geloescht <> 1 $where ORDER BY parent, bezeichnung");
    if($kategorien)
    {
      $out = '<artikelkategorien>';
      foreach($kategorien as $kategorie)
      {
        $out .= '<kategorie>';
        foreach($kategorie as $k => $v)
        {
          $out .= '<'.$k.'>'.htmlspecialchars($v).'</'.$k.'>';
        }
        $out .= '</kategorie>';
      }
      $out .= '</artikelkategorien>';
      if ($intern == true) {
        return $out;
      }
      $this->XMLResponse(1, $out);
      $this->app->ExitXentral();
    }
    if ($intern == true) {
      return false;
    }
    $this->XMLResponse(8);
    $this->app->ExitXentral();
  }

  public function ApiDateiHeader()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    if($id)
    {
      $datei = $this->app->DB->SelectArr("SELECT dv.id, ds.parameter FROM datei_version dv INNER JOIN datei_stichwoerter ds ON ds.datei = dv.datei WHERE dv.datei = '$id' ORDER BY  dv.datei DESC, dv.version DESC LIMIT 1");
      if($datei)
      {
        $version = $this->app->DB->Select("SELECT MAX(version) FROM datei_version WHERE datei='$id'");
        $newid = $this->app->DB->Select("SELECT id FROM datei_version WHERE datei='$id' AND version='$version' LIMIT 1");

        /*if($versionid>0)
          $newid = $versionid;*/

        $name = $this->app->DB->Select("SELECT dateiname FROM datei_version WHERE id='$newid' LIMIT 1");
        if(isset($this->app->Conf->WFuserdata))
        {
          $path = $this->app->Conf->WFuserdata.'/dms/'.$this->app->Conf->WFdbname."/".$newid;
        }else
        $path = dirname(dirname(__DIR__)).'/userdata/dms/'.$this->app->Conf->WFdbname."/".$newid;
        //$name=basename($path);

        //filenames in IE containing dots will screw up the
        //filename unless we add this

        if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE"))
          $name = preg_replace('/\./', '%2e', $name, substr_count($name, '.') - 1);

        $contenttype= $this->app->erp->content_type($name);
        //required, or it might try to send the serving     //document instead of the file
        $res = "Content-Type: $contenttype\n";
        $res .= "Content-Length: " .(string)(filesize($path))."\n" ;
        $name = $this->app->erp->Dateinamen($name);
        $res .= 'Content-Disposition: attachment; filename="'.$name.'"';
        echo $res;
        $this->app->ExitXentral();
      }
    }
    $this->app->ExitXentral();
  }

  function ApiBelegPDFHeader()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $beleg = (string)$this->app->Secure->GetGET('beleg');
    if(in_array($beleg, array('auftrag','angebot','rechnung','lieferschein','gutschrift','produktion','retoure')))
    {
      if(!$this->app->DB->Select("SELECT id FROM $beleg WHERE id = '$id' LIMIT 1")) {
        $this->app->ExitXentral();
      }
      $this->app->erp->checkPDFClass($beleg);
      $classcustom = ucfirst($beleg).'PDFCustom';
      $class = ucfirst($beleg).'PDF';
      if(class_exists($classcustom))
      {
        $pdf = new $classcustom($this->app);
      }elseif(class_exists($class))
      {
        $pdf = new $class($this->app);
      }else{
        $this->app->ExitXentral();
      }
      $function = 'Get'.ucfirst($beleg);
      if(method_exists($pdf, $function))
      {
        $pdf->$function($id);
        $res = "Content-type: application/pdf\n";
        $res .= 'Content-Disposition: attachment; filename="'.$pdf->filename.'"';
        echo $res;
        $this->app->ExitXentral();
      }
    }
    $this->app->ExitXentral();
  }

  function ApiBelegPDF()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    $beleg = (string)$this->app->Secure->GetGET('beleg');
    if(in_array($beleg, array('auftrag','angebot','rechnung','lieferschein','gutschrift','produktion','retoure')))
    {
      if(!$this->app->DB->Select("SELECT id FROM $beleg WHERE id = '$id' LIMIT 1"))die('Beleg '.$id.' nicht gefunden');
      $this->app->erp->checkPDFClass($beleg);
      $classcustom = ucfirst($beleg).'PDFCustom';
      $class = ucfirst($beleg).'PDF';
      if(class_exists($classcustom))
      {
        $pdf = new $classcustom($this->app);
      }elseif(class_exists($class))
      {
        $pdf = new $class($this->app);
      }else{
        echo "PDF-Klasse nicht gefunden";
        $this->app->ExitXentral();
      }
      $function = 'Get'.ucfirst($beleg);
      if(method_exists($pdf, $function))
      {
        $pdf->$function($id);
        $pdf->displayDocument($this->app->DB->Select("SELECT schreibschutz FROM $beleg WHERE id = '$id' LIMIT 1"));
      }
    }elseif($beleg != ''){
      echo "Belegart nicht erlaubt";
    }else{
      echo "Belegart nicht angegeben";
    }
    $this->app->ExitXentral();
  }

  function ApiDateiDownload()
  {
    $id = (int)$this->app->Secure->GetGET('id');
    if($id)
    {
      $datei = $this->app->DB->SelectArr("SELECT dv.id, ds.parameter FROM datei_version dv INNER JOIN datei_stichwoerter ds ON ds.datei = dv.datei WHERE dv.datei = '$id' ORDER BY  dv.datei DESC, dv.version DESC LIMIT 1");
      if($datei) {
        $this->app->erp->SendDatei($id);
      }
    }
    $this->app->ExitXentral();
  }

  function ApiDateiVorschau()
  {
    //$xml = $this->XMLPost();
    $datei = $this->app->Secure->GetGET('id');
    $cmd = $this->app->Secure->GetGet('cmd');
    $id = (int)$datei;
    if($id)
    {
      $datei = $this->app->DB->SelectArr("SELECT dv.id, ds.parameter FROM datei_version dv INNER JOIN datei_stichwoerter ds ON ds.datei = dv.datei WHERE dv.datei = '$id'  ORDER BY  dv.datei DESC, dv.version DESC LIMIT 1");
      if(!$datei)
      {
        if ($this->app->erp->Firmendaten('iconset_dunkel')) {
          $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
        } else {
          $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
        }
        header('Content-type: image/png');
        echo $str;
        $this->app->ExitXentral();
      }
      if($datei[0]['parameter'])
      {
        if($cmd === 'projekt')
        {
          if(!$this->app->erp->UserProjektRecht($datei[0]['parameter']))
          {
            if ($this->app->erp->Firmendaten('iconset_dunkel')) {
              $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
            } else {
              $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
            }
            header('Content-type: image/png');
            echo $str;
            $this->app->ExitXentral();
          }
        }else{
          $projekt = $this->app->DB->Select("SELECT projekt FROM $cmd WHERE id = '".$datei[0]['parameter']."' LIMIT 1");
          if(!$this->app->erp->UserProjektRecht($projekt))
          {
            if ($this->app->erp->Firmendaten('iconset_dunkel')) {
              $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
            } else {
              $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
            }
            header('Content-type: image/png');
            echo $str;
            $this->app->ExitXentral();
          }
        }
      }
      //Rechte prüfen

      $userdata = isset($this->app->Conf->WFuserdata)?$this->app->Conf->WFuserdata:str_replace("index.php", "", $_SERVER['SCRIPT_FILENAME'])."../userdata";
      $path = $userdata."/dms/".$this->app->Conf->WFdbname;
      $cachefolder = $path.'/cache';
      if(!file_exists($cachefolder.'/'.$datei[0]['id'].'_100_100'))
      {
        if(file_exists($path.'/'.$datei[0]['id']))
        {
          $type = mime_content_type($path.'/'.$datei[0]['id']);
          switch($type)
          {
            case 'image/jpg':
            case 'image/jpeg':
              $img = new image($this->app);
              $str = $img->scaledPicByFileId($datei[0]['id'], 100, 100);
              header('Content-type: image/jpg');
              echo $str;
              $this->app->ExitXentral();
            break;
            case 'image/png':
              $img = new image($this->app);
              $str = $img->scaledPicByFileId($datei[0]['id'], 100, 100);
              header('Content-type: image/png');
              echo $str;
              $this->app->ExitXentral();
            break;
            case 'image/gif':
              $img = new image($this->app);
              $str = $img->scaledPicByFileId($datei[0]['id'], 100, 100);
              header('Content-type: image/gif');
              echo $str;
              $this->app->ExitXentral();
            break;
            case 'application/pdf':
              $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/pdf.svg');
              header('Content-type: image/png');
              echo $str;
              $this->app->ExitXentral();
            break;
          }
        }
      }

      if(file_exists($cachefolder.'/'.$datei[0]['id'].'_100_100'))
      {
        $type = mime_content_type($path.'/'.$datei[0]['id']);
        if(strpos($type,'image') !== false)
        {
          header('Content-type: '.$type);
          $str = file_get_contents($cachefolder.'/'.$datei[0]['id'].'_100_100');
          echo $str;
          $this->app->ExitXentral();
        }
      }else{
        if ($this->app->erp->Firmendaten('iconset_dunkel')) {
          $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
        } else {
          $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
        }
        header('Content-type: image/png');
        echo $str;
        $this->app->ExitXentral();
      }
    }else{
      if ($this->app->erp->Firmendaten('iconset_dunkel')) {
        $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
      } else {
        $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
      }
      header('Content-type: image/png');
      echo $str;
    }
    $this->app->ExitXentral();
  }

  function ApiCustom()
  {
    $out = null;
    $responsecode = 0;
    $xml = $this->XMLPost();
    $apiid = $this->api_id;
    $this->app->erp->RunHook('apicustom', 4, $apiid, $xml, $responsecode, $out);
    $this->XMLResponse($responsecode, $out);
    $this->app->ExitXentral();
  }

  public function ApiDateiList()
  {
    $xml = $this->XMLPost();
    $parameter = $xml['parameter'];
    $objekt = $xml['objekt'];
    $dateiliste = $this->app->DB->SelectArr( "SELECT d.titel, s.subjekt, v.version, v.ersteller, v.bemerkung, v.datum, d.id FROM 
    datei d LEFT JOIN datei_stichwoerter s ON d.id=s.datei LEFT JOIN datei_version v ON v.datei=d.id WHERE s.objekt LIKE '$objekt' AND s.parameter='$parameter' AND d.geloescht=0");

    if($dateiliste)
    {
      $xml = $this->CreateXmlObj();
      foreach($dateiliste as $k => $datei)
      {
        $_datei = $xml->AddChild('Datei','');
        foreach($datei as $k2 => $row)
        {
          $_datei->AddChild($k2 ,$row);
        }
      }
      $out = str_replace(array('<?xml version="1.0" standalone="yes"?>','<?xml version="1.0" encoding="UTF-8"?>',"<response>",'</response>'),array('','','',''),$xml->asXML());
      $this->XMLResponse(1, $out);
      $this->app->ExitXentral();
    }
    $this->XMLResponse(8);
    $this->app->ExitXentral();
  }

  function ApiAccountLogin()
  {
    $xml = $this->XMLPost();
    $user = $xml['user'];
    $md5passwort = !empty($xml['md5passwort'])?$xml['md5passwort']:md5($xml['passwort']);

    $art = $xml['art'];
    if(!empty($user) && !empty($md5passwort) && !empty($art))
    {
      $acc = $this->app->DB->SelectArr("SELECT adr.*, aa.benutzername FROM adresse_accounts aa INNER JOIN adresse adr ON aa.adresse = adr.id WHERE adr.geloescht <> 1 AND aa.art = '".$this->app->DB->real_escape_string($art)."' AND aa.aktiv = 1 AND (aa.gueltig_bis = '0000-00-00' OR aa.gueltig_bis >= curdate() or isnull(aa.gueltig_bis)) AND (aa.gueltig_ab = '0000-00-00' OR isnull(gueltig_ab) OR gueltig_ab <= curdate()) AND aa.benutzername = '".$this->app->DB->real_escape_string($user)."' AND md5(aa.passwort) = '".$this->app->DB->real_escape_string($md5passwort)."' AND aa.art = '".$this->app->DB->real_escape_string($art)."' LIMIT 1");
      if($acc)
      {
        $xml = $this->CreateXmlObj();
        $xml->AddChild('name', $acc[0]['name']);
        $xml->AddChild('adresse', $acc[0]['id']);
        $xml->AddChild('benutzername', $acc[0]['benutzername']);
        $out = str_replace(array('<?xml version="1.0" standalone="yes"?>','<?xml version="1.0" encoding="UTF-8"?>',"<response>",'</response>'),array('','','',''),$xml->asXML());
        $this->XMLResponse(1, $out);
        $this->app->ExitXentral();
      }
    }
    $this->XMLResponse(8);
    $this->app->ExitXentral();
  }


  function ApiBelegOhnePositionenList(){
    $xml = $this->XMLPost();
    $belegtypen = array('auftrag','rechnung','angebot','lieferschein','gutschrift','retoure');

    //wir holen alle Projektdaten
    $projekteData = $this->app->DB->SelectArr("SELECT id,abkuerzung FROM projekt");
    $projekte = array();
    foreach($projekteData as $value){
      $projekte[$value['id']] = $value['abkuerzung'];
    }

    if(!in_array($xml['beleg'], $belegtypen)){
      $this->XMLResponse(5,"falsscher oder kein Belegtype im Feld beleg angegeben(auftrag,rechnung,angebot,lieferschein,gutschrift)");
      $this->app->ExitXentral();
    }

    $belegtype = $xml['beleg'];

    $sql = "SELECT 
      $belegtype.id AS id,
      $belegtype.projekt AS projekt,
      $belegtype.adresse AS adresse,
      $belegtype.datum AS datum,
      $belegtype.belegnr AS belegnr,
      $belegtype.status AS status,
      $belegtype.land AS land,
      $belegtype.typ AS typ";

    if($belegtype !== 'lieferschein' && $belegtype !== 'retoure'){
      $sql .= ",
      $belegtype.umsatz_netto AS umsatz_netto,
      $belegtype.erloes_netto AS erloes_netto,
      $belegtype.deckungsbeitrag AS deckungsbeitrag,
      $belegtype.provision_summe AS provision_summe,
      $belegtype.gruppe AS gruppe      
      ";
    }
    $sql .= " FROM $belegtype";

    if($xml['status'] != ''){
      $subwhere[] = "status = '" . $xml['status'] . "'";
    }

    if($xml['projekt'] != ''){
        if(!is_numeric($xml['projekt'])){
          $projektId = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xml['projekt']."' LIMIT 1");
        }else{
          $projektId = $xml['projekt'];
        }
      $subwhere[] = "projekt = '" . $projektId . "'";
    }

    if($xml['datum_von'] != ''){
      if(!strpos($xml['datum_von'],"-")) {
        $datum_von = $this->app->String->Convert($xml['datum_von'], '%1.%2.%3', '%3-%2-%1');
      }else{
        $datum_von = $xml['datum_von'];
      }
      $subwhere[] = "datum >= '" . $datum_von . "'";
    }

    if($xml['datum_bis'] != ''){
      if(!strpos($xml['datum_bis'],"-")) {
        $datum_bis = $this->app->String->Convert($xml['datum_bis'], '%1.%2.%3', '%3-%2-%1');
      }else{
        $datum_bis = $xml['datum_bis'];
      }
      $subwhere[] = "datum <= '" . $datum_bis . "'";
    }

    if($subwhere)$sql .= " WHERE ".implode(" AND ", $subwhere);

    if($xml['order'] != ''){
      $sql .= " ORDER BY " . $xml['order']['field'];
      if($xml['order']['desc'] == '1'){
        $sql .= " DESC";
      }
      if($xml['order']['asc'] == '1'){
        $sql .= " ASC";
      }
    }

    if($xml['limit'] != '' && $xml['offset'] != ''){
      $sql .= " Limit " . $xml['offset'] . ", " . $xml['limit'];
    }
    $belege = $this->app->DB->SelectArr($sql);

    $xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<belege_list1>
</belege_list1>
XML;
    $_xmlobj = new SimpleXMLExtended($xmlstr);

    $xmlobj = $_xmlobj->AddChild('beleg_list','');

    foreach($belege as $beleg) {
      $belegobj = $xmlobj->AddChild('beleg');
      $_belegobj = $belegobj->AddChild('id', $beleg['id']);
      $_belegnr = $belegobj->AddChild('belegnr', $beleg['belegnr']);
      $_projektid = $belegobj->AddChild('projektid',$beleg['projekt']);
      $_projekt = $belegobj->AddChild('projekt',$projekte[$beleg['projekt']]);
      $_adresse = $belegobj->AddChild('adresse',$beleg['adresse']);
      $_datum = $belegobj->AddChild('datum',$beleg['datum']);
      $_status = $belegobj->AddChild('status',$beleg['status']);
      $_land = $belegobj->AddChild('land',$beleg['land']);
      $_typ = $belegobj->AddChild('typ',$beleg['typ']);

      if($belegtype !== 'lieferschein' && $belegtype !== 'retoure'){
        $_umsatz_netto = $belegobj->AddChild('umsatz_netto',$beleg['umsatz_netto']);
        $_erloes_netto = $belegobj->AddChild('erloes_netto',$beleg['erloes_netto']);
        $_deckungsbeitrag = $belegobj->AddChild('deckungsbeitrag',$beleg['deckungsbeitrag']);
        $_provision_summe = $belegobj->AddChild('provision_summe',$beleg['provision_summe']);
        $_gruppe = $belegobj->AddChild('gruppe',$beleg['gruppe']);
      }
    }

    $out = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<belege_list1>",'</belege_list1>'),array('','',''),$_xmlobj->asXML());
    unset($_xmlobj);
    $this->XMLResponse(1, $out);
    $this->app->ExitXentral();
  }


  function ApiBelegeList()
  {
    $xml = $this->XMLPost();
    if($xml['ausgabe'] === 'belege'){
      $this->ApiBelegOhnePositionenList();
      $this->app->ExitXentral();
    }
    $api_id = isset($this->api_id)?$this->api_id:0;
    $adresse = false;
    $belega = null;
    $belegtypen = array('auftrag','rechnung','angebot','lieferschein','gutschrift','retoure');
    $join = '';
    $extraselect = '';
    $groupbyartikel = false;
    if(isset($xml['groupbyartikel']) && $xml['groupbyartikel'])$groupbyartikel = true;
    $groupbyposition = false;
    if(isset($xml['groupbyposition']) && $xml['groupbyposition'])$groupbyposition = true;
    $groupbyadresse = false;
    if(isset($xml['groupbyadresse']) && $xml['groupbyadresse'])$groupbyadresse = true;
    $groupbybeleg = false;
    if(isset($xml['groupbybeleg']) && $xml['groupbybeleg'])$groupbybeleg = true;
    $groupbybeleg2 = $groupbybeleg;
    $groupbybeleg = false;
    if(isset($xml['beleg']))
    {
      if(is_array($xml['beleg']))
      {
        foreach($xml['beleg'] as $_beleg)
        {
          if(in_array($_beleg, $belegtypen))$belega[] = $_beleg;
        }
      }
      if(in_array($xml['beleg'], $belegtypen))$belega[] = $xml['beleg'];
    }

    if(!$belega)
    {
      $this->XMLResponse(8);
      $this->app->ExitXentral();
    }

    $order = '';
    if(isset($xml['order'])){
      if(!isset($xml['order'][0]))
      {
        $xml['order'][0] = $xml['order'];
      }
      foreach($xml['order'] as $key => $sort)
      {
        $field = '';
        if(isset($sort['field']))
        {
          switch(strtolower($sort['field']))
          {
            case 'artikelkategorie':
              $field = 'ak.bezeichnung';
              if($groupbyartikel)$field = 't.artikelkategoriebezeichnung';
              $join = " INNER JOIN artikelkategorien ak ON concat(ak.id,'_kat') = a.typ ";
              $extraselect = ' ,ak.bezeichnung as artikelkategoriebezeichnung ';
            break;
            case 'status':
            case 'belegnr':
            case 'datum':
              if(!$groupbyartikel)$field = 'r.'.strtolower($sort['field']);
            break;
            case 'nummer':
              if(!$groupbyartikel)$field = 'rp.'.strtolower($sort['field']);
              if($groupbyartikel)$field = 't.'.strtolower($sort['field']);
            break;
            case 'menge':
              if(!$groupbyartikel)$field = 'rp.'.strtolower($sort['field']);
              if($groupbyartikel)$field = 'menge';
            break;
            case 'preis':
            case 'bezeichnung':
              if(!$groupbyartikel)$field = 'rp.'.strtolower($sort['field']);
            break;
            case 'name':
            case 'plz':
            case 'ort':
            case 'telefon':
            case 'land':
            case 'telefax':
            case 'strasse':
              if($groupbyadresse && $groupbyartikel)$field = 't.'.strtolower($sort['field']);
              if(!$groupbyartikel)$field = 'r.'.strtolower($sort['field']);
            break;
            case 'name_de':
            case 'name_en':
            case 'anabregs_text':
            case 'anabregs_text_en':
              if($groupbyartikel)$field = 't.'.strtolower($sort['field']);
            break;
            case 'betrag':
            case 'letztes_datum':
              if($groupbyartikel)$field = strtolower($sort['field']);
            break;
          }
          if(!empty($field))
          {
            if(isset($sort['desc']) && $sort['desc'] == 1)
            {
              $field .= " DESC";
            }
            if($order != '')$order .= ',';
            $order .= $field;
          }
        }
      }
    }
    if($order != '')$order = $order.',';
    if(isset($xml['adresse']))
    {
      $adresse = (int)$xml['adresse'];
    }elseif(isset($xml['id_ext']))
    {
      $adresse = $this->app->DB->Select("SELECT id_int FROM api_mapping WHERE api = '".$api_id."' AND tabelle = 'adresse' AND id_ext = '".$this->app->DB->real_escape_string($xml['id_ext'])."' LIMIT 1");
    }
    if(true)
    {
      $filterkategoriename = false;
      if(isset($xml['status']))
      {
        $subwhere[] = " r.status like '".$this->app->DB->real_escape_string($xml['status'])."' ";
      }
      if(isset($xml['kategorie']))
      {
        $subwhere[] = " a.typ = '".((int)$xml['kategorie'])."_kat' ";
      }
      if(isset($xml['kategoriename']))
      {
        $field = 'ak.bezeichnung';
        $join = " INNER JOIN artikelkategorien ak ON concat(ak.id,'_kat') = a.typ ";
        $extraselect = ' ,ak.bezeichnung as artikelkategoriebezeichnung ';
        $subwhere[] = " ak.bezeichnung like '".$this->app->DB->real_escape_string(base64_decode((string)$xml['kategoriename']))."' ";
        $filterkategoriename = true;
      }
      if(isset($xml['datum_von']))
      {
        $subwhere[] = "r.datum >= '".$this->app->DB->real_escape_string($xml['datum_von'])."' ";
      }
      if(isset($xml['datum_bis']))
      {
        $subwhere[] = "r.datum <= '".$this->app->DB->real_escape_string($xml['datum_bis'])."' ";
      }

      if(isset($xml['vertrieb']))
      {
        $subwhere[] = " r.vertriebid = '".(int)$xml['vertrieb']."' ";
      }
      $limit = '';
      $offset = 0;
      $_limit = 999999999;
      if(isset($xml['limit']))
      {
        if((int)$xml['limit'] > 0)$_limit = (int)$xml['limit'];
        if(isset($xml['offset']) && (int)$xml['offset'] > 0)
        {
          $offset = (int)$xml['offset'];
          //$limit = ' LIMIT '.(int)$xml['offset'].', '.(int)$xml['limit'];
        }else{
          //$limit = ' LIMIT '.(int)$xml['limit'];
        }
      }

      $searchmode = ' OR ';
      foreach($xml as $key => $filter)
      {
        $filterescaped = '';
        if(!is_array($filter))$filterescaped = $this->app->DB->real_escape_string($filter);

        switch(strtoupper($key))
        {
          case 'SEARCHMODE':
            if(strtoupper($filter) === 'AND') {
              $searchmode = ' AND ';
            }
            if(strtoupper($filter) === 'OR') {
              $searchmode = ' OR ';
            }
          break;
          case 'EXAKT':
            $exakt = true;
          break;
          case 'SEARCH':

            if(is_array($filter))
            {

              if(!isset($filter[0]))$filter[0] = $filter;

              foreach($filter as $_filter)
              {
                if(isset($_filter['SUCHE']))$_filter['suche'] = $_filter['SUCHE'];
                if(isset($_filter['FIELD']))$_filter['field'] = $_filter['FIELD'];
                if(isset($_filter['EXAKT']))$_filter['exakt'] = $_filter['EXAKT'];
                if(isset($_filter['suche']) && isset($_filter['field']))
                {
                  if(isset($_filter['exakt']) && $_filter['exakt'] == 1)
                  {
                    $like = " LIKE '".$this->app->DB->real_escape_string(base64_decode($_filter['suche']))."' ";
                  }else{
                    $like = " LIKE '%".$this->app->DB->real_escape_string(base64_decode($_filter['suche']))."%' ";
                  }
                  switch(strtolower($_filter['field']))
                  {
                    case 'datum':
                      if(strpos('.',$like) !== false)
                      {
                        if(isset($_filter['exakt']) && $_filter['exakt'] == 1)
                        {
                          $like = " LIKE '".$this->app->DB->real_escape_string($this->app->String->Convert(base64_decode($_filter['suche']),"%1.%2.%3","%3-%2-%1"))."' ";
                        }else{
                          $like = " LIKE '%".$this->app->DB->real_escape_string($this->app->String->Convert(base64_decode($_filter['suche']),"%1.%2.%3","%3-%2-%1"))."%' ";
                        }
                        $swhere[] = ' r.datum '.$like;
                      }
                    break;
                    case 'belegnr':
                      $swhere[] = ' r.belegnr '.$like;
                    break;
                    case 'nummer':
                      $swhere[] = ' a.'.strtolower($_filter['field']).' '.$like;
                    break;
                    case 'bezeichnung':
                      if($groupbyartikel)
                      {
                        if(strtolower($_filter['field']) == 'nummer')
                        {

                        }else{
                          $swhere[] = ' a.name_de '.$like;
                        }
                      }else{
                        $swhere[] = ' rp.'.strtolower($_filter['field']).' '.$like;
                      }
                    break;
                    case 'name_de':
                      $swhere[] = ' a.name_de '.$like;
                    break;
                    case 'kategorieid':
                      if(isset($_filter['exakt']) && $_filter['exakt'] == 1)
                      {
                        $like = " LIKE '".$this->app->DB->real_escape_string(base64_decode($_filter['suche']))."_kat' ";
                      }else{
                        $like = " LIKE '%".$this->app->DB->real_escape_string(base64_decode($_filter['suche']))."%_kat' ";
                      }
                      $swhere[] = ' a.typ '.$like;
                    break;
                    case 'kategoriename':
                      if(!isset($_filterkategoriename) || !$_filterkategoriename)
                      {
                        $join = " INNER JOIN artikelkategorien ak ON concat(ak.id,'_kat') = a.typ ";
                        $extraselect = ' ,ak.bezeichnung as artikelkategoriebezeichnung ';
                        $swhere[] = ' ak.bezeichnung '.$like;
                        $_filterkategoriename = true;
                      }
                    break;
                    case 'name':
                    case 'plz':
                    case 'ort':
                    case 'strasse':
                    case 'kundennummer':

                      if($groupbyadresse)$swhere[] = ' adr.'.strtolower($_filter['field']).' '.$like;
                      if(!$groupbyadresse)$swhere[] = ' r.'.strtolower($_filter['field']).' '.$like;
                    break;
                    break;
                  }
                }
              }
            }
          break;
        }
      }

      if(!empty($swhere))$subwhere[] = " (".implode( ' '.$searchmode.' ', $swhere).') ';

      $where = "";
      if($subwhere)$where = " AND ".implode(" AND ", $subwhere);
      unset($belege);
      $torder = trim($order,',');
      if(!$torder)$torder = ' 1 ';
      $groupbybelegsql = "";
      if($groupbybeleg)$groupbybelegsql = " ,t.id ";
      if($groupbybeleg)$groupbybelegsql2 = " ,r.id ";
      if($groupbyartikel)
      {
        if($groupbyadresse)
        {
          $sql = "SELECT t.adresse, t.name,t.strasse, t.plz, t.land, t.ort, t.kundennummer , t.aid, t.nummer, t.name_de, t.name_en, 
          t.anabregs_text, t.anabregs_text_en, t.artikelkategorie  ,t.artikelkategoriebezeichnung , sum(t.menge) as menge, 
          sum(t.betrag) as betrag, max(t.letztes_datum) as letztes_datum FROM( ";

          foreach($belega as $beleg)
          {

            $sqlb[] = " (SELECT  adr.id as adresse, adr.name,adr.strasse, adr.plz, adr.land, adr.ort, adr.kundennummer , a.id as aid, a.nummer, a.name_de, a.name_en, a.anabregs_text, a.anabregs_text_en, a.typ as artikelkategorie  ,ak.bezeichnung as artikelkategoriebezeichnung , sum(rp.menge) as menge, ".($beleg == 'gutschrift'?'-':'')."sum(rp.menge * (rp.preis*((100-rp.rabatt)/100) )) as betrag, max(r.datum) as letztes_datum  FROM $beleg r INNER JOIN ".$beleg."_position rp ON r.id = rp.".$beleg." INNER JOIN artikel a ON rp.artikel = a.id INNER JOIN adresse adr ON r.adresse = adr.id  LEFT JOIN artikelkategorien ak ON concat(ak.id,'_kat') = a.typ WHERE ".($adresse?("r.adresse = '".$adresse."'"):"1")." $where GROUP BY adr.id, a.id $groupbybelegsql2) ";

          }

          $sql .= implode(" UNION ALL ",$sqlb).") t WHERE t.aid > 0 AND t.adresse > 0 GROUP BY t.aid, t.adresse ORDER BY $torder  $limit";
          $_belege = $this->app->DB->SelectArr($sql);

        }else{

          $sql = "SELECT   t.aid, t.nummer, t.name_de, t.name_en, t.anabregs_text, t.anabregs_text_en, t.artikelkategorie  ,t.artikelkategoriebezeichnung  , t.menge,sum(t.betrag) as betrag,  max(t.letztes_datum) as letztes_datum FROM( ";

          foreach($belega as $beleg)
          {

            $sqlb[] = " (SELECT  r.id as bid,r.datum,rp.sort, a.id as aid, a.nummer, a.name_de, a.name_en, a.anabregs_text, a.anabregs_text_en, a.typ  as artikelkategorie ,ak.bezeichnung as artikelkategoriebezeichnung , sum(rp.menge) as menge, ".($beleg == 'gutschrift'?'-':'')."sum(rp.menge * (rp.preis*((100-rp.rabatt)/100) )) as betrag, max(r.datum) as letztes_datum  FROM $beleg r INNER JOIN ".$beleg."_position rp ON r.id = rp.".$beleg." INNER JOIN adresse adr ON r.adresse = adr.id INNER JOIN artikel a ON rp.artikel = a.id  LEFT JOIN artikelkategorien ak ON concat(ak.id,'_kat') = a.typ  WHERE ".($adresse?("r.adresse = '".$adresse."'"):"1")." $where GROUP BY a.id, adr.id $groupbybelegsql2) ";

          }

          $sql .= implode(" UNION ALL ",$sqlb).") t WHERE t.aid > 0 GROUP BY t.aid ORDER BY $torder  $limit";

          $_belege = $this->app->DB->SelectArr($sql);
        }
        if($_belege)
        {
          foreach($_belege as $k => $position)
          {
            //$position['preis'] = $position['menge'] * $position['preis'] * (1-$position['rabatt']/100);
            $belege[] = $position;
            unset($_belege[$k]);
          }
        }
      }else{
        foreach($belega as $beleg)
        {
          if($groupbybeleg)
          {
            $sql = "SELECT '$beleg' as beleg ,r.adresse, r.name,r.plz,r.ort,r.strasse,r.land,r.ansprechpartner,r.datum,r.status as rstatus,r.belegnr,r.id as belegid, a.typ as artikelkategorie $extraselect FROM $beleg r LEFT JOIN ".$beleg."_position rp ON r.id = rp.".$beleg." LEFT JOIN artikel a ON rp.artikel = a.id $join WHERE ".($adresse?("r.adresse = '".$adresse."'"):"1")." $where GROUP BY r.id ORDER BY $order r.datum, rp.sort  $limit ";
          }else{
            $sql = "SELECT '$beleg' as beleg ,r.adresse, r.name,r.plz,r.ort,r.strasse,r.land,r.ansprechpartner,r.datum,r.status as rstatus,r.belegnr,r.id as belegid, rp.*, a.typ as artikelkategorie $extraselect FROM $beleg r LEFT JOIN ".$beleg."_position rp ON r.id = rp.".$beleg." LEFT JOIN artikel a ON rp.artikel = a.id $join WHERE ".($adresse?("r.adresse = '".$adresse."'"):"1")." $where ORDER BY $order r.datum, rp.sort  $limit ";
          }
          $_belege = $this->app->DB->SelectArr($sql);
          //$this->app->erp->LogFile(addslashes($this->app->DB->error()));
          //$this->app->erp->LogFile(addslashes($sql));
          if($_belege)
          {
            foreach($_belege as $k => $position)
            {
              $position['status'] = $position['rstatus'];
              unset($position['rstatus']);
              $position['preis'] = $position['menge'] * $position['preis'] * (1-$position['rabatt']/100);
              $belege[] = $position;
              unset($_belege[$k]);
            }
          }
        }
      }


      /*
      foreach($belega as $beleg)
      {
        if($groupbyartikel)
        {
          if($groupbyadresse)
          {
            $_belege = $this->app->DB->SelectArr("SELECT '$beleg' as beleg, adr.id as adresse, adr.name,adr.strasse, adr.plz, adr.land, adr.ort, adr.kundennummer , a.id, a.nummer, a.name_de, a.name_en, a.anabregs_text, a.anabregs_text_en, a.typ as artikelkategorie  ,ak.bezeichnung as artikelkategoriebezeichnung , sum(rp.menge) as menge, sum(rp.menge * (rp.preis*((100-rp.rabatt)/100) )) as betrag, max(r.datum) as letztes_datum  FROM $beleg r INNER JOIN ".$beleg."_position rp ON r.id = rp.".$beleg." INNER JOIN artikel a ON rp.artikel = a.id INNER JOIN adresse adr ON r.adresse = adr.id  INNER JOIN artikelkategorien ak ON concat(ak.id,'_kat') = a.typ  WHERE ".($adresse?("r.adresse = '".$adresse."'"):"1")." $where GROUP BY a.id, adr.id ORDER BY $order r.datum, rp.sort  $limit ");

          }else{
            $_belege = $this->app->DB->SelectArr("SELECT '$beleg' as beleg ,  a.id, a.nummer, a.name_de, a.name_en, a.anabregs_text, a.anabregs_text_en, a.typ as artikelkategorie  ,ak.bezeichnung as artikelkategoriebezeichnung , sum(rp.menge) as menge, sum(rp.menge * (rp.preis*((100-rp.rabatt)/100) )) as betrag, max(r.datum) as letztes_datum  FROM $beleg r INNER JOIN ".$beleg."_position rp ON r.id = rp.".$beleg." INNER JOIN artikel a ON rp.artikel = a.id  INNER JOIN artikelkategorien ak ON concat(ak.id,'_kat') = a.typ  WHERE ".($adresse?("r.adresse = '".$adresse."'"):"1")." $where GROUP BY a.id ORDER BY $order r.datum, rp.sort  $limit ");
          }
          if($_belege)
          {
            foreach($_belege as $k => $position)
            {
              //$position['preis'] = $position['menge'] * $position['preis'] * (1-$position['rabatt']/100);
              $belege[] = $position;
              unset($_belege[$k]);
            }
          }
        }
      }*/

$xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<belege_list1>
</belege_list1>
XML;
      $_xmlobj = new SimpleXMLExtended($xmlstr);
      if($groupbyartikel)
      {
        $xmlobj = $_xmlobj->AddChild('artikel_list','');
      }else{
        $xmlobj = $_xmlobj->AddChild('beleg_list','');
      }
      //$out = '<beleg_list>';
      $i = 0;
      $j = 0;
      $anz_belegegesamt = -1;
      $anz_belegeresult = 0;
      $old_beleg = 0;
      $old_beleggesamt = 0;
      if($belege)
      {
        if($groupbyartikel)
        {
          foreach($belege as $position)
          {
            if($i >= $offset && $i < $_limit + $offset)
            {
              $j++;
              $belegobj = $xmlobj->AddChild('artikel');
              $_belegobj = $belegobj->AddChild('id','');
              $_belegobj->value = $position['aid'];
              $_nummer = $belegobj->AddChild('nummer','');
              $_nummer->value = $position['nummer'];
              $_name_de = $belegobj->AddChild('name_de','');
              $_name_de->value = $position['name_de'];
              $_menge = $belegobj->AddChild('menge','');
              $_menge->value = round($position['menge'],4);
              $_betrag = $belegobj->AddChild('betrag','');
              $_betrag->value = $position['betrag'];
              $_artikelkategorie = $belegobj->AddChild('artikelkategorie','');
              $_artikelkategorie->value = $position['artikelkategorie'];
              $_letztes_datum = $belegobj->AddChild('letztes_datum','');
              $_letztes_datum->value = $position['letztes_datum'];
              $_artikelkategoriebezeichnung = $belegobj->AddChild('artikelkategoriebezeichnung','');
              $_artikelkategoriebezeichnung->value = $position['artikelkategoriebezeichnung'];
              if($groupbyadresse)
              {
                $_adresse = $belegobj->AddChild('adresse','');
                $_adresse->value = $position['adresse'];
                $_kundennummer = $belegobj->AddChild('kundennummer','');
                $_kundennummer->value = $position['kundennummer'];
                $_name = $belegobj->AddChild('name','');
                $_name->value = $position['name'];
                $_ort = $belegobj->AddChild('ort','');
                $_ort->value = $position['ort'];
                $_plz = $belegobj->AddChild('plz','');
                $_plz->value = $position['plz'];
                $_strasse = $belegobj->AddChild('strasse','');
                $_strasse->value = $position['strasse'];
                $_land = $belegobj->AddChild('land','');
                $_land->value = $position['land'];
              }
            }
            $i++;
          }

        }else{
          $old = 0;
          foreach($belege as $position)
          {
            if($old_beleggesamt != $position['belegid'].$position['beleg'])
            {
              $anz_belegegesamt++;
              $old_beleggesamt = $position['belegid'].$position['beleg'];
            }
            if(($groupbybeleg2?$anz_belegegesamt:$i) >= $offset && ($groupbybeleg2?$anz_belegegesamt:$i) < $_limit + $offset)
            {
              $j++;
              if($groupbyposition || $old != $position['belegid'].$position['beleg'])
              {
                $anz_belegeresult++;
                $belegobj = $xmlobj->AddChild($position['beleg']);
                //if($old != 0)$out .= '</'.$position['beleg'].'>';
                $old = $position['belegid'].$position['beleg'];

                //$out .= '<'.$position['beleg'].'>';

                $_belegobj = $belegobj->AddChild('belegid','');
                $_belegobj->value = $position['belegid'];
                $_plz = $belegobj->AddChild('plz','');
                $_plz->value = $position['plz'];
                $_ort = $belegobj->AddChild('ort','');
                $_ort->value = $position['ort'];
                $_strasse = $belegobj->AddChild('strasse','');
                $_strasse->value = $position['strasse'];
                $_land = $belegobj->AddChild('land','');
                $_land->value = $position['land'];
                $_ansprechpartner = $belegobj->AddChild('ansprechpartner','');
                $_ansprechpartner->value = $position['ansprechpartner'];
                $_datum = $belegobj->AddChild('datum','');
                $_datum->value = $position['datum'];
                $_belegnr = $belegobj->AddChild('belegnr','');
                $_belegnr->value = $position['belegnr'];
                $_status = $belegobj->AddChild('status','');
                $_status->value = $position['status'];
                $_adresse = $belegobj->AddChild('adresse','');
                $_adresse->value = $position['adresse'];
                $_name = $belegobj->AddChild('name','');
                $_name->value = $position['name'];
                //$out .= '<belegid>'.$position['belegid'].'</belegid>';
                //$out .= '<datum>'.$position['datum'].'</datum>';
                //$out .= '<belegnr>'.$position['belegnr'].'</belegnr>';
                //$out .= '<status>'.$position['status'].'</status>';
                //$out .= '<adresse>'.$position['adresse'].'</adresse>';
              }
              $_positionobj = $belegobj->AddChild('position','');
              //$out .= '<position>';
              foreach($position as $k => $v)
              {
                $objn = $k.'_obj';
                $$objn = $_positionobj->AddChild($k,'');
                $$objn->value = $v;
                //$out .= '<'.$k.'>'.htmlspecialchars($v).'</'.$k.'>';
              }
              //$out .= '</position>';
            }
            $i++;
          }
        }
        //$out .= '</'.$position['beleg'].'>';
      }
      $anz_belegegesamt++;
      //$out .= '</beleg_list><anz_gesamt>'.count($belege).'</anz_gesamt><anz_result>'.$j.'</anz_result>';
      $xmlobj->AddChild('anz_gesamt',count($belege));
      $xmlobj->AddChild('anz_result',$j);
      if(!$groupbyartikel)
      {
        $xmlobj->AddChild('anz_belegegesamt',$anz_belegegesamt);
        $xmlobj->AddChild('anz_belegeresult',$anz_belegeresult);
      }
      $out = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<belege_list1>",'</belege_list1>'),array('','',''),$_xmlobj->asXML());
      unset($_xmlobj);
      $this->XMLResponse(1, $out);
      $this->app->ExitXentral();
    }
    $this->XMLResponse(8);
    $this->app->ExitXentral();
  }

  function ApiGruppenList()
  {
    $gruppen = $this->app->DB->SelectArr("SELECT * FROM gruppen WHERE art like 'gruppe' ");
    if($gruppen)
    {
      $out = '<gruppen>';
      foreach($gruppen as $gruppe)
      {
        $out .= '<gruppe>';
        foreach($gruppe as $k => $v )
        {
          $out .= '<'.$k.'>'.$v.'</'.$k.'>';
        }
        $out .= '</gruppe>';
      }
      $out .= '</gruppen>';
      $this->XMLResponse(1, $out);
      $this->app->ExitXentral();
    }
    $this->XMLResponse(8);
    $this->app->ExitXentral();
  }

  function ApiAdresseGruppenList()
  {
    $xml = $this->XMLPost();
    $api_id = isset($this->api_id)?$this->api_id:0;
    if(isset($xml['adresse']))
    {
      $adresse = (int)$xml['adresse'];

    }elseif(isset($xml['id_ext']))
    {
      $adresse = $this->app->DB->Select("SELECT id_int FROM api_mapping WHERE api = '".$api_id."' AND tabelle = 'adresse' AND id_ext = '".$this->app->DB->real_escape_string($xml['id_ext'])."' LIMIT 1");
    }
    if($adresse)
    {
      $gruppen = $this->app->DB->SelectArr("SELECT gr.id as gruppe, gr.name, am.id_int FROM adresse_rolle ar LEFT JOIN gruppen gr ON gr.id = ar.parameter LEFT JOIN api_mapping am ON am.id_int = gr.id AND am.tabelle = 'gruppen' WHERE  ar.adresse = '$adresse' AND ar.subjekt like 'Mitglied' AND objekt like 'Gruppe'");
      if($gruppen)
      {
        $out = '<gruppen>';
        foreach($gruppen as $gruppe)
        {
          $out .= '<gruppe>';
          foreach($gruppe as $k => $v )
          {
            $out .= '<'.$k.'>'.$v.'</'.$k.'>';
          }
          $out .= '</gruppe>';
        }
        $out .= '</gruppen>';
        $this->XMLResponse(1, $out);
        $this->app->ExitXentral();
      }
      $this->XMLResponse(8);
      $this->app->ExitXentral();
    }
    $this->XMLResponse(8);
    $this->app->ExitXentral();
  }


  public function ApiAccountList()
  {
    $this->XMLPost();
    $api_id = isset($this->api_id)?$this->api_id:0;
    if($api_id)
    {
      $url = $this->app->DB->Select("SELECT remotedomain FROM api_account WHERE id = '$api_id' LIMIT 1");
      $adresse = $this->app->Secure->GetGET('adresse');
      if(!empty($adresse)){
        $accounts = $this->app->DB->SelectArr("SELECT ac.adresse, ac.art, ac.benutzername,ac.passwort, ac.webid, am.id_ext, a.name, a.email FROM adresse_accounts ac INNER JOIN adresse a ON ac.adresse = a.id LEFT JOIN api_mapping am ON am.id_int = ac.adresse AND am.api = '$api_id' AND am.tabelle = 'adresse' WHERE ac.aktiv = 1 AND ac.url = '".$this->app->DB->real_escape_string($url)."' AND (ac.gueltig_bis <= now() or isnull(ac.gueltig_bis)) AND ac.adresse={$adresse}");
      }else{
        $accounts = $this->app->DB->SelectArr("SELECT ac.adresse, ac.art, ac.benutzername,ac.passwort, ac.webid, am.id_ext, a.name, a.email FROM adresse_accounts ac INNER JOIN adresse a ON ac.adresse = a.id LEFT JOIN api_mapping am ON am.id_int = ac.adresse AND am.api = '$api_id' AND am.tabelle = 'adresse' WHERE ac.aktiv = 1 AND ac.url = '".$this->app->DB->real_escape_string($url)."' AND (ac.gueltig_bis <= now() or isnull(ac.gueltig_bis))");
      }
      if($error = $this->app->DB->error())
      {
        $this->XMLResponse(1,'<sqlerror>'.$error.'</sqlerror>');
      }
      if($accounts)
      {
        $out = '<list>';
        foreach($accounts as $account)
        {
          $out .= '<account>';
          foreach($account as $key => $value)
          {
            $out .= '<'.$key.'>'.(is_null($value)?'':$value).'</'.$key.'>';
          }
          $out .= '</account>';
        }
        $out .= '</list>';
        $this->XMLResponse(1,$out);
        $this->app->ExitXentral();
      }
      $this->XMLResponse(8);
      $this->app->ExitXentral();
    }
    $this->XMLResponse(3);
    $this->app->ExitXentral();
  }

  function ApiMappingGet()
  {
    $xml = $this->XMLPost();
    $api_id = isset($this->api_id)?$this->api_id:0;
    if(is_array($xml))
    {
      $list = false;
      if(isset($xml['list']))
      {
        $list = $xml['list'];
      }elseif($xml['LIST'])
      {
        $list = $xml['LIST'];
      }
      if(!$list)
      {
        $table = isset($xml['table'])?$xml['table']:(isset($xml['TABLE'])?$xml['TABLE']:'');
        $id_int = isset($xml['id_int'])?$xml['id_int']:(isset($xml['ID_INT'])?$xml['ID_INT']:'');
        $id_ext = isset($xml['id_ext'])?$xml['id_ext']:(isset($xml['ID_EXT'])?$xml['ID_EXT']:'');
        if($table && (!empty($id_int) || !empty($id_ext)))
        {
          if(!empty($id_int))
          {
            $ergebnis = $this->app->DB->SelectArr("SELECT * FROM api_mapping WHERE tabelle = '".$this->app->DB->real_escape_string($xml['table'])."' AND id_int = '".$this->app->DB->real_escape_string($id_int)."' AND api = '".$api_id."' LIMIT 1");
          }else{
            $ergebnis = $this->app->DB->SelectArr("SELECT * FROM api_mapping WHERE tabelle = '".$this->app->DB->real_escape_string($xml['table'])."' AND id_ext = '".$this->app->DB->real_escape_string($id_ext)."' AND api = '".$api_id."' LIMIT 1");
          }
          if(empty($ergebnis))
          {
            $this->XMLResponse(8);
            $this->app->ExitXentral();
          }
          $this->XMLResponse(1,'<id_int>'.$ergebnis[0]['id_int'].'</id_int><id_ext>'.$ergebnis[0]['id_ext'].'</id_ext><table>'.$ergebnis[0]['tabelle'].'</table>');
          $this->app->ExitXentral();
        }
        $this->XMLResponse(4);
        $this->app->ExitXentral();
      }
      $out = '<list>';
      foreach($list as $k => $v)
      {
        $table = isset($v['table'])?$v['table']:(isset($v['TABLE'])?$v['TABLE']:'');
        $id_int = isset($v['id_int'])?$v['id_int']:(isset($v['ID_INT'])?$v['ID_INT']:'');
        $id_ext = isset($v['id_ext'])?$v['id_ext']:(isset($v['ID_EXT'])?$v['ID_EXT']:'');
        if($table && (!empty($id_int) || !empty($id_ext)))
        {
          if(!empty($id_int))
          {
            $ergebnis = $this->app->DB->SelectArr("SELECT * FROM api_mapping WHERE table = '".$this->app->DB->real_escape_string($xml['table'])."' AND id_int = '".$this->app->DB->real_escape_string($id_int)."' AND api = '".$api_id."' LIMIT 1");
          }else{
            $ergebnis = $this->app->DB->SelectArr("SELECT * FROM api_mapping WHERE table = '".$this->app->DB->real_escape_string($xml['table'])."' AND id_ext = '".$this->app->DB->real_escape_string($id_ext)."' AND api = '".$api_id."' LIMIT 1");
          }
          if(!empty($ergebnis))
          {
            $out .= '<id_int>'.$ergebnis[0]['id_int'].'</id_int><id_ext>'.$ergebnis[0]['id_ext'].'</id_ext><table>'.$ergebnis[0]['tabelle'].'</table>';
          }
        }
      }
      $out = '</list>';
      $this->XMLResponse(1,$out);
      $this->app->ExitXentral();
    }
  }

  function ApiMappingSet()
  {
    $xml = $this->XMLPost();
    $api_id = isset($this->api_id)?$this->api_id:0;
    if(is_array($xml))
    {
      $list = false;
      if(isset($xml['list']))
      {
        $list = $xml['list'];
      }elseif($xml['LIST'])
      {
        $list = $xml['LIST'];
      }
      if(!$list)
      {
        $table = isset($xml['table'])?$xml['table']:(isset($xml['TABLE'])?$xml['TABLE']:'');
        $id_int = isset($xml['id_int'])?$xml['id_int']:(isset($xml['ID_INT'])?$xml['ID_INT']:'');
        $id_ext = isset($xml['id_ext'])?$xml['id_ext']:(isset($xml['ID_EXT'])?$xml['ID_EXT']:'');
        if($table && (!empty($id_int) && !empty($id_ext)))
        {
          $ergebnis = $this->app->DB->SelectArr("SELECT * FROM api_mapping WHERE tabelle = '".$this->app->DB->real_escape_string($table)."' AND id_int = '".$this->app->DB->real_escape_string($id_int)."' AND api = '".$api_id."' LIMIT 1");
          if(empty($ergebnis))
          {
            $this->app->DB->Insert("INSERT INTO api_mapping (tabelle, id_int, id_ext, api) VALUES ('".$this->app->DB->real_escape_string($table)."','".$this->app->DB->real_escape_string($id_int)."','".$this->app->DB->real_escape_string($id_ext)."','".$api_id."')");
            if($insertid = $this->app->DB->GetInsertID())
            {
              $this->XMLResponse(1,'<id_int>'.$id_int.'</id_int><id_ext>'.$id_ext.'</id_ext><table>'.$table.'</table><id>'.$insertid.'</id><sql>'."INSERT INTO api_mapping (table, id_int, id_ext, api) VALUES ('".$this->app->DB->real_escape_string($table)."','".$this->app->DB->real_escape_string($id_int)."','".$this->app->DB->real_escape_string($id_ext)."','".$api_id."')".'</sql>');
              $this->app->ExitXentral();
            }
            $this->XMLResponse(0);
            $this->app->ExitXentral();
          }
          $this->app->DB->Update("UPDATE api_mapping SET id_ext = '".$this->app->DB->real_escape_string($id_ext)."' WHERE id = '".$ergebnis[0]['id']."' LIMIT 1");
          $this->XMLResponse(1,'<id_int>'.$id_int.'</id_int><id_ext>'.$id_ext.'</id_ext><table>'.$table.'</table>');
          $this->app->ExitXentral();
        }
        $this->XMLResponse(4);
        $this->app->ExitXentral();
      }
      $out = '<list>';
      foreach($list as $k => $v)
      {
        $table = isset($v['table'])?$v['table']:(isset($v['TABLE'])?$v['TABLE']:'');
        $id_int = isset($v['id_int'])?$v['id_int']:(isset($v['ID_INT'])?$v['ID_INT']:'');
        $id_ext = isset($v['id_ext'])?$v['id_ext']:(isset($v['ID_EXT'])?$v['ID_EXT']:'');
        if($table && (!empty($id_int) && !empty($id_ext)))
        {
          if(!empty($id_int))
          {
            $ergebnis = $this->app->DB->SelectArr("SELECT * FROM api_mapping WHERE tabelle = '".$this->app->DB->real_escape_string($xml['table'])."' AND id_int = '".$this->app->DB->real_escape_string($id_int)."' AND api = '".$api_id."' LIMIT 1");
          }
          if(!empty($ergebnis))
          {
            $this->app->DB->Update("UPDATE api_mapping SET id_ext = '".$this->app->DB->real_escape_string($id_ext)."' WHERE id = '".$ergebnis[0]['id']."' LIMIT 1");
            $out .= '<id_int>'.$id_int.'</id_int><id_ext>'.$id_ext.'</id_ext><table>'.$table.'</table>';
          }else{
            $this->app->DB->Insert("INSERT INTO api_mapping (table, id_int, id_ext, api) VALUES ('".$this->app->DB->real_escape_string($table)."','".$this->app->DB->real_escape_string($id_int)."','".$this->app->DB->real_escape_string($id_ext)."','".$api_id."')");
            $out .= '<id_int>'.$id_int.'</id_int><id_ext>'.$id_ext.'</id_ext><table>'.$table.'</table>';
          }
        }
      }
      $out = '</list>';
      $this->XMLResponse(1,$out);
      $this->app->ExitXentral();
    }
  }

  // allgemeine funktionen
  function CheckHash()
  {
    $hash = $this->app->Secure->GetGET("hash");
    $api_id = (int)$this->app->Secure->GetGET("api_id");

    try{
        $apiAccount = $this->apiAccountService->getApiAccountById($api_id);
        $this->api_id = $apiAccount->getId();
    }catch (ApiAccountNotFoundException $e){
        $this->XMLResponse(9);
        $this->app->ExitXentral();
    }

    if($hash !== $this->generateHash($apiAccount->getRemoteDomain(),$apiAccount->getInitKey())){
        if($api_id === 0){
            $this->XMLResponse(9);
        }else{
            $this->XMLResponse(2);
        }
        $this->app->ExitXentral();
    }

    $guard = new PermissionGuard($this->app->Container->get('Database'), (int)$this->api_id);
    $guard->checkStandardApiAction($this->app->Secure->GetGET("action"));
  }

  function generateHash($domain,$key)
  {
    $date = gmdate('dmY');
    $hash = "";

    for($i = 0; $i <= 200; $i++)
      $hash = sha1($hash . $key . $domain . $date);

    return $hash;
  }

  function getBelege()
  {
    return array(
      'auftrag','rechnung','gutschrift','angebot','lieferschein','bestellung','produktion','retoure','verbindlichkeit'
    );
  }

  function getTabellen()
  {
    return array('adresse','adresse_rolle','gruppen','adresse_kontakte','artikel','verkaufspreise','einkaufspreise','stueckliste');
  }

    /**
     * @param string $typ
     * @param int    $id
     *
     * @return null|string
     */
  public function GetPDF($typ, $id)
  {
    $id = (int)$id;
    if(!$id) {
      return null;
    }
    if(!in_array($typ, $this->getBelege())) {
      return null;
    }
    $elemente = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM `%s` WHERE `id` = %d LIMIT 1',
        $typ, $id
      )
    );
    if(!$elemente) {
      return null;
    }

    if(!defined('FPDF_FONTPATH')){
      define('FPDF_FONTPATH','lib/pdf/font/');
    }

    if(!class_exists('FPDFWAWISION')){
      if(file_exists(dirname(dirname(__DIR__)).'/conf/user_defined.php')){
        include_once dirname(dirname(__DIR__)).'/conf/user_defined.php';
      }
      /*if(!defined('USEFPDF3'))
      {
        define('USEFPDF3',true);
      }*/
      if(defined('USEFPDF3') && USEFPDF3 && file_exists(dirname(__DIR__) . '/lib/pdf/fpdf_3.php')){
        require_once(dirname(__DIR__) . '/lib/pdf/fpdf_3.php');
      }elseif(defined('USEFPDF2') && USEFPDF2){
        if(file_exists(dirname(__DIR__) . '/lib/pdf/fpdf_2.php')){
          require_once(dirname(__DIR__) . '/lib/pdf/fpdf_2.php');
        }else{
          require_once(dirname(__DIR__) . '/lib/pdf/fpdf.php');
        }
      }else{
        require_once(dirname(__DIR__) . '/lib/pdf/fpdf.php');
      }
    }
    require_once(dirname(__DIR__).'/lib/pdf/fpdf_final.php');

    require_once(dirname(__DIR__)."/lib/dokumente/class.superfpdf.php");
    require_once(dirname(__DIR__)."/lib/dokumente/class.etikett.php");
    if(file_exists(dirname(__DIR__)."/lib/dokumente/class.briefpapier_custom.php"))
    {
      require_once(dirname(__DIR__)."/lib/dokumente/class.briefpapier_custom.php");
    }else{
      require_once(dirname(__DIR__)."/lib/dokumente/class.briefpapier.php");
    }

    if(file_exists(dirname(__DIR__)."/lib/class.image.php"))
    {
      require_once(dirname(__DIR__)."/lib/class.image.php");
    }

    require_once(dirname(__DIR__)."/lib/dokumente/class.dokumentenvorlage.php");
    if(file_exists("lib/dokumente/class.layoutvorlagen.php"))require_once("lib/dokumente/class.layoutvorlagen.php");
    require_once(dirname(__DIR__)."/lib/dokumente/class.sepamandat.php");
    require_once(dirname(__DIR__)."/lib/dokumente/class.dokuarbeitszeit.php");
    require_once(dirname(__DIR__)."/../phpwf/plugins/class.string.php");
    if(!isset($this->app->String))$this->app->String = new WawiString();
    $classname = ucfirst($typ).'PDF';
    $classnamecustom = $classname.'Custom';
    if(!class_exists($classname) && !class_exists($classnamecustom))
    {
      if(file_exists(dirname(__DIR__).'/lib/dokumente/class.'.$typ.'_custom.php'))
      {
        include_once(dirname(__DIR__).'/lib/dokumente/class.'.$typ.'_custom.php');
      }elseif(file_exists(dirname(__DIR__).'/lib/dokumente/class.'.$typ.'.php'))
      {
        include_once(dirname(__DIR__).'/lib/dokumente/class.'.$typ.'.php');
      }
    }
    if(class_exists($classnamecustom))
    {
      $Brief = new $classnamecustom($this->app,$elemente['projekt']);
    }
    elseif(class_exists($classname)){
      $Brief = new $classname($this->app,$elemente['projekt']);
    }
    else {
      return null;
    }
    $funktionsname = 'Get'.ucfirst($typ);
    if(method_exists($Brief, $funktionsname)) {
      $Brief->$funktionsname($elemente['id']);
      $Brief->renderDocument();
      return $Brief->output('','S');
    }
    return null;
  }

    /**
     * @param string     $file
     * @param int        $receiptdocumentId
     * @param bool       $append
     * @param null|array $options
     *
     * @return bool
     */
  public function createReceiptdocumentQsCsv($file, $receiptdocumentId, $append = false, $options = null)
  {
    if(empty($file) || empty($receiptdocumentId)) {
      return false;
    }
    $file_ex = @is_file($file);
    if(!$file_ex) {
      $append = false;
    }
    $handle = @fopen($file, $append?'a':'w');
    if(empty($handle)) {
      return false;
    }
    $noWrap = !empty($options['nowrap']);
    foreach($this->generateQsCsvArr($receiptdocumentId) as $row) {
      if($noWrap) {
        foreach($row as $key => $col) {
          if(!is_string($col)) {
            continue;
          }
          $row[$key] = str_replace(["\r\n","\r","\n"],' ', $col);
        }
      }
      @fputcsv($handle, $row, ';','"');
    }
    @fclose($handle);

    return @file_exists($file);
  }

    /**
     * @param int $receiptdocumentId
     *
     * @return array
     */
  public function generateQsCsvArr($receiptdocumentId)
  {
    $elemente_positionen = $this->app->DB->Query(
      sprintf(
        "SELECT r.document_number, b.belegnr AS bestellung, m2.id_ext AS receiptdocument_id_ext,
       m.id_ext AS receiptdocument_position_id_ext,art.ean, art.gewicht, art.herstellernummer, art.altersfreigabe,
       art.lagerartikel,r.status, r.status_qs, t.amount, t.amount_good, t.amount_bad, bp.menge, bp.geliefert
          FROM receiptdocument_position t 
          INNER JOIN receiptdocument AS r ON t.receiptdocument_id = r.id 
          INNER JOIN `paketannahme` AS parc ON r.parcel_receipt_id = parc.id
          INNER JOIN `paketdistribution` AS pd ON parc.id = pd.paketannahme    
          INNER JOIN bestellung_position AS bp ON pd.bestellung_position = bp.id
          INNER JOIN bestellung AS b ON bp.bestellung = b.id
          LEFT JOIN artikel art ON t.article_id = art.id 
          LEFT JOIN api_mapping m ON m.tabelle = 'receiptdocument_position' AND m.id_int = t.id
          LEFT JOIN api_mapping m2 ON m2.tabelle = 'receiptdocument' AND m2.id_int = r.id 
          WHERE t.receiptdocument_id = %d ",
        $receiptdocumentId
      )
    );

    $rows = [
      ['r.document_number', 'bestellung', 'receiptdocument_id_ext',
        'receiptdocument_position_id_ext','ean', 'gewicht', 'herstellernummer', 'altersfreigabe',
        'lagerartikel','status', 'status_qs', 'amount', 'amount_good', 'amount_bad', 'bp.menge', 'geliefert']
    ];
    if(!empty($elemente_positionen)) {
      while($row = $this->app->DB->fetch_row($elemente_positionen)) {
        $rows[] = $row;
      }
      $this->app->DB->free($elemente_positionen);
    }

    return $rows;
  }

  function ApiDataToXML($typ, $id, &$xml = null, $parameter = null)
  {
    $n = null;
    $typ = strtolower($typ);
    if($xml === null) {
      $xml = $this->CreateXmlObj();
    }

    if(isset($parameter['briefpapier']) && $parameter['briefpapier'] == 1) {
      $briefpapier = '';
      $briefpapier2 = '';
      $logo = '';
      $briefpapier_typ = $this->app->erp->Firmendaten('hintergrund');

      if($briefpapier_typ==='logo')
      {
        $logo = $this->app->erp->Firmendaten('logo');
      }
      else if($briefpapier_typ==='briefpapier')
      {
        $briefpapier = $this->app->erp->Firmendaten('briefpapier');

        $briefpapier2vorhanden = $this->app->erp->Firmendaten('briefpapier2vorhanden');
        if($briefpapier2vorhanden > 0)
        {
          $briefpapier2 = $this->app->erp->Firmendaten('briefpapier2');
        }
      }
      if($briefpapier && !isset($xml->xml->briefpapier)){
        $this->AddToXMLObj($xml, 'briefpapier','', $briefpapier, $n, $erg);
      }
      if($briefpapier2 && !isset($xml->xml->briefpapier2)){
        $this->AddToXMLObj($xml, 'briefpapier2','', $briefpapier2, $n, $erg);
      }
      if($logo && !isset($xml->xml->logo)){
        $this->AddToXMLObj($xml, 'logo','', $logo, $n, $erg);
      }
      if($briefpapier_typ && !isset($xml->xml->briefpapier_typ)){
        $this->AddToXMLObj($xml, 'briefpapier_typ','', $briefpapier_typ, $n, $erg);
      }
    }
    $typen = $this->getTabellen();
    $belege = $this->getBelege();
    if(in_array($typ, $typen)) {
      $elemente = $this->app->DB->SelectArr(
        "SELECT t.*, m.id_ext ".($typ==='bestellung'?",adr.kundennummerlieferant":'')." 
        FROM `$typ` AS `t` ".($typ=='bestellung'?" 
        LEFT JOIN `adresse` AS `adr` ON t.adresse = adr.id ":"")." 
        LEFT JOIN `api_mapping` AS `m` ON m.api = '".$this->api_id."' AND m.tabelle = '$typ' AND m.id_int = t.id 
        WHERE t.id = '".($id)."' 
        LIMIT 1"
      );
      if($elemente) {
        $this->AddToXMLObj($xml, $typ,$typ.'_list', $elemente, $n, $erg);
      }
    }
    elseif(in_array($typ, $belege)) {
      $typposition = $typ.'_position';
      $elemente = $this->app->DB->SelectArr("SELECT t.*, m.id_ext FROM $typ t LEFT JOIN api_mapping m ON m.api = '".$this->api_id."' AND m.tabelle = '$typ' AND m.id_int = t.id WHERE t.id = '".($id)."' LIMIT 1");

      if($elemente) {
        $elemente[0]['anzahluebertragungen'] = 1+(int)$this->app->DB->Select("SELECT anzahl_uebertragen FROM api_request WHERE uebertragung_account = '".$this->uebertragung_account."' and typ = '$typ' AND parameter1 = '$id' LIMIT 1");
        if(isset($parameter['pdf']) && $parameter['pdf'] == 1) {
          $file = $this->GetPDF($typ, $id);
          if(!empty($file)) {
            $elemente[0]['pdf'] = base64_encode($file);
          }
        }
        if(isset($parameter['gln_freifeld'])) {
          $elemente[0]['gln_empfaenger'] = $this->app->DB->Select("SELECT freifeld".(int)$parameter['gln_freifeld']." FROM adresse WHERE id = '".$elemente[0]['adresse']."' LIMIT 1");
        }
        if($typ === 'lieferschein' || $typ === 'retoure') {
          $auftragsdaten = $elemente[0]['auftragid'] <= 0 ? null : $this->app->DB->SelectArr("SELECT * FROM auftrag WHERE id = '".$elemente[0]['auftragid']."' LIMIT 1");
          if($auftragsdaten) {
            $elemente[0]['tatsaechlicheslieferdatum'] = $auftragsdaten[0]['tatsaechlicheslieferdatum'];
            $elemente[0]['lieferdatum'] = $auftragsdaten[0]['lieferdatum'];
            $elemente[0]['lieferdatumkw'] = $auftragsdaten[0]['lieferdatumkw'];
            $elemente[0]['rechnung_name'] = $auftragsdaten[0]['name'];
            $elemente[0]['auftrag_name'] = $auftragsdaten[0]['name'];
            $elemente[0]['rechnung_anrede'] = $auftragsdaten[0]['anrede'];
            $elemente[0]['auftrag_anrede'] = $auftragsdaten[0]['anrede'];
            $elemente[0]['rechnung_strasse'] = $auftragsdaten[0]['strasse'];
            $elemente[0]['auftrag_strasse'] = $auftragsdaten[0]['strasse'];
            $elemente[0]['rechnung_adresszusatz'] = $auftragsdaten[0]['adresszusatz'];
            $elemente[0]['auftrag_adresszusatz'] = $auftragsdaten[0]['adresszusatz'];
            $elemente[0]['rechnung_ansprechpartner'] = $auftragsdaten[0]['ansprechpartner'];
            $elemente[0]['auftrag_ansprechpartner'] = $auftragsdaten[0]['ansprechpartner'];
            $elemente[0]['rechnung_abteilung'] = $auftragsdaten[0]['abteilung'];
            $elemente[0]['auftrag_abteilung'] = $auftragsdaten[0]['abteilung'];
            $elemente[0]['rechnung_unterabteilung'] = $auftragsdaten[0]['unterabteilung'];
            $elemente[0]['auftrag_unterabteilung'] = $auftragsdaten[0]['unterabteilung'];
            $elemente[0]['rechnung_bundesstaat'] = $auftragsdaten[0]['bundesstaat'];
            $elemente[0]['auftrag_bundesstaat'] = $auftragsdaten[0]['bundesstaat'];
            $elemente[0]['rechnung_plz'] = $auftragsdaten[0]['plz'];
            $elemente[0]['auftrag_plz'] = $auftragsdaten[0]['plz'];
            $elemente[0]['rechnung_ort'] = $auftragsdaten[0]['ort'];
            $elemente[0]['auftrag_ort'] = $auftragsdaten[0]['ort'];
            $elemente[0]['rechnung_land'] = $auftragsdaten[0]['land'];
            $elemente[0]['auftrag_land'] = $auftragsdaten[0]['land'];
            $elemente[0]['rechnung_gln'] = $auftragsdaten[0]['gln'];
            $elemente[0]['auftrag_gln'] = $auftragsdaten[0]['gln'];
            $elemente[0]['internet'] = $auftragsdaten[0]['internet'];
            $elemente[0]['shopextid'] = $auftragsdaten[0]['shopextid'];
            $elemente[0]['auftragextid'] = $this->GetExtIDFromIntID('auftrag',$elemente[0]['auftragid']);
          }

          // wenn es eine eigene Rechnung bibt dann fuer die rechnunsdaten die infos da nehmen
          $rechnungsdaten = $elemente[0]['auftragid'] <= 0 ? null : $this->app->DB->SelectArr("SELECT * FROM rechnung WHERE auftragid = '".$elemente[0]['auftragid']."' LIMIT 1");
          if($rechnungsdaten)
          {
            $elemente[0]['rechnung_name'] = $rechnungsdaten[0]['name'];
            $elemente[0]['rechnung_anrede'] = $rechnungsdaten[0]['anrede'];
            $elemente[0]['rechnung_strasse'] = $rechnungsdaten[0]['strasse'];
            $elemente[0]['rechnung_adresszusatz'] = $rechnungsdaten[0]['adresszusatz'];
            $elemente[0]['rechnung_ansprechpartner'] = $rechnungsdaten[0]['ansprechpartner'];
            $elemente[0]['rechnung_abteilung'] = $rechnungsdaten[0]['abteilung'];
            $elemente[0]['rechnung_unterabteilung'] = $rechnungsdaten[0]['unterabteilung'];
            $elemente[0]['rechnung_plz'] = $rechnungsdaten[0]['plz'];
            $elemente[0]['rechnung_ort'] = $rechnungsdaten[0]['ort'];
            $elemente[0]['rechnung_bundesstaat'] = $rechnungsdaten[0]['bundesstaat'];
            $elemente[0]['rechnung_land'] = $rechnungsdaten[0]['land'];
            $elemente[0]['rechnung_gln'] = $rechnungsdaten[0]['gln'];
          }


          if($typ === 'lieferschein'){
            $tracking = (string)$this->app->DB->Select("SELECT tracking FROM versand WHERE lieferschein = '" . $elemente[0]['id'] . "' AND tracking <> '' AND lieferschein <> 0 ORDER by id DESC LIMIT 1");
            if($tracking !== '') {
              $elemente[0]['tracking'] = $tracking;
            }
          }
        }
        if($typ === 'rechnung') {
          $auftragsdaten = $this->app->DB->SelectArr("SELECT * FROM auftrag WHERE id = '".$elemente[0]['auftragid']."' LIMIT 1");
          if($auftragsdaten)
          {
            $elemente[0]['tatsaechlicheslieferdatum'] = $auftragsdaten[0]['tatsaechlicheslieferdatum'];
            $elemente[0]['lieferdatum'] = $auftragsdaten[0]['lieferdatum'];
            $elemente[0]['lieferdatumkw'] = $auftragsdaten[0]['lieferdatumkw'];
            $elemente[0]['liefername'] = $auftragsdaten[0]['abweichendelieferadresse'] == 1? $auftragsdaten[0]['liefername']:$auftragsdaten[0]['name'];
            $elemente[0]['lieferstrasse'] = $auftragsdaten[0]['abweichendelieferadresse'] == 1? $auftragsdaten[0]['liefername']:$auftragsdaten[0]['strasse'];
            $elemente[0]['lieferadresszusatz'] = $auftragsdaten[0]['abweichendelieferadresse'] == 1? $auftragsdaten[0]['lieferadresszusatz']:$auftragsdaten[0]['adresszusatz'];
            $elemente[0]['lieferansprechpartner'] = $auftragsdaten[0]['abweichendelieferadresse'] == 1? $auftragsdaten[0]['lieferansprechpartner']:$auftragsdaten[0]['ansprechpartner'];
            $elemente[0]['lieferabteilung'] = $auftragsdaten[0]['abweichendelieferadresse'] == 1? $auftragsdaten[0]['lieferabteilung']:$auftragsdaten[0]['abteilung'];
            $elemente[0]['lieferunterabteilung'] = $auftragsdaten[0]['abweichendelieferadresse'] == 1? $auftragsdaten[0]['lieferunterabteilung']:$auftragsdaten[0]['unterabteilung'];
            $elemente[0]['lieferplz'] = $auftragsdaten[0]['abweichendelieferadresse'] == 1? $auftragsdaten[0]['lieferplz']:$auftragsdaten[0]['plz'];
            $elemente[0]['lieferort'] = $auftragsdaten[0]['abweichendelieferadresse'] == 1? $auftragsdaten[0]['lieferort']:$auftragsdaten[0]['ort'];
            $elemente[0]['lieferland'] = $auftragsdaten[0]['abweichendelieferadresse'] == 1? $auftragsdaten[0]['lieferland']:$auftragsdaten[0]['land'];
            $elemente[0]['lieferbundesstaat'] = $auftragsdaten[0]['abweichendelieferadresse'] == 1? $auftragsdaten[0]['lieferbundesstaat']:$auftragsdaten[0]['bundesstaat'];
          }
          $tracking = (string)$this->app->DB->Select("SELECT tracking FROM versand WHERE rechnung = '".$elemente[0]['id']."' AND tracking <> '' AND rechnung <> 0 ORDER by id DESC LIMIT 1");
          if($tracking !== '')$elemente[0]['tracking'] = $tracking;

            if($elemente[0]['lieferschein'] > 0){
              $lieferscheindaten = $this->app->DB->SelectArr("SELECT * FROM lieferschein WHERE id = '".$elemente[0]['lieferschein']."' LIMIT 1");
              if($lieferscheindaten)
              {
                $elemente[0]['liefername'] = $lieferscheindaten[0]['name'];
                $elemente[0]['lieferstrasse'] = $lieferscheindaten[0]['strasse'];
                $elemente[0]['lieferadresszusatz'] = $lieferscheindaten[0]['adresszusatz'];
                $elemente[0]['lieferansprechpartner'] = $lieferscheindaten[0]['ansprechpartner'];
                $elemente[0]['lieferabteilung'] = $lieferscheindaten[0]['abteilung'];
                $elemente[0]['lieferunterabteilung'] = $lieferscheindaten[0]['unterabteilung'];
                $elemente[0]['lieferplz'] = $lieferscheindaten[0]['plz'];
                $elemente[0]['lieferort'] = $lieferscheindaten[0]['ort'];
                $elemente[0]['lieferland'] = $lieferscheindaten[0]['land'];
                $elemente[0]['lieferbundesstaat'] = $lieferscheindaten[0]['bundesstaat'];
                $elemente[0]['lieferdatum'] = $lieferscheindaten[0]['datum'];
                $elemente[0]['lieferschein'] = $lieferscheindaten[0]['belegnr'];
                $elemente[0]['gln_lieferadresse'] = $lieferscheindaten[0]['gln'];
              }
            }
        }
        $lieferid = 0;
        if($typ !== 'auftrag')
        {
          $lieferid = $this->app->DB->Select("SELECT lieferid FROM auftrag WHERE id = '".$elemente[0]['auftragid']."' LIMIT 1");
        }else{
          $lieferid = $auftragsdaten[0]['lieferid'];
        }
        if($lieferid && $elemente[0]['gln_lieferadresse']=="")
        {
          $gln_lieferadresse = $this->app->DB->Select("SELECT gln FROM lieferadressen WHERE id = '$lieferid' LIMIT 1");
          if($gln_lieferadresse)$elemente[0]['gln_lieferadresse'] = $gln_lieferadresse;
        }

        $rabatt = 0;
        $gebuehr = 0;
        $summe = 0;
        $summeR = 0;
        $summeV = 0;
        $totalR = 0;
        $totalV = 0;
        $elemente_positionen = $typ === 'verbindlichkeit'
          ?null:
          $this->app->DB->SelectArr(
            "SELECT t.*, m.id_ext, art.ean, art.gewicht,art.herstellernummer, art.altersfreigabe, art.lagerartikel "
            .($typ === 'bestellung'?(" ,art.nummer "):'')." 
            FROM `$typposition` AS `t` 
            LEFT JOIN `artikel` AS `art` ON t.artikel = art.id 
            LEFT JOIN `api_mapping` AS `m` ON m.tabelle = '$typposition' AND m.id_int = t.id 
            WHERE t.$typ = '".($elemente[0]['id'])."' "
          );
        if($elemente_positionen)
        {
          if(isset($elemente_positionen[0]['preis']))
          {
            foreach($elemente_positionen as $key => $value)
            {
              if($typ === 'bestellung' && $value['bestellnummer'] == '')$elemente_positionen[$key]['bestellnummer'] = $value['nummer'];
              if($value['umsatzsteuer'] !== "ermaessigt") $value['umsatzsteuer'] = "normal";
              $netto_gesamt = $value['menge']*($value['preis']-($value['preis']/100*$value['rabatt']));
              $summe = $summe + $netto_gesamt;

              if(is_null($value['steuersatz']) || $value['steuersatz'] < 0) {
                if ($value['umsatzsteuer'] == "" || $value['umsatzsteuer'] === "normal") {
                  $summeV = $summeV + (($netto_gesamt / 100) * $this->app->erp->GetSteuersatzNormal(false, $id, "rechnung"));
                  $totalV = $totalV + $netto_gesamt;
                } elseif ($value['umsatzsteuer'] === 'befreit') {

                } else {
                  $summeR = $summeR + (($netto_gesamt / 100) * $this->app->erp->GetSteuersatzErmaessigt(false, $id, "rechnung"));
                  $totalR = $totalR + $netto_gesamt;
                }
              }

              if($value['preis'] < -0.0001)
              {
                $rabatt -= $netto_gesamt;
              }else{
                $gebuehr_artikel = $this->app->DB->Select("SELECT gebuehr FROM artikel WHERE id = '".$value['artikel']."' LIMIT 1");
                if($gebuehr_artikel){
                  $gebuehr += $netto_gesamt;
                }
              }
            }
            if($rabatt)
            {
              $elemente[0]['rabatt'] = round($rabatt,2);
            }else $elemente[0]['rabatt'] = 0;
            if($gebuehr)
            {
              $elemente[0]['gebuehr'] = round($gebuehr,2);
            }else $elemente[0]['gebuehr'] = 0;
            if($this->app->erp->RechnungMitUmsatzeuer($id))
            {
              $elemente[0]['ust'] = round($summeR+$summeV,2);
              $elemente[0]['ust_ermaessigt'] = round($summeR,2);
              $elemente[0]['ust_normal'] = round($summeV,2);

            }else {
              $elemente[0]['ust'] = 0;
            }
          }
        }
        if(isset($parameter['anhang_base64']) && $parameter['anhang_base64'] == 1)
        {
          $anhaenge = $this->app->DB->SelectArr("SELECT DISTINCT s.datei FROM 
            datei d INNER JOIN datei_stichwoerter s ON d.id=s.datei INNER JOIN datei_version v ON v.datei=d.id WHERE s.objekt LIKE '$typ' AND s.parameter = '$id'");
          if($anhaenge)
          {
            $elemente[0]['anhaenge'] = array();
            foreach($anhaenge as $v)
            {
              $datei = $this->app->erp->GetDatei($v['datei']);
              if($datei != '')
              {
                $elemente[0]['anhaenge']['anhang'][] = array('datei'=>base64_encode($datei), 'name'=>$this->app->erp->GetDateiName($v['datei']));
              }
              unset($datei);
            }
          }
        }elseif(isset($parameter['anhaenge']))
        {
          if(is_array($parameter['anhaenge']) && count($parameter['anhaenge']) > 0)
          {
            foreach($parameter['anhaenge'] as $v)
            {
              $elemente[0]['anhaenge']['anhang'][] = $v;
            }
          }
        }
        $this->AddToXMLObj($xml, $typ,$typ.'_list', $elemente, $n, $erg);
        if($elemente_positionen)
        {
          $this->AddToXMLObj($xml, $typposition,$typposition.'_list', $elemente_positionen, $erg, $erg2);
        }
      }
    }
    elseif($typ === 'versand') {
      $elemente = $this->app->DB->SelectArr("SELECT t.*, m.id_ext FROM $typ t LEFT JOIN api_mapping m ON m.api = '".$this->api_id."' AND m.tabelle = '$typ' AND m.id_int = t.id WHERE t.id = '".($id)."' LIMIT 1");
      if($elemente) {
        $auftragid = $this->app->DB->Select("SELECT auftragid FROM lieferschein WHERE id = '".$elemente[0]['lieferschein']."' LIMIT 1");
        if($auftragid) {
          $sprache = $this->app->DB->Select("SELECT sprache FROM auftrag WHERE id = '$auftragid'");
          if($sprache != ''){
            $elemente[0]['tracking_sprache'] = $sprache;
          }
          $auftragextid = $this->app->DB->Select("SELECT id_ext FROM api_mapping m WHERE m.tabelle = 'auftrag' AND m.id_int = '".$auftragid."' AND api = '".$this->api_id."'");
          if($auftragextid){
            $elemente[0]['auftragextid'] = $auftragextid;
          }
        }

        $this->AddToXMLObj($xml, 'lieferschein','lieferschein_list', $elemente, $n, $erg);
      }
    }
    elseif($typ === 'receiptdocumentqs') {
      $typ = 'receiptdocument';
      $typposition = 'receiptdocument_position';
      $elemente = $this->app->DB->SelectArr(
        "SELECT t.*, m.id_ext 
        FROM receiptdocument t 
            INNER JOIN `paketannahme` AS parc ON t.parcel_receipt_id = parc.id
            LEFT JOIN api_mapping m ON m.api = '".$this->api_id."' AND m.tabelle = 'receiptdocument' AND m.id_int = t.id 
            WHERE t.id = '".($id)."' LIMIT 1");

      if($elemente) {
        $this->AddToXMLObj($xml, $typ,$typ.'_list', $elemente, $n, $erg);
        $elemente_positionen = $this->app->DB->SelectArr(
          "SELECT t.*, m.id_ext, art.ean, art.gewicht,art.herstellernummer, art.altersfreigabe, 
          art.lagerartikel, b.belegnr
          FROM receiptdocument_position t 
          INNER JOIN receiptdocument AS r ON t.receiptdocument_id = r.id 
          INNER JOIN `paketannahme` AS parc ON r.parcel_receipt_id = parc.id
          INNER JOIN `paketdistribution` AS pd ON parc.id = pd.paketannahme    
          INNER JOIN bestellung_position AS bp ON pd.bestellung_position = bp.id
          INNER JOIN bestellung AS b ON bp.bestellung = b.id
          LEFT JOIN artikel art ON t.article_id = art.id 
          LEFT JOIN api_mapping m ON m.tabelle = 'receiptdocument_position' AND m.id_int = t.id 
          WHERE t.receiptdocument_id = '".($elemente[0]['id'])."' ");
        if($elemente_positionen) {
          $this->AddToXMLObj($xml, $typposition,$typposition.'_list', $elemente_positionen, $erg, $erg2);
        }
      }
    }
  }

  function ParseXML($dateiname, $api = 0, $uebertragung = 0, $isfile = true)
  {
    $this->lagerzahlen = null;
    $this->bestBeforeBatchArticleStock = [];
    $api = (int)$api;
    $uebertragung = (int)$uebertragung;
    if(!$api && !$uebertragung){
      return false;
    }
    if(!$api){
      $api = $this->app->DB->Select("SELECT api FROM uebertragungen_account WHERE id = '$uebertragung' LIMIT 1");
    }
    //if(!$api || !$this->app->DB->Select("SELECT id FROM uebertragungen_account WHERE id = '$uebertragung' AND aktiv = 1 AND typ = 'lokal' LIMIT 1"))return false;
    //if(!$api)return false;
    $this->api_id = $api;
    $xml = $this->LoadFromXML($dateiname, $isfile);
    if(!$xml)
    {
      if($uebertragung)
      {
        $obj = $this->app->erp->LoadModul('uebertragungen');
        if($obj)
        {
          $obj->datei_id = $this->datei_id;
          if(!is_file($dateiname))
          {
            $obj->AddUbertragungMonitorLog($uebertragung, $this->datei_id, 0, 'download_error','Datei konnte nicht heruntergeladen werden');
          }elseif('' === (string)file_get_contents($dateiname))
          {
            $obj->AddUbertragungMonitorLog($uebertragung, $this->datei_id, 0, 'file_empty','Datei ist leer');
          }else{
            $obj->AddUbertragungMonitorLog($uebertragung, $this->datei_id, 0, 'xml_parseerror','XML konnte nicht geparsed werden');
          }
        }
      }

      $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragung','datei','".$this->app->DB->real_escape_string($dateiname)."','xml_fehler','XML konnte nicht gelesen werden')");
      return false;
    }

    if(!isset($xml->xml) && get_class($xml) !== 'stdClass') {
      return false;
    }

      if((get_class($xml) === 'stdClass' && count($xml) >  0) || (isset($xml->xml) && count($xml->xml) > 0)) {
        foreach($xml as $key0 => $value0) {
          if($key0 === 'status') {
            $status = &$value0;
            break;
          }
        }
        $ok = true;
        if(isset($status)){
          if(isset($status->messageCode) && $status->messageCode != 1) {
            $ok = false;
          }
        }

        foreach($xml as $key0 => $value0) {
          if($key0 === 'xml'){
            $xml_inhalt = &$value0;
            break;
          }
        }

        $typen = $this->getTabellen();
        foreach($typen as $v){
          $typen_list[] = $v.'_list';
        }
        $belege = $this->getBelege();
        foreach($belege as $v){
          $belege_list[] = $v.'_list';
        }
        if(isset($xml_inhalt)){
          foreach($xml_inhalt as $key => $value) {
            if(in_array($key,$belege) || in_array($key,$typen)) {
              $this->ParseTeilXML($key, $value, $uebertragung);
            }
            if(in_array($key,$belege_list) || in_array($key,$typen_list)){
              foreach($value as $key2 => $value2) {
                if(!in_array($key2,$belege) && !in_array($key2,$typen)) {
                  continue;
                }
                $this->ParseTeilXML($key2, $value2, $uebertragung);
                if(empty($this->app->User) || !method_exists($this->app->User,'GetID') || !$this->app->User->GetID()) {
                  $this->app->DB->Update(
                    "UPDATE `prozessstarter` 
                    SET `letzteausfuerhung`=DATE_ADD(NOW(), INTERVAL 2 MINUTE), `mutex` = 1,`mutexcounter`=0 
                    WHERE `parameter` = 'api_uebertragungen'"
                  );
                }
              }
            }
            if(empty($this->app->User) || !method_exists($this->app->User,'GetID') || !$this->app->User->GetID()) {
              $this->app->DB->Update(
                "UPDATE `prozessstarter` 
                SET `letzteausfuerhung`=DATE_ADD(NOW(), INTERVAL 2 MINUTE), `mutex` = 1,`mutexcounter`=0 
                WHERE `parameter` = 'api_uebertragungen'"
              );
            }
          }
        }
      }
      $this->lagerzahlen = null;
      if((int)$uebertragung <= 0 || empty($this->bestBeforeBatchArticleStock)) {
        $this->bestBeforeBatchArticleStock = [];

        return true;
      }
      $storageMethod = (string)$this->app->DB->Select(
        "SELECT `storage_import_method` FROM `uebertragungen_account` WHERE `id` = {$uebertragung}"
      );
      if($storageMethod === 'overwrite'){
        $this->updateStockByBestBeforeBatch(true);
      }
      if($storageMethod === 'overwritewithoutdelete'){
        $this->updateStockByBestBeforeBatch(false);
      }
      $this->bestBeforeBatchArticleStock = [];

      return true;
  }

    /**
     * @param int  $articleId
     * @param int  $storageLocationId
     * @param bool $isBestBefore
     * @param bool $isBatch
     */
  public function removeBestBeforeBatchesNotExistsInXML(
    int $articleId,
    int $storageLocationId,
    bool $isBestBefore,
    bool $isBatch
  ): void
  {
    if(!$isBatch && !$isBestBefore) {
      return;
    }
    if($articleId <= 0 || $storageLocationId <= 0) {
      return;
    }
    if(empty($this->bestBeforeBatchArticleStock[$articleId])) {
      return;
    }
    if(!isset($this->bestBeforeBatchArticleStock[$articleId][$storageLocationId])) {
      return;
    }
    if($isBestBefore) {
      $bestBefores = [];
      foreach($this->bestBeforeBatchArticleStock[$articleId][$storageLocationId]['entries'] as $entry) {
        if(!empty($entry['bestbefore'])) {
          $bestBefore = $entry['bestbefore'];
          if(strpos($bestBefore, '.') !== false) {
            $bestBefore = $this->app->String->Convert(substr($bestBefore,0,10), '%1.%2.%3', '%3-%2-%1');
          }
          if($isBatch) {
            if(!isset($bestBefores[$bestBefore][(string)$entry['batch']])) {
              $bestBefores[$bestBefore][(string)$entry['batch']] = 0;
            }
            $bestBefores[$bestBefore][(string)$entry['batch']] += $entry['quantity'];
          }
          else {
            if(!isset($bestBefores[$bestBefore])) {
              $bestBefores[$bestBefore] = 0;
            }
            $bestBefores[$bestBefore] += (float)$entry['quantity'];
          }
        }
      }

      if($isBatch) {
        $bestBeforeStocks = $this->app->DB->SelectArr(
          "SELECT `mhddatum`, `charge`, `menge`, `id` 
          FROM `lager_mindesthaltbarkeitsdatum` 
          WHERE `artikel` = {$articleId} AND `lager_platz` = {$storageLocationId}
          ORDER BY `mhddatum`, `charge`, `menge`"
        );
      }
      else {
        $bestBeforeStocks = $this->app->DB->SelectArr(
          "SELECT `mhddatum`, `menge`, `id`
          FROM `lager_mindesthaltbarkeitsdatum` 
          WHERE `artikel` = {$articleId} AND `lager_platz` = {$storageLocationId}
          ORDER BY `mhddatum`, `charge`, `menge`"
        );
      }
      if(empty($bestBeforeStocks)) {
        return;
      }

      foreach($bestBeforeStocks as $bestBeforeStock) {
        if($bestBeforeStock['menge'] <= 0) {
          $this->app->DB->Delete("DELETE FROM `lager_mindesthaltbarkeitsdatum` WHERE `id` = {$bestBeforeStock['id']}");
          continue;
        }
        if(!isset($bestBefores[$bestBeforeStock['mhddatum']])) {
          $this->app->DB->Delete("DELETE FROM `lager_mindesthaltbarkeitsdatum` WHERE `id` = {$bestBeforeStock['id']}");
          $this->app->erp->MHDLog(
            $articleId, $storageLocationId, 0, $bestBeforeStock['mhd'], $bestBeforeStock['menge'], 'API Korrektur'
          );
          if($isBatch) {
            $this->app->erp->ChargeAuslagernLog(
              $articleId, $storageLocationId, $bestBeforeStock['charge'], $bestBeforeStock['menge'], 'API Korrektur'
            );
          }
          continue;
        }
        if($isBatch && !isset($bestBefores[$bestBeforeStock['mhddatum']][$bestBeforeStock['charge']])) {
          $this->app->DB->Delete("DELETE FROM `lager_mindesthaltbarkeitsdatum` WHERE `id` = {$bestBeforeStock['id']}");
          $this->app->erp->MHDLog(
            $articleId, $storageLocationId, 0, $bestBeforeStock['mhd'], $bestBeforeStock['menge'], 'API Korrektur'
          );
          $this->app->erp->ChargeAuslagernLog(
            $articleId, $storageLocationId, $bestBeforeStock['charge'], $bestBeforeStock['menge'], 'API Korrektur'
          );
          continue;
        }
      }
    }
    elseif($isBatch) {
      $batches = [];
      foreach($this->bestBeforeBatchArticleStock[$articleId][$storageLocationId]['entries'] as $entry) {
        $batch = $entry['batch'];
        if(!isset($batches[$batch])) {
          $batches[$batch] = 0;
        }
        $batches[$batch] += (float)$entry['quantity'];
      }
      $batchStocks = $this->app->DB->SelectArr(
        "SELECT `charge`, `menge`, `id` 
        FROM `lager_charge` 
        WHERE `artikel` = {$articleId} AND `lager_platz` = {$storageLocationId}
        ORDER BY `charge`, `menge`"
      );
      if(empty($batchStocks)) {
        return;
      }
      foreach($batchStocks as $batchStock) {
        if($batchStock['menge'] <= 0) {
          $this->app->DB->Delete("DELETE FROM `lager_charge` WHERE `id` = {$batchStock['id']}");
          continue;
        }
        if(!isset($batches[$batchStock['charge']])) {
          $this->app->DB->Delete("DELETE FROM `lager_charge` WHERE `id` = {$batchStock['id']}");
          $this->app->erp->Chargenlog(
            $articleId, $storageLocationId, 0, $batchStock['charge'], $batchStock['menge'], 'API Korrektur'
          );
        }
      }
    }
  }

    /**
     * @param bool $deleteOtherStock
     */
    public function updateStockByBestBeforeBatch(bool $deleteOtherStock = true): void
    {
      if(empty($this->bestBeforeBatchArticleStock)){
        return;
      }
      foreach ($this->bestBeforeBatchArticleStock as $articleId => $stocks) {
        if(empty($articleId) || empty($stocks)){
          continue;
        }
        $article = $this->app->DB->SelectRow(
          "SELECT `projekt`, `chargenverwaltung`, `mindesthaltbarkeitsdatum`, `seriennummern` 
          FROM `artikel` WHERE `id` = {$articleId} LIMIT 1"
        );
        if(empty($article)){
          continue;
        }
        $projectId = $article['projekt'];
        foreach ($stocks as $storageLocationIndex => $stockEntry) {
          if(empty($storageLocationIndex)){
            continue;
          }
          if($deleteOtherStock){
            $this->removeBestBeforeBatchesNotExistsInXML(
              (int)$articleId,
              (int)$storageLocationIndex,
              $article['mindesthaltbarkeitsdatum'] > 0,
              $article['chargenverwaltung'] > 0
            );
          }

          if(!empty($article['mindesthaltbarkeitsdatum'])){
            $stock = (float)$this->app->DB->Select(
              "SELECT SUM(`menge`) 
              FROM `lager_mindesthaltbarkeitsdatum` 
              WHERE `artikel` = {$articleId} AND `lager_platz` = {$storageLocationIndex} AND `menge` > 0"
            );
          }elseif(!empty($article['chargenverwaltung'])){
            $stock = (float)$this->app->DB->Select(
              "SELECT SUM(`menge`) 
              FROM `lager_charge` 
              WHERE `artikel` = {$articleId} AND `lager_platz` = {$storageLocationIndex} AND `menge` > 0"
            );
          }else{
            continue;
          }
          $inStock = (float)$this->app->erp->ArtikelImLagerPlatz($articleId, $storageLocationIndex);
          if(round($inStock, 4) === round($stock, 4)){
            continue;
          }
          if($inStock < $stock){
            $this->app->erp->LagerEinlagern(
              $articleId,
              $stock - $inStock,
              $storageLocationIndex,
              $projectId,
              'API Korrektur'
            );
            continue;
          }
          $this->app->erp->LagerAuslagernRegal(
            $articleId, $storageLocationIndex, $inStock - $stock, $projectId, 'API Korrektur'
          );
        }
      }
    }

    /**
     * @param string $tabelle
     * @param int    $id_int
     * @param string $id_ext
     * @param int    $api
     *
     * @return bool
     */
  public function AddMapping($tabelle, $id_int, $id_ext, $api = 0)
  {
    if(!$api){
      $api = $this->api_id;
    }
    $api = (int)$api;
    if(!$api){
      return false;
    }
    $checkmapping = $this->app->DB->SelectArr("SELECT id, id_ext FROM api_mapping WHERE api = '$api' AND tabelle = '$tabelle' AND id_int = '$id_int' LIMIT 1");
    if($checkmapping) {
      if($checkmapping[0]['id_ext'] == '') {
        $this->app->DB->Update("UPDATE api_mapping SET id_ext = '".$this->app->DB->real_escape_string($id_ext)."' WHERE id = '".$checkmapping[0]['id']."' LIMIT 1");
        return true;
      }
    }
    else{
      $this->app->DB->Insert("INSERT INTO api_mapping (id_int, tabelle, id_ext, api) VALUES ('$id_int','$tabelle','".$this->app->DB->real_escape_string($id_ext)."','$api')");
      return true;
    }

    return false;
  }

  function UpdateMappingXML($typ, &$xml)
  {
    $typen = $this->getTabellen();
    $belege = $this->getBelege();
    $is_beleg = in_array($typ, $belege);
    $is_typ = false;
    if(!$is_beleg){
      $is_typ = in_array($typ, $typen);
    }
    if(!$is_typ && !$is_beleg) {
      return false;
    }

    if(isset($xml->id_ext)) {
      $id_ext = (string)$xml->id_ext;
      if($id_ext != '') {
        $id = 0;
        if(isset($xml->id)){
          $id = (int)$xml->id;
        }
        if($id > 0) {
          $this->AddMapping($typ, $id, $id_ext);
        }
      }
    }
    if(!$is_beleg) {
      return true;
    }

    if(!is_array($xml) && !is_object($xml)) {
      return true;
    }
    if(count($xml) === 0) {
      return true;
    }

    foreach($xml as $key => $value) {
      if($key == $typ.'_position_list'){
        if((is_array($value) || is_object($value)) && count($value) > 0) {
          foreach($value as $key2 => $value2)  {
            if($key2 === $typ.'_position' && isset($value2->id_ext)) {
              $id_ext2 = (string)$value2->id_ext;
              if($id_ext2 != '') {
                $id2 = 0;
                if(isset($value2->id)){
                  $id2 = (int)$value2->id;
                }
                if($id2 > 0) {
                  $this->AddMapping($typ.'_position', $id2, $id_ext2);
                }
              }
            }
          }
        }
      }
      elseif($key === $typ.'_position' && isset($value->id_ext)) {
        $id_ext2 = (string)$value->id_ext;
        if($id_ext2 != '') {
          $id2 = 0;
          if(isset($value->id)) {
            $id2 = (int)$value->id;
          }
          if($id2 > 0) {
            $this->AddMapping($typ.'_position', $id2, $id_ext2);
          }
        }
      }
    }

    return true;
  }

  public function GetIDFromFeld($typ, &$xml)
  {
    if(empty($typ)){
      return false;
    }

    if(!is_object($xml) && !is_array($xml)) {
      return false;
    }

    if(count($xml) === 0) {
      return false;
    }
    $id = 0;

    $id_ext = '';
    if(isset($xml->id_ext)){
      $id_ext = (string)$xml->id_ext;
    }
    if($id_ext != '') {
      $id = (int)$this->app->DB->Select(
        sprintf(
          "SELECT id_int 
          FROM api_mapping 
          WHERE api = %d AND api != 0 AND tabelle = '%s' AND id_ext = '%s' AND id_int > 0 
          LIMIT 1",
          $this->api_id, $typ, $this->app->DB->real_escape_string($id_ext)
        )
      );
      if($id) {
        return $id;
      }
    }
    if(isset($xml->id) && $typ !== 'artikel' && $typ !== 'auftrag' && $typ !== 'bestellung' && $typ !== 'versand'){
      if(in_array($typ, ['lieferschein', 'retoure','rechnung','gutschrift'])) {
        $id = (int)$this->app->DB->Select(sprintf('SELECT `id` FROM `%s` WHERE `id` = %d LIMIT 1', $typ, (int)$xml->id));
      }
      else{
        $id = (int)$xml->id;
      }
    }
    if($id){
      return $id;
    }
    $projekt = $this->app->DB->Select(
      sprintf(
        'SELECT projekt FROM api_account WHERE id = %d AND id != 0 LIMIT 1',
        $this->api_id
      )
    );
    $eigenernummernkreis = false;
    if($projekt) {
      $eigenernummernkreis = $this->app->DB->Select(
        sprintf(
          'SELECT eigenernummernkreis FROM projekt WHERE id = %d LIMIT 1',
          $projekt
        )
      );
    }
    switch($typ) {
      case 'artikel':
        //nummer
        if(!empty($xml->nummer) && $xml->nummer != '') {
          $nummer = trim((string)$xml->nummer);
          if($nummer != '') {
            if($eigenernummernkreis) {
              $id = $this->app->DB->Select(
                sprintf(
                  "SELECT id 
                  FROM artikel 
                  WHERE nummer <> 'DEL' AND nummer = '%s' AND nummer <> '' AND projekt = %d AND geloescht <> 1 
                  LIMIT 1",
                  $this->app->DB->real_escape_string($nummer), $projekt
                )
              );
            }
            else {
              $id = $this->app->DB->Select(
                sprintf(
                  "SELECT id 
                  FROM artikel 
                  WHERE nummer <> 'DEL' AND nummer = '%s' AND nummer <> '' AND geloescht <> 1 
                  LIMIT 1",
                  $this->app->DB->real_escape_string($nummer)
                )
              );
            }
            if($id) {
              return $id;
            }
          }
        }

        //ean
        if(!empty($xml->ean) && $xml->ean != '') {
          $ean = trim((string)$xml->ean);
          if($ean != '') {
            if($eigenernummernkreis) {
              $id = $this->app->DB->Select(
                sprintf(
                  "SELECT id 
                  FROM artikel 
                  WHERE nummer <> 'DEL' AND ean = '%s' AND projekt = %d AND geloescht <> 1 AND ean <> ''
                  ORDER BY intern_gesperrt
                  LIMIT 1",
                  $this->app->DB->real_escape_string($ean), $projekt
                )
              );
            }
            else {
              $id = $this->app->DB->Select(
                sprintf(
                  "SELECT id 
                  FROM artikel 
                  WHERE nummer <> 'DEL' AND ean = '%s' AND geloescht <> 1 AND ean <> ''
                  ORDER BY intern_gesperrt
                  LIMIT 1",
                  $this->app->DB->real_escape_string($ean)
                )
              );
            }
            if($id) {
              return $id;
            }
          }
        }
        //herstellernummer
        if(isset($xml->herstellernummer) && $xml->herstellernummer != '') {
          $herstellernummer = trim((string)$xml->herstellernummer);
          if($herstellernummer != '') {
            if($eigenernummernkreis) {
              $id = $this->app->DB->Select(
                sprintf(
                  "SELECT id 
                  FROM artikel 
                  WHERE nummer <> 'DEL' AND herstellernummer = '%s' AND herstellernummer <> '' 
                    AND projekt = %d AND geloescht <> 1
                  ORDER BY intern_gesperrt
                  LIMIT 1",
                  $this->app->DB->real_escape_string($herstellernummer), $projekt
                )
              );
            }
            else{
              $id = $this->app->DB->Select(
                sprintf(
                  "SELECT id 
                  FROM artikel 
                  WHERE nummer <> 'DEL' AND herstellernummer = '%s' AND herstellernummer <> '' 
                    AND geloescht <> 1
                  ORDER BY intern_gesperrt
                  LIMIT 1",
                  $this->app->DB->real_escape_string($herstellernummer)
                )
              );
            }
            if($id) {
              return $id;
            }
          }
        }

        if(!empty($this->uebertragung_account) && (!empty($xml->bestellnummer) || !empty($xml->nummer))) {
          if(
            $lieferant = $this->app->DB->Select(
              'SELECT adresselieferant FROM uebertragungen_account 
              WHERE  lieferantenbestellnummer = 1 AND 
              id = '.$this->uebertragung_account
            )
          ) {
            $bestellnummer = trim(!empty($xml->bestellnummer)?(string)$xml->bestellnummer:(string)$xml->nummer);
            $id = $this->app->DB->Select(
              sprintf(
                'SELECT art.id 
                FROM einkaufspreise AS ek 
                INNER JOIN artikel AS art ON ek.artikel = art.id
                WHERE ek.bestellnummer = \'%s\' AND ek.bestellnummer <> \'\' AND ek.adresse = %d AND 
                (
                  IFNULL(ek.gueltig_bis,\'0000-00-00\') = \'0000-00-00\' OR
                  ek.gueltig_bis >= CURDATE()
                )
                LIMIT 1',
                $this->app->DB->real_escape_string($bestellnummer),$lieferant
              )
            );
            if($id > 0) {
              return $id;
            }
          }
        }
      break;
      case 'lieferschein':
      case 'retoure':
      case 'rechnung':
      case 'auftrag':
      case 'gutschrift':
      case 'angebot':
        if(isset($xml->belegnr) && (string)$xml->belegnr != ''){
          $id = $this->app->DB->Select("SELECT id FROM $typ WHERE belegnr = '".$this->app->DB->real_escape_string((string)$xml->belegnr)."' LIMIT 1");
        }
        if(!$id && ($typ === 'lieferschein' || $typ === 'retoure'))
        {
          if(!empty($xml->auftrag) && (string)$xml->auftrag != '') {
            $auftragid = $this->app->DB->Select("SELECT id FROM auftrag WHERE belegnr = '".$this->app->DB->real_escape_string((string)$xml->auftrag)."' LIMIT 1");
            if($auftragid) {
              if($typ === 'lieferschein'){
                $id = $this->app->DB->Select("SELECT `id` FROM `lieferschein` WHERE `auftragid` = '$auftragid' AND `status` <> 'storniert' LIMIT 1");
              }
              else {
                $id = $this->app->DB->Select("SELECT `id` FROM `retoure` WHERE `auftragid` = '$auftragid' AND `status` <> 'storniert' LIMIT 1");
              }
            }
          }
        }
        if($id) {
          return $id;
        }
      break;
      case 'lieferschein_position':
      case 'retoure_position':
      case 'rechnung_position':
      case 'auftrag_position':
      case 'gutschrift_position':
      case 'angebot_position':
        $_ptyp = str_replace('_position','',$typ);
        if(isset($xml->$_ptyp) && isset($xml->sort)) {
          $id = $this->app->DB->Select("SELECT id FROM $typ WHERE $_ptyp = '".(int)$xml->$_ptyp."' AND sort = '".(int)$xml->sort."' AND $_ptyp != '0' AND sort != '0' LIMIT 1");
          if($id){
            return $id;
          }
        }
      break;
      case 'adresse':
        if(isset($xml->kundennummer)) {
          $kundennummer = (string)$xml->kundennummer;
          if($kundennummer != '') {
            if($eigenernummernkreis) {
              $id = $this->app->DB->Select("SELECT id FROM adresse WHERE geloescht <> '1' AND kundennummer = '".$this->app->DB->real_escape_string($kundennummer)."' AND projekt = '$projekt' LIMIT 1");
            }
            else{
              $id = $this->app->DB->Select("SELECT id FROM adresse WHERE geloescht <> '1' AND kundennummer = '".$this->app->DB->real_escape_string($kundennummer)."' LIMIT 1");
            }
            if($id){
              return $id;
            }
          }
        }
        if(isset($xml->lieferantennummer)) {
          $lieferantennummer = (string)$xml->lieferantennummer;
          if($lieferantennummer != '') {
            if($eigenernummernkreis) {
              $id = $this->app->DB->Select("SELECT id FROM adresse WHERE geloescht <> '1' AND lieferantennummer = '".$this->app->DB->real_escape_string($lieferantennummer)."' AND projekt = '$projekt' LIMIT 1");
            }
            else{
              $id = $this->app->DB->Select("SELECT id FROM adresse WHERE geloescht <> '1' AND lieferantennummer = '".$this->app->DB->real_escape_string($lieferantennummer)."' LIMIT 1");
            }
            if($id){
              return $id;
            }
          }
        }
      break;
    }

    return false;
  }

    /**
     * @param string $tabelle
     * @param string $extid
     * @param mixed  $xml
     *
     * @return null|int
     */
  public function GetFromExtID($tabelle, $extid, $xml = null)
  {
    if(!$this->uebertragung_account && !$this->api_id){
      return null;
    }
    if($xml && isset($xml->id) && isset($xml->belegnr) && isset($xml->name))
    {
      $id = $this->app->DB->Select("SELECT id FROM $tabelle WHERE id = '".((int)$xml->id)."' AND belegnr = '".$this->app->DB->real_escape_string($xml->belegnr)."' AND name = '".$this->app->DB->real_escape_string($xml->name)."' LIMIT 1");      if($id){
        return $id;
      }
    }
    return $this->app->DB->Select(
      sprintf(
        "SELECT id_int 
        FROM api_mapping 
        WHERE id_ext = '%s' AND tabelle = '%s' AND 
        (
          (uebertragung_account = %d AND uebertragung_account <> 0) OR
          (api = %d AND uebertragung_account <> 0)
        ) 
        ORDER BY %s DESC 
        LIMIT 1",
        $this->app->DB->real_escape_string($extid), $tabelle,(int)$this->uebertragung_account, (int)$this->api_id,
        ($this->uebertragung_account?' uebertragung_account ':' api ')
      )
    );
  }

    /**
     * @param string $tabelle
     * @param int    $id
     *
     * @return null|string
     */
  public function GetExtIDFromIntID($tabelle, $id)
  {
    if(!$this->uebertragung_account && !$this->api_id){
      return null;
    }

    return $this->app->DB->Select(
      sprintf(
        "SELECT id_ext 
        FROM api_mapping 
        WHERE id_int = %d AND tabelle = '%s' AND 
        (
          (uebertragung_account = %d AND uebertragung_account <> 0) AND 
          (api = %d AND uebertragung_account <> 0)
        ) 
        ORDER BY %s DESC 
        LIMIT 1",
        $id,$tabelle, $this->uebertragung_account, $this->api_id,
        $this->uebertragung_account?' uebertragung_account ':' api '
      )
    );
  }

    /**
     * @param string $tabelle
     * @param int    $intid
     * @param string $extid
     *
     * @return bool
     */
  public function SetExtIDMapping($tabelle, $intid, $extid)
  {
    if(!$this->uebertragung_account && !$this->api_id){
      return false;
    }
    $check = $this->GetFromExtID($tabelle, $extid);
    if($check) {
      return $check == $intid;
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `api_mapping` (`tabelle`, `id_ext`, `id_int`, `api`, `uebertragung_account`) 
        VALUES ('%s','%s',%d,%d,%d)",
        $tabelle,$this->app->DB->real_escape_string($extid), $intid, $this->api_id, $this->uebertragung_account
      )
    );
    return true;
  }

    /**
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     * @param string         $typ
     *
     * @return bool
     */
  public function isAllowedToImport($uebertragungen_account, $obj, $typ) {
    switch($typ) {
      case 'auftrag':
      case 'angebot':
        if(!$this->app->DB->Select("SELECT auftrageingang FROM uebertragungen_account WHERE id = '".$uebertragungen_account."' LIMIT 1"))
        {
          if(!$this->app->DB->Select("SELECT id FROM `uebertragungen_monitor` WHERE uebertragungen_account = '$uebertragungen_account' AND datei = '".$this->datei_id."' AND status = 'notallowed' AND doctype = '$typ' AND zeitstempel > DATE_SUB(now(), INTERVAL 1 HOUR)  LIMIT 1")){
            $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'not_allowed', ucfirst($typ).'-Eingang ist nicht aktiviert', '', '', '', $typ);
          }
          return false;
        }
        break;
      case 'bestellung':
      case 'produktion':
        if(!$this->app->DB->Select("SELECT bestellungeingang FROM uebertragungen_account WHERE id = '".$uebertragungen_account."'  LIMIT 1"))
        {
          if(!$this->app->DB->Select("SELECT id FROM `uebertragungen_monitor` WHERE uebertragungen_account = '$uebertragungen_account' AND datei = '".$this->datei_id."' AND status = 'notallowed' AND doctype = '$typ' AND zeitstempel > DATE_SUB(now(), INTERVAL 1 HOUR)  LIMIT 1")){
            $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'not_allowed', ucfirst($typ).'-Eingang ist nicht aktiviert', '', '', '', $typ);
          }
          return false;
        }
        break;
      case 'lieferschein':
        if(!$this->app->DB->Select("SELECT trackingeingang FROM uebertragungen_account WHERE id = '".$uebertragungen_account."'  LIMIT 1"))
        {
          if(!$this->app->DB->Select("SELECT id FROM `uebertragungen_monitor` WHERE uebertragungen_account = '$uebertragungen_account' AND datei = '".$this->datei_id."' AND status = 'notallowed' AND doctype = '$typ' AND zeitstempel > DATE_SUB(now(), INTERVAL 1 HOUR)  LIMIT 1")){
            $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'not_allowed', 'Tracking-Eingang ist nicht aktiviert', '', '', '', $typ);
          }
          return false;
        }
        break;
      case 'retoure':
        if(
          !$this->app->DB->Select(
            sprintf(
              'SELECT `alldoctypes` FROM `uebertragungen_account` WHERE `id` = %d LIMIT 1',
              $uebertragungen_account
            )
          )
        ){
          if(
            !$this->app->DB->Select(
              sprintf(
                "SELECT `id` 
                FROM `uebertragungen_monitor` 
                WHERE `uebertragungen_account` = %d 
                  AND `datei` = %d AND `status` = 'notallowed' 
                  AND `doctype` = '%s' 
                  AND `zeitstempel` > DATE_SUB(now(), INTERVAL 1 HOUR)  
                LIMIT 1",
                $uebertragungen_account, $this->datei_id, $typ
              )
            )
          ){
            $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'not_allowed', ucfirst($typ).'-Eingang ist nicht aktiviert', '', '', '', $typ);
          }
          return false;
        }
        break;
    }

    return true;
  }

    /**
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     * @param int            $id
     */
  public function ParsePartXmlAricleWithId(&$xml, $uebertragungen_account, $obj, $id)
  {
    $artikelarr = ['id' => $id];
    $account = $this->app->DB->SelectRow(
      sprintf(
        'SELECT updatearticles FROM uebertragungen_account WHERE id = %d  LIMIT 1',
        $uebertragungen_account
      )
    );
    $updatearticles = !empty($account['updatearticles']);
    if(!empty($xml->anlegen) && $updatearticles && !empty($xml->nummer) && !empty($xml->name_de)) {
      if(!empty($this->app->stringcleaner)) {
        $this->app->stringcleaner->XMLArray_clean($xml);
      }
      foreach($xml as $k => $v) {
        if(empty($k) || $k === 'id' || $k === 'nummer' || is_numeric($k)) {
          continue;
        }
        $vr = @reset($v);
        if(is_numeric($vr) || is_string($vr)) {
          $artikelarr[$k] = (string)$vr;
        }
      }

      //if(isset($xml->nummer))$artikelarr['nummer'] = (string)$xml->nummer;
      if(isset($xml->ean))$artikelarr['ean'] = (string)$xml->ean;
      if(isset($xml->herstellernummer))$artikelarr['herstellernummer'] = (string)$xml->herstellernummer;
      if(isset($xml->name_de))$artikelarr['name_de'] = (string)$xml->name_de;
      if(isset($xml->name_en))$artikelarr['name_en'] = (string)$xml->name_en;
      if(isset($xml->anabregstext))$artikelarr['anabregstext'] = (string)$xml->anabregstext;
      if(isset($xml->anabregstext_en))$artikelarr['anabregstext_en'] = (string)$xml->anabregstext_en;
      if(isset($xml->uebersicht_de))$artikelarr['uebersicht_de'] = (string)$xml->uebersicht_de;
      if(isset($xml->uebersicht_en))$artikelarr['uebersicht_en'] = (string)$xml->uebersicht_en;
      if(isset($xml->kurztext_de))$artikelarr['kurztext_de'] = (string)$xml->kurztext_de;
      if(isset($xml->kurztext_en))$artikelarr['kurztext_en'] = (string)$xml->kurztext_en;
      if(isset($xml->lagerartikel))$artikelarr['lagerartikel'] = (string)$xml->lagerartikel;

      if(isset($xml->gewicht))$artikelarr['gewicht'] = (string)$xml->gewicht;
      if(isset($xml->breite))$artikelarr['breite'] = (string)$xml->breite;
      if(isset($xml->laenge))$artikelarr['laenge'] = (string)$xml->laenge;
      if(isset($xml->hoehe))$artikelarr['hoehe'] = (string)$xml->hoehe;

      for($i = 1; $i <= 20; $i++) {
        $name = 'freifeld'.$i;
        if(isset($xml->$name)){
          $xml->$name = $this->app->Secure->CleanString($xml->$name, 'nohtml');
          $artikelarr[$name] = (string)$xml->$name;
        }
      }
      $artikelid = $this->app->erp->InsertUpdateArtikel($artikelarr, true);
      if($artikelid) {
        $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'artikel_ok', 'Artikel ' . (string)$xml->nummer . ' updated', '', '', '', 'artikel', $artikelid);
      }
      unset($artikelarr);
    }

    if((isset($xml->lagerzahl) || isset($xml->mhdanzahl) || !empty($xml->mhd) || !empty($xml->charge)) && $this->app->DB->Select("SELECT lagerzahleneingang FROM uebertragungen_account WHERE id = '".$uebertragungen_account."'  LIMIT 1")) {

      $artArr = $this->app->DB->SelectRow(
        sprintf(
          'SELECT chargenverwaltung,seriennummern,mindesthaltbarkeitsdatum, lagerartikel FROM artikel WHERE id = %d',
          $id
        )
      );
      if(empty($artArr['lagerartikel'])) {
        if($uebertragungen_account) {
          $obj->AddUbertragungMonitorLog(
            $uebertragungen_account,
            $this->datei_id,
            0,
            'artikel_error',
            'Artikel ' . (string)$xml->nummer . ' ist kein Lagerartikel',
            '',
            '',
            '',
            'artikel',
            $id
          );
        }
        return;
      }
      if(!in_array((string)$artArr['seriennummern'], ['', 'keine', 'vomprodukt'])) {
        if($uebertragungen_account) {
          $obj->AddUbertragungMonitorLog(
            $uebertragungen_account,
            $this->datei_id,
            0,
            'artikel_error',
            'Artikel ' . (string)$xml->nummer . ' ist ein Seriennummern-Artikel',
            '',
            '',
            '',
            'artikel',
            $id
          );
        }
        return;
      }

      $lagerzahl = isset($xml->lagerzahl)?str_replace(',','.',(string)$xml->lagerzahl):null;
      $mhdanzahl = isset($xml->mhdanzahl)?str_replace(',','.',(string)$xml->mhdanzahl):null;
      if(!empty($artArr['mindesthaltbarkeitsdatum']) && empty($xml->mhd) && ((float)$lagerzahl != 0 || (float)$mhdanzahl != 0)) {
        if($uebertragungen_account) {
          $obj->AddUbertragungMonitorLog(
            $uebertragungen_account,
            $this->datei_id,
            0,
            'artikel_error',
            'Artikel ' . (string)$xml->nummer . ': kein MHD angegeben',
            '',
            '',
            '',
            'artikel',
            $id
          );
        }
        return;
      }

      if(empty($artArr['mindesthaltbarkeitsdatum']) && !empty($artArr['chargenverwaltung']) && empty($xml->charge) && (float)$lagerzahl != 0) {
        if($uebertragungen_account) {
          $obj->AddUbertragungMonitorLog(
            $uebertragungen_account,
            $this->datei_id,
            0,
            'artikel_error',
            'Artikel ' . (string)$xml->nummer . ': keine Charge angegeben',
            '',
            '',
            '',
            'artikel',
            $id
          );
        }
        return;
      }

      //$lagerzahl = str_replace(',','.',isset($xml->lagerzahl)?(string)$xml->lagerzahl:$xml->mhdanzahl);

      $lager_platz_id = false;
      $lagerplatz = false;
      $lagerplatzignorieren = $this->app->DB->Select("SELECT lagerplatzignorieren FROM uebertragungen_account WHERE id = '".$uebertragungen_account."' LIMIT 1");

      if(isset($xml->lager_platz) && !$lagerplatzignorieren) {
        $lagerplatz = (string)$xml->lager_platz;

        $lagerplatz = $this->app->DB->Select("SELECT id FROM lager_platz WHERE kurzbezeichnung = '".$this->app->DB->real_escape_string($lagerplatz)."' LIMIT 1");

        $lager_platz_id = $lagerplatz;
        if(!$lager_platz_id) {
          $lagerplatz = $this->app->DB->Select("SELECT lager FROM uebertragungen_account WHERE id = '".$uebertragungen_account."' LIMIT 1");
          $lager_platz_id = $lagerplatz;
        }
      }
      else {
        if(!isset($xml->lager_platz)) {
          $this->lagerzahlen[$uebertragungen_account][$id][isset($xml->lager_platz) && (string)$xml->lager_platz != ''?(string)$xml->lager_platz:'0'] = 0;
        }
        $lagerplatz = $this->app->DB->Select("SELECT lager FROM uebertragungen_account WHERE id = '".$uebertragungen_account."' LIMIT 1");
        $lager_platz_id = $lagerplatz;
      }
      $storageLocationIndex = (!$lagerplatzignorieren && isset($xml->lager_platz) && (string)$xml->lager_platz != '')?(string)$xml->lager_platz:'0';
      $storageLocationId = empty($lager_platz_id) ? 0 : (int)$lager_platz_id;
      if(empty($this->lagerzahlen)
        || empty($this->lagerzahlen[$uebertragungen_account])
        || empty($this->lagerzahlen[$uebertragungen_account][$id])
        || !isset($this->lagerzahlen[$uebertragungen_account][$id][(!$lagerplatzignorieren && isset($xml->lager_platz) && $xml->lager_platz != '')?(string)$xml->lager_platz:'0'])) {
        $this->lagerzahlen[$uebertragungen_account][$id][(!$lagerplatzignorieren && isset($xml->lager_platz) && (string)$xml->lager_platz != '')?(string)$xml->lager_platz:'0'] = 0;
      }

      if($lagerzahl !== null && round((float)str_replace(',','.',$lagerzahl),8) < 0) {
        if($uebertragungen_account) {
          $obj->AddUbertragungMonitorLog(
            $uebertragungen_account,
            $this->datei_id,
            0,
            'artikel_error',
            'Artikel ' . (string)$xml->nummer . ' Lagerzahl '.$lagerzahl.' kleiner als 0',
            '',
            '',
            '',
            'artikel',
            $id
          );
        }
        return;
      }
      if($lagerzahl !== null && $lagerzahl != ''
        && (empty($artArr['chargenverwaltung']) || !isset($xml->charge))
        && (empty($artArr['mindesthaltbarkeitsdatum']) || !isset($xml->mhd))
      ) {
        $lagerzahl = round((float)str_replace(',','.',$lagerzahl),8);
        $this->lagerzahlen[$uebertragungen_account][$id][(!$lagerplatzignorieren && isset($xml->lager_platz) && (string)$xml->lager_platz != '')?(string)$xml->lager_platz:'0'] += $lagerzahl;
        $lagerzahl = $this->lagerzahlen[$uebertragungen_account][$id][(!$lagerplatzignorieren && isset($xml->lager_platz) && (string)$xml->lager_platz != '')?(string)$xml->lager_platz:'0'];
        if($lager_platz_id) {
          $vorhanden = round((float)$this->app->DB->Select("SELECT IFNULL(sum(menge),0) FROM lager_platz_inhalt WHERE lager_platz = '$lager_platz_id' AND artikel = '$id'"),8);
        }
        else{
          $vorhanden = round((float)$this->app->erp->ArtikelAnzahlLager($id),8);
        }
        $projekt = (int)$this->app->DB->Select("SELECT projekt FROM artikel WHERE id = '$id' LIMIT 1");
        if(round($lagerzahl,8) > round($vorhanden,8)) {

          if(!$lagerplatz) {
            $lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id = '$id' LIMIT 1");
          }
          if(!$lagerplatz) {
            $lagerplatz = $this->app->DB->Select("SELECT lp.id FROM lager_platz_inhalt lpi INNER JOIN lager_platz lp ON lpi.lager_platz = lp.id WHERE lpi.artikel = '$id' AND lp.sperrlager = 0 AND lp.poslager = 0 ORDER by lpi.menge DESC LIMIT 1");
          }
          if(!$lagerplatz) {
            $lagerplatz = $this->app->DB->Select("SELECT lp.id FROM lager_platz lp WHERE lp.sperrlager = 0 AND lp.poslager = 0 ORDER by lp.id ASC LIMIT 1");
          }
          if($lagerplatz) {
            $this->app->erp->LagerEinlagern($id,$lagerzahl - $vorhanden,$lagerplatz,$projekt,'API Korrektur',true);
            $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','artikel','$id','lagerzahl','$lagerzahl')");
            $this->lagerzahlenmonitor[$uebertragungen_account][$id] = $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'lagerzahlen_ok', '', $lagerzahl, '', '', 'artikel', $id,(isset($this->lagerzahlenmonitor) && isset($this->lagerzahlenmonitor[$uebertragungen_account]) && isset($this->lagerzahlenmonitor[$uebertragungen_account][$id]))?$this->lagerzahlenmonitor[$uebertragungen_account][$id]:0);
          }
        }
        elseif(round($lagerzahl,8) < round($vorhanden,8)) {
          $auslagern = $vorhanden - $lagerzahl;
          if(!$lagerplatz) {
            $lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id = '$id' LIMIT 1");
          }
          if($lagerplatz) {
            $bestand = round((float)$this->app->erp->ArtikelImLagerPlatz($id, $lagerplatz),8);
            if($bestand >= $auslagern) {
              $this->app->erp->LagerAuslagernRegal($id, $lagerplatz, $auslagern, $projekt,'API Korrektur',true );
              $auslagern = 0;
            }
            elseif($bestand > 0) {
              $this->app->erp->LagerAuslagernRegal($id, $lagerplatz, $bestand, $projekt,'API Korrektur',true );
              $auslagern -= $bestand;
            }
          }
          if($auslagern > 0 && !$lager_platz_id) {
            $lagerplaetze = $this->app->DB->SelectArr("SELECT lp.id FROM lager_platz_inhalt lpi INNER JOIN lager_platz lp ON lpi.lager_platz = lp.id WHERE lpi.artikel = '$id' AND lp.sperrlager = 0 AND lp.poslager = 0 ORDER by lpi.menge DESC");
            if($lagerplaetze) {
              foreach($lagerplaetze as $lagerplatz) {
                $bestand = round((float)$this->app->erp->ArtikelImLagerPlatz($id, $lagerplatz['id']),8);
                if($bestand >= $auslagern) {
                  $this->app->erp->LagerAuslagernRegal($id, $lagerplatz['id'], $auslagern, $projekt,'API Korrektur',true );
                  $auslagern = 0;
                }
                elseif($bestand > 0) {
                  $this->app->erp->LagerAuslagernRegal($id, $lagerplatz['id'], $bestand, $projekt,'API Korrektur',true );
                  $auslagern -= $bestand;
                }
                if($auslagern <= 0) {
                  break;
                }
              }
            }
          }
          if($auslagern != $vorhanden - $lagerzahl) {
            $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','artikel','$id','lagerzahl','$lagerzahl')");
            $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'lagerzahlen_ok', '', $lagerzahl, '', '', 'artikel', $id);
          }
        }
      }
      $storageChanged = 0;
      //MHD
      if(!empty($artArr['mindesthaltbarkeitsdatum']) && isset($xml->mhd)) {
        if(empty($this->bestBeforeBatchArticleStock[$id])
          || !isset($this->bestBeforeBatchArticleStock[$id][$storageLocationId])
        ) {
          $this->bestBeforeBatchArticleStock[$id][$storageLocationId]['stock'] = 0;
          $this->bestBeforeBatchArticleStock[$id][$storageLocationId]['entries'] = [];
        }

        if(!$lagerplatz) {
          $lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id = '$id' LIMIT 1");
        }
        if(!$lagerplatz) {
          $lagerplatz = $this->app->DB->Select("SELECT lp.id FROM lager_platz_inhalt lpi INNER JOIN lager_platz lp ON lpi.lager_platz = lp.id WHERE lpi.artikel = '$id' AND lp.sperrlager = 0 AND lp.poslager = 0 ORDER by lpi.menge DESC LIMIT 1");
        }
        if(!$lagerplatz) {
          $lagerplatz = $this->app->DB->Select("SELECT lp.id FROM lager_platz lp WHERE lp.sperrlager = 0 AND lp.poslager = 0 ORDER by lp.id ASC LIMIT 1");
        }

        if($lager_platz_id) {
          $wawimhd = $this->app->DB->SelectArr(
            "SELECT * FROM lager_mindesthaltbarkeitsdatum WHERE artikel = '$id' AND lager_platz = '$lager_platz_id' order by mhddatum, menge desc"
          );
          $chargenmhd = $this->app->DB->SelectArr(
            "SELECT * FROM lager_charge WHERE artikel = '$id' AND lager_platz = '$lager_platz_id' order by charge, menge desc"
          );
        }
        else {
          $wawimhd = null;//$this->app->DB->SelectArr("SELECT * FROM lager_mindesthaltbarkeitsdatum WHERE artikel = '$id' order by mhddatum");
          $chargenmhd = null;//$this->app->DB->SelectArr("SELECT * FROM lager_charge WHERE artikel = '$id' order by charge, menge desc");
          if($wawimhd) {
            $mhdohnelager = null;//$this->app->DB->SelectArr("SELECT mhddatum, sum(menge) as smenge FROM lager_mindesthaltbarkeitsdatum WHERE artikel = '$id' group by mhddatum order by mhddatum ");
          }
          if($chargenmhd) {
            $chargenohnelager = null;//$this->app->DB->SelectArr("SELECT charge, sum(menge) as smenge FROM lager_charge WHERE artikel = '$id' group by charge order by charge ");
          }
        }

        $mhd = $xml->mhd;
        if(!empty($mhd) && !is_string($mhd) && !is_numeric($mhd) &&  count($mhd) === 1 && empty($mhd->datum)
          && empty($mhd->mhddatum) && empty($mhd->anzahl)) {
          $mhd = reset($mhd);
        }
        if(isset($mhd->datum)) {
          $mhddatum = json_decode(json_encode($mhd->datum), true);
        }
        elseif(isset($xml->mhddatum)) {
          $mhddatum = json_decode(json_encode($xml->mhddatum), true);
          if(is_string($mhddatum)) {
            $mhddatum = [$mhddatum];
          }
        }
        elseif(is_string($mhd)) {
          $mhddatum = [$mhd];
        }
        $mhdanzahl = null;
        $mhdcharge = null;
        if(isset($mhd->anzahl)) {
          $mhdanzahl = json_decode(json_encode($mhd->anzahl),true);
        }
        elseif(isset($xml->mhdanzahl) && is_string(json_decode(json_encode($xml->mhdanzahl),true))) {
          $mhdanzahl = [json_decode(json_encode($xml->mhdanzahl),true)];
        }
        elseif(isset($xml->mhdanzahl) && is_numeric((string)$xml->mhdanzahl)) {
          $mhdanzahl = [(float)(string)$xml->mhdanzahl];
        }
        elseif(isset($xml->lagerzahl) && is_numeric((string)$xml->lagerzahl)) {
          $mhdanzahl = [(float)(string)$xml->lagerzahl];
        }
        if(isset($mhd->charge)) {
          $mhdcharge = json_decode(json_encode($mhd->charge),true);
        }
        elseif(isset($xml->charge)
          && (is_string($xml->charge) || (!is_numeric($xml->charge) && count($xml->charge) === 1))) {
          $mhdcharge = [(string)$xml->charge];
        }

        foreach($mhddatum as $kmhd => $m) {
          $charge = '';
          $anzahl = 1;
          if(isset($mhd->charge)) {
            $charge = (string)$mhdcharge[$kmhd];
          }
          elseif(isset($mhdcharge[$kmhd])) {
            $charge = (string)$mhdcharge[$kmhd];
          }
          if(isset($mhd->anzahl)) {
            $anzahl = (int)$mhdanzahl[$kmhd];
          }
          elseif(isset($mhdanzahl[$kmhd])) {
            $anzahl = (int)$mhdanzahl[$kmhd];
          }
          $this->bestBeforeBatchArticleStock[$id][$storageLocationId]['stock'] += (float)$anzahl;
          $datum = date('Y-m-d',strtotime( (string)$m));

          $this->bestBeforeBatchArticleStock[$id][$storageLocationId]['entries'][] =
            [
              'bestbefore' => $datum, 'batch' => $artArr['chargenverwaltung'] > 0 ? $charge : null, 'quantity' => $anzahl
            ];

          $chargenlager = null;
          $found = false;
          $foundcharge = false;
          $lagerzahl = null;
          if($wawimhd) {
            if($lager_platz_id) {
              $inBestBeforeStorage = 0.0;
              $countBestBeforeEntries = 0;
              foreach($wawimhd as $k => $v) {
                if($v['mhddatum'] != $datum) {
                  continue;
                }
                if($artArr['chargenverwaltung'] > 0 && !empty($charge)){
                  if((string)$charge !== (string)$v['charge']){
                    continue;
                  }
                }
                $inBestBeforeStorage += (float)$v['menge'];
                $countBestBeforeEntries++;
              }
              $toChange = round($anzahl - $inBestBeforeStorage,4);
              if($inBestBeforeStorage > 0 && $toChange == 0) {
                $found = true;
                $foundcharge = true;
                $anzahl = 0;
                $inBestBeforeStorage = 0;
              }
              elseif($inBestBeforeStorage > 0 && round($inBestBeforeStorage, 4) != round($anzahl,4)){
                $toChange = round($anzahl - $inBestBeforeStorage,4);
                foreach ($wawimhd as $k => $v) {
                  if($v['mhddatum'] == $datum){
                    if($artArr['chargenverwaltung'] > 0 && !empty($charge)){
                      if((string)$charge !== (string)$v['charge']){
                        if(count($mhddatum) === 1){
                          unset($wawimhd[$k]);
                        }
                        continue;
                      }
                    }
                    $found = true;
                    $wawimhd[$k]['gefunden'] = true;
                    if($toChange < 0) {
                      if($v['menge'] <= -$toChange) {
                        $this->app->DB->Delete(
                          sprintf(
                            'DELETE FROM `lager_mindesthaltbarkeitsdatum` WHERE `id` = %d',
                            $v['id']
                          )
                        );
                        $this->app->erp->MHDLog(
                          $v['artikel'], $v['lager_platz'],  0,
                          $v['mhddatum'], $v['menge'], 'API Korrektur', '', 0, $v['charge']
                        );
                        $toChange += $v['menge'];
                        if(!empty($v['charge'])) {
                          if($this->app->erp->ChargeAuslagernLog(
                            $v['artikel'], $v['lager_platz'], $v['charge'], $v['menge'], 'API Korrektur'
                          )){
                            $foundcharge = true;
                          }
                        }
                        $storageChanged -= $v['menge'];
                        continue;
                      }
                      if($v['menge'] > -$toChange) {
                        $this->app->DB->Update(
                          sprintf(
                            'UPDATE `lager_mindesthaltbarkeitsdatum` SET `menge` = `menge` - %f WHERE `id` = %d',
                            -$toChange, $v['id']
                          )
                        );
                        if(!empty($v['charge'])) {
                          if($this->app->erp->ChargeAuslagernLog(
                            $v['artikel'], $v['lager_platz'], $v['charge'], -$toChange, 'API Korrektur'
                          )) {
                            $foundcharge = true;
                          }
                        }
                        $storageChanged += $toChange;
                        $toChange = 0;
                        break;
                      }
                    }

                    if($toChange > 0) {
                      $this->app->DB->Update(
                        sprintf(
                          'UPDATE `lager_mindesthaltbarkeitsdatum` SET `menge` = `menge` + %f WHERE `id` = %d',
                          $toChange, $v['id']
                        )
                      );
                      $this->app->erp->MHDLog(
                        $v['artikel'], $v['lager_platz'], 1,
                        $v['mhddatum'], $toChange, 'API Korrektur', '', 0, $v['charge']
                      );
                      if(!empty($v['charge'])){
                        $this->app->erp->AddChargeLagerOhneBewegung(
                          $v['artikel'], $toChange, $v['lager_platz'], date('Y-m-d'), $v['charge'], 'API Korrektur'
                        );
                        $foundcharge = true;
                      }
                      $storageChanged += $toChange;
                      $anzahl = 0;
                      $toChange = 0;
                      break;
                    }
                  }
                }
              }
              /*if($chargenmhd && $charge) {
                foreach($chargenmhd as $k => $v) {
                  if($v['charge'] == $charge) {
                    $foundcharge = true;
                    $chargenmhd[$k]['gefunden'] = true;
                    if($v['menge'] != $anzahl) {
                      $storageChanged += $v['menge'] - $anzahl;
                      $this->app->DB->Update(
                        "UPDATE lager_charge SET menge = '$anzahl' WHERE id = '".$v['id']."' LIMIT 1"
                      );

                      break;
                    }
                  }
                }
              }*/
              if(!$found && $anzahl > 0) {
                $storageChanged += $anzahl;
                $this->app->DB->Insert(
                  "INSERT INTO lager_mindesthaltbarkeitsdatum (artikel, datum, mhddatum, menge, lager_platz, charge) 
                              values ('$id',now(),'$datum','$anzahl','$lager_platz_id','".$this->app->DB->real_escape_string($charge)."')"
                );
                $this->app->erp->MHDLog($id, $lager_platz_id,1,$datum,$anzahl,'API Korrektur','',0,$charge);
              }
              if(!$foundcharge && $charge && $anzahl > 0) {
                $this->app->DB->Insert(
                  "INSERT INTO lager_charge (artikel, datum, menge, lager_platz, charge) 
                            values ('$id',now(),'$anzahl','$lager_platz_id','".$this->app->DB->real_escape_string($charge)."')"
                );
                $this->app->erp->Chargenlog($id, $lager_platz_id,1,$charge,$anzahl,'API Korrektur','',0,$charge);
              }
            }
            else {
              if($mhdohnelager) {
                foreach($mhdohnelager as $k => $v) {
                  if($v['mhddatum'] == $datum) {
                    $mhdohnelager[$k]['gefunden'] = true;
                    $found = true;
                    if($v['smenge'] < $anzahl) {
                      foreach($wawimhd as $k2 => $v2) {
                        if($v2['mhddatum'] == $datum)
                        {
                          $storageChanged += $anzahl - $v2['menge'];
                          $this->app->DB->Update(
                            "UPDATE lager_mindesthaltbarkeitsdatum 
                                        SET menge = $anzahl WHERE id = '".$v2['id']."' LIMIT 1"
                          );
                          if($v2['menge'] < $anzahl) {
                            $this->app->erp->MHDLog(
                              $id, $v2['lager_platz'],1,$v2['mdhdatum'],abs($v2['menge'] < $anzahl),'API Korrektur','',0,$v2['charge']
                            );
                          }
                          elseif($anzahl < $v2['menge']) {
                            $this->app->erp->MHDLog(
                              $id, $v2['lager_platz'],0,$v2['mdhdatum'],abs($v2['menge'] < $anzahl),'API Korrektur','',0,$v2['charge']
                            );
                          }
                          if($charge) {
                            $hinzuf = $anzahl - $v['smenge'];
                            if(!isset($chargenlager[$v2['lager_platz'].'-'.$charge])) {
                              $chargenlager[$v2['lager_platz'].'-'.$charge] = 0;
                            }
                            $chargenlager[$v2['lager_platz'].'-'.$charge] += $hinzuf;
                          }
                          break;
                        }
                      }
                    }
                    elseif($v['smenge'] > $anzahl) {
                      $abzuziehen = $v['smenge'] - $anzahl;
                      foreach($wawimhd as $k2 => $v2) {
                        if($v2['menge'] > 0) {
                          if($abzuziehen > 0) {
                            if($abzuziehen >= $v2['menge']) {
                              $abzuziehen -= $v2['menge'];
                              $storageChanged -= $v2['menge'];
                              $this->app->DB->Delete(
                                "DELETE FROM lager_mindesthaltbarkeitsdatum WHERE id = '".$v2['id']."' LIMIT 1"
                              );
                              $this->app->erp->MHDLog(
                                $id, $v2['lager_platz'],0,$v2['mdhdatum'],$v2['menge'],'API Korrektur','',0,$v2['charge']
                              );
                              if(!isset($chargenlager[$v2['lager_platz'].'-'.$charge])) {
                                $chargenlager[$v2['lager_platz'].'-'.$charge] = 0;
                              }
                              $chargenlager[$v2['lager_platz'].'-'.$charge] -= $v2['menge'];
                            }
                            else {
                              $storageChanged -= $abzuziehen;
                              $this->app->DB->Update(
                                "UPDATE lager_mindesthaltbarkeitsdatum 
                                          SET menge = menge - $abzuziehen WHERE id = '".$v2['id']."' LIMIT 1"
                              );
                              $this->app->erp->MHDLog($id, $v2['lager_platz'],0,$v2['mdhdatum'],$abzuziehen,'API Korrektur','',0,$v2['charge']);
                              if(!isset($chargenlager[$v2['lager_platz'].'-'.$charge])) {
                                $chargenlager[$v2['lager_platz'].'-'.$charge] = 0;
                              }
                              $chargenlager[$v2['lager_platz'].'-'.$charge] -= $abzuziehen;
                              $abzuziehen = 0;
                              break;
                            }
                          }
                        }
                      }
                    }
                  }
                }
                if(!$found && $anzahl > 0) {
                  $this->app->DB->Insert(
                    "INSERT INTO lager_mindesthaltbarkeitsdatum (artikel, datum, mhddatum, menge, lager_platz, charge) 
                            values ('$id',now(),'$datum','$anzahl','$lager_platz_id','".$this->app->DB->real_escape_string($charge)."')");
                  $this->app->erp->MHDLog($id, $lager_platz_id,1,$datum,$anzahl,'API Korrektur','',0,$charge);
                  if(!isset($chargenlager[$lager_platz_id.'-'.$charge])) {
                    $chargenlager[$lager_platz_id.'-'.$charge] = 0;
                  }
                  $chargenlager[$lager_platz_id.'-'.$charge] += $anzahl;
                  $storageChanged += $anzahl;
                }
              }
            }
          }
          elseif($anzahl > 0) {
            $this->app->DB->Insert(
              "INSERT INTO lager_mindesthaltbarkeitsdatum (artikel, datum, mhddatum, menge, lager_platz, charge) 
                        values ('$id',now(),'$datum','$anzahl','$lagerplatz','".$this->app->DB->real_escape_string($charge)."')"
            );
            $this->app->erp->MHDLog($id, $lagerplatz,1,$datum,$anzahl,'API Korrektur','',0,$charge);
            $storageChanged += $anzahl;
            if(!isset($chargenlager[$lagerplatz.'-'.$charge])) {
              $chargenlager[$lagerplatz.'-'.$charge] = 0;
            }
            $chargenlager[$lagerplatz.'-'.$charge] += $anzahl;
          }
          if(isset($chargenlager)) {
            foreach($chargenlager as $kl => $vls)  {
              $vla = explode('-', $vls, 2);
              $vl = $vla[0];
              $charge = '';
              if(isset($vla[1])) {
                $charge = $vla[1];
              }
              if($charge) {
                if($vl < 0) {
                  if($chargenmhd)
                  {
                    $abzuziehen = -$vl;
                    foreach($chargenmhd as $vch) {
                      if($vch['lager_platz'] == $kl && $charge == $vch['charge']) {
                        if($vch['menge'] > $abzuziehen) {
                          $this->app->DB->Update("UPDATE lager_charge SET menge = menge - $abzuziehen WHERE id = '".$vch['id']."' LIMIT 1");
                          $this->app->erp->Chargenlog($id, $vch['lager_platz'],0,$vch['charge'], $abzuziehen,'API Korrektur');
                          $abzuziehen = 0;
                          break;
                        }
                        if($vch['menge'] == $abzuziehen) {
                          $this->app->DB->Delete("DELETE FROM lager_charge id = '".$vch['id']."' LIMIT 1");
                          $this->app->erp->Chargenlog($id, $vch['lager_platz'],0,$vch['charge'], $vch['menge'],'API Korrektur');
                          break;
                        }
                        if($abzuziehen > 0) {
                          $this->app->DB->Delete("DELETE FROM lager_charge id = '".$vch['id']."' LIMIT 1");
                          $this->app->erp->Chargenlog($id, $vch['lager_platz'],0,$vch['charge'], $vch['menge'],'API Korrektur');
                          $abzuziehen -= $vch['menge'];
                        }
                      }
                    }
                  }
                }
                elseif($vl > 0) {
                  $hinzuf = $vl;
                  if($chargenmhd) {
                    foreach($chargenmhd as $vch) {
                      if($vch['lager_platz'] == $kl && $charge == $vch['charge']) {
                        $this->app->DB->Update(
                          "UPDATE lager_charge SET menge = menge + $vl WHERE id = '".$vch['id']."' LIMIT 1"
                        );
                        $this->app->erp->Chargenlog($id, $vch['lager_platz'],1,$vch['charge'], $vl,'API Korrektur');
                        $hinzuf = 0;
                        break;
                      }
                    }
                  }
                  if($hinzuf) {
                    $this->app->DB->Insert("INSERT INTO lager_charge (artikel, lager_platz, menge, charge) values ('$id','$kl','$hinzuf','$charge')");
                    $this->app->erp->Chargenlog($id, $kl,1,$charge, $hinzuf,'API Korrektur');
                  }
                }
              }
            }
          }
        }
        if(isset($wawimhd) && $lagerzahl !== null) {
          if($lager_platz_id) {
            foreach($wawimhd as $k => $v) {
              if(!isset($v['gefunden'])) {
                $storageChanged -= $v['menge'];
                $this->app->DB->Delete(
                  "DELETE FROM lager_mindesthaltbarkeitsdatum WHERE id = '".$v['id']."' LIMIT 1"
                );
                $this->app->erp->MHDLog(
                  $v['artikel'],$v['lager_platz'],0, $v['mhddatum'], $v['menge'],'API Korrektur','',0,$v['charge']
                );
                if(!empty($v['charge'])){
                  $this->app->erp->ChargeAuslagernLog(
                    $v['artikel'],
                    $v['lager_platz'],
                    $v['charge'],
                    $v['menge'],
                    'API Korrektur'
                  );
                }
              }
            }
          }
          else{
            if($mhdohnelager) {
              foreach($mhdohnelager as $k => $v) {
                if(!isset($v['gefunden'])) {
                  $storageChanged -= $v['menge'];
                  $this->app->DB->Delete(
                    "DELETE FROM lager_mindesthaltbarkeitsdatum WHERE artikel = '$id' AND mhddatum = '".$v['mhddatum']."'"
                  );
                  $this->app->erp->MHDLog(
                    $v['artikel'],$v['lager_platz'],0, $v['mhddatum'], $v['menge'],'API Korrektur','',0,$v['charge']
                  );
                  if(!empty($v['charge'])){
                    $this->app->erp->ChargeAuslagernLog(
                      $v['artikel'],$v['lager_platz'],$v['charge'],$v['menge'],'API Korrektur'
                    );
                  }
                }
              }
            }
          }
        }
      }
      elseif(!empty($artArr['chargenverwaltung']) && isset($xml->charge)) {
        if(!$lagerplatz) {
          $lagerplatz = $this->app->DB->Select("SELECT lager_platz FROM artikel WHERE id = '$id' LIMIT 1");
        }
        if(!$lagerplatz) {
          $lagerplatz = $this->app->DB->Select("SELECT lp.id FROM lager_platz_inhalt lpi INNER JOIN lager_platz lp ON lpi.lager_platz = lp.id WHERE lpi.artikel = '$id' AND lp.sperrlager = 0 AND lp.poslager = 0 ORDER by lpi.menge DESC LIMIT 1");
        }
        if(!$lagerplatz) {
          $lagerplatz = $this->app->DB->Select("SELECT lp.id FROM lager_platz lp WHERE lp.sperrlager = 0 AND lp.poslager = 0 ORDER by lp.id ASC LIMIT 1");
        }

        if($lager_platz_id) {
          $wawicharge = $this->app->DB->SelectArr(
            "SELECT * FROM lager_charge WHERE artikel = '$id' AND lager_platz = '$lager_platz_id' order by charge, menge desc"
          );
        }
        else {
          $wawimhd = null;//$this->app->DB->SelectArr("SELECT * FROM lager_mindesthaltbarkeitsdatum WHERE artikel = '$id' order by mhddatum");
          $chargenlager = null;//$this->app->DB->SelectArr("SELECT * FROM lager_charge WHERE artikel = '$id' order by charge, menge desc");
          if($wawimhd) {
            $mhdohnelager = null;//$this->app->DB->SelectArr("SELECT mhddatum, sum(menge) as smenge FROM lager_mindesthaltbarkeitsdatum WHERE artikel = '$id' group by mhddatum order by mhddatum ");
          }
          if($chargenlager) {
            $chargenohnelager = null;//$this->app->DB->SelectArr("SELECT charge, sum(menge) as smenge FROM lager_charge WHERE artikel = '$id' group by charge order by charge ");
          }
        }
        $charge = $xml->charge;
        if(!empty($charge) && !is_string($charge) && !is_numeric($charge) &&  count($charge) === 1 && empty($charge->charge)
          && empty($charge->anzahl)) {
          $charge = reset($charge);
        }
        if(isset($charge->charge)) {
          $charge = json_decode(json_encode($charge->charge), true);
        }
        elseif(is_string($charge)) {
          $charge = [$charge];
        }
        $chargeanzahl = null;
        if(isset($charge->anzahl)) {
          $chargeanzahl = json_decode(json_encode($charge->anzahl),true);
        }
        elseif(isset($xml->chargeanzahl) && is_string(json_decode(json_encode($xml->chargeanzahl),true))) {
          $chargeanzahl = [json_decode(json_encode($xml->chargeanzahl),true)];
        }
        elseif(isset($xml->chargeanzahl) && is_numeric((string)$xml->chargeanzahl)) {
          $chargeanzahl = [(float)(string)$xml->chargeanzahl];
        }
        elseif(isset($xml->lagerzahl)) {
          $chargeanzahl = [(float)(string)$xml->lagerzahl];
        }
        $chargen = $charge;
        foreach($chargen as $kcharge => $c) {
          $charge = '';
          $anzahl = 1;
          if(isset($c->charge)){
            $charge = (string)$c->charge;
          }
          elseif(is_string($c)){
            $charge = (string)$c;
          }
          if(isset($c->anzahl)){
            $anzahl = (int)$c->anzahl;
          }elseif(is_array($charge[$kcharge]) && isset($charge[$kcharge])){
            $anzahl = (int)$charge[$kcharge];
          }
          elseif($chargeanzahl !== null) {
            $anzahl = $chargeanzahl;
            if(is_array($anzahl)) {
              $anzahl = $anzahl[$kcharge];
            }
          }
          $this->bestBeforeBatchArticleStock[$id][$storageLocationId]['stock'] += (float)$anzahl;
          $this->bestBeforeBatchArticleStock[$id][$storageLocationId]['entries'][] =
            [
              'batch' => $charge, 'quantity' => $anzahl
            ];
          $chargenlager = null;
          $found = false;
          $foundcharge = false;
          if($wawicharge){
            if($lager_platz_id){
              foreach ($wawicharge as $k => $v) {
                if($v['charge'] == $charge) {
                  $found = true;
                  $wawicharge[$k]['gefunden'] = true;
                  if($v['menge'] != $anzahl) {
                    $storageChanged += (float)$anzahl - $v['menge'];
                    $this->app->DB->Update(
                      "UPDATE lager_charge SET menge = '$anzahl' WHERE id = '".$v['id']."' LIMIT 1"
                    );
                    $this->app->erp->Chargenlog(
                      $v['artikel'], $v['lager_platz'],$anzahl > $v['menge']?1:0,
                      $v['charge'], abs($v['menge'] - $anzahl),'API Korrektur','',0
                    );
                    break;
                  }
                }
              }
            }
          }
          if(!$found) {
            $this->app->erp->AddChargeLagerOhneBewegung(
              $id, $anzahl,$lager_platz_id,date('Y-m-d'),$charge,'API Korrektur'
            );
            $storageChanged += (float)$anzahl;
          }
          $lagerzahl = null;
        }
      }
      if($lagerzahl === null) {
        if($storageChanged > 0) {
          $this->app->erp->LagerEinlagern(
            $id, $storageChanged, $lagerplatz, $projekt, 'API Korrektur', true
          );
          $this->app->DB->Insert(
            "INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) 
                                VALUES ('$uebertragungen_account','artikel','$id','lagerzahl','$anzahl')"
          );
          $this->lagerzahlenmonitor[$uebertragungen_account][$id] =
            $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'lagerzahlen_ok',
              '', $anzahl, '', '', 'artikel', $id, (isset($this->lagerzahlenmonitor) &&
                isset($this->lagerzahlenmonitor[$uebertragungen_account])
                && isset($this->lagerzahlenmonitor[$uebertragungen_account][$id])) ?
                $this->lagerzahlenmonitor[$uebertragungen_account][$id] : 0
            );
        }
        elseif($storageChanged < 0) {
          $this->app->erp->LagerAuslagernRegal($id,$lagerplatz, -$storageChanged, $projekt, 'API Korrektur', true);
          $this->app->DB->Insert(
            "INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) 
                                VALUES ('$uebertragungen_account','artikel','$id','lagerzahl','$anzahl')"
          );
          $this->lagerzahlenmonitor[$uebertragungen_account][$id] =
            $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'lagerzahlen_ok',
              '', $anzahl, '', '', 'artikel', $id, (isset($this->lagerzahlenmonitor) &&
                isset($this->lagerzahlenmonitor[$uebertragungen_account])
                && isset($this->lagerzahlenmonitor[$uebertragungen_account][$id])) ?
                $this->lagerzahlenmonitor[$uebertragungen_account][$id] : 0
            );
        }
      }
      elseif($storageChanged && (isset($xml->charge) || isset($xml->mhd))) {
        if($storageChanged > 0) {
          $this->app->erp->LagerEinlagern($id, $storageChanged,$lagerplatz,$projekt,'API Korrektur');
          $anzahl = $this->app->DB->Select(
            sprintf(
              'SELECT trim(SUM(menge))+0 FROM lager_platz_inhalt WHERE artikel = %d AND lager_platz = %d ',
              $id, $lagerplatz
            )
          );
          $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'lagerzahlen_ok',
            '', $anzahl, '', '', 'artikel', $id, (isset($this->lagerzahlenmonitor) &&
              isset($this->lagerzahlenmonitor[$uebertragungen_account])
              && isset($this->lagerzahlenmonitor[$uebertragungen_account][$id])) ?
              $this->lagerzahlenmonitor[$uebertragungen_account][$id] : 0
          );
        }
        elseif($storageChanged < 0) {
          $this->app->erp->LagerAuslagernRegal($id,$lagerplatz,abs($storageChanged),$projekt,'API Korrektur');
          $anzahl = $this->app->DB->Select(
            sprintf(
              'SELECT trim(SUM(menge))+0 FROM lager_platz_inhalt WHERE artikel = %d AND lager_platz = %d ',
              $id, $lagerplatz
            )
          );
          $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'lagerzahlen_ok',
            '', $anzahl, '', '', 'artikel', $id, (isset($this->lagerzahlenmonitor) &&
              isset($this->lagerzahlenmonitor[$uebertragungen_account])
              && isset($this->lagerzahlenmonitor[$uebertragungen_account][$id])) ?
              $this->lagerzahlenmonitor[$uebertragungen_account][$id] : 0
          );
        }
      }
    }
  }

    /**
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     */
  public function ParsePartXmlAricleWithOutId(&$xml, $uebertragungen_account, $obj) {
    $parameter1 = '';
    $meldung = 'Artikel ';

    $account = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM uebertragungen_account WHERE id = %d  LIMIT 1',
        $uebertragungen_account
      )
    );
    $artikeleingang = !empty($account['artikeleingang']);
    $lagerzahleneingang = !empty($account['lagerzahleneingang']);
    $updatearticles = !empty($account['updatearticles']);
    $logarticlenotfound = !empty($account['logarticlenotfound']);
    if(!empty($xml->anlegen) && !empty($xml->nummer) && !empty($xml->name_de) && $artikeleingang) {
      $artikelarr = array('projekt'=> $account['projekt']);

      if(!empty($this->app->stringcleaner)) {
        $this->app->stringcleaner->XMLArray_clean($xml);
      }
      foreach($xml as $k => $v) {
        if(empty($k) || $k === 'id' || $k === 'nummer' || is_numeric($k)) {
          continue;
        }
        $vr = @reset($v);
        if(is_numeric($vr) || is_string($vr)) {
          $artikelarr[$k] = (string)$vr;
        }
      }

      if(isset($xml->nummer))$artikelarr['nummer'] = (string)$xml->nummer;
      if(isset($xml->ean))$artikelarr['ean'] = (string)$xml->ean;
      if(isset($xml->herstellernummer))$artikelarr['herstellernummer'] = (string)$xml->herstellernummer;
      if(isset($xml->name_de))$artikelarr['name_de'] = (string)$xml->name_de;
      if(isset($xml->name_en))$artikelarr['name_en'] = (string)$xml->name_en;
      if(isset($xml->anabregstext))$artikelarr['anabregstext'] = (string)$xml->anabregstext;
      if(isset($xml->anabregstext_en))$artikelarr['anabregstext_en'] = (string)$xml->anabregstext_en;
      if(isset($xml->uebersicht_de))$artikelarr['uebersicht_de'] = (string)$xml->uebersicht_de;
      if(isset($xml->uebersicht_en))$artikelarr['uebersicht_en'] = (string)$xml->uebersicht_en;
      if(isset($xml->kurztext_de))$artikelarr['kurztext_de'] = (string)$xml->kurztext_de;
      if(isset($xml->kurztext_en))$artikelarr['kurztext_en'] = (string)$xml->kurztext_en;
      if(isset($xml->lagerartikel))$artikelarr['lagerartikel'] = (string)$xml->lagerartikel;

      if(isset($xml->gewicht))$artikelarr['gewicht'] = (string)$xml->gewicht;
      if(isset($xml->breite))$artikelarr['breite'] = (string)$xml->breite;
      if(isset($xml->laenge))$artikelarr['laenge'] = (string)$xml->laenge;
      if(isset($xml->hoehe))$artikelarr['hoehe'] = (string)$xml->hoehe;

      for($i = 1; $i <= 20; $i++) {
        $name = 'freifeld'.$i;
        if(isset($xml->$name)){
          $xml->$name = $this->app->Secure->CleanString($xml->$name, 'nohtml');
          $artikelarr[$name] = (string)$xml->$name;
        }
      }
      $artikelid = $this->app->erp->InsertUpdateArtikel($artikelarr, $updatearticles);
      if($artikelid) {
        $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'artikel_ok', "Artikel " . (string)$xml->nummer . " angelegt", '', '', '', 'artikel', $artikelid);
      }
      elseif($updatearticles && $logarticlenotfound) {
        $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'artikel_error', 'Artikel ' . (string)$xml->nummer . ' nicht gefunden', '', '', '', 'artikel', 0);
      }
      unset($artikelarr);
    }
    elseif((isset($xml->lagerzahl) || isset($xml->mhdanzahl)) && $lagerzahleneingang) {
      if(isset($xml->nummer)) {
        if($parameter1 == '') {
          $parameter1 = (string)$xml->nummer;
          $meldung .= "mit Artikelnummer ".$parameter1." nicht gefunden";
        }
      }

      if(isset($xml->ean)) {
        if($parameter1 == '') {
          $parameter1 = (string)$xml->ean;
          $meldung .= "mit EAN ".$parameter1." nicht gefunden";
        }
      }
      if(isset($xml->herstellernummer)) {
        if($parameter1 == '') {
          $parameter1 = (string)$xml->herstellernummer;
          $meldung .= "mit Herstellernumemr ".$parameter1." nicht gefunden";
        }
      }
      if($parameter1) {
        $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','artikel','".$this->app->DB->real_escape_string($parameter1)."','lagerzahl_fehler','".$this->app->DB->real_escape_string($meldung)."')");
        $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'lagerzahlen_error', $meldung, $this->app->DB->real_escape_string($parameter1), '', '', 'artikel');
      }
    }
    elseif(isset($xml->anlegen) && $xml->anlegen && isset($xml->nummer) && $xml->nummer != '' && isset($xml->name_de) && $xml->name_de != '' && !$artikeleingang) {
      if(!$this->app->DB->Select("SELECT id FROM `uebertragungen_monitor` WHERE uebertragungen_account = '$uebertragungen_account' AND datei = '".$this->datei_id."' AND status = 'not_allowed' AND zeitstempel > DATE_SUB(now(), INTERVAL 1 HOUR) AND nachricht like 'Artikel-Eingang%'  LIMIT 1")) {
        $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'not_allowed', 'Artikel-Eingang ist nicht aktiviert', '', '', '', 'artikel');
      }
    }
    elseif((isset($xml->lagerzahl) || isset($xml->mhdanzahl)) && !$lagerzahleneingang) {
      if(!$this->app->DB->Select("SELECT id FROM `uebertragungen_monitor` WHERE uebertragungen_account = '$uebertragungen_account' AND datei = '".$this->datei_id."' AND status = 'not_allowed' AND zeitstempel > DATE_SUB(now(), INTERVAL 1 HOUR) AND nachricht like 'Lagerzahlen-Eingang%'  LIMIT 1")) {
        $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'not_allowed', 'Lagerzahlen-Eingang ist nicht aktiviert', '', '', '', 'artikel');
      }
    }
  }

    /**
     * @param string         $typ
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     */
    public function ParsePartXmlType($typ, &$xml, $uebertragungen_account, $obj)
    {
      $id = $this->GetIDFromFeld($typ, $xml);
      if($id) {
        switch($typ)  {
          case 'artikel':
            $this->ParsePartXmlAricleWithId($xml, $uebertragungen_account, $obj, $id);
            break;
        }
      }
      else {
        switch($typ) {
          case 'artikel':
            $this->ParsePartXmlAricleWithOutId($xml, $uebertragungen_account, $obj);
            break;
        }
      }
    }

    /**
     * @param string         $typ
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     */
    public function ParsePartXmlSupplierorderProductionWithoutId($typ, &$xml, $uebertragungen_account, $obj)
    {
      $isProduction = $typ === 'produktion';
      $lieferantok = false;
      $neuerlieferant = false;
      $adresselieferant = $this->app->DB->Select("SELECT adresselieferant FROM uebertragungen_account WHERE id = '".$uebertragungen_account."' LIMIT 1");
      if($adresselieferant && $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer <> '' AND geloescht = 0 AND id = '$adresselieferant' LIMIT 1"))
      {
        $adresse = $adresselieferant;
        $lieferantok = true;
      }else{
        if((empty($xml->lieferantennummer) && empty($xml->gln) ) || strtoupper((string)$xml->lieferantennummer) === 'NEW' || strtoupper((string)$xml->lieferantennummer) === 'NEU') {
          if(isset($xml->name))
          {
            $lieferantok = true;
            $neuerlieferant = true;
          }else{
            $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','betellung','".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."','bestellung_fehler','Kein Lieferantenname angegeben')");
          }
        }else{
          $adresse = null;
          if(!empty($xml->adresse)){
            $adresse = $this->GetFromExtID('adresse', $xml->adresse);
          }
          if(!empty($adresse)){
            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE id = '$adresse' AND geloescht <> 1 AND lieferantennummer <> '' LIMIT 1");
          }
          if(empty($adresse) && !empty($xml->gln)){
            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE geloescht <> 1 AND gln <> '' AND gln = '".$this->app->DB->real_escape_string((string)$xml->gln)."' LIMIT 1");
          }
          //if(!$adresse && !empty($xml->lieferantennummer))$adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE geloescht <> 1 AND lieferantennummer <> '' AND lieferantennummer = '".$this->app->DB->real_escape_string((string)$xml->lieferantennummer)."' LIMIT 1");
          if(empty($adresse) && !empty($xml->kundennummerlieferant))
          {
            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE geloescht <> 1 AND lieferantennummer <> '' AND lieferantennummer = '".$this->app->DB->real_escape_string((string)$xml->kundennummerlieferant)."' LIMIT 1");
          }
          if(empty($adresse) && !empty($xml->adresse))
          {
            $lieferantok = true;
            $neuerlieferant = true;
          }
          if(!empty($adresse)){
            $lieferantok = true;
          }
        }
      }
      if($lieferantok)
      {
        $bestellung_positionen = null;
        if(isset($xml->bestellung_position_list))
        {
          if(isset($xml->bestellung_position_list->bestellung_position))
          {
            $bestellung_positionen = &$xml->bestellung_position_list->bestellung_position;
          }

        }elseif(isset($xml->bestellung_position)){
          $bestellung_positionen = &$xml->bestellung_position;
        }

        if($bestellung_positionen) {
          $artikelgefunden = true;
          $artikelids = null;
          $key = -1;
          foreach($bestellung_positionen as $position)  {
            $key++;
            if($artikelgefunden)
            {
              $_element1 = '';
              $_element2 = '';
              $_element3 = '';
            }
            $_artikel = null;
            if(!empty($position->artikel))
            {
              $_artikel = $this->GetFromExtID('artikel', $xml->artikel);
              if($_artikel)
              {
                $artikelids[$key] = $_artikel;
                //$xml->artikelextid = $_artikel;
              }
            }
            if(!$_artikel && !empty($position->ean))
            {
              $_artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND ean <> '' AND ean = '".$this->app->DB->real_escape_string((string)$position->ean)."' ORDER BY projekt = '$projekt' DESC LIMIT 1");
              if(!$_artikel)
              {
                $_element1 = 'EAN: '.$position->ean;
              }else {
                $artikelids[$key] = $_artikel;
              }
            }
            if(!$_artikel && !empty($position->herstellernummer))
            {
              $_artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND herstellernummer <> '' AND herstellernummer = '".$this->app->DB->real_escape_string((string)$position->herstellernummer)."' ORDER BY projekt = '$projekt' DESC  LIMIT 1");
              if(!$_artikel)
              {
                if($_element1 == '')
                {
                  $_element1 = 'Herstellernummer: '.$position->herstellernummer;
                }else{
                  $_element2 = 'Herstellernummer: '.$position->herstellernummer;
                }
              }else {
                $artikelids[$key] = $_artikel;
              }
            }
            if(!$_artikel && !empty($position->bestellnummer))
            {
              $_artikel = $this->app->DB->Select("SELECT e.artikel FROM `einkaufspreise` e INNER JOIN artikel a ON e.artikel = a.id WHERE e.bestellnummer <> '' AND e.bestellnummer = '".$this->app->DB->real_escape_string((string)$position->bestellnummer)."' ORDER BY a.projekt = '$projekt' DESC  LIMIT 1");
              if(!$_artikel){
                $_artikel = $this->app->DB->Select("SELECT id FROM `artikel` WHERE geloescht <> 1 AND nummer <> '' AND nummer = '".$this->app->DB->real_escape_string((string)$position->bestellnummer)."' ORDER BY projekt = '$projekt' DESC  LIMIT 1");
              }
              if(!$_artikel)
              {
                if($_element1 == '')
                {
                  $_element1 = 'Bestellernummer: '.$position->bestellnummer;
                }elseif($_element2 == ''){
                  $_element2 = 'Bestellernummer: '.$position->bestellnummer;
                }else{
                  $_element3 = 'Bestellernummer: '.$position->bestellnummer;
                }
              }else {
                $artikelids[$key] = $_artikel;
              }
            }
            if(!$_artikel && !$this->app->DB->Select("SELECT artikelanlegen FROM uebertragungen_account WHERE id = '".$uebertragungen_account."' LIMIT 1")){
              $artikelgefunden = false;
            }
            if(!$_artikel) {
              if(!empty($position->bestellnummer))
              {
                if(!$this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND nummer <> '' AND nummer = '".$this->app->DB->real_escape_string((string)$position->bestellnummer)."' LIMIT 1"))
                {
                  $artikelgefunden = false;
                  $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','bestellung','".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."','bestellung_fehler','Artikelnummer ".$this->app->DB->real_escape_string((string)$position->nummer)." nicht gefunden')");
                  break;
                }
              }elseif(!empty($position->ean)){
                if(!$this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND ean <> '' AND ean = '".$this->app->DB->real_escape_string((string)$position->ean)."' LIMIT 1"))
                {
                  $artikelgefunden = false;
                  $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','bestellung','".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."','bestellung_fehler','EAN ".$this->app->DB->real_escape_string((string)$position->ean)." nicht gefunden')");
                  break;
                }
              }else{
                $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','bestellung','".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."','bestellung_fehler','Keine Artikelnummer gefunden')");
                break;
              }
            }
          }
          if($artikelgefunden)
          {
            $_typ ='';
            $name = '';
            $abteilung = '';
            $unterabteilung = '';
            $ansprechpartner = '';
            $adresszusatz = '';
            $strasse = '';
            $land = $this->app->erp->Firmendaten('land');
            $plz = '';
            $ort = '';
            $email = '';
            $telefon = '';
            $telefax = '';
            $ustid = '';
            $partner = '';
            $bundesstaat = '';
            $projekt = $this->app->DB->Select("SELECT projekt FROM uebertragungen_account WHERE id = '".$uebertragungen_account."' LIMIT 1");
            if(!empty($this->app->stringcleaner)) {
              $this->app->stringcleaner->XMLArray_clean($xml);
            }

            if(isset($xml->name))$name = (string)$xml->name;
            if(isset($xml->anrede))$_typ = (string)$xml->anrede;
            if(isset($xml->abteilung))$abteilung = (string)$xml->abteilung;
            if(isset($xml->unterabteilung))$unterabteilung = (string)$xml->unterabteilung;
            if(isset($xml->ansprechpartner))$ansprechpartner = (string)$xml->ansprechpartner;
            if(isset($xml->adresszusatz))$adresszusatz = (string)$xml->adresszusatz;
            if(isset($xml->strasse))$strasse = (string)$xml->strasse;
            if(isset($xml->land))$land = (string)$xml->land;
            if(isset($xml->bundesstaat))$bundesstaat = (string)$xml->bundesstaat;
            if(isset($xml->plz))$plz = (string)$xml->plz;
            if(isset($xml->ort))$ort = (string)$xml->ort;
            if(isset($xml->email))$email = (string)$xml->email;
            if(isset($xml->telefon))$telefon = (string)$xml->telefon;
            if(isset($xml->telefax))$telefax = (string)$xml->telefax;
            if(isset($xml->ustid))$ustid = (string)$xml->ustid;
            if(isset($xml->partner))$partner = (string)$xml->partner;
            if(isset($xml->projekt) && (!isset($xml->belegnr) || $xml->belegnr == '' || strtoupper($xml->belegnr) === 'NEW' || strtoupper($xml->belegnr) === 'NEU')) {
              if((string)$xml->projekt != '')
              {
                $_projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE geloescht = 0 AND abkuerzung = '".$this->app->DB->real_escape_string((string)$xml->projekt)."' LIMIT 1");
                if($_projekt)$projekt = $_projekt;
              }
              if($land == '')$land = $this->app->erp->Projektdaten($projekt ,'land');
            }
            if($land == '')$land = $this->app->erp->Firmendaten('land');
            if($neuerlieferant)
            {
              $adressearr = array('lieferantennummer'=>$this->app->erp->GetNextNummer('lieferantennummer',$projekt));
              $adressearr['typ'] = $_typ;
              $adressearr['name'] = $name;
              $adressearr['abteilung'] = $abteilung;
              $adressearr['unterabteilung'] = $unterabteilung;
              $adressearr['ansprechpartner'] = $ansprechpartner;
              $adressearr['adresszusatz'] = $adresszusatz;
              $adressearr['land'] = $land;
              $adressearr['plz'] = $plz;
              $adressearr['ort'] = $ort;
              $adressearr['email'] = $email;
              $adressearr['telefon'] = $telefon;
              $adressearr['telefax'] = $telefax;
              $adressearr['ustid'] = $ustid;
              $adressearr['projekt'] = $projekt;
              $adressearr['bundesstaat'] = $bundesstaat;
              $adresse = $this->app->erp->InsertUpdateAdresse($adressearr);
              if(isset($xml->adresse) && $xml->adresse != '') {
                $this->SetExtIDMapping('adresse', $adresse, $xml->adresse);
              }
              unset($adressearr);
            }
            if($adresse)
            {
              if($isProduction) {
                $bestellung = $this->app->erp->CreateProduktion();
                $belegnr = (string)$this->app->DB->Select("SELECT belegnr FROM produktion WHERE id = '$bestellung' LIMIT 1");
                if($belegnr === '' || $belegnr === '0'){
                  $belegnr = $this->app->erp->GetNextNummer('produktion',$projekt,$bestellung);
                }
              }
              else{
                $bestellung = $this->app->erp->CreateBestellung();
                $belegnr = (string)$this->app->DB->Select("SELECT belegnr FROM bestellung WHERE id = '$bestellung' LIMIT 1");
                if($belegnr === '' || $belegnr === '0'){
                  $belegnr = $this->app->erp->GetNextNummer('bestellung',$projekt,$bestellung);
                }
              }


              if(isset($xml->belegnr) && $xml->belegnr != '' && strtoupper($xml->belegnr) !== 'NEW' && strtoupper($xml->belegnr) !== 'NEU'){
                $this->SetExtIDMapping($isProduction?'produktion':'bestellung', $bestellung, $xml->belegnr);
              }
              $this->app->erp->LoadBestellungStandardwerte($bestellung,$adresse);

              $auftragarr = null;
              $auftragarr['belegnr'] = $belegnr;
              $auftragarr['projekt'] = $projekt;
              if($_typ)$auftragarr['typ'] = $_typ;
              $auftragarr['name'] = $name;
              $auftragarr['abteilung'] = $abteilung;
              $auftragarr['unterabteilung'] = $unterabteilung;
              $auftragarr['ansprechpartner'] = $ansprechpartner;
              $auftragarr['strasse'] = $strasse;
              $auftragarr['bundesstaat'] = $bundesstaat;
              $auftragarr['land'] = $land;
              $auftragarr['plz'] = $plz;
              $auftragarr['ort'] = $ort;
              $auftragarr['email'] = $email;
              $auftragarr['telefon'] = $telefon;
              $auftragarr['telefax'] = $telefax;
              $auftragarr['ustid'] = $ustid;
              if($isProduction && isset($xml->unterlistenexplodieren)) {
                $auftragarr['unterlistenexplodieren'] = (string)$xml->unterlistenexplodieren;
              }
              if(isset($xml->abweichendelieferadresse))$auftragarr['abweichendelieferadresse'] = (string)$xml->abweichendelieferadresse;
              if(isset($xml->bestellungsart))$auftragarr['bestellungsart'] = (string)$xml->bestellungsart;
              if(isset($xml->bearbeiter))$auftragarr['bearbeiter'] = (string)$xml->bearbeiter;
              if(isset($xml->datum))$auftragarr['datum'] = (string)$xml->datum;
              if(isset($xml->lieferdatum))$auftragarr['lieferdatum'] = (string)$xml->lieferdatum;
              foreach(['datum', 'lieferdatum'] as $dateField) {
                if(!empty($auftragarr[$dateField]) && strpos($auftragarr[$dateField], '.') !== false) {
                  $auftragarr[$dateField] = $this->app->String->Convert($auftragarr[$dateField], '%1.%2.%3', '%3-%2-%1');
                }
              }
              if(isset($xml->ustid))$auftragarr['ustid'] = (string)$xml->ustid;
              if(isset($xml->ust_befreit))$auftragarr['ust_befreit'] = (string)$xml->ust_befreit;
              if(isset($xml->internet))$auftragarr['internet'] = (string)$xml->internet;
              if(isset($xml->transaktionsnummer))$auftragarr['transaktionsnummer'] = (string)$xml->transaktionsnummer;
              if(isset($xml->versandart))$auftragarr['versandart'] = (string)$xml->versandart;
              if(isset($xml->vertrieb))$auftragarr['vertrieb'] = (string)$xml->vertrieb;
              if(isset($xml->zahlungsweise))$auftragarr['zahlungsweise'] = (string)$xml->zahlungsweise;
              if(isset($xml->freitext))$auftragarr['freitext'] = (string)$xml->freitext;
              if(isset($xml->bank_inhaber))$auftragarr['bank_inhaber'] = (string)$xml->bank_inhaber;
              if(isset($xml->bank_institut))$auftragarr['bank_institut'] = (string)$xml->bank_institut;
              if(isset($xml->bank_blz))$auftragarr['bank_blz'] = (string)$xml->bank_blz;
              if(isset($xml->bank_konto))$auftragarr['bank_konto'] = (string)$xml->bank_konto;
              if(isset($xml->ansprechpartner))$auftragarr['ansprechpartner'] = (string)$xml->ansprechpartner;
              if(isset($xml->liefername))$auftragarr['liefername'] = (string)$xml->liefername;
              if(isset($xml->lieferland))$auftragarr['lieferland'] = (string)$xml->lieferland;
              if(isset($xml->lieferstrasse))$auftragarr['lieferstrasse'] = (string)$xml->lieferstrasse;
              if(isset($xml->lieferabteilung))$auftragarr['lieferabteilung'] = (string)$xml->lieferabteilung;
              if(isset($xml->lieferunterabteilung))$auftragarr['lieferunterabteilung'] = (string)$xml->lieferunterabteilung;
              if(isset($xml->lieferansprechpartner))$auftragarr['lieferansprechpartner'] = (string)$xml->lieferansprechpartner;
              if(isset($xml->lieferort))$auftragarr['lieferort'] = (string)$xml->lieferort;
              if(isset($xml->lieferbundesstaat))$auftragarr['lieferbundesstaat'] = (string)$xml->lieferbundesstaat;
              if(isset($xml->lieferplz))$auftragarr['lieferplz'] = (string)$xml->lieferplz;
              if(isset($xml->lieferadresszusatz))$auftragarr['lieferadresszusatz'] = (string)$xml->lieferadresszusatz;

              if(isset($xml->internebemerkung))$auftragarr['internebemerkung'] = (string)$xml->internebemerkung;
              if(isset($xml->internebezeichnung))$auftragarr['internebezeichnung'] = (string)$xml->internebezeichnung;

              $auftragarr['status']='freigegeben';
              $auftragarr['projekt']=$projekt;
              $auftragarr['zahlungszielskonto']=0;
              if(isset($xml->gesamtsumme))$auftragarr['gesamtsumme'] = (float)str_replace(',','.',$xml->gesamtsumme);
              $auftragarr = $this->formatDateFieldsToMysqlFormat($auftragarr);
              $this->app->DB->UpdateArr($isProduction ? 'produktion' : 'bestellung', $bestellung, 'id', $auftragarr, true);
              $key = -1;
              foreach($bestellung_positionen as $position)
              {
                $key++;
                $artikel = null;
                if(!empty($artikelids) && isset($artikelids[$key])) {
                  $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND id = '".$artikelids[$key]."' LIMIT 1");
                  if($artikel)
                  {
                    if($isProduction) {
                      $newposid = $this->app->erp->AddArtikelProduktion($artikel, isset($position->menge) ? $position->menge : 1, $bestellung);
                      if(isset($position->preis) && $position->preis != 0) {
                        $this->app->DB->Update("UPDATE produktion_position SET preis = '".(float)$position->preis."' WHERE id = '$newposid'");
                      }
                      if(isset($position->vpe) && $position->vpe != '') {
                        $this->app->DB->Update("UPDATE produktion_position SET vpe = '".(string)$position->vpe."' WHERE id = '$newposid'");
                      }
                    }
                    else{
                      $newposid = $this->app->erp->AddBestellungPosition($bestellung, 0, (isset($position->menge) ? $position->menge : 1), isset($auftragarr['datum']) ? $auftragarr['datum'] : date('Y-m-d'),
                        ((isset($position->beschreibung) && $position->beschreibung != '') ? (string)$position->beschreibung : ''), $artikel,
                        ((isset($position->einheit) && $position->einheit != '') ? (string)$position->einheit : ''),
                        ((isset($position->waehrung) && $position->waehrung != '') ? (string)$position->waehrung : '')
                      );
                      if(isset($position->preis) && $position->preis != 0) {
                        $this->app->DB->Update("UPDATE bestellung_position SET preis = '".(float)$position->preis."' WHERE id = '$newposid'");
                      }
                      if(isset($position->bestellnummer) && $position->bestellnummer != '') {
                        $this->app->DB->Update("UPDATE bestellung_position SET bestellnummer = '".(string)$position->bestellnummer."' WHERE id = '$newposid'");
                      }
                      if(isset($position->vpe) && $position->vpe != '') {
                        $this->app->DB->Update("UPDATE bestellung_position SET vpe = '".(string)$position->vpe."' WHERE id = '$newposid'");
                      }
                    }
                    /*$newposid = $this->app->erp->AddPositionManuellPreisNummer('bestellung',$bestellung, $projekt, $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$artikel' LIMIT 1"),
                    (isset($position->menge)?$position->menge:1) ,
                    (isset($position->name)? $position->name:$this->app->DB->Select("SELECT name_de FROM artikel WHERE id = '$artikel' LIMIT 1")),
                       isset($position->preis)? str_replace(',','.',$position->preis):$this->app->erp->GetEinkaufspreis($artikel,(isset($position->menge)?$position->menge:1),$adresse) ,
                       $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '$artikel' LIMIT 1") ,
                       0,0,
                       isset($xml->waehrung)?$xml->waehrung:'EUR');              */
                  }
                }
                if(isset($position->bestellnummer) && !$artikel)
                {
                  //$artikel = $this->app->DB->Select("SELECT e.artikel FROM `einkaufspreise` e INNER JOIN artikel a ON e.artikel = a.id WHERE e.bestellnummer <> '' AND e.bestellnummer = '".$this->app->DB->real_escape_string((string)$position->bestellnummer)."' ORDER BY a.projekt = '$projekt' DESC  LIMIT 1");
                  $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND nummer <> '' AND nummer = '".$this->app->DB->real_escape_string((string)$position->bestellnummer)."' LIMIT 1");
                  if($isProduction) {
                    if($artikel) {
                      $newposid = $this->app->erp->AddArtikelProduktion($artikel, isset($position->menge) ? $position->menge : 1, $bestellung);
                    }
                  } else{
                    $newposid = $this->app->erp->AddPositionManuellPreisNummer('bestellung', $bestellung, $projekt, $position->nummer,
                      (isset($position->menge) ? $position->menge : 1),
                      (isset($position->name) ? $position->name : $this->app->DB->Select("SELECT name_de FROM artikel WHERE id = '$artikel' LIMIT 1")),
                      isset($position->preis) ? str_replace(',', '.', $position->preis) : $this->app->erp->GetEinkaufspreis($artikel, (isset($position->menge) ? $position->menge : 1), $adresse),
                      $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '$artikel' LIMIT 1"),
                      0, 0,
                      isset($xml->waehrung) ? $xml->waehrung : 'EUR');
                  }
                }
                if(!empty($position->ean) && !$artikel)
                {
                  $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND ean <> '' AND ean = '".$this->app->DB->real_escape_string((string)$position->ean)."' LIMIT 1");
                  if($artikel)
                  {
                    if($isProduction) {
                      $newposid = $this->app->erp->AddArtikelProduktion($artikel, isset($position->menge) ? $position->menge : 1, $bestellung);
                    }
                    else{
                      $newposid = $this->app->erp->AddPositionManuellPreisNummer('bestellung', $bestellung, $projekt, $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$artikel' LIMIT 1"),
                        (isset($position->menge) ? $position->menge : 1),
                        (isset($position->name) ? $position->name : $this->app->DB->Select("SELECT name_de FROM artikel WHERE id = '$artikel' LIMIT 1")),
                        isset($position->preis) ? str_replace(',', '.', $position->preis) : $this->app->erp->GetEinkaufspreis($artikel, (isset($position->menge) ? $position->menge : 1), $adresse),
                        $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '$artikel' LIMIT 1"),
                        0, 0,
                        isset($xml->waehrung) ? $xml->waehrung : 'EUR');
                    }
                  }

                }
                if(!empty($position->herstellernummer) && !$artikel)
                {
                  $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND herstellernummer <> '' AND herstellernummer = '".$this->app->DB->real_escape_string((string)$position->herstellernummer)."' LIMIT 1");
                  if($artikel) {
                    if($isProduction) {
                      $newposid = $this->app->erp->AddArtikelProduktion($artikel, isset($position->menge) ? $position->menge : 1, $bestellung);
                    } else{
                      $newposid = $this->app->erp->AddPositionManuellPreisNummer('bestellung', $bestellung, $projekt, $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$artikel' LIMIT 1"),
                        (isset($position->menge) ? $position->menge : 1),
                        (isset($position->name) ? $position->name : $this->app->DB->Select("SELECT name_de FROM artikel WHERE id = '$artikel' LIMIT 1")),
                        isset($position->preis) ? str_replace(',', '.', $position->preis) : $this->app->erp->GetEinkaufspreis($artikel, (isset($position->menge) ? $position->menge : 1), $adresse),
                        $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '$artikel' LIMIT 1"),
                        0, 0,
                        isset($xml->waehrung) ? $xml->waehrung : 'EUR');
                    }
                  }

                }

                /*if($newposid > 0)
                {
                  $this->app->erp->RunHook("beleg_afterinsertposition", 5, "auftrag",$auftrag,$artikel,(isset($position->menge)?$position->menge:1),$newposid);
                }*/
              }
              if($isProduction) {
                $this->app->erp->AuftragExplodieren($bestellung, 'produktion');
                $this->app->erp->ProduktionEinzelnBerechnen($bestellung);
                $this->app->erp->ProduktionNeuberechnen($bestellung);
              }
              else{
                $this->app->erp->BestellungNeuberechnen($bestellung);
              }

              if(isset($xml->dateien)) {
                foreach($xml->dateien as $datei) {
                  foreach($datei as $singledatei) {
                    $isfile = false;
                    $data = null;
                    if(is_file($singledatei->dateiname)){
                      $type = mime_content_type(basename($singledatei->dateiname));
                      if($type !== 'text/x-php' && $type !== 'text/php' && $type !== 'application/php' && $type !== 'application/x-php' && $type !== 'application/x-httpd-php' && $type !== 'application/x-httpd-php-source'){

                        if($singledatei->dateiinhalt == ''){
                          $data = file_get_contents($singledatei->dateiname);
                          $isfile = true;
                        }
                      }else{
                        $singledatei->dateiinhalt = '';
                      }
                    }
                    if(empty($data)){
                      if(!empty($singledatei->dateiinhalt)){
                        $data = base64_decode($singledatei->dateiinhalt);
                      }else{
                        $data = '';
                      }
                    }
                    if($isfile){
                      $name = $this->app->erp->GetTmp().basename($singledatei->dateiname);
                    }else{
                      $name = $this->app->erp->GetTmp().$singledatei->dateiname;
                    }

                    file_put_contents($name, $data);

                    $pfad = $this->app->Conf->WFuserdata;
                    $pfad = rtrim($pfad);
                    $pfad .= '/dms/';

                    if(!file_exists($pfad)){
                      if(!mkdir($pfad, 0777, true) && !is_dir($pfad))
                      {
                        $this->app->erp->LogFile($pfad.' konnte nicht erstellt werden');
                      }
                    }

                    $speicherpfad = $pfad.$this->app->Conf->WFdbname;

                    if(!file_exists($speicherpfad)) {
                      if(!mkdir($speicherpfad, 0777, true) && !is_dir($speicherpfad))
                      {
                        $this->app->erp->LogFile($speicherpfad.' konnte nicht erstellt werden');
                      }
                    }

                    $fileid = $this->app->erp->CreateDatei(
                      $singledatei->dateiname,
                      !empty($singledatei->titel)?$singledatei->titel:$singledatei->dateiname,
                      !empty($singledatei->beschreibung)?(string)$singledatei->beschreibung:'', '',
                      $name, '',true,$speicherpfad
                    );
                    $subjekt = !empty($singledatei->subjekt)?(string)$singledatei->subjekt:'Sonstige';
                    $this->app->erp->AddDateiStichwort($fileid, $subjekt, $isProduction?'Produktion': 'Bestellung', $bestellung);

                    if($isfile){
                      rename($singledatei->dateiname, $singledatei->dateiname.'.del');
                    }
                  }
                }
              }
              $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, $isProduction?'produktion':'bestellung_ok', '', '', '', '', $isProduction?'produktion': 'bestellung', $bestellung);
              $doctype = $isProduction?'produktion': 'bestellung';
              $this->app->erp->RunHook('transfer_document_incoming', 3, $uebertragungen_account, $doctype, $bestellung);
            }
          }
          else {
            $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'bestellung_error', 'Artikel nicht gefunden', $this->app->DB->real_escape_string((string)$_element1), $this->app->DB->real_escape_string((string)$_element2), $this->app->DB->real_escape_string((string)$_element3), 'bestellung');
          }
        }
        else{
          $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'bestellung_error', 'Keine Artikel gefunden', $this->app->DB->real_escape_string(!empty($xml->belegnr)?(string)$xml->belegnr:''), '', '', 'bestellung');
          $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','bestellung','".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."','bestellung_fehler','Keine Positionen gefunden')");
        }
      }
      else{
        $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'bestellung_error', 'Lieferant gefunden', $this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid), '', '', 'bestellung');
        $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','bestellung','".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."','bestellung_fehler','Lieferantennummer ".$this->app->DB->real_escape_string((string)$xml->kundennummer)." nicht gefunden')");
      }
    }

    /**
     * @param array $documentArray
     *
     * @return array
     */
    public function formatDateFieldsToMysqlFormat($documentArray)
    {
      if(!is_array($documentArray)) {
        return $documentArray;
      }
      $dateCols = ['datum', 'lieferdatum', 'tatsaechlicheslieferdatum', ];
      foreach($documentArray as $key => $documentValue) {
        if(!is_string($documentValue)) {
          continue;
        }
        if(!in_array($key, $dateCols)) {
          continue;
        }
        $documentValue = substr($documentValue, 0, 10);
        if(strpos($documentValue, '.') === false) {
          continue;
        }
        $documentArray[$key] = $this->app->String->Convert($documentValue, '%1.%2.%3', '%3-%2-%1');
      }

      return $documentArray;
    }

    /**
     * @param string         $typ
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     */
    public function ParsePartXmlOrderOfferWithoutId($typ, &$xml, $uebertragungen_account, $obj)
    {
      $transferAccount = $this->app->DB->SelectRow(
        sprintf(
          'SELECT `projekt`, `createarticleifnotexists`, `createarticleasstoragearticle` 
          FROM `uebertragungen_account` WHERE `id` = %d LIMIT 1',
          $uebertragungen_account
        )
      );
      $projekt = $transferAccount['projekt'];
      $createArticleIfNotEmpty = !empty($transferAccount['createarticleifnotexists']);
      $markAsStorageArticle = !empty($transferAccount['createarticleasstoragearticle']);
      if(!$this->app->DB->Select("SELECT id FROM $typ WHERE shopextid = '".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."' AND projekt = '$projekt' AND shopextid <> ''")
        && (!isset($xml->belegnr) || strtoupper($xml->belegnr) == 'NEW' || strtoupper($xml->belegnr) == 'NEU' || !$this->GetFromExtID($typ, $xml->belegnr)))
      {
        $auftragarr = [];
        $order = null;
        $addressFromDocument = null;
        if($typ === 'retoure') {
          if(isset($xml->auftragid)) {
            $orderId = (string)$xml->auftragid;
            if(!empty($orderId)) {
              $order = $this->app->DB->SelectRow(
                sprintf(
                  'SELECT `id`, `belegnr`, `adresse` FROM `auftrag` WHERE `id` = %d',
                  $orderId
                )
              );
              if(!empty($order)) {
                $auftragarr['auftragid'] = $order['id'];
                $auftragarr['auftrag'] = $order['belegnr'];
                $addressFromDocument = $order['adresse'];
              }
            }
          }
          if(isset($xml->auftrag) && empty($auftragarr['auftragid'])) {
            $orderNumber = (string)$xml->auftrag;
            if(!empty($orderNumber)) {
              $order = $this->app->DB->SelectRow(
                sprintf(
                  "SELECT `belegnr`, `id`, `adresse` 
                  FROM `auftrag` WHERE `belegnr` = '%s' 
                  ORDER BY `projekt` = %d DESC
                  LIMIT 1",
                  $orderNumber, $projekt
                )
              );
              if(!empty($order['belegnr'])) {
                $auftragarr['auftrag'] = $order['belegnr'];
                $auftragarr['auftragid'] = $order['id'];
                $addressFromDocument = $order['adresse'];
              }
            }
          }
          if(isset($xml->lieferscheinid)) {
            $deliveryNoteId = (string)$xml->lieferscheinid;
            if(!empty($deliveryNoteId)) {
              $deliveryNote = $this->app->DB->SelectRow(
                sprintf(
                  'SELECT `id`, `belegnr`, `adresse` FROM `lieferschein` WHERE `id` = %d',
                  $deliveryNoteId
                )
              );
              if(!empty($deliveryNote)) {
                $auftragarr['lieferscheinid'] = $deliveryNote['id'];
                $auftragarr['lieferschein'] = $deliveryNote['belegnr'];
                if(empty($addressFromDocument)) {
                  $addressFromDocument = $deliveryNote['adresse'];
                }
              }
            }
          }
          if(isset($xml->lieferschein)) {
            $deliveryNoteNumber = (string)$xml->lieferschein;
            if(!empty($deliveryNoteNumber)) {
              $deliveryNote = $this->app->DB->SelectRow(
                sprintf(
                  "SELECT `belegnr`, `id`, `adresse` 
                  FROM `lieferschein` WHERE `belegnr` = '%s' 
                  ORDER BY `projekt` = %d DESC
                  LIMIT 1",
                  $deliveryNoteNumber, $projekt
                )
              );
              if(!empty($deliveryNote['belegnr'])) {
                $auftragarr['lieferschein'] = $deliveryNote['belegnr'];
                $auftragarr['lieferscheinid'] = $deliveryNote['id'];
                if(empty($addressFromDocument)) {
                  $addressFromDocument = $deliveryNote['adresse'];
                }
              }
            }
          }
        }
        if((int)$addressFromDocument > 0) {
          $addressFromDocument = $this->app->DB->Select(
            sprintf(
              'SELECT `id` FROM `adresse` WHERE `id` = %d AND `geloescht` = 0',
              $addressFromDocument
            )
          );
        }
        //if(true)
        //{
        $kundeok = false;
        $neuerkunde = false;
        if(isset($xml->kundennummer) && ( strtoupper((string)$xml->kundennummer) == 'NEW' || strtoupper((string)$xml->kundennummer) == 'NEU') && isset($xml->name))
        {
          $neuerkunde = true;
          $kundeok = true;
        }
        elseif(isset($xml->kundennummer) && ( strtoupper((string)$xml->kundennummer) == 'NEW' || strtoupper((string)$xml->kundennummer) == 'NEU')){
          $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, $typ.'_error', "Auftrag enth&auml;lt keinen Namen", $this->app->DB->real_escape_string((string)$xml->extid), '', '', $typ);
        }
        elseif((empty($xml->gln) )){
          if(isset($xml->name))
          {
            if($this->app->DB->Select("SELECT kundennummernuebernehmen FROM uebertragungen_account WHERE id = '".$uebertragungen_account."' LIMIT 1") && isset($xml->kundennummer) && $xml->kundennummer != '')
            {
              $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer <> '' AND kundennummer = '".$this->app->DB->real_escape_string((string)$xml->kundennummer)."' AND ifnull(geloescht,0) = 0 ORDER BY projekt = '$projekt' DESC LIMIT 1");
            }else {
              $adresse = null;
            }
            if(empty($adresse))
            {
              if(isset($xml->plz) && isset($xml->strasse) && isset($xml->ort) && isset($xml->email))
              {
                $adresseprojekt = $this->app->DB->Select("SELECT projekt FROM uebertragungen_account WHERE id = '$uebertragungen_account' LIMIT 1");
                if($adresseprojekt)
                {
                  $adresseprojekt = " and projekt = '$adresseprojekt' ";
                }else{
                  $adresseprojekt = '';
                }
                $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE name='".$this->app->DB->real_escape_string((string)$xml->name)."' AND email='".$this->app->DB->real_escape_string((string)$xml->email)."' 
                          AND strasse='".$this->app->DB->real_escape_string((string)$xml->strasse)."' AND plz='".$this->app->DB->real_escape_string((string)$xml->plz)."' AND ort='".$this->app->DB->real_escape_string((string)$xml->ort)."' AND kundennummer <> '' AND geloescht!=1 $adresseprojekt LIMIT 1");
                $neuerkunde = true;
                if($adresse)
                {
                  $neuerkunde = false;
                }
              }else{
                $neuerkunde = true;
              }
              $kundeok = true;
            }else{
              $neuerkunde = false;
              $kundeok = true;
            }
          }
          else{
            $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account',$typ,'".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."',$typ.'_fehler','Kein Kundenname angegeben')");
          }
          if(empty($adresse) && (int)$addressFromDocument > 0) {
            $neuerkunde = false;
            $kundeok = true;
            $adresse = $addressFromDocument;
          }
        }
        else{
          if($this->app->DB->Select("SELECT kundennummernuebernehmen FROM uebertragungen_account WHERE id = '".$uebertragungen_account."' LIMIT 1") && isset($xml->kundennummer) && $xml->kundennummer != '')
          {
            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer <> '' AND kundennummer = '".$this->app->DB->real_escape_string((string)$xml->kundennummer)."' AND ifnull(geloescht,0) = 0 ORDER BY projekt = '$projekt' DESC LIMIT 1");
          }else {
            $adresse = null;
          }
          if(!$adresse && !empty($xml->gln) && $xml->gln != ''){
            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE geloescht <> 1 AND gln <> '' AND gln = '".$this->app->DB->real_escape_string((string)$xml->gln)."' LIMIT 1");
          }
          if(!empty($xml->adresse)){
            $adresse = $this->GetFromExtID('adresse', $xml->adresse);
          }
          if(!$adresse && !empty($xml->adresse))
          {
            $kundeok = true;
            $neuerkunde = true;
          }
          //if(!empty($xml->kundennummer))$adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE geloescht <> 1 AND kundennummer <> '' AND kundennummer = '".$this->app->DB->real_escape_string((string)$xml->kundennummer)."' LIMIT 1");

          if($adresse)$kundeok = true;

          if(empty($adresse) && (int)$addressFromDocument > 0) {
            $neuerkunde = false;
            $kundeok = true;
          }
        }
        if($kundeok)
        {
          $auftrag_positionen = null;
          $listname = $typ.'_position_list';
          $positionname = $typ.'_position';
          if(isset($xml->$listname))
          {
            if(isset($xml->$listname->$positionname))
            {
              $auftrag_positionen = &$xml->$listname->$positionname;
            }

          }elseif(isset($xml->$positionname)){
            $auftrag_positionen = &$xml->$positionname;
          }

          if($auftrag_positionen)
          {
            $artikelgefunden = true;
            foreach($auftrag_positionen as $position)
            {
              $_artikel = null;
              $ean = null;
              $extArticleId = null;
              $number = null;
              if(isset($position->artikel) && $position->artikel != '')
              {
                $extArticleId = (string)$position->artikel;
                $_artikel = $this->GetFromExtID('artikel', $xml->artikel);
                if($_artikel) {
                  $xml->artikelextid = $_artikel;
                }
              }
              if(!$_artikel && isset($position->ean) && $position->ean != '')
              {
                $ean = (string)$position->ean;
                $_artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND ean <> '' AND ean = '".$this->app->DB->real_escape_string((string)$position->ean)."' ORDER BY projekt = '$projekt' DESC LIMIT 1");
              }

              if(!$_artikel && isset($position->nummer) && $position->nummer != '')
              {
                $number = (string)$position->nummer;
                $_artikel = $this->app->DB->Select(
                  "SELECT id FROM artikel WHERE geloescht <> 1 AND nummer <> '' AND nummer = '"
                  . $this->app->DB->real_escape_string((string)$position->nummer).
                  "' ORDER BY projekt = '$projekt' DESC LIMIT 1"
                );
                $element1 = $position->nummer;
              }

              if(!$_artikel && $createArticleIfNotEmpty) {
                if(!empty($number) || !empty($extArticleId)) {
                  $newArticleArr = [
                    'projekt' => $projekt,
                  ];
                  if(!empty($markAsStorageArticle)) {
                    $newArticleArr = [
                      'lagerartikel' => 1,
                    ];
                  }
                  if(!empty($position->name)) {
                    $newArticleArr['name_de'] = (string)$position->name;
                  }
                  if($ean !== null) {
                    $newArticleArr['ean'] = $ean;
                  }
                  if($extArticleId === null) {
                    $newArticleArr['nummer'] = $number;
                  }
                  else {
                    $newArticleArr['nummer'] = $this->app->erp->GetNextNummer('artikel', $projekt);
                  }

                  $_artikel = $this->app->erp->InsertUpdateArtikel($newArticleArr);
                  if($extArticleId !== null) {
                    $this->SetExtIDMapping('artikel', $_artikel, $extArticleId);
                  }
                }
              }

              if(!$_artikel)
              {
                $artikelgefunden = false;
                if(!empty($position->nummer))
                {
                  if(!$this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND nummer <> '' AND nummer = '".$this->app->DB->real_escape_string((string)$position->nummer)."' ORDER BY projekt = '$projekt' DESC  LIMIT 1"))
                  {
                    $artikelgefunden = false;
                    $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account',$typ,'".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."',$typ.'_fehler','Artikelnummer ".$this->app->DB->real_escape_string((string)$position->nummer)." nicht gefunden')");
                    break;
                  }
                }elseif(!empty($position->ean)){
                  if(!$this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND ean <> '' AND ean = '".$this->app->DB->real_escape_string((string)$position->ean)."' LIMIT 1"))
                  {
                    $artikelgefunden = false;
                    $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account',$typ,'".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."',$typ.'_fehler','EAN ".$this->app->DB->real_escape_string((string)$position->ean)." nicht gefunden')");
                    break;
                  }
                }else{
                  $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account',$typ,'".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."',$typ.'_fehler','Keine Artikelnummer gefunden')");
                  break;
                }
              }
            }
            if($artikelgefunden)
            {
              $_typ ='';
              $name = '';
              $abteilung = '';
              $unterabteilung = '';
              $ansprechpartner = '';
              $adresszusatz = '';
              $strasse = '';
              $land = $this->app->erp->Firmendaten('land');
              $plz = '';
              $ort = '';
              $email = '';
              $telefon = '';
              $telefax = '';
              $ustid = '';
              $partner = '';
              $bundesstaat = '';
              $projekt = $this->app->DB->Select("SELECT projekt FROM uebertragungen_account WHERE id = '".$uebertragungen_account."' LIMIT 1");

              if(!empty($this->app->stringcleaner))
              {
                $this->app->stringcleaner->XMLArray_clean($xml);
              }

              if(isset($xml->name))$name = (string)$xml->name;
              if(isset($xml->anrede))$_typ = (string)$xml->anrede;
              if(isset($xml->abteilung))$abteilung = (string)$xml->abteilung;
              if(isset($xml->unterabteilung))$unterabteilung = (string)$xml->unterabteilung;
              if(isset($xml->ansprechpartner))$ansprechpartner = (string)$xml->ansprechpartner;
              if(isset($xml->adresszusatz))$adresszusatz = (string)$xml->adresszusatz;
              if(isset($xml->strasse))$strasse = (string)$xml->strasse;
              if(isset($xml->land))$land = (string)$xml->land;
              if(isset($xml->bundesstaat))$bundesstaat = (string)$xml->bundesstaat;
              if(isset($xml->plz))$plz = (string)$xml->plz;
              if(isset($xml->ort))$ort = (string)$xml->ort;
              if(isset($xml->email))$email = (string)$xml->email;
              if(isset($xml->telefon))$telefon = (string)$xml->telefon;
              if(isset($xml->telefax))$telefax = (string)$xml->telefax;
              if(isset($xml->ustid))$ustid = (string)$xml->ustid;
              if(isset($xml->partner))$partner = (string)$xml->partner;
              if(isset($xml->projekt) && (!isset($xml->belegnr) || $xml->belegnr == '' || strtoupper($xml->belegnr) == 'NEW' || strtoupper($xml->belegnr) == 'NEU'))
              {
                if(is_numeric($this->projekt)){
                  $projekt = (string)$xml->projekt;
                }
                if($land == ''){
                  $land = $this->app->erp->Projektdaten($projekt ,'land');
                }
              }
              if($land == '')
              {
                $land = $this->app->erp->Firmendaten('land');
              }
              if($neuerkunde)
              {
                $adresse = $this->app->erp->KundeAnlegen($_typ,$name,$abteilung,
                  $unterabteilung,$ansprechpartner,$adresszusatz,$strasse,$land,$plz,$ort,$email,$telefon,$telefax,$ustid,$partner,$projekt);
                if($bundesstaat != ''){
                  $this->app->DB->Update("UPDATE adresse SET bundesstaat = '".$this->app->DB->real_escape_string($bundesstaat)."' WHERE id = '$adresse' LIMIT 1");
                }
                if(isset($xml->adresse) && $xml->adresse != '')
                {
                  $this->SetExtIDMapping('adresse', $adresse, $xml->adresse);
                }
              }
              if($adresse) {
                $createname = 'Create'.ucfirst($typ);
                $auftrag = $this->app->erp->$createname();
                $belegnr = (string)$this->app->DB->Select("SELECT `belegnr` FROM `$typ` WHERE `id` = '$auftrag' LIMIT 1");
                if($belegnr === '' || $belegnr === '0'){
                  $belegnr = $this->app->erp->GetNextNummer($typ,$projekt,$auftrag);
                }
                if(isset($xml->belegnr) && $xml->belegnr != '' && strtoupper($xml->belegnr) != 'NEW' && strtoupper($xml->belegnr) != 'NEU'){
                  $this->SetExtIDMapping($typ, $auftrag, $xml->belegnr);
                }
                $standardwertename = 'Load'.ucfirst($typ).'Standardwerte';
                $this->app->erp->$standardwertename($auftrag, $adresse);
                if($typ === 'angebot' || $typ === 'auftrag'){
                  $this->app->DB->Update(
                    "UPDATE `$typ` 
                    SET `shopextid` = '" . $this->app->DB->real_escape_string(empty($xml->extid) ? '' : $xml->extid) . "'
                    WHERE `id` = '$auftrag' 
                    LIMIT 1"
                  );
                }
                $auftragarr['belegnr'] = $belegnr;
                if($_typ)
                {
                  $auftragarr['typ'] = $_typ;
                }
                if(!empty($name)) {
                  $auftragarr['name'] = $name;
                  $auftragarr['abteilung'] = $abteilung;
                  $auftragarr['unterabteilung'] = $unterabteilung;
                  $auftragarr['ansprechpartner'] = $ansprechpartner;
                  $auftragarr['strasse'] = $strasse;
                  $auftragarr['bundesstaat'] = $bundesstaat;
                  $auftragarr['land'] = $land;
                  $auftragarr['plz'] = $plz;
                  $auftragarr['ort'] = $ort;
                }
                if(!empty($email)){
                  $auftragarr['email'] = $email;
                }
                if(!empty($telefon)){
                  $auftragarr['telefon'] = $telefon;
                }
                if(!empty($telefax)){
                  $auftragarr['telefax'] = $telefax;
                }
                if(isset($xml->abweichendelieferadresse))$auftragarr['abweichendelieferadresse'] = (string)$xml->abweichendelieferadresse;
                if(isset($xml->art))$auftragarr['art'] = (string)$xml->art;
                if(isset($xml->bearbeiter))$auftragarr['bearbeiter'] = (string)$xml->bearbeiter;
                if(isset($xml->datum))$auftragarr['datum'] = (string)$xml->datum;
                if(isset($xml->lieferdatum))$auftragarr['lieferdatum'] = (string)$xml->lieferdatum;
                if(!empty($xml->tatsaechlicheslieferdatum))$auftragarr['tatsaechlicheslieferdatum'] = (string)$xml->tatsaechlicheslieferdatum;
                if(!empty($xml->lieferdatumkw))$auftragarr['lieferdatumkw'] = (string)$xml->lieferdatumkw;
                if(isset($xml->ustid))$auftragarr['ustid'] = (string)$xml->ustid;
                if(isset($xml->ust_befreit))$auftragarr['ust_befreit'] = (string)$xml->ust_befreit;
                if(isset($xml->internet))$auftragarr['internet'] = (string)$xml->internet;
                if(isset($xml->transaktionsnummer))$auftragarr['transaktionsnummer'] = (string)$xml->transaktionsnummer;
                if(isset($xml->versandart))$auftragarr['versandart'] = (string)$xml->versandart;
                if(isset($xml->vertrieb))$auftragarr['vertrieb'] = (string)$xml->vertrieb;
                if(isset($xml->zahlungsweise))$auftragarr['zahlungsweise'] = (string)$xml->zahlungsweise;
                if(isset($xml->freitext))$auftragarr['freitext'] = (string)$xml->freitext;
                if(isset($xml->bank_inhaber))$auftragarr['bank_inhaber'] = (string)$xml->bank_inhaber;
                if(isset($xml->bank_institut))$auftragarr['bank_institut'] = (string)$xml->bank_institut;
                if(isset($xml->bank_blz))$auftragarr['bank_blz'] = (string)$xml->bank_blz;
                if(isset($xml->bank_konto))$auftragarr['bank_konto'] = (string)$xml->bank_konto;
                if(isset($xml->vorabbezahltmarkieren))$auftragarr['vorabbezahltmarkieren'] = (string)$xml->vorabbezahltmarkieren;
                $auftragarr['autoversand'] ='1';
                if(isset($xml->abweichendelieferadresse))$auftragarr['abweichendelieferadresse'] = (string)$xml->abweichendelieferadresse;
                if(isset($xml->ansprechpartner))$auftragarr['ansprechpartner'] = (string)$xml->ansprechpartner;
                if(isset($xml->liefername))$auftragarr['liefername'] = (string)$xml->liefername;
                if(isset($xml->lieferbundesstaat))$auftragarr['lieferbundesstaat'] = (string)$xml->lieferbundesstaat;
                if(isset($xml->lieferland))$auftragarr['lieferland'] = (string)$xml->lieferland;
                if(isset($xml->lieferstrasse))$auftragarr['lieferstrasse'] = (string)$xml->lieferstrasse;
                if(isset($xml->lieferabteilung))$auftragarr['lieferabteilung'] = (string)$xml->lieferabteilung;
                if(isset($xml->lieferunterabteilung))$auftragarr['lieferunterabteilung'] = (string)$xml->lieferunterabteilung;
                if(isset($xml->lieferansprechpartner))$auftragarr['lieferansprechpartner'] = (string)$xml->lieferansprechpartner;
                if(isset($xml->lieferort))$auftragarr['lieferort'] = (string)$xml->lieferort;
                if(isset($xml->lieferplz))$auftragarr['lieferplz'] = (string)$xml->lieferplz;
                if(isset($xml->lieferadresszusatz))$auftragarr['lieferadresszusatz'] = (string)$xml->lieferadresszusatz;
                if(isset($xml->packstation_inhaber))$auftragarr['packstation_inhaber'] = (string)$xml->packstation_inhaber;
                if(isset($xml->packstation_station))$auftragarr['packstation_station'] = (string)$xml->packstation_station;
                if(isset($xml->packstation_ident))$auftragarr['packstation_ident'] = (string)$xml->packstation_ident;
                if(isset($xml->packstation_plz))$auftragarr['packstation_plz'] = (string)$xml->packstation_plz;
                if(isset($xml->packstation_ort))$auftragarr['packstation_ort'] = (string)$xml->packstation_ort;
                if(isset($xml->partnerid))$auftragarr['partnerid'] = (string)$xml->partnerid;
                if(isset($xml->kennen))$auftragarr['kennen'] = (string)$xml->kennen;

                if(isset($xml->ihrebestellnummer))$auftragarr['ihrebestellnummer'] = (string)$xml->ihrebestellnummer;
                if(isset($xml->internebemerkung))$auftragarr['internebemerkung'] = (string)$xml->internebemerkung;
                if(isset($xml->internebezeichnung))$auftragarr['internebezeichnung'] = (string)$xml->internebezeichnung;

                $auftragarr['status']='freigegeben';
                $auftragarr['projekt']=$projekt;
                $auftragarr['zahlungszielskonto']=0;
                if(isset($xml->gesamtsumme)){
                  $auftragarr['gesamtsumme'] = (float)str_replace(',','.',$xml->gesamtsumme);
                }
                $auftragarr = $this->formatDateFieldsToMysqlFormat($auftragarr);
                $this->app->DB->UpdateArr($typ, $auftrag, 'id', $auftragarr, true);
                $this->app->erp->Standardprojekt($typ, $auftrag);

                foreach($auftrag_positionen as $position)
                {
                  $artikel = null;
                  $newposid = null;
                  if(isset($position->artikelextid))
                  {
                    $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND id = '".$this->app->DB->real_escape_string((string)$position->artikelextid)."' LIMIT 1");
                    if($artikel)
                    {
                      $newposid = $this->app->erp->AddPositionManuellPreisNummer($typ,$auftrag, $projekt, $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$artikel' LIMIT 1"),
                        (isset($position->menge)?$position->menge:1) ,
                        (isset($position->name)? $position->name:$this->app->DB->Select("SELECT name_de FROM artikel WHERE id = '$artikel' LIMIT 1")),
                        isset($position->preis)? str_replace(',','.',$position->preis):$this->app->erp->GetVerkaufspreis($artikel,(isset($position->menge)?$position->menge:1),$adresse) ,
                        $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '$artikel' LIMIT 1") ,
                        0,0,
                        isset($xml->waehrung)?$xml->waehrung:'EUR');
                    }
                  }
                  if(!empty($position->ean) && !$artikel)
                  {
                    $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND ean <> '' AND ean = '".$this->app->DB->real_escape_string((string)$position->ean)."' LIMIT 1");
                    if($artikel)
                    {
                      $newposid = $this->app->erp->AddPositionManuellPreisNummer($typ,$auftrag, $projekt, $this->app->DB->Select("SELECT nummer FROM artikel WHERE id = '$artikel' LIMIT 1"),
                        (isset($position->menge)?$position->menge:1) ,
                        (isset($position->name)? $position->name:$this->app->DB->Select("SELECT name_de FROM artikel WHERE id = '$artikel' LIMIT 1")),
                        isset($position->preis)? str_replace(',','.',$position->preis):$this->app->erp->GetVerkaufspreis($artikel,(isset($position->menge)?$position->menge:1),$adresse) ,
                        $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '$artikel' LIMIT 1") ,
                        0,0,
                        isset($xml->waehrung)?$xml->waehrung:'EUR');
                    }
                  }
                  if(isset($position->nummer) && !$artikel)
                  {
                    $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht <> 1 AND nummer <> '' AND nummer = '".$this->app->DB->real_escape_string((string)$position->nummer)."'  LIMIT 1");
                    $newposid = $this->app->erp->AddPositionManuellPreisNummer($typ,$auftrag, $projekt, $position->nummer,
                      (isset($position->menge)?$position->menge:1) ,
                      (isset($position->name)? $position->name:$this->app->DB->Select("SELECT name_de FROM artikel WHERE id = '$artikel' LIMIT 1")),
                      isset($position->preis)? str_replace(',','.',$position->preis):$this->app->erp->GetVerkaufspreis($artikel,(isset($position->menge)?$position->menge:1),$adresse) ,
                      $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '$artikel' LIMIT 1") ,
                      0,0,
                      isset($xml->waehrung)?$xml->waehrung:'EUR');

                  }
                  if(!empty($newposid))
                  {
                    if($typ === 'auftrag'){
                      if(!empty($position->lieferdatum)){
                        $deliveryDate = (string)$position->lieferdatum;

                        if(strpos($deliveryDate, '.') !== false) {
                          $deliveryDate = $this->app->String->Convert($deliveryDate, '%1.%2.%3', '%3-%2-%1');
                        }

                        $this->app->DB->Update("UPDATE auftrag_position SET lieferdatum = '" . $deliveryDate . "' WHERE id = $newposid LIMIT 1");
                      }
                      if(!empty($position->lieferdatumkw)){
                        $this->app->DB->Update("UPDATE auftrag_position SET lieferdatumkw = '" . $position->lieferdatumkw . "' WHERE id = $newposid LIMIT 1");
                      }
                    }

                    $artikelnummerkunde = !empty($position->kundenartikelnummer)?(string)$position->kundenartikelnummer:$this->app->DB->real_escape_string(
                      $this->app->DB->Select(
                        "SELECT kundenartikelnummer 
                                FROM verkaufspreise 
                                WHERE adresse='$adresse' AND artikel='$artikel' AND kundenartikelnummer!='' AND ab_menge <=".
                        (float)(isset($position->menge)?$position->menge:1)." 
                                AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') 
                                ORDER by ab_menge DESC 
                                LIMIT 1"
                      ));

                    if($artikelnummerkunde == ''){
                      // Anzeige Artikel Nummer von Gruppe aus Verkaufspreis
                      $returnwaehrung = null;
                      $gruppevkresult = $this->app->erp->GetVerkaufspreis(
                        $artikel,
                        (isset($position->menge)?$position->menge:1),
                        $adresse,
                        isset($xml->waehrung)?$xml->waehrung:'EUR',
                        $returnwaehrung,
                        true
                      );
                      if($gruppevkresult['kundenartikelnummer'] != ''){
                        $artikelnummerkunde = $gruppevkresult['kundenartikelnummer'];
                      }
                    }

                    if(!empty($artikelnummerkunde)) {
                      $this->app->DB->Update(
                        sprintf(
                          "UPDATE `%s` SET `artikelnummerkunde` = '%s' WHERE `id` = %d ",
                          $typ.'_position',
                          $this->app->DB->real_escape_string($artikelnummerkunde),
                          $newposid
                        )
                      );
                    }
                    $positionFieldsToUpdate = [];
                    if($typ === 'retoure') {
                      foreach(
                        [
                          'grund',
                          'grundbeschreibung',
                          'beschreibung',
                          'seriennummer',
                          'internerkommentar',
                          'bemerkung',
                          'lieferdatum',
                        ] as $positionField
                      ) {
                        if(isset($position->$positionField)) {
                          $positionFieldValue = (string)$position->$positionField;
                          if(!empty($positionFieldValue)) {
                            if($positionField === 'lieferdatum' && strpos($positionFieldValue, '.') !== false) {
                              $positionFieldValue = $this->app->String->Convert($positionFieldValue, '%1.%2.%3', '%3-%2-%1');
                            }
                            $positionFieldsToUpdate[$positionField] = $positionFieldValue;
                          }
                        }
                      }
                    }
                    for($indexFreeField = 1; $indexFreeField <= 40; $indexFreeField++) {
                      $positionField = 'freifeld'.$indexFreeField;
                      if(isset($position->$positionField)) {
                        $positionFieldValue = (string)$position->$positionField;
                        if(!empty($positionFieldValue)) {
                          $positionFieldsToUpdate[$positionField] = $positionFieldValue;
                        }
                      }
                    }
                    if(!empty($positionFieldsToUpdate)) {
                      $this->app->DB->UpdateArr($typ .'_position', $newposid, 'id', $positionFieldsToUpdate, true);
                    }
                  }
                  /*if($newposid > 0)
                  {
                    $this->app->erp->RunHook("beleg_afterinsertposition", 5, "auftrag",$auftrag,$artikel,(isset($position->menge)?$position->menge:1),$newposid);
                  }*/
                }

                $this->app->erp->LoadSteuersaetzeWaehrung($auftrag,$typ);

                $this->app->DB->Update("
                        UPDATE $typ a 
                        INNER JOIN adresse adr ON a.lieferant = adr.id 
                        SET a.lieferantkdrnummer = if(a.lieferantennummer <> '',a.lieferantennummer,adr.lieferantennummer)
                        WHERE a.lieferantkdrnummer = '' AND a.lieferantenauftrag = 1 AND a.id = '$auftrag'
                        ");
                $this->app->DB->Update("
                        UPDATE $typ a 
                        INNER JOIN adresse adr ON a.adresse = adr.id 
                        SET a.lieferantkdrnummer = if(a.kundennummer <> '',a.kundennummer, adr.kundennummer)
                        WHERE a.lieferantkdrnummer = '' AND a.lieferantenauftrag = 0 AND a.id = '$auftrag'
                        ");

                if($typ == 'auftrag')
                {
                  $reservierung = $this->app->DB->Select("SELECT reservierung FROM projekt WHERE id='$projekt' LIMIT 1");
                  if($reservierung>=1){
                    $this->app->erp->AuftragReservieren($auftrag);
                  }
                  $this->app->erp->AuftragNeuberechnen($auftrag);
                  $this->app->erp->AuftragEinzelnBerechnen($auftrag);
                }elseif($typ == 'angebot')
                {
                  $this->app->erp->AngebotNeuberechnen($auftrag);
                }

                if(isset($xml->dateien)) {
                  foreach($xml->dateien as $datei)
                  {
                    foreach($datei as $singledatei)
                    {
                      $isfile = false;
                      $data = null;
                      if(is_file($singledatei->dateiname)){
                        $type = mime_content_type(basename($singledatei->dateiname));
                        if($type != 'text/x-php' && $type != 'text/php' && $type != 'application/php' && $type != 'application/x-php' && $type != 'application/x-httpd-php' && $type != 'application/x-httpd-php-source'){

                          if($singledatei->dateiinhalt == ''){
                            $data = file_get_contents($singledatei->dateiname);
                            //$singledatei->dateiinhalt = base64_encode($dateiinhalt);
                            $isfile = true;
                          }
                        }else{
                          $singledatei->dateiinhalt = '';
                        }
                      }
                      if(empty($data))
                      {
                        if($singledatei->dateiinhalt == '')
                        {
                          $data = '';
                        }else{
                          $data = base64_decode($singledatei->dateiinhalt);
                        }
                      }
                      if($isfile){
                        $name = $this->app->erp->GetTmp().basename($singledatei->dateiname);
                      }else{
                        $name = $this->app->erp->GetTmp().$singledatei->dateiname;
                      }

                      file_put_contents($name, $data);

                      $pfad = $this->app->Conf->WFuserdata;
                      $pfad = rtrim($pfad);
                      $pfad .= '/dms/';

                      if(!file_exists($pfad) && !mkdir($pfad, 0777, true) && !is_dir($pfad))
                      {
                        $this->app->erp->LogFile($pfad.' konnte nicht erstellt werden');
                      }

                      $speicherpfad = $pfad.$this->app->Conf->WFdbname;

                      if(!file_exists($speicherpfad) && !mkdir($speicherpfad, 0777, true) &&
                        !is_dir($speicherpfad))
                      {
                        $this->app->erp->LogFile($speicherpfad.' konnte nicht erstellt werden');
                      }

                      $fileid = $this->app->erp->CreateDatei($singledatei->dateiname, !empty($singledatei->titel)?$singledatei->titel:$singledatei->dateiname, !empty($singledatei->beschreibung)?(string)$singledatei->beschreibung:'', '', $name, '',true,$speicherpfad);
                      $subjekt = !empty($singledatei->subjekt)?(string)$singledatei->subjekt:'Sonstige';
                      $this->app->erp->AddDateiStichwort($fileid, $subjekt, ucfirst($typ), $auftrag);

                      if($isfile){
                        @rename($singledatei->dateiname, $singledatei->dateiname.'.del');
                      }
                    }
                  }
                }
                if(!empty($obj)){
                  $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, $typ . '_ok', ucfirst($typ) . ' angelegt', '', '', '', $typ, $auftrag);
                  $this->app->erp->RunHook('transfer_document_incoming', 3, $uebertragungen_account, $typ, $auftrag);
                }
              }
            }else
            {
              if(!empty($obj)){
                $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, $typ . '_error', 'Artikel nicht gefunden', $this->app->DB->real_escape_string((string)$element1), '', '', $typ);
              }
            }
          }else{
            $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account',$typ,'".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."',$typ.'_fehler','Keine Positionen gefunden')");
            if(!empty($obj)){
              $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, $typ . '_error', ucfirst($typ) . ' enth&auml;lt keine Positinen', $this->app->DB->real_escape_string((string)$xml->extid), '', '', $typ);
            }
          }
        }else{
          $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account',$typ,'".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."',$typ.'_fehler','Kundennummer ".$this->app->DB->real_escape_string((string)$xml->kundennummer)." nicht gefunden')");
        }
        //}else{
        //$this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','auftrag','".$this->app->DB->real_escape_string(empty($xml->extid)?'':$xml->extid)."','auftrag_fehler','Keine Kundennummer angegeben')");
        //}
      }else
      {
        if($this->app->DB->Select("SELECT id FROM $typ WHERE shopextid = '".$this->app->DB->real_escape_string((empty($xml->extid))?'':$xml->extid)."' AND projekt = '$projekt' AND shopextid <> ''"))
        {
          $element1 = 'Shopextid: '.$xml->extid;
          $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, $typ.'_error', ucfirst($typ).' existiert bereits', $this->app->DB->real_escape_string((string)$element1), '', '', $typ, $id);
        }elseif($this->GetFromExtID($typ, $xml->belegnr)){
          if(!empty($obj)){
            $__id = $this->GetFromExtID($typ, $xml->belegnr);
            $__auftrag = $this->app->DB->Select("SELECT belegnr FROM $typ WHERE id = '$__id' LIMIT 1");
            $element1 = "Belegnr: " . $__auftrag;
            $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, $typ . '_error', ucfirst($typ) . ' existiert bereits', $this->app->DB->real_escape_string((string)$element1), '', '', $typ, $id);
          }
        }else{
          if(!empty($obj)){
            $element1 = 'Belegnr: ' . $xml->belegnr;
            $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, $typ . '_error', ucfirst($typ) . ' existiert bereits', $this->app->DB->real_escape_string((string)$element1), '', '', $typ, $id);
          }
        }
      }

    }

    /**
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     */
    public function ParsePartXmlDeliverynoteWithoutId(&$xml, $uebertragungen_account, $obj)
    {
      if(isset($xml->tracking)) {
        if(!empty($xml->belegnr)) {
          $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','lieferschein','".(string)$xml->belegnr."','tracking_fehler','Lieferschein ".(string)$xml->belegnr." nicht gefunden')");
          $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'tracking_error', 'Lieferschein '.(string)$xml->belegnr." nicht gefunden", $this->app->DB->real_escape_string((string)$xml->belegnr), '', '', 'lieferschein');
        }
      }
    }

    /**
     * @param string         $typ
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     */
    public function ParsePartXmlDocumentWithoutId($typ, &$xml, $uebertragungen_account, $obj)
    {
      switch($typ) {
        case 'bestellung':
        case 'produktion':
          $this->ParsePartXmlSupplierorderProductionWithoutId($typ, $xml, $uebertragungen_account, $obj);
          break;
        case 'auftrag':
        case 'angebot':
        case 'retoure':
          $this->ParsePartXmlOrderOfferWithoutId($typ, $xml, $uebertragungen_account, $obj);
          break;

        case 'lieferschein':
          $this->ParsePartXmlDeliverynoteWithoutId($xml, $uebertragungen_account, $obj);
          if(isset($xml->tracking)) {
            if(!empty($xml->belegnr)) {
              $this->app->DB->Insert("INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) VALUES ('$uebertragungen_account','lieferschein','".(string)$xml->belegnr."','tracking_fehler','Lieferschein ".(string)$xml->belegnr." nicht gefunden')");
              $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, 'tracking_error', 'Lieferschein '.(string)$xml->belegnr." nicht gefunden", $this->app->DB->real_escape_string((string)$xml->belegnr), '', '', 'lieferschein');
            }
          }
          break;
      }
    }

    /**
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     * @param int            $id
     */
    public function ParsePartXmlDeliveryNoteTracking(&$xml, $uebertragungen_account, $obj, $id)
    {
      if(!isset($xml->tracking)){
        return;
      }
      $tracking = (string)$xml->tracking;
      if($tracking == '') {
        return;
      }
      $versand = '';
      if(isset($xml->versandart)) {
        $versand = (string)$xml->versandart;
      }
      $tracking_link = null;
      $sprache = null;
      if(!empty($xml->tracking_sprache) && (string)$xml->tracking_sprache){
        $sprache = (string)$xml->tracking_sprache;
      }
      if(!empty($xml->tracking_link) && (string)$xml->tracking_link){
        $tracking_link = (string)$xml->tracking_link;
      }
      $this->addTrackingToDeliveryNote(
        $obj,
        $id,
        $tracking,
        $versand,
        $sprache,
        $tracking_link
      );

      unset($kg);
      unset($adresse);
      unset($projekt);
      unset($tracking);
      unset($versand);
      $lieferschein_positionen = null;
      if(isset($xml->lieferschein_position_list)) {
        if(isset($xml->lieferschein_position_list->lieferschein_position)) {
          $lieferschein_positionen = &$xml->lieferschein_position_list->lieferschein_position;
        }
      }
      elseif(isset($xml->lieferschein_position)){
        $lieferschein_positionen = &$xml->lieferschein_position;
      }
      if($lieferschein_positionen) {
        foreach($lieferschein_positionen as $position) {
          if(isset($position->sort) &&  !isset($position->lieferschein)){
            $position->lieferschein = $id;
          }
          $id_pos = $this->GetIDFromFeld('lieferschein_position', $position);
          if($id_pos && isset($position->geliefert)) {
            $check = $this->app->DB->SelectRow(
              "SELECT * FROM lieferschein_position WHERE id = '$id_pos' AND lieferschein = '$id' LIMIT 1"
            );
            if($check) {
              $geliefert = round((float)$position->geliefert,4);
              if($check['menge'] >= $geliefert){
                $this->app->DB->Update("UPDATE lieferschein_position SET geliefert = '$geliefert' WHERE id = '$id_pos' LIMIT 1");
              }
            }
          }
        }
      }
      else {
        $this->app->DB->Update("UPDATE lieferschein_position SET geliefert = menge WHERE lieferschein = '$id'");
      }
      $this->app->DB->Update("UPDATE lieferschein SET status = 'versendet' WHERE id = '$id' LIMIT 1");
    }

    /**
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     * @param int            $id
     */
    public function ParsePartXmlDeliveryNoteBestBefore(&$xml, $uebertragungen_account, $obj, $id)
    {
      $lieferschein_positionen = null;
      if(isset($xml->lieferschein_position_list)) {
        if(isset($xml->lieferschein_position_list->lieferschein_position)) {
          $lieferschein_positionen = &$xml->lieferschein_position_list->lieferschein_position;
        }
      }
      elseif(isset($xml->lieferschein_position)){
        $lieferschein_positionen = &$xml->lieferschein_position;
      }
      if(empty($lieferschein_positionen)) {
        return;
      }
      $lastPosId = 0;
      foreach($lieferschein_positionen as $position) {
        if(isset($position->sort) &&  !isset($position->lieferschein)){
          $position->lieferschein = $id;
        }
        $id_pos = $this->GetIDFromFeld('lieferschein_position', $position);
        if($lastPosId === $id_pos) {
          continue;
        }
        $lastPosId = $id_pos;
        if(!isset($position->mhd_charge_block)) {
          continue;
        }
        $sCount = count($position->mhd_charge_block);
        if($sCount > 0) {
          $bestBefores = [];
          foreach($position->mhd_charge_block as $mhd) {
            $bestBefore = ['bestbefore' => '', 'batch' => '', 'amount' => 0];
            if(isset($mhd->mhd)) {
              $bestBefore['bestbefore'] = (string)$mhd->mhd;
            }
            if(isset($mhd->charge)) {
              $bestBefore['batch'] = (string)$mhd->charge;
            }
            if(isset($mhd->anzahl)) {
              $bestBefore['amount'] = (string)$mhd->anzahl;
            }
            elseif(isset($mhd->menge)) {
              $bestBefore['amount'] = (string)$mhd->menge;
            }
            if(empty($bestBefore['bestbefore']) && empty($bestBefore['batch'])) {
              continue;
            }
            $bestBefores[] = $bestBefore;
          }
          if(empty($bestBefores)) {
            continue;
          }
          $posDb = $this->app->DB->SelectRow(
            sprintf(
              'SELECT dnp.id, dnp.`artikel`, art.mindesthaltbarkeitsdatum, art.chargenverwaltung,
                dnp.menge, dnp.explodiert_parent, dnp.menge
              FROM `lieferschein_position` AS `dnp`
              INNER JOIN `artikel` AS `art` ON dnp.artikel = art.id
              WHERE `dnp`.id = %d',
              $id_pos
            )
          );
          if(empty($posDb)) {
            continue;
          }
          if(count($bestBefores) === 1 && empty($bestBefores[0]['amount'])) {
            $bestBefores[0]['amount'] = $posDb['menge'];
          }
          $posDb['menge'] = $this->app->erp->ReplaceMenge(1, $posDb['menge'], 1);
          if($posDb['menge'] <= 0) {
            continue;
          }
          $isBestBeforeArticle = !empty($posDb['mindesthaltbarkeitsdatum']);
          $isBatchArticle = $posDb['chargenverwaltung'] > 0;
          if(!$isBestBeforeArticle && !$isBatchArticle) {
            if(empty($posDb['explodiert_parent'])) {
              continue;
            }
            if(!empty($posDb['explodiert_parent'])) {
              $posDb = $this->app->DB->SelectRow(
                sprintf(
                  'SELECT dnp.id, dnp.`artikel`, art.mindesthaltbarkeitsdatum,art.`chargenverwaltung`, 
                        dnp.menge, dnp.explodiert_parent, dnp.menge
                  FROM `lieferschein_position` AS `dnp`
                  INNER JOIN `artikel` AS `art` ON dnp.artikel = art.id
                  WHERE `dnp`.id = %d',
                  $posDb['explodiert_parent']
                )
              );
              $isBestBeforeArticle = !empty($posDb['mindesthaltbarkeitsdatum']);
              $isBatchArticle = $posDb['chargenverwaltung'] > 0;
              if(!$isBestBeforeArticle && !$isBatchArticle) {
                continue;
              }
            }
          }
          if($isBestBeforeArticle && $isBatchArticle){
            $dbBestbefores = $this->app->DB->SelectFirstCols(
              sprintf(
                "SELECT CONCAT(`wert`,'|', `wert2`)
              FROM `beleg_chargesnmhd` 
              WHERE `wert` != '' AND `type` = 'mhd' AND `doctype` = 'lieferschein' 
                AND `doctypeid` = %d AND `pos` = %d
              GROUP BY `wert`",
                $id, $posDb['id']
              )
            );
          }
          elseif($isBestBeforeArticle) {
            $dbBestbefores = $this->app->DB->SelectFirstCols(
              sprintf(
                "SELECT `wert`
              FROM `beleg_chargesnmhd` 
              WHERE `wert` != '' AND `type` = 'mhd' AND `doctype` = 'lieferschein' 
                AND `doctypeid` = %d AND `pos` = %d
              GROUP BY `wert`",
                $id, $posDb['id']
              )
            );
          }
          elseif($isBatchArticle) {
            $dbBestbefores = $this->app->DB->SelectFirstCols(
              sprintf(
                "SELECT `wert`
                FROM `beleg_chargesnmhd` 
                WHERE `wert` != '' AND `type` = 'charge' AND `doctype` = 'lieferschein' 
                  AND `doctypeid` = %d AND `pos` = %d
                GROUP BY `wert`",
                $id, $posDb['id']
              )
            );
          }
          if(empty($dbBestbefores)){
            $dbBestbefores = [];
          }
          foreach ($bestBefores as $bestBefore) {
            if($isBestBeforeArticle && $isBatchArticle){
              $bestBeforeConcat = $bestBefore['bestbefore'] . '|' . $bestBefore['batch'];
            }
            elseif($isBestBeforeArticle){
              $bestBeforeConcat = $bestBefore['bestbefore'];
            }
            elseif($isBatchArticle){
              $bestBeforeConcat = $bestBefore['batch'];
            }

            if(!in_array($bestBeforeConcat, $dbBestbefores)){
              if($isBestBeforeArticle && $isBatchArticle){
                $this->app->erp->CreateBelegPositionMHDCHARGESRN(
                  'lieferschein', $id, $posDb['id'], 'mhd', $bestBefore['bestbefore'], $bestBefore['amount'],
                  'charge', $bestBefore['batch'], 0, 'API Eingang'
                );

                $this->app->erp->CreateBelegPositionMHDCHARGESRN(
                  'lieferschein', $id, $posDb['id'], 'charge', $bestBefore['batch'], $bestBefore['amount'],
                  '', '', 0, 'API Eingang'
                );
              }
              elseif($isBestBeforeArticle) {
                $this->app->erp->CreateBelegPositionMHDCHARGESRN(
                  'lieferschein', $id, $posDb['id'], 'mhd', $bestBefore['bestbefore'], $bestBefore['amount'],
                  '','', 0, 'API Eingang'
                );
              }
              elseif($isBatchArticle) {
                $this->app->erp->CreateBelegPositionMHDCHARGESRN(
                  'lieferschein', $id, $posDb['id'], 'charge', $bestBefore['batch'], $bestBefore['amount'],
                  '', '', 0, 'API Eingang'
                );
              }
            }
          }
        }
      }
    }


    /**
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     * @param int            $id
     */
    public function ParsePartXmlDeliveryNoteSerials(&$xml, $uebertragungen_account, $obj, $id)
    {
      $lieferschein_positionen = null;
      if(isset($xml->lieferschein_position_list)) {
        if(isset($xml->lieferschein_position_list->lieferschein_position)) {
          $lieferschein_positionen = &$xml->lieferschein_position_list->lieferschein_position;
        }
      }
      elseif(isset($xml->lieferschein_position)){
        $lieferschein_positionen = &$xml->lieferschein_position;
      }
      if(empty($lieferschein_positionen)) {
        return;
      }
      $lastPosId = 0;
      foreach($lieferschein_positionen as $position) {
        if(isset($position->sort) &&  !isset($position->lieferschein)){
          $position->lieferschein = $id;
        }
        $id_pos = $this->GetIDFromFeld('lieferschein_position', $position);
        if($lastPosId === $id_pos) {
          continue;
        }
        $lastPosId = $id_pos;
        if(!isset($position->serial)) {
          continue;
        }
        $sCount = count($position->serial);
        if($sCount > 0) {
          $serials = [];
          foreach($position->serial as $serial) {
            $serials[] = (string)$serial;
          }
          $posDb = $this->app->DB->SelectRow(
            sprintf(
              'SELECT dnp.id, dnp.`artikel`, art.seriennummern, dnp.menge, dnp.explodiert_parent
              FROM `lieferschein_position` AS `dnp`
              INNER JOIN `artikel` AS `art` ON dnp.artikel = art.id
              WHERE `dnp`.id = %d',
              $id_pos
            )
          );
          if(empty($posDb)) {
            continue;
          }
          if((string)$posDb['seriennummern'] === ''
            || (string)$posDb['seriennummern'] === 'keine') {
            if(empty($posDb['explodiert_parent'])) {
              continue;
            }
            if(!empty($posDb['explodiert_parent'])) {
              $posDb = $this->app->DB->SelectRow(
                sprintf(
                  'SELECT dnp.id, dnp.`artikel`, art.seriennummern, dnp.menge, dnp.explodiert_parent
                  FROM `lieferschein_position` AS `dnp`
                  INNER JOIN `artikel` AS `art` ON dnp.artikel = art.id
                  WHERE `dnp`.id = %d',
                  $id_pos
                )
              );
              if((string)$posDb['seriennummern'] === ''
                || (string)$posDb['seriennummern'] === 'keine') {
                continue;
              }
            }
          }
          $dbSerials = $this->app->DB->SelectFirstCols(
            sprintf(
              "SELECT `wert` 
              FROM `beleg_chargesnmhd` 
              WHERE `wert` != '' AND `type` = 'sn' AND `doctype` = 'lieferschein' 
                AND `doctypeid` = %d AND `pos` = %d
              GROUP BY `wert`",
              $id, $posDb['id']
            )
          );
          foreach($serials as $serial) {
            if(!in_array($serial, $dbSerials)) {
              $this->app->erp->CreateBelegPositionMHDCHARGESRN(
                'lieferschein',$id,$posDb['id'],'sn',$serial,1,'','',0,'API Eingang'
              );
              $dbSerials[] = $serial;
            }
          }
        }
      }
    }

    /**
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     * @param int            $id
     */
    public function ParsePartXmlDeliveryNoteWithId(&$xml, $uebertragungen_account, $obj, $id)
    {
      if(isset($xml->tracking)){
        $this->ParsePartXmlDeliveryNoteTracking($xml, $uebertragungen_account, $obj, $id);
      }
      $this->ParsePartXmlDeliveryNoteBestBefore($xml, $uebertragungen_account, $obj, $id);
      $this->ParsePartXmlDeliveryNoteSerials($xml, $uebertragungen_account, $obj, $id);
    }

    /**
     * @param string         $typ
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     * @param int            $id
     */
    public function ParsePartXmlDocumentWithId($typ, &$xml, $uebertragungen_account, $obj, $id)
    {
      switch($typ) {
        case 'auftrag':
        case 'angebot':
        case 'bestellung':
        case 'produktion':
          $obj->AddUbertragungMonitorLog($uebertragungen_account, $this->datei_id, 0, $typ.'_error', ucfirst($typ).' existiert bereits', $this->app->DB->real_escape_string(!empty($xml->belegnr)?(string)$xml->belegnr:$id), '', '', $typ, $id);
          break;
        case 'lieferschein':
          $this->ParsePartXmlDeliveryNoteWithId($xml, $uebertragungen_account, $obj, $id);
          break;
      }
    }

    /**
     * @param string         $typ
     * @param                $xml
     * @param int            $uebertragungen_account
     * @param Uebertragungen $obj
     */
  public function ParsePartXmlDocument($typ, &$xml, $uebertragungen_account, $obj)
  {
    if($typ !== 'auftrag' && $typ !== 'angebot' && $typ !== 'bestellung' && $typ !== 'produktion') {
      $id = $this->GetIDFromFeld($typ, $xml);
    }
    elseif(isset($xml->belegnr) && strtoupper((string)$xml->belegnr) !== 'NEW' && strtoupper((string)$xml->belegnr) !== 'NEU'){
      $id = $this->GetFromExtID($typ, $xml->belegnr, $xml);
    }
    else {
      $id = null;
    }
    if(!$id && $typ === 'lieferschein' && isset($xml->auftragextid) && $xml->auftragextid != "") {
      $projekt = $this->app->DB->Select("SELECT projekt FROM uebertragungen_account WHERE id = '$uebertragungen_account' LIMIT 1");
      $eigenernummernkreis = $this->app->DB->Select("SELECT eigenernummernkreis FROM projekt WHERE id = '$projekt' LIMIT 1");
      if($eigenernummernkreis) {
        $auftrag = $this->app->DB->Select("SELECT id FROM auftrag WHERE belegnr = '".$this->app->DB->real_escape_string($xml->auftragextid)."' AND projekt = '$projekt' LIMIT 1");
      }
      else{
        $auftrag = $this->app->DB->Select("SELECT id FROM auftrag WHERE belegnr = '".$this->app->DB->real_escape_string($xml->auftragextid)."' ORDER BY projekt = '$projekt' DESC LIMIT 1");
      }
      if($auftrag) {
        $id = $this->app->DB->Select("SELECT id FROM lieferschein WHERE auftragid = '$auftrag' AND status <> 'stoniert' AND belegnr != '' LIMIT 1");
        if(!$id && !$this->app->DB->Select("SELECT id FROM lieferschein WHERE auftragid = '$auftrag' LIMIT 1")) {
          $id = $this->app->erp->WeiterfuehrenAuftragZuLieferschein($auftrag);
          if($id) {
            $this->app->erp->BelegFreigabe('lieferschein', $id);
            $this->app->DB->Update("UPDATE lieferschein SET status = 'versendet' AND schreibschutz = 1 WHERE id = '$id' LIMIT 1");
          }
        }
      }
    }
    if($id) {
      $this->ParsePartXmlDocumentWithId($typ, $xml, $uebertragungen_account, $obj, $id);
    }
    else{
      //Beleg nicht gefunden
      $this->ParsePartXmlDocumentWithoutId($typ, $xml, $uebertragungen_account, $obj);
    }
  }

    /**
     * @param $typ
     * @param $xml
     * @param $uebertragungen_account
     */
  public function ParseTeilXML($typ, &$xml, $uebertragungen_account)
  {
    $typen = $this->getTabellen();
    $belege = $this->getBelege();
    $is_beleg = in_array($typ, $belege);
    $is_typ = false;

    if($uebertragungen_account) {
      /** @var Uebertragungen $obj */
      $obj = $this->app->erp->LoadModul('uebertragungen');
      if($obj) {
        $obj->datei_id = $this->datei_id;
      }
    }
    if($uebertragungen_account) {
      if(!$this->isAllowedToImport($uebertragungen_account, $obj, $typ)){
        return;
      }
    }

    if(!$is_beleg){
      $is_typ = in_array($typ, $typen);
    }
    if(!$is_typ && !$is_beleg) {
      return;
    }

    $this->UpdateMappingXML($typ, $xml);

    if($is_beleg) {
      $this->ParsePartXmlDocument($typ, $xml, $uebertragungen_account, $obj);
    }
    elseif($is_typ) {
      $this->ParsePartXmlType($typ, $xml, $uebertragungen_account, $obj);
    }
  }

    /**
     * @param int    $deliveryNoteId
     * @param string $tracking
     * @param string $deliveryType
     * @param string $language
     * @param string $trackingLink
     *
     * @return bool|int
     */
  public function createShipmentByDeliveryNote($deliveryNoteId, $tracking, $deliveryType = '', $language = '', $trackingLink = '')
  {
    $deliveryNote = $this->app->DB->SelectRow(
      sprintf(
        'SELECT adresse,projekt,auftragid, versandart FROM lieferschein WHERE id = %d LIMIT 1',
        $deliveryNoteId
      )
    );
    if(empty($deliveryNote)) {
      return false;
    }
    $addressId = $deliveryNote['adresse'];
    $projectId = $deliveryNote['projekt'];
    $orderId = $deliveryNote['auftragid'];

    if($deliveryType == '') {
      $deliveryType = $deliveryNote['versandart'];
    }
    $kg = $this->app->erp->VersandartMindestgewicht($deliveryNoteId);

    //$tracking = $this->app->erp->TrackingNummerAnpassen($projekt,$tracking);
    $tracking = $this->app->DB->real_escape_string($tracking);

    if($deliveryType === 'versandunternehmen'){
      $deliveryType = (string)$this->app->erp->Firmendaten('versandart');
    }
    $versandid = false;
    if(
      !$this->app->DB->Select(
        sprintf(
          "SELECT id FROM versand WHERE tracking = '%s' AND lieferschein = %d LIMIT 1",
          $tracking, $deliveryNoteId
        )
      )
    ) {
      $keinetrackingmail = 0;
      if($orderId) {
        $keinetrackingmail = (int)$this->app->DB->Select(
          sprintf(
            "SELECT keinetrackingmail FROM auftrag WHERE id = %d LIMIT 1", $orderId
          )
        );
      }
      $versandid = $this->app->DB->Select(
        sprintf(
          "SELECT id 
          FROM versand 
          WHERE lieferschein = %d AND tracking = ''
          ORDER BY weitererlieferschein, improzessuser
          LIMIT 1",
          $deliveryNoteId
        )
      );
      if($versandid) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE versand 
            SET versandunternehmen = '%s', versandart = '%s', tracking = '%s', tracking_link = '%s',
                versendet_am = NOW(), abgeschlossen = 1, lieferschein = %d, freigegeben = 1, firma = 1, adresse = %d,
                projekt = %d, gewicht = %f, paketmarkegedruckt = 1, anzahlpakete = 1, keinetrackingmail = %d,
                improzessuser = 0, improzess = 0
            WHERE id = %d",
            $deliveryType, $deliveryType, $tracking, $trackingLink ,
            (int)$deliveryNoteId, (int)$addressId,
            (int)$projectId, (float)$kg, $keinetrackingmail,
            $versandid
          )
        );
        if($this->app->DB->affected_rows() > 0) {
          return $versandid;
        }
      }

      $this->app->DB->Insert(
        sprintf(
          "INSERT INTO versand (versandunternehmen,versandart, tracking, tracking_link,
                      versendet_am,abgeschlossen,lieferschein, freigegeben,firma,
                     adresse,projekt,gewicht,paketmarkegedruckt,anzahlpakete,keinetrackingmail,
                     logdatei,versender,bearbeiter,download,rechnung)
          VALUES ('%s','%s','%s','%s',
                                NOW(),1,%d, 1,1,
                                %d,%d,%f,1,1,%d,NOW(),'','',0,0) ",
          $deliveryType, $deliveryType, $tracking, $trackingLink ,
          (int)$deliveryNoteId,
          (int)$addressId, (int)$projectId, (float)$kg, $keinetrackingmail
        )
      );
      $versandid = $this->app->DB->GetInsertID();
    }

    return $versandid;
  }

    /**
     * @param Uebertragungen $transferobject
     * @param int            $deliveryNoteId
     * @param string         $tracking
     * @param string         $deliveryType
     * @param string         $language
     * @param string         $trackingLink
     */
  public function addTrackingToDeliveryNote(
    $transferobject,
    $deliveryNoteId,
    $tracking,
    $deliveryType = '',
    $language = '',
    $trackingLink = ''
  ) {
    $versandid = $this->createShipmentByDeliveryNote(
      $deliveryNoteId,
      $tracking,
      $deliveryType,
      $language,
      $trackingLink
    );
    if(empty($versandid)) {
      return;
    }

    $deliveryNote = $this->app->DB->SelectRow(
      sprintf(
        'SELECT adresse, projekt, auftragid, versandart FROM lieferschein WHERE id = %d LIMIT 1',
        $deliveryNoteId
      )
    );

    if(empty($deliveryNote)) {
      return;
    }
    if(!empty($this->uebertragung_account) && !empty($deliveryType) && $deliveryType !== $deliveryNote['versandart']) {
      if(!empty(
        $this->app->DB->Select(
          "SELECT `update_shipping_method` 
          FROM `uebertragungen_account` 
          WHERE `id` = {$this->uebertragung_account}"
        )
        )
      ) {
        $shippingMethodEscaped = $this->app->DB->real_escape_string($deliveryType);
        $isShippingMethodExists = !empty(
        $this->app->DB->Select(
          "SELECT `id` FROM `versandarten` WHERE `aktiv` = 1 AND `type` = '{$shippingMethodEscaped}' LIMIT 1"
        ));
        if($isShippingMethodExists) {
          $this->app->DB->Update(
            "UPDATE `lieferschein` SET `versandart` = '$shippingMethodEscaped' WHERE `id` = {$deliveryNoteId}"
          );
          $deliveryNote['versandart'] = $deliveryType;
        }
      }
    }

    $orderId = $deliveryNote['auftragid'];

    if($transferobject) {
      if($orderId) {
        $this->app->erp->AuftragProtokoll($orderId, 'R&uuml;ckmeldung durch &Uuml;bertragenmodul');
      }
      $this->app->erp->LieferscheinProtokoll($deliveryNoteId,'R&uuml;ckmeldung durch &Uuml;bertragenmodul');
    }

    $tranferRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT trackingmail, autoshopexport FROM uebertragungen_account WHERE id = %d LIMIT 1',
        $this->uebertragung_account
      )
    );

    if(!empty($this->app->remote) && (empty($transferobject) || !empty($tracking) || empty($tranferRow['autoshopexport']))) {
      $this->app->erp->VersandAbschluss($versandid);
    }
    $tracking_link = null;
    $sprache = null;
    if(!empty($language)){
      $sprache = $language;
    }
    if(!empty($trackingLink)){
      $tracking_link = $trackingLink;
    }
    if(!empty($sprache)){
      $sprache = strtolower(trim($sprache));
    }
    if($sprache === 'english'){
      $sprache = 'englisch';
    }
    if(empty($sprache)) {
      $sprache = $this->app->DB->Select(
        sprintf('SELECT sprache FROM lieferschein WHERE id = %d LIMIT 1',$deliveryNoteId)
      );
      if(empty($sprache) && $orderId){
        $sprache = $this->app->DB->Select(
          sprintf('SELECT sprache FROM auftrag WHERE id = %d LIMIT 1', $orderId)
        );
      }
    }
    if($tranferRow['trackingmail']) {
      $this->app->erp->Versandmail($versandid, $tracking_link, $sprache, $tracking, true);
    }
    /** @var Versanderzeugen $objVersanderzeugen */
    $objVersanderzeugen = $this->app->erp->LoadModul('versanderzeugen');
    if($objVersanderzeugen && method_exists($objVersanderzeugen,'CheckKommissionierungByLieferschein')) {
      $objVersanderzeugen->CheckKommissionierungByLieferschein($deliveryNoteId);
    }
    //Rechnungsmail
    $invoceId= $this->app->DB->Select(
      sprintf(
        "SELECT id FROM rechnung where auftragid = %d and status <> 'storniert' LIMIT 1",
        $orderId
      )
    );
    if(!$invoceId && $this->app->DB->Select(
      sprintf(
      'SELECT rechnunganlegen FROM uebertragungen_account WHERE id = %d LIMIT 1',
        (int)$this->uebertragung_account
      )
      ))
    {
      $invoceId = $this->app->erp->WeiterfuehrenAuftragZuRechnung($orderId);
      $this->app->erp->BelegFreigabe('rechnung', $invoceId);
      $invoiceDocument = 'rechnung';
      $transferAccountId = (int)$this->uebertragung_account;
      $this->app->erp->RunHook(
        'transfer_document_incoming', 3, $transferAccountId, $invoiceDocument, $invoceId
      );
    }
    if($this->app->DB->Select(
      sprintf(
      "SELECT rechnungmail FROM uebertragungen_account WHERE id = %d LIMIT 1",
        (int)$this->uebertragung_account
      )
    ))
    {
      if($invoceId && (int)$this->app->DB->Select(
        sprintf(
        "SELECT count(id) FROM versand WHERE tracking <> '' AND lieferschein = %d",
            $deliveryNoteId
          )
        ) < 2)
      {
        if($this->app->DB->Select(sprintf("SELECT soll FROM rechnung WHERE id = %d LIMIT 1", $invoceId)) > 0){
          $this->app->erp->Rechnungsmail($invoceId);
        }
      }
    }
    $element1 = $tracking;
    if($transferobject) {
      $transferobject->AddUbertragungMonitorLog(
        $this->uebertragung_account,
        $this->datei_id,
        0,
        'tracking_ok',
        '',
        $element1,
        '',
        '',
        'versand',
        $versandid
      );
    }
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO uebertragungen_log (uebertragungen_account, typ, parameter1,parameter2, wert) 
        VALUES (%d,'lieferschein',%d,'tracking','%s')",
        (int)$this->uebertragung_account,(int)$deliveryNoteId,$tracking
      )
    );
  }

  function LoadFromXML($dateiname, $isfile = true)
  {
    if(!$isfile || is_file($dateiname))
    {
      if($isfile)
      {
        $content = file_get_contents($dateiname);
      }else{
        $content = $dateiname;
      }
      $xml = @simplexml_load_string($content,null, LIBXML_NOCDATA);// true, true, $action);
      if($xml === false || is_null($xml))
      {
        $xml = json_decode($content);
      }
      return $xml;
    }
    return false;
  }

    /**
     * @param string $string
     *
     * @return string
     */
  public function EntferneSteuerzeichen($string)
  {
    $len = strlen($string);
    $out = '';
    for($i = 0; $i < $len; $i++) {
      $ord = ord($string[$i]);
      if($ord != 127 && ($ord > 31 || $ord == 13 || $ord == 10 || $ord == 9)) {
        $out .= $string[$i];
      }
    }

    return $out;
  }

  public function SaveXML(&$xml, $dateiname, $action = '', $tag = '')
  {
    if(!$dateiname) {
      return false;
    }
    if(!$xml) {
      return false;
    }

    $content = $this->XMLResponse(1,$xml, true, true, $action);
    if(empty($content)) {
      return false;
    }

    if(strpos($dateiname,'/') === false) {
      $dateiname = rtrim($this->app->Config->WFuserdata,'/').'/'.$dateiname;
    }
    $folder = dirname($dateiname);
    if($folder !== '.' && !is_dir($folder) && !mkdir($folder,0700,true) && !is_dir($folder)) {
      $this->app->erp->LogFile($folder.' konnte nicht erstellt werden');
    }
    $content = $this->EntferneSteuerzeichen($content);
    if(!empty($tag)) {
      $content = str_replace($tag,$tag."\n",$content);
    }

    $erg = file_put_contents($dateiname, $content);
    return $erg !== false;
  }

  function CreateXmlObj()
  {
$xmlstr = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<response>
</response>
XML;
    return new SimpleXMLExtended($xmlstr);
  }

  public function ApiZeiterfassungEdit($id = null)
  {
    $xml = $this->XMLPost();
    if(!$id)
    {
      $id = (int)$this->app->Secure->GetGET('id');
    }
    if(!empty($xml['id']))
    {
      $id = (int)$xml['id'];
    }
    if(!$id)
    {
      $this->XMLResponse(5);
    }
    if($id && $zeiterfassungen =  $this->app->DB->SelectArr("SELECT * FROM zeiterfassung WHERE id = '$id' LIMIT 1"))
    {
      foreach($xml as $k => $v)
      {
        if($k != 'id' && array_key_exists($k,$zeiterfassungen[0]))
        {
          if(($k != 'von' || $v != '') && ($k != 'bis' || $v != ''))
          {
            $arr[] = " $k = '".$this->app->DB->real_escape_string($v)."' ";
          }else{
            $arr[] = " $k = '0000-00-00' ";
          }
        }
        if($k !== 'id' && $k === 'mitarbeiternummer'){
          $mitarbeiterid = $this->app->DB->Select("SELECT id FROM adresse where mitarbeiternummer = '".$this->app->DB->real_escape_string($v)."' AND mitarbeiternummer <> '' AND geloescht=0 LIMIT 1");
          if($mitarbeiterid){
            $arr[] = " adresse = '$mitarbeiterid' ";
          }
        }
      }
      if(isset($arr))
      {
        $this->app->DB->Update("UPDATE zeiterfassung SET ".implode(', ',$arr)." WHERE id = '$id' LIMIT 1");
      }
      $this->XMLResponse(1);
    }else{
      $this->XMLResponse(8);
    }
    $this->app->ExitXentral();
  }

  public function ApiZeiterfassungDelete()
  {
    $xml = $this->XMLPost();
    $id = (int)$this->app->Secure->GetGET('id');
    if(!empty($xml['id']))
    {
      $id = (int)$xml['id'];
    }
    if($id && $this->app->DB->Select("SELECT id FROM zeiterfassung WHERE id = '$id' LIMIT 1"))
    {
      $this->app->DB->Delete("DELETE FROM zeiterfassung WHERE id = '$id' LIMIT 1");
      $this->XMLResponse(1);
    }else{
      $this->XMLResponse(8);
    }
    $this->app->ExitXentral();
  }

  public function ApiZeiterfassungCreate()
  {
    $xml = $this->XMLPost();
    if((!empty($xml['mitarbeiternummer']) || !empty($xml['adresse'])) && !empty($xml['aufgabe']) && !empty($xml['von']) && !empty($xml['bis']))
    {
      $this->app->DB->Insert("INSERT INTO zeiterfassung (id) values ('')");
      $id = $this->app->DB->GetInsertID();
      if($id)
      {
        $this->ApiZeiterfassungEdit($id);
      }else{
        $this->XMLResponse(5);
      }
    }else{
      $this->XMLResponse(5);
    }
    $this->app->ExitXentral();
  }

  public function ApiZeiterfassungGet()
  {
    $xml = $this->XMLPost();
    $where = "1";
    $offset = 0;
    $limit = 1000000;
    if(!empty($xml['offset']))$offset = (int)$xml['offset'];
    if($offset < 0)$offset = 0;
    if(!empty($xml['limit']))$limit = (int)$xml['limit'];
    if($limit <= 0)$limit = 1;
    if(!empty($xml['adresse']))
    {
      $where .= " AND adresse = '".((int)$xml['adresse'])."' ";
    }elseif(!empty($xml['kundennummer']))
    {
      $projekt = 0;
      if(!empty($xml['projekt']))
      {
        if(is_numeric($xml['projekt']))
        {
          $projekt = (int)$xml['projekt'];
        }else{
          $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$this->app->DB->real_escape_string($xml['projekt'])."' LIMIT 1");
        }
      }
      $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer <> '' AND geloescht <> 1 AND kundennummer = '".$this->app->DB->real_escape_string($xml['kundennummer'])."' ".(!empty($xml['projekt'])?" AND projekt = '$projekt' ":'')." LIMIT 1 ");
      $where .= " AND adresse = '$adresse' ";
    }
    if(!empty($xml['von']))
    {
      if(strlen($xml['von'] > 11))
      {
        $where .= "AND ((von != '0000-00-00' AND von >= '".$this->app->DB->real_escape_string($xml['von'])."') OR (bis != '0000-00-00' AND bis >= '".$this->app->DB->real_escape_string($xml['von'])."') ) ";
      }else{
        $where .= "AND ((von != '0000-00-00' AND date(von) >= '".$this->app->DB->real_escape_string($xml['von'])."') OR (bis != '0000-00-00' AND date(bis) >= '".$this->app->DB->real_escape_string($xml['von'])."') ) ";
      }
    }
    if(!empty($xml['bis']))
    {
      if(strlen($xml['bis'] > 11))
      {
        $where .= "AND ((von != '0000-00-00' AND von <= '".$this->app->DB->real_escape_string($xml['bis'])."') OR (bis != '0000-00-00' AND bis <= '".$this->app->DB->real_escape_string($xml['bis'])."') ) ";
      }else{
        $where .= "AND ((von != '0000-00-00' AND date(von) <= '".$this->app->DB->real_escape_string($xml['bis'])."') OR (bis != '0000-00-00' AND date(bis) <= '".$this->app->DB->real_escape_string($xml['bis'])."') ) ";
      }
    }
    $xml_obj = $this->CreateXmlObj();
    $zeiterfassungen = $this->app->DB->SelectArr("SELECT * FROM zeiterfassung WHERE $where LIMIT $offset, $limit");
    if($zeiterfassungen)
    {
      $this->AddToXMLObj($xml_obj, 'zeiterfassung', 'zeiterfassungen',$zeiterfassungen);
    }

    $this->XMLResponse(1,$xml_obj, true, false);
    $this->app->ExitXentral();
  }

  function ReplaceUmlauteArray(&$arr, $lvl = 0)
  {
    if($lvl > 10) {
      return;
    }
    if(is_array($arr)) {
      foreach($arr as $elk => $elv) {
        $this->ReplaceUmlauteArray($arr[$elk], $lvl + 1);
      }
    }
    elseif(is_string($arr)) {
      $arr = str_replace(
        array('&uuml;','&ouml;','&auml;','&Uuml;','&Ouml;','&Auml;','&szlig;'),
        array('ü','ö','ä','Ü','Ö','Ä','ß'),
        $arr
      );
    }
  }

  function AddElements(&$xml, $array,$name, $lvl = 0)
  {
    if($lvl > 10) {
      return;
    }
    if(!is_array($array)){
      return;
    }
    $first = true;
    foreach($array as $k => $v) {
      if(is_numeric($k)) {
        if(is_array($v)) {
          if($first) {
            if(isset($xml->$name)) {
              $this->AddElements($xml->$name, $v, $name, $lvl+1);
            }
            else{
              $child = $xml->AddChild($name,'');
              $this->AddElements($child, $v, $name, $lvl+1);
            }
          }
          else{
            $child = $xml->AddChild($name,'');
            $this->AddElements($child, $v, $name, $lvl+1);
          }
        }
        else{
          $xml->AddChild($name, $v);
        }
      }
      else{
        if(is_array($v)) {
          if($k != $name)
          {
            $parent = $xml->AddChild($name, '');
            $name = $k;
            $this->AddElements($parent, $v, $k, $lvl+1);
            //$child = $parent->AddChild($k, '');
          }
          else{
            $child = $xml->AddChild($k, '');
            $this->AddElements($child, $v, $k, $lvl+1);
          }
        }
        else{
          if(isset($xml->$k)) {
            $xml->$k = $v;
          }
          else{
            $child = $xml->AddChild($k, $v);
          }
        }
      }
      $first = false;
    }
  }

  function AddToXMLObj(&$xml_obj, $elementname, $parentelementname, $data, &$toelment = null, &$erg = null)
  {
    $this->ReplaceUmlauteArray($data);

    if($toelment === null) {
      if($xml_obj === null) {
        $xml_obj = $this->CreateXmlObj();
      }
      if(!isset($xml_obj->xml))
      {
        $xml_obj->AddChild('xml','');
      }
      if(is_array($data))
      {
        if($parentelementname)
        {
          if(isset($xml_obj->xml->$parentelementname))
          {
            $parent = $xml_obj->xml->$parentelementname;
          }else{
            $parent = $xml_obj->xml->AddChild($parentelementname,'');
          }
          foreach($data as $k => $c)
          {
            $child = $parent->AddChild($elementname, '');
            if(is_array($c))
            {
              foreach($c as $key => $value)
              {
                if(is_array($value))
                {
                  $this->AddElements($child, $value, is_numeric($key)?$elementname:$key);
                }else{
                  $child->$key = $value;
                }
              }
            }
          }
          $erg = $child;
        }
        else{
          if(isset($data[0])) {
            foreach($data as $k => $c) {
              $child = $xml_obj->xml->AddChild($elementname, '');
              if(is_array($c)) {
                foreach($c as $key => $value) {
                  $child->$key = $value;
                }
              }
            }
            $erg = $child;
          }
          else{
            $child = $xml_obj->xml->AddChild($elementname, '');
            foreach($data as $key => $value) {
              $child->$key = $value;
            }
            $erg = $child;
          }
        }
      }
      else{
        $child = $xml_obj->xml->AddChild($elementname, $data);
        $erg = $child;
      }
    }
    else{
      if(is_array($data))
      {
        if($parentelementname)
        {
          if(isset($toelment->$parentelementname))
          {
            $parent = $toelment->$parentelementname;
          }else{
            $parent = $toelment->AddChild($parentelementname,'');
          }
          foreach($data as $k => $c)
          {
            $child = $parent->AddChild($elementname, '');
            if(is_array($c))
            {
              foreach($c as $key => $value)
              {
                $child->$key = $value;
              }
            }
            $erg = $child;
          }
        }
        else{
          if(isset($data[0]))
          {
            foreach($data as $k => $c)
            {
              $child = $toelment->AddChild($elementname, '');
              if(is_array($c))
              {
                foreach($c as $key => $value)
                {
                  $child->$key = $value;
                }
              }
              $erg = $child;
            }
          }else{
            $toelment->$elementname = $data;
            $erg = $toelment->$elementname;
          }
        }
      }else{
        $child = $toelment->AddChild($elementname, $data);
        $erg = $child;
      }
    }
  }

  function XmlToJSON($xml)
  {
    $simplexml = simplexml_load_string($xml, null, LIBXML_NOCDATA);
    $array = $this->ObjectToArray($simplexml);

    return json_encode($array);
  }

  function ObjectToArray($object)
  {
    $array = (array)$object;
    if (empty($array)) {
      return '';
    }

    foreach ($array as $key => $value) {
      if (is_object($value) || is_array($value)) {
        $array[$key] = $this->ObjectToArray($value);
      }
    }

    return $array;
  }

  function XMLResponse($messagecode,$xml_inside="", $xml_obj = false, $return = false, $action = '')
  {

    if(!$action)
    {
      if(isset($this->app->Secure)) {
        $action = $this->app->Secure->GetGET("action");
      }
    }
    if($xml_obj)
    {
      if(!isset($xml_inside->status))$status = $xml_inside->AddChild('status','');
      if(!isset($xml_inside->status->action))$xml_inside->status->AddChild('action',$action);
      if(!isset($xml_inside->status->message))$xml_inside->status->AddChild('message',utf8_encode($this->MessageCode($messagecode)));
      if(!isset($xml_inside->status->messageCode))$xml_inside->status->AddChild('messageCode',$messagecode);
      if($return)
      {
        if($this->usejson)
        {
          return $this->XmlToJSON($xml_inside->asXML());
        }else
          return $xml_inside->asXML();
      }
      if($this->usejson)
      {
        header('Content-Type: application/json; charset=utf-8');
        echo $this->XmlToJSON($xml_inside->asXML());
      }else {
        header('Content-Type: application/xml; charset=utf-8');
        echo $xml_inside->asXML();
      }
      $this->app->ExitXentral();
    }

    // if further xml is given
    $append = "";
    if($xml_inside!="")
    {
      //$md5sum = md5($xml_inside);

      //$append = '<md5sum>'.$md5sum.'</md5sum>';
      if($this->usejson) {
        $append .='<data>'.$xml_inside.'</data>';
      }else
        $append .='<xml>'.$xml_inside.'</xml>';
    }

    if($this->usejson) {
      header('Content-Type: application/json; charset=utf-8');
      echo $this->XmlToJSON("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
      <response>
      <status>
      <action>$action</action>
      <message>" . utf8_encode($this->MessageCode($messagecode)) . "</message>
      <messageCode>" . $messagecode . "</messageCode>
      </status>
      " . $append . "
      </response>");

    }
    else{
      header('Content-Type: application/xml; charset=utf-8');

      echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
      <response>
      <status>
      <action>$action</action>
      <message>" . utf8_encode($this->MessageCode($messagecode)) . "</message>
      <messageCode>" . $messagecode . "</messageCode>
      </status>
      " . $append . "
      </response>";
    }
    $this->app->ExitXentral();
  }

  /**
   * @param mixed $arr
   * @param bool  $escape
   *
   * @return void
   */
  public function XMLCleanUTF8( &$arr, $escape = false, $isHtmlTransformation = false ): void
  {
    if(empty($arr) || !is_array($arr)) {
      return;
    }

    foreach ($arr as $idx => $value) {
      if (is_array($value)) {
        $this->XMLCleanUTF8($arr[$idx], $escape, $isHtmlTransformation);
        continue;
      }
      if (!is_string($value)) {
        continue;
      }
      if($escape) {
        $arr[$idx] = $this->app->DB->real_escape_string($value);
        continue;
      }

      $arr[$idx] = $this->app->erp->ConvertForDBUTF8($value, true, $isHtmlTransformation);
    }
  }


  function XMLClean( &$arr, $isHtmlTransformation = false ) {
    if($arr && is_array($arr)) {
      foreach ($arr as $idx => $value) {
        if (is_array($value)) $this->XMLClean($arr[$idx], $isHtmlTransformation);
        else {
          if (is_string($value)) {
            if (mb_detect_encoding($value, 'UTF-8, ISO-8859-1') === 'UTF-8')
              $arr[$idx] = $this->app->erp->ConvertForDB($value, true, $isHtmlTransformation);
            else
              $arr[$idx] = $this->app->erp->ConvertForDBUTF8($value, true, $isHtmlTransformation);
          }
        }
      }
    }
  }

  function XMLPostPlain()
  {
    $xml = $this->app->Secure->POST["xml"];

    if($this->app->Secure->GetGET('cdata'))
    {
      $this->usecdata = true;
    }
    if($this->app->Secure->GetGET('json'))
    {
      $this->usejson = true;
    }
    /*
    //$xml = $this->app->Secure->GetPOST("xml");
    $xml = mysqli_real_escape_string($this->app->DB->connection,$this->app->Secure->POST["xml"]);
    $xml = str_replace('<?xml version=\"1.0\" encoding=\"UTF-8\"?>','<?xml version="1.0" encoding="UTF-8"?>',$xml);
    $xml = str_replace('>\n','>',$xml);
    $xml = str_replace('\"','"',$xml);
    $xml = str_replace('&#13;','',$xml);
    //$xml = str_replace('&','&amp;',$xml);
     */

    $deXml = simplexml_load_string($xml, null
      , LIBXML_NOCDATA);
    if($deXml === false && $xml)
    {
      $deXml = json_decode($xml, true);
      if($deXml)$this->usejson = true;
    }
    if(isset($deXml->xml) && isset($deXml->xml->cdata) && $deXml->xml->cdata)
    {
      $this->usecdata = true;
    }
    elseif(isset($deXml->cdata) && $deXml->cdata)$this->usecdata = true;
    if($this->usejson)$this->usecdata = true;
    return $deXml;
  }

  function XMLPost()
  {
    if(isset($this->app->Secure->POST['xml'])) {
      $xml = $this->app->Secure->POST['xml'];
    }
    elseif(isset($this->app->Secure->POST['json']))
    {
      $xml = $this->app->Secure->POST['json'];
    }
    else {
      $xml = '';
    }

    /*
    //$xml = $this->app->Secure->GetPOST("xml");
    $xml = mysqli_real_escape_string($this->app->DB->connection,$this->app->Secure->POST["xml"]);
    $xml = str_replace('<?xml version=\"1.0\" encoding=\"UTF-8\"?>','<?xml version="1.0" encoding="UTF-8"?>',$xml);
    $xml = str_replace('>\n','>',$xml);
    $xml = str_replace('\"','"',$xml);
    $xml = str_replace('&#13;','',$xml);
    //$xml = str_replace('&','&amp;',$xml);
     */

    $deXml = simplexml_load_string($xml, null
      , LIBXML_NOCDATA);
    if($deXml === false && $xml)
    {
      $deXml = json_decode($xml, true);
      if($deXml)$this->usejson = true;
    }
    if($this->app->Secure->GetGET('json'))
    {
      $this->usejson = true;
    }
    $deJson = json_encode($deXml);
    $xml_array = json_decode($deJson,TRUE);
    if(isset($xml_array['cdata']) && $xml_array['cdata'])
    {
      $this->usecdata = true;
    }elseif(isset($xml_array['xml']) && isset($xml_array['xml']['cdata']) && $xml_array['xml']['cdata'])
    {
      $this->usecdata = true;
    }
    if($this->app->Secure->GetGET('cdata'))
    {
      $this->usecdata = true;
    }
    if($this->usejson)$this->usecdata = true;

    try {
      $api_cleanutf8 = $this->getApiAccount()->isCleanUtf8Active();
      $isHtmlTransformation = $this->getApiAccount()->isHtmlTransformationActive();
    } catch (ApiAccountNotFoundException $e){
      $api_cleanutf8 = false;
      $isHtmlTransformation = false;
    }


    if(isset($xml_array['xml'])) {
      $tmp = $xml_array['xml'];
    }elseif($this->usejson) {
      $tmp = $xml_array;
    }

    $action = $this->app->Secure->GetGET('action');

    if(in_array($action,
      [
        'AuftragEdit',
        'AuftragCreate',
        'BestellungEdit',
        'BestellungCreate',
        'AngebotCreate',
        'AngebotEdit',
        'LieferscheinCreate',
        'LieferscheinEdit',
        'RetoureCreate',
        'RetoureEdit',
        'RechnungCreate',
        'RechnungEdit',
        'GutschriftCreate',
        'GutschriftEdit'
      ]
    ))
    {
      if($api_cleanutf8 == true){
        $this->XMLCleanUTF8($tmp, false, $isHtmlTransformation);
      }
      else{
        $this->XMLClean($tmp, $isHtmlTransformation);
      }
    }
    else if(in_array($action, ['AdresseEdit','AdresseCreate','AdresseGet']))
    {
      if($api_cleanutf8 == true){
        $this->XMLCleanUTF8($tmp, true,$isHtmlTransformation);
      }
      else{
        $this->XMLClean($tmp, $isHtmlTransformation);
      }
    }
    else if(in_array($action, ['ArtikelEdit', 'ArtikelCreate','ArtikelGet']))
    {
      if($api_cleanutf8 == true){
        $this->XMLCleanUTF8($tmp, false, $isHtmlTransformation);
      }
      else{
        $this->XMLClean($tmp, $isHtmlTransformation);
      }
    }
    else if(in_array($action, ['GruppeEdit','GruppeCreate']))
    {
      if($api_cleanutf8 == true){
        $this->XMLCleanUTF8($tmp, false, $isHtmlTransformation);
      }
      else{
        $this->XMLClean($tmp, $isHtmlTransformation);
      }
    }
    else{
      if($action==='ArtikelList') {
        return $tmp;
      }
      $this->XMLClean($tmp);
    }
    return $tmp;
  }

  function MessageCode($messagecode)
  {
    switch($messagecode)
    {
      case 1: $message = "OK"; break;
      case 2: $message = "Wrong Hash"; break;
      case 3: $message = "Wrong number of GET parameters"; break;
      case 4: $message = "Wrong XML data structure for method"; break;
      case 5: $message = "Invalid key (id)"; break;
      case 6: $message = "Wrong md5sum"; break;
      case 7: $message = "Username exists already"; break;
      case 8: $message = "Data not found"; break;
      case 9: $message = "Wrong or no api_id provided. (https://community.xentral.com/hc/de/articles/360017436919-API-Dokumentation#toc-0)"; break;
      default: $message = "Unknown message code";
    }
    return $message;
  }

  function ApiAdresseCreate($internal=false)
  {
    $xmldata = $this->XMLPost();
    // anlegen der adresse
    if($xmldata['name']!="" && !is_array($xmldata['name']))
    {
      //adresse anlegen
      $xmldata['strasse'] = $xmldata['strasse']." ".$xmldata['hausnummer'];
      if($xmldata['strasse_hausnummer']!="" && !is_array($xmldata['strasse_hausnummer'])) $xmldata['strasse'] = $xmldata['strasse_hausnummer'];

      $xmldata['email'] = str_replace(" ","",$xmldata['email']);

      switch($xmldata['typ'])
      {
        case "mr": $xmldata['typ']="herr"; break;
        case "mr.": $xmldata['typ']="herr"; break;
        case "ms": $xmldata['typ']="frau"; break;
        case "mrs": $xmldata['typ']="frau"; break;
        case "mrs.": $xmldata['typ']="frau"; break;
      }

      if($xmldata['firma']!="" && !is_array($xmldata['firma']))
      {
        if($xmldata['vorname']!="" && !is_array($xmldata['vorname']))
          $xmldata['ansprechpartner']=$xmldata['vorname']." ".$xmldata['name'];
        else
          $xmldata['ansprechpartner']=$xmldata['name'];
        $xmldata['name']=$xmldata['firma'];
        $xmldata['typ']='firma';
      } else
      {
       if($xmldata['vorname']!="" && !is_array($xmldata['vorname']))
         $xmldata['name']=$xmldata['vorname']." ".$xmldata['name'];
      }

    }

    $id = $this->app->erp->CreateAdresse($xmldata['name']);

    //kundennummer=1 bedeutet gleiche Rolle Kunde anlegen
    if($xmldata['projekt']!="" && !is_array($xmldata['projekt'])){
      $xmldata['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='" . $xmldata['projekt'] . "' LIMIT 1");
    }
    else{
      $xmldata['projekt'] = $this->app->erp->GetStandardProjekt();
    }

    $tmp_data_adresse = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id='$id' LIMIT 1");

    if(strtoupper($xmldata['kundennummer'])==='NEW' || strtoupper($xmldata['kundennummer'])==='NEU')
    {
      $xmldata['kundennummer'] = $this->app->erp->GetNextKundennummer($xmldata['projekt'],$tmp_data_adresse);
      $this->app->erp->ObjektProtokoll("adresse",$id,"adresse_next_kundennummer","Kundennummer ".$xmldata['kundennummer']." erhalten");
    }

    if(strtoupper($xmldata['lieferantennummer'])==='NEW' || strtoupper($xmldata['lieferantennummer'])==='NEU')
    {
      $xmldata['lieferantennummer'] = $this->app->erp->GetNextLieferantennummer($xmldata['projekt'],$tmp_data_adresse);
      $this->app->erp->ObjektProtokoll("adresse",$id,"adresse_next_lieferantennummer","Lieferantennummer ".$xmldata['lieferantennummer']." erhalten");
    }

    if($xmldata['porto_preis']!="" && $xmldata['porto_artikelid']!="" && !is_array($xmldata['porto_preis']))
      $this->app->erp->AddVerkaufspreis($xmldata['porto_artikelid'],1,$id,$xmldata['porto_preis']);


    foreach($xmldata as $key=>$value)
    {
      if(is_array($value)) {
        $value='';
      }
      if($key==='sonstiges')  {
        $value = strip_tags(html_entity_decode($value));
      }
      if($key==='rabattinformation') {
        $value = html_entity_decode($value);
      }
      if($key === 'geburtsdatum' && strpos($value,'.') !== false) {
        $value = $this->app->String->Convert($value,'%1.%2.%3','%3-%2-%1');
      }
      if($key!=='id'){
        $this->app->DB->Update("UPDATE adresse SET $key='$value' WHERE id='$id' LIMIT 1");
      }
    }

    // Wenn Kundennummer vorhanden sofort Lieferant anlegen
    if($xmldata['kundennummer']!="" && !is_array($xmldata['kundennummer']))
      $this->app->erp->AddRolleZuAdresse($id, "Kunde", "von", "Projekt", $xmldata['projekt'] );

    if($xmldata['lieferantennummer']!="" && !is_array($xmldata['lieferantennummer']))
      $this->app->erp->AddRolleZuAdresse($id, "Lieferant", "von", "Projekt", $xmldata['projekt'] );

    if($xmldata['verband']!="" && !is_array($xmldata['verband']))
      $this->app->erp->AddRolleZuAdresse($id, "Kunde", "von", "Gruppe", $xmldata['verband']);

    if($xmldata['liefername']!="" && !is_array($xmldata['liefername']))
    {
      $xmldata['liefername'] = $xmldata['liefervorname']." ".$xmldata['liefername'];
      $xmldata['lieferstrasse'] = $xmldata['lieferstrasse']." ".$xmldata['lieferhausnummer'];

      if($xmldata['lieferfirma']!="" && !is_array($xmldata['lieferfirma']))
      {
        $xmldata['lieferadresszusatz']=$xmldata['liefervorname']." ".$xmldata['liefername'];
        $xmldata['liefername']=$xmldata['lieferfirma'];                $xmldata['liefertyp']='firma';
      }

      $xmldata['liefername'] = trim($xmldata['liefername']);
      $xmldata['lieferstrasse'] = trim($xmldata['lieferstrasse']);
      $xmldata['lieferadresszusatz'] = trim($xmldata['lieferadresszusatz']);

      if(is_array($xmldata['lieferabteilung'])) $xmldata['lieferabteilung'] = "";
      if(is_array($xmldata['lieferunterabteilung'])) $xmldata['lieferunterabteilung'] = "";
      if(is_array($xmldata['lieferland'])) $xmldata['lieferland'] = "";
      if(is_array($xmldata['lieferstrasse'])) $xmldata['lieferstrasse'] = "";
      if(is_array($xmldata['lieferort'])) $xmldata['lieferort'] = "";
      if(is_array($xmldata['lieferplz'])) $xmldata['lieferplz'] = "";
      if(is_array($xmldata['liefertelefon'])) $xmldata['liefertelefon'] = "";
      if(is_array($xmldata['liefertelefax'])) $xmldata['liefertelefax'] = "";
      if(is_array($xmldata['lieferemail'])) $xmldata['lieferemail'] = "";
      if(is_array($xmldata['lieferansprechpartner'])) $xmldata['lieferansprechpartner'] = "";
      if(is_array($xmldata['liefertyp'])) $xmldata['liefertyp'] = "";
      if(is_array($xmldata['lieferadresszusatz'])) $xmldata['lieferadresszusatz'] = "";

      $this->app->DB->Insert("INSERT INTO lieferadressen                   
                  (id,name,abteilung,unterabteilung,land,strasse,ort,plz,telefon,telefax,email,ansprechpartner,adresse,typ,adresszusatz,standardlieferadresse)
                  VALUES ('','{$xmldata['liefername']}','{$xmldata['lieferabteilung']}','{$xmldata['lieferunterabteilung']}',                    
                  '{$xmldata['lieferland']}','{$xmldata['lieferstrasse']}','{$xmldata['lieferort']}',
                    '{$xmldata['lieferplz']}','{$xmldata['liefertelefon']}','{$xmldata['liefertelefax']}','{$xmldata['lieferemail']}',
                    '{$xmldata['lieferansprechpartner']}','$id','{$xmldata['liefertyp']}','{$xmldata['lieferadresszusatz']}',1)");
    }

    if(!empty($xmldata['dateien']) && is_array($xmldata['dateien']))
    {
      $this->AddFiles($xmldata['dateien'], 'adresse', $id);
    }

    if($xmldata['lieferantennummer']=="")
    {
      if($internal) return $xmldata['kundennummer'];
      $this->XMLResponse(1,"<id>$id</id><kundennummer>".$xmldata['kundennummer']."</kundennummer>");
    } else {
      if($internal) return $xmldata['lieferantennummer'];
      $this->XMLResponse(1,"<id>$id</id><lieferantennummer>".$xmldata['lieferantennummer']."</lieferantennummer>");
    }
    $this->app->ExitXentral();
  }

  function ApiAdresseEdit($intern = false)
  {
    $xmldata = $this->XMLPost();
    if(isset($xmldata['adresse']))
    {
      if(is_array($xmldata['adresse']) && isset($xmldata['adresse'][0]))
      {
      }else{
        $xmldata['adresse'][0] = $xmldata['adresse'];
      }
      foreach($xmldata['adresse'] as $_key => $_xmldata)
      {

        $id = 0;
        $kundennummer = '';
        $projekt = 0;
        if(isset($_xmldata['id']))$id = (int)$_xmldata['id'];
        if(isset($_xmldata['kundennummer']))$kundennummer = $this->app->DB->real_escape_string($_xmldata['kundennummer']);
        if(isset($_xmldata['projekt']))$projekt = (int)$_xmldata['projekt'];

        if($kundennummer!="")
        {
          if($projekt !="")
          {
            $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
            $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' AND projekt='$projekt' LIMIT 1");
          } else {
            $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' LIMIT 1");
          }
        }
        if($id)
        {
          if($_xmldata['porto_preis']!="" && $_xmldata['porto_artikelid']!="" && !is_array($_xmldata['porto_preis']))
            $this->app->erp->AddVerkaufspreis($_xmldata['porto_artikelid'],1,$id,$_xmldata['porto_preis']);

          if($_xmldata['projekt']!="" && !is_array($_xmldata['projekt']) && !is_numeric($_xmldata['projekt']))
            $_xmldata['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$_xmldata['projekt']."' LIMIT 1");

          $tmp_data_adresse = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id='$id' LIMIT 1");

          if(strtoupper($_xmldata['kundennummer'])==='NEW' || strtoupper($_xmldata['kundennummer'])==='NEU')
          {

            $_xmldata['kundennummer'] = $this->app->erp->GetNextKundennummer($_xmldata['projekt'],$tmp_data_adresse);
            $this->app->erp->ObjektProtokoll("adresse",$id,"adresse_next_kundennummer","Kundennummer ".$_xmldata['kundennummer']." erhalten");
          }

          if(strtoupper($_xmldata['lieferantennummer'])==='NEW' || strtoupper($_xmldata['lieferantennummer'])==='NEU')
          {
            $_xmldata['lieferantennummer'] = $this->app->erp->GetNextLieferantennummer($_xmldata['projekt'],$tmp_data_adresse);
            $this->app->erp->ObjektProtokoll("adresse",$id,"adresse_next_lieferantennummer","Lieferantennummer ".$_xmldata['lieferantennummer']." erhalten");
          }

          if($_xmldata['kundennummer']!="" && !is_array($_xmldata['kundennummer']))
            $this->app->erp->AddRolleZuAdresse($id, "Kunde", "von", "Projekt", $_xmldata['projekt'] );

          if($_xmldata['lieferantennummer']!="" && !is_array($_xmldata['lieferantennummer']))
            $this->app->erp->AddRolleZuAdresse($id, "Lieferant", "von", "Projekt", $_xmldata['projekt'] );

          if($_xmldata['verband']!="" && !is_array($_xmldata['verband']))
          {
            if($this->app->erp->GetVerband($id)!=$_xmldata['verband'])
            {
              // alle verbaende loeschen
              $this->app->DB->Update("UPDATE adresse_rolle ad LEFT JOIN gruppen g 
                  ON g.id=ad.parameter SET ad.bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE ad.objekt='Gruppe' AND g.art='verband'
                  AND ad.adresse='$id'");
            }
            $this->app->erp->AddRolleZuAdresse($id, "Kunde", "von", "Gruppe", $_xmldata['verband']);
          }
          else
          {
            // alle verbaende loeschen
            $this->app->DB->Update("UPDATE adresse_rolle ad LEFT JOIN gruppen g 
                ON g.id=ad.parameter SET ad.bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE ad.objekt='Gruppe' AND g.art='verband'
                AND ad.adresse='$id'");
          }
          foreach($_xmldata as $key=>$value) {
            if(is_array($value)) {
              $value='';
            }
            if($key==='sonstiges') {
              $value = strip_tags(html_entity_decode($value));
            }
            if($key==='rabattinformation') {
              $value = html_entity_decode($value);
            }
            if($key === 'geburtsdatum' && strpos($value,'.') !== false) {
              $value = $this->app->String->Convert($value,'%1.%2.%3','%3-%2-%1');
            }
            if($key!=='id'){
              $this->app->DB->Update("UPDATE adresse SET $key='$value' WHERE id='$id' LIMIT 1");
            }
          }
        }
      }
      if(!empty($xmldata['dateien']) && is_array($xmldata['dateien']))
      {
        $this->AddFiles($xmldata['dateien'], 'adresse', $id);
      }
      if($intern){
        return $id;
      }
      $this->XMLResponse(1);
      $this->app->ExitXentral();
    }
    $id = $this->app->Secure->GetGET("id");
    $kundennummer = $this->app->Secure->GetGET("kundennummer");

    if(strtoupper($xmldata['kundennummer'])==="NEW" || strtoupper($xmldata['kundennummer'])==="NEU")
    {

      $xmldata['kundennummer'] = $this->app->erp->GetNextKundennummer($xmldata['projekt'],$id);
      $this->app->erp->ObjektProtokoll("adresse",$id,"adresse_next_kundennummer","Kundennummer ".$xmldata['kundennummer']." erhalten");
    }

    if(strtoupper($xmldata['lieferantennummer'])==="NEW" || strtoupper($xmldata['lieferantennummer'])==="NEU")
    {
      $xmldata['lieferantennummer'] = $this->app->erp->GetNextLieferantennummer($xmldata['projekt'],$id);
      $this->app->erp->ObjektProtokoll("adresse",$id,"adresse_next_lieferantennummer","Lieferantennummer ".$xmldata['lieferantennummer']." erhalten");
    }

    $projekt = $this->app->Secure->GetGET("projekt");

    if($kundennummer!="")
    {
      if($projekt!="")
      {
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
        $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' AND projekt='$projekt' LIMIT 1");
      } else {
        $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' LIMIT 1");
      }
    }

    // Key gibt es nicht
    if($id <= 0)
    {

      if($intern)return false;
      $this->XMLResponse(5);
    }



    if($xmldata['porto_preis']!="" && $xmldata['porto_artikelid']!="" && !is_array($xmldata['porto_preis']))
      $this->app->erp->AddVerkaufspreis($xmldata['porto_artikelid'],1,$id,$xmldata['porto_preis']);

    if($xmldata['projekt']!="" && !is_array($xmldata['projekt']))
      $xmldata['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['projekt']."' LIMIT 1");

    if($xmldata['kundennummer']!="" && !is_array($xmldata['kundennummer']))
      $this->app->erp->AddRolleZuAdresse($id, "Kunde", "von", "Projekt", $xmldata['projekt'] );

    if($xmldata['lieferantennummer']!="" && !is_array($xmldata['lieferantennummer']))
      $this->app->erp->AddRolleZuAdresse($id, "Lieferant", "von", "Projekt", $xmldata['projekt'] );

    if($xmldata['verband']!="" && !is_array($xmldata['verband']))
    {
      if($this->app->erp->GetVerband($id)!=$xmldata['verband'])
      {
        // alle verbaende loeschen
        $this->app->DB->Update("UPDATE adresse_rolle ad LEFT JOIN gruppen g 
            ON g.id=ad.parameter SET ad.bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE ad.objekt='Gruppe' AND g.art='verband'
            AND ad.adresse='$id'");
      }
      $this->app->erp->AddRolleZuAdresse($id, "Kunde", "von", "Gruppe", $xmldata['verband']);
    }
    else
    {
      // alle verbaende loeschen
      $this->app->DB->Update("UPDATE adresse_rolle ad LEFT JOIN gruppen g 
          ON g.id=ad.parameter SET ad.bis=DATE_SUB(NOW(),INTERVAL 1 DAY) WHERE ad.objekt='Gruppe' AND g.art='verband'
          AND ad.adresse='$id'");
    }

    foreach($xmldata as $key=>$value)
    {
      if(is_array($value)){
        $value='';
      }
      if($key==='sonstiges') {
        $value = strip_tags(html_entity_decode($value));
      }
      if($key==='rabattinformation') {
        $value = html_entity_decode($value);
      }
      if($key!=='id'){
        $this->app->DB->Update("UPDATE adresse SET $key='$value' WHERE id='$id' LIMIT 1");
      }
    }
    if(!empty($xmldata['dateien']) && is_array($xmldata['dateien']))
    {
      $this->AddFiles($xmldata['dateien'], 'adresse', $id);
    }
    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }

  function ApiAdresseGet($intern = false,$id="")
  {
    if($id=="")
      $id = $this->app->Secure->GetGET("id");
    $kundennummer = $this->app->Secure->GetGET("kundennummer");
    $projekt = $this->app->Secure->GetGET("projekt");


    $xmldata = $this->XMLPost();

    if($id=="" && isset($xmldata['id']))$id = $xmldata['id'];
    if($kundennummer=="" && isset($xmldata['kundennummer']))$kundennummer = $xmldata['kundennummer'];
    if($projekt=="" && isset($xmldata['projekt']))$projekt = $xmldata['projekt'];

    if($kundennummer!="")
    {
      if($projekt!="")
      {
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
        $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' AND projekt='$projekt' LIMIT 1");
      } else {
        $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' LIMIT 1");
      }
    }

    //check
    $id = $this->app->DB->Select("SELECT id FROM adresse WHERE id='$id' LIMIT 1");

    if($id > 0)
    {
      if($intern) {
        return $this->app->erp->XMLAdresse($id);
      }
      $this->XMLResponse(1,$this->app->erp->XMLAdresse($id));
      $this->app->ExitXentral();
    }
    if($intern) {
      return false;
    }
    $this->XMLResponse(5);
    $this->app->ExitXentral();
  }

  function ApiAngebotCreate($intern = false)
  {
    return $this->ApiBelegCreate($intern, 'angebot');
  }

  public function ApiAuftragCreate($intern = false, $doctype = 'auftrag') {
    return $this->ApiBelegCreate($intern, $doctype);
  }

  public function ApiBelegCreate($intern = false, $doctype = 'auftrag')
  {
    if(!in_array($doctype, array('angebot', 'gutschrift', 'lieferschein', 'rechnung','bestellung','retoure'))){
      $doctype = 'auftrag';
    }
    $xmldata = $this->XMLPost();
    // wenn gleich eine neue Adresse mitangelegt werden soll
    if($doctype === 'bestellung'){
      if(strtoupper($xmldata['lieferantennummer']) === 'NEW' || strtoupper($xmldata['lieferantennummer']) === 'NEU'){
        $xmldata['lieferantennummer'] = $this->ApiAdresseCreate(true);
      }
      $adresse = 0;
      if(!empty($xmldata['lieferantennummer'])){
        $adresse = $this->app->DB->Select(
          sprintf(
            "SELECT id FROM adresse WHERE lieferantennummer='%s' AND lieferantennummer <> '' AND IFNULL(geloescht,0) = 0  LIMIT 1",
            $xmldata['lieferantennummer']
          )
        );
      }
      if(!$adresse && !empty($xmldata['kundennummerlieferant'])) {
        $adresse = $this->app->DB->Select(
          sprintf(
            "SELECT id FROM adresse 
          WHERE kundennummer='%s' AND lieferantennummer <> '' AND IFNULL(geloescht,0) = 0  LIMIT 1",
            $xmldata['kundennummerlieferant']
          )
        );
      }

    }else{
      if(strtoupper($xmldata['kundennummer']) === 'NEW' || strtoupper($xmldata['kundennummer']) === 'NEU' || $xmldata['kundennummer'] == '' || !isset($xmldata['kundennummer'])){
        $xmldata['kundennummer'] = $this->ApiAdresseCreate(true);
      }
      $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$xmldata['kundennummer']."' AND kundennummer <> '' LIMIT 1");
    }

    // anlegen der adresse


    if($adresse <= 0)
    {
      if($intern) {
        return false;
      }
      $this->XMLResponse(5,'address not found');
      $this->app->ExitXentral();
    }

    switch ($doctype) {
      case 'angebot':
        $id = $this->app->erp->CreateAngebot($adresse);
        $this->app->erp->LoadAngebotStandardwerte($id,$adresse);
        break;

      case 'gutschrift':
        $id = $this->app->erp->CreateGutschrift($adresse);
        $this->app->erp->LoadGutschriftStandardwerte($id,$adresse);
        break;

      case 'lieferschein':
        $id = $this->app->erp->CreateLieferschein($adresse);
        $this->app->erp->LoadLieferscheinStandardwerte($id,$adresse);
        break;

      case 'retoure':
        $id = $this->app->erp->CreateRetoure($adresse);
        $this->app->erp->LoadRetoureStandardwerte($id,$adresse);
        break;

      case 'rechnung':
        $id = $this->app->erp->CreateRechnung($adresse);
        $this->app->erp->LoadRechnungStandardwerte($id,$adresse);
        break;

      case 'bestellung':
        $this->app->erp->LogFile($adresse);
        $id = $this->app->erp->CreateBestellung($adresse);
        $this->app->erp->LoadBestellungStandardwerte($id,$adresse);
        break;
      default:
        $id = $this->app->erp->CreateAuftrag($adresse);
        $this->app->erp->LoadAuftragStandardwerte($id,$adresse);
        break;
    }

    $xmldata['belegnr'] = (string)$this->app->DB->Select("SELECT belegnr FROM $doctype WHERE id = '$id' LIMIT 1");
    if($xmldata['belegnr'] === '' || $xmldata['belegnr'] === '0'){$xmldata['belegnr'] = $this->app->erp->GetNextNummer($doctype,$xmldata['projekt'],$id);}
    if($doctype === 'bestellung')
    {
      $xmldata['lieferantkdrnummer'] = $xmldata['lieferantennummer'];
    }else{
      $xmldata['lieferantkdrnummer'] = $xmldata['kundennummer'];
    }
    if($doctype==='auftrag'){
      $this->app->DB->Update("UPDATE $doctype SET lieferantkdrnummer='" . $xmldata['lieferantkdrnummer'] . "' WHERE id='" . $id . "' LIMIT 1");
    }
    if($doctype === 'bestellung')
    {
      if(!empty($xmldata['lieferantennummer'])){
        $this->app->DB->Update("UPDATE $doctype SET lieferantennummer='" . $xmldata['lieferantennummer'] . "' WHERE id='" . $id . "' LIMIT 1");
      }
    }else{
      $this->app->DB->Update("UPDATE $doctype SET kundennummer='" . $xmldata['kundennummer'] . "' WHERE id='" . $id . "' LIMIT 1");
    }

    if($xmldata['shopextid']=='' || $xmldata['shopextid']==0){
      $xmldata['shopextid'] = 1;
    }
    if($xmldata['projekt']==''){
      $xmldata['projekt'] = $this->app->DB->Select(
        sprintf(
          'SELECT pr.abkuerzung 
          FROM `%s` AS b
          INNER JOIN adresse AS adr ON b.adresse = adr.id
          INNER JOIN projekt AS pr ON adr.projekt = pr.id AND pr.geloescht <> 1
          WHERE b.id = %d',
          $doctype, $id
        )
      );
      if($xmldata['projekt']=='' && $doctype === 'auftrag') {
        $xmldata['projekt'] = $this->app->DB->Select(
          sprintf(
            "SELECT pr.abkuerzung 
            FROM `auftrag` AS b
            INNER JOIN adresse AS adr ON b.adresse = adr.id AND b.lieferantenauftrag = 0
            INNER JOIN adresse_rolle AS ar on adr.id = ar.adresse AND ar.subjekt = 'Kunde' AND ar.objekt = 'Projekt'
                AND (IFNULL(ar.bis,'0000-00-00') = '0000-00-00' OR ar.bis >= CURDATE())
            INNER JOIN projekt AS pr ON ar.parameter = pr.id AND pr.geloescht <> 1
            WHERE b.id = %d
            ORDER BY ar.id
            LIMIT 1",
            $id
          )
        );
      }
      if($xmldata['projekt']=='') {
        $xmldata['projekt'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='" . $this->app->erp->Firmendaten("projekt") . "' LIMIT 1");
      }
    }

    $this->app->DB->Update("UPDATE $doctype SET belegnr='".$xmldata['belegnr']."' WHERE id='".$id."' LIMIT 1");
    if(!isset($xmldata['status'])){
      $xmldata['status'] = 'freigegeben';
    }
    $this->app->DB->Update("UPDATE $doctype SET status='freigegeben' WHERE id='".$id."' AND (status = 'angelegt' OR status = '') LIMIT 1");
    $this->ApiBelegEdit(true,$id, $doctype, $xmldata);
    if($intern){
      return $id;
    }
    $this->XMLResponse(1,"<id>$id</id><belegnr>".$xmldata['belegnr'].'</belegnr>');

    $this->app->ExitXentral();
  }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return array|bool
     */
  public function ApiGutschriftFreigabe($intern = false, $id = null)
  {
    if($id==''){
      $id = $this->getIdFromData();
    }
    $ret = $this->ApiBelegFreiabe('gutschrift', $id);
    if(!$intern) {
      if(!empty($ret)) {
        if(!empty($ret['error'])) {
          $this->XMLResponse($ret['error']);
        }
        $this->XMLResponse(1,"<id>$id</id><belegnr>".$ret['belegnr'].'</belegnr>');
      }
    }

    return $ret;
  }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return int
     */
  public function ApiRechnungVersenden($intern = false, $id = null)
  {
    return $this->ApiBelegVersenden($intern, $id, 'rechnung');
  }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return int
     */
    public function ApiAuftragVersenden($intern = false, $id = null)
    {
      return $this->ApiBelegVersenden($intern, $id, 'auftrag');
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return int
     */
    public function ApiAngebotVersenden($intern = false, $id = null)
    {
      return $this->ApiBelegVersenden($intern, $id, 'angebot');
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return int
     */
    public function ApiGutschriftVersenden($intern = false, $id = null)
    {
      return $this->ApiBelegVersenden($intern, $id, 'gutschrift');
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return int
     */
    public function ApiLieferscheinVersenden($intern = false, $id = null)
    {
      return $this->ApiBelegVersenden($intern, $id, 'lieferschein');
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return int
     */
    public function ApiReisekostenVersenden($intern = false, $id = null)
    {
      return $this->ApiBelegVersenden($intern, $id, 'reisekosten');
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return int
     */
    public function ApiAuftragArchivieren($intern = false, $id = null)
    {
      return $this->ApiBelegArchivieren($intern, $id, 'auftrag');
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return int
     */
    public function ApiRechnungArchivieren($intern = false, $id = null)
    {
      return $this->ApiBelegArchivieren($intern, $id, 'rechnung');
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return int
     */
    public function ApiAngebotArchivieren($intern = false, $id = null)
    {
      return $this->ApiBelegArchivieren($intern, $id, 'angebot');
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return int
     */
    public function ApiLieferscheinArchivieren($intern = false, $id = null)
    {
      return $this->ApiBelegArchivieren($intern, $id, 'lieferschein');
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return int
     */
    public function ApiGutschriftArchivieren($intern = false, $id = null)
    {
      return $this->ApiBelegArchivieren($intern, $id, 'gutschrift');
    }

    /**
     * @param bool   $intern
     * @param int    $id
     * @param string $doctype
     *
     * @return int
     */
    public function ApiBelegArchivieren($intern, $id, $doctype)
    {
      if($id==''){
        $id = $this->getIdFromData();
      }

      if($id <= 0){
        if(!$intern){
          $this->XMLResponse(5);
        }
        else {
          return 5;
        }
      }
      $projectId = $this->app->DB->Select(
        sprintf(
          'SELECT projekt FROM `%s` WHERE id = %d LIMIT 1',
          $doctype, $id
        )
      );
      $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
      $class = ucfirst($doctype).'PDFCustom';
      if(!class_exists($class)) {
        $class = ucfirst($doctype).'PDF';
      }
      if(!class_exists($class)) {
        if(!$intern){
          $this->XMLResponse(5);
        }
        else {
          return 5;
        }
      }

      $pdf = new $class($this->app, $projectId);
      $method = 'Get'.ucfirst($doctype);
      if(!method_exists($pdf, $method)) {
        if(!$intern){
          $this->XMLResponse(5);
        }
        else {
          return 5;
        }
      }
      $pdf->$method($id);
      $tmpfile = $pdf->displayTMP();
      $pdf->ArchiviereDocument();
      unlink($tmpfile);
      $this->app->erp->BriefpapierHintergrunddisable = !$this->app->erp->BriefpapierHintergrunddisable;
      $pdf = new $class($this->app, $projectId);
      $pdf->$method($id);
      $tmpfile = $pdf->displayTMP();
      $pdf->ArchiviereDocument(1);
      $this->app->DB->Update(sprintf('UPDATE `%s` SET schreibschutz=1 WHERE id=%d', $doctype, $id));
      unlink($tmpfile);
      if(!$intern){
        $this->XMLResponse(1, '<id>'.$id.'</id>');
      }

      return 1;
    }

    /**
     * @param string $doctype
     * @param int    $doctypeId
     * @param array  $xmldata
     *
     * @return int
     */
  public function ApiBelegVersenden($intern, $id, $doctype)
  {
    if($id==''){
      $id = $this->getIdFromData();
    }

    if($id <= 0){
      if(!$intern){
        $this->XMLResponse(5);
      }
      else {
        return 5;
      }
    }
    $xmldata = $this->XMLPost();
    $type = !empty($xmldata['versandart'])?$xmldata['versandart']:$xmldata['art'];
    $printer = !empty($xmldata['drucker'])?$xmldata['drucker']:$xmldata['email'];
    if(!in_array($doctype, ['rechnung','angebot','auftrag','gutschrift','lieferschein','reisekosten'])) {
      if(!$intern){
        $this->XMLResponse(5);
      }
      else {
        return 5;
      }
    }

    if($type!=='email' && $type!=='brief'){
      if(!$intern){
        $this->XMLResponse(5);
      }
      else {
        return 5;
      }
    }
    $this->getEmailSettings();
    $this->app->erp->BelegVersand($doctype, $id, $type, $printer);

    if(!$intern){
      $this->XMLResponse(1, '<id>'.$id.'</id>');
    }

    return 1;
  }

    /**
     * @param bool     $intern
     * @param null|int $id
     */
  public function ApiWeiterfuehrenRechnungZuGutschrift($intern = false, $id = null)
  {
    if($id==''){
      $id = $this->getIdFromData();
    }

    if($id <= 0){
      if(!$intern){
        $this->XMLResponse(5);
      }
      else {
        return;
      }
    }
    $returnOrderId = $this->app->erp->WeiterfuehrenRechnungZuGutschrift($id);
    if(empty($returnOrderId)) {
      if(!$intern){
        $this->XMLResponse(5);
      }
    }
    if(!$intern){
      $this->XMLResponse(1, '<id>' . $returnOrderId . '</id>');
    }
  }

    /**
     * @param bool     $intern
     * @param null|int $id
     */
  public function ApiWeiterfuehrenAuftragZuRechnung($intern = false, $id = null)
  {
    if($id==''){
      $id = $this->getIdFromData();
    }

    if($id <= 0){
      if(!$intern){
        $this->XMLResponse(5);
      }
      else {
        return;
      }
    }
    $invoiceId = $this->app->erp->WeiterfuehrenAuftragZuRechnung($id);
    if(empty($invoiceId)) {
      if(!$intern){
        $this->XMLResponse(5);
      }
    }
    if(!$intern){
      $this->XMLResponse(1, '<id>' . $invoiceId . '</id>');
    }
  }

    /**
     * @param bool     $intern
     * @param null|int $id
     *
     * @return bool
     */
  public function ApiRechnungVersendetMarkieren($intern = false, $id = null)
  {
    if($id==''){
      $id = $this->getIdFromData();
    }
    if(empty($id)) {
      if($intern) {
        return false;
      }
      $this->XMLResponse(5);
    }
    /** @var Rechnung $obj */
    $obj = $this->app->erp->LoadModul('rechnung');
    if(empty($obj)) {
      $ok = false;
    }
    else{
      $ok = $obj->markInvoiceAsClosed();
    }
    if(!$intern) {
      if($ok) {
        $this->XMLResponse(1, '<id>'.$id.'</id>');
      }
      else {
        $this->XMLResponse(5);
      }
    }

    return $ok;
  }

    /**
     * @param bool     $intern
     * @param null|int $id
     *
     * @return array
     */
  public function ApiAuftragAbschliessen($intern = false, $id = null)
  {
    if($id==''){
      $id = $this->getIdFromData();
    }
    /** @var Auftrag $obj */
    $obj = $this->app->erp->LoadModul('auftrag');
    $ret = $obj->closeOrder($id);
    if(!$intern){
      if(!empty($ret['error'])){
        $this->XMLResponse(5);
      }
      $this->XMLResponse(1, '<id>'.$id.'</id>');
    }

    return $ret;
  }

    /**
     * @param bool     $intern
     * @param null|int $id
     *
     * @return array
     */
  public function ApiRechnungAlsBezahltMarkieren($intern = false, $id = null)
  {
    if($id==''){
      $id = $this->getIdFromData();
    }
    /** @var Rechnung $obj */
    $obj = $this->app->erp->LoadModul('rechnung');
    $ret = $obj->setManualPayed($id);
    if(!$intern){
      if(!empty($ret['error'])){
        $this->XMLResponse(5);
      }
      $this->XMLResponse(1, '<id>'.$id.'</id>');
    }

    return $ret;
  }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return array|bool
     */
    public function ApiAngebotFreigabe($intern = false, $id = null)
    {
      if($id==''){
        $id = $this->getIdFromData();
      }
      $ret = $this->ApiBelegFreiabe('angebot', $id);
      if(!$intern) {
        if(!empty($ret)) {
          if(!empty($ret['error'])) {
            $this->XMLResponse($ret['error']);
          }
          $this->XMLResponse(1,"<id>$id</id><belegnr>".$ret['belegnr'].'</belegnr>');
        }
      }

      return $ret;
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return array|bool
     */
    public function ApiBestellungFreigabe($intern = false, $id = null)
    {
      if($id==''){
        $id = $this->getIdFromData();
      }
      $ret = $this->ApiBelegFreiabe('bestellung', $id);
      if(!$intern) {
        if(!empty($ret)) {
          if(!empty($ret['error'])) {
            $this->XMLResponse($ret['error']);
          }
          $this->XMLResponse(1,"<id>$id</id><belegnr>".$ret['belegnr'].'</belegnr>');
        }
      }

      return $ret;
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return array|bool
     */
    public function ApiRechnungFreigabe($intern = false, $id = null)
    {
      if($id==''){
        $id = $this->getIdFromData();
      }
      $ret = $this->ApiBelegFreiabe('rechnung', $id);
      if(!$intern) {
        if(!empty($ret)) {
          if(!empty($ret['error'])) {
            $this->XMLResponse($ret['error']);
          }
          $this->XMLResponse(1,"<id>$id</id><belegnr>".$ret['belegnr'].'</belegnr>');
        }
      }

      return $ret;
    }

    /**
     * @return array|mixed|string
     */
    protected function getIdFromData()
    {
      $id = $this->app->Secure->GetGET('id');
      if(empty($id)) {
        $xmldata = $this->XMLPost();
        if(is_array($xmldata) && !empty($xmldata['id'])) {
          $id = $xmldata['id'];
        }
      }

      return $id;
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return array|bool
     */
    public function ApiAuftragFreigabe($intern = false, $id = null)
    {
      if($id==''){
        $id = $this->getIdFromData();
      }

      $ret = $this->ApiBelegFreiabe('auftrag', $id);
      if(!$intern) {
        if(!empty($ret)) {
          if(!empty($ret['error'])) {
            $this->XMLResponse($ret['error']);
          }
          $this->XMLResponse(1,"<id>$id</id><belegnr>".$ret['belegnr'].'</belegnr>');
        }
      }

      return $ret;
    }

    /**
     * @param bool $intern
     * @param null $id
     *
     * @return array|bool
     */
    public function ApiLieferscheinFreigabe($intern = false, $id = null)
    {
      if($id==''){
        $id = $this->getIdFromData();
      }
      $ret = $this->ApiBelegFreiabe('lieferschein', $id);
      if(!$intern) {
        if(!empty($ret)) {
          if(!empty($ret['error'])) {
            $this->XMLResponse($ret['error']);
          }
          $this->XMLResponse(1,"<id>$id</id><belegnr>".$ret['belegnr'].'</belegnr>');
        }
      }

      return $ret;
    }

    /**
     * @param string $typ
     * @param int    $id
     *
     * @return bool|array
     */
  public function ApiBelegFreiabe($typ, $id)
  {
    if($id <= 0) {
      return ['error' => 5];
    }
    if(!in_array($typ, ['auftrag','angebot','rechnung','gutschrift','lieferschein','bestellung'])) {
      return ['error' => 5];
    }

    $row = $this->app->DB->SelectRow(
      sprintf(
        "SELECT id, belegnr, status
        FROM `%s` 
        WHERE id = %d
        LIMIT 1",
        $typ, $id
      )
    );
    if(empty($row)) {
      return ['error' => 8];
    }

    if(in_array($row['status'], ['storniert','abgeschlossen','versendet'])) {
      return ['error' => 8];
    }
    $this->app->erp->BelegFreigabe($typ, $id);

    return ['id' => $id, 'belegnr' => $this->app->DB->Select(sprintf('SELECT belegnr FROM `%s` WHERE id = %d', $typ, $id))];
  }

  function ApiEtikettendrucker($intern=false)
  {
    $xmldata = $this->XMLPost();

    //  id pruefen
    if($xmldata['etikett'] <= 0)
    {
      if($intern) {
        return false;
      }
      $this->XMLResponse(5);
      $this->app->ExitXentral();
    }

    //$xmldata['kennung']
    $xmldata['etikett']=(isset($xmldata['etikett'])?$xmldata['etikett']:"");
    $xmldata['anzahl']=(isset($xmldata['anzahl']) && $xmldata['anzahl'] > 0?$xmldata['anzahl']:1);
    $xmldata['tabelle']=(isset($xmldata['tabelle'])?$xmldata['tabelle']:"");
    $xmldata['id']=(isset($xmldata['id'])?$xmldata['id']:"");
    $xmldata['drucker']=(isset($xmldata['drucker'])?$xmldata['drucker']:"");

    $tmp = array();
    if(isset($xmldata['parameter']) && is_array($xmldata['parameter']))
    {
      foreach($xmldata['parameter'] as $keyname=>$value)
        $tmp[$keyname] = $value;
    }

    $this->app->erp->EtikettenDrucker($xmldata['etikett'],$xmldata['anzahl'],$xmldata['tabelle'],$xmldata['id'],$tmp,"",$xmldata['drucker']);

    if($intern) {
      return true;
    }
    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }

  function ApiAuftragZuRechnung($intern=false,$id="")
  {
    if($id=="")
      $id = $this->app->Secure->GetGET("id");
    $xmldata = $this->XMLPost();
    if($id==""){
      $id= $xmldata['id'];
    }
    //  id pruefen
    $id = $this->app->DB->Select("SELECT id FROM auftrag WHERE id='".(int)$id."' LIMIT 1");
    if($id <= 0)
    {
      if($intern) {
        return false;
      }
      $this->XMLResponse(5);
      $this->app->ExitXentral();
    }
    if(!class_exists('FormHandler'))
    {
      include_once(__DIR__ ."/../../phpwf/plugins/class.formhandler.php");
      $this->app->FormHandler = new FormHandler($this->app);
    }else{
      if(empty($this->app->FormHandler))$this->app->FormHandler = new FormHandler($this->app);
    }
    $rechnung = $this->app->erp->WeiterfuehrenAuftragZuRechnung($id);
    if($intern) {
      return $rechnung;
    }
    $this->XMLResponse(1,$this->ApiRechnungGet(true, $rechnung));
    $this->app->ExitXentral();
  }

  function ApiAngebotZuAuftrag($intern=false,$id="")
  {
    if($id=="")
      $id = $this->app->Secure->GetGET("id");
    $xmldata = $this->XMLPost();
    if($id==""){
      $id= $xmldata['id'];
    }
    //  id pruefen
    $id = $this->app->DB->Select("SELECT id FROM angebot WHERE id='".(int)$id."' LIMIT 1");
    if($id <= 0)
    {
      if($intern) {
        return false;
      }
      $this->XMLResponse(5);
      $this->app->ExitXentral();
    }
    if(!class_exists('FormHandler'))
    {
      include_once(__DIR__ ."/../../phpwf/plugins/class.formhandler.php");
      $this->app->FormHandler = new FormHandler($this->app);
    }else{
      if(empty($this->app->FormHandler))$this->app->FormHandler = new FormHandler($this->app);
    }
    $auftrag = $this->app->erp->WeiterfuehrenAngebotZuAuftrag($id);
    if($intern) {
      return $auftrag;
    }
    $this->XMLResponse(1,$this->ApiAuftragGet(true, $auftrag));
    $this->app->ExitXentral();
  }

  public function ApiAngebotEdit($intern=false,$id='')
  {
    return $this->ApiBelegEdit($intern, $id, 'angebot');
  }

  public function ApiAuftragEdit($intern=false,$id='', $doctype = 'auftrag', $xmldata = null)
  {
    return $this->ApiBelegEdit($intern,$id, $doctype, $xmldata);
  }

  public function ApiBelegEdit($intern=false,$id='', $doctype = 'auftrag', $xmldata = null)
  {
    if($id==''){
      $id = $this->app->Secure->GetGET('id');
    }


    if(empty($xmldata)){
      $xmldata = $this->XMLPost();
    }

    if(!$intern && $doctype === 'auftrag' && isset($xmldata['belegnr']) && (strtoupper($xmldata['belegnr']) === 'NEW' || strtoupper($xmldata['belegnr']) === 'NEU'))
    {
      $this->ApiAuftragCreate();
    }

    if($id==''){
      $id = $xmldata['id'];
    }

    if(empty($id) && !empty($xmldata['belegnr'])){
      $id = $this->app->DB->Select("SELECT id FROM $doctype WHERE belegnr='".$this->app->DB->real_escape_string($xmldata['belegnr'])."' LIMIT 1");
    }

    //  id pruefen
    $id = $this->app->DB->Select("SELECT id FROM $doctype WHERE id='".(int)$id."' LIMIT 1");

    if($id <= 0)
    {
      if($intern){
        return false;
      }
      $this->XMLResponse(5);
      $this->app->ExitXentral();
    }
    // anlegen der adresse
    $adresse = $this->app->DB->Select("SELECT adresse FROM $doctype WHERE id='".$id."' LIMIT 1");
    if($adresse <= 0)
    {
      if($intern){
        return false;
      }
      $this->XMLResponse(5);
      $this->app->ExitXentral();
    }

    if($xmldata['status']!=='angelegt' && $xmldata['status']!=='freigegeben' &&
      $xmldata['status']!=='abgeschlossen' && $xmldata['status']!=='storniert'){
      $xmldata['status'] = 'angelegt';
    }

    //if($xmldata['status']=="freigegeben")
    //  $xmldata['status']="angelegt";

     //kundennummer=1 bedeutet gleiche Rolle Kunde anlegen
    if($xmldata['projekt']!='' && !is_array($xmldata['projekt'])){
      $xmldata['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='" . $xmldata['projekt'] . "' LIMIT 1");
    }

    if($xmldata['projekt'] <=0){
      $xmldata['projekt'] = $this->app->erp->ImportGetStandardProjekt();
    }

    // schaue ob kundennummer angebene ist und mache Load
    if($doctype !== 'bestellung'){
      if($xmldata['kundennummer'] != '' && strtoupper($xmldata['kundennummer']) !== 'NEW' &&
        strtoupper($xmldata['kundennummer']) !== 'NEU' && !is_array($xmldata['kundennummer'])){
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='" . $xmldata['kundennummer'] . "' LIMIT 1");
        $_funktion = 'Load' . ucfirst($doctype) . 'Standardwerte';
        if(method_exists($this->app->erp, $_funktion)){
          $this->app->erp->$_funktion($id, $adresse);
        }
        //$this->app->erp->LoadAuftragStandardwerte($id,$adresse);
      }else{
        if($xmldata['land'] == ''){
          $xmldata['land'] = 'DE';
        }
      }
    } else{
      if($xmldata['lieferantennummer'] != '' && strtoupper($xmldata['lieferantennummer']) !== 'NEW' &&
        strtoupper($xmldata['lieferantennummer']) !== 'NEU' && !is_array($xmldata['kundennummer'])){
        if(!$adresse){
          $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='" . $xmldata['lieferantennummer'] . "' LIMIT 1");
        }
        $_funktion = 'Load' . ucfirst($doctype) . 'Standardwerte';
        if(method_exists($this->app->erp, $_funktion)){
          $this->app->erp->$_funktion($id, $adresse);
        }
        //$this->app->erp->LoadAuftragStandardwerte($id,$adresse);
      }else{
        if($xmldata['land'] == ''){
          $xmldata['land'] = 'DE';
        }
      }
    }


    $this->app->DB->Delete("DELETE FROM $doctype"."_position WHERE $doctype='$id'");

    if($xmldata['status']==''){
      $xmldata['status'] = 'freigegeben';
    }

    // updat alle felde die angeben wurden sind

    $doctypeArr = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM `%s` WHERE id = %d LIMIT 1',$doctype,$id
      )
    );
    $doctypeKeys = array_keys($doctypeArr);
    foreach($xmldata as $key=>$value)
    {
      if(is_array($value)){
        $value='';
      }
      if($key!=='id'){
        if(!in_array($key,$doctypeKeys)) {
          continue;
        }
        $this->app->DB->Update("UPDATE $doctype SET $key='$value' WHERE id='$id' LIMIT 1");
      }
    }

    if(isset($xmldata['artikelliste']['position']['menge']))
    {
      $tmp = $xmldata['artikelliste']['position'];
      unset($xmldata['artikelliste']['position']);
      $xmldata['artikelliste']['position'][0] = $tmp;
    }elseif(!isset($xmldata['artikelliste']['position'][0]))
    {
      $tmp = $xmldata['artikelliste']['position'];
      unset($xmldata['artikelliste']['position']);
      $xmldata['artikelliste']['position'][0] = $tmp;
    }
    // alle positionen der reihe nach

    $cposition = isset($xmldata['artikelliste']['position'])?count($xmldata['artikelliste']['position']):0;
    for($i=0;$i<$cposition;$i++)
    {
      $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['artikelliste']['position'][$i]['projekt']."' LIMIT 1");
      if(!empty($xmldata['artikelliste']['position'][$i]['nummer'])){
        if($projektid > 0){
          $positionid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='" . $xmldata['artikelliste']['position'][$i]['nummer'] . "' AND projekt='" . $projektid . "' LIMIT 1");
        }else{
          $positionid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='" . $xmldata['artikelliste']['position'][$i]['nummer'] . "' LIMIT 1");
        }
      }elseif($doctype === 'bestellung' && !empty($xmldata['artikelliste']['position'][$i]['bestellnummer'])) {
        $positionid = $this->app->DB->Select(sprintf(
          'SELECT artikel FROM einkaufspreise WHERE adresse = %d AND bestellnummer = \'%s\'',
          $adresse,$this->app->DB->real_escape_string($xmldata['artikelliste']['position'][$i]['bestellnummer'])
          )
        );
      }

      $xmldata['artikelliste']['position'][$i]['menge'] = str_replace(',','.',$xmldata['artikelliste']['position'][$i]['menge']);

      if($xmldata['artikelliste']['position'][$i]['waehrung']=='' || is_array($xmldata['artikelliste']['position'][$i]['waehrung'])){
        $xmldata['artikelliste']['position'][$i]['waehrung'] = 'EUR';
      }

      if($doctype === 'bestellung'){
        $bezeichnungcol = 'bezeichnunglieferant';
      }else{
        $bezeichnungcol = 'bezeichnung';
      }

      if($positionid <= 0)
      {
        //TODO den Artikel gibt es in der datenbank nicht!
        if($doctype === 'bestellung' && empty($xmldata['artikelliste']['position'][$i]['bezeichnung']) && !empty($xmldata['artikelliste']['position'][$i]['bezeichnunglieferant']))
        {
          $felder['name_de'] = $xmldata['artikelliste']['position'][$i]['bezeichnunglieferant'];
        }else{
          $felder['name_de'] = $xmldata['artikelliste']['position'][$i]['bezeichnung'];
        }
        $felder['anabregs_text'] = $xmldata['artikelliste']['position'][$i]['beschreibung'];
        if($doctype === 'bestellung' && empty($xmldata['artikelliste']['position'][$i]['nummer']) && !empty($xmldata['artikelliste']['position'][$i]['bestellnummer'])){
          $felder['nummer'] = $xmldata['artikelliste']['position'][$i]['bestellnummer'];
        }else{
          $felder['nummer'] = $xmldata['artikelliste']['position'][$i]['nummer'];
        }
        $felder['lagerartikel'] = 1;
        if($projektid > 0){
          $felder['projekt'] = $projektid;
        }
        else{
          $felder['projekt'] = $this->app->erp->GetStandardProjekt();
        }

        $positionid = $this->app->erp->AddArtikel($felder);
        $tmpartikelid = $positionid;
      } else {
        $xmldata['artikelliste']['position'][$i]['artikel'] = $positionid;
        $tmpartikelid = $positionid;
        // wenn key == nummer alles laden und kopieren wenn felder leer
        if($doctype === 'bestellung' && !empty($xmldata['artikelliste']['position'][$i]['nummer']) && empty($xmldata['artikelliste']['position'][$i]['bestellnummer']))
        {
          $xmldata['artikelliste']['position'][$i]['bestellnummer'] = $xmldata['artikelliste']['position'][$i]['nummer'];
        }
        if($doctype === 'bestellung' && !empty($xmldata['artikelliste']['position'][$i]['bezeichnung']) && empty($xmldata['artikelliste']['position'][$i]['bezeichnunglieferant']))
        {
          $xmldata['artikelliste']['position'][$i]['bezeichnunglieferant'] = $xmldata['artikelliste']['position'][$i]['bezeichnung'];
        }
        if($xmldata['artikelliste']['position'][$i][$bezeichnungcol]==''){
          $xmldata['artikelliste']['position'][$i][$bezeichnungcol] = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='" . $positionid . "' LIMIT 1");
        }

        if($xmldata['artikelliste']['position'][$i][$bezeichnungcol]==''){
          $xmldata['artikelliste']['position'][$i][$bezeichnungcol] = $this->app->DB->Select("SELECT anabregs_text FROM artikel WHERE id='" . $positionid . "' LIMIT 1");
        }

        if($xmldata['artikelliste']['position'][$i]['preis']==''){
          if($doctype === 'bestellung'){
            $originalwaehrung = null;
            $originalpreis = null;
            $xmldata['artikelliste']['position'][$i]['preis'] = $this->app->erp->GetEinkaufspreisWaehrung($positionid, $xmldata['artikelliste']['position'][$i]['menge'], $xmldata['artikelliste']['position'][$i]['waehrung'], $originalwaehrung, $originalpreis, $adresse);
          }else{
            $xmldata['artikelliste']['position'][$i]['preis'] = $this->app->erp->GetVerkaufspreis($positionid, $xmldata['artikelliste']['position'][$i]['menge'],
              $adresse, $xmldata['artikelliste']['position'][$i]['waehrung']);
          }
        }
      }

      $this->app->DB->Insert("INSERT INTO $doctype"."_position (id,$doctype,sort,artikel) VALUES ('','".$id."','".($i+1)."','".$positionid."')");
      $positionid= $this->app->DB->GetInsertID();
      $this->app->erp->RunHook('beleg_afterinsertposition', 5, $doctype,$id,$tmpartikelid,$xmldata['artikelliste']['position'][$i]['menge'],$positionid);

      // anpassen der felder
      $posArr = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `%s` WHERE id = %d LIMIT 1',
          $doctype.'_position', $positionid
        )
      );
      $posKeys = array_keys($posArr);
      foreach($xmldata['artikelliste']['position'][$i] as $key=>$value)
      {
        if(is_array($value)){
          $value='';
        }
        if($key!=='id'){
          if(!in_array($key, $posKeys)) {
            continue;
          }
          $this->app->DB->Update("UPDATE $doctype" . "_position SET $key='$value' WHERE id='$positionid' LIMIT 1");
        }
      }
      if($doctype === 'bestellung' && !empty($tmpartikelid) && !empty($positionid)) {
        $this->app->DB->Update(
          sprintf(
            'UPDATE bestellung_position AS bp 
            INNER JOIN artikel AS art ON bp.artikel = art.id
            SET bp.bestellnummer = art.nummer 
            WHERE bp.bestellnummer = \'\' AND bp.id = %d 
            ',
            (int)$positionid
          )
        );

        $this->app->DB->Update(
          sprintf(
            'UPDATE bestellung_position AS bp 
            INNER JOIN artikel AS art ON bp.artikel = art.id
            SET bp.bezeichnunglieferant = art.name_de 
            WHERE bp.bezeichnunglieferant = \'\' AND bp.id = %d 
           ',
            (int)$positionid
          )
        );
      }

      $artikelnummerkunde = !empty($xmldata['artikelliste']['position'][$i]['kundenartikelnummer'])?$xmldata['artikelliste']['position'][$i]['kundenartikelnummer']:$this->app->DB->real_escape_string(
        $this->app->DB->Select(
          "SELECT kundenartikelnummer 
          FROM verkaufspreise WHERE adresse='$adresse' AND artikel='$tmpartikelid' AND kundenartikelnummer!='' AND ab_menge <=".
          (float)$xmldata['artikelliste']['position'][$i]['menge']." 
          AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') 
          ORDER by ab_menge DESC 
          LIMIT 1"
        ));

      if($artikelnummerkunde == ''){
        // Anzeige Artikel Nummer von Gruppe aus Verkaufspreis
        $gruppevkresult = $this->app->erp->GetVerkaufspreis($tmpartikelid, $xmldata['artikelliste']['position'][$i]['menge'], $adresse, $xmldata['artikelliste']['position'][$i]['waehrung'], $returnwaehrung, true);
        if($gruppevkresult['kundenartikelnummer'] != ''){
          $artikelnummerkunde = $gruppevkresult['kundenartikelnummer'];
        }
      }

      if(!empty($artikelnummerkunde)) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE `%s` SET artikelnummerkunde = '%s' WHERE id = %d ",
            $doctype.'_position',
            $this->app->DB->real_escape_string($artikelnummerkunde),
            $positionid
          )
        );
      }
    }
    $_funktion = ucfirst($doctype).'Neuberechnen';
    if(method_exists($this->app->erp, $_funktion)){
      $this->app->erp->$_funktion($id);
    }

    $xmldata['belegnr'] = $this->app->DB->Select("SELECT belegnr FROM $doctype WHERE id='".$id."' LIMIT 1");
    if(!empty($xmldata['dateien']) && is_array($xmldata['dateien']))
    {
      $this->AddFiles($xmldata['dateien'], $doctype, $id);
    }
    if($intern) {
      return $id;
    }
    $this->XMLResponse(1,"<id>$id</id><belegnr>".$xmldata['belegnr'].'</belegnr>');
    $this->app->ExitXentral();
  }

    /**
     * Create Files from XML
     * @param array  $files Array of files
     * @param string $doctype
     * @param int    $doctypeid
     */
  protected function AddFiles($files, $doctype, $doctypeid)
  {
    if(empty($doctype) || empty($doctypeid) || empty($files))
    {
      return;
    }
    foreach($files as $file)
    {
      if(empty($file['dateiname']))
      {
        continue;
      }

      $isfile = false;
      $data = null;
      if(is_file($file['dateiname'])){
        $type = mime_content_type(basename($file['dateiname']));
        if($type != 'text/x-php' && $type != 'text/php' && $type != 'application/php' && $type != 'application/x-php' && $type != 'application/x-httpd-php' && $type != 'application/x-httpd-php-source'){
          if(empty($file['dateiinhalt'])){
            $data = file_get_contents($file['dateiname']);
            //$singledatei->dateiinhalt = base64_encode($dateiinhalt);
            $isfile = true;
          }
        }else{
          $file['dateiinhalt'] = '';
        }
      }
      if(empty($data))
      {
        if(empty($file['dateiinhalt']))
        {
          $data = '';
        }else{
          $data = base64_decode($file['dateiinhalt']);
        }
      }
      if(empty($data))
      {
        continue;
      }
      if($isfile){
        $name = $this->app->erp->GetTmp().basename($file['dateiname']);
      }else{
        $name = $this->app->erp->GetTmp().$file['dateiname'];
      }

      file_put_contents($name, $data);

      $pfad = $this->app->Conf->WFuserdata;
      $pfad = rtrim($pfad);
      $pfad .= '/dms/';

      if(!file_exists($pfad) && !mkdir($pfad, 0777, true) && !is_dir($pfad))
      {
        $this->app->erp->LogFile($pfad.' konnte nicht erstellt werden');
      }

      $speicherpfad = $pfad.$this->app->Conf->WFdbname;

      if(!file_exists($speicherpfad) && !mkdir($speicherpfad, 0777, true) &&
        !is_dir($speicherpfad))
      {
        $this->app->erp->LogFile($speicherpfad.' konnte nicht erstellt werden');
      }

      $fileid = $this->app->erp->CreateDatei($file['dateiname'], !empty($file['titel'])?$file['titel']:$file['dateiname'], !empty($file['beschreibung'])?(string)$file['beschreibung']:'', '', $name, '',true,$speicherpfad);
      $subjekt = !empty($file['subjekt'])?(string)$file['subjekt']:'Sonstige';
      if($doctype == 'adresse'){
        $doctype = 'Adressen';
      }
      $this->app->erp->AddDateiStichwort($fileid, $subjekt, $doctype, $doctypeid);
    }
  }

  function ApiAngebotGet($intern = false, $id = null)
  {
    return $this->ApiBelegGet($intern, $id, 'angebot');
  }

  function ApiAuftragGet($intern = false, $id = null, $doctype = 'auftrag')
  {
    return $this->ApiBelegGet($intern, $id, 'auftrag');
  }




  function ApiBelegGet($intern = false, $id = null, $doctype = 'auftrag')
  {
    if(!$id)$id = $this->app->Secure->GetGET("id");
    $belegnr = $this->app->Secure->GetGET("belegnr");
    $projekt= $this->app->Secure->GetGET("projekt");

    $xmldata = $this->XMLPost();
    if(is_array($xmldata) && !empty($xmldata)){
      $id = $xmldata['id'];
      $belegnr = $xmldata['belegnr'];
      $projekt= $xmldata['projekt'];
    }
    if($belegnr!="")
    {
      if($projekt!="")
      {
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
        $id = $this->app->DB->Select("SELECT id FROM $doctype WHERE belegnr='$belegnr' AND projekt='$projekt' LIMIT 1");
      } else {
        $id = $this->app->DB->Select("SELECT id FROM $doctype WHERE belegnr='$belegnr' LIMIT 1");
      }
    }

    //check
    $id = $this->app->DB->Select("SELECT id FROM $doctype WHERE id='$id' LIMIT 1");

    if($id > 0)
    {
      if($intern) {
        return $this->app->erp->XMLAuftrag($id, $doctype);
      }
      $this->XMLResponse(1,$this->app->erp->XMLAuftrag($id, $doctype));
      $this->app->ExitXentral();
    }

    if($intern) {
      return false;
    }
    $this->XMLResponse(5, "<error>".ucfirst($doctype)." nicht gefunden</error>");

    $this->app->ExitXentral();
  }

  function ApiGutschriftGet($intern = false, $id = null)
  {
    return $this->ApiBelegGet($intern, $id, 'gutschrift');
  }

  function ApiGutschriftEdit($intern=false,$id="")
  {
    return $this->ApiBelegEdit($intern, $id, 'gutschrift');
  }

  function ApiGutschriftCreate($intern = false)
  {
    return $this->ApiBelegCreate($intern, 'gutschrift');
  }

  function ApiLieferscheinGet($intern = false, $id = null)
  {
    return $this->ApiBelegGet($intern, $id, 'lieferschein');
  }

  function ApiLieferscheinEdit($intern=false,$id="")
  {
    return $this->ApiBelegEdit($intern, $id, 'lieferschein');
  }

  function ApiLieferscheinCreate($intern = false)
  {
    return $this->ApiBelegCreate($intern, 'lieferschein');
  }

  function ApiRetoureGet($intern = false, $id = null)
  {
    return $this->ApiBelegGet($intern, $id, 'retoure');
  }

  function ApiRetoureEdit($intern=false,$id="")
  {
    return $this->ApiBelegEdit($intern, $id, 'retoure');
  }

  function ApiRetoureCreate($intern = false)
  {
    return $this->ApiBelegCreate($intern, 'retoure');
  }

  function ApiRechnungGet($intern = false, $id = null)
  {
    return $this->ApiBelegGet($intern, $id, 'rechnung');
  }

  function ApiRechnungEdit($intern=false,$id="")
  {
    return $this->ApiBelegEdit($intern, $id, 'rechnung');
  }

  function ApiRechnungCreate($intern = false)
  {
    return $this->ApiBelegCreate($intern, 'rechnung');
  }

  function ApiBestellungGet($intern = false, $id = null)
  {
    return $this->ApiBelegGet($intern, $id, 'bestellung');
  }

  function ApiBestellungEdit($intern=false,$id="")
  {
    return $this->ApiBelegEdit($intern, $id, 'bestellung');
  }

  function ApiBestellungCreate($intern = false)
  {
    return $this->ApiBelegCreate($intern, 'bestellung');
  }


  function ApiArtikelCreate($internal = false)
  {
    $xmldata = $this->XMLPost();
    // anlegen der adresse
    $felder = array();
    if($xmldata['name_de']!="" && !is_array($xmldata['name_de'])) {
        $id = $this->app->erp->AddArtikel($felder);
    } else {
      if ($internal) {
        return false;
      }
      $this->XMLResponse(5);
    }

    //kundennummer=1 bedeutet gleiche Rolle Kunde anlegen
    if($xmldata['projekt']!="" && !is_array($xmldata['projekt'])){
      $projektId = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['projekt']."' LIMIT 1");
      if(empty($projektId)){
        $projektId = $this->app->DB->Select("SELECT id FROM projekt WHERE id='".(int)$xmldata['projekt']."' LIMIT 1");
      }
      $xmldata['projekt'] = $projektId;
    }else{
      $xmldata['projekt'] = $this->app->erp->GetStandardProjekt();
    }

    if(strtoupper($xmldata['nummer'])=="NEW" || strtoupper($xmldata['nummer'])=="NEU" || $xmldata['nummer']=="")
      $xmldata['nummer'] = $this->app->erp->GetNextArtikelnummer($xmldata['typ'],1,$xmldata['projekt']);

    if($xmldata['lager_platz']!="" && !is_array($xmldata['lager_platz']))
    {
      $lagerid = $this->app->DB->Select("SELECT MIN(id) FROM lager WHERE geloescht!='1'");
      if($lagerid<=0)
      {
        $this->app->DB->Insert("INSERT INTO lager (id,bezeichnung,firma) VALUES ('','Hauptlager',1)");
        $lagerid = $this->app->DB->GetInsertID();
      }
      $xmldata['lager_platz'] = $this->app->erp->CreateLagerplatz($lagerid,$xmldata['lager_platz'],$firma="1");

      if($xmldata['lager_menge'] > 0)
      {
        $menge = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$id' AND lager_platz='".$xmldata['lager_platz']."'");
        if($menge != $xmldata['lager_menge'])
        {
          $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE artikel='$id' AND lager_platz='".$xmldata['lager_platz']."'");
          $this->app->erp->LagerEinlagern($id,$xmldata['lager_menge'],$xmldata['lager_platz'],$xmldata['projekt'],"XML Importtool Anpassung");
        }
      }
      $xmldata['lagerartikel']=1;
    }

    if($xmldata['aktiv']=="1") $xmldata['inaktiv']=0;
    if($xmldata['aktiv']=="0"|| (array_key_exists('aktiv', $xmldata) && $xmldata['aktiv']=="")) $xmldata['inaktiv']=1;

    if($xmldata['inaktiv']=="1") {
        $xmldata['intern_gesperrtgrund']="Artikel inaktiv";
        $xmldata['intern_gesperrt']="1";
    }

    if($xmldata['variante_von_nummer']!="" && !is_array($xmldata['variante_von_nummer'])) {
      // pruefen ob es einen echte id ist
      $xmldata['variante_von'] = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$xmldata['variante_von_nummer']."' AND nummer!='' LIMIT 1");
      if($xmldata['variante_von'] > 0)
        $xmldata['variante']=1;
    }

    if(!empty($xmldata['typ_ext']))$typ_ext = (int)$xmldata['typ_ext'];
    if(isset($typ_ext) && $typ_ext)$typ_ext = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE id = '$typ_ext' LIMIT 1");
    if(isset($typ_ext) && $typ_ext)
    {
      $xmldata['typ'] = $typ_ext.'_kat';
      unset($xmldata['typ_ext']);
    }elseif(isset($xmldata['typ']) && strpos($xmldata['typ'],'_kat') && !empty($xmldata['artikelkategorie']))
    {
      $kategorie = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE bezeichnung = '".$this->app->DB->real_escape_string($xmldata['artikelkategorie'])."' LIMIT 1");
      if($kategorie)
      {
        $xmldata['typ'] = $kategorie.'_kat';
        unset($xmldata['artikelkategorie']);
      }
    }

    $this->app->DB->Update("UPDATE artikel SET logdatei=now() WHERE id='$id' LIMIT 1");

    foreach($xmldata as $key=>$value)
    {
      if(is_array($value))$value="";
      if(is_array($value))$value="";
      if($key=="beschreibung_de") $value = html_entity_decode($value);
      if($key=="beschreibung_en") $value = html_entity_decode($value);
      if($key=="uebersicht_de") $value = html_entity_decode($value);
      if($key=="uebersicht_en") $value = html_entity_decode($value);
      if($key=="links_de") $value = html_entity_decode($value);
      if($key=="links_en") $value = html_entity_decode($value);
      if($key=="startseite_de") $value = html_entity_decode($value);
      if($key=="startseite_en") $value = html_entity_decode($value);
      if($key=="katalogtext_de") $value = html_entity_decode($value);
      if($key=="katalogtext_de") $value = html_entity_decode($value);
      if($key=="internerkommentar") $value = strip_tags(html_entity_decode($value));


      if($key!="id")
        $this->app->DB->Update("UPDATE artikel SET $key='$value' WHERE id='$id' LIMIT 1");
    }
    // alle positionen der reihe nach
    if($xmldata['stueckliste_artikel']['artikel']['menge']  > 0)
    {
      $tmp = $xmldata['stueckliste_artikel']['artikel'];
      $xmldata['stueckliste_artikel']['artikel']= null;
      $xmldata['stueckliste_artikel']['artikel'][0] = $tmp;
    }

    $cliste = isset($xmldata['stueckliste_artikel']['artikel'])?count($xmldata['stueckliste_artikel']['artikel']):0;
    for($i=0;$i<$cliste;$i++)
    {
      //$projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['artikelliste']['position'][$i]['projekt']."' LIMIT 1");
      $artikel = $id;
      $menge = $xmldata['stueckliste_artikel']['artikel'][$i]['menge'];
      $xmldata['stueckliste_artikel']['artikel'][$i]['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['stueckliste_artikel']['artikel'][$i]['projekt']."' LIMIT 1");

      if($xmldata['stueckliste_artikel']['artikel'][$i]['projekt']!="" && !is_array($xmldata['stueckliste_artikel']['artikel'][$i]['projekt']))
        $stuecklisteartikel = $this->app->DB->Select("SELECT id FROM adresse WHERE nummer='".$xmldata['stueckliste_artikel']['artikel'][$i]['nummer']."' AND projekt='".$xmldata['stueckliste_artikel']['artikel'][$i]['projekt']."' LIMIT 1");
      else
        $stuecklisteartikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$xmldata['stueckliste_artikel']['artikel'][$i]['nummer']."' LIMIT 1");

      $this->app->DB->Insert("INSERT INTO stueckliste (id,sort,artikel,stuecklistevonartikel,menge) VALUES ('','".($i+1)."','$stuecklisteartikel','$artikel','$menge')");
    }

    // eiinkaufspreise
    if($xmldata['einkaufspreise']['staffelpreis']['ab_menge']  > 0 || $xmldata['einkaufspreise']['staffelpreis']['preis']  > 0)
    {
      $tmp = $xmldata['einkaufspreise']['staffelpreis'];
      $xmldata['einkaufspreise']['staffelpreis']= null;
      $xmldata['einkaufspreise']['staffelpreis'][0] = $tmp;
    }

    $cliste = isset($xmldata['einkaufspreise']['staffelpreis'])?count($xmldata['einkaufspreise']['staffelpreis']):0;
    for($i=0;$i<$cliste;$i++)
    {
      //$projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['artikelliste']['position'][$i]['projekt']."' LIMIT 1");
      $artikel = $id;
      $abmenge = $xmldata['einkaufspreise']['staffelpreis'][$i]['ab_menge'];
      $xmldata['einkaufspreise']['staffelpreis'][$i]['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['einkaufspreise']['staffelpreis'][$i]['projekt']."' LIMIT 1");

      if($xmldata['einkaufspreise']['staffelpreis'][$i]['projekt']!="" && !is_array($xmldata['einkaufspreise']['staffelpreis'][$i]['projekt']))
      {
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']."' 
          AND lieferantennummer!='' AND projekt='".$xmldata['einkaufspreise']['staffelpreis'][$i]['projekt']."' LIMIT 1");

      }
      else
      {
        if($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']!="" && !is_array($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']))
          $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']."' 
            AND lieferantennummer!='' LIMIT 1");

      }

      if($adresse <=0)
      {
        if($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantname']!="" && !is_array($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantname']))
          $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE name='".$xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantname']."' AND name!='' LIMIT 1");

      }

      if($adresse <=0)
      {
        if($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantname']=="")
          $xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantname']="Lieferant";

        $adresse = $this->app->erp->CreateAdresse($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantname'],$firma="1");

        //wenn lieferanennummer vorhanden dann diese verwenden
        if($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']!="" && !is_array($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']))
          $this->app->DB->Update("UPDATE adresse SET lieferantennummer='".$xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']."' WHERE id='$adresse' LIMIT 1");

        $this->app->erp->AddRolleZuAdresse($adresse, "Lieferant", "von", "Projekt", $xmldata['projekt'] );
      }


      $bestellnummer = $xmldata['einkaufspreise']['staffelpreis'][$i]['bestellnummer'];
      $bezeichnunglieferant = $xmldata['einkaufspreise']['staffelpreis'][$i]['bezeichnunglieferant'];
      if(is_array($bestellnummer))$bestellnummer="";

      if($bezeichnunglieferant=="")
        $bezeichnunglieferant = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");
      if(is_array($bezeichnunglieferant) || $bezeichnunglieferant=="" )$bezeichnunglieferant=$xmldata['name_de'];

      $preis = str_replace(',','.',$xmldata['einkaufspreise']['staffelpreis'][$i]['preis']);
      $waehrung = $xmldata['einkaufspreise']['staffelpreis'][$i]['waehrung'];
      if(is_array($waehrung) || $waehrung=="")$waehrung="EUR";

      $this->app->erp->AddEinkaufspreis($artikel,$abmenge,$adresse,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung);
    }


    if($xmldata['verkaufspreise']['staffelpreis']['ab_menge']  > 0)
    {
      $tmp = $xmldata['verkaufspreise']['staffelpreis'];
      $xmldata['verkaufspreise']['staffelpreis']= null;
      $xmldata['verkaufspreise']['staffelpreis'][0] = $tmp;
    }
    // alle positionen der reihe nach
    $cstaffelpreise = isset($xmldata['verkaufspreise']['staffelpreis'])?count($xmldata['verkaufspreise']['staffelpreis']):0;
    for($i=0;$i<$cstaffelpreise;$i++)
    {
      $artikel = $id;
      $abmenge = $xmldata['verkaufspreise']['staffelpreis'][$i]['ab_menge'];
      $xmldata['verkaufspreise']['staffelpreis'][$i]['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['verkaufspreise']['staffelpreis'][$i]['projekt']."' LIMIT 1");


      if($xmldata['verkaufspreise']['staffelpreis'][$i]['projekt']!="" && !is_array($xmldata['verkaufspreise']['staffelpreis'][$i]['projekt']))
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$xmldata['verkaufspreise']['staffelpreis'][$i]['kundennummer']."' AND projekt='".$xmldata['verkaufspreise']['staffelpreis'][$i]['projekt']."' LIMIT 1");
      else
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$xmldata['verkaufspreise']['staffelpreis'][$i]['kundennummer']."' LIMIT 1");

      if($xmldata['verkaufspreise']['staffelpreis'][$i]['kundennummer']=="")
        $adresse = 0;
      else {
        // kunde gibt es nicht
        if($adresse <=0)
          continue;
      }

      if($xmldata['verkaufspreise']['staffelpreis'][$i]['gruppe']>0)
        $gruppe = $this->app->DB->Select("SELECT id FROM gruppen WHERE id='".$xmldata['verkaufspreise']['staffelpreis'][$i]['gruppe']."' LIMIT 1");
      else $gruppe = 0;

      $preis = str_replace(',','.',$xmldata['verkaufspreise']['staffelpreis'][$i]['preis']);
      $waehrung = $xmldata['verkaufspreise']['staffelpreis'][$i]['waehrung'];
      if(is_array($waehrung) || is_array($waehrung)) $waehrung = "EUR";


      //$projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['artikelliste']['position'][$i]['projekt']."' LIMIT 1");
      if($gruppe > 0)
        $this->app->erp->AddVerkaufspreisGruppe($artikel,$abmenge,$gruppe,$preis,$waehrung);
      else
        $this->app->erp->AddVerkaufspreis($artikel,$abmenge,$adresse,$preis,$waehrung);
    }

    $xmldata['nummer'] = $this->app->DB->Select("SELECT nummer FROM artikel WHERE id='$id' LIMIT 1");
    if($internal) {
      return $xmldata['nummer'];
    }

    $this->XMLResponse(1,"<id>$id</id><nummer>".$xmldata['nummer']."</nummer>");
    $this->app->ExitXentral();
  }


  function ApiArtikelEdit($intern = false)
  {
    $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetGET("nummer");
    $projekt = $this->app->Secure->GetGET("projekt");
    $xmldata = $this->XMLPost();
    if($nummer=="" && !empty($xmldata['nummer']))
      $nummer = $xmldata['nummer'];

    if(!empty($xmldata['id_ext']))$id_ext = (int)$xmldata['id_ext'];
    if(!empty($xmldata['typ_ext']))$typ_ext = (int)$xmldata['typ_ext'];
    if(isset($id_ext) && $id_ext)$id_ext = $this->app->DB->Select("SELECT id FROM artikel WHERE geloescht != 1 AND id = '$id_ext' LIMIT 1");
    if(isset($typ_ext) && $typ_ext)$typ_ext = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE id = '$typ_ext' LIMIT 1");
    if(isset($typ_ext) && $typ_ext)
    {
      $xmldata['typ'] = $typ_ext.'_kat';
      unset($xmldata['typ_ext']);
    }elseif(isset($xmldata['typ']) && strpos($xmldata['typ'],'_kat') && !empty($xmldata['artikelkategorie']))
    {
      $kategorie = $this->app->DB->Select("SELECT id FROM artikelkategorien WHERE bezeichnung = '".$this->app->DB->real_escape_string($xmldata['artikelkategorie'])."' LIMIT 1");
      if($kategorie)
      {
        $xmldata['typ'] = $kategorie.'_kat';
        unset($xmldata['artikelkategorie']);
      }
    }
    if(isset($id_ext) && $id_ext)
    {
      $id = $id_ext;
    }else{
      if($nummer!="")
      {
        if($projekt!="")
        {
          $projektId = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
          if(empty($projektId)){
            $projektId = $this->app->DB->Select("SELECT id FROM projekt WHERE id='".(int)$projekt."' LIMIT 1");
          }
          $projekt = $projektId;
          $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND projekt='$projekt' LIMIT 1");
        } else {
          $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
        }
      }
    }

    // Key gibt es nicht
    if($id <= 0) {
        if ($intern == true) {
            return false;
        }
        $this->XMLResponse(5);
    }

    //kundennummer=1 bedeutet gleiche Rolle Kunde anlegen
    if($xmldata['projekt']!="" && !is_array($xmldata['projekt']))
      $xmldata['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['projekt']."' LIMIT 1");


    // TODO wenn juststueckliste ist es nie ein lager artikel
    if($xmldata['juststueckliste']=="1")
    {
      $xmldata['lagerartikel']=0;
      $xmldata['lager_platz']="";
      $xmldata['lager_menge'] = 0;
    }


    if($xmldata['lager_platz']!="" && !is_array($xmldata['lager_platz']))
    {
      $lagerid = $this->app->DB->Select("SELECT MIN(id) FROM lager WHERE geloescht!='1'");
      if($lagerid<=0)
      {
        $this->app->DB->Insert("INSERT INTO lager (id,bezeichnung,firma) VALUES ('','Hauptlager',1)");
        $lagerid = $this->app->DB->GetInsertID();
      }

      $xmldata['lager_platz'] = $this->app->erp->CreateLagerplatz($lagerid,$xmldata['lager_platz'],$firma="1");
      if($xmldata['lager_menge'] > 0)
      {
        $menge = $this->app->DB->Select("SELECT SUM(menge) FROM lager_platz_inhalt WHERE artikel='$id' AND lager_platz='".$xmldata['lager_platz']."'");
        if($menge != $xmldata['lager_menge'])
        {
          $this->app->DB->Delete("DELETE FROM lager_platz_inhalt WHERE artikel='$id' AND lager_platz='".$xmldata['lager_platz']."'");
          $this->app->erp->LagerEinlagern($id,$xmldata['lager_menge'],$xmldata['lager_platz'],$xmldata['projekt'],"XML Importtool Anpassung");
        }
      }
      $xmldata['lagerartikel']=1;
    }

    if($xmldata['aktiv']=="1") $xmldata['inaktiv']=0;
    if($xmldata['aktiv']=="0"|| (array_key_exists('aktiv', $xmldata) && $xmldata['aktiv']=="")) $xmldata['inaktiv']=1;

    if($xmldata['inaktiv']=="1") {
        $xmldata['intern_gesperrtgrund']="Artikel inaktiv";
        $xmldata['intern_gesperrt']="1";
    }

    if($xmldata['variante_von_nummer']!="" && !is_array($xmldata['variante_von_nummer'])) {
      // pruefen ob es einen echte id ist
      $xmldata['variante_von'] = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$xmldata['variante_von_nummer']."' AND nummer!='' LIMIT 1");
      if($xmldata['variante_von'] > 0)
        $xmldata['variante']=1;
    }

    $this->app->DB->Update("UPDATE artikel SET logdatei=now() WHERE id='$id' LIMIT 1");

    foreach($xmldata as $key=>$value)
    {
      if(is_array($value))$value="";
      if($key=="beschreibung_de") $value = html_entity_decode($value);
      if($key=="beschreibung_en") $value = html_entity_decode($value);
      if($key=="uebersicht_de") $value = html_entity_decode($value);
      if($key=="uebersicht_en") $value = html_entity_decode($value);
      if($key=="links_de") $value = html_entity_decode($value);
      if($key=="links_en") $value = html_entity_decode($value);
      if($key=="startseite_de") $value = html_entity_decode($value);
      if($key=="startseite_en") $value = html_entity_decode($value);
      if($key=="katalogtext_de") $value = html_entity_decode($value);
      if($key=="katalogtext_de") $value = html_entity_decode($value);
      if($key=="internerkommentar") $value = strip_tags(html_entity_decode($value));

      if($key!="id")
        $this->app->DB->Update("UPDATE artikel SET $key='$value' WHERE id='$id' LIMIT 1");
    }

    if($xmldata['stueckliste_artikel']['artikel']['menge']  > 0)
    {
      $tmp = $xmldata['stueckliste_artikel']['artikel'];
      $xmldata['stueckliste_artikel']['artikel']= NULL;
      $xmldata['stueckliste_artikel']['artikel'][0] = $tmp;
    }

    if(count($xmldata['stueckliste_artikel']['artikel']) > 0)
      $this->app->DB->Delete("DELETE FROM stueckliste WHERE stuecklistevonartikel='".$id."'");

    $cartikel = isset($xmldata['stueckliste_artikel']['artikel'])?count($xmldata['stueckliste_artikel']['artikel']):0;
    for($i=0;$i<$cartikel;$i++)
    {
      //$projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['artikelliste']['position'][$i]['projekt']."' LIMIT 1");
      $artikel = $id;
      $menge = $xmldata['stueckliste_artikel']['artikel'][$i]['menge'];
      $xmldata['stueckliste_artikel']['artikel'][$i]['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['stueckliste_artikel']['artikel'][$i]['projekt']."' LIMIT 1");

      if($xmldata['stueckliste_artikel']['artikel'][$i]['projekt']!="" && !is_array($xmldata['stueckliste_artikel']['artikel'][$i]['projekt']))
        $stuecklisteartikel = $this->app->DB->Select("SELECT id FROM adresse WHERE nummer='".$xmldata['stueckliste_artikel']['artikel'][$i]['nummer']."' AND projekt='".$xmldata['stueckliste_artikel']['artikel'][$i]['projekt']."' LIMIT 1");
      else
        $stuecklisteartikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$xmldata['stueckliste_artikel']['artikel'][$i]['nummer']."' LIMIT 1");

      $this->app->DB->Insert("INSERT INTO stueckliste (id,sort,artikel,stuecklistevonartikel,menge) VALUES ('','".($i+1)."','$stuecklisteartikel','$artikel','$menge')");
    }

    if($xmldata['einkaufspreise']['staffelpreis']['ab_menge']  > 0 || $xmldata['einkaufspreise']['staffelpreis']['preis']  > 0)
    {
      $tmp = $xmldata['einkaufspreise']['staffelpreis'];
      $xmldata['einkaufspreise']['staffelpreis']= NULL;
      $xmldata['einkaufspreise']['staffelpreis'][0] = $tmp;
    }

    $this->app->erp->SetzteSperreAPIArtikelPreise($id);
    $cstaffelpreise = isset($xmldata['einkaufspreise']['staffelpreis'])?count($xmldata['einkaufspreise']['staffelpreis']):0;
    for($i=0;$i<$cstaffelpreise;$i++)
    {
      //$projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['artikelliste']['position'][$i]['projekt']."' LIMIT 1");
      $artikel = $id;
      // Markierung fuer SPerrung aller nicht uebegebenen Preise

      $abmenge = $xmldata['einkaufspreise']['staffelpreis'][$i]['ab_menge'];
      $xmldata['einkaufspreise']['staffelpreis'][$i]['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['einkaufspreise']['staffelpreis'][$i]['projekt']."' LIMIT 1");

      if($xmldata['einkaufspreise']['staffelpreis'][$i]['projekt']!="" && !is_array($xmldata['einkaufspreise']['staffelpreis'][$i]['projekt']))
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']."' 
          AND projekt='".$xmldata['einkaufspreise']['staffelpreis'][$i]['projekt']."' AND lieferantennummer!='' LIMIT 1");
      else
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']."' 
          AND lieferantennummer!='' LIMIT 1");

      if($adresse <=0)
      {
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE name='".$xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantname']."' AND name!='' LIMIT 1");
      }

      if($adresse <=0)
      {
        if($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantname']=="")
          $xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantname']="Lieferant";

        $adresse = $this->app->erp->CreateAdresse($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantname'],$firma="1");
        // wenn lieferantennummer vorhanden dann diese verwenden
        if($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']!="" && !is_array($xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']))
          $this->app->DB->Update("UPDATE adresse SET lieferantennummer='".$xmldata['einkaufspreise']['staffelpreis'][$i]['lieferantennummer']."' WHERE id='$adresse' LIMIT 1");

        $this->app->erp->AddRolleZuAdresse($adresse, "Lieferant", "von", "Projekt", $xmldata['projekt'] );
      }



      $bestellnummer = $xmldata['einkaufspreise']['staffelpreis'][$i]['bestellnummer'];
      $bezeichnunglieferant = $xmldata['einkaufspreise']['staffelpreis'][$i]['bezeichnunglieferant'];
      if(is_array($bestellnummer))$bestellnummer="";

      if($bezeichnunglieferant=="")
        $bezeichnunglieferant = $this->app->DB->Select("SELECT name_de FROM artikel WHERE id='$artikel' LIMIT 1");


      $preis = str_replace(',','.',$xmldata['einkaufspreise']['staffelpreis'][$i]['preis']);
      $waehrung = $xmldata['einkaufspreise']['staffelpreis'][$i]['waehrung'];
      if(is_array($waehrung) || $waehrung=="")
        $waehrung="EUR";

      $this->app->erp->AddEinkaufspreis($artikel,$abmenge,$adresse,$bestellnummer,$bezeichnunglieferant,$preis,$waehrung);
    }

    if($xmldata['verkaufspreise']['staffelpreis']['ab_menge']  > 0)
    {
      $tmp = $xmldata['verkaufspreise']['staffelpreis'];
      $xmldata['verkaufspreise']['staffelpreis']= NULL;
      $xmldata['verkaufspreise']['staffelpreis'][0] = $tmp;
    }

    // alle positionen der reihe nach
    $cstaffelpreise = isset($xmldata['verkaufspreise']['staffelpreis'])?count($xmldata['verkaufspreise']['staffelpreis']):0;
    for($i=0;$i<$cstaffelpreise;$i++)
    {
      $artikel = $id;
      $abmenge = $xmldata['verkaufspreise']['staffelpreis'][$i]['ab_menge'];
      $xmldata['verkaufspreise']['staffelpreis'][$i]['projekt'] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['verkaufspreise']['staffelpreis'][$i]['projekt']."' LIMIT 1");

      if($xmldata['verkaufspreise']['staffelpreis'][$i]['projekt']!="" && !is_array($xmldata['verkaufspreise']['staffelpreis'][$i]['projekt']))
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$xmldata['verkaufspreise']['staffelpreis'][$i]['kundennummer']."' AND projekt='".$xmldata['verkaufspreise']['staffelpreis'][$i]['projekt']."' LIMIT 1");
      else
        $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$xmldata['verkaufspreise']['staffelpreis'][$i]['kundennummer']."' LIMIT 1");

      $preis = str_replace(',','.',$xmldata['verkaufspreise']['staffelpreis'][$i]['preis']);
      $waehrung = $xmldata['verkaufspreise']['staffelpreis'][$i]['waehrung'];
      if(is_array($waehrung) || $waehrung=="") $waehrung = "EUR";
      $kundenartikelnummer = $xmldata['verkaufspreise']['staffelpreis'][$i]['kundenartikelnummer'];

      if($xmldata['verkaufspreise']['staffelpreis'][$i]['geloescht']=="1") $geloescht = 1;
      else $geloescht = 0;

      if($xmldata['verkaufspreise']['staffelpreis'][$i]['kundennummer']=="")
        $adresse = 0;
      else {
        // kunde gibt es nicht
        if($adresse <=0)
          continue;
      }

      // pruefe ob es diesen Preis schon gibt? wenn nicht lege einen neuen an ...
      // $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$xmldata['artikelliste']['position'][$i]['projekt']."' LIMIT 1");

      if($xmldata['verkaufspreise']['staffelpreis'][$i]['gruppe']>0)
        $gruppe = $this->app->DB->Select("SELECT id FROM gruppen WHERE id='".$xmldata['verkaufspreise']['staffelpreis'][$i]['gruppe']."' LIMIT 1");
      else $gruppe = 0;

      if($gruppe > 0)
        $this->app->erp->AddVerkaufspreisGruppe($artikel,$abmenge,$gruppe,$preis,$waehrung);
      else
        $this->app->erp->AddVerkaufspreis($artikel,$abmenge,$adresse,$preis,$waehrung);

      //deaktiviere alle alten preise von dem Artikel
    }
    $this->app->erp->EntferneSperreAPIArtikelPreise($id);

    if ($intern == true) {
      return true;
    }
    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }


  function ApiArtikelGet($intern = false, $id="")
  {
    if($id=="")
      $id = $this->app->Secure->GetGET("id");
    $nummer = $this->app->Secure->GetGET("nummer");
    $projekt = $this->app->Secure->GetGET("projekt");

    $xmldata = $this->XMLPost();

    if($nummer=="") $nummer = $xmldata["nummer"];
    if($id=="") $id = $xmldata["id"];
    if($projekt=="") $projekt = $xmldata["projekt"];

    if($nummer!="")
    {
      if($projekt!="")
      {
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
        $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' AND projekt='$projekt' LIMIT 1");
      } else {
        $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$nummer' LIMIT 1");
      }
    }
    //check
    $id = $this->app->DB->Select("SELECT id FROM artikel WHERE id='$id' LIMIT 1");


    if($id > 0) {
      if ($intern == true) {
        return $this->app->erp->XMLArtikel($id);
      }
      $this->XMLResponse(1, $this->app->erp->XMLArtikel($id));
      $this->app->ExitXentral();
    }
    if ($intern == true) {
      return false;
    }
    $this->XMLResponse(5);
    $this->app->ExitXentral();
  }


  function ApiBenutzerCreate()
  {
    $xmldata = $this->XMLPost();
    // pruefe ob es adresse gibt
    $adressecheck = $this->app->DB->Select("SELECT id FROM adresse WHERE id='".$xmldata["adresse"]."' LIMIT 1");
    if($adressecheck <=0)
      $this->XMLResponse(5);

    // pruefe ob es username gibt
    $usercheck = $this->app->DB->Select("SELECT id FROM user WHERE username='".$xmldata["username"]."' LIMIT 1");
    if($usercheck > 0)
      $this->XMLResponse(5);

    // pruefe ob ein Passwort uebertragen wird
    if($xmldata["password"] == ""){
      $this->XMLResponse(5,"<error>kein Passwort übergeben</error>");
    }

    $felder = $xmldata;

    if($felder['type']!="admin")
    {
      $rechtevorlage = $felder['type'];
      $felder['vorlage']=$rechtevorlage;
      $felder['type']="standard";
    }

    $id = $this->app->erp->CreateBenutzer($felder);
    foreach($xmldata as $key=>$value)
    {
      if(is_array($value))$value="";

      if($key=="sonstiges") $value = strip_tags(html_entity_decode($value));
      if($key!="id") {
        if($key=="password") {
          $this->app->DB->Update("UPDATE `user` SET `passwordmd5` = MD5('$value') WHERE `id` = '$id' LIMIT 1");
          $this->app->DB->Update("UPDATE `user` SET `password` = '' WHERE `id` = '$id' LIMIT 1");
        } else {
          $this->app->DB->Update("UPDATE `user` SET $key='$value' WHERE `id` = '$id' LIMIT 1");
        }
      }

    }
    if(empty($xmldata['passwordunenescaped']) && empty($xmldata['passwordsha512']))$this->app->DB->Update("UPDATE `user` SET salt = '', passwordsha512 = '' WHERE id = '$id' LIMIT 1");

    $this->app->erp->AbgleichBenutzerVorlagen();
    $this->XMLResponse(1,"<id>$id</id>");
    $this->app->ExitXentral();
  }



  function ApiBenutzerEdit()
  {
    $xmldata = $this->XMLPost();
    if($xmldata['id'] != ''){
      $id = $xmldata['id'];
    }else{
      $id = $this->app->Secure->GetGET("id");
    }


    $usercheck = $this->app->DB->Select("SELECT id FROM `user` WHERE id='".$id."' LIMIT 1");
    // User gibt es nicht
    if($usercheck <= 0)
      $this->XMLResponse(5);

    $xmldata = $this->XMLPost();
    $usernamecheck = $this->app->DB->Select("SELECT id FROM `user` WHERE username='".$xmldata["username"]."' AND id!='".$id."' LIMIT 1");
    // Username hat schon jemand anders
    if($usernamecheck > 0)
      $this->XMLResponse(6);

    if($xmldata['type']!="admin")
    {
      $rechtevorlage = $xmldata['type'];
      $xmldata['vorlage']=$rechtevorlage;
      $xmldata['type']="standard";
    }

    foreach($xmldata as $key=>$value)
    {
      if(is_array($value))$value="";
      if($key=="sonstiges") $value = strip_tags(html_entity_decode($value));
      if($key!="id")
        $this->app->DB->Update("UPDATE `user` SET $key='$value' WHERE id='$id' LIMIT 1");
    }

    if(empty($xmldata['passwordunenescaped']) && empty($xmldata['passwordsha512']) && !empty($xmldata['passwordmd5']))$this->app->DB->Update("UPDATE `user` SET salt = '', passwordsha512 = '' WHERE id = '$id' LIMIT 1");

    $this->app->erp->AbgleichBenutzerVorlagen();
    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }

  function ApiBenutzerGet()
  {
    $xmldata = $this->XMLPost();
    if($xmldata['id'] != ''){
      $id = $xmldata['id'];
    }else{
      $id = $this->app->Secure->GetGET("id");
    }


    //checl
    $id = $this->app->DB->Select("SELECT id FROM user WHERE id='$id' LIMIT 1");

    if($id > 0){
      $this->XMLResponse(1, $this->app->erp->XMLBenutzer($id));
      $this->app->ExitXentral();
    }

    $this->XMLResponse(5);

    $this->app->ExitXentral();
  }

  function ApiPreiseEdit()
  {
    $xmldata = $this->XMLPostPlain();

    foreach ($xmldata->xml->artikel as $artikel){
      if($artikel->id > 0)
        $id = $this->app->DB->Select("SELECT id FROM artikel WHERE id='$artikel->id' LIMIT 1");
      else if ($artikel->nummer!="")
        $id = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$artikel->nummer."' LIMIT 1");

      if($id<=0) continue;
      $this->app->erp->SetzteSperreAPIArtikelPreise($id);

      foreach ($artikel->verkaufspreise as $vk) {
        foreach ($vk->staffelpreis as $staffelpreis) {

          if($staffelpreis->kundennummer!="")
            $staffelpreis->adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$staffelpreis->kundennummer."' LIMIT 1");
          if($staffelpreis->gruppe > 0)
            $this->app->erp->AddVerkaufspreisGruppe($id,$staffelpreis->ab_menge,$staffelpreis->gruppe,$staffelpreis->preis);
          else if($staffelpreis->adresse > 0)
            $this->app->erp->AddVerkaufspreis($id,$staffelpreis->ab_menge,$staffelpreis->adresse,$staffelpreis->preis);
          else if($staffelpreis->kundennummer=="" && $staffelpreis->adresse <= 0)
            $this->app->erp->AddVerkaufspreis($id,$staffelpreis->ab_menge,0,$staffelpreis->preis);
        }
      }

      // test
      foreach ($artikel->einkaufspreise as $ek) {
        foreach ($ek->staffelpreis as $staffelpreis) {

    $lieferantadresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$staffelpreis->lieferantennummer."' AND lieferantennummer!='' LIMIT 1");
    if($lieferantadresse > 0)
    {
            $this->app->erp->AddEinkaufspreis($id,$staffelpreis->ab_menge,$lieferantadresse,$staffelpreis->bestellnummer,$staffelpreis->bezeichnunglieferant,
    $staffelpreis->preis,$staffelpreis->waehrung);
    }
        }
      }

      $this->app->erp->EntferneSperreAPIArtikelPreise($id);
    }

    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }


  function ApiSessionStart()
  {

    $xmldata = $this->XMLPost();

    $sessionid = $xmldata['sessionID'];
    $userID = $xmldata['userID'];
    $validSession = $xmldata['isValidSession'];

    if($validSession=="1")
      $this->app->DB->Insert("INSERT INTO useronline (user_id,login,sessionid,time) VALUES ('$userID','1','$sessionid',NOW())");

    $this->XMLResponse(1);
    // Eintrag anlegen in useronline
    // class.acl.php erweitern, wenn diese merkt das externer login war dann session freischalten ...
    $this->app->ExitXentral();
  }


  function ApiSessionClose()
  {
    $xmldata = $this->XMLPost();

    $sessionid = $xmldata['sessionID'];

    $this->app->DB->Delete("DELETE FROM useronline WHERE sessionid='$sessionid' LIMIT 1");

    $this->XMLResponse(1);
    // loeschen aktiver login
    $this->app->ExitXentral();
  }


  function ApiAdresseKontaktCreate()
  {
    $id = $this->app->Secure->GetGET("id");
    $kundennummer = $this->app->Secure->GetGET("kundennummer");
    $projekt = $this->app->Secure->GetGET("projekt");


    $xmldata = $this->XMLPost();
    if($id=="" && $kundennummer=="")
    {
      if($xmldata["kundennummer"]!="" && !is_array($xmldata["kundennummer"]))
        $kundennummer = $xmldata["kundennummer"];
      else
        $id = $xmldata["adresse"];
    }
    if($kundennummer!="")
    {
      if($projekt!="")
      {
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
        $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' AND projekt='$projekt' LIMIT 1");
      } else {
        $id = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='$kundennummer' LIMIT 1");
      }
    }

    // Key gibt es nicht
    $id = $this->app->DB->Select("SELECT id FROM adresse WHERE id='$id' LIMIT 1");
    if($id <= 0){
      $this->XMLResponse(5);
      $this->app->ExitXentral();
    }

    $bezeichnung = $xmldata["bezeichnung"];
    $kontakt = $xmldata["kontakt"];
    $this->app->DB->Insert("INSERT INTO adresse_kontakte (id,adresse,bezeichnung,kontakt) VALUES ('','$id','$bezeichnung','$kontakt')");

    $id = $this->app->DB->GetInsertID();

    $this->XMLResponse(1,"<id>$id</id>");
    $this->app->ExitXentral();
  }


  function ApiAdresseKontaktEdit()
  {
    $id = $this->app->Secure->GetGET("id");
	  if($id==''){
		  $xmldata = $this->XMLPost();
		  $id = $xmldata['id'];
    }
    $id = $this->app->DB->Select("SELECT id FROM adresse_kontakte WHERE id='$id' LIMIT 1");
    // Key gibt es nicht
    if($id <= 0){
      $this->XMLResponse(5);
      $this->app->ExitXentral();
    }

    $xmldata = $this->XMLPost();

    foreach($xmldata as $key=>$value)
    {
      if(is_array($value)) {
        $value='';
      }
      if($key!='id'){
        $this->app->DB->Update("UPDATE adresse_kontakte SET $key='$value' WHERE id='$id' LIMIT 1");
      }
    }

    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }

  function ApiAdresseKontaktList()
  {
    $id = (int)$this->app->Secure->GetGET("id");
	  if(!$id){
	    $xmldata = $this->XMLPost();
	    if($xmldata['id']){
	      $id = $xmldata['id'];
      }
	  }


    if(!$this->app->DB->Select("SELECT id FROM adresse WHERE id = '$id' AND geloescht <> 1 LIMIT 1"))
    {
      $this->XMLResponse(5);
      $this->app->ExitXentral();
    }
    $kontakte = $this->app->DB->SelectArr("SELECT * FROM adresse_kontakte WHERE adresse = '$id' ORDER BY bezeichnung");
    if($kontakte)
    {
      $xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<kontakte2>
</kontakte2>
XML;
      $_xmlobj = new SimpleXMLExtended($xmlstr);
      $xmlobj = $_xmlobj->AddChild('kontakte','');
      $xmlobj->AddChild('anz_gesamt',count($kontakte));
      $xmlobj->AddChild('anz_result',count($kontakte));
      //$sxe->addAttribute('type', 'documentary');

      foreach($kontakte as $kontakt)
      {
        $kobj = $xmlobj->AddChild('kontakt','');
        foreach($kontakt as $k => $v)
        {
          $valobj = $kobj->AddChild($k, (string)$v);
        }
      }

      $xml = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<kontakte2>",'</kontakte2>'),array('','',''),$_xmlobj->asXML());
      $this->XMLResponse(1,$xml);
      $this->app->ExitXentral();
    }
    $this->XMLResponse(8);
    $this->app->ExitXentral();
  }

  function ApiAdresseKontaktGet()
  {
	  $id = (int)$this->app->Secure->GetGET("id");
	  if(!$id){
		  $xmldata = $this->XMLPost();
		  if($xmldata['id']){
			  $id = $xmldata['id'];
		  }
	  }

    //checl
    $id = $this->app->DB->Select("SELECT id FROM adresse_kontakte WHERE id='$id' LIMIT 1");
    if($id > 0){
      $this->XMLResponse(1, $this->app->erp->XMLAdresseKontakt($id));
      $this->app->ExitXentral();
    }

    $this->XMLResponse(5);
    $this->app->ExitXentral();
  }

  function ApiAdresseListeGet($intern = false)
  {
    $xmldata = $this->XMLPost();
    $exakt = false;
    $filterspalten = array('email','name','plz','ort','kundennummer','land','strasse','abteilung','unterabteilung','id');
    $limit = '';
    $_limit = 999999999;
    $offset = 0;
    if(isset($xmldata['limit']))
    {
      $_limit = $xmldata['limit'];
      if(isset($xmldata['offset']))
      {
        $offset = $xmldata['offset'];
        //$limit = ' LIMIT '.(int)$xmldata['offset'].', '.(int)$xmldata['limit'];
      }else{
        //$limit = ' LIMIT '.(int)$xmldata['limit'];
      }
    }

    if(!empty($xmldata['gruppen']))
    {
      if(isset($xmldata['gruppen']['kennziffer']))
      {
        if(!is_array($xmldata['gruppen']['kennziffer']))
        {
          $gruppen[0] = $xmldata['gruppen']['kennziffer'];
        }else{
          foreach($xmldata['gruppen']['kennziffer'] as $k => $gruppe)
          {
            $gruppen[] = $gruppe;
          }
        }
      }
    }

    $sumjoin = '';
    $sumwhere = '';
    $sumspalten = '';
    $re = false;
    $gu = false;
    $be = false;
    $ab = false;
    $an = false;
    $filterkategorie = false;

    $bvertrieb = 0;
    if($xmldata && is_array($xmldata))
    {
      foreach($xmldata as $key => $filter)
      {
        $filterescaped = '';
        if(!is_array($filter))$filterescaped = $this->app->DB->real_escape_string($filter);
        switch(strtoupper($key))
        {
          case 'VERTRIEB':
            $bvertrieb = (int)$filter;
          break;
          case 'KATEGORIE':
            $filterkategorie = (int)$filter;
          break;
        }
      }
    }

    if(isset($xmldata['summierung']))
    {
      if(isset($xmldata['summierung']['beleg']))
      {
        if(!is_array($xmldata['summierung']['beleg']) || !isset($xmldata['summierung']['beleg'][0]))
        {
          $belegea[0] = $xmldata['summierung']['beleg'];
        }else{
          $belegea = $xmldata['summierung']['beleg'];
        }
        foreach($belegea as $beleg)
        {
          switch($beleg)
          {
            case 'rechnung':
              if(!$re)
              {
                $re = true;
                $sumspalten .= ', (SELECT sum(re.umsatz_netto) FROM rechnung re  WHERE re.adresse = adr.id '.($bvertrieb?" AND re.vertriebid = '".$bvertrieb."' ":'');
                $sumjoin .= ' LEFT JOIN rechnung re ON re.adresse = adr.id ';
                $skat = '';
                if($filterkategorie)$skat = ' , (SELECT rear.id FROM rechnung re2 INNER JOIN rechnung_position rep2 ON re2.id = rep2.rechnung  INNER JOIN artikel rear ON rep2.artikel = rear.id WHERE re2.adresse = adr.id AND rear.typ = \''.$filterkategorie.'_kat\' ';
                //if($sumwhere != '')$sumwhere .= ' AND ';
                if(isset($xmldata['status']))
                {
                  $sumspalten .= " AND re.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                  $skat .= " AND re2.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                  //$sumwhere .= " (isnull(re.id) OR (re.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                }else{
                  $sumspalten .= " AND re.status != 'storniert' AND re.status <> 'angelegt' ";
                  $skat .= " AND re2.status != 'storniert' AND re2.status <> 'angelegt' ";
                  //$sumwhere .= " (isnull(re.id) OR (re.status != 'storniert' AND re.status <> 'angelegt' ";
                }
                if(isset($xmldata['datumvon']))
                {
                  $sumspalten .= " AND re.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                  $skat .= " AND re2.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                  //$sumwhere .= " AND re.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                }
                if(isset($xmldata['datumbis']))
                {
                  $sumspalten .= " AND re.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                  $skat .= " AND re2.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                  //$sumwhere .= " AND re.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                }
                //$sumwhere .= "))";

                $sumspalten .= ') as rechnung_umsatz_netto';
                if($filterkategorie)$skat .= ' LIMIT 1) as re_iskat ';
                if($filterkategorie)$sumspalten .= $skat;
              }
            break;
            case 'gutschrift':
              if(!$gu)
              {
                $gu = true;
                $sumspalten .= ', (SELECT sum(gu.umsatz_netto) FROM gutschrift gu WHERE gu.adresse = adr.id '.($bvertrieb?" AND gu.vertriebid = '".$bvertrieb."' ":'');
                //$sumspalten .= ', sum(gu.soll) as gutschrift_soll';
                $sumjoin .= ' LEFT JOIN gutschrift gu ON gu.adresse = adr.id ';
                $skat = '';
                if($filterkategorie)$skat = ' , (SELECT guar.id FROM gutschrift gu2 INNER JOIN gutschrift_position gup2 ON gu2.id = gup2.gutschrift  INNER JOIN artikel guar ON gup2.artikel = guar.id WHERE gu2.adresse = adr.id  AND guar.typ = \''.$filterkategorie.'_kat\' ';
                //if($sumwhere != '')$sumwhere .= ' AND ';
                if(isset($xmldata['status']))
                {
                  $sumspalten .= " AND gu.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                  $skat .= " AND gu2.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                  //$sumwhere .= " (isnull(gu.id) OR (gu.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                }else{
                  $sumspalten .= " AND gu.status != 'storniert' AND gu.status <> 'angelegt' ";
                  $skat .= " AND gu2.status != 'storniert' AND gu2.status <> 'angelegt' ";
                  //$sumwhere .= " (isnull(gu.id) OR (gu.status != 'storniert' AND gu.status <> 'angelegt'  ";
                }
                if(isset($xmldata['datumvon']))
                {
                  $sumspalten .= " AND gu.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                  $skat .= " AND gu2.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                  //$sumwhere .= " AND gu.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                }
                if(isset($xmldata['datumbis']))
                {
                  $sumspalten .= " AND gu.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                  $skat .= " AND gu2.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                  //$sumwhere .= " AND gu.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                }
                //$sumwhere .= "))";
                $sumspalten .= ') as gutschrift_umsatz_netto';
                if($filterkategorie)$skat .= ' LIMIT 1) as gu_iskat ';
                if($filterkategorie)$sumspalten .= $skat;
              }
            break;
            case 'bestellung':
              if(!$be)
              {
                $be = true;
                //$sumspalten .= ', sum(be.gesamtsumme) as bestellung_gesamtsumme';
                $sumspalten .= ', (SELECT sum(be.gesamtsumme) FROM bestellung be WHERE be.adresse = adr.id ';
                $sumjoin .= ' LEFT JOIN bestellung be ON be.adresse = adr.id ';
                $skat = '';
                if($filterkategorie)$skat = ' , (SELECT bear.id FROM bestellung be2 INNER JOIN bestellung_position bep2 ON be2.id = bep2.bestellung  INNER JOIN artikel bear ON bep2.artikel = bear.id WHERE be2.adresse = adr.id  AND bear.typ = \''.$filterkategorie.'_kat\' ';
                //if($sumwhere != '')$sumwhere .= ' AND ';
                if(isset($xmldata['status']))
                {
                  $sumspalten .= " AND be.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                  $skat .= " AND be2.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                  //$sumwhere .= " (isnull(be.id) OR (be.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                }else{
                  $sumspalten .= " AND be.status != 'storniert' AND be.status <> 'angelegt' ";
                  $skat .= " AND be2.status != 'storniert' AND be2.status <> 'angelegt' ";
                  //$sumwhere .= " (isnull(be.id) OR (be.status != 'storniert' AND be.status <> 'angelegt' ";
                }
                if(isset($xmldata['datumvon']))
                {
                  $sumspalten .= " AND be.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                  $skat .= " AND be2.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                  //$sumwhere .= " AND be.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                }
                if(isset($xmldata['datumbis']))
                {
                  $sumspalten .= " AND be.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                  $skat .= " AND be2.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                  //$sumwhere .= " AND be.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                }
                //$sumwhere .= "))";
                $sumspalten .= ') as bestellung_gesamtsumme ';
                if($filterkategorie)$skat .= ' LIMIT 1) as be_iskat ';
                if($filterkategorie)$sumspalten .= $skat;
              }
            break;
            case 'auftrag':
              if(!$ab)
              {
                $ab = true;
                $sumspalten .= ', (SELECT sum(ab.umsatz_netto) FROM auftrag ab WHERE ab.adresse = adr.id '.($bvertrieb?" AND re.vertriebid = '".$bvertrieb."' ":'');
                //$sumspalten .= ', sum(ab.gesamtsumme) as auftrag_gesamtsumme';
                $sumjoin .= ' LEFT JOIN auftrag ab ON ab.adresse = adr.id ';
                $skat = '';
                if($filterkategorie)$skat = ' , (SELECT abar.id FROM auftrag ab2 INNER JOIN auftrag_position abp2 ON ab2.id = abp2.auftrag INNER JOIN artikel abar ON abp2.artikel = abar.id WHERE ab2.adresse = adr.id  AND abar.typ = \''.$filterkategorie.'_kat\' ';
                //if($sumwhere != '')$sumwhere .= ' AND ';
                if(isset($xmldata['status']))
                {
                  $sumspalten .= " AND ab.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                  $skat .= " AND ab2.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                  //$sumwhere .= " (isnull(ab.id) OR (ab.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                }else{
                  $sumspalten .= " AND ab.status != 'storniert' AND ab.status <> 'angelegt' ";
                  $skat .= " AND ab2.status != 'storniert' AND ab2.status <> 'angelegt' ";
                  //$sumwhere .= " (isnull(ab.id) OR (ab.status != 'storniert' AND ab.status <> 'angelegt' ";
                }
                if(isset($xmldata['datumvon']))
                {
                  $sumspalten .= " AND ab.datum <= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                  $skat .= " AND ab2.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                  //$sumwhere .= " AND ab.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                }
                if(isset($xmldata['datumbis']))
                {
                  $sumspalten .= " AND ab.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                  $skat .= " AND ab2.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                  //$sumwhere .= " AND ab.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                }
                //$sumwhere .= "))";
                $sumspalten .= ') as auftrag_umsatz_netto ';
                if($filterkategorie)$skat .= ' LIMIT 1) as ab_iskat ';
                if($filterkategorie)$sumspalten .= $skat;
              }
            break;
            case 'angebot':
              if(!$an)
              {
                $an = true;
                $sumspalten .= ', (SELECT sum(an.umsatz_netto) FROM angebot an WHERE an.adresse = adr.id '.($bvertrieb?" AND an.vertriebid = '".$bvertrieb."' ":'');
                //$sumspalten .= ', sum(an.gesamtsumme) as angebot_gesamtsumme';
                $sumjoin .= ' LEFT JOIN angebot an ON an.adresse = adr.id ';
                $skat = '';
                if($filterkategorie)$skat = ' , (SELECT anar.id FROM angebot an2 INNER JOIN angebot_position an2 ON an2.id = anp2.auftrag INNER JOIN artikel anar ON anp2.artikel = anar.id WHERE an2.adresse = adr.id  AND anar.typ = \''.$filterkategorie.'_kat\' ';
                //if($sumwhere != '')$sumwhere .= ' AND ';
                if(isset($xmldata['status']))
                {
                  //$sumwhere .= " (isnull(an.id) OR (an.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                  $sumspalten .= " AND an.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                  $skat .= " AND an2.status like '".$this->app->DB->real_escape_string($xmldata['status'])."' ";
                }else{
                  //$sumwhere .= " (isnull(an.id) OR (an.status != 'storniert' AND an.status <> 'angelegt' ";
                  $sumspalten .= " AND an.status != 'storniert' AND an.status <> 'angelegt' ";
                  $skat .= " AND an2.status != 'storniert' AND an2.status <> 'angelegt' ";
                }
                if(isset($xmldata['datumvon']))
                {
                  $sumspalten .= " AND an.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                  $skat .= " AND an2.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                  //$sumwhere .= " AND an.datum >= '".$this->app->DB->real_escape_string($xmldata['datumvon'])."' ";
                }
                if(isset($xmldata['datumbis']))
                {
                  $sumspalten .= " AND an.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                  $skat .= " AND an2.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                  //$sumwhere .= " AND an.datum <= '".$this->app->DB->real_escape_string($xmldata['datumbis'])."' ";
                }
                //$sumwhere .= "))";
                $sumspalten .= ') as angebot_umsatz_netto ';
                if($filterkategorie)$skat .= ' LIMIT 1) as an_iskat ';
                if($filterkategorie)$sumspalten .= $skat;
              }
            break;
          }
        }
        /*
        if($sumwhere != '')
        {

          if($re)
          {
            $sumwherea[] = " not isnull(re.id) ";
          }
          if($gu)
          {
            $sumwherea[] = " not isnull(gu.id) ";
          }
          if($ab)
          {
            $sumwherea[] = " not isnull(ab.id) ";
          }
          if($an)
          {
            $sumwherea[] = " not isnull(an.id) ";
          }
          if($be)
          {
            $sumwherea[] = " not isnull(be.id) ";
          }

          $sumwhere .= " AND ( ".implode(" OR ",$sumwherea)." ) ";
        }*/
      }
    }

    //if($sumwhere != '')$sumwhere = ' AND ('.$sumwhere.') ';
    $sumwhere = '';
    $sumjoin = '';
    $searchmode = ' AND ';
    if($xmldata && is_array($xmldata))
    {
      foreach($xmldata as $key => $filter)
      {
        $filterescaped = '';
        if(!is_array($filter))$filterescaped = $this->app->DB->real_escape_string($filter);
        switch(strtoupper($key))
        {
          case 'EXAKT':
            $exakt = true;
          break;
          case 'SEARCHMODE':
            if(strtoupper($filter) == 'AND')$searchmode = ' AND ';
            if(strtoupper($filter) == 'OR')$searchmode = ' OR ';
          break;
          case 'SEARCH':

            if(is_array($filter))
            {
              if(isset($filter[0]))
              {
                foreach($filter as $k => $_filter)
                {
                  if(isset($_filter['SUCHE']))$_filter['suche'] = $_filter['SUCHE'];
                  if(isset($_filter['FIELD']))$_filter['field'] = $_filter['FIELD'];
                  if(isset($_filter['EXAKT']))$_filter['exakt'] = $_filter['EXAKT'];
                  if(isset($_filter['suche']) && isset($_filter['field']) && in_array(strtolower($_filter['field']),$filterspalten))
                  {
                    if(isset($_filter['exakt']) && $_filter['exakt'] == 1)
                    {
                      if(strtolower($_filter['field']) == 'id')
                      {
                        $swhere[] = " adr.".$_filter['field']." LIKE '".$this->app->DB->real_escape_string((int)($_filter['suche']))."' ";
                      }else
                      $swhere[] = " adr.".$_filter['field']." LIKE '".$this->app->DB->real_escape_string(base64_decode($_filter['suche']))."' ";
                    }else{
                      if(strtolower($_filter['field']) == 'id')
                      {
                        $swhere[] = " adr.".$_filter['field']." LIKE '%".$this->app->DB->real_escape_string((int)($_filter['suche']))."%' ";
                      }
                        else
                      $swhere[] = " adr.".$_filter['field']." LIKE '%".$this->app->DB->real_escape_string(base64_decode($_filter['suche']))."%' ";
                    }
                  }
                }
              }else{
                if(isset($filter['SUCHE']))$filter['suche'] = $filter['SUCHE'];
                if(isset($filter['FIELD']))$filter['field'] = $filter['FIELD'];
                if(isset($filter['EXAKT']))$filter['exakt'] = $filter['EXAKT'];
                if(isset($filter['suche']) && isset($filter['field']) && in_array(strtolower($filter['field']),$filterspalten))
                {
                  if(strtolower($filter['field']) == 'id')
                  {
                    if(isset($filter['exakt']) && $filter['exakt'] == 1)
                    {
                      $swhere[] = " adr.".$filter['field']." LIKE '".$this->app->DB->real_escape_string((int)($filter['suche']))."' ";
                    }else{
                      $swhere[] = " adr.".$filter['field']." LIKE '%".$this->app->DB->real_escape_string((int)($filter['suche']))."%' ";
                    }

                  }else{
                    if(isset($filter['exakt']) && $filter['exakt'] == 1)
                    {
                      $swhere[] = " adr.".$filter['field']." LIKE '".$this->app->DB->real_escape_string(base64_decode($filter['suche']))."' ";
                    }else{
                      $swhere[] = " adr.".$filter['field']." LIKE '%".$this->app->DB->real_escape_string(base64_decode($filter['suche']))."%' ";
                    }
                  }
                }
              }
            }else{
              $suchbegriff = $this->app->DB->real_escape_string(base64_decode($filterescaped));
            }

            //$subwhere[] = " (adr.name like '%$filterescaped%' OR adr.plz like '%$filterescaped%'  OR adr.ort like '%$filterescaped%' OR adr.kundennummer like '%$filterescaped%') ";

          break;
          case 'SUCHSPALTE':
            //if($filterescaped)$customsearch[] = $filterescaped;
          break;
          case 'VERTRIEB':
            $subwhere[] = " adr.vertrieb = '".(int)$filter."' ";
          break;
          case 'EXT':
            $filter_ext = $filter;
          break;
          case 'ID':
            if(is_array($filter) && isset($filter[0]))
            {
              foreach($filter as $k => $_filter)
              {
                $subwhere[] = " adr.id = '".(int)$_filter."' ";
              }
            }else
            $subwhere[] = " adr.id = '".(int)$filter."' ";
          break;
        }
      }
    }


    if(isset($suchbegriff) || !empty($swhere))
    {
      if(empty($swhere))
      {
        foreach($filterspalten as $f)
        {
          $swhere[] = " adr.".$f." like '".($exakt?'':"%").($suchbegriff).($exakt?'':"%")."' ";
        }
        $subwhere[] = ' ('.implode(' '.$searchmode.' ', $swhere).') ';
      }else{
        /*foreach($customsearch as $f)
        {
          if(in_array($f, $filterspalten))$swhere[] = " adr.".$f." like '".($exakt?'':"%").($suchbegriff).($exakt?'':"%")."' ";
        }*/
        if(!empty($swhere))$subwhere[] = " (".implode(' '.$searchmode.' ', $swhere).') ';
      }
    }
    $where = '1';
    if(!empty($subwhere))$where = implode(' '.$searchmode.' ',$subwhere);


    $order = '';
    if(isset($xmldata['order'])){
      if(!isset($xml['order'][0]))
      {
        $xmldata['order'][0] = $xmldata['order'];
      }
      foreach($xmldata['order'] as $key => $sort)
      {
        $field = '';
        if(isset($sort['field']))
        {
          switch(strtolower($sort['field']))
          {
            case 'gruppenname':
            case 'kennziffer':
              if($gruppen)$field = 'gr.name';
            break;

            case 'name':
            case 'plz':
            case 'ort':
            case 'telefon':
            case 'telefax':
            case 'ansprechpartner':
            case 'typ':
            case 'strasse':
            case 'land':
            case 'email':
            case 'kundennummer':
            case 'lieferantennummer':
              $field = 'adr.'.strtolower($sort['field']);
            break;
          }
          if(!empty($field))
          {
            if(isset($sort['desc']) && $sort['desc'] == 1)
            {
              $field .= " DESC";
            }
            if($order != '')$order .= ',';
            $order .= $field;
          }
        }
      }
    }
    if($order != '')$order = $order.',';

    if(isset($gruppen))
    {
      $where .= " AND (gr.kennziffer = '".implode("' OR gr.kennziffer = '",$gruppen). "') AND (ar.bis = '0000-00-00' OR ar.bis >= date(now())) ";
      if(isset($filter_ext))
      {
        if(strtolower($filter_ext) == 'ja')
        {
          $where .= " AND not isnull(am.id) ";
        }elseif(strtolower($filter_ext) == 'nein')
        {
          $where .= " AND isnull(am.id) ";
        }
        $sql = "SELECT adr.*, gr.kennziffer, gr.name as gruppenname,am.id_ext $sumspalten FROM adresse adr LEFT JOIN api_mapping am ON am.id_int = adr.id AND am.tabelle = 'adresse' INNER JOIN adresse_rolle ar ON adr.id = ar.adresse AND ar.objekt like 'gruppe' INNER JOIN gruppen gr ON gr.id = ar.parameter $sumjoin WHERE $where $sumwhere ORDER BY $order adr.name, adr.id $limit";
        $adressen = $this->app->DB->Query($sql);

      }else{
        $sql = "SELECT adr.*, gr.kennziffer, gr.name as gruppenname $sumspalten FROM adresse adr INNER JOIN adresse_rolle ar ON adr.id = ar.adresse AND ar.objekt like  'gruppe' INNER JOIN gruppen gr ON gr.id = ar.parameter $sumjoin WHERE $where $sumwhere ORDER BY $order adr.name, adr.id $limit";
        $adressen = $this->app->DB->Query($sql);

      }
    }
    else{
      if(isset($filter_ext))
      {
        if(strtolower($filter_ext) == 'ja' || $filter_ext === '1' || $filter_ext === 1)
        {
          $where .= " AND not isnull(am.id) ";
        }elseif(strtolower($filter_ext) == 'nein' || $filter_ext === '0' || $filter_ext === 0)
        {
          $where .= " AND isnull(am.id) ";
        }
        $sql = "SELECT adr.*,am.id_ext $sumspalten FROM adresse adr LEFT JOIN api_mapping am ON am.id_int = adr.id AND am.tabelle = 'adresse' $sumjoin WHERE $where $sumwhere ORDER BY $order adr.name, adr.id $limit";
        $adressen = $this->app->DB->Query($sql);

      }else{
        $sql = "SELECT adr.* $sumspalten FROM adresse adr $sumjoin WHERE $where  $sumwhere ORDER BY $order adr.name, adr.id $limit";
        $adressen = $this->app->DB->Query($sql);

      }
    }
    error_log($sql . "\r\n", 3, "/var/www/html/debug.log");
    if($adressen)
    {

$xmlstr = <<<XML
<?xml version="1.0" standalone="yes"?>
<adressen2>
</adressen2>
XML;
      $_xmlobj = new SimpleXMLExtended($xmlstr);
      $xmlobj = $_xmlobj->AddChild('adressen','');
      //$sxe->addAttribute('type', 'documentary');



      $xml = '';
      $anz_gesamt = 0;
      $anz_result = 0;
      $i = 0;
      $gesamt_umsatz = 0;
      $anzeige_umsatz = 0;
      while($adresse = $this->app->DB->Fetch_Array($adressen))
      {

        $do = true;
        if($re && $gu)
        {

          if(isset($xmldata['umsatzvon']))
          {
            if((float)$adresse['rechnung_umsatz_netto'] - (float)$adresse['gutschrift_umsatz_netto'] < (float)$xmldata['umsatzvon'])$do = false;
          }
          if(isset($xmldata['umsatzbis']))
          {
            if((float)$adresse['rechnung_umsatz_netto'] - (float)$adresse['gutschrift_umsatz_netto'] > (float)$xmldata['umsatzbis'])$do = false;
          }
        }elseif($re)
        {
          if(isset($xmldata['umsatzvon']))
          {
            if((float)$adresse['rechnung_umsatz_netto'] < (float)$xmldata['umsatzvon'])$do = false;
          }
          if(isset($xmldata['umsatzbis']))
          {
            if((float)$adresse['rechnung_umsatz_netto'] > (float)$xmldata['umsatzbis'])$do = false;
          }
        }
        if($filterkategorie && $do)
        {
          $do2 = false;
          if($re && $adresse['re_iskat'])$do2 = true;
          if($gu && $adresse['gu_iskat'])$do2 = true;
          if($be && $adresse['be_iskat'])$do2 = true;
          if($ab && $adresse['ab_iskat'])$do2 = true;
          if($an && $adresse['an_iskat'])$do2 = true;
          if(!$do2)$do = false;
        }
        if($do && $re && $gu)
        {
          $gesamt_umsatz += (float)$adresse['rechnung_umsatz_netto'] - (float)$adresse['gutschrift_umsatz_netto'];
        }
        if($do && $i >= $offset && $i < $offset+$_limit)
        {
          $anzeige_umsatz += (float)$adresse['rechnung_umsatz_netto'] - (float)$adresse['gutschrift_umsatz_netto'];
          $adrobj = $xmlobj->AddChild('adresse','');
          //$xml .= '<adresse>';
          $RowKeys = array_keys($adresse);
          $RowValues = array_values($adresse);
          $cRowKeys = count($RowKeys);
          for($k = 1; $k < $cRowKeys;$k+=2)
          {
            if($RowKeys[$k] !== 're_iskat' && $RowKeys[$k] !== 'gu_iskat' && $RowKeys[$k] !== 'be_iskat' && $RowKeys[$k] !== 'ab_iskat' && $RowKeys[$k] !== 'an_iskat')
            {
              if($RowKeys[$k] === 'rechnung_umsatz_netto' || $RowKeys[$k] === 'gutschrift_umsatz_netto' || $RowKeys[$k] === 'bestellung_umsatz_netto' || $RowKeys[$k] === 'auftrag_umsatz_netto' || $RowKeys[$k] === 'angebot_umsatz_netto')
                $RowValues[$k] = number_format((float)$this->GetPlainText($RowValues[$k]),2,'.','');
              $valobj = $adrobj->AddChild($RowKeys[$k],'');
              if($this->usecdata)
              {
                if ((string)$this->GetPlainText($RowValues[$k]) != '')$valobj->addCData($RowValues[$k]);
              }else {
                if ((string)$this->GetPlainText($RowValues[$k]) != '') $valobj->value = (string)$this->app->erp->GetPlainText($RowValues[$k]);
              }
            }
            //$xml .= '<'.$RowKeys[$k].'>'.(string)$this->app->erp->GetPlainText($RowValues[$k]).'</'.$RowKeys[$k].'>';
          }
          //$xml .= '</adresse>';
          $anz_result++;
        }
        if($do)$i++;
      }
      $xmlobj->AddChild('anz_gesamt',$i);
      $xmlobj->AddChild('anz_result',$anz_result);
      if($re && $gu)
      {
        $xmlobj->AddChild('anzeige_umsatz',number_format($anzeige_umsatz,2,'.',''));
        $xmlobj->AddChild('gesamt_umsatz',number_format($gesamt_umsatz,2,'.',''));
      }
      $xml = str_replace(array('<?xml version="1.0" standalone="yes"?>',"<adressen2>",'</adressen2>'),array('','',''),$_xmlobj->asXML());
      unset($_xmlobj);

    }else{
      $xml = '<anz_result>0</anz_result><anz_gesamt>0</anz_gesamt>';
      if($this->app->DB->error())
      {
        $xml .= '<sqlerror>' .$this->GetPlainText($this->app->DB->error()).'</sqlerror>';
      }
    }
    //$xml .= $xmldata;
    if ($intern != false) {
      return $xml;
    }
    $this->XMLResponse(1,$xml);
    $this->app->ExitXentral();
  }


  function ApiGruppeCreate()
  {
    $id = $this->app->Secure->GetGET("id");

    $xmldata = $this->XMLPost();

    $this->app->DB->Insert("INSERT INTO gruppen (id) VALUES ('')");

    $id = $this->app->DB->GetInsertID();

    $this->ApiGruppeEdit($id,true);

    $this->XMLResponse(1,"<id>$id</id>");
    $this->app->ExitXentral();
  }


  function ApiGruppeEdit($id="",$internal=false)
  {
    $xmldata = $this->XMLPost();
    $id = $xmldata['id'];
    if($id <=0)
      $id = $this->app->Secure->GetGET("id");

    $id = $this->app->DB->Select("SELECT id FROM gruppen WHERE id='$id' LIMIT 1");
    // Key gibt es nicht
    if($id <= 0)
      $this->XMLResponse(5);

    $xmldata = $this->XMLPost();

    if($xmldata['porto_preis']!="" && $xmldata['porto_artikelid']!="" && !is_array($xmldata['porto_preis']))
    {
      $this->app->erp->AddVerkaufspreisGruppe($xmldata['porto_artikelid'],1,$id,$xmldata['porto_preis']);
    }

    foreach($xmldata as $key=>$value)
    {
      if(is_array($value)) {
        $value='';
      }
      if($key==='internebemerkung') {
        $value = html_entity_decode($value);
      }
      if($key!=='id'){
        $this->app->DB->Update("UPDATE gruppen SET $key='$value' WHERE id='$id' LIMIT 1");
      }
    }

    if($internal){
      return $id;
    }

    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }


  public function ApiGruppeGet()
  {
    $xmldata = $this->XMLPost();
    if($xmldata['id'] == '') {
      $id = $this->app->Secure->GetGET("id");
    }else{
      $id =$xmldata['id'];
    }
    //checl
    $id = $this->app->DB->Select("SELECT id FROM gruppen WHERE id='$id' LIMIT 1");

    if($id > 0){
      $this->XMLResponse(1, $this->app->erp->XMLGruppe($id, $this->usecdata));
      $this->app->ExitXentral();
    }

    $this->XMLResponse(5);
    $this->app->ExitXentral();
  }


  // alte funktion
  public function ApiShopimages()
  {
    $artikel = $this->app->Secure->GetGET("artikel");
    $number = $this->app->Secure->GetGET("number");
    $datei = $this->app->DB->SelectArr("SELECT datei FROM  datei_stichwoerter WHERE subjekt='Shopbild' AND objekt='Artikel' AND parameter='$artikel'");
    if($number <= 0) $number = 1;

    $datei = $datei[$number-1]['datei'];

    $mimetype = mime_content_type($this->app->erp->GetDateiPfad($datei));
    // Wir werden eine PDF Datei ausgeben
    header('Content-type: '.$mimetype);
    echo $this->app->erp->GetDatei($datei);
    $this->app->ExitXentral();
  }

  public function ApiExportVorlageGet()
  {
    $id = $this->app->Secure->GetGET("id");
    $projekt = $this->app->Secure->GetGET("projekt");
    $von = $this->app->Secure->GetGET("von");
    $bis = $this->app->Secure->GetGET("bis");

    if($id!="")
    {
      if($projekt!="")
      {
        $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='$projekt' LIMIT 1");
      }
    }

    //check
    $id = $this->app->DB->Select("SELECT id FROM exportvorlage WHERE id='$id' AND apifreigabe=1 LIMIT 1");

    $filter['projekt']=$projekt;
    $filter['von']=$von;
    $filter['bis']=$bis;

    if($id > 0){
      $this->XMLResponse(1, $this->app->erp->XMLExportVorlage($id, $filter, $this->usecdata));
      $this->app->ExitXentral();
    }

    $this->XMLResponse(5);
    $this->app->ExitXentral();
  }

  public function ApiBerichteGet()
  {
    $id = $this->app->Secure->GetGET("id");

    $xmldata = $this->XMLPost();
    if($id=="") $id = $xmldata['id'];

    $id = $this->app->DB->Select("SELECT id FROM berichte WHERE id='$id' LIMIT 1");

    if($id > 0){
      $this->XMLResponse(1, $this->app->erp->XMLBerichte($id, $this->usecdata));
      $this->app->ExitXentral();
    }
    $this->XMLResponse(5);
    $this->app->ExitXentral();
  }

  public function ApiArtikelList($intern = false)
  {
    $xmldata = $this->XMLPost();
    $keys = array_keys($xmldata);
    $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,  a.* FROM artikel a";
    if($xmldata['kategorie'] != ''){
      $sql = "SELECT SQL_CALC_FOUND_ROWS a.id,  a.*,ak.bezeichnung FROM artikel a";
      $tabellenkuerzel = "a.";
      $sql .= "  inner join artikelkategorien ak on ak.id = SUBSTRING_INDEX(a.typ,'_',1)";
    }
    $wherevorhanden = false;
    $ckeys = count($keys);
    for($i=0; $i<$ckeys; $i++){
      if( ($keys[$i] != "order") && ($keys[$i] != "field") && ($keys[$i] != "limit") && ($keys[$i] != "offset") ){
        $this->app->DB->Select("SELECT '" . $keys[$i] . "' FROM artikel LIMIT 1");
        if ($this->app->DB->error() == '') {
          if ($wherevorhanden == false) {
            if($keys[$i] == 'kategorie'){
              // schauen ob mehrere kategorien
              if(is_array($xmldata[$keys[$i]])){
                $sql .= " WHERE (";
                foreach($xmldata[$keys[$i]] as $katvalue){
                  if(is_numeric($katvalue)){
                    $sql .= " ak.id = '" . $katvalue . "' OR ";
                  } else {
                    $sql .= " ak.bezeichnung LIKE '%" . $katvalue . "%' OR ";
                  }
                }
                $sql = rtrim($sql, " OR");
                $sql .= " ) ";

              }else{
                if(is_numeric($xmldata[$keys[$i]])){
                  $sql .= " WHERE ak.id = '" . $xmldata[$keys[$i]] . "' AND ";
                } else {
                  $sql .= " WHERE ak.bezeichnung LIKE '%" . $xmldata[$keys[$i]] . "%' AND ";
                }
              }
            } else {
              $sql .= " WHERE " . $tabellenkuerzel.$keys[$i] . " LIKE '%" . $xmldata[$keys[$i]] . "%' AND ";
            }

            $wherevorhanden = true;
          } else {
            if($keys[$i] === 'kategorie') {
              $sql .= "ak.bezeichnung LIKE '%" . $xmldata[$keys[$i]] . "%' AND ";
            }elseif($keys[$i] === 'nummer') {
              if(is_array($xmldata[$keys[$i]])) {

              } else{
                $sql .= $tabellenkuerzel . $keys[$i] . " LIKE '%" . $xmldata[$keys[$i]] . "%' AND ";
              }
            } else{
              $sql .= $tabellenkuerzel.$keys[$i] . " LIKE '%" . $xmldata[$keys[$i]] . "%' AND ";
            }
          }
        }
      }
    }
    $sql = trim($sql);
    $sql = rtrim($sql, " AND");

    if (array_key_exists('order', $xmldata)) {
      if (is_array($xmldata['order'])) {
        $orderkeys = array_keys($xmldata['order']);
        if (array_key_exists('field', $xmldata['order'])) {
          if ($xmldata['order']['field'] != "") {
            $this->app->DB->Select("SELECT " . $xmldata['order']['field'] . " FROM artikel LIMIT 1");
            if ($this->app->DB->error() == '') {
              if ($xmldata['order']['desc'] == 0 || $xmldata['order']['desc'] == "") {
                $sql .= " ORDER BY " . $tabellenkuerzel . $xmldata['order']['field'] . " ASC";
              } elseif ($xmldata['order']['desc'] == 1) {
                $sql .= " ORDER BY " . $tabellenkuerzel . $xmldata['order']['field'] . " DESC";
              }
            }
          }

        }
      }

      if (array_key_exists('limit', $xmldata)) {
        if ($xmldata['limit'] > 0) {
          if (array_key_exists('offset', $xmldata)) {
            if ($xmldata['offset'] != "") {
              $sql .= " LIMIT " . $xmldata['offset'] . ", " . $xmldata['limit'];
            } else {
              $sql .= " LIMIT " . $xmldata['limit'];
            }
          } else {
            $sql .= " LIMIT " . $xmldata['limit'];
          }
        }
      }
    }

    $ergebnis = $this->app->DB->SelectArr($sql);
    $anz_gesamt = $this->app->DB->Select("SELECT FOUND_ROWS()");
    if(!is_array($ergebnis)){
      $this->XMLResponse(1,$sql);
    }

    $output = "";

    if(count($ergebnis)>0)
    {
      $output .= "<items>";

      $cergebnis = count($ergebnis);
      for($i=0;$i<$cergebnis;$i++)
      {
        $output .= "<item>";

        foreach($ergebnis[$i] as $key=>$value){
          if($this->usecdata) {
            $output .= "<$key><![CDATA[" . htmlspecialchars($value) . "]]></$key>";
          }else{
            $output .= "<$key>" . htmlspecialchars($value) . "</$key>";
          }
        }
        $output .= "</item>";
      }
      $output .= "<anz_gesamt>".$anz_gesamt."</anz_gesamt>";
      $output .= "<anz_result>".count($ergebnis)."</anz_result>";
      $output .= "<sql>".$sql."</sql>";
      $output .= "</items>";
    }

    if($output != ""){
      if ($intern != false) {
        return $output;
      }
      $this->XMLResponse(1, $output);
      $this->app->ExitXentral();
    }
    if ($intern != false) {
      return false;
    }
    $this->XMLResponse(4);
    $this->app->ExitXentral();
  }

  public function ApiArtikelStueckliste()
  {
    $xmldata = $this->XMLPost();

    $keys = array_keys($xmldata);

    //$this->app->erp->LogFile(print_r($xmldata, 1));

    $mineininserterfolgreich = false;

    if(array_key_exists('stuecklistevonartikel', $xmldata)){
      $artikelvorhanden = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '".$xmldata['stuecklistevonartikel']."' LIMIT 1");
      if($artikelvorhanden != ""){
        $altestueckliste = $this->app->DB->SelectArr("SELECT id FROM stueckliste WHERE stuecklistevonartikel = '$artikelvorhanden'");
        if(array_key_exists('items', $xmldata)){
          if(is_array($xmldata['items'])){
            if($xmldata['items']['item'][1] != ""){
              $stuecklistearray = $xmldata['items']['item'];
            }else{
              $stuecklistearray = $xmldata['items'];
            }
            foreach($stuecklistearray as $key=>$value){
              if(array_key_exists('stuecklistemenge', $value)){
                $stuecklistemenge = str_replace(',', '.', $value['stuecklistemenge']);
                if($stuecklistemenge > 0){
                  if(array_key_exists('nummer', $value)){
                    $nummervorhanden = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '".$value['nummer']."' LIMIT 1");
                    if($nummervorhanden != ""){
                      //hier optional
                      if(array_key_exists('stuecklisteart', $value) && !is_array($value['stuecklisteart'])){//standard et
                        $stuecklisteart = $value['stuecklisteart'];
                        if($stuecklisteart != "et" && $stuecklisteart != "it" && $stuecklisteart != "bt"){
                          $stuecklisteart = "et";
                        }else{
                          $stuecklisteart = $value['stuecklisteart'];
                        }
                        if($stuecklisteart == ""){
                          $stuecklisteart = "et";
                        }
                      }else{
                        $stuecklisteart = "et";
                      }
                      if(array_key_exists('stuecklistealternative', $value) && !is_array($value['stuecklistealternative'])){
                        $stuecklistealternative = $value['stuecklistealternative'];
                        if($stuecklistealternative == ""){
                          $stuecklistealternative = "0";
                        }else{
                          $alternativevorhanden = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '$stuecklistealternative' LIMIT 1");
                          if($alternativevorhanden != ""){
                            $stuecklistealternative = $alternativevorhanden;
                          }else{
                            $stuecklistealternative = 0;
                          }
                        }
                      }else{
                        $stuecklistealternative = "0";
                      }
                      if(array_key_exists('stuecklistereferenz', $value) && !is_array($value['stuecklistereferenz'])){
                        $stuecklistereferenz = $value['stuecklistereferenz'];
                        if($stuecklistereferenz == ""){
                          $stuecklistereferenz = "";
                        }
                      }else{
                        $stuecklistereferenz = "";
                      }
                      if(array_key_exists('stuecklistelayer', $value) && !is_array($value['stuecklistelayer'])){//standard Top
                        if($value['stuecklistelayer'] != "Top" && $value['$stuecklistelayer'] != "Bottom"){
                          $stuecklistelayer = "Top";
                        }else{
                          $stuecklistelayer = $value['stuecklistelayer'];
                        }
                        if($stuecklistelayer == ""){
                          $stuecklistelayer = "Top";
                        }
                      }else{
                        $stuecklistelayer = "Top";
                      }
                      if(array_key_exists('stuecklisteplatzierung', $value) && !is_array($value['stuecklisteplatzierung'])){//standard DP
                        if($value['stuecklisteplatzierung'] != "DP" && $value['$stuecklisteplatzierung'] != "DNP"){
                          $stuecklisteplatzierung = "DP";
                        }else{
                          $stuecklisteplatzierung = $value['stuecklisteplatzierung'];
                        }
                        if($stuecklisteplatzierung == ""){
                          $stuecklisteplatzierung = "DP";
                        }
                      }else{
                        $stuecklisteplatzierung = "DP";
                      }
                      if(array_key_exists('stuecklistewert', $value) && !is_array($value['stuecklistewert'])){
                        $stuecklistewert = $value['stuecklistewert'];
                        if($stuecklistewert == ""){
                          $stuecklistewert = "";
                        }
                      }else{
                        $stuecklistewert = "";
                      }
                      if(array_key_exists('stuecklistebauform', $value) && !is_array($value['stuecklistebauform'])){
                        $stuecklistebauform = $value['stuecklistebauform'];
                        if($stuecklistebauform == ""){
                          $stuecklistebauform = "";
                        }
                      }else{
                        $stuecklistebauform = "";
                      }
                      if(array_key_exists('stuecklistezachse', $value) && !is_array($value['stuecklistezachse'])){
                        $stuecklistezachse = $value['stuecklistezachse'];
                        if($stuecklistezachse == ""){
                          $stuecklistezachse = "";
                        }
                      }else{
                        $stuecklistezachse = "";
                      }
                      if(array_key_exists('stuecklistexposition', $value) && !is_array($value['stuecklistexposition'])){
                        $stuecklistexposition = $value['stuecklistexposition'];
                        if($stuecklistexposition == ""){
                          $stuecklistexposition = "";
                        }
                      }else{
                        $stuecklistexposition = "";
                      }
                      if(array_key_exists('stuecklisteyposition', $value) && !is_array($value['stuecklisteyposition'])){
                        $stuecklisteyposition = $value['stuecklisteyposition'];
                        if($stuecklisteyposition == ""){
                          $stuecklisteyposition = "";
                        }
                      }else{
                        $stuecklisteyposition = "";
                      }

                      $this->app->DB->Insert("INSERT INTO stueckliste (artikel, referenz, place, layer, stuecklistevonartikel, 
                        menge, firma, wert, bauform, alternative, zachse, xpos, ypos, art) 
                              VALUES ('$nummervorhanden', '$stuecklistereferenz', '$stuecklisteplatzierung', '$stuecklistelayer', '$artikelvorhanden', 
                              '$stuecklistemenge', 1, '$stuecklistewert', '$stuecklistebauform', '$stuecklistealternative', '$stuecklistezachse', '$stuecklistexposition', '$stuecklisteyposition', '$stuecklisteart')");

                      if(!$this->app->DB->error()){
                        $this->app->DB->Update("UPDATE artikel SET stueckliste = 1 WHERE id = '$artikelvorhanden'");
                        $mineininserterfolgreich = true;
                      }

                    }else{
                      $this->XMLResponse(4);
                    }
                  }
                }else{
                  $this->XMLResponse(4);
                }
              }
            }

            if($mineininserterfolgreich == true){
              foreach($altestueckliste as $key2=>$value2){
                $this->app->DB->Delete("DELETE FROM stueckliste WHERE id = '".$value2['id']."'");
              }
              $this->XMLResponse(1);
              $this->app->ExitXentral();
            }
          }
        }
      }else{
        $this->XMLResponse(4);
      }
    }else{
      $this->XMLResponse(4);
    }
  }


  public function ApiAdresseAccountsGet()
  {
    $xmldata = $this->XMLPost();
    $id = $this->app->Secure->GetGET("id");
    $art = $this->app->Secure->GetGET("art");
    if($xmldata['id']!=''){
      $id = $xmldata['id'];
    }
    if($xmldata['art']!=''){
      $art = $xmldata['art'];} else{$art = '%%';
    }
    $filter['art']=$art;
    $filter['id']=$id;
    if($id > 0 ) {
      $this->XMLResponse(1, $this->app->erp->XMLAdresseAccounts($filter, $this->usecdata));
    } else {
      $this->XMLResponse(5);
    }
    $this->app->ExitXentral();
  }

  public function ApiAdresseAccountCreate()
  {
    $xmldata = $this->XMLPost();

    if($xmldata['adresse'] == '' || is_array($xmldata['adresse']) || $xmldata['bezeichnung'] == '' || is_array($xmldata['bezeichnung'])){
      $this->XMLResponse(5, "<error>Adress-ID und/oder Bezeichnung fehlt</error>");
      $this->app->ExitXentral();
    }

    $newId = $this->app->erp->CreateAccount($xmldata['adresse'],$xmldata);
    $this->XMLResponse(1,"<id>$newId</id>");
    $this->app->ExitXentral();
  }

  public function ApiAdresseAccountEdit(){

    $id = $this->app->Secure->GetGET("id");
    if($id==''){
      $xmldata = $this->XMLPost();
      $id = $xmldata['id'];
    }
    $id = $this->app->DB->Select("SELECT id FROM adresse_accounts WHERE id='$id' LIMIT 1");

    // Key gibt es nicht
    if($id <= 0){
      $this->XMLResponse(5);
    }

    $xmldata = $this->XMLPost();

    foreach($xmldata as $key=>$value)
    {
      if(is_array($value))
      {
        $value='';
      }
      if($key!=="id"){
        if($key === "gueltig_ab" || $key === "gueltig_bis"){
          if($value == "" || $value == "0000-00-00" || $value === "00.00.0000"){
            $value = '0000-00-00';
          }else{
            $value = date('Y-m-d',strtotime($value));
          }
        }
        $this->app->DB->Update("UPDATE adresse_accounts SET $key='$value' WHERE id='$id' LIMIT 1");
      }
    }

    $this->XMLResponse(1);
    $this->app->ExitXentral();
  }

  public function ApiArtikelkontingenteGet()
  {
    $xmldata = $this->XMLPost();
    if(is_array($xmldata) && !empty($xmldata)){
      $filter = $xmldata;
    }else{
      $filter['id'] = $this->app->Secure->GetGET("id");
      $filter['projekt'] = $this->app->Secure->GetGET("projekt");
      $filter['nummer'] = $this->app->Secure->GetGET("nummer");
      $filter['von'] = $this->app->Secure->GetGET("von");
      $filter['bis'] = $this->app->Secure->GetGET("bis");
    }

    if($filter['nummer']!="" || $filter['id']!=""){
      $this->XMLResponse(1, $this->app->erp->XMLArtikelkontingente($filter, $this->usecdata));
    }
    else{
      $this->XMLResponse(5);
    }
    $this->app->ExitXentral();
  }

  protected function getApiAccount(){
    if(!$this->apiAccount){
      $this->apiAccount = $this->apiAccountService->getApiAccountById($this->api_id);
    }

    return $this->apiAccount;
  }

    public function EventAPIAdd($eventname,$parameter,$module,$action,$kommentar='', $api = 0)
    {
      $module_call_from = isset($this->app->Secure)?$this->app->Secure->GetGET('module'):'';

      try {
        $apiaktiv = $this->getApiAccount()->isActive();
      } catch (ApiAccountNotFoundException $e){
        $apiaktiv = false;
      }


      if($module_call_from !=='api')
      {
        $uebertragungen = $this->app->DB->SelectArr("select a.id FROM api_account a INNER JOIN uebertragungen_account u ON a.id = u.api AND a.aktiv=1 AND u.aktiv = 1 INNER JOIN uebertragungen_event_einstellungen e ON u.id = e.uebertragung_account AND e.aktiv = 1 WHERE e.eventname = '$eventname' group by a.id");

        if($uebertragungen)
        {
          foreach($uebertragungen as  $uebertragung)
          {
            $aacount = $uebertragung['id'];
            if($aacount){
              $this->app->DB->Insert("INSERT INTO event_api (id,cachetime,eventname,parameter,retries,module,action,kommentar,api)
          VALUES ('',NOW(),'$eventname','$parameter','0','$module','$action','$kommentar','$aacount')");
              $tmpid =  $this->app->DB->GetInsertID();
              $this->EventCall($tmpid);
            }
          }
        }
      }

      if($apiaktiv === true && $module_call_from !== 'api')
      {
        $this->app->DB->Insert("INSERT INTO event_api (cachetime,eventname,parameter,retries,module,action,kommentar,api)
        VALUES (NOW(),'$eventname','$parameter','0','$module','$action','$kommentar','$api')");
        $tmpid =  $this->app->DB->GetInsertID();
        $this->EventCall($tmpid);
      } else {
        return false;
      }

      return null;
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function EventCall($id)
    {
      $eventApiArr = $id <= 0?null:$this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `event_api` WHERE `id` = %d LIMIT 1',
          $id
        )
      );

      if(empty($eventApiArr)) {
        return false;
      }

      $eventname = $eventApiArr['eventname'];
      $parameter = $eventApiArr['parameter'];
      $module = $eventApiArr['module'];
      $action = $eventApiArr['action'];
      $api = $eventApiArr['api'];

      switch($module)
      {
        case "adresse":
          $xml = $this->app->erp->XMLAdresse($parameter);
          break;

        case "artikel":
          $xml = $this->app->erp->XMLArtikel($parameter, $api);
          break;

        case "auftrag":
          $xml = $this->app->erp->XMLAuftrag($parameter);
          break;

        case "gruppe":
          $xml = $this->app->erp->XMLGruppe($parameter);
          break;

        case "adresse_kontakt":
          $xml = $this->app->erp->XMLAdresseKontakt($parameter);
          break;

        default:
          $this->app->DB->Delete("DELETE FROM event_api WHERE id='$id' LIMIT 1");
          return false;
      }

      $hash = $this->generateHashFromApi($api);
      //$result = $this->EventSendRequest($eventname,$xml,$hash,"&id=".$parameter);
      //Hack
      $result_body = null;
      $result = $this->EventSendRequest($eventname,$xml,$hash,"&id=".$parameter,$result_body, $api);
      switch($eventname) {
        case "EventArtikelCreate":
          if($api && $parameter)
          {
            if(isset($result_body['xml']['id']) && $result_body['xml']['id']!="")
            {
              $this->app->DB->Insert("INSERT INTO api_mapping (tabelle, id_int, id_ext, api, zeitstempel) VALUES ('artikel','$parameter','".$this->app->DB->real_escape_string($result_body['xml']['id'])."','$api',now())");
            }
          }
          break;
        case "EventAuftragEdit":
          //print_r($result_body);
          if(isset($result_body['xml']['belegnr']) && $result_body['xml']['belegnr']!="")
          {
            $this->app->DB->Update("UPDATE auftrag SET belegnr='".$result_body['xml']['belegnr']."' WHERE id='".$parameter."' AND id > 0 LIMIT 1");
            $this->app->DB->Delete("DELETE FROM event_api WHERE id='$id' LIMIT 1");
          }
          break;
        default:
          $result = true;
      }

      if($result===false) {
        $this->app->DB->Update("UPDATE event_api SET retries=retries+1 WHERE id='$id' LIMIT 1");
        return false;
      }
      $this->app->DB->Delete("DELETE FROM event_api WHERE id='$id' LIMIT 1");

      return true;
    }

    public function EventSendRequest($methodname,$xml,$hash,$parameter='',&$result_body='', $api = 0)
    {
      try {
        $url = $this->getApiAccount()->getEventUrl();
      } catch (ApiAccountNotFoundException $e){
        return false;
      }

      if($url == ''){
        return false;
      }

      $xml ='<?xml version="1.0" encoding="UTF-8"?>
      <request>
      <status>
      <function>'.$methodname.'</function>
      </status>
      <xml>'.$xml.'</xml>
      </request>';

      if(strpos($url,'?') === false){
        $url = $url.'?hash='.$hash.$parameter;
      } else{
        $url = $url.'&hash='.$hash.$parameter;
      }
      if(strpos($url,'module=api') === false){
        $url .= '&module=api';
      }
      $url .= '&action='.substr($methodname,5);
      $data = array('xml' => $xml);

      // use key 'http' even if you send the request to https://...
      $options = array(
        'http' => array(
          'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
          'method'  => 'POST',
          'content' => http_build_query($data),
        ),
      );
      $context  = stream_context_create($options);
      $result = file_get_contents($url, false, $context);

      if($result===false) {
        return false;
      }

      $deXml = simplexml_load_string($result);
      $deJson = json_encode($deXml);
      $xml_array = json_decode($deJson,TRUE);

      $result_body = $xml_array;
      if(isset($xml_array['xml']['status']['messageCode'])){
        $xml_array['xml']['status']['messageCode'] = strtolower($xml_array['xml']['status']['messageCode']);
        return $xml_array['xml']['status']['messageCode'] == 0;
      }

      return false;
    }

    /**
     * @param string $initKey
     * @param string $remoteDomain
     *
     * @return string
     */
    public function generateHashFromDomainAndKey($initKey, $remoteDomain)
    {
      $date = gmdate('dmY');

      $hash = '';

      for($i = 0; $i <= 200; $i++) {
        $hash = sha1($hash . $initKey . $remoteDomain . $date);
      }

      return $hash;
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function generateHashFromApi($id = 0)
    {
      try {
        $initKey = $this->getApiAccount()->getInitKey();
        $remoteDomain = $this->getApiAccount()->getRemoteDomain();
      } catch (ApiAccountNotFoundException $e){
        $initKey = '';
        $remoteDomain = '';
      }

      return $this->generateHashFromDomainAndKey($initKey, $remoteDomain);
    }

    public function GetPlainText($string)
    {
      $string = str_replace('NONBLOCKINGZERO','&#65279;',$string);
      return htmlspecialchars(trim(html_entity_decode($string, ENT_QUOTES, 'UTF-8')));
    }


    public function XMLGruppe($id, $cdata = false)
    {
      if($id > 0){
        $arr = $this->app->DB->SelectRow(sprintf('SELECT * FROM gruppen WHERE id=%d LIMIT 1',$id));
      }
      if(empty($arr)){
        return '';
      }
      $result = '';
      foreach($arr as $key=>$value) {
        if(is_array($value)){
          $value='';
        }
        if($cdata && $value != '' && !is_numeric($value)) {
          $result .= "<" . $key . "><![CDATA[" . ($value) . "]]></" . $key . ">";
        }else{
          $result .= "<" . $key . ">" . $this->GetPlainText($value) . "</" . $key . ">";
        }
      }
      return $result;
    }

    public function XMLAdresseKontakt($id, $cdata = false)
    {
      if($id > 0){
        $address_arr = $this->app->DB->SelectRow(sprintf('SELECT * FROM adresse_kontakte WHERE id=%d LIMIT 1', $id));
      }
      if(empty($address_arr)){
        return '';
      }
      $result = '';
      foreach($address_arr as $key=>$value)
      {
        if(is_array($value)){
          $value='';
        }
        if($cdata && $value != '' && !is_numeric($value)) {
          $result .= "<" . $key . "><![CDATA[" . ($value) . "]]></" . $key . ">";
        }else{
          $result .= "<" . $key . ">" . $this->GetPlainText($value) . "</" . $key . ">";
        }
      }
      return $result;
    }


    public function XMLAdresse($id, $cdata = false)
    {
      if($id > 0){
        $address_arr = $this->app->DB->SelectRow(sprintf('SELECT * FROM adresse WHERE id=%d LIMIT 1',$id));
      }
      if(empty($address_arr)){
        return '';
      }
      $result = '';
      foreach($address_arr as $key=>$value) {
        if(is_array($value)){
          $value='';
        }
        if($cdata && $value != '' && !is_numeric($value)) {
          $result .= "<" . $key . "><![CDATA[" . ($value) . "]]></" . $key . ">";
        }else{
          $result .= "<" . $key . ">" . $this->GetPlainText($value) . "</" . $key . ">";
        }
      }
      return $result;
    }

    public function XMLAuftrag($id, $doctype = 'auftrag', $cdata = false)
    {
      if($id > 0 && !empty($doctype)){
        $order_arr = $this->app->DB->SelectRow(sprintf('SELECT * FROM `%s` WHERE id=%d LIMIT 1',$doctype,$id));
      }
      if(empty($order_arr)){
        return '';
      }
      $result = '';

      foreach($order_arr as $key=>$value)
      {
        if(is_array($value)){
          $value='';
        }
        if($cdata && $value != '' && !is_numeric($value)) {
          $result .= "<" . $key . "><![CDATA[" . ($value) . "]]></" . $key . ">";
        }else{
          $result .= "<" . $key . ">" . $this->GetPlainText($value) . "</" . $key . ">";
        }
      }

      if (in_array($doctype, array('gutschrift', 'rechnung'))) {
        $sqlExplodiertParent = 'AND ap.explodiert_parent_artikel<=0';
      }
      else if ($doctype==="bestellung")
      {
        $sqlExplodiertParent ='';
      }
      else {
        $sqlExplodiertParent = 'AND ap.explodiert_parent<=0';
      }
      $order_arr = $this->app->DB->SelectArr("SELECT ap.*, art.ean FROM $doctype"."_position ap LEFT JOIN artikel art ON ap.artikel = art.id WHERE ap.$doctype='$id' {$sqlExplodiertParent} ORDER by ap.sort");

      $result .='<artikelliste>';
      $corder_arr = !empty($order_arr)?count($order_arr):0;
      for($i=0;$i<$corder_arr;$i++)
      {
        $result .='<position>';
        foreach($order_arr[$i] as $key=>$value)
        {
          if(is_array($value)){
            $value='';
          }
          if($cdata && $value != '' && !is_numeric($value)) {
            $result .= "<" . $key . "><![CDATA[" . ($value) . "]]></" . $key . ">";
          }else{
            $result .= "<" . $key . ">" . $this->GetPlainText($value) . "</" . $key . ">";
          }

        }
        $result .='</position>';
      }
      $result .='</artikelliste>';
      return $result;
    }

    public function XMLBenutzer($id, $cdata = false)
    {
      if($id > 0){
        $address_arr = $this->app->DB->SelectRow(sprintf('SELECT * FROM user WHERE id=%d LIMIT 1',$id));
      }
      if(empty($address_arr))
      {
        return '';
      }
      $result = '';
      foreach($address_arr as $key=>$value)
      {
        if(is_array($value))$value='';
        if($key==='startseite')
          $result .='<'.$key.'>'.$this->app->erp->base64_url_encode($value).'</'.$key.'>';
        else {
          if($cdata && $value != '' && !is_numeric($value)) {
            $result .= '<' . $key . '><![CDATA[' . ($value) . ']]></' . $key . '>';
          }else {
            $result .= '<' . $key . '>' . $this->GetPlainText($value) . '</' . $key . '>';
          }
        }
      }
      return $result;
    }

    public function XMLAdresseAccounts($filter=array(), $cdata = false)
    {
      if(!empty($filter['id'])){
        $sql = "SELECT * FROM adresse_accounts WHERE adresse='" . $filter['id'] . "' AND art LIKE '" . $filter['art'] . "' AND aktiv=1";
        $result = $this->app->DB->SelectArr($sql);
      }
      if(empty($result))
      {
        return '';
      }
      $output ='<adresse_accounts>';
      foreach($result as $account)
      {
        $output .='<account>';
        foreach($account as $key=>$value)
        {
          if($cdata && $value != '' && !is_numeric($value))
          {
            $output .= sprintf('<%1$s><![CDATA[%2$s]]></%1$s>', $key, $value);
          }else
            $output .= sprintf('<%1$s>%2$s</%1$s>', $key, $value);
        }
        $output .='</account>';
      }
      $output .='</adresse_accounts>';
      return $output;
    }

    public function XMLBerichte($id, $cdata = false){
      if($id > 0){
        $result = $this->app->DB->SelectRow(sprintf('SELECT * FROM berichte WHERE id = %d LIMIT 1', $id));
      }
      if(empty($result) || empty($result['struktur']))
      {
        return '';
      }

      $rows = $this->app->DB->SelectArr($result['struktur']);
      $output ='<items>';
      if(!empty($rows)) {
        foreach($rows as $row) {
          $output .='<item>';
          foreach($row as $key=>$value)
          {
            if($cdata && $value != '' && !is_numeric($value)) {
              $output .= "<" . $key . "><![CDATA[" . ($value) . "]]></" . $key . ">";
            }else{
              $output .= "<" . $key . ">" . $this->GetPlainText($value) . "</" . $key . ">";
            }
          }
          $output .='</item>';
        }
      }
      $output .='</items>';
      return $output;
    }

    public function XMLArtikelkontingente($filter=array(), $cdata = false)
    {
      if($filter['id']=='')
      {
        if($filter['projekt']!='')
        {
          $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$filter['projekt']."' AND abkuerzung!=''");
          if($projekt > 0){
            $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='" . $filter['nummer'] . "' AND projekt = $projekt AND nummer!='' LIMIT 1");
          }else{
            $artikel = 0;
          }
        } else {
          $artikel = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$filter['nummer']."' AND nummer!='' LIMIT 1");
        }
      } else {
        $artikel = $filter['id'];
      }
      if($artikel > 0){
        $artikel = $this->app->DB->Select(sprintf('SELECT id FROM artikel WHERE id = %d LIMIT 1', $artikel));
      }
      if($artikel <= 0)
      {
        return '';
      }

      if($filter['von']!='')
      {
        $fall = 1;
        $result = $this->app->DB->SelectArr("SELECT SUM(ap.menge) as gebucht,
        if(ak.menge IS NULL,(SELECT ak2.menge FROM artikelkontingente ak2 WHERE (ak2.datum='1970-01-01' OR ak2.datum='0000-00-00' OR ak2.datum IS NULL) AND ak2.artikel=ap.artikel),ak.menge)
            as menge,
        if(a.lieferdatum ='0000-00-00' OR a.lieferdatum is NULL, a.datum, a.lieferdatum) as datum
        FROM auftrag_position ap
          LEFT JOIN auftrag a ON a.id=ap.auftrag
          LEFT JOIN artikelkontingente ak ON ak.artikel=ap.artikel AND if(a.lieferdatum ='0000-00-00' OR a.lieferdatum is NULL, a.datum, a.lieferdatum)=ak.datum
          WHERE
            if(a.lieferdatum ='0000-00-00' OR a.lieferdatum is NULL,
              (a.datum >='".$filter['von']."' AND a.datum <='".$filter['bis']."'),(a.lieferdatum >='".$filter['von']."' AND a.lieferdatum <='".$filter['bis']."'))
            AND a.status='freigegeben'
          AND ap.artikel='$artikel' GROUP by 3 ORDER by 3");
      }
      else {
        $fall = 2;
        $result = $this->app->DB->SelectArr("SELECT * FROM artikelkontingente ak WHERE ak.artikel='$artikel' AND (datum >= DATE_FORMAT(NOW(),'%Y-%m-%d') OR datum='0000-00-00' OR datum='1970-01-01')");
      }

      if(empty($result))
      {
        return '';
      }

      $output = '<artikelkontingente>';
      foreach($result as $artikelkontigent) {
        $output .='<kontingent>';
        foreach($artikelkontigent as $key=>$value)
        {
          if($key==='datum') {
            $value=str_replace('1970-01-01','0000-00-00',$value);
          }

          if($key!=='id' && $key!=='artikel')
          {
            if($cdata && $value != '' && !is_numeric($value)) {
              $output .= '<' . $key . '><![CDATA[' . ($value) . ']]></' . $key . '>';
            }else{
              $output .= '<' . $key . '>' . $this->GetPlainText($value) . '</' . $key . '>';
            }
          }
          if($key==='datum' && $fall==2)
          {
            $gebucht = $this->app->DB->Select(sprintf('SELECT SUM(ap.menge) 
              FROM auftrag_position AS ap 
              INNER JOIN auftrag a ON a.id=ap.auftrag
              WHERE a.lieferdatum=\'%s\' AND a.status=\'freigegeben\' AND ap.artikel= %d ',$value, $artikel));
            if($gebucht <=0) {
              $gebucht = 0;
            }
            $output .='<gebucht>'.$gebucht.'</gebucht>';
          }
        }
        $output .='</kontingent>';
      }
      $output .='</artikelkontingente>';

      return $output;
    }

    public function XMLExportVorlage($id,$filter=array(), $cdata = false)
    {
      /** @var Exportvorlage $tmp */
      $tmp = $this->app->erp->LoadModul('exportvorlage');
      $csv = $tmp->ExportvorlageExport(true,$id,$filter);

      $csv_array = preg_split ('/$\R?^/m', $csv);

      if($id > 0){
        $vorlageArr = $this->app->DB->SelectRow(sprintf('SELECT exporttrennzeichen, exporterstezeilenummer,exportdatenmaskierung FROM exportvorlage WHERE id= %d  LIMIT 1', $id));
      }
      if(!empty($vorlageArr))
      {
        $exporttrennzeichen = $vorlageArr['exporttrennzeichen'];
        $exportdatenmaskierung = $vorlageArr['exportdatenmaskierung'];
        $exporterstezeilenummer = $vorlageArr['exporterstezeilenummer'];
      }else{
        $exporttrennzeichen = '';
        $exportdatenmaskierung = '';
        $exporterstezeilenummer = '';
      }

      if($exporttrennzeichen==='semikolon') {
        $exporttrennzeichen=';';
      }
      else if($exporttrennzeichen==='komma') {
        $exporttrennzeichen=',';
      }
      else if($exporttrennzeichen==='tab') {
        $exporttrennzeichen="\t";
      }
      else {
        $exporttrennzeichen=';';
      }

      if($exportdatenmaskierung==='gaensefuesschen') {
        $exportdatenmaskierung='"';
      } else {
        $exportdatenmaskierung='';
      }

      $output = '';
      $field_name = [];
      $counter = 0;
      if(!empty($csv_array)){
        foreach ($csv_array as $row) {
          $fields = explode($exporttrennzeichen, $row);
          if($counter == 0){
            foreach ($fields as $value) {
              if($exportdatenmaskierung != ''){
                $value = str_replace($exportdatenmaskierung, '', $value);
              }
              $field_name[] = $value;
            }
          }else{
            $output .= '<item>';
            foreach($field_name as $field_index => $fieldValue) {
              $value = str_replace('"', '', $fields[$field_index]);
              if($cdata && $value != '' && !is_numeric($value)){
                $output .= '<' . $field_name[$field_index] . '><![CDATA[' . $value . ']]></' . $field_name[$field_index] . ">\r\n";
              }else{
                $output .= '<' . $field_name[$field_index] . '>' . $value . '</' . $field_name[$field_index] . ">\r\n";
              }
            }
            $output .= '</item>';
          }
          $counter++;
        }
      }
      return '<items>'.$output.'</items>';
    }

    public function XMLArtikel($id, $api = 0)
    {
      if($id > 0){
        $artikel_arr = $this->app->DB->SelectRow(sprintf('SELECT * FROM artikel WHERE id=%d LIMIT 1',$id));
      }
      $result = '';
      if(!empty($artikel_arr)){
        foreach ($artikel_arr as $key => $value) {
          if($key !== 'usereditid' && $key !== 'useredittimestamp'){
            if(is_array($value)){
              $value = '';
            }
            if($key === 'id' && $api){
              $extid = $this->app->DB->Select("SELECT id_ext FROM api_mapping WHERE tabelle = 'artikel' AND id_int = '$id' AND api = '$api' LIMIT 1");
              if($extid){
                $result .= '<id_ext>' . $this->GetPlainText($extid) . '</id_ext>';
              }
            }
            if($key === 'projekt') $value = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$value' LIMIT 1");
            $result .= '<' . $key . '>' . $this->GetPlainText($value) . "</" . $key . ">";
            if($key === 'typ'){
              if(strpos($value, '_kat') !== false){
                $kategorie = (int)str_replace('_kat', '', $value);
                $bezeichnung = $this->app->DB->Select("SELECT bezeichnung FROM artikelkategorien WHERE id = '" . $kategorie . "' LIMIT 1");
                if($bezeichnung){
                  $result .= '<artikelkategorie>' . $this->GetPlainText($bezeichnung) . '</artikelkategorie>';
                  $extid = $this->app->DB->Select("SELECT id_ext FROM api_mapping WHERE tabelle = 'artikelkategorien' AND id_int = '$kategorie' AND api = '$api' LIMIT 1");
                  if($extid){
                    $result .= '<typ_ext>' . $this->GetPlainText($extid) . '</typ_ext>';
                  }
                }
              }
            }

          }
        }
      }
      // Lagerbestand
      $summe = $this->app->DB->Select("SELECT SUM(lpi.menge) FROM lager_platz_inhalt lpi LEFT JOIN lager_platz lp ON lp.id=lpi.lager_platz WHERE lpi.artikel='$id' AND lp.sperrlager!=1");
      $reserviert = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='$id'");// AND datum >= NOW()");
      $auftraege = $this->app->DB->Select("SELECT SUM(ap.menge) as menge,ap.bezeichnung FROM auftrag_position ap LEFT JOIN artikel a ON a.id=ap.artikel LEFT JOIN auftrag auf ON auf.id=ap.auftrag WHERE a.id='$id' AND a.lagerartikel=1 AND auf.status='freigegeben'");
      $liefern= $this->app->DB->Select("SELECT SUM(ap.menge) as menge,ap.bezeichnung FROM auftrag_position ap, auftrag aa, artikel a WHERE a.id=ap.artikel AND aa.id = ap.auftrag AND a.id='$id' AND a.lagerartikel=1 AND aa.status='freigegeben'");
      $reserviert_im_versand = $this->app->DB->Select("SELECT SUM(menge) FROM lager_reserviert WHERE artikel='$id' AND objekt='lieferschein'");
      $berechnet = $summe - $auftraege - $reserviert_im_versand;
      $verkaufte = $auftraege + $reserviert_im_versand;

      $rest = $summe - $liefern;
      if($reserviert=='') {
        $reserviert =0;
      }
      if($liefern <=0) {
        $liefern=0;
      }

      if($rest > 0) {
        $verfuegbar = (string)$rest;
      } else {
        $verfuegbar = '0';
      }

      $verkaufbare = $this->app->erp->ArtikelAnzahlVerkaufbar($id);
      $summe = round($summe, $this->app->erp->GetLagerNachkommastellen());
      $reserviert = round($reserviert ,$this->app->erp->GetLagerNachkommastellen());
      $liefern = round($liefern ,$this->app->erp->GetLagerNachkommastellen());
      $verkaufte = round($verkaufte ,$this->app->erp->GetLagerNachkommastellen());
      $berechnet = round($berechnet ,$this->app->erp->GetLagerNachkommastellen());
      $verkaufbare = round($verkaufbare ,$this->app->erp->GetLagerNachkommastellen());

      $result .= "<artikel_lagerbestand>$summe</artikel_lagerbestand>";
      $result .= "<artikel_reserviert>$reserviert</artikel_reserviert>";
      $result .= "<artikel_offeneauftraege>$liefern</artikel_offeneauftraege>";
      $result .= "<artikel_verkaufte>$verkaufte</artikel_verkaufte>";
      $result .= "<artikel_berechneterbestand>$berechnet</artikel_berechneterbestand>";
      $result .= "<artikel_verkaufbare>$verkaufbare</artikel_verkaufbare>";

      // stueckliste
      if($artikel_arr['stueckliste']=='1'){
        $arr_stueckliste = $this->app->DB->SelectArr(sprintf('SELECT * FROM stueckliste WHERE stuecklistevonartikel=%d',$id));
        if(!empty($arr_stueckliste)){
          $result .= '<stueckliste_artikel>';
          foreach($arr_stueckliste as $stuecklisteKey => $stuecklisteRow) {
            $artikelArr = $this->app->DB->SelectRow(sprintf('SELECT nummer, projekt 
            FROM artikel WHERE id = %d LIMIT 1', $stuecklisteRow['artikel']));
            if(!empty($artikelArr))
            {
              $arr_stueckliste[$stuecklisteKey]['nummer'] = $artikelArr['nummer'];
              $projekt = $artikelArr['projekt'];
              if($projekt > 0)
              {
                $arr_stueckliste[$stuecklisteKey]['projekt'] = $this->app->DB->Select(
                  sprintf('SELECT abkuerzung 
                    FROM projekt 
                    WHERE id = %d LIMIT 1', $projekt));
              }else{
                $arr_stueckliste[$stuecklisteKey]['projekt'] = '';
              }
            }else{
              $arr_stueckliste[$stuecklisteKey]['nummer'] = '';
              $arr_stueckliste[$stuecklisteKey]['projekt'] = '';
            }
            $result .= '<artikel>';
            $result .= '<menge>' . $arr_stueckliste[$stuecklisteKey]['menge'] . '</menge>';
            $result .= '<nummer>' . $arr_stueckliste[$stuecklisteKey]['nummer'] . '</nummer>';
            $result .= '<artikel>' . $arr_stueckliste[$stuecklisteKey]['artikel'] . '</artikel>';
            $result .= '<projekt>' . $arr_stueckliste[$stuecklisteKey]['projekt'] . '</projekt>';
            $result .= "</artikel>";
          }
          $result .= '</stueckliste_artikel>';
        }
      }
      // einkaufspreise
      $arr_einkauf = $this->app->DB->SelectArr("SELECT * FROM einkaufspreise WHERE artikel='$id' AND (gueltig_bis >= NOW() OR gueltig_bis='0000-00-00')");
      if(!empty($arr_einkauf))
      {
        $result .='<einkaufspreise>';
        foreach($arr_einkauf as $einkaufKey => $einkaufValue) {
          if($einkaufValue['adresse'] > 0){
            $adresseArr = $this->app->DB->SelectRow(sprintf(
              'SELECT projekt, lieferantennummer FROM adresse WHERE id = %d ', $einkaufValue['adresse']
            ));
          }else{
            $adresseArr = null;
          }
          if(!empty($adresseArr))
          {
            $einkaufValue['lieferantennummer'] = $adresseArr['lieferantennummer'];
            $einkaufValue['projekt'] = $adresseArr['projekt'];
          }else{
            $einkaufValue['lieferantennummer'] = '';
            $einkaufValue['projekt'] = 0;
          }
          if($einkaufValue['projekt'] > 0)
          {
            $einkaufValue['projekt'] = $this->app->DB->SelectRow(sprintf('SELECT projekt FROM adresse WHERE id= %d LIMIT 1', $einkaufValue['projekt']));
          }else{
            $einkaufValue['projekt'] = '';
          }

          $result .='<staffelpreis>';
          $result .='<ab_menge>'.$einkaufValue['ab_menge'].'</ab_menge>';
          $result .='<preis>'.$einkaufValue['preis'].'</preis>';
          $result .='<waehrung>'.$einkaufValue['waehrung'].'</waehrung>';
          $result .='<lieferantennummer>'.$einkaufValue['lieferantennummer'].'</lieferantennummer>';
          $result .='<projekt>'.$einkaufValue['projekt'].'</projekt>';
          $result .='<bestellnummer>'.$this->GetPlainText($einkaufValue['bestellnummer']).'</bestellnummer>';
          $result .='<bezeichnunglieferant>'.$this->GetPlainText($einkaufValue['bezeichnunglieferant']).'</bezeichnunglieferant>';
          $result .='</staffelpreis>';
        }
        $result .='</einkaufspreise>';
      }

      // verkaufspreise
      $arr_verkauf = $this->app->DB->SelectArr("SELECT * FROM verkaufspreise WHERE artikel='$id' AND (gueltig_bis >= NOW() OR gueltig_bis='0000-00-00' ) AND geloescht!='1'");
      if(!empty($arr_verkauf))
      {
        $result .='<verkaufspreise>';
        foreach($arr_verkauf as $verkaufValue) {
          if($einkaufValue['adresse'] > 0){
            $adresseArr = $this->app->DB->SelectRow(sprintf(
              'SELECT projekt, lieferantennummer FROM adresse WHERE id = %d ', $verkaufValue['adresse']
            ));
          }else{
            $adresseArr = null;
          }
          if(!empty($adresseArr))
          {
            $verkaufValue['lieferantennummer'] = $adresseArr['lieferantennummer'];
            $verkaufValue['projekt'] = $adresseArr['projekt'];
          }else{
            $verkaufValue['lieferantennummer'] = '';
            $verkaufValue['projekt'] = 0;
          }
          if($verkaufValue['projekt'] > 0)
          {
            $verkaufValue['projekt'] = $this->app->DB->SelectRow(sprintf('SELECT projekt FROM adresse WHERE id= %d LIMIT 1', $einkaufValue['projekt']));
          }else{
            $verkaufValue['projekt'] = '';
          }
          $arr_verkauf[$i]['kundennummer'] = $this->app->DB->Select("SELECT kundennummer FROM adresse WHERE id='".$arr_verkauf[$i]['adresse']."' LIMIT 1");
          $arr_verkauf[$i]['projekt'] = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='".$arr_verkauf[$i]['adresse']."' LIMIT 1");
          $arr_verkauf[$i]['projekt'] = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id='".$arr_verkauf[$i]['projekt']."' LIMIT 1");

          $result .='<staffelpreis>';
          $result .='<ab_menge>'.$verkaufValue['ab_menge'].'</ab_menge>';
          $result .='<preis>'.$verkaufValue['preis'].'</preis>';
          $result .='<vpe>'.$verkaufValue['vpe'].'</vpe>';
          if($verkaufValue['vpe_menge'] > 0){
            $result .= '<vpe_menge>' . $verkaufValue['vpe_menge'] . '</vpe_menge>';
          }

          $result .='<waehrung>'.$verkaufValue['waehrung'].'</waehrung>';

          if($verkaufValue['kundennummer']!='')
          {
            $result .='<kundennummer>'.$verkaufValue['kundennummer'].'</kundennummer>';
            $result .='<projekt>'.$verkaufValue['projekt'].'</projekt>';
            if($verkaufValue['kundenartikelnummer']!=''){
              $result .= '<kundenartikelnummer>' . $verkaufValue['kundenartikelnummer'] . '</kundenartikelnummer>';
            }
          }
          $result .='</staffelpreis>';
        }
        $result .='</verkaufspreise>';
      }
      return $result;
    }


    public function BelegeimportAusfuehren($idliste = null, $uebertragungen = false)
    {
      $ret = null;
      $erlaubtebelege = array(
        'auftrag',
        'lieferschein',
        'rechnung',
        'gutschrift',
        'angebot',
        'preisanfrage',
        'bestellung',
        'produktion',
        'proformarechnung',
        'retoure'
      );
      $belege = $this->app->DB->SelectArr('SELECT * 
        FROM belegeimport 
        WHERE '.($idliste?' id in ('.implode(', ',$idliste).') ':"userid='".$this->app->User->GetID()."'").' 
        ORDER BY art, beleg_hauptbelegnr, beleg_belegnr,artikel_sort, id');
      if($belege) {
        if($uebertragungen) {
          $transferData = $this->app->DB->SelectRow(
            sprintf(
              'SELECT `api`, `projekt`,`auftrageingang`,`bestellungeingang`, `createproduction`, `ownaddress`, 
                    `createarticleifnotexists`, `createarticleasstoragearticle`
              FROM `uebertragungen_account` 
              WHERE `id` = %d 
              LIMIT 1',
              $uebertragungen
            )
          );
          $this->uebertragung_account = $uebertragungen;
          $this->api_id = $transferData['api'];
          $projekt = $transferData['projekt'];
          $ret = null;
          if(empty($transferData['alldoctypes'])) {
            $erlaubtebelege = array();
            if($transferData['auftrageingang']) {
              $erlaubtebelege[] = 'auftrag';
              $erlaubtebelege[] = 'angebot';
            }
            if($transferData['bestellungeingang']) {
              $erlaubtebelege[] = 'bestellung';
              $erlaubtebelege[] = 'produktion';
            }
          }
        }
        $aktbelegart = false;
        $aktbelegnr = false;
        $aktbelegid = false;
        $hauptbelegnr = false;
        //Für Belegstatus "angelegt", um die Belegnr entfernen zu können
        $erstelltebelegeids = array();
        $erstelltebelegeNichtAngelegtids = [];
        foreach($belege as $beleg) {
          if(!empty($beleg['art'])) {
            $beleg['art'] = trim(strtolower($beleg['art']));
          }
          if($beleg['art'] && $beleg['beleg_belegnr'] && in_array($beleg['art'],$erlaubtebelege)) {
            if($beleg['art'] != $aktbelegart || $beleg['beleg_belegnr'] != $aktbelegnr)
            {
              $projekt = !empty($beleg['projekt'])?$beleg['projekt']:0;
              if(empty($projekt)) {
                $projekt = !empty($beleg['beleg_projekt'])?$beleg['beleg_projekt']:0;
              }
              $aktbelegnr = $beleg['beleg_belegnr'];
              $hauptbelegnr = $beleg['beleg_hauptbelegnr'];
              $aktbelegart = $beleg['art'];
              $aktbelegid = false;
              $alteadresse = $beleg['adresse'];
              if($beleg['adresse'] && $uebertragungen) {
                $beleg['adresse'] = $this->GetFromExtID('adresse', $beleg['adresse']);
              }
              if(!$beleg['adresse'] && $uebertragungen && $beleg['beleg_lieferantennummer'] != '' && ($beleg['art'] === 'bestellung' || $beleg['art'] === 'produktion')) {
                $beleg['adresse'] = $this->app->DB->Select(
                  sprintf(
                    'SELECT id 
                    FROM adresse 
                    WHERE lieferantennummer <> \'\' AND lieferantennummer = \'%s\' AND IFNULL(geloescht,0) = 0
                    ORDER BY projekt = %d DESC 
                    LIMIT 1',
                    $this->app->DB->real_escape_string($beleg['beleg_lieferantennummer']), (int)$beleg['projekt']
                  )
                );
              }

              if($uebertragungen && !empty($beleg['adresse']) && $beleg['art'] === 'bestellung'
                && !empty($transferData['createproduction']) && $beleg['adresse'] == $transferData['ownaddress']
              ) {
                $beleg['art'] = 'produktion';
              }

              if($uebertragungen) {
                $hauptbelegnr = $this->GetFromExtID($beleg['art'], $hauptbelegnr);
                if($hauptbelegnr != '')$hauptbelegnr = $this->app->DB->Select("SELECT belegnr 
                    FROM '".$beleg['art']."' WHERE id = '$hauptbelegnr' LIMIT 1");
                if(!$beleg['beleg_projekt']) {
                  $beleg['beleg_projekt'] = $projekt;
                }
              }

              if($uebertragungen && !$beleg['adresse'] && $beleg['art'] === 'produktion' && !empty($beleg['beleg_auftragid'])){
                $beleg['adresse'] = $this->app->DB->Select(
                  sprintf(
                    'SELECT adresse FROM auftrag WHERE id = %d LIMIT 1', $beleg['beleg_auftragid']
                  )
                );
              }

              if(!$beleg['adresse'] && $uebertragungen) {
                $adresseprojekt = '';
                if($projekt) {
                  $adresseprojekt = " AND projekt = '$projekt' ";
                }
                if(in_array($beleg['art'],
                  array('auftrag','lieferschein','rechnung','gutschrift','angebot','produktion','retoure')
                )) {
                  $beleg['adresse'] = $this->app->DB->Select("SELECT id FROM adresse 
                    WHERE name='".$this->app->DB->real_escape_string($beleg['beleg_name'])."' 
                    AND email='".$this->app->DB->real_escape_string($beleg['beleg_email'])."' 
                          AND strasse='".$this->app->DB->real_escape_string((string)$beleg['beleg_strasse'])."' 
                          AND plz='".$this->app->DB->real_escape_string((string)$beleg['beleg_plz'])."'
                          AND ort='".$this->app->DB->real_escape_string((string)$beleg['beleg_ort'])."' 
                          AND kundennummer <> '' AND geloescht!=1 $adresseprojekt LIMIT 1");
                }
                else {
                  $beleg['adresse'] = $this->app->DB->Select("SELECT id FROM adresse 
                      WHERE name='".
                    $this->app->DB->real_escape_string($beleg['beleg_name'])."' 
                    AND email='".$this->app->DB->real_escape_string($beleg['beleg_email'])."' 
                          AND strasse='".$this->app->DB->real_escape_string((string)$beleg['beleg_strasse'])."' 
                          AND plz='".$this->app->DB->real_escape_string((string)$beleg['beleg_plz'])."' 
                          AND ort='".$this->app->DB->real_escape_string((string)$beleg['beleg_ort'])."' 
                          AND lieferantennummer <> '' AND geloescht!=1 $adresseprojekt LIMIT 1");
                }
                if($beleg['beleg_kundennummer'] == '' && $beleg['adresse']) {
                  $beleg['beleg_kundennummer'] = $this->app->DB->Select("SELECT kundennummer 
                  FROM adresse WHERE id = '".$beleg['adresse']."' LIMIT 1");
                }
              }
              $belegKundennummer = !empty($beleg['beleg_kundennummer'])?$beleg['beleg_kundennummer']:'';
              if(empty($belegKundennummer)) {
                $belegKundennummer = !empty($beleg['kundennummer'])?$beleg['kundennummer']:'';
              }
              if(strtoupper((string)$belegKundennummer) === 'NEU' || strtoupper($belegKundennummer) === 'NEW') {
                $beleg['adresse'] = 0;
              }

              if(!$beleg['adresse']) {
                if(in_array($beleg['art'],array('auftrag','lieferschein','rechnung','gutschrift','angebot', 'produktion'))) {
                  $adr['kundennummer'] = $beleg['beleg_kundennummer'];
                  if((string)$adr['kundennummer'] === '' || strtoupper((string)$adr['kundennummer']) === 'NEU' ||
                    strtoupper($adr['kundennummer']) === 'NEW') {
                    $adr['kundennummer'] = $this->app->erp->GetNextKundennummer($beleg['beleg_projekt']);
                  }
                }else{
                  $adr['lieferantennummer'] = $this->app->erp->GetNextLieferantennummer($beleg['beleg_projekt']);
                }
                if(!$beleg['beleg_projekt'])$beleg['beleg_projekt'] = $this->app->erp->Firmendaten('projekt');
                $adr['name'] = $beleg['beleg_name'];
                $adr['abteilung'] = $beleg['beleg_abteilung'];
                $adr['unterabteilung'] = $beleg['beleg_unterabteilung'];
                $adr['adresszusatz'] = $beleg['beleg_adresszusatz'];
                $adr['ansprechpartner'] = $beleg['beleg_ansprechpartner'];
                $adr['telefon'] = $beleg['beleg_telefon'];
                $adr['adresszusatz'] = $beleg['beleg_adresszusatz'];
                $adr['email'] = $beleg['beleg_email'];
                $adr['land'] = $beleg['beleg_land'];
                if($adr['land']=="") {
                  $adr['land'] = $this->app->erp->Firmendaten("land");
                  $beleg['beleg_land'] = $adr['land'];
                }
                $adr['strasse'] = $beleg['beleg_strasse'];
                $adr['plz'] = $beleg['beleg_plz'];
                $adr['ort'] = $beleg['beleg_ort'];
                $adr['lieferbedingung'] = $beleg['beleg_lieferbedingung'];
                $adr['projekt'] = $beleg['beleg_projekt'];

                $beleg['adresse'] = $this->app->erp->InsertUpdateAdresse($adr);
                if($alteadresse && $alteadresse != $beleg['adresse'])
                {
                  $this->SetExtIDMapping('adresse', $beleg['adresse'], $alteadresse);
                }
                unset($adr);
              }
              if($beleg['adresse'])
              {
                if($uebertragungen)
                {
                  $check = $this->GetFromExtID($beleg['art'], $beleg['beleg_belegnr']);
                }else{
                  $check = $this->app->DB->Select("SELECT id FROM ".$beleg['art']." 
                  WHERE belegnr = '".$this->app->DB->real_escape_string($beleg['beleg_belegnr'])."' LIMIT 1");
                }
                if(!$check)
                {
                  $methode = 'Create'.ucfirst($beleg['art']);
                  $methodeLoad = 'Load'.ucfirst($beleg['art']).'Standardwerte';
                  if(method_exists($this->app->erp, $methode))
                  {
                    $check = $this->app->erp->$methode($beleg['adresse']);
                    if($uebertragungen)
                    {
                      $beleg['id'] = $check;
                      $ret[] = $beleg;
                    }
                    if(method_exists($this->app->erp, $methodeLoad)){
                      $this->app->erp->$methodeLoad($check, $beleg['adresse']);
                    }

                    if($beleg['status'] !== 'angelegt')
                    {
                      if($beleg['beleg_belegnr']==='NEU' || $beleg['beleg_belegnr']==='NEW' || $beleg['beleg_belegnr']=='' ||
                        $beleg['beleg_belegnr']==='ENTWURF' || !isset($beleg['beleg_belegnr']) || $uebertragungen)  {
                        $beleg['beleg_belegnr'] = $this->app->erp->GetNextNummer($beleg['art'],$beleg['beleg_projekt'],$check);
                      }
                    }else {
                      $beleg['beleg_belegnr'] = '';
                    }

                    if($check)
                    {
                      $adressArr = $this->app->DB->SelectRow(
                        sprintf(
                          'SELECT * FROM adresse WHERE id = %d LIMIT 1',
                          $beleg['adrese']
                        )
                      );

                      if(!$beleg['beleg_projekt']) {
                        $beleg['beleg_projekt'] = $this->app->DB->Select("SELECT projekt 
                          FROM adresse 
                          WHERE id = '".$beleg['adresse']."' 
                          LIMIT 1");
                      }

                      if($beleg['beleg_projekt'] &&
                        $beleg['beleg_projekt'] != $this->app->DB->Select("SELECT projekt 
                        FROM ".$beleg['art']." 
                        WHERE id = '$check' LIMIT 1"))
                      {
                        $this->app->DB->Update("UPDATE ".$beleg['art']." SET projekt = '".$beleg['beleg_projekt']."' 
                          WHERE id = '$check' LIMIT 1");
                        if($beleg['art'] === 'auftrag')
                        {
                          $standardlager = $this->app->DB->Select("SELECT standardlager FROM projekt WHERE id = '".$beleg['beleg_projekt']."' LIMIT 1");
                          if($standardlager)$this->app->DB->Update("UPDATE auftrag SET standardlager = '$standardlager' 
                            WHERE id = '$check' LIMIT 1");
                          if($this->app->erp->StandardZahlungsweise($beleg['beleg_projekt'])==="rechnung")
                          {
                            $this->app->DB->Update("UPDATE auftrag 
                              set zahlungsweise = '".$this->app->erp->StandardZahlungsweise($beleg['beleg_projekt'])."',
                              zahlungszieltage = '".$this->app->erp->ZahlungsZielTage($beleg['beleg_projekt'])."', 
                              zahlungszieltageskonto = '".$this->app->erp->ZahlungsZielTageSkonto($beleg['beleg_projekt'])."', 
                              zahlungszielskonto = '".$this->app->erp->ZahlungsZielSkonto($beleg['beleg_projekt'])."' 
                              WHERE id = '$check' LIMIT 1");
                          }else{
                            $this->app->DB->Update("UPDATE auftrag 
                              set zahlungsweise = '".$this->app->erp->StandardZahlungsweise($beleg['beleg_projekt'])."',
                              zahlungszieltage = '0', zahlungszieltageskonto = '0', zahlungszielskonto = '0' 
                              WHERE id = '".$check."' LIMIT 1");
                          }
                        }
                        $this->app->erp->LoadSteuersaetzeWaehrung($check,$beleg['art'],$beleg['beleg_projekt']);
                      }

                      $teillieferungvon = false;
                      if($hauptbelegnr)
                      {
                        $teillieferungvon = $this->app->DB->Select("SELECT id 
                          FROM auftrag 
                          WHERE belegnr = '".$this->app->DB->real_escape_string($hauptbelegnr)."' 
                          LIMIT 1");
                        if($teillieferungvon)
                        {
                          $teillieferungnummer= $this->app->DB->Select("SELECT MAX(teillieferungnummer) FROM auftrag WHERE teillieferungvon='$teillieferungvon'");
                          $teillieferungnummer++;
                          $this->app->DB->Update("UPDATE ".$beleg['art']." 
                            SET teillieferungvon = '".$teillieferungvon."',teillieferungnummer='$teillieferungnummer' 
                            WHERE id = '$check' LIMIT 1");
                        }
                      }

                      $fields_fromdb = array('name','strasse','ort','land','plz');
                      foreach($fields_fromdb as $fieldname)
                      {
                        if($beleg['beleg_'.$fieldname]=='') {
                          $beleg['beleg_'.$fieldname] = $this->app->DB->Select("SELECT ".$fieldname." 
                          FROM adresse 
                          WHERE id='".$beleg['adresse']."' 
                          LIMIT 1");
                        }
                      }

                      $belegArt = $beleg['art'];

                      if($beleg['beleg_status']) {
                        $this->app->DB->Update("UPDATE ".$belegArt." 
                        SET status = '".$this->app->DB->real_escape_string($beleg['beleg_status'])."' WHERE id = '$check' LIMIT 1");
                      }else{
                        $this->app->DB->Update("UPDATE ".$belegArt." 
                        SET status = '".$this->app->DB->real_escape_string($beleg['status'])."' WHERE id = '$check' LIMIT 1");
                      }
                      if($beleg['beleg_datum'])
                      {
                        $this->app->DB->Update("UPDATE ".$belegArt." 
                        SET datum = '".$this->app->DB->real_escape_string($beleg['beleg_datum'])."' WHERE id = '$check' LIMIT 1");
                      }else{
                        $this->app->DB->Update("UPDATE ".$belegArt." 
                        SET datum = now() WHERE id = '$check' AND datum = '0000-00-00' LIMIT 1");
                      }
                      if($beleg['beleg_lieferdatum'])
                      {
                        if($belegArt === 'bestellung') {
                          $this->app->DB->Update("UPDATE bestellung
                          SET gewuenschteslieferdatum = '" . $this->app->DB->real_escape_string($beleg['beleg_lieferdatum']) . "' WHERE id = '$check' LIMIT 1");
                        }else{
                          $this->app->DB->Update("UPDATE " . $belegArt . " 
                          SET lieferdatum = '" . $this->app->DB->real_escape_string($beleg['beleg_lieferdatum']) . "' WHERE id = '$check' LIMIT 1");
                        }
                      }
                      if($beleg['beleg_tatsaechlicheslieferdatum'])
                      {
                        $this->app->DB->Update("UPDATE ".$belegArt." 
                        SET tatsaechlicheslieferdatum = '".$this->app->DB->real_escape_string($beleg['beleg_tatsaechlicheslieferdatum'])."' WHERE id = '$check' LIMIT 1");
                      }
                      if($beleg['beleg_art'])
                      {
                        $this->app->DB->Update("UPDATE ".$belegArt." 
                        SET art = '".$this->app->DB->real_escape_string($beleg['beleg_art'])."' WHERE id = '$check' LIMIT 1");
                      }

                      if($beleg['beleg_versandart'])
                      {
                        $this->app->DB->Update("UPDATE ".$belegArt." SET versandart = '".$this->app->DB->real_escape_string($beleg['beleg_versandart'])."' WHERE id = '$check' LIMIT 1");
                      }
                      if($beleg['beleg_zahlungsweise'])
                      {
                        $this->app->DB->Update("UPDATE ".$belegArt." SET zahlungsweise = '".$this->app->DB->real_escape_string($beleg['beleg_zahlungsweise'])."' WHERE id = '$check' LIMIT 1");
                      }
                      $this->app->DB->Update("UPDATE ".$belegArt." SET belegnr = '".$this->app->DB->real_escape_string($beleg['beleg_belegnr'])."' WHERE id = '$check' LIMIT 1");
                      if(empty($beleg['kundennummer']) && $beleg['art'] !== 'bestellung') {
                        $beleg['kundennummer'] = $adressArr['kundennummer'];
                      }
                      if(empty($beleg['beleg_name'])) {
                        $beleg['beleg_name'] = $adressArr['name'];
                        $beleg['beleg_plz'] = $adressArr['plz'];
                        $beleg['beleg_ort'] = $adressArr['ort'];
                        $beleg['beleg_land'] = $adressArr['land'];
                        $beleg['beleg_abteilung'] = $adressArr['abteilung'];
                        $beleg['beleg_unterabteilung'] = $adressArr['unterabteilung'];
                        $beleg['beleg_adresszusatz'] = $adressArr['adresszusatz'];
                      }
                      $this->app->DB->Update("UPDATE ".$belegArt." SET name = '".$this->app->DB->real_escape_string($beleg['beleg_name'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET abteilung = '".$this->app->DB->real_escape_string($beleg['beleg_abteilung'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET unterabteilung = '".$this->app->DB->real_escape_string($beleg['beleg_unterabteilung'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET strasse = '".$this->app->DB->real_escape_string($beleg['beleg_strasse'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET plz = '".$this->app->DB->real_escape_string($beleg['beleg_plz'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET ort = '".$this->app->DB->real_escape_string($beleg['beleg_ort'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET land = '".$this->app->DB->real_escape_string($beleg['beleg_land'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET email = '".$this->app->DB->real_escape_string($beleg['beleg_email'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET telefon = '".$this->app->DB->real_escape_string($beleg['beleg_telefon'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET adresszusatz = '".$this->app->DB->real_escape_string($beleg['beleg_adresszusatz'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET kundennummer = '".$this->app->DB->real_escape_string($beleg['beleg_kundennummer'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET adresse = '".$this->app->DB->real_escape_string($beleg['adresse'])."' WHERE id = '$check' LIMIT 1");

                      $this->app->DB->Update("UPDATE ".$belegArt." SET internebemerkung = '".$this->app->DB->real_escape_string($beleg['beleg_internebemerkung'])."' WHERE id = '$check' LIMIT 1");
                      $this->app->DB->Update("UPDATE ".$belegArt." SET internebezeichnung = '".$this->app->DB->real_escape_string($beleg['beleg_internebezeichnung'])."' WHERE id = '$check' LIMIT 1");
                      if($belegArt !== 'bestellung') {
                        $this->app->DB->Update("UPDATE " . $belegArt . " SET aktion = '" . $this->app->DB->real_escape_string($beleg['beleg_aktion']) . "' WHERE id = '$check' LIMIT 1");
                      }
                      if($belegArt === 'bestellung') {
                        $this->app->DB->Update("UPDATE ".$belegArt." SET lieferantennummer = '".$this->app->DB->real_escape_string($beleg['beleg_lieferantennummer'])."' WHERE id = '$check' LIMIT 1");
                      }
                      $this->app->DB->Update("UPDATE ".$belegArt." SET freitext = '".$this->app->DB->real_escape_string($beleg['beleg_freitext'])."' WHERE id = '$check' LIMIT 1");
                      if($belegArt !== 'bestellung'){
                        $this->app->DB->Update("UPDATE " . $belegArt . " SET ihrebestellnummer = '" . $this->app->DB->real_escape_string($beleg['beleg_ihrebestellnummer']) . "' WHERE id = '$check' LIMIT 1");
                      }
                      $this->app->DB->Update("UPDATE ".$belegArt." SET lieferbedingung = '".$this->app->DB->real_escape_string($beleg['beleg_lieferbedingung'])."' WHERE id = '$check' LIMIT 1");
                      if($belegArt === 'produktion') {
                        $this->app->DB->Update(
                          sprintf(
                            'UPDATE produktion SET unterlistenexplodieren = %d WHERE id = %d LIMIT 1',
                            $beleg['beleg_unterlistenexplodieren'], $check
                          )
                        );
                        if(!empty($beleg['beleg_auftragid'])) {
                          $this->app->DB->Update(
                            sprintf(
                              'UPDATE produktion SET auftragid = %d WHERE id = %d LIMIT 1',
                              $beleg['beleg_auftragid'], $check
                            )
                          );
                          $datumauslieferung = $this->app->DB->Select(
                            sprintf(
                              'SELECT datumauslieferung FROM produktion WHERE id = %d', $check
                            )
                          );
                          IF($datumauslieferung == '' || $datumauslieferung === '0000-00-00') {
                            $datumauslieferung = $this->app->DB->Select(
                              sprintf(
                                'SELECT lieferdatum FROM auftrag WHERE id = %d', $beleg['beleg_auftragid']
                              )
                            );
                            IF($datumauslieferung == '' || $datumauslieferung === '0000-00-00') {
                              $datumauslieferung = $this->app->DB->Select(
                                sprintf(
                                  'SELECT datum FROM auftrag WHERE id = %d', $beleg['beleg_auftragid']
                                )
                              );
                            }
                            if($datumauslieferung != '' && $datumauslieferung !== '0000-00-00') {
                              $this->app->DB->Update(
                                sprintf(
                                  "UPDATE produktion SET datumauslieferung = '%s' WHERE id = %d",
                                  $datumauslieferung, $check
                                )
                              );
                            }
                          }
                        }
                      }
                    }
                  }
                }
                if($check)
                {
                  $aktbelegid = $check;
                  if($beleg['status'] === 'angelegt'){
                    //Merken der Belegids für nachträgliches löschen der Belegnummern aus der Angebotstabelle
                    $erstelltebelegeids[$check] = $beleg['art'];
                  } else {
                    $erstelltebelegeNichtAngelegtids[$check] = $beleg['art'];
                  }
                }
              }
            }
            if($aktbelegid && ($beleg['artikel'] || $beleg['artikel_nummer'])) {
              if(!$beleg['artikel']) {
                $beleg['artikel'] = $this->app->DB->Select("SELECT id 
                  FROM artikel 
                  WHERE nummer = '".$this->app->DB->real_escape_string($beleg['artikel_nummer'])."' and geloescht <> 1 
                  ORDER BY projekt = '".$beleg['beleg_projekt']."' DESC 
                  LIMIT 1");
                if(!$beleg['artikel'] && isset($beleg['artikel_ean']) && $beleg['artikel_ean'] != '') {
                  $beleg['artikel'] = $this->app->DB->Select("SELECT id 
                    FROM artikel 
                    WHERE ean = '".$this->app->DB->real_escape_string($beleg['artikel_ean'])."' AND ean <> '' and geloescht <> 1 
                    ORDER BY projekt = '".$beleg['beleg_projekt']."' DESC 
                    LIMIT 1");
                }
              }
              if(!$beleg['artikel']) {
                $art['nummer'] = $beleg['artikel_nummer'];
                if(isset($beleg['artikel_ean']) && $beleg['artikel_ean'] != '') {
                  $art['ean'] = $beleg['artikel_ean'];
                }
                $art['projekt'] = $beleg['beleg_projekt'];
                $art['name_de'] = $beleg['artikel_bezeichnung'];
                $art['anabregs_text'] = $beleg['artikel_bezeichnung'];
                if($beleg['artikel_umsatzsteuer'] === 'ermaessigt') {
                  $art['umsatzsteuer'] = $beleg['artikel_umsatzsteuer'];
                }
                $art['einheit'] = $beleg['artikel_einheit'];
                $art['zolltarifnummer'] = $beleg['artikel_zolltarifnummer'];
                $art['herkunftsland'] = $beleg['artikel_herkunftsland'];
                if(!empty($uebertragungen)
                  && !empty($transferData['createarticleifnotexists'])
                  && !empty($transferData['createarticleasstoragearticle'])
                ) {
                  $art['lagerartikel'] = 1;
                }
                $beleg['artikel'] = $this->app->erp->InsertUpdateArtikel($art);
                unset($art);
              }
              if($beleg['artikel'])
              {
                if(empty($beleg['artikel_nummer'])) {
                  $beleg['artikel_nummer'] = $this->app->DB->Select(
                    sprintf(
                      'SELECT nummer FROM artikel WHERE id = %d LIMIT 1',
                      $beleg['artikel']
                    )
                  );
                }

                if(empty($beleg['artikel_bezeichnung'])) {
                  $article = $this->app->DB->SelectRow(
                    sprintf(
                      'SELECT art.name_de, art.name_en, art.`anabregs_text_en`, art.anabregs_text 
                        FROM `artikel` AS `art` WHERE art.id = %d',
                      $beleg['artikel']
                    )
                  );
                  $sprache = $this->app->DB->Select(
                    sprintf(
                      'SELECT `sprache` FROM `%s` WHERE `id` = %d LIMIT 1',
                      $aktbelegart, $aktbelegid
                    )
                  );
                  if(empty($sprache)) {
                    $sprache = $this->app->DB->Select(
                      sprintf(
                        'SELECT `sprache` FROM `adresse` WHERE `id` = %d LIMIT 1',
                        $beleg['adresse']
                      )
                    );
                  }
                  if($sprache !== 'deutsch' && $sprache != '' && strtolower($sprache) !== 'de') {
                    if(!empty($article['name_en'])) {
                      $beleg['artikel_bezeichnung'] = $article['name_en'];
                      if(!empty($aricle['anabregs_text_en'])) {
                        $beleg['artikel_beschreibung'] = $aricle['anabregs_text_en'];
                      }
                    }
                  }
                  if(empty($beleg['artikel_bezeichnung'])){
                    $beleg['artikel_bezeichnung'] = $aricle['name_de'];
                  }
                }
                elseif($uebertragungen > 0) {
                  $article = $this->app->DB->SelectRow(
                    sprintf(
                      'SELECT art.name_de, art.name_en, art.`anabregs_text_en`, art.anabregs_text 
                      FROM `artikel` AS `art` WHERE art.id = %d',
                      $beleg['artikel']
                    )
                  );
                  if(!empty($article['name_en']) && $beleg['artikel_bezeichnung'] !== $article['name_en']) {
                    $sprache = $this->app->DB->Select(
                      sprintf(
                        'SELECT `sprache` FROM `%s` WHERE `id` = %d LIMIT 1',
                        $aktbelegart, $aktbelegid
                      )
                    );
                    if(empty($sprache)) {
                      $sprache = $this->app->DB->Select(
                        sprintf(
                          'SELECT `sprache` FROM `adresse` WHERE `id` = %d LIMIT 1',
                          $beleg['adresse']
                        )
                      );
                    }
                    if($sprache !== 'deutsch' && $sprache != '' && strtolower($sprache) !== 'de') {
                      $beleg['artikel_bezeichnung'] = $article['name_en'];
                      if(!empty($article['anabregs_text_en'])) {
                        $beleg['artikel_beschreibung'] = $article['anabregs_text_en'];
                      }
                    }
                  }
                }
                $belegpos = false;
                switch($aktbelegart)
                {
                  case 'auftrag':
                    $belegpos = $this->app->erp->AddAuftragPositionManuell($aktbelegid, $beleg['artikel'],
                      ($beleg['artikel_preis']/$beleg['artikel_preisfuermenge']), $beleg['artikel_menge'],
                      $beleg['artikel_bezeichnung'],$beleg['artikel_beschreibung']);
                    if(!$belegpos) {
                      $belegpos = $this->app->DB->GetInsertID();
                    }

                    if($teillieferungvon)
                    {
                      if(!$this->app->DB->Select("SELECT porto FROM artikel WHERE id = '".$beleg['artikel']."' LIMIT 1"))
                      {
                        $checkhauptartikel = $this->app->DB->SelectArr("SELECT id, menge, sort 
                          FROM auftrag_position 
                          WHERE artikel = '".$beleg['artikel']."' AND auftrag = '$teillieferungvon' 
                          ORDER BY sort, menge = '".$beleg['menge']."' DESC, menge > '".$beleg['menge']."' DESC");
                        if($checkhauptartikel)
                        {
                          foreach($checkhauptartikel as $k => $v)
                          {
                            if($beleg['artikel_menge'] > 0)
                            {
                              if($v['menge'] > $beleg['artikel_menge'])
                              {
                                $this->app->DB->Update("UPDATE auftrag_position SET menge = menge - ".$beleg['artikel_menge']."  
                                  WHERE id = '".$v['id']."' LIMIT 1");
                                $beleg['artikel_menge'] = 0;
                              }else{
                                $this->app->DB->Delete("DELETE FROM auftrag_position WHERE id = '".$v['id']."' LIMIT 1");
                                $this->app->DB->Update("UPDATE auftrag_position SET sort = sort - 1 
                                WHERE auftrag = '$teillieferungvon' AND sort > '".$v['sort']."'");
                                $beleg['artikel_menge'] -= $v['menge'];
                              }
                            }
                          }
                          $checkmengen = $this->app->DB->Select("SELECT ap.id FROM auftrag_position ap.left 
                                join artikel a on ap.artikel = a.id WHERE ap.auftrag = '$teillieferungvon' AND a.porto <> 1 LIMIT 1");
                          if(!$checkmengen) {
                            $this->app->DB->Update("UPDATE auftrag SET status = 'abgeschlossen' 
                              WHERE id = '$teillieferungvon' LIMIT 1");
                          }
                          $this->app->erp->ANABREGSNeuberechnen($teillieferungvon,$aktbelegart);
                        }
                      }
                    }
                    if($beleg['artikel_rabatt']) {
                      $this->app->DB->Update("UPDATE auftrag_position SET rabatt = '".$beleg['artikel_rabatt']."' 
                      WHERE id = '$belegpos' LIMIT 1");
                    }
                    if($beleg['artikel_waehrung']) {
                      $this->app->DB->Update("UPDATE auftrag_position 
                      SET waehrung = '".$this->app->DB->real_escape_string($beleg['artikel_waehrung'])."' 
                      WHERE id = '$belegpos' LIMIT 1");
                    }

                    break;
                  case 'rechnung':
                    $belegpos = $this->app->erp->AddRechnungPositionManuell($aktbelegid, $beleg['artikel'],
                      ($beleg['artikel_preis']/$beleg['artikel_preisfuermenge']),$beleg['artikel_menge'],$beleg['artikel_bezeichnung'],
                      $beleg['artikel_bezeichnung'],$beleg['artikel_waehrung']);
                    if(!$belegpos) {
                      $belegpos = $this->app->DB->GetInsertID();
                    }

                    if($beleg['artikel_rabatt'])$this->app->DB->Update("UPDATE rechnung_position 
                        SET rabatt = '".$beleg['artikel_rabatt']."' WHERE id = '$belegpos' LIMIT 1");

                    break;
                  case 'gutschrift':
                    $sort = 1+(int)$this->app->DB->Select("SELECT max(sort) FROM gutschrift_position WHERE gutschrift = '$aktbelegid'");

                    $this->app->DB->Insert("INSERT INTO gutschrift_position (gutschrift, artikel, sort, 
                      preis, menge, waehrung, rabatt, bezeichnung,beschreibung) 
                      values ('".$aktbelegid."','".$beleg['artikel']."','".$sort."',
                      '".($beleg['artikel_preis']/$beleg['artikel_preisfuermenge'])."','".$beleg['artikel_menge']."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_waehrung'])."','".$beleg['artikel_rabatt']."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_bezeichnung'])."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_beschreibung'])."')");

                    $belegpos = $this->app->DB->GetInsertID();
                    break;
                  case 'bestellung':
                    $sort = 1+(int)$this->app->DB->Select("SELECT max(sort) FROM bestellung_position WHERE bestellung = '$aktbelegid'");

                    $umsatzsteuer = $beleg['artikel_umsatzsteuer'] === 'ermaessigt'?'ermaessigt':'';
                    $datum = $this->app->DB->Select("SELECT datum FROM bestellung WHERE id = '$aktbelegid' LIMIT 1");

                    $einkauf = $this->app->erp->Einkaufspreis($beleg['artikel'],$beleg['artikel_menge'],$beleg['adresse']);

                    if(!empty($einkauf)){
                      $bestellnummer = $this->app->DB->Select(
                        sprintf(
                          'SELECT e.bestellnummer FROM `einkaufspreise` AS `e` WHERE e.id = %d',
                          $einkauf
                        )
                      );
                      $bestellnummer = $this->app->DB->real_escape_string($bestellnummer);
                    }

                    if(empty($bestellnummer)){
                      $bestellnummer = $this->app->DB->real_escape_string($beleg['artikel_nummer']);
                    }

                    $this->app->DB->Insert(
                      sprintf(
                        "INSERT INTO bestellung_position (bestellung,artikel,bezeichnunglieferant,bestellnummer,menge,preis, 
                          waehrung, sort,lieferdatum, umsatzsteuer, status,projekt, beschreibung)
                        VALUES (%d,%d,'%s','%s',%f,%f,
                        '%s',%d,'%s','%s',
                        'angelegt','%s','%s')",
                        (int)$aktbelegid,
                        (int)$beleg['artikel'],
                        $this->app->DB->real_escape_string($beleg['artikel_bezeichnung']),
                        $bestellnummer,
                        (float)$beleg['artikel_menge'],
                        (float)($beleg['artikel_preis']/$beleg['artikel_preisfuermenge']),
                        $this->app->DB->real_escape_string($beleg['artikel_waehrung']),
                        $sort,$datum,$umsatzsteuer,
                        $this->app->DB->real_escape_string($beleg['artikel_bezeichnung']),
                        $this->app->DB->real_escape_string($beleg['artikel_beschreibung'])
                      )
                    );

                    $belegpos = $this->app->DB->GetInsertID();
                    break;
                  case 'angebot':
                    $sort = 1+(int)$this->app->DB->Select("SELECT max(sort) FROM angebot_position WHERE angebot = '$aktbelegid'");

                    $umsatzsteuer = $beleg['artikel_umsatzsteuer'] === 'ermaessigt'?'ermaessigt':'';
                    $datum = $this->app->DB->Select("SELECT datum FROM angebot WHERE id = '$aktbelegid' LIMIT 1");

                    $this->app->DB->Insert("INSERT INTO angebot_position (angebot,artikel,beschreibung,bezeichnung,nummer,menge,preis, 
                      waehrung, sort,lieferdatum, umsatzsteuer, status,projekt,vpe)
                      VALUES ($aktbelegid','".$beleg['artikel']."','".$this->app->DB->real_escape_string($beleg['artikel_beschreibung'])."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_bezeichnung'])."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_nummer'])."',
                      '".$beleg['artikel_menge']."','".($beleg['artikel_preis']/$beleg['artikel_preisfuermenge'])."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_waehrung'])."','$sort','$datum','$umsatzsteuer','angelegt',
                      '$projekt','')");

                    $belegpos = $this->app->DB->GetInsertID();
                    break;
                  case 'lieferschein':
                    $datum = '0000-00-00';
                    $sort = 1+(int)$this->app->DB->Select("SELECT max(sort) FROM lieferschein_position WHERE lieferschein = '$aktbelegid'");

                    $this->app->DB->Insert("INSERT INTO lieferschein_position (lieferschein,artikel,beschreibung,bezeichnung,nummer,menge, 
                        sort,lieferdatum, status,projekt)
                         VALUES ('$aktbelegid','".$beleg['artikel']."','".$this->app->DB->real_escape_string($beleg['artikel_beschreibung'])."',
                        '".$this->app->DB->real_escape_string($beleg['artikel_bezeichnung'])."',
                        '".$this->app->DB->real_escape_string($beleg['artikel_nummer'])."','".$beleg['artikel_menge']."',
                        '$sort','$datum','angelegt','$projekt')");

                    $belegpos = $this->app->DB->GetInsertID();
                    break;
                  case 'retoure':
                    $datum = '0000-00-00';
                    $sort = 1+(int)$this->app->DB->Select("SELECT max(sort) FROM retoure_position WHERE retoure = '$aktbelegid'");

                    $this->app->DB->Insert("INSERT INTO retoure_position (retoure,artikel,beschreibung,bezeichnung,nummer,menge, 
                        sort,lieferdatum, projekt)
                         VALUES ('$aktbelegid','".$beleg['artikel']."','".$this->app->DB->real_escape_string($beleg['artikel_beschreibung'])."',
                        '".$this->app->DB->real_escape_string($beleg['artikel_bezeichnung'])."',
                        '".$this->app->DB->real_escape_string($beleg['artikel_nummer'])."','".$beleg['artikel_menge']."',
                        '$sort','$datum','$projekt')");

                    $belegpos = $this->app->DB->GetInsertID();
                    break;
                  case 'preisanfrage':
                    $sort = 1+(int)$this->app->DB->Select("SELECT max(sort) FROM preisanfrage_position WHERE preisanfrage= '$aktbelegid'");
                    $datum = $this->app->DB->Select("SELECT datum FROM preisanfrage WHERE id = '$aktbelegid' LIMIT 1");
                    $this->app->DB->Insert("INSERT INTO preisanfrage_position (preisanfrage,artikel,beschreibung,bezeichnung,nummer,menge, 
                      sort,lieferdatum, status,projekt)
                      VALUES ('$aktbelegid','".$beleg['artikel']."','".$this->app->DB->real_escape_string($beleg['artikel_beschreibung'])."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_bezeichnung'])."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_nummer'])."','".$beleg['artikel_menge']."',
                      '$sort','$datum','angelegt','$projekt')");

                    $belegpos = $this->app->DB->GetInsertID();

                    break;
                  case 'proformarechnung':
                    $sort = 1+(int)$this->app->DB->Select("SELECT max(sort) FROM proformarechnung_position WHERE proformarechnung = '$aktbelegid'");
                    $datum = $this->app->DB->Select("SELECT datum FROM proformarechnung WHERE id = '$aktbelegid' LIMIT 1");
                    $umsatzsteuer = $beleg['artikel_umsatzsteuer'] === 'ermaessigt'?'ermaessigt':'';
                    $this->app->DB->Insert("INSERT INTO proformarechnung_position (proformarechnung,artikel,beschreibung,bezeichnung,
                        nummer,menge, sort,lieferdatum, status,projekt,umsatzsteuer)
                      VALUES ($aktbelegid','".$beleg['artikel']."','".$this->app->DB->real_escape_string($beleg['artikel_beschreibung'])."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_bezeichnung'])."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_nummer'])."','".$beleg['artikel_menge']."','$sort',
                      '$datum','angelegt','$projekt','$umsatzsteuer')");

                    $belegpos = $this->app->DB->GetInsertID();
                    break;
                  case 'produktion':
                    $sort = 1+(int)$this->app->DB->Select("SELECT max(sort) FROM produktion_position WHERE produktion= '$aktbelegid'");
                    $datum = $this->app->DB->Select("SELECT datum FROM produktion WHERE id = '$aktbelegid' LIMIT 1");
                    $this->app->DB->Insert("INSERT INTO produktion_position (produktion,artikel,beschreibung,bezeichnung,nummer,menge, 
                      sort,lieferdatum, status,projekt)
                      VALUES ('$aktbelegid','".$beleg['artikel']."','".$this->app->DB->real_escape_string($beleg['artikel_beschreibung'])."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_bezeichnung'])."',
                      '".$this->app->DB->real_escape_string($beleg['artikel_nummer'])."','".$beleg['artikel_menge']."','$sort',
                      '$datum','angelegt','$projekt')");

                    $belegpos = $this->app->DB->GetInsertID();
                    $already_new_item_is_bom = $this->app->DB->Select(
                      sprintf('SELECT stueckliste FROM artikel WHERE id=%d AND produktion=1',
                        $beleg['artikel']
                      )
                    );
                    $already = $this->app->DB->Select(
                      sprintf(
                        'SELECT id FROM produktion_position WHERE produktion=%d AND explodiert=1 LIMIT 1',
                        $aktbelegid
                      )
                    );

                    if(!$already || !$already_new_item_is_bom) {
                      /** @var Produktion $objProduction */
                      $objProduction = $this->app->erp->LoadModul('produktion');
                      if(!empty($objProduction) && method_exists($objProduction, 'addArbeitsanweisungenFunktionsprotokoll')) {
                        $objProduction->addArbeitsanweisungenFunktionsprotokoll($aktbelegid,$belegpos, $beleg['artikel']);
                      }
                    }
                    if(!empty($objProduction)){
                      $this->app->erp->AuftragExplodieren($aktbelegid, 'produktion');
                      $objProduction->ProduktionEinzelnBerechnen($aktbelegid);
                      $objProduction->ProduktionNeuberechnen($aktbelegid);
                    }
                    break;
                  default:
                    break;
                }
                if($belegpos)
                {
                  $artikelnummerkunde = $this->app->DB->real_escape_string(
                    $this->app->DB->Select(
                      "SELECT kundenartikelnummer 
                                    FROM verkaufspreise 
                                    WHERE adresse='".$beleg['beleg_adresse']."' AND artikel='".$beleg['artikel']."' 
                                    AND kundenartikelnummer!='' AND ab_menge <=".
                      (float)$beleg['artikel_menge']." 
                                    AND (gueltig_bis>=NOW() OR gueltig_bis='0000-00-00') 
                                    ORDER by ab_menge DESC 
                                    LIMIT 1"
                    ));

                  if($artikelnummerkunde == ''){
                    // Anzeige Artikel Nummer von Gruppe aus Verkaufspreis
                    $returnwaehrung = null;
                    $gruppevkresult = $this->app->erp->GetVerkaufspreis(
                      $beleg['artikel'],
                      (float)$beleg['artikel_menge'],
                      $beleg['adresse'],
                      !empty($beleg['artikel_waehrung'])?$beleg['artikel_waehrung']:'EUR',
                      $returnwaehrung,
                      true
                    );
                    if($gruppevkresult['kundenartikelnummer'] != ''){
                      $artikelnummerkunde = $gruppevkresult['kundenartikelnummer'];
                    }
                  }

                  if(!empty($artikelnummerkunde)) {
                    $this->app->DB->Update(
                      sprintf(
                        "UPDATE `%s` SET artikelnummerkunde = '%s' WHERE id = %d ",
                        $aktbelegart.'_position',
                        $this->app->DB->real_escape_string($artikelnummerkunde),
                        $belegpos
                      )
                    );
                  }


                  $felder = array('artikelnummerkunde','herkunftsland','zolltarifnummer','einheit','lieferdatum');
                  for($i = 1; $i <= 20; $i++) {
                    $felder[] = 'freifeld'.$i;
                  }
                  foreach($felder as $feld)
                  {
                    $this->app->DB->Update("UPDATE ".$aktbelegart."_position 
                    SET $feld = '".$this->app->DB->real_escape_string($beleg['artikel_'.$feld])."' 
                    WHERE id = '$belegpos' LIMIT 1");
                  }
                  $this->app->erp->ANABREGSNeuberechnen($aktbelegid,$aktbelegart);
                  $this->app->DB->Delete("DELETE FROM belegeimport WHERE id = '".$beleg['id']."' LIMIT 1");
                }
              }
            }
          }
        }
        foreach ($erstelltebelegeids as $belegid => $belegart) {
          //Nachträgliches löschen aller Belegnummern aus den Angeboten wenn Status == angelegt
          $this->app->DB->Update("UPDATE " . $belegart . " SET belegnr = '' WHERE id = '$belegid' LIMIT 1");
        }
        foreach ($erstelltebelegeNichtAngelegtids as $belegid => $belegart) {
          if($this->app->DB->Select(
            sprintf('
              SELECT id 
              FROM `%s` 
              WHERE id = %d AND (status = \'freigegeben\' OR status = \'abgeschlossen\' OR status = \'versendet\') AND belegnr = \'\'
              ', $belegart, $belegid
            )
          )
          ){
            $this->app->erp->BelegFreigabe($belegart, $belegid);
          }
        }
        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Die Belege wurden importiert!</div>");
      }else{
        $msg = $this->app->erp->base64_url_encode("<div class=\"info\">Keine Belege zum Importieren gefunden!</div>");
      }
      if($uebertragungen) {
        return $ret;
      }
      if($idliste) {
        return !empty($belege)?count($belege):0;
      }
      return $msg;
    }

    public function BelegeimportDatei($_datei = null, $uebertragungen = false)
    {
      $ret = null;
      $_projekt = 0;
      if($uebertragungen) {
        $_projekt = $this->app->DB->Select("SELECT projekt FROM uebertragungen_account WHERE id = '$uebertragungen' LIMIT 1");
      }
      if($_datei === null)
      {
        //$this->app->erp->BelegeimportMenu();
        //$this->app->YUI->TableSearch('TAB1','belegeimport_list', 'show','','',basename(__FILE__), __CLASS__);

        $art = $this->app->Secure->GetPOST("art");
        $status = $this->app->Secure->GetPOST("status");
        $datei = (int)$this->app->Secure->GetGET('datei');
      }else{
        $ret = null;
        $art = '';
        $datei = $_datei;
        $status = 'angelegt';
      }
      if($uebertragungen) {
        $api = $this->app->DB->Select("SELECT api FROM uebertragungen_account WHERE id = '$uebertragungen' LIMIT 1");
      }
      $row = 1;
      if($_datei === null)
      {
        $art = $this->app->Secure->GetPOST("art");
        $status = $this->app->Secure->GetPOST("status");
        $datei = (int)$this->app->Secure->GetGET('datei');
      }else{
        $ret = null;
        $art = '';
        $datei = $_datei;
        $status = 'angelegt';
      }

      if($datei || (!empty($_FILES['datei']) && !empty($_FILES['datei']['tmp_name'])))
      {
        if (
          ($handle = fopen(
            $_datei === null?(isset($_FILES['datei'])?$_FILES['datei']['tmp_name']:$this->app->erp->GetDateiPfad($datei)):$datei,
            'r')
          ) !== FALSE
        ) {
          $vkpreisex = false;
          $artikel_bezeichnung = false;
          $beleg_datum = false;
          $menge = false;
          $beleg_art = false;
          $beleg_nr_parent = 0;
          $oldbeleg_belegnr = '';
          $fields = [];
          $csvseparator = $this->app->DB->Select(
            sprintf(
              'SELECT csvseparator FROM uebertragungen_account WHERE id = %d',
              $uebertragungen
            )
          );
          if(empty($csvseparator)) {
            $csvseparator = ';';
          }
          while (($data = fgetcsv($handle, 0, $csvseparator,'"')) !== FALSE) {
            $num = count($data);
            //echo "<p> $num Felder in Zeile $row: <br /></p>\n";

            if($row > 1){
              if($_datei !== null) {
                $status = 'angelegt';
              }
              for ($c=0; $c < $num; $c++){
                if($fields[$c] === 'art' && $data[$c]) {
                  $art = trim(strtolower($data[$c]));
                }
              }
              $this->app->DB->Insert("INSERT INTO belegeimport (userid,status,art) VALUES ('".($_datei === null?$this->app->User->GetID():0)."','$status','$art')");
              $rowid = $this->app->DB->GetInsertID();
              if($uebertragungen) {
                $this->app->DB->Update("UPDATE belegeimport SET beleg_projekt = '$_projekt' WHERE id = '$rowid' LIMIT 1");
              }
              $ret[] = $rowid;
            }
            $preis = 0;
            $preisfuermenge = 1;
            $projekt = $_projekt;
            $tmpartikelid = 0;
            $tmpadresseid = 0;
            for ($c=0; $c < $num; $c++){
              if($row==1){
                $fields[$c]=$this->app->DB->real_escape_string($data[$c]);
                if($fields[$c] === 'artikel_preis')$vkpreisex = str_replace(',','.',$c);
                if($fields[$c] === 'artikel_menge')$menge = str_replace(',','.',$c);
                if($fields[$c] === 'artikel_bezeichnung')$artikel_bezeichnung = $c;
                if($fields[$c] === 'beleg_datum')$beleg_datum = $c;
                if($fields[$c] === 'beleg_art')$beleg_art = $c;
              }else{
                switch($fields[$c])
                {
                  case 'beleg_status':
                    if(in_array($data[$c],['angelegt','freigegeben','storniert','abgeschlossen','gestartet','versendet'])) {
                      $status = $data[$c];

                    }

                    break;
                  case 'beleg_belegnr':

                    if($uebertragungen && ($data[$c] != '' && $data[$c] == $oldbeleg_belegnr))
                    {
                      $data[$c] = 'PARENT';
                    }elseif($uebertragungen && $oldbeleg_belegnr != $data[$c] && $data[$c] !== 'NEW' && $data[$c] !== 'PARENT')
                    {
                      $oldbeleg_belegnr = $data[$c];
                    }

                    if(($data[$c] !== 'PARENT' && $status === 'angelegt') || $data[$c] == ''){
                      $data[$c] = "NEW";
                    }

                    if($data[$c] === 'PARENT'){
                      if($beleg_nr_parent){
                        //Übernimm Belegnummer der vorherigen Zeile
                        $data[$c] = $beleg_nr_parent;
                      }else{
                        //Falls vor dieser PARENT Zeile kein Eintrag vorhanden ist soll sie als NEW betrachtet werden
                        $data[$c] = 'NEW';
                      }
                    }


                    if($data[$c] === 'NEW'){
                      if($status === 'angelegt'){
                        //Neue Entwurfsbelegnummer
                        $data[$c] = 'ENTWURF_'.$rowid.'_';
                      }else{
                        //suche nächste freie Belegnummer falls NEW oder leere Belegnummer
                        $data[$c] = $this->app->erp->GetNextNummer($art);
                      }
                      $oldbeleg_belegnr = $data[$c];
                    }
                    //Merke die aktuelle Belegnummer falls der nächste Eintrag PARENT ist
                    $beleg_nr_parent = $data[$c];
                    break;

                  case 'beleg_projekt':
                    if((!is_numeric($data[$c]) || strpos($data[$c],'.') !== false) && $data[$c])
                    {
                      $data[$c] = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '".$this->app->DB->real_escape_string($data[$c])."' LIMIT 1");
                      if($data[$c]) {
                        $projekt = $data[$c];
                      }
                    }
                    break;

                  case 'artikel':
                    if($uebertragungen && $api)
                    {
                      if(!$tmpartikelid)
                      {
                        $tmpartikelid = $this->app->DB->Select(
                          "SELECT id_int 
                          FROM api_mapping 
                          WHERE id_ext = '%s' AND id_ext != '' AND id_int <> 0 AND tabelle = 'artikel' AND api = %d 
                          LIMIT 1",
                          $data[$c], (int)$api
                        );
                        if($tmpartikelid > 0) {
                          $this->app->DB->Update("UPDATE belegeimport SET artikel='$tmpartikelid' WHERE id='".$rowid."' LIMIT 1");
                        }
                      }
                    }
                    break;

                  case 'artikel_nummer':
                    if(!$tmpartikelid) {
                      $tmpartikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='".$this->app->DB->real_escape_string($data[$c])."' AND nummer!='' AND geloescht!=1 ORDER BY projekt = '$projekt' DESC LIMIT 1");
                    }
                    if($tmpartikelid > 0) {
                      $this->app->DB->Update("UPDATE belegeimport SET artikel='$tmpartikelid' WHERE id='".$rowid."' LIMIT 1");
                    }
                    break;

                  case 'artikel_ean':
                    if(!$tmpartikelid)$tmpartikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE ean='".$this->app->DB->real_escape_string($data[$c])."' AND ean != '' AND nummer!='' AND geloescht!=1 ORDER BY projekt = '$projekt' DESC LIMIT 1");
                    if($tmpartikelid > 0) {
                      $this->app->DB->Update("UPDATE belegeimport SET artikel='$tmpartikelid' WHERE id='".$rowid."' LIMIT 1");
                    }
                    break;

                  case 'adresse':
                    if($uebertragungen && $api)
                    {
                      $tmpadresseid = (int)$data[$c];
                    }
                    break;

                  case 'beleg_kundennummer':
                    if(!$tmpadresseid)
                    {
                      $tmpadresseid = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer='".$this->app->DB->real_escape_string($data[$c])."' AND kundennummer!='' AND geloescht!=1 ORDER BY projekt = '$projekt' DESC LIMIT 1");
                      if($tmpadresseid > 0) {
                        $this->app->DB->Update("UPDATE belegeimport SET adresse='$tmpadresseid' WHERE id='".$rowid."' LIMIT 1");
                      }
                      else $tmpadresseid = 0;
                    }
                    if($data[$c]=='') {
                      $data[$c] = 'NEW';
                    }
                    break;
                  case 'beleg_lieferantennummer':
                    if(!$tmpadresseid)
                    {
                      $tmpadresseid = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer='".$this->app->DB->real_escape_string($data[$c])."' AND lieferantennummer!='' AND geloescht!=1 ORDER BY projekt = '$projekt' DESC LIMIT 1");
                      if($tmpadresseid > 0) {
                        $this->app->DB->Update("UPDATE belegeimport SET adresse='$tmpadresseid' WHERE id='".$rowid."' LIMIT 1");
                      }
                      else $tmpadresseid = 0;
                    }
                    if($data[$c]=='') {
                      $data[$c] = 'NEW';
                    }
                    break;

                  case 'beleg_datum':
                    if(strpos($data[$c],'.') !== false)
                    {
                      $datuma = explode('.',$data[$c]);
                      if(count($datuma) == 3)
                      {
                        if(strlen($datuma[2]) == 2)
                        {
                          $curryear = date("Y")-2000;
                          if((int)$datuma[2] <= $curryear)
                          {
                            $data[$c] = $datuma[0].'.'.$datuma[1].'.20'.$datuma[2];
                          }else{
                            $data[$c] = $datuma[0].'.'.$datuma[1].'.19'.$datuma[2];
                          }
                        }
                      }
                      $data[$c] = date('Y-m-d',strtotime($data[$c]));
                    }
                    break;

                  case 'beleg_lieferdatum':
                    if(strpos($data[$c],'.') !== false)
                    {
                      $datuma = explode('.',$data[$c]);
                      if(count($datuma) == 3)
                      {
                        if(strlen($datuma[2]) == 2)
                        {
                          $curryear = date("Y")-2000;
                          if((int)$datuma[2] <= $curryear)
                          {
                            $data[$c] = $datuma[0].'.'.$datuma[1].'.20'.$datuma[2];
                          }else{
                            $data[$c] = $datuma[0].'.'.$datuma[1].'.19'.$datuma[2];
                          }
                        }
                      }
                      $data[$c] = date('Y-m-d',strtotime($data[$c]));
                    }
                    break;

                  case 'beleg_tatsaechlicheslieferdatum':
                    if(strpos($data[$c],'.') !== false)
                    {
                      $datuma = explode('.',$data[$c]);
                      if(count($datuma) == 3)
                      {
                        if(strlen($datuma[2]) == 2)
                        {
                          $curryear = date("Y")-2000;
                          if((int)$datuma[2] <= $curryear)
                          {
                            $data[$c] = $datuma[0].'.'.$datuma[1].'.20'.$datuma[2];
                          }else{
                            $data[$c] = $datuma[0].'.'.$datuma[1].'.19'.$datuma[2];
                          }
                        }
                      }
                      $data[$c] = date('Y-m-d',strtotime($data[$c]));
                    }
                    break;

                  case 'artikel_lieferdatum':
                    if(strpos($data[$c],'.') !== false)
                    {
                      $datuma = explode('.',$data[$c]);
                      if(count($datuma) == 3)
                      {
                        if(strlen($datuma[2]) == 2)
                        {
                          $curryear = date("Y")-2000;
                          if((int)$datuma[2] <= $curryear)
                          {
                            $data[$c] = $datuma[0].'.'.$datuma[1].'.20'.$datuma[2];
                          }else{
                            $data[$c] = $datuma[0].'.'.$datuma[1].'.19'.$datuma[2];
                          }
                        }
                      }
                      $data[$c] = date('Y-m-d',strtotime($data[$c]));
                    }
                    break;

                  case 'artikel_preis':
                    $preis = str_replace(',','.',$data[$c]);
                    if ($preisfuermenge == 0) {
                      $preisfuermenge =1;
                    }
                    $data[$c] = $preis / $preisfuermenge;
                    break;
                  case 'artikel_preisfuermenge':
                    $preisfuermenge = str_replace(',','.',$data[$c]);
                    if ($preisfuermenge == 0) {
                      $preisfuermenge =1;
                    }
                    $data['artikel_preis'] = $preis / $preisfuermenge;
                    break;
                  case 'artikel_rabatt':
                  case 'artikel_menge':
                    $data[$c] = str_replace(',','.',$data[$c]);
                    break;

                }
                if(isset($fields[$c]) && $fields[$c])
                {
                  $this->app->DB->Update("UPDATE belegeimport SET ".$fields[$c]." = '".$this->app->DB->real_escape_string($data[$c])."' WHERE id='".$rowid."' LIMIT 1");
                }
                if(!empty($status)) {
                  $this->app->DB->Update(
                    sprintf(
                      "UPDATE belegeimport SET status = '%s' WHERE id = %d",
                      $this->app->DB->real_escape_string($status), $rowid
                    )
                  );
                }

              }
              //echo $data[$c] . "<br />\n";
            }

            if($row > 1){
              if($menge === false)
              {
                $this->app->DB->Update("UPDATE belegeimport SET artikel_menge = 1 WHERE id='".$rowid."' LIMIT 1");
              }
              if($beleg_datum === false)
              {
                $this->app->DB->Update("UPDATE belegeimport SET beleg_datum = now() WHERE id = '".$rowid."' LIMIT 1");
              }
              if($tmpadresseid && $beleg_art === false)
              {
                $arttmp = $this->app->DB->Select("SELECT art FROM adresse WHERE id = '$tmpadresseid' LIMIT 1");
                if($arttmp) {
                  $this->app->DB->Update("UPDATE belegeimport SET beleg_art = '".$this->app->DB->real_escape_string($arttmp)."' WHERE id = '".$rowid."' LIMIT 1");
                }
              }
              if( $tmpartikelid)
              {
                if($vkpreisex === false)
                {
                  $this->app->DB->Update("UPDATE belegeimport SET artikel_preis = '".$this->app->erp->GetVerkaufspreis($tmpartikelid,($menge === false?1: ($data[$menge]>0?$data[$menge]:1)),$tmpadresseid )."' WHERE id = '".$rowid."' LIMIT 1");
                }
                if($artikel_bezeichnung === false)
                {
                  $this->app->DB->Update("UPDATE belegeimport SET artikel_bezeichnung = '".$this->app->DB->real_escape_string($this->app->DB->Select("SELECT name_de FROM artikel WHERE id = '$tmpartikelid' LIMIT 1" ))."' WHERE id = '".$rowid."' LIMIT 1");
                }
              }
              if(!$data[$menge]){
                //Artikel mit Menge 0 entfernen
                $this->app->DB->Update("DELETE FROM belegeimport WHERE id='".$rowid."'");
              }
            }

            $row++;
          }
          fclose($handle);
        }
      }
      return $ret;
    }
  }

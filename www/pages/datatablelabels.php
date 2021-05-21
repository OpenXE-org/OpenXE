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

class Datatablelabels
{
  /** @var string MODULE_NAME */
  const MODULE_NAME = 'Label';

  /** @var erpooSystem $app */
  public $app;

  /** @var array $javascript */
  public $javascript = [
    'head' => [
      './classes/Modules/Label/www/js/jquery.labels.js',
      './classes/Modules/Label/www/js/datatablelabel-manager.js',
    ],
    'body' => [
      './classes/Modules/Label/www/js/datatablelabel-module.js',
    ],
  ];

  /** @var array $stylesheet */
  public $stylesheet = [
    './classes/Modules/Label/www/css/labels.css',
    './classes/Modules/Label/www/css/datatablelabel.css',
  ];

  /**
   * @param erpooSystem $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;
    if($intern){
      return;
    }

    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler('list', 'DataTableLabelsList');
    $this->app->ActionHandler('edit', 'DataTableLabelsEdit');
    $this->app->ActionHandler('automaticlabels', 'DataTableLabelsAutomaticLabelsList');
    $this->app->ActionHandler('automaticlabelsedit', 'DataTableLabelsAutomaticLabelsEdit');
    $this->app->ActionHandlerListen($app);
  }

  /**
   * @param erpooSystem $app
   * @param string      $name
   * @param array       $allowed
   *
   * @return array|false
   */
  public static function TableSearch(&$app, $name, $allowed)
  {
    switch ($name) {
      case 'datatablelabels_list':

        $heading = array('Bezeichnung', 'Kennung', 'Gruppe', 'Farbe', 'Men&uuml;');
        $width = array('50%', '15%', '15%', '15%', '5%');

        $findcols = array('t.title', 't.type', 'g.group_table', 't.id');
        $searchsql = array('t.title', 't.type', 'g.group_table');

        $aligncenter = [4];
        $defaultorder = 3;
        $defaultorderdesc = 0;

        $menu = '<table border="0" cellpadding="0" cellspacing="0">';
        $menu .= '<tr>';
        $menu .= '<td nowrap>';
        $menu .= '<a href="#" class="datatablelabels-edit" data-id="%value%">';
        $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= '</a>&nbsp;';
        $menu .= '<a href="#" class="datatablelabels-delete" data-id="%value%">';
        $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= '</a>';
        $menu .= '</td>';
        $menu .= '</tr>';
        $menu .= '</table>';

        $where = ' t.id > 0';

        $sql =
          "SELECT SQL_CALC_FOUND_ROWS t.id, t.title, t.type, g.group_table, ".
          "CONCAT('<span class=\"label-color-preview\" data-hexcolor=\"', t.hexcolor, '\"></span>') AS color, ".
          "t.id ".
          "FROM label_type AS t ".
          "LEFT JOIN label_group AS g ON t.label_group_id = g.id";
        $count =
          "SELECT COUNT(t.id) FROM label_type AS t ".
          "LEFT JOIN label_group AS g ON t.label_group_id = g.id ".
          "WHERE {$where}";
        break;

      case 'datatablelabels_automaticlabelslist':

        $heading = array('Label', 'Aktion', 'Auswahl', 'Projekt', 'Men&uuml;');
        $width = array('15%', '20%', '20%', '15%', '1%');

        $findcols = array('t.type', 'al.action', 'al.selection', 'p.abkuerzung', 'al.id');
        $searchsql = array('t.type', 'al.action', 'al.selection', 'p.abkuerzung');

        $defaultorder = 1;
        $defaultorderdesc = 0;

        $menu = '<table border="0" cellpadding="0" cellspacing="0">';
        $menu .= '<tr>';
        $menu .= '<td nowrap>';
        $menu .= '<a href="#" class="datatablelabels-automaticlabeledit" data-id="%value%">';
        $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/edit.svg\" border=\"0\">";
        $menu .= '</a>&nbsp;';
        $menu .= '<a href="#" class="datatablelabels-automaticlabeldelete" data-id="%value%">';
        $menu .= "<img src=\"themes/{$app->Conf->WFconf['defaulttheme']}/images/delete.svg\" border=\"0\">";
        $menu .= '</a>';
        $menu .= '</td>';
        $menu .= '</tr>';
        $menu .= '</table>';

        $where = ' al.id > 0';

        $sql = "SELECT SQL_CALC_FOUND_ROWS al.id, t.type, CONCAT(UCASE(LEFT(al.action, 1)), SUBSTRING(al.action, 2)), 
                  CONCAT(UCASE(LEFT(al.selection, 1)), SUBSTRING(al.selection, 2)), p.abkuerzung, al.id 
                FROM label_automatic AS al
                LEFT JOIN label_type AS t ON al.label_type_id = t.id
                LEFT JOIN projekt AS p ON al.project_id = p.id";

        break;

      default:
        return false;
        break;
    }

    $result = [
      'heading' => $heading,
      'width' => $width,
      'findcols' => $findcols,
      'searchsql' => $searchsql,
      'aligncenter' => $aligncenter,
      'defaultorder' => $defaultorder,
      'defaultorderdesc' => $defaultorderdesc,
      'menu' => $menu,
      'where' => $where,
      'sql' => $sql,
      'count' => $count,
      'maxrows' => 50,
    ];

    $allowedKeys = array_flip((array)$allowed);
    $result = array_intersect_key($result, $allowedKeys);

    return $result;
  }

  /**
   * @return void
   */
  public function Install()
  {
    $this->app->erp->CheckTable('label_type');
    $this->app->erp->CheckColumn('id', 'INT(11) UNSIGNED', 'label_type', 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('label_group_id', 'INT(11) UNSIGNED', 'label_type', 'NOT NULL DEFAULT 0');
    $this->app->erp->CheckColumn('type', 'VARCHAR(24)', 'label_type', 'NOT NULL');
    $this->app->erp->CheckColumn('title', 'VARCHAR(64)', 'label_type', 'NOT NULL');
    $this->app->erp->CheckColumn('hexcolor', 'VARCHAR(7)', 'label_type', 'NOT NULL DEFAULT \'#FFFFFF\'');
    $this->app->erp->CheckColumn('created_at', 'TIMESTAMP', 'label_type', 'NOT NULL DEFAULT CURRENT_TIMESTAMP');
    $this->app->erp->CheckColumn('updated_at', 'TIMESTAMP', 'label_type', 'NOT NULL DEFAULT \'0000-00-00 00:00:00\'');
    $this->app->erp->CheckIndex('label_type', 'type', true);

    $this->app->erp->CheckTable('label_reference');
    $this->app->erp->CheckColumn('id', 'INT(11) UNSIGNED', 'label_reference', 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('label_type_id', 'INT(11) UNSIGNED', 'label_reference', 'NOT NULL');
    $this->app->erp->CheckColumn('reference_table', 'VARCHAR(64)', 'label_reference', 'NOT NULL');
    $this->app->erp->CheckColumn('reference_id', 'INT(11) UNSIGNED', 'label_reference', 'NOT NULL');
    $this->app->erp->CheckColumn('created_at', 'TIMESTAMP', 'label_reference', 'NOT NULL DEFAULT CURRENT_TIMESTAMP');
    $this->app->erp->CheckAlterTable('ALTER TABLE `label_reference` ADD UNIQUE (`label_type_id`, `reference_table`, `reference_id`)');

    $this->app->erp->CheckTable('label_group');
    $this->app->erp->CheckColumn('id', 'INT(11) UNSIGNED', 'label_group', 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('group_table', 'VARCHAR(64)', 'label_group', 'NOT NULL');
    $this->app->erp->CheckColumn('title', 'VARCHAR(64)', 'label_group', 'NOT NULL');
    $this->app->erp->CheckColumn('created_at', 'TIMESTAMP', 'label_group', 'NOT NULL DEFAULT CURRENT_TIMESTAMP');
    $this->app->erp->CheckIndex('label_group', 'group_table', true);

    if ($this->app->DB->CheckTableExistence("automatic_label")){
      $this->app->DB->Query("RENAME TABLE `automatic_label` TO `label_automatic`");
    }

    $this->app->erp->CheckTable('label_automatic');
    $this->app->erp->CheckColumn('id', 'INT(11)', 'label_automatic', 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('label_type_id', 'INT(11)', 'label_automatic', 'NOT NULL DEFAULT 0');
    $this->app->erp->CheckColumn('project_id', 'INT(11)', 'label_automatic', 'NOT NULL DEFAULT 0');
    $this->app->erp->CheckColumn('action', 'VARCHAR(64)', 'label_automatic', 'NOT NULL');
    $this->app->erp->CheckColumn('selection', 'VARCHAR(64)', 'label_automatic', 'NOT NULL');

    $this->app->erp->RegisterHook('eproosystem_ende', 'Datatablelabels', 'DataTableLabelsInclude');
    $this->app->erp->RegisterHook('dokumentsend_ende', 'Datatablelabels', 'DataTableLabelsDokumentSendHook');
    $this->app->erp->RegisterHook('parseuservars', 'Datatablelabels', 'DatatablelabelsParseUserVars');
    $this->app->erp->RegisterHook('auftrag_versand_ende', 'Datatablelabels', 'DatatablelabelsOrderSent');
    $this->app->erp->RegisterHook('transfer_document_incoming', 'Datatablelabels', 'DatatablelabelsTransferDocumentIncomming');

    $this->app->DB->Query("UPDATE label_reference AS lr SET lr.reference_table = 'lieferschein' WHERE lr.reference_table = 'lieferscheine'");
    $this->app->DB->Query("UPDATE label_group AS lg SET lg.group_table = 'lieferschein', lg.title = 'Lieferschein' WHERE lg.group_table = 'lieferscheine'");
    $this->app->DB->Query("UPDATE label_reference AS lr SET lr.reference_table = 'gutschrift' WHERE lr.reference_table = 'gutschriften'");
    $this->app->DB->Query("UPDATE label_group AS lg SET lg.group_table = 'gutschrift', lg.title = 'Gutschrift' WHERE lg.group_table = 'gutschriften'");

  }

  /**
   * @return void
   */
  public function DataTableLabelsInclude()
  {
    $this->app->ModuleScriptCache->IncludeModule('Datatablelabels');
  }

  /**
   * @param int    $tranferId
   * @param string $doctype
   * @param int    $doctypeId
   */
  public function DatatablelabelsTransferDocumentIncomming($tranferId, $doctype, $doctypeId)
  {
    if(empty($doctypeId) ||
      !in_array(
        $doctype,
        [
        'angebot' , 'auftrag' , 'bestellung', 'gutschrift', 'lieferschein', 'produktion', 'rechnung'
        ]
      )
    ) {
      return;
    }
    $projectId = (int)$this->app->DB->Select(sprintf('SELECT `projekt` FROM `%s` WHERE `id` = %d',$doctype, $doctypeId));
    // Passende Automatische Label suchen; IDs der LabelTypen werden zurückgegeben
    $labelTypeIds = $this->FindLabelTypesForAutomaticLabel('uebertragungen_eingang', $doctype, $projectId);

    // Labels erstellen
    foreach ($labelTypeIds as $labelTypeId) {
      $this->CreateDataTableLabel($labelTypeId, $doctype, $doctypeId);
    }
  }

  /**
   * @param string $documentType [angebot|auftrag|...]
   * @param string $documentId   ID des Angebots, Auftrags oder ...
   * @param string $projektId
   * @param string $adresseId
   *
   * @return void
   */
  public function DataTableLabelsDokumentSendHook($documentType, $documentId, $projectId, $addressId, $type)
  {
    if($type !== 'drucken' && $type !== 'brief' && $type !== 'fax'){
      return;
    }
    $projectId = (int)$projectId;
    $documentId = (int)$documentId;

    // Passende Automatische Label suchen; IDs der LabelTypen werden zurückgegeben
    $labelTypeIds = $this->FindLabelTypesForAutomaticLabel('drucken', $documentType, $projectId);

    // Labels erstellen
    foreach ($labelTypeIds as $labelTypeId) {
      $this->CreateDataTableLabel($labelTypeId, $documentType, $documentId);
    }
  }

  /**
   * @param int|string $orderId
   */
  public function DatatablelabelsOrderSent($orderId)
  {
    $orderId = (int)$orderId;
    if($orderId <= 0) {
      return;
    }
    $projectId = (int)$this->app->DB->Select(sprintf('SELECT `projekt` FROM `auftrag` WHERE `id` = %d', $orderId));
    // Passende Automatische Label suchen; IDs der LabelTypen werden zurückgegeben
    $labelTypeIds = $this->FindLabelTypesForAutomaticLabel('autoversand', 'auftrag', $projectId);

    // Labels erstellen
    foreach ($labelTypeIds as $labelTypeId) {
      $this->CreateDataTableLabel($labelTypeId, 'auftrag', $orderId);
    }
  }

  /**
   * @param string $type Dokumenttyp
   * @param int    $id   Dokument-ID
   * @param string $text Textbaustein
   *
   * @return void
   */
  public function DatatablelabelsParseUserVars($type, $id, &$text)
  {
    if (strpos($text, '{LABELS}') !== false) {

      /** @var \Xentral\Modules\Label\LabelGateway $gateway */
      $gateway = $this->app->Container->get('LabelGateway');
      $labels = (array)$gateway->findLabelsByReference($type, $id);
      if (!empty($labels)){
        $labelTitles = array_column($labels, 'title');
        $labelText = implode(', ', $labelTitles);
      } else {
        $labelText = '';
      }

      $text = str_replace('{LABELS}', $labelText, $text);
    }
  }

  /**
   * @return void
   */
  public function DataTableLabelsMenu()
  {
    $this->app->erp->MenuEintrag('index.php?module=datatablelabels&action=list', 'Zur&uuml;ck zur &Uuml;bersicht');
    $this->app->erp->MenuEintrag('index.php?module=datatablelabels&action=list', 'Label-Typen');
    $this->app->erp->MenuEintrag('index.php?module=datatablelabels&action=automaticlabels', 'Automatische Labels');
    $this->app->erp->MenuEintrag('DataTableLabelsUi.createItem()', 'Neuer Eintrag');
  }

  /**
   * @return void
   */
  public function DataTableLabelsList()
  {
    $this->DataTableLabelsMenu();

    $this->app->YUI->ColorPicker('datatablelabel_hexcolor');
    $this->app->YUI->TableSearch('TAB1', 'datatablelabels_list', 'show', '', '', basename(__FILE__), __CLASS__);

    // Label-Gruppen laden und HTML-Dropdown-Optionen generieren
    $labelGroups = $this->GetLabelGroups();
    $labelGroupsOptions = '<option value="0" selected="selected">{|Alle|}</option>';
    foreach ($labelGroups as $labelGroup) {
      $labelGroupsOptions .= sprintf(
        '<option value="%s">%s (%s)</option>',
        $labelGroup['id'],
        $labelGroup['title'],
        $labelGroup['group_table']
      );
    }
    $this->app->Tpl->Set('DATATABLE_GROUP_OPTIONS', $labelGroupsOptions);

    $this->app->erp->Headlines('Labels');
    $this->app->Tpl->Parse('PAGE', 'datatablelabels_list.tpl');
  }

  /**
   * @return void
   */
  public function DataTableLabelsEdit()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    switch ($cmd) {

      case 'get':
        $id = (int)$this->app->Secure->GetPOST('id');
        $result = $this->AjaxGetLabelType($id);
        break;

      case 'save':
        $id = (int)$this->app->Secure->GetPOST('id');
        $type = (string)$this->app->Secure->GetPOST('type');
        $title = (string)$this->app->Secure->GetPOST('title', 'nohtml');
        $groupId = (int)$this->app->Secure->GetPOST('group');
        $hexColor = (string)$this->app->Secure->GetPOST('hexcolor');
        $result = $this->AjaxSaveLabelType($id, $type, $title, $hexColor, $groupId);
        break;

      case 'delete':
        $id = (int)$this->app->Secure->GetPOST('id');
        $result = $this->AjaxDeleteLabelType($id);
        break;

      default:
        $result = ['success' => false, 'error' => 'Invalid command'];
        break;
    }

    if(isset($result['success']) && $result['success'] === false){
      header('HTTP/1.0 404 Not Found');
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    $this->app->ExitXentral();
  }

  /**
   * @return void
   */
  public function DataTableLabelsAutomaticLabelsList()
  {
    $this->DataTableLabelsMenu();
    
    $action = array(
      //'versand' => 'Versand von Beleg',
      'drucken' => 'Drucken von Beleg',
      'autoversand' => 'Autoversand',
      //'versandzentrum' => 'Versandzentrum'
    );
    if($this->app->erp->ModulVorhanden('uebertragungen')) {
      $action['uebertragungen_eingang'] = 'API / &Uuml;bertragungen Eingang';
    }
    $selection = array(
      'angebot' => 'Angebot', 'auftrag' => 'Auftrag', 'bestellung' => 'Bestellung', 'gutschrift' => 'Gutschrift',
      'lieferschein' => 'Lieferschein', 'produktion' => 'Produktion', 'rechnung' => 'Rechnung'
    );

    $actionString = '';
    $selectionString = '';

    foreach($action as $key=>$value){
      $actionString .= '<option value="'.$key.'">'.$value.'</option>';
    }

    foreach($selection as $key=>$value){
      $selectionString .= '<option value="'.$key.'">'.$value.'</option>';
    }

    $this->app->Tpl->Set('AUTOMATICLABELACTION', $actionString);
    $this->app->Tpl->Set('AUTOMATICLABELSELECTION', $selectionString);

    $this->app->YUI->AutoComplete('datatablelabel_automaticlabelname', 'label_type');
    $this->app->YUI->AutoComplete('datatablelabel_automaticlabelproject', 'projektname', 1);

    $this->app->YUI->TableSearch('TAB1', 'datatablelabels_automaticlabelslist', 'show', '', '', basename(__FILE__), __CLASS__);

    $this->app->erp->Headlines('Labels');
    $this->app->Tpl->Parse('PAGE', 'datatablelabels_automaticlabelslist.tpl');
  }

  /**
   * @return void
   */
  public function DataTableLabelsAutomaticLabelsEdit()
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    switch ($cmd) {

      case 'get':
        $id = (int)$this->app->Secure->GetPOST('id');
        $result = $this->AjaxGetAutomaticLabel($id);
        break;

      case 'save':
        $id = (int)$this->app->Secure->GetPOST('id');
        $labelName = (string)$this->app->Secure->GetPOST('labelname');
        $action = (string)$this->app->Secure->GetPOST('action');
        $selection = (string)$this->app->Secure->GetPOST('selection');
        $project = (string)$this->app->Secure->GetPOST('project');
        $result = $this->AjaxSaveAutomaticLabel($id, $labelName, $action, $selection, $project);
        break;

      case 'delete':
        $id = (int)$this->app->Secure->GetPOST('id');
        $result = $this->AjaxDeleteAutomaticLabel($id);
        break;

      default:
        $result = ['success' => false, 'error' => 'Invalid command'];
        break;
    }

    if(isset($result['success']) && $result['success'] === false){
      header('HTTP/1.0 404 Not Found');
    }
    header('Content-Type: application/json');
    echo json_encode($result);
    $this->app->ExitXentral();
  }

  /**
   * @param string   $action       [versand|drucken|versandzentrum]
   * @param string   $documentType [angebot|auftrag|...]
   * @param int|null $projectId
   *
   * @return array Empty array if no automatic label matches
   */
  protected function FindLabelTypesForAutomaticLabel($action, $documentType, $projectId = null)
  {
    $labelArray = $this->app->DB->SelectArr(sprintf(
      "SELECT la.label_type_id FROM label_automatic AS la 
       WHERE la.action = '%s' AND la.selection = '%s' 
         AND (la.project_id = '%s' OR la.project_id = 0)",
      $this->app->DB->real_escape_string($action),
      $this->app->DB->real_escape_string($documentType),
      $this->app->DB->real_escape_string((int)$projectId)
    ));

    if ($labelArray === null) {
      return [];
    }

    $labelTypeIds = [];
    foreach ($labelArray as $labelRow) {
      $labelTypeIds[] = (int)$labelRow['label_type_id'];
    }

    return $labelTypeIds;
  }

  /**
   * @return array
   */
  protected function GetLabelGroups()
  {
    $groupArray = $this->app->DB->SelectArr(
      "SELECT lg.id, lg.group_table, lg.title
       FROM label_group AS lg WHERE 1 ORDER BY lg.group_table"
    );
    if ($groupArray === null) {
      return [];
    }

    return $groupArray;
  }

  /**
   * @param int    $labelTypeId
   * @param string $referenceTable
   * @param int    $referenceId
   *
   * @return int|null ID des angelegten Datenbank-Eintrags
   */
  protected function CreateDataTableLabel($labelTypeId, $referenceTable, $referenceId)
  {
    $sql = sprintf(
      'INSERT INTO label_reference (`id`, `label_type_id`, `reference_table`, `reference_id`, `created_at`) 
       VALUES (NULL, \'%s\',\'%s\',\'%s\', CURRENT_TIMESTAMP)',
      $this->app->DB->real_escape_string((int)$labelTypeId),
      $this->app->DB->real_escape_string($referenceTable),
      $this->app->DB->real_escape_string((int)$referenceId)
    );

    $this->app->DB->Insert($sql);
    $insertId = (int)$this->app->DB->GetInsertID();

    if ($insertId === 0) {
      return null;
    }

    return $insertId;
  }

  /**
   * @param int $id
   *
   * @return array
   */
  protected function AjaxGetLabelType($id)
  {
    $id = (int)$id;
    $data = $this->app->DB->SelectArr(
      "SELECT lt.id, lt.type, lt.title, lt.hexcolor, lt.label_group_id AS group_id
       FROM label_type AS lt
       LEFT JOIN label_group AS lg ON lt.label_group_id = lg.id
       WHERE lt.id = '{$id}' 
       LIMIT 1"
    );
    if(empty($data)){
      return ['success' => false, 'error' => sprintf('Label ID%s wurde nicht gefunden.', $id)];
    }

    $data = reset($data);

    return ['success' => true, 'data' => $data];
  }

  /**
   * @param int $id
   *
   * @return array
   */
  protected function AjaxDeleteLabelType($id)
  {
    $id = (int)$id;

    // Prüfen ob ID existiert
    $checkId = (int)$this->app->DB->Select("SELECT lt.id FROM label_type AS lt WHERE lt.id = '{$id}'");
    if($checkId === 0 || $checkId !== $id){
      return ['success' => false, 'error' => sprintf('Label-Typ kann nicht gelöscht werden. ID%s wurde nicht gefunden.', $id)];
    }

    // Prüfen ob Label-Typ in Verwendung ist
    $checkRefCount = (int)$this->app->DB->Select(
      "SELECT COUNT(lr.id) AS num FROM label_reference AS lr WHERE lr.label_type_id = '{$id}'"
    );
    if($checkRefCount <= 0){
      $checkRefCount = (int)$this->app->DB->Select(
        "SELECT COUNT(al.id) AS num FROM label_automatic AS al WHERE al.label_type_id = '{$id}'"
      );
    }
    if($checkRefCount > 0){
      return ['success' => false, 'error' => 'Label-Typ kann nicht gelöscht werden. Es existieren noch Verknüpfungen.'];
    }

    $this->app->DB->Delete("DELETE FROM label_type WHERE id = '{$id}' LIMIT 1");

    return ['success' => true];
  }

  /**
   * @param int    $id
   * @param string $type
   * @param string $title
   * @param string $hexColor
   * @param int    $groupId
   *
   * @return array
   */
  protected function AjaxSaveLabelType($id, $type, $title, $hexColor, $groupId = 0)
  {
    $id = (int)$id;
    $groupId = (int)$groupId;
    try {
      $result = ['success' => false, 'error' => 'Unbekannter Fehler'];

      if($id === 0){
        // Neuen Eintrag anlegen
        $this->ValidateFormData($type, $title, $hexColor, $groupId, null);
        $this->app->DB->Insert(
          "INSERT INTO label_type (id, label_group_id, type, title, hexcolor, created_at) 
           VALUES (NULL, '{$groupId}', '{$type}', '{$title}', '{$hexColor}', CURRENT_TIMESTAMP)"
        );
        $result = ['success' => true];
      }

      if($id > 0){
        // Vorhandenen Eintrag bearbeiten
        $this->ValidateFormData($type, $title, $hexColor, $groupId, $id);
        $this->app->DB->Update(
          "UPDATE label_type 
           SET label_group_id = '{$groupId}', type = '{$type}', title = '{$title}', 
               hexcolor = '{$hexColor}', updated_at = CURRENT_TIMESTAMP 
           WHERE id = '{$id}' LIMIT 1"
        );
        $result = ['success' => true];
      }
    } catch (RuntimeException $exception) {
      $result = ['success' => false, 'error' => $exception->getMessage()];
    }

    return $result;
  }


  /**
   * @param int $id
   *
   * @return array
   */
  protected function AjaxGetAutomaticLabel($id)
  {
    $id = (int)$id;
    $data = $this->app->DB->SelectArr(
      "SELECT al.id, t.type AS labelname, al.action, al.selection, p.abkuerzung AS project
       FROM label_automatic AS al 
       LEFT JOIN label_type t ON al.label_type_id = t.id 
       LEFT JOIN projekt AS p ON al.project_id = p.id 
       WHERE al.id = '{$id}' LIMIT 1"
    );
    if(empty($data)){
      return ['success' => false, 'error' => sprintf('Label ID%s wurde nicht gefunden.', $id)];
    }

    $data = reset($data);

    return ['success' => true, 'data' => $data];
  }

  /**
   * @param int $id
   *
   * @return array
   */
  protected function AjaxDeleteAutomaticLabel($id)
  {
    $id = (int)$id;

    // Prüfen ob ID existiert
    $checkId = (int)$this->app->DB->Select("SELECT al.id FROM label_automatic AS al WHERE al.id = '{$id}'");
    if($checkId === 0 || $checkId !== $id){
      return ['success' => false, 'error' => sprintf('Automatisches Label kann nicht gelöscht werden. ID%s wurde nicht gefunden.', $id)];
    }

    $this->app->DB->Delete("DELETE FROM label_automatic  WHERE id = '{$id}' LIMIT 1");

    return ['success' => true];
  }

  /**
   * @param int    $id
   * @param string $labelName
   * @param string $action
   * @param string $selection
   * @param string $project
   *
   * @return array
   */
  protected function AjaxSaveAutomaticLabel($id, $labelName, $action, $selection, $project)
  {
    $id = (int)$id;
    try {
      $result = ['success' => false, 'error' => 'Unbekannter Fehler'];

      if($id === 0){
        // Neuen Eintrag anlegen

        if($project == ''){
          $projectId = 0;
        }else{
          $project = explode(' ', $project);
          $project = $project[0];
          $projectId = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '{$project}' LIMIT 1");
        }

        $typeId = $this->app->DB->Select("SELECT id FROM label_type WHERE type = '$labelName' LIMIT 1");

        $this->ValidateFormDataAutomaticLabel($typeId, $action, $selection, $projectId, null);
        $this->app->DB->Insert(
          "INSERT INTO label_automatic (id, label_type_id, action, selection, project_id) 
           VALUES (NULL, '{$typeId}', '{$action}', '{$selection}', '{$projectId}')"
        );
        $result = ['success' => true];
      }

      if($id > 0){
        // Vorhandenen Eintrag bearbeiten
        if($project == ''){
          $projectId = 0;
        }else{
          $project = explode(' ', $project);
          $project = $project[0];
          $projectId = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung = '{$project}' LIMIT 1");
        }

        $typeId = $this->app->DB->Select("SELECT id FROM label_type WHERE type = '$labelName' LIMIT 1");

        $this->ValidateFormDataAutomaticLabel($typeId, $action, $selection, $projectId, $id);
        $this->app->DB->Update(
          "UPDATE label_automatic 
           SET label_type_id = '{$typeId}', action = '{$action}', selection = '{$selection}', project_id = '{$projectId}' 
           WHERE id = '{$id}' LIMIT 1"
        );
        $result = ['success' => true];
      }
    } catch (RuntimeException $exception) {
      $result = ['success' => false, 'error' => $exception->getMessage()];
    }

    return $result;
  }


  /**
   * @param string   $type
   * @param string   $title
   * @param string   $hexColor
   * @param int      $groupId
   * @param int|null $id
   *
   * @return void
   * @throws RuntimeException
   *
   */
  protected function ValidateFormData($type, $title, $hexColor, $groupId, $id = null)
  {
    if($id !== null){
      $id = (int)$id;
      $check = (int)$this->app->DB->Select("SELECT lt.id FROM label_type AS lt WHERE lt.id = '{$id}'");
      if($check === 0 || $check !== $id){
        throw new RuntimeException(sprintf('Label-Typ ID%s wurde nicht gefunden.', $id));
      }
    }

    $groupId = (int)$groupId;
    if($groupId !== 0){
      $checkGroup = (int)$this->app->DB->Select("SELECT lg.id FROM label_group AS lg WHERE lg.id = '{$groupId}'");
      if($checkGroup === 0 || $checkGroup !== $groupId){
        throw new RuntimeException(sprintf('Label-Gruppe ID%s wurde nicht gefunden.', $groupId));
      }
    }

    if(empty(trim($title))){
      throw new RuntimeException('Die Bezeichung darf nicht leer sein.');
    }

    $typeFiltered = preg_replace('/[^a-z0-9]+/', '', $type);
    if(empty(trim($type))){
      throw new RuntimeException('Die Kennung darf nicht leer sein.');
    }
    if($type !== $typeFiltered){
      throw new RuntimeException('Die Kennung enthält ungültige Zeichen. Es sind nur Kleinbuchstaben und Zahlen erlaubt.');
    }

    $hexcolorFiltered = preg_replace('/[^\#]{1}[^a-fA-F0-9]+/', '', $hexColor);
    if(empty(trim($hexColor))){
      throw new RuntimeException('Die Farbe darf nicht leer sein.');
    }
    if($hexColor !== $hexcolorFiltered){
      throw new RuntimeException('Die Farbe enthält ungültige Zeichen: ' . htmlspecialchars($hexColor));
    }
  }

  /**
   * @param int      $typeId
   * @param string   $action
   * @param string   $selection
   * @param int      $projectId
   * @param int|null $id
   *
   * @return void
   * @throws RuntimeException
   *
   */
  protected function ValidateFormDataAutomaticLabel($typeId, $action, $selection, $projectId, $id = null)
  {
    if($id !== null){
      $id = (int)$id;
      $check = (int)$this->app->DB->Select("SELECT al.id FROM label_automatic AS al WHERE al.id = '{$id}'");
      if($check === 0 || $check !== $id){
        throw new RuntimeException(sprintf('Label ID%s wurde nicht gefunden.', $id));
      }
    }

    if(empty(trim($typeId)) || $typeId == 0){
      throw new RuntimeException('Bitte Label auswählen.');
    }

    if(empty(trim($action))){
      throw new RuntimeException('Die Aktion darf nicht leer sein.');
    }

    if(empty(trim($action))){
      throw new RuntimeException('Die Auswahl darf nicht leer sein.');
    }

  }
}

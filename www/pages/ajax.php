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
use Xentral\Components\Http\Request;
use Xentral\Components\Util\StringUtil;
use Xentral\Modules\Label\Exception\LabelExceptionInterface;
use Xentral\Modules\MandatoryFields\Exception\MandatoryFieldNotFoundException;
use Xentral\Modules\MandatoryFields\Exception\UnknownTypeException;

class Ajax {
  public $app;

  /**
   * Ajax constructor.
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

    $this->app->ActionHandler("filter","AjaxFilter");
    $this->app->ActionHandler("table","AjaxTable");
    $this->app->ActionHandler("labels","AjaxLabels");
    $this->app->ActionHandler("validator","AjaxValidator");
    $this->app->ActionHandler("ansprechpartner","AjaxAnsprechpartner");
    $this->app->ActionHandler("lieferadresse","AjaxLieferadresse");
    $this->app->ActionHandler("verzolladresse","AjaxVerzolladresse");
    $this->app->ActionHandler("adressestammdaten","AjaxAdresseStammdaten");
    $this->app->ActionHandler("tooltipsuche","AjaxTooltipSuche");
    $this->app->ActionHandler("tableposition","AjaxTablePosition");
    $this->app->ActionHandler("tablefilter", "AjaxTableFilter");
    $this->app->ActionHandler("articlematrixselection", "AjaxArticleMatrixSelection");
    $this->app->ActionHandler("moduleunlock", "AjaxModuleUnlock");
    $this->app->ActionHandler("thumbnail", "AjaxThumbnail");
    $this->app->ActionHandler("autosavekonfiguration", "AjaxAutoSaveKonfiguration");
    $this->app->ActionHandler("autosaveuserparameter", "AjaxAutoSaveUserParameter");
    $this->app->ActionHandler("getuserparameter","AjaxGetUserParameter");
    $this->app->ActionHandler("getdateititel","AjaxGetDateiTitel");
    $this->app->ActionHandler("editdateititel","AjaxEditDateiTitel");
    $this->app->ActionHandler("profilbild","AjaxProfilbild");
    $this->app->ActionHandler("getgewicht","AjaxGetGewicht");
    $this->app->ActionHandler("upload","AjaxUpload");
    $this->app->ActionHandler("sidebar","AjaxSidebar");
    $this->app->ActionHandler("livetable","AjaxLiveTable");
    $this->app->ActionHandlerListen($app);
  }

  /**
   * @return JsonResponse
   */
  public function AjaxSidebar(): JsonResponse
  {
      $userId = $this->app->User->GetID();
      $cmd = $this->app->Secure->GetGET('cmd');

      switch ($cmd) {
          case 'set_collapsed':
              $state = $this->app->Secure->GetGET('value') === 'true';
              /** @var Xentral\Modules\User\Service\UserConfigService $userConfig */
              $userConfig = $this->app->Container->get('UserConfigService');
              $userConfig->set('sidebar_collapsed', $state,$userId);
              $data = ['success' => true, 'collapsed' => $state];
              break;

          default:
              $data = ['success' => false, 'error' => 'Incomplete request'];
              break;
      }

      return new JsonResponse(
        $data,
        $data['success'] === false ? JsonResponse::HTTP_BAD_REQUEST : JsonResponse::HTTP_OK
      );
  }

  public function AjaxUpload()
  {
    $fromUrl = $this->app->Secure->GetGET('fromurl');
    $fromUrl = pathinfo($fromUrl);
    $fromUrl = $fromUrl['basename'];
    if(strpos($fromUrl, 'index.php?') === 0) {
      $fromUrl = substr($fromUrl, 10);
    }
    $fromUrl = explode('&', $fromUrl);
    $parts = [];
    foreach($fromUrl as $urlpart) {
      $urlpartA = explode('=', $urlpart);
      $parts[$urlpartA[0]] = isset($urlpartA[1])?$urlpartA[1]:'';
    }
    if(!empty($_FILES)) {
      $this->app->erp->RunHook('ajaxupload', 1, $parts);
    }

    header('Content-Type: application/json');
    echo json_encode([]);
    $this->app->ExitXentral();
  }

  public function AjaxGetGewicht()
  {
    $seriennummer = $this->app->Secure->GetPOST('seriennummer');
    ///$mindestgewicht = (float)$this->app->Secure->GetPOST('mindestgewicht');
    $gewicht = str_replace(',','.',$this->app->erp->GetAdapterboxAPIWaage($seriennummer));
    if(!is_numeric($gewicht)) {
      $gewicht = 0;
    }
    //if($gewicht < $mindestgewicht)$gewicht = $mindestgewicht;
    echo json_encode(array('gewicht'=>number_format($gewicht ,1,'.','')));
    $this->app->ExitXentral();
  }

  public function AjaxLabels()
  {
    /** @var \Xentral\Modules\Label\LabelModule $labelModule */
    $labelModule = $this->app->Container->get('LabelModule');

    $cmd = $this->app->Secure->GetGET('cmd');
    switch ($cmd) {

      case 'collect':
        // Aktive Labels für eine DataTable-Seite abrufen
        $collection = $this->app->Secure->GetPOST('collection');

        $data = $this->FindLabelsByCollection($collection);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
        $this->app->erp->ExitWawi();
        break;

      case 'list':
        // Label-Typen auflisten + Zugewiesene Typen markieren
        $referenceId = (int)$this->app->Secure->GetPOST('reference_id');
        $referenceTable = $this->app->Secure->GetPOST('reference_table');

        $labelTypes = $this->FindLabelTypesByReference($referenceTable, $referenceId);
        header('Content-Type: application/json');
        echo json_encode($labelTypes);
        $this->app->erp->ExitWawi();
        break;

      case 'assign':
        // Label-Zuweisung erstellen
        $referenceId = (int)$this->app->Secure->GetPOST('reference_id');
        $referenceTable = $this->app->Secure->GetPOST('reference_table');
        $labelType = $this->app->Secure->GetPOST('type');

        try {
          $labelModule->assignLabel($referenceTable, $referenceId, $labelType);
        } catch (LabelExceptionInterface $exception) {
          header('HTTP/1.1 404 Not Found');
          header('Content-Type: application/json');
          echo json_encode(['success' => false, 'error' => 'Zuweisung nicht möglich. Fehler: ' . $exception->getMessage()]);
          $this->app->erp->ExitWawi();
          return;
        }

        // Ausgabe
        $data = $this->FindLabelsByReference($referenceTable, $referenceId);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
        $this->app->erp->ExitWawi();
        break;

      case 'unassign':
        // Label-Zuweisung löschen
        $referenceId = (int)$this->app->Secure->GetPOST('reference_id');
        $referenceTable = $this->app->Secure->GetPOST('reference_table');
        $labelType = $this->app->Secure->GetPOST('type');

        try {
          $labelModule->unassignLabel($referenceTable, $referenceId, $labelType);
        } catch (LabelExceptionInterface $exception) {
          header('HTTP/1.1 404 Not Found');
          header('Content-Type: application/json');
          echo json_encode(['success' => false, 'error' => 'Zuweisung löschen nicht möglich. Fehler: ' . $exception->getMessage()]);
          $this->app->erp->ExitWawi();
          return;
        }

        // Ausgabe
        $data = $this->FindLabelsByReference($referenceTable, $referenceId);
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $data]);
        $this->app->erp->ExitWawi();
        break;
    }
  }

  /**
   * @param string $referenceTable
   * @param int    $referenceId
   *
   * @return array
   */
  protected function FindLabelsByReference($referenceTable, $referenceId)
  {
    /** @var \Xentral\Modules\Label\LabelModule $labelModule */
    $labelModule = $this->app->Container->get('LabelModule');
    $labels = $labelModule->findLabelsByReference($referenceTable, $referenceId);

    $target = sprintf('labels-%s-%s', $referenceTable, $referenceId);
    $result = [$target => []];

    foreach ($labels as $item) {
      $item['target'] = $target;
      $item['title'] = htmlspecialchars($item['title']);
      $item['bgcolor'] = $item['hexcolor'];
      unset($item['hexcolor']);
      unset($item['id']);

      $result[$target][] = $item;
    }

    return $result;
  }

  /**
   * @param array $collection
   *
   * @return array
   */
  protected function FindLabelsByCollection($collection)
  {
    /** @var \Xentral\Modules\Label\LabelModule $labelModule */
    $labelModule = $this->app->Container->get('LabelModule');

    $result = [];
    foreach ($collection as $referenceTable => $referenceIds) {
      $referenceTable = (string)$referenceTable;
      if (empty($referenceTable)) {
        continue;
      }

      // Für jede angefragte Referenz ein Ergebnis liefern; Leeres Ergebnis als Default
      foreach ($referenceIds as $referenceId) {
        $target = sprintf('labels-%s-%s', $referenceTable, $referenceId);
        $result[$target] = [];
      }

      // Label-Gruppen anlegen
      $labelGroupId = $this->app->DB->Select(
        "SELECT lg.id FROM label_group AS lg WHERE lg.group_table = '{$referenceTable}'"
      );
      if (empty($labelGroupId)) {
        $groupTitle = ucwords($referenceTable);
        $this->app->DB->Insert(
          "INSERT INTO label_group (id, group_table, title, created_at) 
               VALUES (NULL, '{$referenceTable}', '{$groupTitle}', CURRENT_TIMESTAMP)"
        );
      }

      $labels = $labelModule->findLabelsByReferences($referenceTable, $referenceIds);
      foreach ($labels as $item) {
        $target = sprintf('labels-%s-%s', $item['reference_table'], $item['reference_id']);
        if (!isset($result[$target])) {
          $result[$target] = [];
        }
        $item['target'] = $target;
        $item['title'] = htmlspecialchars($item['title']);
        $item['bgcolor'] = $item['hexcolor'];
        $item['referenceTable'] = $item['reference_table'];
        $item['referenceId'] = $item['reference_id'];
        unset($item['reference_table']);
        unset($item['reference_id']);
        unset($item['hexcolor']);
        unset($item['id']);

        $result[$target][] = $item;
      }
    }

    return $result;
  }

  /**
   * @param string $referenceTable
   * @param int    $referenceId
   *
   * @return array
   */
  protected function FindLabelTypesByReference($referenceTable, $referenceId)
  {
    /** @var \Xentral\Modules\Label\LabelModule $labelModule */
    $labelModule = $this->app->Container->get('LabelModule');
    $labelTypes = $labelModule->findLabelTypesByReference($referenceTable, $referenceId);

    foreach ($labelTypes as &$labelType) {
      $labelType['id'] = (int)$labelType['id'];
      $labelType['target'] = 'labels-' . $referenceTable . '-' . $referenceId;
      $labelType['selected'] = !empty($labelType['label_id']);
      if ((int)$labelType['label_id'] > 0) {
        $labelType['key'] = 'label-' . (int)$labelType['label_id'];
      }
      $labelType['bgcolor'] = $labelType['hexcolor'];
      $labelType['referenceTable'] = $referenceTable;
      $labelType['referenceId'] = $referenceId;
      unset($labelType['hexcolor']);
    }

    return $labelTypes;
  }

  public function AjaxValidator()
  {
    $rule = $this->app->Secure->GetPOST('rule');
    $value = $this->app->Secure->GetPOST('value');
    $mandatoryId = (int)$this->app->Secure->GetPOST('mandatoryid');

    /** @var \Xentral\Modules\MandatoryFields\MandatoryFieldsModule $mandatoryFields */
    $mandatoryFields = $this->app->Container->get('MandatoryFieldsModule');

    try{
      $data = $mandatoryFields->validate($rule,$value,$mandatoryId)->toArray();
    } catch(UnknownTypeException $e){
      $data = ['error' => true, 'message' => 'Validatorregel nicht gültig.'];
    }  catch(MandatoryFieldNotFoundException $e){
      $data = ['error' => true, 'message' => 'Die Validierungsregel konnte nicht gefunden werden.'];
    }
    header('Content-Type: application/json');
    echo json_encode($data);
    $this->app->ExitXentral();
  }

  public function AjaxGetDateiTitel()
  {
    $status = 0;
    $cmds = $this->CmdList();
    $cmd = $this->app->Secure->GetPOST('typ');
    $data = null;
    if($this->app->erp->RechteVorhanden($cmd, 'dateien'))
    {
      $id = $this->app->Secure->GetPOST('id');
      $objekt = $this->app->Secure->GetPOST('typ');
      $parameter = $this->app->Secure->GetPOST('parameter');
      if($objekt === 'adresse'){
        $objekt = 'Adressen';
      }

      $data = $this->app->DB->SelectRow(
        "SELECT d.*, s.subjekt 
        FROM datei AS d 
        LEFT JOIN datei_stichwoerter AS s ON d.id=s.datei 
        LEFT JOIN datei_version AS v ON v.datei=d.id 
        WHERE s.objekt LIKE '$objekt' AND s.parameter='$parameter' AND d.geloescht=0 AND d.id = '$id' 
        LIMIT 1"
      );


      $module = strtolower($objekt);
      if($module === 'adressen'){
        $module = 'adresse';
      }

      $typen = $this->app->erp->getDateiTypen($module);
      $found = false;
      foreach($typen as $typ) {
        if($typ['wert'] === $data['subjekt']) {
          $found = true;
          break;
        }
      }
      $subjekthtml = '';
      if(!$found) {
        $subjekthtml = '<option value="'.$data['subjekt'].'">'.$data['subjekt'].'</option>';
      }
      foreach($typen as $typ) {
        $subjekthtml .= '<option value="'.$typ['wert'].'">'.$typ['beschriftung'].'</option>';
      }
      /*
      $subjekthtml = '<option value="Sonstige">Sonstige Datei</option><option value="anhang">Anhang</option><option value="Shopbild">Standard Artikelbild (Shopbild)</option><option value="Gruppenbild">Standard Gruppenbild</option><option value="Etikettenbild">Etikettenbild</option><option value="Bild">Sonstiges Bild</option><option value="Briefpapier1">Briefpapier Seite 1</option><option value="Briefpapier2">Briefpapier Seite 2</option><option value="Datenblatt">Datenblatt</option><option value="Druckbild">Druckbild (300dpi)</option><option value="Zertifikat">Zertifikat Anhang (PDF)</option>';
      if($module==='adresse')
      {
        $subjekthtml .= '<option value="Profilbild">Profilbild</option>';
      }

      if($module!='')
      {
        $tmp = $this->app->DB->SelectArr("SELECT * FROM datei_stichwortvorlagen WHERE modul='$module' ORDER by beschriftung");
        $ctmp = $tmp?count($tmp):0;
        for($i=0;$i<$ctmp;$i++) {
          $subjekthtml .= '<option value="' . $tmp[$i]['beschriftung'] . '">' . $tmp[$i]['beschriftung'] . '</option>';
        }
      }

      $tmp = $this->app->DB->SelectArr("SELECT * FROM datei_stichwortvorlagen WHERE modul='' ORDER by beschriftung");
      $ctmp = $tmp?count($tmp):0;
      for($i=0;$i<$ctmp;$i++) {
        $subjekthtml .= '<option value="' . $tmp[$i]['beschriftung'] . '">' . $tmp[$i]['beschriftung'] . '</option>';
      }
      */

      if($data){
        $data['subjekthtml'] = $subjekthtml;
      }

    }
    
    echo json_encode($data);
    exit;
  }

  public function AjaxEditDateiTitel()
  {
    $status = 0;
    $cmds = $this->CmdList();
    $cmd = $this->app->Secure->GetPOST('typ');
    $data = null;
    if($this->app->erp->RechteVorhanden($cmd, 'dateien'))
    {
      $id = $this->app->Secure->GetPOST('id');
      $objekt = $this->app->Secure->GetPOST('typ');
      $parameter = $this->app->Secure->GetPOST('parameter');
      $titel = $this->app->Secure->GetPOST('titel');
      $beschreibung = $this->app->Secure->GetPOST('beschreibung');
      $subjekt = $this->app->Secure->GetPOST('subjekt');
      if($objekt == 'adresse')
      {
        $objekt = 'Adressen';
      }
      $ersteller = $this->app->DB->real_escape_string($this->app->User->GetName());
      $datei = $this->app->DB->SelectArr("SELECT d.id, s.id as sid FROM datei d LEFT JOIN datei_stichwoerter s ON d.id=s.datei LEFT JOIN datei_version v ON v.datei=d.id WHERE s.objekt LIKE '$objekt' AND s.parameter='$parameter' AND d.geloescht=0 AND d.id = '$id' LIMIT 1");
      if($datei)
      {
        $sid = $datei[0]['sid'];
        if($subjekt && $sid)
        {
          $this->app->DB->Update("UPDATE datei_stichwoerter SET subjekt = '".$this->app->DB->real_escape_string($subjekt)."' WHERE id = '$sid' LIMIT 1");
        }
        $this->app->DB->Update("UPDATE datei SET titel = '$titel', beschreibung = '$beschreibung' WHERE id = '$id' LIMIT 1");
        if(!empty($_FILES['datei']) && $_FILES['datei']['tmp_name']!='')
        {
          $dateiname = $_FILES['datei']['name'];
          $this->app->erp->AddDateiVersion($id,$ersteller,$dateiname, $beschreibung,$_FILES['datei']['tmp_name']);
        }
        $status = 1;
      }
    }

    echo json_encode(array('status'=>$status));
    exit;
  }
  
  protected function CmdList()
  {
    return array('artikel','adresse','angebot','auftrag','rechnung','gutschrift','lieferschein','bestellung','projekt','produktion','anfrage','reisekosten','kalkulation','serviceauftrag','verbindlichkeit','kasse','geschaeftsbrief_vorlagen','wiedervorlage','wiki');
  }

  /**
   * @param int         $userId
   * @param string|null $alt
   * @param string|null $imgClass
   * @param int|null    $widthHeight
   *
   * @return string
   */
  public function getProfileHtml($userId, $alt = null, $imgClass=null, $widthHeight = null): string
  {
    $userId = (int)$userId;
    $addressId = 0;
    $shortUserName = '';
    $user = $userId <= 0?null:$this->app->DB->SelectRow(
      sprintf(
        "SELECT u.adresse, u.`username`
        FROM `user` AS `u` 
        WHERE u.id=%d 
        LIMIT 1",
        $userId
      )
    );
    if(!empty($user)) {
      $addressId = $user['adresse'];
      $shortUserName = substr($user['username'],0,2);
    }

    $fileId = $this->getFileVersionFromProfileImage($addressId);
    if($this->getProfilePictureFromFileVersionId($fileId) !== null) {
      $imgString = sprintf(
        '<img src="index.php?module=ajax&action=profilbild&id=%d" ',
        $userId
      );
      if($widthHeight !== null) {
        $imgString .= sprintf(' width="%s" heigth="%s" ', $widthHeight, $widthHeight);
      }
      if($alt !== null) {
        $imgString .= sprintf(' alt="%s"', $alt);
      }
      if($imgClass !== null) {
        $imgString .= sprintf(' class="%s"', $imgClass);
      }

      return $imgString. ' />';
    }

    return sprintf('%s', $shortUserName);
  }

  /**
   * @param int $addressId
   *
   * @return int|null
   */
  public function getFileVersionFromProfileImage($addressId): ?int
  {
    $addressId = (int)$addressId;
    if($addressId <= 0) {
      return null;
    }
    $fileVersionId = (int)$this->app->DB->Select(
      sprintf(
        "SELECT dv.id 
        FROM `datei_stichwoerter` AS `ds` 
        INNER JOIN `datei` AS `d` ON ds.datei = d.id 
        INNER JOIN `datei_version` AS `dv` ON dv.datei = d.id 
        WHERE d.geloescht = 0 AND objekt LIKE 'Adressen' AND parameter = '%d' AND subjekt LIKE 'Profilbild' 
        ORDER BY dv.id DESC 
        LIMIT 1",
        $addressId
      )
    );
    if($fileVersionId <= 0) {
      return null;
    }

    return $fileVersionId;
  }

  /**
   * @param int $fileVersionId
   *
   * @return bool
   */
  public function getProfilePictureFromFileVersionId($fileVersionId): ?array
  {
    $fileVersionId = (int)$fileVersionId;
    if($fileVersionId <= 0) {
      return null;
    }
    $userdata = isset($this->app->Conf->WFuserdata)?$this->app->Conf->WFuserdata:str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME'])."../userdata";
    $path = $userdata.'/dms/'.$this->app->Conf->WFdbname;
    $cachefolder = $path.'/cache';
    $path = $this->app->erp->GetDMSPath($fileVersionId, $path);
    $cachefolder = $this->app->erp->GetDMSPath($fileVersionId.'_100_100', $cachefolder, true);
    if(!file_exists($cachefolder.'/'.$fileVersionId.'_100_100')) {
      if(file_exists($path.'/'.$fileVersionId)) {
        $type = mime_content_type($path.'/'.$fileVersionId);
        switch($type) {
          case 'image/jpg':
          case 'image/jpeg':
            $img = new image($this->app);
            $str = $img->scaledPicByFileId($fileVersionId, 100, 100);
            if((string)$str === '') {
              return null;
            }
            return [
              'header' => 'Content-type: image/jpg',
              'image' => $str,
            ];
            break;
          case 'image/png':
            $img = new image($this->app);
            $str = $img->scaledPicByFileId($fileVersionId, 100, 100);
            if((string)$str === '') {
              return null;
            }
            return [
              'header' => 'Content-type: image/png',
              'image' => $str,
            ];
            break;
          case 'image/gif':
            $img = new image($this->app);
            $str = $img->scaledPicByFileId($fileVersionId, 100, 100);
            if((string)$str === '') {
              return null;
            }
            return [
              'header' => 'Content-type: image/gif',
              'image' => $str,
            ];
            break;
          case 'application/pdf':
            $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/pdf.svg');
            if((string)$str === '') {
              return null;
            }

            return [
              'header' => 'Content-type: image/svg',
              'picture' => $str,
            ];
            break;
        }
      }
    }
    if(file_exists($cachefolder.'/'.$fileVersionId.'_100_100')){
      $type = mime_content_type($cachefolder . '/' . $fileVersionId . '_100_100');
      if(strpos($type, 'image') !== false){
        $str = file_get_contents($cachefolder . '/' . $fileVersionId . '_100_100');
        if((string)$str === '') {
          return null;
        }

        return [
          'header' => 'Content-type: ' . $type,
          'picture' => $str,
        ];
      }
    }

    return null;
  }

  public function AjaxProfilbild()
  {
    $userId = (int)$this->app->Secure->GetGET('id');
    $addressId = $userId === $this->app->User->GetID()
      ?$this->app->User->GetAdresse():
      (int)$this->app->DB->Select(sprintf('SELECT `adresse` FROM `user` WHERE `id` = %d', $userId));
    $dateiversion = $this->getFileVersionFromProfileImage($addressId);
    $picture =  $this->getProfilePictureFromFileVersionId($dateiversion);
    if($picture !== null) {
      header($picture['header']);
      echo $picture['picture'];
      exit;
    }

    $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/profil.png');
    header('Content-type: image/png');
    echo $str;
    exit;
  }
  
  public function AjaxThumbnail()
  {
    $cmds = $this->CmdList();
    $cmd = trim($this->app->Secure->GetGET('cmd'));
    $id = (int)$this->app->Secure->GetGET('id');

    if(!empty($cmd) && $id
      && (!in_array($cmd, $cmds) || (in_array($cmd, $cmds) && $this->app->erp->RechteVorhanden($cmd,'dateien')))) {
      $datei = $this->app->DB->SelectRow(
        sprintf(
          "SELECT dv.id, ds.parameter, dv.dateiname 
          FROM datei_version AS dv 
          INNER JOIN datei_stichwoerter ds ON ds.datei = dv.datei 
          WHERE dv.datei = %d AND (ds.objekt like '%s'".($cmd === 'adresse'?" OR ds.objekt like 'Adressen' ":'').") 
          ORDER BY  dv.datei DESC, dv.version DESC 
          LIMIT 1",
          $id, $cmd
        )
      );
      if(empty($datei)) {
        if ($this->app->erp->Firmendaten('iconset_dunkel')) {
          $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
        } else {
          $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
        }
        header('Content-type: image/png');
        echo $str;
        exit;
      }
      if(!empty($datei['parameter'])) {
        if($cmd === 'projekt') {
          if(!$this->app->erp->UserProjektRecht($datei['parameter'])) {
            if ($this->app->erp->Firmendaten('iconset_dunkel')) {
              $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
            } else {
              $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
            }
            header('Content-type: image/png');
            echo $str;
            exit;
          }
        }
        else{
          $projekt = $this->app->DB->Select(
            sprintf(
              'SELECT `projekt` FROM `%s` WHERE `id` = %d LIMIT 1',
              $cmd, $datei[0]['parameter']
            )
          );
          if(!$this->app->erp->UserProjektRecht($projekt)) {
            if ($this->app->erp->Firmendaten('iconset_dunkel')) {
              $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
            }
            else {
              $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
            }
            header('Content-type: image/png');
            echo $str;
            exit;
          }
        }
      }
      //Rechte prüfen
      
      $userdata = isset($this->app->Conf->WFuserdata)
        ?$this->app->Conf->WFuserdata
        :(str_replace('index.php', '', $_SERVER['SCRIPT_FILENAME']).'../userdata');
      $path = $userdata.'/dms/'.$this->app->Conf->WFdbname;
      $cachefolder = $path.'/cache';
      $_cachefolder = $cachefolder;
      $cachefolder = $this->app->erp->GetDMSPath($datei['id'].'_100_100', $cachefolder, true);
      if(!file_exists($cachefolder.'/'.$datei['id'].'_100_100')) {
        $cachefolder = $this->app->erp->CreateDMSPath($_cachefolder, $datei['id']);
        $datei_orig = $this->app->erp->GetDateiPfadVersion($datei['id']);
        if(file_exists($datei_orig)) {
          $type = mime_content_type($datei_orig);
          switch($type)
          {
            case 'image/jpg':
            case 'image/jpeg':
              $img = new image($this->app);
              $str = $img->scaledPicByFileId($datei['id'], 100, 100);
              header('Content-type: image/jpg');
              echo $str;
              exit;
            break;
            case 'image/png':
              $img = new image($this->app);
              $str = $img->scaledPicByFileId($datei['id'], 100, 100);
              header('Content-type: image/png');
              echo $str;
              exit;
            break;
            case 'image/gif':
              $img = new image($this->app);
              $str = $img->scaledPicByFileId($datei['id'], 100, 100);
              header('Content-type: image/gif');
              echo $str;
              exit;
            break;
            case 'application/pdf':
              $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/pdf.svg');
              header('Content-type: image/png');
              echo $str;
              exit;
            break;
            default:
              $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/pdf.svg');
              if(substr(strtolower($datei['dateiname']),-4) === '.gif'){
                header('Content-type: image/gif');
                echo $str;
                exit; 
              }
              if(substr(strtolower($datei['dateiname']),-4) === '.png'){
                header('Content-type: image/png');
                echo $str;
                exit; 
              }
              if(substr(strtolower($datei['dateiname']),-4) === '.jpg'
                || substr(strtolower($datei['dateiname']),-4) === 'jpeg'){
                header('Content-type: image/jpg');
                echo $str;
                exit;                 
              }
            break;
          }
        }
      }
      
      if(file_exists($cachefolder.'/'.$datei['id'].'_100_100')) {
        $type = is_file($path.'/'.$datei['id'])? false : mime_content_type($path.'/'.$datei['id']);
        if($type === false) {
          $type = mime_content_type($cachefolder.'/'.$datei['id'].'_100_100');
        }
        if(strpos($type,'image') !== false) {
          header('Content-type: '.$type);
          $str = file_get_contents($cachefolder.'/'.$datei['id'].'_100_100');
          echo $str;
          exit;
        }
        $str = file_get_contents($cachefolder.'/'.$datei['id'].'_100_100');
        if(substr(strtolower($datei['dateiname']),-4) === '.gif') {
          header('Content-type: image/gif');
          echo $str;
          exit;
        }
        if(substr(strtolower($datei['dateiname']),-4) === '.png') {
          header('Content-type: image/png');
          echo $str;
          exit;
        }
        if(substr(strtolower($datei['dateiname']),-4) === '.jpg'
          || substr(strtolower($datei['dateiname']),-5) === '.jpeg') {
          header('Content-type: image/jpg');
          echo $str;
          exit;
        }
      }
      else{
        if ($this->app->erp->Firmendaten('iconset_dunkel')) {
          $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
        } else {
          $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
        }
        header('Content-type: image/png');
        echo $str;
        exit;
      }
    }
    else{
      if ($this->app->erp->Firmendaten('iconset_dunkel')) {
        $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_dunkel.png');
      } else {
        $str = file_get_contents(dirname(__DIR__) . '/themes/new/images/keinbild_hell.png');
      }
      header('Content-type: image/png');
      echo $str;
    }
    exit;
  }
  
  public function AjaxModuleUnlock() {
    if($this->app->erp->RechteVorhanden('welcome','unlock') &&
      ($salt = $this->app->Secure->GetGET('salt')))
    {
      $this->app->DB->Delete("DELETE from module_lock where salt = '".$salt."'");
    }
    $this->app->erp->ExitWawi();
  }

  public function AjaxTableFilter() {

    /*header("Content-Type: text/html; charset=utf-8");*/

    $do = $this->app->Secure->GetGET('do');
    $filter = $this->app->Secure->GetGET('filter');

    switch ($do) {
      case 'getParameters':
        $params = $this->app->User->GetParameter('table_filter_' . $filter);
        echo base64_decode($params);
        break;
      case 'setParameters':
        $params = base64_encode(json_encode($_GET)); 
        $this->app->User->SetParameter('table_filter_' . $filter, $params);
        break;
      case 'clearParameters':
        $this->app->User->SetParameter('table_filter_' . $filter,'');
        break;
      default:
        return false;
        break;
    }

    $this->app->erp->ExitWawi();
  }

  public function AjaxArticleMatrixSelection()
  {
    $menge = $this->app->Secure->GetPOST('menge');
    $auswahl = $this->app->Secure->GetPOST('auswahl');
    $cmd = $this->app->Secure->GetGET('cmd');
    $vorgangsId = (int)$this->app->Secure->GetGET('id');
    $vorgangsTyp = $this->app->Secure->GetGET('typ');
    if ($vorgangsId === 0) {
      $this->app->erp->ExitWawi();
    }

    if ($cmd === 'get') {
      $articleMatrixSelection = $this->GetArticleMatrixSelection($vorgangsTyp, $vorgangsId);
      header('Content-Type: application/json');
      echo json_encode($articleMatrixSelection);
      $this->app->erp->ExitWawi();
    }

    if ($cmd === 'set') {
      // Vorhandene Auswahl laden und aktuelle Auswahl hinzufügen
      // Notwendig, da sich der Auswahlprozess über mehrere Seiten erstrecken kann.
      $articleMatrix = $this->GetArticleMatrixSelection($vorgangsTyp, $vorgangsId);

      foreach ($menge as $artikelId => $artikelAnzahl) {
        // Nur Mengen größer Null merken
        if (!empty($artikelAnzahl)) {
          $articleMatrix['menge'][(int)$artikelId] = (int)$artikelAnzahl;
        }
        // Auswahl wurde entfernt > Menge ebenfalls leeren
        if (isset($menge[$artikelId]) && !isset($auswahl[$artikelId])) {
          unset($articleMatrix['auswahl'][(int)$artikelId],$articleMatrix['menge'][(int)$artikelId]);
        }
      }
      // Nur aktive Checkboxen merken
      foreach ($auswahl as $artikelId => $artikelAuswahl) {
        if ($artikelAuswahl === 'on') {
          $articleMatrix['auswahl'][(int)$artikelId] = true;
        }
      }

      $this->SaveArticleMatrixSelection($vorgangsTyp, $vorgangsId, $articleMatrix);
      $this->app->erp->ExitWawi();
    }

    // Artikelmatrix-Auswahl zurücksetzen
    if ($cmd === "reset") {
      $this->SaveArticleMatrixSelection($vorgangsTyp, $vorgangsId, []);
      $this->app->erp->ExitWawi();
    }
  }

  protected function GetArticleMatrixSelection($vorgangsTyp, $vorgangsId)
  {
    if (empty($vorgangsTyp) || (int)$vorgangsId === 0) {
      return [
        'auswahl' => [],
        'menge' => [],
      ];
    }

    $selection = $this->app->User->GetParameter("article_matrix_selection_{$vorgangsTyp}_{$vorgangsId}");
    $matrix = json_decode($selection, true);
    if (empty($matrix)) {
      $matrix = [];
    }

    // Alter des Eintrags kontrollieren; nach 24 Stunden ohne Änderung > Eintrag verwerfen
    $yesterday = time() - (60 * 60 * 24);
    if (empty($matrix['time']) || (int)$matrix['time'] < $yesterday) {
      $this->SaveArticleMatrixSelection($vorgangsTyp, $vorgangsId, []);
      $matrix = [];
    }

    if (empty($matrix['auswahl'])) {
      $matrix['auswahl'] = [];
    }
    if (empty($matrix['menge'])) {
      $matrix['menge'] = [];
    }

    return $matrix;
  }

  protected function SaveArticleMatrixSelection($vorgangsTyp, $vorgangsId, $data = [])
  {
    if ((int)$vorgangsId === 0) {
      return;
    }
    if (empty($vorgangsTyp)) {
      return;
    }
    if (!is_array($data)) {
      $data = [];
    }

    // Aktuellen Timestamp hinzufügen/überschreiben
    $data['time'] = time();

    // Auswahl pro User und Vorgang merken
    $this->app->User->SetParameter("article_matrix_selection_{$vorgangsTyp}_{$vorgangsId}", json_encode($data));
  }

  public function AjaxTooltipSuche()
  {
    $term = $this->app->Secure->GetGET('term');

    if(is_numeric($term))
    {
      $rechnung = $this->app->DB->SelectArr("SELECT id,belegnr,soll,ist FROM rechnung WHERE belegnr='$term'");
      $gutschrift = $this->app->DB->SelectArr("SELECT id,belegnr,soll,ist FROM gutschrift WHERE belegnr='$term'");
      $auftrag = $this->app->DB->SelectArr("SELECT id,belegnr FROM auftrag WHERE belegnr='$term'");
      $internet = $this->app->DB->SelectArr("SELECT id,belegnr FROM auftrag WHERE internet='$term'");
      $kunde = $this->app->DB->SelectArr("SELECT id,name FROM adresse WHERE kundennummer='$term'");
    }
    if(!empty($rechnung) && is_array($rechnung))
    {
      foreach($rechnung as $value){
        echo '<table width="500"><tr><td>Rechnung '.$value['belegnr'].' SOLL:'.$value['soll'].' IST:'.$value['ist'].'</td></tr></table>';
      }
    }

    if(!empty($auftrag) && is_array($auftrag))
    {
      foreach($auftrag as $value){
        echo 'Auftrag '.$value['belegnr'];
      }
    }



    if(!empty($internet) && is_array($internet))
    {
      foreach($internet as $value){
        echo 'Internet Auftrag '.$value['belegnr'];
      }
    }


    /*if($internetnummer)
      echo "Internetnummer";*/


    if(!empty($kunde) && is_array($kunde))
    {
      foreach($kunde as $value){
        echo 'Kunde '.$value['name'];
      }
    }


    echo 'ENDE ';

    $this->app->erp->ExitWawi();

  }

  public function AjaxAdresseStammdaten()
  {
    $id = $this->app->Secure->GetGET('id');	
    if($id <= 0)
    {
      $this->app->erp->ExitWawi();
    }

    //name	abteilung		unterabteilung	land	strasse		ort		plz

    $values = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$id' LIMIT 1");
    if(!empty($values)){
      foreach ($values[0] as $key => $value) {
        $values[0][$key] = $this->app->erp->ReadyForPDF($value);
      }

      echo $this->app->erp->ClearDataBeforeOutput($values[0]['name'] . '#*#' . $values[0]['abteilung'] . '#*#' . $values[0]['unterabteilung'] . '#*#' . $values[0]['land'] . '#*#' . $values[0]['strasse'] . '#*#' . $values[0]['ort'] . '#*#' . $values[0]['plz'] . '#*#' . $values[0]['adresszusatz'] . '#*#' . $values[0]['ansprechpartner'] . '#*#' . $values[0]['titel'] . '#*#' . $values[0]['id'] .
        '#*#' . $values[0]['email'] .
        '#*#' . $values[0]['telefon'] .
        '#*#' . $values[0]['telfax'] .
        '#*#' . $values[0]['anschreiben'] .
        '#*#' . $values[0]['gln']
      );
    }
    $this->app->erp->ExitWawi();

  }

  public function AjaxVerzolladresse()
  {
    $id = $this->app->Secure->GetGET('id');	
    if($id <= 0)
    {
      $this->app->erp->ExitWawi();
    }

    //name	abteilung		unterabteilung	land	strasse		ort		plz

    $values = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='$id' LIMIT 1");
    if(!empty($values)){
      foreach ($values[0] as $key => $value) {
        if($key !== 'zollinformationen') {
          $values[0][$key] = $this->app->erp->ReadyForPDF($value);
        }
      }
      echo $this->app->erp->ClearDataBeforeOutput($values[0]['name'] . '#*#' . $values[0]['abteilung'] . '#*#' . $values[0]['unterabteilung'] . '#*#' . $values[0]['land'] . '#*#' . $values[0]['strasse'] . '#*#' . $values[0]['ort'] . '#*#' . $values[0]['plz'] . '#*#' . $values[0]['adresszusatz'] . '#*#' . $values[0]['ansprechpartner'] . '#*#' . $values[0]['titel'] . '#*#' . base64_encode($values[0]['zollinformationen']) . '#*#');
    }
    $this->app->erp->ExitWawi();
  }
  
  public function AjaxLieferadresse()
  {
    $id = $this->app->Secure->GetGET('id');	
    if($id <= 0)
    {
      $this->app->erp->ExitWawi();
    }

    //name	abteilung		unterabteilung	land	strasse		ort		plz

    $values = $this->app->DB->SelectArr("SELECT * FROM lieferadressen WHERE id='$id' LIMIT 1");
    if(!empty($values)){
      foreach ($values[0] as $key => $value) {
        $values[0][$key] = $this->app->erp->ReadyForPDF($value);
      }
      echo $this->app->erp->ClearDataBeforeOutput($values[0]['name'] . '#*#' . $values[0]['abteilung'] . '#*#' . $values[0]['unterabteilung'] . '#*#' . $values[0]['land'] . '#*#' . $values[0]['strasse'] . '#*#' . $values[0]['ort'] . '#*#' . $values[0]['plz'] . '#*#' . $values[0]['adresszusatz'] . '#*#' . $values[0]['ansprechpartner'] . '#*#' . $values[0]['id'] . '#*#' . $values[0]['gln'] . '#*#' . $values[0]['ustid'] . '#*#' . $values[0]['ust_befreit'] . '#*#' . $values[0]['lieferbedingung']. '#*#' . $values[0]['email']);
    }
    $this->app->erp->ExitWawi();

  }



  public function AjaxAnsprechpartner()
  {
    $id = $this->app->Secure->GetGET('id');	
    if($id <= 0)
    {
      $this->app->erp->ExitWawi();
    }
    $values = $this->app->DB->SelectArr("SELECT * FROM ansprechpartner WHERE id='$id' LIMIT 1");
    if(!empty($values[0])){
      foreach ($values[0] as $key => $value) {
        $values[0][$key] = $this->app->erp->ReadyForPDF($value);
      }
      echo $this->app->erp->ClearDataBeforeOutput($values[0]['name'] . '#*#' . $values[0]['email'] . '#*#' . $values[0]['telefon'] . '#*#' . $values[0]['telefax'] . '#*#' . $values[0]['abteilung'] . '#*#' . $values[0]['unterabteilung'] .
        '#*#' . $values[0]['land'] . '#*#' . $values[0]['strasse'] . '#*#' . $values[0]['plz'] . '#*#' . $values[0]['ort'] . '#*#' . $values[0]['adresszusatz'] . '#*#' . $values[0]['typ'] . '#*#' . $values[0]['anschreiben'] . '#*#' . $values[0]['titel'] . '#*#' . $values[0]['id']);
    }
    $this->app->erp->ExitWawi();
  }

  public function AjaxAutoSaveKonfiguration()
  {
    $name = $this->app->Secure->GetPOST('name');
    $value = $this->app->Secure->GetPOST('value');
    $this->app->erp->SetKonfigurationValue($name,base64_decode($value));
    $this->app->erp->ExitWawi();
  }

  public function AjaxAutoSaveUserParameter()
  {
    $name = $this->app->Secure->GetPOST('name');
    $value = $this->app->Secure->GetPOST('value');
    $this->app->User->SetParameter($name,base64_decode($value));
    $this->app->erp->ExitWawi();
  }


  public function AjaxGetUserParameter()
  {
    $name = $this->app->Secure->GetPOST('name');
    $names = $this->app->Secure->GetPOST('names');
    if(!empty($names))
    {
      $names = explode(',', $names);
      $elems = explode(',',$this->app->Secure->GetPOST('elems'));
      
      $values = $this->app->User->GetParameter($names);
      if(!empty($values))
      {
        foreach($values as $k => $v)
        {
          $values[$k]['elem'] = $elems[$k];
        }
      }
      echo json_encode($values);
    }else{
      echo json_encode(array('name'=>$name,'elem'=>$this->app->Secure->GetPOST('elem'),'value'=>$this->app->User->GetParameter($name)));
    }
    $this->app->erp->ExitWawi();
  }

  public function AjaxFilterWhere($term, $fields)
  {
    if(empty($fields))
    {
      return '1';
    }
    while(strpos($term,'  ') !== false)
    {
      $term = str_replace('  ',' ', $term);
    }
    $term = trim($term);
    $term2 = $term;
    $term3 = $term;
    $term2 = $this->app->erp->ConvertForDBUTF8($term);
    $term3 = $this->app->erp->ConvertForDB($term);
    $terma = explode( ' ', $term);
    $term2a = explode( ' ', $term2);
    $term3a = explode( ' ', $term3);
    if(count($terma) === 1)
    {
      $wherea = [];
      foreach($fields as $v) {
        $wherea[] = $v . " LIKE '%" . $term . "%'";
        if($term2 !== $term && $term2 !== ''){
          $wherea[] = $v . " LIKE '%" . $term2 . "%'";
        }
        if($term3 !== $term && $term3 !== $term2 && $term3!==''){
          $wherea[] = $v . " LIKE '%" . $term3 . "%'";
        }
      }
      return ' ('.implode(' OR ', $wherea).') ';
    }
    $wherea = [];
    foreach($fields as $v) {
      if(!empty($term2) && $term2 !== $term){
        $tmp = [];
        //foreach ($terma as $v2) {
          $tmp[] = $this->AjaxTableWhereBuilderArray($v, $terma, $term2a);
        //}
        $wherea[] = implode(' AND ', $tmp);
      }
      elseif(!empty($term3) && $term3 !== $term){
        $tmp = [];
        //foreach ($terma as $v2) {
          $tmp[] = $this->AjaxTableWhereBuilderArray($v, $terma, $term3a);
        //}
        $wherea[] = implode(' AND ', $tmp);
      }
      else{
        $tmp = [];
        //foreach ($terma as $v2) {
          $tmp[] = $this->AjaxTableWhereBuilderArray($v, $terma);
        //}
        $wherea[] = implode(' AND ', $tmp);
      }
    }

    return ' ('.implode(' OR ', $wherea).') ';
  }

  public function AjaxFilter()
  {
    //$term = $this->app->Secure->GetGET("term");
    $term = $this->app->Secure->GetGET('term');
    $termorig = $term;
    $rmodule = $this->app->Secure->GetGET('rmodule');
    $raction = $this->app->Secure->GetGET('raction');
    $rid = (int)$this->app->Secure->GetGET('rid');
    $pruefemodule = array('artikel','auftrag','angebot','rechnung','lieferschein','gutschrift','bestellung','produktion');
    $filter_projekt = 0;
    if($raction === 'edit' && $rid && in_array($rmodule, $pruefemodule))
    {
      $projekt = $this->app->DB->Select("SELECT projekt FROM $rmodule WHERE id = '$rid' LIMIT 1");
      if($projekt)
      {
        $eigenernummernkreis = $this->app->DB->Select("SELECT eigenernummernkreis FROM projekt WHERE id = '$projekt' LIMIT 1");
        //if($eigenernummernkreis)
        $filter_projekt = $projekt;
      }
    }    
    $term2 = $term;
    $term3 = $term;
    $term = $this->app->erp->ConvertForDBUTF8($term);
    $term2 = $this->app->erp->ConvertForDB($term2);
    if($term2=='') {
      $term2 = $term;
    }
    $term = str_replace(' ','%',$term);
    $term2 = str_replace(' ','%',$term2);
    $term3 = str_replace(' ','%',$term3);
    //$term = $this->app->erp->ConvertForDBUTF8($term);
    //$term = str_replace(' ','%',$term);
    $filtername = $this->app->Secure->GetGET('filtername');

    $term = trim($term);
    $term2 = trim($term2);

    switch($filtername)
    {
      case "adressenamegruppe":
        $gruppe = $this->app->Secure->GetGET('gruppe');
        $gruppea = explode(',',$gruppe);
        $gruppenwhere = ' 0 ';
        foreach($gruppea as $v)
        {
          if($v){
            $gruppenw[] = " ar.parameter = '$v' ";
          }
        }
        if(!empty($gruppenw))
        {
          $gruppenwhere = ' ('.implode(' OR ', $gruppenw).') ';
        }

        $felder = array('a.email','a.name');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT concat(a.id, ' ',a.name) as name2 FROM adresse a 
        INNER JOIN adresse_rolle ar ON a.id = ar.adresse AND $gruppenwhere AND ar.objekt LIKE 'Gruppe' AND (bis = '0000-00-00' OR bis >= curdate())
        WHERE ($subwhere) AND a.geloescht <> 1 ".$this->app->erp->ProjektRechte('a.projekt')."
        ORDER BY a.name LIMIT 20
        ");
        
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = "{$arr[$i]['name2']}";
        }
      break;
      
      case "adressename":
        $arr = $this->app->DB->SelectArr("SELECT a.email, (SELECT name FROM adresse a2 WHERE a2.kundennummer = a.kundennummer ".$this->app->erp->ProjektRechte('a2.projekt')." order by ".($filter_projekt?" a2.projekt = '$filter_projekt' DESC, ":"")."  projekt LIMIT 1) as name2 FROM adresse a WHERE (a.email LIKE '%$term%' OR a.name LIKE '%$term%' OR a.name LIKE '%$term2%' OR a.name LIKE '%$term3%') ".$this->app->erp->ProjektRechte('a.projekt')." GROUP by email ORDER BY a.email, name2 LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = "{$arr[$i]['name2']}";
        }
        break;
/*

select a.kundennummer, (SELECT name FROM adresse a2 WHERE a2.kundennummer = a.kundennummer order by a2.projekt = 13 DESC, a2.projekt = 0 DESC, projekt LIMIT 1) as name FROM adresse a WHERE a.kundennummer like '10500' group by a.kundennummer 
*/
      case "arbeitspaket":
        if(trim($this->app->Secure->GetGET('projekt')) != ''){
          $checkprojekt = trim($this->app->Secure->GetGET('projekt'));
        }else{
          $checkprojekt = $this->app->User->GetParameter("teilprojekt_filter");
        }
         
        if(is_numeric($checkprojekt) && $checkprojekt > 0){
          $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE id='" . $checkprojekt . "' LIMIT 1");
        }

        $limit = '';
        if($projektid <=0)
        {
          $checkprojekt = explode(' ',$checkprojekt);
          $projektid = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$checkprojekt[0]."' AND abkuerzung!='' LIMIT 1");
          if($projektid <=0){
            $limit = ' LIMIT 20 ';
          }
        }

        if($projektid > 0){
          $subwhere = " AND p.id='".$projektid."'";
        } else {
          $subwhere='';
        }

        $felder = array('p.abkuerzung', 'ap.aufgabe');
        $subwhere2 = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(ap.id,' ',p.abkuerzung,' ',ap.aufgabe) as name2 FROM arbeitspaket ap LEFT JOIN projekt p ON p.id=ap.projekt WHERE ($subwhere2) AND ap.status!='abgeschlossen' AND ap.aufgabe!='' AND p.id > 0 $subwhere ".$limit);
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = "{$arr[$i]['name2']}";
        }
        break;
      case "artikeleigenschaften":
        $subwhere = $this->app->erp->ProjektRechte('e.projekt');
        $felder = array('name');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT name FROM artikeleigenschaften e WHERE ($subwhere) AND geloescht <> 1 LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = "{$arr[$i]['name']}";
        }
        break;
      case "artikeleigenschaftenwerte":
        //$arr = $this->app->DB->SelectArr("SELECT DISTINCT wert FROM artikeleigenschaftenwerte WHERE wert LIKE '%$term%' OR wert LIKE '%$term2%' OR wert LIKE '%$term3%' LIMIT 20");
        //$arr2 = $this->app->DB->SelectArr("SELECT DISTINCT property_value_from FROM article_property_translation WHERE (property_value_from LIKE '%$term%' OR property_value_from LIKE '%$term2%' OR property_value_from LIKE '%$term3') AND language_from = 'DE' LIMIT 20");


        $arr = $this->app->DB->SelectArr("(SELECT DISTINCT wert FROM artikeleigenschaftenwerte WHERE wert LIKE '%$term%' OR wert LIKE '%$term2%' OR wert LIKE '%$term3%' LIMIT 20) UNION 
          (SELECT DISTINCT property_value_from as wert FROM article_property_translation WHERE (property_value_from LIKE '%$term%' OR property_value_from LIKE '%$term2%' OR property_value_from LIKE '%$term3') AND language_from = 'DE' LIMIT 20) ORDER BY wert");

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++){
          $newarr[] = "{$arr[$i]['wert']}";
        }
        break;
      case "matrixprodukt_uebersetzungen":
        $arr = $this->app->DB->SelectArr("(SELECT DISTINCT name_from AS name FROM matrix_article_translation WHERE name_from LIKE '%$term%' OR name_from LIKE '%$term2%' OR name_from LIKE '%$term3%' LIMIT 20) 
        UNION (SELECT DISTINCT name_to AS name FROM matrix_article_translation WHERE name_to LIKE '%$term%' OR name_to LIKE '%$term2%' OR name_to LIKE '%$term3%' LIMIT 20)
        UNION (SELECT DISTINCT name FROM matrixprodukt_eigenschaftengruppen WHERE name LIKE '%$term%' OR name LIKE '%$term2%' OR name LIKE '%$term3%' LIMIT 20)");

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++){
          $newarr[] = "{$arr[$i]['name']}";
        }
        break;
      case "matrixprodukt_optionen_uebersetzungen":
        $arr = $this->app->DB->SelectArr("(SELECT DISTINCT name_from AS name FROM matrix_article_options_translation WHERE name_from LIKE '%$term%' OR name_from LIKE '%$term2%' OR name_from LIKE '%$term3%' LIMIT 10)
        UNION (SELECT DISTINCT name_to AS name FROM matrix_article_options_translation WHERE name_to LIKE '%$term%' OR name_to LIKE '%$term2%' OR name_to LIKE '%$term3%' LIMIT 20)
        UNION (SELECT DISTINCT name FROM matrixprodukt_eigenschaftenoptionen WHERE name LIKE '%$term%' OR name LIKE '%$term2%' OR name LIKE '%$term3%' LIMIT 20)");

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++){
          $newarr[] = "{$arr[$i]['name']}";
        }
        break;
      case "drucker":
        $felder = array('name');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT name FROM drucker WHERE $subwhere LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = "{$arr[$i]['name']}";
        }
        break;
      case "wiedervorlage_stages":
        $felder = array('ws.name','ws.kurzbezeichnung');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr(
          "SELECT CONCAT(ws.id, ' ', ws.kurzbezeichnung, ' (', IFNULL(wv.shortname, 'Standard'), ' - ', ws.name, ')') AS `name2` 
           FROM `wiedervorlage_stages` AS `ws`
           LEFT JOIN `wiedervorlage_view` AS `wv` ON ws.view = wv.id
           WHERE $subwhere 
           ORDER BY ws.view, ws.kurzbezeichnung LIMIT 20"
        );
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = "{$arr[$i]['name2']}";
        }
        break;
      case "wiedervorlage_view":
        $felder = array('name', 'shortname');
        $subwhere = $this->AjaxFilterWhere($termorig, $felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(id, ' ', shortname, ' (',name,')') AS name2 FROM wiedervorlage_view WHERE $subwhere ORDER BY shortname LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++){
          $newarr[] = "{$arr[$i]['name2']}";
        }
        break;
      case "etiketten":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(id,' ',name) as name2 FROM etiketten WHERE name LIKE '%$term%' OR name LIKE '%$term2%' OR name LIKE '%$term3%' LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = "{$arr[$i]['name2']}";
        break;


      case "laender":
        $laender = $this->app->erp->GetSelectLaenderliste();
        $_term = explode(',',$term);
        $_term = $_term[count($_term)-1];
        if($laender){
          foreach($laender as $key => $value)
          {
            if(stripos($key, $_term) !== false || stripos($value, $_term) !== false)
            {
              $newarr[] = $key.' '.$value;
            }
          }
        }
      break;
      case "artikelname":
        $felder = array('CONCAT(nummer,\' \',name_de)','nummer','name_de');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT name_de FROM artikel WHERE geloescht=0 AND intern_gesperrt!=1 AND ($artikel_freitext1_suche) AND geloescht=0 ORDER by name_de LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name_de'];
        }
        break;

      case "artikelgruppe":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT typ FROM artikel WHERE geloescht=0 AND intern_gesperrt!=1 AND typ LIKE '%$term%' ORDER by typ");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['typ'];
        }
        break;

      case "artikelkategorienfull":
        $anz = $this->app->DB->Select("SELECT count(*) FROM artikelkategorien");
        $subwhere = $this->app->erp->ProjektRechte('ar.projekt');
        if($anz)
        {

          $artikelbaum = array();
          $allekategorien = $this->app->DB->SelectArr("SELECT id, bezeichnung, parent FROM artikelkategorien WHERE geloescht = 0");
          foreach($allekategorien as $key=>$value){
            if($value['parent'] == 0){
              $artikelbaum[$value['id']] = $value['bezeichnung'];
              foreach($allekategorien as $key2=>$value2){
                if(array_key_exists($value2['parent'], $artikelbaum)){
                  $artikelbaum[$value2['id']] = $artikelbaum[$value2['parent']]." / ".$value2['bezeichnung'];
                }
              }
            }
          }
          
          $arr = array();
          $i = 0;

          if($term == "" || $term == "%"){
            foreach($artikelbaum as $key=>$value){
              $arr[$i] = $key." ".$value;
              $i++;
            }
          }else{
            foreach($artikelbaum as $key=>$value){
              if(strpos(strtolower($value), strtolower($term)) !== false){
                $arr[$i] = $key.' '.$value;
                $i++;
              }            
            }
          }
          
        } 
                

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++){
          if($i<20){
            $newarr[] = $arr[$i];
          }else{
            break;
          }
        }


      break;

      case "artikelkategorien":
      
        $anz = $this->app->DB->Select("SELECT count(*) FROM artikelkategorien");
        $subwhere = $this->app->erp->ProjektRechte('ar.projekt');
        if($anz)
        {
          $arr = $this->app->DB->SelectArr("SELECT a.typ,ar.bezeichnung  FROM artikel a LEFT JOIN artikelkategorien ar ON a.typ = concat(ar.id,'_kat') WHERE a.geloescht=0 AND a.intern_gesperrt!=1 AND ar.bezeichnung LIKE '%$term%' ".$subwhere." GROUP BY a.typ ORDER by ar.bezeichnung ");
          if($arr)
          {
            $typen = false;
            foreach($arr as $k => $a)
            {
              if($a['bezeichnung'])$arr[$k]['typ'] = $a['bezeichnung'];
              $typen = $arr[$k]['typ'];
            }
            array_multisort($typen, $arr);
            $last = false;
            foreach($arr as $k => $a)
            {
              if($last == $a['typ'])
              {
                unset($arr[$k]);
              } else {
                $last = $a['typ'];
              }
            }
          }
          
          
        } else {
          $arr = $this->app->DB->SelectArr("SELECT DISTINCT typ FROM artikel WHERE geloescht=0 AND intern_gesperrt!=1 AND typ LIKE '%$term%' ORDER by typ");  
        }
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++){
          $newarr[] = $arr[$i]['typ'];
        }
      break;
      
      case 'alleartikelkategorien':
        $anz = $this->app->DB->Select("SELECT count(*) FROM artikelkategorien");
        $subwhere = $this->app->erp->ProjektRechte('ar.projekt');
        if($anz)
        {
          $arr = $this->app->DB->SelectArr("SELECT ar.bezeichnung as typ  FROM artikelkategorien ar  WHERE ar.bezeichnung LIKE '%$term%' ".$subwhere." AND ar.geloescht <> 1 ORDER by ar.bezeichnung ");
        } else {
          $arr = $this->app->DB->SelectArr("SELECT DISTINCT typ FROM artikel WHERE geloescht=0 AND intern_gesperrt!=1 AND typ LIKE '%$term%' ORDER by typ");  
        }

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['typ'];
        }
      break;
      case 'xcs_tables':
        $felder = array('name');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);

        $arr = $this->app->DB->SelectArr("SELECT name FROM xcs_table ORDER BY name LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;
      case "artikeleanbeleg":
        $doctype = $this->app->Secure->GetGET('doctype');
        $doctypeid = (int)$this->app->Secure->GetGET('doctypeid');
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(if(art.ean <> '', art.ean, art.nummer),' ',art.name_de) as name FROM artikel art
        INNER JOIN $doctype"."_position ap ON ap.artikel = art.id AND $doctype = '$doctypeid'
        WHERE art.geloescht=0 AND  ($subwhere) AND art.geloescht=0 AND art.intern_gesperrt!=1 LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
      break;
      case "artikelnummerbeleg":
        $doctype = $this->app->Secure->GetGET('doctype');
        $doctypeid = (int)$this->app->Secure->GetGET('doctypeid');
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(art.nummer,' ',art.name_de) as name FROM artikel art
        INNER JOIN $doctype"."_position ap ON ap.artikel = art.id AND $doctype = '$doctypeid'
        WHERE art.geloescht=0 AND  ($artikel_freitext1_suche) AND art.geloescht=0 AND art.intern_gesperrt!=1 LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
      break;
      case "spracheniso":
        $arr = $this->app->DB->SelectArr('SELECT iso FROM sprachen');
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++){
          $newarr[] = $arr[$i]['iso'];
        }
        break;
      case "geschaeftsbrief_vorlagen":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(id,' ',subjekt,' (',sprache,')') as name FROM geschaeftsbrief_vorlagen");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++){
          $newarr[] = $arr[$i]['name'];
        }
      break;

      case "artikeleinheit":
        //$arr = $this->app->DB->SelectArr("SELECT DISTINCT einheit_de FROM artikeleinheit WHERE firma='".$this->app->User->GetFirma()."' AND einheit_de LIKE '%$term%' ORDER by einheit_de");
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT einheit_de FROM artikeleinheit WHERE einheit_de LIKE '%$term%' ORDER by einheit_de");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['einheit_de'];
        break;



      case "ihrebestellnummer":
        $adresse = $this->app->Secure->GetGET('adresse');
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT ihrebestellnummer FROM auftrag WHERE ihrebestellnummer LIKE '%$term%' AND adresse='$adresse' ORDER by ihrebestellnummer ");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['ihrebestellnummer'];
        }
      break;


      case "accountart":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT art FROM adresse_accounts WHERE art LIKE '%$term%' ORDER by art");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['art'];
        }
        break;


      case "ansprechpartneradresse":
        $adressId = $this->app->Secure->GetGET('adresse');

        if(is_numeric($adressId) && $adressId > 0){
          $adressId = $this->app->DB->Select("SELECT id FROM adresse WHERE id = '$adressId' LIMIT 1");
        }

        $limit = '';
        if($adressId <= 0){
          $adresse = explode(' ', $adressId);
          $adressId = $this->app->DB->Select("SELECT id FROM adresse WHERE name = '".$adresse[0]."' AND name != '' LIMIT 1");
          if($adressId <= 0){
            $limit = ' LIMIT 20 ';
          }
        }

        if($adressId > 0){
          $subwhere = " AND a.id = '$adressId'";
        }else{
          $subwhere = '';
        }

        $felder = array('an.name');
        $subwhere2 = $this->AjaxFilterWhere($termorig,$felder);

        $arr = $this->app->DB->SelectArr("SELECT DISTINCT CONCAT(an.id, ' ', an.name, ' ', IF(a.lieferantennummer,CONCAT('(', a.name, ', Kdr: ', a.kundennummer, ' Liefr: ', a.lieferantennummer, ')'), CONCAT('(', a.name, ', Kdr: ', a.kundennummer, ')'))) AS name 
                        FROM ansprechpartner an 
                        LEFT JOIN adresse a ON an.adresse = a.id 
                        WHERE ($subwhere2) AND a.id > 0 AND a.geloescht = 0 $subwhere ".$this->app->erp->ProjektRechte('a.projekt').$limit);
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = "{$arr[$i]['name']}";
        }
        break;

      case "ansprechpartner":
        $adresse = $this->app->Secure->GetGET('adresse');
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT name FROM ansprechpartner WHERE adresse='$adresse' AND name LIKE '%$term%' AND geloescht <> 1 ORDER by name");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
      case "hersteller":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT hersteller FROM artikel WHERE geloescht=0 AND intern_gesperrt!=1 AND hersteller LIKE '%$term%' ORDER by hersteller");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['hersteller'];
        break;
      case "rmakategorien":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT bezeichnung FROM rma_vorlagen_kategorien WHERE bezeichnung LIKE '%$term%' ORDER BY bezeichnung");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;

      case "spedition_einstellungen_feld":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT feld FROM spedition_einstellungen WHERE aktiv=1 AND feld LIKE '%$term%' ORDER by feld");        
          $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['feld'];
        }
        break;
  
      case "zeiterfassung_beschreibung":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT beschreibung FROM zeiterfassung_kosten WHERE beschreibung LIKE '%$term%' ORDER by beschreibung");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['beschreibung'];
        }
        break;

      case "eigenschaften_vorlagen":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT bezeichnung FROM eigenschaften_vorlagen WHERE bezeichnung LIKE '%$term%' AND aktiv = 1 ORDER BY bezeichnung");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;

      case "ticketnummer":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(t.schluessel, ' Name: ', t.kunde, ' Betr: ', t.betreff) as schluessel FROM ticket t WHERE t.schluessel LIKE '%$term%' OR t.kunde LIKE '%$term%' OR t.betreff LIKE '%$term%' ORDER BY t.schluessel");

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['schluessel'];
        }
        break;

      case "abosammelrechnungen":
        $id = $this->app->Secure->GetGET('adresse');        
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(id, ' ', bezeichnung) as bezeichnung FROM adresse_abosammelrechnungen WHERE bezeichnung LIKE '%$term%' AND adresse = '$id' ORDER BY bezeichnung");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;


      case "arbeitsplatzgruppe":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(id, ' ',bezeichnung) as bezeichnung FROM arbeitsplatzgruppen WHERE bezeichnung LIKE '%$term%' AND aktiv = 1 ORDER BY bezeichnung");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['bezeichnung'];
        break;

      case "artikelarbeitsanweisung_vorlagen":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT bezeichnung FROM artikelarbeitsanweisung_vorlagen WHERE bezeichnung LIKE '%$term%' AND aktiv = 1 ORDER BY bezeichnung");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['bezeichnung'];
        break;

      case "artikelfunktionsprotokoll_vorlagen":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT bezeichnung FROM artikelfunktionsprotokoll_vorlagen WHERE bezeichnung LIKE '%$term%' AND aktiv = 1 ORDER BY bezeichnung");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['bezeichnung'];
        break;

      case "fahrtenbuch_kennzeichen":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT kennzeichen FROM fahrtenbuch_fahrzeuge WHERE kennzeichen LIKE '%$term%' AND aktiv = 1 ORDER BY kennzeichen");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['kennzeichen'];
        break;   

      case "fahrtenbuch_strecke":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT strecke FROM fahrtenbuch_vorlagen WHERE (strecke LIKE '%$term%' OR strecke LIKE '%$term2%' OR strecke LIKE '%$term3%') AND aktiv = 1 ORDER BY strecke");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['strecke'];
        break;

      case "verpackungsgruppe":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT verpackungsgruppe FROM verpackungen_details WHERE verpackungsgruppe LIKE '%$term%' ORDER by verpackungsgruppe");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['verpackungsgruppe'];
        break;

      case "dropshipping_gruppe":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT bezeichnung FROM dropshipping_gruppe WHERE bezeichnung LIKE '%$term%' ORDER BY bezeichnung");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['bezeichnung'];
        break;
   


      case "layoutvorlage":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(id, ' ', name) AS name FROM layoutvorlagen WHERE name LIKE '%$term%' ORDER BY name");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;
 
      case "uservorlage":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT bezeichnung FROM uservorlage WHERE bezeichnung LIKE '%$term%' ORDER by bezeichnung");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['bezeichnung'];
        break;


      case "lagergrund":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT TRIM(REPLACE(REPLACE(referenz,'Umlagern f&uuml; :',''),'Differenz:','')) as ergebnis FROM lager_bewegung WHERE REPLACE(referenz,'Differenz:','') LIKE '%$term%' AND referenz NOT LIKE '%Inventur%' AND referenz NOT LIKE '%Charge%' AND referenz NOT LIKE '%Lieferschein%'
            AND referenz NOT LIKE '%Manuell%' AND referenz NOT LIKE '%Wareneingang%' AND referenz NOT LIKE '%Lieferungen%' LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['ergebnis'];
        break;



      case "auftrag_zahlungseingang":
        if(strpos($term,',')!==false)
        {
          $term = substr($term,strripos($term,','));
          $term = str_replace(',','',$term);
        }

        $arr = $this->app->DB->SelectArr("SELECT CONCAT(r.belegnr,' ',REPLACE(a.name,',',''),' ',r.internet,' GESAMT: ',r.gesamtsumme,' (Kunde ',a.kundennummer,') vom ',DATE_FORMAT(r.datum,'%d.%m.%Y'),' Status: ',r.status) as name
            FROM auftrag r LEFT JOIN adresse a ON a.id=r.adresse WHERE r.belegnr!='' 
            AND (a.name LIKE '%$term%' OR r.belegnr LIKE '%$term%' OR a.kundennummer LIKE '%$term%' OR a.name LIKE '%$term2%' OR a.name LIKE '%$term3%' OR IFNULL(r.internet,'') LIKE '%$term%' ) ORDER by r.belegnr  DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;


      case "rechnung_zahlungseingang":
        if(strpos($term,',')!==false)
        {
          $term = substr($term,strripos($term,','));
          $term = str_replace(',','',$term);
        }

        $arr = $this->app->DB->SelectArr("SELECT CONCAT(r.belegnr,' Soll:',r.soll,' Ist:',r.ist,' ',' Diff:',(r.soll-r.ist)*-1,' ',
          if(r.zahlungszielskonto > 0,if(isnull(r.skontobetrag),CONCAT('SK:',r.zahlungszielskonto,'%(',FORMAT((r.soll/100)*r.zahlungszielskonto,2),') '),concat('SK:',FORMAT(100*r.skontobetrag / r.soll,2),'%(',FORMAT(r.skontobetrag,2),')')),''),REPLACE(a.name,',',''),'(Kunde ',a.kundennummer,') vom ',DATE_FORMAT(r.datum,'%d.%m.%Y'),' Status: ',r.status
          ,IF(IFNULL(ab.internet,'')!='',CONCAT(' Intenet: ',ab.internet),'')
          ) as name
            FROM rechnung r 
            LEFT JOIN auftrag ab ON r.auftragid = ab.id
            LEFT JOIN adresse a ON a.id=r.adresse 
            WHERE r.belegnr!='' AND 
            (a.name LIKE '%$term%' OR r.belegnr LIKE '%$term%' OR a.kundennummer LIKE '%$term%' 
            OR a.name LIKE '%$term2%' OR a.name LIKE '%$term3%'
            OR IFNULL(ab.internet,'') LIKE '%$term%'
            ) 
            AND r.zahlungsstatus!='bezahlt' 
            ORDER by r.belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

      case "gutschrift_zahlungseingang":
        if(strpos($term,',')!==false)
        {
          $term = substr($term,strripos($term,','));
          $term = str_replace(',','',$term);
        }


        $arr = $this->app->DB->SelectArr("SELECT CONCAT(r.belegnr,' SOLL: ',r.soll,' IST:',r.ist,' ',REPLACE(a.name,',',''),' (Kunde ',a.kundennummer,') vom ',DATE_FORMAT(r.datum,'%d.%m.%Y'),' Status: ',r.status) as name
            FROM gutschrift r LEFT JOIN adresse a ON a.id=r.adresse WHERE r.belegnr!='' AND (a.name LIKE '%$term%' OR r.belegnr LIKE '%$term%' OR a.kundennummer LIKE '%$term%' OR a.name LIKE '%$term2%' OR a.name LIKE '%$term3%') AND (r.manuell_vorabbezahlt IS NULL OR r.manuell_vorabbezahlt='0000-00-00') ORDER by r.belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;



      case "gutschrift":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) AS name
            FROM gutschrift WHERE belegnr != '' AND belegnr != '0' AND (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ".$this->app->erp->ProjektRechte()." ORDER BY belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

      case "angebot":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) as name 
            FROM angebot WHERE belegnr!='' AND belegnr!='0' AND (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ".$this->app->erp->ProjektRechte("projekt")."  ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

      case "bestellung":
        $status = $this->app->Secure->GetGET('status');
        switch($status) {
          case 'freigegeben':
          case 'abgeschlossen':
          case 'versendet':
          case 'strorniert':
            break;
          default:
            $status = '';
            break;
        }
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) as name 
            FROM bestellung WHERE belegnr!='0' AND belegnr!='' AND (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ".($status != ''?" AND status = '".$status."' ":'').$this->app->erp->ProjektRechte("projekt")."  ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;
  
      case "preisanfrage":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) as name 
            FROM preisanfrage WHERE belegnr!='0' AND belegnr!='' AND (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ".$this->app->erp->ProjektRechte("projekt")."  ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;
  

      case "bestellunggesamtsumme":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',".$this->app->erp->FormatPreis("gesamtsumme",2).",' ',waehrung,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) as name 
            FROM bestellung WHERE belegnr!='0' AND belegnr!='' AND (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%')  ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;



      case "auftrag_position_dienstleistung":
        $arr = $this->app->DB->SelectArr("SELECT ap.menge, ap.id as auftragspositionid,CONCAT(a.belegnr,'-',ap.sort,' ',a.name,' ',DATE_FORMAT(a.datum,'%d.%m.%Y'),' ',ap.bezeichnung) as name 
            FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag LEFT JOIN artikel art ON art.id=ap.artikel WHERE art.dienstleistung=1 AND  a.belegnr!='0' AND a.belegnr!='' AND (a.name LIKE '%$term%' OR ap.bezeichnung LIKE '%$term%' OR a.belegnr LIKE '%$term%' OR DATE_FORMAT(a.datum,'%Y-%m-%d') LIKE '%$term%') ORDER by a.belegnr DESC LIMIT 20");
        foreach($arr as $value){
          $sollzeit = number_format($value['menge'],2);
          $istzeit = $this->app->DB->Select("SELECT SUM(Time_to_sec(Timediff(z.von,z.bis))/3600) FROM zeiterfassung z WHERE z.auftragpositionid = '{$value['auftragspositionid']}'");
          $newarr[] = $value['name'] . " ( " . number_format(($istzeit*-1),2) . " von " . $sollzeit . ")";
        }
        break;

      case "alle_auftrag_positionen":
        $arr = $this->app->DB->SelectArr("SELECT ap.menge, ap.id as auftragspositionid,CONCAT(a.belegnr,'-',ap.sort,' ',a.name,' ',DATE_FORMAT(a.datum,'%d.%m.%Y'),' ',ap.bezeichnung) as name 
            FROM auftrag_position ap LEFT JOIN auftrag a ON a.id=ap.auftrag WHERE a.belegnr!='0' AND a.belegnr!='' AND (a.name LIKE '%$term%' OR ap.bezeichnung LIKE '%$term%' OR a.belegnr LIKE '%$term%' OR DATE_FORMAT(a.datum,'%Y-%m-%d') LIKE '%$term%') ORDER by a.belegnr DESC LIMIT 20");
        foreach($arr as $value){
          $sollzeit = number_format($value['menge'],2);
          $istzeit = $this->app->DB->Select("SELECT SUM(Time_to_sec(Timediff(z.von,z.bis))/3600) FROM zeiterfassung z WHERE z.auftragpositionid = '{$value['auftragspositionid']}'");
          $newarr[] = $value['name'] . " ( " . number_format(($istzeit*-1),2) . " von " . $sollzeit . ")";
        }
        break;

      case "auftragihrebestellnummer":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y'),
            if(ihrebestellnummer!='',CONCAT(' ',ihrebestellnummer),''),if(internebezeichnung!='',CONCAT(' ',internebezeichnung),'')) as name 
            FROM auftrag WHERE belegnr!='0' AND belegnr!='' AND status!='angelegt' AND
            (name LIKE '%$term%' OR name LIKE '%$term2%' OR name LIKE '%$term3%' OR ihrebestellnummer LIKE '%$term%' OR internebezeichnung LIKE '%$term%' 
              OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;


      case "auftrag":
        $status = $this->app->Secure->GetGET('status');
        switch($status) {
          case 'freigegeben':
          case 'abgeschlossen':
          case 'strorniert':
            break;
          default:
            $status = '';
            break;
        }
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(a.belegnr,' ',a.name,' ',DATE_FORMAT(a.datum,'%d.%m.%Y')) as name
            FROM auftrag a LEFT JOIN projekt p ON p.id=a.projekt             WHERE a.belegnr!='0' AND a.belegnr!='' AND (a.name LIKE '%$term%' OR a.belegnr LIKE '%$term%' OR DATE_FORMAT(a.datum,'%Y-%m-%d') LIKE '%$term%') ".$this->app->erp->ProjektRechte()." ".($status != ''?" AND a.status = '".$status."' ":'')." ORDER by a.belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

      case "auftragmitrechnung":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(a.belegnr,' ',a.name,' ',DATE_FORMAT(a.datum,'%d.%m.%Y')) as name 
            FROM auftrag a
            INNER JOIN rechnung r ON a.id = r.auftragid AND r.belegnr <> ''
            WHERE a.belegnr!='0' AND a.belegnr!='' AND (a.name LIKE '%$term%' OR a.belegnr LIKE '%$term%' OR DATE_FORMAT(a.datum,'%Y-%m-%d') LIKE '%$term%') GROUP BY a.id ORDER by a.belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

        
      case "auftrag_freigegeben":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) as name 
            FROM auftrag WHERE belegnr!='0' AND belegnr!='' AND status='freigegeben' AND (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;


      case "rechnung_freigegeben":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) as name 
            FROM rechnung WHERE belegnr!='0' AND belegnr!='' AND status='freigegeben' AND (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

      case "produktion":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) as name 
            FROM produktion WHERE belegnr!='0' AND belegnr!='' AND (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;



      case "arbeitsnachweis":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) as name 
            FROM arbeitsnachweis WHERE belegnr!='0' AND belegnr!='' AND (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

      case "lieferschein":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) as name 
            FROM lieferschein WHERE belegnr!='0' AND belegnr!='' AND  (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

      case 'rechnung':
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) as name 
            FROM rechnung WHERE belegnr!='0' AND belegnr!='' AND (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;
      case 'retoure':
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(belegnr,' ',name,' ',DATE_FORMAT(datum,'%d.%m.%Y')) as name 
            FROM retoure WHERE belegnr!='0' AND belegnr!='' AND (name LIKE '%$term%' OR belegnr LIKE '%$term%' OR DATE_FORMAT(datum,'%Y-%m-%d') LIKE '%$term%') ORDER by belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;
        

      case "rechnungmitauftrag":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(r.belegnr,' ',r.name,' ',DATE_FORMAT(r.datum,'%d.%m.%Y')) as name
            FROM rechnung r
            INNER JOIN auftrag a ON r.auftragid = a.id
            LEFT JOIN projekt p ON p.id=r.projekt
            WHERE r.belegnr!='0' AND r.belegnr!='' AND (r.name LIKE '%$term%' OR r.belegnr LIKE '%$term%' OR DATE_FORMAT(r.datum,'%Y-%m-%d') LIKE '%$term%') ".$this->app->erp->ProjektRechte()." GROUP BY r.id ORDER by r.belegnr DESC LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

      case "vpeartikel":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT vpe FROM verkaufspreise WHERE geloescht=0 AND vpe LIKE '%$term%' ORDER by vpe");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['vpe'];
        break;

      case "herstellerlink":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT herstellerlink FROM artikel WHERE geloescht=0 AND intern_gesperrt!=1 AND herstellerlink LIKE '%$term%' ORDER by herstellerlink");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['herstellerlink'];
        break;


      case 'lagerplatz':
      case 'lagerplatzstandardlager':
        $onlyStdLager = $filtername === 'lagerplatzstandardlager';
        $stdLager = 0;
        if($rmodule === 'produktionszentrum' || $rmodule==='produktion') {
          if($onlyStdLager > 0 && $rid > 0) {
            $stdLager = (int)$this->app->DB->Select(
              sprintf(
                'SELECT standardlager FROM produktion WHERE id = %d',
                $rid
              )
            );
          }
        }
        $withzwischenlager = $this->app->Secure->GetGET('zwischenlager');
        $withstadardlager = $this->app->Secure->GetGET('withstandardlager');
        $sql = "SELECT lp.kurzbezeichnung 
          FROM lager_platz AS lp 
          LEFT JOIN lager AS l ON l.id=lp.lager
          WHERE lp.geloescht=0  AND ('$stdLager' = '0' OR l.id = '$stdLager') 
            AND lp.kurzbezeichnung LIKE '%$term%' ".
          $this->app->erp->ProjektRechte('l.projekt').' 
          ORDER BY lp.kurzbezeichnung';
        $arr = $this->app->DB->SelectArr($sql);
        if(empty($arr)) {
          $arr = [];
        }
        if($withzwischenlager) {
          $arr2 = $this->app->DB->SelectArr(
            "SELECT 'Zwischenlager' AS kurzbezeichnung FROM (SELECT 1) a WHERE 'Zwischenlager' LIKE '%$term%' "
          );
          if(!empty($arr2)) {
            $arr = array_merge($arr, $arr2);
          }
        }
        if($withstadardlager) {
          $arr2 = $this->app->DB->SelectArr(
            "SELECT 'Standardlager' AS kurzbezeichnung FROM (SELECT 1) a WHERE 'Standardlager' LIKE '%$term%'"
          );
          if(!empty($arr2)) {
            $arr = array_merge($arr, $arr2);
          }
        }
        if(!empty($arr)) {
          sort($arr);
        }
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['kurzbezeichnung'];
        }
        break;
      case 'artikelfremdnummern':
        $article = explode(' ', $this->app->Secure->GetGET('artikel'));
        $article = reset($article);
        $articleId = (int)$this->app->Secure->GetGET('artikelid');
        $bezeichnung = $this->app->Secure->GetGET('bezeichnung');
        $shopid = (int)$this->app->Secure->GetGET('shopid');
        if(empty($articleId) && !empty($article)) {
          $articleId = $this->app->DB->Select(
            sprintf(
              "SELECT id 
              FROM artikel 
              WHERE nummer = '%s' AND nummer <> '' AND geloescht <> 1 
              ORDER BY intern_gesperrt
              LIMIT 1",
              $article
            )
          );
        }
        if(!empty($articleId)) {
          $newarr = $this->app->DB->SelectFirstCols(
            sprintf(
              "SELECT DISTINCT af.nummer 
              FROM artikelnummer_fremdnummern AS af 
              WHERE af.artikel = %d AND af.aktiv = 1 AND (af.bezeichnung = '%s' OR '%s' = '')
                AND af.nummer LIKE '%%%s%%' AND af.shopid = %d",
              $articleId, $bezeichnung, $bezeichnung, $term, $shopid
            )
          );
        }

        break;
      case "bezeichnungfremdnr":
        $arr = $this->app->DB->SelectArr('SELECT DISTINCT af.bezeichnung FROM artikelnummer_fremdnummern af');
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        if(!in_array('ID', $newarr)){
          $newarr[] = 'ID';
        }
        if(!in_array('SKU', $newarr)){
          $newarr[] = 'SKU';
        }

        break;
      case "lagerplatzprojekt":
        $arr = $this->app->DB->SelectArr('SELECT lp.kurzbezeichnung 
          FROM lager_platz AS lp
          INNER JOIN lager l ON lp.lager = l.id AND (l.projekt = 0 OR (1 '.$this->app->erp->ProjektRechte('l.projekt')."))
          WHERE lp.geloescht=0 AND lp.kurzbezeichnung LIKE '%$term%' 
          ORDER BY lp.kurzbezeichnung");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['kurzbezeichnung'];
        }
        break;
      case "sperrlagerplatz":
        $arr = $this->app->DB->SelectArr("SELECT kurzbezeichnung FROM lager_platz WHERE geloescht=0 AND sperrlager = 1 AND kurzbezeichnung LIKE '%$term%' ORDER by kurzbezeichnung");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['kurzbezeichnung'];      
      break;
      case 'lagerplatzartikel':
        $artikel = (int)$this->app->Secure->GetGET('artikel');
        $pos = (int)$this->app->Secure->GetGET('pos');
        $doctype = strtolower($this->app->Secure->GetGET('doctype'));
        $join = '';
        if($pos > 0 && $doctype === 'lieferschein' && $artikel > 0) {
          $seriennummern = $this->app->DB->Select(
            sprintf(
              'SELECT `seriennummern` FROM artikel WHERE id = %d LIMIT 1',
              $artikel
            )
          );
          if($seriennummern !== 'keine' && !empty($seriennummern)) {
            $cSn = $this->app->DB->SelectArr(
              sprintf(
                'SELECT IFNULL(COUNT(id), 0) 
                FROM `beleg_chargesnmhd` 
                WHERE doctype = \'%s\' AND pos = %d AND type = \'sn\' AND wert <> \'\'
                  ',
                $doctype, $pos
              )
            );
            $position = $this->app->DB->SelectRow(
              sprintf('SELECT menge, geliefert FROM lieferschein_position WHERE id = %d',
                $pos
              )
            );
            if($cSn == $position['menge']) {
              $join = sprintf(' INNER JOIN 
                (
                  SELECT lagerplatz 
                  FROM `beleg_chargesnmhd` 
                  WHERE doctype = \'%s\' AND parameter = %d 
                  GROUP BY lagerplatz
                ) AS bc ON lp.id = bc.lagerplatz ',
                $doctype, $pos
              );
            }
          }
        }
        $arr = $this->app->DB->SelectArr(
          sprintf('SELECT lp.kurzbezeichnung 
            FROM lager_platz AS lp 
            INNER JOIN lager_platz_inhalt AS lpi on lp.id = lpi.lager_platz 
            %s
            WHERE lpi.artikel=%d AND lp.geloescht=0 AND lp.kurzbezeichnung LIKE \'%%%s%%\' 
            GROUP BY lp.kurzbezeichnung 
            ORDER by lp.kurzbezeichnung',
            $join, $artikel, $term
          )
        );
        if(empty($arr)) {
          break;
        }
        foreach($arr as $row) {
          $newarr[] = $row['kurzbezeichnung'];
        }
        break;

      case "lager":
        $arr = $this->app->DB->SelectArr("SELECT l.bezeichnung FROM lager l WHERE l.geloescht=0 AND l.bezeichnung LIKE '%$term%' ".$this->app->erp->ProjektRechte("l.projekt")." ORDER by 1");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['bezeichnung'];
        break;

      case 'lager_produktion':
        $arr = $this->app->DB->SelectArr("SELECT l.bezeichnung FROM lager l JOIN lager_platz lp ON l.id = lp.lager WHERE l.geloescht=0 AND l.bezeichnung LIKE '%$term%' ".$this->app->erp->ProjektRechte("l.projekt")." AND lp.allowproduction = 1 ORDER BY 1");

        if(empty($arr)){
          $arr = $this->app->DB->SelectArr("SELECT l.bezeichnung FROM lager l WHERE l.geloescht=0 AND l.bezeichnung LIKE '%$term%' ".$this->app->erp->ProjektRechte("l.projekt")." ORDER by 1");
        }

        $carr = !empty($arr)?count($arr):0;
        for($i=0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['bezeichnung'];
        break;

      case "aktionscode":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(code,' ',beschriftung) as name FROM aktionscode_liste
            WHERE (beschriftung LIKE '%$term%' OR code LIKE '%$term%' OR code LIKE '%$term2%' OR code LIKE '%$term3%') AND ausblenden!=1 ORDER by code");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

      case "waehrung":
        if($this->app->DB->Select("SELECT id FROM waehrung_umrechnung LIMIT 1")) {


          $arr = $this->app->DB->SelectArr("
            (
              SELECT DISTINCT waehrung_nach as name FROM waehrung_umrechnung
              WHERE (waehrung_nach LIKE '%$term%') ORDER by waehrung_nach
            ) 
            UNION 
            (
              SELECT DISTINCT waehrung_von as name FROM waehrung_umrechnung
              WHERE (waehrung_von LIKE '%$term%') 
            
              ORDER by waehrung_von
            )
            ORDER by name");
        }else{
      
          $waehrungen = $this->app->erp->GetWaehrung();
          if($waehrungen)
          {
            foreach($waehrungen as $v)
            {
              if($v) {
                $sqla[] = " (SELECT '$v' as name FROM (SELECT 1) AS X WHERE 
              '$v' LIKE '%$term%'
              ) ";
              }
            }
            $arr = $this->app->DB->SelectArr("SELECT t.name FROM ( ".implode(' UNION ', $sqla)." ) t ORDER BY name ");
          }
        }

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];


        break;

      case "sachkonto":
        $cmd = $this->app->Secure->GetGET("cmd");

        if($cmd!="") $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE abkuerzung='".$this->app->DB->real_escape_string($cmd)."' LIMIT 1");

        $checkprojekt = ($projekt > 0?$this->app->DB->Select("SELECT COUNT(id) FROM kontorahmen WHERE projekt='$projekt'"):0);
        $checkprojektnull = $this->app->DB->Select("SELECT COUNT(id) FROM kontorahmen WHERE projekt=0 OR projekt IS NULL");

        if($checkprojekt > 0)
        {
          $andprojekt = "AND (projekt='$projekt' OR projekt = 0 OR projekt IS NULL)";
        } else {
          if($checkprojektnull>0)
          {
            $andprojekt = " AND (projekt=0 OR projekt IS NULL) ";
          } else {
            $andprojekt = "";
          }
        }


        $arr = $this->app->DB->SelectArr("SELECT CONCAT(sachkonto,' ',beschriftung) as name FROM kontorahmen 
            WHERE (beschriftung LIKE '%$term%' OR sachkonto LIKE '%$term%' OR sachkonto LIKE '%$term2%' OR sachkonto LIKE '%$term3%' OR beschriftung LIKE '%$term2%' OR beschriftung LIKE '%$term3%') AND ausblenden!=1 $andprojekt ORDER by sachkonto");

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

      case "lieferbedingungen":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(lieferbedingungen) as name FROM lieferbedingungen
            WHERE (lieferbedingungen LIKE '%$term%' OR lieferbedingungen LIKE '%$term2%' OR lieferbedingungen LIKE '%$term3%') ORDER by lieferbedingungen");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;
 
      case "zeiterfassungvorlage":
        $arr = $this->app->DB->SelectArr("SELECT vorlage as name FROM zeiterfassungvorlage
            WHERE (vorlage LIKE '%$term%' OR vorlage LIKE '%$term2%' OR vorlage LIKE '%$term3%') AND ausblenden!=1 ORDER by vorlage");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;
      case "zeiterfassungvorlagedetail":
        $vorlage = $this->app->Secure->GetPOST('vorlage');
        $arr = $this->app->DB->SelectRow("SELECT vorlagedetail as name, art, projekt, teilprojekt, kunde, abrechnen FROM zeiterfassungvorlage WHERE vorlage = '$vorlage' LIMIT 1");

        if($arr['projekt'] > 0){
          $arr['projekt'] = $this->app->DB->Select("SELECT CONCAT(abkuerzung, ' ', name) FROM projekt WHERE id = '".$arr['projekt']."' LIMIT 1");
        }else{
          $arr['projekt'] = '';
        }

        if($arr['teilprojekt'] > 0){
          $projektid = $this->app->DB->Select("SELECT projekt FROM arbeitspaket WHERE id = '".$arr['teilprojekt']."' LIMIT 1");
          if($projektid > 0){
            $projektabk = $this->app->DB->Select("SELECT abkuerzung FROM projekt WHERE id = '$projektid' LIMIT 1");
            if($projektabk != ""){
              $arr['teilprojekt'] = $this->app->DB->Select("SELECT CONCAT('".$arr['teilprojekt']."', ' ', '$projektabk', ' ', aufgabe) FROM arbeitspaket WHERE id = '".$arr['teilprojekt']."' LIMIT 1");
            }else{
              $arr['teilprojekt'] = '';
            }
          }else{
            $arr['teilprojekt'] = '';
          }
        }else{
          $arr['teilprojekt'] = '';
        }

        if($arr['kunde'] > 0){
          $arr['kunde'] = $this->app->DB->Select("SELECT CONCAT(kundennummer, ' ', name) FROM adresse WHERE id = '".$arr['kunde']."' LIMIT 1");
        }

        if($arr['art'] == ''){
          $arr['art'] = 'Arbeit';
        }


        $newarr[] = $arr['name'];
        $newarr[] = $arr['art'];
        $newarr[] = $arr['projekt'];
        $newarr[] = $arr['teilprojekt'];
        $newarr[] = $arr['kunde'];
        $newarr[] = $arr['abrechnen'];
        break;
      case "zeiterfassungprojektdetail":
        $projekt = explode(' ',$this->app->Secure->GetPOST('projekt'));
        $projektkennung = $projekt[0];

        $kunde = $this->app->DB->Select("SELECT CONCAT(a.kundennummer,' ',a.name,' (',a.ort,')') AS kunde FROM projekt p JOIN adresse a ON p.kunde=a.id WHERE a.geloescht=0 AND p.abkuerzung='$projektkennung' LIMIT 1");
        $newarr[] = $kunde;
        break;

      case "zolltarifnummer":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',beschreibung) as name FROM zolltarifnummer WHERE beschreibung LIKE '%$term%' OR nummer LIKE '%$term%' ORDER by nummer");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;


      case "kostenstelle":
        $felder = array('CONCAT(nummer,\' \',beschreibung)','nummer','beschreibung');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);

        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',beschreibung) as name FROM kostenstellen WHERE $subwhere ORDER by nummer");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;

      case "verrechnungsart":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',beschreibung) as name FROM verrechnungsart WHERE beschreibung LIKE '%$term%' OR nummer LIKE '%$term%' ORDER by nummer");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
        break;
      
      case "kundenrechnung":
      case "kundenauftrag":
      case "kundenlieferschein":
      case "kundenangebot":
      case "kundenproformarechnung":
        $adresse = (int)$this->app->Secure->GetGET('adresse');
        if(!$adresse)
        {
          $kunde = explode(' ',$this->app->Secure->GetGET('kunde'));
          $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer = '".$kunde[0]."' AND kundennummer <> '' LIMIT 1");
        }
        $beleg = str_replace('kunden','',$filtername);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(id,' ',if(belegnr <> '',belegnr,'ENTWURF'),' ',kundennummer,' ',name) as name FROM $beleg WHERE (belegnr LIKE '%$term%' OR name LIKE '%$term%' OR kundennummer LIKE '$%term%') AND (status = 'angelegt' OR status = 'freigegeben') 
        ".($adresse?" AND adresse = '$adresse' ":'')."   ".$this->app->erp->ProjektRechte('projekt')."
        ORDER by belegnr LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
      break;
      case "lieferantenpreisanfrage":
      case "lieferantenbestellung":
        $adresse = (int)$this->app->Secure->GetGET('adresse');
        if(!$adresse)
        {
          $lieferant = explode(' ',$this->app->Secure->GetGET('lieferant'));
          $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE lieferantennummer = '".$lieferant[0]."' AND lieferantennummer <> '' LIMIT 1");
        }
        $beleg = str_replace('lieferanten','',$filtername);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(id,' ',if(belegnr <> '',belegnr,'ENTWURF'),' ',lieferantennummer,' ',name) as name FROM $beleg WHERE (belegnr LIKE '%$term%' OR name LIKE '%$term%' OR lieferantennummer LIKE '$%term%') AND (status = 'angelegt' OR status = 'freigegeben') 
        ".($adresse?" AND adresse = '$adresse' ":'')."  ".$this->app->erp->ProjektRechte('projekt')."
        ORDER by belegnr LIMIT 20" );
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
          $newarr[] = $arr[$i]['name'];
      break;      
      
      // Suche nach einzelner Artikelnummer
      case 'artikelnummer':
      case 'artikelnummerseriennummer':
        $isSeriennummer = $filtername === 'artikelnummerseriennummer';
        $tmp_where = '';
        if($isSeriennummer) {
          $tmp_where = " AND seriennummern <> '' AND seriennummern <> 'keine' ";
        }
        $projekt = $this->app->Secure->GetGET('projekt');
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        //$checkprojekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id='$projekt' LIMIT 1");
        //$eigenernummernkreis = $this->app->DB->Select("SELECT eigenernummernkreis FROM projekt WHERE id='$projekt' LIMIT 1");

        //if($checkprojekt > 0 && $eigenernummernkreis=="1") $tmp_where = " AND projekt='$checkprojekt' ";
        //else $tmp_where = "";


        $arr = $this->app->DB->SelectArr(
          "SELECT CONCAT(nummer,' ',name_de) as `name` 
          FROM artikel AS art WHERE geloescht=0 AND  ($subwhere) AND geloescht=0 AND intern_gesperrt!=1 $tmp_where ".
          $this->app->erp->ProjektRechte('art.projekt'). ' LIMIT 20'
        );
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;

      case "artikelnummerstueckliste":
        $tmp_where = '';
        $projekt = $this->app->Secure->GetGET('projekt');
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }

        $artikel_artikelnummer_suche = $this->app->erp->Firmendaten('artikel_artikelnummer_suche');

        if($artikel_artikelnummer_suche){
          $felder[] = 'e.bestellnummer';
          $felder[] = 'v.kundenartikelnummer';
        }

        $subwhere = $this->AjaxFilterWhere($termorig,$felder);

        if($artikel_artikelnummer_suche){
          $arr = $this->app->DB->SelectArr("SELECT DISTINCT CONCAT(nummer,' ',name_de) as name 
                  FROM artikel AS art 
                  LEFT JOIN einkaufspreise e ON art.id = e.artikel AND e.bestellnummer != '' AND e.geloescht = 0 AND e.bestellnummer IS NOT NULL AND 
                    (IFNULL(e.gueltig_bis,'0000-00-00') > NOW() OR IFNULL(e.gueltig_bis,'0000-00-00')='0000-00-00')
                  LEFT JOIN verkaufspreise v ON art.id = v.artikel AND v.kundenartikelnummer != '' AND v.geloescht = 0 AND v.kundenartikelnummer IS NOT NULL AND
                    (IFNULL(v.gueltig_bis,'0000-00-00') > NOW() OR IFNULL(v.gueltig_bis,'0000-00-00')='0000-00-00')
                  WHERE art.geloescht=0 AND ($subwhere) AND art.intern_gesperrt!=1 $tmp_where ".
                    $this->app->erp->ProjektRechte('art.projekt'). "LIMIT 20");
        }else{
          $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',name_de) as name FROM artikel AS art WHERE geloescht=0 AND  ($subwhere) AND geloescht=0 AND intern_gesperrt!=1 $tmp_where ".
            $this->app->erp->ProjektRechte('art.projekt'). "LIMIT 20");
        }

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;


      case "artikelnummermitseriennummern":
        $tmp_where = '';
        $projekt = $this->app->Secure->GetGET('projekt');
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);

        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',name_de) as name FROM artikel AS art WHERE geloescht=0 AND  ($subwhere) AND geloescht=0 AND intern_gesperrt!=1 AND seriennummern<>'keine' $tmp_where ".
          $this->app->erp->ProjektRechte('art.projekt'). 'LIMIT 20');
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
      case "artikelmengeinbeleg":
        $beleg = $this->app->Secure->GetGet('beleg');
        $belegid = $this->app->Secure->GetGet('id');
        $artikel = explode(' ',$this->app->Secure->GetPost('vorlage'));
        $artikelnummer = $artikel[0];
        $artikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer='$artikelnummer' AND geloescht=0 LIMIT 1");
        if($artikelid){
          $menge = $this->app->DB->Select('SELECT '.$this->app->erp->FormatMenge('SUM(menge)').' FROM '.$beleg."_position WHERE artikel='$artikelid' AND $beleg='$belegid'");
        }
        if(!$menge){
          $menge='0';
        }

        $newarr[] = $menge;
        break;
      // Suche nach mehreren Artikelnummern (kommagetrennt)
      case "artikelnummer_multi":
        $tmp_where = '';
        if(strpos($term,',')!==false) {
          $term = substr($term, (strripos($term,',')+1));
        }
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);

        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',name_de) as name FROM artikel AS art WHERE geloescht=0 AND  ($subwhere) AND geloescht=0 AND intern_gesperrt!=1 $tmp_where LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;

      case "portoartikel":
        $tmp_where = '';
        //$projekt = $this->app->Secure->GetGET('projekt');
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        //$checkprojekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id='$projekt' LIMIT 1");
        //$eigenernummernkreis = $this->app->DB->Select("SELECT eigenernummernkreis FROM projekt WHERE id='$projekt' LIMIT 1");

        //if($checkprojekt > 0 && $eigenernummernkreis=="1") $tmp_where = " AND projekt='$checkprojekt' ";
        //else $tmp_where = "";


        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',name_de) as name FROM artikel AS art WHERE geloescht=0 AND porto = 1 AND  ($subwhere) AND geloescht=0 AND intern_gesperrt!=1 $tmp_where LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;

      case "juststuecklistenartikel":
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',name_de) as name FROM artikel AS art WHERE geloescht=0 AND  ($subwhere) AND geloescht=0 AND intern_gesperrt!=1 AND stueckliste = 1  LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i=0;$i<$carr;$i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
      case "stuecklistenartikel":
        $projekt = $this->app->Secure->GetGET('projekt');
        $tmp_where = '';
        if($projekt != '')
        {
          if(is_numeric($projekt))
          {
            $tmp_where = " AND projekt = '$projekt' ";
          }else{
            $projekt = $this->app->DB->Select("SELECT id FROM projekt WHERE ifnull(geloescht,0) = 0 AND abkuerzung = '$projekt' LIMIT 1");
            if($projekt){
              $tmp_where = " AND projekt = '$projekt' ";
            }
          }
        }
        $juststueckliste = $this->app->Secure->GetGET('juststueckliste');
        if($juststueckliste)
        {
          $swhere = '';
        }else{
          $swhere = ' AND juststueckliste = 0 ';
        }
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',name_de) as name FROM artikel AS art WHERE geloescht=0 AND ($subwhere) AND geloescht=0 AND intern_gesperrt!=1 AND stueckliste = 1  $swhere  $tmp_where LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i=0;$i<$carr;$i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
      break;

      case "artikelstueckliste":
        $stuecklistenartikel = $this->app->Secure->GetGET('stuecklistenartikel');
        $artikelID = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '$stuecklistenartikel'");
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(a.nummer,' ',a.name_de) AS name FROM artikel a LEFT JOIN stueckliste s ON a.id=s.artikel WHERE s.stuecklistevonartikel='$artikelID' AND a.geloescht=0 LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i=0;$i<$carr;$i++){
          $newarr[] = $arr[$i]['name'];
        }
        break;

      case "artikelinstueckliste":
        $stuecklistenartikel = trim($this->app->Secure->GetGET('art'));
        if($stuecklistenartikel != ''){
          $stuecklistenartikel = explode(' ', $stuecklistenartikel);
          $stuecklistenartikelnr = $stuecklistenartikel[0];
          $stuecklistenartikelid = $this->app->DB->Select("SELECT id FROM artikel WHERE nummer = '$stuecklistenartikelnr' LIMIT 1");
          if($stuecklistenartikelid != "" && $stuecklistenartikelid > 0){
            $arr = $this->app->DB->SelectArr("SELECT CONCAT(a.nummer, ' ', a.name_de) as name FROM artikel a LEFT JOIN stueckliste s ON a.id = s.artikel WHERE a.geloescht = 0 AND (a.nummer LIKE '%$term%' OR a.name_de LIKE '%$term%' OR a.herstellernummer LIKE '%$term%' OR a.ean LIKE '%$term%') AND a.intern_gesperrt != 1 AND s.stuecklistevonartikel = '$stuecklistenartikelid'");
            $carr = !empty($arr)?count($arr):0;
            for($i = 0; $i < $carr; $i++) {
              $newarr[] = $arr[$i]['name'];
            }
            break;
          }
        }
      break;

      case "partlistfrom":
        $article_id = $this->app->Secure->GetGET('article_id');
        $arr = $this->app->DB->SelectArr("
          SELECT concat(art.nummer,' ',art.name_de) as name 
          FROM artikel art 
          INNER JOIN (SELECT DISTINCT stuecklistevonartikel FROM stueckliste WHERE artikel = '$article_id') s
          ON  art.id = s.stuecklistevonartikel
          WHERE (concat(art.nummer,' ',art.name_de) LIKE '%$term%' OR concat(art.nummer,' ',art.name_de) LIKE '%$term2%' OR concat(art.nummer,' ',art.name_de) LIKE '%$term3%') 
          ");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++){
          $newarr[] = $arr[$i]['name'];
        }
        break;

      break;
      
      case "artikelnummertagespreise":
        $projekt = $this->app->Secure->GetGET('projekt');
        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        $checkprojekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id='$projekt' LIMIT 1");
        $eigenernummernkreis = $this->app->DB->Select("SELECT eigenernummernkreis FROM projekt WHERE id='$projekt' LIMIT 1");

        if($checkprojekt > 0 && $eigenernummernkreis=='1')
        {
          $tmp_where = " AND projekt='$checkprojekt' ";
        }
        else {
          $tmp_where = '';
        }


        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',name_de) as name FROM artikel WHERE geloescht=0 AND tagespreise = 1 AND  (nummer LIKE '%$term%' OR name_de LIKE '%$term%' OR nummer LIKE '%$term2%' OR name_de LIKE '%$term2%' OR nummer LIKE '%$term3%' OR name_de LIKE '%$term3%' OR herstellernummer LIKE '%$term%' OR ean LIKE '%$term%' ".($artikel_freitext1_suche?" OR freifeld1 LIKE '%$term%' ":"").") AND geloescht=0 AND intern_gesperrt!=1 $tmp_where LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;

      case "keinelagerartikelnummer":
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean','CONCAT(art.nummer,\' \',art.name_de)');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',name_de) as name 
            FROM artikel AS art WHERE ($subwhere) AND geloescht=0  AND intern_gesperrt!=1
            AND (lagerartikel!='1' OR dienstleistung=1) AND porto!=1 AND stueckliste!=1 LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
 
      case "lagerartikelnummer":

        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean','CONCAT(art.nummer,\' \',art.name_de)');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',name_de) as name 
            FROM artikel AS art WHERE ($subwhere) AND geloescht=0  AND intern_gesperrt!=1
            AND lagerartikel='1' LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
        
      case "lagerartikelnummerohnechargemhdseriennummer":
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean','CONCAT(art.nummer,\' \',art.name_de)');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',name_de) as name 
            FROM artikel AS art WHERE ($subwhere) AND geloescht=0  AND intern_gesperrt!=1
            AND lagerartikel='1' AND chargenverwaltung = 0 AND mindesthaltbarkeitsdatum <> 1 AND (seriennummern = '' OR seriennummern = 'keine') ".$this->app->erp->ProjektRechte('projekt')." LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
      case "lagerartikelkategorie":
        $felder = array('bezeichnung');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT bezeichnung FROM artikelkategorien WHERE ($subwhere) AND geloescht=0");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;
      
      case "chargenartikel":
        $felder = array('art.nummer','art.name_de','art.herstellernummer','art.ean','CONCAT(art.nummer,\' \',art.name_de)');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(nummer,' ',name_de) as name 
            FROM artikel AS art WHERE ($subwhere) AND geloescht=0  AND intern_gesperrt!=1
            AND chargenverwaltung > 0  ".$this->app->erp->ProjektRechte('projekt')." LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
        
      case "artikelnummerlager":
        $lager_platz = (int)$this->app->Secure->GetGET('lager_platz');
        $lwhere = '';
        if($lager_platz){
          $lwhere = " and lpi.lager_platz = '$lager_platz' ";
        }
        $felder = array('ar.nummer','ar.name_de','ar.herstellernummer','ar.ean','CONCAT(ar.nummer,\' \',ar.name_de)');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'ar.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);

        // heute 13.07. das INNER zu einem LEFT gemacht
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(ar.nummer,' ',ar.name_de) as name 
            FROM artikel ar LEFT JOIN lager_platz_inhalt lpi ON ar.id=lpi.artikel WHERE ($subwhere) AND ar.geloescht=0  AND ar.intern_gesperrt!=1
            AND ar.lagerartikel='1' $lwhere GROUP BY CONCAT(ar.nummer,' ',ar.name_de) ".$this->app->erp->ProjektRechte('ar.projekt')."  LIMIT 20");

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
        
 
      case "artikelnummerprojektpos":
        $felder = array('a.nummer','a.name_de','a.herstellernummer','a.ean','CONCAT(a.nummer,\' \',a.name_de)','a.herstellernummer');

        if($this->app->erp->Firmendaten('artikel_freitext1_suche'))
        {
          $felder[] = 'a.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $projekt = $this->app->User->GetParameter('pos_list_projekt');
        $projekArr = $this->app->DB->SelectRow(
          sprintf(
            'SELECT id, pos_artikelnurausprojekt,eanherstellerscan 
            FROM projekt WHERE id = %d
            LIMIT 1',
            (int)$projekt
          )
        );
        $checkprojekt = 0;
        $eigenernummernkreis = 0;
        $eanherstellerscan = 0;
        if(!empty($projekArr)){
          $checkprojekt = $projekArr['id'];
          $eigenernummernkreis = $projekArr['pos_artikelnurausprojekt'];
          $eanherstellerscan = $projekArr['eanherstellerscan'];
        }
        if($checkprojekt > 0 && $eigenernummernkreis=='1') {
          $tmp_where = " AND a.projekt='$checkprojekt' ";
        }
        else {
          $tmp_where = '';
        }

        // besser ist wenn man die immer scannt da es oberflächen gibt wo das projekt nicht angegeben werden kann
        if(0)//$eanherstellerscan)	
        {
          $arr = $this->app->DB->SelectArr("SELECT DISTINCT CONCAT(a.nummer,' ',a.name_de,if(a.herstellernummer IS NULL OR a.herstellernummer='','',CONCAT(' PN: ',a.herstellernummer))) as name, a.id FROM artikel a WHERE a.geloescht=0 AND a.intern_gesperrt!=1 AND (a.nummer LIKE '%$term%' OR a.name_de LIKE '%$term%' OR CONCAT(a.nummer,' ',a.name_de) LIKE '%$term%' OR CONCAT(a.nummer,' ',a.name_de) LIKE '%$term2%' OR CONCAT(a.nummer,' ',a.name_de) LIKE '%$term3%'   OR a.herstellernummer LIKE '%$term%' OR a.ean LIKE '%$term%'".($artikel_freitext1_suche?" OR freifeld1 LIKE '%$term%' ":"").") $tmp_where ORDER by a.id DESC LIMIT 20");
        }
        else {
          $arr = $this->app->DB->SelectArr("SELECT DISTINCT CONCAT(a.nummer,' ',a.name_de,if(a.herstellernummer IS NULL OR a.herstellernummer='','',CONCAT(' PN: ',a.herstellernummer))) as name, a.id FROM artikel a WHERE a.geloescht=0 AND a.intern_gesperrt!=1 AND ($subwhere) $tmp_where ORDER by a.id DESC LIMIT 20");
        }
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
        {
          $check_lagerartikel = $this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='".$arr[$i]['id']."' LIMIT 1");
          if($check_lagerartikel)
          {
            $summe_im_lager = (float)$this->app->DB->Select("SELECT ifnull(SUM(li.menge),0) FROM lager_platz_inhalt li LEFT JOIN lager_platz lp ON lp.id=li.lager_platz WHERE li.artikel='".$arr[$i]['id']."'");
            if($summe_im_lager > 0)
            {
              $artikel_reserviert = (float)$this->app->DB->Select("SELECT ifnull(SUM(menge),0) FROM lager_reserviert WHERE artikel='".$arr[$i]['id']."' AND (datum>=NOW() OR datum='0000-00-00')");
            }else $artikel_reserviert = 0;
          }
          if($check_lagerartikel && ($summe_im_lager - $artikel_reserviert) <= 0) {
            $lager=' (Aktuell kein Lagerbestand bzw. durch Aufträge reserviert) ';
          } else {
            $lager='';
          }
          $newarr[] = $arr[$i]['name'].$lager;
          
        }
        break;

 

      case "artikelnummerprojekt":
        $felder = array('a.nummer','a.name_de','a.herstellernummer','a.ean','CONCAT(a.nummer,\' \',a.name_de)','a.herstellernummer');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $projekt = $this->app->Secure->GetGET('projekt');
        $checkprojekt = $this->app->DB->Select("SELECT id FROM projekt WHERE id='$projekt' LIMIT 1");
        $eigenernummernkreis = $this->app->DB->Select("SELECT eigenernummernkreis FROM projekt WHERE id='$projekt' LIMIT 1");
        $eanherstellerscan = $this->app->DB->Select("SELECT eanherstellerscan FROM projekt WHERE id='$projekt'");

        if($checkprojekt > 0 && $eigenernummernkreis=='1') {
          $tmp_where = " AND a.projekt='$checkprojekt' ";
        }
        else {
          $tmp_where = '';
        }

        // besser ist wenn man die immer scannt da es oberflächen gibt wo das projekt nicht angegeben werden kann
        if(0)//$eanherstellerscan)	
        {
          $arr = $this->app->DB->SelectArr("SELECT DISTINCT CONCAT(a.nummer,' ',a.name_de,if(a.herstellernummer IS NULL OR a.herstellernummer='','',CONCAT(' PN: ',a.herstellernummer))) as name FROM artikel a WHERE a.geloescht=0 AND a.intern_gesperrt!=1 AND (a.nummer LIKE '%$term%' OR a.name_de LIKE '%$term%' OR a.herstellernummer LIKE '%$term%' OR CONCAT(a.nummer,' ',a.name_de) LIKE '%$term%' OR CONCAT(a.nummer,' ',a.name_de) LIKE '%$term2%' OR CONCAT(a.nummer,' ',a.name_de) LIKE '%$term3%'   OR a.ean LIKE '%$term%'".($artikel_freitext1_suche?" OR freifeld1 LIKE '%$term%' ":"").") $tmp_where ORDER by a.id DESC LIMIT 20");
        }
        else {
          $arr = $this->app->DB->SelectArr("SELECT DISTINCT CONCAT(a.nummer,' ',a.name_de,if(a.herstellernummer IS NULL OR a.herstellernummer='','',CONCAT(' PN: ',a.herstellernummer))) as name FROM artikel a WHERE a.geloescht=0 AND a.intern_gesperrt!=1 AND ($subwhere) $tmp_where ORDER by a.id DESC LIMIT 20");

        }
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;


      case "lagerartikelnummerprojekt":
        $felder = array('a.nummer','a.name_de','a.herstellernummer','a.ean','CONCAT(a.nummer,\' \',a.name_de)','a.herstellernummer');

        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'art.freifeld1';
        }
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(a.nummer,' ',a.name_de,' (',p.abkuerzung,')') as name FROM artikel a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 AND a.porto=0 AND a.intern_gesperrt!=1  
          AND (
          $subwhere
          ) LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i=0;$i<$carr;$i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;


      case "verkaufartikelnummerprojekt":
        $letzte_menge = null;
        $artikel_freitext1_suche = $this->app->erp->Firmendaten('artikel_freitext1_suche');
        $projekt = $this->app->Secure->GetGET('projekt');
        $projectRow = empty($projekt)?null: $this->app->DB->SelectRow(
          sprintf(
            'SELECT `id`, `eigenernummernkreis`, `projektlager` FROM `projekt` WHERE `id` = %d', $projekt
          )
        );
        $checkprojekt = empty($projectRow)?null:$projectRow['id'];// $this->app->DB->Select("SELECT id FROM projekt WHERE id='$projekt' LIMIT 1");
        $eigenernummernkreis = empty($projectRow)?null:$projectRow['eigenernummernkreis'];//$this->app->DB->Select("SELECT eigenernummernkreis FROM projekt WHERE id='$projekt' LIMIT 1");
        $projectStorage = !empty($projectRow['projektlager'])?$checkprojekt:0;
        $smodule = $this->app->Secure->GetGET('smodule');	
        $sid = $this->app->Secure->GetGET('sid');
        $document = $this->app->DB->SelectRow(sprintf('SELECT * FROM `%s` WHERE `id` = %d', $smodule, $sid));

        $adresse = $document['adresse'];// $this->app->DB->Select("SELECT adresse FROM $smodule WHERE id='$sid' LIMIT 1");
        $waehrung = $document['waehrung'];//$this->app->DB->Select("SELECT waehrung FROM $smodule WHERE id='$sid' LIMIT 1");
        $posanz = (int)$this->app->DB->Select("SELECT count(id) FROM $smodule"."_position WHERE $smodule = '$sid'");
        
        if($posanz == 0)
        {
          $waehrung = '';
        }
        
        $anzeigebrutto = false;
        if($smodule == 'auftrag' || $smodule == 'rechnung' || $smodule == 'gutschrift' || $smodule == 'angebot' || $smodule == 'proformarechnung')
        {
          $_anrede = $this->app->DB->Select("SELECT typ FROM $smodule WHERE id = '$sid' LIMIT 1");
          $_projekt = $this->app->DB->Select("SELECT projekt FROM $smodule WHERE id = '$sid' LIMIT 1");
          $funktion = ucfirst($smodule).'MitUmsatzeuer';
          if($this->app->erp->AnzeigePositionenBrutto($_anrede, $smodule, $_projekt, $adresse,$sid) && $this->app->erp->$funktion($sid))
          {
            $anzeigebrutto = true;
          }
        }
        
        $tmp_where = $this->app->erp->ProjektRechte('p.id', true, '', array(0, $projekt));
        //if($checkprojekt > 0 && $eigenernummernkreis=="1") $tmp_where = $this->app->erp->ProjektRechte();
        //else $tmp_where = "";
        $felder = array('a.nummer','a.name_de','a.ean','a.herstellernummer','a.name_de','CONCAT(a.nummer,\' \',a.name_de)');
        if($artikel_freitext1_suche)
        {
          $felder[] = 'a.freifeld1';
        }

        $artikel_artikelnummer_suche = $this->app->erp->Firmendaten('artikel_artikelnummer_suche');

        if($artikel_artikelnummer_suche){
          $felder[] = 'v.kundenartikelnummer';
          $felder[] = 'e.bestellnummer';

          $artikelnummer_suche_join = " 
                LEFT JOIN `einkaufspreise` AS `e` ON e.artikel=a.id AND e.geloescht = 0 
                  AND e.bestellnummer IS NOT NULL AND (e.gueltig_bis IS NULL OR e.gueltig_bis = '0000-00-00' OR e.gueltig_bis >= CURDATE())
                   AND (e.gueltig_bis > NOW() OR e.gueltig_bis='0000-00-00' OR e.gueltig_bis IS NULL) ";
          if($waehrung === 'EUR') {
            $artikelnummer_suche_join .=  " AND (e.waehrung='EUR' OR e.waehrung = '') ";
          }
          elseif($waehrung != ''){
            $artikelnummer_suche_join .=  " AND e.waehrung='{$waehrung}' ";
          }
          $artikelnummer_suche_join .= "LEFT JOIN `verkaufspreise` AS `v` ON v.artikel=a.id AND v.geloescht = 0 
                AND v.kundenartikelnummer IS NOT NULL 
                AND (v.gueltig_bis > NOW() OR v.gueltig_bis='0000-00-00' OR v.gueltig_bis IS NULL) ";
          if($waehrung === 'EUR') {
            $artikelnummer_suche_join .=  " AND (v.waehrung='EUR' OR v.waehrung = '') ";
          }
          elseif($waehrung != ''){
            $artikelnummer_suche_join .=  " AND v.waehrung = '{$waehrung}' ";
          }

          $artikelnummer_suche_where = '';
        }else{
          $artikelnummer_suche_join = "";
          $artikelnummer_suche_where = "";
        }

        $subwhere = $this->AjaxFilterWhere($termorig,$felder);

        $arr = $this->app->DB->SelectArr(
          "SELECT DISTINCT a.id as id, 
                CONCAT(
                    a.nummer,' ',a.name_de,' (',p.abkuerzung,if(a.lagerartikel=1,'',''),')',
                    if(a.herstellernummer!='',CONCAT(' (PN: ',a.herstellernummer,')'),'')
                    ) as `name` , a.lagerartikel, a.porto, a.keinrabatterlaubt, a.juststueckliste, a.stueckliste
          FROM `artikel` AS `a` 
          LEFT JOIN `projekt` AS `p` ON p.id=a.projekt 
          ".$artikelnummer_suche_join."
          WHERE a.geloescht=0 AND a.intern_gesperrt!=1
          ".$artikelnummer_suche_where."  
          AND ($subwhere) $tmp_where LIMIT 20");


        $rabatt = $this->app->DB->Select("SELECT realrabatt FROM $smodule WHERE id='$sid' LIMIT 1");
        $sql_erweiterung = '';
        $carr = !empty($arr)?count($arr):0;
        for($i=0;$i<$carr;$i++)  {

          $arr[$i]['name'] = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de,if(herstellernummer!='',CONCAT(' (PN: ',herstellernummer,')'),'') ) FROM artikel WHERE id='".$arr[$i]['id']."' LIMIT 1");
          $keinrabatterlaubt = $arr[$i]['keinrabatterlaubt'];//$this->app->DB->Select("SELECT keinrabatterlaubt FROM artikel WHERE id='".$arr[$i]['id']."' LIMIT 1");
          $checkporto = $arr[$i]['porto'];//$this->app->DB->Select("SELECT porto FROM artikel WHERE id='".$arr[$i]['id']."' LIMIT 1");
          $gruppenarray = $this->app->erp->GetGruppen($adresse);
          $cgruppenarray = !empty($gruppenarray)?count($gruppenarray):0;
          if($cgruppenarray >0)
          {
            $sql_erweiterung = ' OR ';
          }
          for($gi=0;$gi<$cgruppenarray;$gi++)
          {
            $sql_erweiterung .= " gruppe='".$gruppenarray[$gi]."' ";

            if($gi<$cgruppenarray-1){
              $sql_erweiterung .= ' OR ';
            }
          }

          $vkarr = $this->app->erp->GeneratePreisliste($arr[$i]['id'],$adresse,$rabatt, $waehrung);

          $check_lagerartikel = $arr[$i]['lagerartikel'];//$this->app->DB->Select("SELECT lagerartikel FROM artikel WHERE id='".$arr[$i]['id']."' LIMIT 1");
          $isJit = $arr[$i]['juststueckliste'];
          //$newarr[]=$arr[$i]['name']." ($label Inkl. Rabatt ".$rabatt."%: ".$this->app->erp->Rabatt($arr[$i]['preis'],$rabatt).")";
          if($isJit) {
            $preproducedpartlist = $this->app->erp->getPreproducedPartlistFromArticle($arr[$i]['id']);
            if(!empty($preproducedpartlist)
              && ($partlistsellable = $this->app->erp->ArtikelAnzahlVerkaufbar($preproducedpartlist, $projektlager))
            ) {
              $lager = ' (Verfügbar: '.round($partlistsellable,4);
            }
            else {
              $lager = (float)$this->app->erp->ArtikelAnzahlLagerStueckliste($arr[$i]['id'], $projectStorage);
              if($lager == 0) {
                $lager=' (Aktuell kein Lagerbestand bzw. durch Aufträge reserviert) ';
              }
              else {
                $lager = ' (Verfügbar: '.round($lager,4);
              }
            }
          }
          elseif($this->app->erp->LagerFreieMenge($arr[$i]['id']) <= 0 && $check_lagerartikel){
            $lager=' (Aktuell kein Lagerbestand bzw. durch Aufträge reserviert) ';
          }
          else{
            if($this->app->erp->Firmendaten('lagerbestand_in_auftragspositionen_anzeigen')){
              $artikel_reserviert = $this->app->DB->Select('SELECT '.$this->app->erp->FormatMenge('SUM(menge)')." FROM lager_reserviert WHERE artikel='".$arr[$i]['id']."' AND (datum>=NOW() OR datum='0000-00-00')");

              $lager=' (Verfügbar: '.$this->app->erp->LagerFreieMenge($arr[$i]['id']);
              if($artikel_reserviert)
              {
                $lager .= ' Reserviert: '.$artikel_reserviert;
              }
              else {
                $lager .= '  Reserviert: 0';
              }
              $lager .= ') ';
            }else{
              $lager='';
            }
          }
          $cvkarr = !empty($vkarr)?count($vkarr):0;
          $letzte_menge = null;
          for($vi=0;$vi<$cvkarr;$vi++)
          {
            if(isset($vkarr[$vi]['ab_menge']))
            {
              $vkarr[$vi]['ab_menge'] = round($vkarr[$vi]['ab_menge'], 8);
            }
            $tmprabatt = $rabatt;
            if($vkarr[$vi]['art']=='Kunde' && ($vkarr[$vi]['adresse']<=0 || $vkarr[$vi]['adresse']=='')){
              $vkarr[$vi]['art'] = 'Standardpreis';
            }

            $preis = 0;
            if($letzte_menge !=$vkarr[$vi]['ab_menge'])
            {
              if($keinrabatterlaubt=='1' || $checkporto=='1')
              {
                $preis = $vkarr[$vi]['preis']; //$this->app->erp->GetVerkaufspreis($arr[$i]['id'],$vkarr[$vi][ab_menge],$adresse);

                $newarr[]=$arr[$i]['name']." $lager ab Menge ".$vkarr[$vi]['ab_menge'].' | Preis: '.$preis.
                  ' ('.$vkarr[$vi]['art'].' - Kein Rabatt erlaubt) ';

              } else {
                if($this->app->erp->IsSpezialVerkaufspreis($arr[$i]['id'],$vkarr[$vi]['ab_menge'],$adresse))
                {
                  $tmprabatt=0;
                  $rabatt_string = ' - Kein Rabatt auf Spezialpreis';
                  $uvp_string = '(UVP: '.$this->app->erp->GetVerkaufspreis($arr[$i]['id'],$vkarr[$vi]['ab_menge'],$adresse).") ";
                } else {
                  if($tmprabatt > 0) {
                    $rabatt_string = ' Inkl. Rabatt '.$tmprabatt.'%'; 
                    $uvp_string = '(UVP: '.$this->app->erp->GetVerkaufspreis($arr[$i]['id'],$vkarr[$vi]['ab_menge'],$adresse).") ";
                  } else {
                    $rabatt_string = '';
                    $uvp_string = '';
                  }
                }
                
                if($anzeigebrutto)
                {
                  $umsatzsteuer = $this->app->DB->Select("SELECT umsatzsteuer FROM artikel WHERE id = '".$arr[$i]['id']."' LIMIT 1");
                  if($umsatzsteuer == 'ermaessigt')
                  {
                    $vkarr[$vi]['preis'] = round($vkarr[$vi]['preis']* (1+ (float)$this->app->DB->Select("SELECT steuersatz_ermaessigt FROM $smodule WHERE id = '$sid' LIMIT 1")/100),8);
                  }elseif($umsatzsteuer != 'befreit')
                  {
                    $vkarr[$vi]['preis'] = round($vkarr[$vi]['preis'] * (1+ (float)$this->app->DB->Select("SELECT steuersatz_normal FROM $smodule WHERE id = '$sid' LIMIT 1")/100),8);
                  }
                }
                
                if($this->app->erp->Firmendaten('viernachkommastellen_belege')){
                  $preis = number_format(rtrim($vkarr[$vi]['preis'], 0), 4, ',', '.'); //$this->app->erp->GetVerkaufspreis($arr[$i]['id'],$vkarr[$vi][ab_menge],$adresse);
                }
                else{
                  $preis = number_format(rtrim($vkarr[$vi]['preis'], 0), 2, ',', '.'); //$this->app->erp->GetVerkaufspreis($arr[$i]['id'],$vkarr[$vi][ab_menge],$adresse);
                }

                $newarr[]=$arr[$i]['name'].($vkarr[$vi]['vpe']!=''?' (Menge in VPE: '.$vkarr[$vi]['vpe'].")":"")." $lager ab Menge ".$vkarr[$vi]['ab_menge']." | Preis: ".$preis.
                  " $uvp_string(".$vkarr[$vi]['art'].$rabatt_string.') ';
              }
            }
          }	

          if($vi==0)
          {
            $rabattartikel = $this->app->DB->Select("SELECT rabatt FROM artikel WHERE id='".$arr[$i]['id']."' LIMIT 1");
            $rabattartikel_prozent = $this->app->DB->Select("SELECT rabatt_prozent FROM artikel WHERE id='".$arr[$i]['id']."' LIMIT 1");
            $arr[$i]['name'] = $this->app->DB->Select("SELECT CONCAT(nummer,' ',name_de,if(herstellernummer!='',CONCAT(' (PN: ',herstellernummer,')'),'')) FROM artikel WHERE id='".$arr[$i]['id']."' LIMIT 1");

            if($rabattartikel=='1'){
              $newarr[] = $arr[$i]['name'] . " $lager ab Menge 1 | Preis: $rabattartikel_prozent% Rabatt auf Gesamtsumme ohne Porto";
            }
            else {
              $preis = $this->app->erp->GetVerkaufspreis($arr[$i]['id'],1,$adresse,$waehrung);
              if($preis > 0){
                $newarr[] = $arr[$i]['name'] . " $lager ab Menge 1 | Preis: $preis";
              }
              else{
                $newarr[] = $arr[$i]['name'] . " $lager ab Menge 1 | Preis: nicht vorhanden";
              }
            }
          }
        }
        break;

      case "einkaufartikelnummerprojekt":

        $smodule = $this->app->Secure->GetGET('smodule');
        $sid = $this->app->Secure->GetGET('sid');

        $waehrung = $this->app->DB->Select("SELECT `waehrung` FROM `{$smodule}` WHERE `id`='{$sid}' LIMIT 1");

        $felder = [
            'a.nummer',
            'a.name_de',
            'e.bezeichnunglieferant',
            'e.bestellnummer',
        ];

        $artikel_artikelnummer_suche = (int)$this->app->erp->Firmendaten('artikel_artikelnummer_suche');

        if($artikel_artikelnummer_suche > 0){
          $felder[] = 'v.kundenartikelnummer';

          $artikelnummer_suche_join = 'LEFT JOIN `verkaufspreise` `v` ON v.artikel=a.id'
              .' AND v.geloescht = 0 AND v.kundenartikelnummer IS NOT NULL '
              . " AND (v.gueltig_bis > NOW() OR v.gueltig_bis='0000-00-00' OR v.gueltig_bis IS NULL) ";
          if($waehrung === 'EUR') {
            $artikelnummer_suche_join .=  "AND (v.waehrung='EUR' OR v.waehrung = '')";
          }
          elseif($waehrung != ''){
            $artikelnummer_suche_join .=  "AND v.waehrung='{$waehrung}'";
          }
          $artikelnummer_suche_where = '';
        }else{
          $artikelnummer_suche_join = "";
          $artikelnummer_suche_where = "";
        }

        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $adresse = (int)$this->app->Secure->GetGET('adresse');

        $sql = "SELECT 
            CONCAT(
              a.nummer,
              ' ',
              a.name_de,
              ' | Bezeichnung bei Lieferant ',
              IFNULL(e.bestellnummer,'nicht vorhanden'),
              ' ',
              LEFT(IFNULL(e.bezeichnunglieferant,'nicht vorhanden'),50),
              ' | ',
              ' ab Menge ',
              ".$this->app->erp->FormatMenge("IFNULL(e.ab_menge,1)").", 
              ' | Preis ',
              ".$this->app->erp->FormatPreis("IFNULL(e.preis,0)").", 
              ' | VPE ',
              ".$this->app->erp->FormatMenge("IF(IFNULL(e.vpe,1)='',1,IFNULL(e.vpe,1))")."
            ) as `name` 
            FROM `artikel` AS `a` 
            LEFT JOIN `projekt` AS `p` ON p.id=a.projekt 
            LEFT JOIN `einkaufspreise` AS `e` ON e.artikel=a.id "
            .($waehrung!=""?"AND IFNULL(e.waehrung,'$waehrung')='$waehrung'":"").
            $artikelnummer_suche_join."
            WHERE a.tagespreise = 0 AND a.geloescht=0 AND a.intern_gesperrt!=1 
            AND (e.gueltig_bis > NOW() OR e.gueltig_bis='0000-00-00' OR e.gueltig_bis IS NULL)".
            $artikelnummer_suche_where."
            AND (IFNULL(e.adresse,0)='$adresse' OR a.allelieferanten=1) 
            AND $subwhere "
            .$this->app->erp->ProjektRechte("a.projekt")."
            GROUP BY a.nummer,a.name_de,e.bezeichnunglieferant,e.bestellnummer, e.preis, e.ab_menge, e.vpe LIMIT 20";

        $arr = $this->app->DB->SelectArr($sql);

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
      case "lieferantname":
        $felder = array('a.nummer','a.name_de','e.bezeichnunglieferant','e.bestellnummer');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT name FROM adresse 
          WHERE geloescht=0 AND a.lieferantennummer!='' AND a.lieferantennummer!='0' AND (
          name LIKE '%$term%' OR name LIKE '%$term2%' OR name LIKE '%$term3%'
         ) order by name LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;

      case "lieferant":
        //$arr = $this->app->DB->SelectArr("SELECT CONCAT(a.lieferantennummer,' ',a.name) as name FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 AND a.lieferantennummer!='' AND lieferantennummer!='0' AND (a.name LIKE '%$term%' OR a.lieferantennummer LIKE '%$term%' OR a.name LIKE '%$term2%' OR a.name LIKE '%$term3%') ".$this->app->erp->ProjektRechte()." order by a.name LIMIT 20");
        /*
        $arr = $this->app->DB->SelectArr("SELECT a.lieferantennummer, 
          
          (SELECT 
              CONCAT(a2.lieferantennummer, ' ',a2.name)
                FROM adresse a2 WHERE a2.lieferantennummer = a.lieferantennummer ".$this->app->erp->ProjektRechte("a2.projekt")." ORDER BY  
              ".($filter_projekt?" a2.projekt = '$filter_projekt' DESC, ":"")." a2.projekt LIMIT 1
          )as name2
        FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 AND a.lieferantennummer!='' AND lieferantennummer!='0' AND (a.name LIKE '%$term%' OR a.lieferantennummer LIKE '%$term%' OR a.name LIKE '%$term2%' OR a.name LIKE '%$term3%') ".$this->app->erp->ProjektRechte()." group by a.lieferantennummer order by name2 LIMIT 20");
        */
        $felder = array("concat(a.lieferantennummer,' ',a.name, if(a.ort!='', CONCAT(' ',a.ort),''))");
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);

        $arr = $this->app->DB->SelectArr("SELECT concat(a.lieferantennummer,' ',a.name, if(a.ort!='', CONCAT(' (',a.ort,')'),'')) as name2
        FROM adresse a INNER JOIN   (SELECT a2.lieferantennummer,  ".($filter_projekt?"  min( if(a2.projekt = ".$filter_projekt.", -1,a2.projekt))":" min(a2.projekt) ")." as mprojekt FROM adresse a2 
        LEFT JOIN adresse_rolle ar2 ON a2.id = ar2.adresse AND ar2.projekt > 0 ".$this->app->erp->ProjektRechte("ar2.projekt")."
              WHERE (a2.geloescht = 0 or isnull(a2.geloescht))  AND a2.lieferantennummer!='' AND a2.lieferantennummer!='0' AND (1 ".$this->app->erp->ProjektRechte("a2.projekt", true, 'a2.vertrieb')." OR not isnull(ar2.id) )
              group by a2.lieferantennummer) adr  
              ON a.lieferantennummer = adr.lieferantennummer AND ".($filter_projekt?"(a.projekt = adr.mprojekt OR a.projekt = $filter_projekt AND adr.mprojekt = -1)":"a.projekt = adr.mprojekt")." 
          LEFT JOIN adresse_rolle ar ON a.id = ar.adresse AND ar.projekt > 0
          WHERE a.geloescht=0 AND a.lieferantennummer!='' AND a.lieferantennummer!='0' AND ($subwhere) 
          group by a.lieferantennummer order by name2 LIMIT 20
        
        ");

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name2'];
        }
        break;

      case "lieferantartikel":
        $felder = array("concat(a.lieferantennummer,' ',a.name)");
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $artikel = (int)$this->app->Secure->GetGET('artikel');
        $arr = $this->app->DB->SelectArr("SELECT concat(a.lieferantennummer,' ',a.name) as name2
        FROM adresse a INNER JOIN   (SELECT a2.lieferantennummer,  ".($filter_projekt?"  min( if(a2.projekt = ".$filter_projekt.", -1,a2.projekt))":" min(a2.projekt) ")." as mprojekt FROM adresse a2 
              WHERE (a2.geloescht = 0 or isnull(a2.geloescht))  AND a2.lieferantennummer!='' AND a2.lieferantennummer!='0'  ".$this->app->erp->ProjektRechte("a2.projekt", true, 'a2.vertrieb')."
              group by a2.lieferantennummer) adr  
              ON a.lieferantennummer = adr.lieferantennummer AND ".($filter_projekt?"(a.projekt = adr.mprojekt OR a.projekt = $filter_projekt AND adr.mprojekt = -1)":"a.projekt = adr.mprojekt")." 
          INNER JOIN einkaufspreise ep ON ep.adresse = a.id AND ep.artikel = '$artikel'
          WHERE a.geloescht=0 AND a.lieferantennummer!='' AND a.lieferantennummer!='0' AND ($subwhere) ".$this->app->erp->ProjektRechte("a.projekt")." group by a.lieferantennummer order by name2 LIMIT 20
        
        ");
        
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name2'];
        }
        break;

      case "adressegruppevertriebbearbeiter":

        $typ = $this->app->Secure->GetGET('typ');
        if($typ === 'vertrieb'){
          $gruppe = $this->app->erp->Firmendaten('group_sales');
        }elseif($typ === 'bearbeiter'){
          $gruppe = $this->app->erp->Firmendaten('group_employee');
        }else{
          $gruppe = '';
        }

        $gruppeJoin = '';
        $gruppeWhere = '';

        if($gruppe !== ''){
          $gruppeKennziffer = explode(' ', $gruppe);
          $gruppeKennziffer = $gruppeKennziffer[0];
          $gruppeId = $this->app->DB->Select("SELECT id FROM gruppen WHERE kennziffer = '$gruppeKennziffer' LIMIT 1");
          if($gruppeId > 0){
            $gruppeJoin =
              ' LEFT JOIN `adresse_rolle` AS `ar` ON a.id = ar.adresse';

            $gruppeWhere =
              ' AND ar.subjekt = \'Mitglied\' AND ar.objekt = \'Gruppe\' AND ar.parameter = \''.$gruppeId.'\' 
              AND ar.von <= CURDATE() AND (ar.bis = \'0000-00-00\' OR ar.bis >= CURDATE())';
          }
        }

        $felder =
          array(
            '(CASE
            WHEN a.lieferantennummer != \'\' THEN CONCAT(a.id,\' \',a.name,\' (Kdr: \',a.kundennummer,\' Liefr: \',a.lieferantennummer,\')\')
            WHEN a.kundennummer != \'\' THEN CONCAT(a.id,\' \',a.name,\' (Kdr: \',a.kundennummer,\')\')
            WHEN a.mitarbeiternummer != \'\' THEN CONCAT(a.id,\' \',a.name,\' (Mitr: \',a.mitarbeiternummer,\')\')
            END)'
          );
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $sql =
          'SELECT 
          (CASE
            WHEN a.lieferantennummer != \'\' THEN CONCAT(a.id,\' \',a.name,\' (Kdr: \',a.kundennummer,\' Liefr: \',a.lieferantennummer,\')\')
            WHEN a.kundennummer != \'\' THEN CONCAT(a.id,\' \',a.name,\' (Kdr: \',a.kundennummer,\')\')
            WHEN a.mitarbeiternummer != \'\' THEN CONCAT(a.id,\' \',a.name,\' (Mitr: \',a.mitarbeiternummer,\')\')
          END) AS `name` 
          FROM `adresse` AS `a` '.$gruppeJoin.' 
          WHERE a.geloescht=0 
          AND ('.$subwhere.') '.$gruppeWhere.$this->app->erp->ProjektRechte('a.projekt').'
          ORDER BY a.name LIMIT 20';

        $arr = $this->app->DB->SelectArr($sql);
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }

        break;

      case "adresse":
        $felder = array("if(a.lieferantennummer,CONCAT(a.name,' ',a.kundennummer,' ',a.lieferantennummer,')'),CONCAT(a.id,' ',a.name,' (Kdr: ',a.kundennummer,')'))");
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $sql = "SELECT if(a.lieferantennummer,CONCAT(a.id,' ',a.name,' (Kdr: ',a.kundennummer,' Liefr: ',a.lieferantennummer,')'),CONCAT(a.id,' ',a.name,' (Kdr: ',a.kundennummer,')')) as `name` 
            FROM adresse a  WHERE a.geloescht=0 AND ($subwhere) ".$this->app->erp->ProjektRechte('a.projekt')." 
            order by a.name LIMIT 20";
        $arr = $this->app->DB->SelectArr($sql);
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
        case "adressemitvertrieb":
          $felder = array("concat(a.name, 
          if(a.kundennummer <> '' OR a.lieferantennummer <> '' OR a.mitarbeiternummer <> '',
            concat(if(a.kundennummer <> '',concat(' ',a.kundennummer),''), if(a.kundennummer <> '' AND a.lieferantennummer <> '',' ',''),if(a.lieferantennummer <> '',concat(' ',a.lieferantennummer),'')
          ,if((a.kundennummer <> '' OR a.lieferantennummer <> '') AND a.mitarbeiternummer <> '',' ',''),
          if(a.mitarbeiternummer <> '',concat(' ',a.mitarbeiternummer),'')
          )
          ,'')
          )");
          $subwhere = $this->AjaxFilterWhere($termorig,$felder);
          $arr = $this->app->DB->SelectArr("SELECT concat(a.id, ' ',a.name, 
          if(a.kundennummer <> '' OR a.lieferantennummer <> '' OR a.mitarbeiternummer <> '',
            concat(' (',if(a.kundennummer <> '',concat('Kdr: ',a.kundennummer),''), if(a.kundennummer <> '' AND a.lieferantennummer <> '',' ',''),if(a.lieferantennummer <> '',concat('Liefr: ',a.lieferantennummer),'')
          ,if((a.kundennummer <> '' OR a.lieferantennummer <> '') AND a.mitarbeiternummer <> '',' ',''),
          if(a.mitarbeiternummer <> '',concat('Mitr: ',a.mitarbeiternummer),'')
          ,')')
          ,'')
          )
          as name
        
          FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 AND ($subwhere) AND ((1 ".$this->app->erp->ProjektRechte().") OR a.id = '".$this->app->User->GetAdresse()."') order by a.name LIMIT 20");
          $carr = !empty($arr)?count($arr):0;
          for($i = 0; $i < $carr; $i++) {
            $newarr[] = $arr[$i]['name'];
          }
        break;
        case 'kundepos':
          $aktprojekt = $this->app->User->GetParameter('pos_list_projekt');
          $felder = array("CONCAT(ifnull(a.kundennummer,''),' ',a.name,if(a.projekt > 0,CONCAT(' (',p.abkuerzung,')'),''),if(ifnull(a.freifeld1,'')!='',CONCAT(' (',a.freifeld1,')'),''))");

          $swhere = '';
          if($aktprojekt && !$this->app->DB->Select("SELECT pos_kundenalleprojekte FROM projekt WHERE id = '$aktprojekt' LIMIT 1") && $this->app->DB->Select("SELECT eigenernummernkreis FROM projekt WHERE id = '$aktprojekt' LIMIT 1")){
            $swhere = " AND p.id = '$aktprojekt' ";
          }
          $subwhere = $this->AjaxFilterWhere($termorig,$felder);
          $arr = $this->app->DB->SelectArr("SELECT CONCAT(a.kundennummer,' ',a.name,if(a.projekt > 0,CONCAT(' (',p.abkuerzung,')'),''),if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),'')) as name FROM adresse a LEFT JOIN projekt p ON p.id=a.projekt WHERE a.geloescht=0 AND a.kundennummer!='' AND a.kundennummer!='0' AND ($subwhere) $swhere order by name LIMIT 20");
          $carr = !empty($arr)?count($arr):0;
          for($i = 0; $i < $carr; $i++) {
            $newarr[] = $arr[$i]['name'];
          }
        break;
        case 'kunde':
            
          $felder = array("CONCAT(a.kundennummer, ' ',a.name,if(ifnull(a.freifeld1,'')!='',CONCAT(' (',ifnull(a.freifeld1,''),')'),''),' ',a.plz,' ',a.ort)");
          if($term2 === $term){
            $term2 = '';
          }
          if($term3 === $term){
            $term3 = '';
          }
          $terma = explode('%', trim(str_replace('%%','%',$term),'%'));
          $terma2 = explode('%', trim(str_replace('%%','%',$term2),'%'));
          $terma3 = explode('%', trim(str_replace('%%','%',$term3),'%'));
          foreach($felder as $v)
          {
            $subwherea[] = " $v LIKE '%$term%' ";
            if($term2 !== '')
            {
              $subwherea[] = " $v LIKE '%$term2%' ";
            }
            if($term3 !== '')
            {
              $subwherea[] = " $v LIKE '%$term3%' ";
            }
          }
          $terma21 = '';
          $terma22 = '';
          $terma31 = '';
          $terma32 = '';
          if(count($terma2) > 1)
          {
            $terma22 = $terma2[count($terma2)-1];
            unset($terma2[count($terma2)-1]);
            $terma21 = implode('%', $terma2);
          }          
          if(count($terma3) > 1)
          {
            $terma32 = $terma3[count($terma3)-1];
            unset($terma3[count($terma3)-1]);
            $terma31 = implode('%', $terma3);
          }
          
          if(count($terma) > 1) {
            $terma12 = $terma[count($terma)-1];
            unset($terma[count($terma)-1]);
            $terma11 = implode('%', $terma);
            $subwheretmpa1 = null;
            $subwheretmpa2 = null;
            foreach($felder as $v)  {
              if($v !== 'a.ort')
              {
                $subwheretmpa1[] = " $v LIKE '%$terma11%' ";
                $subwheretmpa2[] = " $v LIKE '%$terma12%' ";
                if($terma21 !== '')
                {
                  $subwheretmpa1[] = " $v LIKE '%$terma21%' ";
                  $subwheretmpa2[] = " $v LIKE '%$terma22%' ";
                }
                if($terma31 !== '')
                {
                  $subwheretmpa1[] = " $v LIKE '%$terma31%' ";
                  $subwheretmpa2[] = " $v LIKE '%$terma32%' ";
                }
              }
            }
            $subwheretmpa3[] = " a.ort LIKE '%$terma12%' ";
            $subwheretmpa4[] = " a.ort LIKE '%$terma11%' ";
            if($terma21 !== '')
            {
              $subwheretmpa3[] = " a.ort LIKE '%$terma22%' ";
              $subwheretmpa4[] = " a.ort LIKE '%$terma21%' ";
            }
            if($terma31 !== '')
            {
              $subwheretmpa3[] = " a.ort LIKE '%$terma32%' ";
              $subwheretmpa4[] = " a.ort LIKE '%$terma31%' ";
            }            
            
            $subwherea[] = " ((".implode(' OR ', $subwheretmpa3).") AND (".implode(" OR ", $subwheretmpa1).") ) ";
            $subwherea[] = " ((".implode(' OR ', $subwheretmpa4).") AND (".implode(" OR ", $subwheretmpa2).") ) ";
          }

          //$subwhere = implode(' OR ', $subwherea);

          $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        if($this->app->DB->Select('SELECT id FROM projekt WHERE geloescht <> 1 AND eigenernummernkreis = 1 LIMIT 1')){
          $sql = "SELECT CONCAT(a.kundennummer, ' ',a.name,if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),'')) as name, a.ort 
            FROM adresse AS a 
            INNER JOIN
              (
                  SELECT a2.kundennummer,  " . ($filter_projekt ? "  min( if(a2.projekt = " . $filter_projekt . ", -1,a2.projekt))" : " min(a2.projekt) ") . " as mprojekt 
                  FROM adresse a2 
                  WHERE (a2.geloescht = 0 or isnull(a2.geloescht))  AND a2.kundennummer!='' AND a2.kundennummer!='0'  " . $this->app->erp->ProjektRechte("a2.projekt", true, 'a2.vertrieb') . "
                  group by a2.kundennummer
              ) AS adr 
              ON a.kundennummer = adr.kundennummer AND " . ($filter_projekt ? "(a.projekt = adr.mprojekt OR a.projekt = $filter_projekt AND adr.mprojekt = -1)" : "a.projekt = adr.mprojekt") . " 
              WHERE ($subwhere) 
          " . $this->app->erp->ProjektRechte('a.projekt', true, 'a.vertrieb') . '
              GROUP BY a.kundennummer, a.name, a.ort
              ORDER BY `name` LIMIT 20
          ';
        }
        else {
          $sql = "
          SELECT CONCAT(a.kundennummer, ' ',a.name,if(a.freifeld1!='',CONCAT(' (',a.freifeld1,')'),'')) as name, a.ort 
          FROM adresse AS a  
          WHERE ($subwhere) AND kundennummer <> '' AND kundennummer <> '0' AND a.geloescht <> 1
          " . $this->app->erp->ProjektRechte('a.projekt', true, 'a.vertrieb') . '
          GROUP BY a.kundennummer, a.name, a.ort
          ORDER BY `name` LIMIT 20
          ';
        }
          $arr = $this->app->DB->SelectArr($sql);

            
          $carr = !empty($arr)?count($arr):0;
          for($i = 0; $i < $carr; $i++)
          {
            if($arr[$i]['ort']!=''){
              $newarr[] = $arr[$i]['name'] . ' (' . $arr[$i]['ort'] . ')';
            }
            else{
              $newarr[] = $arr[$i]['name'];
            }
          }
        break;
        case "mitarbeiter":
          $felder = array('mitarbeiternummer','name');
          $subwhere = $this->AjaxFilterWhere($termorig,$felder);
          $arr = $this->app->DB->SelectArr("SELECT CONCAT(mitarbeiternummer,' ',name) as name FROM adresse WHERE geloescht=0 AND mitarbeiternummer!='' AND mitarbeiternummer!='0' AND ($subwhere) order by name LIMIT 20");
          $carr = !empty($arr)?count($arr):0;
          for($i = 0; $i < $carr; $i++) {
            $newarr[] = $arr[$i]['name'];
          }
        break;
      case "mitarbeiterid":
        $felder = array('mitarbeiternummer','name');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(id,' ',name) as name FROM adresse WHERE geloescht=0 AND mitarbeiternummer!='' AND mitarbeiternummer!='0' AND ($subwhere) order by name LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
        case "mitarbeiteraktuell":
          $felder = array('mitarbeiternummer','name');
          $subwhere = $this->AjaxFilterWhere($termorig,$felder);
          $arr = $this->app->DB->SelectArr("SELECT DISTINCT CONCAT(a.mitarbeiternummer,' ',a.name) as name FROM adresse a LEFT JOIN adresse_rolle ar ON a.id = ar.adresse WHERE a.geloescht=0 AND a.mitarbeiternummer!='' AND a.mitarbeiternummer!='0' AND ($subwhere) AND ar.subjekt = 'Mitarbeiter' AND (ar.bis = '0000-00-00' OR ar.bis >= CURDATE()) ORDER BY name LIMIT 20");
          $carr = !empty($arr)?count($arr):0;
          for($i = 0; $i < $carr; $i++) {
            $newarr[] = $arr[$i]['name'];
          }
          break;
        case "mitarbeitername":
          $felder = array('name','mitarbeiternummer');
          $subwhere = $this->AjaxFilterWhere($termorig,$felder);
          $arr = $this->app->DB->SelectArr("SELECT name FROM adresse WHERE geloescht=0 AND mitarbeiternummer!='' AND mitarbeiternummer!='0' AND ($subwhere) order by name LIMIT 20");
          $carr = !empty($arr)?count($arr):0;
          for($i = 0; $i < $carr; $i++) {
            $newarr[] = $arr[$i]['name'];
          }
        break;
        
        case "emailadresse":
          if(strpos($term,',')!==false)
          {
            $term = substr($term,strripos($term,','));
            $term = str_replace(',','',$term);
          }
          
          $subwhere1 = '';
          $subwhere2 = '';
          if($this->app->Secure->GetGET('kundennummer')!="")
          {
            //$adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer like '".$this->app->Secure->GetGET('kundennummer')."' ".($filter_projekt?" AND (projekt = '$filter_projekt' or projekt = 0) ":"")." LIMIT 1");
            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE kundennummer like '".$this->app->Secure->GetGET('kundennummer')."' AND geloescht <> 1 ORDER BY ".($filter_projekt?" projekt = '$filter_projekt' DESC,  ":"")." projekt LIMIT 1");
            if($adresse)
            {
              $subwhere1 .= " AND adresse = '$adresse' ";
              $subwhere2 .= " AND id = '$adresse' ";
            }
          }

          if($this->app->Secure->GetGET('kd_lf_ma_nummer')!="")
          {
            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE (kundennummer like '".$this->app->Secure->GetGET('kd_lf_ma_nummer')."' OR lieferantennummer like '".$this->app->Secure->GetGET('kd_lf_ma_nummer')."' OR mitarbeiternummer like '".$this->app->Secure->GetGET('kd_lf_ma_nummer')."') AND geloescht <> 1 ORDER BY ".($filter_projekt?" projekt = '$filter_projekt' DESC,  ":"")." projekt LIMIT 1");
            if($adresse)
            {
              $subwhere1 .= " AND adresse = '$adresse' ";
              $subwhere2 .= " AND id = '$adresse' ";
            }
          }

          if($this->app->Secure->GetGET('kd_id')){
            $adresse = $this->app->DB->Select("SELECT id FROM adresse WHERE id = '".$this->app->Secure->GetGET('kd_id')."' AND geloescht != 1 ORDER BY ".($filter_projekt?"projekt = '$filter_projekt' DESC, ":"")." projekt LIMIT 1");
            if($adresse){
              $subwhere1 .= " AND adresse = '$adresse' ";
              $subwhere2 .= " AND id = '$adresse' ";
            }
          }

          $limit = "LIMIT 20";
          if ($this->app->Secure->GetGET('limit')) {
            $limit = "LIMIT 1";
          }

          if($limit=="LIMIT 1")
          {
            $arr = $this->app->DB->SelectArr("SELECT email FROM ansprechpartner WHERE email <> '' $subwhere1 AND geloescht <> 1 order by name $limit");
          } else {
            $arr = $this->app->DB->SelectArr("SELECT email FROM ansprechpartner WHERE (name LIKE '%$term%' OR email LIKE '%$term%') and email <> '' $subwhere1 AND geloescht <> 1 order by name $limit");
          }
          $carr = !empty($arr)?count($arr):0;
          for($i = 0; $i < $carr; $i++) {
            $newarr[] = $arr[$i]['email'];
          }
          if($limit=="LIMIT 1")
          {
            $arr = $this->app->DB->SelectArr("SELECT email  FROM adresse WHERE geloescht!='1' $subwhere2 order by name $limit");
          }
          else
          {
            $arr = $this->app->DB->SelectArr("SELECT email  FROM adresse WHERE (name LIKE '%$term%' OR email LIKE '%$term%') and email <> '' AND geloescht!='1' $subwhere2 order by name $limit");
          }

          $carr = !empty($arr)?count($arr):0;
          for($i = 0; $i < $carr; $i++) {
            $newarr[] = $arr[$i]['email'];
          }

          $newarr = array_unique($newarr); 
          sort($newarr);
        break;
        case "emailbackup":
          $arr = $this->app->DB->SelectArr("SELECT email FROM emailbackup");
          $carr = !empty($arr)?count($arr):0;
          for($i = 0; $i < $carr; $i++) {
            $newarr[] = $arr[$i]['email'];
          }

          $newarr = array_unique($newarr);
          sort($newarr);
          break;
        case "emailname":

        $felder = array("CONCAT(name,' -ltrep-',email,'-gtrep-')",'name','email');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        if(strpos($term,',')!==false)
        {
          $term = substr($term,strripos($term,','));
          $term = str_replace(',','',$term);
        }

        $arr = $this->app->DB->SelectArr("SELECT CONCAT(name,' -ltrep-',email,'-gtrep-') as name FROM ansprechpartner WHERE ($subwhere) AND geloescht <> 1 order by name LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }

        $arr = $this->app->DB->SelectArr("SELECT CONCAT(name,' -ltrep-',email,'-gtrep-') as name FROM adresse WHERE ($subwhere) AND geloescht!='1' order by name LIMIT 20");

        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++){
          $newarr[] = $arr[$i]['name'];
        }

        $newarr = array_unique($newarr); 
        sort($newarr);
        $isChangeLtGt = true;
        break;

        case "shopname":
        $arr = $this->app->DB->SelectArr("SELECT s.bezeichnung FROM shopexport s LEFT JOIN projekt p ON p.id=s.projekt  WHERE s.bezeichnung LIKE '%$term%' ".$this->app->erp->ProjektRechte("s.projekt"));
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;

        case "shopnameid":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(id,' ',bezeichnung) as bezeichnung FROM shopexport WHERE bezeichnung LIKE '%$term%' ".$this->app->erp->ProjektRechte("projekt")."");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;

        case "gruppekennziffer":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(g.kennziffer,' ',g.name) as bezeichnung FROM gruppen g LEFT JOIN projekt p ON p.id=g.projekt  
          WHERE (g.name LIKE '%$term%' OR g.kennziffer LIKE '%$term%') AND g.aktiv=1 ".$this->app->erp->ProjektRechte());
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;

        case "preisgruppekennziffer":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(g.kennziffer,' ',g.name) as bezeichnung FROM gruppen g LEFT JOIN projekt p ON p.id=g.projekt  
          WHERE (g.name LIKE '%$term%' OR g.kennziffer LIKE '%$term%') AND g.art = 'preisgruppe' AND g.aktiv=1 ".$this->app->erp->ProjektRechte());
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;
        
        case "gruppe":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(g.name,' ',g.kennziffer) as bezeichnung FROM gruppen g 
          LEFT JOIN projekt p ON p.id=g.projekt WHERE (g.name LIKE '%$term%' OR g.kennziffer LIKE '%$term%') AND g.aktiv=1 ".$this->app->erp->ProjektRechte());
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;

            case "verband":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(name,' ',kennziffer) as bezeichnung FROM gruppen WHERE (name LIKE '%$term%' OR kennziffer LIKE '%$term%') AND aktiv=1 AND art='verband'");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;

        case "verbindlichkeit":
        $felder = array("CONCAT(v.belegnr, v.betrag, a.name, v.rechnung, a.lieferantennummer, a.lieferantennummer_buchhaltung, ".$this->app->erp->FormatPreis('v.betrag',2).")",
          'v.id','IFNULL(v.belegnr, \'\')','v.betrag','a.name','v.rechnung','a.lieferantennummer','a.lieferantennummer_buchhaltung',$this->app->erp->FormatPreis('v.betrag',2));
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        if(strpos($term,',')!==false)
        {
          $term = substr($term,strripos($term,','));
          $term = str_replace(',','',$term);
        }

        $sql =
          "SELECT CONCAT(v.id,
          IF(IFNULL(v.belegnr, '') <> '' AND v.belegnr!=v.id,
          CONCAT(' Nr. ',v.belegnr),''),
          ' Betrag: ',".$this->app->erp->FormatPreis('v.betrag',2).",
          if(v.skonto <> 0,CONCAT(' mit Skonto ',v.skonto,'% ',
          ".$this->app->erp->FormatPreis("v.betrag-((v.betrag/100.0)*v.skonto)",2)."),''),' ',
                    ' Ist: ',".$this->app->erp->FormatPreis('v.betragbezahlt',2).",
          ' Offen: ',".$this->app->erp->FormatPreis(
            'IF(v.betrag - v.betragbezahlt > ((v.betrag/100.0)*v.skonto), 
            v.betrag - v.betragbezahlt,0)',2
          ).",
          
          a.name,' (Lieferant ',a.lieferantennummer,if(a.lieferantennummer_buchhaltung!='' AND a.lieferantennummer <> a.lieferantennummer_buchhaltung,CONCAT(' ',a.lieferantennummer_buchhaltung),''),') RE ',v.rechnung,' Rechnungsdatum ',DATE_FORMAT(v.rechnungsdatum,'%d.%m.%Y')) as bezeichnung 
          FROM verbindlichkeit AS v 
          LEFT JOIN adresse AS a ON a.id=v.adresse 
        WHERE ($subwhere) AND bezahlt!=1 AND status!='storniert' 
        ORDER by v.id DESC"; //AND v.status!='bezahlt' // heute wieder raus

        $arr = $this->app->DB->SelectArr($sql);
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;

            case "projektname":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(p.abkuerzung,' ',p.name) as name FROM projekt p WHERE p.geloescht=0 AND status <> 'abgeschlossen' AND (p.name LIKE '%$term%' OR p.name LIKE '%$term2%' OR p.name LIKE '%$term3%' OR p.abkuerzung LIKE '%$term%' OR p.abkuerzung LIKE '%$term2%' OR p.abkuerzung LIKE '%$term3%') ".$this->app->erp->ProjektRechte());
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
        
      case "uebertragung_account":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(u.id,' ',u.bezeichnung) as name FROM uebertragungen_account u WHERE (u.bezeichnung LIKE '%$term%') ".$this->app->erp->ProjektRechte('u.projekt'));
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
      break;
      case "api_account":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(u.id,' ',u.bezeichnung) as name FROM api_account u WHERE (u.bezeichnung LIKE '%$term%') ".$this->app->erp->ProjektRechte('u.projekt'));
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
      break;
      case "gruppen_kategorien":
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(g.id,' ',g.bezeichnung) as name FROM gruppen_kategorien g 
          LEFT JOIN projekt p ON p.id=g.projekt WHERE (g.bezeichnung LIKE '%$term%' ) ".$this->app->erp->ProjektRechte());
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
        break;
      case "gruppenkategoriegruppen":
        $kategorie = (int)$this->app->Secure->GetGET('gkid');
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(g.kennziffer,' ',g.name) as bezeichnung FROM gruppen g 
          LEFT JOIN projekt p ON p.id=g.projekt WHERE kategorie = '$kategorie' AND (g.name LIKE '%$term%' OR g.kennziffer LIKE '%$term%') AND g.aktiv=1 ".$this->app->erp->ProjektRechte());
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        
      break;
      case 'steuersatz':
        $newarr[] = $this->app->erp->Firmendaten('steuersatz_normal').' normal';
        $newarr[] = $this->app->erp->Firmendaten('steuersatz_ermaessigt').' ermaessigt';
        $newarr = array_merge($newarr, $this->app->DB->SelectFirstCols(
          "SELECT concat(
                `satz`,' ',`bezeichnung`, ' ',`country_code`, 
                IF(`type` != '', CONCAT(' ',`type`),''),
                IF(
                    `valid_from` = '0000-00-00' OR `valid_from` IS NULL,
                    '',
                    CONCAT(' gültig ab: ',DATE_FORMAT(valid_from,'%d.%m.%Y'))
                ),
                IF(
                    `valid_to` = '0000-00-00' OR `valid_to` IS NULL,
                    '',
                    CONCAT(' gültig bis: ',DATE_FORMAT(valid_to,'%d.%m.%Y'))
                )
            ) as `name` 
          FROM `steuersaetze` WHERE `aktiv` = 1 
          AND concat(
                `satz`,' ',`bezeichnung`,' ',`country_code`, 
                IF(`type` != '', CONCAT(' ',`type`),''),
                IF(
                    `valid_from` = '0000-00-00' OR `valid_from` IS NULL,
                    '',
                    CONCAT(' gültig ab: ',DATE_FORMAT(valid_from,'%d.%m.%Y'))
                ),
                IF(
                    `valid_to` = '0000-00-00' OR `valid_to` IS NULL,
                    '',
                    CONCAT(' gültig bis: ',DATE_FORMAT(valid_to,'%d.%m.%Y'))
                )
            ) LIKE '%$term%'"
        ));
        break;
      case "eigenschaftname":
        $arr = $this->app->DB->SelectArr("SELECT name FROM artikeleigenschaften WHERE geloescht != 1 AND name like '%$term%' ORDER BY name ");
        if($arr)
        {
          $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['name'];
        }
          break;          
        }
      break;
      case "eigenschaftwert":
        $eigenschaftname = $this->app->DB->real_escape_string(urldecode($this->app->Secure->GetGET('eigenschaftname')));
        if($eigenschaftname !== '')
        {
          $arr = $this->app->DB->SelectArr("SELECT DISTINCT aw.wert FROM artikeleigenschaftenwerte aw
          INNER JOIN artikeleigenschaften ae ON aw.artikeleigenschaften = ae.id AND ae.geloescht <> 1
          WHERE aw.wert like '%$term%' AND ae.name = '$eigenschaftname' ORDER BY aw.wert ");
        }else{
          $arr = $this->app->DB->SelectArr("SELECT DISTINCT aw.wert FROM artikeleigenschaftenwerte aw
          INNER JOIN artikeleigenschaften ae ON aw.artikeleigenschaften = ae.id AND ae.geloescht <> 1
          WHERE aw.wert like '%$term%'  ORDER BY aw.wert");
        }
        if($arr)
        {
          $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++)
            $newarr[] = $arr[$i]['wert'];
          break;          
        }
        
        
      break;
      case "angebot_position":
        $angebot = $this->app->Secure->GetGET('angebot');
        $angebotposition = $this->app->Secure->GetGET('angebotposition');
        $arr = $this->app->DB->SelectArr("SELECT CONCAT(ap.sort,' ',ap.nummer) as bezeichnung FROM angebot_position ap INNER JOIN angebot a ON ap.angebot = a.id AND a.id = '$angebot'
          LEFT JOIN projekt p ON p.id=a.projekt 
          WHERE  (ap.sort LIKE '%$term%' OR ap.nummer LIKE '%$term%') AND ap.explodiert_parent = 0 AND ap.id <> '$angebotposition' ".$this->app->erp->ProjektRechte());
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;
      break;
      case "supportapp_gruppen":
      $suchbegriff = $this->app->DB->real_escape_string($this->app->Secure->GetGET('term'));
      $suchbegriff = trim($suchbegriff);
        $arr = $this->app->DB->SelectArr("SELECT bezeichnung FROM supportapp_gruppen WHERE aktiv = '1' AND bezeichnung LIKE '%$suchbegriff%'");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['bezeichnung'];
        }
        break;
      break;
      case "datevkonto":
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT t.gegenkonto FROM
        ( (SELECT  concat(datevkonto, ' ',bezeichnung) as gegenkonto FROM konten WHERE datevkonto <> 0 AND datevkonto <> '' AND aktiv = 1)
          UNION ALL (SELECT  concat(sachkonto, ' ',beschriftung) as gegenkonto FROM kontorahmen WHERE sachkonto <> 0 AND sachkonto <> '' AND ausblenden <> 1)
        
        ) t WHERE t.gegenkonto <> '' AND t.gegenkonto LIKE '%$term%' ORDER BY t.gegenkonto");
        $carr = !empty($arr)?count($arr):0;
        for($i=0;$i<$carr;$i++) {
          $newarr[] = $arr[$i]['gegenkonto'];
        }
        break;
      case 'gegenkonto':

        $kontorahmenArr = $this->app->DB->SelectPairs(
          "SELECT concat(kr.sachkonto, ' ',kr.beschriftung) as a, kr.sachkonto 
          FROM kontorahmen as kr 
          WHERE kr.ausblenden <> 1"
        );
        $subwhere = '  ';
        if(!empty($kontorahmenArr)) {
          $kontorahmenArr = array_unique(array_merge(array_keys($kontorahmenArr), array_values($kontorahmenArr)));
          $subwhere = sprintf(" AND ka.gegenkonto NOT IN ('%s') ", implode("','", $kontorahmenArr));
        }

        $arr = $this->app->DB->SelectArr(
          "SELECT DISTINCT t.gegenkonto 
        FROM
        (
            ( 
                SELECT concat(ka.gegenkonto,' ',ka.name) as gegenkonto
                FROM (SELECT ka2.gegenkonto,a.name
                  FROM kontoauszuege ka2
                  INNER JOIN adresse a ON ka2.gegenkonto = a.kundennummer OR ka2.gegenkonto = a.lieferantennummer
                  WHERE ka2.gegenkonto <> ''
                  GROUP BY ka2.gegenkonto, a.name
                ) AS ka
                WHERE ka.gegenkonto <> '' $subwhere
            )
          UNION ALL (
              SELECT  concat(datevkonto, ' ',bezeichnung) 
              FROM konten 
              WHERE datevkonto <> 0 AND datevkonto <> '' AND aktiv = 1
              AND concat(datevkonto, ' ',bezeichnung) LIKE '%$term%' 
              )
          UNION ALL (
              SELECT  concat(sachkonto, ' ',beschriftung) 
              FROM kontorahmen 
              WHERE sachkonto <> 0 AND sachkonto <> '' AND ausblenden <> 1
                AND concat(sachkonto, ' ',beschriftung) LIKE '%$term%' 
              )
        
        ) t 
        WHERE t.gegenkonto <> '' AND t.gegenkonto LIKE '%$term%' 
        ORDER BY t.gegenkonto"
        );


      if($this->app->DB->error()){
        $arr = $this->app->DB->SelectArr(
          "SELECT DISTINCT t.gegenkonto 
        FROM
        (
            ( 
                SELECT concat(ka.gegenkonto,' ',(
                    SELECT a.name 
                    FROM adresse a 
                    WHERE (a.kundennummer=ka.gegenkonto OR a.lieferantennummer=ka.gegenkonto) AND ka.gegenkonto!='')
                    ) as gegenkonto 
                FROM kontoauszuege ka 
                LEFT JOIN kontorahmen kr 
                    ON (ka.gegenkonto = kr.sachkonto OR ka.gegenkonto = concat(kr.sachkonto, ' ',kr.beschriftung)) 
                           AND kr.ausblenden <> 1
                WHERE isnull(kr.id) AND ka.gegenkonto <> ''
                GROUP BY ka.gegenkonto
            )
          UNION ALL (
              SELECT  concat(datevkonto, ' ',bezeichnung) 
              FROM konten 
              WHERE datevkonto <> 0 AND datevkonto <> '' AND aktiv = 1
              AND concat(datevkonto, ' ',bezeichnung) LIKE '%$term%' 
              )
          UNION ALL (
              SELECT  concat(sachkonto, ' ',beschriftung) 
              FROM kontorahmen 
              WHERE sachkonto <> 0 AND sachkonto <> '' AND ausblenden <> 1
                AND concat(sachkonto, ' ',beschriftung) LIKE '%$term%' 
              )
        
        ) t 
        WHERE t.gegenkonto <> '' AND t.gegenkonto LIKE '%$term%' 
        ORDER BY t.gegenkonto"
        );
      }
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['gegenkonto'];
        }
        break;
        
      break;
      case 'versand_klaergrund':
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT problemcase FROM
        delivery_problemcase WHERE problemcase LIKE '%$term%' ORDER BY sort, problemcase");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['problemcase'];
        }
        break;
      case 'label_type':
        $felder = array('type');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr("SELECT DISTINCT lt.type FROM label_type AS lt WHERE ($subwhere) ORDER BY type LIMIT 20");
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['type'];
        }
        break;
      case 'versandartentype':
        $felder = array('va.type');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr(
          "SELECT DISTINCT va.type 
          FROM versandarten AS va 
          WHERE ($subwhere) AND va.geloescht <> 1 AND va.aktiv = 1 
          ORDER BY va.type 
          LIMIT 20"
        );
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['type'];
        }
        break;
      case 'zahlungsweisetype':
        $felder = array('va.type');
        $subwhere = $this->AjaxFilterWhere($termorig,$felder);
        $arr = $this->app->DB->SelectArr(
          "SELECT DISTINCT va.type 
          FROM zahlungsweisen AS va 
          WHERE ($subwhere) AND va.geloescht <> 1 AND va.aktiv = 1 
          ORDER BY va.type 
          LIMIT 20"
        );
        $carr = !empty($arr)?count($arr):0;
        for($i = 0; $i < $carr; $i++) {
          $newarr[] = $arr[$i]['type'];
        }
        break;
      case 'ticketcategory':
        $newarr = $this->app->DB->SelectFirstCols(
          sprintf(
            "SELECT CONCAT(`id`,' ',`name`) FROM `ticket_category` WHERE (`name` LIKE '%%%s%%' OR `name` LIKE '%%%s%%') %s",
            $term, $term2, $this->app->erp->ProjektRechte('project_id')
          )
        );
        break;
      case 'shopimport_auftraege':
        $shopId = $this->app->Secure->GetGET('id');
        $newarr = $this->app->DB->SelectFirstCols(
          sprintf(
            "SELECT CONCAT(sa.`extid`,' ',IFNULL(sa.`bestellnummer`,'')) 
            FROM  `shopimport_auftraege` AS `sa` 
            WHERE (
                CONCAT(sa.`extid`,' ',IFNULL(sa.`bestellnummer`,'')) LIKE '%%%s%%' 
                OR CONCAT(sa.`extid`,' ',IFNULL(sa.`bestellnummer`,'')) LIKE '%%%s%%'
                )  AND (%d = 0 OR %d = sa.shopid) %s",
            $term, $term2, $shopId, $shopId, $this->app->erp->ProjektRechte('sa.projekt')
          )
        );

        break;
      default:
        $newarr = null;
        $this->app->erp->RunHook('ajax_filter_hook1', 5,$filtername,$newarr, $term, $term2, $term3);
    }

    $tmp = null;
    //if(isset($this->app->stringcleaner) && false)
    if(false)
    {
      $cnewarr = $newarr?count($newarr):0;
      for($i=0;$i<$cnewarr;$i++){
        $tmp[] = $this->app->erp->ClearDataBeforeOutput($this->app->stringcleaner->CleanString(html_entity_decode( $this->app->stringcleaner->CleanString($newarr[$i], 'nojs'), ENT_QUOTES, 'UTF-8'), 'nojs'));
      }
    }else{
      $cnewarr = !empty($newarr)?count($newarr):0;
      for($i=0;$i<$cnewarr;$i++) {
        $tmp[] = $this->app->erp->ClearDataBeforeOutput(html_entity_decode($newarr[$i], ENT_QUOTES, 'UTF-8'));
      }
    }

    if(!empty($isChangeLtGt)){
      $ctmp = !empty($tmp)?count($tmp):0;
      for($i=0;$i<$ctmp;$i++){
        $tmp[$i] = str_replace('-gtrep-','>',str_replace('-ltrep-','<',$tmp[$i]));
      }
    }

    echo json_encode($tmp);
    $this->app->erp->ExitWawi();
  }

  public function AjaxTablePosition()
  {

    $iDisplayStart = $this->app->Secure->GetGET('iDisplayStart');
    $iDisplayLength = $this->app->Secure->GetGET('iDisplayLength');
    $iSortCol_0 = $this->app->Secure->GetGET('iSortCol_0');
    $iSortingCols = $this->app->Secure->GetGET('iSortingCols');
    $sSearch = $this->app->Secure->GetGET('sSearch');
    $sEcho = $this->app->Secure->GetGET('sEcho');
    $cmd = $this->app->Secure->GetGET('cmd');


    $sLimit = '';
    if ( isset($iDisplayStart) )
    {

      $sLimit = 'LIMIT '. (int)$iDisplayStart .', '.
        (int)$iDisplayLength ;
    }

    /* Ordering */
    if ( isset( $iSortCol_0 ) )
    {
      $sOrder = 'ORDER BY  ';
      $ciSortingCols = (int)$iSortingCols;
      for ( $i=0 ; $i<$ciSortingCols ; $i++ )
      {
        $iSortingCols_tmp = $this->app->Secure->GetGET('iSortCol_'.$i);
        $sSortDir_tmp = $this->app->Secure->GetGET('sSortDir_'.$i);

        $sOrder .= $this->fnColumnToFieldPosition($iSortingCols_tmp ).'
          '. $sSortDir_tmp  .', ';
      }
      $sOrder = substr_replace( $sOrder, '', -2 );
    }

    /* Filtering - NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */


    $sWhere = '';
    $where = $this->app->YUI->TablePositionSearch('',$cmd,'where');
    if ( $sSearch != '' )
    {
      $searchsql = $this->app->YUI->TablePositionSearch('',$cmd,'searchsql');

      if($where==''){
        $sWhere = ' WHERE (';
      }
      else
      {
        if(!empty($searchsql) && count($searchsql) >0){
          $sWhere = " WHERE $where AND (";
        }
        else{
          $sWhere = " WHERE $where ";
        }
      }

      
      for($i=0;$i<count($searchsql)-1;$i++)
      {
        $sWhere .= $searchsql[$i]." LIKE '%".$this->app->DB->real_escape_string($sSearch )."%' OR ";
      }
      $sWhere .= $searchsql[$i]." LIKE '%".$this->app->DB->real_escape_string($sSearch )."%')";

    
    } else {
      if($where!=''){
        $sWhere = " WHERE $where ";
      }
    } 

   
    $searchfulltext = $this->app->YUI->TablePositionSearch('',$cmd,'searchfulltext');
    if($searchfulltext!='' && $sSearch!='')
    {
      $searchfulltext = ' MATCH('.$searchfulltext.") AGAINST ('$sSearch') ";
      if($sWhere==''){
        $sWhere = " WHERE $searchfulltext ";
      }
      else{
        $sWhere .= "AND $searchfulltext ";
      }
    }
    $tmp = $this->app->YUI->TablePositionSearch('',$cmd,'sql');


    $sQuery = "
      $tmp
      $sWhere 
      $sOrder
      $sLimit
      ";


    $rResult = $this->app->DB->Query($sQuery);



    $sQuery = '
      SELECT FOUND_ROWS()
      ';
    $rResultFilterTotal = $this->app->DB->Query($sQuery);
    if(!empty($rResultFilterTotal)){
      $aResultFilterTotal = $this->app->DB->Fetch_Array($rResultFilterTotal);
      $this->app->DB->free($rResultFilterTotal);
      $iFilteredTotal = $aResultFilterTotal[0];
    }else{
      $aResultFilterTotal = 0;
      $iFilteredTotal = 0;
    }

    /*    
          $sQuery = "
          SELECT COUNT(id)
          FROM   artikel
          ";
     */
    $sQuery = $this->app->YUI->TablePositionSearch('',$cmd,'count');
    $rResultTotal = $this->app->DB->Query($sQuery);
    $aResultTotal = $this->app->DB->Fetch_Array($rResultTotal);
    $this->app->DB->free($rResultTotal);
    $iTotal = $aResultTotal[0];


    $heading = count($this->app->YUI->TablePositionSearch('',$cmd,'heading'));
    $menu = $this->app->YUI->TablePositionSearch('',$cmd,'menu');

    $sOutput = '{';
    $sOutput .= '"sEcho": '.(int)$sEcho.', ';
    $sOutput .= '"iTotalRecords": '.$iTotal.', ';
    $sOutput .= '"iTotalDisplayRecords": '.$iFilteredTotal.', ';
    $sOutput .= '"aaData": [ ';
    if($rResult){
      while ($aRow = $this->app->DB->Fetch_Row($rResult)) {
        $sOutput .= '[';
        for ($i = 1; $i < $heading; $i++) {
          $sOutput .= '"' . addslashes($aRow[$i]) . '",';
        }

        $sOutput .= '"' . addslashes(str_replace('%value%', $aRow[$i], $menu)) . '"';

        $sOutput .= '],';

      }
      $this->app->DB->free($rResult);
    }
    $sOutput = substr_replace( $sOutput, '', -1 );
    $sOutput .= '] }';

    $sOutput = str_replace("\t",'',$sOutput);

    echo json_encode(json_decode($this->app->erp->ClearDataBeforeOutput($sOutput)));
    $this->app->erp->ExitWawi();
  }
  
  protected function AjaxTableWhereBuilder($spalte, $sSearch, $datecol = false, $numbercol = false)
  {
    $sSearch = str_replace('&#37;','%',$sSearch);
    $sSearcha = explode(' ', $sSearch);
    $sSearch = str_replace(' ','%',$sSearch);


    if($spalte == 'datum' || $datecol)
    {
      return ' ( DATE_FORMAT('.$spalte.",'%d.%m.%Y %H:%i:%s') LIKE '%".$this->app->DB->real_escape_string($sSearch )."%' OR ".$spalte." LIKE '%".$this->app->DB->real_escape_string($sSearch )."%') ";
    }
    if(strpos($spalte, 'datum')) {
      $spaltea = explode('.', $spalte);
      if(count($spaltea) === 2){
        return ' ( DATE_FORMAT('.$spalte.",'%d.%m.%Y %H:%i:%s') LIKE '%".$this->app->DB->real_escape_string($sSearch )."%' OR ".$spalte." LIKE '%".$this->app->DB->real_escape_string($sSearch )."%') ";
      }
    }elseif(($numbercol || $spalte === 'soll' || $spalte === 'gesamtsumme') && strpos($sSearch,','))
    {
      return ' ( '.$spalte." LIKE '%".$this->app->DB->real_escape_string(str_replace(',','.',$sSearch) )."%' OR ".$spalte." LIKE '%".$this->app->DB->real_escape_string($sSearch )."%') ";
    }elseif((strpos($spalte, 'soll') || strpos($spalte, 'gesamtsumme'))  && strpos($sSearch,','))
    {
      $spaltea = explode('.', $spalte);
      if(count($spaltea) === 2){
        return ' ( '.$spalte." LIKE '%".$this->app->DB->real_escape_string(str_replace(',','.',$sSearch) )."%' OR ".$spalte." LIKE '%".$this->app->DB->real_escape_string($sSearch )."%') ";
      }
    }
    if(count($sSearcha) > 1)
    {
      return ' ('.$this->AjaxTableWhereBuilderArray($spalte, $sSearcha).')';
    }
    return ' ('.$spalte." LIKE '%".$this->app->DB->real_escape_string($sSearch )."%')";
  }

  protected function AjaxTableWhereBuilderArray($column, $sSearcha, $sSearcha2 = null)
  {
    if(empty($column) || empty($sSearcha))
    {
      return '';
    }
    $csSearcha = count($sSearcha);
    $tmp = [];
    foreach($sSearcha as $v)
    {
      $tmp[strtolower($v)] = 1+(!empty($tmp[strtolower($v)])?$tmp[strtolower($v)]:0);
    }
    if(count($tmp) === 1)
    {
      $sSearch = implode('%', $sSearcha);

      $sSearch2 = $this->app->erp->ConvertForTableSearch($sSearch);
      if($sSearch2 === '')
      {
        $sWhere = "({$column} LIKE '%" . $this->app->DB->real_escape_string($sSearch) . "%') ";
      }else{
        $sWhere = "({$column} LIKE '%" . $this->app->DB->real_escape_string($sSearch) . "%' OR 
            {$column} LIKE '%" . $this->app->erp->ConvertForTableSearch($sSearch) . "%' ) ";
      }
      return $sWhere;
    }

    if(!empty($sSearcha2))
    {
      $wherea = [];
      foreach($tmp as $v => $c) {
        if($c > 1)
        {
          $vold = $v;
          for($i = 1; $i < $c; $i++)
          {
            $v .= '%'.$vold;
          }
        }
        $vt = $this->app->erp->ConvertForTableSearch($v);
        if($vt === '' || $vt === $v)
        {
          $vt = $this->app->erp->ConvertForDBUTF8($v);
        }
        if($v === '')
        {
          $v = $vt;
        }
        if($v !== $vt && $vt !== ''){
          $wherea[] = ' (' . $column . " LIKE '%" . $v . "%' OR " . $column . " LIKE '%" . $vt . "%') ";
        }else{

          $wherea[] = ' (' . $column . " LIKE '%" . $v . "%') ";
        }
      }
      return '('.implode(' AND ',$wherea).')';
    }else{
      $wherea = [];
      foreach($tmp as $v => $c) {
        if($c > 1)
        {
          $vold = $v;
          for($i = 1; $i < $c; $i++)
          {
            $v .= '%'.$vold;
          }
        }
        $wherea[] = $column." LIKE '%".$v."%'";
      }
      return '('.implode(' AND ',$wherea).')';
    }
  }



  public function AjaxTable()
  {
    $iDisplayStart = $this->app->Secure->GetGET('iDisplayStart');
    $iDisplayLength = $this->app->Secure->GetGET('iDisplayLength');
    $iSortCol_0 = $this->app->Secure->GetGET('iSortCol_0');
    $sSortDir_0  = $this->app->Secure->GetGET('sSortDir_0');
    $iSortingCols = $this->app->Secure->GetGET('iSortingCols');
    $sSearch = $this->app->Secure->GetGET('sSearch');
    $sEcho = $this->app->Secure->GetGET('sEcho');
    $cmd = $this->app->Secure->GetGET('cmd');
    $frommodule = $this->app->Secure->GetGET('frommodule');
    $fromclass = $this->app->Secure->GetGET('fromclass');
    $sSearch = trim($sSearch);
    $sSearch = str_replace('%','\%',$sSearch);

    $sSearch2 = $sSearch;
    $sSearch3 = $this->app->erp->ConvertForDB($sSearch);
    $sSearch = $this->app->erp->ConvertForDBUTF8($sSearch);
    if($this->app->Secure->GetGet('deferLoading')){
      echo '{"sEcho":'.(int)$sEcho.',"iTotalRecords":0,"iTotalDisplayRecords":0,"aaData":[]}';
      $this->app->ExitXentral();
    }


    $YUIs = $this->app->YUI->TableSearch('',$cmd,'ALL','','',$frommodule, $fromclass);
    $starttime = microtime(true);
    $limiert = false;
    if(method_exists($this->app->erp, 'BegrenzungLivetabelle'))
    {
      $limiert = 2*$this->app->erp->BegrenzungLivetabelle($cmd, $this->app->DB->real_escape_string( $iDisplayLength ));
    }
    $maxrows = 0;
    if(isset($YUIs['maxrows']) && $YUIs['maxrows'] > 0)
    {
      $maxrows = $YUIs['maxrows'];
    }
    $sLimit = '';
    if($limiert)
    {
      $limiert += (int) $iDisplayStart ;
      $iDisplayLength = $iDisplayLength ;
      if(($maxrows > 0) && $iDisplayLength > $maxrows){
        $iDisplayLength = $maxrows;
      }
        $sLimit = 'LIMIT '. $iDisplayStart .', '. ( $limiert );
    }else{
      if ( isset($iDisplayStart) )
      {
        $iDisplayLength = $iDisplayLength ;
        if(($maxrows > 0) && $iDisplayLength > $maxrows)
        {
          $iDisplayLength = $maxrows;
        }
        $sLimit = 'LIMIT '. $iDisplayStart .', '. $iDisplayLength ;
      }
    }
    /* Ordering */

    // check if is allowed
    if(!$this->app->erp->TableSearchAllowed($cmd)) 
    {
      $this->app->erp->Protokoll("Nicht erlaubter Zugriff auf $cmd von Benutzer ".$this->app->User->GetName());
      $this->app->erp->ExitWawi();
    }
  
    

    //$findcolstmp = $this->app->YUI->TableSearch("",$cmd,"findcols","","",$frommodule, $fromclass);
    $findcolstmp = $YUIs['findcols'];
    //$moreinfo = $this->app->YUI->TableSearch("",$cmd,"moreinfo","","",$frommodule, $fromclass);
    $moreinfo = $YUIs['moreinfo'];

    if (isset($iSortCol_0) || ($moreinfo && $iSortCol_0 < 1))
    {
      if($moreinfo){
        if(!($iSortCol_0 < 1)){
          $iSortCol_0++;
        }
      }else{
        $iSortCol_0++;

      }
      if($iSortCol_0 < 1){
        $iSortCol_0 = 1;
      }

      if(trim($findcolstmp[$iSortCol_0 - 1]) == 'belegnr' ||
        strpos($findcolstmp[$iSortCol_0 - 1], '.belegnr') !== false){
        if(preg_match_all('/([a-zA-Z0-9]*)(\.*)belegnr/', $findcolstmp[$iSortCol_0 - 1], $ergebnis)){
          if(isset($ergebnis[1][0]) && !isset($ergebnis[1][1])){
            $findcolstmp[$iSortCol_0 - 1] = $this->app->erp->BelegnummerSortierung($ergebnis[1][0]);
          }
        }
      }

      $sOrder = 'ORDER BY ' . $findcolstmp[$iSortCol_0 - 1] . " $sSortDir_0";
    }
    else
    {
      //standard einstellung nach datum absteigend wenn datumsspalte vorhanden
      //$defaultorder = $this->app->YUI->TableSearch("",$cmd,"defaultorder","","",$frommodule, $fromclass);
      $defaultorder = $YUIs['defaultorder'];
      //$defaultorderdesc = $this->app->YUI->TableSearch("",$cmd,"defaultorderdesc","","",$frommodule, $fromclass);
      $defaultorderdesc = $YUIs['defaultorderdesc'];
      if($defaultorder<=0) {
        $defaultorder = count($findcolstmp);
        $defaultorderdesc = 1;
      }

      if($defaultorderdesc=='1') {
        $defaultorderdesc = ' DESC';
      } else {
        $defaultorderdesc='';
      }


      if($defaultorder >=0 && is_numeric($defaultorder))
      {
        $defaultorder++;
        //$findcolstmp = $this->app->YUI->TableSearch("",$cmd,"findcols","","",$frommodule, $fromclass);
        $findcolstmp = $YUIs['findcols'];
        if($defaultorder < 2)
        {
          $defaultorder = 2;
        }
        $sOrder = 'ORDER BY '.$findcolstmp[$defaultorder-2]." $defaultorderdesc";
      }else
      {
        $sOrder = 'ORDER BY '.$findcolstmp[0]." $defaultorderdesc";
      }
    }


    /* Filtering - NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */

    $sWhere = '';
    //$where = $this->app->YUI->TableSearch("",$cmd,"where","","",$frommodule, $fromclass);
    $where = $YUIs['where'];
    //echo $where;

    $matchesql = !empty($YUIs['matchesql'])?$YUIs['matchesql']:'';

    if ($sSearch != '' && !empty($matchesql) && !empty($matchesql['sqlpre']))
    {
      while(strpos($sSearch,'  ') !== false)
      {
        $sSearch = str_replace('  ',' ', $sSearch);
      }
      while(strpos($sSearch2,'  ') !== false)
      {
        $sSearch2 = str_replace('  ',' ', $sSearch2);
      }
      $sSearch = str_replace('&#37;','%',$sSearch);
      $sSearch2 = str_replace('&#37;','%',$sSearch2);
      $sSearcha = explode(' ', $sSearch);
      $sSearcha2 = explode(' ', $sSearch2);
      $sSearch = str_replace(' ','%',$sSearch);
      $sSearch2 = str_replace(' ','%',$sSearch2);
      $YUIs['sql'] = $matchesql['sqlpre'];
      $unions = [];
      foreach($matchesql['elements'] as $keyEl => $SqlElement)
      {
        $SqlElementQuery = $SqlElement['sql'].' WHERE ';
        $whereArr = [];
        $firstsubwhere = true;
        if(!empty($SqlElement['where']))
        {
          foreach($SqlElement['where'] as $keyWhere => $valWhere)
          {
            if(!$firstsubwhere)
            {
              $SqlElementQuery .= ' OR ';
            }
            if($this->app->DB->real_escape_string( $sSearch ) !== $this->app->erp->ConvertForTableSearch( $sSearch ))
            {
              if(count($sSearcha) > 1)
              {
                $SqlElementQuery .= $this->AjaxTableWhereBuilderArray($valWhere, $sSearcha, $sSearcha2);
              }else{
                $SqlElementQuery .= "({$valWhere} LIKE '%" . $this->app->DB->real_escape_string($sSearch) . "%' OR 
                {$valWhere} LIKE '%" . $this->app->erp->ConvertForTableSearch($sSearch) . "%' ) ";
              }
            }else{
              if(count($sSearcha) > 1)
              {
                $SqlElementQuery .= $this->AjaxTableWhereBuilderArray($valWhere, $sSearcha);
              }else{
                $SqlElementQuery .= "({$valWhere} LIKE '%" . $this->app->DB->real_escape_string($sSearch) . "%') ";
              }
            }
            if($sSearch2!='' && ($sSearch2 !== $sSearch))
            {
              if($this->app->DB->real_escape_string( $sSearch2 ) !== $this->app->erp->ConvertForTableSearch( $sSearch2 ))
              {
                $SqlElementQuery .=" OR ({$valWhere} LIKE '%".$this->app->DB->real_escape_string( $sSearch2 )."%' OR {$valWhere} LIKE '%".$this->app->erp->ConvertForTableSearch( $sSearch2 )."%' ) ";
              }else{
                if(count($sSearcha) > 1)
                {
                  $SqlElementQuery .= ' OR ('. $this->AjaxTableWhereBuilderArray($valWhere, $sSearcha2).') ';
                }else{
                  $SqlElementQuery .= " OR ({$valWhere} LIKE '%" . $this->app->DB->real_escape_string($sSearch2) . "%') ";
                }
              }
            }
            $firstsubwhere = false;
          }
        }
        if(!empty($SqlElement['match']))
        {
          if(!$firstsubwhere)
          {
            $SqlElementQuery .= ' OR ';
          }
          $sSearchArr = explode('%', str_replace(['+','-','*','~'],['%','','%','%'], $sSearch));
          foreach($sSearchArr as $keyS => $keyv)
          {
            if(strlen($keyv) === 0)
            {
              unset($sSearchArr[$keyS]);
            }
          }

          $sSearchMatch = '';
          foreach ($sSearchArr as $sSearchItem) {
            // $sSearchItem enthält einzelne Wörter evtl. mit HTML-Entities
            // Problem 1: Das Ampersand-Zeichen der HTML-Entities wird im BOOLEAN MODE als Worttrenner gesehen.
            // Problem 2: Das Ampersand-Zeichen kann im BOOLEAN MODE nicht escaped werden.
            // Die einzige Lösung ist das Suchwort mit doppelten Anführungszeichen zu umschließen,
            // die Worttrenner-Eigenschaft des Ampersandzeichens wird damit aufgehoben.
            // Der Nachteil bei dieser Lösung: Es werden nur noch ganze Wörter gefunden.
            $sSearchMatch .= sprintf('+%s* ', $this->app->DB->real_escape_string($sSearchItem));
          }
          $SqlElementQuery .= ' MATCH('.implode(',',$SqlElement['match']).') AGAINST (\''.$sSearchMatch.'\' IN BOOLEAN MODE) ';
          if($this->app->DB->real_escape_string( $sSearch ) !== $this->app->erp->ConvertForTableSearch( $sSearch ))
          {
            $SqlElementQuery .= ' OR MATCH('.implode(',',$SqlElement['match']).') AGAINST (\''.$this->app->erp->ConvertForTableSearch($sSearchMatch).'\' IN BOOLEAN MODE) ';
          }
          elseif(!empty($sSearch2) &&  $sSearch2 !== $sSearch) {
            $sSearchArr2 = explode('%', str_replace(['+','-','*','~'],['%','','%','%'], $sSearch2));
            foreach($sSearchArr2 as $keyS => $keyv)
            {
              if(strlen($keyv) === 0) {
                unset($sSearchArr2[$keyS]);
              }
            }
            $sSearchMatch2 = '+'.implode('* +', $sSearchArr2).'*';
            $SqlElementQuery .= ' OR MATCH('.implode(',',$SqlElement['match']).') AGAINST (\''.$this->app->DB->real_escape_string($sSearchMatch2).'\' IN BOOLEAN MODE) ';
          }
        }
        $SqlElementQuery .= implode(' OR ', $whereArr);
        $unions[] = $SqlElementQuery;
      }
      $YUIs['sql'] .= implode(' 
      UNION 
      ', $unions);
      $YUIs['sql'] .= $matchesql['sqlpost'];

      if($where!=''){
        $sWhere = " WHERE $where ";
      }
    }
    elseif ( $sSearch != '' ) {
      /*
         $sWhere = "WHERE a.nummer LIKE '%".$this->app->DB->real_escape_string( $sSearch )."%' OR ".
         "p.abkuerzung LIKE '%".$this->app->DB->real_escape_string( $sSearch )."%' OR ".
         "a.name_de LIKE '%".$this->app->DB->real_escape_string( $sSearch )."%'";
       */
      //$searchsql = $this->app->YUI->TableSearch("",$cmd,"searchsql","","",$frommodule, $fromclass);
      $searchsql = $YUIs['searchsql'];
      $searchsql2 = null;
      $datesearchcol2 = null;
      $datesearchcol = $YUIs['datesearchsols'];
      if(is_array($searchsql))
      {
        foreach($searchsql as $k => $v)
        {
          if(is_array($v))
          {
            foreach($v as $k2 => $v2)
            {
              $searchsql2[] = $v2;
              if($datesearchcol && in_array($k, $datesearchcol)) {
                $datesearchcol2[] = count($searchsql2)-1;
              }
            }
          }else{
            $searchsql2[] = $v;
            if($datesearchcol && in_array($k, $datesearchcol))
            {
              $datesearchcol2[] = count($searchsql2)-1;
            }
          }
        }
      }
      if($where==''){
        $sWhere = ' WHERE (';
      }
      else
      {
        if(count($searchsql) > 0){
          $sWhere = " WHERE $where AND (";
        }
        else{
          $sWhere = " WHERE $where ";
        }
      }

      // Prozent austauschen da dies mysql wildcat ist

      while(strpos($sSearch,'  ') !== false)
      {
        $sSearch = str_replace('  ',' ', $sSearch);
      }
      while(strpos($sSearch2,'  ') !== false)
      {
        $sSearch2 = str_replace('  ',' ', $sSearch2);
      }

      $sSearch = str_replace('&#37;','%',$sSearch);
      $sSearch2 = str_replace('&#37;','%',$sSearch2);

      //$sSearch3 = str_replace('&#37;','%',$sSearch3);
      //$sSearch3 = str_replace(' ','%',$sSearch3);

      $sSearcha = explode(' ', $sSearch);
      $sSearcha2 = explode(' ', $sSearch2);
      //$sSearcha = [];
      //$sSearcha2 = [];

      $sSearch = str_replace(' ','%',$sSearch);
      $sSearch2 = str_replace(' ','%',$sSearch2);


      $csearchsql2 = $searchsql2?count($searchsql2):0;
      for($i=0;$i<$csearchsql2;$i++)
      {
        if($this->app->DB->real_escape_string( $sSearch ) !== $this->app->erp->ConvertForTableSearch( $sSearch ))
        {
          if($datesearchcol2 && in_array($i, $datesearchcol2))
          {
            $sWhere .= '('.$this->AjaxTableWhereBuilder($searchsql2[$i], $sSearch, true, false).' OR  '.$this->AjaxTableWhereBuilder($searchsql2[$i], $this->app->erp->ConvertForTableSearch( $sSearch ), true, false).")";
          }else{
            if(count($sSearcha) > 1)
            {
              $sWhere .= $this->AjaxTableWhereBuilderArray($searchsql2[$i], $sSearcha, $sSearcha2);
            }else{
              $sWhere .= "({$searchsql2[$i]} LIKE '%" . $this->app->DB->real_escape_string($sSearch) . "%' OR 
            {$searchsql2[$i]} LIKE '%" . $this->app->erp->ConvertForTableSearch($sSearch) . "%' ) ";
            }
          }
        }else{
          if($datesearchcol2 && in_array($i, $datesearchcol2)) {
            $sWhere .= $this->AjaxTableWhereBuilder($searchsql2[$i], $sSearch, true, false);
          }else{
            if(count($sSearcha) > 1)
            {
              $sWhere .= $this->AjaxTableWhereBuilderArray($searchsql2[$i], $sSearcha);
            }else{
              $sWhere .= "({$searchsql2[$i]} LIKE '%" . $this->app->DB->real_escape_string($sSearch) . "%') ";
            }
          }
        }

        
        if($sSearch2!='' && ($sSearch2 !== $sSearch))
        {
          if($this->app->DB->real_escape_string( $sSearch2 ) !== $this->app->erp->ConvertForTableSearch( $sSearch2 ))
          {
            $sWhere .=" OR ({$searchsql2[$i]} LIKE '%".$this->app->DB->real_escape_string( $sSearch2 )."%' OR {$searchsql2[$i]} LIKE '%".$this->app->erp->ConvertForTableSearch( $sSearch2 )."%' ) ";
          }else{
            if(count($sSearcha) > 1)
            {
              $sWhere .= ' OR ('. $this->AjaxTableWhereBuilderArray($searchsql2[$i], $sSearcha2).') ';
            }else{
              $sWhere .= " OR ({$searchsql2[$i]} LIKE '%" . $this->app->DB->real_escape_string($sSearch2) . "%') ";
            }
          }
        }

        if($sSearch3!='' && ($sSearch3 !== $sSearch || $sSearch3 !== $sSearch2))
        {
          if($this->app->DB->real_escape_string( $sSearch3 ) !== $this->app->erp->ConvertForTableSearch( $sSearch3 ))
          {
            $sWhere .= "OR ({$searchsql2[$i]} LIKE '%".$this->app->DB->real_escape_string( $sSearch3 )."%' OR {$searchsql2[$i]} LIKE '%".$this->app->erp->ConvertForTableSearch( $sSearch3 )."%' ) OR ";
          }else{
            $sWhere .= "OR ({$searchsql2[$i]} LIKE '%".$this->app->DB->real_escape_string( $sSearch3 )."%') OR ";
          }
        }
        else {
          $sWhere .= ' OR ';
        }
      }

      //$searchfulltext = $this->app->YUI->TableSearch("",$cmd,"searchfulltext","","",$frommodule, $fromclass);
      $searchfulltext = $YUIs['searchfulltext'];
      if($searchfulltext!='' && $sSearch!='')
      {
        $sSearch = str_replace('&quot;','"',$sSearch);
        $sSearch .= '*';
        $searchfulltext = ' MATCH(e.subject,e.sender,e.action,e.action_html) AGAINST (\''.$this->app->erp->ConvertForTableSearch($sSearch).'\' IN BOOLEAN MODE ) ';
      }
      if(count($searchsql2) > 0){
        if($searchfulltext == '')
        {
          $sWhere .= ' 0)';
        }else{
          $sWhere .= ' '.$searchfulltext.')';
          /*$i--;
          if($searchfulltext != ''){
            $searchfulltext = ' OR ' . $searchfulltext;
          }
          if($this->app->DB->real_escape_string($sSearch) !== $this->app->erp->ConvertForTableSearch($sSearch)){
            $sWhere .= "( {$searchsql2[$i]} LIKE '%" . $this->app->DB->real_escape_string($sSearch) . "%' OR {$searchsql2[$i]} LIKE '%" . $this->app->erp->ConvertForTableSearch($sSearch) . "%')     $searchfulltext           )";
          }else{
            $sWhere .= "( {$searchsql2[$i]} LIKE '%" . $this->app->DB->real_escape_string($sSearch) . "%')     $searchfulltext           )";
          }*/
        }
      } else {
        $sWhere .= " AND $searchfulltext";
      }

    } else {
      if($where!=''){
        $sWhere = " WHERE $where ";
      }
    } 


    //$searchsql = $this->app->YUI->TableSearch("",$cmd,"searchsql","","",$frommodule, $fromclass);
    $searchsql = $YUIs['searchsql'];

    //$moreinfo = $this->app->YUI->TableSearch("",$cmd,"moreinfo","","",$frommodule, $fromclass);
    $moreinfo = $YUIs['moreinfo'];
    if($moreinfo) {
      $offset = 1;
    } else {
      $offset=0;
    }
    if(!$YUIs['columnfilter'])
    {
      $searchsql = $YUIs['findcols'];
      $offset = 0;
    }
    $csearchsql = $searchsql?count($searchsql):0;
    for($isearch=0;$isearch<$csearchsql;$isearch++)
    {
      $sSearch = $this->app->Secure->GetGET('sSearch_'.$isearch);
      if($sSearch!='' && $sSearch!='A')
      {
        if(isset($searchsql[$isearch-$offset]) && is_array($searchsql[$isearch-$offset]))
        {
          $gef = false;
          foreach($searchsql[$isearch-$offset] as $v)
          {
            if($v != '')
            {
              if($sWhere == '')
              {
                //$sWhere = "WHERE (".$v." LIKE '%".$this->app->DB->real_escape_string( $sSearch )."%'";
                $sWhere = 'WHERE ('.$this->AjaxTableWhereBuilder($v, $sSearch,isset($YUIs['datecols']) && is_array($YUIs['datecols'])  && in_array($isearch-$offset,$YUIs['datecols'])?true:false,isset($YUIs['numbercols']) && is_array($YUIs['numbercols'])  && in_array($isearch-$offset,$YUIs['numbercols'])?true:false );
                $gef = true;
              }else{
                $sWhere = "$sWhere ";
                if(!$gef)
                {
                  $sWhere .= ' AND (';
                }else{
                  $sWhere .= ' OR ';
                }
                //$sWhere .= " (".$v." LIKE '%".$this->app->DB->real_escape_string( $sSearch )."%')";
                $sWhere .= $this->AjaxTableWhereBuilder($v, $sSearch,isset($YUIs['datecols']) && is_array($YUIs['datecols'])  && in_array($isearch-$offset,$YUIs['datecols'])?true:false,isset($YUIs['numbercols']) && is_array($YUIs['numbercols'])  && in_array($isearch-$offset,$YUIs['numbercols'])?true:false );
                $gef = true;
              }
            }
          }
          if($gef){
            $sWhere .= ' ) ';
          }
        }else{
          if($sWhere=='')
          {
            if($searchsql[$isearch-$offset]!='')
            {
              //$sWhere = "WHERE ".$searchsql[$isearch-$offset]." LIKE '%".$this->app->DB->real_escape_string( $sSearch )."%'";
              $sWhere = 'WHERE '.$this->AjaxTableWhereBuilder($searchsql[$isearch-$offset], $sSearch ,isset($YUIs['datecols']) && is_array($YUIs['datecols'])  && in_array($isearch-$offset,$YUIs['datecols'])?true:false,isset($YUIs['numbercols']) && is_array($YUIs['numbercols'])  && in_array($isearch-$offset,$YUIs['numbercols'])?true:false );
            }
          }
          else
          {
            if($searchsql[$isearch-$offset]!='')
            {
              //$sWhere = "$sWhere AND (".$searchsql[$isearch-$offset]." LIKE '%".$this->app->DB->real_escape_string( $sSearch )."%')";
              $sWhere = "$sWhere AND (".$this->AjaxTableWhereBuilder($searchsql[$isearch-$offset], $sSearch,isset($YUIs['datecols']) && is_array($YUIs['datecols'])  && in_array($isearch-$offset,$YUIs['datecols'])?true:false,isset($YUIs['numbercols']) && is_array($YUIs['numbercols'])  && in_array($isearch-$offset,$YUIs['numbercols'])?true:false ).")";
            }
          }
        }
      }
    }



    //$tmp = $this->app->YUI->TableSearch("",$cmd,"sql","","",$frommodule, $fromclass);
    $tmp = $YUIs['sql'];
    //$groupby = $this->app->YUI->TableSearch("",$cmd,"groupby","","",$frommodule, $fromclass);
    $groupby = $YUIs['groupby'];
    //$orderby = $this->app->YUI->TableSearch("",$cmd,"orderby","","",$frommodule, $fromclass);
    $orderby = $YUIs['orderby'];
    
    $fastcount = isset($YUIs['fastcount'])?$YUIs['fastcount']:'';
    if($orderby){
      $sOrder = $orderby;
    }
    $uid = $this->app->Secure->GetGET('uid');
    $pid = $this->app->User->GetParameter('tablesearch_'.$uid);
    if(!empty($pid)) {
      $pid = explode('|',$pid);
      $sEchoCheck = !empty($pid[1])?(int)$pid[1]:0;
      $pid = reset($pid);
      if($sEchoCheck < $sEcho && $pid != $this->app->DB->connection->thread_id){
        $this->app->DB->kill($pid);
      }
      $this->app->User->deleteParameter('tablesearch_'.$uid);
    }
    //$sQuery = $sWhere." ".$sOrder." ". $sLimit;

    //$rResult = $this->app->DB->Query( $sQuery);
    $sQuery = "
      $tmp
      $sWhere 
      $groupby
      $sOrder
      $sLimit
      ";
    if($fastcount || $limiert){
      $sQuery = str_replace('SQL_CALC_FOUND_ROWS','',$sQuery);
    }
    
    $jsarray = null;
    if(isset($this->app->stringcleaner)) {
      $jsarray = $this->app->stringcleaner->CheckSQLHtml($sQuery);
    }
    if($this->app->erp->Firmendaten('schnellsuchecount') && strpos($sQuery, 'SQL_CALC_FOUND_ROWS')){
      $YUIs['count'] = '';
    }

    if(isset($YUIs['onequeryperuser']) && $YUIs['onequeryperuser']) {
      $killId = $this->app->User->GetParameter('tablesearch_'.$cmd.'_id2');
      $killId = explode('|',$killId);
      $sEchoCheck = !empty($killId[1])?(int)$killId[1]:0;
      $killId = reset($killId);
      if(!empty($killId) && $sEchoCheck < $sEcho) {
        $this->app->DB->kill($killId);
        $this->app->User->deleteParameter('tablesearch_'.$cmd.'_id2');
      }
      $killId = $this->app->User->GetParameter('tablesearch_'.$cmd.'_id');
      $killId = explode('|',$killId);
      $sEchoCheck = !empty($killId[1])?(int)$killId[1]:0;
      $killId = reset($killId);
      if(!empty($killId) && $sEchoCheck < $sEcho) {
        $this->app->DB->kill($killId);
        $this->app->User->deleteParameter('tablesearch_'.$cmd.'_id');
      }
      //TODO Alte Prozesse killen
      $_sql = substr($this->app->YUI->CodiereSQLForOneQuery($tmp, $cmd),0, 100);
      $processlist = $this->app->DB->SelectArr('SHOW PROCESSLIST');
      if(count($processlist) > 1) {
        foreach($processlist as $v) {
          if($v['Time'] > 1 && $v['db'] == $this->app->Conf->WFdbname && $v['User'] == $this->app->Conf->WFdbuser && substr($v['Info'], 0, 100) == $_sql) {
            $this->app->DB->kill($v['Id']);
          }
        }
      }
      $this->app->User->SetParameter('tablesearch_'.$cmd.'_id', $this->app->DB->connection->thread_id);
    }
    $maxExecutionTime = 300;
    if($cmd === 'report_table') {
      $maxExecutionTime = 30;
    }
    $useasync = function_exists('mysqli_poll');
    if($useasync) {
      ignore_user_abort(true);
      $db2 = $this->app->DB->getClone();
      $threadid = $db2->connection->thread_id;
      $this->app->User->SetParameter('tablesearch_'.$uid, $threadid.'|'.$sEcho);
      if(!empty($YUIs['onequeryperuser'])) {
        $this->app->User->SetParameter('tablesearch_'.$cmd.'_id2', $db2->connection->thread_id.'|'.$sEcho);
      }

      $startExecutionTime = microtime(true);
      $rResult = $db2->Query($sQuery, true);
      $all_links = array($db2->connection);
      $processed = 0;
      do {
        echo ' ';
        flush();
        ob_flush();
        if(connection_aborted() == 1
          || ($maxExecutionTime > 0 && microtime(true) - $startExecutionTime > $maxExecutionTime)
        ) {
          $this->app->DB->kill($threadid);
          if((int)$this->app->User->GetParameter('tablesearch_'.$uid) == $threadid) {
            $this->app->User->deleteParameter('tablesearch_'.$uid);
          }
          exit;
        }
        $links = $errors = $reject = array();
        foreach ($all_links as $link) {
          $links[] = $errors[] = $reject[] = $link;
        }
        if (!mysqli_poll($links, $errors, $reject, 0,50000)) {
          continue;
        }
        foreach ($links as $link) {
          if ($rResult = $link->reap_async_query()) {
            break 2;
          };
          $processed++;
        }
      } while ($processed < count($all_links));
    }
    else{
      $rResult = $this->app->DB->Query($sQuery);
    }
    if($cmd === 'adresse_brief' && $this->app->DB->error() && strpos($this->app->DB->error(), 'COLLATION \'utf8_general_ci\' is not valid for CHARACTER') !== false)
    {
      if((String)$this->app->erp->GetKonfiguration('adresse_crm_collateerror') === ''){
        $this->app->erp->SetKonfigurationValue('adresse_crm_collateerror', 1);
      }
    }elseif($cmd === 'adresse_brief' && $this->app->DB->error() && $this->app->erp->GetKonfiguration('adresse_crm_collateerror'))
    {
      $this->app->erp->SetKonfigurationValue('adresse_crm_collateerror', 0);
    }

    $iTotal = 0;
    if(!$limiert) {
      if($fastcount) {
        $sQuery = "$fastcount $sWhere";
        if($useasync) {
          if(!empty($YUIs['onequeryperuser'])) {
            $this->app->User->SetParameter('tablesearch_'.$cmd.'_id2', $db2->connection->thread_id.'|'.$sEcho);
          }
          $startExecutionTime = microtime(true);
          $rResultFilterTotal = $db2->Query($sQuery, true);
          $threadid = $db2->connection->thread_id;
          $all_links = array($db2->connection);
          $processed = 0;
          do {
            echo ' ';
            flush();
            ob_flush();
            if(connection_aborted() == 1 || (
              $maxExecutionTime > 0 && microtime(true) - $startExecutionTime > $maxExecutionTime)
            ){
              $this->app->DB->kill($threadid);
              exit;
            }
            $links = $errors = $reject = array();
            foreach ($all_links as $link) {
              $links[] = $errors[] = $reject[] = $link;
            }
            if(!mysqli_poll($links, $errors, $reject, 0, 50000)){
              continue;
            }
            foreach ($links as $link) {
              if($rResultFilterTotal = $link->reap_async_query()){
                break 2;
              };
              $processed++;
            }
          } while ($processed < count($all_links));
          if(!empty($YUIs['onequeryperuser'])) {
            $this->app->User->deleteParameter('tablesearch_'.$cmd.'_id2');
          }
        }
        else {
          $rResultFilterTotal = $this->app->DB->Query($sQuery);
        }
        $aResultFilterTotal = $this->app->DB->Fetch_Row($rResultFilterTotal);
        $this->app->DB->free($rResultFilterTotal);
        $iFilteredTotal = $aResultFilterTotal[0];
      }
      else {
        $sQuery = '
          SELECT FOUND_ROWS()
          ';
        if($useasync) {
          if(!empty($YUIs['onequeryperuser'])) {
            $this->app->User->SetParameter('tablesearch_'.$cmd.'_id2', $db2->connection->thread_id.'|'.$sEcho);
          }
          $startExecutionTime = microtime(true);
          $rResultFilterTotal = $db2->Query($sQuery, true);
          $threadid = $db2->connection->thread_id;
          $all_links = array($db2->connection);
          $processed = 0;
          do {
            echo ' ';
            flush();
            ob_flush();
            if(connection_aborted() == 1 || (
                $maxExecutionTime > 0 && microtime(true) - $startExecutionTime > $maxExecutionTime)
            ) {
              $this->app->DB->kill($threadid);
              exit;
            }
            $links = $errors = $reject = array();
            foreach ($all_links as $link) {
              $links[] = $errors[] = $reject[] = $link;
            }
            if(!mysqli_poll($links, $errors, $reject, 0, 50000)){
              continue;
            }
            foreach ($links as $link) {
              if($rResultFilterTotal = $link->reap_async_query()){
                break 2;
              };
              $processed++;
            }
          } while ($processed < count($all_links));
          if(!empty($YUIs['onequeryperuser'])) {
            $this->app->User->deleteParameter('tablesearch_'.$cmd.'_id2');
          }
        }
        else {
          $rResultFilterTotal = $this->app->DB->Query($sQuery);
        }
        $aResultFilterTotal = $this->app->DB->Fetch_Row($rResultFilterTotal);
        $this->app->DB->free($rResultFilterTotal);
        $iFilteredTotal = $aResultFilterTotal[0];
      }
    }
    if($useasync) {
      echo ' ';
      flush();
      ob_flush();
      if(connection_aborted() == 1) {
        if(!empty($db2)) {
          $db2->Close();
        }
        if(!empty($YUIs['onequeryperuser'])) {
          $this->app->User->deleteParameter('tablesearch_'.$cmd.'_id');
        }
        $this->app->DB->Close();
        exit;
      }
    }
    /*    
          $sQuery = "
          SELECT COUNT(id)
          FROM   artikel
          ";
     */
    //$sQuery = $this->app->YUI->TableSearch("",$cmd,"count","","",$frommodule, $fromclass);
    if(!$limiert){
      $sQuery = $YUIs['count'];
      if((String)$sQuery !== '') {
        $rResultTotal = $this->app->DB->Query( $sQuery);
        
        $aResultTotal = $this->app->DB->Fetch_Array($rResultTotal);
        $this->app->DB->free($rResultTotal);
        $iTotal = (int)$aResultTotal[0];
      }
      else {
        $iTotal = !empty($iFilteredTotal)?(int)$iFilteredTotal:0;
      }
      $this->app->erp->CheckBegrenzungLiveTabelle($cmd, $iTotal, microtime(true)-$starttime);
    }
    elseif(!empty($YUIs['cached_count'])) {
      $aResultTotal = $this->app->DB->SelectArrCache($YUIs['cached_count'], 180, 'tablesearch_count');
      if(!empty($aResultTotal)) {
        $iTotal = reset($aResultTotal);
        $iTotal = reset($iTotal);
      }
    }
    if(method_exists($this->app->erp,'CheckSchnellsuche')) {
      $this->app->erp->CheckSchnellsuche($cmd, $iTotal, microtime(true) - $starttime);
    }
    //$heading = count($this->app->YUI->TableSearch("",$cmd,"heading","","",$frommodule, $fromclass));
    $heading = count($YUIs['heading']);
    //$menu = $this->app->YUI->TableSearch("",$cmd,"menu","","",$frommodule, $fromclass);
    $menu = $this->app->Tpl->ParseTranslation($YUIs['menu']);
    $sOutput2 = '';
    $rowc = 0;
    while ( $aRow = $this->app->DB->Fetch_Row( $rResult )) {
      $rowc++;
      if(!$limiert || ($rowc <= $iDisplayLength)) {
        $sOutput2 .= '[';
        for($i=1;$i<$heading;$i++) {
          /*if(strpos($aRow[$i],'<') !== false) //30.07.2018 Bruno Entfernt wegen fehlerhaften Entfernen von Tags
          {
            if($jsarray && isset($jsarray[$i]) && !$jsarray[$i])
            {
              $aRow[$i] = strip_tags($aRow[$i]);
            }elseif(isset($jsarray[$i]) && 1 == $jsarray[$i])
            {
              $aRow[$i] = $this->app->stringcleaner->xss_clean($aRow[$i], false);
            }
          }*/
          $aRow[$i] = $this->EntferneSteuerzeichen(trim(str_replace("'",'&apos;',$aRow[$i])));
          $aRow[$i] = str_replace("\r",'',$aRow[$i]);
          $aRow[$i] = str_replace("\n",'',$aRow[$i]);
          $sOutput2 .= '"'.addslashes($aRow[$i]).'",';
        }

        $sOutput2 .= '"'.addslashes(str_replace('%value%',$aRow[$i],$menu)).'"';

        $sOutput2 .= '],';
      }
    }
    if($limiert) {
      $sOutput = '{';
      $sOutput .= '"sEcho": '.(int)$sEcho.', ';
      $sOutput .= '"iTotalRecords": '.(!empty($iTotal)?$iTotal:$rowc+(int)$iDisplayStart).', ';
      $sOutput .= '"iTotalDisplayRecords": '.($rowc+(int)$iDisplayStart).', ';
      $sOutput .= '"aaData": [ ';
    }
    else{
      $sOutput = '{';
      $sOutput .= '"sEcho": '.(int)$sEcho.', ';
      $sOutput .= '"iTotalRecords": '.$iTotal.', ';
      $sOutput .= '"iTotalDisplayRecords": '.$iFilteredTotal.', ';
      $sOutput .= '"aaData": [ ';
    }
    $sOutput .= $sOutput2;
    
    $sOutput = substr_replace( $sOutput, "", -1 );
    $sOutput .= '] }';

    $sOutput = str_replace("\t",'',$sOutput);

    // Eventuell deutsches Datumsformat in allen Tabellen und sortieren geht auch
    //$repl =    preg_replace('~\"([1-2]{1}\d{3})-(\d{2})-(\d{2})\"~', '"<!--$1-%s-%3--> $3.$2.$1"', $sOutput);
    //$repl =    preg_replace('~\"([1-2]{1}\d{3})-(\d{2})-(\d{2})\s+~', '"<!--$1-%s-%3--> $3.$2.$1 ', $repl);
    //$repl =    preg_replace('~\s+([1-2]{1}\d{3})-(\d{2})-(\d{2})\s+~', ' <!--$1-%s-%3--> $3.$2.$1 ', $repl);
    //$repl = preg_replace('~\"(\d{4})-(\d{2})-(\d{2})\"~', '"<!--$1-%s-%3-->$3.$2.$1"', $sOutput);	
    $repl = $sOutput;
    $repl = $this->app->erp->ClearDataBeforeOutput($repl);
    $repl = json_encode(json_decode($repl));
    echo $repl;
    if(!empty($YUIs['onequeryperuser'])) {
      $this->app->User->deleteParameter('tablesearch_'.$cmd.'_id');
    }
    $this->app->erp->ExitWawi();
  }

  /**
   * @return void
   */
  public function AjaxLiveTable(): void
  {
      /** @var Request request */
      $request = $this->app->Container->get('Request');
      $tableName = $request->get->get('srctable', '');
      $module = $request->get->get('srcmodule', '');
      $className = $request->get->get('srcclass', '');
      $className = StringUtil::toTitleCase($className, '-');

      $this->app->BuildNavigation=false;

      if (empty($tableName)) {
          $this->app->Tpl->Set(
              'MESSAGE',
              '<div class="error">Fehler: Tabelle ist nicht spezifiziert.</div>'
          );
          $this->app->Tpl->Parse('PAGE', 'livetable_async.tpl');

          return;
      }

      if ((empty($module) xor empty($className))) {
          $this->app->Tpl->Set(
              'MESSAGE',
              '<div class="error">Fehler: Tabelle kann nicht gefunden werden.</div>'
          );
          $this->app->Tpl->Parse('PAGE', 'livetable_async.tpl');

          return;
      }

      if (!empty($module) && !str_ends_with(strtolower($module), '.php')) {
          $module .= '.php';
      }

      $this->app->YUI->TableSearch('LIVETABLE', $tableName, 'show', '', '', $module, $className);
      $this->app->Tpl->Parse('PAGE', 'livetable_async.tpl');
  }

  protected function EntferneSteuerzeichen($string)
  {
    $len = strlen($string);
    $out = '';
    for($i = 0; $i < $len; $i++)  {
      $ord = ord($string[$i]);
      if($ord != 127 && ($ord > 31 || $ord == 13 || $ord == 10 || $ord == 9)) {
        $out .= $string[$i];
      }
    }
    return $out;
  }
    
  protected function fnColumnToFieldPosition( $i )
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    $findcols = $this->app->YUI->TablePositionSearch('',$cmd,'findcols');

    return !empty($findcols[$i])?$findcols[$i]:0;
  }

  protected function fnColumnToField( $i )
  {
    $cmd = $this->app->Secure->GetGET('cmd');
    $frommodule = $this->app->Secure->GetGET('frommodule');
    $fromclass = $this->app->Secure->GetGET('fromclass');
    $findcols = $this->app->YUI->TableSearch('',$cmd,'findcols','','',$frommodule, $fromclass);

    return $findcols[$i];
  }

}


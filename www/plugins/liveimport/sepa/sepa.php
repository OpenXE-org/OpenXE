<?php

class sepa extends LiveimportBase
{
  /**
   * @return  array
   */
  public function showReturnOrderStructure()
  {
    return [
      'legend1' => [
        'typ' => 'legend',
        'bezeichnung' => 'Angaben zum Zahlungsempf&auml;nger',
      ],
      'empfaenger' => [
        'bezeichnung' => 'Name:',
        'size' => 50
      ],
      'iban' => [
        'bezeichnung' => 'IBAN:',
        'size' => 50
      ],
      'bic' => [
        'bezeichnung' => 'BIC:',
        'size' => 50
      ],
      'legend2' => [
        'typ' => 'legend',
        'bezeichnung' => 'Betrag',
      ],
      'betrag' => [
        'bezeichnung' => 'Euro, Cent:',
        'typ' => 'price',
        'size' => 50,
        'replace' => 'ReplaceBetrag',
      ],
      'waehrung' => [
        'bezeichnung' => 'W&auml;hrung:',
        'autocomplete' => 'waehrung',
        'size' => 50,
      ],
      'legend3' => [
        'typ' => 'legend',
        'bezeichnung' => 'Verwendungszweck',
      ],
      'vz1' => [
        'bezeichnung' => 'Zeile 1:',
        'size' => 50
      ],
      'vz2' => [
        'bezeichnung' => 'Zeile 2:',
        'size' => 50
      ],
      'legend4' => [
        'typ' => 'legend',
        'bezeichnung' => 'Datum'
      ],
      'datum' => [
        'bezeichnung' => 'Datum:',
        'typ' => 'date',
        'replace' => 'ReplaceDatum'
      ],
    ];
  }

  /**
   * @param int[]       $paymentTransactionIds
   *
   * @return int
   */
  public function createReturnOrdersPaymentEntries($paymentTransactionIds)
  {
    return parent::createReturnOrdersPaymentEntries($paymentTransactionIds);
  }

  /**
   * @param int         $paymentTransactionId
   * @param int         $paymentAccountId
   * @param Application $app
   *
   * @return bool
   */
  public function createReturnOrderPaymentEntry($paymentTransactionId)
  {
    $orders = $this->isPaymentTransactionOk($paymentTransactionId);
    if($orders === false) {
      return false;
    }

    $return = parent::createReturnOrderPaymentEntry($paymentTransactionId);
    if($return) {
      $json = @json_decode(
        $this->app->DB->Select(
          sprintf(
            'SELECT `payment_json` FROM `payment_transaction` WHERE id = %d', $paymentTransactionId
          )
        ),
        true
      );
      if(!empty($json['vz1'])) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE `payment_transaction` SET `payment_reason` = '%s' WHERE id = %d",
            $this->app->DB->real_escape_string($json['vz1']), $paymentTransactionId
          )
        );
      }
      if(!empty($json['betrag'])) {
        $this->app->DB->Update(
          sprintf(
            'UPDATE `payment_transaction` SET `amount` = %f WHERE id = %d',
            $json['betrag'], $paymentTransactionId
          )
        );
      }
    }

    return $return;
  }

  /**
   * @param int $paymentTransactionId
   */
  public function afterSavePaymentTransaction($paymentTransactionId)
  {
    $paymentTransaction = $this->app->DB->SelectRow(
      sprintf(
        'SELECT `amount`, `payment_json`,`payment_reason` FROM `payment_transaction` WHERE `id` = %d',
        $paymentTransactionId
      )
    );
    if(empty($paymentTransaction)) {
      return;
    }
    $json = @json_decode($paymentTransaction['payment_json'], true);
    if(empty($json['betrag'])) {
      return;
    }
    $json['betrag'] = $this->app->erp->ReplaceBetrag(1, $json['betrag']);
    if($json['betrag'] != $paymentTransaction['amount']) {
      $this->app->DB->Update(
        sprintf(
          "UPDATE `payment_transaction` SET `amount` = %f WHERE `id` = %d",
          $json['betrag'], $paymentTransactionId
        )
      );
    }
    if($json['vz1'] != $paymentTransaction['payment_reason']) {
      $this->app->DB->Update(
        sprintf(
          "UPDATE `payment_transaction` SET `payment_reason` = '%s' WHERE `id` = %d",
          $this->app->DB->real_escape_string($json['vz1']), $paymentTransactionId
        )
      );
    }
  }

  /**
   * @param int $paymentGroupId
   *
   * @return string
   */
  public function showPaymentGroupMiniDetail($paymentGroupId)
  {
    $payments = $this->app->DB->SelectArr(
      sprintf(
        "SELECT `payment_json`, `amount` FROM `payment_transaction` WHERE `payment_transaction_group_id` = %d",
        $paymentGroupId
      )
    );
    if(empty($payments)) {
      return '';
    }

    $table = new EasyTable($this->app);
    $table->Query("SELECT '' AS `Name / VZ1`,'' AS `BIC / IBAN`, '' AS `Betrag` ");
    $table->datasets = [];
    foreach($payments as $payment) {
      $json = json_decode($payment['payment_json'], true);
      $table->datasets[] = [
        'Name / VZ1' => 'Name: '.$json['empfaenger'].'<br />VZ1: '.$json['vz1'],
        'BIC / IBAN' => 'BIC: '.$json['bic'].'<br />IBAN: '.$json['iban'],
        'Betrag' =>  number_format(!empty($json['betrag'])?$json['betrag']:$payment['amount'],2,',','.'),
      ];
    }
    $table->align[2] = 'right';
    return $table->DisplayNew('return','Betrag','noAction');
  }

  /**
   * @param array $order
   *
   * @return array
   */
  public function getJsonFormPaymentEntry($order)
  {
    $ret = [];
    if(!empty($order['json']) && is_array($order['json'])) {
      $ret = $order['json'];
    }
    $skontoversatz = $this->app->erp->Firmendaten('skonto_ueberweisung_ueberziehen');
    if($skontoversatz <= 0) {
      $skontoversatz=0;
    }
    if(!isset($order['betrag']) && isset($order['soll'])) {
      $order['betrag'] = $order['soll'];
    }
    if(!empty($order['liability_id'])) {
      $address = $this->app->DB->SelectRow(
        sprintf(
          'SELECT adr.name,adr.swift,adr.iban,adr.mandatsreferenzwdhart,adr.mandatsreferenzwdhart,
            adr.mandatsreferenzaenderung,adr.kundennummer 
          FROM `adresse` AS `adr` 
          WHERE adr.id = %d',
          $order['adresse']
        )
      );
      $name = $address['name'];
      $betrag = $order['betrag'];
      if($order['skonto'] != 0 && $order['skontobis'] !== '0000-00-00' && (string)$order['skontobis'] !== '') {
        $skontobis = new DateTime($order['skontobis']);
        if($skontoversatz > 0){
          $skontobis->add(new DateInterval(sprintf('P%dD', $skontoversatz)));
        }
        if(strtotime($skontobis->format('Y-m-d')) >= strtotime(date('Y-m-d'))
          && ($order['status'] === 'offen' || (string)$order['status'] === '')) {
          $betrag = round($order['betrag'] * (1 - ($order['skonto'] / 100)), 2);
        }
      }

      $vz1 = $this->app->DB->real_escape_string('NR '.($order['belegnr']!=''?$order['belegnr']:$order['liability_id'])
        .' RE '.$order['rechnung'].' '.$order['verwendungszweck']);

      $waehrung = $order['waehrung'];
    }

    if($order['skonto'] > 0) {
      //$datum = $arr_verbindlichkeit[$i]['skontobis']; // wollten sie nicht 831738
      $datum = $order['zahlbarbis'];

      $sparen = $order['betrag'] - round($order['betrag']*(1-($order['skonto']/100)),2);
      $gesamt = $order['betrag'];
      if(empty($order['skontobis2']) && !empty($order['skonto'])) {
        $order['skontobis2'] = $this->app->String->Convert($order['skontobis'],'%1-%2-%3','%3.%2.%1');
      }

      $vz1 = $vz1.'<!--ENDE--><br><font color=red>Skonto '.$order['skonto']
        .'% ('.$sparen.' '.$order['waehrung']." von $gesamt ".$order['waehrung'].') bis: '.$order['skontobis2'].'</font>';
    }
    else {
      $datum = $order['zahlbarbis'];
    }

    if($datum=='0000-00-00') {
      $datum=date('Y-m-d');
    }
    if($vz1 !== null){
      $ret['vz1'] = $vz1;
    }
    if($waehrung !== null){
      $ret['waehrung'] = $waehrung;
    }
    if($address !== null){
      $ret['iban'] = $address['iban'];
      $ret['bic'] = $address['swift'];
    }
    if($betrag !== null){
      $ret['betrag'] = str_replace(',', '', $betrag);
    }
    if($datum !== null){
      $ret['datumueberweisung'] = $datum;
    }
    if($name !== null){
      $ret['empfaenger'] = $name;
    }

    return $ret;
  }

  /**
   * @param array $returnOrders
   * @param array $liabilities
   * @param array $transactions
   *
   * @return array
   */
  public function checkPayments($returnOrders, $liabilities, $transactions)
  {
    $ids = array_merge($returnOrders, $liabilities);
    $idsOk = $this->splitTransactionAccountIds($ids);
    $countOK = 0;
    foreach($idsOk as $idOk) {
      $countOK += count($idOk);
    }
    $ret = ['idsstring' => implode(';', $transactions), 'status' => 1, 'accountid' => $this->id ];
    if($countOK === 0) {
      $ret['status'] = 0;
      $ret['error'] = 'Die Zahlungen können nicht erstellt werden';
      return $ret;
    }
    if(count($ids) === $countOK) {
      return $ret;
    }
    $ret['error'] = 'Es können nicht alle Zahlungen erstellt werden';

    return $ret;
  }

  /**
   * @param array $returnOrders
   * @param array $liabilities
   * @param array $transactions
   *
   * @return array
   */
  public function createPayments($returnOrders, $liabilities, $transactions)
  {
    $checkPayment = $this->checkPayments($returnOrders, $liabilities, $transactions);
    if(empty($checkPayment['status'])) {
      return $checkPayment;
    }

    $ids = array_merge($returnOrders, $liabilities);

    $paymentGroupId = $this->createPaymentGroup($this->id);

    $transactionsArr = $this->getJsonTransactionArrByIds($ids);

    $this->addPaymentsToGroup($paymentGroupId, $ids);

    $konto = $this->id;

    $filePrefix = $this->app->erp->GetTMP().$this->app->User->GetID();
    //$file = $filePrefix.'sepa_lastschrift.zip';
    $file = $filePrefix.'sepa.zip';
    $xmlFile = $filePrefix.'sepa.xml';

    $kontoRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM `konten` WHERE `id` = %d LIMIT 1',
        $konto
      )
    );
    $bic = strtoupper(str_replace(' ','', trim($kontoRow['swift'])));
    $iban = strtoupper(str_replace(' ','',trim($kontoRow['iban'])));
    $inhaber = trim($kontoRow['inhaber']);
    $glaeubiger = trim($kontoRow['glaeubiger']);

    if($glaeubiger==='') {
      $glaeubiger = $this->app->erp->Firmendaten('sepaglaeubigerid');
    }
    $inhaber = preg_replace('/[^A-Za-z0-9\-äöüßÖÜÄ+ .]/u','',$inhaber);
    $glaeubiger = preg_replace('/[^A-Za-z0-9\-äöüßÖÜÄ+ .]/u','',$glaeubiger);

    try {
      $content = $this->getSepaXmlFromArr($transactionsArr, $inhaber, $iban, $bic);
    }
    catch(Exception $e) {
      $content = '';
    }

    if(empty($content) || !file_put_contents($xmlFile, $content)) {
      foreach($ids as $id) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE `payment_transaction` SET `payment_status` = 'error' WHERE id = %d",
            $id
          )
        );
      }
      return $checkPayment;
    }
    $zip = new ZipArchive();
    if(is_file($file)){
      @unlink($file);
    }

    $zip->open($file, ZipArchive::CREATE);

    if(is_file($xmlFile)){
      $zip->addFile($xmlFile, basename($xmlFile));
    }

    $zip->close();
    foreach($ids as $id) {
      $paymentTransaction = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `payment_transaction` WHERE `id` = %d',
          $id
        )
      );
      $paymentTransaction['json'] = json_decode($paymentTransaction['payment_json'], true);
      if(empty($paymentTransaction['json'])) {
        $paymentTransaction['json'] = [];
      }
      $paymentTransaction['json']['file'] = $file;
      $this->app->DB->Update(
        sprintf(
          "UPDATE `payment_transaction` SET `payment_json` = '%s', `payment_status` = 'verbucht' WHERE id = %d",
          $this->app->DB->real_escape_string(json_encode($paymentTransaction['json'])), $paymentTransaction['id']
        )
      );
      if(empty($paymentTransaction['returnorder_id']) && !empty($paymentTransaction['liability_id'])) {
        $this->app->DB->Update(
          sprintf(
            "UPDATE `verbindlichkeit` SET `status` = 'bezahlt' WHERE `id` = %d",
            $paymentTransaction['liability_id']
          )
        );
      }
    }

    $checkPayment['file'] = $file;

    $this->app->erp->CreateDateiWithStichwort(
      'sepa.zip',//'sepa_lastschrift.zip',
      'sepa.zip',//
      '',
      '',
      $file,
      $this->app->User->GetName(),
      'Anhang',
      'payment_transaction_group',
      $paymentGroupId
    );

    return $checkPayment;

    /*$cmdtmp = array('erste','folge','letzte');

    try {
      foreach ($splitted as $cmd => $paymentTransactions) {
        $xmlFile = $filePrefix . "sepa_lastschrift_$cmd.xml";
        if(is_file($xmlFile)){
          @unlink($xmlFile);
        }
        $sepaxml = $this->createXmlByJson($paymentTransactions, $cmd, $inhaber, $iban, $bic, $glaeubiger);
        if(!empty($sepaxml)){
          file_put_contents($xmlFile, $sepaxml);
        }
      }

      // zippen und archiv anbieten

      $zip = new ZipArchive();
      if(is_file($file)){
        @unlink($file);
      }

      $zip->open($file, ZipArchive::CREATE);
      foreach ($cmdtmp AS $cmd) {
        if(is_file($filePrefix . sprintf('sepa_lastschrift_%s.xml', $cmd))){
          $zip->addFile($filePrefix . sprintf('sepa_lastschrift_%s.xml', $cmd));
        }
      }

      $zip->close();
    }
    catch(Exception $e) {
      $checkPayment['error'] = $e->getMessage();

      $checkPayment['status'] = 0;
      foreach($splitted as $cmd => $paymentTransactions) {
        foreach($paymentTransactions as $paymentTransaction) {
          $this->app->DB->Update(
            sprintf(
              "UPDATE `payment_transaction` SET `payment_status` = 'error' WHERE id = %d",
              $paymentTransaction['id']
            )
          );
        }
      }

      return $checkPayment;
    }
    foreach($splitted as $cmd => $paymentTransactions) {
      foreach($paymentTransactions as $paymentTransaction) {
        $paymentTransaction['json']['file'] = $file;
        $this->app->DB->Update(
          sprintf(
            "UPDATE `payment_transaction` SET `payment_json` = '%s', `payment_status` = 'verbucht' WHERE id = %d",
            $this->app->DB->real_escape_string(json_encode($paymentTransaction['json'])), $paymentTransaction['id']
          )
        );
      }
    }

    $checkPayment['file'] = $file;

    $this->app->erp->CreateDateiWithStichwort(
      'sepa.zip',//'sepa_lastschrift.zip',
      'sepa.zip',//
      '',
      '',
      $file,
      $this->app->User->GetName(),
      'Anhang',
      'payment_transaction_group',
      $paymentGroupId
    );

    return $checkPayment;*/
  }

  /**
   * @param array $transactionAccountIds
   *
   * @return array
   */
  public function getJsonTransactionArrByIds($transactionAccountIds)
  {
    $transactionAccounts = $this->app->DB->SelectArr(
      sprintf(
        "SELECT * FROM `payment_transaction` WHERE `id` IN (%s)",
        implode(',',$transactionAccountIds)
      )
    );
    if(empty($transactionAccounts)) {
      return [];
    }

    foreach ($transactionAccounts as $key => $transactionAccount) {
      $json = @json_decode($transactionAccount['payment_json'], true);
      $transactionAccount['json'] = $json;
      if(!empty($transactionAccount['json']['swift']) && empty($transactionAccount['json']['blz'])){
        $transactionAccount['json']['blz'] = $transactionAccount['json']['swift'];
      }
      elseif(!empty($transactionAccount['json']['blz']) && empty($transactionAccount['json']['swift'])){
        $transactionAccount['json']['swift'] = $transactionAccount['json']['blz'];
      }
      if(!empty($transactionAccount['json']['iban']) && empty($transactionAccount['json']['konto'])){
        $transactionAccount['json']['konto'] = $transactionAccount['json']['iban'];
      }
      elseif(!empty($transactionAccount['json']['konto']) && empty($transactionAccount['json']['iban'])){
        $transactionAccount['json']['iban'] = $transactionAccount['json']['konto'];
      }
      $transactionAccounts[$key]['json'] = $transactionAccount['json'];
      foreach($transactionAccount['json'] as $subKey => $val) {
        if(!isset($transactionAccounts[$key][$subKey])) {
          $transactionAccounts[$key][$subKey] = $val;
        }
      }
      if(!empty($transactionAccounts[$key]['empfaenger'] && empty($transactionAccounts[$key]['name']))) {
        $transactionAccounts[$key]['name'] = $transactionAccounts[$key]['empfaenger'];
      }

      if(empty($transactionAccounts[$key]['name'])) {
        if(empty($transactionAccounts[$key]['address_id'])) {
          if($transactionAccounts[$key]['returnorder_id'] > 0) {
            $transactionAccounts[$key]['address_id'] = $this->app->DB->Select(
              sprintf(
                'SELECT `adresse` FROM `retoure` WHERE `id` = %d',
                $transactionAccounts[$key]['returnorder_id']
              )
            );
          }
          elseif($transactionAccounts[$key]['liability_id']) {
            $transactionAccounts[$key]['address_id'] = $this->app->DB->Select(
              sprintf(
                'SELECT `adresse` FROM `verbindlichkeit` WHERE `id` = %d',
                $transactionAccounts[$key]['liability_id']
              )
            );
          }
        }
        if(!empty($transactionAccounts[$key]['address_id'])) {
          $transactionAccounts[$key]['name'] = $this->app->DB->Select(
            sprintf(
              'SELECT `name` FROM `adresse` WHERE `id` = %d',
              $transactionAccounts[$key]['address_id']
            )
          );
        }
      }
    }

    return $transactionAccounts;
  }

  /**
   * @param int[] $transactionAccountIds
   *
   * @return array
   */
  public function splitTransactionAccountIds($transactionAccountIds)
  {
    $transactionAccounts = $this->getJsonTransactionArrByIds($transactionAccountIds);

    $erste = [];
    $folge = [];
    $letzte = [];

    if(!empty($transactionAccounts)){
      foreach ($transactionAccounts as $key => $transactionAccount) {
        $json = @json_decode($transactionAccount['payment_json'], true);
        if(!isset($transactionAccount['json'])){
          $transactionAccount['json'] = $json;
        }

        if(($json['mandatsreferenzart'] === 'whd' && $json['mandatsreferenzwdhart'] === 'erste') ||
          $json['mandatsreferenzart'] === 'einmalig'
        ){
          $erste[] = $transactionAccount;
        }elseif($json['mandatsreferenzart'] === 'whd' && $json['mandatsreferenzwdhart'] === 'folge'){
          $letzte[] = $transactionAccount;
        }elseif(($json['mandatsreferenzart'] === 'whd' && $json['mandatsreferenzwdhart'] === 'folge') ||
          (string)$json['mandatsreferenzart'] === ''
        ){
          $folge[] = $transactionAccount;
        }
      }
    }

    return ['erste' => $erste, 'folge'=>$folge, 'letzte' => $letzte];
  }

  /**
   * @param array  $paymentTransactions
   * @param string $cmd
   * @param string $inhaber
   * @param string $iban
   * @param string $bic
   * @param string $glaeubiger
   *
   * @return string
   */
  public function createXmlByJson($paymentTransactions, $cmd, $inhaber, $iban, $bic, $glaeubiger)
  {
    if(empty($paymentTransactions)) {
      return '';
    }

    if(!class_exists('SepaXmlCreator')) {
      require_once dirname(dirname(__DIR__)) . '/sepa/SepaXmlCreator.php';
    }


    // Erzeugen einer neuen Instanz
    $creator = new SepaXmlCreator();
    if($cmd==='erste'){
      $creator->setAusfuehrungDatum(date('Y-m-d', strtotime('+10 days'))); //6
    }
    else if($cmd==='folge') {
      $creator->setAusfuehrungDatum(date('Y-m-d', strtotime('+6 days'))); //3

    }
    else{
      $creator->setAusfuehrungDatum(date('Y-m-d', strtotime('+10 days')));
    }

    /*
    * Mit den Account-Werten wird das eigene Konto beschrieben
    * erster Parameter = Name
    * zweiter Parameter = IBAN
    * dritter Paramenter = BIC
    */
    $creator->setAccountValues($inhaber, $iban, $bic);

    /*
    * Setzen Sie von der Bundesbank übermittelte Gläubiger-ID
    */

    $creator->setGlaeubigerId($glaeubiger);

    if($cmd==='folge'){
      $creator->setIsFolgelastschrift();
    }

    // pro Auftrag wenn dann
    //$creator->setAusfuehrungOffset(7);
    foreach($paymentTransactions as $paymentTransaction) {
      $json = $paymentTransaction['json'];
      $verwendungszweck = $this->app->erp->UmlauteEntfernen($json['vz1']);
      $verwendungszweck = preg_replace('/[^A-Za-z0-9\-äöüßÖÜÄ .]/u', '', $verwendungszweck);
      $betrag = $json['betrag'];
      $waehrung = $json['waehrung'];
      if($waehrung == ''){
        $waehrung = "EUR";
      }

      $creator->setCurrency($waehrung);

      $adr_id = $json['adresse'];
      if(!empty($json['bic'])){
        $adr_bic = str_replace(' ', '', strtoupper(trim($json['bic'])));
      }
      else {
        $adr_bic = str_replace(' ', '', strtoupper(trim($json['blz'])));
      }
      if(!empty($json['iban'])) {
        $adr_iban = str_replace(' ', '', strtoupper(trim($json['iban'])));
      }
      else{
        $adr_iban = str_replace(' ', '', strtoupper(trim($json['konto'])));
      }
      $mandatsreferenzaenderung = $json['mandatsreferenzaenderung'];

      $addressRow = $this->app->DB->SelectRow(
        sprintf(
          'SELECT adr.mandatsreferenz,adr.mandatsreferenzdatum,adr.kundennummer 
          FROM `adresse` AS `adr` 
          WHERE adr.id=%d 
          LIMIT 1',
          $adr_id
        )
      );

      $mandatsreferenz = trim($json['mandatsreferenz']);
      $mandatsreferenzdatum = $json['mandatsreferenzdatum'];

      if($mandatsreferenzaenderung == 1){
        $mandatsreferenzaenderung = true;
      }else{
        $mandatsreferenzaenderung = false;
      }

      if($mandatsreferenzdatum === '0000-00-00' || $mandatsreferenzdatum == ''){
        $mandatsreferenzdatum = date('Y') . '-01-01';
      }
      if($mandatsreferenz === '' || $mandatsreferenz === '0'){
        $mandatsreferenz = $addressRow['kundennummer'];
      }
      $mandatsreferenz = preg_replace('/[^A-Za-z0-9]+/', '', $mandatsreferenz);

      $adr_inhaber = $this->app->erp->UmlauteEntfernen($json['name']);
      $adr_inhaber = preg_replace('/[^A-Za-z0-9\-äöüßÖÜÄ+ .]/u', '', $adr_inhaber);
      //$betrag = str_replace('.','',$betrag);
      $betrag = str_replace(',', '', $betrag);
      // Erzeugung einer neuen Buchungssatz
      $buchung = new SepaBuchung();
      // gewünschter Einzugsbetrag
      $buchung->setBetrag($betrag);
      // gewünschte End2End Referenz (OPTIONAL)
      //$buchung->setCurrency($waehrung);
      //$buchung->setEnd2End('ID-00002');
      // BIC des Zahlungspflichtigen Institutes
      $buchung->setBic($adr_bic);
      // Name des Zahlungspflichtigen
      $buchung->setName($adr_inhaber);//'Mustermann, Max');
      // IBAN des Zahlungspflichtigen
      $buchung->setIban($adr_iban);
      // gewünschter Verwendungszweck (OPTIONAL)
      $buchung->setVerwendungszweck($verwendungszweck);
      // Referenz auf das vom Kunden erteilte Lastschriftmandat
      // ID = MANDAT0001
      // Erteilung durch Kunden am 20. Mai 2013
      // False = seit letzter Lastschrift wurde am Mandat nichts geändert
      $buchung->setMandat($mandatsreferenz, $mandatsreferenzdatum, $mandatsreferenzaenderung);
      // Buchung zur Liste hinzufügen
      $creator->addBuchung($buchung);
    }

    return $creator->generateBasislastschriftXml();
  }

  /**
   * @param int    $id
   * @param string $cmd
   * @param string $inhaber
   * @param string $iban
   * @param string $bic
   * @param string $glaeubiger
   *
   * @return string
   */
  public function createXml($id, $cmd, $inhaber, $iban, $bic, $glaeubiger)
  {
    if($cmd==='erste'){
      // HACK einmalige raus schmeissen
      $dta = $this->app->DB->SelectArr(
        "SELECT * 
        FROM dta 
        WHERE datei='$id' AND ((mandatsreferenzart='wdh' AND mandatsreferenzwdhart='erste') 
                                     OR mandatsreferenzart='einmalig')"
      );
    }
    else if($cmd==='folge'){
      $dta = $this->app->DB->SelectArr(
        "SELECT * 
        FROM dta 
        WHERE datei='$id' AND ((mandatsreferenzart='wdh' AND mandatsreferenzwdhart='folge') 
          OR (mandatsreferenzart='' AND mandatsreferenzwdhart=''))");
    }
    else if($cmd==='letzte'){
      $dta = $this->app->DB->SelectArr(
        "SELECT * 
        FROM dta 
        WHERE datei='$id' AND mandatsreferenzart='wdh' AND mandatsreferenzwdhart='letzte'"
      );
    }

    if(empty($dta)) {
      return '';
    }

    if(!class_exists('SepaXmlCreator')) {
      require_once dirname(dirname(__DIR__)) . '/sepa/SepaXmlCreator.php';
    }
    // Erzeugen einer neuen Instanz
    $creator = new SepaXmlCreator();

    if($cmd==='erste'){
      $creator->setAusfuehrungDatum(date('Y-m-d', strtotime('+10 days'))); //6
    }
    else if($cmd==='folge') {
      $creator->setAusfuehrungDatum(date('Y-m-d', strtotime('+6 days'))); //3

    }
    else{
      $creator->setAusfuehrungDatum(date('Y-m-d', strtotime('+10 days')));
    }
    /*
    * Mit den Account-Werten wird das eigene Konto beschrieben
    * erster Parameter = Name
    * zweiter Parameter = IBAN
    * dritter Paramenter = BIC
    */
    $creator->setAccountValues($inhaber, $iban, $bic);

    /*
    * Setzen Sie von der Bundesbank übermittelte Gläubiger-ID
    */

    $creator->setGlaeubigerId($glaeubiger);

    if($cmd==='folge'){
      $creator->setIsFolgelastschrift();
    }

    // pro Auftrag wenn dann
    //$creator->setAusfuehrungOffset(7);


    $cDta = empty($dta)?0:count($dta);
    for($i=0;$i<$cDta;$i++) {
      $verwendungszweck = $this->app->erp->UmlauteEntfernen($dta[$i]['vz1']);
      $verwendungszweck = preg_replace('/[^A-Za-z0-9\-äöüßÖÜÄ .]/u', '', $verwendungszweck);
      $betrag = $dta[$i]['betrag'];
      $waehrung = $dta[$i]['waehrung'];
      if($waehrung=='') {
        $waehrung="EUR";
      }

      $creator->setCurrency($waehrung);

      $adr_id = $dta[$i]['adresse'];
      $adr_bic = str_replace(' ','',strtoupper(trim($dta[$i]['blz'])));
      $adr_iban = str_replace(' ','',strtoupper(trim($dta[$i]['konto'])));
      $mandatsreferenzaenderung = $dta[$i]['mandatsreferenzaenderung'];

      $addressRow = $this->app->DB->SelectRow(
        sprintf(
          'SELECT adr.mandatsreferenz,adr.mandatsreferenzdatum,adr.kundennummer 
          FROM `adresse` AS `adr` 
          WHERE adr.id=%d 
          LIMIT 1',
          $adr_id
        )
      );

      $mandatsreferenz = trim($addressRow['mandatsreferenz']);
      $mandatsreferenzdatum = $addressRow['mandatsreferenzdatum'];

      if($mandatsreferenzaenderung==1) {
        $mandatsreferenzaenderung=true;
      } else {
        $mandatsreferenzaenderung=false;
      }

      if($mandatsreferenzdatum==='0000-00-00' || $mandatsreferenzdatum=='') {
        $mandatsreferenzdatum=date('Y').'-01-01';
      }
      if($mandatsreferenz==='' || $mandatsreferenz==='0') {
        $mandatsreferenz = $addressRow['kundennummer'];
      }
      $mandatsreferenz = preg_replace('/[^A-Za-z0-9]+/', '', $mandatsreferenz);

      $adr_inhaber = $this->app->erp->UmlauteEntfernen($dta[$i]['name']);
      $adr_inhaber = preg_replace('/[^A-Za-z0-9\-äöüßÖÜÄ+ .]/u','',$adr_inhaber);
      //$betrag = str_replace('.','',$betrag);
      $betrag = str_replace(',','',$betrag);
      // Erzeugung einer neuen Buchungssatz
      $buchung = new SepaBuchung();
      // gewünschter Einzugsbetrag
      $buchung->setBetrag($betrag);
      // gewünschte End2End Referenz (OPTIONAL)
      //$buchung->setCurrency($waehrung);
      //$buchung->setEnd2End('ID-00002');
      // BIC des Zahlungspflichtigen Institutes
      $buchung->setBic($adr_bic);
      // Name des Zahlungspflichtigen
      $buchung->setName($adr_inhaber);//'Mustermann, Max');
      // IBAN des Zahlungspflichtigen
      $buchung->setIban($adr_iban);
      // gewünschter Verwendungszweck (OPTIONAL)
      $buchung->setVerwendungszweck($verwendungszweck);
      // Referenz auf das vom Kunden erteilte Lastschriftmandat
      // ID = MANDAT0001
      // Erteilung durch Kunden am 20. Mai 2013
      // False = seit letzter Lastschrift wurde am Mandat nichts geändert
      $buchung->setMandat($mandatsreferenz, $mandatsreferenzdatum, $mandatsreferenzaenderung);
      // Buchung zur Liste hinzufügen
      $creator->addBuchung($buchung);
    }

    return $creator->generateBasislastschriftXml();
  }

  /**
   * @param int              $id
   * @param null|Application $app
   * @param string           $filePrefix
   *
   * @return string
   */
  protected function getZipFileFromDta($id, $app = null, $filePrefix = '')
  {
    if(empty($this->app) && $app !== null) {
      $this->app = $app;
    }

    if($filePrefix === '') {
      $filePrefix = $this->app->erp->GetTMP().$this->app->User->GetID();
    }

    $file = $filePrefix.'sepa_lastschrift.zip';

    $this->app->DB->Update(
      sprintf(
        "UPDATE `dta_datei` SET `status`='abgeschlossen' WHERE `id` = %d LIMIT 1",
        $id
      )
    );

    $konto = $this->app->DB->Select(
      sprintf(
        'SELECT `konto` FROM `dta_datei` WHERE `id` = %d LIMIT 1',
        $id
      )
    );

    $kontoRow = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM `konten` WHERE `id` = %d LIMIT 1',
        $konto
      )
    );
    $bic = strtoupper(str_replace(' ','', trim($kontoRow['swift'])));
    $iban = strtoupper(str_replace(' ','',trim($kontoRow['iban'])));
    $inhaber = trim($kontoRow['inhaber']);
    $glaeubiger = trim($kontoRow['glaeubiger']);

    if($glaeubiger==='') {
      $glaeubiger = $this->app->erp->Firmendaten('sepaglaeubigerid');
    }
    $inhaber = preg_replace('/[^A-Za-z0-9\-äöüßÖÜÄ+ .]/u','',$inhaber);
    $glaeubiger = preg_replace('/[^A-Za-z0-9\-äöüßÖÜÄ+ .]/u','',$glaeubiger);

    $cmdtmp = array('erste','folge','letzte');

    foreach($cmdtmp as $cmd) {
      $xmlFile = $filePrefix."sepa_lastschrift_$cmd.xml";
      if(is_file($xmlFile)) {
        @unlink($xmlFile);
      }
      $sepaxml = $this->createXml($id, $cmd,$inhaber,$iban,$bic,$glaeubiger);
      if(!empty($sepaxml)) {
        file_put_contents($xmlFile, $sepaxml);
      }
    }
    // zippen und archiv anbieten

    $zip = new ZipArchive();
    if(is_file($file)) {
      @unlink($file);
    }

    $zip->open($file, ZipArchive::CREATE);
    foreach($cmdtmp AS $cmd) {
      if(is_file($filePrefix.sprintf('sepa_lastschrift_%s.xml',$cmd))) {
        $zip->addFile($filePrefix.sprintf('sepa_lastschrift_%s.xml',$cmd));
      }
    }

    $zip->close();

    return $file;
  }

  /**
   * @param array  $dta
   * @param string $inhaber
   * @param string $iban
   * @param string $bic
   *
   * @return string
   *
   * @throws Exception
   */
  function getSepaXmlFromArr($dta, $inhaber, $iban, $bic)
  {
    if(!class_exists('Sepa_credit_XML_Transfer_initation')){
      include_once dirname(dirname(dirname(__DIR__))) . '/plugins/sepa/Sepa_credit_XML_Transfer_initation.class.php';
    }
    if(!class_exists('Sepa_credit_XML_Transfer_initation')){
      throw new Exception('Class Sepa_credit_XML_Transfer_initation not found');
    }
    /*
    $id = $this->app->Secure->GetGET("id");

    if($id < 0) return;*/

    //$this->app->DB->Update("UPDATE dta_datei SET status='abgeschlossen' WHERE id='$id' LIMIT 1");

    /*$konto = $this->app->DB->Select("SELECT konto FROM dta_datei WHERE id='".$id."' LIMIT 1");

    $bic = $this->app->DB->Select("SELECT swift FROM konten WHERE id='$konto' LIMIT 1");
    $iban = $this->app->DB->Select("SELECT iban FROM konten WHERE id='$konto' LIMIT 1");
    $inhaber = $this->app->DB->Select("SELECT inhaber FROM konten WHERE id='$konto' LIMIT 1");*/

    $inhaber = $this->app->erp->UmlauteEntfernen($inhaber);
    $iban = str_replace(' ','',$iban);
    $bic = str_replace(' ','',$bic);



    //header ('Content-Type:text/xml');
    //header('Content-Disposition: attachment; filename="sepa.xml"');

    $test = new Sepa_credit_XML_Transfer_initation(date('Ymd')); // batch name
    $test->setOrganizationName($inhaber); // your accountname
    $test->setOrganizationIBAN($iban);  // your IBAN
    $test->setOrganizationBIC($bic);  // your BIC

    $cdta = empty($dta)?0:count($dta);
    for($i=0;$i<$cdta;$i++) {
      $verwendungszweck = $this->app->erp->UmlauteEntfernen($dta[$i]['vz1']);

      $betrag = $this->app->erp->ReplaceBetrag(1, $dta[$i]['betrag']);
      $waehrung = !empty($dta[$i]['curreny'])?$dta[$i]['curreny']:$dta[$i]['waehrung'];

      //$adr_id = $dta[$i]['adresse'];
      if(!empty($dta[$i]['bic'])) {
        $adr_bic = $dta[$i]['bic'];
      }
      else{
        $adr_bic = $dta[$i]['blz'];
      }
      if(!empty($dta[$i]['iban'])) {
        $adr_iban = $dta[$i]['iban'];
      }
      else{
        $adr_iban = $dta[$i]['konto'];
      }
      $adr_inhaber = $this->app->erp->UmlauteEntfernen($dta[$i]['name']);

      //$betrag = str_replace('.','',$betrag);
      $betrag = str_replace(',','',$betrag);
      $adr_bic = str_replace(',','',$adr_bic);
      $adr_iban = str_replace(',','',$adr_iban);

      // add 3 test transactions
      $test_transaction = new Sepa_credit_XML_Transfer_initation_Transaction(
        $adr_inhaber,$betrag,$adr_iban,$adr_bic,$verwendungszweck,true,$waehrung
      );

      // add the first to payment group 'a', second and third to 'b'
      $test->addTransaction($test_transaction,'001');
    }
    $test->build();

    return $test->getXML();
  }

  /**
   * @param int              $id
   * @param null|Application $app
   */
  public function ZahlungsverkehrDownloadDTALastschrift($id, $app = null, $filePrefix = '')
  {
    if(!empty($app)) {
      $this->app = $app;
    }
    if($filePrefix === '') {
      $filePrefix = $this->app->erp->GetTMP().$this->app->User->GetID();
    }

    $zipFile = $this->getZipFileFromDta($id, null, $filePrefix);
    $this->downloadFile($zipFile);
  }

  /**
   * @param string $zipFile
   */
  public function downloadFile($zipFile)
  {
    header('Content-Type: application/xml; charset=utf-8');
    $filename = basename($zipFile);
    if(strpos($filename,'..') === false && substr($filename,-4) !== '.zip') {
      $filename = $this->app->erp->Dateinamen(basename($zipFile));
      if(substr($filename,-4) !== '.zip' || strpos($filename,'..') === false) {
        $filename = 'sepa.zip';
      }
    }
    else {
      $filename = 'sepa.zip';
    }

    // http headers for zip downloads
    header('Pragma: public');
    header('Expires: 0');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Cache-Control: public');
    header('Content-Description: File Transfer');
    header('Content-type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.$filename.'"');
    header('Content-Transfer-Encoding: binary');
    header('Content-Length: '.filesize($zipFile));
    ob_end_flush();
    @readfile($zipFile);

    $this->app->ExitXentral();
  }
}
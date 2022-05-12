<?php
class LiveimportBase {
  /** @var Application $app */
  protected $app;
  /** @var int $id */
  protected $id;

  /**
   * @param Application $app
   */
  public function loadApp($app, $id)
  {
    $this->app = $app;
    $this->id = $id;
  }

  /**
   * @param string $config
   *
   * @return array
   */
  public function Config2Array($config)
  {
    $entries = explode (';', $config);
    foreach ($entries as $pair) {
      preg_match('/(.+)=>(.+)$/', $pair, $matches);
      if(isset($matches[2])) {
        $array[$matches[1]] = $matches[2];
      }
    }
    if(!isset($array)) {
      return [];
    }

    return array_filter($array);
  }

  /**
   * @return array
   */
  protected function getCredentialsFromId()
  {
    if(!$this->id || empty($this->app)) {
      return [];
    }
    $account = $this->app->DB->SelectRow(
      sprintf(
        'SELECT `liveimport`, `liveimport_passwort` FROM `konten` WHERE `id` = %d',
        $this->id
      )
    );
    if(empty($account)) {
      return [];
    }
    $credential = html_entity_decode($account['liveimport'],ENT_QUOTES,'UTF-8');
    if(!empty($account['liveimport_passwort'])) {
      $credential = str_replace('{PASSWORT}', $account['liveimport_passwort'], $credential);
    }

    return $this->Config2Array($credential);
  }


  /**
   * @return array
   */
  public static function liveimportCapabilities()
  {
    return [
      'payment_modules' =>
        [
          'sepa'   => [
              ''
          ],
          'paypal' => [
            ''
          ],
          'stripe' => [
            ''
          ],
          'amazonpay' => [
            ''
          ],
          'amazonfbm' => [
            ''
          ],
        ],
      'default_module' => 'sepa',
      'mapping' => [
        'amazonkonto' => 'amazonfbm',
      ]
    ];
  }

  /**
   * @param int[] $paymentTransactionIds
   *
   * @return int
   */
  public function createReturnOrdersPaymentEntries($paymentTransactionIds)
  {
    if(empty($paymentTransactionIds)) {
      return 0;
    }
    $ret = 0;
    foreach($paymentTransactionIds as $paymentTransactionId) {
      if($this->createReturnOrderPaymentEntry($paymentTransactionId)) {
        $ret++;
      }
    }

    return $ret;
  }

  /**
   * @param int|array|null $paymentTransaction
   *
   * @return bool|array
   */
  protected function isPaymentTransactionOk($paymentTransaction)
  {
    if(is_numeric($paymentTransaction)) {
      $paymentTransaction = $this->app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `payment_transaction` WHERE `id` = %d',
          $paymentTransaction
        )
      );
    }

    if(empty($paymentTransaction)) {
      return false;
    }

    if(empty($paymentTransaction)) {
      return false;
    }
    if(in_array($paymentTransaction['payment_status'], ['payed','verbucht','abgeschlossen'])) {
      return false;
    }
    $returnOrderId = $paymentTransaction['returnorder_id'];
    $liabilityId = $paymentTransaction['liability_id'];
    if($returnOrderId > 0){
      $orders = $this->app->DB->SelectRow(
        sprintf(
          "SELECT ro.*, ro.id AS returnorder_id
          FROM `gutschrift` AS `ro`
          WHERE ro.id = %d 
            AND ro.belegnr <> '' AND ro.status <> 'storniert'
          LIMIT 1",
          $returnOrderId
        )
      );
      if(empty($orders)){
        return false;
      }
    }
    elseif($liabilityId > 0) {
      $orders = $this->app->DB->SelectRow(
        sprintf(
          "SELECT v.*, v.betrag AS `soll`, v.id AS liability_id
          FROM `verbindlichkeit` AS `v`
          WHERE v.id = %d 
            AND v.freigabe='1' AND v.rechnungsfreigabe=1 
            AND v.status_beleg <> 'storniert' AND v.status_beleg <> 'angelegt' 
            AND v.betrag > 0
          LIMIT 1",
          $liabilityId
        )
      );
      if(empty($orders)){
        return false;
      }
    }
    else {
      $orders = [
        'soll' => $paymentTransaction['amount'],
        'waehrung' => $paymentTransaction['currency'],
        'json' => @json_decode($paymentTransaction['payment_json'], true),
        'returnorder_id' => 0,
        'liability_id' => 0,
      ];
    }

    return $orders;
  }

  /**
   * @param array $order
   *
   * @return array
   */
  public function getJsonFormPaymentEntry($order)
  {
    return [];
  }

  /**
   * @param int $paymentTransactionId
   *
   * @return bool
   */
  public function createReturnOrderPaymentEntry($paymentTransactionId)
  {
    $orders = $this->isPaymentTransactionOk($paymentTransactionId);
    if($orders === false) {
      return false;
    }

    $amount = empty($orders['soll'])?null:$orders['soll'];
    $currency = empty($orders['waehrung'])?'EUR':$orders['waehrung'];
    $json = $this->getJsonFormPaymentEntry($orders);

    $this->app->DB->Update(
      sprintf(
        "UPDATE `payment_transaction` 
           SET `payment_account_id` = %d, 
               `payment_status` = '%s',
               `payment_reason` = '%s',
               `amount` = %f,
               `currency` = '%s'
           WHERE `id` = %d",
        $this->id,
        'angelegt',
        '',
        $amount,
        $this->app->DB->real_escape_string($currency),
        $paymentTransactionId
      )
    );

    if (!empty($json)) {
      $jsonString = json_encode($json);
      $this->app->DB->Update(
        sprintf(
          "UPDATE `payment_transaction` 
           SET `payment_json` = '%s'
           WHERE `id` = %d",
          $this->app->DB->real_escape_string($jsonString),
          $this->id
        )
      );
    }

    $paymentTransaction = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM `payment_transaction` WHERE `id` = %d',
        $paymentTransactionId
      )
    );
    if(!empty($paymentTransaction) && empty($paymentTransaction['address_id'])) {
      if($paymentTransaction['returnorder_id'] > 0) {
        $document = $this->app->DB->SelectRow(
          sprintf(
            'SELECT `adresse` FROM `gutschrift` WHERE `id` = %d',
            $paymentTransaction['returnorder_id']
          )
        );
      }
      elseif($paymentTransaction['liability_id'] > 0) {
        $document = $this->app->DB->SelectRow(
          sprintf(
            'SELECT `adresse` FROM `verbindlichkeit` WHERE `id` = %d',
            $paymentTransaction['liability_id']
          )
        );
      }
      if(!empty($document['adresse'])) {
        $this->app->DB->Update(
          sprintf(
            'UPDATE `payment_transaction` SET `address_id` = %d WHERE `id` = %d',
            $document['adresse'], $paymentTransactionId
          )
        );
      }
    }

    return $this->app->DB->affected_rows() > 0;
  }

  /**
   * @return array
   */
  public function showReturnOrderStructure()
  {
    return [];
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
    return [];
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
    return [];
  }


  /**
   * @param int $paymentAccountId
   *
   * @return int
   */
  public function createPaymentGroup($paymentAccountId)
  {
    $this->app->DB->Insert(
      sprintf(
        "INSERT INTO `payment_transaction_group` 
        (`created_by`, `payment_account_id`, `created_at`) 
        VALUES ('%s', %d, NOW())",
        $this->app->DB->real_escape_string($this->app->User->GetName()), $paymentAccountId
      )
    );

    return (int)$this->app->DB->GetInsertID();
  }

  /**
   * @param int   $paymentGroupId
   * @param int[] $paymentTransactionIds
   */
  public function addPaymentsToGroup($paymentGroupId, $paymentTransactionIds)
  {
    if(empty($paymentGroupId) || empty($paymentTransactionIds)) {
      return;
    }
    $this->app->DB->Update(
      sprintf(
        "UPDATE `payment_transaction` SET `payment_transaction_group_id` = %d WHERE `id` IN (%s)",
        $paymentGroupId, implode(',', $paymentTransactionIds)
      )
    );
  }


  /**
   * @param int $paymentTransactionId
   *
   * @return array|null
   */
  public function getJsonFromPaymentTransaction($paymentTransactionId)
  {
    $paymentTransaction = $this->app->DB->SelectRow(
      sprintf(
        "SELECT `payment_json` 
        FROM `payment_transaction` AS `pt` 
        WHERE pt.id = %d",
        $paymentTransactionId
      )
    );
    if(empty($paymentTransaction)) {
      return null;
    }
    $json = json_decode($paymentTransaction['payment_json'], true);
    if(!is_array($json)) {
      $json = [];
    }

    return $json;
  }

  /**
   * @param int    $paymentTransactionId
   * @param string $name
   * @param mixed  $value
   */
  public function addJsonEntry($paymentTransactionId, $name, $value)
  {
    if($name === '') {
      return;
    }
    $json = $this->getJsonFromPaymentTransaction($paymentTransactionId);
    if($json === null) {
      return;
    }
    if(isset($json[$name]) && $json[$name] === $value) {
      return;
    }
    $json[$name] = $value;
    $this->app->DB->Update(
      sprintf(
        "UPDATE `payment_transaction` SET `payment_json` = '%s' WHERE `id` = %d",
        $this->app->DB->real_escape_string(json_encode($json)), $paymentTransactionId
      )
    );
  }

  /**
   * @param int    $paymentTransactionId
   * @param string $name
   */
  public function removeJsonEntry($paymentTransactionId, $name)
  {
    if($name === '') {
      return;
    }
    $json = $this->getJsonFromPaymentTransaction($paymentTransactionId);
    if($json === null) {
      return;
    }
    if(!isset($json[$name])) {
      return;
    }
    unset($json[$name]);
    $this->app->DB->Update(
      sprintf(
        "UPDATE `payment_transaction` SET `payment_json` = '%s' WHERE `id` = %d",
        $this->app->DB->real_escape_string(json_encode($json)), $paymentTransactionId
      )
    );
  }
}

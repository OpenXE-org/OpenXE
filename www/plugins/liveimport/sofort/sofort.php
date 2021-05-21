<?php

/**
 * Class sofort
 * sofortimport for 'sofort.com'
 *
 * @see https://www.sofort.com/integrationCenter-ger-DE/content/view/full/3047/#h4-6
 */
class sofort
{
  const COL_DIVIDER = ';';
  const ROW_DIVIDER = "\r\n";
  const DATE_FORMAT = 'Y-m-d';
  const URL = 'https://api.sofort.com/api/xml';
  const CONTENT_TYPE = 'application/xml; charset=UTF-8';

  /**
   * customer id as user name for API-access
   *
   * @var string
   */
  private $customerID = '';

  /**
   * The API key
   *
   * Den API-Key können Sie im Anbietermenü unter Weitere Dienste -> API-Key einsehen.
   *
   * @var string
   */
  private $api = '';

  /**
   * look n days into the past, max 30 days allowed!
   *
   * @var int
   */
  private $days = 5;


  /**
   * Als Benutzername verwenden Sie bitte Ihre Kundennummer (bspw. 99999)
   * als Passwort Ihren API-Key (bspw. a12b34cd567890123e456f7890123456)
   *
   * required credentials in array $zugangsdaten:
   * array (
   *  'API_USER' => 'customer id'
   *  'API_KEY'  => 'API key'
   * )
   *
   * optional: 'API_DAYS'
   *
   * @param array $credentials
   * @param string csv
   *
   * @throws Exception
   * @throws RuntimeException
   * @throws ResponseException
   * @throws MissingArgumentException
   *
   * @return string
   */
  public function Import(array $credentials)
  {
    $credentials = array_change_key_case($credentials, CASE_UPPER);

    if (!array_key_exists('API_USER', $credentials)) {
      throw new MissingArgumentException('API_USER fehlt');
    }
    $this->customerID = $credentials['API_USER'];

    if (!array_key_exists('API_KEY', $credentials)) {
      throw new MissingArgumentException('API_KEY fehlt');
    }
    $this->api = $credentials['API_KEY'];


    if (array_key_exists('API_DAYS', $credentials)) {
      $days = $credentials['API_DAYS'];

      if (is_numeric($days)) {
        $days = (int) $days;
        $this->days = $days;
      }
    }

    $csv = $this->importLoop();

    $csv = implode(self::ROW_DIVIDER, $csv);
    $csv = utf8_decode($csv);

    return $csv;
  }

  /**
   * copied and modified from 'ImportKontoauszug' in 'class.erpapi'
   * used case "stripe" in switch statement
   *
   * @param string $csv the csv 'file' to import
   * @param int $konto the konto id
   * @param $app
   * @return array($inserted, $duplicate);
   */
  public function ImportKontoauszug($csv, $konto, $app)
  {
    $inserted = 0;
    $duplicate = 0;

    $db_array = preg_split("/(\r\n)+|(\n|\r)+/", $csv);
    if (empty($db_array)) {
      // empty db_array
      return array($inserted, $duplicate);
    }

    // fix values
    $gegenkonto = '';
    $stamp = time();

    $userName = $app->User->GetName();
    $userName = mysqli_real_escape_string($app->DB->connection, $userName);

    // skip first row -> 'header' line
    $count = count($db_array);
    for ($i = 1; $i < $count; $i++) {
      // explode row
      $row = $db_array[$i];
      $row = str_replace('"','', $row);
      $row = explode(self::COL_DIVIDER, $row);

      // $csv="date;description;amount;currency\r\n";
      // 0 date
      // 1 description
      // 2 amount
      // 3 currency

      // extract the values
      list($buchung, $transaction, $vorgang, $projektID, $betrag, $waehrung,$gebuehr) = $row;

      $buchung = mysqli_real_escape_string($app->DB->connection, $buchung);
      $buchung = str_replace('"','', $buchung);

      $vorgang = utf8_encode($vorgang );
      $vorgang = mysqli_real_escape_string($app->DB->connection, $vorgang);
      $vorgang = str_replace('"','',$vorgang);

      $betrag = mysqli_real_escape_string($app->DB->connection, $betrag);
      $waehrung = mysqli_real_escape_string($app->DB->connection, $waehrung);
      $gebuehr = mysqli_real_escape_string($app->DB->connection, $gebuehr);
      // haben vs. soll
      list($haben, $soll) = $betrag > 0
        ? [$betrag, '']
        : ['', $betrag];

      // hash over some values
      $pruefsumme = md5(serialize([$buchung, $vorgang, $soll, $haben, $waehrung]));

      $check = $app->DB->Select("SELECT id FROM kontoauszuege WHERE buchung='$buchung' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1");
      if($check > 0) {
        $duplicate++;
        continue;
      }

      $sql = "INSERT INTO kontoauszuege (
          konto,
          buchung,
          vorgang,
          soll,
          haben,
          gebuehr,
          waehrung,
          fertig,
          bearbeiter,
          pruefsumme,
          importgroup,
          originalbuchung,
          originalvorgang,
          originalsoll,
          originalhaben,
          originalgebuehr,
          originalwaehrung,
          gegenkonto
        ) VALUE (
          '$konto',
          '$buchung',
          '$vorgang',
          '$soll',
          '$haben',
          '$gebuehr',
          '$waehrung',
          0,
          '".$userName."',
          '$pruefsumme',
          '$stamp',
          '$buchung',
          '$vorgang',
          '$soll',
          '$haben',
          '$gebuehr',
          '$waehrung',
          '$gegenkonto')";

      $app->DB->Insert($sql);
      $newid = $app->DB->GetInsertID();
      $app->DB->Update("UPDATE kontoauszuege SET sort='$newid' WHERE id='$newid' LIMIT 1");
      $inserted++;
    }

    return [$inserted, $duplicate];
  }

  /**
   * perform curl request
   * protected to override via child class, if necessary
   *
   * Sie müssen die korrekte URL aufrufen und dabei HTTPS als Protokoll verwenden.
   * Sie müssen die korrekten Authentifizierungsinformationen übermitteln. Zur Authentifizierung
   * wird die Basic-HTTP-Authentication (RFC 2617) verwendet.
   *
   * Sie müssen die korrekten Content-Type Header angeben.
   * Ihre Daten müssen korrekt als XML formatiert sein (RFC 3023, siehe Parameterübersicht) und per HTTP POST
   * verschickt werden.
   *
   * @param string $xml
   * @return string
   * @throws Exception
   */
  protected function curl($xml)
  {

    $header = [
      'Content-Type' => self::CONTENT_TYPE,
      'Accept:' => self::CONTENT_TYPE,
    ];

    $url = self::URL;


    $ch = curl_init($url);
    curl_setopt_array($ch, array(
      CURLOPT_POST => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_SSL_VERIFYPEER => true,
      CURLOPT_SSLVERSION => 6,
      CURLOPT_HTTPAUTH => CURLAUTH_BASIC,
      CURLOPT_USERPWD => $this->getAuthString(),
      CURLOPT_POSTFIELDS => $xml,
      CURLOPT_HTTPHEADER => $header
    ));

    $result = curl_exec($ch);
    $info = curl_getinfo($ch);
    curl_close($ch);

    if (isset($info['http_code']) && $info['http_code'] !== 200) {
      $code = $info['http_code'];
      throw new RuntimeException(sprintf(
          'Verbindung fehlgeschlagen - konnte \'%s\' nicht erreichen (Statuscode: \'%s\')', $url, $code
      ));
    }
    return $result;
  }

  /**
   * perform the imports
   *
   * @throws Exception
   * @throws RuntimeException
   *
   * @return array
   */
  private function importLoop()
  {
    $page   = 1;
    $number = 20;
    $end    = $this->getDate();
    $start  = $this->getDate($this->days);
    $csv    = array('date'.self::COL_DIVIDER.'description'.self::COL_DIVIDER.'amount'.self::COL_DIVIDER.'currency');

    do {
      /**
       * - product:
       *    Produkt, auf das die ausgegebenen Transaktionen eingeschränkt werden soll
       *    ("paycode": SOFORT Überweisung Paycode, "payment": SOFORT Überweisung)
       * - status:
       *    Status, auf den die ausgegebenen Transaktionen eingeschränkt werden sollen
       *
       *    status,   reason,           meaning
       *    loss 	    not_credited 	    Das Geld ist nicht eingegangen.
       *    pending 	not_credited_yet 	Das Geld ist noch nicht eingegangen.
       *    received 	credited 	        Das Geld ist eingegangen.
       *    refunded 	compensation 	    Das Geld wurde zurückerstattet (Teilrückbuchung).
       *    refunded 	refunded 	        Das Geld wurde zurückerstattet (komplette Rückbuchung des Gesamtbetrags).
       *
       */
      $request = [
        'from_time' => $start,
        'to_time' => $end,
        'number' => $number,
        'page' => $page,
        'product' => 'payment',
        //'status' => 'received',
      ];

      $xml = $this->generateXML('transaction_request version="2"', $request);
      $response = $this->curl($xml);

      if (empty($response)) {
        break;
      }
      $xml = simplexml_load_string($response);
      $childs = $xml->count();

      $name = $xml->getName();
      if ($name !== 'transactions') {
        /*
         * no 'transactions' response -> may an error or a warning?!
         *
         * <?xml version="1.0" encoding="UTF-8" ?>
         * <errors>
         *  <error>
         *    <code>1000</code>
         *    <message>Invalid request.</message>
         *  </error>
         * </errors>
         */
        throw new ResponseException(sprintf(
            'Expected \'transactions\' response, got \'%s\'\n%s', $name, $response
        ));
      }

      if (isset($xml->transaction_details)) {
        // transaction_details found -> loop
        foreach ($xml->transaction_details as $transaction) {
          $csv[] = $this->extractData($transaction);
        }
      }

      // loop as long as the received childs
      // are the max. number of requested child
      // so step to the next page
      $page = $page + 1;
    } while($childs === $number);

    return $csv;
  }

  /**
   * extract some data from the given transaction
   * extract
   * - date
   * - amount
   * - currency
   * - description (transaction id, paycode, project id)
   *
   * @param SimpleXMLElement $xml
   * @return string
   */
  private function extractData(SimpleXMLElement $xml)
  {
    $transaction = (string) $xml->transaction;
    $reasons = '';
    foreach ($xml->reasons->reason as $index => $reason){
      $reasons .= ' '.$reason;
    }
    $reasons = trim($reasons);
    $date = (string) $xml->time;
    $date = substr($date, 0, 10);
    $amount = (float) $xml->amount;
    $currency = (string) $xml->currency_code;
    $paycode = (string) $xml->paycode->code;
    $project = (string) $xml->project_id;
    $costs = (float) $xml->costs->fees;

    /*
     * amount_refunded;	[0,1]; 	Decimal (8.2); 	Zurück überwiesener Betrag
     * [0,1] = optionaler Parameter, es kann max. ein Wert übergeben werden
     */
    if (isset($xml->amount_refunded)) {
      $amount_refunded = (float) $xml->amount_refunded;
      if ($amount_refunded != 0) {
        /*
         * Make sure, the refunded amount is negative and replace the amount.
         */
        $amount = -1 * abs($amount_refunded);
      }
    }

    $line = [
      $date,
      $transaction,
      $reasons,
      $paycode.' '.$project,
      $amount,
      $currency,
      $costs,
    ];

    $line = implode(self::COL_DIVIDER, $line);
    return $line;
  }

  /**
   * generate the xml request from given array & tag
   *
   * e.g.
   * $tag = 'transaction_request version="2"'
   * $request = array(
   *    'from_time' => '2013-04-01',
   *    'to_time' => '2013-04-30',
   *    'number' => 10,
   *    'page' => 2,
   *    'product' => 'paycode'
   * );
   *
   * converts to
   *
   * <?xml version="1.0" encoding="UTF-8" ?>
   * <transaction_request version="2">
   *    <from_time>2013-04-01</from_time>
   *    <to_time>2013-04-30</to_time>
   *    <product>paycode</product>
   *    <number>10</number>
   *    <page>2</page>
   * </transaction_request>
   *
   *
   * @param string $tag
   * @param array $request
   * @return string
   */
  private function generateXML($tag, array $request)
  {
    // remove surrounding '< -- >' from tag
    $tag = ltrim($tag, '<');
    $tag = rtrim($tag, '>');
    // explode the tag -> use first part as end tag (cut's version=2)
    // e.g. $tag = 'transaction_request version="2"'
    // converts to $end = transaction_request
    $end = explode(' ', $tag)[0];

    $xml = array();
    $xml[] = '<?xml version="1.0" encoding="UTF-8" ?>';
    $xml[] = "<{$tag}>";
    foreach ($request as $k => $v){ $xml[] = "\t<{$k}>{$v}</{$k}>"; }
    $xml[] = "</{$end}>";
    $xml = implode("\n", $xml);
    return $xml;
  }

  /**
   * get the necessary Date
   * if daysAgo is '0', return today's date
   * else the date, n days ago
   *
   * @param int $daysAgo note: the max value is 29
   *
   * @throws RuntimeException
   *
   * @return string
   */
  public function getDate($daysAgo = 0)
  {
    $daysAgo = (int)$daysAgo;
    $daysAgo = (int) abs($daysAgo);
    // allow max. 29 days
    $daysAgo = min(29, $daysAgo);

    if ($daysAgo === 0) {
        $time = time();
    } else {
        $tmp = sprintf('-%s days', $daysAgo);
        $time = strtotime($tmp);
    }

    // generate formatted date string from time
    $date = date(self::DATE_FORMAT, $time);
    if ($date === false) {
        throw new RuntimeException(sprintf(
            'Cannot create formatted date for %s day(s) ago.', $daysAgo
        ));
    }

    return $date;
  }

  /**
   * Zur Authentifizierung wird die Basic-HTTP-Authentication (RFC 2617) verwendet.
   * Als Benutzername verwenden Sie bitte Ihre Kundennummer (bspw. 99999) und als Passwort
   * Ihren API-Key (bspw. a12b34cd567890123e456f7890123456), die Sie durch ":" getrennt
   * aneinander fügen und mit Base64 codieren (base64(99999:a12b34cd567890123e456f7890123456)).
   *
   * @return string
   */
  private function getAuthString()
  {
    return $this->customerID.':'.$this->api;
  }
}

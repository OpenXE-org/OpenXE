<?php

/**
 * Immutable object about one request
 * to the Klarna api
 *
 * Class Response
 */
class KlarnaResponse
{
    /**
     * the api response as string just as 'fallback' for
     * logging or debugging purposes if json_decode fails
     *
     * @var string
     */
    private $resultString = '';

    /**
     * the api response as array
     *
     * @var array
     */
    private $result = [];

    /**
     * all http headers from curl
     *
     * @var array
     */
    private $header = [];

    /**
     * curl error message, if available
     *
     * @var string
     */
    private $curlError = '';

    /**
     * curl error number, if available
     *
     * @var int
     */
    private $curlErrno = 0;

    /**
     * internal error number
     *
     * @var int
     */
    private $errno = 0;

    /**
     * internal error message
     *
     * @var string
     */
    private $error = '';

    /**
     * Result constructor.
     *
     * @param string $result
     * @param array $header
     * @param string $curlError
     * @param int $curlErrno
     */
    public function __construct($result, $header = array(), $curlError = '', $curlErrno = 0)
    {
        $this->header = $header;
        $this->curlError = $curlError;
        $this->curlErrno = $curlErrno;
        $this->resultString = $result;
        $this->result = $this->decode($result);
    }

    /**
     * Dump the result data.
     *
     * Just for development
     *
     * @param bool $info
     * @internal
     */
    public function dump($info)
    {
        $cli = PHP_SAPI === 'cli';
        if (!$cli) {
            echo '<pre>';
            echo PHP_EOL;
        }
        var_dump($this->result);
        echo PHP_EOL;
        if ($info) {
            var_dump($this->header);
            echo PHP_EOL;
        } else {
            var_dump('Status: ' . $this->status());
        }
        if (!$cli) {
            echo '</pre>';
        }
    }

    /**
     * @return string
     */
    public function getResultString()
    {
        return $this->resultString;
    }

    /**
     * @param string|int $key
     *
     * @return bool
     */
    public function hasResult($key)
    {
        return array_key_exists($key, $this->result);
    }

    /**
     * just the short form for 'hasResult'
     *
     * @param null $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->hasResult($key);
    }

    /**
     * get the result or the fallback if not available
     * without parameter, return the full result array
     *
     * @param string|int|null $key
     * @param bool $fallback
     *
     * @return array|bool|mixed
     */
    public function getResult($key = null, $fallback = false)
    {
        if ($key === null) {
            return $this->result;
        }
        return array_key_exists($key, $this->result)
            ? $this->result[$key]
            : $fallback;
    }

    /**
     * just the short form for 'getResult'
     *
     * @param string|int|null $key
     * @param bool $fallback
     *
     * @return array|bool|mixed
     */
    public function get($key = null, $fallback = false)
    {
        return $this->getResult($key, $fallback);
    }

    /**
     * Get all or one header.
     *
     * @param null $key
     * @param bool $fallback
     *
     * @return array|mixed
     */
    public function getHeader($key = null, $fallback = false)
    {
        if ($key === null) {
            return $this->header;
        }
        return array_key_exists($key, $this->header)
            ? $this->header[$key]
            : $fallback;
    }

    /**
     * get the status code:
     *
     * @return int
     */
    public function getStatusCode()
    {
        return (int)$this->getHeader('http_code', 0);
    }

    /**
     * short version from 'getStatusCode'
     *
     * @return int
     */
    public function status()
    {
        return $this->getStatusCode();
    }

    /**
     * Short hand for getURL
     *
     * @return string
     */
    public function url()
    {
        return $this->getURL();
    }

    /**
     * Return the used url.
     *
     * @return string
     */
    public function getURL()
    {
        return $this->getHeader('url', '');
    }

    /**
     * @return bool
     */
    public function hasCurlError()
    {
        return $this->curlErrno !== 0;
    }

    /**
     * @return string
     */
    public function getCurlError()
    {
        return $this->curlError;
    }

    /**
     * @return string
     */
    public function curlError()
    {
        return $this->curlError;
    }

    /**
     * @return int
     */
    public function getCurlErrno()
    {
        return $this->curlErrno;
    }

    /**
     * @return int
     */
    public function curlErrno()
    {
        return $this->curlErrno;
    }

    /**
     * @return int
     */
    public function getErrno()
    {
        return $this->errno;
    }

    /**
     * @return int
     */
    public function errno()
    {
        return $this->errno;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return string
     */
    public function error()
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function hasError()
    {
        return $this->errno !== 0;
    }

    /**
     * decode the api response via json_decode
     * sets errno & error if necessary
     *
     * @param string $result
     *
     * @return array
     */
    private function decode($result)
    {
        if (!$result) {
            return [];
        }
        // todo: check response type header
        $result = json_decode($result, true);
        $errno = json_last_error();
        if ($errno) {
            $this->errno = $errno;
            $this->error = json_last_error_msg();
            return array();
        }
        return (array)$result;
    }
}

/**
 * Create the KlarnaResponse objects.
 */
class KlarnaRequest
{
    /**
     * The request header stay here as Key -> value pairs.
     *
     * @var array
     */
    private $headers = [];

    protected $fixHeaders = [
        'accept' => 'application/json'
    ];

    private $auth = '';

    protected $apiBase = 'https://api.klarna.com/';

    /**
     * Set the basic auth fields.
     *
     * @param $username
     * @param $password
     */
    public function basicAuth($username, $password)
    {
        $type = 'Basic';
        $token = base64_encode("{$username}:{$password}");

        $this->auth = "{$type} {$token}";
    }

    /**
     * @param $url
     *
     * @throws Exception
     *
     * @return KlarnaResponse
     */
    public function get($url)
    {
        return $this->curl([
            CURLOPT_URL => $url
        ]);
    }

    /**
     * @param $startDate
     * @param int $size
     *
     * @throws Exception
     *
     * @return KlarnaResponse
     */
    public function getPayOuts($startDate, $size = 50)
    {
        $size = max(20, abs($size));
        $url = '/settlements/v1/payouts';
        $startDate = date('Y-m-d', $startDate);
        $query = ['start_date' => $startDate, 'size' => $size];
        $url = $url . '?' . http_build_query($query);

        return $this->get($url);
    }

    /**
     * @param $url
     * @param int $size
     *
     * @throws Exception
     *
     * @return KlarnaResponse
     */
    public function getTransactionsByURL($url, $size = 50)
    {
        if (!strpos($url, 'size=')) {
            // note: $size argument may be ignored if already set in url
            $size = max(20, abs($size));
            $url = $url . '&size=' . $size;
        }

        return $this->get($url);
    }

    /**
     * @param $orderID
     *
     * @throws Exception
     *
     * @return KlarnaResponse
     */
    public function getOrder($orderID)
    {
        $url = "/ordermanagement/v1/orders/{$orderID}";
        return $this->get($url);
    }

    /**
     * @param $orders
     *
     * @throws Exception
     *
     * @return array
     */
    public function getOrders($orders)
    {
        $baseURL = $this->createURL('/ordermanagement/v1/orders/');

        $config = $this->createOptions([
            // url is empty as we'll replace it later for each order
            CURLOPT_URL => ''
        ]);
        $index = null;
        $result = [];
        $multiCurl = [];

        $mh = curl_multi_init();
        foreach ($orders as $i => $order) {
            $config[CURLOPT_URL] = $baseURL . $order;
            $multiCurl[$i] = curl_init();
            curl_setopt_array($multiCurl[$i], $config);
            curl_multi_add_handle($mh, $multiCurl[$i]);
        }
        do {
            curl_multi_exec($mh, $index);
        } while ($index > 0);
        // get content and remove handles
        foreach ($multiCurl as $k => $ch) {
            $tmp = curl_multi_getcontent($ch);
            $info = curl_getinfo($ch);
            $errno = curl_errno($ch);
            $error = curl_error($ch);

            $tmp = new KlarnaResponse($tmp, $info, $errno, $error);
            $result[$k] = $tmp;
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        // close
        curl_multi_close($mh);

        return $result;
    }

    /**
     * Run the http request.
     *
     * Requires at least the 'CURLOPT_URL' option set to the api path.
     * The API base is not required.
     *
     * If the body is set (CURLOPT_POSTFIELDS) it's required as string
     * or array. If it's an array, it will be encoded via json_encode.
     *
     * @param array $options
     *
     * @throws Exception
     *
     * @return KlarnaResponse
     */
    protected function curl($options = [])
    {
        $options = $this->createOptions($options);
        $ch = $this->init($options);
        return $this->exec($ch);
    }

    /**
     * @param array $options
     *
     * @throws Exception
     *
     * @return array
     */
    private function createOptions($options = [])
    {
        /*
         * Extract the url from the given options.
         */
        if (!array_key_exists(CURLOPT_URL, $options)) {
            throw new RuntimeException('No URL given.');
        }
        $url = $this->createURL($options[CURLOPT_URL]);
        unset($options[CURLOPT_URL]);

        /*
         * If a body is set:
         * -> json_encode the array
         * -> append the content length.
         * -> set method to POST if no custom post is defined.
         */
        if (array_key_exists(CURLOPT_POSTFIELDS, $options)) {
            /*
             * 1. encode
             */
            $data = $options[CURLOPT_POSTFIELDS];
            if (is_array($data)) {
                $data = json_encode($data);
                if (json_last_error()) {
                    throw new RuntimeException(json_last_error_msg());
                }
                $options[CURLOPT_POSTFIELDS] = $data;
                $options[CURLOPT_HTTPHEADER]['Content-Type'] = 'application/json';
            }
            if (!is_string($data)) {
                $type = gettype($data);
                throw new RuntimeException('Body is required as string, got ' . $type);
            }

            /*
             * 2. Set content length
             */
            $options[CURLOPT_HTTPHEADER]['Content-Length'] = strlen($data);

            /*
             * 3. Set request type
             */
            if (!array_key_exists(CURLOPT_CUSTOMREQUEST, $options)) {
                $options[CURLOPT_POST] = true;
            }
        }

        /*
         * Extract the headers from given options.
         */
        $headers = $this->headers;
        if (array_key_exists(CURLOPT_HTTPHEADER, $options)) {
            $add = $options[CURLOPT_HTTPHEADER];
            unset($options[CURLOPT_HTTPHEADER]);
            $headers = $headers + (array)$add;
            unset($add);
        }
        $headers = array_merge($headers, $this->fixHeaders);
        if ($this->auth) {
            $headers['Authorization'] = $this->auth;
        }
        $headers = $this->mergeHeader($headers);

        $default = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_TIMEOUT => 25,
            CURLOPT_CONNECTTIMEOUT => 15,
        );
        $final = array(
            CURLOPT_URL => $url,
            CURLOPT_HTTPHEADER => $headers
        );

        $ret = $default;
        foreach ($options as $i => $value) {
            /*
             * Array merge does not work here
             * because the arrays have numeric indexes
             * so the later array is appended instead
             * of overriding the previous value.
             */
            $ret[$i] = $value;
        }
        foreach ($final as $i => $value) {
            /*
             * See comment above!
             */
            $ret[$i] = $value;
        }

        return $ret;
    }

    private function init($option)
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('Curl is not available');
        }
        $ch = curl_init();
        if (!$ch) {
            throw new RuntimeException('Cannot initialize curl');
        }

        curl_setopt_array($ch, $option);

        return $ch;
    }

    private function exec($ch)
    {
        $result = curl_exec($ch);
        $info = curl_getinfo($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($errno || $error) {
            $msg = "Curl ({$errno}): {$error}";
            throw new RuntimeException($msg);
        }

        return new KlarnaResponse($result, $info);
    }

    public function createURL($url)
    {
        if ($this->startsWith($url, $this->apiBase)) {
            return $url;
        }

        $url = ltrim($url, '/');
        $base = rtrim($this->apiBase);
        $url = $base . '/' . $url;

        return $url;
    }

    private function startsWith($haystack, $needle)
    {
        $length = strlen($needle);

        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * Combine array keys with their value using $glue between
     * key & value. Used in 'curl' method to create the auth
     * header fields
     *
     * @param array $opt
     * @param string $glue
     *
     * @return array
     */
    private function mergeHeader($opt, $glue = ': ')
    {
        $tmp = [];
        $opt = (array)$opt;
        $glue = (string)$glue;

        foreach ($opt as $key => $value) {
            $tmp[] = "{$key}{$glue}{$value}";
        }
        return $tmp;
    }
}

/**
 * Class AbstractLiveImportKlarna
 *
 * This abstract class holds some of the often used
 * methods in the liveimport modules to avoid unnecessary
 * copy and paste.
 */
abstract class AbstractLiveImportKlarna
{
    const COL_DIVIDER = ';';
    const ROW_DIVIDER = "\r\n";
    const DATE_FORMAT = 'Y-m-d';

    /**
     * Extract the data to import.
     *
     * @param array $data
     * @param array $header
     *
     * @return array [$betrag, $vorgang, $buchung, $waehrung
     */
    abstract protected function extractDataForBankStatements($data, $header);

    abstract public function Import($config);

    /**
     * @param string $csv the csv 'file' to import
     * @param int $konto the konto id
     * @param $app
     *
     * @throws Exception
     *
     * @return array [$inserted, $duplicate]
     */
    public function ImportKontoauszug($csv, $konto, $app)
    {
        /*
         * The original is copied from 'ImportKontoauszug' in 'class.erpapi' (case "stripe" in switch statement)
         * This method was split up into two parts:
         * - convert the csv into an array of [ 0 => [$betrag, $vorgang, $buchung, $waehrung], 1 => .. ]
         * - read the csv and import it into the db.
         */
        $data = $this->extractDataFromCSV($csv);
        return $this->ImportCSV($data, $konto, $app);
    }

    /**
     * Convert the CSV string into an array with
     * [
     *  [$betrag, $vorgang, $buchung, $waehrung],
     *  [$betrag, $vorgang, $buchung, $waehrung],
     *  ...
     * ]
     * @param $csv
     *
     * @throws Exception
     *
     * @return array
     */
    public function extractDataFromCSV($csv)
    {
        if (!is_string($csv)) {
            $type = gettype($csv);
            throw new RuntimeException(sprintf(
                'Expected csv as string, got \'%s\'', $type
            ));
        }

        $csv = $this->explodeCSVLines($csv);
        if (empty($csv)) {
            return [];
        }
        $count = count($csv);

        if ($count < 2) {
            /*
             * Empty CSV or only header line -> nothing to import.
             */
            return [];
        }

        $csv = array_map([$this, 'explodeCSVLine'], $csv);

        $header = $csv[0];

        $data = [];

        /*
         * skip first row -> 'header' line
         */
        for ($i = 1; $i < $count; $i++) {

            $tmp = $csv[$i];

            $tmp = $this->extractDataForBankStatements($tmp, $header);
            // list($betrag, $vorgang, $buchung, $waehrung) = $data;
            $tmp = array_map('utf8_encode', $tmp);

            $data[] = $tmp;
            unset($tmp, $csv[$i]);
        }

        return $data;
    }

    /**
     * Get one array value by it's key.
     * Multiple keys can be defined, the fist match is used.
     *
     * @param array $config
     * @param array|string $keys
     *
     * @throws Exception
     *
     * @return string
     */
    protected function getConfig($config, $keys)
    {
        $keys = (array)$keys;
        foreach ($keys as $key) {
            if (!is_string($key) && !is_int($key)) {
                continue;
            }
            if (array_key_exists($key, $config) && is_string($config[$key]) && !empty($config[$key])) {
                return $config[$key];
            }
        }
        throw new RuntimeException(sprintf(
            'No %s given.', $keys[0]
        ));
    }

    /**
     * Loop through the CSV import array from extractCSVImport
     * and import each line to xentral.
     *
     * @param $csv
     * @param $konto
     * @param $app
     *
     * @return array [$inserted, $duplicate]
     */
    protected function ImportCSV($csv, $konto, $app)
    {
        $gebuehr = 0;
        $inserted = 0;
        $duplicate = 0;
        $stamp = time();
        $gegenkonto = '';

        if (!is_array($csv) || empty($csv)) {
            return [$inserted, $duplicate];
        }

        $userName = $app->User->GetName();
        $userName = mysqli_real_escape_string($app->DB->connection, $userName);

        foreach ($csv as list($betrag, $vorgang, $buchung, $waehrung)) {
//      list($betrag, $vorgang, $buchung, $waehrung) = $data;
//      unset($data);

            $buchung = mysqli_real_escape_string($app->DB->connection, $buchung);
            $vorgang = mysqli_real_escape_string($app->DB->connection, $vorgang);
            $betrag = mysqli_real_escape_string($app->DB->connection, $betrag);
            $waehrung = mysqli_real_escape_string($app->DB->connection, $waehrung);

            $vorgang = str_replace('"', '', $vorgang);
            $buchung = str_replace('"', '', $buchung);
            $buchung = explode(' ', $buchung);
            $buchung = $buchung[0];

            // haben vs. soll
            list($haben, $soll) = $betrag > 0
                ? array($betrag, '')
                : array('', $betrag);

            $soll = str_replace('-','',$soll);
            $haben = str_replace('-','',$haben);
      
            // hash over some values
            $pruefsumme = md5(serialize(array($buchung, $vorgang, $soll, $haben, $waehrung)));

            $sql = "SELECT id FROM kontoauszuege WHERE buchung='$buchung' AND konto='$konto' AND pruefsumme='$pruefsumme' LIMIT 1";
            $check = $app->DB->Select($sql);
            if ($check > 0) {
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
          '" . $userName . "',
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
     * get the number of days as positive integer,
     * they can be set via the config keys
     * 'API_DAYS', 'DAYS' or 'TAGE'
     *
     * @param array $config
     * @param int $default
     * @param array $keys
     *
     * @return int
     */
    protected function getFirstDate($config, $default, $keys = [])
    {
        if (!is_array($keys) || !$keys) {
            $keys = ['API_DAYS', 'DAYS', 'DAY', 'TAGE'];
        }
        $days = abs($default);
        foreach ($keys as $key) {
            if (array_key_exists($key, $config) && is_numeric($config[$key])) {
                $days = $config[$key];
                break;
            }
        }
        $days = max(1, $days);

        $time = time();
        $time = strtotime("-{$days} days", $time);

        // var_dump($time, date('Y-m-d', $time), $days);

        return $time;
    }

    protected function implodeCSVLine($line)
    {
        return implode(self::COL_DIVIDER, $line);
    }

    protected function explodeCSVLine($line)
    {
        $line = str_replace('"', '', $line);
        $line = explode(self::COL_DIVIDER, $line);

        return $line;
    }

    /**
     * Implode the given csv lines.
     *
     * @param $lines
     * @param array|string $header
     *
     * @return string
     */
    protected function implodeCSVLines($lines, $header = [])
    {
        $lines = array_map([$this, 'implodeCSVLine'], $lines);

        if ($header) {
            if (is_array($header)) {
                $header = implode(self::COL_DIVIDER, $header);
            }
            if (is_string($header)) {
                $lines = array_merge([$header], $lines);
            }
        }

        $lines = implode("\n", $lines);

        return $lines;
    }

    protected function explodeCSVLines($lines)
    {
        if (is_array($lines)) {
            $lines = implode("\n", $lines);
        }

        $lines = preg_split("/(\r\n)+|(\n|\r)+/", $lines);

        return $lines;
    }

    protected function extractDataOfInterest($transaction, $keys)
    {
        $keys = array_flip($keys);
        $tmp = array_intersect_key($transaction, $keys);
        ksort($tmp);

        return $tmp;
    }
}

class klarna extends AbstractLiveImportKlarna
{
    const DEFAULT_N_DAYS = 5;

    /**
     * @var KlarnaRequest
     */
    private $request = null;

    /**
     * The required transaction keys:
     *
     * @var array
     */
    private $required = [
        'amount',
        'sale_date',
        'payout_date',
        'currency_code',
        'type',
        'capture_id',
        'capture_date',
        'order_id',
        'purchase_country'
    ];


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
   * @param int[]       $paymentTransactionIds
   * @param int         $paymentAccountId
   * @param Application $app
   *
   * @return int
   */
    public function createReturnOrdersPaymentEntries($paymentTransactionIds, $paymentAccountId, $app)
    {
      if(empty($paymentTransactionIds)) {
        return 0;
      }
      $ret = 0;
      foreach($paymentTransactionIds as $paymentTransactionId) {
        if($this->createReturnOrderPaymentEntry($paymentTransactionId, $paymentAccountId, $app)) {
          $ret++;
        }
      }
      return $ret;
    }

    /**
     * @param int         $paymentTransactionId
     * @param int         $paymentAccountId
     * @param Application $app
     *
     * @return bool
     */
    public function createReturnOrderPaymentEntry($paymentTransactionId, $paymentAccountId, $app)
    {
      if($paymentTransactionId <= 0) {
        return false;
      }
      $paymentAccount = $app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `payment_transaction` WHERE `id` = %d',
          $paymentTransactionId
        )
      );
      if(empty($paymentAccount) || empty($paymentAccount['returnorder_id'])) {
        return false;
      }
      if(in_array($paymentAccount['payment_status'], ['payed','verbucht','abgeschlossen'])) {
        return false;
      }
      $returnOrderId = $paymentAccount['returnorder_id'];
      $orders = $app->DB->SelectRow(
        sprintf(
          "SELECT o.id, 
              IF(o.transaktionsnummer = '', o.internet, o.transaktionsnummer) AS transaktionsnummer, 
              ro.soll, ro.waehrung
          FROM `auftrag` AS `o`
          INNER JOIN `rechnung` AS `i` ON o.id = i.auftragid
          INNER JOIN `gutschrift` AS `ro` ON i.id = ro.rechnungid
          WHERE ro.id = %d AND (o.transaktionsnummer <> '' OR o.internet <> '') 
            AND i.belegnr <> '' AND o.status <> 'storniert'
          ORDER BY o.transaktionsnummer <> '' DESC
          LIMIT 1",
          $returnOrderId
        )
      );
      if(empty($orders)) {
        return false;
      }
      $transaktionsnummer = $orders['transaktionsnummer'];
      $amount = $orders['soll'];
      $currency = empty($orders['waehrung'])?'EUR':$orders['waehrung'];
      $json = ['transaktionsnummer' => $transaktionsnummer];

      $json = json_encode($json);
      $app->DB->Update(
        sprintf(
          "UPDATE `payment_transaction` 
           SET `payment_account_id` = %d, 
               `payment_status` = '%s',
               `payment_reason` = '%s',
               `payment_json` = '%s',
               `amount` = %f,
               `currency` = '%s'
           WHERE `id` = %d",
          $paymentAccountId,
          'angelegt',
          $app->DB->real_escape_string($transaktionsnummer),
          $app->DB->real_escape_string($json),
          $amount,
          $app->DB->real_escape_string($currency),
          $paymentTransactionId
        )
      );

      return $app->DB->affected_rows() > 0;
    }

  /**
   * @return  array
   */
    public function showReturnOrderStructure()
    {
      return [
        'legend1' => [
          'typ' => 'legend',
          'bezeichnung' => 'Zahlungsempf&auml;nger'
        ],
        'transaktionsnummer' =>
          [
            'bezeichnung' => 'Transaktionsnummer'
          ],
      ];
    }

  /**
   * @param int         $returnOrderId
   * @param int         $paymentAccountId
   * @param Application $app
   *
   * @return bool
   */
    public function executeReturnOrder($paymentTransactionId, $paymentAccountId, $app)
    {
      if($paymentTransactionId <= 0) {
        return false;
      }
      $paymentAccount = $app->DB->SelectRow(
        sprintf(
          'SELECT * FROM `payment_transaction` WHERE `id` = %d AND `payment_account_id` = %d',
          $paymentTransactionId, $paymentAccountId
        )
      );
      if(empty($paymentAccount) || empty($paymentAccount['returnorder_id'])) {
        return false;
      }
      if(in_array($paymentAccount['payment_status'],
        ['payed','verbucht','abgeschlossen','failed','fehlgeschlagen']
      )) {
        return false;
      }

      $ok = false;
      //@todo senden


      if($ok) {
        $app->DB->Update(
          sprintf(
            "UPDATE `payment_transaction` SET `payment_status` = 'verbucht' WHERE `id` = %d",
            $paymentAccountId
          )
        );
        return true;
      }

      $app->DB->Update(
        sprintf(
          "UPDATE `payment_transaction` SET `payment_status` = 'error' WHERE `id` = %d",
          $paymentAccountId
        )
      );

      return false;
    }

    /**
     * @param $config
     *
     * @throws Exception
     *
     * @return string
     */
    public function Import($config)
    {
        $config = (array)$config;
        $config = array_change_key_case($config, CASE_UPPER);

        try {
            $username = $this->getConfig($config, ['API_USER', 'USER_NAME', 'USERNAME', 'USER']);
            $password = $this->getConfig($config, ['API_PASSWORD', 'PASSWORD', 'PW', 'API_KEY', 'KEY']);
            $firstDate = $this->getFirstDate($config, self::DEFAULT_N_DAYS);

            $this->request = new KlarnaRequest();
            $this->request->basicAuth($username, $password);
            unset($username, $password);

            /*
             * Note:
             * Here we'll search through all payments received the last n days.
             * For each found payment, the transactions are extracted and imported.
             *
             * Another way is to loop through the transactions via offset. The pagination
             * response contains the total number of transactions. But I've got no transactions
             * for the last ~25 days.
             */
            $transactions = [];
            $transactionsCollection = $this->getTransactionUrlsByPayment($firstDate);
            foreach ($transactionsCollection as $collection) {
                $url = $collection['url'];
                $date = $collection['date'];
                $tmp = $this->getTransactionsByURL($url, $date);

                $transactions = array_merge($transactions, $tmp);
            }

            $orderIDs = [];
            foreach ($transactions as $index => $value) {
                /*
                 * Note: array_column is not working here, because
                 * this method resets the numeric indexes.
                 */
                $orderIDs[$index] = $value['order_id'];
            }

            $offset = 0;
            $length = 10;
            // $transactions = array_slice($transactions, 0, 3, true);

            $transactionsCount = count($transactions);
            do {
                /*
                 * Note: preserve_keys is set to true!
                 */
                $current = array_slice($orderIDs, $offset, $length, true);

                // echo "Offset: {$offset}; length: {$length} (" . implode(' ', $orderIDs) . ')' . PHP_EOL;

                $orders = $this->request->getOrders($current);
                foreach ($orders as $j => $order) {
                    $orderID = $transactions[$j]['order_id'];

                    if (!strpos($order->url(), $orderID)) {
                        /*
                         * Just a check if the expected order is part of the used url.
                         * So we make sure, there's nothing wrong with the numeric indexes
                         * and $orders[$n] belongs to $transactions[$n].
                         */
                        throw new RuntimeException('Wrong order merged!');
                    }

                    /*
                     * Here we replace the old order id with
                     * the enriched data. It's important to keep
                     * the array key because the csv depends on
                     * this key.
                     */
                    $address = $order->get('billing_address');
                    $tmp = $this->formatAddressData($address, $orderID);
                    $transactions[$j]['order_id'] = $tmp;
                }

                $offset += $length;
            } while ($offset <= $transactionsCount);

            $header = $this->required;
            sort($header);

            // var_dump(count($transactions) . ' lines');

            return $this->implodeCSVLines($transactions, $header);
        } catch (Exception $e) {
//      var_dump($e->getMessage());
//      var_dump($e->getLine());
//      var_dump($e->getFile());
            throw $e;
        }
    }

    /**
     * Returns the necessary data required to build the sql statements
     * in the ImportKontoauszug() method.
     *
     * @param array $data the data set
     * @param array $header the csv header line
     *
     * @return array return [$betrag, $vorgang, $buchung, $waehrung];
     */
    protected function extractDataForBankStatements($data, $header)
    {
        /*
         * combine header with data to access
         * the values with named indexes
         */
        $data = array_combine($header, $data);

        /*
         * translate / extract
         */
        $betrag = $data['amount'];
        $vorgang = implode(' ', [
            $data['order_id'],
            $data['purchase_country'],
            $data['type'],
            $data['sale_date'],
        ]);
        // $buchung = $data['sale_date'];
        $buchung = $data['payout_date'];
        $waehrung = $data['currency_code'];

        return [$betrag, $vorgang, $buchung, $waehrung];
    }

    /**
     * Get all transactions since $startDate
     *
     * return array like
     * 0 => [
     *  'url' => 'https://api.klarna.com/settlements/v1/transactions?payment_reference=******************',
     *  'date' => '2019-06-05'
     * ],
     *
     * @param $startDate
     * @param int $size positive number, minimum 20, get n payments per request.
     *
     * @throws Exception
     *
     * @return array
     */
    private function getTransactionUrlsByPayment($startDate, $size = 50)
    {
        $ret = [];

        do {
            $response = $this->request->getPayOuts($startDate, $size);
            $payouts = $response->get('payouts', []);

            foreach ($payouts as $payout) {
                /*
                 * if no amount is present, ignore that payout:
                 */
                $total = $payout['totals'];
                $total = array_map('abs', $total);
                $total = array_sum($total);
                if ($total === 0) {
                    continue;
                }

                /*
                 * store only the transactions url
                 */
                $transaction = $payout['transactions'];
                $date = $payout['payout_date'];
                $date = substr($date, 0, 10);

                $ret[] = [
                    'url' => $transaction,
                    'date' => $date
                ];
            }

            $pagination = $response->get('pagination', []);
            /*
             * just ot make sure, the next element is set
             */
            $pagination = array_merge(['next' => ''], $pagination);
            $url = $pagination['next'];
        } while (!empty($url));

        // $urls = array_unique($urls);

        return $ret;
    }

    /**
     * Get transactions via url.
     *
     * @param $url
     * @param $payOutDate
     * @param int $size
     *
     * @throws Exception
     *
     * @return array
     */
    private function getTransactionsByURL($url, $payOutDate, $size = 50)
    {
        $transactions = [];

        while (!empty($url)) {

            $response = $this->request->getTransactionsByURL($url, $size);

            $tmp = $response->get('transactions', []);
            $tmp = array_filter($tmp, [$this, 'filterTransactions']);
            // enrich the transaction data with their payout date.
            foreach ($tmp as $i => $iValue) {
                $tmp[$i]['payout_date'] = $payOutDate;
            }
            $tmp = array_map([$this, 'extractRequiredData'], $tmp);
            $tmp = array_map([$this, 'convertDataOfInterest'], $tmp);
            $transactions = array_merge($transactions, $tmp);

            $pagination = $response->get('pagination', []);
            /*
             * just to make sure, the next element is set
             */
            $pagination = array_merge(['next' => ''], $pagination);
            $url = $pagination['next'];
        }

        return $transactions;
    }

    private function formatAddressData($address, $orderID)
    {
        $ret = implode(' ', array_filter([
            $address['title'] . '.',
            $address['family_name'],
            $address['given_name'],
            '-',
            $address['street_address'],
            $address['postal_code'],
            $address['city'],
            //      $address['email'],
            //      $address['phone'],
            // We'll keep the order id as unique id
            '-',
            $orderID
        ]));

        $ret = utf8_decode($ret);

//    $replace = [
//      'ä' => 'ae',
//      'Ä' => 'Ae',
//      'ö' => 'oe',
//      'Ö' => 'Oe',
//      'ü' => 'ue',
//      'Ü' => 'Ue',
//      'ß' => 'ss'
//    ];
//
//    $ret = strtr($ret, $replace);

        return $ret;
    }

    private function filterTransactions($transaction)
    {
        $type = strtoupper($transaction['type']);
        return in_array($type, ['SALE', 'RETURN']);
    }

    private function extractRequiredData($transaction)
    {
        return $this->extractDataOfInterest($transaction, $this->required);
    }

    private function convertDataOfInterest($transaction)
    {
        $amount = $transaction['amount'] / 100;
        $negate = ['FEE', 'RETURN', 'REVERSAL'];
        if ($amount > 0 && in_array($transaction['type'], $negate, true)) {
            $amount = -1 * $amount;
        }
        $transaction['amount'] = $amount;

        $keys = ['sale_date', 'capture_date'];
        foreach ($keys as $key) {
            $date = $transaction[$key];
            // convert 2019-02-06T23:00:00.000Z to 2019-02-06 to 06.02.2019
            $date = substr($date, 0, 10);
//    // convert 2019-02-06 to 06.02.2019
//    list ($year, $month, $day) = explode('-', $date);
//    $date = implode('.', [$day, $month, $year]);
            $transaction[$key] = $date;
        }

        return $transaction;
    }
}

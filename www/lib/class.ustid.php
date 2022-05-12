<?php

/**
 * Class USTID
 *
 * echo $this->check("DE263136143","SE556459933901","Wind River AB","Kista","Finlandsgatan 52","16493","ja");
 * echo $this->check("DE263136143","HU12925481","TGIF KFT.","Egerszaloki","3394","Szechenyiu","ja");
 * echo $this->check("DE263136143","SE556459933901","jkjk","Kista","Finlandsgatan 52","16493","ja");
 */
class USTID
{
  /**
   * online formular @https://evatr.bff-online.de/eVatR/index_html#Einfach_Ergebnis
   *
   * @var string API base
   * @var string API endpoint
   */
  const BASE = 'https://evatr.bff-online.de';
  const ENDPOINT = 'evatrRPC';

  /**
   * just for downward compatibility,
   * the classes 'Adresse' and the 'erpAPI'
   * use that public value to access the
   * response data
   *
   * @var array
   */
  var $answer = array(
    'UstId_1' => '',
    'UstId_2' => '',
    'ErrorCode' => '',
    'Druck' => '',
    'Erg_PLZ' => '',
    'Ort' => '',
    'Datum' => '',
    'PLZ' => '',
    'Erg_Ort' => '',
    'Uhrzeit' => '',
    'Erg_Name' => '',
    'Gueltig_ab' => '',
    'Gueltig_bis' => '',
    'Strasse' => '',
    'Firmenname' => '',
    'Erg_Str' => '',
    'ErrorMSG' => '',
    'OK' => '',
  );

  /**
   * the required GET-parameter,
   * leave blank if field is not necessary,
   * but don't remove it from the request.
   * @see: https://evatr.bff-online.de/eVatR/xmlrpc/schnittstelle
   *
   * @var array
   */
  private $_data = array(
    'UstId_1' => '',    // Ihre deutsche USt-IdNr.
    'UstId_2' => '',    // Anzufragende ausländische USt-IdNr.
    'Firmenname' => '', // Name der anzufragenden Firma einschl. Rechtsform
    'Ort' => '',        // Ort der anzufragenden Firma
    'PLZ' => '',        // Postleitzahl der anzufragenden Firma
    'Strasse' => '',    // Strasse und Hausnummer der anzufragenden Firma,
    'Druck' => 'nein'   // ja = mit amtlicher Bestätigungsmitteilung
  );

  /**
   * @see https://evatr.bff-online.de/eVatR/xmlrpc/codes
   * @var array $_errorCodes
   */
  private $_errorCodes = array(
    /*
     * my own
     * Kein gültiger Fehlercode vom Finanzamt-Server erhalten.
     */
    100 => 'Es wurde keine Antwort vom Finanzamt-Server erhalten. Nur in der Zeit zwischen 05:00 Uhr und 23:00 Uhr sind abfragen möglich',
    101 => 'Es wurde eine Fehlerhafte Antwort vom Finanzamt-Server erhalten',
    /*
     * API's status/error codes
     */
    200 => 'Die angefragte USt-IdNr. ist gültig.',
    201 => 'Die angefragte USt-IdNr. ist ungültig.',
    202 => 'Die angefragte USt-IdNr. ist ungültig. Sie ist nicht in der Unternehmerdatei des betreffenden EU-Mitgliedstaates registriert. (Hinweis: Ihr Geschäftspartner kann seine gültige USt-IdNr. bei der für ihn zuständigen Finanzbehörde in Erfahrung bringen. Möglicherweise muss er einen Antrag stellen, damit seine USt-IdNr. in die Datenbank aufgenommen wird.)',
    203 => 'Die angefragte USt-IdNr. ist ungültig. Sie ist erst ab dem {{Gueltig_ab}} gültig.',
    204 => 'Die angefragte USt-IdNr. ist ungültig. Sie war im Zeitraum von {{Gueltig_ab}} bis {{Gueltig_bis}} gültig.',
    205 => 'Ihre Anfrage kann derzeit durch den angefragten EU-Mitgliedstaat oder aus anderen Gründen nicht beantwortet werden.',
    206 => 'Ihre deutsche USt-IdNr. ist ungültig. Eine Bestätigungsanfrage ist daher nicht möglich.',
    207 => 'Ihnen wurde die deutsche USt-IdNr. ausschliesslich zu Zwecken der Besteuerung des innergemeinschaftlichen Erwerbs erteilt. Sie sind somit nicht berechtigt, Bestätigungsanfragen zu stellen.',
    208 => 'Für die von Ihnen angefragte USt-IdNr. läuft gerade eine Anfrage von einem anderen Nutzer. Eine Bearbeitung ist daher nicht möglich. Bitte versuchen Sie es später noch einmal.',
    209 => 'Die angefragte USt-IdNr. ist ungültig. Sie entspricht nicht dem Aufbau der für diesen EU-Mitgliedstaat gilt.',
    210 => 'Die angefragte USt-IdNr. ist ungültig. Sie entspricht nicht den Prüfziffernregeln die für diesen EU-Mitgliedstaat gelten.',
    211 => 'Die angefragte USt-IdNr. ist ungültig. Sie enthält unzulässige Zeichen (wie z.B. Leerzeichen oder Punkt oder Bindestrich usw.).',
    212 => 'Die angefragte USt-IdNr. ist ungültig. Sie enthält ein unzulässiges Länderkennzeichen.',
    213 => 'Die Abfrage einer deutschen USt-IdNr. ist nicht möglich.',
    214 => 'Ihre deutsche USt-IdNr. ist fehlerhaft. Sie beginnt mit \'DE\' gefolgt von 9 Ziffern.',
    215 => 'Ihre Anfrage enthält nicht alle notwendigen Angaben für eine einfache Bestätigungsanfrage (Ihre deutsche USt-IdNr. und die ausl. USt-IdNr.).',
    216 => 'Ihre Anfrage enthält nicht alle notwendigen Angaben für eine qualifizierte Bestätigungsanfrage. (Ihre deutsche USt-IdNr., die ausl. USt-IdNr., Firmenname einschl. Rechtsform und Ort).',
    217 => 'Bei der Verarbeitung der Daten aus dem angefragten EU-Mitgliedstaat ist ein Fehler aufgetreten.',
    218 => 'Eine qualifizierte Bestätigung ist zur Zeit nicht möglich.',
    219 => 'Bei der Durchführung der qualifizierten Bestätigungsanfrage ist ein Fehler aufgetreten. Die angefragte USt-IdNr. ist gültig.',
    220 => 'Bei der Anforderung der amtlichen Bestätigungsmitteilung ist ein Fehler aufgetreten. Sie werden kein Schreiben erhalten.',
    221 => 'Die Anfragedaten enthalten nicht alle notwendigen Parameter oder einen ungültigen Datentyp.',
    222 => 'Die angefragte USt-IdNr. ist gültig.',
    999 => 'Eine Bearbeitung Ihrer Anfrage ist zurzeit nicht möglich. Bitte versuchen Sie es später noch einmal.'
  );

  /**
   * the last error message
   * @var string
   */
  private $_error = '';

  /**
   * @param string $ust1 your UST ID
   * @param string $ust2 requested UST ID
   * @param string $firmenname
   * @param string $ort
   * @param string $strasse
   * @param string $plz
   * @param string $druck
   * @param $onlinefehler
   * @return int
   *
   * return values:
   * 1: ok,
   * -1: invalid/wrong response,
   * -2: curl error
   */
  function check($ust1, $ust2, $firmenname, $ort, $strasse, $plz, $druck = "nein", &$onlinefehler)
  {
    $this->set('UstId_1', $ust1);
    $this->set('UstId_2', $ust2);
    $this->set('Firmenname', $firmenname);
    $this->set('Ort', $ort);
    $this->set('Strasse', $strasse);
    $this->set('PLZ', $plz);
    $this->set('Druck', $druck);

    /*
     * we could use $response = $this->send();
     * send() returns only false or the response.
     * To detect the reason of an error, don't use it.
     */

    $url = $this->buildQueryURL();
    $response = $this->curl($url);

    if ($response == false) {
      /**
       * curl failed,
       *
       */
      $onlinefehler = $this->_error;
      return -2;
    }

    $response = $this->convertXML2Array($response);
    $this->answer = $response;
    $this->_error = $response['ErrorMSG'];
    $onlinefehler = $this->_error;

    return $response['OK'] == true ? 1 : -1;
  }

  /**
   * from erpAPI (class.erpapi.php) to simplify that class
   * from first version of USTID, still unused?
   */
  function checkAndSendMailIfWrong()
  {
    // Job: implement
    // this method was empty
  }

  /**
   * just for downward compatibility,
   * the class 'Adresse' use that method
   * to get the error code message
   *
   * @param $code
   * @return string
   */
  function errormessages($code)
  {
    return $this->generateErrorMsg($code);
  }

  /**
   * return the error code message
   * alias for errormessages
   *
   * @param $code
   * @return string
   */
  public function getErrorMsg($code)
  {
    return $this->generateErrorMsg($code);
  }

  /**
   * create the full url and send the request
   *
   * @param array $args
   * @return mixed
   */
  public function send($args = array())
  {
    if (is_array($args) && !empty($args)) {
      foreach ($args as $key => $value) {
        $this->set($key, $value);
      }
    }
    if (!empty($this->_error)) {
      /**
       * on error, don't send the request,
       * let the user change the settings
       */
      return false;
    }

    $url = $this->buildQueryURL();
    $response = $this->curl($url);
    if ($response == false) {
      /**
       * curl failed,
       *
       */
      return false;
    }
    $response = $this->convertXML2Array($response);
    $this->answer = $response;
    $this->_error = $response['ErrorMSG'];
    return true;
  }

  /**
   * just a little helper to reset the request
   */
  public function reset()
  {
    foreach ($this->_data as $key => $_) {
      $this->_data[$key] = '';
    }
  }

  /**
   * set an GET parameter,
   * only the keys from $_data array are allowed,
   * only string values are allowed
   *
   * @param string $key
   * @param string $value
   * @return bool
   */
  public function set($key, $value)
  {
    if (!is_string($value)) {
      $this->_error = 'Invalid value type given';
      return false;
    }
    if (!is_string($key)) {
      $this->_error = 'Invalid key type given';
      return false;
    }
    $keys = array_keys($this->_data);
    if (!in_array($key, $keys)) {
      $this->_error = "Key [{$key}] is not allowed";
      return false;
    }
    $this->_data[$key] = $value;
    return true;
  }

  /**
   * build the query url and reset the config
   *
   * @return string
   */
  private function buildQueryURL()
  {
    $args = $this->formatData($this->_data);
    $args = http_build_query($args);

    $url = self::BASE . '/' . self::ENDPOINT . '?' . $args;
    $this->reset();

    return $url;
  }

  /**
   * before curl, reformat the data.
   * validate the 'Druck' value,
   * remove blanks from the UstId
   *
   * @param array $data
   * @return array
   */
  private function formatData($data)
  {
    if (!$data['Druck'] == 'ja') {
      /*
       * the 'Druck' value was not set to 'ja',
       * so empty that value
       */
      $data['Druck'] = '';
    }
    foreach (array('UstId_1', 'UstId_2') as $key) {
      /*
       * unzulässige Zeichen (wie z.B. Leerzeichen oder Punkt oder Bindestrich usw.)
       */
      $value = $data[$key];
      $value = str_replace(" ", "", $value);
      $value = str_replace(".", "", $value);
      $value = str_replace("-", "", $value);
      $data[$key] = $value;
    }

    return $data;
  }

  /**
   * curl the given url
   * @param $url
   * @return mixed
   */
  private function curl($url)
  {
    $ch = curl_init($url);

    curl_setopt_array($ch, array(
      CURLOPT_SSL_VERIFYPEER => false,
      CURLOPT_SSLVERSION => 6,
      CURLOPT_POST => false,
      CURLOPT_RETURNTRANSFER => true,
    ));

    $result = curl_exec($ch);
    if (!$result) {
      $this->_error = 'curl error: ' . curl_error($ch);
      return false;
    }

    $info = curl_getinfo($ch);

    curl_close($ch);

    $httpCode = $info['http_code'];
    if ($httpCode != 200 && $httpCode != 222) {
      $this->_error = 'Konnte Finanzamt-Server nicht erreichen (HTTP status code: ' . $httpCode . ')';
      return false;
    }

    return $result;
  }

  /**
   * convert the xml to array
   *
   * @param $xml
   * @return array
   */
  private function convertXML2Array($xml)
  {
    if (!$xml) {
      return array(
        'ErrorMSG' => $this->_errorCodes[100],
        'ErrorCode' => 100
      );
    }

    $xml = simplexml_load_string($xml);
    if ($xml === false) {
      return array(
        'ErrorMSG' => $this->_errorCodes[101],
        'ErrorCode' => 101
      );
    }

    $response = array();
    if (isset($xml->param)) {
      foreach ($xml->param as $param) {
        if (!isset($param->value->array->data->value)) {
          continue;
        }
        $key = (string)$param->value->array->data->value[0]->string;
        $value = (string)$param->value->array->data->value[1]->string;
        $response[$key] = is_numeric($value) ? ((int)$value) : $value;
      }
    }
    $response = $this->extendResponse($response);
    return $response;
  }

  /**
   * extend the 'ErrorMSG' and the 'OK' field
   *
   * @param array $response
   * @return array
   */
  private function extendResponse($response)
  {
    $response = (array)$response;

    /**
     * let's add the error msg from the error codes 'table'
     */
    $response['ErrorMSG'] = 'Kein gültiger Fehlercode vom Finanzamt-Server erhalten.';

    if (array_key_exists('ErrorCode', $response)) {
      $error = (int)$response['ErrorCode'];
      $response['ErrorMSG'] = $this->generateErrorMsg($error, $response);
      $response['OK'] = $response['ErrorCode'] == 200;
    } else {
      $response['ErrorMSG'] = 'Kein gültiger Fehlercode vom Finanzamt-Server erhalten.';
      $response['OK'] = false;
    }
    return $response;
  }

  /**
   * @param $code
   * @param $env
   * @return string
   */
  private function generateErrorMsg($code, $env = array())
  {
    if (!array_key_exists($code, $this->_errorCodes)) {
      return 'Kein gültiger Fehlercode vom Finanzamt-Server erhalten.';
    }

    $msg = $this->_errorCodes[$code];

    if (empty($env) || !is_array($env)) {
      $rep = array('{{' => '(Siehe Feld: ', '}}' => ')');
      return strtr($msg, $rep);
    }

    /**
     * extracts the placeholders in the msg string.
     * E.g.: Sie war im Zeitraum von {{Gueltig_ab}} bis {{Gueltig_bis}} gültig.
     * converts to:
     * array(
     *  0 => array(
     *    0 => '{{Gueltig_ab}}'
     *    1 => '{{Gueltig_bis}}'
     *  )
     *  1 => array(
     *    0 => Gueltig_ab
     *    1 => Gueltig_bis
     *  )
     * )
     */
    preg_match_all("/\{\{([^}]+)\}\}/", $msg, $keys);
    /*
     * filter empty results:
     */
    $keys = array_filter($keys);

    /*
     * for each key in the extract, replace the string
     */
    $rep = array();
    if (is_array($keys) && count($keys) == 2) {
      foreach ($keys[1] as $key) {
        if (!array_key_exists($key, $env)) {
          continue;
        }
        $rep['{{' . $key . '}}'] = $env[$key];
      }
      $msg = strtr($msg, $rep);
    }
    return $msg;
  }
}

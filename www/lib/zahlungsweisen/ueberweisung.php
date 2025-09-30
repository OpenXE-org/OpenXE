<?php
require_once dirname(__DIR__).'/class.zahlungsweise.php';
class Zahlungsweise_ueberweisung extends Zahlungsweisenmodul
{
  /** @var Application  */
  var $app;
  /** @var array */
  protected $data;

  /**
   * Zahlungsweise_rechnung constructor. SEPA XML
   *
   * @param Application $app
   * @param int         $id
   */
  public function __construct($app, $id)
  {
    $this->app= $app;
    $this->id = $id;
    $this->data = $this->app->DB->SelectRow(
      sprintf(
        'SELECT * FROM zahlungsweisen WHERE id = %d',
        $id
      )
    );
    $einstellungen_json = $this->data['einstellungen_json'];
    if(!empty($einstellungen_json)) {
      $einstellungen_json = json_decode($einstellungen_json,true);
    }
    if(!empty($einstellungen_json)) {
      $this->einstellungen = $einstellungen_json;
    }
    else{
      $this->einstellungen = array();
    }
  }

  /**
   * @return string
   */
  public function GetBezeichnung()
  {
    return 'Ueberweisung';
  }

  /**
   * @return array
   */
  public function EinstellungenStruktur()
  {   
    return [
      'konto' => [
        'typ'=>'select',
        'optionen' => $this->app->DB->SelectPairs("SELECT id, CONCAT(kurzbezeichnung,' ',bezeichnung) kurzbezeichnung FROM konten"),
        'bezeichnung'=>'Gesch&auml;ftskonto',
        'replace' => 'konto'
      ]
    ];
  }

  /**
   * @param string $doctype
   * @param int    $doctypeid
   *
   * @return string
   */
  public function GetZahlungsweiseText($doctype, $doctypeid)
  {   
    return '';
  }

  /**
   * @param $postData
   */
  public function updatePostDataForAssistent($postData)
  {
    $postData['verhalten'] = 'rechnung';

    return $postData;
  }
   
}


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
  
  /*
  Create dataset from belege (Verbindlichkeit, Gutschrift)
  items = array(array('doc_typ', 'doc_id'))
  */  
  function ueberweisung_create_dataset(array $items) {
    foreach ($items as $item) {
        
        /*
            angelegt
            fehlgeschlagen
            verbucht
            exportiert
            abgeschlossen
        */

        $doc_typ = $item['doc_typ'];
        $id = $item['doc_id'];
        $doc_name = ucfirst($doc_typ);

        $sql = "SELECT id FROM payment_transaction WHERE doc_typ = '".$doc_typ."' AND doc_id = ".$id." LIMIT 1";
        $pm = $this->app->DB->Select($sql);
        if ($pm) {
            return (array('error' => $doc_name.' bereits im Zahllauf'));
        }

        $sql = "SELECT * FROM ".$doc_typ." WHERE id = ".$id." LIMIT 1";
        $belegrow = $this->app->DB->SelectRow($sql);

        if (
            $belegrow['status'] <> 'freigegeben' ||
            $belegrow['bezahlt']
        ) {
            return (array('error' => $doc_name.' hat falschen Status'));
        }

        $paymentMethodService = $this->app->Container->get('PaymentMethodService');
        try {
            $zahlungsweiseData = $paymentMethodService->getFromShortname($belegrow['zahlungsweise']);
            if ($zahlungsweiseData['modul'] != 'ueberweisung') {
                return (array('error' => $doc_name.' hat falsche Zahlungsweise'));
            }
            if (empty($zahlungsweiseData)) {
                return (array('error' => $doc_name.' hat keine Zahlungsweise'));
            }
        } catch (Exception $e) {
            return (array('error' => $doc_name.' hat keine Zahlungsweise'));
        }

        $kontodaten = $this->app->DB->SelectRow("SELECT * FROM konten WHERE id = ".$zahlungsweiseData['einstellungen']['konto']." LIMIT 1");
        $adressdaten = $this->app->DB->SelectRow("SELECT * FROM adresse WHERE id = ".$belegrow['adresse']);

        // Skonto
        $skontobis = date_create_from_format('!Y-m-d+', $belegrow['skontobis']);
        $heute = new DateTime('midnight');
        $abstand = $skontobis->diff($heute)->format("%r%a"); // What a load of bullshit, WTF php...

        if ($abstand <= 0) {
            $betrag = round($belegrow['betrag']*(100-($belegrow['skonto']/100)),2);
            $duedate = $belegrow['skontobis'];
        } else {
            $betrag = $belegrow['betrag'];
            $duedate = $belegrow['zahlbarbis'];
        }

        if ($duedate == '0000-00-00') {
            return (array('error' => 'Ung&uuml;ltiges Zahlungsziel'));
        }

        // Generate Dataset
        $payment_details = array(
            'sender' => $kontodaten['inhaber'],
            'sender_iban' => $kontodaten['iban'],
            'sender_bic' => $kontodaten['swift'],
            'empfaenger' => $adressdaten['inhaber'],
            'iban' => $adressdaten['iban'],
            'bic' => $adressdaten['swift'],
            'betrag' => $betrag,
            'waehrung' => $belegrow['waehrung'],
            'vz1' => $belegrow['rechnung'],
            'datumueberweisung' => ''
        );

        // Save to DB
        $input = array(
            'payment_account_id' => $zahlungsweiseData['id'],
            'doc_typ' => $doc_typ,
            'doc_id' => $id,
            'address_id' => $adressdaten['id'],
            'payment_status' => 'angelegt',
            'amount' => $betrag,
            'currency' => $belegrow['waehrung'],
            'duedate' => $duedate,
            'payment_reason' => $doc_name.' '.$belegrow['belegnr'],
            'payment_info' => $belegrow['rechnung'],
            'payment_json ' => json_encode($payment_details)
        );

        $columns = "id, ";
        $values = "$id, ";
        $update = "";

        $fix = "";

        foreach ($input as $key => $value) {
            $columns = $columns.$fix.$key;
            $values = $values.$fix."'".$value."'";
            $update = $update.$fix.$key." = '$value'";

            $fix = ", ";
        }
        $sql = "INSERT INTO payment_transaction (".$columns.") VALUES (".$values.") ON DUPLICATE KEY UPDATE ".$update;
        $this->app->DB->Update($sql);

        $this->app->erp->BelegProtokoll($doc_typ,$id,$doc_name." zum Zahllauf gegeben.");
        return(true);
    }
  
  /*
  Create SEPA XML from a list of transactions
  */
  function ueberweisung_create_SEPA_XML(array payment_transactions) {
  
  }
  
}


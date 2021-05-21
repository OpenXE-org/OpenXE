<?php
require_once dirname(__DIR__).'/class.zahlungsweise.php';
class Zahlungsweise_rechnung extends Zahlungsweisenmodul
{
  /** @var Application  */
  var $app;
  /** @var array */
  protected $data;

  /**
   * Zahlungsweise_rechnung constructor.
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
   * @param string      $doctype
   * @param int         $doctypeId
   * @param string      $text
   * @param string      $language
   * @param string|null $zahlungszielskonto
   * @param string|null $zahlungszieltageskonto
   * @param int|null    $zahlungszieltage
   *
   * @return string
   */
  public function getSkontoText(
    $doctype,
    $doctypeId,
    $text,
    $language = '',
    $zahlungszielskonto = null,
    $zahlungszieltageskonto = null,
    $zahlungszieltage = null
  ) {
    if($zahlungszielskonto === null || $zahlungszieltageskonto === null) {
      $doctypeRow = $this->app->DB->SelectRow(
        sprintf(
          "SELECT *
          FROM `%s` 
          WHERE id = %d",
          $doctype, $doctypeId
        )
      );
      if(isset($doctypeRow['zahlungszieltage']) && isset($doctypeRow['zahlungszieltageskonto'])) {
        $zahlungszielskonto = $doctypeRow['zahlungszielskonto'];
        $zahlungszieltageskonto = $doctypeRow['zahlungszieltageskonto'];
        $zahlungszieltage = $doctypeRow['zahlungszieltage'];
      }
      else {
        $doctypeRow = $this->app->DB->SelectRow(
          sprintf(
            "SELECT zahlungszieltage, zahlungszieltageskonto
            FROM `adresse` 
            WHERE id = %d",
            $doctypeRow['adresse']
          )
        );
        $zahlungszielskonto = $doctypeRow['zahlungszielskonto'];
        $zahlungszieltageskonto = $doctypeRow['zahlungszieltageskonto'];
        $zahlungszieltage = $doctypeRow['zahlungszieltage'];
      }
      if($zahlungszielskonto == 0) {
        return $text;
      }
    }
    $zahlungszielskontototal=$zahlungszielskonto;
    if(empty($language)) {
      $doctypeRow = $this->app->DB->SelectRow(
        sprintf(
          "SELECT sprache, adresse, datum, DATE_FORMAT(datum, '%%d.%%m.%%Y') AS datum_de, 
          DATE_FORMAT(DATE_ADD(datum, INTERVAL zahlungszieltage DAY),'%%d.%%m.%%Y') AS zahlungdatum,
          zahlungszieltage
          FROM `%s` 
          WHERE id = %d",
          $doctype, $doctypeId
        )
      );

      $language = !empty($doctypeRow['sprache'])?(String)$doctypeRow['sprache']:(String)$this->app->DB->Select(
        sprintf(
          'SELECT sprache FROM adresse WHERE id = %d',
          $doctypeRow['adresse']
        )
      );
    }
    $zahlungszielskontodatum = $this->app->DB->Select(
      sprintf(
        "SELECT DATE_FORMAT(DATE_ADD(`datum`, INTERVAL %d DAY),'%%d.%%m.%%Y') 
          FROM `%s`
          WHERE `id` = %d 
          LIMIT 1",
        $zahlungszieltageskonto, $doctype, $doctypeId
      )
    );
    if($this->einstellungen['own_discount_text']) {
      if($doctype === 'rechnung') {
        $ownText = $this->app->erp->Beschriftung('own_discount_text_invoice', $language);
        if(empty($ownText)) {
          $ownText = $this->einstellungen['own_discount_text_invoice'];
        }
        $text .= "\n".$ownText;
      }
      else{
        $ownText = $this->app->erp->Beschriftung('own_discount_text_order', $language);
        if(empty($ownText)) {
          $ownText = $this->einstellungen['own_discount_text_order'];
        }
        $text .= "\n".$ownText;
      }

      if($zahlungszieltageskonto<=0){
        $zahlungdatum = $this->app->DB->Select(
          sprintf(
            "SELECT DATE_FORMAT(DATE_ADD(`datum`, INTERVAL %d DAY),'%%d.%%m.%%Y') 
            FROM `%s`
            WHERE `id` = %d 
            LIMIT 1",
            $zahlungszieltageskonto, $doctype, $doctypeId
          )
        );
        $zahlungszielskontodatum = $zahlungdatum;
      }
    }
    else {
      $text .= "\n".$this->app->erp->Beschriftung('dokument_skonto', $language)
        ." $zahlungszielskonto% "
        .$this->app->erp->Beschriftung('dokument_innerhalb', $language)
        ." $zahlungszieltageskonto "
        .$this->app->erp->Beschriftung('dokument_tagen', $language);
    }

    return str_replace(
      [
        '{ZAHLUNGSZIELSKONTO}',
        '{ZAHLUNGSZIELTAGE}',
        '{ZAHLUNGSZIELTAGESKONTO}',
        '{ZAHLUNGSZIELSKONTOTOTAL}',
        '{ZAHLUNGSZIELSKONTODATUM}',
      ],
      [
        number_format($zahlungszielskonto,2,',','.'),
        $zahlungszieltage,
        $zahlungszieltageskonto,
        number_format($zahlungszielskontototal,2,',','.'),
        $zahlungszielskontodatum,
      ],
      $text
    );
  }

  /**
   * @return string
   */
  public function GetBezeichnung()
  {
    return 'Rechnung';
  }

  /**
   * @return array
   */
  public function EinstellungenStruktur()
  {
    return [
      'invoice_immediately' => [
        'typ'=>'textarea',
        'bezeichnung'=>'Satz in Rechnung: (sofort) (DE)',
        'default' => $this->app->erp->Beschriftung('zahlung_rechnung_sofort_de'),
        'lang' => 'zahlungsweisen_rechnung_invoice_immediately',
      ],
      'invoice_next'        => [
        'typ'=>'textarea',
        'bezeichnung'=>'Satz in Rechnung: (>= 1 Tag) (DE)',
        'default' => $this->app->erp->Beschriftung('zahlung_rechnung_de'),
        'lang' => 'zahlungsweisen_rechnung_invoice_next',
      ],
      'order_immediately'   => [
        'typ'=>'textarea',
        'bezeichnung'=>'Satz in Angebot/Auftrag: (sofort) (DE)',
        'default' => $this->app->erp->Beschriftung('zahlung_auftrag_sofort_de'),
        'lang' => 'zahlungsweisen_rechnung_order_immediately',
      ],
      'order_next'          => [
        'typ'=>'textarea',
        'bezeichnung'=>'Satz in Angebot/Auftrag: (>= 1 Tag) (DE)',
        'default' => $this->app->erp->Beschriftung('zahlung_auftrag_de'),
        'lang' => 'zahlungsweisen_rechnung_order_next',
      ],
      'own_discount_text' => [
        'typ' => 'checkbox',
        'bezeichnung' => 'Eigener Skontotext',
      ],
      'own_discount_text_order' => [
        'typ'=>'textarea',
        'bezeichnung'=>'Satz in Angebot/Auftrag (DE)',
        'default' => $this->app->erp->Beschriftung('eigener_skontotext_anab'),
        'lang' => 'own_discount_text_order',
      ],
      'own_discount_text_invoice' => [
        'typ' => 'textarea',
        'bezeichnung'=>'Satz in Rechnung (DE)',
        'default' => $this->app->erp->Beschriftung('eigener_skontotext_re'),
        'lang' => 'own_discount_text_invoice',
      ],
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
    $doctypeRow = $this->app->DB->SelectRow(
      sprintf(
        "SELECT sprache, adresse, datum, DATE_FORMAT(datum, '%%d.%%m.%%Y') AS datum_de, 
        DATE_FORMAT(DATE_ADD(datum, INTERVAL zahlungszieltage DAY),'%%d.%%m.%%Y') AS zahlungdatum,
        zahlungszieltage
        FROM `%s` 
        WHERE id = %d",
        $doctype, $doctypeid
      )
    );
    $zahlungszieltage = $doctypeRow['zahlungszieltage'];
    $zahlungdatum= $doctypeRow['zahlungdatum'];
    $language = !empty($doctypeRow['sprache'])?$doctypeRow['sprache']:
      $this->app->DB->Select(
        sprintf(
          'SELECT sprache FROM adresse WHERE id = %d',
          $doctypeRow['adresse']
        )
      );

    if($zahlungszieltage == 0) {
      $name = $doctype==='rechnung'?'invoice_immediately':'order_immediately';
    }
    else {
      $name = $doctype==='rechnung'?'invoice_next':'order_next';
    }
    $zahlungsweisetext = (String)$this->app->erp->Beschriftung('zahlungsweisen_rechnung_'.$name, $language);
    if(empty($zahlungsweisetext)) {
      $zahlungsweisetext = (String)$this->einstellungen[$name];
    }
    if(empty($zahlungsweisetext)) {
      
    }
    if(empty($zahlungsweisetext)) {
      switch($name) {
        case 'invoice_immediately':
            $zahlungsweisetext = 'Rechnung zahlbar sofort. ';
          break;
        case 'invoice_next':
          $zahlungsweisetext = 'Rechnung zahlbar innerhalb von {ZAHLUNGSZIELTAGE} Tagen bis zum {ZAHLUNGBISDATUM}. ';
          break;
      }
    }
    $zahlungsweisetext = str_replace(
      ['{ZAHLUNGSZIELTAGE}','{ZAHLUNGBISDATUM}'],
      [$zahlungszieltage,$zahlungdatum],
      $zahlungsweisetext
    );

    if(in_array($doctype, ['rechnung','auftrag','angebot'])) {
      $zahlungsweisetext = $this->getSkontoText($doctype, $doctypeid, $zahlungsweisetext);
    }

    return $zahlungsweisetext;
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


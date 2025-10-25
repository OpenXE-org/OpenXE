<?php
require_once dirname(__DIR__).'/class.zahlungsweise.php';
require_once dirname(__DIR__).'/../plugins/sepa/Sepa_credit_XML_Transfer_initation.class.php';
class Zahlungsweise_sepa_xml extends Zahlungsweisenmodul
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
            'optionen' => $this->app->DB->SelectPairs("SELECT 0 id, '' kurzbezeichnung UNION SELECT id, CONCAT(kurzbezeichnung,' ',bezeichnung) kurzbezeichnung FROM konten"),
            'bezeichnung'=>'Gesch&auml;ftskonto',
            'replace' => 'konto'
          ],
        'smarty' => [
            'typ'=>'select',
            'optionen' => $this->app->DB->SelectPairs("SELECT 0 id, '' kurzbezeichnung UNION SELECT id, name FROM smarty_templates"),
            'bezeichnung' => 'Smarty Template für SEPA-XML'
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

    public function ProcessPayment(array $transaction_block): array {

        $payment_result = array(
                'success' => false,
                'successful_transactions' => array(),
                'errors' => array(),
                'payment_objects' => array()
        );

        // Validate
        if (!Sepa_credit_XML_Transfer_initation::validateIBAN($transaction_block['accountdata']['iban'])) {
            $payment_result['errors'][] = "Sender-IBAN ung&uuml;ltig: ".$transaction_block['accountdata']['iban'];
            return($payment_result);
        };
        if (!Sepa_credit_XML_Transfer_initation::validateBIC($transaction_block['accountdata']['swift'])) {
            $payment_result['errors'][] = "Sender-BIC ung&uuml;ltig: ".$transaction_block['accountdata']['swift'];
            return($payment_result);
        }

        foreach($transaction_block['transactions'] as $key => $transaction) {
            if ($transaction['waehrung'] != 'EUR') {
                $kurs = $this->app->erp->GetWaehrungUmrechnungskurs($transaction['waehrung'],'EUR');
                if ($kurs > 0) {
                    $transaction_block['transactions'][$key]['waehrung'] = 'EUR';
                    $transaction_block['transactions'][$key]['betrag'] = $transaction['betrag']*$kurs;
                } else {
                    $payment_result['errors'][] = "Transaktionen ist nicht in EUR und es existiert kein Umrechnungskurs: ".$transaction['verwendungszweck'];
                    return($payment_result);
                }
            }
            if (!Sepa_credit_XML_Transfer_initation::validateIBAN($transaction['adresse']['iban'])) {
                $payment_result['errors'][] = "Empf&auml;nger-IBAN ung&uuml;ltig: ".$$transaction['adresse']['iban']." (".$transaction['adresse']['name'].")";
                return($payment_result);
            };
            if (!empty($transaction['adresse']['swift']) && !Sepa_credit_XML_Transfer_initation::validateBIC($transaction['adresse']['swift'])) {
                $payment_result['errors'][] = "Empf&auml;nger-BIC ung&uuml;ltig: ".$transaction['adresse']['swift']." (".$transaction['adresse']['name'].")";
                return($payment_result);
            }
            if (empty($transaction['adresse']['inhaber'])) {
                $transaction_block['transactions'][$key]['adresse']['inhaber'] = $transaction['adresse']['name'];
            }
            $payment_result['successful_transactions'][] = $transaction['id'];
        }

        $datetime = new DateTime();

        $message_id = preg_replace("/[^a-z0-9-]+/i", "", $datetime->format('YmdHis')."-".$this->data['bezeichnung']);

        $template = $this->app->DB->Select("SELECT template from smarty_templates WHERE id = '".$this->einstellungen['smarty']."' LIMIT 1");

        if (empty($template)) {
            $template = '<?xml version="1.0" encoding="UTF-8"?>
<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.001.003.03" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:iso:std:iso:20022:tech:xsd:pain.001.003.03 pain.001.003.03.xsd">
    <CstmrCdtTrfInitn>
        <GrpHdr>
            <MsgId><![CDATA[{$sepa_nachricht_id|truncate:35:"":true}]]></MsgId>
            <CreDtTm>{$zeit}</CreDtTm>
            <NbOfTxs>{$anzahl_transaktionen}</NbOfTxs>
            <CtrlSum>{$gesamt_betrag}</CtrlSum>
            <InitgPty>
                <Nm><![CDATA[{$sender_konto.inhaber|truncate:70:"":true}]]></Nm>
            </InitgPty>
        </GrpHdr>
        <PmtInf>
            <PmtInfId><![CDATA[{$sepa_nachricht_id|truncate:35:"":true}]]></PmtInfId>
            <PmtMtd>TRF</PmtMtd>
            <NbOfTxs>{$anzahl_transaktionen}</NbOfTxs>
            <ReqdExctnDt>{$datum}</ReqdExctnDt>
            <Dbtr>
                <Nm><![CDATA[{$sender_konto.inhaber|truncate:35:"":true}]]></Nm>
            </Dbtr>
            <DbtrAcct>
                <Id>
                    <IBAN><![CDATA[{$sender_konto.iban}]]></IBAN>
                </Id>
                <Ccy>EUR</Ccy>
            </DbtrAcct>
            <DbtrAgt>
                <FinInstnId>
                    <BIC><![CDATA[{$sender_konto.swift}]]></BIC>
                </FinInstnId>
            </DbtrAgt>
{foreach from=$transaktionen item=transaktion}
            <CdtTrfTxInf>
                <PmtId>
                    <EndToEndId><![CDATA[{$transaktion.adresse.iban}]]></EndToEndId>
                </PmtId>
                <Amt>
                    <InstdAmt Ccy="{$transaktion.waehrung}">{$transaktion.betrag}</InstdAmt>
                </Amt>
                <CdtrAgt>
                    <FinInstnId>
                        <BIC><![CDATA[{$transaktion.adresse.swift}]]></BIC>
                    </FinInstnId>
                </CdtrAgt>
                <Cdtr>
                    <Nm><![CDATA[{$transaktion.adresse.inhaber|truncate:70:"":true}]]></Nm>
                </Cdtr>
                <CdtrAcct>
                    <Id>
                        <IBAN><![CDATA[{$transaktion.adresse.iban}]]></IBAN>
                    </Id>
                </CdtrAcct>
                <RmtInf>
                    <Ustrd><![CDATA[{$transaktion.verwendungszweck|truncate:140:"":true}]]></Ustrd>
                </RmtInf>
            </CdtTrfTxInf>
 {/foreach}
        </PmtInf>
    </CstmrCdtTrfInitn>
</Document>';
        }

        try {
            $smarty = new Smarty;
            $directory = $this->app->erp->GetTMP().'/smarty/templates';
            $smarty->setCompileDir($directory);
            $smarty->assign('sender_konto', $transaction_block['accountdata']);
            $smarty->assign('transaktionen', $transaction_block['transactions']);
            $smarty->assign('anzahl_transaktionen', is_array($transaction_block['transactions'])?count($transaction_block['transactions']):0);
            $smarty->assign('gesamt_betrag', array_sum(array_column($transaction_block['transactions'],'amount')));
            $smarty->assign('zeit', $datetime->format('Y-m-d').'T'.$datetime->format('H:i:s'));
            $smarty->assign('datum', $datetime->format('Y-m-d'));
            $smarty->assign('bezeichnung', $this->data['bezeichnung']);
            $smarty->assign('sepa_nachricht_id', $message_id);
            $output = $smarty->fetch('string:'.$template);
        }
        catch (Exception $e) {
          throw $e;
        }

        $payment_result['payment_objects'][] =
            array(
                'payment_transaction_ids' => $payment_result['successful_transactions'],
                'attachments' => array(
                    array(
                        'filename' => $message_id.".xml",
                        'description' => 'SEPA SCT PAIN.001.003.03 - Überweisung',
                        'contents' => $output
                    )
                )
            );

        $payment_result['success'] = true;

        return($payment_result);
    }
}


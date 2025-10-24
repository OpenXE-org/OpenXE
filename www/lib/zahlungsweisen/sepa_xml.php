<?php
require_once dirname(__DIR__).'/class.zahlungsweise.php';
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
            'optionen' => $this->app->DB->SelectPairs("SELECT id, CONCAT(kurzbezeichnung,' ',bezeichnung) kurzbezeichnung FROM konten"),
            'bezeichnung'=>'Gesch&auml;ftskonto',
            'replace' => 'konto'
          ],
        'smarty' => [
            'typ'=>'select',
            'optionen' => $this->app->DB->SelectPairs("SELECT id, name FROM smarty_templates"),
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
/*        
   $payment_result = array(
                'success' => true,
                'successful_transactions' => $payment_transaction_ids,
                'payment_objects' => array(
                    array(
                        'id' => 'mipmap234',
                        'description' => 'This is a nice payment, you got there',
                        'type' => payment_object_types::DOWNLOAD,
                        'payment_transaction_ids' => $payment_transaction_ids,
                        'attachments' => array(
                            array(
                                'filename' => 'SEPA1.xml',
                                'contents' => 'Hallo Hallo contents'
                            )
                        )
                    ),
                    array(
                        'id' => 'knuffeldipupp',
                        'description' => 'shame if someone would transfer it...',
                        'type' => payment_object_types::DOWNLOAD,
                        'payment_transaction_ids' => $payment_transaction_ids,
                        'attachments' => array(
                            array(
                                'filename' => 'SEPA2.xml',
                                'contents' => 'ADSLJFALÖJSDFLJASDLFJALSJDFLJASDLF '
                            ),
                            array(
                                'filename' => 'SEPA3.xml',
                                'contents' => 'Ein Mann ging in den Wald, dort war es kalt.'
                            )
                        )
                    )
                )
            );*/

        $payment_result = array(
                'success' => false,
                'successful_transactions' => array(),
                'payment_objects' => array()
        );

        $datetime = new DateTime();        

        $message_id = str_replace(' ','_',$this->data['bezeichnung']).'_'.$datetime->format('YmdHis');

        $template = $this->app->DB->Select("SELECT template from smarty_templates WHERE id = ".$this->einstellungen['smarty']." LIMIT 1");

        if (empty($template)) {
            $template = '<?xml version="1.0" encoding="UTF-8"?>
<Document xmlns="urn:iso:std:iso:20022:tech:xsd:pain.001.003.03" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="urn:iso:std:iso:20022:tech:xsd:pain.001.003.03 pain.001.003.03.xsd">
    <CstmrCdtTrfInitn>
        <GrpHdr>
            <MsgId><![CDATA[{$sepa_nachricht_id}]]></MsgId>
            <CreDtTm>{$zeit}</CreDtTm>
            <NbOfTxs>{$anzahl_transaktionen}</NbOfTxs>
            <CtrlSum>{$gesamt_betrag}</CtrlSum>
            <InitgPty>
                <Nm><![CDATA[{$sender_konto.inhaber}]]></Nm>
            </InitgPty>
        </GrpHdr>
        <PmtInf>
            <PmtInfId><![CDATA[{$sepa_nachricht_id}]]></PmtInfId>
            <PmtMtd>TRF</PmtMtd>
            <NbOfTxs>{$anzahl_transaktionen}</NbOfTxs>
            <ReqdExctnDt>{$datum}</ReqdExctnDt>
            <Dbtr>
                <Nm><![CDATA[{$sender_konto.inhaber}]]></Nm>
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
                    <Nm><![CDATA[{$transaktion.adresse.inhaber}]]></Nm>
                </Cdtr>
                <CdtrAcct>
                    <Id>
                        <IBAN><![CDATA[{$transaktion.adresse.iban}]]></IBAN>
                    </Id>
                </CdtrAcct>
                <RmtInf>
                    <Ustrd><![CDATA[{$transaktion.verwendungszweck}]]></Ustrd>
                </RmtInf>
            </CdtTrfTxInf>
 {/foreach}
        </PmtInf>
    </CstmrCdtTrfInitn>
</Document>';
        }

        print_r($transaction_block);

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


        echo('<textarea style="width:100%; height:100%;">');
        print_r($output);
        echo("</textarea>");

        exit();

        return($payment_result);        

    }

}


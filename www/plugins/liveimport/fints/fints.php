<?php
chdir(dirname(dirname(dirname(__DIR__))));
if(file_exists(dirname(dirname(__DIR__)).'/fints-hbci-php/vendor/autoload.php')){
  require_once dirname(dirname(__DIR__)) . '/fints-hbci-php/vendor/autoload.php';
}

class fints
{
  public function __construct()
  {
    $this->numberofdays = 50;
    $this->numberofdayswait = 0;
    $this->unterkonto = 0;
  }

  public function Import($zugangsdaten)
  {
    if(!file_exists(dirname(dirname(__DIR__)).'/fints-hbci-php/vendor/autoload.php'))
    {
      return;
    }
    //user login information
    $this->FHP_BANK_URL=!empty($zugangsdaten["FHP_BANK_URL"])?$zugangsdaten["FHP_BANK_URL"]:'';
    $this->FHP_BANK_PORT=!empty($zugangsdaten["FHP_BANK_PORT"])?(int)$zugangsdaten["FHP_BANK_PORT"]:0;
    $this->FHP_BANK_CODE=!empty($zugangsdaten["FHP_BANK_CODE"])?$zugangsdaten["FHP_BANK_CODE"]:'';
    $this->FHP_ONLINE_BANKING_USERNAME=!empty($zugangsdaten["FHP_ONLINE_BANKING_USERNAME"])?$zugangsdaten["FHP_ONLINE_BANKING_USERNAME"]:'';
    $this->FHP_ONLINE_BANKING_PIN=!empty($zugangsdaten["FHP_ONLINE_BANKING_PIN"])?$zugangsdaten["FHP_ONLINE_BANKING_PIN"]:'';

    if(!empty($zugangsdaten["FHP_BANK_UNTERKONTO"]) && $zugangsdaten["FHP_BANK_UNTERKONTO"] > 0 && is_numeric($zugangsdaten["FHP_BANK_UNTERKONTO"])){
      $this->unterkonto = $zugangsdaten["FHP_BANK_UNTERKONTO"];
    }


    if(!empty($zugangsdaten["API_DAYS"]) && $zugangsdaten["API_DAYS"] > 0 && is_numeric($zugangsdaten["API_DAYS"])){
      $this->numberofdays = $zugangsdaten["API_DAYS"];
    }

    if(!empty($zugangsdaten["API_DAYS_WAIT"]) && $zugangsdaten["API_DAYS_WAIT"] > 0 && is_numeric($zugangsdaten["API_DAYS_WAIT"])){
      $this->numberofdayswait = $zugangsdaten["API_DAYS_WAIT"];
    }

    $fints = new Fhp\FinTs(
        $this->FHP_BANK_URL,
        $this->FHP_BANK_PORT,
        $this->FHP_BANK_CODE,
        $this->FHP_ONLINE_BANKING_USERNAME,
        $this->FHP_ONLINE_BANKING_PIN
        );

    $accounts = $fints->getSEPAAccounts();

    $oneAccount = $accounts[$this->unterkonto];

    if($this->numberofdayswait > 0)
    {
      $to = new \DateTime();
      $to->sub(new DateInterval('P'.$this->numberofdayswait.'D'));
    } else {
      $to = new \DateTime('NOW');
    }
    $to->format('Y-m-d');

    $from   = new \DateTime();
    $from->sub(new DateInterval('P'.$this->numberofdays.'D'));
    $from->format('Y-m-d');

    $soa = $fints->getStatementOfAccount($oneAccount, $from, $to);

    $csv = "";
    foreach ($soa->getStatements() as $statement) {
      foreach ($statement->getTransactions() as $transaction) {
        $csv .= $transaction->getBookingDate()->format('Y-m-d') .";";
        $csv .= ($transaction->getCreditDebit() == Fhp\Model\StatementOfAccount\Transaction::CD_DEBIT ? '-' : '') . $transaction->getAmount() . ";";
        $csv .= $transaction->getName() . ";";
        $csv .= $transaction->getDescription1() . ";";
        $csv .= $transaction->getDescription2() ." ".$transaction->getBookingText()." ".$transaction->getAccountNumber()." ".$transaction->getBankCode().";";
        $csv .= "EUR;\r\n";
      }
    }

    return $csv;
  }

}


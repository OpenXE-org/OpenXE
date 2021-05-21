<?php
/*
**** COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
* 
* Xentral (c) Xentral ERP Sorftware GmbH, Fuggerstrasse 11, D-86150 Augsburg, * Germany 2019
*
* This file is licensed under the Embedded Projects General Public License *Version 3.1. 
*
* You should have received a copy of this license from your vendor and/or *along with this file; If not, please visit www.wawision.de/Lizenzhinweis 
* to obtain the text of the corresponding license version.  
*
**** END OF COPYRIGHT & LICENSE NOTICE *** DO NOT REMOVE ****
*/
?>
<?php
class Geschaeftsbrief extends Brief {
  private $app;
  
  private $senderData;
  private $recipientData;
  private $letterData;


  public function __construct($app, $briefId) {
    parent::__construct();
    
    define("BEGRUESSUNG", "Sehr geehrter Herr XY,");
    define("SCHLUSSFORMEL", "Mit freundlichen Grüßen");
    
    $this->app=$app;
    $this->acquireData($briefId);
    //print_r($this->senderData);
    $this->assemblePDF();
    //$this->displayDocument();
  }

  function acquireData($briefId) {
    // Briefdetails
    $query = "SELECT * FROM brief WHERE id=".$briefId;
    $this->letterData = $this->app->DB->SelectArr($query);
    $this->letterData = $this->letterData[0];

    // Emfänger
    //$query = "SELECT * FROM adresse WHERE id=".$this->letterData['adresse'];
    //$this->recipientData = $this->app->DB->SelectArr($query);
    //$this->recipientData = $this->recipientData[0];
    $this->setRecipientDB($this->letterData['adresse']);


    // Sender
    $query = "SELECT * FROM adresse WHERE id=".$this->letterData['bearbeiter'];
    $this->senderData = $this->app->DB->SelectArr($query);
    $this->senderData = $this->senderData[0];
  }
  
  function displayDocument() {
    
    if($this->recipientData['typ']=="person") {
      $this->setDocumentDetails(
          "recipient", 
          array("enterprise"=>"","firstname"=>$this->recipientData['vorname'],"familyname"=>$this->recipientData['name'],"address1"=>$this->recipientData['strasse'],"address2"=>$this->recipientData['adresszusatz'],"areacode"=>$this->recipientData['plz'],"city"=>$this->recipientData['ort'],"country"=>$this->recipientData['land'],"phone1"=>$this->recipientData['telefon'],"fax"=>$this->recipientData['telefax'],"email"=>$this->recipientData['email'],"web"=>"","taxnr"=>"", "ustid"=>$this->recipientData['ustid'])
      );
    } else {
      $this->setDocumentDetails(
          "recipient", 
          array("enterprise"=>$this->recipientData['name'],"firstname"=>"","familyname"=>"","address1"=>$this->recipientData['strasse'],"address2"=>"","areacode"=>$this->recipientData['plz'],"city"=>$this->recipientData['ort'],"country"=>$this->recipientData['land'],"phone1"=>$this->recipientData['telefon'],"fax"=>$this->recipientData['telefax'],"email"=>$this->recipientData['email'],"web"=>"","taxnr"=>"", "ustid"=>$this->recipientData['ustid'])
      );
    }

    if($this->senderData['typ']=="person") {
      $this->setDocumentDetails(
          "sender", 
          array("enterprise"=>"","firstname"=>$this->senderData['vorname'],"familyname"=>$this->senderData['name'],"address1"=>$this->senderData['strasse'],"address2"=>$this->senderData['adresszusatz'],"areacode"=>$this->senderData['plz'],"city"=>$this->senderData['ort'],"country"=>$this->senderData['land'],"phone1"=>$this->senderData['telefon'],"fax"=>$this->senderData['telefax'],"email"=>$this->senderData['email'],"web"=>"","taxnr"=>"", "ustid"=>$this->senderData['ustid'])
      );
    } else {
      $this->setDocumentDetails(
          "sender", 
          array("enterprise"=>$this->senderData['name'],"firstname"=>"","familyname"=>"","address1"=>$this->senderData['strasse'],"address2"=>"","areacode"=>$this->senderData['plz'],"city"=>$this->senderData['ort'],"country"=>$this->senderData['land'],"phone1"=>$this->senderData['telefon'],"fax"=>$this->senderData['telefax'],"email"=>$this->senderData['email'],"web"=>"","taxnr"=>"", "ustid"=>$this->senderData['ustid'])
      );
    }
    
    $this->setDocumentDetails(
      "letterDetails",
      array('subject' => $this->letterData['betreff'], 'salutation' => BEGRUESSUNG,	'body' => $this->letterData['nachricht'], 'valediction' => SCHLUSSFORMEL)
    );

    $this->setLogo("logo_briefkopf.jpg");
    parent::displayDocument();

  }

}


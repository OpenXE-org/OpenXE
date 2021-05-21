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

class Korrespondenz
{

  function __construct(&$app)
  {
    $this->app=&$app; 
  
    $this->app->ActionHandlerInit($this);

    $this->app->ActionHandler("list","KorrespondenzList");
    $this->app->ActionHandler("create","KorrespondenzCreate");
    $this->app->ActionHandler("edit","KorrespondenzEdit");
    $this->app->ActionHandler("pdf","KorrespondenzPDF");
    $this->app->ActionHandler("send","KorrespondenzSend");
    $this->app->ActionHandler("delete","KorrespondenzDelete");
  
    $this->app->DefaultActionHandler("edit");
    
    $this->app->ActionHandlerListen($app);
  }

	function KorrespondenzCreate()
	{
		$post = $this->SaveInput();

		// PDF
		if($post['pdf']!='') {
			$this->CreatePDF($post);	
		}

		// Save
		if($post['save']!='') {
			$insert = $this->CreateDokument($post);
			if(is_numeric($insert) && $insert>0) {
				header("Location: ./index.php?module=korrespondenz&action=edit&id=$insert");
				exit;
			}else
				$this->app->Tpl->Set('MESSAGE', '<div class="error">Das Dokument konnte nicht erstellt werden</div>');
		}

		// Send
		if($post['send']!='') {
			$insert = $this->CreateDokument($post);
      if(is_numeric($insert) && $insert>0) {
        header("Location: ./index.php?module=korrespondenz&action=send&id=$insert");
        exit;
      }else
        $this->app->Tpl->Set('MESSAGE', '<div class="error">Das Dokument konnte nicht erstellt werden</div>');	
		}

		// Prefill Form
		if($post['prefill']=='') {
			$post['von'] = $this->app->User->GetName();
			$post['firma'] = $this->app->DB->Select('SELECT absender FROM firmendaten WHERE firma="'.$this->app->User->GetFirma().'" LIMIT 1');
			$post['datum'] = date("d.m.Y");

			$userdata = $this->app->DB->SelectArr("SELECT * FROM adresse WHERE id='{$post['user']}' LIMIT 1");
			if(is_array($userdata) && count($userdata)>0) {
				$vorname = '';
				if(($userdata[0]['typ']=='herr' || $userdata[0]['typ']=='frau') && $userdata[0]['vorname']!='')
					$vorname =  "{$userdata[0]['vorname']} ";

				$post['an'] = $vorname.$userdata[0]['name'];
				$post['ansprechpartner'] = $userdata[0]['ansprechpartner'];
				$post['email_an'] = $userdata[0]['email'];
				$post['adresse'] = $userdata[0]['strasse'];
				$post['plz'] = $userdata[0]['plz'];
				$post['ort'] = $userdata[0]['ort'];
				$post['land'] = $userdata[0]['land'];
				$post['firma_an'] = $this->app->DB->Select("SELECT name FROM firma WHERE id='{$userdata[0]['firma_an']}' LIMIT 1");
			}
			

			$post['prefill'] = '1';
		}
		$this->LoadInput($post);
	
		$this->app->BuildNavigation=false;
		$this->app->YUI->AutoComplete('ansprechpartner', 'adressename');
		$this->app->Tpl->Parse('PAGE', 'korrespondenz_create.tpl');
	}

	function KorrespondenzEdit()
	{
		$post = $this->SaveInput();

		// PDF
		if($post['pdf']!='' && is_numeric($post['id'])) {
			$this->UpdateDokument($post);
      header("Location: ./index.php?module=korrespondenz&action=pdf&id={$post['id']}");
      exit;
		}

		// Save
		if($post['save']!='' && is_numeric($post['id'])) {
			$this->UpdateDokument($post);		
		}

		// Send
		if($post['send']!='' && is_numeric($post['id'])) {
			$this->UpdateDokument($post);
			header("Location: ./index.php?module=korrespondenz&action=send&id={$post['id']}");
      exit;
		}

		if(is_numeric($post['id'])) {
			$data = $this->app->DB->SelectArr("SELECT * FROM dokumente WHERE id='{$post['id']}' LIMIT 1");
			$postdata = $this->ConvertFromDB($data);
		}

		$this->LoadInput($postdata);

		$this->app->BuildNavigation=false;
		$this->app->YUI->AutoComplete('ansprechpartner', 'adressename');
		$this->app->Tpl->Parse('PAGE', 'korrespondenz_create.tpl');
	}

	function KorrespondenzSend()
	{
		$this->app->BuildNavigation=false;

		$id = $this->app->Secure->GetGET('id');
		$data = $this->ConvertFromDB($this->app->DB->SelectArr("SELECT * FROM dokumente WHERE id='$id' LIMIT 1"));

		$data['adresse_to'] = $this->app->DB->Select("SELECT adresse_to FROM dokumente WHERE id='$id' LIMIT 1");
		$projekt = $this->app->DB->Select("SELECT projekt FROM adresse WHERE id='".$data['adresse_to']."' LIMIT 1");

		switch($data['art']) {
			case 'email':
 					if($this->app->erp->MailSend($data['email'],$data['von'],$data['email_an'],$data['to'],$data['betreff'],$data['content'],'',$projekt)=='1'){
						$this->app->Tpl->Set('PAGE',"<div class=\"success\">Die E-Mail wurde erfolgreich versendet.</div>");
						$this->app->DB->Update("UPDATE dokumente SET sent='1' WHERE id='$id' LIMIT 1");
					} else
						$this->app->Tpl->Set('PAGE',"<div class=\"error\">Die E-Mail wurde erfolgreich versendet.</div>");
				break;
			case 'mail':
					$korrespondenz = $this->CreatePDF($data, false);
					$this->app->printer->Drucken($data['mail'],$korrespondenz);
					$this->app->DB->Update("UPDATE dokumente SET sent='1' WHERE id='$id' LIMIT 1");
    			unlink($korrespondenz);
					$this->app->Tpl->Set('PAGE',"<div class=\"success\">Das Dokument wurde an den Drucker &uuml;bertragen</div>");
				break;
			case 'fax':
					$korrespondenz = $this->CreatePDF($data, false);
					$this->app->printer->Drucken($data['fax'],$korrespondenz);
					$this->app->DB->Update("UPDATE dokumente SET sent='1' WHERE id='$id' LIMIT 1");
    			unlink($korrespondenz);
					$this->app->Tpl->Set('PAGE',"<div class=\"success\">Das Dokument wurde an das Fax &uuml;bertragen</div>");
				break;
		}
	}

	function KorrespondenzDelete()
	{
		$id = $this->app->Secure->GetGET('id');
		if(is_numeric($id)) 
			$this->app->DB->Update("UPDATE dokumente SET deleted='1' WHERE id='$id' LIMIT 1");
		header("Location: {$_SERVER['HTTP_REFERER']}");
		exit;
	}
	
	function KorrespondenzPdf()
	{
		$id = $this->app->Secure->GetGET('id');
		$data = $this->ConvertFromDB($this->app->DB->SelectArr("SELECT * FROM dokumente WHERE id='$id' LIMIT 1"));		
		$this->CreatePDF($data);	
	}

	function CreatePDF($data, $display=true) 
	{
		$korrespondenz = new KorrespondenzPDF($this->app);
    $korrespondenz->SetBetreff($this->app->erp->ReadyForPDF($data['betreff']));

    $korrespondenz->SetDetail('Datum', $data['datum']);
    $korrespondenz->SetDetail('Bearbeiter', $this->app->erp->ReadyForPDF($data['von']));

    $korrespondenz->setRecipient(array($this->app->erp->ReadyForPDF($data['firma_an']), 
		$this->app->erp->ReadyForPDF($data['an']), '', 
			$this->app->erp->ReadyForPDF($data['adresse']), $data['plz'],$this->app->erp->ReadyForPDF($data['ort'])
		,$data['land']));
    $korrespondenz->setLetterDetails(array($this->app->erp->ReadyForPDF($data['betreff']),str_replace('\r\n',"\n\n",$this->app->erp->ReadyForPDF($data['content']))));
    $korrespondenz->setAbsender($data['firma']);

    $korrespondenz->Create();
		if($display)
    	$korrespondenz->displayDocument();
		else
			return $korrespondenz->displayTMP();
	}	

	function UpdateDokument($data)
	{
    $datum = $this->app->String->Convert($data['datum'],"%1.%2.%3","%3-%2-%1");

		$this->app->DB->Update("UPDATE dokumente SET von='{$data['von']}', firma='{$data['firma']}', ansprechpartner='{$data['ansprechpartner']}',an='{$data['an']}', email_an='{$data['email_an']}', 
														firma_an='{$data['firma_an']}', adresse='{$data['adresse']}',	plz='{$data['plz']}', ort='{$data['ort']}', land='{$data['land']}', datum='$datum', betreff='{$data['betreff']}',
														content='{$data['content']}', signatur='{$data['signatur']}', send_as='{$data['art']}', email='{$data['email']}', printer='{$data['mail']}',
														fax='{$data['fax']}' WHERE id='{$data['id']}' LIMIT 1");
	}

	function CreateDokument($data)
	{
		$adresse = $this->app->User->GetAdresse();
    $datum = $this->app->String->Convert($data['datum'],"%1.%2.%3","%3-%2-%1");

    $this->app->DB->Insert("INSERT INTO dokumente (adresse_from,adresse_to,typ,von,firma,ansprechpartner,an,email_an,firma_an,adresse,plz,ort,land,datum,betreff,content,signatur,send_as,email,
														printer,fax,created)
                            VALUES ('$adresse','{$data['user']}','brieffax','{$data['von']}','{$data['firma']}','{$data['ansprechpartner']}','{$data['an']}','{$data['email_an']}',
														'{$data['firma_an']}','{$data['adresse']}',
														'{$data['plz']}','{$data['ort']}','{$data['land']}','$datum','{$data['betreff']}','{$data['content']}','{$data['signatur']}','{$data['art']}','{$data['email']}',
														'{$data['mail']}','{$data['fax']}',NOW())");
    return $this->app->DB->GetInsertID();
	}

	function DruckerSelect($selected='')
	{
		if($selected=="")
      $selected = $this->app->DB->Select("SELECT standarddrucker FROM user WHERE id='".$this->app->User->GetID()."' LIMIT 1");

    $drucker = $this->app->DB->SelectArr("SELECT id, name FROM  drucker WHERE firma='".$this->app->User->GetFirma()."' AND aktiv='1'");
    for($i=0;$i<count($drucker);$i++)
    {
      if($drucker[$i]['id']==$selected) $mark="selected"; else $mark="";
      $out .="<option value=\"{$drucker[$i]['id']}\" $mark>{$drucker[$i]['name']}</option>";
    }
    return $out;
	}

	

	function ConvertFromDB($db)
	{
		$data = array();
		$data['von'] = $db[0]['von'];
		$data['firma'] = $db[0]['firma'];
		$data['ansprechpartner'] = $db[0]['ansprechpartner'];
		$data['an'] = $db[0]['an'];
		$data['email_an'] = $db[0]['email_an'];
		$data['firma_an'] = $db[0]['firma_an'];
		$data['adresse'] = $db[0]['adresse'];
		$data['plz'] = $db[0]['plz'];
		$data['ort'] = $db[0]['ort'];
		$data['land'] = $db[0]['land'];
		$data['datum'] = $this->app->String->Convert($db[0]['datum'],"%1-%2-%3","%3.%2.%1");
		$data['betreff'] = $db[0]['betreff'];
		$data['content'] = $db[0]['content'];
		$data['signatur'] = $db[0]['signatur'];
		$data['art'] = $db[0]['send_as'];
		$data['email'] = $db[0]['email'];
		$data['mail'] = $db[0]['printer'];
		$data['fax'] = $db[0]['fax'];
		return $data;
	}

	function SaveInput()
	{
		$data = array();		
		
		$data['id'] = $this->app->Secure->GetGET('id');
		$data['user'] = $this->app->Secure->GetGET('user');

		$data['von'] = $this->app->Secure->GetPOST('von');
		$data['firma'] = $this->app->Secure->GetPOST('firma');
		$data['ansprechpartner'] = $this->app->Secure->GetPOST('ansprechpartner');
		$data['an'] = $this->app->Secure->GetPOST('an');
		$data['email_an'] = $this->app->Secure->GetPOST('email_an');
		$data['firma_an'] = $this->app->Secure->GetPOST('firma_an');
		$data['adresse'] = $this->app->Secure->GetPOST('adresse');
		$data['plz'] = $this->app->Secure->GetPOST('plz');
		$data['ort'] = $this->app->Secure->GetPOST('ort');
		$data['land'] = $this->app->Secure->GetPOST('land');
		$data['datum'] = $this->app->Secure->GetPOST('datum');
		$data['betreff'] = $this->app->Secure->GetPOST('betreff');
		$data['content'] = $this->app->Secure->GetPOST('content');
		$data['signatur'] = $this->app->Secure->GetPOST('signatur');
		$data['art'] = $this->app->Secure->GetPOST('art');
		$data['email'] = $this->app->Secure->GetPOST('email');
		$data['mail'] = $this->app->Secure->GetPOST('mail');
		$data['fax'] = $this->app->Secure->GetPOST('fax');

		$data['prefill'] = $this->app->Secure->GetPOST('prefill');

		$data['pdf'] = $this->app->Secure->GetPOST('pdf');
		$data['save'] = $this->app->Secure->GetPOST('save');
		$data['send'] = $this->app->Secure->GetPOST('send');
	
		return $data;
	} 

	function LoadInput($data)
	{
		$this->app->Tpl->Set('VON', $data['von']);
		$this->app->Tpl->Set('FIRMA', $data['firma']);
		$this->app->Tpl->Set('ANSPRECHPARTNER', $data['ansprechpartner']);

		$this->app->Tpl->Set('AN', $data['an']);
		$this->app->Tpl->Set('EMAILAN', $data['email_an']);
		$this->app->Tpl->Set('FIRMAAN', $data['firma_an']);
		$this->app->Tpl->Set('ADRESSE', $data['adresse']);
		$this->app->Tpl->Set('PLZ', $data['plz']);
		$this->app->Tpl->Set('ORT', $data['ort']);
		$this->app->Tpl->Set('LAND', $data['land']);
		$this->app->Tpl->Set('DATUM', $data['datum']);
		$this->app->Tpl->Set('BETREFF', $data['betreff']);
		$this->app->Tpl->Set('CONTENT', $data['content']);

		if($data['signatur']=='0')
			$this->app->Tpl->Set('SIGNATURNO', 'checked');
		else
			$this->app->Tpl->Set('SIGNATURYES', 'checked');

		if($data['art']=='mail')
			$this->app->Tpl->Set('ARTMAIL', 'checked');
		else if($data['art']=='fax')
			$this->app->Tpl->Set('ARTFAX', 'checked');
		else
			$this->app->Tpl->Set('ARTMAIL', 'checked');

			
		$this->app->Tpl->Set('EMAILSELECT', $this->app->erp->GetSelectEmail($data['email']));
		$this->app->Tpl->Set('DRUCKERSELECT', $this->DruckerSelect($data['mail']));
    $this->app->Tpl->Set('FAXSELECT', $this->DruckerSelect($data['fax']));
	
		$this->app->Tpl->Set('PREFILL', $data['prefill']);

		$this->app->YUI->AutoComplete('an', 'adressename');
	}
}
?>

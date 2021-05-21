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

use Xentral\Components\Http\JsonResponse;
use Xentral\Modules\TOTPLogin\TOTPLoginService;

class Totp
{
  /** @var Application */
  private $app;

  /**
   * Totp constructor.
   *
   * @param Application $app
   * @param bool $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;

    if($intern) return;

    $app->ActionHandlerInit($this);

    $app->ActionHandler('generate', 'TOTPGenerateSecretJSON');
    $app->ActionHandler('enable', 'TOTPEnable');
    $app->ActionHandler('disable', 'TOTPDisable');

    $app->ActionHandlerListen($app);
  }

  public function Install()
  {
    $tableName = 'user_totp';
    $this->app->erp->CheckTable($tableName);
    $this->app->erp->CheckColumn('id', 'UNSIGNED INT', $tableName, 'NOT NULL AUTO_INCREMENT');
    $this->app->erp->CheckColumn('user_id', 'INT', $tableName, 'UNSIGNED NOT NULL');
    $this->app->erp->CheckColumn('active', 'TINYINT(1)', $tableName, 'UNSIGNED DEFAULT 0');
    $this->app->erp->CheckColumn('secret', 'VARCHAR(100)', $tableName, 'NOT NULL');
    $this->app->erp->CheckColumn('created_at', 'TIMESTAMP', $tableName, 'DEFAULT NOW()');
    $this->app->erp->CheckColumn('modified_at', 'TIMESTAMP', $tableName);
    $this->app->erp->CheckIndex($tableName, 'user_id', true);

    $this->app->erp->RegisterHook('login_password_check_otp', 'totp', 'TOTPCheckLogin', 1, false, null, 3);
  }

  function TOTPDisable(){
    $action = $this->app->Secure->GetPOST('action');

    if($action !== 'disable'){
      return new JsonResponse(['status' => 'error', 'msg' => 'muss POST sein'], 400);
    }

    /** @var TOTPLoginService $totpLoginService */
    $totpLoginService = $this->app->Container->get('TOTPLoginService');

    $userId = $this->app->User->GetID();

    $totpLoginService->disableTotp($userId);

    return new JsonResponse(['status' => 'success']);
  }

  function TOTPEnable(){
    $secret = $this->app->Secure->GetPOST('secret');

    if(empty($secret)){
      return new JsonResponse(['status' => 'error', 'msg' => 'Secret Empty'], 400);
    }

    /** @var TOTPLoginService $totpLoginService */
    $totpLoginService = $this->app->Container->get('TOTPLoginService');

    $userId = $this->app->User->GetID();

    $totpLoginService->enableTotp($userId);
    $totpLoginService->setUserSecret($userId, $secret);

    return new JsonResponse(['status' => 'success']);
  }

  /**
   * @param $userID
   * @param $token
   * @param $passwordValid
   *
   * @throws Exception
   */
  public function TOTPCheckLogin($userID, $token, &$passwordValid)
  {
    /** @var TOTPLoginService $totpLoginService */
    $totpLoginService = $this->app->Container->get('TOTPLoginService');

    if(!$totpLoginService->isTOTPEnabled($userID)){
      return;
    }
    $passwordValid = $totpLoginService->isTokenValid($userID, $token);
  }

  public function TOTPGenerateSecretJSON(){
    /** @var TOTPLoginService $totpLoginService */
    $totpLoginService = $this->app->Container->get('TOTPLoginService');

    /** @var \Xentral\Components\Token\TOTPTokenManager $tokenManager */
    $tokenManager = $this->app->Container->get('TOTPTokenManager');

    $secret = $tokenManager->generateBase32Secret();

    $label = 'Xentral' . ' | ' . $this->app->erp->GetFirmaName();

    $qr = $totpLoginService->generatePairingQrCode($this->app->User->GetID(), $label, $secret);

    return new JsonResponse(
      [
        'secret' => $secret,
        'qr' => $qr->toHtml(4, 4)
      ]
    );
  }
}

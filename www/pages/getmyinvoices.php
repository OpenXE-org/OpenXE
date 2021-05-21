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
use Xentral\Components\Http\Request;
use Xentral\Modules\ApiAccount\Data\ApiAccountData;
use Xentral\Modules\ApiAccount\Exception\ApiAccountNotFoundException;
use Xentral\Modules\ApiAccount\Service\ApiAccountService;

class Getmyinvoices
{
  /** @var string MODULE_NAME */
  const MODULE_NAME = 'GetMyInvoices';

  /** @var erpooSystem $app */
  private $app;

  /**
   * @param erpooSystem $app
   * @param bool        $intern
   */
  public function __construct($app, $intern = false)
  {
    $this->app = $app;

    if ($intern) {
      return;
    }

    $this->app->ActionHandlerInit($this);
    $this->app->ActionHandler('list', 'GetMyInvoicesList');
    $this->app->ActionHandlerListen($app);
    $this->app->erp->Headlines('GetMyInvoices');
  }

  public function GetMyInvoicesMenu()
  {
    $this->app->erp->MenuEintrag("index.php?module=appstore&action=list","Zur&uuml;ck zur &Uuml;bersicht");
    $this->app->erp->MenuEintrag("index.php?module=getmyinvoices&action=list","&Uuml;bersicht");
  }

  public function GetMyInvoicesList()
  {
    $this->GetMyInvoicesMenu();

    /** @var ApiAccountService $apiAccountService */
    $apiAccountService = $this->app->Container->get('ApiAccountService');

    try {
      $apiAccount = $apiAccountService->getApiAccountByRemoteDomain('getmyinvoices');
      $apiAccountExists = true;
    } catch (ApiAccountNotFoundException $exception){
      $apiAccountExists = false;
    }

    if($apiAccountExists === false){
      /** @var Request $request */
      $request = $this->app->Container->get('Request');
      if($request->getPost('create_api_account')){
        $title = "getmyinvoices";
        $serverUrl = $request->getBaseUrl() . '/';
        $password = md5(uniqid('', true));
        $permissions = '["create_scanned_document"]';

        $formData = [
          'id' => 0,
          'name' => $title,
          'init_key' => $password,
          'import_queue_name' => '',
          'event_url' => $serverUrl,
          'remotedomain' => $title,
          'active' => true,
          'import_queue' => false,
          'cleanutf8' => true,
          'transfer_account_id' => 0,
          'project_id' => 0,
          'permissions' => $permissions,
          'is_legacy' => false,
          'is_html_transformation' => false
        ];

        try {
          $apiAccountData = ApiAccountData::fromFormData($formData);
          $apiAccountId = $apiAccountService->createApiAccount($apiAccountData);
          $apiAccount = $apiAccountService->getApiAccountById($apiAccountId);
          $apiAccountExists = true;
        } catch (ApiAccountNotFoundException $exception) {
          $this->app->Tpl->Set('MESSAGE', '<div class="error">API-Account konnte nicht gefunden werden!</div>');
        } catch (Exception $exception) {
          $this->app->Tpl->Set('MESSAGE', '<div class="error">API-Account konnte nicht angelegt werden!</div>');
        }
      }
    }

    if($apiAccountExists){
      $getmyInvoiceExistingAccountHtml =
        '<table>
					<tr>
						<td width="110">{|URL|}:</td>
						<td>'.$apiAccount->getEventUrl().'</td>
						<td><input type="button" class="button button-secondary" id="api_account_url_clipboard" name="api_account_url_clipboard" value="Zwischenablage" onclick="copyTextToClipboard(\''.$apiAccount->getEventUrl().'\');"></td>
					</tr>
					<tr>
						<td>{|Benutzername|}:</td>
						<td>'.$apiAccount->getRemoteDomain().'</td>
						<td><input type="button" class="button button-secondary" id="api_account_app_name_clipboard" name="appi_account_app_name_clipboard" value="Zwischenablage" onclick="copyTextToClipboard(\''.$apiAccount->getRemoteDomain().'\');"></td>
					</tr>
					<tr>
						<td>{|Passwort|}:</td>
						<td width="250">'.$apiAccount->getInitKey().'</td>
						<td><input type="button" class="button button-secondary" id="api_account_key_clipboard" name="api_account_key_clipboard" value="Zwischenablage" onclick="copyTextToClipboard(\''.$apiAccount->getInitKey().'\');"></td>
					</tr>
				</table>';

      $this->app->Tpl->Set('GETMYINVOICES', $getmyInvoiceExistingAccountHtml);
    }else{
      $getmyInvoiceCreateAccountHtml =
        '<form method="post">
				  <table>
				    <tr>
				      <td><input type="submit" id="create_api_account" name="create_api_account" value="Zugang anlegen"></td>
				      <td>Legt einen API-Account an.</td>
						</tr>
					</table>
				</form>';
      $this->app->Tpl->Set('GETMYINVOICES', $getmyInvoiceCreateAccountHtml);
    }

    $this->app->Tpl->Parse("PAGE","getmyinvoices_list.tpl");
  }

}

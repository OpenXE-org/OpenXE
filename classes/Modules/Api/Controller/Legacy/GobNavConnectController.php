<?php
namespace Xentral\Modules\Api\Controller\Legacy;

use Xentral\Components\Http\Request;
use Xentral\Modules\Api\LegacyBridge\LegacyApplication;

class GobNavConnectController
{
    /** @var Request $request */
    protected $request;

    /** @var LegacyApplication */
    protected $app;

    /**
     * @param LegacyApplication $app
     * @param Request           $request
     */
    public function __construct(LegacyApplication $app, Request $request)
    {
        $this->request = $request;
        $this->app = $app;
    }

    public function exampleAction()
    {
        $post = $this->request->getContent();
        $id = (int)$this->app->DB->Select(
            "SELECT id FROM uebertragungen_account WHERE aktiv = 1 AND xml_pdf = 'TransferGobNav' LIMIT 1"
        );
        if ($id > 0) {
            /** @var \Uebertragungen $transferObject */
            $transferObject = $this->app->loadModule('uebertragungen');
            if (!empty($transferObject)) {
                /** @var \TransferGobNav $transferGobnav */
                $transferGobnav = $transferObject->LoadTransferModul('TransferGobNav', $id);
                $transferGobnav->ParseRequest($post);
            }
        }
        exit;
    }
}

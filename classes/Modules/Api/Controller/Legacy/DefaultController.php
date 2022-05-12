<?php

namespace Xentral\Modules\Api\Controller\Legacy;

use Xentral\Components\Http\Request;
use Xentral\Modules\Api\Exception\BadRequestException;

class DefaultController
{
    /** @var Request $request */
    protected $request;

    /** @var \Api $legacyApi */
    protected $legacyApi;

    /** @var int $apiId */
    protected $apiId;

    /**
     * @param \Api    $legacyApi
     * @param Request $request
     * @param int     $apiId
     */
    public function __construct($legacyApi, $request, $apiId)
    {
        $this->request = $request;
        $this->legacyApi = $legacyApi;
        $this->apiId = $apiId;
    }

    public function postAction()
    {
        $action = $this->request->attributes->get('action');
        $contentType = $this->request->getContentType();
        $content = $this->request->getContent();

        if ($contentType === 'xml') {
            $this->legacyApi->app->Secure->POST['xml'] = '<xml>' . $content . '</xml>';
        }

        if ($contentType === 'json') {
            $requestData = json_decode($content, true);
            $contentPrepared = isset($requestData['data']) ? json_encode($requestData['data']) : $content;
            $this->legacyApi->app->Secure->GET['json'] = true;
            $this->legacyApi->app->Secure->POST['json'] = $contentPrepared;
        }

        // API-Methode aufrufen
        $this->legacyApi->setApiId($this->apiId);
        $this->legacyApi->app->Secure->GET['action'] = $action;
        $apiMethod = 'Api' . $action;

        $actionMapping = [
            'AccountCreate' => 'ApiAdresseAccountCreate',
            'AccountEdit'   => 'ApiAdresseAccountEdit',
        ];
        if (isset($actionMapping[$action])) {
            $apiMethod = $actionMapping[$action];
        }

        $this->legacyApi->$apiMethod();

        // API-Methode liefert normalerweise selbst das Ergebnis aus und beendet die Script-Ausführung.
        // Falls aber eine nicht existierende API-Methode aufgerufen wird, läuft das Script in die Exception.
        throw new BadRequestException();
    }

    public function readAction()
    {
        $this->legacyApi->setApiId($this->apiId);
        $action = $this->request->attributes->get('action');

    }
}

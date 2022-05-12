<?php


namespace Xentral\Modules\Api\Controller\Legacy;

use Xentral\Components\Http\Request;
use Xentral\Components\Http\Response;
use Xentral\Modules\Api\Exception\ResourceNotFoundException;
use Xentral\Modules\Api\LegacyBridge\LegacyApplication;

class ShopimportController
{
    /** @var Request $request */
    protected $request;

    /** @var LegacyApplication $app */
    protected $app;

    /** @var int $accountId */
    protected $accountId;

    /**
     * @param LegacyApplication $app
     * @param Request           $request
     */
    public function __construct(LegacyApplication $app, Request $request, $accountId)
    {
        $this->request = $request;
        $this->app = $app;
        $this->accountId = $accountId;
    }

    /**
     * @param bool $onlyActive
     *
     * @return array
     */
    private function getShopFromApi($onlyActive = true)
    {
        $shop = $this->app->DB->SelectRow(
            sprintf(
                'SELECT * FROM `shopexport` WHERE `api_account_id` = %d LIMIT 1',
                $this->accountId
            )
        );

        if (empty($shop)) {
            throw new ResourceNotFoundException('Shop not found');
        }

        if($onlyActive && empty($shop['aktiv'])) {
            throw new ResourceNotFoundException('Shop not connected');
        }

        return $shop;
    }

    /**
     * @return Response
     */
    public function auth()
    {
        $shop = $this->getShopFromApi();
        $pageContents = $this->app->remote->RemoteConnection($shop['id'], true);
        if (strpos($pageContents, 'success') !== 0) {
            throw new ResourceNotFoundException('Auth Error ' . $pageContents);
        }

        /*$this->app->DB->Update(
            sprintf(
                "UPDATE `shopexport` SET `api_account_token` = '' WHERE `id` = %d",
                $shop['id']
            )
        );*/

        return $this->sendResponse(json_encode(['success' => true]), Response::HTTP_OK);
    }

    /**
     * @return string
     */
    public function getOrderByRequest()
    {
        $orderNumber = $this->request->attributes->get('ordernumber');
        $orderNumber = base64_decode($orderNumber);
        if (empty($orderNumber)) {
            throw new ResourceNotFoundException(
                'Ordernumber is empty'
            );
        }

        return $orderNumber;
    }

    /**
     * @param int  $shopId
     * @param bool $withDbCheck
     */
    public function getArticleByRequest($shopId, $withDbCheck = true)
    {
        $articlenumber = $this->request->attributes->get('articlenumber');
        $articlenumber = base64_decode($articlenumber);
        if (empty($articlenumber)) {
            throw new ResourceNotFoundException(
                'Articlenumber is empty'
            );
        }

        $article = $this->app->DB->SelectRow(
            sprintf(
                "SELECT art.id, art.projekt FROM `artikel` AS art 
                LEFT JOIN `artikelnummer_fremdnummern` AS af on art.id = af.artikel AND af.aktiv = 1 AND af.shopid = %d
                WHERE (art.nummer = '%s' OR af.nummer = '%s') AND (art.geloescht = 0 OR art.geloescht IS NULL)
                ORDER BY af.id DESC
                LIMIT 1",
                $shopId,
                $this->app->DB->real_escape_string($articlenumber),
                $this->app->DB->real_escape_string($articlenumber)
            )
        );
        if (empty($article)) {
            if($withDbCheck) {
                throw new ResourceNotFoundException(
                    sprintf('Articlenumber %s not found', $articlenumber)
                );
            }
            $article = [];
        }

        $article['number'] = $articlenumber;

        return $article;
    }

    /**
     * @return Response
     */
    public function putArticleToShop()
    {
        $this->auth();
        $shop = $this->getShopFromApi();
        $article = $this->getArticleByRequest($shop['id']);

        $ret = $this->app->remote->RemoteSendArticleList($shop['id'],[$article['id']], $article['number'], false);
        if (empty($ret) || !is_array($ret) || isset($ret['error'])) {
            return $this->sendResponse(
                json_encode(['success' => false]),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->sendResponse(json_encode(['success' => true]), Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function getStatus()
    {
        $shop = $this->getShopFromApi(false);
        $status = !empty($shop['aktiv']);
        if($status) {
            $this->auth();
        }

        return $this->sendResponse(json_encode(['success' => true, 'connected' => $status]), Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function postDisconnect()
    {
        $shop = $this->getShopFromApi(false);
        $status = !empty($shop['aktiv']);
        if(!$status) {
            return $this->sendResponse(
                json_encode(
                    ['success' => false,'error'=>'shop allready disconnected']
                ),
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->app->DB->Update(sprintf("UPDATE `shopexport` SET `aktiv` = 0 WHERE `id` = %d", $shop['id']));

        return $this->sendResponse(
            json_encode(
                ['success' => true,'message'=>'shop disconnected']
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @return Response
     */
    public function postReconnect()
    {
        $shop = $this->getShopFromApi(false);
        $status = !empty($shop['aktiv']);
        if($status) {
            return $this->sendResponse(
                json_encode(
                    ['success' => false,'error'=>'shop allready connected']
                ),
                Response::HTTP_BAD_REQUEST
            );
        }

        $this->app->DB->Update(sprintf("UPDATE `shopexport` SET `aktiv` = 1 WHERE `id` = %d", $shop['id']));

        return $this->sendResponse(
            json_encode(
                ['success' => true,'message'=>'shop reconnected']
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @return Response
     */
    public function putOrderToXentral()
    {
        $this->auth();
        $shop = $this->getShopFromApi();
        $orderNumber = $this->getOrderByRequest();
        /** @var \Shopimport $shopimport */
        $shopimport = $this->app->loadModule('shopimport');
        $res = $shopimport->importSingleOrder(
            $shop['id'], $orderNumber, empty($shop['demomodus']), $shop['projekt'], true
        );
        if(empty($res['status'])) {
            return $this->sendResponse(
                json_encode(
                    ['success' => false,'error'=>$res['error']]
                ),
                Response::HTTP_BAD_REQUEST
            );
        }
        if($shop['auftraegeaufspaeter']) {
            return $this->sendResponse(
                json_encode(
                    [
                        'success' => true,
                        'message'=>$res['info'],
                    ]
                ),
                Response::HTTP_OK
            );
        }
        $cart = $this->app->DB->SelectRow(
            sprintf('SELECT * FROM `shopimport_auftraege` WHERE `id` = %d', $res['id'])
        );
        [$customerNumber, $customerNumberImported] = $shopimport->getCustomerNumberFromShopCart($cart);
        $res = $shopimport->importShopOrder(
            $res['id'], $shop['utf8codierung'],
            $customerNumber, $customerNumberImported,
            $unknownPaymentTypes
        );
        
        return $this->sendResponse(
            json_encode(
                [
                    'success' => true,
                    'message'=>$res['info'],
                ]
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @return Response
     */
    public function putArticleToXentral()
    {
        $this->auth();
        $shop = $this->getShopFromApi();
        $article = $this->getArticleByRequest($shop['id'], false);
        $ret = $this->app->remote->RemoteGetArticle($shop['id'], $article['number'], true);
        if (empty($ret) || !is_array($ret) || isset($ret['error'])) {
            return $this->sendResponse(
                json_encode(['success' => false]),
                Response::HTTP_BAD_REQUEST
            );
        }

        if(empty($article['id'])) {
            $article = $this->getArticleByRequest($shop['id'], false);
        }
        if(!empty($article['id'])) {
            /** @var \Artikel $articleObj */
            $articleObj = $this->app->loadModule('artikel');
            $articleObj->updateShopArticle($article['id'], $ret);
        }

        return $this->sendResponse(json_encode(['success' => true]), Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function syncStorage()
    {
        //$this->auth();
        $shop = $this->getShopFromApi();
        $article = $this->getArticleByRequest($shop['id']);
        $ret = $this->app->remote->RemoteSendArticleList($shop['id'], [$article['id']],$article['number'], true);
        if (empty($ret) || (!is_array($ret) && $ret !== 1) || isset($ret['error'])) {
            return $this->sendResponse(
                json_encode(['success' => false]),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->sendResponse(json_encode(['success' => true]), Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function getArticleSyncState()
    {
        $shop = $this->getShopFromApi();
        $count = $this->app->DB->Select(
            sprintf(
                'SELECT COUNT(`ao`.`id`) 
                FROM `artikel_onlineshops` AS `ao` 
                INNER JOIN `artikel` AS `art` ON `ao`.artikel = `art`.`id` AND `art`.geloescht = 0
                WHERE `ao`.shop = %d AND `ao`.`aktiv` = 1',
                $shop['id']
            )
        );

        return $this->sendResponse(json_encode(['success' => true, 'count' => $count]), Response::HTTP_OK);
    }

    public function postDistconnect()
    {
        //postReconnect
    }

    /**
     * @return Response
     */
    public function getModulelinks()
    {
        $shop = $this->getShopFromApi();
        $shopId = $shop['id'];
        /** @var \Onlineshops $onlineShop */
        $onlineShop = $this->app->loadModule('onlineshops');
        $moduleList = $onlineShop->getModulelinks($shopId);
        return $this->sendResponse(
            json_encode(
                ['success' => true, 'modulelist' => $moduleList]
            ),
            Response::HTTP_OK
        );
    }

    /**
     * @return Response
     */
    public function getStatistics()
    {
        $shop = $this->getShopFromApi();
        $shopId = $shop['id'];
        $stats = [];
        /** @var \Verkaufszahlen $verkaufszahlen */
        $verkaufszahlen = $this->app->loadModule('verkaufszahlen');

        [$stats['orders_in_shipment'], $stats['orders_open']] = $verkaufszahlen->getVersandStats(
            sprintf(' AND a.shop = %d ', $shopId)
        );

        $stats['packages_yesterday'] = $verkaufszahlen->getPackages(
            " v.versendet_am=DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),'%Y-%m-%d') '",
            sprintf('INNER JOIN `auftrag` AS `a` ON l.auftragid = a.id AND a.shop = %d', $shopId)
        );
        $stats['packages_today'] = $verkaufszahlen->getPackages(
            " v.versendet_am=DATE_FORMAT(NOW(),'%Y-%m-%d') '",
            sprintf('INNER JOIN `auftrag` AS `a` ON l.auftragid = a.id AND a.shop = %d', $shopId)
        );

        [
            $stats['order_income_yesterday'],
            $stats['contribution_margin_yesterday'],
            $stats['contribution_margin_perc_yesterday']
            ] =
            $verkaufszahlen->getOrderStats(
                sprintf(
                    " AND `datum` = DATE_FORMAT(DATE_SUB(NOW(),INTERVAL 1 day),'%%Y-%%m-%%d') AND `shop` = %d ",
                    $shopId
                )
            );
        [
            $stats['order_income_today'],
            $stats['contribution_margin_today'],
            $stats['contribution_margin_perc_today']
            ] =
            $verkaufszahlen->getOrderStats(
                sprintf(
                    " AND `datum` = DATE_FORMAT(NOW(),'%%Y-%%m-%%d') AND `shop` = %d ",
                    $shopId
                )
            );

        return $this->sendResponse(json_encode(['success' => true, 'stats' => $stats]), Response::HTTP_OK);
    }

    /**
     * @return Response
     */
    public function postRefund()
    {
        $shop = $this->getShopFromApi();
        $shopId = $shop['id'];
        $post = $this->request->getContent();
        if(empty($post)) {
            throw new ResourceNotFoundException('Data is empty');
        }
        $contentType = $this->request->getContentType();
        $data = null;
        if ($contentType === 'json' || $contentType === null) {
            $data = json_decode($post);
        }
        if ($data === null && ($contentType === 'xml' || $contentType === null)) {
            $data = simplexml_load_string($post);
        }
        if(empty($post)) {
            throw new ResourceNotFoundException('could not parse Data');
        }

        /** @var \Shopimport $shopimport */
        $shopimport = $this->app->loadModule('shopimport');
        if($shopimport === null || !method_exists($shopimport, 'Refund')) {
            return $this->sendResponse(
                json_encode(
                    [
                        'success' => false,
                        'error'=>'not implemented'
                    ]
                ),
                Response::HTTP_BAD_REQUEST
            );
        }

        try {
            $ret = $shopimport->Refund($shopId, $data);
        }
        catch(\Exception $e) {
            return $this->sendResponse(
                json_encode(
                    [
                        'success' => false,
                        'error'   => $e->getMessage(),
                    ]
                ),
                Response::HTTP_BAD_REQUEST
            );
        }

        return $this->sendResponse(json_encode(['success' => true,'creditnote_id' => $ret]), Response::HTTP_OK);
    }

    /**
     * @param string $data
     * @param string $contentType [xml|json]
     * @param int    $statusCode  HTTP-Statuscode
     *
     * @return Response
     */
    protected function sendResponse($data, $contentType, $statusCode = Response::HTTP_OK)
    {
        return new Response(
            $data,
            $statusCode,
            ['Content-Type' => 'application/json; charset=UTF-8']
        );
    }
}

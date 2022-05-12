<?php


namespace Xentral\Modules\Api\Controller\Legacy;

use TransferOpentrans;
use Xentral\Components\Http\Request;
use Xentral\Components\Http\Response;
use Xentral\Modules\Api\Converter\OpenTransConverter;
use Xentral\Modules\Api\Exception\ResourceNotFoundException;
use Xentral\Modules\Api\LegacyBridge\LegacyApplication;

class OpenTransConnectController
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
    public function __construct(LegacyApplication $app, OpenTransConverter $converter, Request $request, $accountId)
    {
        $this->request = $request;
        $this->converter = $converter;
        $this->app = $app;
        $this->accountId = $accountId;
    }

    /**
     * @return Response
     */
    public function deleteOrder()
    {
        $orderId = $this->getDoctypeIdByRequestAttributes('order');
        $transferOpenTrans = $this->getTransferObject();
        list($result, $statusCode, $rootNode) = $transferOpenTrans->deleteOrder($orderId);
        if(is_array($result)) {
            $result = $this->converter->arrayToXml($result, $rootNode);
        }
        if(empty($result)) {
            throw new ResourceNotFoundException('Auftrag konnte nicht gelöscht werden');
        }

        return $this->sendResponse($result,$statusCode);
    }

    /**
     * @param string $doctype
     *
     * @return int
     */
    protected function getDoctypeIdByRequestAttributes($doctype = 'deliverynote')
    {
        $id = (int)$this->request->attributes->get('id');
        $orderId = $this->request->attributes->get('orderid');
        $ordernumber = $orderId > 0?'':$this->request->attributes->get('ordernumber');
        $extOrder = $orderId > 0 || !empty($ordernumber)?'':$this->request->attributes->get('extorder');
        if($id > 0) {
            return $id;
        }

        if(!empty($ordernumber)) {
            $orderId = $this->app->DB->Select(
                sprintf(
                    "SELECT id FROM auftrag WHERE belegnr = '%s' AND belegnr <> '' LIMIT 1",
                    $this->app->DB->real_escape_string($ordernumber)
                )
            );
            if(empty($orderId)) {
                throw new ResourceNotFoundException(sprintf('Auftrag mit Belegnr \'%s\' nicht gefunden', $ordernumber));
            }
        }
        if(!empty($extOrder)) {
            $orderId = $this->app->DB->Select(
                sprintf(
                    "SELECT id FROM auftrag WHERE internet = '%s' AND internet <> '' LIMIT 1",
                    $this->app->DB->real_escape_string($extOrder)
                )
            );
            if(empty($orderId)) {
                throw new ResourceNotFoundException(sprintf('Auftrag mit Externer Belegnr \'%s\' nicht gefunden', $extOrder));
            }
        }

        if(!empty($orderId)) {
            switch($doctype) {
                case 'order':
                    return $orderId;
                    break;
                case 'invoice':
                    $id = $this->app->DB->Select(
                        sprintf(
                            "SELECT id FROM rechnung WHERE auftragid = %d ORDER BY status = 'storniert' LIMIT 1",
                            $orderId
                        )
                    );
                    if(!empty($id)) {
                        return $id;
                    }

                    throw new ResourceNotFoundException(
                        sprintf('Rechnung mit Order-ID \'%s\' nicht gefunden',
                            $orderId
                        )
                    );
                    break;
                case 'deliverynote':
                default:
                    $id = $this->app->DB->Select(
                        sprintf(
                            "SELECT id FROM lieferschein WHERE auftragid = %d ORDER BY status = 'storniert' LIMIT 1",
                            $orderId
                        )
                    );
                    if(!empty($id)) {
                        return $id;
                    }

                    throw new ResourceNotFoundException(
                        sprintf('Lieferschein mit Order-ID \'%s\' nicht gefunden',
                            $orderId
                        )
                    );

                    break;
            }
        }

        return $id;
    }

    /**
     * @return Response
     */
    public function readDispatchnotification()
    {
        $deliveryNoteId = $this->getDoctypeIdByRequestAttributes('deliverynote');
        $transferOpenTrans = $this->getTransferObject();

        list($result, $statusCode, $rootNode) = $transferOpenTrans->getDispatchnotification($deliveryNoteId);
        if(is_array($result)) {
            $result = $this->converter->arrayToXml($result, $rootNode);
        }
        if(empty($result)) {
            throw new ResourceNotFoundException(
                sprintf('Lieferschein mit ID \'%s\' nicht gefunden',
                    $deliveryNoteId
                )
            );
        }

        return $this->sendResponse($result,$statusCode);
    }

    /**
     * @param int    $apiId
     * @param string $request
     * @param string $type
     * @param bool   $isIncoming
     * @param string $doctype
     * @param string $status
     * @param int    $doctypeId
     *
     * @return int
     */
    protected function insertApiRequestLog(
        $apiId, $request, $type, $isIncoming, $doctype, $status = '', $doctypeId = 0
    )
    {
        $this->app->DB->Insert(
            sprintf(
                "INSERT INTO `api_request_response_log` 
                (api_id, raw_request, raw_response, type, status, doctype, doctype_id, is_incomming, created_at) 
                VALUES (%d, '%s', '%s', '%s', '%s','%s',%d,%d,NOW()) ",
                $apiId,
                ($isIncoming?$this->app->DB->real_escape_string($request):''),
                (!$isIncoming?$this->app->DB->real_escape_string($request):''),
                $this->app->DB->real_escape_string($type),
                $this->app->DB->real_escape_string($status),
                $this->app->DB->real_escape_string($doctype),
                $doctypeId,
                $isIncoming
            )
        );

        return (int)$this->app->DB->GetInsertID();
    }

    /**
     * @param int    $logId
     * @param string $status
     */
    protected function setLogStatus($logId, $status)
    {
        $this->app->DB->Update(
            sprintf(
                "UPDATE `api_request_response_log` SET `status` = '%s' WHERE `id` = %d",
                $this->app->DB->real_escape_string($status),
                $logId
            )
        );
    }

    /**
     * @param int $logId
     * @param int $doctypeId
     */
    protected function setLogDoctypeId($logId, $doctypeId)
    {
        $this->app->DB->Update(
            sprintf(
                'UPDATE `api_request_response_log` SET `doctype_id` = %d WHERE `id` = %d',
                $doctypeId,
                $logId
            )
        );
    }

    /**
     * @return Response
     */
    public function createOrder()
    {
        $transferOpenTrans = $this->getTransferObject();
        if(!empty($this->accountId)) {
            $transferOpenTrans->setApiId($this->accountId);
        }
        $post = $this->request->getContent();

        if(empty($post)) {
            throw new ResourceNotFoundException('Data is empty');
        }

        $logId = $this->insertApiRequestLog($this->accountId, $post, 'create_order',true,'auftrag');

        $xml = $this->converter->getXmlFromString($post);
        if(empty($xml)) {
            $this->setLogStatus($logId, 'error');
            throw new ResourceNotFoundException('Data is no valid Xml');
        }

        list($result, $statusCode, $rootNode, $orderId) = $transferOpenTrans->createOrder($xml);
        if(!empty($orderId)) {
            $this->setLogDoctypeId($logId, $orderId);
        }
        if(is_array($result)) {
            $result = $this->converter->arrayToXml($result, $rootNode);
        }
        if(empty($result)) {
            $this->setLogStatus($logId, 'error');
            throw new ResourceNotFoundException('Auftrag konnte nicht erstellt werden');
        }

        if($statusCode === Response::HTTP_CREATED) {
            $this->setLogStatus($logId, 'ok');
        }
        else {
            $this->setLogStatus($logId, 'error');
        }

        return $this->sendResponse($result,$statusCode);
    }

    /**
     * @return Response
     */
    public function updateDispatchnotification()
    {
        $deliveryNoteId = $this->getDoctypeIdByRequestAttributes('deliverynote');
        $post = $this->request->getContent();
        if(empty($post)) {
            throw new ResourceNotFoundException('Data is empty');
        }
        $logId = $this->insertApiRequestLog(
            $this->accountId, $post, 'update_dispatchnotification',true,'lieferschein','', $deliveryNoteId
        );

        $xml = $this->converter->getXmlFromString($post);
        if(empty($xml)) {
            $this->setLogStatus($logId, 'error');
            throw new ResourceNotFoundException('Data is no valid Xml');
        }
        $transferOpenTrans = $this->getTransferObject();
        list($result, $statusCode, $rootNode) = $transferOpenTrans->updateDispatchnotification($deliveryNoteId, $xml);
        if(is_array($result)) {
            $result = $this->converter->arrayToXml(
                $result,
                $rootNode
            );
        }

        if($statusCode === Response::HTTP_OK) {
            $this->setLogStatus($logId, 'ok');
        }

        return $this->sendResponse($result,$statusCode);
    }

    /**
     * @return Response
     */
    public function readInvoice()
    {
        $invoiceId = $this->getDoctypeIdByRequestAttributes('invoice');
        $transferOpenTrans = $this->getTransferObject();

        list($result, $statusCode, $rootNode) = $transferOpenTrans->getInvoice($invoiceId);
        if(is_array($result)) {
            $result = $this->converter->arrayToXml(
                $result,
                $rootNode
            );
        }

        if(empty($result)) {
            throw new ResourceNotFoundException(sprintf('Rechnung mit ID \'%s\' nicht gefunden', $invoiceId));
        }

        return $this->sendResponse($result,$statusCode);
    }

    /**
     * @return Response
     */
    public function readOrder()
    {
        $orderId = $this->getDoctypeIdByRequestAttributes('order');

        $transferOpenTrans = $this->getTransferObject();

        list($result,$statusCode, $rootNode) = $transferOpenTrans->getOrder($orderId);
        if(is_array($result)) {
            $result = $this->converter->arrayToXml
            (
                $result,
                $rootNode
            );
        }

        if(empty($result)) {
            throw new ResourceNotFoundException(sprintf('Auftrag mit ID \'%s\' nicht gefunden', $orderId));
        }

        return $this->sendResponse($result,$statusCode);
    }

    public function updateOrder()
    {
        $orderId = $this->getDoctypeIdByRequestAttributes('order');

        $transferOpenTrans = $this->getTransferObject();
        $order = $transferOpenTrans->getOrderArr($orderId);
        if(empty($order)) {
            throw new ResourceNotFoundException(sprintf('Auftrag mit ID \'%s\' nicht gefunden', $orderId));
        }
        $post = $this->request->getContent();
        $logId = $this->insertApiRequestLog(
            $this->accountId, $post, 'update_order',true,'auftrag','', $orderId
        );
        $arr = $this->converter->toArray($post);
        if(empty($arr)) {
            $this->setLogStatus($logId, 'error');
            throw new ResourceNotFoundException('XML konnte nicht geparsed werden');
        }
    }

    /**
     * @param string  $data
     * @param string $contentType [xml|json]
     * @param int    $statusCode HTTP-Statuscode
     *
     * @return Response
     */
    protected function sendResponse($data, $contentType, $statusCode = Response::HTTP_OK)
    {
        return new Response(
            $data,
            $statusCode,
            ['Content-Type' => 'application/xml; charset=UTF-8']
        );
    }

    /**
     * @return TransferOpentrans
     */
    private function getTransferObject()
    {
        $id = (int)$this->app->DB->Select(
            "SELECT id FROM uebertragungen_account WHERE aktiv = 1 AND xml_pdf = 'TransferOpenTrans' AND id = %d LIMIT 1",
            $this->accountId
        );
        if($id < 0) {
            throw new ResourceNotFoundException('TransferOpenTrans Module not found');
        }

        /** @var \Uebertragungen $transferObject */
        $transferObject = $this->app->loadModule('uebertragungen');
        if(empty($transferObject)) {
            throw new ResourceNotFoundException('TransferOpenTrans Module not found');
        }

        return $transferObject->LoadTransferModul('TransferOpentrans', $id);
    }
}
